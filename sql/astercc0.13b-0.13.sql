alter table queue_agent add `status` varchar(32) not null default '' after `agent_status`;
alter table diallist add `callOrder` int(11) not null default '1' after `dialtime`;
alter table dialedlist add `callOrder` int(11) not null default '1';
alter table dialedlist change `uniqueid` `uniqueid` varchar(40) not null default '';
alter table monitorrecord change `uniqueid` `uniqueid` varchar(40) not null default '';
alter table`surveyresult` add `uniqueid` varchar(40) not null default '' after `surveynote`;
alter table monitorrecord change fileformat `fileformat` enum('wav','gsm','mp3','error') NOT NULL DEFAULT 'error';
alter table monitorrecord add `processed` enum('yes','no') NOT NULL DEFAULT 'no';

