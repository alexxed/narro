<?php
    require_once(__NARRO_INCLUDES__ . '/lightopenid/openid.php');
    class NarroUserLoginPanel extends QPanel {
        public $lblMessage;
        public $txtUsername;
        public $txtPassword;
        public $btnLogin;
        public $txtPreviousUrl;
        public $txtOpenId;
        public $btnGoogleLogin;

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
            
            $this->txtOpenId = new QTextBox($this, 'openid');
            $this->txtOpenId->TabIndex = 1;
            
            $this->txtPassword = new QTextBox($this, 'password');
            $this->txtPassword->TabIndex = 2;
            $this->txtPassword->TextMode = QTextMode::Password;
            
            $this->btnLogin = new QButton($this);
            $this->btnLogin->Text = t('Login');
            $this->btnLogin->PrimaryButton = true;
            $this->btnLogin->TabIndex = 3;
            $this->btnLogin->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnLogin_Click'));
            
            $this->btnGoogleLogin = new QButton($this);
            $this->btnGoogleLogin->Text = t('Login with your Google account');
            $this->btnGoogleLogin->PrimaryButton = false;
            $this->btnGoogleLogin->TabIndex = 4;
            $this->btnGoogleLogin->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnGoogleLogin_Click'));

            if (isset($_SERVER['HTTP_REFERER']) && !strstr($_SERVER['HTTP_REFERER'], basename(__FILE__)) && $_SERVER['HTTP_REFERER'] !='')
                $this->txtPreviousUrl = $_SERVER['HTTP_REFERER'];

            $openid = new LightOpenID($_SERVER['HTTP_HOST']);
            if ($openid->mode) {
                if ($openid->mode == 'cancel') {
                    $this->lblMessage->Text = t('The user has canceled authentication');
                    $this->lblMessage->ForeColor = 'red';
                }
                else {
                    if ($openid->validate()) {
                        $arrAttributes = $openid->getAttributes();
                        
                        $objUser = NarroUser::LoadByUsername($openid->identity);
    
                        if (!$objUser instanceof NarroUser) {
                            try {
                                $objUser = NarroUser::RegisterUser($openid->identity, $openid->identity, '');
                                if (isset($arrAttributes['namePerson']))
                                    $objUser->Username = $arrAttributes['namePerson'];
                                if (isset($arrAttributes['contact/email']))
                                    $objUser->Email = $arrAttributes['contact/email'];
                                $objUser->Save();
                            }
                            catch (Exception $objEx) {
                                $this->lblMessage->ForeColor = 'red';
                                $this->lblMessage->Text = t('Failed to create an associated user for this OpenId') . $objEx->getMessage() . var_export($openid->identity, true);
                                return false;
                            }
    
                            QApplication::$Session->User = $objUser;
                            QApplication::Redirect(NarroLink::UserPreferences($objUser->UserId));
                        }
                        elseif ($objUser->Password != md5('')) {
                            $this->lblMessage->ForeColor = 'red';
                            $this->lblMessage->Text = t('This user has a password set, please login with that instead');
                            return false;
                        }
    
                        QApplication::$Session->User = $objUser;
                        QApplication::$User = $objUser;
                        
                        if ($this->txtPreviousUrl)
                            QApplication::Redirect($this->txtPreviousUrl);
                        else
                            QApplication::Redirect(NarroLink::ProjectList());
                    }
                    else {
                        $this->lblMessage->Text = t('OpenID login failed');
                        $this->lblMessage->ForeColor = 'red';
                    }
                }
            }
        }
        
        public function btnGoogleLogin_Click($strFormId, $strControlId, $strParameter) {
            $openid = new LightOpenID($_SERVER['HTTP_HOST']);
            $openid->identity = 'https://www.google.com/accounts/o8/id';
            header('Location: ' . $openid->authUrl());
            exit;
        }

        public function btnLogin_Click($strFormId, $strControlId, $strParameter) {
            if ($this->txtOpenId->Text != '') {
                try {
                    $openid = new LightOpenID($_SERVER['HTTP_HOST']);
                    $openid->identity = $this->txtOpenId->Text;
                    $openid->required = array('contact/email', 'namePerson');
                    header('Location: ' . $openid->authUrl());
                    exit;
                } catch(Exception $objEx) {
                    $this->lblMessage->Text = sprintf(t('OpenID login failed: %s'), $objEx->getMessage());
                    $this->lblMessage->ForeColor = 'red';
                }
            }
            else {
                $objUser = NarroUser::LoadByUsernameAndPassword($this->txtUsername->Text, md5($this->txtPassword->Text));
            }

            if ($objUser instanceof NarroUser) {
                QApplication::$Session->User = $objUser;
                
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
