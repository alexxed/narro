<?php

    class NarroUserLoginPanel extends QPanel {
        public $lblMessage;
        public $txtUsername;
        public $txtPassword;
        public $btnLogin;
        public $txtPreviousUrl;

        public function __construct($objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroUserLoginPanel.tpl.php';

            $this->lblMessage = new QLabel($this);
            $this->lblMessage->HtmlEntities = false;
            $this->txtUsername = new QTextBox($this, 'username');
            $this->txtUsername->TabIndex = 1;
            $this->txtPassword = new QTextBox($this);
            $this->txtPassword->TabIndex = 2;
            $this->txtPassword->TextMode = QTextMode::Password;
            $this->btnLogin = new QButton($this);
            $this->btnLogin->Text = t('Login');
            $this->btnLogin->PrimaryButton = true;
            $this->btnLogin->TabIndex = 3;
            $this->btnLogin->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnLogin_Click'));

            if (isset($_SERVER['HTTP_REFERER']) && !strstr($_SERVER['HTTP_REFERER'], basename(__FILE__)) && $_SERVER['HTTP_REFERER'] !='')
                $this->txtPreviousUrl = $_SERVER['HTTP_REFERER'];

            if (isset($_REQUEST['openid_mode'])) {
                require_once "Zend/Auth.php";
                require_once "Zend/Auth/Adapter/OpenId.php";
                require_once "Zend/Auth/Storage/NonPersistent.php";

                $auth = Zend_Auth::getInstance();
                $auth->authenticate(new Zend_Auth_Adapter_OpenId($this->txtUsername->Text));

                if ($auth->hasIdentity()) {
                    $objUser = NarroUser::LoadByUsername($auth->getIdentity());
                    require_once 'Zend/Session/Namespace.php';
                    $objNarroSession = new Zend_Session_Namespace('Narro');

                    if (!$objUser instanceof NarroUser) {
                        try {
                            $objUser = NarroUser::RegisterUser($auth->getIdentity(), $auth->getIdentity(), '');
                        }
                        catch (Exception $objEx) {
                            $this->lblMessage->ForeColor = 'red';
                            $this->lblMessage->Text = t('Failed to create an associated user for this OpenId') . $objEx->getMessage() . var_export($auth->getIdentity(), true);
                            return false;
                        }

                        $objNarroSession->User = $objUser;
                        QApplication::Redirect(NarroLink::UserPreferences($objUser->UserId));
                    }
                    elseif ($objUser->Password != md5('')) {
                        $this->lblMessage->ForeColor = 'red';
                        $this->lblMessage->Text = t('This user has a password set, please login with that instead');
                        return false;
                    }

                    $objNarroSession->User = $objUser;

                    QApplication::$User = $objUser;
                    if ($this->txtPreviousUrl)
                        QApplication::Redirect($this->txtPreviousUrl);
                    else
                        QApplication::Redirect(NarroLink::ProjectList());
                }
            }
        }

        public function btnLogin_Click($strFormId, $strControlId, $strParameter) {
            if (trim($this->txtPassword->Text) == '') {
                require_once "Zend/Auth.php";
                require_once "Zend/Auth/Adapter/OpenId.php";
                require_once "Zend/Auth/Storage/NonPersistent.php";

                $this->txtUsername->Text = preg_replace('/\/$/', '', $this->txtUsername->Text);

                $status = "";
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate(new Zend_Auth_Adapter_OpenId($this->txtUsername->Text));
                if ($result->isValid()) {
                    Zend_OpenId::redirect(Zend_OpenId::selfURL());
                } else {
                    $auth->clearIdentity();
                    foreach ($result->getMessages() as $message) {
                        $status .= "$message<br>\n";
                    }
                    $this->lblMessage->ForeColor = 'red';
                    $this->lblMessage->Text = 'OpenId: ' . $status;
                    return false;
                }
            }
            else {
                $objUser = NarroUser::LoadByUsernameAndPassword($this->txtUsername->Text, md5($this->txtPassword->Text));

                /**
                 * If the stored password is empty, try to authenticate against an external database
                 */
                if (defined('__AUTH_EXTERNAL_DB_HOST__') &&
                    !$objUser instanceof NarroUser &&
                    NarroUser::QueryCount(
                        QQ::AndCondition(QQ::Equal(QQN::NarroUser()->Username, $this->txtUsername->Text), QQ::NotEqual(QQN::NarroUser()->Password, md5('')))
                    ) == 0
                    ) {
                    require_once "Zend/Auth.php";
                    require_once "Zend/Auth/Adapter/DbTable.php";
                    require_once "Zend/Db/Adapter/Pdo/Mysql.php";
                    require_once "Zend/Db/Adapter/Pdo/Pgsql.php";
                    require_once "Zend/Db/Adapter/Pdo/Mssql.php";
                    require_once "Zend/Db/Adapter/Pdo/Sqlite.php";

                    $external_db = Zend_Db::factory('Pdo_Mysql', array(
                        'host'     => __AUTH_EXTERNAL_DB_HOST__,
                        'username' => __AUTH_EXTERNAL_DB_USERNAME__,
                        'password' => __AUTH_EXTERNAL_DB_PASSWORD__,
                        'dbname'   => __AUTH_EXTERNAL_DB_NAME__
                    ));

                    try {
                        $external_db->getServerVersion();
                    }
                    catch (Exception $objEx) {
                        $this->lblMessage->ForeColor = 'red';
                        $this->lblMessage->Text = sprintf(t('There was an error while trying to authenticate you against an external database: %s'), $objEx->getMessage());
                        return false;
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
                            __AUTH_EXTERNAL_DB_TABLE__,
                            __AUTH_EXTERNAL_DB_TABLE_USER_FIELD__,
                            __AUTH_EXTERNAL_DB_TABLE_PASSWORD_FIELD__,
                            __AUTH_EXTERNAL_DB_TABLE_PASSWORD_FUNCTION__
                        );

                        /**
                         * set here the username and password provided by the user on the external login page
                         */
                        $auth_adapter->setIdentity($this->txtUsername->Text);
                        $auth_adapter->setCredential($this->txtPassword->Text);

                        $auth = Zend_Auth::getInstance();
                        $result = $auth->authenticate($auth_adapter);
                    }
                    catch (Exception $objEx) {
                        $this->lblMessage->ForeColor = 'red';
                        $this->lblMessage->Text = sprintf(t('There was an error while trying to authenticate you against an external database: %s'), $objEx->getMessage());
                        return false;
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
//                                /**
//                                 * If you want to assing an extra role, here's a bit of code to do so
//                                 */
//                                $objUserRole = new NarroUserRole();
//
//                                $objUserRole->RoleId = 1;
//                                $objUserRole->UserId = $objUser->UserId;
//
//                                /**
//                                 * LanguageId and ProjectId are optional, if not set, the role is valid for any project or language
//                                 */
//                                $objUserRole->ProjectId = 1;
//                                $objUserRole->LanguageId = 1;
//
//                                $objUserRole->Save();
                            }
                            catch( Exception $objEx ) {
                                $this->lblMessage->ForeColor = 'red';
                                $this->lblMessage->Text = sprintf(t('The authentication against an external database suceeded, but the registration in Narro failed: %s'), $objEx->getMessage());
                                return false;
                            }
                        }
                    }
                }
            }

            if ($objUser instanceof NarroUser) {
                require_once 'Zend/Session/Namespace.php';
                $objNarroSession = new Zend_Session_Namespace('Narro');
                $objNarroSession->User = $objUser;

                QApplication::$User = $objUser;
                if ($this->txtPreviousUrl)
                    QApplication::Redirect($this->txtPreviousUrl);
                else
                    QApplication::Redirect(NarroLink::ProjectList());
            }
            else {
                $this->lblMessage->ForeColor = 'red';
                $this->lblMessage->Text = t('Bad username or password');
                return false;
            }
        }

    }
?>
