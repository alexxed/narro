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

    class NarroApp extends QApplication {
        /**
         * @var boolean
         */
        public static $UseAjax = true;
        /**
         * @var NarroUser
         */
        public static $User;
        /**
         * @var NarroPluginHandler
         */
        public static $PluginHandler;
        /**
         * @var array
         */
        public static $arrPreferences;
        /**
         * @var array
         */
        public static $arrFormats;
        /**
         * @var Zend_Cache_Core
         */
        public static $Cache;
        /**
         * @var NarroLanguage
         */
        public static $Language;
        /**
         * @var Zend_Translate
         */
        public static $TranslationEngine;

        ////////////////////////////
        // Additional Static Methods
        ////////////////////////////

        public static function RegisterPreference($strName, $strType = 'text', $strDescription = '', $strDefaultValue = '', $arrValues = array()) {
            self::$arrPreferences[$strName] = array('type'=> $strType, 'description'=>$strDescription, 'default'=>$strDefaultValue, 'values'=>$arrValues);
        }

        public static function RegisterFormat($strName, $strPluginName) {
            self::$arrFileFormats[$strName] = $strPluginName;
        }

        public static function GetUserId() {
            return self::$User->UserId;
        }

        public static function GetLanguageId() {
            return self::$Language->LanguageId;
        }

        public static function HasPermissionForThisLang($strPermissionName, $intProjectId = null) {
            if (self::$User instanceof NarroUser)
                return self::$User->hasPermission($strPermissionName, $intProjectId, self::GetLanguageId());
            else
                return false;
        }

        public static function HasPermission($strPermissionName, $intProjectId = null, $intLanguageId = null) {
            if (self::$User instanceof NarroUser)
                return self::$User->hasPermission($strPermissionName, $intProjectId, $intLanguageId);
            else
                return false;
        }

        /**
         * Translation function, no plural suport yet in Zend_Translate
         * @param $strText
         * @param $strPlural
         * @param $intCnt
         * @return string
         */
        public static function Translate($strText, $strPlural = null, $intCnt = null) {
            if (isset(self::$TranslationEngine))
                return self::$TranslationEngine->_($strText);
            else
                return $strText;
        }

        public static function ResetUser($intUserId) {
            foreach(NarroApp::$Cache->getIdsMatchingTags(array('NarroUser' . $intUserId)) as $strCacheId)
                NarroApp::$Cache->remove($strCacheId);
        }
    }

    function t($strText, $strPlural = null, $intCnt = null) {
        return NarroApp::Translate($strText, $strPlural, $intCnt);
    }

    if (!file_exists(__DOCROOT__ . __SUBDIRECTORY__ . '/data'))
        die(sprintf('Please create a directory "data" in %s and give it write permissions for everyone (chmod 777)', __DOCROOT__ . __SUBDIRECTORY__));

    foreach (array('cache', 'cache/i18n', 'cache/zend', 'dictionaries', 'import', 'tmp', 'tmp/session', 'tmp/qform_state') as $strDirName) {
        if (!file_exists(__DOCROOT__ . __SUBDIRECTORY__ . '/data/' . $strDirName)) {
            if (!mkdir(__DOCROOT__ . __SUBDIRECTORY__ . '/data/' . $strDirName))
                die(sprintf('Could not create a directory. Please create the directory "%s" and give it write permissions for everyone (chmod 777)', __DOCROOT__ . __SUBDIRECTORY__ . '/data/' . $strDirName));
            else
                chmod(__DOCROOT__ . __SUBDIRECTORY__ . '/data/' . $strDirName, 0777);
        }
    }

    $arrConData = unserialize(DB_CONNECTION_1);

    $link = mysql_connect($arrConData['server'].(($arrConData['port'])?':' . $arrConData['port']:''), $arrConData['username'], $arrConData['password']);
    if (!$link) {
        print(sprintf('Unable to connect to the dabase. Please check database settings in file "%s"', dirname(__FILE__) . '/configuration.inc.php') . '<br />');
        print(sprintf('Error: "%s"', mysql_error()));
        die();
    }

    if (!mysql_select_db($arrConData['database'], $link)) {
        print(sprintf('Unable to connect to the dabase. Please check database settings in file "%s"', dirname(__FILE__) . '/configuration.inc.php') . '<br />');
        print(sprintf('Error: "%s"', mysql_error()));
        die();
    }

    NarroApp::$ClassFile['File_Archive'] = __INCLUDES__ . '/PEAR/File/Archive.php';
    NarroApp::$ClassFile['NarroTextListForm'] = 'narro_text_list.php';
    NarroApp::$ClassFile['FirePHP'] = __INCLUDES__ . '/PEAR/FirePHPCore/FirePHP.class.php';
    NarroApp::$ClassFile['FB'] = __INCLUDES__ . '/PEAR/FirePHPCore/fb.php';

    ///////////////////////
    // Setup Error Handling
    ///////////////////////
    /*
    * Set Error/Exception Handling to the default
    * Qcodo HandleError and HandlException functions
    * (Only in non CLI mode)
    *
    * Feel free to change, if needed, to your own
    * custom error handling script(s).
    */
    if (array_key_exists('SERVER_PROTOCOL', $_SERVER)) {
        set_error_handler('QcodoHandleError');
        set_exception_handler('QcodoHandleException');
    }


    ////////////////////////////////////////////////
    // Initialize the Application and DB Connections
    ////////////////////////////////////////////////
    NarroApp::Initialize();
    NarroApp::InitializeDatabaseConnections();


    /////////////////////////////
    // Start Session Handler (if required)
    /////////////////////////////
    require_once 'Zend/Session.php';
    Zend_Session::setOptions(
        array(
            'name'              => 'NARRO_ID',
            'cookie_lifetime'   => 31*24*3600,
            'gc_maxlifetime'    => 31*24*3600,
            'save_path'         => __TMP_PATH__ . '/session',
            'cookie_path'       => __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__,
        )
    );

    require_once 'Zend/Session/Namespace.php';
    $objNarroSession = new Zend_Session_Namespace('Narro');

    QCache::$CachePath = __DOCROOT__ . __SUBDIRECTORY__ . '/data/cache';
    QForm::$FormStateHandler = 'QFileFormStateHandler';
    QFileFormStateHandler::$StatePath = __TMP_PATH__ . '/qform_state';

    require_once 'Zend/Cache.php';

    $frontendOptions = array(
        'lifetime' => null, // cache forever
        'automatic_serialization' => true
    );

    $backendOptions = array(
        'cache_dir' => QCache::$CachePath . '/zend'
    );

    NarroApp::$Cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    if (NarroApp::QueryString('l'))
        NarroApp::$Language = NarroLanguage::LoadByLanguageCode(NarroApp::QueryString('l'));

    NarroApp::RegisterPreference('Items per page', 'number', 'How many items are displayed per page', 10);
    NarroApp::RegisterPreference('Font size', 'option', 'The application font size', 'medium', array('x-small', 'small', 'medium', 'large', 'x-large'));
    NarroApp::RegisterPreference('Language', 'option', 'The language you are translating to.', NarroApp::QueryString('l'), array(NarroApp::QueryString('l')));
    NarroApp::RegisterPreference('Application language', 'option', 'The language you want to see Narro in.', (isset(NarroApp::$Language))?NarroApp::$Language->LanguageCode:'en-US', array((isset(NarroApp::$Language))?NarroApp::$Language->LanguageCode:'en-US'));
    NarroApp::RegisterPreference('Special characters', 'text', 'Characters that are not on your keyboard, separated by spaces.', '$€');
    NarroApp::RegisterPreference('Other languages', 'text', 'Other languages that you want to check for suggestions, separated by spaces.', 'ro');

    if (isset($objNarroSession->User) && $objNarroSession->User instanceof NarroUser)
        NarroApp::$User = $objNarroSession->User;
    else
        NarroApp::$User = NarroUser::LoadAnonymousUser();

    if (!NarroApp::$User instanceof NarroUser)
        // @todo add handling here
        throw new Exception('Could not create an instance of NarroUser');

    if (!NarroApp::$Cache->getIdsMatchingTags(array('NarroUser' . NarroApp::$User->UserId))) {
        NarroApp::$User = NarroUser::LoadByUserId(NarroApp::$User->UserId);
        $objNarroSession->User = NarroApp::$User;
        NarroApp::$Cache->save(NarroApp::$User, 'NarroUser' . NarroApp::$User->UserId, array('NarroUser' . NarroApp::$User->UserId));
    }

    if (!isset(NarroApp::$Language))
        NarroApp::$Language = NarroApp::$User->Language;

    NarroApp::$LanguageCode = NarroApp::$Language->LanguageCode;

    require_once 'Zend/Translate.php';

    define('__LOCALE_DIRECTORY__', __DOCROOT__ . __SUBDIRECTORY__ . '/locale/' . NarroApp::$User->getPreferenceValueByName('Application language') . '/LC_MESSAGES');
    if (!is_writable(__DOCROOT__ . __SUBDIRECTORY__ . '/locale/'))
        die(sprintf('Please give write permissions for everyone (chmod 777) to the directory "%s"', __DOCROOT__ . __SUBDIRECTORY__ . '/locale/'));

    if (!file_exists(__LOCALE_DIRECTORY__)) {
        if (!mkdir(__LOCALE_DIRECTORY__, 0777, true))
            die(sprintf('Could not create a directory. Please create the directory "%s" and give it write permissions for everyone (chmod 777)', __LOCALE_DIRECTORY__));
        else
            NarroUtils::RecursiveChmod(__LOCALE_DIRECTORY__);
    }

    if (file_exists(__LOCALE_DIRECTORY__ . '/narro.mo'))
        NarroApp::$TranslationEngine = new Zend_Translate('gettext', __LOCALE_DIRECTORY__ . '/narro.mo', NarroApp::$User->getPreferenceValueByName('Application language'));

    if (!extension_loaded('mbstring'))
        die('This version of Narro needs php-mbstring, please install it');

    if (!function_exists('mb_stripos'))
        die('This version of Narro needs mb_stripos, that\'s available only in php versions bigger than 5.2.0');

    if (!extension_loaded('gd'))
        die('This version of Narro needs php-gd, please install it');

    NarroApp::$PluginHandler = new NarroPluginHandler(dirname(__FILE__) . '/narro/plugins');
?>
