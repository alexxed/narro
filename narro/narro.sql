/**
 * Narro is an application that allows online software translation and maintenance.
 * Copyright (C) 2008 Alexandru Szasz <alexxed@gmail.com>
 * http://code.google.com/p/narro/
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the
 * Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */
DROP INDEX language_name ON narro_language;
DROP INDEX string_value_md5 ON narro_text;
DROP INDEX username ON narro_user;
DROP INDEX UQ_qdrupal_narro_file_type_1 ON narro_file_type;
DROP INDEX file_name ON narro_file;
DROP INDEX type_id ON narro_file;
DROP INDEX project_id ON narro_file;
DROP INDEX parent_id ON narro_file;
DROP INDEX unique_text_suggestion ON narro_text_suggestion;
DROP INDEX user_id ON narro_text_suggestion;
DROP INDEX text_id ON narro_text_suggestion;
DROP INDEX language_id ON narro_text_suggestion;
DROP INDEX string_id ON narro_text_context;
DROP INDEX file_id ON narro_text_context;
DROP INDEX plural_id ON narro_text_context;
DROP INDEX valid_suggestion_id ON narro_text_context;
DROP INDEX has_suggestion ON narro_text_context;
DROP INDEX project_id ON narro_text_context;
DROP INDEX popular_suggestion_id ON narro_text_context;
DROP INDEX suggestion_id ON narro_suggestion_comment;
DROP INDEX user_id ON narro_suggestion_comment;
DROP INDEX suggestion_id ON narro_suggestion_vote;
DROP INDEX text_id ON narro_suggestion_vote;
DROP INDEX suggestion_id_2 ON narro_suggestion_vote;
DROP INDEX user_id ON narro_suggestion_vote;
DROP INDEX context_id ON narro_text_context_comment;
DROP INDEX user_id ON narro_text_context_comment;
DROP INDEX context_id ON narro_text_context_plural;
DROP INDEX text_id ON narro_text_context_plural;
DROP INDEX valid_suggestion_id ON narro_text_context_plural;
DROP INDEX popular_suggestion_id ON narro_text_context_plural;
DROP INDEX user_id ON narro_user_permission;
DROP INDEX permission_id ON narro_user_permission;
DROP INDEX project_id ON narro_user_permission;

DROP TABLE IF EXISTS narro_file_header;
DROP TABLE IF EXISTS narro_user_permission;
DROP TABLE IF EXISTS narro_text_context_plural;
DROP TABLE IF EXISTS narro_text_context_comment;
DROP TABLE IF EXISTS narro_suggestion_vote;
DROP TABLE IF EXISTS narro_suggestion_comment;
DROP TABLE IF EXISTS narro_text_context;
DROP TABLE IF EXISTS narro_text_suggestion;
DROP TABLE IF EXISTS narro_file;
DROP TABLE IF EXISTS narro_file_type;
DROP TABLE IF EXISTS narro_user;
DROP TABLE IF EXISTS narro_text;
DROP TABLE IF EXISTS narro_project;
DROP TABLE IF EXISTS narro_permission;
DROP TABLE IF EXISTS narro_language;

CREATE TABLE narro_language (
       language_id INT(10) NOT NULL AUTO_INCREMENT
     , language_name VARCHAR(128) NOT NULL
);
CREATE UNIQUE INDEX language_name ON narro_language (language_name ASC);

CREATE TABLE narro_permission (
       permission_id INT(10) NOT NULL AUTO_INCREMENT
     , permission_name VARCHAR(128) NOT NULL
);

CREATE TABLE narro_project (
       project_id INT(10) NOT NULL AUTO_INCREMENT
     , project_name VARCHAR(255) NOT NULL
     , active TINYINT(3) NOT NULL DEFAULT 1
);

