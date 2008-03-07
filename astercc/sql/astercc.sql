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
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  ;

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
--  `accountgroup`
-- 

DROP TABLE IF EXISTS `accountgroup`;
CREATE TABLE `accountgroup` (
  `id` int(11) NOT NULL auto_increment,
  `groupname` varchar(20) NOT NULL default '',
  `accountcode` varchar(20) NOT NULL default '',
  `allowcallback` varchar(10) NOT NULL default '',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  `resellerid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  ;

-- --------------------------------------------------------

-- 
--  `callback`
-- 

DROP TABLE IF EXISTS `callback`;
CREATE TABLE `callback` (
  `id` int(11) NOT NULL auto_increment,
  `lega` varchar(30) NOT NULL default '',
  `legb` varchar(30) NOT NULL default '',
  `credit` double(24,4) NOT NULL default '0.0000',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  KEY `leg` (`lega`,`legb`)
) ENGINE=MyISAM  ;

-- --------------------------------------------------------

-- 
--  `callshoprate`
-- 

DROP TABLE IF EXISTS `callshoprate`;
CREATE TABLE `callshoprate` (
  `id` int(11) NOT NULL auto_increment,
  `dialprefix` varchar(20) NOT NULL default '',
  `numlen` int(11) NOT NULL default '0',
  `destination` varchar(100) NOT NULL default '',
  `rateinitial` double(24,4) NOT NULL default '0.0000',
  `initblock` int(11) NOT NULL default '0',
  `billingblock` int(11) NOT NULL default '0',
  `connectcharge` double(24,4) NOT NULL default '0.0000',
  `groupid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  KEY `dialprefix` (`dialprefix`)
) ENGINE=MyISAM  ;

-- --------------------------------------------------------

-- 
--  `clid`
-- 

DROP TABLE IF EXISTS `clid`;
CREATE TABLE `clid` (
  `id` int(11) NOT NULL auto_increment,
  `clid` varchar(20) NOT NULL default '',
  `pin` varchar(20) NOT NULL default '',
  `status` tinyint(4) NOT NULL default '1',			-- added by solo 2008/2/25
  `display` varchar(20) NOT NULL default '',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  ;

-- --------------------------------------------------------

-- 
--  `curcdr`
-- 

DROP TABLE IF EXISTS `curcdr`;
CREATE TABLE `curcdr` (
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
  `groupid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `credit` double(24,4) NOT NULL default '0.0000',
  `callshopcredit` double(24,4) NOT NULL default '0.0000',
  `resellercredit` double(24,4) NOT NULL default '0.0000',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  UNIQUE KEY `id` (`id`),
  KEY `srcid` (`src`,`dst`,`srcchan`,`dstchan`,`srcuid`,`dstuid`,`disposition`)
) ENGINE=MyISAM  ;

-- --------------------------------------------------------

-- 
--  `mycdr`
-- 

DROP TABLE IF EXISTS `mycdr`;
CREATE TABLE `mycdr` (
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
  `userid` int(11) NOT NULL default '0',
  UNIQUE KEY `id` (`id`),
  KEY `srcid` (`src`,`dst`,`channel`,`dstchannel`,`duration`,`billsec`,`disposition`)
) ENGINE=MyISAM  ;

-- --------------------------------------------------------

-- 
--  `myrate`
-- 

DROP TABLE IF EXISTS `myrate`;
CREATE TABLE `myrate` (
  `id` int(11) NOT NULL auto_increment,
  `dialprefix` varchar(20) NOT NULL default '',
  `numlen` int(11) NOT NULL default '0',
  `destination` varchar(100) NOT NULL default '',
  `rateinitial` double(24,4) NOT NULL default '0.0000',
  `initblock` int(11) NOT NULL default '0',
  `billingblock` int(11) NOT NULL default '0',
  `connectcharge` double(24,4) NOT NULL default '0.0000',
  `groupid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  KEY `dialprefix` (`dialprefix`)
) ENGINE=MyISAM  ;

-- --------------------------------------------------------

-- 
--  `resellergroup`
-- 

DROP TABLE IF EXISTS `resellergroup`;
CREATE TABLE `resellergroup` (
  `id` int(11) NOT NULL auto_increment,
  `resellername` varchar(20) NOT NULL default '',
  `accountcode` varchar(20) NOT NULL default '',
  `allowcallback` varchar(10) NOT NULL default '',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM ;

-- --------------------------------------------------------

-- 
--  `resellerrate`
-- 

DROP TABLE IF EXISTS `resellerrate`;
CREATE TABLE `resellerrate` (
  `id` int(11) NOT NULL auto_increment,
  `dialprefix` varchar(20) NOT NULL default '',
  `numlen` int(11) NOT NULL default '0',
  `destination` varchar(100) NOT NULL default '',
  `rateinitial` double(24,4) NOT NULL default '0.0000',
  `initblock` int(11) NOT NULL default '0',
  `billingblock` int(11) NOT NULL default '0',
  `connectcharge` double(24,4) NOT NULL default '0.0000',
  `resellerid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  KEY `dialprefix` (`dialprefix`)
) ENGINE=MyISAM ;
