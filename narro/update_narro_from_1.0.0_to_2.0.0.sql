UPDATE `narro_file_progress` SET export=1
DROP TABLE `narro_context_comment`;
ALTER TABLE `narro_context` ADD `comment` TEXT NULL AFTER `context_md5` ;