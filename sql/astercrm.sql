#version 0.047

#DROP DATABASE astercrm;

#CREATE DATABASE astercrm;

#USE astercrm;

DROP TABLE IF EXISTS `astercrm_account`;

CREATE TABLE `astercrm_account` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(30) NOT NULL default '',
  `password` varchar(30) NOT NULL default '',
  `extension` varchar(30) NOT NULL default '',
  `extensions` varchar(200) NOT NULL default '',
  `channel` varchar(30) NOT NULL default '',
  `usertype` varchar(20) NOT NULL default '',
  `accountcode` varchar(20) NOT NULL default '',
  `groupid` int(11) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM;

DROP TABLE IF EXISTS astercrm_accountgroup;

CREATE TABLE `astercrm_accountgroup` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`groupname` VARCHAR( 100 ) NOT NULL ,
	`groupid` INT NOT NULL ,
	`pdcontext` VARCHAR( 30 ) NOT NULL  ,
	`pdextension` VARCHAR( 30 ) NOT NULL  ,
	`cretime` datetime NOT NULL default '0000-00-00 00:00:00',
	`creby` varchar(50) NOT NULL default '',
	UNIQUE (
	`groupid` 
	)
) ENGINE = MYISAM ;

DROP TABLE IF EXISTS campaign;

