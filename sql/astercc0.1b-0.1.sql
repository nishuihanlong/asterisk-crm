############database of astercc0.1-beta update to astercc0.1#############
ALTER TABLE `surveyoptions` ADD `optiontype` ENUM( 'checkbox', 'radio', 'text' ) NOT NULL DEFAULT 'radio' AFTER `optionnote` ;

ALTER TABLE `surveyresult` ADD `surveytitle` VARCHAR( 30 ) NOT NULL AFTER `surveyid` ;
ALTER TABLE `surveyresult` ADD `surveyoptionid` INT NOT NULL AFTER `surveytitle` ;
ALTER TABLE `surveyresult` ADD `itemid` INT NOT NULL AFTER `surveyoption` ;
ALTER TABLE `surveyresult` ADD `itemcontent` VARCHAR( 50 ) NOT NULL AFTER `itemid` ;

ALTER TABLE `astercrm_accountgroup` CHANGE `pdcontext` `incontext` VARCHAR( 50 ) NOT NULL ;
ALTER TABLE `astercrm_accountgroup` CHANGE `pdextension` `outcontext` VARCHAR( 50 ) NOT NULL ;

ALTER TABLE `campaign` ADD `limit_type` varchar(15) NOT NULL default 'channel' AFTER `queuename`;
ALTER TABLE `campaign` ADD `max_channel` int(4) NOT NULL default '5' AFTER `limit_type`;
ALTER TABLE `campaign` ADD `queue_increasement` float(8,2) NOT NULL default '1.00' AFTER `max_channel`;
ALTER TABLE `campaign` ADD `status`  varchar(4) NOT NULL default 'idle' AFTER `queue_increasement`;
ALTER TABLE `campaign` ADD `callerid` varchar(30) NOT NULL default '' AFTER `inexten`;
ALTER TABLE `dialedlist` ADD `dialtime` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `dialednumber`;



ALTER TABLE `accountgroup` ADD `grouptitle` VARCHAR( 50 ) NOT NULL AFTER `groupname`;
ALTER TABLE `accountgroup` ADD `grouptagline` VARCHAR( 80 ) NOT NULL AFTER `grouptitle`;
ALTER TABLE `accountgroup` ADD `grouplogo` VARCHAR( 30 ) NOT NULL AFTER `grouptagline`;
ALTER TABLE `accountgroup` ADD `grouplogostatus` int(1) NOT NULL default 1 AFTER `grouplogo`;


ALTER TABLE `accountgroup` ADD `group_multiple` DOUBLE( 8, 4 ) NOT NULL DEFAULT '1.0000' AFTER `limittype` ;
ALTER TABLE `accountgroup` ADD `customer_multiple` DOUBLE( 8, 4 ) NOT NULL DEFAULT '1.0000' AFTER `group_multiple` ;
ALTER TABLE `resellergroup` ADD `multiple` DOUBLE( 8, 4 ) NOT NULL DEFAULT '1.0000' AFTER `limittype` ;

ALTER TABLE `historycdr` ADD INDEX `calldate` (`calldate`);
ALTER TABLE `historycdr` ADD INDEX `resellerid` (`resellerid`);
ALTER TABLE `historycdr` ADD INDEX `groupid` (`groupid`);
ALTER TABLE `historycdr` ADD INDEX `dst` (`dst`);
ALTER TABLE `campaign` ADD `bindqueue` BOOL NOT NULL DEFAULT '0' AFTER `queuename` ;

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


##########################0.1#################################################
ALTER TABLE myrate ADD UNIQUE rate (dialprefix,numlen,resellerid,groupid);
ALTER TABLE callshoprate ADD UNIQUE rate (dialprefix,numlen,resellerid,groupid);
ALTER TABLE resellerrate ADD UNIQUE rate (dialprefix,numlen,resellerid);

DROP TABLE IF EXISTS `parkedcalls`;

 CREATE TABLE `parkedcalls` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Num` VARCHAR( 10 ) NOT NULL ,
`Channel` VARCHAR( 50 ) NOT NULL ,
`Context` VARCHAR( 50 ) NOT NULL ,
`Extension` VARCHAR( 50 ) NOT NULL ,
`Pri` VARCHAR( 50 ) NOT NULL ,
`Timeout` VARCHAR( 10 ) NOT NULL ,
PRIMARY KEY ( `id` ) ,
UNIQUE (
`id`
)
) ENGINE = HEAP DEFAULT CHARSET utf8 DEFAULT COLLATE utf8_general_ci; 