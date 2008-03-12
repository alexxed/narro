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

    class NarroOooFileImporter extends NarroFileImporter {

        public function ExportSdfFile($intProjectId, $strTemplateFile, $strOutputFile) {
            $objDatabase = QApplication::$Database[1];

            $this->startTimer();

            $hndTemplateFile = fopen($strTemplateFile, 'r');
            if (!$hndTemplateFile) {
                $this->Output(3, __LINE__ . ':' . sprintf('Nu se poate deschide fișierul „%s” pentru citire', $strTemplateFile));
                return false;
            }

            $hndTranslatedFile = fopen($strOutputFile, 'w');
            if (!$hndTranslatedFile) {
                $this->Output(3, __LINE__ . ':' . sprintf('Nu se poate deschide fișierul „%s” pentru citire', $strOutputFile));
                return false;
            }

            $this->intTotalToProcess = count(file($strTemplateFile));

            $this->Output(1, __LINE__ . ':' . sprintf('Începe procesarea fișierului „%s” (%d texte), rezultatul se va scrie în „%s”.', $strTemplateFile, $this->intTotalToProcess, $strOutputFile));

            /**
             * Pentru început, se iau doar textele care au sugestii valide
             */
            $strQuery = sprintf("SELECT `suggestion_value`, `text_value`, `context` FROM `narro_context` c, narro_suggestion s, narro_text t WHERE c.active=1 AND c.text_id=t.text_id AND c.valid_suggestion_id=s.suggestion_id AND c.project_id=%d", $intProjectId);

            if (!$objDbResult = $objDatabase->Query($strQuery)) {
                $this->Output(3, __METHOD__ . ':' . __LINE__ . ':db_query failed. $strQuery=' . $strQuery);
                return false;
            }

            if ($objDbResult->CountRows()) {
                while($arrDbRow = $objDbResult->FetchArray()) {
                    /**
                     * Poate riscant, dar fiindcă contextul e uneori foarte mare, cheia este md5 pe valoarea contextului.
                     */
                    if (isset($arrFile[md5($arrDbRow['context'])]) && $arrDbRow['suggestion_value'] != $arrFile[md5($arrDbRow['context'])]) {
                        $this->Output(3, __LINE__ . ':' . sprintf('Atenție, md5("%s") există deja ca cheie și are valoarea „%s”. Valoarea care ar trebui pusă este „%s”!', $arrDbRow['context'], $arrFile[md5($arrDbRow['context'])], $arrDbRow['suggestion_value']));
                    }
                    $arrFile[md5($arrDbRow['context'])] = $arrDbRow['suggestion_value'];
                }
            }
            else {
                $this->Output(3, __LINE__ . ':' . sprintf('Eșec la apelul db_num_rows pe interogarea „%s”', $strQuery));
                return false;
            }

            $intValidSuggestions = count($arrFile);
            $this->Output(1, __LINE__ . ':' . sprintf('S-au găsit %d texte cu sugestii validate', count($arrFile)));

            /**
             * Apoi, se caută în textele care au sugestii dar niciuna validată, și se ia ultima adăugată
             * @todo Schimbă astfel încât să se ia sugestia cea mai votată
             */

//            $strQuery = sprintf("SELECT `suggestion_value`, `text_value`, `context` FROM `narro_context` c, narro_suggestion s, narro_text t WHERE c.valid_suggestion_id IS NULL AND c.text_id=t.text_id AND c.text_id=s.text_id AND c.project_id=%d ORDER BY c.context_id ASC, s.suggestion_id ASC", $intProjectId);
//
//            if (!$objResult = db_query($strQuery)) {
//                $this->Output(3,  __METHOD__ . ':' . __LINE__ . ':db_query failed. $strQuery=' . $strQuery );
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
//                $this->Output(3, __LINE__ . ':' . sprintf('Eșec la apelul db_num_rows pe interogarea „%s”', $strQuery));
//                return false;
//            }

            $this->Output(1, __LINE__ . ':' . sprintf('S-au găsit %d texte sugestii nevalidate', count($arrFile) - $intValidSuggestions));


            $this->Output(1, __LINE__ . ':' . sprintf('În total, s-au găsit %d traduceri', count($arrFile)));

            $this->Output(1, __LINE__ . ':' . 'Se începe scrierea fișierului pe baza sugestiilor găsite');

            $intFileLineNr=0;
            $this->intTextsNotTranslated = 0;
            $this->intTextsTranslated = 0;
            while(!feof($hndTemplateFile)) {
                $strFileLine = fgets($hndTemplateFile, 4096);
                $intFileLineNr++;

                $arrColumn = preg_split('/\t/', $strFileLine);
                if (count($arrColumn) < 10) {
                    $this->Output(2, __LINE__ . ':' . sprintf('S-a sărit peste „%s”, pentru că împărțirea cu tab dă mai puțin de 10 coloane.', $strFileLine));
                    $this->intSkipped;
                    continue;
                }

                $arrTranslatedColumn = $arrColumn;

                $strLangCode = $arrColumn[9];
                $strText = $arrColumn[10];
                $strContext = trim($arrColumn[0] . "\n" . $arrColumn[1] . "\n" . $arrColumn[3] . "\n" . $arrColumn[4] . "\n" . $arrColumn[5]);

                $arrColumn[8] = 0;
                $arrTranslatedColumn[8] = 0;
                $arrTranslatedColumn[9] = 'ro';
                if (isset($arrFile[md5($strContext)])) {
                    $arrTranslatedColumn[10] = str_replace(array("\n", "\r"), array("",""), QApplication::ConvertToComma($arrFile[md5($strContext)]));
                    $this->intTextsTranslated++;
                }
                else {
                    $this->intTextsNotTranslated++;
                    //$this->Output(2, __LINE__ . ':' . sprintf('S-a sărit peste „%s” („%s”), pentru că nu e tradus.', $strContext, $strText));
                    continue;
                }

                preg_match_all('/\\\\"/', $strText, $arrEscOrigMatches);
                preg_match_all('/\\\\"/', $arrFile[md5($strContext)], $arrEscTransMatches);

                if (isset($arrEscOrigMatches[0])) {
                    if (!isset($arrEscTransMatches[0])) {
                        $this->Output(3, __LINE__ . ':' . sprintf('Atenție! Textul original „%s” are niște ghilimele dar textul tradus „%s” nu le are.', $strText, $arrFile[md5($strContext)]));
                        continue;
                    }

                    if (count($arrEscOrigMatches[0]) != count($arrEscTransMatches[0])) {
                        $this->Output(3, __LINE__ . ':' . sprintf('Atenție! Textul original „%s” are niște ghilimele dar textul tradus „%s” are mai puține sau mai multe.', $strText, $arrFile[md5($strContext)]));
                        continue;
                    }
                }

                fwrite($hndTranslatedFile, join("\t", $arrColumn));
                fwrite($hndTranslatedFile, join("\t", $arrTranslatedColumn));

                $this->intProcessedSoFar = $intFileLineNr;
                if ($this->intProcessedSoFar % 1000 == 0)
                    $this->Output(1, __LINE__ . ':' . 'Progres: ' . floor(($this->intProcessedSoFar * 100)/$this->intTotalToProcess) . "%");

            }

            fclose($hndTemplateFile);
            fclose($hndTranslatedFile);
            $this->stopTimer();
            $this->Output(1, sprintf('Texte din fișierul model peste care s-a sărit: %d', $this->intSkipped));
            $this->Output(1, sprintf('Texte traduse: %d', $this->intTextsTranslated));
            $this->Output(1, sprintf('Texte fără traduceri: %d', $this->intTextsNotTranslated));
            $this->Output(1, sprintf('Timp trecut: %s', $this->strElapsedTime));
        }

        public function ImportSdfFile($intProjectId, $strTemplateLang, $strTranslationLang = null, $strTemplateFile, $blnCheckEqual = false, $blnValidate = false, $blnOnlySuggestions = false) {
            $objDatabase = QApplication::$Database[1];
            $this->startTimer();
            $hndFile = fopen($strTemplateFile, 'r');

            if (!$hndFile) {
                $this->Output(3, sprintf(t('Cannot open input file "%s" for reading.'), $strTemplateFile));
                return false;
            }

            $this->intTotalToProcess = count(file($strTemplateFile));

            $objDatabase->NonQuery(sprintf("UPDATE `narro_context` SET `active` = 0 WHERE project_id=%d", $intProjectId));
            $objDatabase->NonQuery(sprintf("UPDATE `narro_file` SET `active` = 0 WHERE project_id=%d", $intProjectId));

            $intSkippedTexts = 0;
            $intSkippedContexts = 0;
            $intAddedTexts = 0;
            $intAddedContexts = 0;
            $intLastPercent = -1;
            $arrFiles = array();

            while(!feof($hndFile)) {
                $strFileLine = fgets($hndFile, 16384);
                $this->intProcessedSoFar++;

                $arrColumn = preg_split('/\t/', $strFileLine);

                $strContext = $arrColumn[0] . "\n" . $arrColumn[1] . "\n" . $arrColumn[3] . "\n" . $arrColumn[4] . "\n" . $arrColumn[5];

                $strLangCode = $arrColumn[9];

                if (!$strTranslationLang && $strLangCode != $strTemplateLang) {
                    $this->Output(2, sprintf(t('Skipped line "%s" because the language code found "%s" does not match the one passed in the arguments, "%s"'), $strFileLine, $strLangCode, $strTemplateLang));
                    continue;
                }

                $strText = $arrColumn[10];

                if ($strTranslationLang) {
                    $strFileTranslationLine = fgets($hndFile, 16384);
                    $arrTranslatedColumn = preg_split('/\t/', $strFileTranslationLine);

                    if (
                        $arrColumn[0] ==  $arrTranslatedColumn[0] &&
                        $arrColumn[1] ==  $arrTranslatedColumn[1] &&
                        $arrColumn[3] ==  $arrTranslatedColumn[3] &&
                        $arrColumn[4] ==  $arrTranslatedColumn[4] &&
                        $arrColumn[5] ==  $arrTranslatedColumn[5] &&
                        $strTranslationLang == $arrTranslatedColumn[9] )
                        $strTranslation = $arrTranslatedColumn[10];
                    else {
                        $this->Output(2, sprintf(t('Original line and translation line do not match. Here is the condition checked: "%s". Skipping.'), $arrColumn[0] .'=='.  $arrTranslatedColumn[0] . " && " .
                        $arrColumn[1] .'=='.  $arrTranslatedColumn[1] . " && " .
                        $arrColumn[3] .'=='.  $arrTranslatedColumn[3] . " && " .
                        $arrColumn[4] .'=='.  $arrTranslatedColumn[4] . " && " .
                        $arrColumn[5] .'=='.  $arrTranslatedColumn[5] . " && " .
                        $strTranslationLang .'==' . $arrTranslatedColumn[9]));
                        continue;
                    }
                }

                $strDate = $arrColumn[14];

                if (!isset($strDate))
                    continue;

                if (!preg_match('/[0-9]{4,4}[\-]?[0-9]{2,2}[\-]?[0-9]{2,2}\s[0-9]{2,2}:[0-9]{2,2}:[0-9]{2,2}/', $strDate)) {
                    $this->Output(2, var_export($strDate,true) . ' not good. Count: ' . count($arrColumn) . var_export($arrColumn, true));
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
                    //error_log(var_export($arrColMatches[1],true));

                    $strPath = '';
                    /**
                     * $arrColMatches[1] contains dialogs and macrosecurity in our case
                     * we'll go over them and see if the files exists or need to be updated
                     */
                    foreach($arrColMatches[1] as $intKey=>$strFileName) {
                        //error_log('$strPath = ' . $strPath . ', $strFileName = ' . $strFileName);
                        if (!isset($arrFiles[$strPath . '/' . $strFileName])) {
                            //error_log($strPath . '/' . $strFileName . ' is not set');
                            //error_log($arrFiles[$strPath]);
                            //error_log(var_export($arrFiles[$strPath] instanceof NarroFile,true));
                            if ($arrFiles[$strPath] instanceof NarroFile) {
                                $objFile = NarroFile::QuerySingle(QQ::AndCondition(QQ::Equal(QQN::NarroFile()->ProjectId, $intProjectId), QQ::Equal(QQN::NarroFile()->FileName, $strFileName), QQ::Equal(QQN::NarroFile()->ParentId, $arrFiles[$strPath]->FileId)));
                                //error_log('found child ' . $strFileName);
                                //error_log($objFile);
                            }
                            else {
                                $objFile = NarroFile::QuerySingle(QQ::AndCondition(QQ::Equal(QQN::NarroFile()->ProjectId, $intProjectId), QQ::Equal(QQN::NarroFile()->FileName, $strFileName), QQ::IsNull(QQN::NarroFile()->ParentId)));
                                //error_log('found parrent' . $strFileName);
                            }

                            if (!$objFile instanceof NarroFile) {
                                if ($blnOnlySuggestions)
                                    continue;
                                $objFile = new NarroFile();
                                $objFile->FileName = $strFileName;
                                if ($intKey == count($arrColMatches[1]) - 1)
                                    $objFile->TypeId = NarroFileType::SdfOpenOffice;
                                else
                                    $objFile->TypeId = NarroFileType::Dosar;
                                $objFile->ProjectId = $intProjectId;
                                if ($strPath != '' && isset($arrFiles[$strPath])) {
                                    $objFile->ParentId = $arrFiles[$strPath]->FileId;
                                }
                                $objFile->Active = 1;
                                $objFile->Encoding = 'UTF-8';
                                $objFile->Save();
                                $this->Output(1, sprintf('S-a adăugat pseudo fișierul „%s” cu calea „%s”', $strFileName, $strPath));
                                $this->intImportedFilesCount++;
                            }
                            else {
                                $objFile->Active = 1;
                                if ($intKey == count($arrColMatches[1]) - 1)
                                    $objFile->TypeId = NarroFileType::SdfOpenOffice;
                                else
                                    $objFile->TypeId = NarroFileType::Dosar;
                                $objFile->Save();
                            }


                            //error_log($strPath);
                            $arrFiles[$strPath . '/' . $strFileName] = $objFile;
                            //error_log($strPath . '/' . $strFileName . ' is now set');
                        }
                        //error_log($strPath . '/' . $strFileName . ' is already added.');

                        $strPath .= '/' . $strFileName;

                    }
                }
                else {
                    $this->Output(3, $arrColumn[1] . ' faile preg_match');
                }

                if (!$objFile instanceof NarroFile && $blnOnlySuggestions)
                    continue;

                $this->AddTranslation($intProjectId, $arrFiles[$strPath], $strText, $strTranslation, $strContext, null, $blnCheckEqual, $blnValidate, $blnOnlySuggestions);

                if ($this->intProcessedSoFar % 100 == 0 && floor(($this->intProcessedSoFar * 100)/$this->intTotalToProcess) != $intLastPercent) {
                    $this->Output(1, 'Progres: ' . floor(($this->intProcessedSoFar * 100)/$this->intTotalToProcess) . "%");
                    $intLastPercent = floor(($this->intProcessedSoFar * 100)/$this->intTotalToProcess);
                }

            }
            fclose($hndFile);
            $this->stopTimer();
            $this->Output(1, 'Import terminat.');
            $this->Output(1, var_export($this->arrStatistics,true));
        }
    }
?>
