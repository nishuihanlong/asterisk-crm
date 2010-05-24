############database of astercc0.1 update to astercc0.11#############

## 
## table `servers`
## 

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
## table `callshop_customers`
## 

CREATE TABLE `callshop_customers` (
  `id` int(11) NOT NULL auto_increment,
  `pin` varchar(30) NOT NULL default '',
  `first_name` varchar(50) NOT NULL default '',
  `last_name` varchar(50) NOT NULL default '',
  `amount` double(24,4) NOT NULL default '0.0000',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  UNIQUE `pin` (`pin`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

## ########################################################

## 
## table `discount`
## 

CREATE TABLE `discount` (
  `id` int(11) NOT NULL auto_increment,
  `amount` double(24,4) NOT NULL default '0.0000',  
  `discount` double(8,4) NOT NULL default '0.0000',  
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  UNIQUE `discount` (`discount`,`amount`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `agentlogin_history` (
 `agent` varchar(30) NOT NULL default '',
 `channel` varchar(30) NOT NULL default '',
 `agentlogin` datetime NOT NULL default '0000-00-00 00:00:00',
 `agentlogout` datetime NOT NULL default '0000-00-00 00:00:00',
 `uniqueid` varchar(15) NOT NULL,
 `online` int(11) NOT NULL default '0'
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

alter table `campaign` add `serverid` int(11) NOT NULL default '0';
alter table note add private int(1) default '1';

alter table historycdr add `customerid` int(11) NOT NULL default 0;
alter table historycdr add  `discount` double(8,4) NOT NULL default '0.0000';
alter table mycdr add `customerid` int(11) NOT NULL default 0;
alter table mycdr add  `discount` double(8,4) NOT NULL default '0.0000';

alter table historycdr add INDEX `customerid` (`customerid`);
alter table mycdr add INDEX `customerid` (`customerid`);
alter table `callshop_customers` add `discount` double(8,4) NOT NULL default -1;

alter table historycdr add `payment`  varchar(15) NOT NULL default '';
alter table mycdr add `payment`  varchar(15) NOT NULL default '';

alter table myrate add INDEX `destination` (`destination`);