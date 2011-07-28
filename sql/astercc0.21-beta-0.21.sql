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