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
            return QApplication::GetSpellSuggestionsWithPspell($strText);
        }

        public static function GetSpellSuggestionsWithPspell($strText) {
            $strCleanText = str_replace(array('\n', '.', '\\', '!', '?'), array(' ', ' ', ' ', ' ', ' '), $strText);
            $strCleanText = strip_tags($strCleanText);
            /**
             * mozilla entitites: &xxx;
             */
            $strCleanText = preg_replace('/&[a-zA-Z\-0-9]+\;/', ' ' , $strCleanText);
            /**
            * keyboard shortcuts
            */
            $strCleanText = preg_replace('/[~&]/', '' , $strCleanText);
            /**
            * openoffice entities: %xxx %%xxx %%%xxx #xxx and so on
             */
            $strCleanText = preg_replace('/[\$\[\#\%]{1,3}[a-zA-Z\_\-0-9]+[\$\]\#\%]{0,3}/i', ' ', $strCleanText);

            $strCleanText = preg_replace('/[^a-z\-\.!;ăîşţĂÎŞŢșȘțȚ]+/i', ' ', $strCleanText);
            $arrCleanText = preg_split('/\s+/', $strCleanText);
            $arrSuggestions = array();

            if (!defined('PSPELL_FAST'))
                return self::GetSpellSuggestionsWithAspell($strText);

            if (!$pspell_config = pspell_config_create(QApplication::$objUser->getPreferenceValueByName('Spellcheck language'), null, null, 'utf-8'))
                return self::GetSpellSuggestionsWithAspell($strText);

            if (!pspell_config_data_dir($pspell_config, realpath(dirname(__FILE__)) . "/../data/dictionaries/"))
                return self::GetSpellSuggestionsWithAspell($strText);

            if (!pspell_config_dict_dir($pspell_config, realpath(dirname(__FILE__)) . "/../data/dictionaries/"))
                return self::GetSpellSuggestionsWithAspell($strText);

            if (!$pspell_link = pspell_new_config($pspell_config)) {
                return self::GetSpellSuggestionsWithAspell($strText);
            }

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

        public static function GetSpellSuggestionsWithAspell($strText) {
            $strCleanText = str_replace(array('\n', '.', '\\', '!', '?'), array(' ', ' ', ' ', ' ', ' '), $strText);
            $strCleanText = strip_tags($strCleanText);
            /**
             * mozilla entitites: &xxx;
             */
            $strCleanText = preg_replace('/&[a-zA-Z\-0-9]+\;/', ' ' , $strCleanText);
            /**
             * keyboard shortcuts
             */
            $strCleanText = preg_replace('/[~&]/', '' , $strCleanText);
            /**
             * openoffice entities: %xxx %%xxx %%%xxx #xxx and so on
             */
            $strCleanText = preg_replace('/[\$\[\#\%]{1,3}[a-zA-Z\_\-0-9]+[\$\]\#\%]{0,3}/i', ' ', $strCleanText);

            $strCleanText = preg_replace('/[^a-z\-\.!;ăîşţĂÎŞŢșȘțȚ]+/i', ' ', $strCleanText);
            $arrCleanText = preg_split('/\s+/', $strCleanText);

            $arrResult = array();

            $strCleanText = iconv('utf-8', 'iso8859-2', $strCleanText);
            exec('echo "'.$strCleanText.'" | aspell --lang='.QApplication::$objUser->getPreferenceValueByName('Spellcheck language').' --dict-dir='.__DOCROOT__ . __SUBDIRECTORY__ .'/data/dictionaries -a', $arr, $ret);
            if ($ret != 0)
                return false;
                //return self::GetSpellSuggestionsWithSoap($strText);
            foreach($arr as $strWord) {
                if (strpos($strWord, '&') === 0) {
                    preg_match('/&\s+([^\s]+)\s+[^:]+:(.*)/', $strWord, $arrMatches);
                    $strMisspelledWord = iconv('iso8859-2', 'utf-8', $arrMatches[1]);
                    $strSuggestions = iconv('iso8859-2', 'utf-8', $arrMatches[2]);
                    $strSuggestions = str_replace(' ', '', $strSuggestions);
                    $arrSuggestions = split(',', $strSuggestions);
                    array_slice($arrSuggestions, 0, 3);
                    if (in_array($strMisspelledWord, $arrSuggestions))
                        continue;
                    $arrResult[$strMisspelledWord] = $arrSuggestions;
                    $arrResult[$strMisspelledWord] = array_slice($arrResult[$strMisspelledWord], 0, 3);
                }
            }
            return $arrResult;
        }

        public static function GetSpellSuggestionsWithSoap($strText) {
            $strWsdlUrl = sprintf('%s?wsdl', 'http://89.137.64.115/~alexxed/pspell.php');
            try {
                $objClient = new SoapClient($strWsdlUrl);
            }
            catch (SoapFault $objFault) {
                return array();
            }
            try {
                $arrSuggestions = unserialize($objClient->GetSpellSuggestions($strText));
            }
            catch (SoapFault $objFault) {
                return array();
            }

            if (!is_array($arrSuggestions))
                return array();
            else
                return $arrSuggestions;
        }

        public static function RegisterPreference($strName, $strType = 'text', $strDescription = '', $strDefaultValue = '', $arrValues = array()) {
            self::$arrPreferences[$strName] = array('type'=> $strType, 'description'=>$strDescription, 'default'=>$strDefaultValue, 'values'=>$arrValues);
        }

        public static function RegisterFormat($strName, $strPluginName) {
            self::$arrFileFormats[$strName] = $strPluginName;
        }
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


    QApplication::$objPluginHandler = new NarroPluginHandler(dirname(__FILE__) . '/narro/plugins');

    QApplication::RegisterPreference('Items per page', 'number', 'How many items are displayed per page', 10);
    QApplication::RegisterPreference('Font size', 'option', 'The application font size', 'medium', array('x-small', 'small', 'medium', 'large', 'x-large'));
    QApplication::RegisterPreference('Language', 'option', 'The preferred language for the application interface', 'en', array('ro', 'en'));
    QApplication::RegisterPreference('Spellcheck language', 'option', 'The language used for spellchecking', 'en-US', array('ro', 'en-US'));

    if (isset($_SESSION['objUser']) && $_SESSION['objUser'] instanceof NarroUser)
        QApplication::$objUser = $_SESSION['objUser'];
    else
        QApplication::$objUser = NarroUser::LoadAnonymousUser();
    if (!QApplication::$objUser instanceof NarroUser)
        // @todo add handling here
        throw Exception('Could not create an instance of NarroUser');


    //////////////////////////////////////////////
    // Setup Internationalization and Localization (if applicable)
    // Note, this is where you would implement code to do Language Setting discovery, as well, for example:
    // * Checking against $_GET['language_code']
    // * checking against session (example provided below)
    // * Checking the URL
    // * etc.
    // TODO: options to do this are left to the developer
    //////////////////////////////////////////////
    if (isset($_SESSION)) {
        if (array_key_exists('country_code', $_SESSION))
            QApplication::$CountryCode = $_SESSION['country_code'];
        if (array_key_exists('language_code', $_SESSION))
            QApplication::$LanguageCode = $_SESSION['language_code'];
    }

    QCache::$CachePath = __DOCROOT__ . __SUBDIRECTORY__ . '/data/cache';

    // Initialize I18n if QApplication::$LanguageCode is set
    if (QApplication::$LanguageCode)
        QI18n::Initialize();
    else {
        QApplication::$CountryCode = 'us';
        QApplication::$LanguageCode = QApplication::$objUser->getPreferenceValueByName('Language');
        try {
            QI18n::Initialize();
        } catch (Exception $objEx) {
            QApplication::$LanguageCode = 'en';
            QI18n::Initialize();
        }
    }

?>
