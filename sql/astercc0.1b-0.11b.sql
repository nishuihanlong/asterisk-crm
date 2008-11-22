############database of astercc0.1-beta update to astercc0.11-beta#############
ALTER TABLE `surveyresult` ADD `surveytitle` VARCHAR( 30 ) NOT NULL AFTER `surveyid` ;
ALTER TABLE `surveyresult` ADD `surveyoptionid` INT NOT NULL AFTER `surveytitle` ;
ALTER TABLE `surveyresult` ADD `itemid` INT NOT NULL AFTER `surveyoption` ,
ADD `itemcontent` VARCHAR( 50 ) NOT NULL AFTER `itemid` ;
###############################################################################