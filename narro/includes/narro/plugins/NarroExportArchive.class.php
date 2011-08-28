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
    class NarroExportArchive extends NarroPlugin {

        public function __construct() {
            parent::__construct();
            $this->blnEnable = true;
            $this->strName = t('Archive Exporter');
        }

        private function CreateExportArchive($strTranslationPath, $strArchive) {
            if (file_exists($strArchive))
                unlink($strArchive);

            $arrFiles = NarroUtils::ListDirectory($strTranslationPath, null, null, null, true);

            $objZipFile = new ZipArchive();
            if ($objZipFile->open($strArchive, ZipArchive::OVERWRITE) === TRUE) {
                foreach($arrFiles as $strFileName) {
                    if ($strTranslationPath == $strFileName) continue;
                    if (is_dir($strFileName)) {
                        $objZipFile->addEmptyDir(str_replace($strTranslationPath . '/', '', $strFileName ));
                    }
                    elseif (is_file($strFileName)) {
                        $objZipFile->addFile($strFileName, str_replace($strTranslationPath . '/', '', $strFileName ));
                    }
                }
            } else {
                QApplication::LogError(sprintf('Failed to create a new archive %s', $strArchive));
                return false;
            }
            $objZipFile->close();
            if (file_exists($strArchive))
                chmod($strArchive, 0666);
            else {
                QApplication::LogError(sprintf('Failed to create an archive %s', $strArchive));
                return false;
            }
            return true;
        }

        public function DisplayInProjectListInProgressColumn(NarroProject $objProject, $strText = '') {
            if (file_exists(__IMPORT_PATH__ . '/' . $objProject->ProjectId . '/' . $objProject->ProjectName . '-' . QApplication::$TargetLanguage->LanguageCode . '.zip')) {
                // @todo replace this with a download method that can serve files from a non web public directory
                $strDownloadUrl = __HTTP_URL__ . __SUBDIRECTORY__ . str_replace(__DOCROOT__ . __SUBDIRECTORY__, '', __IMPORT_PATH__) . '/' . $objProject->ProjectId . '/' . $objProject->ProjectName . '-' . QApplication::$TargetLanguage->LanguageCode . '.zip';
                $strExportText = sprintf('<a href="%s">%s</a>', $strDownloadUrl, $objProject->ProjectName . '-' . QApplication::$TargetLanguage->LanguageCode . '.zip');
            }
            else {
                $strExportText = '';
            }


            return array($objProject, $strExportText);
        }

        public function DisplayExportMessage(NarroProject $objProject, $strText = '') {
            $this->CreateExportArchive(
                $objProject->DefaultTranslationPath,
                __IMPORT_PATH__ . '/' . $objProject->ProjectId . '/' . $objProject->ProjectName . '-' . QApplication::$TargetLanguage->LanguageCode . '.zip'
            );
            if (file_exists(__IMPORT_PATH__ . '/' . $objProject->ProjectId . '/' . $objProject->ProjectName . '-' . QApplication::$TargetLanguage->LanguageCode . '.zip')) {
                $strDownloadUrl = sprintf(
                    __HTTP_URL__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__ . '/includes/narro/plugins/' . __CLASS__ . '.class.php?p=%d&file=%s-%s.zip',
                    $objProject->ProjectId,
                    $objProject->ProjectName,
                    QApplication::$TargetLanguage->LanguageCode
                );
                $strExportText = sprintf(sprintf(t('Download link: %s'), '<a href="%s">%s</a>'), $strDownloadUrl, $objProject->ProjectName . '-' . QApplication::$TargetLanguage->LanguageCode . '.zip');
            }
            else {
                $strExportText = t('Failed to create an archive for download');
            }


            return array($objProject, $strExportText);
        }
    }

    if (QApplication::QueryString('p') && QApplication::QueryString('file')) {
        $strFullPath = __IMPORT_PATH__ . '/' . QApplication::QueryString('p') . '/' . QApplication::QueryString('file');
        // File Exists?
        if( file_exists($strFullPath)) {
            header("Pragma: public"); // required
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false); // required for certain browsers
            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=\"" . QApplication::QueryString('file') . "\";" );
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($strFullPath));
            ob_clean();
            flush();
            readfile( $fullPath );
        }
    }
?>