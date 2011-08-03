UPDATE `narro_file_progress` SET export=1
DROP TABLE `narro_context_comment`;
ALTER TABLE `narro_context` ADD `comment` TEXT NULL AFTER `context_md5` ;
ALTER TABLE `narro_context` ADD `comment_md5` VARCHAR( 255 ) NULL AFTER `comment` ;
ALTER TABLE `narro_context` DROP INDEX `text_id` ,
ADD UNIQUE `text_id` ( `text_id` , `context_md5` , `file_id` , `comment_md5` )