CREATE TABLE `dnc_list` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `number` varchar(30) NOT NULL DEFAULT '',
  `campaignid` int(11) NOT NULL default '0',
  `groupid` int(11) NOT NULL default '0',
  `status` enum('enable','disabled') default 'enable',
  `creby` varchar(30) NOT NULL default '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

ALTER TABLE `dialedlist` change `callresult` `callresult` varchar(60) default 'normal';