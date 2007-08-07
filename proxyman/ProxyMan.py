#!/bin/env python
#
# Copyright (C) 2006 Earl C. Terwilliger
# Email contact: earl@micpc.com
#
#
#    This file is part of The Asterisk Event Monitor WEB/PHP Interface.
#
#    These files are free software; you can redistribute them and/or modify
#    them under the terms of the GNU General Public License as published by
#    the Free Software Foundation; either version 2 of the License, or
#    (at your option) any later version.
#
#    These programs are distributed in the hope that they will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#
#  Asterisk Manager Proxy Version 1.0  2006-05-24
#
#    A multi-threaded server which connects to an Asterisk Manager and logs all events 
#
#    Connects to the Asterisk Manager and listens for all events 
#    Optionally listens on socket and accepts client connections 
#               proxies all client commands to the Asterisk Manager Interface 
#               sends all data received from the manager to all connected clients
#    Optionally prints data as received (also in optional hex dump format)
#    Optionally logs all data to a MySQL database table
#
#
#  MySQL commands for the MySQL table:
#
#      CREATE DATABASE asterisk;
#
#      GRANT ALL
#        ON asterisk.*
#        TO asteriskuser@localhost
#        IDENTIFIED BY 'asterisk';
#      USE asterisk;
#
#      DROP TABLE IF EXISTS events;
#
#      CREATE TABLE events (
#         id int(10) unsigned NOT NULL auto_increment,
#         timestamp datetime NOT NULL default '0000-00-00 00:00:00',
#         event LONGTEXT ,
#         PRIMARY KEY (`id`)
#      ); 
#
#
#
#     Configurable Parameters
#
#  HOST        is the HOST IP address to listen on for client connections
#  CLIENTPORT  is the HOST PORT       to listen on for client connections
#  MANAGER     is the IP address of the Asterisk Manager 
#  MANAGERPORT is the HOST PORT  of the Asterisk Manager
#  USERNAME    is the Asterisk Manager user name to login to the Asterisk Manager
#  SECRET      is the Asterisk Manager password associated with USERNAME
#  debug       if 1 then ASCII echo the events as received  (0 = no print)
#                  nodebug or debug are acceptable parameters on the command line
#  dblog       if 1 then open the MySQL database and log all events (0 = no MySQL logging)
#                  SQLhost,SQLuser,SQLpass,SQLdb specify the MySQL connection parameters
#                  nodblog or dblog are acceptable parameters on the command line
#  hexdump     if 1 then echo the events in HEXidecimal output (i.e. DUMP FORMAT) (0 = no)
#                  nohexdump or hexdump are acceptable parameters on the command line
#  clients     if 1 then allow clients to connect (0 = no client connections)
#                  MAXCONNECTIONS  is the number of allowed client connections
#                  noclients or clients are acceptable parameters on the command line
#
#
#              Note: the original 'defaults' are nodebug,nohexdump,dblog,noclients
#                    which is equivalent to invoking ProxyMan via:
#
#                      ./ProxyMan.py nodebug nohexdump dblog noclients
#
#
HOST           = '127.0.0.1' 
CLIENTPORT     = 4575 
MANAGER        = '127.0.0.1'
MANAGERPORT    = 5038
USERNAME       = 'eventmonitor'
SECRET         = 'asterisksecret'
debug          = 0
dblog          = 1
hexdump        = 0
clients        = 0
MAXCONNECTIONS = 20

SQLhost        = 'localhost'
SQLuser        = 'asteriskuser'
SQLpass        = 'asterisk'
SQLdb          = 'asterisk'

#
#
# The CODE
#
#

import sys,thread,socket,os,MySQLdb,time,datetime,string

alive = 0
conns = []
tlock = thread.allocate_lock()

def dumphex(s):
  str = ""
  for i in range(0,len(s)):
    if s[i] in string.whitespace:
      str = str +  '.'
      continue
    if s[i] in string.printable:
      str = str + s[i]
      continue
    str = str +  '.'
  bytes = map(lambda x: '%.2x' % x, map(ord, s))
  print
  for i in xrange(0,len(bytes)/16):
    print '    %s' % string.join(bytes[i*16:(i+1)*16],' '),
    print '    %s' % str[i*16:(i+1)*16]
  print '    %-51s' % string.join(bytes[(i+1)*16:],' '),
  print '%s' % str[(i+1)*16:]
  print

