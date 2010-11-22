ALTER TABLE `campaigndialedlist` ADD `detect` varchar(30) NOT NULL default '' AFTER `callresult`;
ALTER TABLE `dialedlist` ADD `detect` varchar(30) NOT NULL default '' AFTER `callresult`;


