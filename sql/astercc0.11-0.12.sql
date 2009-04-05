

alter table customer add index `groupid` (`groupid`);
alter table note add index `customerid` (`customerid`);

alter table `astercrm_account` change `agent` `agent` varchar(50) NOT NULL default '';
alter table campaign add `waittime`  varchar(3) NOT NULL default '45';
alter table campaign add  `worktime_package_id` int(11) NOT NULL default '0';


alter table `astercrm_accountgroup` add `billingid` int(11) NOT NULL default 0;

CREATE TABLE `worktimes` (
`id` int(11) NOT NULL auto_increment,
`starttime` time default null,
`endtime` time default null,
`startweek` int(1)  NOT NULL default '0',
`endweek` int(1)  NOT NULL default '0',
`groupid` INT NOT NULL DEFAULT '0',
`cretime` datetime default NULL ,
`creby` varchar(30) NOT NULL default '',
UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `worktimepackages` (
`id` int(11) NOT NULL auto_increment,
`worktimepackage_name` varchar(30) NOT NULL,
`worktimepackage_note` varchar(255) NOT NULL,
`worktimepackage_status` enum('enable','disabled') DEFAULT 'enable',
`groupid` INT NOT NULL DEFAULT '0',
`cretime` datetime default NULL ,
`creby` varchar(30) NOT NULL default '',
UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `worktimepackage_worktimes` (
`id` int(11) NOT NULL auto_increment,
`worktimepackageid` int(11) NOT NULL,
`worktimeid` int(11) NOT NULL,
`cretime` datetime default NULL ,
`creby` varchar(30) NOT NULL default '',
UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;


#for paypal epayment
alter table resellergroup add epayment_account varchar(255) NOT NULL default '';
alter table resellergroup add epayment_status enum('enable','disable') NOT NULL default 'disable';
alter table resellergroup add epayment_item_name varchar(30) NOT NULL default '';
alter table resellergroup add epayment_identity_token varchar(255) NOT NULL default '';
alter table resellergroup add epayment_amount_package varchar(30) NOT NULL default '';
alter table resellergroup add epayment_notify_mail varchar(60) NOT NULL default '';

alter table credithistory add `comment` varchar(20) NOT NULL default ''; 
alter table credithistory add epayment_txn_id varchar(60) NOT NULL default '';

alter table `curcdr` change `srcuid` `srcuid` varchar(40) NOT NULL default '';
alter table `curcdr` change `dstuid` `dstuid` varchar(40) NOT NULL default '';
alter table `mycdr` change `srcuid` `srcuid` varchar(40) NOT NULL default '';
alter table `mycdr` change `dstuid` `dstuid` varchar(40) NOT NULL default '';

CREATE TABLE `uploadfile` (
`id` int(11) NOT NULL auto_increment,
`filename` varchar(100) NOT NULL default '',
`originalname` varchar(100) NOT NULL default '',
`cretime` datetime default NULL ,
`creby` varchar(30) NOT NULL default '',
`groupid` int(11) NOT NULL default 0,
UNIQUE KEY `id` (`id`)
)ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `mailboxes` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(30) NOT NULL default '',
  `mailbox` varchar(50) NOT NULL default '',
  `newmessages` int(11) NOT NULL default '0',
  `oldmessages` int(11) NOT NULL default '0',
UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;