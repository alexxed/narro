SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE narro_context (
  context_id bigint(20) unsigned NOT NULL auto_increment,
  text_id bigint(20) unsigned NOT NULL,
  project_id int(10) unsigned NOT NULL,
  context text NOT NULL,
  context_md5 varchar(255) NOT NULL,
  file_id int(10) unsigned NOT NULL,
  created timestamp NOT NULL default '0000-00-00 00:00:00',
  modified timestamp NOT NULL default '0000-00-00 00:00:00',
  active tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (context_id),
  KEY string_id (text_id),
  KEY file_id (file_id),
  KEY project_id (project_id),
  KEY context_md5 (context_md5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_context_comment (
  comment_id bigint(20) unsigned NOT NULL auto_increment,
  context_id bigint(20) unsigned NOT NULL,
  user_id int(11) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  created timestamp NOT NULL default '0000-00-00 00:00:00',
  modified timestamp NOT NULL default '0000-00-00 00:00:00',
  comment_text text NOT NULL,
  comment_text_md5 varchar(128) NOT NULL,
  PRIMARY KEY  (comment_id),
  UNIQUE KEY context_id_2 (context_id,language_id,comment_text_md5),
  KEY context_id (context_id),
  KEY user_id (user_id),
  KEY language_id (language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_context_info (
  context_info_id bigint(20) unsigned NOT NULL auto_increment,
  context_id bigint(20) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  validator_user_id int(10) unsigned default NULL,
  valid_suggestion_id bigint(20) unsigned default NULL,
  popular_suggestion_id bigint(20) unsigned default NULL,
  has_comments tinyint(1) NOT NULL default '0',
  has_suggestions tinyint(1) unsigned default '0',
  text_access_key varchar(2) default NULL,
  suggestion_access_key varchar(2) default NULL,
  created timestamp NOT NULL default '0000-00-00 00:00:00',
  modified timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (context_info_id),
  UNIQUE KEY context_id_2 (context_id,language_id),
  KEY context_id (context_id),
  KEY language_id (language_id),
  KEY suggestion_id (valid_suggestion_id),
  KEY popular_suggestion_id (popular_suggestion_id),
  KEY validator_user_id (validator_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_file (
  file_id int(10) unsigned NOT NULL auto_increment,
  file_name varchar(255) NOT NULL,
  file_path varchar(255) NOT NULL,
  parent_id int(10) unsigned default NULL,
  type_id tinyint(3) unsigned NOT NULL,
  project_id int(10) unsigned NOT NULL,
  encoding varchar(16) NOT NULL default 'UTF-8',
  context_count int(10) unsigned default '0',
  active tinyint(1) NOT NULL default '1',
  created timestamp NOT NULL default '0000-00-00 00:00:00',
  modified timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (file_id),
  UNIQUE KEY file_name (file_name,parent_id),
  KEY type_id (type_id),
  KEY project_id (project_id),
  KEY parent_id (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_file_header (
  file_id int(10) unsigned NOT NULL,
  file_header blob NOT NULL,
  PRIMARY KEY  (file_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_file_type (
  file_type_id tinyint(3) unsigned NOT NULL auto_increment,
  file_type varchar(32) NOT NULL,
  PRIMARY KEY  (file_type_id),
  UNIQUE KEY UQ_qdrupal_narro_file_type_1 (file_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO narro_file_type VALUES(3, 'Folder');
INSERT INTO narro_file_type VALUES(1, 'GettextPo');
INSERT INTO narro_file_type VALUES(4, 'MozillaDtd');
INSERT INTO narro_file_type VALUES(7, 'MozillaInc');
INSERT INTO narro_file_type VALUES(5, 'MozillaIni');
INSERT INTO narro_file_type VALUES(6, 'Narro');
INSERT INTO narro_file_type VALUES(2, 'OpenOfficeSdf');
INSERT INTO narro_file_type VALUES(8, 'Svg');
INSERT INTO narro_file_type VALUES(9, 'DumbGettextPo');
INSERT INTO narro_file_type VALUES(10, 'PhpMyAdmin');

CREATE TABLE narro_language (
  language_id int(10) unsigned NOT NULL auto_increment,
  language_name varchar(128) NOT NULL,
  language_code varchar(6) NOT NULL,
  country_code varchar(6) NOT NULL,
  encoding varchar(10) NOT NULL,
  text_direction varchar(3) NOT NULL default 'ltr',
  special_characters varchar(255) default NULL,
  plural_form varchar(255) NOT NULL default '"Plural-Forms: nplurals=2; plural=(n != 1);\n"',
  PRIMARY KEY  (language_id),
  UNIQUE KEY language_name (language_name),
  UNIQUE KEY language_code (language_code)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(1, 'Romanian', 'ro', 'ro', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=3; plural=(n==1 ? 0 : (n==0 or (n%100 > 0 && n%100 < 20)) ? 1 : 2);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(2, 'French', 'fr', 'fr', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n > 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(3, 'Spanish', 'es-ES', 'es-ES', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(4, 'Afrikaans', 'af', 'af', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(5, 'Arabic', 'ar', 'ar', 'UTF-8', 'rtl', NULL, '"Plural-Forms: nplurals=6; plural= n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 && n%100<=99 ? 4 : 5;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(6, 'Belarusian', 'be', 'be', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10< =4 && (n%100<10 or n%100>=20) ? 1 : 2);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(7, 'Bulgarian', 'bg', 'bg', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(8, 'Catalan', 'ca', 'ca', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(9, 'Czech', 'cs', 'cs', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=3; plural=(n==1) ? 0 : (n>=2 && n< =4) ? 1 : 2;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(10, 'Danish', 'da', 'da', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(11, 'German', 'de', 'de', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(12, 'Greek', 'el', 'el', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(13, 'English, UK', 'en-GB', 'en-GB', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(14, 'English (South African)', 'en-ZA', 'en-ZA', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(15, 'Spanish, Argentina', 'es-AR', 'es-AR', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(16, 'Frisian', 'fy-NL', 'fy-NL', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(17, 'Basque', 'eu', 'eu', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(18, 'Finnish', 'fi', 'fi', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(19, 'Irish', 'ga-IE', 'ga-IE', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=5; plural=n==1 ? 0 : n==2 ? 1 : n<7 ? 2 : n<11 ? 3 : 4;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(20, 'Gujarati', 'gu-IN', 'gu-IN', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(21, 'Hebrew', 'he', 'he', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(22, 'Hungarian', 'hu', 'hu', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=1; plural=0;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(23, 'Armenian', 'hy-AM', 'hy-AM', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=1; plural=0;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(24, 'Italian', 'it', 'it', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(25, 'Japanese', 'ja', 'ja', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=1; plural=0;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(26, 'Georgian', 'ka', 'ka', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=1; plural=0;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(27, 'Korean', 'ko', 'ko', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=1; plural=0;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(28, 'Kurdish', 'ku', 'ku', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n!= 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(29, 'Lithuanian', 'lt', 'lt', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 or n%100>=20) ? 1 : 2);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(30, 'Macedonian', 'mk', 'mk', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=3; plural= n==1 or n%10==1 ? 0 : 1;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(31, 'Mongolian', 'mn', 'mn', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(32, 'Norwegian bokmaal', 'nb-NO', 'nb-NO', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(33, 'Dutch', 'nl', 'nl', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(34, 'Ndebele', 'nr', 'nr', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(35, 'Northern Sotho', 'nso', 'nso', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n > 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(36, 'Norwegian nynorsk', 'nn-NO', 'nn-NO', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(37, 'Punjabi', 'pa-IN', 'pa-IN', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(38, 'Polish', 'pl', 'pl', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10< =4 && (n%100<10 or n%100>=20) ? 1 : 2);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(39, 'Portuguese, Brazil', 'pt-BR', 'pt-BR', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n > 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(40, 'Portuguese, Portugal', 'pt-PT', 'pt-PT', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(41, 'Russian', 'ru', 'ru', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10< =4 && (n%100<10 or n%100>=20) ? 1 : 2);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(42, 'Slovak', 'sk', 'sk', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(43, 'Slovenian', 'sl', 'sl', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=4; plural=(n%100==1 ? 0 : n%100==2 ? 1 : n%100==3 or n%100==4 ? 2 : 3);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(44, 'Albanian', 'sq', 'sq', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(45, 'Serbian', 'sr', 'sr', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=4; plural=n==1? 3 : n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 or n%100>=20) ? 1 : 2;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(46, 'Swati', 'ss', 'ss', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(47, 'Southern Sotho', 'st', 'st', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(48, 'Swedish', 'sv-SE', 'sv-SE', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1) ;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(49, 'Tswana', 'tn', 'tn', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(50, 'Turkish', 'tr', 'tr', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=1; plural=0;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(51, 'Tsonga', 'ts', 'ts', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(52, 'Ukrainian', 'uk', 'uk', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10< =4 && (n%100<10 or n%100>=20) ? 1 : 2);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(53, 'Venda', 've', 've', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(54, 'Xhosa', 'xh', 'xh', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(55, 'Chinese Simplified, China', 'zh-CN', 'zh-CN', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=1; plural=0;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(56, 'Chinese Traditional, Taiwan', 'zh-TW', 'zh-TW', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=1; plural=0;\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(57, 'Zulu', 'zu', 'zu', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(58, 'English US', 'en-US', 'en-US', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(59, 'Urdu (Pakistan)', 'ur-PK', 'ur-PK', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"');
INSERT INTO narro_language (language_id, language_name, language_code, country_code, encoding, text_direction, special_characters, plural_form) VALUES(60, 'Vietnamese', 'vi', 'vi', 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=1; plural=0;\\n"');

CREATE TABLE narro_permission (
  permission_id int(10) unsigned NOT NULL auto_increment,
  permission_name varchar(128) NOT NULL,
  PRIMARY KEY  (permission_id),
  UNIQUE KEY permission_name (permission_name)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO narro_permission VALUES(12, 'Administrator');
INSERT INTO narro_permission VALUES(16, 'Can add language');
INSERT INTO narro_permission VALUES(14, 'Can add project');
INSERT INTO narro_permission VALUES(4, 'Can comment');
INSERT INTO narro_permission VALUES(5, 'Can delete any suggestion');
INSERT INTO narro_permission VALUES(17, 'Can delete language');
INSERT INTO narro_permission VALUES(11, 'Can delete project');
INSERT INTO narro_permission VALUES(6, 'Can edit any suggestion');
INSERT INTO narro_permission VALUES(15, 'Can edit language');
INSERT INTO narro_permission VALUES(13, 'Can edit project');
INSERT INTO narro_permission VALUES(9, 'Can export file');
INSERT INTO narro_permission VALUES(8, 'Can import file');
INSERT INTO narro_permission VALUES(10, 'Can manage project');
INSERT INTO narro_permission VALUES(7, 'Can manage users');
INSERT INTO narro_permission VALUES(1, 'Can suggest');
INSERT INTO narro_permission VALUES(3, 'Can validate');
INSERT INTO narro_permission VALUES(2, 'Can vote');
INSERT INTO narro_permission VALUES(18, 'Can import project');
INSERT INTO narro_permission VALUES(19, 'Can export project');
INSERT INTO narro_permission VALUES(20, 'Can upload project');


CREATE TABLE narro_project (
  project_id int(10) unsigned NOT NULL auto_increment,
  project_name varchar(255) NOT NULL,
  project_type smallint(5) unsigned NOT NULL,
  active tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY  (project_id),
  UNIQUE KEY project_name (project_name),
  KEY project_type (project_type)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO narro_project VALUES(1, 'Narro', 4, 1);

CREATE TABLE narro_project_type (
  project_type_id smallint(5) unsigned NOT NULL auto_increment,
  project_type varchar(64) NOT NULL,
  PRIMARY KEY  (project_type_id),
  UNIQUE KEY project_type (project_type)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO narro_project_type VALUES(3, 'Gettext');
INSERT INTO narro_project_type VALUES(1, 'Mozilla');
INSERT INTO narro_project_type VALUES(4, 'Narro');
INSERT INTO narro_project_type VALUES(2, 'OpenOffice');
INSERT INTO narro_project_type VALUES(5, 'Svg');
INSERT INTO narro_project_type VALUES(6, 'DumbGettextPo');
INSERT INTO narro_project_type VALUES(7, 'Generic');

CREATE TABLE narro_suggestion (
  suggestion_id bigint(20) unsigned NOT NULL auto_increment,
  user_id int(10) unsigned default NULL,
  text_id bigint(20) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  suggestion_value text NOT NULL,
  suggestion_value_md5 varchar(128) NOT NULL,
  suggestion_char_count int(10) unsigned NOT NULL,
  has_comments tinyint(1) default '0',
  created timestamp NOT NULL default '0000-00-00 00:00:00',
  modified timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (suggestion_id),
  UNIQUE KEY text_id_2 (text_id,language_id,suggestion_value_md5),
  KEY user_id (user_id),
  KEY text_id (text_id),
  KEY language_id (language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_suggestion_comment (
  comment_id int(10) unsigned NOT NULL auto_increment,
  suggestion_id bigint(20) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  comment_text text NOT NULL,
  created timestamp NOT NULL default '0000-00-00 00:00:00',
  modified timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (comment_id),
  KEY suggestion_id (suggestion_id),
  KEY user_id (user_id),
  KEY language_id (language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_suggestion_vote (
  suggestion_id bigint(20) unsigned NOT NULL,
  context_id bigint(20) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  vote_value tinyint(3) NOT NULL,
  created timestamp NOT NULL default '0000-00-00 00:00:00',
  modified timestamp NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY suggestion_id (suggestion_id,user_id,context_id),
  KEY suggestion_id_2 (suggestion_id),
  KEY user_id (user_id),
  KEY context_id (context_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_text (
  text_id bigint(20) unsigned NOT NULL auto_increment,
  text_value text NOT NULL,
  text_value_md5 varchar(64) NOT NULL,
  text_char_count smallint(5) unsigned NOT NULL default '0',
  has_comments tinyint(1) default '0',
  created timestamp NOT NULL default '0000-00-00 00:00:00',
  modified timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (text_id),
  UNIQUE KEY string_value_md5 (text_value_md5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_text_comment (
  text_comment_id bigint(20) unsigned NOT NULL auto_increment,
  text_id bigint(20) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  created timestamp NOT NULL default '0000-00-00 00:00:00',
  modified timestamp NOT NULL default '0000-00-00 00:00:00',
  comment_text text NOT NULL,
  comment_text_md5 varchar(128) NOT NULL,
  PRIMARY KEY  (text_comment_id),
  UNIQUE KEY text_id_2 (text_id,user_id,comment_text_md5),
  KEY text_id (text_id),
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE narro_user (
  user_id int(10) unsigned NOT NULL,
  username varchar(128) NOT NULL,
  password varchar(64) NOT NULL,
  email varchar(128) NOT NULL,
  data text,
  PRIMARY KEY  (user_id),
  UNIQUE KEY username (username),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO narro_user VALUES(0, '', '', '', NULL);

CREATE TABLE narro_user_permission (
  user_permission_id int(10) unsigned NOT NULL auto_increment,
  user_id int(10) unsigned NOT NULL,
  permission_id int(10) unsigned NOT NULL,
  project_id int(10) unsigned default NULL,
  language_id int(10) unsigned default NULL,
  PRIMARY KEY  (user_permission_id),
  UNIQUE KEY user_id_2 (user_id,permission_id,project_id,language_id),
  KEY user_id (user_id),
  KEY permission_id (permission_id),
  KEY project_id (project_id),
  KEY language_id (language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE narro_context
  ADD CONSTRAINT narro_context_ibfk_13 FOREIGN KEY (text_id) REFERENCES narro_text (text_id),
  ADD CONSTRAINT narro_context_ibfk_14 FOREIGN KEY (project_id) REFERENCES narro_project (project_id),
  ADD CONSTRAINT narro_context_ibfk_15 FOREIGN KEY (file_id) REFERENCES narro_file (file_id);

ALTER TABLE narro_context_comment
  ADD CONSTRAINT narro_context_comment_ibfk_4 FOREIGN KEY (context_id) REFERENCES narro_context (context_id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT narro_context_comment_ibfk_5 FOREIGN KEY (user_id) REFERENCES narro_user (user_id),
  ADD CONSTRAINT narro_context_comment_ibfk_6 FOREIGN KEY (language_id) REFERENCES narro_language (language_id);

ALTER TABLE narro_context_info
  ADD CONSTRAINT narro_context_info_ibfk_10 FOREIGN KEY (popular_suggestion_id) REFERENCES narro_suggestion (suggestion_id) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT narro_context_info_ibfk_15 FOREIGN KEY (validator_user_id) REFERENCES narro_user (user_id) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT narro_context_info_ibfk_17 FOREIGN KEY (context_id) REFERENCES narro_context (context_id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT narro_context_info_ibfk_18 FOREIGN KEY (language_id) REFERENCES narro_language (language_id),
  ADD CONSTRAINT narro_context_info_ibfk_9 FOREIGN KEY (valid_suggestion_id) REFERENCES narro_suggestion (suggestion_id) ON DELETE SET NULL ON UPDATE SET NULL;

ALTER TABLE narro_file
  ADD CONSTRAINT narro_file_ibfk_10 FOREIGN KEY (project_id) REFERENCES narro_project (project_id),
  ADD CONSTRAINT narro_file_ibfk_4 FOREIGN KEY (parent_id) REFERENCES narro_file (file_id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT narro_file_ibfk_9 FOREIGN KEY (type_id) REFERENCES narro_file_type (file_type_id);

ALTER TABLE narro_file_header
  ADD CONSTRAINT narro_file_header_ibfk_1 FOREIGN KEY (file_id) REFERENCES narro_file (file_id);

ALTER TABLE narro_project
  ADD CONSTRAINT narro_project_ibfk_1 FOREIGN KEY (project_type) REFERENCES narro_project_type (project_type_id);

ALTER TABLE narro_suggestion
  ADD CONSTRAINT narro_suggestion_ibfk_7 FOREIGN KEY (user_id) REFERENCES narro_user (user_id),
  ADD CONSTRAINT narro_suggestion_ibfk_8 FOREIGN KEY (text_id) REFERENCES narro_text (text_id),
  ADD CONSTRAINT narro_suggestion_ibfk_9 FOREIGN KEY (language_id) REFERENCES narro_language (language_id);

ALTER TABLE narro_suggestion_comment
  ADD CONSTRAINT narro_suggestion_comment_ibfk_1 FOREIGN KEY (suggestion_id) REFERENCES narro_suggestion (suggestion_id),
  ADD CONSTRAINT narro_suggestion_comment_ibfk_2 FOREIGN KEY (user_id) REFERENCES narro_user (user_id),
  ADD CONSTRAINT narro_suggestion_comment_ibfk_3 FOREIGN KEY (language_id) REFERENCES narro_language (language_id);

ALTER TABLE narro_suggestion_vote
  ADD CONSTRAINT narro_suggestion_vote_ibfk_10 FOREIGN KEY (suggestion_id) REFERENCES narro_suggestion (suggestion_id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT narro_suggestion_vote_ibfk_7 FOREIGN KEY (context_id) REFERENCES narro_context (context_id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT narro_suggestion_vote_ibfk_9 FOREIGN KEY (user_id) REFERENCES narro_user (user_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE narro_text_comment
  ADD CONSTRAINT narro_text_comment_ibfk_3 FOREIGN KEY (text_id) REFERENCES narro_text (text_id),
  ADD CONSTRAINT narro_text_comment_ibfk_4 FOREIGN KEY (user_id) REFERENCES narro_user (user_id);

ALTER TABLE narro_user_permission
  ADD CONSTRAINT narro_user_permission_ibfk_12 FOREIGN KEY (user_id) REFERENCES narro_user (user_id),
  ADD CONSTRAINT narro_user_permission_ibfk_13 FOREIGN KEY (permission_id) REFERENCES narro_permission (permission_id),
  ADD CONSTRAINT narro_user_permission_ibfk_14 FOREIGN KEY (project_id) REFERENCES narro_project (project_id),
  ADD CONSTRAINT narro_user_permission_ibfk_15 FOREIGN KEY (language_id) REFERENCES narro_language (language_id);

SET FOREIGN_KEY_CHECKS=1;