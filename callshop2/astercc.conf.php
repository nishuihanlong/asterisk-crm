[database]

dbtype = mysql
dbhost = 127.0.0.1
dbname = callshop
dbport = 3306
username = root
password = 

tb_curchan = curcdr
tb_cdr = mycdr

[asterisk]

server = 
port = 
username = 
secret = 

[licence]

licenceto = free demo
key = 
channel = 5

[sipbuddy]
type = friend
host = dynamic
insecure = very
canreinvite = no
nat = yes
disallow = all
allow = g729
allow = g723.1


[system]

log_enabled = 0

;Log file path
log_file_path = /tmp/astercrmDebug.log

;
; Asterisk context parameter, use which context when dial in or dial out
;

;context when dial out, in trixbox this could be from-internal
outcontext =  callshop

;context when dial in, in trixbox this could be from-internal
incontext = callshop

upload_file_path = ./upload/
