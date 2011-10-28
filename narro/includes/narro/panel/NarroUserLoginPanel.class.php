<?php
    require_once(__NARRO_INCLUDES__ . '/lightopenid/openid.php');
    class NarroUserLoginPanel extends QPanel {
        public $lblMessage;
        public $txtUsername;
        public $txtPassword;
        public $btnLogin;
        public $txtPreviousUrl;
        public $txtOpenId;
        public $btnOpenIdLogin;
        public $btnGoogleLogin;
        
        public $objAccordion;

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
            
            $this->objAccordion = new QAccordion($this);
            
            $lblNarroLogin = new QLinkButton($this->objAccordion);
            $lblNarroLogin->Text = t('Login with your Narro account');

            $pnlNarroLogin = new QPanel($this->objAccordion);
            $pnlNarroLogin->AutoRenderChildren = true;
            $pnlNarroLogin->PreferedRenderMethod = 'RenderWithName';
            $pnlNarroLogin->SetCustomStyle('text-align', 'right');
            
            $this->txtUsername = new QTextBox($pnlNarroLogin, 'username');
            $this->txtUsername->TabIndex = 1;
            $this->txtUsername->Name = t('Username');
            $this->txtUsername->PreferedRenderMethod = 'RenderWithName';
            
            $this->txtPassword = new QTextBox($pnlNarroLogin, 'password');
            $this->txtPassword->TabIndex = 2;
            $this->txtPassword->TextMode = QTextMode::Password;
            $this->txtPassword->Name = t('Password');
            $this->txtPassword->PreferedRenderMethod = 'RenderWithName';
            
            $this->btnLogin = new QButton($pnlNarroLogin);
            $this->btnLogin->Text = t('Login');
            $this->btnLogin->PrimaryButton = true;
            $this->btnLogin->TabIndex = 3;
            $this->btnLogin->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnLogin_Click'));
            
            $lblOpenIdLogin = new QLinkButton($this->objAccordion);
            $lblOpenIdLogin->Text = t('Login with your OpenID');
            
            $pnlOpenIdLogin = new QPanel($this->objAccordion);
            $pnlOpenIdLogin->AutoRenderChildren = true;
            
            $this->txtOpenId = new QTextBox($pnlOpenIdLogin, 'openid');
            $this->txtOpenId->Name = t('OpenID URL');
            $this->txtOpenId->Instructions = t('Use your existing OpenID account information to login.');
            $this->txtOpenId->PreferedRenderMethod = 'RenderWithName';
            
            $this->btnOpenIdLogin = new QButton($pnlOpenIdLogin);
            $this->btnOpenIdLogin->Text = t('Login');
            $this->btnOpenIdLogin->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnOpenIdLogin_Click'));
            
            $lblGoogleLogin = new QLinkButton($this->objAccordion);
            $lblGoogleLogin->Text = t('Login with your Google account');
            
            $pnlGoogleLogin = new QPanel($this->objAccordion);
            $pnlGoogleLogin->AutoRenderChildren = true;
                        
            $this->btnGoogleLogin = new QImageButton($pnlGoogleLogin);
            $this->btnGoogleLogin->ImageUrl = 'http://www.google.com/favicon.ico';
            $this->btnGoogleLogin->AlternateText = t('Login with your Google account');
            $this->btnGoogleLogin->ToolTip = t('Login with your Google account');
            $this->btnGoogleLogin->SetCustomStyle('vertical-align', 'middle');
            $this->btnGoogleLogin->SetCustomStyle('padding', '5px');
            $this->btnGoogleLogin->Cursor = QCursor::Pointer;
            $this->btnGoogleLogin->Instructions = t('Just click on the image above to use your Google account');
            $this->btnGoogleLogin->PreferedRenderMethod = 'RenderWithName';
            $this->btnGoogleLogin->PrimaryButton = false;
            $this->btnGoogleLogin->TabIndex = 4;
            $this->btnGoogleLogin->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnGoogleLogin_Click'));

            $openid = new LightOpenID($_SERVER['HTTP_HOST']);
            if (!$openid->mode && isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !='' && !strstr($_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI']))
                $this->txtPreviousUrl = $_SERVER['HTTP_REFERER'];

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
                                $objUser = NarroUser::RegisterUser($openid->identity, $openid->identity, '', $openid->identity);
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
    
                            $objUser->Reload();
                            QApplication::$Session->User = $objUser;
                            QApplication::Redirect(NarroLink::UserPreferences($objUser->UserId));
                            exit;
                        }
                        elseif ($objUser->Password != md5('')) {
                            $this->lblMessage->ForeColor = 'red';
                            $this->lblMessage->Text = t('This user has a password set, please login with that instead');
                            return false;
                        }
    
                        QApplication::$Session->User = $objUser->User;
                        QApplication::$User = $objUser;
                        
                        if ($this->txtPreviousUrl)
                            QApplication::Redirect($this->txtPreviousUrl);
                        else
                            QApplication::Redirect(NarroLink::ProjectList());
                        
                        exit;
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
        
        public function btnOpenIdLogin_Click($strFormId, $strControlId, $strParameter) {
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

        public function btnLogin_Click($strFormId, $strControlId, $strParameter) {
            if ($this->txtOpenId->Text != '') return $this->btnOpenIdLogin($strFormId, $strControlId, $strParameter);
            
            $objUser = NarroUser::LoadByUsernameAndPassword($this->txtUsername->Text, md5($this->txtPassword->Text));

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
