#!/usr/local/bin/python

import sys,datetime,MySQLdb

db = MySQLdb.connect(host="localhost",
                     user="asteriskuser",
                     passwd="",
                     db="")

cursor = db.cursor()
cdate = datetime.datetime.now()
mydate = cdate - datetime.timedelta(minutes=5)
cmd = "DELETE FROM events WHERE timestamp <  '" + str(mydate) + "'"
cursor.execute(cmd)
sys.exit()