<?php
    require_once (dirname(__FILE__) . '/../../configuration/configuration.qcubed.inc.php');
    require_once (dirname(__FILE__) . '/../../includes/narro/NarroUtils.class.php');

    if (!file_exists(__DOCROOT__ . __SUBDIRECTORY__ . '/data'))
        die(sprintf('Please create a directory "data" in %s and give it write permissions for everyone (chmod 777)', __DOCROOT__ . __SUBDIRECTORY__));

    foreach (array(__TMP_PATH__, __TMP_PATH__ . '/zend', __DOCROOT__ . __SUBDIRECTORY__ . '/data/dictionaries', __DOCROOT__ . __SUBDIRECTORY__ . '/data/import', __TMP_PATH__ . '/session', __TMP_PATH__ . '/qform_state') as $strDirName) {
        if (!file_exists($strDirName)) {
            if (!mkdir($strDirName))
            die(sprintf('Could not create a directory. Please create the directory "%s" and give it write permissions for everyone (chmod 777)', $strDirName));
            else
            chmod($strDirName, 0777);
        }
    }

    $arrConData = unserialize(DB_CONNECTION_1);

    $link = mysql_connect($arrConData['server'].(($arrConData['port'])?':' . $arrConData['port']:''), $arrConData['username'], $arrConData['password']);
    if (!$link) {
        print(sprintf('Unable to connect to the database. Please check database settings in file "%s"', dirname(__FILE__) . '/configuration.inc.php') . '<br />');
        print(sprintf('Error: "%s"', mysql_error()));
        die();
    }

    if (!mysql_select_db($arrConData['database'], $link)) {
        print(sprintf('Unable to connect to the database. Please check database settings in file "%s"', dirname(__FILE__) . '/configuration.inc.php') . '<br />');
        print(sprintf('Error: "%s"', mysql_error()));
        die();
    }


    if (!extension_loaded('mbstring'))
    die('This version of Narro needs php-mbstring, please install it');

    if (!function_exists('mb_stripos'))
    die('This version of Narro needs mb_stripos, that\'s available only in php versions bigger than 5.2.0');

    if (!extension_loaded('gd'))
    die('This version of Narro needs php-gd, please install it');


    if (!is_writable(__DOCROOT__ . __SUBDIRECTORY__ . '/locale/'))
    die(sprintf('Please give write permissions for everyone (chmod 777) to the directory "%s"', __DOCROOT__ . __SUBDIRECTORY__ . '/locale/'));

    if (!file_exists(__LOCALE_DIRECTORY__)) {
        if (!mkdir(__LOCALE_DIRECTORY__, 0777, true))
        die(sprintf('Could not create a directory. Please create the directory "%s" and give it write permissions for everyone (chmod 777)', __LOCALE_DIRECTORY__));
        else
        NarroUtils::RecursiveChmod(__LOCALE_DIRECTORY__);
    }
