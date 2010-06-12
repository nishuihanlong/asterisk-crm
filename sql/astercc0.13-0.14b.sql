
alter table mycdr change `src` `src` varchar(40) NOT NULL default '';
alter table mycdr change `dst` `dst` varchar(40) NOT NULL default '';
alter table mycdr change `channel` `channel` varchar(80) NOT NULL default '';
alter table mycdr change `dstchannel` `dstchannel` varchar(80) NOT NULL default '';
alter table mycdr add `srcname` varchar(100) NOT NULL default '';
alter table mycdr add note text default '';
alter table mycdr add setfreecall enum('yes','no') default 'no';
alter table mycdr add accountid int(11) not null default '0';
alter table mycdr add `dialstring` varchar(100) NOT NULL default '';
alter table mycdr change `accountcode` `accountcode` varchar(100) NOT NULL default '';
alter table mycdr add `crm_customerid` int(11) NOT NULL default 0;
alter table mycdr add `contactid` int(11) NOT NULL default 0;

alter table curcdr change `src` `src` varchar(50) NOT NULL default '';
alter table curcdr change `dst` `dst` varchar(50) NOT NULL default '';
alter table curcdr change `srcchan` `srcchan` varchar(100) NOT NULL default '';
alter table curcdr change `dstchan` `dstchan` varchar(100) NOT NULL default '';
alter table curcdr add `srcname` varchar(100) NOT NULL default '';
alter table curcdr add `dialstring` varchar(100) NOT NULL default '';
alter table curcdr add `accountcode` varchar(100) NOT NULL default '';

alter table monitorrecord add accountid int(11) not null default '0';

alter table historycdr add setfreecall enum('yes','no') default 'no';
alter table historycdr add note text default '';

alter table clid add isshow enum('yes','no') default 'yes';
alter table peerstatus add channeltype varchar(30) not null default '';
alter table peerstatus add responsetime int(11) not null default '0';

ALTER TABLE dialedlist ADD INDEX nnt (`dialednumber`,`dialedtime`);
ALTER TABLE dialedlist ADD INDEX nnu (`dialednumber`,`uniqueid`);
ALTER TABLE `surveyresult` ADD `uniqueid` varchar(40) NOT NULL default '';

ALTER TABLE `resellergroup` ADD `trunk1_id` int(11) NOT NULL default 0;
ALTER TABLE `resellergroup` ADD `trunk2_id` int(11) NOT NULL default 0;

ALTER TABLE `campaign` ADD `nextcontext` varchar(60) NOT NULL default '';
ALTER TABLE `campaign` ADD `firstcontext` varchar(60) NOT NULL default '';

ALTER table registry change protocal protocal enum('sip','iax2','other') not null default 'sip';

alter table `peerstatus` add `address` varchar(100) not null default '';
alter table `peerstatus` add `port` varchar(10) not null default '';

CREATE TABLE `trunks` (
  `id` int(11) NOT NULL auto_increment,
  `trunkname` varchar(30) NOT NULL default '',
  `trunkidentity` varchar(50) NOT NULL default '',
  `trunkprotocol` enum('sip','iax') NOT NULL default 'sip',
  `registrystring` varchar(254) NOT NULL default '',
  `trunkdetail` text NOT NULL,
  `trunktimeout` int(5) NOT NULL default 30,
  `trunkusage` bigint(20) NOT NULL default '0',
  `trunkprefix` varchar(20) NOT NULL default '',
  `removeprefix` varchar(20) NOT NULL default '',
  `property` enum('normal','new','edit','delete') NOT NULL default 'normal',
  `creby` int(11) NOT NULL default '0',
  `created` datetime  NULL,
  `updated` datetime  NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE `trunkname` (`trunkname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;


CREATE TABLE `knowledge` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `knowledgetitle` varchar(200) NOT NULL DEFAULT '',
  `content` text NOT NULL DEFAULT '',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `creby` varchar(30) NOT NULL default '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `account_log` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `account_id` int(11) NOT NULL DEFAULT 0,
  `username` varchar(30) NOT NULL default '',
  `usertype` varchar(30) NOT NULL default '',
  `ip` varchar(30) NOT NULL default '',
  `action` varchar(30) NOT NULL default '',
  `status` enum("success","failed") default 'failed',
  `failedcause` varchar(100) NOT NULL default '',
  `failedtimes` int(11) NOT NULL default 0,
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;
