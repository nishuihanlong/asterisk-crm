-- phpMyAdmin SQL Dump
-- version 2.10.0.2
-- 
-- 主机: 127.0.0.1
-- 生成日期: 2008 年 03 月 11 日 21:05
-- 服务器版本: 4.1.22
-- PHP 版本: 4.4.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 数据库: `callshop`
-- 

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
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- 表的结构 `accountgroup`
-- 

CREATE TABLE IF NOT EXISTS `accountgroup` (
  `id` int(11) NOT NULL auto_increment,
  `groupname` varchar(20) NOT NULL default '',
  `accountcode` varchar(20) NOT NULL default '',
  `allowcallback` varchar(10) NOT NULL default '',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  `limittype` varchar(10) NOT NULL default '',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `resellerid` int(11) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM;

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
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- 表的结构 `callshoprate`
-- 

CREATE TABLE IF NOT EXISTS `callshoprate` (
  `id` int(11) NOT NULL auto_increment,
  `dialprefix` varchar(20) NOT NULL default '',
  `numlen` int(11) NOT NULL default '0',
  `destination` varchar(100) NOT NULL default '',
  `rateinitial` double(24,4) NOT NULL default '0.0000',
  `initblock` int(11) NOT NULL default '0',
  `billingblock` int(11) NOT NULL default '0',
  `connectcharge` double(24,4) NOT NULL default '0.0000',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  KEY `dialprefix` (`dialprefix`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- 表的结构 `clid`
-- 

CREATE TABLE IF NOT EXISTS `clid` (
  `id` int(11) NOT NULL auto_increment,
  `clid` varchar(20) NOT NULL default '',
  `pin` varchar(20) NOT NULL default '',
  `display` varchar(20) NOT NULL default '',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '1',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM;

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
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `credit` double(24,4) NOT NULL default '0.0000',
  `callshopcredit` double(24,4) NOT NULL default '0.0000',
  `resellercredit` double(24,4) NOT NULL default '0.0000',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  UNIQUE KEY `id` (`id`),
  KEY `srcid` (`src`,`dst`,`srcchan`,`dstchan`,`srcuid`,`dstuid`,`disposition`)
) ENGINE=MyISAM;

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
  UNIQUE KEY `id` (`id`),
  KEY `srcid` (`src`,`dst`,`channel`,`dstchannel`,`duration`,`billsec`,`disposition`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- 表的结构 `myrate`
-- 

CREATE TABLE IF NOT EXISTS `myrate` (
  `id` int(11) NOT NULL auto_increment,
  `dialprefix` varchar(20) NOT NULL default '',
  `numlen` int(11) NOT NULL default '0',
  `destination` varchar(100) NOT NULL default '',
  `rateinitial` double(24,4) NOT NULL default '0.0000',
  `initblock` int(11) NOT NULL default '0',
  `billingblock` int(11) NOT NULL default '0',
  `connectcharge` double(24,4) NOT NULL default '0.0000',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  KEY `dialprefix` (`dialprefix`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- 表的结构 `resellergroup`
-- 

CREATE TABLE IF NOT EXISTS `resellergroup` (
  `id` int(11) NOT NULL auto_increment,
  `resellername` varchar(20) NOT NULL default '',
  `accountcode` varchar(20) NOT NULL default '',
  `allowcallback` varchar(10) NOT NULL default '',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  `limittype` varchar(10) NOT NULL default '',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- 表的结构 `resellerrate`
-- 

CREATE TABLE IF NOT EXISTS `resellerrate` (
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
) ENGINE=MyISAM;
