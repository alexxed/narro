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

    class NarroMozillaIniFileImporter extends NarroMozillaFileImporter {

        public function ImportFile($strTemplateFile, $strTranslatedFile = null) {
            $intTime = time();

            if ($strTranslatedFile)
                $strTranslatedFileContents = file_get_contents($strTranslatedFile);
            else
                $strTranslatedFileContents = file_get_contents($strTemplateFile);

            $strTemplateContents = file_get_contents($strTemplateFile);

            if (!$strTranslatedFileContents || !$strTemplateContents)
                return false;

            $strTranslatedFileContents = str_replace("\\\n", '', $strTranslatedFileContents);
            $strTemplateContents = str_replace("\\\n", '', $strTemplateContents);

            $arrFileContents = explode("\n", $strTranslatedFileContents);
            $arrTemplateContents = explode("\n", $strTemplateContents);

            $arrTranslation = array();
            foreach($arrFileContents as $intPos=>$strLine) {
                if (preg_match('/^\s*([\@0-9a-zA-Z\-\_\.\?\{\}]+)\s*=\s*(.*)\s*$/s', trim($strLine), $arrMatches))
                    $arrTranslation[trim($arrMatches[1])] = trim($arrMatches[2]);
            }

            $strContext = '';
            $arrTemplate = array();
            foreach($arrTemplateContents as $intPos=>$strLine) {
                if (preg_match('/^\s*([\@0-9a-zA-Z\-\_\.\?\{\}]+)\s*=\s*(.*)\s*$/s', trim($strLine), $arrMatches)) {
                    $arrTemplate[trim($arrMatches[1])] = trim($arrMatches[2]);
                    $arrTemplateComments[trim($arrMatches[1])] = $strContext;
                    $strContext = '';
                }
                elseif (strlen($strLine) > 2)
                    $strContext .= $strLine . "\n";
            }

            list($arrTemplate, $arrTemplateAccKeys) = $this->GetAccessKeys($arrTemplate);

            list($arrTranslation, $arrTranslationAccKeys) = $this->GetAccessKeys($arrTranslation);

            $intElapsedTime = time() - $intTime;
            if ($intElapsedTime > 0)
                QApplication::$Logger->debug(sprintf('Ini/Properties file %s preprocessing took %d seconds.', $this->objFile->FileName, $intElapsedTime));

            QApplication::$Logger->debug(sprintf('Found %d contexts in file %s.', count($arrTemplate), $this->objFile->FileName));

            if (is_array($arrTemplate))
                foreach($arrTemplate as $strKey=>$strVal) {
                    $this->AddTranslation(
                                $strVal,
                                isset($arrTemplateAccKeys[$strKey])?$arrTemplateAccKeys[$strKey]:null,
                                isset($arrTranslation[$strKey])?$arrTranslation[$strKey]:null,
                                isset($arrTranslationAccKeys[$strKey])?$arrTranslationAccKeys[$strKey]:null,
                                trim($strKey),
                                isset($arrTemplateComments[$strKey])?$arrTemplateComments[$strKey]:null
                    );
                }
            else {
                QApplication::$Logger->warn(sprintf('Found a empty template (%s), copying the original', $strTemplateFile));
                copy($strTemplateFile, $strTranslatedFile);
                chmod($strTranslatedFile, 0666);
            }

        }

        public function ExportFile($strTemplateFile, $strTranslatedFile) {
            $strTemplateContents = file_get_contents($strTemplateFile);

            if (!$strTemplateContents) {
                QApplication::$Logger->warn(sprintf('Found a empty template (%s), copying the original', $strTemplateFile));
                copy($strTemplateFile, $strTranslatedFile);
                chmod($strTranslatedFile, 0666);
                return false;
            }

            $strTemplateContents = str_replace("\\\n", '', $strTemplateContents);

            $arrTemplateContents = explode("\n", $strTemplateContents);

            foreach($arrTemplateContents as $intPos=>$strLine) {
                if (preg_match('/^\s*([\@0-9a-zA-Z\-\_\.\?\{\}]+)\s*=\s*(.*)\s*$/s', trim($strLine), $arrMatches)) {
                    $arrTemplate[trim($arrMatches[1])] = trim($arrMatches[2]);
                    $arrTemplateLines[trim($arrMatches[1])] = $arrMatches[0];
                }
                elseif (trim($strLine) != '' && $strLine[0] != '#')
                    QApplication::$Logger->debug(sprintf('Skipped line "%s" from the template "%s".', $strLine, $this->objFile->FileName));
            }

            $strTranslateContents = '';

            if (!isset($arrTemplate) || count($arrTemplate) < 1) {
                QApplication::$Logger->warn(sprintf('Found a empty template (%s), copying the original', $strTemplateFile));
                copy($strTemplateFile, $strTranslatedFile);
                chmod($strTranslatedFile, 0666);
                return false;
            }

            $arrTranslation = $this->GetTranslations($this->objFile, $arrTemplate);

            $strTranslateContents = $strTemplateContents;

            foreach($arrTemplate as $strKey=>$strOriginalText) {

                if (isset($arrTranslation[$strKey])) {

                    $arrResult = QApplication::$PluginHandler->ExportSuggestion($strOriginalText, $arrTranslation[$strKey], $strKey, $this->objFile, $this->objProject);

                    if
                    (
                        $arrResult[1] != '' &&
                        $arrResult[0] == $strOriginalText &&
                        $arrResult[2] == $strKey &&
                        $arrResult[3] == $this->objFile &&
                        $arrResult[4] == $this->objProject
                    ) {

                        $arrTranslation[$strKey] = $arrResult[1];
                    }
                    else
                        QApplication::$Logger->warn(sprintf('The plugin "%s" returned an unexpected result while processing the suggestion "%s": %s', QApplication::$PluginHandler->CurrentPluginName, $arrTranslation[$strKey], var_export($arrResult, true)));

                    if (preg_match('/[\@A-Z0-9a-z\.\_\-\?\{\}]+(\s*=\s*)/', $arrTemplateLines[$strKey], $arrMiddleMatches)) {
                        $strGlue = $arrMiddleMatches[1];
                    }
                    else {
                        QApplication::$Logger->warn(sprintf('Glue faield: "%s"', $arrTemplateLines[$strKey]));
                        $strGlue = '=';
                    }

                    if (strstr($arrTranslation[$strKey], "\n")) {
                        QApplication::$Logger->warn(sprintf('Skpping translation "%s" because it has a newline in it', $arrTranslation[$strKey]));
                        continue;
                    }

                    if (strstr($strTranslateContents, $strKey . $strGlue . $strOriginalText))
                        $strTranslateContents = str_replace($strKey . $strGlue . $strOriginalText, $strKey . $strGlue . $arrTranslation[$strKey], $strTranslateContents);
                    else
                        QApplication::$Logger->warn(sprintf('Can\'t find "%s" in the file "%s"', $strKey . $strGlue . $strOriginalText, $this->objFile->FileName));

                }
                else {
                    QApplication::$Logger->debug(sprintf('Couldn\'t find the key "%s" in the translations, using the original text.', $strKey, $this->objFile->FileName));
                    NarroImportStatistics::$arrStatistics['Texts kept as original']++;
                }
            }

            if (file_exists($strTranslatedFile) && !is_writable($strTranslatedFile) && !unlink($strTranslatedFile)) {
                QApplication::$Logger->err(sprintf('Can\'t delete the file "%s"', $strTranslatedFile));
            }

            if (!file_put_contents($strTranslatedFile, $strTranslateContents)) {
                QApplication::$Logger->err(sprintf('Can\'t write to file "%s"', $strTranslatedFile));
            }

            @chmod($strTranslatedFile, 0666);
        }
    }
?>
