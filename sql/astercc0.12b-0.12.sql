alter table uploadfile add `type` enum('astercrm','asterbilling') NOT NULL default 'astercrm';
alter table uploadfile add `resellerid` int(11) NOT NULL default 0;