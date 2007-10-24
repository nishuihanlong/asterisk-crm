[database]
;
; Database connection parameter 

dbtype		= "mysql"	;Database type 

dbhost		= "localhost"
dbname		= "asterisk"	
username	= ""
password	= ""

[asterisk]
;
; Asterisk connection parameter 
 
server		= "127.0.0.1"	
;should be matched in manager.conf
port		= ""	
username	= ""
secret		= ""	

; Recorded file path
monitorpath	= "/var/spool/asterisk/monitor/"	

; gsm,wav,wav49 
monitorformat	= "gsm"


[system]

log_enabled		= "1"
;Log file path
log_file_path	= "/tmp/astercrmDebug.log"	

; 
; Asterisk context parameter, use which context when dial in or dial out
; 

;context when dial out, in trixbox this could be from-internal
outcontext		= "from-internal"

;context when dial in, in trixbox this could be from-trunk
incontext		= "from-internal"	



; Asterisk context parameter, use which context and extenstion 
; when predictive dialer connect the call
; 

preDialer_context	= "from-trunk"
preDialer_extension	= "1"


;
; astercrm wouldnot pop-up unless the length of callerid is greater than 
; this number
;

phone_number_length	= "6"	

;
; if astercrm trim the 0 in the phonenumber before search for customer/contact in database
;

trim_zreo			= "1"

;
; if your astercrm work on the same server with asterisk, set to true
; when astercrm start a call, it would drop a .call file to asterisk spool 
; or else astercrm would use AMI command: Originate to start a call
;
allow_dropcall		= "1"

;
; if astercrm allow same customer name
;

allow_same_data		= "1"


;
; astercrm wouldnot pop-up when dial out unless this parameter is true
;
pop_up_when_dial_out	= "1"

;
; astercrm wouldnot pop-up when dial in unless this parameter is true
;
pop_up_when_dial_in		= "1"

;
; browser will maximize when pop up
;
maximize_when_pop_up	= "1"

;
; which phone ring first when using click to dial
;

;caller | callee
firstring				= "caller"

;
; astercrm will use external crm software if this parameter is true
;
enable_external_crm		= "0"

;
; asterCRM will open a new browser window when need popup
;
open_new_window			= "0"

;
; when using external crm, put default page here
;
external_crm_default_url	= "http://www.magiclink.cn"

;
; when using external crm, put pop up page here
; %callerid		callerid				
; %calleeid		calleeid				
; %method		dialout or dialin	
;
external_crm_url = "http://www.magiclink.cn/index.html?callerid=%callerid&calleeid=%calleeid&method=%method"

upload_excel_path = "./upload/"