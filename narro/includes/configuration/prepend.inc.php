<?php
    if (!defined('__PREPEND_INCLUDED__')) {
        // Ensure prepend.inc is only executed once
        define('__PREPEND_INCLUDED__', 1);


        ///////////////////////////////////
        // Define Server-specific constants
        ///////////////////////////////////
        /*
        * This assumes that the configuration include file is in the same directory
        * as this prepend include file.  For security reasons, you can feel free
        * to move the configuration file anywhere you want.  But be sure to provide
        * a relative or absolute path to the file.
        */
    require(dirname(__FILE__) . '/configuration.inc.php');


        //////////////////////////////
    // Include the QCubed Framework
        //////////////////////////////
    if (get_magic_quotes_gpc())
            require(__QCUBED_CORE__ . '/framework/DisableMagicQuotes.inc.php');
    require(__QCUBED_CORE__ . '/qcubed.inc.php');


    // Register the autoloader
    //spl_autoload_register(array('QApplication', 'Autoload'));

        //////////////////////////
        // Custom Global Functions
        //////////////////////////
        // TODO: Define any custom global functions (if any) here...


        ////////////////
        // Include Files
        ////////////////
        // TODO: Include any other include files (if any) here...

        require_once(dirname(__FILE__) . '/application.inc.php');

        QDateTime::$DefaultFormat = QDateTime::FormatIso;

        function t($strText, $strPlural = null, $intCnt = null) {
            return QApplication::Translate($strText, $strPlural, $intCnt);
        }

        if (!file_exists(__DOCROOT__ . __SUBDIRECTORY__ . '/data'))
            die(sprintf('Please create a directory "data" in %s and give it write permissions for everyone (chmod 777)', __DOCROOT__ . __SUBDIRECTORY__));

        foreach (array(__TMP_PATH__, __TMP_PATH__ . '/zend', __DOCROOT__ . __SUBDIRECTORY__ . '/data/dictionaries', __DOCROOT__ . __SUBDIRECTORY__ . '/data/import') as $strDirName) {
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
            print(sprintf('Unable to connect to the dabase. Please check database settings in file "%s"', dirname(__FILE__) . '/configuration.inc.php') . '<br />');
            print(sprintf('Error: "%s"', mysql_error()));
            die();
        }

        if (!mysql_select_db($arrConData['database'], $link)) {
            print(sprintf('Unable to connect to the dabase. Please check database settings in file "%s"', dirname(__FILE__) . '/configuration.inc.php') . '<br />');
            print(sprintf('Error: "%s"', mysql_error()));
            die();
        }

        ///////////////////////
        // Setup Error Handling
        ///////////////////////
        /*
        * Set Error/Exception Handling to the default
     * QCubed HandleError and HandlException functions
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

        NarroForm::$FormStateHandler = 'QFileFormStateHandler';
        define('__FORM_STATE_HANDLER__', 'QFileFormStateHandler');
        define('__FILE_FORM_STATE_HANDLER_PATH__',  __TMP_PATH__ . '/qform_state');

        if (strstr($_SERVER['SCRIPT_NAME'], 'assets/') === false) {

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

            require_once 'Zend/Cache.php';

            $frontendOptions = array(
                'lifetime' => null, // cache forever
                'automatic_serialization' => true
            );

            $backendOptions = array(
                'cache_dir' => __TMP_PATH__ . '/zend'
            );

            QApplication::$Cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
            if (QApplication::QueryString('l'))
                QApplication::$Language = NarroLanguage::LoadByLanguageCode(QApplication::QueryString('l'));
            elseif ($objLanguage = QApplication::GetBrowserLanguage() instanceof NarroLanguage) {
                QApplication::Redirect(sprintf('narro_project_list.php?l=%s', $objLanguage->LanguageCode));
            }

            QApplication::RegisterPreference('Items per page', 'number', t('How many items are displayed per page'), 10);
            QApplication::RegisterPreference('Font size', 'option', t('The application font size'), 'medium', array('x-small', 'small', 'medium', 'large', 'x-large'));
            QApplication::RegisterPreference('Language', 'option', t('The language you are translating to'), QApplication::QueryString('l'), array(QApplication::QueryString('l')));
            QApplication::RegisterPreference('Application language', 'option', t('The language you want to see Narro in'), (isset(QApplication::$Language))?QApplication::$Language->LanguageCode:NarroLanguage::SOURCE_LANGUAGE_CODE, array((isset(QApplication::$Language))?QApplication::$Language->LanguageCode:NarroLanguage::SOURCE_LANGUAGE_CODE));
            QApplication::RegisterPreference('Special characters', 'text', t('Characters that are not on your keyboard, separated by spaces'), '$€');
            QApplication::RegisterPreference('Other languages', 'text', t('Other languages that you want to check for suggestions, separated by spaces'), 'ro');
            QApplication::RegisterPreference('Force ascii letters as access keys', 'option', t('Access keys are the letters that are underlined in menus and on buttons that you can use to quickly get to that button or menu item'), 'No', array('Yes', 'No'));
            QApplication::RegisterPreference('Use AJAX', 'option', t('AJAX (transfers in background) will make Narro very fast. If you have problems because of this, choose No'), 'Yes', array('Yes', 'No'));

            if (isset($objNarroSession->User) && $objNarroSession->User instanceof NarroUser) {
                QApplication::$User = $objNarroSession->User;
                QApplication::$UseAjax = (QApplication::$User->getPreferenceValueByName('Use AJAX') == 'Yes');
            }
            else
                QApplication::$User = NarroUser::LoadAnonymousUser();

            if (!QApplication::$User instanceof NarroUser)
                // @todo add handling here
                throw new Exception('Could not create an instance of NarroUser');

            $objNarroSession->User = QApplication::$User;

            if (QApplication::$User->UserId != NarroUser::ANONYMOUS_USER_ID && !QApplication::$Cache->getIdsMatchingTags(array('NarroUser' . QApplication::$User->UserId))) {
                QApplication::$User = NarroUser::LoadByUserId(QApplication::$User->UserId);
                if (!QApplication::$User instanceof NarroUser)
                    QApplication::$User = NarroUser::LoadAnonymousUser();

                QApplication::$Cache->save(QApplication::$User, 'NarroUser' . QApplication::$User->UserId, array('NarroUser' . QApplication::$User->UserId));
            }

            if (!isset(QApplication::$Language))
                QApplication::$Language = QApplication::$User->Language;

            QApplication::$LanguageCode = QApplication::$Language->LanguageCode;

            require_once('Zend/Log.php');
            require_once('Zend/Log/Writer/Stream.php');
            require_once('Zend/Log/Writer/Firebug.php');
            if (is_numeric(QApplication::QueryString('p')))
                QApplication::$LogFile = sprintf('%s/project-%d-%s.log', __TMP_PATH__, QApplication::QueryString('p'), QApplication::GetLanguageId());
            elseif (isset($argv) && $intProjectId = $argv[array_search('--project', $argv)+1])
                QApplication::$LogFile = sprintf('%s/project-%d-%s.log', __TMP_PATH__, $argv[array_search('--project', $argv)+1], QApplication::GetLanguageId());
            else
                QApplication::$LogFile = sprintf('%s/app-%s.log', __TMP_PATH__, QApplication::GetLanguageId());

            QApplication::$Logger = new Zend_Log();
            QApplication::$Logger->addWriter(new Zend_Log_Writer_Stream(QApplication::$LogFile));
            if (SERVER_INSTANCE == 'dev' && QFirebug::getEnabled())
                QApplication::$Logger->addWriter(new Zend_Log_Writer_QFirebug());

            QApplication::$EncodingType = 'UTF-8';
            QApplication::$Database[1]->NonQuery("SET NAMES 'utf8'");

            require_once 'Zend/Translate.php';

            define('__LOCALE_DIRECTORY__', __DOCROOT__ . __SUBDIRECTORY__ . '/locale/' . QApplication::$User->getPreferenceValueByName('Application language') . '/LC_MESSAGES');
            if (!is_writable(__DOCROOT__ . __SUBDIRECTORY__ . '/locale/'))
                die(sprintf('Please give write permissions for everyone (chmod 777) to the directory "%s"', __DOCROOT__ . __SUBDIRECTORY__ . '/locale/'));

            if (!file_exists(__LOCALE_DIRECTORY__)) {
                if (!mkdir(__LOCALE_DIRECTORY__, 0777, true))
                    die(sprintf('Could not create a directory. Please create the directory "%s" and give it write permissions for everyone (chmod 777)', __LOCALE_DIRECTORY__));
                else
                    NarroUtils::RecursiveChmod(__LOCALE_DIRECTORY__);
            }

            if (file_exists(__LOCALE_DIRECTORY__ . '/narro.mo')) {
                require_once('Zend/Translate.php');
                require_once('Zend/Translate/Adapter/Gettext.php');
                QApplication::$TranslationEngine = new Zend_Translate('gettext', __LOCALE_DIRECTORY__ . '/narro.mo', QApplication::$User->getPreferenceValueByName('Application language'));
            }

            if (!extension_loaded('mbstring'))
                die('This version of Narro needs php-mbstring, please install it');

            if (!function_exists('mb_stripos'))
                die('This version of Narro needs mb_stripos, that\'s available only in php versions bigger than 5.2.0');

            if (!extension_loaded('gd'))
                die('This version of Narro needs php-gd, please install it');

            QApplication::$PluginHandler = new NarroPluginHandler(dirname(__FILE__) . '/../narro/plugins');
        }

        ///////////////////////
        // Setup Error Handling
        ///////////////////////
        /*
        * Set Error/Exception Handling to the default
     * QCubed HandleError and HandlException functions
        * (Only in non CLI mode)
        *
        * Feel free to change, if needed, to your own
        * custom error handling script(s).
        */
        if (array_key_exists('SERVER_PROTOCOL', $_SERVER)) {
            set_error_handler('QcodoHandleError');
            set_exception_handler('QcodoHandleException');
        }
    }
?>
