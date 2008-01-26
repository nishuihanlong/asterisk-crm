
alter table account add channel varchar(30) NOT NULL default '';
alter table account add accountcode varchar(30) NOT NULL default '';
alter table account add groupid int NOT NULL default '0';