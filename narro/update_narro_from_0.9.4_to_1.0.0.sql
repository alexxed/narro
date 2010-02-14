ALTER TABLE `narro_context`
    CHANGE `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE `modified` `modified` DATETIME NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `narro_context_info`
    CHANGE `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE `modified` `modified` DATETIME NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `narro_context_comment`
    CHANGE `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE `modified` `modified` DATETIME NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `narro_file`
    CHANGE `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE `modified` `modified` DATETIME NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `narro_suggestion`
    CHANGE `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE `modified` `modified` DATETIME NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `narro_suggestion_comment`
    CHANGE `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE `modified` `modified` DATETIME NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `narro_suggestion_vote`
    CHANGE `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE `modified` `modified` DATETIME NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `narro_text`
    CHANGE `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE `modified` `modified` DATETIME NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `narro_text_comment`
    CHANGE `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE `modified` `modified` DATETIME NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `narro_suggestion` ADD `suggestion_word_count` SMALLINT UNSIGNED  DEFAULT '0' AFTER `suggestion_char_count` ;
ALTER TABLE `narro_suggestion` CHANGE `suggestion_char_count` `suggestion_char_count` SMALLINT( 5 ) UNSIGNED DEFAULT '0';
ALTER TABLE `narro_text` ADD `text_word_count` SMALLINT UNSIGNED DEFAULT '0' AFTER `text_char_count` ;

ALTER TABLE `narro_suggestion_comment` DROP FOREIGN KEY `narro_suggestion_comment_ibfk_1` ;
ALTER TABLE `narro_suggestion_comment` ADD FOREIGN KEY ( `suggestion_id` ) REFERENCES `narro_suggestion` (`suggestion_id`);
ALTER TABLE `narro_suggestion_comment` DROP FOREIGN KEY `narro_suggestion_comment_ibfk_2` ;
ALTER TABLE `narro_suggestion_comment` ADD FOREIGN KEY ( `user_id` ) REFERENCES `narro_user` (`user_id`);
ALTER TABLE `narro_suggestion_comment` DROP FOREIGN KEY `narro_suggestion_comment_ibfk_3` ;
ALTER TABLE `narro_suggestion_comment` DROP INDEX `language_id`;
ALTER TABLE `narro_suggestion_comment` DROP `language_id`;

INSERT INTO `narro_permission` (`permission_id` ,`permission_name`) VALUES (NULL , 'Can manage user roles');

ALTER TABLE `narro_context_info` ADD INDEX ( `created` );
ALTER TABLE `narro_context_info` ADD INDEX ( `modified` );

ALTER TABLE  `narro_suggestion` ADD  `is_imported` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `has_comments`;
