<?php
    /**
     * Narro is an application that allows online software translation and maintenance.
     * Copyright (C) 2008 Alexandru Szasz <alexxed@gmail.com>
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

        public function ExportFile($objFile, $strTemplateFile, $strTranslatedFile) {
            $objDatabase = QApplication::$Database[1];

            $hndTemplateFile = fopen($strTemplateFile, 'r');
            if (!$hndTemplateFile) {
                NarroLog::LogMessage(3, __LINE__ . ':' . sprintf(t('Can\'t open file "%s" for reading'), $strTemplateFile));
                return false;
            }

            $hndTranslatedFile = fopen($strTranslatedFile, 'w');
            if (!$hndTranslatedFile) {
                NarroLog::LogMessage(3, __LINE__ . ':' . sprintf('Can\'t open file "%s” for reading', $strTranslatedFile));
                return false;
            }

            $intTotalToProcess = count(file($strTemplateFile));


            NarroLog::LogMessage(1, __LINE__ . ':' . sprintf(t('Starting to process file "%s" (%d texts), the result is written to "%s".'), $strTemplateFile, $this->intTotalToProcess, $strTranslatedFile));

            /**
             * Get the contexts with valid suggestions
             */
            $strQuery = sprintf("SELECT `text_access_key`, `suggestion_access_key`, `suggestion_value`, `text_value`, `context` FROM narro_context_info ci, narro_context c, narro_suggestion s, narro_text t WHERE c.active=1 AND c.text_id=t.text_id AND ci.valid_suggestion_id=s.suggestion_id AND c.project_id=%d AND c.context_id=ci.context_id AND ci.language_id=%d", $this->objProject->ProjectId, $this->objTargetLanguage->LanguageId);

            if (!$objDbResult = $objDatabase->Query($strQuery)) {
                NarroLog::LogMessage(3, __METHOD__ . ':' . __LINE__ . ':db_query failed. $strQuery=' . $strQuery);
                return false;
            }

            if ($objDbResult->CountRows()) {
                while($arrDbRow = $objDbResult->FetchArray()) {
                    if (isset($arrFile[md5($arrDbRow['context'])]) && $arrDbRow['suggestion_value'] != $arrFile[md5($arrDbRow['context'])]) {
                        NarroLog::LogMessage(3, __LINE__ . ':' . sprintf('Warning, md5("%s") already exists as key and it has the value "%s". I was trying to set the value "%s"!', $arrDbRow['context'], $arrFile[md5($arrDbRow['context'])], $arrDbRow['suggestion_value']));
                    }
                    if ($arrDbRow['text_access_key'] == '')
                        $arrFile[md5($arrDbRow['context'])] = $arrDbRow['suggestion_value'];
                    else
                        $arrFile[md5($arrDbRow['context'])] = NarroString::Replace($arrDbRow['suggestion_access_key'], '~' . $arrDbRow['suggestion_access_key'], $arrDbRow['suggestion_value'], 1);
                }
            }
            else {
                NarroLog::LogMessage(3, __LINE__ . ':' . sprintf('Failed to count rows after running query "%s"', $strQuery));
                return false;
            }

            $intFileLineNr=0;

            while(!feof($hndTemplateFile)) {
                $strFileLine = fgets($hndTemplateFile, 4096);
                $intFileLineNr++;

                $arrColumn = preg_split('/\t/', $strFileLine);
                if (count($arrColumn) != 15) {
                    NarroLog::LogMessage(2, __LINE__ . ':' . sprintf('Skipped "%s" because splitting by tab does not give 14 columns.', $strFileLine));
                    continue;
                }

                $arrTranslatedColumn = $arrColumn;

                $strLangCode = $arrColumn[9];
                $strText = $arrColumn[10];
                $strContext = trim(str_replace("\t", "\n", $strFileLine));


                $arrColumn[8] = 0;
                $arrTranslatedColumn[8] = 0;
                $arrTranslatedColumn[9] = 'ro';

                if (isset($arrFile[md5($strContext)])) {
                    $arrResult = QApplication::$objPluginHandler->ExportSuggestion($strText, $arrFile[md5($strContext)], $strContext, $objFile, $this->objProject);
                    if
                    (
                        trim($arrResult[1]) != '' &&
                        $arrResult[0] == $strText &&
                        $arrResult[2] == $strContext &&
                        $arrResult[3] == $objFile &&
                        $arrResult[4] == $this->objProject
                    ) {

                        $arrFile[md5($strContext)] = $arrResult[1];
                    }
                    else
                        NarroLog::LogMessage(2, sprintf(t('A plugin returned an unexpected result while processing the suggestion "%s": %s'), $arrFile[md5($strContext)], print_r($arrResult, true)));

                    $arrTranslatedColumn[10] = str_replace(array("\n", "\r"), array("",""), $arrFile[md5($strContext)]);
                }
                else {
                    continue;
                }

                preg_match_all('/\\\\"/', $strText, $arrEscOrigMatches);
                preg_match_all('/\\\\"/', $arrFile[md5($strContext)], $arrEscTransMatches);

                if (isset($arrEscOrigMatches[0])) {
                    if (!isset($arrEscTransMatches[0])) {
                        NarroLog::LogMessage(3, __LINE__ . ':' . sprintf('Warning! The original text "%s" has some doube quotes but the translated text "%s" doesn\'t.', $strText, $arrFile[md5($strContext)]));
                        continue;
                    }

                    if (count($arrEscOrigMatches[0]) != count($arrEscTransMatches[0])) {
                        NarroLog::LogMessage(3, __LINE__ . ':' . sprintf('Warning! The original text "%s" has some double quotes but the translated text "%s" has less or more of them.', $strText, $arrFile[md5($strContext)]));
                        continue;
                    }
                }

                fwrite($hndTranslatedFile, join("\t", $arrColumn));
                fwrite($hndTranslatedFile, join("\t", $arrTranslatedColumn));

                $intProcessedSoFar++;
            }

            fclose($hndTemplateFile);
            fclose($hndTranslatedFile);
            chmod($strTranslatedFile, 0666);
        }

        public function ImportFile($objFile, $strTemplateFile, $strTranslatedFile) {
            $objDatabase = QApplication::$Database[1];
            /**
             * Open the template file
             */
            $hndFile = fopen($strTemplateFile, 'r');

            if (!$hndFile) {
                NarroLog::LogMessage(3, sprintf(t('Cannot open input file "%s" for reading.'), $strTemplateFile));
                return false;
            }

            //@todo replace this with a command or something, this takes way too much memory
            $intTotalToProcess = count(file($strTemplateFile));

            /**
             * read the template file line by line
             */
            while(!feof($hndFile)) {
                $strFileLine = fgets($hndFile, 16384);
                $intProcessedSoFar++;

                /**
                 * OpenOffice uses tab separated values
                 */
                $arrColumn = preg_split('/\t/', $strFileLine);

                /**
                 * skip help
                 */
                if ($arrColumn[0] == 'helpcontent2') continue;

                $strLangCode = $arrColumn[9];

                if ($strLangCode == 1)
                    $strLangCode = 'en_US';
                elseif($strLangCode == 40)
                    $strLangCode = 'ro';
                elseif($strLangCode == 'en-US')
                    $strLangCode = 'en_US';

                /**
                 * if we have a line with the target language in the language column, then the previous line was probably the original english value
                 * to be sure, we're checking the context too
                 */
                if ($this->objTargetLanguage->LanguageCode == trim($strLangCode) && $strContext == $arrColumn[0] . "\n" . $arrColumn[1] . "\n" . $arrColumn[3] . "\n" . $arrColumn[4]) {
                    /**
                     * $strText and $strTextAccKey are kept from the previous cycle
                     */
                    $strTranslation = $arrColumn[10];

                    /**
                     * search for access key if needed
                     */
                    if ($strTextAccKey) {
                        /**
                         * if we import a translation and it already has an access key set, keep it
                         * if not, find one or just use the first usable character
                         */
                        if (preg_match('/~(\w)/', $strTranslation, $arrTranslationAccMatches)) {
                            $strTranslationAccKey = $arrTranslationAccMatches[1];
                        }
                        else {
                            $intPos = mb_stripos($strTranslation, $strTextAccKey);
                            if ($intPos != false)
                                $strTranslationAccKey = mb_substr($strTranslation, $intPos, 1);
                            else {
                                if (preg_match('/(\w)/', $strTranslation, $arrTranslationAccMatches))
                                    $strTranslationAccKey = $arrTranslationAccMatches[1];
                                else
                                    $strTranslationAccKey = mb_substr($strTranslation, 0, 1);
                            }
                        }

                        $strTranslation = mb_ereg_replace('~' . $strTranslationAccKey, $strTranslationAccKey, $strTranslation);
                    }
                    else
                        $strTranslationAccKey = null;

                }
                elseif ($this->objSourceLanguage->LanguageCode == trim($strLangCode) ) {
                    $strText = $arrColumn[10];

                    $strTranslation = null;
                    $strTranslationAccKey = null;

                    if (preg_match('/~(\w)/', $strText, $arrTextAccMatches)) {
                        $strTextAccKey = $arrTextAccMatches[1];
                        $strText = mb_ereg_replace('~' . $strTextAccKey, $strTextAccKey, $strText);
                    }
                    else {
                        $strTextAccKey = null;
                    }
                }
                else {
                    NarroLog::LogMessage(2, sprintf(t('Skipped line "%s" because the language code found "%s" does not match the source or target language. Columns: %s'), $strFileLine, $strLangCode, print_r($arrColumn, true)));
                    continue;
                }

                $strContext = str_replace("\t", "\n", $strFileLine);

                $strDate = $arrColumn[14];

                if (!isset($strDate))
                    continue;

                if (!preg_match('/[0-9]{4,4}[\-]?[0-9]{2,2}[\-]?[0-9]{2,2}\s[0-9]{2,2}:[0-9]{2,2}:[0-9]{2,2}/', $strDate)) {
                    NarroLog::LogMessage(2, var_export($strDate,true) . ' not good. Count: ' . count($arrColumn) . var_export($arrColumn, true));
                    continue;
                }

                /**
                 * $arrColumn[1] looks like this: source\dialogs\macrosecurity.src
                 * Now every line contains source\ and ends with .src, so we'll ignore those
                 * for everything else, dialogs is a folder and macrosecurity will be a file
                 */
                if (preg_match_all('/([^\.\\\]+)[\\\\.]{1,1}/', $arrColumn[1], $arrColMatches)) {
                    /**
                     * Replace source with the component name
                     */
                    $arrColMatches[1][0] = $arrColumn[0];
                    /**
                     * ignore the last part
                     */
                    //unset($arrColMatches[1][count($arrColMatches[1]) - 1]);

                    $strPath = '';
                    /**
                     * $arrColMatches[1] contains dialogs and macrosecurity in our case
                     * we'll go over them and see if the files exists or need to be updated
                     */
                    foreach($arrColMatches[1] as $intKey=>$strFileName) {
                        if (!isset($arrFiles[$strPath . '/' . $strFileName])) {
                            if ($arrFiles[$strPath] instanceof NarroFile) {
                                $objFile = NarroFile::QuerySingle(QQ::AndCondition(QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId), QQ::Equal(QQN::NarroFile()->FileName, $strFileName), QQ::Equal(QQN::NarroFile()->ParentId, $arrFiles[$strPath]->FileId)));
                            }
                            else {
                                // Found parent
                                $objFile = NarroFile::QuerySingle(QQ::AndCondition(QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId), QQ::Equal(QQN::NarroFile()->FileName, $strFileName), QQ::IsNull(QQN::NarroFile()->ParentId)));
                            }

                            if (!$objFile instanceof NarroFile) {
                                if ($this->blnOnlySuggestions)
                                    continue;
                                $objFile = new NarroFile();
                                $objFile->FileName = $strFileName;
                                if ($intKey == count($arrColMatches[1]) - 1)
                                    $objFile->TypeId = NarroFileType::OpenOfficeSdf;
                                else
                                    $objFile->TypeId = NarroFileType::Folder;
                                $objFile->ProjectId = $this->objProject->ProjectId;
                                if ($strPath != '' && isset($arrFiles[$strPath])) {
                                    $objFile->ParentId = $arrFiles[$strPath]->FileId;
                                }
                                $objFile->Active = 1;
                                $objFile->Encoding = 'UTF-8';
                                $objFile->Modified = date('Y-m-d H:i:s');
                                $objFile->Created = date('Y-m-d H:i:s');
                                $objFile->FilePath = $strPath;
                                $objFile->Save();
                                NarroLog::LogMessage(1, sprintf(t('Added file "%s"'), $strFileName));
                                NarroImportStatistics::$arrStatistics['Imported files']++;
                            }
                            else {
                                $objFile->Active = 1;
                                if ($intKey == count($arrColMatches[1]) - 1)
                                    $objFile->TypeId = NarroFileType::OpenOfficeSdf;
                                else
                                    $objFile->TypeId = NarroFileType::Folder;
                                $objFile->Modified = date('Y-m-d H:i:s');
                                $objFile->Save();
                            }

                            $arrFiles[$strPath . '/' . $strFileName] = $objFile;
                        }

                        $strPath .= '/' . $strFileName;

                    }
                }
                else {
                    NarroLog::LogMessage(3, $arrColumn[1] . ' failed preg_match');
                }

                if (!$objFile instanceof NarroFile && $this->blnOnlySuggestions)
                    continue;

                $this->AddTranslation($objFile, $strText, $strTextAccKey, $strTranslation, $strTranslationAccKey, $strContext);

                if ($intProcessedSoFar % 10 === 0) {
                    NarroLog::LogMessage(3, sprintf(t("Progress: %s%%"), (int) ceil(($intProcessedSoFar*100)/$intTotalToProcess)));
                }


            }
            fclose($hndFile);
        }
    }
?>
