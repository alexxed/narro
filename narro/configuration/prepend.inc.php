<?php
if (!defined('__PREPEND_INCLUDED__')) {
    define('__PREPEND_INCLUDED__', 1);
    require(dirname(__FILE__) . '/configuration.qcubed.inc.php');
    if (get_magic_quotes_gpc())
        require(__QCUBED_CORE__ . '/framework/DisableMagicQuotes.inc.php');
    require(__QCUBED_CORE__ . '/qcubed.inc.php');
    require_once(dirname(__FILE__) . '/QApplication.class.php');

    QDateTime::$DefaultFormat = QDateTime::FormatIso;

    function t($strText, $strPlural = null, $intCnt = null) {
        return QApplication::Translate($strText, $strPlural, $intCnt);
    }

    ///////////////////////
    // Setup Error Handling
    ///////////////////////
    if (array_key_exists('SERVER_PROTOCOL', $_SERVER)) {
        set_error_handler('QcodoHandleError', error_reporting());
        set_exception_handler('QcodoHandleException');
    }

    spl_autoload_register(array('QApplication', 'Autoload'));

    QApplication::Initialize();
    QApplication::InitializeDatabaseConnections();
    QApplication::InitializeSession();
    QApplication::InitializeCache();
    QApplication::InitializeLanguage();
    QApplication::InitializeUser();

    NarroUser::RegisterPreference('Items per page', 'number', t('How many items are displayed per page'), 10);
    NarroUser::RegisterPreference('Font size', 'option', t('The application font size'), 'medium', array('x-small', 'small', 'medium', 'large', 'x-large'));
    NarroUser::RegisterPreference('Language', 'option', t('The language you are translating to'), QApplication::QueryString('l'), array(QApplication::QueryString('l')));
    NarroUser::RegisterPreference('Application language', 'option', t('The language you want to see Narro in'), (isset(QApplication::$TargetLanguage))?QApplication::$TargetLanguage->LanguageCode:NarroLanguage::SOURCE_LANGUAGE_CODE, array((isset(QApplication::$TargetLanguage))?QApplication::$TargetLanguage->LanguageCode:NarroLanguage::SOURCE_LANGUAGE_CODE));
    NarroUser::RegisterPreference('Special characters', 'text', t('Characters that are not on your keyboard, separated by spaces'), '$€');
    NarroUser::RegisterPreference('Other languages', 'text', t('Other languages that you want to check for suggestions, separated by spaces'), 'ro');
    NarroUser::RegisterPreference('Force ascii letters as access keys', 'option', t('Access keys are the letters that are underlined in menus and on buttons that you can use to quickly get to that button or menu item'), 'No', array('Yes', 'No'));
    // @todo needs testing
    // NarroUser::RegisterPreference('Use AJAX', 'option', t('AJAX (transfers in background) will make Narro very fast. If you have problems because of this, choose No'), 'Yes', array('Yes', 'No'));
    NarroUser::RegisterPreference('Launch imports and exports in background', 'option', t('Launch imports and exports in background'), 'Yes', array('Yes', 'No'));

    QApplication::InitializeLogging();
    QApplication::InitializeTranslationEngine();

    QApplication::$EncodingType = 'UTF-8';
    QApplication::$Database[1]->NonQuery("SET NAMES 'utf8'");

    QApplication::$PluginHandler = new NarroPluginHandler(dirname(__FILE__) . '/../includes/narro/plugins');
}
?>
