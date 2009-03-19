
alter table customer add index `groupid` (`groupid`);
alter table note add index `customerid` (`customerid`);

alter table campaign add `waittime`  varchar(3) NOT NULL default '45';
alter table campaign add  `worktime_package_id` int(11) NOT NULL default '0';

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