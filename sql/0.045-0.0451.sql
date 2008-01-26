

alter table surveyoption add optionnote varchar(254);
alter table survey add groupid INT NOT NULL DEFAULT '0';
alter table survey add surveynote varchar(254);
alter table surveyresult add groupid INT NOT NULL DEFAULT '0';
alter table customer add groupid INT NOT NULL DEFAULT '0';
alter table contact add groupid INT NOT NULL DEFAULT '0';
alter table note add groupid INT NOT NULL DEFAULT '0';

alter table diallist add creby VARCHAR(20);
alter table diallist add cretime datetime NOT NULL default '0000-00-00 00:00:00';

