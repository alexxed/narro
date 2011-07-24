<?php
    /**
     * requirements:
     * ZendFramework ( http://framework.zend.com ) installed and in include_path
     */
    require_once "../configuration/prepend.inc.php";
    require_once "Zend/Auth.php";
    require_once "Zend/Auth/Adapter/DbTable.php";
    require_once "Zend/Db/Adapter/Pdo/Mysql.php";
//    require_once "Zend/Db/Adapter/Pdo/Pgsql.php";
//    require_once "Zend/Db/Adapter/Pdo/Mssql.php";
//    require_once "Zend/Db/Adapter/Pdo/Sqlite.php";

    $external_db = Zend_Db::factory('Pdo_Mysql', array(
        'host'     => 'external_host',
        'username' => 'external_db_username',
        'password' => 'external_db_password',
        'dbname'   => 'external_db_database_name'
    ));

    try {
        $external_db->getServerVersion();
    }
    catch (Exception $objEx) {
        die('Error while trying a simple connection to the external db server: ' . $objEx->getMessage());
    }

    /**
     * your_user is the table name from the external database
     * username is a unique identifier
     * password is something that will be checked with MD5 in this example
     *   feel free to change MD5 with PASSWORD or even add extra conditions
     *   example: MD5(?) AND active <> 0
     */
    try {

        $auth_adapter = new Zend_Auth_Adapter_DbTable(
            $external_db,
            'external_user_table',
            'external_username_field',
            'external_password_field',
            'MD5(?)'
        );

        /**
         * set here the username and password provided by the user on the external login page
         */
        $auth_adapter->setIdentity($_REQUEST['username']);
        $auth_adapter->setCredential($_REQUEST['password']);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($auth_adapter);
    }
    catch (Exception $objEx) {
        die('An exception occured: ' . $objEx->getMessage());
    }

    if ($result->isValid()) {
        /**
         * Try loading the user from Narro
         */
        $objUser = NarroUser::LoadByUsername($auth->getIdentity());

        /**
         * Register the user in Narro if not registered yet
         */
        if (!$objUser instanceof NarroUser) {
            try {
                $objUser = NarroUser::RegisterUser($auth->getIdentity(), $auth->getIdentity(), '');
/**
 * If you want to assing an extra role, here's a bit of code to do so
 */
//                $objUserRole = new NarroUserRole();
//
//                $objUserRole->RoleId = 1;
//                $objUserRole->UserId = $objUser->UserId;
//
//                LanguageId and ProjectId are optional, if not set, the role is valid for any project or language
//                $objUserRole->ProjectId = 1;
//                $objUserRole->LanguageId = 1;
//
//                $objUserRole->Save();
            }
            catch( Exception $objEx ) {
                echo "Automatic registration failed: " . $objEx->getMessage();
            }
        }

        /**
         * store the user in Narro's sesssion
         */
        require_once 'Zend/Session/Namespace.php';
        $objNarroSession = new Zend_Session_Namespace('Narro');
        $objNarroSession->User = $objUser;

        echo "Authenticated as " . $auth->getIdentity();

    }
    else {
        echo "Failed authentication";
    }
?>