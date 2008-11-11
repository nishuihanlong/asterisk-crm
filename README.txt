====================================================================================

	asterCC  (C) 2006,2007,2008  Solo Fu  solo@astercc.org

====================================================================================
asterCC is a software package, in which we provide two asterisk solutions for now:

    * asterCRM, an open source contact center solution for asterisk
    * asterBilling, a realtime billing solution for asterisk, could be used for hosted callshop, asterisk pbx billing

all above in the package will use a linux daemon script named astercc, which could grab realtime CDR from asterisk, and it’s welcomed that if you want to develop your own application based astercc daemon.

The astercc daemon connect to asterisk via AMI(Asterisk Management Interface), so it could work with all kinds of asterisk solutions, and would not affect your original asterisk.

Here’re the benefits of solutions using astercc daemon

   1. brilliant performance

      we have tested that astercc could support more than 240 calls in asterisk
   2. good compatibility

      astercc could work with all asterisk based solutions, and it support both asterisk 1.2.X and 1.4.X
   3. distributed solution

      no need install astercc daemon on your asterisk server, even an embedded asterisk product could use astercc for expand

Installation:
 Rquirement:
	 httpd
	 mysql
	 mysql-devel
	 mysql-server
	 php (or php4)
	 php-mysql
	 php-gd

 A Auto install by 'install.sh'
  In this way, just to run 
	/bin/sh install.sh
  as root in astercc main directory and enter some parameter require by it.
  attention: You have to create dababase for astercc before run install.sh.

 B install manual
  1) Download and unzip the source (assuming your WEB root is /var/www/html)

	cd /var/www/html
	unzip astercc-X.X.zip
	mv astercc-X.X astercc
	
	/var/www/html/astercc/astercrm		# main directory and PHP scripts of astercrm
	/var/www/html/astercc/asterbilling		# main directory and PHP scripts of asterbilling	
	/var/www/html/astercc/sql			# sql to create database tables 
	/var/www/html/astercc/script 		# astercc daemon and some other script files 

	It is highly advised that the whole script directory be moved to a more secure
	location like /opt and out of the WEB root directory (in step 2)

  2) Create the MySQL database and tables, asterCRM need mysql 4.1 or above

	Note: here we create the database named astercc, it both used for astercrm and asterbilling,
	you could use whatever db name you want	use your configration to replace "yourmysqluser" 
	and "yourmysqlpasswd"

	mysqladmin -uyourmysqluser -pyourmysqlpasswd create astercc
	mysql -uyourmysqluser -pyourmysqlpasswd astercc < /var/www/html/astercc/sql/astercc.sql	

  3) Update /etc/asterisk/manager.conf to enable Manager connections

	Note:allow asterisk on different server
 
	Add something like this to the manager.conf file:

	[general]
	enabled = yes
	port = 5038
	bindaddr = 0.0.0.0
	;displayconnects = yes

	;the following line could be changed by yourself
	[astercc]
	secret = astercc
	read = system,call,log,verbose,command,agent,user
	write = system,call,log,verbose,command,agent,user
	deny=0.0.0.0/0.0.0.0
	; if you want to run astercc on another server
	; use your astercc ip to replace 127.0.0.1 or add a new line
	permit=127.0.0.1/255.255.255.0

  4) Create the directories and move daemon scripts:

	mkdir -p /opt/asterisk/scripts/astercc 
	mv /var/www/html/astercc/script/* /opt/asterisk/scripts/astercc
	chmod +x /opt/asterisk/scripts/astercc/eventsdaemon.pl
	chmod +x /opt/asterisk/scripts/astercc/eventdog.sh
	chmod +x /opt/asterisk/scripts/astercc/astercc
	chmod +x /opt/asterisk/scripts/astercc/astercctools
	chmod +x /opt/asterisk/scripts/astercc/dialer.pl
	chmod +x /opt/asterisk/scripts/astercc/cdr_move.pl
	chmod +x /opt/asterisk/scripts/astercc/asterrc
	chmod +x /opt/asterisk/scripts/astercc/astercclock
	chmod +x /opt/asterisk/scripts/astercc/asterccdaemon
	chmod +x /opt/asterisk/scripts/astercc/asterccd

  5) modify config file
	
	modity /var/www/html/astercc/astercrm/astercrm.conf.php to fit your configration
	modity /var/www/html/astercc/astercrm/astercc.conf.php to fit your configration

  6) Start Asterisk and daemon

	There are two daemon modes you can choose, 
	astercc mode or eventsdaemon(can be used for astercrm only) mode
	
	A for astercc mode(can be used for both astercrm and asterbilling)

	try start astercc:		
		modify /opt/asterisk/scripts/astercc/astercc.conf to fit your configuration
		mainly database setting and AMI setting.
		
		run astercc for test
		/opt/asterisk/scripts/eventsdaemon/astercc

	if you could read like following line:
		"Connecting to mysql database on 127.0.0.1:
		 Database connection successful.
		 Connecting to asterisk on 127.0.0.1 port 5038:
		 Asterisk socket connection successful.
		 Check asterisk username & secret:
		 Success
		 Monitor Start: 
		 ...(some log message)..."

	congratulations, your astercc works well, use 'ctrl + c' to exit
	or else, please check your database/AMI configration in astercc.conf

	Start up astercc (default settings):
		modify /var/www/html/astercrm/astercrm.conf.php set eventtype to curcdr
		/opt/asterisk/scripts/astercc/astercc -d

	Start up astercc daemons when system startup:
	Note: This option can only fit to redhat-release system. 
	If you want astercc daemons to start automatically when you boot your machine,
	you need to :

	cp /opt/asterisk/scripts/astercc/asterccd /etc/rc.d/init.d
	chmod 755 /etc/rc.d/init.d/asterccd
	chkconfig --add asterccd
	
	Advice: Configure your astercc restart once everyday, 
		it's not necessary, but it 's good for your astercc operation.
	for example: you want to restart astercc at 0'clock everyday,
		     just do the following line as root. 
	crontab -e
	add a new line: 
		0 0 * * * /etc/rc.d/init.d/asterccd restart 
	the first "0" figures minutes and the second "0" figures hours.

	B for eventsdaemon mode(can be used for astercrm only)
	try start eventsdaemon:
		modify eventsdaemon.pl to fit your configuration mainly database setting and AMI setting.
		/opt/asterisk/scripts/astercc/eventsdaemon.pl 	
	if you could read:

		"Message: Authentication accepted"

	congratulations, your eventsdaemon works well
	use ctrl + c to exit
	or else, please check your database/AMI configration in eventsdaemon.pl

	Start eventsdaemon (default settings):
		modify astercrm.conf set eventtype to event
		/opt/asterisk/scripts/astercc/eventsdaemon.pl -d

	At some point it may be desirable to delete unwanted events from the database
	table. The eventsdaemon is also designed for this.
	please check eventsdaemon.pl for parameter "log_life"

	also we provide a "watch dog", it would help you restart eventsdaemon when it shutdown
	add this shell to your start-up file, for example:

	echo /opt/asterisk/scripts/astercc/eventdog.sh >> /etc/rc.d/rc.local

	so that everytime your server start, eventsdaemon would be loaded

  7) set file&folder access 
	
	for astercrm
	chmod 777 /var/www/html/astercc/astercrm/upload
	chmod 777 /var/www/html/astercc/astercrm/astercrm.conf.php

	if asterisk and astercrm running in one server,you could make a soft link to 
	astercrm web directory for listening monitor records online. 
	ln -s /var/spool/asterisk/monitor/ /vavr/www/html/astercc/astercrm/monitor
	note: astercrm support listen monitors online only can be wav format file.

	for asterbilling
	chmod 777 /var/www/html/astercc/astercbilling/upload

  8) web browsing 
	for astercc guide
	http://localhost/astercc

	or http://YOUR-WEB-SERVER-ADDRESS/astercc

	for astercrm:
	http://localhost/astercc/astercrm 

	or http://YOUR-WEB-SERVER-ADDRESS/astercc/astercrm

	login with admin/admin

	for asterbilling:
	http://localhost/astercc/asterbilling

	or http://YOUR-WEB-SERVER-ADDRESS/astercc/asterbilling
	
	NOTE:There are two login interface in asterbilling, user mode and manager mode, the default setting is manger mode.
	You can visit user interface by http://YOUR-WEB-SERVER-ADDRESS/astercc/asterbilling/login.php 
	and visit mannger interface by http://YOUR-WEB-SERVER-ADDRESS/astercc/asterbilling/manager_login.php .
	You could change the default login mode in asterbilling.conf.php by prameter "useindex".

	also you can move astercrm and asterbilling directory to any path where your web server be allowed to access.
		
	login with admin/admin

	set your first booth by asterbilling

		1. go "Reseller" and add a reseller
		2. go "Group" add a group belong to the reseller
		3. go "Clid" add some clid for this group, then the account in asterisk with the clid would be billing as a user in this group
		4. go "Account" add a account, usertype could be "groupadmin" and belongs to the group you just added
		5. go "Rate to Customer" and add some rate for the group, if you dont select reseller or group, the rate could be the default rate all all resellers/groups
		6. login as groupadmin account, then you should see some box as the attachments.
		7. try make a call using the ip phone with the clid, you could see the calling and billing message in the box
	
====================================================================================
			asterCRM section 
====================================================================================

asterCRM is an open source contact center software for asterisk.

asterCRM scripts require a running WEB and MySQL server (in which stored asterisk events).
All of these processes can run on the same server, 
however, multiple servers enhance performance.

*Features

	1).	pop-up when incoming calls 
	2).	pop-up when outbound calls
	3).	suggestion when enter business name or contact 
	4).	account/extension manager
	5).	click to dial
	6).	manully dial
	7).	click to transfer
	8).	click to monitor
	9).	can be integrated with all asterisk based systems, 
			such as Elastix IP PBX, Trixbox ...
	10).	multi-language support (Chinese, English, German)
	11).	multi-skin support	
	12).	support external crm
	13).	extension status
	14).	show asterisk active channels 
	15).	predictive dialer
	16).	CRM data import/export online
	17).	diallist import online
	18).	survey
	19).	survey result statistics
	20).	distributed solution

*Manager Interface 

	1. Import
	2. Export
	3. Statisic
	4. Extension

		Binding astercrm account and asterisk extension here, 
		also manager could grant privileges to astercrm account.

		username:		username to log into astercrm
		password:		password to log into astercrm
		usertype:		only two levels for now. 
						If it's "admin" in this field, 
						which means this account could enter manager interface section.
		extension:		a extension would be binding to this user,
						which means all events of the extension would reflect this user
						such as dial, hangup, also pop-up
						digits only 
						(e.g. 8000)
		extensions:		which extensions the user could see status, and use click to transfer
						put several extensions here, use comma to seperate
						(e.g. 8000,8001,8002,8003)
	5. Customer

	6. Predictive Dialer

		Administrator could upload a phone list to table "diallist", so that astercrm know 
		which number could be dialed.
		there's are two field in table "diallist"
			phonenumber and assign
		About assign, it would be a extension number in this field, means this number could be dialed by which extension, 
		this phone number would appears in "agent dial mode"

		there're two mode in dialer setion:

			a. agent dial mode

		if phone number is assigned to extension in table "dial list", when agent login, 
		he could see a button "get a new number" and XX records in dial list, 
		then he can dial the button, a new table would pop up,
		and in this table would have a phone number, agent could click the number to make a call. 
		once agent click the button and a new table pop up with a new phone number, 
		the number would be
		deleted from database, so agent could see that the number in dial list would decrease, 
		until no records in database, and the button disappears.

			b. manager predial mode

		manager dial mode need a "admim" user, in predictive dialer module, 
		if there're records in table "diallist"
		(whatever assigned or not), he could see XXX records (means records number in database), 
		a "Dial" button and a inputbox (means maximum concurrent active channel in asterisk, 
		it would "pause" when asterisk active channels reach this number untill some channels 
		hang up dialer would "continue" automaticly), during
		this progress manger could stop/start the dialer or change max channels number. 
		In config.php you could set where the call would be sent to when answered, 
		such as a IVR, a queue or whatever you need.

	7. System 

		7.1
			You could see sip peer status here, 
			such as which peer is calling, 
				    which peer is online or offline

			Notice:
				some peer may not appear here, because system monitor read data from database
				so usually we can not read all sip peer status here

		7.2 you coudl see the result of cli command:
				sip show channels verbose
			this result refresh every one second.



	8. Survey

====================================================================================
			asterBilling section 
====================================================================================

asterBilling is a real time billing software. It could be used mainly for hosted callshop solution, or billing for office IP PBX system.

astercc features:

   1. work with all asterisk based system
   2. realtime billing
   3. prepaid/postpaid support
   4. maximum 240 simultaneous calls support
   5. booth lock/unlock
   6. booth/callshop/reseller credit limit
   7. mulit-callshop support in one system
   8. mulit-reseller support in one system
   9. each customer/callshop/reseller could use a rate template or use specific rate
  10. callback with credit limit support (billing lega/legb/both)
  11. reseller rate/callshop rate/customer rate three level billing
  12. admin/reseller/callshop admin/operator four user types
  13. rate import/export
  14. hangup calls
  15. web script part is open source
  16. profit calculate
  17. Grid layout
  18. CDR search/browser
  19. 5 free channels license


