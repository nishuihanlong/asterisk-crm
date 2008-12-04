#<?
[database]
;
dbtype = mysql
dbhost = 127.0.0.1
dbname = astercc
dbport = 3306
username = root
password = 

tb_curchan = curcdr
tb_cdr = mycdr

[asterisk]

server = 127.0.0.1
port = 5038
username = admin
secret = amp111

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
allow = ulaw,alaw,g729,g723.1
context = yourcontext
dtmfmode=rfc2833

[system]

log_enabled = 0

;Log file path
log_file_path = /tmp/astercrmDebug.log

;
; Asterisk context parameter, use which context when dial in or dial out
;

;if set to 'admin', the index page will link to "manager login" page,
;else if set to 'user' defaulf page is user login page
useindex = admin

;context when dial out, in trixbox this could be from-internal
outcontext =  from-internal

; individual: set the limit in credit limit field to the call
; balance: set limit in balance to the call
creditlimittype = balance

upload_file_path = ./upload/

; astercc will refresh the balance of the group
; set to 0 if you dont want it refresh automaticly
refreshBalance = 0

; if we use history cdr(move the billed cdr to historycdr and read the cdr from historycdr)
useHistoryCdr = 1

; when we set useHistoryCDR = 1, then here set if we move the no answer cdr to historycdr
keepNoanswerCDR = 1

; if we set clid credit
setclid = 1

;set length of clid pin number, max 20; min 10.
pin_len = 10;

; not .conf need
; if you dont want astercc generate the conf for u, just leave this value blank
sipfile = /etc/asterisk/sip_astercc

; if require valid code when login
validcode = no
#?>