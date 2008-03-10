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

    class NarroMozillaFileImporter extends NarroFileImporter {

        public function ImportProjectArchive($strFile) {

            $this->Output(1, sprintf(QApplication::Translate('Starting import for the project %s from the file %s'), $this->objProject->ProjectName, $strFile));
            $this->startTimer();

            /**
             * work with tar.bz2 archives
             */
            if (preg_match('/\.tar.bz2$/', $strFile)) {
                $this->Output(1, sprintf(__t('Got an archive, processing file "%s"'), $strFile));
                $strWorkPath = sprintf('%s/%s/%s', __IMPORT_PATH__, $this->objUser->UserId, $this->objProject->ProjectName);
                $strUserPath = sprintf('%s/%s', __IMPORT_PATH__, $this->objUser->UserId);
                if (!file_exists($strUserPath)) {
                    if (!mkdir($strUserPath)) {
                        $this->Output(3, sprintf(__t('Could not create import directory "%s" for the user "%s"'), $strUserPath, $this->objUser->Username));
                        return false;
                    }
                }

                if (!file_exists($strWorkPath) && !mkdir($strWorkPath)) {
                    $this->Output(3, sprintf(__t('Could not create import directory "%s" for the project "%s"'), $strWorkPath, $this->objProject->ProjectName));
                    return false;
                }

                exec('rm -rf ' . $strWorkPath . '/*');

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
                exec(sprintf('tar jxf %s', $strFile), $arrOutput, $retVal);
                if ($retVal != 0) {
                    $this->Output(3, sprintf(__t('Error untaring: %s'), join("\n", $arrOutput)));
                    return false;
                }

                $this->ImportFromDirectory($strWorkPath);
            }
            else
                $this->ImportFromDirectory($strFile);

            $this->stopTimer();
        }

        public function ImportFromDirectory($strDirectory) {
            $objDatabase = QApplication::$Database[1];

            /**
             * get the file list with complete paths
             * the file list is retrieved from en-US
             */
            $arrFiles = $this->ListDir($strDirectory . '/en-US');
            $intTotalFilesToProcess = count($arrFiles);

            $this->Output(1, sprintf(QApplication::Translate('Starting to process %d files using directory %s'), $intTotalFilesToProcess, $strDirectory));

            $strQuery = sprintf("UPDATE `narro_file` SET `active` = 0 WHERE project_id=%d", $this->objProject->ProjectId);
            try {
                $objDatabase->NonQuery($strQuery);
            }catch (Exception $objEx) {
                $this->Output(3, sprintf(__t('Error while executing sql query in file %s, line %d: %s'), __FILE__, __LINE__ - 4, $objEx->getMessage()));
                return false;
            }

            $strQuery = sprintf("UPDATE `narro_context` SET `active` = 0 WHERE project_id=%d", $this->objProject->ProjectId);
            try {
                $objDatabase->NonQuery($strQuery);
            }catch (Exception $objEx) {
                $this->Output(3, sprintf(__t('Error while executing sql query in file %s, line %d: %s'), __FILE__, __LINE__ - 4, $objEx->getMessage()));
                return false;
            }

            $arrDirectories = array();
            foreach($arrFiles as $intFileNo=>$strFileToImport) {
                $arrFileParts = split('/', str_replace($strDirectory . '/en-US', '', $strFileToImport));
                $strFileName = $arrFileParts[count($arrFileParts)-1];

                unset($arrFileParts[count($arrFileParts)-1]);
                unset($arrFileParts[0]);

                $strPath = '';
                $intParentId = 0;
                foreach($arrFileParts as $intPos=>$strDir) {
                    $strPath = $strPath . '/' . $strDir;
                    if (!isset($arrDirectories[$strPath])) {
                        if ($intParentId) {
                            $objFile = NarroFile::QuerySingle(
                                            QQ::AndCondition(
                                                QQ::Equal(
                                                    QQN::NarroFile()->ProjectId,
                                                    $this->objProject->ProjectId
                                                ),
                                                QQ::Equal(
                                                    QQN::NarroFile()->FileName,
                                                    $strDir
                                                ),
                                                QQ::Equal(
                                                    QQN::NarroFile()->ParentId,
                                                    $intParentId
                                                )
                                            )
                            );
                        }
                        else {
                            $objFile = NarroFile::QuerySingle(
                                            QQ::AndCondition(
                                                QQ::Equal(
                                                    QQN::NarroFile()->ProjectId,
                                                    $this->objProject->ProjectId
                                                ),
                                                QQ::Equal(
                                                    QQN::NarroFile()->FileName,
                                                    $strDir
                                                ),
                                                QQ::IsNull(QQN::NarroFile()->ParentId)
                                            )
                            );
                        }

                        if ($objFile instanceof NarroFile) {
                            $this->arrStatistics['Kept folders']++;
                            $objFile->Active = 1;
                            $objFile->ContextCount = 0;
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
                            $objFile->ProjectId = $this->objProject->ProjectId;
                            $objFile->ContextCount = 0;
                            $objFile->Active = 1;
                            $objFile->Save();
                            $this->Output(1, sprintf(QApplication::Translate('Added folder "%s" from "%s"'), $strDir, $strPath));
                            $this->arrStatistics['Imported folders']++;
                        }
                        $arrDirectories[$strPath] = $objFile->FileId;
                    }
                    $intParentId = $arrDirectories[$strPath];
                }

                /**
                 * import the file
                 */
                if (!$intFileType = $this->GetFileType($strFileName))
                    continue;

                $objFile = NarroFile::QuerySingle(
                                QQ::AndCondition(
                                    QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId),
                                    QQ::Equal(QQN::NarroFile()->FileName, $strFileName),
                                    QQ::Equal(QQN::NarroFile()->ParentId, $intParentId)
                                )
                );

                if ($objFile instanceof NarroFile) {
                    $objFile->Active = 1;
                    $objFile->TypeId = $intFileType;
                    $objFile->Save();
                    $this->arrStatistics['Kept files']++;
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
                    $objFile->ProjectId = $this->objProject->ProjectId;
                    $objFile->Active = 1;
                    $objFile->Encoding = 'UTF-8';
                    $objFile->Save();
                    $this->Output(1, sprintf(QApplication::Translate('Added file "%s" from "%s"'), $strFileName, $strPath));
                    $this->arrStatistics['Imported files']++;
                }

                $strTranslatedFileToImport = str_replace($strDirectory . '/en-US', $strDirectory . '/' . $this->objLanguage->LanguageCode, $strFileToImport);
//error_log('str_replace(' . $strDirectory . '/en-US' . ', ' . $strDirectory . '/' . $this->objLanguage->LanguageCode . ', ' .$strFileToImport);
                if (file_exists($strTranslatedFileToImport))
                    $this->ImportFile($objFile, $strFileToImport, $strTranslatedFileToImport);
                else {
                    // it's ok, equal strings won't be imported
                    $this->ImportFile($objFile, $strFileToImport);
                }

                if ($intFileNo % 10 === 0)
                    $this->Output(1, sprintf(QApplication::Translate("Progress: %s%%"), ceil(($intFileNo*100)/$intTotalFilesToProcess)));
            }

        }

        public function ImportFile ($objFile, $strTemplateFile, $strTranslatedFile = false) {
            if (!$objFile instanceof NarroFile)
                return false;

            switch($objFile->TypeId) {
                case NarroFileType::DtdMozilla:
                        return $this->ImportDtdFile($objFile, $strTemplateFile, $strTranslatedFile);
                case NarroFileType::IniProperties:
                        return $this->ImportPropertiesFile($objFile, $strTemplateFile, $strTranslatedFile);
                default:
                        return false;
            }

        }

        public function ImportPropertiesFile($objFile, $strTemplateFile, $strTranslatedFile) {
            if ($strTranslatedFile)
                $strFileContents = file_get_contents($strTranslatedFile);
            $strTemplateContents = file_get_contents($strTemplateFile);

            if (!$strFileContents || !$strTemplateContents)
                return false;

            $strFileContents = str_replace("\\\n", '\\n', $strFileContents);
            $strTemplateContents = str_replace("\\\n", '\\\n', $strTemplateContents);

            $arrFileContents = split("\n", $strFileContents);
            $arrTemplateContents = split("\n", $strTemplateContents);

            foreach($arrFileContents as $intPos=>$strLine) {
                if (preg_match('/^\s*([0-9a-zA-Z\-\_\.\?]+)\s*=\s*(.*)\s*$/s', trim($strLine), $arrMatches))
                    $arrTranslation[$arrMatches[1]] = trim($arrMatches[2]);
                elseif (trim($strLine) != '' && $strLine[0] != '#')
                    $this->Output(2, sprintf(__t('Skipped line "%s" from translation "%s".'), $strLine, $objFile->FileName));
            }

            foreach($arrTemplateContents as $intPos=>$strLine) {
                if (preg_match('/^\s*([0-9a-zA-Z\-\_\.\?]+)\s*=\s*(.*)\s*$/s', trim($strLine), $arrMatches))
                    $arrTemplate[$arrMatches[1]] = trim($arrMatches[2]);
                elseif (trim($strLine) != '' && $strLine[0] != '#')
                    $this->Output(2, sprintf(__t('Skipped line "%s" from template "%s".'), $strLine, $objFile->FileName));
            }

            $arrTemplate = $this->CheckForAccessKeysMozilla($arrTemplate);

            $arrTranslation = $this->CheckForAccessKeysMozilla($arrTranslation);

            if (is_array($arrTemplate))
                foreach($arrTemplate as $strKey=>$strVal) {
                    $this->AddTranslation($objFile, $strVal, $arrTranslation[$strKey], $strKey);
                }
            else
                $this->Output(2, sprintf(__t('Found a empty template (%s)'), $strTemplateFile));

        }

        public function ImportDtdFile($objFile, $strTemplateFile, $strTranslatedFile) {
            if ($strTranslatedFile)
                $strFileContents = file_get_contents($strTranslatedFile);
            $strTemplateContents = file_get_contents($strTemplateFile);

            if (!$strFileContents || !$strTemplateContents)
                return false;

            if (!preg_match_all('/^<!ENTITY\s+([^\s]+)\s+"([^"]+)"\s?>\s*/m', $strFileContents, $arrMatches))
                return false;

            if (!preg_match_all('/^<!ENTITY\s+([^\s]+)\s+"([^"]+)"\s?>\s*/m', $strTemplateContents, $arrTemplateMatches))
                return false;

            foreach($arrMatches[1] as $intPos=>$strVal) {
                $arrTranslation[$strVal] = $arrMatches[2][$intPos];
            }

            foreach($arrTemplateMatches[1] as $intPos=>$strVal) {
                $arrTemplate[$strVal] = $arrTemplateMatches[2][$intPos];
            }

            $arrTemplate = $this->CheckForAccessKeysMozilla($arrTemplate);

            $arrTranslation = $this->CheckForAccessKeysMozilla($arrTranslation);

            foreach($arrTemplate as $strKey=>$strVal) {
                $this->AddTranslation($objFile, $strVal, $arrTranslation[$strKey], $strKey);
            }

        }

        public function ExportProjectArchive($strFile) {
            $this->Output("Începe exportul pentru proiectul " . $this->objProject->ProjectId . " din " . $strFile);
            $this->startTimer();
            $this->intImportedFilesCount = 0;
            $this->intImportedSuggestionsCount = 0;
            $this->intImportedValidationsCount = 0;
            $this->intImportedTextsCount = 0;
            $this->intImportedContextsCount = 0;

            /**
             * work with tar.bz2 archives
             */
            if (preg_match('/\.tar.bz2$/', $strFile)) {
                /**
                 * set up a working path in the temporary directory
                 */
                $this->Output("Se caută un director valid de lucru ...");
                if (file_exists($this->strImportDirectory . '/' . $this->objProject->ProjectId)) {
                    $i=0;
                    while(file_exists($this->strImportDirectory . '/' . $this->objProject->ProjectId . '-' . $i))
                        $i++;
                    $strWorkPath = $this->strImportDirectory . '/' . $this->objProject->ProjectId . '-' . $i;
                }
                else {
                    $strWorkPath = $this->strImportDirectory . '/' . $this->objProject->ProjectId;
                }


                $this->Output("Începe procesarea fişierului " . $strFile);
                if (!mkdir($strWorkPath)) {
                    $this->OutputLog('Nu se poate crea directorul „' . self::$ImportDirectory . '/' . $this->objProject->ProjectId . '”');
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
                    $this->OutputLog('Eroare la dezarhivare: ' . join("\n", $arrOutput));
                    return false;
                }
            }
            else {
                $strWorkPath = $strFile . '/l10n';
                $strCurDir = getcwd();
                chdir($strWorkPath);
            }

            if (!file_exists('en-US') || !file_exists('ro')) {
                throw new Exception('Arhiva trebuie să conţină două directoare en-US şi ro în care să se găsească modelele, respectiv traducerea în română.');
                return false;
            }

            /**
             * get the file list with complete paths
             * the file list is retrieved from en-US
             */
            $arrFiles = self::ListDir($strWorkPath . '/en-US');
            $intTotalFilesToProcess = count($arrFiles);
            $this->Output("Începe procesarea a " . $intTotalFilesToProcess . " fişiere");
            $arrDirectories = array();
            foreach($arrFiles as $intFileNo=>$strFileToImport) {
                $arrFileParts = split('/', str_replace($strWorkPath . '/en-US', '', $strFileToImport));
                $strFileName = $arrFileParts[count($arrFileParts)-1];
                unset($arrFileParts[count($arrFileParts)-1]);
                unset($arrFileParts[0]);

                $strPath = '';
                $intParentId = 0;
                foreach($arrFileParts as $intPos=>$strDir) {
                    $strPath = $strPath . '/' . $strDir;
                    if (!isset($arrDirectories[$strPath])) {
                        if ($intParentId)
                            $arrFile = NarroFile::QueryArray(QQ::AndCondition(QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId), QQ::Equal(QQN::NarroFile()->FileName, $strDir), QQ::Equal(QQN::NarroFile()->ParentId, $intParentId)));
                        else
                            $arrFile = NarroFile::QueryArray(QQ::AndCondition(QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId), QQ::Equal(QQN::NarroFile()->FileName, $strDir), QQ::IsNull(QQN::NarroFile()->ParentId)));

                        if (isset($arrFile[0]) && $arrFile[0] instanceof NarroFile) {
                            $objFile = $arrFile[0];
                        }
                        else {
                            continue;
                        }

                        $arrDirectories[$strPath] = $objFile->FileId;
                    }
                    $intParentId = $arrDirectories[$strPath];
                }

                /**
                 * import the file
                 */
                if (!$intFileType = $this->GetFileType($strFileName))
                    continue;

                $objFile = NarroFile::QuerySingle(QQ::AndCondition(QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId), QQ::Equal(QQN::NarroFile()->FileName, $strFileName), QQ::Equal(QQN::NarroFile()->ParentId, $intParentId)));

                if (!$objFile instanceof NarroFile) {
                    continue;
                }

                $strTranslatedFileToImport = str_replace($strWorkPath . '/en-US', $strWorkPath . '/ro', $strFileToImport);
                $this->OutputLog(sprintf('Începe exportul fișierului „%s”, „%s”', $objFile->FileName, $strFileToImport));

                if (file_exists($strTranslatedFileToImport))
                    $this->ExportFile($this->objProject->ProjectId, $objFile, $strFileToImport, $strTranslatedFileToImport);
                else {
                    copy($strFileToImport, $strTranslatedFileToImport);
                    $this->ExportFile($this->objProject->ProjectId, $objFile, $strFileToImport);
                }

                if ($intFileNo % 10 === 0)
                    $this->Output("Progres: " . ceil(($intFileNo*100)/$intTotalFilesToProcess) . "%");
            }

            $this->stopTimer();
        }

        public function ExportFile ($objFile, $strTemplateFile, $strTranslatedFile = false) {
            if (!$objFile instanceof NarroFile)
                return false;

            switch($objFile->TypeId) {
                case NarroFileType::DtdMozilla:
                        return $this->ExportDtdFile($objFile, $strTemplateFile, $strTranslatedFile);
                case NarroFileType::IniProperties:
                        return $this->ExportPropertiesFile($objFile, $strTemplateFile, $strTranslatedFile);
                default:
                        return false;
            }

        }

        public function ExportPropertiesFile($objFile, $strTemplateFile, $strTranslatedFile) {
            $strTemplateContents = file_get_contents($strTemplateFile);

            if (!$strTemplateContents)
                return false;

            $strTemplateContents = str_replace("\\\n", '\\\n', $strTemplateContents);

            $arrTemplateContents = split("\n", $strTemplateContents);

            foreach($arrTemplateContents as $intPos=>$strLine) {
                if (preg_match('/^\s*([0-9a-zA-Z\-\_\.\?]+)\s*=\s*(.*)\s*$/s', trim($strLine), $arrMatches)) {
                    $arrTemplate[$arrMatches[1]] = trim($arrMatches[2]);
                    $arrTemplateLines[$arrMatches[1]] = $arrMatches[0];
                }
                elseif (trim($strLine) != '' && $strLine[0] != '#')
                    $this->Output(sprintf('S-a sărit peste linia „%s” din „%s” din model.', $strLine, $objFile->FileName));
            }

            $arrTranslationObjects = NarroContext::LoadArrayByFileId($objFile->FileId);
            foreach($arrTranslationObjects as $objNarroContext) {
                if ($objNarroContext->ValidSuggestionId > 0)
                    $arrTranslation[$objNarroContext->Context] = $objNarroContext->ValidSuggestion->SuggestionValue;
            }

            $strTranslateContents = '';

            foreach($arrTemplate as $strKey=>$strOriginalText) {
                if (trim($strKey) == '') continue;

                if (preg_match('/([A-Z0-9a-z\.\_\-]+)([\.\-\_]{1,1})accesskey$/s', $strKey, $arrMatches)) {
                    if (isset($arrTranslation[$arrMatches[1] . $arrMatches[2] . 'label']))
                        $strMatchedKey = $arrMatches[1] . $arrMatches[2] . 'label';
                    elseif (isset($arrTranslation[$arrMatches[1]]))
                        $strMatchedKey = $arrMatches[1];
                    else {
                        $this->OutputLog(sprintf('%s. „%s” pare a fi o tastă rapidă, dar nu s-a găsit nici „%s” nici „%s”', $objFile->FileName, $strKey, $arrMatches[1] . $arrMatches[2] . 'label', $arrMatches[1]));
                        continue;
                    }
                    if ($strMatchedKey && isset($arrTranslation[$arrMatches[1] . $arrMatches[2] . 'accesskey'])) {
                        $this->OutputLog(sprintf('%s. S-a găsit tasta rapidă „%s” pentru „%s” („%s”) ca şi cheie separată.', $objFile->FileName, $arrTranslation[$arrMatches[1] . $arrMatches[2] . 'accesskey'], $arrTranslation[$strMatchedKey], $arrTemplate[$strMatchedKey]));
                        //die();
                    }
                    elseif ($strMatchedKey) {
                        $strStrippedText = preg_replace('/\&[a-zA-Z]\;/', ' ', $arrTranslation[$strMatchedKey]);
                        $intPos = strpos($strStrippedText, '&');
                        if ($intPos !== false) {
                            $arrTranslation[$arrMatches[1] . $arrMatches[2] . 'accesskey'] = $strStrippedText[$intPos + 1];
                            $intPos = strpos($arrTranslation[$strMatchedKey], '&' .  $strStrippedText[$intPos + 1]);
                            if ($intPos !== false) {
                                $arrTranslation[$strMatchedKey] = substr($arrTranslation[$strMatchedKey], 0, $intPos) . substr($arrTranslation[$strMatchedKey], $intPos + 1);
                                //$this->Output(sprintf('S-a găsit tasta rapidă „%s” pentru „%s” („%s”)', $strStrippedText[$intPos + 1], $arrTranslation[$strMatchedKey], $arrTemplate[$strMatchedKey]));
                            }
                            else {
                                $this->Output(sprintf('%s. S-a găsit tasta rapidă „%s” pentru „%s” („%s”), dar nu s-a găsit „%s” în textul tradus.', $objFile->FileName, $strStrippedText[$intPos + 1], $arrTranslation[$strMatchedKey], $arrTemplate[$strMatchedKey], '&' .  $strStrippedText[$intPos + 1]));
                                $this->Output(print_r($arrTranslation,true));
                                $this->Output(print_r($arrTemplate,true));
                                die();
                            }
                        }
                        else {
                            $this->OutputLog(sprintf('%s. Nu s-a găsit tasta rapidă pentru „%s” („%s”). S-a căutat „&” în „%s” (textul tradus fără entităţi). Se va lua prima literă din text', $objFile->FileName, $arrTranslation[$strMatchedKey], $arrTemplate[$strMatchedKey], $strStrippedText));
                            $arrTranslation[$arrMatches[1] . $arrMatches[2] . 'accesskey'] = $strStrippedText[0];
                        }

                    }
                    else
                        unset($strMatchedKey);
                }
            }

            $strTranslateContents = $strTemplateContents;

            foreach($arrTemplate as $strKey=>$strOriginalText) {

                if (isset($arrTranslation[$strKey])) {
                    //$strTranslateContents .= sprintf('%s=%s' . "\n", $strKey, QApplication::ConvertToComma($arrTranslation[$strKey]));
                    if (preg_match('/[A-Z0-9a-z\.\_\-]+(\s*=\s*)/', $arrTemplateLines[$strKey], $arrMiddleMatches)) {
                        $strGlue = $arrMiddleMatches[1];
                    }
                    else {
                        $this->OutputLog(sprintf('Glue faield: „%s”', $arrTemplateLines[$strKey]));
                        $strGlue = '=';
                    }

                    if (strstr($strTranslateContents, $strKey . $strGlue . $strOriginalText))
                        $strTranslateContents = str_replace($strKey . $strGlue . $strOriginalText, $strKey . $strGlue . QApplication::ConvertToComma($arrTranslation[$strKey]), $strTranslateContents);
                    else
                        $this->OutputLog(sprintf('Atenție! În fișierul „%s”, nu se găsește „%s”', $objFile->FileName, $strKey . $strGlue . $strOriginalText));

                }
                else{
                    $this->OutputLog(sprintf('%s. Nu s-a găsit cheia „%s” în traducere („%s”). Se va folosi textul din engleză.', $objFile->FileName, $strKey, $strOriginalText));
                    //$strTranslateContents .= sprintf('%s=%s' . "\n", $strKey, $strOriginalText);
                }
            }
            if (!unlink($strTranslatedFile)) {
                $this->OutputLog(sprintf('Nu se poate şterge fişierul „%s”', $strTranslatedFile));
            }

            if (in_array($objFile->FileName, array('custom.properties', 'mui.properties', 'override.properties', 'crashreporter.ini'))) {
                $strTranslateContents = QApplication::ConvertToSedila($strTranslateContents);
            }

            if (!file_put_contents($strTranslatedFile, $strTranslateContents)) {
                $this->OutputLog(sprintf('Nu se poate scrie în fişierul „%s”', $strTranslatedFile));
            }
        }

        public function ExportDtdFile($objFile, $strTemplateFile, $strTranslatedFile) {
            $strTemplateContents = file_get_contents($strTemplateFile);

            if (!$strTemplateContents)
                return false;

            if (!preg_match_all('/^<!ENTITY\s+([^\s]+)\s+"([^"]*)"\s?>\s*/ms', $strTemplateContents, $arrTemplateMatches))
                return false;

            foreach($arrTemplateMatches[1] as $intPos=>$strVal) {
                $arrTemplate[$strVal] = $arrTemplateMatches[2][$intPos];
                $arrTemplateLines[$strVal] = $arrTemplateMatches[0][$intPos];
            }

            $arrTranslationObjects = NarroContext::LoadArrayByFileId($objFile->FileId);
            foreach($arrTranslationObjects as $objNarroContext) {
                if ($objNarroContext->ValidSuggestionId > 0)
                    $arrTranslation[$objNarroContext->Context] = $objNarroContext->ValidSuggestion->SuggestionValue;
                else
                    $this->Output(2, sprintf('%s. Contextul „%s” nu are o sugestie validată.', $objFile->FileName, $objNarroContext->Context));
            }

            $strTranslateContents = '';

            foreach($arrTemplate as $strKey=>$strOriginalText) {
                if (trim($strKey) == '') continue;

                if (preg_match('/([A-Z0-9a-z\.\_\-]+)([\.\-\_]{1,1})accesskey$/s', $strKey, $arrMatches)) {
                    if (isset($arrTranslation[$arrMatches[1] . $arrMatches[2] . 'label']))
                        $strMatchedKey = $arrMatches[1] . $arrMatches[2] . 'label';
                    elseif (isset($arrTranslation[$arrMatches[1]]))
                        $strMatchedKey = $arrMatches[1];
                    else {
                        $this->Output(2, sprintf('%s. „%s” pare a fi o tastă rapidă, dar nu s-a găsit nici „%s” nici „%s”', $objFile->FileName, $strKey, $arrMatches[1] . $arrMatches[2] . 'label', $arrMatches[1]));
                        continue;
                    }

                    if ($strMatchedKey && isset($arrTranslation[$arrMatches[1] . $arrMatches[2] . 'accesskey'])) {
                        $this->Output(2, sprintf('%s. S-a găsit tasta rapidă „%s” pentru „%s” („%s”) ca şi cheie separată.', $objFile->FileName, $arrTranslation[$arrMatches[1] . $arrMatches[2] . 'accesskey'], $arrTranslation[$strMatchedKey], $arrTemplate[$strMatchedKey]));
                        //die();
                    }

                    elseif ($strMatchedKey) {
                        $strStrippedText = preg_replace('/\&[a-zA-Z]+\;/', ' ', $arrTranslation[$strMatchedKey]);
                        if (preg_match('/\&\s/', $strStrippedText)) {
                            $this->Output(2, sprintf('%s. Textul „%s” are caracterul & urmat de un spaţiu, lucru nepermis. Se va folosi textul din engleză: „%s”.', $objFile->FileName, $strStrippedText, $arrTemplate[$strMatchedKey]));
                            $arrTranslation[$strMatchedKey] = $arrTemplate[$strMatchedKey];
                        }

                        if (!preg_match_all('/&[a-zA-Z0-9-]/', $strStrippedText, $arrKeyMatches)) {

                            mb_regex_encoding("UTF-8");
                            $arrKeyMatches = array();

                            mb_ereg_search_init($strStrippedText, '\&[ăîâşţĂÎÂŞŢ]');
                            $r = mb_ereg_search();

                            if(!$r) {
                                $this->OutputLog(2, sprintf('%s. Nu s-a găsit tasta rapidă pentru „%s” („%s”). S-a căutat „&” în „%s” (textul tradus fără entităţi). Se va lua prima literă din text (%s)', $objFile->FileName, $arrTranslation[$strMatchedKey], $arrTemplate[$strMatchedKey], $strStrippedText, mb_substr($strStrippedText, 0, 1)));
                                $arrTranslation[$arrMatches[1] . $arrMatches[2] . 'accesskey'] = mb_substr($strStrippedText, 0, 1);
                            }
                            else
                            {
                                $r = mb_ereg_search_getregs(); //get first result
                                do
                                {
                                    $arrKeyMatches[0][] = $r[0];
                                    $r = mb_ereg_search_regs();//get next result
                                }
                                while($r);
                            }

                            if (isset($arrKeyMatches[0]) && isset($arrKeyMatches[0][0])) {
                                $this->Output(2, sprintf('%s. Atenţie: tasta rapidă pentru „%s” este o diacritică: „%s”.', $objFile->FileName, $strStrippedText, $arrKeyMatches[0][0]));
                            }
                            else {
                                if (strstr($strStrippedText, '&'))
                                    $this->Output(2, sprintf('%s. În textul „%s” există un caracter & dar nu a fost prins de expresia regulată.', $objFile->FileName, $strStrippedText));
                            }
                        }

                        if (!isset($arrKeyMatches[0]) || count($arrKeyMatches[0]) == 0) {
                            $this->OutputLog(2, sprintf('%s. Nu s-a găsit tasta rapidă pentru „%s” („%s”). S-a căutat „&” în „%s” (textul tradus fără entităţi). Se va lua prima literă din text (%s)', $objFile->FileName, $arrTranslation[$strMatchedKey], $arrTemplate[$strMatchedKey], $strStrippedText, mb_substr($strStrippedText, 0, 1)));
                            $arrTranslation[$arrMatches[1] . $arrMatches[2] . 'accesskey'] = mb_substr($strStrippedText, 0, 1);
                        }
                        elseif (count($arrKeyMatches[0]) > 1) {
                            $this->OutputLog(2, sprintf('%s. În textul „%s” s-a găsit & de mai multe ori: „%s”. Se va lua prima literă din text (%s)', $objFile->FileName, $strStrippedText, join(',', $arrKeyMatches[0]), mb_substr($strStrippedText, 0, 1)));
                            $arrTranslation[$arrMatches[1] . $arrMatches[2] . 'accesskey'] = mb_substr($strStrippedText, 0, 1);
                        }
                        else {
                            $strAccessKey = str_replace('&', '', $arrKeyMatches[0][0]);
                            $arrTranslation[$arrMatches[1] . $arrMatches[2] . 'accesskey'] = $strAccessKey;
                            $intPos = strpos($arrTranslation[$strMatchedKey], '&' .  $strAccessKey);
                            if ($intPos !== false) {
                                $arrTranslation[$strMatchedKey] = substr($arrTranslation[$strMatchedKey], 0, $intPos) . substr($arrTranslation[$strMatchedKey], $intPos + 1);
                                $this->OutputLog(1, sprintf('S-a găsit tasta rapidă „%s” pentru „%s” („%s”)', $strAccessKey, $arrTranslation[$strMatchedKey], $arrTemplate[$strMatchedKey]));
                            }
                            else {
                                $this->Output(2, sprintf('%s. S-a găsit tasta rapidă „%s” pentru „%s” („%s”), dar nu s-a găsit „%s” în textul tradus.', $objFile->FileName, $strAccessKey, $arrTranslation[$strMatchedKey], $arrTemplate[$strMatchedKey], '&' .  $strAccessKey));
                                $this->Output(print_r($arrTranslation,true));
                                $this->Output(print_r($arrTemplate,true));
                                $this->Output(print_r($arrKeyMatches,true));
                                die();
                            }
                        }
                    }
                    else {
                        if ($strMatchedKey)
                            $this->OutputLog(2, sprintf('%s. „%s” pare a fi o tastă rapidă, dar „%s”=„%s”', $objFile->FileName, $strKey, '$arrTranslation[' . $arrMatches[1] . $arrMatches[2] . 'accesskey' . ']', var_export(isset($arrTranslation[$arrMatches[1] . $arrMatches[2] . 'accesskey']),true)));
                        else
                            $this->OutputLog(2, sprintf('%s. „%s” pare a fi o tastă rapidă, dar nu s-a găsit a cui este', $objFile->FileName, $strKey));
                        unset($strMatchedKey);
                    }

                }
            }

            foreach($arrTemplate as $strKey=>$strOriginalText) {
                if (isset($arrTranslation[$strKey])) {
                    if (trim(QApplication::ConvertToComma($arrTranslation[$strKey]))) {
                        $strTranslatedLine = str_replace('"' . $arrTemplate[$strKey] . '"', '"' . QApplication::ConvertToComma($arrTranslation[$strKey]) . '"', $arrTemplateLines[$strKey]);

                        if ($strTranslatedLine)
                            $strTemplateContents = str_replace($arrTemplateLines[$strKey], $strTranslatedLine, $strTemplateContents);
                        else {
                            $this->OutputLog(3, sprintf('%s. A eșuat înlocuirea „%s”', 'str_replace("' . $arrTemplate[$strKey] . '"' . ', "' . QApplication::ConvertToComma($arrTranslation[$strKey]) . '", ' . $arrTemplateLines[$strKey] . ');'));
                        }
                    }
                    else
                        $this->OutputLog(2, sprintf('%s. Traducerea pentru cheia „%s” pare a fi goală: „%s”', $objFile->FileName, $strKey, $arrTranslation[$strKey]));
                }
                else
                    $this->OutputLog(1, sprintf('%s. Nu s-a găsit cheia „%s” în traducere („%s”). Se va folosi textul din engleză.', $objFile->FileName, $strKey, $strOriginalText));
            }

            $strTranslateContents = $strTemplateContents;

            if (!unlink($strTranslatedFile)) {
                $this->OutputLog(sprintf('Nu se poate şterge fişierul „%s”', $strTranslatedFile));
            }
            if (!file_put_contents($strTranslatedFile, $strTranslateContents)) {
                $this->OutputLog(sprintf('Nu se poate scrie în fişierul „%s”', $strTranslatedFile));
            }
        }

        public function GetFileType($strFile) {
            if (!preg_match('/^.+\.(.+)$/', $strFile, $arrMatches))
                return false;

            if (!isset($arrMatches[1]))
                return false;

            switch($arrMatches[1]) {
                case 'dtd':
                        return NarroFileType::DtdMozilla;
                case 'properties':
                        return NarroFileType::IniProperties;
                case 'ini':
                        return NarroFileType::IniProperties;

                default:
                        return false;
            }
        }

        protected function CheckForAccessKeysMozilla($arrData) {
            if (is_array($arrData))
            foreach($arrData as $strKey=>$strVal) {
                if (preg_match('/([A-Z0-9a-z\.\_\-]+)([\.\-\_]{1,1})accesskey$/s', $strKey, $arrMatches)) {
                    if (isset($arrData[$arrMatches[1] . $arrMatches[2] . 'label']))
                        $strMatchedKey = $arrMatches[1] . $arrMatches[2] . 'label';
                    elseif (isset($arrData[$arrMatches[1]]))
                        $strMatchedKey = $arrMatches[1];
                    else
                        continue;

                    if ($strMatchedKey) {

                        if (strpos( $arrData[$strMatchedKey], $strVal) !== false)
                            $strNewVal = $strVal;
                        elseif (strpos( $arrData[$strMatchedKey], strtolower($strVal)) !== false)
                            $strNewVal = strtolower($strVal);
                        elseif (strpos( $arrData[$strMatchedKey], strtoupper($strVal)) !== false)
                            $strNewVal = strtoupper($strVal);

                        if ($strNewVal) {
                            $arrData[$strMatchedKey] = preg_replace('/' . preg_quote($strNewVal) . '/', '&' . $strNewVal, $arrData[$strMatchedKey], 1);
                            unset($arrData[$strKey]);
                            unset($strNewVal);
                        }
                    }

                    unset($strMatchedLabel);
                    unset($strMatchedKey);
                }
            }
            return $arrData;

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


    }
?>
