ALTER TABLE `account` ADD `resellerid` INT(11) NOT NULL  default '0';
ALTER TABLE `accountgroup` ADD `resellerid` INT(11) NOT NULL  default '0';
ALTER TABLE `callshoprate` ADD `resellerid` INT(11) NOT NULL  default '0';
ALTER TABLE `myrate` ADD `resellerid` INT(11) NOT NULL  default '0';
ALTER TABLE `clid` ADD `resellerid` INT(11) NOT NULL  default '0';
ALTER TABLE `curcdr` ADD `resellerid` INT(11) NOT NULL  default '0';
ALTER TABLE `mycdr` ADD `resellerid` INT(11) NOT NULL  default '0';

ALTER TABLE `accountgroup` ADD `limittype` varchar(10) NOT NULL default '';


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
