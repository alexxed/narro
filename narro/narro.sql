SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `narro`
--

-- --------------------------------------------------------

--
-- Table structure for table `narro_context`
--

CREATE TABLE IF NOT EXISTS `narro_context` (
  `context_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `text_id` bigint(20) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `context` text NOT NULL,
  `context_md5` varchar(255) NOT NULL,
  `comment` text,
  `comment_md5` varchar(255) DEFAULT NULL,
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
  KEY `project_id_2` (`active`,`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `narro_context`
--


-- --------------------------------------------------------

--
-- Table structure for table `narro_context_info`
--

CREATE TABLE IF NOT EXISTS `narro_context_info` (
  `context_info_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `context_id` bigint(20) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `validator_user_id` int(10) unsigned DEFAULT NULL,
  `valid_suggestion_id` bigint(20) unsigned DEFAULT NULL,
  `popular_suggestion_id` bigint(20) unsigned DEFAULT NULL,
  `has_suggestions` tinyint(1) unsigned DEFAULT '0',
  `text_access_key` varchar(2) DEFAULT NULL,
  `suggestion_access_key` varchar(2) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`context_info_id`),
  UNIQUE KEY `context_id_2` (`context_id`,`language_id`),
  KEY `context_id` (`context_id`),
  KEY `language_id` (`language_id`),
  KEY `suggestion_id` (`valid_suggestion_id`),
  KEY `popular_suggestion_id` (`popular_suggestion_id`),
  KEY `validator_user_id` (`validator_user_id`),
  KEY `created` (`created`),
  KEY `modified` (`modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `narro_context_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `narro_file`
--

CREATE TABLE IF NOT EXISTS `narro_file` (
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
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `narro_file`
--


-- --------------------------------------------------------

--
-- Table structure for table `narro_file_progress`
--

CREATE TABLE IF NOT EXISTS `narro_file_progress` (
  `file_progress_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
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
  KEY `file_id_3` (`file_id`,`language_id`,`export`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `narro_file_progress`
--


-- --------------------------------------------------------

--
-- Table structure for table `narro_file_type`
--

CREATE TABLE IF NOT EXISTS `narro_file_type` (
  `file_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `file_type` varchar(32) NOT NULL,
  PRIMARY KEY (`file_type_id`),
  UNIQUE KEY `UQ_qdrupal_narro_file_type_1` (`file_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `narro_file_type`
--

INSERT INTO `narro_file_type` (`file_type_id`, `file_type`) VALUES
(9, 'DumbGettextPo'),
(3, 'Folder'),
(1, 'GettextPo'),
(12, 'Html'),
(4, 'MozillaDtd'),
(7, 'MozillaInc'),
(5, 'MozillaIni'),
(6, 'Narro'),
(2, 'OpenOfficeSdf'),
(10, 'PhpMyAdmin'),
(8, 'Svg'),
(11, 'Unsupported');

-- --------------------------------------------------------

--
-- Table structure for table `narro_language`
--

CREATE TABLE IF NOT EXISTS `narro_language` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=123 ;

--
-- Dumping data for table `narro_language`
--

INSERT INTO `narro_language` (`language_id`, `language_name`, `language_code`, `country_code`, `dialect_code`, `encoding`, `text_direction`, `special_characters`, `plural_form`, `active`) VALUES
(1, 'Romanian', 'ro', 'ro', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms:  nplurals=3; plural=n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2;\\n"', 1),
(2, 'French', 'fr', 'fr', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(3, 'Spanish, Spain', 'es-ES', 'es-ES', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(4, 'Afrikaans', 'af', 'af', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(5, 'Arabic', 'ar', 'ar', NULL, 'UTF-8', 'rtl', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(6, 'Belarusian', 'be', 'be', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(7, 'Bulgarian', 'bg', 'bg', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(8, 'Catalan', 'ca', 'ca', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(9, 'Czech', 'cs', 'cs', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(10, 'Danish', 'da', 'da', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(11, 'German', 'de', 'de', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(12, 'Greek', 'el', 'el', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(13, 'English, UK', 'en-GB', 'en-GB', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(14, 'English (South African)', 'en-ZA', 'en-ZA', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(15, 'Spanish, Argentina', 'es-AR', 'es-AR', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(16, 'Frisian', 'fy-NL', 'fy-NL', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(17, 'Basque', 'eu', 'eu', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(18, 'Finnish', 'fi', 'fi', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(19, 'Irish', 'ga-IE', 'ga-IE', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(20, 'Gujarati', 'gu-IN', 'gu-IN', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(21, 'Hebrew', 'he', 'he', NULL, 'UTF-8', 'rtl', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(22, 'Hungarian', 'hu', 'hu', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(23, 'Armenian', 'hy-AM', 'hy-AM', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(24, 'Italian', 'it', 'it', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(25, 'Japanese', 'ja', 'ja', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(26, 'Georgian', 'ka', 'ka', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(27, 'Korean', 'ko', 'ko', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(28, 'Kurdish', 'ku', 'ku', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(29, 'Lithuanian', 'lt', 'lt', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(30, 'Macedonian', 'mk', 'mk', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(31, 'Mongolian', 'mn', 'mn', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(32, 'Norwegian bokmaal', 'nb-NO', 'nb-NO', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(33, 'Dutch', 'nl', 'nl', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(34, 'Ndebele', 'nr', 'nr', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(35, 'Northern Sotho', 'nso', 'nso', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(36, 'Norwegian nynorsk', 'nn-NO', 'nn-NO', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(37, 'Punjabi', 'pa-IN', 'pa-IN', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(38, 'Polish', 'pl', 'pl', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(39, 'Portuguese, Brazil', 'pt-BR', 'pt-BR', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(40, 'Portuguese, Portugal', 'pt-PT', 'pt-PT', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(41, 'Russian', 'ru', 'ru', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(42, 'Slovak', 'sk', 'sk', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(43, 'Slovenian', 'sl', 'sl', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(44, 'Albanian', 'sq', 'sq', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(45, 'Serbian', 'sr', 'sr', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 or n%100>=20) ? 1 : 2);\\n"', 1),
(46, 'Swati', 'ss', 'ss', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(47, 'Southern Sotho', 'st', 'st', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(48, 'Swedish', 'sv-SE', 'sv-SE', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(49, 'Tswana', 'tn', 'tn', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(50, 'Turkish', 'tr', 'tr', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(51, 'Tsonga', 'ts', 'ts', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(52, 'Ukrainian', 'uk', 'uk', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(53, 'Venda', 've', 've', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(54, 'Xhosa', 'xh', 'xh', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(55, 'Chinese Simplified, China', 'zh-CN', 'zh-CN', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(56, 'Chinese Traditional, Taiwan', 'zh-TW', 'zh-TW', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(57, 'Zulu', 'zu', 'zu', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(58, 'English US', 'en-US', 'en-US', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(59, 'Urdu (Pakistan)', 'ur-PK', 'ur-PK', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(60, 'Vietnamese', 'vi', 'vi', NULL, 'UTF-8', 'ltr', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(61, 'Persian', 'fa', 'ir', NULL, 'UTF-8', 'rtl', NULL, '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(62, 'Spanish, Chile', 'es-CL', 'es-CL', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(63, 'Uyghur', 'ug', 'ug', NULL, 'UTF-8', 'rtl', '', '', 1),
(64, 'Dzongkha', 'dz', 'dz', NULL, 'UTF-8', 'ltr', '', '', 1),
(65, 'Amharic', 'am', 'am', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals = 2; plural=(n > 1);\\n"', 0),
(66, 'Spanish, Mexico', 'es-MX', 'es-MX', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(67, 'Bosnian', 'bs', 'bs', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10< =4 && (n%100<10 or n%100>=20) ? 1 : 2)\\n"', 1),
(68, 'Malayalam', 'ml', 'ml', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(69, 'Zapotec (OT19745)', 'zap-MX-OT19745', 'zap-MX-OT19745', NULL, 'UTF-8', 'ltr', '', '', 1),
(70, 'Latvian', 'lv', 'lv', NULL, 'UTF-8', 'ltr', 'ā č ē ģ ī ķ ļ ņ š ū ž Ā Č Ē Ģ Ī Ķ Ļ Ņ Š Ū Ž', '"Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2);\\n"', 1),
(71, 'Kazakh', 'kk', 'KZ', NULL, 'UTF-8', 'ltr', '', '', 0),
(72, 'Kabyle', 'kab', 'kab', NULL, 'UTF-8', 'ltr', '', '', 0),
(73, 'Romance, Spain (Valencian)', 'roa-ES-val', 'roa-ES-val', NULL, 'UTF-8', 'ltr', '', '', 1),
(74, 'Mayan', 'myn-MX', 'myn-MX', NULL, 'UTF-8', 'ltr', '', '', 1),
(75, 'Oromo', 'om', 'om', NULL, 'UTF-8', 'ltr', '', '', 1),
(76, 'Iloko', 'ilo-PH', 'ilo-PH', NULL, 'UTF-8', 'ltr', '', '', 1),
(77, 'Tagalog', 'tl-PH', 'tl-PH', NULL, 'UTF-8', 'ltr', '', '', 1),
(78, 'Fijian', 'fj-FJ', 'fj-FJ', NULL, 'UTF-8', 'ltr', '', '', 1),
(79, 'Latin', 'la', 'la', NULL, 'UTF-8', 'ltr', '', '', 1),
(80, 'Romansch', 'rm', 'rm', NULL, 'UTF-8', 'ltr', '', '', 1),
(81, 'Telugu', 'te', 'te', NULL, 'UTF-8', 'ltr', '', '', 0),
(82, 'Croatian', 'hr', 'hr', NULL, 'UTF-8', 'ltr', '', '', 1),
(83, 'Indonesian', 'id', 'id', NULL, 'UTF-8', 'ltr', '', '', 0),
(84, 'Thai', 'th', 'TH', NULL, 'UTF-8', 'ltr', '', '', 0),
(85, 'Tarahumara (Y08703)', 'tar-MX-Y08703', 'tar-MX-Y08703', NULL, 'UTF-8', 'ltr', '', '', 1),
(86, 'Assamese', 'as', 'as', NULL, 'UTF-8', 'ltr', '', '', 1),
(87, 'Friulian', 'fur', 'fur', NULL, 'UTF-8', 'ltr', 'â ê î ô û ç', '', 1),
(88, 'Tajik', 'tg-TJ', 'tg-TJ', NULL, 'UTF-8', 'ltr', '', '', 1),
(89, 'Spanish, Peru', 'es-PE', 'es-PE', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(90, 'Spanish, Bolivia', 'es-BO', 'es-BO', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(91, 'Dari (Eastern Persian)', 'ps-AF', 'ps-AF', NULL, 'UTF-8', 'rtl', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 0),
(92, 'Asturian', 'ast', 'ast', NULL, 'UTF-8', 'ltr', '', '', 1),
(93, 'X-testing', 'xx-XX', 'xx-XX', NULL, 'UTF-8', 'ltr', 'ă î ș ț â „ ”', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(94, 'Quechua, Bolivia', 'qu-BO', 'qu-BO', NULL, 'UTF-8', 'ltr', '', '', 1),
(95, 'Norwegian hognorsk', 'nn-hognorsk', 'nn-hognorsk', NULL, 'UTF-8', 'ltr', '€', '', 1),
(96, 'Guaraní', 'grn', 'grn', NULL, 'UTF-8', 'ltr', '', '', 1),
(97, 'Azerbaijani', 'az', 'az', NULL, 'UTF-8', 'ltr', '', '', 1),
(98, 'Welsh', 'cy', 'cy', NULL, 'UTF-8', 'ltr', '', '', 1),
(99, 'Montenegrin', 'sla', 'sla', NULL, 'UTF-8', 'ltr', '', '', 1),
(100, 'Spanish, Colombia', 'es-CO', 'es-CO', NULL, 'UTF-8', 'ltr', 'á ¿', '', 1),
(101, 'Spanish, Venezuela', 'es-VE', 'es-VE', NULL, 'UTF-8', 'ltr', 'á ¿', '', 1),
(102, 'Aymara, Bolivia', 'ay', 'ay', NULL, 'UTF-8', 'ltr', 'ɲ', '', 1),
(103, 'Aragonese', 'an', 'an', NULL, 'UTF-8', 'ltr', '', '', 1),
(104, 'Galician', 'gl', 'gl', NULL, 'UTF-8', 'ltr', 'á ¿', '', 1),
(105, 'Scottish Gaelic', 'gd', 'gd', NULL, 'UTF-8', 'ltr', 'á ¿', '"Plural-Forms: nplurals=4; plural=(n%10==1 && n < 40) ? 0 : (n%10==2 && n < 40) ? 1 : (n==10 || (n%10 > 2 && n < 40)) ? 2 : 3\\n"', 1),
(106, 'Nepali', 'ne-NP', 'ne-NP', NULL, 'UTF-8', 'ltr', 'भ प् र भा त', '', 1),
(107, 'Bengali-Bangladesh', 'bn-BD', 'bn-BD', NULL, 'UTF-8', 'ltr', 'á ¿', '', 1),
(108, 'Hindi', 'hi-IN', 'hi-IN', NULL, 'UTF-8', 'ltr', '', '', 1),
(109, 'Breton', 'br', 'br', NULL, 'UTF-8', 'ltr', '', '', 1),
(110, 'Khmer', 'km', 'km', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=1; plural=0;\\n"', 1),
(111, 'Haitian Creole', 'ht', 'ht', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(112, 'Ligurian', 'lij', 'lij', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(113, 'Kurdî, Soranî (Latin)', 'ckb-latin', 'ckb-latin', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(114, 'Kurdî, Soranî (Aramic)', 'ckb-arabic', 'ckb-arabic', NULL, 'UTF-8', 'rtl', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(115, 'Meadow Mari', 'mhr', 'mhr', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(116, 'Kashubian', 'csb', 'csb', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=3; n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;\\n"', 1),
(119, 'Guaraní, Bolivia', 'grn-BO', 'grn-BO', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(120, 'Burmese (my-MM)', 'my-MM', 'my-MM', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1),
(121, 'Urdu', 'ur', 'ur', NULL, 'UTF-8', 'rtl', '', '"Plural-Forms: nplurals=3; n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;\\n"', 1),
(122, 'Yoruba', 'yo', 'yo', NULL, 'UTF-8', 'ltr', '', '"Plural-Forms: nplurals=2; plural=n != 1;\\n"', 1);

-- --------------------------------------------------------

--
-- Table structure for table `narro_permission`
--

CREATE TABLE IF NOT EXISTS `narro_permission` (
  `permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(128) NOT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `permission_name` (`permission_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `narro_permission`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `narro_project`
--

CREATE TABLE IF NOT EXISTS `narro_project` (
  `project_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_category_id` int(11) unsigned DEFAULT '1',
  `project_name` varchar(255) NOT NULL,
  `project_type` smallint(5) unsigned NOT NULL,
  `project_description` varchar(255) DEFAULT NULL,
  `source` text,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`project_id`),
  UNIQUE KEY `project_name` (`project_name`),
  KEY `project_type` (`project_type`),
  KEY `narro_project_ibfk_2` (`project_category_id`),
  KEY `active` (`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `narro_project`
--

INSERT INTO `narro_project` (`project_id`, `project_category_id`, `project_name`, `project_type`, `project_description`, `source`, `active`) VALUES
(1, 1, 'Narro', 3, 'Narro itself. After you translate, even if you''re not complete, if you export and your locale is supported on the server, you''ll see the translation.', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `narro_project_category`
--

CREATE TABLE IF NOT EXISTS `narro_project_category` (
  `project_category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `category_description` varchar(255) NOT NULL,
  PRIMARY KEY (`project_category_id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `narro_project_category`
--

INSERT INTO `narro_project_category` (`project_category_id`, `category_name`, `category_description`) VALUES
(1, 'General', '');

-- --------------------------------------------------------

--
-- Table structure for table `narro_project_progress`
--

CREATE TABLE IF NOT EXISTS `narro_project_progress` (
  `project_progress_id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `active` tinyint(1) DEFAULT '0',
  `last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `total_text_count` int(10) unsigned NOT NULL,
  `fuzzy_text_count` int(10) unsigned NOT NULL,
  `approved_text_count` int(10) unsigned NOT NULL,
  `progress_percent` int(10) unsigned NOT NULL,
  `source` text COMMENT 'list of sources of translations',
  PRIMARY KEY (`project_progress_id`),
  UNIQUE KEY `project_id` (`project_id`,`language_id`),
  KEY `language_id` (`language_id`),
  KEY `project_id_2` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `narro_project_progress`
--


-- --------------------------------------------------------

--
-- Table structure for table `narro_project_type`
--

CREATE TABLE IF NOT EXISTS `narro_project_type` (
  `project_type_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `project_type` varchar(64) NOT NULL,
  PRIMARY KEY (`project_type_id`),
  UNIQUE KEY `project_type` (`project_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `narro_project_type`
--

INSERT INTO `narro_project_type` (`project_type_id`, `project_type`) VALUES
(6, 'DumbGettextPo'),
(7, 'Generic'),
(3, 'Gettext'),
(8, 'Html'),
(1, 'Mozilla'),
(4, 'Narro'),
(2, 'OpenOffice'),
(5, 'Svg');

-- --------------------------------------------------------

--
-- Table structure for table `narro_role`
--

CREATE TABLE IF NOT EXISTS `narro_role` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(128) NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `narro_role`
--

INSERT INTO `narro_role` (`role_id`, `role_name`) VALUES
(5, 'Administrator'),
(1, 'Anonymous'),
(3, 'Approver'),
(4, 'Project manager'),
(2, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `narro_role_permission`
--

CREATE TABLE IF NOT EXISTS `narro_role_permission` (
  `role_permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`role_permission_id`),
  KEY `role_id` (`role_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=56 ;

--
-- Dumping data for table `narro_role_permission`
--

INSERT INTO `narro_role_permission` (`role_permission_id`, `role_id`, `permission_id`) VALUES
(1, 2, 4),
(2, 2, 2),
(3, 2, 1),
(4, 3, 4),
(5, 3, 2),
(6, 3, 1),
(7, 3, 6),
(8, 3, 5),
(9, 4, 4),
(10, 4, 5),
(11, 4, 6),
(12, 4, 9),
(13, 4, 19),
(14, 4, 8),
(15, 4, 18),
(16, 4, 10),
(18, 4, 1),
(19, 4, 20),
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
(42, 5, 2),
(43, 3, 3),
(46, 3, 22),
(51, 3, 8),
(52, 3, 9),
(53, 4, 22),
(54, 4, 3),
(55, 4, 23);

-- --------------------------------------------------------

--
-- Table structure for table `narro_suggestion`
--

CREATE TABLE IF NOT EXISTS `narro_suggestion` (
  `suggestion_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `text_id` bigint(20) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `suggestion_value` text NOT NULL,
  `suggestion_value_md5` varchar(128) NOT NULL,
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
  KEY `text_id_3` (`text_id`,`language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `narro_suggestion`
--


-- --------------------------------------------------------

--
-- Table structure for table `narro_suggestion_comment`
--

CREATE TABLE IF NOT EXISTS `narro_suggestion_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `suggestion_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `comment_text` text NOT NULL,
  `comment_text_md5` varchar(128) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`comment_id`),
  UNIQUE KEY `suggestion_id_2` (`suggestion_id`,`user_id`,`comment_text_md5`),
  KEY `suggestion_id` (`suggestion_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `narro_suggestion_comment`
--


-- --------------------------------------------------------

--
-- Table structure for table `narro_suggestion_vote`
--

CREATE TABLE IF NOT EXISTS `narro_suggestion_vote` (
  `suggestion_id` bigint(20) unsigned NOT NULL,
  `context_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `vote_value` tinyint(3) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `suggestion_id` (`suggestion_id`,`user_id`,`context_id`),
  KEY `suggestion_id_2` (`suggestion_id`),
  KEY `user_id` (`user_id`),
  KEY `context_id` (`context_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `narro_suggestion_vote`
--


-- --------------------------------------------------------

--
-- Table structure for table `narro_text`
--

CREATE TABLE IF NOT EXISTS `narro_text` (
  `text_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `text_value` text NOT NULL,
  `text_value_md5` varchar(64) NOT NULL,
  `text_char_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `text_word_count` smallint(5) unsigned DEFAULT '0',
  `has_comments` tinyint(1) DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`text_id`),
  UNIQUE KEY `text_value_md5` (`text_value_md5`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `narro_text`
--


-- --------------------------------------------------------

--
-- Table structure for table `narro_text_comment`
--

CREATE TABLE IF NOT EXISTS `narro_text_comment` (
  `text_comment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `text_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `comment_text` text NOT NULL,
  `comment_text_md5` varchar(128) NOT NULL,
  PRIMARY KEY (`text_comment_id`),
  KEY `text_id` (`text_id`),
  KEY `user_id` (`user_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `narro_text_comment`
--


-- --------------------------------------------------------

--
-- Table structure for table `narro_user`
--

CREATE TABLE IF NOT EXISTS `narro_user` (
  `user_id` int(10) unsigned NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `data` text,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `narro_user`
--

INSERT INTO `narro_user` (`user_id`, `username`, `password`, `email`, `data`) VALUES
(0, 'Anonymous', '0d107d09f5bbe40cade3de5c71e9e9b7', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `narro_user_role`
--

CREATE TABLE IF NOT EXISTS `narro_user_role` (
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
  KEY `user_id_2` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=56 ;

--
-- Dumping data for table `narro_user_role`
--


-- --------------------------------------------------------

--
-- Table structure for table `zend_cache`
--

CREATE TABLE IF NOT EXISTS `zend_cache` (
  `id` varchar(255) NOT NULL,
  `content` text,
  `lastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `zend_cache_id_expire_index` (`id`,`expire`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `zend_cache`
--


-- --------------------------------------------------------

--
-- Table structure for table `zend_cache_tag`
--

CREATE TABLE IF NOT EXISTS `zend_cache_tag` (
  `name` text,
  `id` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `zend_cache_tag`
--


-- --------------------------------------------------------

--
-- Table structure for table `zend_cache_version`
--

CREATE TABLE IF NOT EXISTS `zend_cache_version` (
  `num` int(11) NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `zend_cache_version`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `narro_context`
--
ALTER TABLE `narro_context`
  ADD CONSTRAINT `narro_context_ibfk_17` FOREIGN KEY (`project_id`) REFERENCES `narro_project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_context_ibfk_18` FOREIGN KEY (`file_id`) REFERENCES `narro_file` (`file_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_context_ibfk_19` FOREIGN KEY (`text_id`) REFERENCES `narro_text` (`text_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `narro_context_info`
--
ALTER TABLE `narro_context_info`
  ADD CONSTRAINT `narro_context_info_ibfk_18` FOREIGN KEY (`popular_suggestion_id`) REFERENCES `narro_suggestion` (`suggestion_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_context_info_ibfk_14` FOREIGN KEY (`context_id`) REFERENCES `narro_context` (`context_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_context_info_ibfk_15` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_context_info_ibfk_16` FOREIGN KEY (`validator_user_id`) REFERENCES `narro_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_context_info_ibfk_17` FOREIGN KEY (`valid_suggestion_id`) REFERENCES `narro_suggestion` (`suggestion_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `narro_file`
--
ALTER TABLE `narro_file`
  ADD CONSTRAINT `narro_file_ibfk_11` FOREIGN KEY (`type_id`) REFERENCES `narro_file_type` (`file_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_file_ibfk_12` FOREIGN KEY (`project_id`) REFERENCES `narro_project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_file_ibfk_4` FOREIGN KEY (`parent_id`) REFERENCES `narro_file` (`file_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `narro_file_progress`
--
ALTER TABLE `narro_file_progress`
  ADD CONSTRAINT `narro_file_progress_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `narro_file` (`file_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_file_progress_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `narro_project`
--
ALTER TABLE `narro_project`
  ADD CONSTRAINT `narro_project_ibfk_3` FOREIGN KEY (`project_type`) REFERENCES `narro_project_type` (`project_type_id`),
  ADD CONSTRAINT `narro_project_ibfk_2` FOREIGN KEY (`project_category_id`) REFERENCES `narro_project_category` (`project_category_id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `narro_project_progress`
--
ALTER TABLE `narro_project_progress`
  ADD CONSTRAINT `narro_project_progress_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `narro_project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_project_progress_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `narro_role_permission`
--
ALTER TABLE `narro_role_permission`
  ADD CONSTRAINT `narro_role_permission_ibfk_4` FOREIGN KEY (`permission_id`) REFERENCES `narro_permission` (`permission_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_role_permission_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `narro_role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `narro_suggestion`
--
ALTER TABLE `narro_suggestion`
  ADD CONSTRAINT `narro_suggestion_ibfk_12` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_suggestion_ibfk_10` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `narro_suggestion_ibfk_11` FOREIGN KEY (`text_id`) REFERENCES `narro_text` (`text_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `narro_suggestion_comment`
--
ALTER TABLE `narro_suggestion_comment`
  ADD CONSTRAINT `narro_suggestion_comment_ibfk_7` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_suggestion_comment_ibfk_6` FOREIGN KEY (`suggestion_id`) REFERENCES `narro_suggestion` (`suggestion_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `narro_suggestion_vote`
--
ALTER TABLE `narro_suggestion_vote`
  ADD CONSTRAINT `narro_suggestion_vote_ibfk_10` FOREIGN KEY (`suggestion_id`) REFERENCES `narro_suggestion` (`suggestion_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_suggestion_vote_ibfk_7` FOREIGN KEY (`context_id`) REFERENCES `narro_context` (`context_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_suggestion_vote_ibfk_9` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `narro_text_comment`
--
ALTER TABLE `narro_text_comment`
  ADD CONSTRAINT `narro_text_comment_ibfk_10` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`),
  ADD CONSTRAINT `narro_text_comment_ibfk_11` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`),
  ADD CONSTRAINT `narro_text_comment_ibfk_9` FOREIGN KEY (`text_id`) REFERENCES `narro_text` (`text_id`);

--
-- Constraints for table `narro_user_role`
--
ALTER TABLE `narro_user_role`
  ADD CONSTRAINT `narro_user_role_ibfk_10` FOREIGN KEY (`project_id`) REFERENCES `narro_project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_user_role_ibfk_11` FOREIGN KEY (`language_id`) REFERENCES `narro_language` (`language_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_user_role_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `narro_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `narro_user_role_ibfk_9` FOREIGN KEY (`role_id`) REFERENCES `narro_role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;
