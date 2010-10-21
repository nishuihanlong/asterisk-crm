#####################  2010-08-09  ######################
ALTER TABLE `diallist` ADD `memo` varchar(255) NOT NULL default '';#shixb
ALTER TABLE `astercrm_accountgroup` ADD `clear_popup` int(5) NULL;#shixb
ALTER TABLE `campaign` ADD dialtwoparty enum ("yes","no") not null default "no";#shixb


#####################  2010-08-14  ######################
CREATE TABLE `tickets`(
`id` int(11) NOT NULL AUTO_INCREMENT,
`ticketname` VARCHAR(100) NOT NULL DEFAULT '',
`campaignid` int(11) NOT NULL DEFAULT 0,
`groupid` int(11) NOT NULL DEFAULT  0,
`fid` int(11) NOT NULL DEFAULT 0,
`cretime` datetime DEFAULT NULL,
`creby` varchar(30) NOT NULL DEFAULT '',
 UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;
ALTER TABLE `tickets` AUTO_INCREMENT=100000;

CREATE TABLE `ticket_details`(
`id` int(11) NOT NULL AUTO_INCREMENT,
`ticketcategoryid` int(11) NOT NULL DEFAULT 0,
`ticketid` int(11) NOT NULL DEFAULT 0,
`customerid` int(11) NOT NULL DEFAULT 0,
`status` ENUM('new','panding','closed','cancel') NOT NULL DEFAULT 'new',
`assignto` int(11) NOT NULL DEFAULT 0,
`groupid` int(11) NOT NULL DEFAULT 0,
`memo` varchar(100) NOT NULL DEFAULT '',
`cretime` datetime DEFAULT NULL,
`creby` varchar(30) NOT NULL DEFAULT '',
 UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

alter table queue_name add talktime int(11) not null default 0 after `holdtime`;

alter table `astercrm_accountgroup` add `firstring` ENUM('caller','callee') NOT NULL DEFAULT 'caller' AFTER cretime;


CREATE TABLE `hold_channel`(
`id` int(11) NOT NULL AUTO_INCREMENT,
`number` VARCHAR(30) NOT NULL DEFAULT '',
`channel` VARCHAR(100) NOT NULL DEFAULT '',
`uniqueid` VARCHAR(100) NOT NULL DEFAULT '',
`status` VARCHAR(16) NOT NULL DEFAULT '',
`agentchan` VARCHAR(100) NOT NULL DEFAULT '',
`direction` enum('in','out') NOT NULL DEFAULT 'in',
`accountid` int(11) NOT NULL DEFAULT  0,
`cretime` datetime DEFAULT NULL,
 UNIQUE KEY `id` (`id`)
)ENGINE = MEMORY DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


ALTER TABLE `resellergroup` ADD `callshop_pay_fee` ENUM('yes','no') NOT NULL DEFAULT 'no';#created by shixb  2010/08/26
ALTER TABLE `mycdr` ADD `processed` int(1) NOT NULL default '0';
ALTER TABLE `mycdr` ADD `ischild`  enum('yes','no') not null default 'no' AFTER `dialstring`;
ALTER TABLE `mycdr` ADD `children`  varchar(255) not null default '' AFTER `dialstring`;

ALTER TABLE `resellergroup` ADD `clid_context` varchar(30) NOT NULL DEFAULT '';#created by shixb  2010/09/02

ALTER TABLE `mycdr` ADD `astercrm_groupid` INT(11) NOT NULL DEFAULT 0;#created shixb 2010/09/07

ALTER TABLE `queuestatus` ADD uniqueid varchar(50) NOT NULL DEFAULT '' AFTER `count`;
ALTER TABLE curcdr ADD calldate datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE curcdr ADD queue varchar(30) NOT NULL DEFAULT '' after `dstuid`;
ALTER TABLE curcdr CHANGE `monitored` `monitored` int(11) NOT NULL default 0;
ALTER TABLE mycdr ADD billsec_leg_a int(11) NOT NULL DEFAULT 0 after `billsec`;
ALTER TABLE mycdr ADD queue varchar(30) NOT NULL DEFAULT ''  after `dstuid`;
ALTER TABLE mycdr ADD `monitored` int(11) NOT NULL default 0 after `destination`;

DROP TABLE IF EXISTS `localchannels`;

CREATE TABLE `localchannels` (
`localchannel`  VARCHAR( 60 ) NOT NULL,
`channel` VARCHAR( 60 ) NOT NULL,
`channelstate` varchar(10) NOT NULL DEFAULT '',
`calleridnum` varchar(50) NOT NULL DEFAULT '',
`calleridname` VARCHAR( 50 ) NOT NULL DEFAULT '',
`accountcode` VARCHAR( 50 ) NOT NULL DEFAULT '',
`exten` varchar(20) NOT NULL DEFAULT '',
`context` VARCHAR(20) NOT NULL DEFAULT '',
`uniqueid` VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( `channel` ) ,
UNIQUE (`channel`)
) ENGINE = HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci; 

DROP TABLE IF EXISTS `campaigndialedlist`;
CREATE TABLE `campaigndialedlist` (
  `id` int(11) NOT NULL auto_increment,
  `mycdr_id` int(11) NOT NULL default 0,
  `dialednumber` varchar(30) NOT NULL default '',
  `dialtime` datetime NOT NULL default '0000-00-00 00:00:00',       
  `answertime` datetime NOT NULL default '0000-00-00 00:00:00',       
  `duration` int(4) NOT NULL default '0',               
  `billsec` int(4) NOT NULL default '0', 
  `billsec_leg_a` int(4) NOT NULL default '0',               
  `transfertime` int(11) NOT NULL default '0',
  `response` varchar(20) NOT NULL default '',
  `customerid` int(11) NOT NULL default 0,
  `customername` varchar(100) default '',
  `callresult` varchar(60) default '',
  `campaignresult` varchar(60) default '',
  `memo` varchar(255) not null default '',
  `resultby` varchar(30) NOT NULL default '',
  `uniqueid` varchar(40) NOT NULL default '',               
  `channel` varchar(40) NOT NULL default '',               
  `groupid` INT NOT NULL DEFAULT '0',                   
  `campaignid` INT NOT NULL DEFAULT 0,                   
  `assign` varchar(20) NOT NULL default '',               
  `trytime` INT(11) NOT NULL DEFAULT '0',
  `dialedby` varchar(30) NOT NULL default '',
  `dialedtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `processed` enum('yes','no') NOT NULL default 'no',
  `callOrder` INT(11) NOT NULL DEFAULT '1',
  `creby` varchar(30) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

ALTER TABLE dialedlist ADD `processed` enum('yes','no') NOT NULL default 'no';
ALTER TABLE dialedlist ADD `memo` varchar(255) NOT NULL default '';
ALTER TABLE dialedlist ADD billsec int(11) NOT NULL DEFAULT 0 after `duration` ;
ALTER TABLE dialedlist ADD billsec_leg_a int(11) NOT NULL DEFAULT 0 after `billsec` ;
ALTER TABLE dialedlist ADD channel varchar(50) NOT NULL DEFAULT '' after `uniqueid`;
ALTER TABLE dialedlist ADD mycdr_id int(11) NOT NULL default 0 after `id`;

INSERT INTO `campaigndialedlist` SELECT * FROM dialedlist ;

DROP TABLE IF EXISTS `dialedlist`;

CREATE TABLE `dialedlist` (
  `id` int(11) NOT NULL auto_increment,
  `dialednumber` varchar(30) NOT NULL default '',
  `dialtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `answertime` datetime NOT NULL default '0000-00-00 00:00:00',	
  `duration` int(11) NOT NULL default '0',
  `billsec` int(11) NOT NULL DEFAULT 0,
  `billsec_leg_a` int(11) NOT NULL DEFAULT 0,
  `transfertime` int(11) NOT NULL default '0',
  `response` varchar(20) NOT NULL default '',
  `customerid` int(11) NOT NULL default 0,
  `customername` varchar(100) default '',
  `callresult` varchar(60) default '',
  `campaignresult` varchar(60) default '',
  `resultby` varchar(30) NOT NULL default '',
  `uniqueid` varchar(40) NOT NULL default '',
  `channel` varchar(50) NOT NULL DEFAULT '',
  `groupid` INT NOT NULL DEFAULT '0',
  `campaignid` INT NOT NULL DEFAULT 0,
  `assign` varchar(20) NOT NULL default '',
  `trytime` INT(11) NOT NULL DEFAULT '0',
  `dialedby` varchar(30) NOT NULL default '',
  `dialedtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `callOrder` INT(11) NOT NULL DEFAULT '1',	
  `memo` varchar(255) NOT NULL default '',
  `creby` varchar(30) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


ALTER TABLE `astercrm_accountgroup` ADD `allowloginqueue` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `firstring`;
ALTER TABLE `campaign` ADD `queue_context` varchar(60) not null default '' AFTER `dialtwoparty`;


#####################  2010-09-16  ######################

DROP TABLE IF EXISTS `sip_show_peers`;
DROP TABLE IF EXISTS `peerstatus`;

CREATE TABLE peerstatus (
    peername varchar(50) NOT NULL default '',
    username varchar(50) NOT NULL default '',
    host varchar(50) NOT NULL default '',
    mask varchar(50) NOT NULL default '',
    dyn char(1) NOT NULL default '',
    nat char(1) NOT NULL default '',
    acl char(1) NOT NULL default '',
    port varchar(5) NOT NULL default '',
    status varchar(50) NOT NULL default '',
    responsetime int(4) NOT NULL default '0',
    freshtime datetime NOT NULL default '0000-00-00 00:00:00',
    protocol enum ('sip','iax') not null default 'sip',
    pbxserver varchar(50) NOT NULL default '',
    UNIQUE KEY peer (`peername`,`protocol`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


ALTER TABLE campaign add `billsec` int(4) NOT NULL default '0';
ALTER TABLE campaign add `billsec_leg_a` int(4) NOT NULL default '0';
ALTER TABLE campaign add `duration_answered` int(4) NOT NULL default '0';
ALTER TABLE campaign add `duration_noanswer` int(4) NOT NULL default '0';
ALTER TABLE campaign add `answered` int(4) NOT NULL default '0';
ALTER TABLE campaign add `dialed` int(4) NOT NULL default '0';

#####################  2010-09-26  ######################

DROP TABLE IF EXISTS `queue_agent`;

CREATE TABLE `queue_agent` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `queuename` varchar(32) NOT NULL default '',
  `agentname` varchar(50) NOT NULL default '',
  `agent` varchar(255) NOT NULL default '',
  `agent_status` varchar(32) NOT NULL default '',
  `ispaused` int(1) NOT NULL default 0,
  `isdynamic` int(1) NOT NULL default 0,
  `takencalls` int NOT NULL default 0,
  `lastcall` int NOT NULL default 0,
  `data` varchar(255) NOT NULL default '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;




ALTER TABLE `campaigndialedlist` ADD `recycles` int(11) NOT NULL default 0 AFTER `processed`;#2010/09/28

ALTER TABLE `note` ADD `callerid` varchar(30) NOT NULL default '';