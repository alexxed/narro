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
                NarroLog::LogMessage(3, __LINE__ . ':' . sprintf('Nu se poate deschide fișierul „%s” pentru citire', $strTemplateFile));
                return false;
            }

            $hndTranslatedFile = fopen($strTranslatedFile, 'w');
            if (!$hndTranslatedFile) {
                NarroLog::LogMessage(3, __LINE__ . ':' . sprintf('Nu se poate deschide fișierul „%s” pentru citire', $strTranslatedFile));
                return false;
            }

            $intTotalToProcess = count(file($strTemplateFile));

            NarroLog::LogMessage(1, __LINE__ . ':' . sprintf('Începe procesarea fișierului „%s” (%d texte), rezultatul se va scrie în „%s”.', $strTemplateFile, $this->intTotalToProcess, $strTranslatedFile));

            /**
             * Pentru început, se iau doar textele care au sugestii valide
             */
            $strQuery = sprintf("SELECT `suggestion_value`, `text_value`, `context` FROM narro_context_info ci, narro_context c, narro_suggestion s, narro_text t WHERE c.active=1 AND c.text_id=t.text_id AND ci.valid_suggestion_id=s.suggestion_id AND c.project_id=%d AND c.context_id=ci.context_id AND ci.language_id=%d", $this->objProject->ProjectId, $this->objTargetLanguage->LanguageId);

            if (!$objDbResult = $objDatabase->Query($strQuery)) {
                NarroLog::LogMessage(3, __METHOD__ . ':' . __LINE__ . ':db_query failed. $strQuery=' . $strQuery);
                return false;
            }

            if ($objDbResult->CountRows()) {
                while($arrDbRow = $objDbResult->FetchArray()) {
                    /**
                     * Poate riscant, dar fiindcă contextul e uneori foarte mare, cheia este md5 pe valoarea contextului.
                     */
                    if (isset($arrFile[md5($arrDbRow['context'])]) && $arrDbRow['suggestion_value'] != $arrFile[md5($arrDbRow['context'])]) {
                        NarroLog::LogMessage(3, __LINE__ . ':' . sprintf('Atenție, md5("%s") există deja ca cheie și are valoarea „%s”. Valoarea care ar trebui pusă este „%s”!', $arrDbRow['context'], $arrFile[md5($arrDbRow['context'])], $arrDbRow['suggestion_value']));
                    }
                    $arrFile[md5($arrDbRow['context'])] = $arrDbRow['suggestion_value'];
                }
            }
            else {
                NarroLog::LogMessage(3, __LINE__ . ':' . sprintf('Eșec la apelul db_num_rows pe interogarea „%s”', $strQuery));
                return false;
            }

            $intValidSuggestions = count($arrFile);
            NarroLog::LogMessage(1, __LINE__ . ':' . sprintf('S-au găsit %d texte cu sugestii validate', count($arrFile)));

            /**
             * Apoi, se caută în textele care au sugestii dar niciuna validată, și se ia ultima adăugată
             * @todo Schimbă astfel încât să se ia sugestia cea mai votată
             */

