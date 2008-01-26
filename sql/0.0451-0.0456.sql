
alter table account add channel varchar(30) NOT NULL default '';
alter table account add accountcode varchar(30) NOT NULL default '';
alter table account add groupid int NOT NULL default '0';

DROP TABLE IF EXISTS accountgroup;

CREATE TABLE `accountgroup` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`groupname` VARCHAR( 100 ) NOT NULL ,
`groupid` INT NOT NULL ,
`pdcontext` VARCHAR( 30 ) NOT NULL  ,
`pdextension` VARCHAR( 30 ) NOT NULL  ,
UNIQUE (
`groupid` 
)
) ENGINE = MYISAM ;
