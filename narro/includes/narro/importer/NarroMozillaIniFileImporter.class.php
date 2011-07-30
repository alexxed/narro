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

    class NarroMozillaIniFileImporter extends NarroMozillaFileImporter {
        protected function IsComment($strLine) {
            return preg_match('/^[^a-z0-9]/i', $strLine);
        }

        protected function PreProcessFile($strFile) {
            // some ini files spread across more lines, this fixed that so that the key and value are on one line.
            return str_replace(array("\\\n"), array(''), $strFile);
        }


        protected function PreProcessLine($strLine, $arrComment, $arrLinesBefore) {
            return array($strLine, $arrComment, $arrLinesBefore);
        }

        protected function ProcessLine($strLine, $arrComment, $arrLinesBefore) {
            if (count($arrComment) && strstr($arrComment[count($arrComment) - 1], 'END LICENSE BLOCK')) {
                $arrLinesBefore = array_merge($arrLinesBefore, $arrComment);
                $arrComment = array();
            }

            $arrData = explode('=', $strLine, 2);
            if (count($arrData) == 2) {
                list($strKey, $strValue) = $arrData;
                return array(array('key' => $strKey, 'value' => $strValue), $arrComment, $arrLinesBefore);
            }
            else
                return array(false, $arrComment, $arrLinesBefore);
        }

        public function ImportFile($strTemplateFile, $strTranslatedFile = null) {
            $intTime = time();

            if ($strTranslatedFile)
                $arrTransKey = $this->FileAsArray($strTranslatedFile);

            $arrSourceKey = $this->FileAsArray($strTemplateFile);

            QFirebug::error(array($arrSourceKey, $arrTransKey));

            $intElapsedTime = time() - $intTime;
            if ($intElapsedTime > 0)
                QApplication::LogDebug(sprintf('Preprocessing %s took %d seconds.', $this->objFile->FileName, $intElapsedTime));

            QApplication::LogDebug(sprintf('Found %d contexts in file %s.', count($arrSourceKey), $this->objFile->FileName));

            if (is_array($arrSourceKey)) {
                $arrSourceKey = $this->GetAccessKeys($arrSourceKey);
                if (isset($arrTransKey))
                    $arrTransKey = $this->GetAccessKeys($arrTransKey);

                foreach($arrSourceKey as $strKey=>$arrData) {
                    // if it's a matched access key, keep going
                    if (isset($arrData['label_ctx']))
                        continue;
                    $this->AddTranslation(
                                trim($arrData['text']),
                                isset($arrData['access_key'])?trim($arrData['access_key']):null,
                                isset($arrTransKey[$strKey])?trim($arrTransKey[$strKey]['text']):null,
                                isset($arrTransKey[$strKey])?(isset($arrTransKey[$strKey]['access_key'])?trim($arrTransKey[$strKey]['access_key']):null):null,
                                trim($strKey),
                                (isset($arrData['access_key_ctx']))?trim($arrData['comment']) . "\n" . trim($arrSourceKey[$arrData['access_key_ctx']]['comment']):trim($arrData['comment'])
                    );
                }
            }
            else {
                QApplication::LogWarn(sprintf('Found a empty template (%s), copying the original', $strTemplateFile));
                copy($strTemplateFile, $strTranslatedFile);
                chmod($strTranslatedFile, 0666);
            }
        }

        public function ExportFile($strTemplateFile, $strTranslatedFile) {
            // get the modified date of the file and don't query for texts that were not modified after that unless there's no translation
            if (file_exists($strTranslatedFile)) {
                $intModifiedTime = filemtime($strTranslatedFile);
                $arrTransKeys = $this->FileAsArray($strTranslatedFile);
            }

            $arrSourceKey = $this->FileAsArray($strTemplateFile);

            if (!count($arrSourceKey)) {
                QApplication::LogWarn(sprintf('Found a empty template (%s), copying the original', $strTemplateFile));
                copy($strTemplateFile, $strTranslatedFile);
                chmod($strTranslatedFile, 0666);
                return false;
            }

            foreach($arrSourceKey as $intPos=>$strLine) {
                if (preg_match('/^\s*([\@0-9a-zA-Z\-\_\.\?\{\}]+)\s*=\s*(.*)\s*$/s', trim($strLine), $arrMatches)) {
                    $arrTemplate[trim($arrMatches[1])] = trim($arrMatches[2]);
                    $arrTemplateLines[trim($arrMatches[1])] = $arrMatches[0];
                    $arrTemplateComment[trim($arrMatches[1])] = $strComment;
                    $strComment = '';
                }
                elseif (trim($strLine) != '' && $strLine[0] != '#')
                    QApplication::LogDebug(sprintf('Skipped line "%s" from the template "%s".', $strLine, $this->objFile->FileName));
                elseif ($strLine[0] == '#') {
                    $strComment .= "\n" . $strLine;
                }
            }

            $strTranslateContents = '';

            if (!isset($arrTemplate) || count($arrTemplate) < 1) {
                QApplication::LogWarn(sprintf('Found a empty template (%s), copying the original', $strTemplateFile));
                copy($strTemplateFile, $strTranslatedFile);
                NarroUtils::Chmod($strTranslatedFile, 0666);
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
                        QApplication::LogWarn(sprintf('The plugin "%s" returned an unexpected result while processing the suggestion "%s": %s', QApplication::$PluginHandler->CurrentPluginName, $arrTranslation[$strKey], var_export($arrResult, true)));

                    if (preg_match('/[\@A-Z0-9a-z\.\_\-\?\{\}]+(\s*=\s*)/', $arrTemplateLines[$strKey], $arrMiddleMatches)) {
                        $strGlue = $arrMiddleMatches[1];
                    }
                    else {
                        QApplication::LogWarn(sprintf('Glue faield: "%s"', $arrTemplateLines[$strKey]));
                        $strGlue = '=';
                    }

                    if (strstr($arrTranslation[$strKey], "\n")) {
                        QApplication::LogWarn(sprintf('Skpping translation "%s" because it has a newline in it', $arrTranslation[$strKey]));
                        continue;
                    }

                    if (strstr($strTranslateContents, $strKey . $strGlue . $strOriginalText))
                        $strTranslateContents = str_replace($strKey . $strGlue . $strOriginalText, $strKey . $strGlue . $arrTranslation[$strKey], $strTranslateContents);
                    else
                        QApplication::LogWarn(sprintf('Can\'t find "%s" in the file "%s"', $strKey . $strGlue . $strOriginalText, $this->objFile->FileName));

                }
                else {
                    QApplication::LogDebug(sprintf('Couldn\'t find the key "%s" in the translations, using the original text.', $strKey, $this->objFile->FileName));
                    NarroImportStatistics::$arrStatistics['Texts kept as original']++;
                    if ($this->blnSkipUntranslated == true) {
                        if (isset($arrTemplateComment[$strKey]) && $arrTemplateComment[$strKey] != '') {
                            $strTranslateContents = str_replace($arrTemplateComment[$strKey] . "\n", "\n", $strTranslateContents);
                            $strTranslateContents = str_replace("\n" . $strKey . $strGlue . $strOriginalText . "\n", "\n", $strTranslateContents);
                        }
                    }
                }
            }

            if (file_exists($strTranslatedFile) && !is_writable($strTranslatedFile) && !unlink($strTranslatedFile)) {
                QApplication::LogError(sprintf('Can\'t delete the file "%s"', $strTranslatedFile));
            }

            if (!file_put_contents($strTranslatedFile, $strTranslateContents)) {
                QApplication::LogError(sprintf('Can\'t write to file "%s"', $strTranslatedFile));
            }

            @chmod($strTranslatedFile, 0666);
        }
    }
?>
