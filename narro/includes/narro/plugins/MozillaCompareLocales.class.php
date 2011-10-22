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
        $strFullPath = sprintf('%s/%d/%s_%s_compare-locales.html', __IMPORT_PATH__, $_REQUEST['p'], $_REQUEST['pn'], $_REQUEST['l']);
        // File Exists?
        if( file_exists($strFullPath)) {
            header("Pragma: public"); // required
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false); // required for certain browsers
            header("Content-Type: text/html");
            header("Content-Length: " . filesize($strFullPath));
            ob_clean();
            flush();
            readfile($strFullPath);
            exit;
        }
    }
    
    class MozillaCompareLocales extends NarroPlugin {

        public function __construct() {
            parent::__construct();
            $this->blnEnable = false;
            $this->strName = t('Mozilla compare locales');
            $this->Enable();
            $this->blnEnable = $this->blnEnable;
            
            // NarroProject::RegisterPreference('Compare locales Python path', true, NarroProjectType::Mozilla, 'text', 'e.g. /home/alexxed/apps/compare-locales-0.9/lib', '');
            // NarroProject::RegisterPreference('Path to a hg clone of this project', true, NarroProjectType::Mozilla, 'text', 'e.g. /home/alexxed/mozilla_projects/mozilla-aurora', '');
        }
        
        protected function GetOutputFileName($objProject) {
            return __IMPORT_PATH__ . '/' . $objProject->ProjectId . '/' . $objProject->ProjectName . '_' . QApplication::$TargetLanguage->LanguageCode . '_compare-locales.html';
        }
        
        protected function RunCompareLocales() {
            $strFileName = __IMPORT_PATH__ . '/' . $objProject->ProjectId . '/' . $objProject->ProjectName . '-' . QApplication::$TargetLanguage->LanguageCode . '-compare-locales.html';
            
            exec(
                'export PYTHONPATH=%s;' .
                'COMPARE_LOCALE=%s;' .
            	'PROJECT_DIR=%s;' .
                '$COMPARE_LOCALE $PROJECT_DIR/mozilla-aurora/browser/locales/l10n.ini $PROJECT_DIR/l10n $1;' .
            	'$COMPARE_LOCALE $PROJECT_DIR/mozilla-aurora/toolkit/locales/l10n.ini $PROJECT_DIR/l10n $1;' .
                '$COMPARE_LOCALE $PROJECT_DIR/mozilla-aurora/services/sync/locales/l10n.ini $PROJECT_DIR/l10n $1', 
                $arrOutput, 
                $retVal
            );

            $strOutput = join("\n", $arrOutput);
            //if ($retVal != 0) die('Running compare locales failed: ' . $strOutput );
            
            
            
            $strOutput = preg_replace('/within\s+([a-zA-Z0-9\.\-_]+)/', sprintf('within <a target="_blank" href="https://l10n.mozilla.org/narro/narro_project_text_list.php?l=%s&p=%d&tf=1&st=3&s=\1">\1</a>', $argv[1], $argv[2]), $strOutput);
            $strOutput = preg_replace('/(\s+)\+([a-zA-Z0-9\.\-_]+)/', sprintf('\1+<a target="_blank" href="https://l10n.mozilla.org/narro/narro_project_text_list.php?l=%s&p=%d&tf=1&st=3&s=\2">\2</a>', $argv[1], $argv[2]), $strOutput);
            
            $strOutput = str_replace('ERROR', '<span style="background-color:red;font-weight:bold">ERROR</span>', $strOutput);
            
            $strOutput = str_replace('WARNING', '<span style="background-color:orange;font-weight:bold">WARNING</span>', $strOutput);
            
            file_put_contents($strFileName, '<pre>' . $strOutput . '</pre>');            
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
