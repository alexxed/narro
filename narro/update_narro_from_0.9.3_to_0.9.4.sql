 ALTER TABLE `narro_file` CHANGE `context_count` `total_text_count` INT( 10 ) UNSIGNED NULL DEFAULT '0';
 ALTER TABLE `narro_file` ADD `fuzzy_text_count` INT( 10 ) UNSIGNED NULL DEFAULT '0' AFTER `total_text_count` ,
ADD `approved_text_count` INT( 10 ) UNSIGNED NULL DEFAULT '0' AFTER `fuzzy_text_count` ;

ALTER TABLE `narro_project` ADD `total_text_count` INT( 10 ) UNSIGNED NULL DEFAULT '0' AFTER `project_description` ,
ADD `fuzzy_text_count` INT( 10 ) UNSIGNED NULL DEFAULT '0' AFTER `total_text_count` ,
ADD `approved_text_count` INT( 10 ) UNSIGNED NULL DEFAULT '0' AFTER `fuzzy_text_count` ;

ALTER TABLE `narro_project` ADD `progress_percent` INT( 10 ) UNSIGNED NULL DEFAULT '0' AFTER `approved_text_count` ;
ALTER TABLE `narro_file` ADD `progress_percent` INT( 10 ) UNSIGNED NULL DEFAULT '0' AFTER `approved_text_count` ;

INSERT INTO `narro`.`narro_permission` (`permission_id` ,`permission_name`) VALUES (NULL , 'Can mass approve');