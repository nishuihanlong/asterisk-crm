#!/bin/env python

#
# Copyright (C) 2006 Earl Terwilliger
#               EMAIL: earl@micpc.com
#
# Version 1.2  11-22-2006

import sys,os,string,time

def get_processes():
  output = []
  ps = os.popen('ps -eo pid,command')
  ps.readline()
  for line in ps:
    parts  = line.lstrip()[:-1].split(' ')
    output.append( (int(parts[0]), ' '.join(parts[1:])) )
  return output

def find_cmd(cmd):
  parts = cmd.split('/')
  l = len(parts)
  if (l > 0): 
    cp = parts[l-1].split(' ')
  else :
    cp = cmd.split(' ')
  return cp[0]

def check_process(cmd):
  cnt = 0
  ids = get_processes()
  rcmd = find_cmd(cmd)
  for i in range(len(ids)):
    ps =  find_cmd(ids[i][1])
    if ps == rcmd: cnt += 1
  return cnt

def kill_process(cmd):
  ids = get_processes()
  rcmd = find_cmd(cmd)
  for i in range(len(ids)):
    ps =  find_cmd(ids[i][1])
    if ps == rcmd:
      killcmd = "kill -9 " + str(ids[i][0])
      print killcmd
      os.system(killcmd)

def start_process(cmd,parms):
  if os.path.isfile(cmd):
    cnt = check_process(cmd)
    if cnt == 0: 
      os.system(cmd + parms)
      print "Started: ",cmd,parms

if __name__ == '__main__':
  cnt = check_process(sys.argv[0])
  if (cnt > 1):
    print "Exiting.. already running!"
    sys.exit(0)
  pid = os.fork()
  if pid: sys.exit(0)
  while(1):
    cnt = check_process('/usr/sbin/asterisk')
    if cnt == 0: 
      kill_process('/opt/asterisk/scripts/events/ProxyMan.py')
      start_process('/usr/sbin/asterisk',' -p')
      time.sleep(1)
      start_process('/opt/asterisk/scripts/events/ProxyMan.py','')
    time.sleep(10)
