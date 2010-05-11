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

    class NarroFileExportPanel extends QPanel {
        protected $fileToUpload;
        protected $btnExport;
        protected $objNarroFile;

        public function __construct(NarroFile $objNarroFile, $objParentObject, $strControlId = null) {
            parent::__construct($objParentObject, $strControlId);

            $this->objNarroFile = $objNarroFile;

            $this->btnExport = new QButton($this);
            $this->btnExport->Text = t('Export');
            $this->btnExport->ActionParameter = $this->objNarroFile->FileId;
            $this->btnExport->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnExport_Click'));
            $this->btnExport->Display = QApplication::HasPermissionForThisLang('Can export file', $this->objNarroFile->ProjectId);

            if (!$this->btnExport->Display) {
                $strTemplateFile = $this->objNarroFile->Project->DefaultTemplatePath . $this->objNarroFile->FilePath;
                if (file_exists($strTemplateFile) && filesize($strTemplateFile) < __MAXIMUM_FILE_SIZE_TO_EXPORT__)
                    $this->btnExport->Display = true;
                else
                    $this->btnExport->Display = false;
            }

            $this->fileToUpload = new QFileControl($this);
            $this->fileToUpload->Display = true;
        }

        public function GetControlHtml() {
            $this->strText = '';

            if ($this->fileToUpload->Display)
                $this->strText .=  t('Model to use') . ': ' . $this->fileToUpload->Render(false) . $this->btnExport->Render(false);

            if ($this->btnExport->Display && !$this->btnExport->Rendered)
                $this->strText .=  $this->btnExport->Render(false);

            return parent::GetControlHtml();
        }

        public function btnExport_Click($strFormId, $strControlId, $strParameter) {
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
                case NarroFileType::OpenOfficeSdf:
                    $objFileImporter = new NarroOpenOfficeSdfFileImporter();
                    break;
                case NarroFileType::Svg:
                    $objFileImporter = new NarroSvgFileImporter();
                    break;
                case NarroFileType::PhpMyAdmin:
                    $objFileImporter = new NarroPhpMyAdminFileImporter();
                    break;
                case NarroFileType::Unsupported:
                default:
                    $objFileImporter = new NarroUnsupportedFileImporter();
            }

            $objFileImporter->User = QApplication::$User;
            $objFileImporter->Project = $this->objNarroFile->Project;
            $objFileImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode(NarroLanguage::SOURCE_LANGUAGE_CODE);
            $objFileImporter->TargetLanguage = QApplication::$Language;
            $objFileImporter->File = $this->objNarroFile;

            $strTempFileName = tempnam(__TMP_PATH__, QApplication::$Language->LanguageCode);

            if (file_exists($this->fileToUpload->File)) {
                $objFileImporter->ExportFile($this->fileToUpload->File, $strTempFileName);
                unlink($this->fileToUpload->File);
            }
            else
                $objFileImporter->ExportFile($this->objNarroFile->Project->DefaultTemplatePath . $this->objNarroFile->FilePath, $strTempFileName);

            header(sprintf('Content-Disposition: attachment; filename="%s"', $this->objNarroFile->FileName));
            readfile($strTempFileName);
            unlink($strTempFileName);
            exit;
        }
    }