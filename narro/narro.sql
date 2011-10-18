-- MySQL dump 10.13  Distrib 5.5.14, for Linux (x86_64)
--
-- Host: localhost    Database: narro_tradu
-- ------------------------------------------------------
-- Server version   5.5.14

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `narro_context`
--

DROP TABLE IF EXISTS `narro_context`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_context` (
  `context_id` integer(10) unsigned NOT NULL AUTO_INCREMENT,
  `text_id` integer(10) unsigned NOT NULL,
  `text_access_key` char(1) DEFAULT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `context` text NOT NULL,
  `context_md5` varchar(32) NOT NULL,
  `comment` text,
  `comment_md5` varchar(32) DEFAULT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`context_id`),
  UNIQUE KEY `text_id` (`text_id`,`context_md5`,`file_id`,`comment_md5`),
  KEY `string_id` (`text_id`),
  KEY `file_id` (`file_id`),
  KEY `project_id` (`project_id`),
  KEY `context_md5` (`context_md5`),
  KEY `project_id_2` (`project_id`,`active`),
  CONSTRAINT `narro_context_ibfk_13` FOREIGN KEY (`text_id`) REFERENCES `narro_text` (`text_id`),
  CONSTRAINT `narro_context_ibfk_14` FOREIGN KEY (`project_id`) REFERENCES `narro_project` (`project_id`),
  CONSTRAINT `narro_context_ibfk_15` FOREIGN KEY (`file_id`) REFERENCES `narro_file` (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_context_info`
--

