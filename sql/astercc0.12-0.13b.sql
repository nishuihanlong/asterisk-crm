alter table dialedlist add callresult enum('normal','fax','voicemail') default 'normal';
alter table dialedlist add campaignresult varchar(60) default '' after `callresult`;
alter table dialedlist add customerid int(11) default '0';

alter table queue_agent add `agent_status` varchar(32) not null default '' after `agent`;
alter table queue_agent change `agent` `agent` varchar(255) not null default '';

alter table surveyresult add phonenumber varchar(30) not null default '' after contactid;
alter table surveyresult add campaignid int(11) not null default '0' after phonenumber;

alter table survey add campaignid int(11) not null default 0 ;

alter table `diallist` add `customerid` int(11) not null default '0' after `status`;
alter table `diallist` add `customername` varchar(100) not null default '' after `status`;
alter table `dialedlist` add `customername` varchar(100) not null default '' after `campaignid`;
alter table `dialedlist` add `resultby` varchar(30) not null default '' after `campaignresult`;
alter table `dialedlist` add `creby` varchar(30) not null default '';

alter table campaign add recyletime  int(11) not null default '3600' after `maxtrytime`;
alter table campaign add minduration  int(11) not null default '0' after `recyletime`;

alter table curcdr add monitored enum('yes','no') not null default 'no' ;

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

CREATE TABLE `registry` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `host` varchar(100) NOT NULL DEFAULT '',
  `username` varchar(30) NOT NULL DEFAULT '',
  `refresh` varchar(10) NOT NULL DEFAULT '',
  `state` varchar(50) NOT NULL DEFAULT '',
  `reg_time` varchar(50) NOT NULL DEFAULT '',
  `protocal` enum('SIP','IAX2','other') NOT NULL DEFAULT 'SIP',
  PRIMARY KEY (`id`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