CREATE TABLE `campaign` ( #added by solo 2008-2-5
  `id` int(11) NOT NULL auto_increment,
  `groupid` int(11) NOT NULL default '0',
  `campaignname` varchar(60) NOT NULL default '',
  `campaignnote` varchar(255) NOT NULL default '',
  `outcontext` varchar(60) NOT NULL default '',
  `incontext` varchar(60) NOT NULL default '',
  `inexten` varchar(30) NOT NULL default '',
  `fileid` int(11) NOT NULL default '0',		#added by solo 2008-5-4
  `end-fileid` int(11) NOT NULL default '0',		#added by solo 2008-5-4
  `phonenumber` varchar(255) NOT NULL default '',	#added by solo 2008-5-4
  `creby` varchar(30) NOT NULL default '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS contact;

CREATE TABLE contact (
  id int(11) NOT NULL auto_increment,
  contact varchar(30) NOT NULL default '',
  gender varchar(10) NOT NULL default 'unknown',	#add 2007-10-5 by solo
  position varchar(100) NOT NULL default '',
  phone varchar(50) NOT NULL default '',
  ext varchar(8) NOT NULL default '',
  phone1 varchar(50) NOT NULL default '',
  ext1 varchar(8) NOT NULL default '',
  phone2 varchar(50) NOT NULL default '',
  ext2 varchar(8) NOT NULL default '',
  mobile varchar(50) NOT NULL default '',
  fax varchar(50) NOT NULL default '',
  email varchar(100) NOT NULL default '',
  cretime datetime NOT NULL default '0000-00-00 00:00:00',
  creby varchar(30) NOT NULL default '',
  customerid int(11) NOT NULL default '0',
  groupid INT NOT NULL ,
  UNIQUE KEY id (id)
) ENGINE = MYISAM;

DROP TABLE IF EXISTS customer;

CREATE TABLE customer (
  id int(11) NOT NULL auto_increment,
  customer varchar(120) NOT NULL default '',
  address varchar(200) NOT NULL default '',
  zipcode varchar(10) NOT NULL default '',
  website varchar(100) NOT NULL default '',
  category varchar(20) NOT NULL default '',
  city	varchar(50) NOT NULL default '',	#add 2007-9-30 by solo
  state varchar(50) NOT NULL default '',	#add 2007-9-30 by solo
  phone varchar(50) NOT NULL default '',	#add 2007-9-30 by solo
  fax	varchar(50) NOT NULL default '',	#add 2007-10-24 by solo
  mobile varchar(50) NOT NULL default '',	#add 2007-10-24 by solo
  email varchar(50) NOT NULL default '',	#add 2007-10-24 by solo
  contact varchar(50) NOT NULL default '',	#add 2007-9-30 by solo
  contactgender varchar(10) NOT NULL default 'unknown',	#add 2007-10-5 by solo
  bankname		varchar(100) NOT NULL default '',	#add 2007-10-15 by solo
  bankaccount	varchar(100) NOT NULL default '',	#add 2007-10-15 by solo
  bankzip		varchar(100) NOT NULL default '',	#add 2007-10-26 by solo
  bankaccountname	varchar(100) NOT NULL default '',	#add 2007-10-25 by solo
  cretime datetime NOT NULL default '0000-00-00 00:00:00',
  creby varchar(30) NOT NULL default '',
  groupid INT NOT NULL ,
  UNIQUE KEY id (id)
) ENGINE = MYISAM;

DROP TABLE IF EXISTS dialedlist;

CREATE TABLE dialedlist (
  `id` int(11) NOT NULL auto_increment,
  `dialednumber` varchar(30) NOT NULL default '',
  `answertime` datetime NOT NULL default '0000-00-00 00:00:00',		#added by solo 2008-2-1
  `duration` int(11) NOT NULL default '0',												#added by solo 2008-2-1
  `transfertime` int(11) NOT NULL default '0',				#added by solo 2008-5-4										#added by solo 2008-2-1
  `response` varchar(20) NOT NULL default '',											#added by solo 2008-2-1
  `uniqueid` varchar(20) NOT NULL default '',											#added by solo 2008-2-1
  `groupid` INT NOT NULL DEFAULT '0',															#added by solo 2008-2-3
  `campaignid` INT NOT NULL DEFAULT 0,														#added by solo 2008-2-5
  `assign` varchar(20) NOT NULL default '',												#added by solo 2008-2-10
  `dialedby` varchar(30) NOT NULL default '',
  `dialedtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM;

DROP TABLE IF EXISTS diallist;

#store Predictive dialer phone list
CREATE TABLE `diallist` (
  `id` int(11) NOT NULL auto_increment,
  `dialnumber` varchar(30) NOT NULL default '',
  `dialtime` datetime NOT NULL default '0000-00-00 00:00:00',		#added by solo 2008/05/04
  `assign` varchar(20) NOT NULL default '',
  `status` varchar(50) NOT NULL default '',				#added by solo 2008/05/04
  `groupid` INT(11) NOT NULL DEFAULT '0',				#added by solo 2007-12-17
  `trytime` INT(11) NOT NULL DEFAULT '0',				#added by solo 2008/05/04
  `campaignid` INT NOT NULL DEFAULT 0,					#added by solo 2008-2-5
  `creby`	varchar(50) NOT NULL default '',			#added by solo 2008-1-15
  `cretime`	datetime NOT NULL default '0000-00-00 00:00:00',	#added by solo 2008-1-15
  UNIQUE KEY id (id)
) ENGINE = MYISAM;

DROP TABLE IF EXISTS events;

CREATE TABLE `events` (
  `id` int(16) NOT NULL auto_increment,
  `timestamp` datetime default NULL,
  `event` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `timestamp` (`timestamp`),
  KEY `event` (`event`)
) ENGINE=HEAP;


DROP TABLE IF EXISTS survey;

CREATE TABLE `survey` (
  `id` int(11) NOT NULL auto_increment,
  `surveyname` varchar(50) NOT NULL default '',
  `surveynote` varchar(255) NOT NULL default '',							#add 2008-1-11 by solo
  `enable` smallint(6) NOT NULL default '0',									#add 2007-10-15 by solo
  `groupid` int(11) NOT NULL default '0',											#added by solo 2008-1-15
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  `creby` varchar(50) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM;

DROP TABLE IF EXISTS surveyoptions;

CREATE TABLE `surveyoptions` (
  `id` int(11) NOT NULL auto_increment,
  `surveyoption` varchar(50) NOT NULL default '',
  `optionnote` varchar(255) NOT NULL default '',							#added by solo 2008-1-14
  `surveyid` int(11) NOT NULL default '0',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  `creby` varchar(30) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM;

DROP TABLE IF EXISTS surveyresult;

CREATE TABLE `surveyresult` (
  `id` int(11) NOT NULL auto_increment,
  `customerid` int(11) NOT NULL default '0',
  `contactid` int(11) NOT NULL default '0',
  `surveyid` int(11) NOT NULL default '0',
  `surveyoption` varchar(50) NOT NULL default '',
  `surveynote` text NOT NULL,
  `groupid` int(11) NOT NULL default '0',
  `creby` varchar(50) NOT NULL default '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM;

DROP TABLE IF EXISTS note;

CREATE TABLE `note` (
  `id` int(11) NOT NULL auto_increment,
  `note` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `priority` int(11) NOT NULL default '0',
  `attitude` int(11) NOT NULL default '0',												#add 2007-10-26 by solo
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  `creby` varchar(30) NOT NULL default '',
  `customerid` int(11) NOT NULL default '0',
  `contactid` int(11) NOT NULL default '0',
  `groupid` int(11) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM;

DROP TABLE IF EXISTS remind;

CREATE TABLE remind (
  id int(11) NOT NULL auto_increment,
  title varchar(100) NOT NULL default '', #标题
  content text NOT NULL default '',       #内容
  remindtime  datetime NOT NULL default '0000-00-00 00:00:00', #提醒时间
  remindtype int(10) not null default 0 , #提醒类别，0为发给自己，1为发给别人
  priority int(10) NOT NULL default 0, #紧急程度,5为普通,10为紧急 
  username varchar(50) not  null default '' , #用户名
  remindabout varchar(255) not  null default '',      #提醒的相关内容
  readed  int(10) not null default 0 , #是否读取，0为未读，1为已读
  touser  varchar(50) not null default '', #发给谁
  creby  varchar(50) NOT NULL default '',
  cretime datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY id (id)
)ENGINE = MYISAM;

DROP TABLE IF EXISTS monitorrecord;

CREATE TABLE `monitorrecord` (
`id` INT NOT NULL AUTO_INCREMENT ,
`callerid` VARCHAR( 20 ) NOT NULL ,
`filename` VARCHAR( 60 ) NOT NULL ,
`groupid` INT NOT NULL ,
`extension` VARCHAR( 30 ) NOT NULL ,
`uniqueid` varchar(20) NOT NULL default '',
`creby` VARCHAR( 30 ) NOT NULL ,
`cretime` DATETIME NOT NULL ,
UNIQUE (
`id`
),
KEY `monitorid`(`uniqueid`,`filename`,`creby`)
) ENGINE = MYISAM ;

DROP TABLE IF EXISTS trunkinfo;

CREATE TABLE `trunkinfo` (
`id` INT NOT NULL AUTO_INCREMENT ,
`trunkname` VARCHAR( 50 ) NOT NULL ,
`trunkchannel` VARCHAR( 50 ) NOT NULL ,
`trunknote` TEXT NOT NULL ,
`creby` VARCHAR( 50 ) NOT NULL ,
`cretime` DATETIME NOT NULL ,
INDEX ( `trunkchannel` ) ,
UNIQUE (
`id` 
)
) ENGINE = MYISAM ;

DROP TABLE IF EXISTS asteriskcalls;

CREATE TABLE `asteriskcalls` (
`id` INT NOT NULL AUTO_INCREMENT ,
`asteriskcallsname` VARCHAR( 50 ) NOT NULL ,
`outcontext` VARCHAR( 50 ) NOT NULL ,
`incontext` VARCHAR( 50 ) NOT NULL ,
`inextension` VARCHAR( 50 ) NOT NULL ,
`groupid` INT NOT NULL ,
`cretime` DATETIME NOT NULL ,
`creby` VARCHAR( 50 ) NOT NULL ,
UNIQUE (
`id` 
)
) ENGINE = MYISAM ;

DROP TABLE IF EXISTS remindercalls;

CREATE TABLE `remindercalls` (
`id` INT NOT NULL AUTO_INCREMENT ,
`customerid` INT NOT NULL ,
`contactid` INT NOT NULL ,
`phonenumber` VARCHAR( 50 ) NOT NULL ,
`asteriskcallsid` INT NOT NULL ,
`creby` VARCHAR( 50 ) NOT NULL ,
`cretime` DATETIME NOT NULL ,
`note` VARCHAR( 255 ) NOT NULL ,
`result` VARCHAR( 255 ) NOT NULL ,
`groupid` INT NOT NULL ,
`dialtime` DATETIME NOT NULL ,
`status` VARCHAR( 50 ) NOT NULL ,
UNIQUE (
`id` 
)
) ENGINE = MYISAM ;


INSERT INTO `astercrm_account` (
`id` ,
`username` ,
`password` ,
`extension` ,
`extensions` ,
`usertype` 
)
VALUES (
NULL , 'admin', 'admin', '0000', '', 'admin'
);



-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `curcdr` (
  `id` int(11) NOT NULL auto_increment,
  `src` varchar(20) NOT NULL default '',
  `dst` varchar(20) NOT NULL default '',
  `srcchan` varchar(100) NOT NULL default '',
  `dstchan` varchar(100) NOT NULL default '',
  `starttime` datetime NOT NULL default '0000-00-00 00:00:00',
  `answertime` datetime NOT NULL default '0000-00-00 00:00:00',
  `srcuid` varchar(20) NOT NULL default '',
  `dstuid` varchar(20) NOT NULL default '',
  `disposition` varchar(10) NOT NULL default '',
  `userid` int(11) NOT NULL default '0',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `credit` double(24,4) NOT NULL default '0.0000',
  `callshopcredit` double(24,4) NOT NULL default '0.0000',
  `resellercredit` double(24,4) NOT NULL default '0.0000',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  `destination` varchar(100) NOT NULL default '',
  `memo` varchar(100) NOT NULL default '',
  UNIQUE KEY `id` (`id`),
  KEY `srcid` (`src`,`dst`,`srcchan`,`dstchan`,`srcuid`,`dstuid`,`disposition`)
) ENGINE=HEAP;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `mycdr` (
  `id` int(11) NOT NULL auto_increment,
  `calldate` datetime NOT NULL default '0000-00-00 00:00:00',
  `src` varchar(80) NOT NULL default '',
  `dst` varchar(80) NOT NULL default '',
  `channel` varchar(80) NOT NULL default '',
  `dstchannel` varchar(80) NOT NULL default '',
  `duration` int(11) NOT NULL default '0',
  `billsec` int(11) NOT NULL default '0',
  `disposition` varchar(45) NOT NULL default '',
  `accountcode` varchar(20) NOT NULL default '',
  `userfield` varchar(255) NOT NULL default '',
  `srcuid` varchar(20) NOT NULL default '',
  `dstuid` varchar(20) NOT NULL default '',
  `calltype` varchar(255) NOT NULL default '',
  `credit` double(24,4) NOT NULL default '0.0000',
  `callshopcredit` double(24,4) NOT NULL default '0.0000',
  `resellercredit` double(24,4) NOT NULL default '0.0000',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `destination` varchar(100) NOT NULL default '',
  `memo` varchar(100) NOT NULL default '',
  UNIQUE KEY `id` (`id`),
  KEY `srcid` (`src`,`dst`,`channel`,`duration`,`billsec`,`disposition`)
) ENGINE=MyISAM;

-- --------------------------------------------------------