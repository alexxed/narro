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

    class NarroOpenOfficeSdfFileImporter extends NarroFileImporter {

        public function ExportFile($strTemplateFile, $strTranslatedFile) {

            $hndTranslatedFile = @fopen($strTranslatedFile, 'w');

            if (!$hndTranslatedFile)
                throw new Exception(sprintf('Can\'t open file "%s" for writing', $strTranslatedFile));

            $intTotalToProcess = NarroUtils::CountFileLines($strTemplateFile);

            /**
             * get all the texts and contexts from the template file, including the file line
             */
            $arrTexts = $this->FileToArray($strTemplateFile, $this->objSourceLanguage->LanguageCode, true);

            QApplication::$Logger->debug(sprintf('Starting to process file "%s" (%d texts), the result is written to "%s".', $strTemplateFile, $intTotalToProcess, $strTranslatedFile));

            $intFileLineNr=0;

            foreach($arrTexts as $strContext=>$arrTextInfo) {
                $strText = $arrTextInfo[0];
                $strTextAccKey = $arrTextInfo[1];
                $strTextAccKeyPrefix = $arrTextInfo[2];
                $strFileLine = $arrTextInfo[3];

                $intFileLineNr++;

                $arrColumn = preg_split('/\t/', $strFileLine);

                /**
                 * Unset a number before language code
                 */
                $arrColumn[8] = '';

                /**
                 * create a copy for the translated line, we'll just replace lang code, the number on column 8 and the text with the translation
                 */
                $arrTranslatedColumn = $arrColumn;

                $arrTranslatedColumn[8] = 0;
                $arrTranslatedColumn[9] = 'ro';

                $objNarroContextInfo = $this->GetContextInfo($strText, $strContext);

                /**
                 * the original texts are used if no suggestion is found, so we export only approved texts
                 */
                if ($objNarroContextInfo instanceof NarroContextInfo)
                    $strSuggestionValue = $this->GetExportedSuggestion($objNarroContextInfo);
                else
                    continue;

                if (!isset($strSuggestionValue) || !$strSuggestionValue)
                    continue;

                if ( $objNarroContextInfo->TextAccessKey != '') {
                    if ($objNarroContextInfo->ValidSuggestionId && $objNarroContextInfo->SuggestionAccessKey != '')
                        $strSuggestionValue = NarroString::Replace(
                            $objNarroContextInfo->SuggestionAccessKey,
                            $strTextAccKeyPrefix . $objNarroContextInfo->SuggestionAccessKey,
                            $strSuggestionValue,
                            1
                        );
                    else
                        $strSuggestionValue = $strTextAccKeyPrefix . $strSuggestionValue;
                }


                $arrResult = QApplication::$PluginHandler->ExportSuggestion($strText, $strSuggestionValue, $strContext, new NarroFile(), $this->objProject);
                if
                (
                    $arrResult[1] != '' &&
                    $arrResult[0] == $strText &&
                    $arrResult[2] == $strContext &&
                    $arrResult[3] == new NarroFile() &&
                    $arrResult[4] == $this->objProject
                ) {

                    $strSuggestionValue = $arrResult[1];
                }
                else
                    QApplication::$Logger->warn(sprintf('The plugin "%s" returned an unexpected result while processing the suggestion "%s": %s', QApplication::$PluginHandler->CurrentPluginName, $strSuggestionValue, print_r($arrResult, true)));

                $arrTranslatedColumn[10] = str_replace(array("\n", "\r"), array("",""), $strSuggestionValue);


                preg_match_all('/\\\\"/', $strText, $arrEscOrigMatches);
                preg_match_all('/\\\\"/', $strSuggestionValue, $arrEscTransMatches);

                if (isset($arrEscOrigMatches[0]) && count($arrEscTransMatches[0]) % 2 != 0) {
                    QApplication::$Logger->warn(sprintf('Warning! The translated text "%s" has unclosed double quotes.', $strSuggestionValue));
                    continue;
                }

                fwrite($hndTranslatedFile, join("\t", $arrColumn));
                fwrite($hndTranslatedFile, join("\t", $arrTranslatedColumn));

            }

            fclose($hndTranslatedFile);
            chmod($strTranslatedFile, 0666);
        }

        public function ImportFile($strTemplateFile, $strTranslatedFile = null) {

            if (file_exists($strTemplateFile))
                $arrTexts = $this->FileToArray($strTemplateFile, $this->objSourceLanguage->LanguageCode);
            else
                QApplication::$Logger->err(sprintf('The template file "%s" does not exist.', $strTemplateFile));

            if (trim($strTranslatedFile) != '')
                if (file_exists($strTranslatedFile))
                    $arrTranslations = $this->FileToArray($strTranslatedFile, $this->objTargetLanguage->LanguageCode);
                else
                    QApplication::$Logger->err(sprintf('The translation file "%s" does not exist.', $strTranslatedFile));

            foreach($arrTexts as $strContext=>$arrTextInfo) {
                if (isset($arrTranslations[$strContext]))
                    $this->AddTranslation($arrTexts[$strContext][0], $arrTexts[$strContext][1], $arrTranslations[$strContext][0], $arrTranslations[$strContext][1], $strContext);
                else
                    $this->AddTranslation($arrTexts[$strContext][0], $arrTexts[$strContext][1], null, null, $strContext);
            }
        }

        private function FileToArray($strFile, $strLocale, $blnIncludeFileLine = false) {
            $arrTexts = array();

            $hndFile = fopen($strFile, 'r');

            if (!$hndFile) {
                QApplication::$Logger->err(sprintf('Cannot open input file "%s" for reading.', $strFile));
                return false;
            }

            $intTotalToProcess = NarroUtils::CountFileLines($strFile);
            $intProcessedSoFar = 0;

            /**
             * read the template file line by line
             */
            while(!feof($hndFile)) {
                $strFileLine = fgets($hndFile);
                $intProcessedSoFar++;
                if ($strFileLine == '') {
                    QApplication::$Logger->debug(sprintf('Skipping empty line from "%s"', $strFileLine));
                    continue;
                }

                /**
                 * OpenOffice uses tab separated values
                 */
                $arrColumn = explode("\t", $strFileLine);
                if (count($arrColumn) != 15) {
                    QApplication::$Logger->err(sprintf('Skipping line "%s" from "%s" because it does not split into 15 fields by tab', $strFileLine, $strFile));
                    continue;
                }

                $strLangCode = $arrColumn[9];

                if ($strLangCode == 1)
                    $strLangCode = NarroLanguage::SOURCE_LANGUAGE_CODE;

                if ($strLocale == trim($strLangCode) ) {
                    $strContext = '';

                    /**
                     * positions 8, 9 and 10 contain a number, the language code and the text/translation
                     * positions 1 and 2 contain path info
                     * position 3 contains a number
                     * position 14 contains a date
                     */
                    foreach(array(3, 4, 5, 6, 7, 11, 12, 13) as $intPos) {
                        if (trim($arrColumn[$intPos]) != '')
                            $strContext .= $arrColumn[$intPos] ."\n";
                    }

                    $strContext = trim($strContext);


                    $strText = $arrColumn[10];
                    $strTextAccKey = null;
                    $strTextAccKeyPrefix = null;

                    if (strstr($strText, '~') && preg_match('/~(\w)/', $strText, $arrTextAccMatches)) {
                        $strTextAccKey = $arrTextAccMatches[1];
                        $strText = mb_ereg_replace('~' . $strTextAccKey, $strTextAccKey, $strText);
                        $strTextAccKeyPrefix = '~';
                    }
                    elseif (strstr($strText, '&') && preg_match('/&(\w)/', $strText, $arrTextAccMatches)) {
                        $strTextAccKey = $arrTextAccMatches[1];
                        $strText = mb_ereg_replace('&' . $strTextAccKey, $strTextAccKey, $strText);
                        $strTextAccKeyPrefix = '&';
                    }
                    else {
                        $strTextAccKey = null;
                    }

                    if (isset($arrTexts[$strContext]))
                        $strContext .= "\n" . $intProcessedSoFar;

                    $arrTexts[$strContext] = array($strText, $strTextAccKey, $strTextAccKeyPrefix, ($blnIncludeFileLine)?$strFileLine:'');
                }
                else {
                    QApplication::$Logger->debug(sprintf('Skipping line "%s" from "%s" because detected language code "%s" does not match the expected one "%s"', $strFileLine, $strFile, $strLangCode, $strLocale));
                }
            }
            fclose($hndFile);

            return $arrTexts;
        }

        public static function SplitFile($strFile, $strPath, $arrLocale = array(NarroLanguage::SOURCE_LANGUAGE_CODE)) {
            $hndFile = fopen($strFile, 'r');

            if (!$hndFile) {
                QApplication::$Logger->err(sprintf('Cannot open input file "%s" for reading.', $strFile));
                return false;
            }

            /**
             * read the template file line by line
             */
            while(!feof($hndFile)) {
                $strFileLine = fgets($hndFile);

                /**
                 * OpenOffice uses tab separated values
                 */
                $arrColumn = preg_split('/\t/', $strFileLine);

                if (count($arrColumn) < 14) continue;

                if (!in_array($arrColumn[9], $arrLocale)) continue;

                $strFilePath = $strPath . '/' . $arrColumn[9] . '/' . $arrColumn[0] . '/' . str_replace('\\', '/', $arrColumn[1]) . '.sdf';

                if (!file_exists($strFilePath)) {
                    if (!file_exists(dirname($strFilePath))) {
                        mkdir(dirname($strFilePath), 0777, true);
                    }
                }

                $hndSplitFile = fopen($strFilePath, 'a+');
                fputs($hndSplitFile, $strFileLine);
                fclose($hndSplitFile);
            }

            NarroUtils::RecursiveChmod($strPath);
        }
    }
?>
