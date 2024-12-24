ALTER TABLE `{prefix}comment` ADD `comment_domain_id` INT( 12 ) NOT NULL DEFAULT '0'; 
ALTER TABLE `{prefix}setting` ADD `setting_domain_id` INT( 12 ) NOT NULL DEFAULT '0';

ALTER TABLE `{prefix}setting` ADD `setting_id` INT( 12 ) NOT NULL AUTO_INCREMENT FIRST ,
ADD PRIMARY KEY ( setting_id ) ;



ALTER TABLE `{prefix}domain` ADD INDEX `domain_hash`(`domain_hash`);
ALTER TABLE `{prefix}setting` ADD INDEX `setting_domain_id`(`setting_domain_id`);
ALTER TABLE `{prefix}comment` ADD INDEX `status_domain_id`(`comment_domain_id`, `comment_status`);

ALTER TABLE `{prefix}comment` ADD `comment_hash` CHAR( 40 ) NOT NULL; 
ALTER TABLE `{prefix}comment` ADD INDEX `comment_hash`(`comment_hash`);