CREATE TABLE narro_text (
       text_id BIGINT(20) NOT NULL AUTO_INCREMENT
     , text_value TEXT NOT NULL
     , text_value_md5 VARCHAR(64) NOT NULL
     , text_char_count SMALLINT(5) NOT NULL DEFAULT 0
);
CREATE UNIQUE INDEX string_value_md5 ON narro_text (text_value_md5 ASC);

CREATE TABLE narro_user (
       user_id INT(10) NOT NULL
     , username VARCHAR(128) NOT NULL
     , password VARCHAR(64) NOT NULL
     , email VARCHAR(128) NOT NULL
     , data TEXT
);
CREATE UNIQUE INDEX username ON narro_user (username ASC);

CREATE TABLE narro_file_type (
       file_type_id TINYINT(3) NOT NULL AUTO_INCREMENT
     , file_type VARCHAR(32) NOT NULL
);
CREATE UNIQUE INDEX UQ_qdrupal_narro_file_type_1 ON narro_file_type (file_type ASC);

CREATE TABLE narro_file (
       file_id INT(10) NOT NULL AUTO_INCREMENT
     , file_name VARCHAR(255) NOT NULL
     , parent_id INT(10)
     , type_id TINYINT(3) NOT NULL
     , project_id INT(10) NOT NULL
     , encoding VARCHAR(16) NOT NULL DEFAULT 'UTF-8'
     , context_count INT(10) NOT NULL DEFAULT 0
     , active TINYINT(3) NOT NULL DEFAULT 1
);
CREATE UNIQUE INDEX file_name ON narro_file (file_name ASC, parent_id ASC);

CREATE TABLE narro_text_suggestion (
       suggestion_id INT(10) NOT NULL AUTO_INCREMENT
     , user_id INT(10)
     , text_id BIGINT(20) NOT NULL
     , language_id INT(10) NOT NULL
     , suggestion_value TEXT NOT NULL
     , suggestion_value_md5 VARCHAR(128) NOT NULL
);
CREATE UNIQUE INDEX unique_text_suggestion ON narro_text_suggestion (text_id ASC, suggestion_value_md5 ASC, language_id ASC);

CREATE TABLE narro_text_context (
       context_id BIGINT(20) NOT NULL AUTO_INCREMENT
     , text_id BIGINT(20) NOT NULL
     , project_id INT(10) NOT NULL
     , context TEXT NOT NULL
     , file_id INT(10) NOT NULL
     , valid_suggestion_id INT(10)
     , popular_suggestion_id INT(10)
     , has_suggestion TINYINT(3) NOT NULL DEFAULT 0
     , has_plural BIT(1) NOT NULL DEFAULT 0
     , active TINYINT(3) NOT NULL DEFAULT 1
     , translatable TINYINT(3) NOT NULL DEFAULT 1
);
CREATE INDEX plural_id ON narro_text_context (has_plural ASC);
CREATE INDEX has_suggestion ON narro_text_context (has_suggestion ASC);

CREATE TABLE narro_suggestion_comment (
       comment_id INT(10) NOT NULL AUTO_INCREMENT
     , suggestion_id INT(10) NOT NULL
     , user_id INT(10) NOT NULL
     , comment_text TEXT NOT NULL
);

CREATE TABLE narro_suggestion_vote (
       suggestion_id INT(10) NOT NULL
     , text_id BIGINT(20) NOT NULL
     , user_id INT(10) NOT NULL
     , vote_value TINYINT(3) NOT NULL
);
CREATE UNIQUE INDEX suggestion_id ON narro_suggestion_vote (suggestion_id ASC, text_id ASC, user_id ASC);

CREATE TABLE narro_text_context_comment (
       comment_id BIGINT(20) NOT NULL
     , context_id BIGINT(20) NOT NULL
     , user_id INT(11) NOT NULL
     , comment_text TEXT NOT NULL
);

