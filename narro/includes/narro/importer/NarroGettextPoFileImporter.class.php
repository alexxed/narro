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

    class NarroGettextPoFileImporter extends NarroFileImporter {
        protected $strImportDirectory = '/tmp';

        public function ImportProjectArchive($intProjectId, $strFile, $blnValidate = true, $blnCheckEqual = true) {
            NarroLog::LogMessage("Începe importul pentru proiectul " . $intProjectId . " din " . $strFile);
            $this->startTimer();
            $this->intImportedFilesCount = 0;
            $this->intImportedSuggestionsCount = 0;
            $this->intImportedValidationsCount = 0;
            $this->intImportedTextsCount = 0;
            $this->intImportedContextsCount = 0;

            /**
             * set up a working path in the temporary directory
             */
            NarroLog::LogMessage("Se caută un director valid de lucru ...");
            if (file_exists($this->strImportDirectory . '/' . $intProjectId)) {
                $i=0;
                while(file_exists($this->strImportDirectory . '/' . $intProjectId . '-' . $i))
                    $i++;
                $strWorkPath = $this->strImportDirectory . '/' . $intProjectId . '-' . $i;
            }
            else {
                $strWorkPath = $this->strImportDirectory . '/' . $intProjectId;
            }

            $strQuery = sprintf("UPDATE `narro_context` SET `active` = 0 WHERE project_id=%d", $intProjectId);

            if (!$objResult = db_query($strQuery)) {
                NarroLog::LogMessage( __METHOD__ . ':' . __LINE__ . ':db_query failed. $strQuery=' . $strQuery );
                return false;
            }

            $strQuery = sprintf("UPDATE `narro_file` SET `active` = 0 WHERE project_id=%d", $intProjectId);

            if (!$objResult = db_query($strQuery)) {
                NarroLog::LogMessage( __METHOD__ . ':' . __LINE__ . ':db_query failed. $strQuery=' . $strQuery );
                return false;
            }

            /**
             * work with tar.bz2 archives
             */
            if (preg_match('/\.tar.bz2$/', $strFile)) {
                NarroLog::LogMessage("Începe procesarea fişierului " . $strFile);
                if (!mkdir($strWorkPath)) {
                    NarroLog::LogMessage('Nu se poate crea directorul „' .$strWorkPath . '”');
                    return false;
                }
                /*
                 * save current directory
                 */
                $strCurDir = getcwd();
                /**
                 * change to working directory
                 */
                chdir($strWorkPath);
                /**
                 * extract the files
                 */
                exec('tar jxf ' . $strFile, $arrOutput, $retVal);
                if ($retVal != 0) {
                    NarroLog::LogMessage('Eroare la dezarhivare: ' . join("\n", $arrOutput));
                    return false;
                }

                /**
                 * get the file list with complete paths
                 */
                $arrFiles = $this->ListDir($strWorkPath);
                $intTotalFilesToProcess = count($arrFiles);
                NarroLog::LogMessage("Începe procesarea a " . $intTotalFilesToProcess . " fişiere");

                $arrDirectories = array();
                foreach($arrFiles as $intFileNo=>$strFileToImport) {
                    $arrFileParts = split('/', str_replace($strWorkPath, '', $strFileToImport));
                    $strFileName = $arrFileParts[count($arrFileParts)-1];
                    unset($arrFileParts[count($arrFileParts)-1]);
                    unset($arrFileParts[0]);

                    $strPath = '';
                    $intParentId = 0;
                    foreach($arrFileParts as $intPos=>$strDir) {
                        $strPath = $strPath . '/' . $strDir;
                        if (!isset($arrDirectories[$strPath])) {
                            if ($intParentId)
                                $objFile = NarroFile::QuerySingle(QQ::AndCondition(QQ::Equal(QQN::NarroFile()->ProjectId, $intProjectId), QQ::Equal(QQN::NarroFile()->FileName, $strDir), QQ::Equal(QQN::NarroFile()->ParentId, $intParentId)));
                            else
                                $objFile = NarroFile::QuerySingle(QQ::AndCondition(QQ::Equal(QQN::NarroFile()->ProjectId, $intProjectId), QQ::Equal(QQN::NarroFile()->FileName, $strDir), QQ::IsNull(QQN::NarroFile()->ParentId)));

                            if ($objFile instanceof NarroFile) {
                                $this->intSkippedFilesCount++;
                                $objFile->Active = 1;
                                $objFile->Save();
                            }
                            else {
                                /**
                                 * add the file
                                 */
                                $objFile = new NarroFile();
                                $objFile->FileName = $strDir;
                                $objFile->Encoding = 'UTF-8';
                                $objFile->TypeId = NarroFileType::Dosar;
                                if ($intParentId)
                                    $objFile->ParentId = $intParentId;
                                $objFile->ProjectId = $intProjectId;
                                $objFile->Active = 1;
                                $objFile->Save();
                                NarroLog::LogMessageLog(sprintf('S-a adăugat dosarul „%s” din „%s”', $strDir, $strPath));
                                $this->intImportedFilesCount++;
                            }

                            $arrDirectories[$strPath] = $objFile->FileId;
                        }
                        $intParentId = $arrDirectories[$strPath];
                    }

                    /**
                     * ignore files that don't have a .po extension
                     * @todo maybe replace this with a more complex detection ?
                     */
                    if (!strstr($strFileName, '.po'))
                        continue;

                    $intFileType = NarroFileType::PoGettext;

                    $objFile = NarroFile::QuerySingle(
                                    QQ::AndCondition(
                                        QQ::Equal(QQN::NarroFile()->ProjectId, $intProjectId),
                                        QQ::Equal(QQN::NarroFile()->FileName, $strFileName),
                                        QQ::Equal(QQN::NarroFile()->ParentId, $intParentId)
                                    )
                    );

                    if ($objFile instanceof NarroFile) {
                        $objFile->Active = 1;
                        $objFile->Save();
                    }
                    else {
                        /**
                         * add the file
                         */
                        $objFile = new NarroFile();
                        $objFile->FileName = $strFileName;
                        $objFile->TypeId = $intFileType;
                        if ($intParentId)
                            $objFile->ParentId = $intParentId;
                        $objFile->ProjectId = $intProjectId;
                        $objFile->Active = 1;
                        $objFile->Encoding = 'UTF-8';
                        $objFile->Save();
                        NarroLog::LogMessageLog(sprintf('S-a adăugat fișierul „%s” din „%s”', $strFileName, $strPath));
                        $this->intImportedFilesCount++;
                    }

                    $this->ImportFile($intProjectId, $objFile, $strFileToImport, $blnValidate, $blnCheckEqual);

                    if ($intFileNo % 10 === 0)
                        NarroLog::LogMessage("Progres: " . ceil(($intFileNo*100)/$intTotalFilesToProcess) . "%");
                }

                if (isset($i))
                    exec('rm -rf ' . $this->strImportDirectory . '/' . $intProjectId . '-' . $i, $arrOutput, $retVal);
                else
                    exec('rm -rf ' . $this->strImportDirectory . '/' . $intProjectId, $arrOutput, $retVal);
                if ($retVal != 0) {
                    NarroLog::LogMessage('Eroare la curăţarea directorului după import: ' . join("\n", $arrOutput));
                    return false;
                }
            }
            $this->stopTimer();
            NarroLog::LogMessage("Procesarea proiectului cu id „" . $intProjectId . "” s-a încheiat.");
            var_export(NarroImportStatistics::$arrStatistics);
        }

        protected function ListDir($start_dir='.') {

            $files = array();
            if (is_dir($start_dir)) {
                $fh = opendir($start_dir);
                while (($file = readdir($fh)) !== false) {
                    // loop through the files, skipping . and .., and recursing if necessary
                    if (strcmp($file, '.')==0 || strcmp($file, '..')==0) continue;
                        $filepath = $start_dir . '/' . $file;
                    if ( is_dir($filepath) )
                        $files = array_merge($files, $this->ListDir($filepath));
                    else
                        array_push($files, $filepath);
                }
                    closedir($fh);
            } else {
                // false if the function was called with an invalid non-directory argument
                $files = false;
            }
            return $files;
        }

        public function ImportFile($intProjectId, $objFile, $strFileToImport, $blnValidate, $blnCheckEqual) {
            $hndFile = fopen($strFileToImport, 'r');
            if ($hndFile) {
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    // echo "Processing " . $strLine . "<br />";
                    if (strpos($strLine, '# ') === 0) {
                        // echo 'Found translator comment. <br />';
                        $strTranslatorComment = $strLine;
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '# ') === 0)
                                $strTranslatorComment .= $strLine;
                            else
                                break;

                        }
                    }

                    if (strpos($strLine, '#.') === 0) {
                        // echo 'Found extracted comment. <br />';
                        $strExtractedComment = $strLine;
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '#.') === 0)
                                $strExtractedComment .= $strLine;
                            else
                                break;

                        }
                    }

                    if (strpos($strLine, '#:') === 0) {
                        // echo 'Found reference. <br />';
                        $strReference = $strLine;
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '#:') === 0)
                                $strReference .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, '#,') === 0) {
                        // echo 'Found flag. <br />';
                        $strFlag = $strLine;
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '#,') === 0)
                                $strFlag .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, '#| msgctxt') === 0) {
                        // echo 'Found previous context. <br />';
                        $strPreviousContext = $strLine;
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '#| msgctxt') === 0)
                                $strPreviousContext .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, '#| msgid') === 0) {
                        // echo 'Found previous translated string. <br />';
                        $strPreviousUntranslated = $strLine;
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '#| msgid') === 0)
                                $strPreviousUntranslated .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, '#| msgid_plural') === 0) {
                        // echo 'Found previous translated plural string. <br />';
                        $strPreviousUntranslatedPlural = $strLine;
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '#| msgid_plural') === 0)
                                $strPreviousUntranslatedPlural .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgctxt ') === 0) {
                        // echo 'Found string. <br />';
                        preg_match('/msgctxt\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgContext = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgContext .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgid ') === 0) {
                        preg_match('/msgid\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgId = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgId .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgid_plural') === 0) {
                        // echo 'Found plural string. <br />';
                        preg_match('/msgid_plural\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgPluralId = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgPluralId .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgstr ') === 0) {
                        // echo 'Found translation. <br />';
                        preg_match('/msgstr\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgStr = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgStr .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgstr[0]') === 0) {
                        // echo 'Found translation plural 1. <br />';
                        preg_match('/msgstr\[0\]\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgStr0 = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgStr0 .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgstr[1]') === 0) {
                        // echo 'Found translation plural 2. <br />';
                        preg_match('/msgstr\[1\]\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgStr1 = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgStr1 .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgstr[2]') === 0) {
                        // echo 'Found translation plural 3. <br />';
                        preg_match('/msgstr\[2\]\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgStr2 = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndFile)) {
                            $strLine = fgets($hndFile, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgStr2 .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if($strMsgId) {
                        /**
                        echo '$strTranslatorComment: ' . $strTranslatorComment . "<br />";
                        echo '$strExtractedComment: ' . $strExtractedComment . "<br />";
                        echo '$strReference: ' . $strReference . "<br />";
                        echo '$strFlag: ' . $strFlag . "<br />";
                        echo '$strPreviousContext: ' . $strPreviousContext . "<br />";
                        echo '$strPreviousUntranslated: ' . $strPreviousUntranslated . "<br />";
                        echo '$strPreviousUntranslatedPlural: ' . $strPreviousUntranslatedPlural . "<br />";
                        echo '$strMsgContext: ' . $strMsgContext . "<br />";
                        echo '$strMsgId: ' . $strMsgId . "<br />";
                        echo '$strMsgPluralId: ' . $strMsgPluralId . "<br />";
                        echo '$strMsgStr: ' . $strMsgStr . "<br />";
                        echo '$strMsgStr0: ' . $strMsgStr0 . "<br />";
                        echo '$strMsgStr1: ' . $strMsgStr1 . "<br />";
                        echo '$strMsgStr2: ' . $strMsgStr2 . "<br />";
                        echo '<hr />';
                        */

                        /**
                         * if the string is marked fuzzy, don't import the translation and delete fuzzy flag
                         */
                        if (strstr($strFlag, ', fuzzy')) {
                            if (!is_null($strMsgStr)) $strMsgStr = '';

                            if (!is_null($strMsgStr0)) $strMsgStr0 = '';
                            if (!is_null($strMsgStr1)) $strMsgStr1 = '';
                            if (!is_null($strMsgStr2)) $strMsgStr2 = '';

                            $strFlag = str_replace(', fuzzy', '', $strFlag);
                            /**
                             * if no other flags are found, just empty the variable
                             */
                            if (strlen(trim($strFlag)) < 4) $strFlag = null;
                        }

                        $strContext = $strTranslatorComment . $strExtractedComment . $strReference . $strFlag . $strPreviousContext . $strPreviousUntranslated . $strPreviousUntranslatedPlural . $strMsgContext;

                        if (!is_null($strMsgId)) $strMsgId = str_replace('\"', '"', $strMsgId);
                        if (!is_null($strMsgStr)) $strMsgStr = str_replace('\"', '"', $strMsgStr);

                        if (!is_null($strMsgPluralId)) $strMsgPluralId = str_replace('\"', '"', $strMsgPluralId);
                        if (!is_null($strMsgStr0)) $strMsgStr0 = str_replace('\"', '"', $strMsgStr0);
                        if (!is_null($strMsgStr1)) $strMsgStr1 = str_replace('\"', '"', $strMsgStr1);
                        if (!is_null($strMsgStr2)) $strMsgStr2 = str_replace('\"', '"', $strMsgStr2);

                        /**
                         * if it's not a plural, just add msgid and msgstr
                         */
                        if (is_null($strMsgPluralId)) {
                                $this->AddTranslation($intProjectId, $objFile, $strMsgId, $strMsgStr, $strContext, null, $blnValidate, $blnCheckEqual);
                        }
                        else {
                            /**
                             * if it's a plural, add the pluralid with all the msgstr's available
                             * currently limited to 3 (so 3 plural forms)
                             * the first one is added with msgid/msgstr[0] (this is the singular)
                             * the next ones (currently 2) are added with plural id, so in fact they will be tied to the same text
                             */
                            if (!is_null($strMsgStr0))
                                $this->AddTranslation($intProjectId, $objFile, $strMsgId, $strMsgStr0, $strContext . "\nThis text has plurals.", 0, $blnValidate, $blnCheckEqual);
                            if (!is_null($strMsgStr1))
                                $this->AddTranslation($intProjectId, $objFile, $strMsgPluralId, $strMsgStr1, $strContext . "\nThis is plural form 1 for the text \"$strMsgId\".", 1, $blnValidate, $blnCheckEqual);
                            if (!is_null($strMsgStr2))
                                $this->AddTranslation($intProjectId, $objFile, $strMsgPluralId, $strMsgStr2, $strContext . "\nThis is plural form 2 for the text \"$strMsgId\".", 2, $blnValidate, $blnCheckEqual);
                        }
                    }

                    $strTranslatorComment = null;
                    $strExtractedComment = null;
                    $strReference = null;
                    $strFlag = null;
                    $strPreviousUntranslated = null;
                    $strPreviousContext = null;
                    $strPreviousUntranslatedPlural = null;
                    $strMsgContext = null;
                    $strMsgId = null;
                    $strMsgPluralId = null;
                    $strMsgStr = null;
                    $strMsgStr0 = null;
                    $strMsgStr1 = null;
                    $strMsgStr2 = null;

                }
            }
            else {
                NarroLog::LogMessage('Cannot open file: ' . $strFileToImport );
            }
        }
    }
?>
