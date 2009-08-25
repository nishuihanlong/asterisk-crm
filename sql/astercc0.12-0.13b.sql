alter table queue_agent add `agent_status` varchar(32) not null default '' after `agent`;
alter table queue_agent change `agent` `agent` varchar(255) not null default '';


DROP TABLE IF EXISTS `meetmes`;

CREATE TABLE `meetmes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `confnum` varchar(10) NOT NULL DEFAULT '',
  `parties` varchar(5) NOT NULL DEFAULT '',
  `marked` varchar(30) NOT NULL DEFAULT '',
  `activity` varchar(8) NOT NULL DEFAULT '',
  `creation` varchar(20) NOT NULL DEFAULT '',
  `data` varchar(255) NOT NULL DEFAULT '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `meetmelists`;

CREATE TABLE `meetmelists` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `confnum` varchar(10) NOT NULL DEFAULT '',
  `userid` varchar(2) NOT NULL DEFAULT '',
  `callerid` varchar(30) NOT NULL DEFAULT '',
  `callername` varchar(30) NOT NULL DEFAULT '',
  `channel` varchar(100) NOT NULL DEFAULT '',
  `monitorstatus` varchar(20) NOT NULL DEFAULT '',
  `duration` varchar(20) NOT NULL DEFAULT '',
  `durationsrc` int(11) NOT NULL DEFAULT '0',
  `data` varchar(255) NOT NULL DEFAULT '',
  `cretime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

