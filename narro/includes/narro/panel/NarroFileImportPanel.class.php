<?php
    /**
     * Narro is an application that allows online software translation and maintenance.
     * Copyright (C) 2008-2011 Alexandru Szasz <alexxed@gmail.com>
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

    class NarroFileImportPanel extends QPanel {
        protected $fileToUpload;
        protected $btnImport;
        /**
         * @var NarroFile
         */
        protected $objNarroFile;

        public function __construct(NarroFile $objNarroFile, $objParentObject, $strControlId = null) {
            parent::__construct($objParentObject, $strControlId);

            $this->objNarroFile = $objNarroFile;

            $this->btnImport = new QButton($this);
            $this->btnImport->Text = t('Import');
            $this->btnImport->ActionParameter = $this->objNarroFile->FileId;
            $this->btnImport->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnImport_Click'));

            $strTemplateFile = $this->objNarroFile->Project->DefaultTemplatePath . $this->objNarroFile->FilePath;
            if ((filesize($strTemplateFile) < __MAXIMUM_FILE_SIZE_TO_IMPORT__ || QApplication::HasPermissionForThisLang('Can import file', $this->objNarroFile->ProjectId)) && file_exists($strTemplateFile))
                $this->blnDisplay = true;
            else
                $this->blnDisplay = false;

            $this->fileToUpload = new QFileControl($this);
        }

        public function GetControlHtml() {
            $this->strText = '';

            if ($this->fileToUpload->Display)
                $this->strText .=  t('File to import') . ': ' . $this->fileToUpload->Render(false);

            if ($this->btnImport->Display)
                $this->strText .=  $this->btnImport->Render(false);

            return parent::GetControlHtml();
        }

        public function btnImport_Click($strFormId, $strControlId, $strParameter) {
            if (!$this->fileToUpload->Display) {
                $this->fileToUpload->Display = true;
                return false;
            }

            switch($this->objNarroFile->TypeId) {
                case NarroFileType::MozillaDtd:
                    $objFileImporter = new NarroMozillaDtdFileImporter();
                    break;
                case NarroFileType::MozillaInc:
                    $objFileImporter = new NarroMozillaIncFileImporter();
                    break;
                case NarroFileType::MozillaIni:
                    $objFileImporter = new NarroMozillaIniFileImporter();
                    break;
                case NarroFileType::GettextPo:
                    $objFileImporter = new NarroGettextPoFileImporter();
                    break;
                case NarroFileType::DumbGettextPo:
                    $objFileImporter = new NarroDumbGettextPoFileImporter();
                    break;
                case NarroFileType::Svg:
                    $objFileImporter = new NarroSvgFileImporter();
                    break;
                case NarroFileType::OpenOfficeSdf:
                    $objFileImporter = new NarroOpenOfficeSdfFileImporter();
                    break;
                case NarroFileType::PhpMyAdmin:
                    $objFileImporter = new NarroPhpMyAdminFileImporter();
                    break;
                case NarroFileType::Html:
                    $objFileImporter = new NarroHtmlFileImporter();
                    break;
                case NarroFileType::Unsupported:
                default:
                    $objFileImporter = new NarroUnsupportedFileImporter();
                    copy($this->fileToUpload->File, $this->objNarroFile->Project->DefaultTranslationPath . '/' . $this->objNarroFile->FilePath);
            }

            $objFileImporter->User = QApplication::$User;
            $objFileImporter->Project = $this->objNarroFile->Project;
            $objFileImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode(NarroLanguage::SOURCE_LANGUAGE_CODE);
            $objFileImporter->TargetLanguage = QApplication::$TargetLanguage;
            $objFileImporter->CheckEqual = true;
            $objFileImporter->File = $this->objNarroFile;
            $objFileImporter->OnlySuggestions = !QApplication::HasPermissionForThisLang('Can approve', $this->objNarroFile->ProjectId);
            $objFileImporter->DeactivateFiles = false;
            $objFileImporter->DeactivateContexts = false;

            $objFileImporter->Approve = QApplication::HasPermissionForThisLang('Can approve', $this->objNarroFile->ProjectId);

            $strTempFileName = tempnam(__TMP_PATH__, QApplication::$TargetLanguage->LanguageCode);

            $objFileImporter->ImportFile($this->objNarroFile->Project->DefaultTemplatePath . $this->objNarroFile->FilePath, $this->fileToUpload->File);

        }
    }