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

    class QApplication extends QApplicationBase {
        public static $blnUseAjax = true;
        public static $objUser;
        public static $objPluginHandler;
        public static $arrPreferences;
        public static $arrFormats;
        public static $Cache;

        /**
        * This is called by the PHP5 Autoloader.  This method overrides the
        * one in ApplicationBase.
        *
        * @return void
        */
        public static function Autoload($strClassName) {
            // First use the Qcodo Autoloader
            parent::Autoload($strClassName);

            if (file_exists(dirname(__FILE__) . '/narro/' . $strClassName . '.class.php'))
                require_once(dirname(__FILE__) . '/narro/' . $strClassName . '.class.php');

            // TODO: Run any custom autoloading functionality (if any) here...
        }

        public static $EncodingType = 'UTF-8';

        ////////////////////////////
        // Additional Static Methods
        ////////////////////////////

        public static function GetSpellSuggestions($strText) {
            $strCleanText = mb_ereg_replace('[\\n\.,:;\\\!\?0-9]+', ' ', $strText);
            $strCleanText = strip_tags($strCleanText);
            /**
             * mozilla entitites: &xxx;
             */
            $strCleanText = mb_ereg_replace('&[a-zA-Z\-0-9]+\;', ' ' , $strCleanText);
            /**
             * keyboard shortcuts
             */
            $strCleanText = mb_ereg_replace('[~&]', '' , $strCleanText);
            /**
             * openoffice entities: %xxx %%xxx %%%xxx #xxx and so on
             */
            $strCleanText = mb_ereg_replace('[\$\[\#\%]{1,3}[a-zA-Z\_\-0-9]+[\$\]\#\%]{0,3}', ' ', $strCleanText);

            /**
             * some characters that mess with the spellchecking
             */
            $strCleanText = mb_ereg_replace('[\(\)]+', ' ', $strCleanText);

            $strSpellLang = QApplication::$objUser->getPreferenceValueByName('Language');

            return QApplication::GetSpellSuggestionsWithPspell($strCleanText, $strSpellLang);
        }

        public static function GetSpellSuggestionsWithPspell($strText, $strSpellLang) {


            if (file_exists(__DICTIONARY_PATH__ . '/' . $strSpellLang . '.dat')) {
                $strDictPath = realpath(dirname(__FILE__)) . "/../data/dictionaries/";
                if (!defined('PSPELL_FAST'))
                    return self::GetSpellSuggestionsWithHunspell($strText, $strSpellLang);

                if (!$pspell_config = pspell_config_create($strSpellLang, null, null, 'utf-8'))
                    return self::GetSpellSuggestionsWithHunspell($strText, $strSpellLang);
                if (!pspell_config_data_dir($pspell_config, $strDictPath))
                    return self::GetSpellSuggestionsWithHunspell($strText, $strSpellLang);

                if (!pspell_config_dict_dir($pspell_config, $strDictPath))
                    return self::GetSpellSuggestionsWithHunspell($strText, $strSpellLang);

                if (!$pspell_link = pspell_new_config($pspell_config)) {
                    return self::GetSpellSuggestionsWithHunspell($strText, $strSpellLang);
                }
            }
            else
                if (file_exists('/usr/lib/aspell-0.60/' . $strSpellLang . '.dat')) {
                    $strDictPath = '/usr/lib/aspell-0.60/';
                    $pspell_link = pspell_new($strSpellLang, null, null, 'utf-8');
                }
                else
                    return self::GetSpellSuggestionsWithHunspell($strText, $strSpellLang);

            $arrSuggestions = array();
            $arrCleanText = mb_split('\s+', $strText);

            foreach($arrCleanText as $strCleanText) {

                if (!pspell_check($pspell_link, trim($strCleanText))) {
                    $suggestions = pspell_suggest($pspell_link, trim($strCleanText));
                    if (in_array($strCleanText, $suggestions))
                        continue;
                    $arrSuggestions[$strCleanText] = array_slice($suggestions, 0, 4);
                }
            }

            return $arrSuggestions;
        }

        public static function GetSpellSuggestionsWithHunspell($strText, $strSpellLang) {

            $arrCleanText = mb_split('\s+', $strText);
            $arrResult = array();

            $hndFile = fopen(__TMP_PATH__ .'/spell-' . md5($strText), 'w');

            fwrite($hndFile, $strText);
            fclose($hndFile);
            chmod(__TMP_PATH__ .'/spell-' . md5($strText), 0777);

            $strCommand = sprintf('/usr/bin/hunspell -i utf-8 -a -d %s -a %s',__DICTIONARY_PATH__ . '/' . $strSpellLang, __TMP_PATH__ .'/spell-' . md5($strText));

            if (file_exists(__DICTIONARY_PATH__ . '/' . $strSpellLang . '.aff'))
                $strCmdOutput = system($strCommand, $intRet);
            else
                return false;

            if ($strCmdOutput == '') {
                return false;
            }

            $arrLines = mb_split('\n', $strCmdOutput);

            foreach($arrLines as $strWord) {
                if (strpos($strWord, '&') === 0) {
                    preg_match('/&\s+([^\s]+)\s+[^:]+:(.*)/', $strWord, $arrMatches);

                    $strMisspelledWord = $arrMatches[1];
                    $strSuggestions = $arrMatches[2];
                    $arrSuggestions = mb_split('\,', $strSuggestions);
                    array_slice($arrSuggestions, 0, 3);
                    if (in_array($strMisspelledWord, $arrSuggestions))
                        continue;
                    $arrResult[$strMisspelledWord] = $arrSuggestions;
                    $arrResult[$strMisspelledWord] = array_slice($arrResult[$strMisspelledWord], 0, 3);
                }
            }

            return $arrResult;
        }

        public static function RegisterPreference($strName, $strType = 'text', $strDescription = '', $strDefaultValue = '', $arrValues = array()) {
            self::$arrPreferences[$strName] = array('type'=> $strType, 'description'=>$strDescription, 'default'=>$strDefaultValue, 'values'=>$arrValues);
        }

        public static function RegisterFormat($strName, $strPluginName) {
            self::$arrFileFormats[$strName] = $strPluginName;
        }

        public static function Translate($strText) {
            if (class_exists('NarroSelfTranslate'))
                return NarroSelfTranslate::Translate($strText);
            else
                return $strText;
        }
    }

    function t($strText) {
        return QApplication::Translate($strText);
    }

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
    QApplication::Initialize();
    QApplication::InitializeDatabaseConnections();


    /////////////////////////////
    // Start Session Handler (if required)
    /////////////////////////////
    session_start();

    QApplication::RegisterPreference('Items per page', 'number', 'How many items are displayed per page', 10);
    QApplication::RegisterPreference('Font size', 'option', 'The application font size', 'medium', array('x-small', 'small', 'medium', 'large', 'x-large'));
    QApplication::RegisterPreference('Language', 'option', 'The language you are translating to.', 'en_US', array('en_US'));

    if (isset($_SESSION['objUser']) && $_SESSION['objUser'] instanceof NarroUser)
        QApplication::$objUser = $_SESSION['objUser'];
    else
        QApplication::$objUser = NarroUser::LoadAnonymousUser();

    if (!QApplication::$objUser instanceof NarroUser)
        // @todo add handling here
        throw Exception('Could not create an instance of NarroUser');

    QApplication::$LanguageCode = QApplication::$objUser->Language->LanguageCode;

    QCache::$CachePath = __DOCROOT__ . __SUBDIRECTORY__ . '/data/cache';
    QForm::$FormStateHandler = 'QFileFormStateHandler';
    QFileFormStateHandler::$StatePath = __TMP_PATH__ . '/qform_states/';

    require_once __INCLUDES__ . '/Zend/Cache.php';

    $frontendOptions = array(
        'lifetime' => null, // cache forever
        'automatic_serialization' => true
    );

    $backendOptions = array(
        'cache_dir' => QCache::$CachePath . '/zend'
    );

    QApplication::$Cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

    QApplication::$objPluginHandler = new NarroPluginHandler(dirname(__FILE__) . '/narro/plugins');
?>