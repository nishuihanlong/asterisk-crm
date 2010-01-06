alter table historycdr add note text default '';
alter table mycdr change `src` `src` varchar(50) NOT NULL default '';
alter table mycdr change `dst` `dst` varchar(50) NOT NULL default '';
alter table curcdr change `src` `src` varchar(50) NOT NULL default '';
alter table curcdr change `dst` `dst` varchar(50) NOT NULL default '';
alter table mycdr add note text default '';
alter table historycdr add setfreecall enum('yes','no') default 'no';
alter table mycdr add setfreecall enum('yes','no') default 'no';
alter table `resellergroup` add `trunk_id` int(11) NOT NULL default 0;
alter table clid add isshow enum('yes','no') default 'yes';

CREATE TABLE `trunks` (
  `id` int(11) NOT NULL auto_increment,
  `trunkname` varchar(30) NOT NULL default '',
  `trunkidentity` varchar(50) NOT NULL default '',
  `trunkprotocol` enum('sip','iax') NOT NULL default 'sip',
  `registrystring` varchar(254) NOT NULL default '',
  `trunkdetail` text NOT NULL,
  `trunkusage` bigint(20) NOT NULL default '0',
  `trunkprefix` varchar(20) NOT NULL default '',
  `removeprefix` varchar(20) NOT NULL default '',
  `property` enum('normal','new','edit','delete') NOT NULL default 'normal',
  `creby` int(11) NOT NULL default '0',
  `created` datetime  NULL,
  `updated` datetime  NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE `trunkname` (`trunkname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;


CREATE TABLE `knowledge` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `knowledgetitle` varchar(200) NOT NULL DEFAULT '',
  `content` text NOT NULL DEFAULT '',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `creby` varchar(30) NOT NULL default '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

