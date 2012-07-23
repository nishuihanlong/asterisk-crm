alter table `dialedlist`  add `credit` float(8,2) NOT NULL default '0.00';   #added by menglj   2011#5#6
alter table `campaigndialedlist`  add `credit` float(8,2) NOT NULL default '0.00';   #added by menglj   2011#5#6

ALTER TABLE `campaign` ADD `balance` int(11) NOT NULL default 0;#可用余额
ALTER TABLE `campaign` ADD `init_billing` int(11) NOT NULL default 0;#初始计费
ALTER TABLE `campaign` ADD `billing_block` int(11) NOT NULL default 0;#计费周期
ALTER TABLE `campaign` ADD `enablebalance` ENUM('yes','no','strict') NOT NULL default 'yes';#余额控制
ALTER TABLE `campaign` ADD `use_ext_chan` ENUM('yes','no') NOT NULL default 'no'; #动态座系签入时使用分机的channel

#############################    2011-06-01  ##################################
CREATE TABLE `ticket_op_logs` (
  `id` int(11) NOT NULL auto_increment,
  `operate` enum('add','update','assign','delete') not null default 'add',
  `op_field` varchar(30) NOT NULL default '',
  `op_ori_value` varchar(30) NOT NULL default '',
  `op_new_value` varchar(30) NOT NULL default '',
  `curOwner` varchar(30) NOT NULL default '',
  `groupid` int(11) NOT NULL default 0,
  `operator` varchar(30) NOT NULL default '',
  `optime` varchar(250) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

ALTER TABLE `astercrm_accountgroup` ADD `notice_interval` int(11) NOT NULL default '0';#the ticket notice interval time(任务提醒时间间隔)

ALTER TABLE `ticket_details` ADD `parent_id` varchar(30) NOT NULL DEFAULT '';#parent ticket_detail_id(上级ticket的id)
alter table dialedlist add amd enum('yes','no') not null default 'no' after `channel`;

CREATE TABLE `agent_queue_log` (
  `id` int(11) NOT NULL auto_increment,
  `action` varchar(50) NOT NULL default '',
  `queue` varchar(30) NOT NULL default '',
  `account` varchar(30) NOT NULL default '',
  `pausetime` int(11) NOT NULL default 0,
  `reasion` text not null,
  `groupid` int(11) NOT NULL default 0,
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


####################    2011-7-14    ######################
ALTER TABLE `customer` ADD `first_name` varchar(50) NOT NULL default '' AFTER `customer`;#add 2011#7#14 by shixb
ALTER TABLE `customer` ADD `last_name` varchar(50) NOT NULL default '' AFTER `first_name`;#add 2011#7#14 by shixb
ALTER TABLE `customer_leads` ADD `first_name` varchar(50) NOT NULL default '' AFTER `customer`;#add 2011#7#14 by shixb
ALTER TABLE `customer_leads` ADD `last_name` varchar(50) NOT NULL default '' AFTER `first_name`;#add 2011#7#14 by shixb

ALTER TABLE campaign ADD `max_dialing` int(4) NOT NULL default '0' after status;

ALTER TABLE clid ADD `accountcode` varchar(40) NOT NULL default '' after clid;



##########################        history 2012-01-05           ####################################
CREATE TABLE `account_history` (
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

CREATE TABLE `accountgroup_history` (
  `id` int(11) NOT NULL auto_increment,
  `groupname` varchar(30) NOT NULL default '',
  `grouptitle` varchar(50) NOT NULL default '',
  `grouptagline` varchar(80) NOT NULL default '',
  `grouplogo` varchar(30) NOT NULL default '',
  `grouplogostatus` int(1) NOT NULL default 1,
  `accountcode` varchar(20) NOT NULL default '',
  `allowcallback` varchar(10) NOT NULL default 'no',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  `limittype` varchar(10) NOT NULL default '',
  `group_multiple` double(8,4) NOT NULL default '1.0000',
  `customer_multiple` double(8,4) NOT NULL default '1.0000',
  `curcredit` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_clid` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_group` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_reseller` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `billingtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `resellerid` int(11) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `resellergroup_history` (
  `id` int(11) NOT NULL auto_increment,
  `resellername` varchar(30) NOT NULL default '',
  `accountcode` varchar(20) NOT NULL default '',
  `allowcallback` varchar(10) NOT NULL default '',
  `creditlimit` double(24,4) NOT NULL default '0.0000',
  `curcredit` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `limittype` varchar(10) NOT NULL default '',
  `multiple` double(8,4) NOT NULL default '1.0000',
  `credit_clid` DOUBLE( 24, 4 ) NOT NULL default '0.0000',  
  `credit_group` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_reseller` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `billingtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `epayment_account` varchar(255) NOT NULL default '',                 
  `epayment_status` enum('enable','disable') NOT NULL default 'disable',
  `epayment_item_name` varchar(30) NOT NULL default '',     
  `epayment_identity_token` varchar(255) NOT NULL default '',           
  `epayment_amount_package` varchar(30) NOT NULL default '',            
  `epayment_notify_mail` varchar(60) NOT NULL default '',
  `trunk1_id` int(11) NOT NULL default 0,
  `trunk2_id` int(11) NOT NULL default 0,
  `callshop_pay_fee` ENUM('yes','no') NOT NULL DEFAULT 'no',
  `clid_context` varchar(30) NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `clid_history` (
  `id` int(11) NOT NULL auto_increment,
  `clid` varchar(20) NOT NULL default '',
  `accountcode` varchar(40) NOT NULL default '',
  `pin` varchar(30) NOT NULL default '',
  `creditlimit` DOUBLE NOT NULL default '0.0000',
  `curcredit` DOUBLE NOT NULL default '0.0000',
  `limittype` VARCHAR( 10 ) NOT NULL,
  `credit_clid` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_group` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `credit_reseller` DOUBLE( 24, 4 ) NOT NULL default '0.0000',
  `display` varchar(20) NOT NULL default '',
  `groupid` int(11) NOT NULL default '0',
  `resellerid` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '1',
  `isshow` enum('yes','no') NOT NULL default 'yes',
  `addtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `billingtime` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `pin` (`pin`),
  UNIQUE KEY `clid` (`clid`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `myrate_history` (
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
  UNIQUE rate (dialprefix,numlen,resellerid,groupid),
  KEY `dialprefix` (`dialprefix`),
  INDEX `destination` (`destination`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `resellerrate_history` (
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
  UNIQUE rate (dialprefix,numlen,resellerid),
  KEY `dialprefix` (`dialprefix`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `callshoprate_history` (
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
  UNIQUE rate (dialprefix,numlen,resellerid,groupid),
  KEY `dialprefix` (`dialprefix`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

alter table localchannels add lastupdate datetime not null default '0000-00-00 00:00:00';