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

    class NarroMozillaIncFileImporter extends NarroMozillaFileImporter {
        protected function IsComment($strLine) {
            return strpos($strLine, '#define') !== 0;
        }

        protected function PreProcessFile($strFile) {
            // some files spread across more lines with an ending backslash, this fixed that so that the key and value are on one line.
            return str_replace(array("\\\n"), array(''), $strFile);
        }


        protected function PreProcessLine($strLine, $arrComment, $arrLinesBefore) {
            // special case, this is usually commented
            if (strstr($strLine, '# #define MOZ_LANGPACK_CONTRIBUTORS'))
                $strLine = substr($strLine, 2);
            else
                $strLine = $strLine;

            return array($strLine, $arrComment, $arrLinesBefore);
        }

        protected function ProcessLine($strLine, $arrComment, $arrLinesBefore) {
            if (count($arrComment) && strstr($arrComment[count($arrComment) - 1], 'END LICENSE BLOCK')) {
                $arrLinesBefore = array_merge($arrLinesBefore, $arrComment);
                $arrComment = array();
            }

            $arrData = explode(' ', $strLine, 3);
            if (count($arrData) == 3) {
                list($strDefineStatement, $strKey, $strValue) = $arrData;
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

            $intElapsedTime = time() - $intTime;
            if ($intElapsedTime > 0)
                QApplication::LogDebug(sprintf('Preprocessing %s took %d seconds.', $this->objFile->FileName, $intElapsedTime));

            QApplication::LogDebug(sprintf('Found %d contexts in file %s.', count($arrSourceKey), $this->objFile->FileName));

            if (is_array($arrSourceKey)) {
                list($arrTemplate, $arrTemplateAccKeys) = $this->GetAccessKeys($arrTemplate);
                list($arrTranslation, $arrTranslationAccKeys) = $this->GetAccessKeys($arrTranslation);

                foreach($arrSourceKey as $strKey=>$arrData) {
                    $this->AddTranslation(
                                trim($arrData['text']),
                                null,
                                isset($arrTransKey[$strKey])?trim($arrTransKey[$strKey]['text']):null,
                                null,
                                trim($strKey),
                                trim($arrData['comment'])
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
            $strTemplateContents = file_get_contents($strTemplateFile);

            if (!$strTemplateContents) {
                QApplication::LogWarn(sprintf('Found a empty template (%s), copying the original', $strTemplateFile));
                copy($strTemplateFile, $strTranslatedFile);
                chmod($strTranslatedFile, 0666);
                return false;
            }

            if (strstr($strTemplateContents, '#define MOZ_LANGPACK_CONTRIBUTORS'))
                $strTemplateContents = preg_replace('/^#\s+#define MOZ_LANGPACK_CONTRIBUTORS.*$/m', '#define MOZ_LANGPACK_CONTRIBUTORS <em:contributor>Joe Solon</em:contributor> <em:contributor>Suzy Solon</em:contributor>', $strTemplateContents);

            $arrTemplateContents = explode("\n", $strTemplateContents);

            $strComment = '';
            foreach($arrTemplateContents as $intPos=>$strLine) {
                if (preg_match('/^#define\s+([^\s]+)\s+(.+)$/s', trim($strLine), $arrMatches)) {
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

            if (count($arrTemplate) < 1) return false;

            $arrTranslationObjects =
                NarroContextInfo::QueryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $this->objFile->FileId),
                        QQ::Equal(QQN::NarroContextInfo()->LanguageId, $this->objTargetLanguage->LanguageId),
                        QQ::Equal(QQN::NarroContextInfo()->Context->Active, 1)
                    )
                );

            foreach($arrTranslationObjects as $objNarroContextInfo) {
                if ($objNarroContextInfo->ValidSuggestionId > 0) {
                    $arrTranslation[$objNarroContextInfo->Context->Context] = $this->GetExportedSuggestion($objNarroContextInfo);

                    if ($arrTranslation[$objNarroContextInfo->Context->Context] === false)
                        $arrTranslation[$objNarroContextInfo->Context->Context] = $objNarroContextInfo->Context->Text->TextValue;

                    if ($objNarroContextInfo->TextAccessKey) {
                        if ($objNarroContextInfo->SuggestionAccessKey)
                            $strAccessKey = $objNarroContextInfo->SuggestionAccessKey;
                        else
                            $strAccessKey = $objNarroContextInfo->TextAccessKey;

                        $arrTranslation[$objNarroContextInfo->Context->Context] = preg_replace('/' . $strAccessKey . '/', '&' . $strAccessKey, $arrTranslation[$objNarroContextInfo->Context->Context] , 1);

                        NarroImportStatistics::$arrStatistics['Texts that have access keys']++;
                    }
                    else
                        NarroImportStatistics::$arrStatistics["Texts that don't have access keys"]++;
                }
                else {
                    QApplication::LogDebug(sprintf('In file "%s", the context "%s" does not have a valid suggestion.', $this->objFile->FileName, $objNarroContextInfo->Context->Context));
                    NarroImportStatistics::$arrStatistics['Texts without valid suggestions']++;
                    NarroImportStatistics::$arrStatistics['Texts kept as original']++;
                }
            }


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

                    if (strstr($strTranslateContents, sprintf('#define %s %s', $strKey, $strOriginalText)))
                        $strTranslateContents = str_replace(sprintf('#define %s %s', $strKey, $strOriginalText), sprintf('#define %s %s', $strKey, $arrTranslation[$strKey]), $strTranslateContents);
                    else
                        QApplication::LogWarn(sprintf('Can\'t find "%s" in the file "%s"'), $strKey . $strGlue . $strOriginalText, $this->objFile->FileName);

                    if (strstr($arrTranslation[$strKey], "\n")) {
                        QApplication::LogWarn(sprintf('Skpping translation "%s" because it has a newline in it'), $arrTranslation[$strKey]);
                        continue;
                    }

                }
                else {
                    QApplication::LogDebug(sprintf('Couldn\'t find the key "%s" in the translations, using the original text.', $strKey, $this->objFile->FileName));
                    NarroImportStatistics::$arrStatistics['Texts kept as original']++;
                    if ($this->blnSkipUntranslated == true) {

                    }

                    if ($this->blnSkipUntranslated == true) {
                        if (isset($arrTemplateComment[$strKey]) && $arrTemplateComment[$strKey] != '') {
                            $strTranslateContents = str_replace($arrTemplateComment[$strKey] . "\n", "\n", $strTranslateContents);
                            $strTranslateContents = str_replace(sprintf("#define %s %s\n", $strKey, $strOriginalText), '', $strTranslateContents);
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
