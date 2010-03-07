<?php
    /**
     * Narro is an application that allows online software translation and maintenance.
     * Copyright (C) 2008-2010 Alexandru Szasz <alexxed@gmail.com>
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
    class NarroProjectSourcePanel extends QPanel {
        protected $lstProject;
        protected $objProject;
        protected $objLanguage;

        public function __construct(NarroProject $objProject, NarroLanguage $objLanguage, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->objProject = $objProject;
            $this->objLanguage = $objLanguage;

            $this->lstProject = new QListBox($this);
            $this->lstProject->DisplayStyle = QDisplayStyle::Block;
            foreach(
                NarroProject::QueryArray(
                        QQ::Equal(QQN::NarroProject()->Active, 1),
                        array(QQ::OrderBy(QQN::NarroProject()->ProjectName))
                ) as $objProject
            )
            {
                $this->lstProject->AddItem($objProject->ProjectName, $objProject->ProjectId);
            }
        }

        public function GetControlHtml() {
            $this->strText = t('Please choose the project from which you will import matching approved translations') .
                $this->lstProject->Render(false);
            return parent::GetControlHtml();
        }

        public function __get($strName) {
            switch ($strName) {
                case "Directory":
                    return sprintf('%s/%s/%s', __IMPORT_PATH__, $this->lstProject->SelectedValue, QApplication::$LanguageCode);

                default:
                    try {
                        return parent::__get($strName);
                        break;
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }
    }

    class NarroDirectorySourcePanel extends QPanel {
        protected $txtDirectory;
        protected $objProject;
        protected $objLanguage;

        public function __construct(NarroProject $objProject, NarroLanguage $objLanguage, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->objProject = $objProject;
            $this->objLanguage = $objLanguage;

            $this->txtDirectory = new QTextBox($this);
            $this->txtDirectory->DisplayStyle = QDisplayStyle::Block;
            $this->txtDirectory->Width = 500;
        }

        public function GetControlHtml() {
            $this->strText = t('Please enter the full path to the directory that contains the files') .
                $this->txtDirectory->Render(false);
            return parent::GetControlHtml();
        }

        public function __get($strName) {
            switch ($strName) {
                case "Directory":
                    return $this->txtDirectory->Text;

                default:
                    try {
                        return parent::__get($strName);
                        break;
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }

        public function __set($strName, $mixValue) {
            $this->blnModified = true;

            switch ($strName) {
                case "Directory":
                    try {
                        $this->txtDirectory->Text = QType::Cast($mixValue, QType::String);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                default:
                    try {
                        parent::__set($strName, $mixValue);
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
            }
        }

    }

    class NarroUploadSourcePanel extends QPanel {
        protected $fileSource;
        protected $objProject;
        protected $strWorkingDirectory;
        protected $objLanguage;
        protected $chkCopyFilesToDefaultDirectory;

        public function __construct(NarroProject $objProject, NarroLanguage $objLanguage, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->objProject = $objProject;
            $this->objLanguage = $objLanguage;

            $this->fileSource = new QFileAsset($this);
            $this->fileSource->DisplayStyle = QDisplayStyle::Block;
            $this->fileSource->TemporaryUploadPath = __TMP_PATH__;

            $this->chkCopyFilesToDefaultDirectory = new QCheckBox($this);
            $this->chkCopyFilesToDefaultDirectory->Name = t('Copy files to the default project directory for later use');
            $this->chkCopyFilesToDefaultDirectory->Instructions = sprintf(t('This will also delete the files from "%s/"'), __IMPORT_PATH__ . '/' . $this->objProject->ProjectId . '/' . $this->objLanguage->LanguageCode);
            $this->chkCopyFilesToDefaultDirectory->Checked = true;

        }

        public function GetControlHtml() {
            $this->strText = t('Please upload an archive that contains the files') .
                $this->fileSource->Render(false) .
                $this->chkCopyFilesToDefaultDirectory->Render(false) . sprintf('<label for="%s">%s</label><br /><i style="color:gray;font-size:80%%">%s</i>', $this->chkCopyFilesToDefaultDirectory->ControlId, $this->chkCopyFilesToDefaultDirectory->Name, $this->chkCopyFilesToDefaultDirectory->Instructions);
            return parent::GetControlHtml();
        }

        protected function CleanWorkingDirectory() {
            if (file_exists($this->strWorkingDirectory)) {
                NarroUtils::RecursiveDelete($this->strWorkingDirectory);
            }
        }

        protected function GetWorkingDirectory() {
            if (!file_exists($this->fileSource->File))
                throw new Exception('You have to upload a file');

            $this->strWorkingDirectory = sprintf('%s/upload-u_%d-l_%s-p_%d', __TMP_PATH__, QApplication::GetUserId(), $this->objLanguage->LanguageCode, $this->objProject->ProjectId);

            $this->CleanWorkingDirectory();

            mkdir($this->strWorkingDirectory);
            chmod($this->strWorkingDirectory, 0777);

            QApplication::$Logger->info(sprintf('Trying to uncompress %s', $this->fileSource->File));
            $objZipFile = new ZipArchive();
            $intErrCode = $objZipFile->open($this->fileSource->File);
            if ($intErrCode === TRUE) {
                $objZipFile->extractTo($this->strWorkingDirectory);
                $objZipFile->close();
                QApplication::$Logger->info(sprintf('Sucessfully uncompressed %s.', $this->fileSource->File));
            } else {
                switch($intErrCode) {
                    case ZIPARCHIVE::ER_NOZIP:
                        $strError = 'Not a zip archive';
                        break;
                    default:
                        $strError = 'Error code: '. $intErrCode;
                }
                $this->fileSource->File = '';

                throw new Exception(sprintf('Failed to uncompress %s: %s', $this->fileSource->File, $strError));
            }

            if (file_exists($this->fileSource->File))
                unlink($this->fileSource->File);


            $arrSearchResult = NarroUtils::SearchDirectoryByName($this->strWorkingDirectory, $this->objLanguage->LanguageCode);

            if ($arrSearchResult == false)
                $arrSearchResult = NarroUtils::SearchDirectoryByName($this->strWorkingDirectory, $this->objLanguage->LanguageCode . '-' . $this->objLanguage->CountryCode);

            if ($arrSearchResult == false)
                $arrSearchResult = NarroUtils::SearchDirectoryByName($this->strWorkingDirectory, $this->objLanguage->LanguageCode . '_' . $this->objLanguage->CountryCode);

            NarroUtils::RecursiveChmod($this->strWorkingDirectory);

            if (is_array($arrSearchResult) && count($arrSearchResult) == 1) {
                QApplication::$Logger->warn(sprintf('Template path changed from "%s" to "%s" because a directory named "%s" was found deeper in the given path.', $this->strWorkingDirectory, $arrSearchResult[0], $this->objLanguage->LanguageCode));
                $this->strWorkingDirectory = $arrSearchResult[0];
            }

            if ($this->chkCopyFilesToDefaultDirectory->Checked) {
                NarroUtils::RecursiveDelete(__IMPORT_PATH__ . '/' . $this->objProject->ProjectId . '/' . $this->objLanguage->LanguageCode .'/*');
                NarroUtils::RecursiveCopy($this->strWorkingDirectory, __IMPORT_PATH__ . '/' . $this->objProject->ProjectId . '/' . $this->objLanguage->LanguageCode);
                NarroUtils::RecursiveChmod(__IMPORT_PATH__ . '/' . $this->objProject->ProjectId . '/' . $this->objLanguage->LanguageCode);
            }

            return $this->strWorkingDirectory;
        }

        public function __get($strName) {
            switch ($strName) {
                case "Directory":
                    return $this->GetWorkingDirectory();

                default:
                    try {
                        return parent::__get($strName);
                        break;
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }

        public function __set($strName, $mixValue) {
            $this->blnModified = true;

            switch ($strName) {
                case "Directory":
                    try {
                        $this->strWorkingDirectory = QType::Cast($mixValue, QType::String);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                default:
                    try {
                        parent::__set($strName, $mixValue);
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
            }
        }
    }

    class NarroProjectTextSourcePanel extends QPanel {

        public $pnlTextSource;
        protected $objProject;
        protected $objLanguage;

        public function __construct(NarroProject $objProject, NarroLanguage $objLanguage, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->objProject = $objProject;
            $this->objLanguage = $objLanguage;

            $this->pnlTextSource = new QTabPanel($this);
            $this->pnlTextSource->UseAjax = QApplication::$UseAjax;
            $objDirectoryPanel = new NarroDirectorySourcePanel($objProject, $objLanguage, $this->pnlTextSource);
            $objDirectoryPanel->Directory = $this->objProject->DefaultTemplatePath;
            $this->pnlTextSource->addTab($objDirectoryPanel, t('On this server'));
            $this->pnlTextSource->addTab(new NarroUploadSourcePanel($objProject, $objLanguage, $this->pnlTextSource), t('On my computer'));
        }

        public function GetControlHtml() {
            $this->strText = $this->pnlTextSource->Render(false);
            return parent::GetControlHtml();
        }

        public function __get($strName) {
            switch ($strName) {
                case "Directory":
                    return $this->pnlTextSource->SelectedTab->Directory;

                default:
                    try {
                        return parent::__get($strName);
                        break;
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }

    }

    class NarroProjectTranslationSourcePanel extends QPanel {

        protected $pnlTranslationSource;
        protected $objProject;
        protected $objLanguage;

        public function __construct(NarroProject $objProject, NarroLanguage $objLanguage, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->objProject = $objProject;
            $this->objLanguage = $objLanguage;

            $this->pnlTranslationSource = new QTabPanel($this);
            $this->pnlTranslationSource->UseAjax = QApplication::$UseAjax;
            $objDirectoryPanel = new NarroDirectorySourcePanel($objProject, $objLanguage, $this->pnlTranslationSource);
            $objDirectoryPanel->Directory = $this->objProject->DefaultTranslationPath;
            $this->pnlTranslationSource->addTab($objDirectoryPanel, t('On this server'));
            $this->pnlTranslationSource->addTab(new NarroUploadSourcePanel($objProject, $objLanguage, $this->pnlTranslationSource), t('On my computer'));
            $this->pnlTranslationSource->addTab(new NarroProjectSourcePanel($objProject, $objLanguage, $this->pnlTranslationSource), t('In another project'));
        }

        public function GetControlHtml() {
            $this->strText = $this->pnlTranslationSource->Render(false);
            return parent::GetControlHtml();
        }

        public function __get($strName) {
            switch ($strName) {
                case "Directory":
                    return $this->pnlTranslationSource->SelectedTab->Directory;

                default:
                    try {
                        return parent::__get($strName);
                        break;
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }
    }
?>
