<?php
    /**
     * Narro is an application that allows online software translation and maintenance.
     * Copyright (C) 2008-2011 Alexandru Szasz <alexxed@gmail.com>
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

    class QApplication extends QApplicationBase {
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
        public static $AvailablePreferences;
        /**
         * @var Zend_Cache_Core
         */
        public static $Cache;
        /**
         * @var Zend_Session_Namespace
         */
        public static $Session;
        /**
         * @var NarroLanguage
         */
        public static $TargetLanguage;
        /**
         * @var NarroLanguage
         */
        public static $SourceLanguage;
        /**
         * @var Zend_Translate
         */
        public static $TranslationEngine;
        /**
         * @var Zend_Log
         */
        public static $Logger;

        public static $LogFile;

        /**
         * An array of Database objects, as initialized by QApplication::InitializeDatabaseConnections()
         *
         * @var DatabaseBase[] QMySqli5Database
         */
        public static $Database;

        ////////////////////////////
        // Additional Static Methods
        ////////////////////////////

        public static function Autoload($strClassName) {
            if (!parent::Autoload($strClassName)) {
                if (file_exists($strFilePath = sprintf('%s/narro/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/database/%s.class.php', __QCUBED_CORE__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/narro/importer/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/narro/panel/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/narro/sources/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/narro/search/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/model/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/qcubed_custom_controls/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/%s.php', __NARRO_INCLUDES__, str_replace('_', '/', $strClassName))))
                    require_once($strFilePath);
                else
                    throw new Exception(sprintf('Cannot find the file that contains the class "%s"', $strClassName));
            }

        }

        public static function LogError($strError) {
            if (QApplication::$Logger)
                QApplication::$Logger->log($strError, Zend_Log::ERR);
        }

        public static function LogInfo($strError) {
            if (QApplication::$Logger)
                QApplication::$Logger->log($strError, Zend_Log::INFO);
        }

        public static function LogWarn($strError) {
            if (QApplication::$Logger)
                QApplication::$Logger->log($strError, Zend_Log::WARN);
        }

        public static function LogDebug($strError) {
            if (QApplication::$Logger && SERVER_INSTANCE == 'dev')
                QApplication::$Logger->log($strError, Zend_Log::DEBUG);
        }

        public static function GetLogger() {
            return QApplication::$Logger;
        }

        public static function RegisterPreference($strName, $strType = 'text', $strDescription = '', $strDefaultValue = '', $arrValues = array()) {
            self::$AvailablePreferences[$strName] = array('type'=> $strType, 'description'=>$strDescription, 'default'=>$strDefaultValue, 'values'=>$arrValues);
        }

        public static function RegisterFormat($strName, $strPluginName) {
            self::$arrFileFormats[$strName] = $strPluginName;
        }

        public static function GetUserId() {
            return self::$User->UserId;
        }

        public static function GetLanguageId() {
            return self::$TargetLanguage->LanguageId;
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
            foreach(QApplication::$Cache->getIdsMatchingTags(array('NarroUser' . $intUserId)) as $strCacheId)
                QApplication::$Cache->remove($strCacheId);
        }

        public static function GetBrowserLanguage() {

            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                if (strstr($_SERVER['HTTP_ACCEPT_LANGUAGE'], ';')) {
                    $arrLangGroups = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                    foreach($arrLangGroups as $strLangGroup) {
                        if (strstr($strLangGroup, ',')) {
                            $arrLangCodes = explode(',', $strLangGroup);
                            foreach($arrLangCodes as $strLangCode) {
                                $objLanguage = NarroLanguage::QuerySingle(QQ::AndCondition(QQ::Equal(QQN::NarroLanguage()->LanguageCode, $strLangCode), QQ::Equal(QQN::NarroLanguage()->Active, 1)));
                                if ($objLanguage instanceof NarroLanguage) {
                                    return $objLanguage;
                                }
                            }
                        }
                        else {
                            $objLanguage = NarroLanguage::QuerySingle(QQ::AndCondition(QQ::Equal(QQN::NarroLanguage()->LanguageCode, $strLangGroup), QQ::Equal(QQN::NarroLanguage()->Active, 1)));
                            if ($objLanguage instanceof NarroLanguage) {
                                return $objLanguage;
                            }
                        }
                    }
                }
            }
            return false;
        }

        public static function ClearLog() {
            if (file_exists(self::$LogFile))
                return @unlink(self::$LogFile);
        }

        public static function InitializeSession() {
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
            QApplication::$Session = new Zend_Session_Namespace('Narro');
        }

        public static function InitializeCache() {
            require_once 'Zend/Cache.php';


            $frontendOptions = array(
                'lifetime' => null, // cache forever
                'automatic_serialization' => true,
                'caching' => defined('__ZEND_CACHE_ENABLED__')?__ZEND_CACHE_ENABLED__:true
            );

            require_once __NARRO_INCLUDES__ . '/Zend_Cache_Backend_Pdomysql.php';
            Zend_Cache::$standardExtendedBackends[] = 'Zend_Cache_Backend_Pdomysql';
            Zend_Cache::$availableBackends[] = 'Zend_Cache_Backend_Pdomysql';
            $arrDB = unserialize(DB_CONNECTION_1);

            $backendOptions = array(
                'host' => $arrDB['server'],
                'port' => $arrDB['port'],
                'dbname' => $arrDB['database'],
                'user' => $arrDB['username'],
                'password' => $arrDB['password'],
            );

            QApplication::$Cache = Zend_Cache::factory('Core', 'Zend_Cache_Backend_Pdomysql', $frontendOptions, $backendOptions, true, true, true);
        }

        public static function InitializeLanguage() {
            global $argv;

            if (strstr($_SERVER['REQUEST_URI'], '_devtools')) return false;
            if (strstr($_SERVER['REQUEST_URI'], 'image.php')) return false;

            QApplication::$SourceLanguage = NarroLanguage::LoadByLanguageCode(__SOURCE_LANGUAGE_CODE__);

            // language passed through the l parameter
            if ($_REQUEST['l'])
                QApplication::$TargetLanguage = NarroLanguage::LoadByLanguageCode($_REQUEST['l']);
            // language passed through cli parameter
            elseif (isset($argv) && $strLanguage = $argv[array_search('--translation-lang', $argv)+1])
                QApplication::$TargetLanguage = NarroLanguage::LoadByLanguageCode($strLanguage);
            // language guessed from the browser settings
            else {
                $objGuessedLanguage = QApplication::GetBrowserLanguage();
                if ($objGuessedLanguage instanceof NarroLanguage && !isset($_REQUEST['openid_mode'])) {
                    QApplication::Redirect(sprintf('projects.php?l=%s', $objGuessedLanguage->LanguageCode));
                    exit;
                }
                else
                    QApplication::$TargetLanguage = QApplication::$SourceLanguage;

            }
        }

        public static function InitializeUser() {
            if (isset(QApplication::$Session->User) && QApplication::$Session->User instanceof NarroUser) {
                QApplication::$User = QApplication::$Session->User;
                QApplication::$UseAjax = (QApplication::$User->getPreferenceValueByName('Use AJAX') == 'Yes');
            }
            else {
                QApplication::$User = NarroUser::LoadAnonymousUser();
                QApplication::$Session->User = QApplication::$User;
            }

            if (!QApplication::$User instanceof NarroUser)
                // @todo add handling here
                throw new Exception('Could not create an instance of NarroUser');

            define('__LOCALE_DIRECTORY__', __DOCROOT__ . __SUBDIRECTORY__ . '/locale/' . QApplication::$User->getPreferenceValueByName('Application language') . '/LC_MESSAGES');
        }

        public static function InitializeLogging($intProjectId = null) {
            global $argv;

            require_once('Zend/Log.php');
            require_once('Zend/Log/Writer/Stream.php');
            require_once('Zend/Log/Writer/Firebug.php');
            require_once('Zend/Log/Writer/Syslog.php');
            // project log via browser
            if (is_numeric($_REQUEST['p'])) {
                $intProjectId = $_REQUEST['p'];
                $strLanguageCode = $_REQUEST['l'];
            }
            // project log via cli
            elseif (isset($argv) && $intProjectId = $argv[array_search('--project', $argv)+1])
                $strLanguageCode = $argv[array_search('--translation-lang', $argv)+1];

            if (!is_null($intProjectId) && !is_null($strLanguageCode))
                QApplication::$LogFile = sprintf('%s/project-%d-%s.log', __TMP_PATH__, $intProjectId, $strLanguageCode);
            elseif (!is_null($intProjectId))
                QApplication::$LogFile = sprintf('%s/app-%s.log', __TMP_PATH__, $strLanguageCode);
            else
                QApplication::$LogFile = sprintf('%s/app.log', __TMP_PATH__, $intProjectId);

            @chmod(QApplication::$LogFile, 0666);

            QApplication::$Logger = new Zend_Log();
            QApplication::$Logger->addWriter(new Zend_Log_Writer_Stream(QApplication::$LogFile));
            if (isset($argv[0]))
                QApplication::$Logger->addWriter(new Zend_Log_Writer_Syslog());

            if (SERVER_INSTANCE == 'dev') {
                QApplication::$Logger->addWriter(new Zend_Log_Writer_QFirebug());
                QApplication::$Logger->addWriter(new Zend_Log_Writer_Syslog());
            }
        }

        public static function InitializeTranslationEngine() {
            if (file_exists(__LOCALE_DIRECTORY__ . '/narro.mo')) {
                require_once('Zend/Translate.php');
                require_once('Zend/Translate/Adapter/Gettext.php');
                try {
                    QApplication::$TranslationEngine = new Zend_Translate(
                        'gettext', __IMPORT_PATH__ . '/1/' . QApplication::$TargetLanguage->LanguageCode . '/narro.mo',
                        QApplication::$User->getPreferenceValueByName('Application language'),
                        array(
                            'disableNotices'=>true
                        )
                    );
                }
                catch (Exception $objEx) {
                    // gettext installed on the system does not support the language
                }
            }
        }
    }
?>
