<?php
    /**
     * Narro is an application that allows online software translation and maintenance.
     * Copyright (C) 2008-2010 Alexandru Szasz <alexxed@gmail.com>
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
    define('SERVER_INSTANCE', 'dev');
    define ('__PHP_CLI_PATH__', '/usr/bin/php');
    define ('ADMIN_EMAIL_ADDRESS', 'user@host.com');
    define ('__HTTP_URL__', 'http://localhost');
    define ('NARRO_VERSION', '1.0beta');
    /**
     * this constant allows any user do export files or import up to this defined size
     * default is almost 10MB
     */
    define ('__MAXIMUM_FILE_SIZE_TO_IMPORT__', 10048576);
    define ('__MAXIMUM_FILE_SIZE_TO_EXPORT__', 10048576);
    define ('__SOURCE_LANGUAGE_ID__', 58);
    /**
     * used for email sending from narro (notifications and password recovery)
     */
    define ('__FROM_EMAIL_ADDRESS__', 'root@localhost');
    define ('__FROM_EMAIL_NAME__', 'Narro');

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

    define ('__DOCROOT__', realpath(dirname(__FILE__) . '/../../..'));
    define ('__VIRTUAL_DIRECTORY__', '');
    define ('__SUBDIRECTORY__', '/narro');

    define ('__NARRO_INCLUDES__', __DOCROOT__ . __SUBDIRECTORY__ . '/includes');

    define ('__DICTIONARY_PATH__', __DOCROOT__ . __SUBDIRECTORY__ . '/data/dictionaries');
    define ('__TMP_PATH__', __DOCROOT__ . __SUBDIRECTORY__ . '/data/tmp');
    define ('__IMPORT_PATH__', __DOCROOT__ . __SUBDIRECTORY__ . '/data/import');
    define ('__RSS_PATH__', __DOCROOT__ . __SUBDIRECTORY__ . '/data/rss');
    define ('__SEARCH_INDEX_PATH__', __DOCROOT__ . __SUBDIRECTORY__ . '/data/search');

    define('DB_CONNECTION_1', serialize(array(
        'adapter' => 'MySqli5',
        'server' => 'localhost',
        'port' => null,
        'database' => 'narro',
        'username' => 'narro',
        'password' => '',
        'profiling' => false)));

    set_include_path(
        dirname(__FILE__) . __SUBDIRECTORY__ . PATH_SEPARATOR .
        'includes' . PATH_SEPARATOR .
        'includes' . '/qcubed_custom_controls' . PATH_SEPARATOR .
        'includes' . '/narro/importer' . PATH_SEPARATOR .
        'includes' . '/narro/search' . PATH_SEPARATOR .
        'includes' . '/narro' . PATH_SEPARATOR .
        'includes' . '/PEAR' . PATH_SEPARATOR .
        '/usr/share/php' . PATH_SEPARATOR .
        '/usr/share/pear' . PATH_SEPARATOR .
        get_include_path()
        );

    ini_set('mbstring.encoding_translation', true);
    ini_set('mbstring.internal_encoding', 'UTF-8');
    ini_set('memory_limit', "512M");
    set_time_limit(0);
    error_reporting(E_ALL ^ E_NOTICE);
?>