CREATE TABLE narro_text_context_plural (
       plural_id BIGINT(20) NOT NULL AUTO_INCREMENT
     , context_id BIGINT(20) NOT NULL
     , text_id BIGINT(20) NOT NULL
     , valid_suggestion_id INT(10)
     , popular_suggestion_id INT(10)
     , plural_form BIT(1) NOT NULL
     , has_suggestion BIT(1) NOT NULL DEFAULT 0
);

CREATE TABLE narro_user_permission (
       user_permission_id INT(10) NOT NULL AUTO_INCREMENT
     , user_id INT(10) NOT NULL
     , permission_id INT(10) NOT NULL
     , project_id INT(10)
);

CREATE TABLE narro_file_header (
       file_id INT(10) NOT NULL
     , file_header BLOB NOT NULL
);

ALTER TABLE narro_language
  ADD CONSTRAINT PK_NARRO_LANGUAGE_PRIMARY
      PRIMARY KEY (language_id);

ALTER TABLE narro_permission
  ADD CONSTRAINT PK_NARRO_PERMISSION_PRIMARY
      PRIMARY KEY (permission_id);

ALTER TABLE narro_project
  ADD CONSTRAINT PK_NARRO_PROJECT_PRIMARY
      PRIMARY KEY (project_id);

ALTER TABLE narro_text
  ADD CONSTRAINT PK_NARRO_TEXT_PRIMARY
      PRIMARY KEY (text_id);

ALTER TABLE narro_user
  ADD CONSTRAINT PK_NARRO_USER_PRIMARY
      PRIMARY KEY (user_id);

ALTER TABLE narro_file_type
  ADD CONSTRAINT PK_NARRO_FILE_TYPE_PRIMARY
      PRIMARY KEY (file_type_id);

ALTER TABLE narro_file
  ADD CONSTRAINT PK_NARRO_FILE_PRIMARY
      PRIMARY KEY (file_id);

ALTER TABLE narro_text_suggestion
  ADD CONSTRAINT PK_NARRO_TEXT_SUGGESTION_PRIMARY
      PRIMARY KEY (suggestion_id);

ALTER TABLE narro_text_context
  ADD CONSTRAINT PK_NARRO_TEXT_CONTEXT_PRIMARY
      PRIMARY KEY (context_id);

ALTER TABLE narro_suggestion_comment
  ADD CONSTRAINT PK_NARRO_SUGGESTION_COMMENT_PRIMARY
      PRIMARY KEY (comment_id);

ALTER TABLE narro_text_context_comment
  ADD CONSTRAINT PK_NARRO_TEXT_CONTEXT_COMMENT_PRIMARY
      PRIMARY KEY (comment_id);

ALTER TABLE narro_text_context_plural
  ADD CONSTRAINT PK_NARRO_TEXT_CONTEXT_PLURAL_PRIMARY
      PRIMARY KEY (plural_id);

ALTER TABLE narro_user_permission
  ADD CONSTRAINT PK_NARRO_USER_PERMISSION_PRIMARY
      PRIMARY KEY (user_permission_id);

ALTER TABLE narro_file_header
  ADD CONSTRAINT PK_NARRO_FILE_HEADER_PRIMARY
      PRIMARY KEY (file_id);

