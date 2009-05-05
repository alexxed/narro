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

    class NarroLanguageEditPanel extends QPanel {
        // General Panel Variables
        protected $objNarroLanguage;
        protected $strTitleVerb;
        protected $blnEditMode;

        // Controls for NarroLanguage's Data Fields
        public $txtLanguageName;
        public $txtLanguageCode;
        public $txtCountryCode;
        public $txtDialectCode;
        public $txtEncoding;
        public $lstTextDirection;
        public $txtSpecialCharacters;
        public $txtPluralForm;
        public $chkActive;

        // Other ListBoxes (if applicable) via Unique ReverseReferences and ManyToMany References

        // Button Actions
        public $btnSave;
        public $btnCancel;
        public $btnDelete;

        protected function SetupNarroLanguage($objNarroLanguage) {
            if ($objNarroLanguage) {
                $this->objNarroLanguage = $objNarroLanguage;
                $this->strTitleVerb = NarroApp::Translate('Edit');
                $this->blnEditMode = true;
            } else {
                $this->objNarroLanguage = new NarroLanguage();
                $this->strTitleVerb = NarroApp::Translate('Create');
                $this->blnEditMode = false;
            }
        }

        public function __construct($objParentObject, $objNarroLanguage = null, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->strTemplate = __DOCROOT__ . __SUBDIRECTORY__ . '/templates/NarroLanguageEditPanel.tpl.php';

            // Call SetupNarroLanguage to either Load/Edit Existing or Create New
            $this->SetupNarroLanguage($objNarroLanguage);

            // Create/Setup Controls for NarroLanguage's Data Fields
            $this->txtLanguageName_Create();
            $this->txtLanguageCode_Create();
            $this->txtCountryCode_Create();
            $this->txtDialectCode_Create();
            $this->txtEncoding_Create();
            $this->lstTextDirection_Create();
            $this->txtSpecialCharacters_Create();
            $this->txtPluralForm_Create();
            $this->chkActive_Create();

            // Create/Setup ListBoxes (if applicable) via Unique ReverseReferences and ManyToMany References

            // Create/Setup Button Action controls
            $this->btnSave_Create();
            $this->btnCancel_Create();
            $this->btnDelete_Create();
        }

        // Protected Create Methods
        // Create and Setup txtLanguageName
        protected function txtLanguageName_Create() {
            $this->txtLanguageName = new QTextBox($this);
            $this->txtLanguageName->Name = NarroApp::Translate('Language Name');
            $this->txtLanguageName->Text = $this->objNarroLanguage->LanguageName;
            $this->txtLanguageName->Required = true;
            $this->txtLanguageName->MaxLength = NarroLanguage::LanguageNameMaxLength;
        }

        // Create and Setup txtLanguageCode
        protected function txtLanguageCode_Create() {
            $this->txtLanguageCode = new QTextBox($this);
            $this->txtLanguageCode->Name = NarroApp::Translate('Language Code');
            $this->txtLanguageCode->Text = $this->objNarroLanguage->LanguageCode;
            $this->txtLanguageCode->Required = true;
            $this->txtLanguageCode->MaxLength = NarroLanguage::LanguageCodeMaxLength;
        }

        // Create and Setup txtCountryCode
        protected function txtCountryCode_Create() {
            $this->txtCountryCode = new QTextBox($this);
            $this->txtCountryCode->Name = NarroApp::Translate('Country Code');
            $this->txtCountryCode->Text = $this->objNarroLanguage->CountryCode;
            $this->txtCountryCode->Required = true;
            $this->txtCountryCode->MaxLength = NarroLanguage::CountryCodeMaxLength;
        }

        // Create and Setup txtDialectCode
        protected function txtDialectCode_Create() {
            $this->txtDialectCode = new QTextBox($this);
            $this->txtDialectCode->Name = NarroApp::Translate('Dialect Code');
            $this->txtDialectCode->Text = $this->objNarroLanguage->DialectCode;
            $this->txtDialectCode->MaxLength = NarroLanguage::DialectCodeMaxLength;
        }

        // Create and Setup txtEncoding
        protected function txtEncoding_Create() {
            $this->txtEncoding = new QTextBox($this);
            $this->txtEncoding->Name = NarroApp::Translate('Encoding');
            $this->txtEncoding->Text = $this->objNarroLanguage->Encoding;
            $this->txtEncoding->Required = true;
            $this->txtEncoding->MaxLength = NarroLanguage::EncodingMaxLength;
        }

        // Create and Setup lstTextDirection
        protected function lstTextDirection_Create() {
            $this->lstTextDirection = new QListBox($this);
            $this->lstTextDirection->Name = NarroApp::Translate('Text Direction');
            $this->lstTextDirection->Required = true;
            $this->lstTextDirection->AddItem(t('Left to right'), 'ltr');
            $this->lstTextDirection->AddItem(t('Right to left'), 'rtl');
        }


        // Create and Setup txtSpecialCharacters
        protected function txtSpecialCharacters_Create() {
            $this->txtSpecialCharacters = new QTextBox($this);
            $this->txtSpecialCharacters->Name = NarroApp::Translate('Special Characters');
            $this->txtSpecialCharacters->Text = $this->objNarroLanguage->SpecialCharacters;
            $this->txtSpecialCharacters->MaxLength = NarroLanguage::SpecialCharactersMaxLength;
        }

        // Create and Setup txtPluralForm
        protected function txtPluralForm_Create() {
            $this->txtPluralForm = new QTextBox($this);
            $this->txtPluralForm->Name = NarroApp::Translate('Plural Form');
            $this->txtPluralForm->Text = $this->objNarroLanguage->PluralForm;
            $this->txtPluralForm->Required = true;
            $this->txtPluralForm->MaxLength = NarroLanguage::PluralFormMaxLength;
            $this->txtPluralForm->Width = 400;
        }

        // Create and Setup chkActive
        protected function chkActive_Create() {
            $this->chkActive = new QCheckBox($this);
            $this->chkActive->Name = NarroApp::Translate('Active');
            $this->chkActive->Checked = $this->objNarroLanguage->Active;
        }


        // Setup btnSave
        protected function btnSave_Create() {
            $this->btnSave = new QButton($this);
            $this->btnSave->Text = NarroApp::Translate('Save');
            $this->btnSave->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnSave_Click'));
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->CausesValidation = true;
        }

        // Setup btnCancel
        protected function btnCancel_Create() {
            $this->btnCancel = new QButton($this);
            $this->btnCancel->Text = NarroApp::Translate('Cancel');
            $this->btnCancel->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnCancel_Click'));
            $this->btnCancel->CausesValidation = false;
        }

        // Setup btnDelete
        protected function btnDelete_Create() {
            $this->btnDelete = new QButton($this);
            $this->btnDelete->Text = NarroApp::Translate('Delete');
            $this->btnDelete->AddAction(new QClickEvent(), new QConfirmAction(NarroApp::Translate('Are you SURE you want to DELETE this language?')));
            $this->btnDelete->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnDelete_Click'));
            $this->btnDelete->CausesValidation = false;
            if (!$this->blnEditMode)
                $this->btnDelete->Visible = false;
        }

        // Protected Update Methods
        protected function UpdateNarroLanguageFields() {
            $this->objNarroLanguage->LanguageName = $this->txtLanguageName->Text;
            $this->objNarroLanguage->LanguageCode = $this->txtLanguageCode->Text;
            $this->objNarroLanguage->CountryCode = $this->txtCountryCode->Text;
            $this->objNarroLanguage->DialectCode = $this->txtDialectCode->Text;
            $this->objNarroLanguage->Encoding = $this->txtEncoding->Text;
            $this->objNarroLanguage->TextDirection = $this->lstTextDirection->SelectedValue;
            $this->objNarroLanguage->SpecialCharacters = $this->txtSpecialCharacters->Text;
            $this->objNarroLanguage->PluralForm = $this->txtPluralForm->Text;
            $this->objNarroLanguage->Active = $this->chkActive->Checked;
        }


        // Control ServerActions
        public function btnSave_Click($strFormId, $strControlId, $strParameter) {
            if (NarroApp::HasPermissionForThisLang('Can edit language', null)) {
                $this->UpdateNarroLanguageFields();
                $this->objNarroLanguage->Save();
            }
            NarroApp::Redirect(NarroLink::LanguageList());
        }

        public function btnCancel_Click($strFormId, $strControlId, $strParameter) {
            NarroApp::Redirect(NarroLink::LanguageList());
        }

        public function btnDelete_Click($strFormId, $strControlId, $strParameter) {
            if (NarroApp::HasPermissionForThisLang('Can delete language', null))
                $this->objNarroLanguage->Delete();
            NarroApp::Redirect(NarroLink::LanguageList());
        }

    }
?>
