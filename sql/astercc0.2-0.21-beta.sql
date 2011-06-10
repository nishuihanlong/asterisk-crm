## 
## table `customer_leads`
## 

CREATE TABLE `customer_leads` (
 `id` int(11) NOT NULL auto_increment,
 `customer` varchar(120) NOT NULL default '',
 `customertitle` varchar(30) default '',
 `address` varchar(200) NOT NULL default '',
 `zipcode` varchar(10) NOT NULL default '',
 `website` varchar(100) NOT NULL default '',
 `category` varchar(255) NOT NULL default '',
 `city`	varchar(50) NOT NULL default '',	
 `state` varchar(50) NOT NULL default '',	
 `country` varchar(50) NOT NULL default '',			
 `phone` varchar(50) NOT NULL default '',	
 `phone_ext` varchar(8) NOT NULL default '',	
 `fax` varchar(50) NOT NULL default '',		
 `fax_ext` varchar(8) NOT NULL default '',	
 `mobile` varchar(50) NOT NULL default '',	
 `email` varchar(50) NOT NULL default '',	
 `contact` varchar(50) NOT NULL default '',	
 `contactgender` varchar(10) NOT NULL default 'unknown',
 `bankname` varchar(100) NOT NULL default '',
 `bankaccount` varchar(100) NOT NULL default '',
 `bankzip` varchar(100) NOT NULL default '',
 `bankaccountname` varchar(100) NOT NULL default '',
 `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
 `creby` varchar(30) NOT NULL default '',
 `groupid` INT NOT NULL ,
 `last_note_id` int(11) NOT NULL default 0,
  UNIQUE KEY `id` (`id`),
  INDEX `groupid` (`groupid`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## 
## table `codes`
## 

DROP TABLE IF EXISTS codes;

CREATE TABLE codes (
 `id` int(11) NOT NULL auto_increment,
 `code` varchar(50) not null default '',
 `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
 `creby` varchar(30) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;
INSERT INTO codes (id,code,cretime,creby) VALUES
(1,'fax',now(),'admin'),
(2,'email',now(),'admin'),
(3,'fax no-time',now(),'admin'),
(4,'email no-time',now(),'admin'),
(5,'T-O',now(),'admin'),
(6,'NI INFO',now(),'admin'),
(7,'CALL BACK INT',now(),'admin'),
(8,'BUSY BUT INFO',now(),'admin'),
(9,'NA',now(),'admin'),
(10,'MANAGER',now(),'admin'),
(11,'CORP',now(),'admin'),
(12,'OC',now(),'admin'),
(13,'OD',now(),'admin'),
(14,'H-UP',now(),'admin'),
(15,'AM',now(),'admin'),
(16,'NP',now(),'admin'),
(17,'MAIL',now(),'admin'),
(18,'WP',now(),'admin'),
(19,'ADDON',now(),'admin'),
(20,'WN',now(),'admin'),
(21,'HOLD',now(),'admin'),
(22,'DA',now(),'admin'),
(23,'NI H-UP',now(),'admin'),
(24,'1800',now(),'admin');

ALTER TABLE `note` ADD `codes` varchar(50) NOT NULL default '';
ALTER TABLE `customer` ADD `last_note_id` int(11) NOT NULL default 0;


ALTER TABLE `historycdr` ADD `srcname` varchar(100) NOT NULL default '' after `dst`;
ALTER TABLE `historycdr` ADD `astercrm_groupid` int(11) NOT NULL default 0 after `setfreecall`;
ALTER TABLE `historycdr` ADD `crm_customerid` int(11) NOT NULL default 0 after `customerid`;
ALTER TABLE `historycdr` ADD `contactid` int(11) NOT NULL default 0 after `crm_customerid`;
ALTER TABLE `historycdr` ADD `dialstring` varchar(100) not null default '' after `memo`;
ALTER TABLE `historycdr` ADD `children`  varchar(255) not null default '' after `dialstring`;
ALTER TABLE `historycdr` ADD `ischild`  enum('yes','no') not null default 'no' after `children`;
ALTER TABLE `historycdr` ADD `processed` int(1) NOT NULL default '0' after `ischild`;
ALTER TABLE `historycdr` ADD `accountid` int(11) NOT NULL default '0' after `userid`;
ALTER TABLE `historycdr` ADD `queue` varchar(30) NOT NULL DEFAULT '' after `dstuid`;
ALTER TABLE `historycdr` ADD `billsec_leg_a` int(11) NOT NULL DEFAULT 0 after `billsec`;
ALTER TABLE `historycdr` ADD  `monitored` int(11) NOT NULL default 0 after `destination`;




ALTER TABLE `astercrm_account` ADD `last_login_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' after `accountcode`;
ALTER TABLE `astercrm_account` ADD `last_update_time` datetime NOT NULL default '0000-00-00 00:00:00' after `last_login_time`;

## 
## table `agent_online_time`
## 

CREATE TABLE `agent_online_time` (
 `id` int(11) NOT NULL auto_increment,
 `username` varchar(30) NOT NULL default '',
 `login_time` datetime NOT NULL default '0000-00-00 00:00:00',
 `logout_time` datetime NOT NULL default '0000-00-00 00:00:00',
 `onlinetime` int(11) NOT NULL default 0,
 UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

ALTER TABLE mycdr ADD transfertime datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE mycdr ADD transfertarget varchar(50) NOT NULL default '';

ALTER TABLE campaigndialedlist CHANGE transfertime transfertime datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE campaigndialedlist ADD transfertarget varchar(50) NOT NULL default '' after transfertime;
ALTER TABLE campaign ADD `transfered` int(11) NOT NULL default '0' after `dialed`;


CREATE TABLE `note_leads` (
  `id` int(11) NOT NULL auto_increment,
  `note` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `callerid` varchar(30) NOT NULL default '',
  `priority` int(11) NOT NULL default '0',
  `attitude` int(11) NOT NULL default '0',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  `creby` varchar(30) NOT NULL default '',
  `customerid` int(11) NOT NULL default '0',
  `contactid` int(11) NOT NULL default '0',
  `groupid` int(11) NOT NULL default '0',
  `codes` varchar(50) NOT NULL default '',
  `private` int(1) default '1',
  UNIQUE KEY `id` (`id`),
  INDEX `customerid` (`customerid`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;



CREATE TABLE `sms_templates` (
  `id` int(11) NOT NULL auto_increment,
  `templatetitle` varchar(80) NOT NULL default '',
  `belongto` enum('all','campaign','trunk') NOT NULL default 'all',
  `campaign_id` int(11) NOT NULL default 0,
  `trunkinfo_id` int(11) NOT NULL default 0,
  `content` varchar(70) NOT NULL default '',
  `is_edit` enum('yes','no') NOT NULL default 'yes',
  `cretime` datetime NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `sms_sents` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(30) NOT NULL default '',
  `callerid` varchar(30) NOT NULL default '',
  `target` varchar(20) NOT NULL default '',
  `is_edit` enum('yes','no') NOT NULL default 'yes',
  `content` varchar(70) NOT NULL default '',
  `cretime` datetime NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

ALTER TABLE `trunkinfo` ADD `trunk_number` varchar(30) NOT NULL default '';
ALTER TABLE `campaign` ADD `sms_number` varchar(30) NOT NULL default '';

###########################   2010-12-31 ###########################################
ALTER TABLE `astercrm_account` ADD `usertype_id` int(11) NOT NULL default 0;#2010-12-31

## 
## table `user_types`
## 

DROP TABLE IF EXISTS `user_types`;
CREATE TABLE `user_types` (
  `id` int(11) NOT NULL auto_increment,
  `usertype_name` varchar(50) NOT NULL default '',
  `memo` varchar(255) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## 
## table `user_privileges`
## 
DROP TABLE IF EXISTS `user_privileges`;
CREATE TABLE `user_privileges` (
  `id` int(11) NOT NULL auto_increment,
  `action` enum('view','edit','delete') NOT NULL default 'view',
  `page` varchar(100) NOT NULL default '',
  `user_type_id` varchar(255) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;



ALTER TABLE `curcdr` ADD `agentchan` varchar(100) NOT NULL default '' AFTER `dialstring`;
ALTER TABLE `mycdr` ADD `agentchan` varchar(100) NOT NULL default '' AFTER `dialstring`;
ALTER TABLE `astercrm_account` add `callerid` varchar(30) NOT NULL default '';

ALTER TABLE curcdr ADD dialstatus VARCHAR(40) NOT NULL DEFAULT '' AFTER `dialstring`;
ALTER TABLE mycdr ADD hangupcause varchar(3) NOT NULL DEFAULT '';
ALTER TABLE mycdr ADD hangupcausetxt varchar(50) NOT NULL DEFAULT '';
ALTER TABLE mycdr ADD dialstatus VARCHAR(40) NOT NULL DEFAULT '' AFTER `dialstring`;

ALTER TABLE historycdr ADD hangupcause varchar(3) NOT NULL DEFAULT '';
ALTER TABLE historycdr ADD hangupcausetxt varchar(50) NOT NULL DEFAULT '';
ALTER TABLE historycdr ADD dialstatus VARCHAR(40) NOT NULL DEFAULT '' AFTER `dialstring`;

ALTER TABLE `campaign` ADD enablerecyle enum ('yes','no') not null default 'no';
ALTER TABLE `campaign` ADD minduration_leg_a INT(11) NOT NULL DEFAULT 0 AFTER `minduration`;
ALTER TABLE `campaign` ADD minduration_billsec INT(11) NOT NULL DEFAULT 0 AFTER `minduration`;
