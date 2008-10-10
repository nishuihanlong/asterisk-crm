-- phpMyAdmin SQL Dump
-- version 2.10.0.2
-- 
-- 主机: 127.0.0.1
-- 生成日期: 2008 年 03 月 11 日 21:05
-- 服务器版本: 4.1.22
-- PHP 版本: 4.4.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 数据库: `astercc`
-- 

############ For astercc ####################################

-- --------------------------------------------------------

-- 
-- 表的结构 `account`
-- 

CREATE TABLE IF NOT EXISTS `account` (
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

-----------------------------------------------------------

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

-- --------------------------------------------------------

-- 
-- 表的结构 `accountgroup`
-- 

CREATE TABLE IF NOT EXISTS `accountgroup` (
  `id` int(11) NOT NULL auto_increment,
  `groupname` varchar(30) NOT NULL default '',
  `accountcode` varchar(20) NOT NULL default '',
  `allowcallback` varchar(10) NOT NULL default '',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  `limittype` varchar(10) NOT NULL default '',
  `curcredit` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_clid` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_group` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_reseller` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `billingtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `resellerid` int(11) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

-- --------------------------------------------------------

-- 
-- 表的结构 `callback`
-- 

CREATE TABLE IF NOT EXISTS `callback` (
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

-- --------------------------------------------------------

-- 
-- 表的结构 `callshoprate`
-- 

CREATE TABLE IF NOT EXISTS `callshoprate` (
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
  KEY `dialprefix` (`dialprefix`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

-- --------------------------------------------------------

-- 
-- 表的结构 `clid`
-- 

CREATE TABLE IF NOT EXISTS `clid` (
  `id` int(11) NOT NULL auto_increment,
  `clid` varchar(20) NOT NULL default '',
  `pin` varchar(20) NOT NULL default '',
  `creditlimit` DOUBLE NOT NULL default '0.0000',
  `curcredit` DOUBLE NOT NULL default '0.0000',
  `limittype` VARCHAR( 20 ) NOT NULL,
  `credit_clid` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_group` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_reseller` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `display` varchar(20) NOT NULL default '',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '1',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `billingtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


-- --------------------------------------------------------

-- 
-- 表的结构 `myrate`
-- 

CREATE TABLE IF NOT EXISTS `myrate` (
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
  KEY `dialprefix` (`dialprefix`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

-- --------------------------------------------------------

-- 
-- 表的结构 `resellergroup`
-- 

CREATE TABLE IF NOT EXISTS `resellergroup` (
  `id` int(11) NOT NULL auto_increment,
  `resellername` varchar(30) NOT NULL default '',
  `accountcode` varchar(20) NOT NULL default '',
  `allowcallback` varchar(10) NOT NULL default '',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  `curcredit` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `limittype` varchar(10) NOT NULL default '',
  `credit_clid` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_group` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_reseller` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `billingtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


-- --------------------------------------------------------

-- 
-- 表的结构 `resellerrate`
-- 

CREATE TABLE IF NOT EXISTS `resellerrate` (
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
  KEY `dialprefix` (`dialprefix`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

-- --------------------------------------------------------

-- 
-- 表的结构 `credithistory`
-- 

CREATE TABLE IF NOT EXISTS `credithistory` (
  `id` int(11) NOT NULL auto_increment,
  `modifytime` datetime NOT NULL default '0000-00-00 00:00:00',
  `resellerid` int(11) NOT NULL default '0',
  `groupid` int(11) NOT NULL default '0',
  `srccredit` double(24,4) NOT NULL default '0.0000',
  `modifystatus` varchar(20) NOT NULL default '',
  `modifyamount` double(24,4) NOT NULL default '0.0000',
  `operator` varchar(20) NOT NULL default '',
  UNIQUE KEY `id` (`id`),
  key `resellerid` (`resellerid`,`groupid`,`modifytime`,`modifystatus`,`modifyamount`,`operator`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

###############################################################

############### Both for astercc and astercrm #################

----------------------------------------------------------

-- 
-- 表的结构 `peerstatus`
-- 

CREATE TABLE IF NOT EXISTS `peerstatus` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `status` VARCHAR( 50 ) NOT NULL ,
 `peer` VARCHAR( 100 ) NOT NULL ,
 `lastupdate` DATETIME NOT NULL ,
UNIQUE (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

-- --------------------------------------------------------

-- 
-- 表的结构 `curcdr`
-- 

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
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

-- --------------------------------------------------------

-- 
-- 表的结构 `mycdr`
-- 

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
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

--------------------------------------------------------

-- 
-- 表的结构 `historycdr`
-- 

CREATE TABLE IF NOT EXISTS `historycdr` (
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
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

############### For astercrm ###################################

----------------------------------------------------------

-- 
-- 表的结构 `astercrm_account`
-- 

CREATE TABLE IF NOT EXISTS `astercrm_account` (
 `id` int(11) NOT NULL auto_increment,
 `username` varchar(30) NOT NULL default '',
 `password` varchar(30) NOT NULL default '',
 `extension` varchar(30) NOT NULL default '',
 `extensions` varchar(200) NOT NULL default '',
 `channel` varchar(30) NOT NULL default '',
 `usertype` varchar(20) NOT NULL default '',
 `dialinterval` int(5) NULL,
 `accountcode` varchar(20) NOT NULL default '',
 `groupid` int(11) NOT NULL default '0',
 UNIQUE KEY `id` (`id`,`username`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

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

-----------------------------------------------------------

-- 
-- 表的结构 `queuestatus`
-- 

CREATE TABLE IF NOT EXISTS `queuestatus` (
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

----------------------------------------------------------

-- 
-- 表的结构 `astercrm_accountgroup`
-- 

CREATE TABLE IF NOT EXISTS `astercrm_accountgroup` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `groupname` VARCHAR( 30 ) NOT NULL ,
 `groupid` INT NOT NULL ,
 `pdcontext` VARCHAR( 30 ) NOT NULL  ,
 `pdextension` VARCHAR( 30 ) NOT NULL  ,
 `agentinterval` int(5) NULL,
 `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
 `creby` varchar(30) NOT NULL default '',
 UNIQUE (`groupid`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `campaign`
-- 

CREATE TABLE IF NOT EXISTS `campaign` ( #added by solo 2008-2-5
 `id` int(11) NOT NULL auto_increment,
 `groupid` int(11) NOT NULL default '0',
 `campaignname` varchar(30) NOT NULL default '',
 `campaignnote` varchar(255) NOT NULL default '',
 `outcontext` varchar(60) NOT NULL default '',
 `incontext` varchar(60) NOT NULL default '',
 `inexten` varchar(30) NOT NULL default '',
 `fileid` int(11) NOT NULL default '0',		#added by solo 2008-5-4
 `end-fileid` int(11) NOT NULL default '0',		#added by solo 2008-5-4
 `phonenumber` varchar(255) NOT NULL default '',	#added by solo 2008-5-4
 `maxtrytime` int(11) NOT NULL default '0',
 `creby` varchar(30) NOT NULL default '',
 `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
 UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `contact`
-- 

CREATE TABLE IF NOT EXISTS `contact` (
 `id` int(11) NOT NULL auto_increment,
 `contact` varchar(30) NOT NULL default '',
 `gender` varchar(10) NOT NULL default 'unknown',	#add 2007-10-5 by solo
 `position` varchar(100) NOT NULL default '',
 `phone` varchar(50) NOT NULL default '',
 `ext` varchar(8) NOT NULL default '',
 `phone1` varchar(50) NOT NULL default '',
 `ext1` varchar(8) NOT NULL default '',
 `phone2` varchar(50) NOT NULL default '',
 `ext2` varchar(8) NOT NULL default '',
 `mobile` varchar(50) NOT NULL default '',
 `fax` varchar(50) NOT NULL default '',
 `email` varchar(100) NOT NULL default '',
 `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
 `creby` varchar(30) NOT NULL default '',
 `customerid` int(11) NOT NULL default '0',
 `groupid` INT NOT NULL ,
 UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `customer`
-- 

CREATE TABLE IF NOT EXISTS `customer` (
 `id` int(11) NOT NULL auto_increment,
 `customer` varchar(120) NOT NULL default '',
 `address` varchar(200) NOT NULL default '',
 `zipcode` varchar(10) NOT NULL default '',
 `website` varchar(100) NOT NULL default '',
 `category` varchar(20) NOT NULL default '',
 `city`	varchar(50) NOT NULL default '',	#add 2007-9-30 by solo
 `state` varchar(50) NOT NULL default '',	#add 2007-9-30 by solo
 `phone` varchar(50) NOT NULL default '',	#add 2007-9-30 by solo
 `fax` varchar(50) NOT NULL default '',	#add 2007-10-24 by solo
 `mobile` varchar(50) NOT NULL default '',	#add 2007-10-24 by solo
 `email` varchar(50) NOT NULL default '',	#add 2007-10-24 by solo
 `contact` varchar(50) NOT NULL default '',	#add 2007-9-30 by solo
 `contactgender` varchar(10) NOT NULL default 'unknown',	#add 2007-10-5 by solo
 `bankname` varchar(100) NOT NULL default '',	#add 2007-10-15 by solo
 `bankaccount` varchar(100) NOT NULL default '',	#add 2007-10-15 by solo
 `bankzip` varchar(100) NOT NULL default '',	#add 2007-10-26 by solo
 `bankaccountname` varchar(100) NOT NULL default '',	#add 2007-10-25 by solo
 `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
 `creby` varchar(30) NOT NULL default '',
 `groupid` INT NOT NULL ,
  UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `dialedlist`
-- 

CREATE TABLE IF NOT EXISTS `dialedlist` (
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
  `trytime` INT(11) NOT NULL DEFAULT '0',
  `dialedby` varchar(30) NOT NULL default '',
  `dialedtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `diallist`
-- 

#store Predictive dialer phone list
CREATE TABLE IF NOT EXISTS `diallist` (
  `id` int(11) NOT NULL auto_increment,
  `dialnumber` varchar(30) NOT NULL default '',
  `dialtime` datetime NOT NULL default '0000-00-00 00:00:00',		#added by solo 2008/05/04
  `assign` varchar(20) NOT NULL default '',
  `status` varchar(50) NOT NULL default '',				#added by solo 2008/05/04
  `groupid` INT(11) NOT NULL DEFAULT '0',				#added by solo 2007-12-17
  `trytime` INT(11) NOT NULL DEFAULT '0',				#added by solo 2008/05/04
  `campaignid` INT NOT NULL DEFAULT 0,					#added by solo 2008-2-5
  `creby` varchar(30) NOT NULL default '',			#added by solo 2008-1-15
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',	#added by solo 2008-1-15
  UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `events`
-- 

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(16) NOT NULL auto_increment,
  `timestamp` datetime default NULL,
  `event` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `timestamp` (`timestamp`),
  KEY `event` (`event`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `survey`
-- 

CREATE TABLE IF NOT EXISTS `survey` (
  `id` int(11) NOT NULL auto_increment,
  `surveyname` varchar(30) NOT NULL default '',
  `surveynote` varchar(255) NOT NULL default '',							#add 2008-1-11 by solo
  `enable` smallint(6) NOT NULL default '0',									#add 2007-10-15 by solo
  `groupid` int(11) NOT NULL default '0',											#added by solo 2008-1-15
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  `creby` varchar(30) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `surveyoptions`
-- 

CREATE TABLE IF NOT EXISTS `surveyoptions` (
  `id` int(11) NOT NULL auto_increment,
  `surveyoption` varchar(50) NOT NULL default '',
  `optionnote` varchar(255) NOT NULL default '',							#added by solo 2008-1-14
  `surveyid` int(11) NOT NULL default '0',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  `creby` varchar(30) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `surveyresult`
-- 

CREATE TABLE IF NOT EXISTS `surveyresult` (
  `id` int(11) NOT NULL auto_increment,
  `customerid` int(11) NOT NULL default '0',
  `contactid` int(11) NOT NULL default '0',
  `surveyid` int(11) NOT NULL default '0',
  `surveyoption` varchar(50) NOT NULL default '',
  `surveynote` text NOT NULL,
  `groupid` int(11) NOT NULL default '0',
  `creby` varchar(30) NOT NULL default '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `note`
-- 

CREATE TABLE IF NOT EXISTS `note` (
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
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `remind`
-- 

CREATE TABLE IF NOT EXISTS `remind` (
 `id` int(11) NOT NULL auto_increment,
 `title` varchar(100) NOT NULL default '', #标题
 `content` text NOT NULL default '',       #内容
 `remindtime`  datetime NOT NULL default '0000-00-00 00:00:00', #提醒时间
 `remindtype` int(10) not null default 0 , #提醒类别，0为发给自己，1为发给别人
 `priority` int(10) NOT NULL default 0, #紧急程度,5为普通,10为紧急 
 `username` varchar(30) not  null default '' , #用户名
 `remindabout` varchar(255) not  null default '',      #提醒的相关内容
 `readed` int(10) not null default 0 , #是否读取，0为未读，1为已读
 `touser` varchar(50) not null default '', #发给谁
 `creby` varchar(30) NOT NULL default '',
 `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `monitorrecord`
-- 

CREATE TABLE IF NOT EXISTS `monitorrecord` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `callerid` VARCHAR( 20 ) NOT NULL ,
 `filename` VARCHAR( 128 ) NOT NULL ,
 `groupid` INT NOT NULL ,
 `extension` VARCHAR( 30 ) NOT NULL ,
 `uniqueid` varchar(20) NOT NULL default '',
 `creby` VARCHAR( 30 ) NOT NULL ,
 `cretime` DATETIME NOT NULL ,
 UNIQUE (`id`),
KEY `monitorid`(`uniqueid`,`filename`,`creby`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `trunkinfo`
-- 

CREATE TABLE IF NOT EXISTS `trunkinfo` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `trunkname` VARCHAR( 30 ) NOT NULL ,
 `trunkchannel` VARCHAR( 50 ) NOT NULL ,
 `trunknote` TEXT NOT NULL ,
 `creby` VARCHAR( 30 ) NOT NULL ,
 `cretime` DATETIME NOT NULL ,
 INDEX ( `trunkchannel` ) ,
 UNIQUE (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

-- 
-- 表的结构 `asteriskcalls`
-- 

CREATE TABLE IF NOT EXISTS `asteriskcalls` (
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

----------------------------------------------------------

-- 
-- 表的结构 `remindercalls`
-- 

CREATE TABLE IF NOT EXISTS `remindercalls` (
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

----------------------------------------------------------

-- 
-- 表的结构 `speeddial`
-- 

CREATE TABLE IF NOT EXISTS `speeddial` (
  `id` int(11) NOT NULL auto_increment,
  `description` varchar(255) NOT NULL default '',
  `number` varchar(20) NOT NULL default '',
  `groupid` int(11) NOT NULL default '0',
  `creby` VARCHAR( 30 ) NOT NULL ,
  `cretime` DATETIME NOT NULL ,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

----------------------------------------------------------

###################################################