
###########   Table structure for asterCC database   ################
###########   astercc current version: 0.12          ################

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

## 
## database: `astercc`
## 

############ For astercc ####################################

#############################################################


## 
## table `servers`
## 

DROP TABLE IF EXISTS `servers`;
CREATE TABLE `servers` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `ip` varchar(80) NOT NULL default '',
  `port` varchar(6) NOT NULL default '',
  `username` varchar(30) NOT NULL default '',
  `secret` varchar(30) NOT NULL default '',
  `note` varchar(250) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## 
## table `account`
## 

DROP TABLE IF EXISTS `account`;

CREATE TABLE `account` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(30) NOT NULL default '',
  `password` varchar(30) NOT NULL default '',
  `usertype` varchar(20) NOT NULL default '',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `accountcode` varchar(20) NOT NULL default '',
  `callback` varchar(10) NOT NULL default '',
  UNIQUE KEY `id` (`id`,`username`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

###########################################################

INSERT INTO `account` (
`id` ,
`username` ,
`password` ,
`usertype` ,
`addtime`
)
VALUES (
NULL , 'admin', 'admin', 'admin' , now()
);


##########################################################

## 
## table `accountgroup`
## 

DROP TABLE IF EXISTS `accountgroup`;

CREATE TABLE `accountgroup` (
  `id` int(11) NOT NULL auto_increment,
  `groupname` varchar(30) NOT NULL default '',
  `grouptitle` varchar(50) NOT NULL default '',
  `grouptagline` varchar(80) NOT NULL default '',
  `grouplogo` varchar(30) NOT NULL default '',
  `grouplogostatus` int(1) NOT NULL default 1,
  `accountcode` varchar(20) NOT NULL default '',
  `allowcallback` varchar(10) NOT NULL default '',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  `limittype` varchar(10) NOT NULL default '',
  `group_multiple` double(8,4) NOT NULL default '1.0000',
  `customer_multiple` double(8,4) NOT NULL default '1.0000',
  `curcredit` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_clid` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_group` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_reseller` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `billingtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `resellerid` int(11) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `callback`
## 

DROP TABLE IF EXISTS `callback`;

CREATE TABLE `callback` (
  `id` int(11) NOT NULL auto_increment,
  `lega` varchar(30) NOT NULL default '0',
  `legb` varchar(30) NOT NULL default '',
  `credit` double(24,4) NOT NULL default '0.0000',
  `groupid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  KEY `leg` (`lega`,`legb`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## ########################################################

## 
## table `callshoprate`
## 

DROP TABLE IF EXISTS `callshoprate`;

CREATE TABLE `callshoprate` (
  `id` int(11) NOT NULL auto_increment,
  `dialprefix` varchar(20) NOT NULL default '',
  `numlen` int(11) NOT NULL default '0',
  `destination` varchar(100) NOT NULL default '',
  `connectcharge` double(24,4) NOT NULL default '0.0000',
  `initblock` int(11) NOT NULL default '0',
  `rateinitial` double(24,4) NOT NULL default '0.0000',
  `billingblock` int(11) NOT NULL default '0',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  UNIQUE rate (dialprefix,numlen,resellerid,groupid),
  KEY `dialprefix` (`dialprefix`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## ########################################################

## 
## table `clid`
## 

DROP TABLE IF EXISTS `clid`;

CREATE TABLE `clid` (
  `id` int(11) NOT NULL auto_increment,
  `clid` varchar(20) NOT NULL default '',
  `pin` varchar(30) NOT NULL default '',
  `creditlimit` DOUBLE NOT NULL default '0.0000',
  `curcredit` DOUBLE NOT NULL default '0.0000',
  `limittype` VARCHAR( 10 ) NOT NULL,
  `credit_clid` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_group` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_reseller` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `display` varchar(20) NOT NULL default '',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '1',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `billingtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `pin` (`pin`),
  UNIQUE KEY `clid` (`clid`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


## ########################################################

## 
## table `myrate`
## 

DROP TABLE IF EXISTS `myrate`;

CREATE TABLE `myrate` (
  `id` int(11) NOT NULL auto_increment,
  `dialprefix` varchar(20) NOT NULL default '',
  `numlen` int(11) NOT NULL default '0',
  `destination` varchar(100) NOT NULL default '',
  `connectcharge` double(24,4) NOT NULL default '0.0000',
  `initblock` int(11) NOT NULL default '0',
  `rateinitial` double(24,4) NOT NULL default '0.0000',
  `billingblock` int(11) NOT NULL default '0',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  UNIQUE rate (dialprefix,numlen,resellerid,groupid),
  KEY `dialprefix` (`dialprefix`),
  INDEX `destination` (`destination`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## ########################################################

## 
## table `resellergroup`
## 

DROP TABLE IF EXISTS `resellergroup`;

CREATE TABLE `resellergroup` (
  `id` int(11) NOT NULL auto_increment,
  `resellername` varchar(30) NOT NULL default '',
  `accountcode` varchar(20) NOT NULL default '',
  `allowcallback` varchar(10) NOT NULL default '',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  `curcredit` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `limittype` varchar(10) NOT NULL default '',
  `multiple` double(8,4) NOT NULL default '1.0000',
  `credit_clid` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_group` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_reseller` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `billingtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `epayment_account` varchar(255) NOT NULL default '',                 
  `epayment_status` enum('enable','disable') NOT NULL default 'disable',
  `epayment_item_name` varchar(30) NOT NULL default '',     
  `epayment_identity_token` varchar(255) NOT NULL default '',           
  `epayment_amount_package` varchar(30) NOT NULL default '',            
  `epayment_notify_mail` varchar(60) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


## ########################################################

## 
## table `resellerrate`
## 

DROP TABLE IF EXISTS `resellerrate`;

CREATE TABLE `resellerrate` (
  `id` int(11) NOT NULL auto_increment,
  `dialprefix` varchar(20) NOT NULL default '',
  `numlen` int(11) NOT NULL default '0',
  `destination` varchar(100) NOT NULL default '',
  `connectcharge` double(24,4) NOT NULL default '0.0000',
  `initblock` int(11) NOT NULL default '0',
  `rateinitial` double(24,4) NOT NULL default '0.0000',
  `billingblock` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  UNIQUE rate (dialprefix,numlen,resellerid),
  KEY `dialprefix` (`dialprefix`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## ########################################################

## 
## table `callshop_customers`
## 

DROP TABLE IF EXISTS `callshop_customers`;

CREATE TABLE `callshop_customers` (
  `id` int(11) NOT NULL auto_increment,
  `pin` varchar(30) NOT NULL default '',
  `first_name` varchar(50) NOT NULL default '',
  `last_name` varchar(50) NOT NULL default '',
  `amount` double(24,4) NOT NULL default '0.0000',
  `discount` double(8,4) NOT NULL default -1,
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  UNIQUE `pin` (`pin`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## ########################################################

## 
## table `discount`
## 

DROP TABLE IF EXISTS `discount`;

CREATE TABLE `discount` (
  `id` int(11) NOT NULL auto_increment,
  `amount` double(24,4) NOT NULL default '0.0000',  
  `discount` double(8,4) NOT NULL default '0.0000',  
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  UNIQUE `amount` (`amount`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


## ########################################################

## 
## table `credithistory`
## 

DROP TABLE IF EXISTS `credithistory`;

CREATE TABLE `credithistory` (
  `id` int(11) NOT NULL auto_increment,
  `modifytime` datetime NOT NULL default '0000-00-00 00:00:00',
  `resellerid` int(11) NOT NULL default '0',
  `groupid` int(11) NOT NULL default '0',
  `clidid` int(11) NOT NULL default '0',
  `srccredit` double(24,4) NOT NULL default '0.0000',
  `modifystatus` varchar(20) NOT NULL default '',
  `modifyamount` double(24,4) NOT NULL default '0.0000',
  `comment` varchar(20) NOT NULL default '',
  `epayment_txn_id` varchar(60) NOT NULL default '',
  `operator` varchar(20) NOT NULL default '',
  UNIQUE KEY `id` (`id`),
  key `resellerid` (`resellerid`,`groupid`,`clidid`,`modifytime`,`modifystatus`,`modifyamount`,`operator`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


###############################################################

############### Both for astercc and astercrm #################

###############################################################

## 
## table `peerstatus`
## 

DROP TABLE IF EXISTS `peerstatus`;

CREATE TABLE `peerstatus` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `status` VARCHAR( 50 ) NOT NULL ,
 `peer` VARCHAR( 100 ) NOT NULL ,
 `lastupdate` DATETIME NOT NULL ,
UNIQUE (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## ########################################################

## 
## table `curcdr`
## 

DROP TABLE IF EXISTS `curcdr`;

CREATE TABLE `curcdr` (
  `id` int(11) NOT NULL auto_increment,
  `src` varchar(20) NOT NULL default '',
  `dst` varchar(20) NOT NULL default '',  
  `srcchan` varchar(100) NOT NULL default '',
  `dstchan` varchar(100) NOT NULL default '',
  `didnumber` varchar(30) NOT NULL default '',
  `starttime` datetime NOT NULL default '0000-00-00 00:00:00',
  `answertime` datetime NOT NULL default '0000-00-00 00:00:00',
  `srcuid` varchar(40) NOT NULL default '',
  `dstuid` varchar(40) NOT NULL default '',
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
  KEY `srcid` (`src`,`dst`,`didnumber`,`srcchan`,`dstchan`,`srcuid`,`dstuid`,`disposition`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `parkedcalls`;

 CREATE TABLE `parkedcalls` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Num` VARCHAR( 10 ) NOT NULL ,
`Channel` VARCHAR( 50 ) NOT NULL ,
`Context` VARCHAR( 50 ) NOT NULL ,
`Extension` VARCHAR( 50 ) NOT NULL ,
`Pri` VARCHAR( 50 ) NOT NULL ,
`Timeout` VARCHAR( 10 ) NOT NULL ,
PRIMARY KEY ( `id` ) ,
UNIQUE (
`id`
)
) ENGINE = HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci; 

## ########################################################

## 
## table `mycdr`
## 

DROP TABLE IF EXISTS `mycdr`;

CREATE TABLE `mycdr` (
  `id` int(11) NOT NULL auto_increment,
  `calldate` datetime NOT NULL default '0000-00-00 00:00:00',
  `src` varchar(30) NOT NULL default '',
  `dst` varchar(30) NOT NULL default '',  
  `channel` varchar(50) NOT NULL default '',
  `dstchannel` varchar(50) NOT NULL default '',
  `didnumber` varchar(30) NOT NULL default '',
  `duration` int(11) NOT NULL default '0',
  `billsec` int(11) NOT NULL default '0',
  `disposition` varchar(45) NOT NULL default '',
  `accountcode` varchar(20) NOT NULL default '',
  `userfield` varchar(255) NOT NULL default '',
  `srcuid` varchar(40) NOT NULL default '',
  `dstuid` varchar(40) NOT NULL default '',
  `calltype` varchar(255) NOT NULL default '',
  `credit` double(24,4) NOT NULL default '0.0000',
  `callshopcredit` double(24,4) NOT NULL default '0.0000',
  `resellercredit` double(24,4) NOT NULL default '0.0000',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `destination` varchar(100) NOT NULL default '',
  `memo` varchar(100) NOT NULL default '',
  `customerid` int(11) NOT NULL default 0,
  `discount` double(8,4) NOT NULL default '0.0000',
  `payment`  varchar(15) NOT NULL default '',
  UNIQUE KEY `id` (`id`),
  INDEX `customerid` (`customerid`),
  KEY `srcid` (`src`,`dst`,`channel`,`didnumber`,`dstchannel`,`duration`,`billsec`,`disposition`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

########################################################

## 
## table `historycdr`
## 

DROP TABLE IF EXISTS `historycdr`;

CREATE TABLE `historycdr` (
  `id` int(11) NOT NULL auto_increment,
  `calldate` datetime NOT NULL default '0000-00-00 00:00:00',
  `src` varchar(30) NOT NULL default '',
  `dst` varchar(30) NOT NULL default '',
  `channel` varchar(50) NOT NULL default '',
  `dstchannel` varchar(50) NOT NULL default '',
  `didnumber` varchar(30) NOT NULL default '',
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
  `customerid` int(11) NOT NULL default 0,
  `discount` double(8,4) NOT NULL default '0.0000',
  `payment`  varchar(15) NOT NULL default '',
  UNIQUE KEY `id` (`id`),
  KEY `srcid` (`src`,`dst`,`channel`,`didnumber`,`dstchannel`,`duration`,`billsec`,`disposition`),
  INDEX `dst` (`dst`),
  INDEX `destination` (`destination`),
  INDEX `calldate` (`calldate`),
  INDEX `customerid` (`customerid`),
  INDEX `resellerid` (`resellerid`),
  INDEX `groupid` (`groupid`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

############### For astercrm ###################################

##########################################################

## 
## table `agentlogin_history`
## 

DROP TABLE IF EXISTS `agentlogin_history`;

CREATE TABLE `agentlogin_history` (
 `agent` varchar(30) NOT NULL default '',
 `channel` varchar(30) NOT NULL default '',
 `agentlogin` datetime NOT NULL default '0000-00-00 00:00:00',
 `agentlogout` datetime NOT NULL default '0000-00-00 00:00:00',
 `uniqueid` varchar(15) NOT NULL,
 `online` int(11) NOT NULL default '0'
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## 
## table `astercrm_account`
## 

DROP TABLE IF EXISTS `astercrm_account`;

CREATE TABLE `astercrm_account` (
 `id` int(11) NOT NULL auto_increment,
 `username` varchar(30) NOT NULL default '',
 `password` varchar(30) NOT NULL default '',
 `firstname` varchar(15) NOT NULL default '',		
 `lastname` varchar(15) NOT NULL default '',		
 `agent` varchar(50) NOT NULL default '',		
 `extension` varchar(15) NOT NULL default '',
 `extensions` varchar(200) NOT NULL default '',
 `channel` varchar(30) NOT NULL default '',
 `usertype` varchar(20) NOT NULL default '',
 `dialinterval` int(5) NULL,
 `accountcode` varchar(20) NOT NULL default '',
 `groupid` int(11) NOT NULL default '0',
 UNIQUE KEY `id` (`id`),
 UNIQUE KEY `username` (`username`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

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

##########################################################

## 
## table `mailboxes`
## 
DROP TABLE IF EXISTS `mailboxes`;

CREATE TABLE `mailboxes` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(30) NOT NULL default '',
  `mailbox` varchar(50) NOT NULL default '',
  `newmessages` int(11) NOT NULL default '0',
  `oldmessages` int(11) NOT NULL default '0',
UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

###########################################################

## 
## table `queuestatus`
## 

DROP TABLE IF EXISTS `queuestatus`;

CREATE TABLE `queuestatus` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `channel` VARCHAR( 60 ) NOT NULL ,
 `callerid` VARCHAR( 40 ) NOT NULL ,
 `calleridname` VARCHAR( 40 ) NOT NULL ,
 `queue` VARCHAR( 40 ) NOT NULL ,
 `position` INT NOT NULL ,
 `count` INT NOT NULL ,
 `cretime` DATETIME NOT NULL ,
 UNIQUE (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `astercrm_accountgroup`
## 

DROP TABLE IF EXISTS `astercrm_accountgroup`;

CREATE TABLE `astercrm_accountgroup` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `groupname` VARCHAR( 30 ) NOT NULL ,
 `groupnote` VARCHAR( 255 ) NOT NULL ,				
 `groupid` INT NOT NULL ,
 `incontext` VARCHAR( 50 ) NOT NULL  ,
 `outcontext` VARCHAR( 50 ) NOT NULL  ,
 `monitorforce` INT(1) NOT NULL default 0,
 `agentinterval` int(5) NULL,
 `billingid` int(11) NOT NULL default 0,
 `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
 `creby` varchar(30) NOT NULL default '',
 UNIQUE (`groupid`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `campaign`
## 

DROP TABLE IF EXISTS `campaign`;

CREATE TABLE `campaign` ( #added by solo 2008#2#5
 `id` int(11) NOT NULL auto_increment,
 `groupid` int(11) NOT NULL default '0',
 `serverid` int(11) NOT NULL default '0',
 `campaignname` varchar(30) NOT NULL default '',
 `campaignnote` varchar(255) NOT NULL default '',
 `enable` int(1) NOT NULL default '0',
 `outcontext` varchar(60) NOT NULL default '',
 `incontext` varchar(60) NOT NULL default '',
 `inexten` varchar(30) NOT NULL default '',
 `callerid` varchar(30) NOT NULL default '',
 `queuename` varchar(15) NOT NULL default '',
 `bindqueue` BOOL NOT NULL DEFAULT '0',
 `limit_type` varchar(15) NOT NULL default 'channel',
 `max_channel` int(4) NOT NULL default '5',
 `queue_increasement` float(8,2) NOT NULL default '1.00',
 `status` varchar(4) NOT NULL default 'idle',
 `fileid` int(11) NOT NULL default '0',		#added by solo 2008#5#4
 `end_fileid` int(11) NOT NULL default '0',		#added by solo 2008#5#4
 `phonenumber` varchar(255) NOT NULL default '',	#added by solo 2008#5#4
 `waittime`  varchar(3) NOT NULL default '45',
 `worktime_package_id` int(11) NOT NULL default '0',
 `maxtrytime` int(11) NOT NULL default '1',
 `recyletime` int(11) NOT NULL default '3600',
 `creby` varchar(30) NOT NULL default '',
 `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
 UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `worktimes`
## 

DROP TABLE IF EXISTS `worktimes`;

CREATE TABLE `worktimes` (
`id` int(11) NOT NULL auto_increment,
`starttime` time default null,
`endtime` time default null,
`startweek` int(1)  NOT NULL default '0',
`endweek` int(1)  NOT NULL default '0',
`groupid` INT NOT NULL DEFAULT '0',
`cretime` datetime default NULL ,
`creby` varchar(30) NOT NULL default '',
UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `worktimepackages`
## 

DROP TABLE IF EXISTS `worktimepackages`;

CREATE TABLE `worktimepackages` (
`id` int(11) NOT NULL auto_increment,
`worktimepackage_name` varchar(30) NOT NULL,
`worktimepackage_note` varchar(255) NOT NULL,
`worktimepackage_status` enum('enable','disabled') DEFAULT 'enable',
`groupid` INT NOT NULL DEFAULT '0',
`cretime` datetime default NULL ,
`creby` varchar(30) NOT NULL default '',
UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `worktimepackage_worktimes`
## 

DROP TABLE IF EXISTS `worktimepackage_worktimes`;

CREATE TABLE `worktimepackage_worktimes` (
`id` int(11) NOT NULL auto_increment,
`worktimepackage_id` int(11) NOT NULL,
`worktime_id` int(11) NOT NULL,
`cretime` datetime default NULL ,
`creby` varchar(30) NOT NULL default '',
UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################
## 
## table `contact`
## 

DROP TABLE IF EXISTS `contact`;

CREATE TABLE `contact` (
 `id` int(11) NOT NULL auto_increment,
 `contact` varchar(30) NOT NULL default '',
 `gender` varchar(10) NOT NULL default 'unknown',	#add 2007#10#5 by solo
 `position` varchar(100) NOT NULL default '',
 `phone` varchar(50) NOT NULL default '',
 `ext` varchar(8) NOT NULL default '',
 `phone1` varchar(50) NOT NULL default '',
 `ext1` varchar(8) NOT NULL default '',
 `phone2` varchar(50) NOT NULL default '',
 `ext2` varchar(8) NOT NULL default '',
 `mobile` varchar(50) NOT NULL default '',
 `fax` varchar(50) NOT NULL default '',
 `fax_ext` varchar(8) NOT NULL default '',					
 `email` varchar(100) NOT NULL default '',
 `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
 `creby` varchar(30) NOT NULL default '',
 `customerid` int(11) NOT NULL default '0',
 `groupid` INT NOT NULL ,
 UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `customer`
## 

DROP TABLE IF EXISTS `customer`;

CREATE TABLE `customer` (
 `id` int(11) NOT NULL auto_increment,
 `customer` varchar(120) NOT NULL default '',
 `address` varchar(200) NOT NULL default '',
 `zipcode` varchar(10) NOT NULL default '',
 `website` varchar(100) NOT NULL default '',
 `category` varchar(255) NOT NULL default '',
 `city`	varchar(50) NOT NULL default '',	#add 2007#9#30 by solo
 `state` varchar(50) NOT NULL default '',	#add 2007#9#30 by solo
 `country` varchar(50) NOT NULL default '',			
 `phone` varchar(50) NOT NULL default '',	#add 2007#9#30 by solo
 `phone_ext` varchar(8) NOT NULL default '',		#add 2008#10#24 by solo
 `fax` varchar(50) NOT NULL default '',		#add 2007#10#24 by solo
 `fax_ext` varchar(8) NOT NULL default '',		#add 2008#10#24 by solo
 `mobile` varchar(50) NOT NULL default '',	#add 2007#10#24 by solo
 `email` varchar(50) NOT NULL default '',	#add 2007#10#24 by solo
 `contact` varchar(50) NOT NULL default '',	#add 2007#9#30 by solo
 `contactgender` varchar(10) NOT NULL default 'unknown',	#add 2007#10#5 by solo
 `bankname` varchar(100) NOT NULL default '',	#add 2007#10#15 by solo
 `bankaccount` varchar(100) NOT NULL default '',	#add 2007#10#15 by solo
 `bankzip` varchar(100) NOT NULL default '',	#add 2007#10#26 by solo
 `bankaccountname` varchar(100) NOT NULL default '',	#add 2007#10#25 by solo
 `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
 `creby` varchar(30) NOT NULL default '',
 `groupid` INT NOT NULL ,
  UNIQUE KEY `id` (`id`),
  INDEX `groupid` (`groupid`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `dialedlist`
## 

DROP TABLE IF EXISTS `dialedlist`;

CREATE TABLE `dialedlist` (
  `id` int(11) NOT NULL auto_increment,
  `dialednumber` varchar(30) NOT NULL default '',
  `dialtime` datetime NOT NULL default '0000-00-00 00:00:00',		#added by solo 2008/05/04
  `answertime` datetime NOT NULL default '0000-00-00 00:00:00',		#added by solo 2008#2#1
  `duration` int(11) NOT NULL default '0',												#added by solo 2008#2#1
  `transfertime` int(11) NOT NULL default '0',				#added by solo 2008#5#4										#added by solo 2008#2#1
  `response` varchar(20) NOT NULL default '',											#added by solo 2008#2#1
  `customerid` int(11) NOT NULL default 0,
  `callresult` enum('normal','fax','voicemail') default 'normal',
  `campaignresult` varchar(60) default '',
  `uniqueid` varchar(20) NOT NULL default '',											#added by solo 2008#2#1
  `groupid` INT NOT NULL DEFAULT '0',															#added by solo 2008#2#3
  `campaignid` INT NOT NULL DEFAULT 0,														#added by solo 2008#2#5
  `assign` varchar(20) NOT NULL default '',												#added by solo 2008#2#10

  `trytime` INT(11) NOT NULL DEFAULT '0',
  `dialedby` varchar(30) NOT NULL default '',
  `dialedtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `diallist`
## 

DROP TABLE IF EXISTS `diallist`;

#store Predictive dialer phone list
CREATE TABLE `diallist` (
  `id` int(11) NOT NULL auto_increment,
  `dialnumber` varchar(30) NOT NULL default '',
  `dialtime` datetime NOT NULL default '0000-00-00 00:00:00',		#added by solo 2008/05/04
  `assign` varchar(20) NOT NULL default '',
  `status` varchar(50) NOT NULL default '',				#added by solo 2008/05/04
  `customerid` INT(11) NOT NULL DEFAULT '0',				#added by solo 2009#09#03
  `groupid` INT(11) NOT NULL DEFAULT '0',				#added by solo 2007#12#17
  `trytime` INT(11) NOT NULL DEFAULT '0',				#added by solo 2008/05/04
  `campaignid` INT NOT NULL DEFAULT 0,					#added by solo 2008#2#5
  `creby` varchar(30) NOT NULL default '',			#added by solo 2008#1#15
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',	#added by solo 2008#1#15
  UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `events`
## 

DROP TABLE IF EXISTS `events`;

CREATE TABLE `events` (
  `id` int(16) NOT NULL auto_increment,
  `timestamp` datetime default NULL,
  `event` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `timestamp` (`timestamp`),
  KEY `event` (`event`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `survey`
## 

DROP TABLE IF EXISTS `survey`;

CREATE TABLE `survey` (
  `id` int(11) NOT NULL auto_increment,
  `surveyname` varchar(30) NOT NULL default '',
  `surveynote` varchar(255) NOT NULL default '',							#add 2008#1#11 by solo
  `enable` smallint(6) NOT NULL default '0',									#add 2007#10#15 by solo
  `campaignid` int(11) not null default 0,
  `groupid` int(11) NOT NULL default '0',											#added by solo 2008#1#15
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  `creby` varchar(30) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `surveyoptions`
## 

DROP TABLE IF EXISTS `surveyoptions`;

CREATE TABLE `surveyoptions` (
  `id` int(11) NOT NULL auto_increment,
  `surveyoption` varchar(50) NOT NULL default '',
  `optionnote` varchar(255) NOT NULL default '',	#added by solo 2008#1#14
  `optiontype` ENUM( 'checkbox', 'radio', 'text' ) NOT NULL DEFAULT 'radio',
  `surveyid` int(11) NOT NULL default '0',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  `creby` varchar(30) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `surveyresult`
## 

DROP TABLE IF EXISTS `surveyresult`;

CREATE TABLE `surveyresult` (
  `id` int(11) NOT NULL auto_increment,
  `customerid` int(11) NOT NULL default '0',
  `contactid` int(11) NOT NULL default '0',
  `phonenumber` varchar(30) not null default '',
  `campaignid` int(11) not null default '0',
  `surveyid` int(11) NOT NULL default '0',
  `surveytitle` VARCHAR( 30 ) NOT NULL,
  `surveyoptionid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `itemcontent` VARCHAR( 50 ) NOT NULL,
  `surveyoption` varchar(50) NOT NULL default '',
  `surveynote` text NOT NULL,
  `groupid` int(11) NOT NULL default '0',
  `creby` varchar(30) NOT NULL default '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `surveyoptionitems`
## 

DROP TABLE IF EXISTS `surveyoptionitems`;

CREATE TABLE `surveyoptionitems` (
`id` int(11) NOT NULL AUTO_INCREMENT ,
`optionid` INT NOT NULL ,
`itemtype` ENUM( 'checkbox', 'radio', 'text' ) NOT NULL DEFAULT 'radio',
`itemcontent` VARCHAR( 254 ) NOT NULL ,
`creby` VARCHAR( 30 ) NOT NULL ,
`cretime` DATETIME NOT NULL ,
PRIMARY KEY ( `id` ) ,
UNIQUE (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `note`
##

DROP TABLE IF EXISTS `note`;

CREATE TABLE `note` (
  `id` int(11) NOT NULL auto_increment,
  `note` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `priority` int(11) NOT NULL default '0',
  `attitude` int(11) NOT NULL default '0',												#add 2007#10#26 by solo
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  `creby` varchar(30) NOT NULL default '',
  `customerid` int(11) NOT NULL default '0',
  `contactid` int(11) NOT NULL default '0',
  `groupid` int(11) NOT NULL default '0',
  `private` int(1) default '1',
  UNIQUE KEY `id` (`id`),
  INDEX `customerid` (`customerid`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `remind`
## 

DROP TABLE IF EXISTS `remind`;

CREATE TABLE `remind` (
 `id` int(11) NOT NULL auto_increment,
 `title` varchar(100) NOT NULL default '',	
 `content` text NOT NULL default '',		
 `remindtime`  datetime NOT NULL default '0000-00-00 00:00:00',
 `remindtype` int(10) not null default 0 ,
 `priority` int(10) NOT NULL default 0,		
 `username` varchar(30) not  null default '' ,	
 `remindabout` varchar(255) not  null default '',      
 `readed` int(10) not null default 0 ,		
 `touser` varchar(50) not null default '',	
 `creby` varchar(30) NOT NULL default '',
 `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `monitorrecord`
## 

DROP TABLE IF EXISTS `monitorrecord`;

CREATE TABLE `monitorrecord` (
 `id` INT NOT NULL AUTO_INCREMENT,
 `callerid` VARCHAR( 20 ) NOT NULL DEFAULT '',
 `filename` VARCHAR( 128 ) NOT NULL DEFAULT '',
 `fileformat` VARCHAR( 8 ) NOT NULL DEFAULT '',
 `groupid` INT NOT NULL DEFAULT 0,
 `extension` VARCHAR( 15 ) NOT NULL DEFAULT '',
 `uniqueid` varchar(20) NOT NULL default '',
 `creby` VARCHAR( 30 ) NOT NULL ,
 `cretime` DATETIME NOT NULL ,
 UNIQUE (`id`),
KEY `monitorid`(`uniqueid`,`filename`,`creby`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `trunkinfo`
## 

DROP TABLE IF EXISTS `trunkinfo`;

CREATE TABLE `trunkinfo` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `trunkname` VARCHAR( 30 ) NOT NULL ,
 `trunkchannel` VARCHAR( 50 ) NOT NULL ,
 `didnumber` VARCHAR(30) NOT NULL,
 `trunknote` TEXT NOT NULL ,
 `creby` VARCHAR( 30 ) NOT NULL ,
 `cretime` DATETIME NOT NULL ,
 INDEX ( `trunkchannel` ) ,
 INDEX ( `didnumber` ) ,
 UNIQUE (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `asteriskcalls`
## 

DROP TABLE IF EXISTS `asteriskcalls`;

CREATE TABLE `asteriskcalls` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `asteriskcallsname` VARCHAR( 50 ) NOT NULL ,
 `outcontext` VARCHAR( 50 ) NOT NULL ,
 `incontext` VARCHAR( 50 ) NOT NULL ,
 `inextension` VARCHAR( 50 ) NOT NULL ,
 `groupid` INT NOT NULL ,
 `cretime` DATETIME NOT NULL ,
 `creby` VARCHAR( 30 ) NOT NULL ,
 UNIQUE ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `remindercalls`
## 

DROP TABLE IF EXISTS `remindercalls`;

CREATE TABLE `remindercalls` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `customerid` INT NOT NULL ,
 `contactid` INT NOT NULL ,
 `phonenumber` VARCHAR( 50 ) NOT NULL ,
 `asteriskcallsid` INT NOT NULL ,
 `creby` VARCHAR( 30 ) NOT NULL ,
 `cretime` DATETIME NOT NULL ,
 `note` VARCHAR( 255 ) NOT NULL ,
 `result` VARCHAR( 255 ) NOT NULL ,
 `groupid` INT NOT NULL ,
 `dialtime` DATETIME NOT NULL ,
 `status` VARCHAR( 50 ) NOT NULL ,
 UNIQUE ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `speeddial`
## 

DROP TABLE IF EXISTS `speeddial`;

CREATE TABLE `speeddial` (
  `id` int(11) NOT NULL auto_increment,
  `description` varchar(30) NOT NULL default '',
  `number` varchar(20) NOT NULL default '',
  `groupid` int(11) NOT NULL default '0',
  `creby` VARCHAR( 30 ) NOT NULL ,
  `cretime` DATETIME NOT NULL ,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

## 
## table `sip_show_peers`
## 

DROP TABLE IF EXISTS `sip_show_peers`;

CREATE TABLE `sip_show_peers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(50) NOT NULL default '',
  `host` varchar(50) NOT NULL default '',
  `dyn` char(1) NOT NULL default '',
  `nat` char(1) NOT NULL default '',
  `port` varchar(5) NOT NULL default '',
  `status` varchar(20) NOT NULL default '',
  `freshtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `pbxserver` varchar(50) NOT NULL default '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;



####### FOR QUEUE STATS ###########

##
## Table structure for table `qagent`
##

DROP TABLE IF EXISTS `qagent`;

CREATE TABLE `qagent` (
  agent_id int(6) NOT NULL auto_increment,
  agent varchar(40) NOT NULL default '',
  PRIMARY KEY  (agent_id)
) TYPE=MyISAM;

##
## Table structure for table `qevent`
##

DROP TABLE IF EXISTS `qevent`;

CREATE TABLE `qevent` (
  event_id int(2) NOT NULL default '0',
  event varchar(40) default NULL,
  PRIMARY KEY  (event_id)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##
## Dumping data for table `qevent`
##


INSERT INTO qevent VALUES (1,'ABANDON');
INSERT INTO qevent VALUES (2,'AGENTDUMP');
INSERT INTO qevent VALUES (3,'AGENTLOGIN');
INSERT INTO qevent VALUES (4,'AGENTCALLBACKLOGIN');
INSERT INTO qevent VALUES (5,'AGENTLOGOFF');
INSERT INTO qevent VALUES (6,'AGENTCALLBACKLOGOFF');
INSERT INTO qevent VALUES (7,'COMPLETEAGENT');
INSERT INTO qevent VALUES (8,'COMPLETECALLER');
INSERT INTO qevent VALUES (9,'CONFIGRELOAD');
INSERT INTO qevent VALUES (10,'CONNECT');
INSERT INTO qevent VALUES (11,'ENTERQUEUE');
INSERT INTO qevent VALUES (12,'EXITWITHKEY');
INSERT INTO qevent VALUES (13,'EXITWITHTIMEOUT');
INSERT INTO qevent VALUES (14,'QUEUESTART');
INSERT INTO qevent VALUES (15,'SYSCOMPAT');
INSERT INTO qevent VALUES (16,'TRANSFER');
INSERT INTO qevent VALUES (17,'PAUSE');
INSERT INTO qevent VALUES (18,'UNPAUSE');
INSERT INTO qevent VALUES (19,'RINGNOANSWER');


##
## Table structure for table `qname`
##

DROP TABLE IF EXISTS `qname`;

CREATE TABLE `qname` (
  qname_id int(6) NOT NULL auto_increment,
  queue varchar(40) NOT NULL default '',
  PRIMARY KEY  (qname_id)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##
## Table structure for table `queue_stats`
##

DROP TABLE IF EXISTS `queue_stats`;

CREATE TABLE `queue_stats` (
  queue_stats_id int(12) NOT NULL auto_increment,
  uniqueid varchar(40) default NULL,
  datetime datetime NOT NULL default '0000-00-00 00:00:00',
  qname int(6) default NULL,
  qagent int(6) default NULL,
  qevent int(2) default NULL,
  info1 varchar(40) default NULL,
  info2 varchar(40) default NULL,
  info3 varchar(40) default NULL,
  src varchar(32) default NULL,
  dst varchar(32) default NULL,
 PRIMARY KEY  (queue_stats_id),
  UNIQUE KEY unico (datetime,qname,qagent,qevent)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## 
## table `queue_name`
## 

DROP TABLE IF EXISTS `queue_name`;

CREATE TABLE `queue_name` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `queuename` varchar(32) NOT NULL default '',
  `curcalls` int NOT NULL default 0,
  `limit_type` varchar(32) NOT NULL default '',
  `strategy` varchar(32) NOT NULL default '',
  `holdtime` int NOT NULL default 0,
  `w` int NOT NULL default 0,
  `calls_answered` int NOT NULL default 0,
  `calls_unanswered` int NOT NULL default 0,
  `service_level` int NOT NULL default 0,
  `t` int NOT NULL default 0,
  `data` varchar(255) NOT NULL default '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY unico (`queuename`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## 
## table `queue_agent`
## 

DROP TABLE IF EXISTS `queue_agent`;

CREATE TABLE `queue_agent` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `queuename` varchar(32) NOT NULL default '',
  `agent` varchar(255) NOT NULL default '',
  `agent_status` varchar(32) NOT NULL default '',
  `takencalls` int NOT NULL default 0,
  `lastcall` int NOT NULL default 0,
  `data` varchar(255) NOT NULL default '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## 
## table `queue_caller`
## 

DROP TABLE IF EXISTS `queue_caller`;

CREATE TABLE `queue_caller` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `queuename` varchar(32) NOT NULL default '',
  `corder` int NOT NULL default 0,
  `caller` varchar(32) NOT NULL default '',
  `waittime` int NOT NULL default 0,
  `prio` int NOT NULL default 0,
  `data` varchar(255) NOT NULL default '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


## 
## table `agentlogin_history`
## 

DROP TABLE IF EXISTS `agentlogin_history`;

CREATE TABLE `agentlogin_history` (
 `agent` varchar(30) NOT NULL default '',
 `channel` varchar(30) NOT NULL default '',
 `agentlogin` datetime NOT NULL default '0000-00-00 00:00:00',
 `agentlogout` datetime NOT NULL default '0000-00-00 00:00:00',
 `uniqueid` varchar(15) NOT NULL,
 `online` int(11) NOT NULL default '0'
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


## 
## table `uploadfile`
## 

DROP TABLE IF EXISTS `uploadfile`;

CREATE TABLE `uploadfile` (
`id` int(11) NOT NULL auto_increment,
`filename` varchar(100) NOT NULL default '',
`originalname` varchar(100) NOT NULL default '',
`type` enum('astercrm','asterbilling') NOT NULL default 'astercrm',
`cretime` datetime default NULL ,
`creby` varchar(30) NOT NULL default '',
`resellerid` int(11) NOT NULL default 0,
`groupid` int(11) NOT NULL default 0,
UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## 
## table `registry`
## 

DROP TABLE IF EXISTS `registry`;

CREATE TABLE `registry` (
`id` int(11) NOT NULL auto_increment,
`host` varchar(100) NOT NULL default '',
`username` varchar(30) NOT NULL default '',
`refresh` varchar(10) NOT NULL default '',
`state` varchar(50) NOT NULL default '',
`reg_time` varchar(50) NOT NULL default '',
`protocal` enum('SIP','IAX2','other') NOT NULL default 'sip',
 UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

##########################################################

###################################################

DROP TABLE IF EXISTS `meetmes`;

CREATE TABLE `meetmes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `confnum` varchar(10) NOT NULL DEFAULT '',
  `parties` varchar(5) NOT NULL DEFAULT '',
  `marked` varchar(30) NOT NULL DEFAULT '',
  `activity` varchar(8) NOT NULL DEFAULT '',
  `creation` varchar(20) NOT NULL DEFAULT '',
  `data` varchar(255) NOT NULL DEFAULT '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `meetmelists`;

CREATE TABLE `meetmelists` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `confnum` varchar(10) NOT NULL DEFAULT '',
  `userid` varchar(2) NOT NULL DEFAULT '',
  `callerid` varchar(30) NOT NULL DEFAULT '',
  `callername` varchar(30) NOT NULL DEFAULT '',
  `channel` varchar(100) NOT NULL DEFAULT '',
  `monitorstatus` varchar(20) NOT NULL DEFAULT '',
  `duration` varchar(20) NOT NULL DEFAULT '',
  `durationsrc` int(11) NOT NULL DEFAULT '0',
  `data` varchar(255) NOT NULL DEFAULT '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `campaignresult` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `resultname` varchar(30) NOT NULL DEFAULT '',
  `resultnote` varchar(255) NOT NULL DEFAULT '',
  `status` enum('ANSWERED','NOANSWER'),
  `parentid` int(11) NOT NULL DEFAULT '0',
  `campaignid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `creby` varchar(30) NOT NULL default '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;