ALTER TABLE narro_file
  ADD CONSTRAINT narro_file_ibfk_1
      FOREIGN KEY (parent_id)
      REFERENCES narro_file (file_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_file
  ADD CONSTRAINT narro_file_ibfk_2
      FOREIGN KEY (type_id)
      REFERENCES narro_file_type (file_type_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_file
  ADD CONSTRAINT narro_file_ibfk_3
      FOREIGN KEY (project_id)
      REFERENCES narro_project (project_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_text_suggestion
  ADD CONSTRAINT narro_text_suggestion_ibfk_6
      FOREIGN KEY (user_id)
      REFERENCES narro_user (user_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_text_suggestion
  ADD CONSTRAINT narro_text_suggestion_ibfk_7
      FOREIGN KEY (text_id)
      REFERENCES narro_text (text_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_text_suggestion
  ADD CONSTRAINT narro_text_suggestion_ibfk_8
      FOREIGN KEY (language_id)
      REFERENCES narro_language (language_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_text_context
  ADD CONSTRAINT narro_text_context_ibfk_28
      FOREIGN KEY (valid_suggestion_id)
      REFERENCES narro_text_suggestion (suggestion_id)
   ON DELETE SET NULL
   ON UPDATE SET NULL;

ALTER TABLE narro_text_context
  ADD CONSTRAINT narro_text_context_ibfk_29
      FOREIGN KEY (popular_suggestion_id)
      REFERENCES narro_text_suggestion (suggestion_id)
   ON DELETE SET NULL
   ON UPDATE SET NULL;

ALTER TABLE narro_text_context
  ADD CONSTRAINT narro_text_context_ibfk_34
      FOREIGN KEY (text_id)
      REFERENCES narro_text (text_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_text_context
  ADD CONSTRAINT narro_text_context_ibfk_35
      FOREIGN KEY (project_id)
      REFERENCES narro_project (project_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_text_context
  ADD CONSTRAINT narro_text_context_ibfk_36
      FOREIGN KEY (file_id)
      REFERENCES narro_file (file_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_suggestion_comment
  ADD CONSTRAINT narro_suggestion_comment_ibfk_1
      FOREIGN KEY (suggestion_id)
      REFERENCES narro_text_suggestion (suggestion_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_suggestion_comment
  ADD CONSTRAINT narro_suggestion_comment_ibfk_2
      FOREIGN KEY (user_id)
      REFERENCES narro_user (user_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_suggestion_vote
  ADD CONSTRAINT narro_suggestion_vote_ibfk_5
      FOREIGN KEY (suggestion_id)
      REFERENCES narro_text_suggestion (suggestion_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_suggestion_vote
  ADD CONSTRAINT narro_suggestion_vote_ibfk_6
      FOREIGN KEY (text_id)
      REFERENCES narro_text (text_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_suggestion_vote
  ADD CONSTRAINT narro_suggestion_vote_ibfk_7
      FOREIGN KEY (user_id)
      REFERENCES narro_user (user_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_text_context_comment
  ADD CONSTRAINT narro_text_context_comment_ibfk_1
      FOREIGN KEY (context_id)
      REFERENCES narro_text_context (context_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_text_context_comment
  ADD CONSTRAINT narro_text_context_comment_ibfk_2
      FOREIGN KEY (user_id)
      REFERENCES narro_user (user_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_text_context_plural
  ADD CONSTRAINT narro_text_context_plural_ibfk_13
      FOREIGN KEY (context_id)
      REFERENCES narro_text_context (context_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_text_context_plural
  ADD CONSTRAINT narro_text_context_plural_ibfk_14
      FOREIGN KEY (text_id)
      REFERENCES narro_text (text_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_text_context_plural
  ADD CONSTRAINT narro_text_context_plural_ibfk_15
      FOREIGN KEY (valid_suggestion_id)
      REFERENCES narro_text_suggestion (suggestion_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_text_context_plural
  ADD CONSTRAINT narro_text_context_plural_ibfk_16
      FOREIGN KEY (popular_suggestion_id)
      REFERENCES narro_text_suggestion (suggestion_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_user_permission
  ADD CONSTRAINT narro_user_permission_ibfk_1
      FOREIGN KEY (user_id)
      REFERENCES narro_user (user_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_user_permission
  ADD CONSTRAINT narro_user_permission_ibfk_2
      FOREIGN KEY (permission_id)
      REFERENCES narro_permission (permission_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_user_permission
  ADD CONSTRAINT narro_user_permission_ibfk_3
      FOREIGN KEY (project_id)
      REFERENCES narro_project (project_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

ALTER TABLE narro_file_header
  ADD CONSTRAINT narro_file_header_ibfk_1
      FOREIGN KEY (file_id)
      REFERENCES narro_file (file_id)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION;

