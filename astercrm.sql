#DROP DATABASE asterisk;

#CREATE DATABASE asterisk;

#GRANT ALL
#  ON asterisk.*
#  TO asteriskuser@localhost
#  IDENTIFIED BY 'asterisk';

USE asterisk;

DROP TABLE IF EXISTS contact;

CREATE TABLE contact (
  id int(11) NOT NULL auto_increment,
  contact varchar(30) NOT NULL default '',
  position varchar(100) NOT NULL default '',
  phone varchar(50) NOT NULL default '',
  ext varchar(8) NOT NULL default '',
  phone1 varchar(50) NOT NULL default '',
  ext1 varchar(8) NOT NULL default '',
  phone2 varchar(50) NOT NULL default '',
  ext2 varchar(8) NOT NULL default '',
  mobile varchar(50) NOT NULL default '',
  fax varchar(50) NOT NULL default '',
  email varchar(100) NOT NULL default '',
  cretime date NOT NULL default '0000-00-00',
  creby varchar(40) NOT NULL default '',
  customerid int(11) NOT NULL default '0',
  UNIQUE KEY id (id)
) ;

CREATE TABLE customer (
  id int(11) NOT NULL auto_increment,
  customer varchar(120) NOT NULL default '',
  address varchar(200) NOT NULL default '',
  zipcode varchar(10) NOT NULL default '',
  website varchar(100) NOT NULL default '',
  category varchar(20) NOT NULL default '',
  cretime date NOT NULL default '0000-00-00',
  creby varchar(30) NOT NULL default '',
  UNIQUE KEY id (id)
) ;

CREATE TABLE events (
   id int(10) unsigned NOT NULL auto_increment,
   timestamp datetime NOT NULL default '0000-00-00 00:00:00',
   event LONGTEXT ,
   PRIMARY KEY (`id`)
); 

CREATE TABLE note (
  id int(11) NOT NULL auto_increment,
  note text NOT NULL,
  priority int(11) NOT NULL default '0',
  cretime date NOT NULL default '0000-00-00',
  creby varchar(30) NOT NULL default '',
  customerid int(11) NOT NULL default '0',
  contactid int(11) NOT NULL default '0',
  UNIQUE KEY id (id)
) ;

CREATE TABLE account (
  id int(11) NOT NULL auto_increment,
  username varchar(30) NOT NULL default '',
  password varchar(30) NOT NULL default '',
  extension varchar(30) NOT NULL default '',
  extensions varchar(200) NOT NULL default '',
  usertype varchar(20) NOT NULL default '',
  UNIQUE KEY id (id)
) ;

#store Predictive dialer phone list
CREATE TABLE diallist (
  id int(11) NOT NULL auto_increment,
  dialnumber varchar(30) NOT NULL default '',
  assign varchar(30) NOT NULL default '',
  UNIQUE KEY id (id)
) ;

#store Predictive dialer dial result

CREATE TABLE dialresult (
  id int(11) NOT NULL auto_increment,
  dialnumber varchar(30) NOT NULL default '',
  dialresult varchar(30) NOT NULL default '',
  UNIQUE KEY id (id)
) ;


INSERT INTO `asterisk`.`account` (
`id` ,
`username` ,
`password` ,
`extension` ,
`extensions` ,
`usertype` 
)
VALUES (
NULL , 'admin', 'admin', '0000', '', 'admin'
);
