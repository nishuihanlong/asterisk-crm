alter table `dialedlist`  add `credit` float(8,2) NOT NULL default '0.00';   #added by menglj   2011#5#6
alter table `campaigndialedlist`  add `credit` float(8,2) NOT NULL default '0.00';   #added by menglj   2011#5#6

ALTER TABLE `campaign` ADD `balance` int(11) NOT NULL default 0;#可用余额
ALTER TABLE `campaign` ADD `init_billing` int(11) NOT NULL default 0;#初始计费
ALTER TABLE `campaign` ADD `billing_block` int(11) NOT NULL default 0;#计费周期
ALTER TABLE `campaign` ADD `enablebalance` ENUM('yes','no','strict') NOT NULL default 'yes';#余额控制