DROP TABLE IF EXISTS `narro_context_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_context_info` (
  `context_info_id` integer(10) unsigned NOT NULL AUTO_INCREMENT,
  `context_id` integer(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `validator_user_id` int(10) unsigned DEFAULT NULL,
  `valid_suggestion_id` integer(10) unsigned DEFAULT NULL,
  `has_suggestions` tinyint(1) unsigned DEFAULT '0',
  `suggestion_access_key` char(1) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`context_info_id`),
  UNIQUE KEY `context_id_2` (`context_id`,`language_id`),
  KEY `context_id` (`context_id`),
  KEY `language_id` (`language_id`),
  KEY `suggestion_id` (`valid_suggestion_id`),
  KEY `validator_user_id` (`validator_user_id`),
  KEY `created` (`created`),
  KEY `modified` (`modified`),
  CONSTRAINT `narro_context_info_ibfk_15` FOREIGN KEY (`validator_user_id`) REFERENCES `narro_user` (`user_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `narro_context_info_ibfk_17` FOREIGN KEY (`context_id`) REFERENCES `narro_context` (`context_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `narro_context_info_ibfk_18` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`),
  CONSTRAINT `narro_context_info_ibfk_9` FOREIGN KEY (`valid_suggestion_id`) REFERENCES `narro_suggestion` (`suggestion_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_file`
--

DROP TABLE IF EXISTS `narro_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_file` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_md5` varchar(32) DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `type_id` tinyint(3) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `header` text,
  PRIMARY KEY (`file_id`),
  UNIQUE KEY `file_name` (`file_name`,`parent_id`),
  KEY `type_id` (`type_id`),
  KEY `project_id` (`project_id`),
  KEY `parent_id` (`parent_id`),
  
  CONSTRAINT `narro_file_ibfk_10` FOREIGN KEY (`project_id`) REFERENCES `narro_project` (`project_id`),
  CONSTRAINT `narro_file_ibfk_4` FOREIGN KEY (`parent_id`) REFERENCES `narro_file` (`file_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `narro_file_ibfk_9` FOREIGN KEY (`type_id`) REFERENCES `narro_file_type` (`file_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_file_progress`
--

DROP TABLE IF EXISTS `narro_file_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_file_progress` (
  `file_progress_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `file_md5` varchar(32) DEFAULT NULL,
  `header` text,
  `total_text_count` int(10) NOT NULL,
  `approved_text_count` int(10) NOT NULL,
  `fuzzy_text_count` int(10) NOT NULL,
  `progress_percent` int(10) NOT NULL,
  `export` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`file_progress_id`),
  UNIQUE KEY `file_id` (`file_id`,`language_id`),
  KEY `language_id` (`language_id`),
  KEY `file_id_2` (`file_id`),
  KEY `file_id_3` (`file_id`,`language_id`,`export`),
  CONSTRAINT `narro_file_progress_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `narro_file` (`file_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `narro_file_progress_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_file_type`
--

DROP TABLE IF EXISTS `narro_file_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_file_type` (
  `file_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `file_type` varchar(32) NOT NULL,
  PRIMARY KEY (`file_type_id`),
  UNIQUE KEY `UQ_qdrupal_narro_file_type_1` (`file_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_language`
--

DROP TABLE IF EXISTS `narro_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_language` (
  `language_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_name` varchar(128) NOT NULL,
  `language_code` varchar(64) NOT NULL,
  `country_code` varchar(64) NOT NULL,
  `dialect_code` varchar(64) DEFAULT NULL,
  `encoding` varchar(10) NOT NULL,
  `text_direction` varchar(3) NOT NULL DEFAULT 'ltr',
  `special_characters` varchar(255) DEFAULT NULL,
  `plural_form` varchar(255) DEFAULT '"Plural-Forms: nplurals=2; plural=n != 1;\\n"',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`language_id`),
  UNIQUE KEY `language_name` (`language_name`),
  UNIQUE KEY `language_code` (`language_code`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_permission`
--

DROP TABLE IF EXISTS `narro_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_permission` (
  `permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(128) NOT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `permission_name` (`permission_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_project`
--

DROP TABLE IF EXISTS `narro_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_project` (
  `project_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_category_id` int(11) unsigned DEFAULT '1',
  `project_name` varchar(255) NOT NULL,
  `project_type` smallint(5) unsigned NOT NULL,
  `project_description` varchar(255) DEFAULT NULL,
  `data` text,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`project_id`),
  UNIQUE KEY `project_name` (`project_name`),
  KEY `project_type` (`project_type`),
  KEY `narro_project_ibfk_2` (`project_category_id`),
  KEY `active` (`active`),
  CONSTRAINT `narro_project_ibfk_1` FOREIGN KEY (`project_type`) REFERENCES `narro_project_type` (`project_type_id`),
  CONSTRAINT `narro_project_ibfk_2` FOREIGN KEY (`project_category_id`) REFERENCES `narro_project_category` (`project_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_project_category`
--

DROP TABLE IF EXISTS `narro_project_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_project_category` (
  `project_category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `category_description` varchar(255) NOT NULL,
  PRIMARY KEY (`project_category_id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_project_progress`
--

DROP TABLE IF EXISTS `narro_project_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_project_progress` (
  `project_progress_id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `active` tinyint(1) DEFAULT '0',
  `last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `total_text_count` int(10) unsigned NOT NULL,
  `fuzzy_text_count` int(10) unsigned NOT NULL,
  `approved_text_count` int(10) unsigned NOT NULL,
  `progress_percent` int(10) unsigned NOT NULL,
  `data` text,
  PRIMARY KEY (`project_progress_id`),
  UNIQUE KEY `project_id` (`project_id`,`language_id`),
  KEY `language_id` (`language_id`),
  KEY `project_id_2` (`project_id`),
  CONSTRAINT `narro_project_progress_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `narro_project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `narro_project_progress_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_project_type`
--

DROP TABLE IF EXISTS `narro_project_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_project_type` (
  `project_type_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `project_type` varchar(64) NOT NULL,
  PRIMARY KEY (`project_type_id`),
  UNIQUE KEY `project_type` (`project_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_role`
--

DROP TABLE IF EXISTS `narro_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_role` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(128) NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_role_permission`
--

DROP TABLE IF EXISTS `narro_role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_role_permission` (
  `role_permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`role_permission_id`),
  KEY `role_id` (`role_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `narro_role_permission_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `narro_role` (`role_id`),
  CONSTRAINT `narro_role_permission_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `narro_permission` (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_suggestion`
--

DROP TABLE IF EXISTS `narro_suggestion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_suggestion` (
  `suggestion_id` integer(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `text_id` integer(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `suggestion_value` text NOT NULL,
  `suggestion_value_md5` varchar(32) NOT NULL,
  `suggestion_char_count` smallint(5) unsigned DEFAULT '0',
  `suggestion_word_count` smallint(5) unsigned DEFAULT '0',
  `has_comments` tinyint(1) DEFAULT '0',
  `is_imported` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`suggestion_id`),
  UNIQUE KEY `text_id_2` (`text_id`,`language_id`,`suggestion_value_md5`),
  KEY `user_id` (`user_id`),
  KEY `text_id` (`text_id`),
  KEY `language_id` (`language_id`),
  KEY `text_id_3` (`text_id`,`language_id`),
  CONSTRAINT `narro_suggestion_ibfk_7` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`),
  CONSTRAINT `narro_suggestion_ibfk_8` FOREIGN KEY (`text_id`) REFERENCES `narro_text` (`text_id`),
  CONSTRAINT `narro_suggestion_ibfk_9` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_suggestion_comment`
--

DROP TABLE IF EXISTS `narro_suggestion_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_suggestion_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `suggestion_id` integer(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `comment_text` text NOT NULL,
  `comment_text_md5` varchar(128) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`comment_id`),
  UNIQUE KEY `suggestion_id_2` (`suggestion_id`,`user_id`,`comment_text_md5`),
  KEY `suggestion_id` (`suggestion_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `narro_suggestion_comment_ibfk_4` FOREIGN KEY (`suggestion_id`) REFERENCES `narro_suggestion` (`suggestion_id`),
  CONSTRAINT `narro_suggestion_comment_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_suggestion_vote`
--

DROP TABLE IF EXISTS `narro_suggestion_vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_suggestion_vote` (
  `suggestion_id` integer(10) unsigned NOT NULL,
  `context_id` integer(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `vote_value` tinyint(3) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `suggestion_id` (`suggestion_id`,`user_id`,`context_id`),
  KEY `suggestion_id_2` (`suggestion_id`),
  KEY `user_id` (`user_id`),
  KEY `context_id` (`context_id`),
  CONSTRAINT `narro_suggestion_vote_ibfk_10` FOREIGN KEY (`suggestion_id`) REFERENCES `narro_suggestion` (`suggestion_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `narro_suggestion_vote_ibfk_7` FOREIGN KEY (`context_id`) REFERENCES `narro_context` (`context_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `narro_suggestion_vote_ibfk_9` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_text`
--

DROP TABLE IF EXISTS `narro_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_text` (
  `text_id` integer(10) unsigned NOT NULL AUTO_INCREMENT,
  `text_value` text NOT NULL,
  `text_value_md5` varchar(64) NOT NULL,
  `text_char_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `text_word_count` smallint(5) unsigned DEFAULT '0',
  `has_comments` tinyint(1) DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`text_id`),
  UNIQUE KEY `string_value_md5` (`text_value_md5`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_text_comment`
--

DROP TABLE IF EXISTS `narro_text_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_text_comment` (
  `text_comment_id` integer(10) unsigned NOT NULL AUTO_INCREMENT,
  `text_id` integer(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `comment_text` text NOT NULL,
  `comment_text_md5` varchar(128) NOT NULL,
  PRIMARY KEY (`text_comment_id`),
  KEY `text_id` (`text_id`),
  KEY `user_id` (`user_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `narro_text_comment_ibfk_10` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`),
  CONSTRAINT `narro_text_comment_ibfk_11` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`),
  CONSTRAINT `narro_text_comment_ibfk_9` FOREIGN KEY (`text_id`) REFERENCES `narro_text` (`text_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_user`
--

DROP TABLE IF EXISTS `narro_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_user` (
  `user_id` int(10) unsigned NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `data` text,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `narro_user_role`
--

DROP TABLE IF EXISTS `narro_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `narro_user_role` (
  `user_role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned DEFAULT NULL,
  `language_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_role_id`),
  UNIQUE KEY `user_id` (`user_id`,`role_id`,`project_id`,`language_id`),
  KEY `role_id` (`role_id`),
  KEY `project_id` (`project_id`),
  KEY `language_id` (`language_id`),
  KEY `user_id_2` (`user_id`),
  CONSTRAINT `narro_user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`),
  CONSTRAINT `narro_user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `narro_role` (`role_id`),
  CONSTRAINT `narro_user_role_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `narro_project` (`project_id`),
  CONSTRAINT `narro_user_role_ibfk_4` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zend_cache`
--

DROP TABLE IF EXISTS `zend_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zend_cache` (
  `id` varchar(255) NOT NULL,
  `content` text,
  `lastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `zend_cache_id_expire_index` (`id`,`expire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zend_cache_tag`
--

DROP TABLE IF EXISTS `zend_cache_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zend_cache_tag` (
  `name` text,
  `id` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zend_cache_version`
--

DROP TABLE IF EXISTS `zend_cache_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zend_cache_version` (
  `num` int(11) NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-09-10 11:04:03

INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES(NULL, 'DumbGettextPo');
INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES(NULL, 'Folder');
INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES(NULL, 'GettextPo');
INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES(NULL, 'Html');
INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES(NULL, 'MozillaDtd');
INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES(NULL, 'MozillaInc');
INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES(NULL, 'MozillaIni');
INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES(NULL, 'Narro');
INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES(NULL, 'OpenOfficeSdf');
INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES(NULL, 'PhpMyAdmin');
INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES(NULL, 'Svg');
INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES(NULL, 'Unsupported');

--
-- Dumping data for table `narro_language`
--

INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Romanian', 'ro', 'ro', NULL, 'UTF-8', 'ltr', 'ă î â ș ț Ă Î Â Ș Ț „ ” « »', '"Plural-Forms:  nplurals=3; plural=n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2;\\n"', 1);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'French', 'fr', 'fr', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Spanish', 'es', 'es', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'German', 'de', 'de', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Italian', 'it', 'it', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'English US', 'en-US', 'en-US', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Portuguese, Brazil', 'pt-BR', 'pt-BR', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Portuguese, Portugal', 'pt', 'pt', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Afrikaans', 'af', 'af', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Arabic', 'ar', 'ar', NULL, 'UTF-8', 'rtl', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Belarusian', 'be', 'be', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Bulgarian', 'bg', 'bg', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Catalan', 'ca', 'ca', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Czech', 'cs', 'cs', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Danish', 'da', 'da', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'English, UK', 'en-GB', 'en-GB', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'English (South African)', 'en-ZA', 'en-ZA', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Spanish, Argentina', 'es-AR', 'es-AR', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Frisian', 'fy-NL', 'fy-NL', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Basque', 'eu', 'eu', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Finnish', 'fi', 'fi', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Irish', 'ga-IE', 'ga-IE', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Gujarati', 'gu-IN', 'gu-IN', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Hebrew', 'he', 'he', NULL, 'UTF-8', 'rtl', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Hungarian', 'hu', 'hu', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Armenian', 'hy-AM', 'hy-AM', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Spanish, Spain', 'es-ES', 'es-ES', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Greek', 'el', 'el', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'lao', 'lo', 'la', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Kabardian', 'kbd', 'cyrl', NULL, 'UTF-8', 'ltr', 'э', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Malayalam', 'ml', 'ml', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Zapotec (OT19745)', 'zap-MX-OT19745', 'zap-MX-OT19745', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Latvian', 'lv', 'lv', NULL, 'UTF-8', 'ltr', 'ā č ē ģ ī ķ ļ ņ š ū ž Ā Č Ē Ģ Ī Ķ Ļ Ņ Š Ū Ž', '"Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2);\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Kazakh', 'kk', 'KZ', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Kabyle', 'kab', 'kab', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Romance, Spain (Valencian)', 'roa-ES-val', 'roa-ES-val', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Mayan', 'myn-MX', 'myn-MX', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Oromo', 'om', 'om', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Iloko', 'ilo-PH', 'ilo-PH', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Tagalog', 'tl-PH', 'tl-PH', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Fijian', 'fj-FJ', 'fj-FJ', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Latin', 'la', 'la', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Romansch', 'rm', 'rm', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Telugu', 'te', 'te', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Croatian', 'hr', 'hr', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Japanese', 'ja', 'ja', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Georgian', 'ka', 'ka', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Korean', 'ko', 'ko', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Kurdish', 'ku', 'ku', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Lithuanian', 'lt', 'lt', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Macedonian', 'mk', 'mk', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Mongolian', 'mn', 'mn', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Norwegian bokmaal', 'nb-NO', 'nb-NO', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Dutch', 'nl', 'nl', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Ndebele', 'nr', 'nr', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Northern Sotho', 'nso', 'nso', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Norwegian nynorsk', 'nn-NO', 'nn-NO', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Punjabi', 'pa-IN', 'pa-IN', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Polish', 'pl', 'pl', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Russian', 'ru', 'ru', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Slovak', 'sk', 'sk', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Slovenian', 'sl', 'sl', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Albanian', 'sq', 'sq', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Serbian', 'sr', 'sr', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 or n%100>=20) ? 1 : 2);\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Swati', 'ss', 'ss', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Southern Sotho', 'st', 'st', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Swedish', 'sv-SE', 'sv-SE', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Tswana', 'tn', 'tn', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Turkish', 'tr', 'tr', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Tsonga', 'ts', 'ts', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Ukrainian', 'uk', 'uk', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Venda', 've', 've', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Xhosa', 'xh', 'xh', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Chinese Simplified, China', 'zh-CN', 'zh-CN', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Chinese Traditional, Taiwan', 'zh-TW', 'zh-TW', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Zulu', 'zu', 'zu', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Urdu (Pakistan)', 'ur-PK', 'ur-PK', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Vietnamese', 'vi', 'vi', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Persian', 'fa', 'ir', NULL, 'UTF-8', 'rtl', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Spanish, Chile', 'es-CL', 'es-CL', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Uyghur', 'ug', 'ug', NULL, 'UTF-8', 'rtl', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Dzongkha', 'dz', 'dz', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Amharic', 'am', 'am', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals = 2; plural=(n > 1);\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Spanish, Mexico', 'es-MX', 'es-MX', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Bosnian', 'bs', 'bs', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10< =4 && (n%100<10 or n%100>=20) ? 1 : 2)\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Indonesian', 'id', 'id', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Thai', 'th', 'TH', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Tarahumara (Y08703)', 'tar-MX-Y08703', 'tar-MX-Y08703', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Assamese', 'as', 'as', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Friulian', 'fur', 'fur', NULL, 'UTF-8', 'ltr', 'â ê î ô û ç', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Tajik', 'tg-TJ', 'tg-TJ', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Spanish, Peru', 'es-PE', 'es-PE', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Spanish, Bolivia', 'es-BO', 'es-BO', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Dari (Eastern Persian)', 'ps-AF', 'ps-AF', NULL, 'UTF-8', 'rtl', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Asturian', 'ast', 'ast', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'X-testing', 'xx-XX', 'xx-XX', NULL, 'UTF-8', 'ltr', 'ă î ș ț â „ ”', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Quechua, Bolivia', 'qu-BO', 'qu-BO', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Norwegian hognorsk', 'nn-hognorsk', 'nn-hognorsk', NULL, 'UTF-8', 'ltr', '€', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Guaraní', 'grn', 'grn', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Azerbaijani', 'az', 'az', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Welsh', 'cy', 'cy', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Montenegrin', 'sla', 'sla', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Spanish, Colombia', 'es-CO', 'es-CO', NULL, 'UTF-8', 'ltr', 'á ¿', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Spanish, Venezuela', 'es-VE', 'es-VE', NULL, 'UTF-8', 'ltr', 'á ¿', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Aymara, Bolivia', 'ay', 'ay', NULL, 'UTF-8', 'ltr', 'ɲ', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Aragonese', 'an', 'an', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Galician', 'gl', 'gl', NULL, 'UTF-8', 'ltr', 'á ¿', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Scottish Gaelic', 'gd', 'gd', NULL, 'UTF-8', 'ltr', 'á ¿', '"Plural-Forms: nplurals=4; plural=(n%10==1 && n < 40) ? 0 : (n%10==2 && n < 40) ? 1 : (n==10 || (n%10 > 2 && n < 40)) ? 2 : 3\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Nepali', 'ne-NP', 'ne-NP', NULL, 'UTF-8', 'ltr', 'भ प् र भा त', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Bengali-Bangladesh', 'bn-BD', 'bn-BD', NULL, 'UTF-8', 'ltr', 'á ¿', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Hindi', 'hi-IN', 'hi-IN', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Breton', 'br', 'br', NULL, 'UTF-8', 'ltr', '', '', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Khmer', 'km', 'km', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=1; plural=0;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Haitian Creole', 'ht', 'ht', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Ligurian', 'lij', 'lij', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Kurdî, Soranî (Latin)', 'ckb-latin', 'ckb-latin', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Kurdî, Soranî (Aramic)', 'ckb-arabic', 'ckb-arabic', NULL, 'UTF-8', 'rtl', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Meadow Mari', 'mhr', 'mhr', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Kashubian', 'csb', 'csb', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=3; n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Guaraní, Bolivia', 'grn-BO', 'grn-BO', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Burmese (my-MM)', 'my-MM', 'my-MM', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Urdu', 'ur', 'ur', NULL, 'UTF-8', 'rtl', '', '"Plural-Forms: nplurals=3; n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Yoruba', 'yo', 'yo', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Tibetan', 'bo', 'bo', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Balkanika', 'blk', 'blk', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Esperanto', 'eo', 'eo', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);
INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES(NULL, 'Malay', 'ms', 'ms', NULL, 'UTF-8', 'ltr', 'a b c', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0);

--
-- Dumping data for table `narro_permission`
--

INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Administrator');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can add context comments');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can add language');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can add project');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can approve');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can comment');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can delete any suggestion');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can delete language');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can delete project');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can edit any suggestion');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can edit language');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can edit project');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can export file');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can export project');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can import file');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can import project');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can manage project');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can manage roles');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can manage user roles');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can manage users');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can mass approve');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can suggest');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can upload project');
INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES(NULL, 'Can vote');

--
-- Dumping data for table `narro_project_category`
--

INSERT INTO `narro_project_category` (`project_category_id`, `category_name`, `category_description`) VALUES(NULL, 'General', '');

--
-- Dumping data for table `narro_project_type`
--

INSERT INTO `narro_project_type` (`project_type_id`, `project_type`) VALUES(NULL, 'DumbGettextPo');
INSERT INTO `narro_project_type` (`project_type_id`, `project_type`) VALUES(NULL, 'Generic');
INSERT INTO `narro_project_type` (`project_type_id`, `project_type`) VALUES(NULL, 'Gettext');
INSERT INTO `narro_project_type` (`project_type_id`, `project_type`) VALUES(NULL, 'Html');
INSERT INTO `narro_project_type` (`project_type_id`, `project_type`) VALUES(NULL, 'Mozilla');
INSERT INTO `narro_project_type` (`project_type_id`, `project_type`) VALUES(NULL, 'Narro');
INSERT INTO `narro_project_type` (`project_type_id`, `project_type`) VALUES(NULL, 'OpenOffice');
INSERT INTO `narro_project_type` (`project_type_id`, `project_type`) VALUES(NULL, 'Svg');

--
-- Dumping data for table `narro_project`
--

INSERT INTO `narro_project` (`project_id`, `project_category_id`, `project_name`, `project_type`, `project_description`, `data`, `active`) VALUES(NULL, 1, 'Narro', (SELECT `project_type_id` FROM `narro_project_type` WHERE `project_type`='Narro'), '', '', 1);

--
-- Dumping data for table `narro_user`
--

INSERT INTO `narro_user` (`user_id`, `username`, `password`, `email`, `data`) VALUES(0, '', '', '', '');

--
-- Dumping data for table `narro_role`
--

INSERT INTO `narro_role` (`role_id`, `role_name`) VALUES(NULL, 'Administrator');
INSERT INTO `narro_role` (`role_id`, `role_name`) VALUES(NULL, 'Anonymous');
INSERT INTO `narro_role` (`role_id`, `role_name`) VALUES(NULL, 'Approver');
INSERT INTO `narro_role` (`role_id`, `role_name`) VALUES(NULL, 'Project manager');
INSERT INTO `narro_role` (`role_id`, `role_name`) VALUES(NULL, 'User');

--
-- Dumping data for table `zend_cache_version`
--

INSERT INTO `zend_cache_version` (`num`) VALUES(1);

ALTER TABLE `narro_context` ADD `text_command_key` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `text_access_key`;
ALTER TABLE `narro_context_info` ADD `suggestion_command_key` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `suggestion_access_key`;

ALTER TABLE `narro_file`
ADD UNIQUE `file_path` ( `file_path` , `project_id` )  ;
ALTER TABLE `narro_file` ADD INDEX ( `active` ) ;

CREATE TABLE IF NOT EXISTS `narro_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_id` int(10) unsigned DEFAULT NULL,
  `project_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `message` text NOT NULL,
  `priority` smallint(6) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `language_id` (`language_id`,`project_id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `narro_log`
  ADD CONSTRAINT `narro_log_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_log_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_log_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `narro_project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;