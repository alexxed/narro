ALTER TABLE `narro_context` ADD `comment` TEXT NULL AFTER `context_md5` ;
ALTER TABLE `narro_context` ADD `comment_md5` VARCHAR( 32 ) NULL AFTER `comment` ;
ALTER TABLE `narro_context` ADD UNIQUE `text_id` ( `text_id` , `context_md5` , `file_id` , `comment_md5` );
ALTER TABLE `narro_context` ADD INDEX `project_id_2` ( `project_id` , `active` );

DROP TABLE `narro_context_comment`;

ALTER TABLE `narro_context_info` DROP `has_comments`;

DROP TABLE `narro_context_plural_info`;
DROP TABLE `narro_context_plural`;

ALTER TABLE `narro_file` ADD `header` TEXT NULL ;

ALTER TABLE `narro_file_progress` ADD `header` TEXT NULL AFTER `language_id` ;
ALTER TABLE `narro_file_progress` ADD `export` tinyint(1) NULL DEFAULT 1 AFTER `progress_percent` ;
ALTER TABLE `narro_file_progress` ADD INDEX `file_id_3` ( `file_id` , `language_id` , `export` ) ;

DROP TABLE `narro_glossary_term`;

ALTER TABLE `narro_language` CHANGE `plural_form` `plural_form` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '"Plural-Forms: nplurals=2; plural=n != 1;\\n"' ;

ALTER TABLE `narro_project` ADD `source` text NULL AFTER `project_description` ;

ALTER TABLE `narro_project_progress` ADD `active` tinyint(1) NULL DEFAULT '0' AFTER `language_id` ;
ALTER TABLE `narro_project_progress` ADD `source` TEXT NULL AFTER `progress_percent` ;

ALTER TABLE `narro_suggestion` ADD INDEX `text_id_3` ( `text_id` , `language_id` );

ALTER TABLE `narro_suggestion_vote` DROP `text_id`;

CREATE TABLE IF NOT EXISTS `zend_cache` (
  `id` varchar(255) NOT NULL,
  `content` text,
  `lastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `zend_cache_id_expire_index` (`id`,`expire`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `zend_cache_tag` (
  `name` text,
  `id` text
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `zend_cache_version` (
  `num` int(11) NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB;

UPDATE `narro_project_progress` SET narro_project_progress.active=(SELECT narro_project.active FROM narro_project WHERE narro_project.project_id=narro_project_progress.project_id);
UPDATE `narro_file_progress` SET export=1;


ALTER TABLE `narro_file_progress` ADD `file_md5` VARCHAR( 32 ) NULL AFTER `language_id` ;
DROP TABLE IF EXISTS `narro_user_permission`;