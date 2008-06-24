SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
DROP TABLE IF EXISTS `narro_context`;
CREATE TABLE IF NOT EXISTS `narro_context` (
  `context_id` bigint(20) unsigned NOT NULL auto_increment,
  `text_id` bigint(20) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `context` text NOT NULL,
  `context_md5` varchar(255) NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  `active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`context_id`),
  KEY `string_id` (`text_id`),
  KEY `file_id` (`file_id`),
  KEY `project_id` (`project_id`),
  KEY `context_md5` (`context_md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `narro_context_comment`;
CREATE TABLE IF NOT EXISTS `narro_context_comment` (
  `comment_id` bigint(20) unsigned NOT NULL auto_increment,
  `context_id` bigint(20) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  `comment_text` text NOT NULL,
  `comment_text_md5` varchar(128) NOT NULL,
  PRIMARY KEY  (`comment_id`),
  UNIQUE KEY `context_id_2` (`context_id`,`language_id`,`comment_text_md5`),
  KEY `context_id` (`context_id`),
  KEY `user_id` (`user_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `narro_context_info`;
CREATE TABLE IF NOT EXISTS `narro_context_info` (
  `context_info_id` bigint(20) unsigned NOT NULL auto_increment,
  `context_id` bigint(20) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `validator_user_id` int(10) unsigned default NULL,
  `valid_suggestion_id` bigint(20) unsigned default NULL,
  `popular_suggestion_id` bigint(20) unsigned default NULL,
  `has_plural` tinyint(1) NOT NULL default '0',
  `has_comments` tinyint(1) NOT NULL default '0',
  `has_suggestions` tinyint(1) unsigned default '0',
  `text_access_key` varchar(2) default NULL,
  `suggestion_access_key` varchar(2) default NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`context_info_id`),
  UNIQUE KEY `context_id_2` (`context_id`,`language_id`),
  KEY `context_id` (`context_id`),
  KEY `language_id` (`language_id`),
  KEY `suggestion_id` (`valid_suggestion_id`),
  KEY `popular_suggestion_id` (`popular_suggestion_id`),
  KEY `validator_user_id` (`validator_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `narro_file`;
CREATE TABLE IF NOT EXISTS `narro_file` (
  `file_id` int(10) unsigned NOT NULL auto_increment,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `parent_id` int(10) unsigned default NULL,
  `type_id` tinyint(3) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `encoding` varchar(16) NOT NULL default 'UTF-8',
  `context_count` int(10) unsigned default '0',
  `active` tinyint(1) NOT NULL default '1',
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`file_id`),
  UNIQUE KEY `file_name` (`file_name`,`parent_id`),
  KEY `type_id` (`type_id`),
  KEY `project_id` (`project_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `narro_file_header`;
CREATE TABLE IF NOT EXISTS `narro_file_header` (
  `file_id` int(10) unsigned NOT NULL,
  `file_header` blob NOT NULL,
  PRIMARY KEY  (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `narro_file_type`;
CREATE TABLE IF NOT EXISTS `narro_file_type` (
  `file_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `file_type` varchar(32) NOT NULL,
  PRIMARY KEY  (`file_type_id`),
  UNIQUE KEY `UQ_qdrupal_narro_file_type_1` (`file_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES
(3, 'Folder'),
(1, 'GettextPo'),
(4, 'MozillaDtd'),
(7, 'MozillaInc'),
(5, 'MozillaIni'),
(6, 'Narro'),
(2, 'OpenOfficeSdf'),
(8, 'Svg');

DROP TABLE IF EXISTS `narro_language`;
CREATE TABLE IF NOT EXISTS `narro_language` (
  `language_id` int(10) unsigned NOT NULL auto_increment,
  `language_name` varchar(128) NOT NULL,
  `language_code` varchar(6) NOT NULL,
  `country_code` varchar(6) NOT NULL,
  `encoding` varchar(10) NOT NULL,
  `text_direction` varchar(3) NOT NULL default 'ltr',
  PRIMARY KEY  (`language_id`),
  UNIQUE KEY `language_name` (`language_name`),
  UNIQUE KEY `language_code` (`language_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `encoding`, `text_direction`) VALUES
(1, 'Romanian', 'ro', 'ro', 'UTF-8', 'ltr'),
(2, 'French', 'fr', 'fr', 'UTF-8', 'ltr'),
(3, 'Spanish', 'es_ES', 'es_ES', 'UTF-8', 'ltr'),
(11, 'German', 'de', 'de', 'UTF-8', 'ltr'),
(24, 'Italian', 'it', 'it', 'UTF-8', 'ltr'),
(58, 'English US', 'en_US', 'en_US', 'UTF-8', 'ltr');

DROP TABLE IF EXISTS `narro_permission`;
CREATE TABLE IF NOT EXISTS `narro_permission` (
  `permission_id` int(10) unsigned NOT NULL auto_increment,
  `permission_name` varchar(128) NOT NULL,
  PRIMARY KEY  (`permission_id`),
  UNIQUE KEY `permission_name` (`permission_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES
(4, 'Can comment'),
(5, 'Can delete any suggestion'),
(11, 'Can delete project'),
(6, 'Can edit any suggestion'),
(9, 'Can export'),
(8, 'Can import'),
(10, 'Can manage project'),
(7, 'Can manage users'),
(1, 'Can suggest'),
(3, 'Can validate'),
(2, 'Can vote');

DROP TABLE IF EXISTS `narro_project`;
CREATE TABLE IF NOT EXISTS `narro_project` (
  `project_id` int(10) unsigned NOT NULL auto_increment,
  `project_name` varchar(255) NOT NULL,
  `project_type` smallint(5) unsigned NOT NULL,
  `active` tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY  (`project_id`),
  UNIQUE KEY `project_name` (`project_name`),
  KEY `project_type` (`project_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `narro_project_type`;
CREATE TABLE IF NOT EXISTS `narro_project_type` (
  `project_type_id` smallint(5) unsigned NOT NULL auto_increment,
  `project_type` varchar(64) NOT NULL,
  PRIMARY KEY  (`project_type_id`),
  UNIQUE KEY `project_type` (`project_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `narro_project_type` (`project_type_id`, `project_type`) VALUES
(3, 'Gettext'),
(1, 'Mozilla'),
(4, 'Narro'),
(2, 'OpenOffice'),
(5, 'Svg');

DROP TABLE IF EXISTS `narro_suggestion`;
CREATE TABLE IF NOT EXISTS `narro_suggestion` (
  `suggestion_id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned default NULL,
  `text_id` bigint(20) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `suggestion_value` text NOT NULL,
  `suggestion_value_md5` varchar(128) NOT NULL,
  `suggestion_char_count` int(10) unsigned NOT NULL,
  `has_comments` tinyint(1) default '0',
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`suggestion_id`),
  UNIQUE KEY `text_id_2` (`text_id`,`language_id`,`suggestion_value_md5`),
  KEY `user_id` (`user_id`),
  KEY `text_id` (`text_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `narro_suggestion_comment`;
CREATE TABLE IF NOT EXISTS `narro_suggestion_comment` (
  `comment_id` int(10) unsigned NOT NULL auto_increment,
  `suggestion_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `comment_text` text NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`comment_id`),
  KEY `suggestion_id` (`suggestion_id`),
  KEY `user_id` (`user_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `narro_suggestion_vote`;
CREATE TABLE IF NOT EXISTS `narro_suggestion_vote` (
  `suggestion_id` bigint(20) unsigned NOT NULL,
  `context_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `vote_value` tinyint(3) NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `suggestion_id` (`suggestion_id`,`user_id`,`context_id`),
  KEY `suggestion_id_2` (`suggestion_id`),
  KEY `user_id` (`user_id`),
  KEY `context_id` (`context_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `narro_text`;
CREATE TABLE IF NOT EXISTS `narro_text` (
  `text_id` bigint(20) unsigned NOT NULL auto_increment,
  `text_value` text NOT NULL,
  `text_value_md5` varchar(64) NOT NULL,
  `text_char_count` smallint(5) unsigned NOT NULL default '0',
  `has_comments` tinyint(1) default '0',
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`text_id`),
  UNIQUE KEY `string_value_md5` (`text_value_md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `narro_text_comment`;
CREATE TABLE IF NOT EXISTS `narro_text_comment` (
  `text_comment_id` bigint(20) unsigned NOT NULL auto_increment,
  `text_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
  `comment_text` text NOT NULL,
  `comment_text_md5` varchar(128) NOT NULL,
  PRIMARY KEY  (`text_comment_id`),
  UNIQUE KEY `text_id_2` (`text_id`,`user_id`,`comment_text_md5`),
  KEY `text_id` (`text_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `narro_user`;
CREATE TABLE IF NOT EXISTS `narro_user` (
  `user_id` int(10) unsigned NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `data` text,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `narro_user` (`user_id`, `username`, `password`, `email`, `data`) VALUES
(0, '', '', '', 'a:6:{s:16:"Cedilla or comma";s:7:"cedilla";s:14:"Items per page";s:2:"20";s:9:"Font size";s:6:"medium";s:8:"Language";s:2:"ro";s:19:"Spellcheck language";s:5:"en_US";s:18:"Special characters";s:20:"ăîâşţĂÎÂŞŢ";}');

DROP TABLE IF EXISTS `narro_user_permission`;
CREATE TABLE IF NOT EXISTS `narro_user_permission` (
  `user_permission_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned default NULL,
  `language_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`user_permission_id`),
  UNIQUE KEY `user_id_2` (`user_id`,`permission_id`,`project_id`,`language_id`),
  KEY `user_id` (`user_id`),
  KEY `permission_id` (`permission_id`),
  KEY `project_id` (`project_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=202 ;

INSERT INTO `narro_user_permission` (`user_permission_id`, `user_id`, `permission_id`, `project_id`, `language_id`) VALUES
(201, 0, 1, NULL, NULL);

ALTER TABLE `narro_context`
  ADD CONSTRAINT `narro_context_ibfk_13` FOREIGN KEY (`text_id`) REFERENCES `narro_text` (`text_id`),
  ADD CONSTRAINT `narro_context_ibfk_14` FOREIGN KEY (`project_id`) REFERENCES `narro_project` (`project_id`),
  ADD CONSTRAINT `narro_context_ibfk_15` FOREIGN KEY (`file_id`) REFERENCES `narro_file` (`file_id`);

ALTER TABLE `narro_context_comment`
  ADD CONSTRAINT `narro_context_comment_ibfk_4` FOREIGN KEY (`context_id`) REFERENCES `narro_context` (`context_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_context_comment_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`),
  ADD CONSTRAINT `narro_context_comment_ibfk_6` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`);

ALTER TABLE `narro_context_info`
  ADD CONSTRAINT `narro_context_info_ibfk_10` FOREIGN KEY (`popular_suggestion_id`) REFERENCES `narro_suggestion` (`suggestion_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `narro_context_info_ibfk_15` FOREIGN KEY (`validator_user_id`) REFERENCES `narro_user` (`user_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `narro_context_info_ibfk_17` FOREIGN KEY (`context_id`) REFERENCES `narro_context` (`context_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_context_info_ibfk_18` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`),
  ADD CONSTRAINT `narro_context_info_ibfk_9` FOREIGN KEY (`valid_suggestion_id`) REFERENCES `narro_suggestion` (`suggestion_id`) ON DELETE SET NULL ON UPDATE SET NULL;

ALTER TABLE `narro_file`
  ADD CONSTRAINT `narro_file_ibfk_10` FOREIGN KEY (`project_id`) REFERENCES `narro_project` (`project_id`),
  ADD CONSTRAINT `narro_file_ibfk_4` FOREIGN KEY (`parent_id`) REFERENCES `narro_file` (`file_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_file_ibfk_9` FOREIGN KEY (`type_id`) REFERENCES `narro_file_type` (`file_type_id`);

ALTER TABLE `narro_file_header`
  ADD CONSTRAINT `narro_file_header_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `narro_file` (`file_id`);

ALTER TABLE `narro_project`
  ADD CONSTRAINT `narro_project_ibfk_1` FOREIGN KEY (`project_type`) REFERENCES `narro_project_type` (`project_type_id`);

ALTER TABLE `narro_suggestion`
  ADD CONSTRAINT `narro_suggestion_ibfk_7` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`),
  ADD CONSTRAINT `narro_suggestion_ibfk_8` FOREIGN KEY (`text_id`) REFERENCES `narro_text` (`text_id`),
  ADD CONSTRAINT `narro_suggestion_ibfk_9` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`);

ALTER TABLE `narro_suggestion_comment`
  ADD CONSTRAINT `narro_suggestion_comment_ibfk_1` FOREIGN KEY (`suggestion_id`) REFERENCES `narro_suggestion` (`suggestion_id`),
  ADD CONSTRAINT `narro_suggestion_comment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`),
  ADD CONSTRAINT `narro_suggestion_comment_ibfk_3` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`);

ALTER TABLE `narro_suggestion_vote`
  ADD CONSTRAINT `narro_suggestion_vote_ibfk_10` FOREIGN KEY (`suggestion_id`) REFERENCES `narro_suggestion` (`suggestion_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_suggestion_vote_ibfk_7` FOREIGN KEY (`context_id`) REFERENCES `narro_context` (`context_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_suggestion_vote_ibfk_9` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `narro_text_comment`
  ADD CONSTRAINT `narro_text_comment_ibfk_3` FOREIGN KEY (`text_id`) REFERENCES `narro_text` (`text_id`),
  ADD CONSTRAINT `narro_text_comment_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`);

ALTER TABLE `narro_user_permission`
  ADD CONSTRAINT `narro_user_permission_ibfk_12` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`),
  ADD CONSTRAINT `narro_user_permission_ibfk_13` FOREIGN KEY (`permission_id`) REFERENCES `narro_permission` (`permission_id`),
  ADD CONSTRAINT `narro_user_permission_ibfk_14` FOREIGN KEY (`project_id`) REFERENCES `narro_project` (`project_id`),
  ADD CONSTRAINT `narro_user_permission_ibfk_15` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`);

SET FOREIGN_KEY_CHECKS=1;
