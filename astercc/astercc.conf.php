[database]

dbtype = mysql
dbhost = 127.0.0.1
dbname = astercc
dbport = 3306
username = 
password = 

tb_curchan = curcdr
tb_cdr = mycdr

[asterisk]

server = 127.0.0.1
port = 5038
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

; individual: set the limit in credit limit field to the call
; balance: set limit in balance to the call
creditlimittype = balance

upload_file_path = ./upload/

; astercc will refresh the balance of the group
; set to 0 if you dont want it refresh automaticly
refreshBalance = 30

; not .conf need
; if you dont want astercc generate the conf for u, just leave this value blank
sipfile = /etc/asterisk/sip_astercc

; if require valid code when login
validcode = yes