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

        public function __construct($objProject, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->objProject = $objProject;

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

        public function __construct($objProject, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->objProject = $objProject;

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

        public function __construct($objProject, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->objProject = $objProject;

            $this->fileSource = new QFileAsset($this);
            $this->fileSource->DisplayStyle = QDisplayStyle::Block;
            $this->fileSource->TemporaryUploadPath = __TMP_PATH__;

        }

        public function GetControlHtml() {
            $this->strText = t('Please upload an archive that contains the files') .
                $this->fileSource->Render(false);
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

            $this->strWorkingDirectory = __TMP_PATH__ . '/upload-source-' . uniqid();

            $this->CleanWorkingDirectory();

            mkdir($this->strWorkingDirectory);
            chmod($this->strWorkingDirectory, 0777);

            NarroLog::LogMessage(3, sprintf('Trying to uncompress %s', $this->fileSource->File));
            $objZipFile = new ZipArchive();
            $intErrCode = $objZipFile->open($this->fileSource->File);
            if ($intErrCode === TRUE) {
                $objZipFile->extractTo($this->strWorkingDirectory);
                $objZipFile->close();
                NarroLog::LogMessage(3, sprintf('Sucessfully uncompressed %s.', $this->fileSource->File));
            } else {
                switch($intErrCode) {
                    case ZIPARCHIVE::ER_NOZIP:
                        $strError = 'Not a zip archive';
                        break;
                    default:
                        $strError = 'Error code: '. $intErrCode;
                }
                unlink($this->fileSource->File);
                $this->fileSource->File = '';

                throw new Exception(sprintf('Failed to uncompress %s: %s', $this->fileSource->File, $strError));
            }

            unlink($this->fileSource->File);
            NarroUtils::RecursiveChmod($this->strWorkingDirectory);

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

        public function __construct($objProject, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->objProject = $objProject;

            $this->pnlTextSource = new QTabPanel($this);
            $this->pnlTextSource->UseAjax = QApplication::$UseAjax;
            $objDirectoryPanel = new NarroDirectorySourcePanel($objProject, $this->pnlTextSource);
            $objDirectoryPanel->Directory = __IMPORT_PATH__ . '/' . $this->objProject->ProjectId . '/' . NarroLanguage::SOURCE_LANGUAGE_CODE;
            $this->pnlTextSource->addTab($objDirectoryPanel, t('On this server'));
            $this->pnlTextSource->addTab(new NarroUploadSourcePanel($objProject, $this->pnlTextSource), t('On my computer'));
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

        public function __construct($objProject, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->objProject = $objProject;

            $this->pnlTranslationSource = new QTabPanel($this);
            $this->pnlTranslationSource->UseAjax = QApplication::$UseAjax;
            $objDirectoryPanel = new NarroDirectorySourcePanel($objProject, $this->pnlTranslationSource);
            $objDirectoryPanel->Directory = __IMPORT_PATH__ . '/' . $this->objProject->ProjectId . '/' . QApplication::$Language->LanguageCode;
            $this->pnlTranslationSource->addTab($objDirectoryPanel, t('On this server'));
            $this->pnlTranslationSource->addTab(new NarroUploadSourcePanel($objProject, $this->pnlTranslationSource), t('On my computer'));
            $this->pnlTranslationSource->addTab(new NarroProjectSourcePanel($objProject, $this->pnlTranslationSource), t('In another project'));
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
