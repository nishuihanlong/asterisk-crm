# upgrade from 0.0461 to 0.047

alter table `account` rename as `astercrm_account`;
alter table `accountgroup` rename as `astercrm_accountgroup`;

alter table campaign add `outcontext` varchar(60) NOT NULL default '';
alter table campaign add `incontext` varchar(60) NOT NULL default '';
alter table campaign add `inexten` varchar(30) NOT NULL default '';
alter table campaign add `fileid` int(11) NOT NULL default '0';
alter table campaign add `end-fileid` int(11) NOT NULL default '0';
alter table campaign add `phonenumber` varchar(255) NOT NULL default '';

alter table `dialedlist` change `dialnumber` `dialednumber` varchar(30) NOT NULL default '';
alter table `dialedlist` add `transfertime` int(11) NOT NULL default '0';

alter table `diallist` add `dialtime` datetime NOT NULL default '0000-00-00 00:00:00';
alter table `diallist` add `status` varchar(50) NOT NULL default '';
alter table `diallist` add `trytime` INT(11) NOT NULL DEFAULT '0';

DROP TABLE IF EXISTS monitorrecord;

CREATE TABLE `monitorrecord` (
`id` INT NOT NULL AUTO_INCREMENT ,
`callerid` VARCHAR( 20 ) NOT NULL ,
`filename` VARCHAR( 60 ) NOT NULL ,
`groupid` INT NOT NULL ,
`extension` VARCHAR( 30 ) NOT NULL ,
`creby` VARCHAR( 30 ) NOT NULL ,
`cretime` DATETIME NOT NULL ,
UNIQUE (
`id`
)
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
