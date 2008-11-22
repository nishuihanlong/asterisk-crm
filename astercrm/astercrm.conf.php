;<?
[database]
;
; Database connection parameter

dbtype = mysql

dbhost = 127.0.0.1
dbname = astercc
username = root
password = 

[asterisk]
;
; Asterisk connection parameter

server = 127.0.0.1
;should be matched in manager.conf
port = 5038
username = admin
secret = amp111

; Recorded file path, if you want to lisens records from web,
; the path must could be read by apache and allow php to exec
monitorpath = /var/spool/asterisk/monitor/

; gsm,wav
monitorformat = gsm


[system]

log_enabled = 0
;Log file path
log_file_path = /tmp/astercrmDebug.log

;path of astercc daemon
astercc_path = /opt/asterisk/scripts/astercc

; where astercrm get asterisk call events, set to curcdr when using astercc
; option: event, curcdr
eventtype = curcdr

;
; Asterisk context parameter, use which context when dial in or dial out
;

;context when dial out, in trixbox this could be from-internal
outcontext = from-internal

;context when dial in, in trixbox this could be from-trunk
incontext = from-internal

;if need to enter accountcode/password of admin or groupadmin when agent stop work
stop_work_verify = 0

; Asterisk context parameter, use which context and extenstion
; when predictive dialer connect the call
;

;predialer_context = from-siptrunk
;predialer_extension =


;
; astercrm wouldnot pop-up unless the length of callerid is greater than
; this number
;
phone_number_length = 0

;how many digits end of callerid remove when incoming call smart matching, disabled if set it to 0
smart_match_remove = 1
;

;time intervals of update event in pages
status_check_interval = 2
;

;
; if astercrm trim fellowing prefix, use gamma to sperate
; leave it blank if no prefix need to be removed

trim_prefix = 

;
; if your astercrm work on the same server with asterisk, set to true
; when astercrm start a call, it would drop a .call file to asterisk spool
; or else astercrm would use AMI command: Originate to start a call
;
allow_dropcall = 0

;
; if astercrm allow same customer name
;

allow_same_data = 0

; define what information would be displayed in portal page
; customer | note
portal_display_type = customer

;
; astercrm wouldnot pop-up when dial out unless this parameter is true
;
pop_up_when_dial_out = 1

;
; astercrm wouldnot pop-up when dial in unless this parameter is true
;
pop_up_when_dial_in = 1

;
; browser will maximize when pop up
;
browser_maximize_when_pop_up = 0

;
; which phone ring first when using click to dial
;caller | callee
firstring = caller

;
; astercrm will show contact
;
enable_contact = 

upload_file_path = ./upload/

;
; astercrm will use external crm software if this parameter is true
;
enable_external_crm = 0

;
; asterCRM will open a new browser window when need popup
;
open_new_window = 0

;
; when using external crm, put default page here
;
external_crm_default_url = http://astercc.org

;
; when using external crm, put pop up page here
; %callerid		callerid
; %calleeid		calleeid
; %method		dialout or dialin
;
external_crm_url = http://astercc.org/index.php?callerid=%callerid&calleeid=%calleeid&method=%method

detail_level = all

astercc_conf_path = 
[google-map]

key = 
;?>
