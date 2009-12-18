alter table historycdr add note text default '';
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

