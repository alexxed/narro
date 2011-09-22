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

    if (isset($_REQUEST['p']) && isset($_REQUEST['pn']) && isset($_REQUEST['l'])) {
        require_once(dirname(__FILE__) . '/../../../configuration/configuration.narro.inc.php');
        $strFullPath = sprintf('%s/%d/%s-%s.xpi', __IMPORT_PATH__, $_REQUEST['p'], $_REQUEST['pn'], $_REQUEST['l']);
        // File Exists?
        if( file_exists($strFullPath)) {
            header("Pragma: public"); // required
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false); // required for certain browsers
            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=\"" . basename($strFullPath) . "\";" );
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($strFullPath));
            ob_clean();
            flush();
            readfile($strFullPath);
            exit;
        }
    }
    
    class MozillaXpiCreator extends NarroPlugin {

        public function __construct() {
            parent::__construct();
            $this->blnEnable = false;
            $this->strName = t('Mozilla XPI creator');
            $this->Enable();
            $this->blnEnable = $this->blnEnable;
        }
        
        protected function GetOutputFileName($objProject) {
            return __IMPORT_PATH__ . '/' . $objProject->ProjectId . '/' . $objProject->ProjectName . '_' . QApplication::$TargetLanguage->LanguageCode . '.xpi';
        }
        
        public function DisplayExportMessage(NarroProject $objProject, $strText = '') {
            $strExportText = '';
            if (file_exists($this->GetOutputFileName($objProject))) {
                $strDownloadUrl = sprintf(
                    __HTTP_URL__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__ . '/includes/narro/plugins/' . __CLASS__ . '.class.php?p=%d&pn=%s&l=%s',
                    $objProject->ProjectId,
                    $objProject->ProjectName,
                    QApplication::$TargetLanguage->LanguageCode
                );
                $objDateSpan = new QDateTimeSpan(time() - filemtime($this->GetOutputFileName($objProject)));
                $strExportText = sprintf(
                    '<a href="%s">%s</a>, ' . t('generated %s ago'),
                    $strDownloadUrl ,
                    basename($this->GetOutputFileName($objProject)),
                    $objDateSpan->SimpleDisplay()
                );
            }

            return array($objProject, $strExportText);
        }

        public function DisplayInProjectListInProgressColumn(NarroProject $objProject, $strText = '') {
            return $this->DisplayExportMessage($objProject, $strText);
        }
    }
?>
