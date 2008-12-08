############database of astercc0.1-beta update to astercc0.11-beta#############

ALTER TABLE `surveyresult` ADD `surveytitle` VARCHAR( 30 ) NOT NULL AFTER `surveyid` ;
ALTER TABLE `surveyresult` ADD `surveyoptionid` INT NOT NULL AFTER `surveytitle` ;
ALTER TABLE `surveyresult` ADD `itemid` INT NOT NULL AFTER `surveyoption` ;
ALTER TABLE `surveyresult` ADD `itemcontent` VARCHAR( 50 ) NOT NULL AFTER `itemid` ;
ALTER TABLE `accountgroup` ADD `grouptitle` VARCHAR( 50 ) NOT NULL AFTER `groupname`;
ALTER TABLE `accountgroup` ADD `grouptagline` VARCHAR( 80 ) NOT NULL AFTER `grouptitle`;
ALTER TABLE `accountgroup` ADD `grouplogo` VARCHAR( 30 ) NOT NULL AFTER `grouptagline`;
ALTER TABLE `accountgroup` ADD `grouplogostatus` int(1) NOT NULL default 1 AFTER `grouplogo`;


ALTER TABLE `accountgroup` ADD `group_multiple` DOUBLE( 8, 4 ) NOT NULL DEFAULT '1.0000' AFTER `limittype` ,
ADD `customer_multiple` DOUBLE( 8, 4 ) NOT NULL DEFAULT '1.0000' AFTER `group_multiple` ;
ALTER TABLE `resellergroup` ADD `multiple` DOUBLE( 8, 4 ) NOT NULL DEFAULT '1.0000' AFTER `limittype` ;

ALTER TABLE `campaign` ADD `limit_type` varchar(15) NOT NULL default 'channel' AFTER `queuename`;
ALTER TABLE `campaign` ADD `max_channel` int(4) NOT NULL default '5' AFTER `limit_type`;
ALTER TABLE `campaign` ADD `queue_increasement` float(8,2) NOT NULL default '1.00' AFTER `max_channel`;
ALTER TABLE `campaign` ADD `status`  varchar(4) NOT NULL default 'idle' AFTER `queue_increasement`;
ALTER TABLE `dialedlist` ADD `dialtime` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `dialednumber`;
ALTER TABLE `campaign` ADD `callerid` varchar(30) NOT NULL default '' AFTER `inexten`;


## 
## table `surveyoptionitems`
## 
 CREATE TABLE IF NOT EXISTS `surveyoptionitems` (
`id` int(11) NOT NULL AUTO_INCREMENT ,
`optionid` INT NOT NULL ,
`itemtype` ENUM( 'checkbox', 'radio', 'text' ) NOT NULL DEFAULT 'radio',
`itemcontent` VARCHAR( 254 ) NOT NULL ,
`creby` VARCHAR( 30 ) NOT NULL ,
`cretime` DATETIME NOT NULL ,
PRIMARY KEY ( `id` ) ,
UNIQUE (`id`)
) ENGINE = MYISAM DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci;

###############################################################################