//            $strQuery = sprintf("SELECT `suggestion_value`, `text_value`, `context` FROM `narro_context` c, narro_suggestion s, narro_text t WHERE c.valid_suggestion_id IS NULL AND c.text_id=t.text_id AND c.text_id=s.text_id AND c.project_id=%d ORDER BY c.context_id ASC, s.suggestion_id ASC", $this->objProject->ProjectId);
//
//            if (!$objResult = db_query($strQuery)) {
//                NarroLog::LogMessage(3,  __METHOD__ . ':' . __LINE__ . ':db_query failed. $strQuery=' . $strQuery );
//                return false;
//            }
//
//            if (db_num_rows($objResult)) {
//                while($arrDbRow = db_fetch_array($objResult)) {
//                    /**
//                     * Poate riscant, dar fiindcă contextul e uneori foarte mare, cheia este md5 pe valoarea contextului.
//                     */
//                    $arrFile[md5($arrDbRow['context'])]= $arrDbRow['suggestion_value'];
//                }
//            }
//            else {
//                NarroLog::LogMessage(3, __LINE__ . ':' . sprintf('Eșec la apelul db_num_rows pe interogarea „%s”', $strQuery));
//                return false;
//            }

            NarroLog::LogMessage(1, __LINE__ . ':' . sprintf('S-au găsit %d texte sugestii nevalidate', count($arrFile) - $intValidSuggestions));


            NarroLog::LogMessage(1, __LINE__ . ':' . sprintf('În total, s-au găsit %d traduceri', count($arrFile)));

            NarroLog::LogMessage(1, __LINE__ . ':' . 'Se începe scrierea fișierului pe baza sugestiilor găsite');

            $intFileLineNr=0;
            $this->intTextsNotTranslated = 0;
            $this->intTextsTranslated = 0;

            if (file_exists('export.status'))
                throw new Exception(sprintf(t('An export process is already running in the directory "%s" although no pid is recorded. Status is: "%s"'), file_get_contents('export.status')));

            $hndStatusFile = fopen('import.status', 'w');
            if (!$hndStatusFile)
                throw new Exception(sprintf(t('Cannot create %s in %s.'), 'import.status', getcwd()));
            fputs($hndStatusFile, '0');

            while(!feof($hndTemplateFile)) {
                $strFileLine = fgets($hndTemplateFile, 4096);
                $intFileLineNr++;

                $arrColumn = preg_split('/\t/', $strFileLine);
                if (count($arrColumn) < 10) {
                    NarroLog::LogMessage(2, __LINE__ . ':' . sprintf('S-a sărit peste „%s”, pentru că împărțirea cu tab dă mai puțin de 10 coloane.', $strFileLine));
                    $this->intSkipped;
                    continue;
                }

                $arrTranslatedColumn = $arrColumn;

                $strLangCode = $arrColumn[9];
                $strText = $arrColumn[10];
                $strContext = trim($arrColumn[0] . "\n" . $arrColumn[1] . "\n" . $arrColumn[3] . "\n" . $arrColumn[4]);

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
                    $this->intTextsTranslated++;
                }
                else {
                    $this->intTextsNotTranslated++;
                    //NarroLog::LogMessage(2, __LINE__ . ':' . sprintf('S-a sărit peste „%s” („%s”), pentru că nu e tradus.', $strContext, $strText));
                    continue;
                }

                preg_match_all('/\\\\"/', $strText, $arrEscOrigMatches);
                preg_match_all('/\\\\"/', $arrFile[md5($strContext)], $arrEscTransMatches);

                if (isset($arrEscOrigMatches[0])) {
                    if (!isset($arrEscTransMatches[0])) {
                        NarroLog::LogMessage(3, __LINE__ . ':' . sprintf('Atenție! Textul original „%s” are niște ghilimele dar textul tradus „%s” nu le are.', $strText, $arrFile[md5($strContext)]));
                        continue;
                    }

                    if (count($arrEscOrigMatches[0]) != count($arrEscTransMatches[0])) {
                        NarroLog::LogMessage(3, __LINE__ . ':' . sprintf('Atenție! Textul original „%s” are niște ghilimele dar textul tradus „%s” are mai puține sau mai multe.', $strText, $arrFile[md5($strContext)]));
                        continue;
                    }
                }

                fwrite($hndTranslatedFile, join("\t", $arrColumn));
                fwrite($hndTranslatedFile, join("\t", $arrTranslatedColumn));

                $intProcessedSoFar++;
                ftruncate($hndStatusFile, 0);
                fputs($hndStatusFile, (int) ceil(($intProcessedSoFar*100)/$intTotalToProcess));


            }

            fclose($hndTemplateFile);
            fclose($hndTranslatedFile);

            fclose($hndStatusFile);
            if (file_exists('import.status'))
                unlink('import.status');

            NarroLog::LogMessage(1, sprintf('Texte din fișierul model peste care s-a sărit: %d', $this->intSkipped));
            NarroLog::LogMessage(1, sprintf('Texte traduse: %d', $this->intTextsTranslated));
            NarroLog::LogMessage(1, sprintf('Texte fără traduceri: %d', $this->intTextsNotTranslated));
            NarroLog::LogMessage(1, sprintf('Timp trecut: %s', $this->strElapsedTime));
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
             * initialize status file
             */
            if (file_exists('import.status'))
                throw new Exception(sprintf(t('An export process is already running in the directory "%s" although no pid is recorded. Status is: "%s"'), file_get_contents('import.status')));

            $hndStatusFile = fopen('import.status', 'w');
            if (!$hndStatusFile)
                throw new Exception(sprintf(t('Cannot create %s in %s.'), 'import.status', getcwd()));
            fputs($hndStatusFile, '0');

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

                $strContext = $arrColumn[0] . "\n" . $arrColumn[1] . "\n" . $arrColumn[3] . "\n" . $arrColumn[4];

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



//                if (!is_null($strTranslation)) {
//                    if ($strTextAccKey) {
//                        if (preg_match('/~(\w)/', $strTranslation, $arrTranslationAccMatches)) {
//                            $strTranslationAccKey = $arrTranslationAccMatches[1];
//                        }
//                        elseif ($intPos = mb_stripos($strTranslation, $strTextAccKey)) {
//                            if (mb_strtolower($strTextAccKey) == mb_substr($strTranslation, $intPos, 1))
//                                $strTranslationAccKey = mb_strtolower($strTextAccKey);
//                            else
//                                $strTranslationAccKey = mb_strtoupper($strTextAccKey);
//                        }
//                        elseif (preg_match('/\w/', $strTranslation, $arrTranslationAccMatches))
//                            $strTranslationAccKey = $arrTranslationAccMatches[0];
//                        else
//                            $strTranslationAccKey = null;
//
//                        $strTranslation = mb_ereg_replace('~' . $strTranslationAccKey, $strTranslationAccKey, $strTranslation);
//                    }
//                }
//                else {
//                    $strTranslationAccKey = null;
//
//                    if (preg_match('/~(\w)/', $strText, $arrTextAccMatches)) {
//                        $strTextAccKey = $arrTextAccMatches[1];
//                        $strText = mb_ereg_replace('~' . $strTextAccKey, $strTextAccKey, $strText);
//                    }
//                    else {
//                        $strTextAccKey = null;
//                    }
//                }

                $this->AddTranslation($objFile, $strText, $strTextAccKey, $strTranslation, $strTranslationAccKey, $strContext);

                ftruncate($hndStatusFile, 0);
                fputs($hndStatusFile, (int) ceil(($intProcessedSoFar*100)/$intTotalToProcess));

                if ($intProcessedSoFar % 10 === 0) {
                    NarroLog::LogMessage(3, sprintf(t("Progress: %s%%"), (int) ceil(($intProcessedSoFar*100)/$intTotalToProcess)));
                }


            }
            fclose($hndFile);

            fclose($hndStatusFile);
            if (file_exists('import.status'))
                unlink('import.status');

        }
    }
?>