def client_AGI(conn,addr):
  global tlock,msconn,debug
  dbuff = ""
  while (1):
    data = conn.recv(1024)
    if not data: break
    dbuff += data
    if (dbuff[-4:] != "\r\n\r\n"): continue
    tlock.acquire()
    msconn.send(dbuff)
    tlock.release()
    dbuff = ""
  tlock.acquire()
  conns.remove(conn)
  conn.close()
  tlock.release()
  if (debug): print "\nDisconnected from ",addr,"\n"

def server_AGI():
  global alive,tlock,conns,msconn,debug,dblog,hexdump,SQLhost,SQLuser,SQLpass,SQLdb
  if (dblog) :
    try:
      db = MySQLdb.connect(host=SQLhost,user=SQLuser,passwd=SQLpass,db=SQLdb)
    except MySQLdb.Error, err:
      if (debug):
        print "\nError %d: %s" % (err.args[0], err.args[1])
        print "\nExiting."
      alive = 0
      sys.exit (1)
    cursor = db.cursor()
  if (debug): print "Connecting to Asterisk Manager....."
  try:
    msconn = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    msconn.connect((MANAGER,MANAGERPORT))
  except socket.error, err:
    if (debug):
      print "\nError %d: %s" % (err.args[0],err.args[1])
      print "\nExiting."
    alive = 0
    sys.exit (1)
  if (debug): print "Sending Login....."
  msconn.send("Action: Login\r\n")
  msconn.send("UserName: " + USERNAME + "\r\n")
  msconn.send("Secret: " + SECRET + "\r\n\r\n");
  if (debug): print "Sending Action: Events Eventmask: On"
  msconn.send("Action: Events\r\nEventmask: On\r\n\r\n")
  if (debug): print "Waiting for Events.....\n"
  dbdata = ""
  while (1):
    try: data = msconn.recv(1024)
    except socket.error, err:
      if (debug):
        print "\nError %d: %s" % (err.args[0],err.args[1])
        print "\nExiting."
      alive = 0
      sys.exit (1)
    if not data: break
    ct = datetime.datetime.now()
    if (debug) :
      print ct 
      print data
    tlock.acquire()
    for i in range(len(conns)):
      conn = conns[i]
      conn.send(data)
    tlock.release()
    if (hexdump): dumphex(data)
    dbdata += data
    if (dbdata[-4:] != "\r\n\r\n"): continue
    events = dbdata.split("\r\n\r\n")
    for i in range(len(events)):
      events[i] = events[i].replace("\r"," ")
      events[i] = events[i].replace("\n"," ")
      if (events[i] == ""): continue
      if (dblog) :
        cursor.execute("INSERT INTO events (timestamp,event) VALUES (%s, %s)", (ct, events[i]))
      if (debug) and (dblog):
        print "Inserted event record id %s\n" % (int(db.insert_id()))
    dbdata = ""
  if (debug): print "\nManager closed connection..."
  tlock.acquire()
  for i in range(len(conns)):
    conn = conns[i]
    conn.close()
  tlock.release()
  msconn.close()
  if (debug): print "\nExiting."
  alive = 0
  sys.exit(0)

if __name__ == '__main__':
  for i in range(len(sys.argv)):
    if (sys.argv[i] == "debug"):
      debug = 1
      continue
    if (sys.argv[i] == "nodebug"):
      debug = 0
      continue
    if (sys.argv[i] == "dblog"):
      dblog = 1
      continue
    if (sys.argv[i] == "nodblog"):
      dblog = 0
      continue
    if (sys.argv[i] == "hexdump"):
      hexdump = 1
      continue
    if (sys.argv[i] == "nohexdump"):
      hexdump = 0
      continue
    if (sys.argv[i] == "clients"):
      clients = 1
      continue
    if (sys.argv[i] == "noclients"):
      clients = 0
      continue
  pid = os.fork()
  if pid: sys.exit(0)
  alive = 1
  thread.start_new_thread(server_AGI,())
  if (debug): print 'Started Server AGI task '
  if (clients):
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.bind((HOST, CLIENTPORT))
    s.listen(MAXCONNECTIONS)
    while 1:
      conn, addr = s.accept()
      tlock.acquire()
      conns.append(conn)
      tlock.release()
      thread.start_new_thread(client_AGI,(conn,addr))
      if (debug): print '\nStarted Client AGI task ->',addr,"\n"
  else :
    while 1: 
      if (alive): time.sleep(10)
      else:
        if (debug): print "\nExiting."
        sys.exit(0)
