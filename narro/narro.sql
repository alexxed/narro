SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE narro_context (
  context_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  text_id bigint(20) unsigned NOT NULL,
  project_id int(10) unsigned NOT NULL,
  `context` text NOT NULL,
  context_md5 varchar(255) NOT NULL,
  file_id int(10) unsigned NOT NULL,
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified datetime DEFAULT '0000-00-00 00:00:00',
  active tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (context_id),
  UNIQUE KEY text_id (text_id,context_md5,file_id),
  KEY string_id (text_id),
  KEY file_id (file_id),
  KEY project_id (project_id),
  KEY context_md5 (context_md5),
  KEY active (active,project_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_context_comment (
  comment_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  context_id bigint(20) unsigned NOT NULL,
  user_id int(11) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified datetime DEFAULT '0000-00-00 00:00:00',
  comment_text text NOT NULL,
  comment_text_md5 varchar(128) NOT NULL,
  PRIMARY KEY (comment_id),
  KEY context_id (context_id),
  KEY user_id (user_id),
  KEY language_id (language_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
CREATE TABLE narro_context_info (
  context_info_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  context_id bigint(20) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  validator_user_id int(10) unsigned DEFAULT NULL,
  valid_suggestion_id bigint(20) unsigned DEFAULT NULL,
  popular_suggestion_id bigint(20) unsigned DEFAULT NULL,
  has_comments tinyint(1) NOT NULL DEFAULT '0',
  has_suggestions tinyint(1) unsigned DEFAULT '0',
  text_access_key varchar(2) DEFAULT NULL,
  suggestion_access_key varchar(2) DEFAULT NULL,
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (context_info_id),
  UNIQUE KEY context_id_2 (context_id,language_id),
  KEY context_id (context_id),
  KEY language_id (language_id),
  KEY suggestion_id (valid_suggestion_id),
  KEY popular_suggestion_id (popular_suggestion_id),
  KEY validator_user_id (validator_user_id),
  KEY created (created),
  KEY modified (modified)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_file (
  file_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  file_name varchar(255) NOT NULL,
  file_path varchar(255) NOT NULL,
  file_md5 varchar(32) DEFAULT NULL,
  parent_id int(10) unsigned DEFAULT NULL,
  type_id tinyint(3) unsigned NOT NULL,
  project_id int(10) unsigned NOT NULL,
  active tinyint(1) NOT NULL DEFAULT '1',
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (file_id),
  UNIQUE KEY file_name (file_name,parent_id),
  KEY type_id (type_id),
  KEY project_id (project_id),
  KEY parent_id (parent_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_file_progress (
  file_progress_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  file_id int(10) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  total_text_count int(10) NOT NULL,
  approved_text_count int(10) NOT NULL,
  fuzzy_text_count int(10) NOT NULL,
  progress_percent int(10) NOT NULL,
  PRIMARY KEY (file_progress_id),
  UNIQUE KEY file_id (file_id,language_id),
  KEY language_id (language_id),
  KEY file_id_2 (file_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_file_type (
  file_type_id tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  file_type varchar(32) NOT NULL,
  PRIMARY KEY (file_type_id),
  UNIQUE KEY UQ_qdrupal_narro_file_type_1 (file_type)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_language (
  language_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  language_name varchar(128) NOT NULL,
  language_code varchar(64) NOT NULL,
  country_code varchar(64) NOT NULL,
  dialect_code varchar(64) DEFAULT NULL,
  encoding varchar(10) NOT NULL,
  text_direction varchar(3) NOT NULL DEFAULT 'ltr',
  special_characters varchar(255) DEFAULT NULL,
  plural_form varchar(255) NOT NULL DEFAULT '"Plural-Forms: nplurals=2; plural=n != 1;\\n"',
  active tinyint(1) DEFAULT '1',
  PRIMARY KEY (language_id),
  UNIQUE KEY language_name (language_name),
  UNIQUE KEY language_code (language_code)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_permission (
  permission_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  permission_name varchar(128) NOT NULL,
  PRIMARY KEY (permission_id),
  UNIQUE KEY permission_name (permission_name)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_project (
  project_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  project_category_id int(11) unsigned DEFAULT '1',
  project_name varchar(255) NOT NULL,
  project_type smallint(5) unsigned NOT NULL,
  project_description varchar(255) DEFAULT NULL,
  active tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (project_id),
  UNIQUE KEY project_name (project_name),
  KEY project_type (project_type),
  KEY narro_project_ibfk_2 (project_category_id),
  KEY active (active)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_project_category (
  project_category_id int(11) unsigned NOT NULL AUTO_INCREMENT,
  category_name varchar(255) NOT NULL,
  category_description varchar(255) NOT NULL,
  PRIMARY KEY (project_category_id),
  UNIQUE KEY category_name (category_name)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_project_progress (
  project_progress_id int(10) NOT NULL AUTO_INCREMENT,
  project_id int(10) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  last_modified datetime NOT NULL,
  total_text_count int(10) unsigned NOT NULL,
  fuzzy_text_count int(10) unsigned NOT NULL,
  approved_text_count int(10) unsigned NOT NULL,
  progress_percent int(10) unsigned NOT NULL,
  PRIMARY KEY (project_progress_id),
  UNIQUE KEY project_id (project_id,language_id),
  KEY language_id (language_id),
  KEY project_id_2 (project_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_project_type (
  project_type_id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  project_type varchar(64) NOT NULL,
  PRIMARY KEY (project_type_id),
  UNIQUE KEY project_type (project_type)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_role (
  role_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  role_name varchar(128) NOT NULL,
  PRIMARY KEY (role_id),
  UNIQUE KEY role_name (role_name)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_role_permission (
  role_permission_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  role_id int(10) unsigned NOT NULL,
  permission_id int(10) unsigned NOT NULL,
  PRIMARY KEY (role_permission_id),
  KEY role_id (role_id),
  KEY permission_id (permission_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_suggestion (
  suggestion_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(10) unsigned DEFAULT NULL,
  text_id bigint(20) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  suggestion_value text NOT NULL,
  suggestion_value_md5 varchar(128) NOT NULL,
  suggestion_char_count smallint(5) unsigned DEFAULT '0',
  suggestion_word_count smallint(5) unsigned DEFAULT '0',
  has_comments tinyint(1) DEFAULT '0',
  is_imported tinyint(1) NOT NULL DEFAULT '0',
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (suggestion_id),
  UNIQUE KEY text_id_2 (text_id,language_id,suggestion_value_md5),
  KEY user_id (user_id),
  KEY text_id (text_id),
  KEY language_id (language_id),
  KEY text_id_3 (text_id,language_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_suggestion_comment (
  comment_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  suggestion_id bigint(20) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  comment_text text NOT NULL,
  comment_text_md5 varchar(128) NOT NULL,
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (comment_id),
  UNIQUE KEY suggestion_id_2 (suggestion_id,user_id,comment_text_md5),
  KEY suggestion_id (suggestion_id),
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_suggestion_vote (
  suggestion_id bigint(20) unsigned NOT NULL,
  context_id bigint(20) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  vote_value tinyint(3) NOT NULL,
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified datetime DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY suggestion_id (suggestion_id,user_id,context_id),
  KEY suggestion_id_2 (suggestion_id),
  KEY user_id (user_id),
  KEY context_id (context_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_text (
  text_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  text_value text NOT NULL,
  text_value_md5 varchar(64) NOT NULL,
  text_char_count smallint(5) unsigned NOT NULL DEFAULT '0',
  text_word_count smallint(5) unsigned DEFAULT '0',
  has_comments tinyint(1) DEFAULT '0',
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (text_id),
  UNIQUE KEY text_value_md5 (text_value_md5)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_text_comment (
  text_comment_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  text_id bigint(20) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified datetime DEFAULT '0000-00-00 00:00:00',
  comment_text text NOT NULL,
  comment_text_md5 varchar(128) NOT NULL,
  PRIMARY KEY (text_comment_id),
  KEY text_id (text_id),
  KEY user_id (user_id),
  KEY language_id (language_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE narro_user (
  user_id int(10) unsigned NOT NULL,
  username varchar(128) NOT NULL,
  `password` varchar(64) NOT NULL,
  email varchar(128) NOT NULL,
  `data` text,
  PRIMARY KEY (user_id),
  UNIQUE KEY username (username),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_user_role (
  user_role_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(10) unsigned NOT NULL,
  role_id int(10) unsigned NOT NULL,
  project_id int(10) unsigned DEFAULT NULL,
  language_id int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (user_role_id),
  UNIQUE KEY user_id (user_id,role_id,project_id,language_id),
  KEY role_id (role_id),
  KEY project_id (project_id),
  KEY language_id (language_id),
  KEY user_id_2 (user_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `narro_context`
  ADD CONSTRAINT narro_context_ibfk_13 FOREIGN KEY (text_id) REFERENCES narro_text (text_id),
  ADD CONSTRAINT narro_context_ibfk_14 FOREIGN KEY (project_id) REFERENCES narro_project (project_id),
  ADD CONSTRAINT narro_context_ibfk_15 FOREIGN KEY (file_id) REFERENCES narro_file (file_id);
ALTER TABLE `narro_context_comment`
  ADD CONSTRAINT narro_context_comment_ibfk_4 FOREIGN KEY (context_id) REFERENCES narro_context (context_id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT narro_context_comment_ibfk_5 FOREIGN KEY (user_id) REFERENCES narro_user (user_id),
  ADD CONSTRAINT narro_context_comment_ibfk_6 FOREIGN KEY (language_id) REFERENCES narro_language (language_id);
ALTER TABLE `narro_context_info`
  ADD CONSTRAINT narro_context_info_ibfk_10 FOREIGN KEY (popular_suggestion_id) REFERENCES narro_suggestion (suggestion_id) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT narro_context_info_ibfk_15 FOREIGN KEY (validator_user_id) REFERENCES narro_user (user_id) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT narro_context_info_ibfk_17 FOREIGN KEY (context_id) REFERENCES narro_context (context_id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT narro_context_info_ibfk_18 FOREIGN KEY (language_id) REFERENCES narro_language (language_id),
  ADD CONSTRAINT narro_context_info_ibfk_9 FOREIGN KEY (valid_suggestion_id) REFERENCES narro_suggestion (suggestion_id) ON DELETE SET NULL ON UPDATE SET NULL;
ALTER TABLE `narro_file`
  ADD CONSTRAINT narro_file_ibfk_10 FOREIGN KEY (project_id) REFERENCES narro_project (project_id),
  ADD CONSTRAINT narro_file_ibfk_4 FOREIGN KEY (parent_id) REFERENCES narro_file (file_id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT narro_file_ibfk_9 FOREIGN KEY (type_id) REFERENCES narro_file_type (file_type_id);
ALTER TABLE `narro_file_progress`
  ADD CONSTRAINT narro_file_progress_ibfk_1 FOREIGN KEY (file_id) REFERENCES narro_file (file_id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT narro_file_progress_ibfk_2 FOREIGN KEY (language_id) REFERENCES narro_language (language_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `narro_project`
  ADD CONSTRAINT narro_project_ibfk_1 FOREIGN KEY (project_type) REFERENCES narro_project_type (project_type_id),
  ADD CONSTRAINT narro_project_ibfk_2 FOREIGN KEY (project_category_id) REFERENCES narro_project_category (project_category_id);
ALTER TABLE `narro_project_progress`
  ADD CONSTRAINT narro_project_progress_ibfk_1 FOREIGN KEY (project_id) REFERENCES narro_project (project_id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT narro_project_progress_ibfk_2 FOREIGN KEY (language_id) REFERENCES narro_language (language_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `narro_role_permission`
  ADD CONSTRAINT narro_role_permission_ibfk_1 FOREIGN KEY (role_id) REFERENCES narro_role (role_id),
  ADD CONSTRAINT narro_role_permission_ibfk_2 FOREIGN KEY (permission_id) REFERENCES narro_permission (permission_id);
ALTER TABLE `narro_suggestion`
  ADD CONSTRAINT narro_suggestion_ibfk_7 FOREIGN KEY (user_id) REFERENCES narro_user (user_id),
  ADD CONSTRAINT narro_suggestion_ibfk_8 FOREIGN KEY (text_id) REFERENCES narro_text (text_id),
  ADD CONSTRAINT narro_suggestion_ibfk_9 FOREIGN KEY (language_id) REFERENCES narro_language (language_id);
ALTER TABLE `narro_suggestion_comment`
  ADD CONSTRAINT narro_suggestion_comment_ibfk_4 FOREIGN KEY (suggestion_id) REFERENCES narro_suggestion (suggestion_id),
  ADD CONSTRAINT narro_suggestion_comment_ibfk_5 FOREIGN KEY (user_id) REFERENCES narro_user (user_id);
ALTER TABLE `narro_suggestion_vote`
  ADD CONSTRAINT narro_suggestion_vote_ibfk_10 FOREIGN KEY (suggestion_id) REFERENCES narro_suggestion (suggestion_id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT narro_suggestion_vote_ibfk_7 FOREIGN KEY (context_id) REFERENCES narro_context (context_id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT narro_suggestion_vote_ibfk_9 FOREIGN KEY (user_id) REFERENCES narro_user (user_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `narro_text_comment`
  ADD CONSTRAINT narro_text_comment_ibfk_10 FOREIGN KEY (user_id) REFERENCES narro_user (user_id),
  ADD CONSTRAINT narro_text_comment_ibfk_11 FOREIGN KEY (language_id) REFERENCES narro_language (language_id),
  ADD CONSTRAINT narro_text_comment_ibfk_9 FOREIGN KEY (text_id) REFERENCES narro_text (text_id);
ALTER TABLE `narro_user_role`
  ADD CONSTRAINT narro_user_role_ibfk_1 FOREIGN KEY (user_id) REFERENCES narro_user (user_id),
  ADD CONSTRAINT narro_user_role_ibfk_2 FOREIGN KEY (role_id) REFERENCES narro_role (role_id),
  ADD CONSTRAINT narro_user_role_ibfk_3 FOREIGN KEY (project_id) REFERENCES narro_project (project_id),
  ADD CONSTRAINT narro_user_role_ibfk_4 FOREIGN KEY (language_id) REFERENCES narro_language (language_id);

INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES
(9, 'DumbGettextPo'),
(3, 'Folder'),
(1, 'GettextPo'),
(4, 'MozillaDtd'),
(7, 'MozillaInc'),
(5, 'MozillaIni'),
(6, 'Narro'),
(2, 'OpenOfficeSdf'),
(10, 'PhpMyAdmin'),
(8, 'Svg'),
(11, 'Unsupported');

INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES
(1, 'Romanian', 'ro', 'ro', NULL, 'UTF-8', 'ltr', 'ă î ș ț â „” Ă Î Ș Ț Â « »', '"Plural-Forms:  nplurals=3; plural=n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2;\\n"', 1),
(2, 'French', 'fr', 'fr', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(3, 'Spanish', 'es', 'es', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(11, 'German', 'de', 'de', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(24, 'Italian', 'it', 'it', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(58, 'English US', 'en-US', 'en-US', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(59, 'Portuguese, Brazil', 'pt-BR', 'pt-BR', NULL, 'UTF-8', 'ltr', '', '', 0),
(60, 'Portuguese, Portugal', 'pt', 'pt', NULL, 'UTF-8', 'ltr', '', '', 0);


INSERT INTO `narro_permission` (`permission_id`, `permission_name`) VALUES
(12, 'Administrator'),
(24, 'Can add context comments'),
(16, 'Can add language'),
(14, 'Can add project'),
(3, 'Can approve'),
(4, 'Can comment'),
(5, 'Can delete any suggestion'),
(17, 'Can delete language'),
(11, 'Can delete project'),
(6, 'Can edit any suggestion'),
(15, 'Can edit language'),
(13, 'Can edit project'),
(9, 'Can export file'),
(19, 'Can export project'),
(8, 'Can import file'),
(18, 'Can import project'),
(10, 'Can manage project'),
(21, 'Can manage roles'),
(23, 'Can manage user roles'),
(7, 'Can manage users'),
(22, 'Can mass approve'),
(1, 'Can suggest'),
(20, 'Can upload project'),
(2, 'Can vote');

INSERT INTO `narro_project_category` (`project_category_id`, `category_name`, `category_description`) VALUES
(1, 'General', '');


INSERT INTO `narro_project_type` (`project_type_id`, `project_type`) VALUES
(6, 'DumbGettextPo'),
(7, 'Generic'),
(3, 'Gettext'),
(1, 'Mozilla'),
(4, 'Narro'),
(2, 'OpenOffice'),
(5, 'Svg');

INSERT INTO `narro_role` (`role_id`, `role_name`) VALUES
(5, 'Administrator'),
(1, 'Anonymous'),
(3, 'Approver'),
(4, 'Project manager'),
(2, 'User');

INSERT INTO `narro_role_permission` (`role_permission_id`, `role_id`, `permission_id`) VALUES
(1, 2, 4),
(2, 2, 2),
(3, 2, 1),
(4, 3, 4),
(5, 3, 2),
(6, 3, 1),
(9, 4, 4),
(12, 4, 9),
(13, 4, 19),
(14, 4, 8),
(15, 4, 18),
(16, 4, 10),
(18, 4, 1),
(19, 4, 20),
(20, 4, 3),
(21, 4, 2),
(22, 5, 12),
(23, 5, 16),
(24, 5, 14),
(25, 5, 4),
(26, 5, 5),
(27, 5, 17),
(28, 5, 11),
(29, 5, 6),
(30, 5, 15),
(31, 5, 13),
(32, 5, 9),
(33, 5, 19),
(34, 5, 8),
(35, 5, 18),
(36, 5, 10),
(37, 5, 21),
(38, 5, 7),
(39, 5, 1),
(40, 5, 20),
(41, 5, 3),
(42, 5, 2),
(43, 3, 3),
(44, 3, 22),
(45, 4, 22);

INSERT INTO `narro_user` (`user_id`, `username`, `password`, `email`, `data`) VALUES
(0, '', '', '', '');