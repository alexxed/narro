<?php
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

    /**
     * Paths that may need changing
     */
    define ('__DOCROOT__', realpath(dirname(__FILE__) . '/../..'));
    define ('__VIRTUAL_DIRECTORY__', '');
    define ('__HTTP_URL__', 'http://localhost');
    define ('__SUBDIRECTORY__', '/narro');
    define ('__PHP_CLI_PATH__', '/usr/bin/php');
    define ('ADMIN_EMAIL_ADDRESS', 'user@host.com');

    /**
     * Database configuration
     */
    define('DB_CONNECTION_1', serialize(array(
        'adapter' => 'MySqli5',
        'encoding' => 'UTF8',
        'server' => 'localhost',
        'port' => null,
        'database' => 'narro',
        'username' => 'narro',
        'password' => '',
        'profiling' => false)));

    define('SERVER_INSTANCE', 'prod');

    switch (SERVER_INSTANCE) {
        case 'dev':
        case 'prod':
    }

    define ('NARRO_VERSION', '0.9.4');
    define ('ALLOW_REMOTE_ADMIN', false);

    /**
     * default is almost 10MB
     */
    define ('__MAXIMUM_FILE_SIZE_TO_IMPORT__', '10048576');
    define ('__URL_REWRITE__', 'none');

    /**
     * Uncomment this lines and fill in the values if you want to use external authentication
     * @see narro_login.php for more detailes
     */
//    define ('__AUTH_EXTERNAL_DB_HOST__', 'localhost');
//    define ('__AUTH_EXTERNAL_DB_USERNAME__', 'root');
//    define ('__AUTH_EXTERNAL_DB_PASSWORD__', '');
//    define ('__AUTH_EXTERNAL_DB_NAME__', 'drupal');
//    define ('__AUTH_EXTERNAL_DB_TABLE__', 'users');
//    define ('__AUTH_EXTERNAL_DB_TABLE_USER_FIELD__', 'name');
//    define ('__AUTH_EXTERNAL_DB_TABLE_PASSWORD_FIELD__', 'pass');
//    define ('__AUTH_EXTERNAL_DB_TABLE_PASSWORD_FUNCTION__', 'MD5(?)');

    if ((function_exists('date_default_timezone_set')) && (!ini_get('date.timezone')))
        date_default_timezone_set('America/Los_Angeles');

    /**
     * Normally, you won't need to change the following settings
     */
    define ('__DEVTOOLS_CLI__', __DOCROOT__ . __SUBDIRECTORY__ . '/_devtools_cli');
    define ('__INCLUDES__', __DOCROOT__ .  __SUBDIRECTORY__ . '/includes');
    define ('__QCODO__', __INCLUDES__ . '/qcodo');
    define ('__QCODO_CORE__', __INCLUDES__ . '/qcodo/_core');
    define ('__DATA_CLASSES__', __INCLUDES__ . '/data_classes');
    define ('__DATAGEN_CLASSES__', __INCLUDES__ . '/data_classes/generated');
    define ('__FORMBASE_CLASSES__', __DOCROOT__ .  __SUBDIRECTORY__ . '/data/qcodo_generated/formbase_classes_generated');
    define ('__PANELBASE_CLASSES__', __DOCROOT__ .  __SUBDIRECTORY__ . '/data/qcodo_generated/panelbase_classes_generated');
    define ('__DEVTOOLS__', __SUBDIRECTORY__ . '/_devtools');
    define ('__FORM_DRAFTS__', __SUBDIRECTORY__ . '/data/qcodo_generated/form_drafts');
    define ('__PANEL_DRAFTS__', __SUBDIRECTORY__ . '/data/qcodo_generated/panel_drafts');

    // We don't want "Examples", and we don't want to download them during qcodo_update
    define ('__EXAMPLES__', null);

    define ('__JS_ASSETS__', __SUBDIRECTORY__ . '/assets/js');
    define ('__CSS_ASSETS__', __SUBDIRECTORY__ . '/assets/css');
    define ('__IMAGE_ASSETS__', __SUBDIRECTORY__ . '/assets/images');
    define ('__PHP_ASSETS__', __SUBDIRECTORY__ . '/assets/php');

    define('ERROR_PAGE_PATH', __PHP_ASSETS__ . '/_core/error_page.php');

    define ('__DICTIONARY_PATH__', __DOCROOT__ . __SUBDIRECTORY__ . '/data/dictionaries');
    define ('__TMP_PATH__', __DOCROOT__ . __SUBDIRECTORY__ . '/data/tmp');
    define ('__IMPORT_PATH__', '/data/import');
    define ('__RSS_PATH__', __DOCROOT__ . __SUBDIRECTORY__ . '/data/rss');
    define ('__SEARCH_INDEX_PATH__', __DOCROOT__ . __SUBDIRECTORY__ . '/data/search');

    set_include_path(
        get_include_path() . PATH_SEPARATOR .
        dirname(__FILE__) . __SUBDIRECTORY__ . PATH_SEPARATOR .
        __INCLUDES__ . PATH_SEPARATOR .
        __INCLUDES__ . '/narro/importer' . PATH_SEPARATOR .
        __INCLUDES__ . '/narro' . PATH_SEPARATOR .
        __INCLUDES__ . '/PEAR'
        );

    ini_set('mbstring.encoding_translation', true);
    ini_set('mbstring.internal_encoding', 'UTF-8');
    ini_set('memory_limit', "512M");

    set_time_limit(0);
    error_reporting(E_ALL ^ E_NOTICE);
    $GLOBALS['_PEAR_default_error_mode'] = PEAR_ERROR_TRIGGER;

?>
