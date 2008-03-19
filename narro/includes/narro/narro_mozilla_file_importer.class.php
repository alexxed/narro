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

            $this->Output(1, sprintf(t('Starting import for the project %s from the file %s'), $this->objProject->ProjectName, $strFile));
            $this->startTimer();

            /**
             * work with tar.bz2 archives
             */
            if (preg_match('/\.tar.bz2$/', $strFile)) {
                $this->Output(1, sprintf(t('Got an archive, processing file "%s"'), $strFile));
                $strWorkPath = sprintf('%s/%d', __IMPORT_PATH__, $this->objProject->ProjectId);

                if (!file_exists($strWorkPath) && !mkdir($strWorkPath)) {
                    $this->Output(3, sprintf(t('Could not create import directory "%s" for the project "%s"'), $strWorkPath, $this->objProject->ProjectName));
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
                    $this->Output(3, sprintf(t('Error untaring: %s'), join("\n", $arrOutput)));
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

            $this->Output(1, sprintf(t('Starting to process %d files using directory %s'), $intTotalFilesToProcess, $strDirectory));

            $strQuery = sprintf("UPDATE `narro_file` SET `active` = 0 WHERE project_id=%d", $this->objProject->ProjectId);
            try {
                $objDatabase->NonQuery($strQuery);
            }catch (Exception $objEx) {
                $this->Output(3, sprintf(t('Error while executing sql query in file %s, line %d: %s'), __FILE__, __LINE__ - 4, $objEx->getMessage()));
                return false;
            }

            $strQuery = sprintf("UPDATE `narro_context` SET `active` = 0 WHERE project_id=%d", $this->objProject->ProjectId);
            try {
                $objDatabase->NonQuery($strQuery);
            }catch (Exception $objEx) {
                $this->Output(3, sprintf(t('Error while executing sql query in file %s, line %d: %s'), __FILE__, __LINE__ - 4, $objEx->getMessage()));
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
                            $this->Output(1, sprintf(t('Added folder "%s" from "%s"'), $strDir, $strPath));
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
                    $this->Output(1, sprintf(t('Added file "%s" from "%s"'), $strFileName, $strPath));
                    $this->arrStatistics['Imported files']++;
                }

                $strTranslatedFileToImport = str_replace($strDirectory . '/en-US', $strDirectory . '/' . str_replace('_', '-', $this->objLanguage->LanguageCode), $strFileToImport);

                $intTime = time();
                if (file_exists($strTranslatedFileToImport))
                    $this->ImportFile($objFile, $strFileToImport, $strTranslatedFileToImport);
                else {
                    // it's ok, equal strings won't be imported
                    $this->ImportFile($objFile, $strFileToImport);
                }
                $intElapsedTime = time() - $intTime;
                $this->Output(1, sprintf(t('Processed file "%s" in %d seconds, %d files left'), str_replace($strDirectory . '/en-US', '', $strFileToImport), $intElapsedTime, (count($arrFiles) - $intFileNo)));

                if ($intFileNo % 10 === 0)
                    $this->Output(2, sprintf(t("Progress: %s%%"), ceil(($intFileNo*100)/$intTotalFilesToProcess)));
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
                case NarroFileType::IncMozilla:
                        return $this->ImportIncMozillaFile($objFile, $strTemplateFile, $strTranslatedFile);
                default:
                        return false;
            }

        }

        public function ImportPropertiesFile($objFile, $strTemplateFile, $strTranslatedFile) {
            $intTime = time();

            if ($strTranslatedFile)
                $strTranslatedFileContents = file_get_contents($strTranslatedFile);
            else
                $strTranslatedFileContents = file_get_contents($strTemplateFile);

            $strTemplateContents = file_get_contents($strTemplateFile);

            if (!$strTranslatedFileContents || !$strTemplateContents)
                return false;

            $strTranslatedFileContents = str_replace("\\\n", '\\n', $strTranslatedFileContents);
            $strTemplateContents = str_replace("\\\n", '\\\n', $strTemplateContents);

            $arrFileContents = split("\n", $strTranslatedFileContents);
            $arrTemplateContents = split("\n", $strTemplateContents);

            foreach($arrFileContents as $intPos=>$strLine) {
                if (preg_match('/^\s*([0-9a-zA-Z\-\_\.\?]+)\s*=\s*(.*)\s*$/s', trim($strLine), $arrMatches))
                    $arrTranslation[trim($arrMatches[1])] = trim($arrMatches[2]);
            }

            $strContext = '';
            foreach($arrTemplateContents as $intPos=>$strLine) {
                if (preg_match('/^\s*([0-9a-zA-Z\-\_\.\?]+)\s*=\s*(.*)\s*$/s', trim($strLine), $arrMatches)) {
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
                $this->Output(1, sprintf(t('Ini/Properties file %s preprocessing took %d seconds.'), $objFile->FileName, $intElapsedTime));

            $this->Output(1, sprintf(t('Found %d contexts in file %s.'), count($arrTemplate), $objFile->FileName));

            if (is_array($arrTemplate))
                foreach($arrTemplate as $strKey=>$strVal) {
                    $this->AddTranslation(
                                $objFile,
                                $strVal,
                                isset($arrTemplateAccKeys[$strKey])?$arrTemplateAccKeys[$strKey]:null,
                                $arrTranslation[$strKey],
                                isset($arrTranslationAccKeys[$strKey])?$arrTranslationAccKeys[$strKey]:null,
                                trim($strKey),
                                null,
                                $arrTemplateComments[$strKey]
                    );
                }
            else
                $this->Output(2, sprintf(t('Found a empty template (%s)'), $strTemplateFile));

        }

        public function ImportIncMozillaFile($objFile, $strTemplateFile, $strTranslatedFile) {
            $intTime = time();

            if ($strTranslatedFile)
                $strTranslatedFileContents = file_get_contents($strTranslatedFile);
            else
                $strTranslatedFileContents = file_get_contents($strTemplateFile);

            $strTemplateContents = file_get_contents($strTemplateFile);

            if (!$strTranslatedFileContents || !$strTemplateContents)
                return false;

            $arrFileContents = split("\n", $strTranslatedFileContents);
            $arrTemplateContents = split("\n", $strTemplateContents);

            foreach($arrFileContents as $intPos=>$strLine) {
                if (preg_match('/^#define\s+([^\s]+)\s+(.+)$/s', trim($strLine), $arrMatches)) {
                    $arrTranslation[trim($arrMatches[1])] = trim($arrMatches[2]);
                    if (preg_match('/&([a-zA-Z])/', trim($arrMatches[2]), $arrKeyMatches)) {
                        $arrTranslationAccKeys[trim($arrMatches[1])] = $arrKeyMatches[1];
                        $arrTranslation[trim($arrMatches[1])] = str_replace('&' . $arrKeyMatches[1], $arrKeyMatches[1], trim($arrMatches[2]));
                    }

                }
            }

            $strContext = '';
            foreach($arrTemplateContents as $intPos=>$strLine) {
                if (preg_match('/^#define\s+([^\s]+)\s+(.+)$/s', trim($strLine), $arrMatches)) {
                    $arrTemplate[trim($arrMatches[1])] = trim($arrMatches[2]);
                    $arrTemplateComments[trim($arrMatches[1])] = $strContext;
                    if (preg_match('/&([a-zA-Z])/', trim($arrMatches[2]), $arrKeyMatches)) {
                        $arrTemplateAccKeys[trim($arrMatches[1])] = $arrKeyMatches[1];
                        $arrTemplate[trim($arrMatches[1])] = str_replace('&' . $arrKeyMatches[1], $arrKeyMatches[1], trim($arrMatches[2]));
                    }

                    $strContext = '';
                }
                elseif (strlen($strLine) > 2)
                    $strContext .= $strLine . "\n";
            }

            $intElapsedTime = time() - $intTime;
            if ($intElapsedTime > 0)
                $this->Output(1, sprintf(t('Inc file %s preprocessing took %d seconds.'), $objFile->FileName, $intElapsedTime));

            $this->Output(1, sprintf(t('Found %d contexts in file %s.'), count($arrTemplate), $objFile->FileName));

            if (is_array($arrTemplate))
                foreach($arrTemplate as $strKey=>$strVal) {
                    $this->AddTranslation(
                                $objFile,
                                $strVal,
                                isset($arrTemplateAccKeys[$strKey])?$arrTemplateAccKeys[$strKey]:null,
                                $arrTranslation[$strKey],
                                isset($arrTranslationAccKeys[$strKey])?$arrTranslationAccKeys[$strKey]:null,
                                trim($strKey),
                                null,
                                $arrTemplateComments[$strKey]
                    );
                }
            else
                $this->Output(2, sprintf(t('Found a empty template (%s)'), $strTemplateFile));

        }

        public function ImportDtdFile($objFile, $strTemplateFile, $strTranslatedFile) {
            $intTime = time();

            $strEntitiesAndCommentsRegex = '/<!--\s*(.+)\s*-->\s+<!ENTITY\s+([^\s]+)\s+"([^"]+)"\s?>\s*|<!ENTITY\s+([^\s]+)\s+"([^"]*)"\s?>\s*|<!--\s*(.+)\s*-->\s+<!ENTITY\s+([^\s]+)\s+\'([^\']+)\'\s?>\s*|<!ENTITY\s+([^\s]+)\s+\'([^\']*)\'\s?>\s*/m';
            $strEntitiesRegex = '/<!ENTITY\s+([^\s]+)\s+"([^"]*)"\s?>\s*|<!ENTITY\s+([^\s]+)\s+\'([^\']*)\'\s?>\s*/m';

            /**
             * If a translation file exists, process it so the suggestions in it are imported
             */
            if ($strTranslatedFile) {
                $strTranslatedFileContents = file_get_contents($strTranslatedFile);
                if ($strTranslatedFileContents) {
                    /**
                     * Fetch all entities, we don't care about comments in the translation file
                     */
                    if (preg_match_all($strEntitiesRegex, $strTranslatedFileContents, $arrMatches)) {
                        foreach($arrMatches[1] as $intPos=>$strContextKey) {
                            if (trim($arrMatches[2][$intPos]) != '')
                                $arrTranslation[$arrMatches[1][$intPos]] = $arrMatches[2][$intPos];
                            else
                                $arrTranslation[$arrMatches[3][$intPos]] = $arrMatches[4][$intPos];
                        }
                        list($arrTranslation, $arrTranslationAccKeys) = $this->GetAccessKeys($arrTranslation);

                    }
                    else {
                        $this->Output(2, sprintf(t('No entities found in translation file %s'), $strTranslatedFile));
                    }
                }
                else
                    $this->Output(2, sprintf(t('Failed to open file %s'), $strTranslatedFile));
            }
            else
                $strTranslatedFileContents = false;

            /**
             * Process template with original texts, contexts and context comments
             */
            $strTemplateContents = file_get_contents($strTemplateFile);

            if ($strTemplateContents) {
                /**
                 * Fetch all entities and eventual comments before them
                 */
                if (preg_match_all($strEntitiesAndCommentsRegex, $strTemplateContents, $arrTemplateMatches)) {
                    /**
                     * Do a second match only for entities to make sure that comments matching didn't do something unexpected
                     */
                    if (preg_match_all($strEntitiesRegex, $strTemplateContents, $arrCheckMatches)) {
                        /**
                         * Build an array with context as keys and original texts as value
                         */
                        foreach($arrTemplateMatches[1] as $intPos=>$strContextKey) {
                            if (trim($arrTemplateMatches[2][$intPos]) != '') {
                                $arrTemplate[$arrTemplateMatches[2][$intPos]] = $arrTemplateMatches[3][$intPos];
                                $arrTemplateComments[$arrTemplateMatches[2][$intPos]] = $arrTemplateMatches[1][$intPos];
                            }
                            elseif (trim($arrTemplateMatches[4][$intPos]) != '')
                                $arrTemplate[$arrTemplateMatches[4][$intPos]] = $arrTemplateMatches[5][$intPos];
                            elseif (trim($arrTemplateMatches[7][$intPos]) != '') {
                                $arrTemplate[$arrTemplateMatches[7][$intPos]] = $arrTemplateMatches[8][$intPos];
                                $arrTemplateComments[$arrTemplateMatches[7][$intPos]] = $arrTemplateMatches[6][$intPos];
                            }
                            elseif (trim($arrTemplateMatches[9][$intPos]) != '')
                                $arrTemplate[$arrTemplateMatches[9][$intPos]] = $arrTemplateMatches[10][$intPos];

                        }

                        /**
                         * add po style access keys instead of keeping separate entries for access keys
                         */

                        list($arrTemplate, $arrTemplateKeys) = $this->GetAccessKeys($arrTemplate);



                        $intElapsedTime = time() - $intTime;
                        if ($intElapsedTime > 0)
                            $this->Output(1, sprintf(t('DTD file %s processing took %d seconds.'), $objFile->FileName, $intElapsedTime));

                        $this->Output(1, sprintf(t('Found %d contexts in file %s.'), count($arrTemplate), $objFile->FileName));

                        foreach($arrTemplate as $strContextKey=>$strOriginalText) {
                            if (isset($arrTranslation) && isset($arrTranslation[$strContextKey]))
                                $strTranslation = $arrTranslation[$strContextKey];
                            else
                                $strTranslation = false;

                            if (isset($arrTemplateComments) && isset($arrTemplateComments[$strContextKey]))
                                $strContextComment = $arrTemplateComments;
                            else
                                $strContextComment = null;

                            $this->AddTranslation(
                                        $objFile,
                                        $strOriginalText,
                                        isset($arrTemplateKeys[$strContextKey])?$arrTemplateKeys[$strContextKey]:null,
                                        $strTranslation,
                                        isset($arrTranslationAccKeys[$strContextKey])?$arrTranslationAccKeys[$strContextKey]:null,
                                        trim($strContextKey),
                                        null,
                                        $strContextComment[$strContextKey]
                            );
                        }
                    }
                    elseif (count($arrCheckMatches[0]) != count($arrTemplateMatches[0]))
                        $this->Output(3, sprintf(t('Error on matching expressions in file %s'), $strTemplateFile));
                    else
                        $this->Output(2, sprintf(t('No entities found in file %s'), $strTemplateFile));
                }
                else
                    $this->Output(2, sprintf(t('No entities found in template file %s'), $strTemplateFile));
            }
            else
                return false;
        }

        public function ExportProjectArchive($strFile = null) {
            if ($strFile)
                $this->Output(1, sprintf(t('Starting export for the project %s using as template the file %s'), $this->objProject->ProjectName, $strFile));
            else
                $this->Output(1, sprintf(t('Starting export for the project %s using as template a previous import'), $this->objProject->ProjectName));

            $this->startTimer();

            /**
             * work with tar.bz2 archives
             */
            if ($strFile && preg_match('/\.tar.bz2$/', $strFile)) {
                $this->Output(1, sprintf(t('Got an archive, processing file "%s"'), $strFile));
                $strWorkPath = sprintf('%s/%d/%d', __IMPORT_PATH__, $this->objProject->ProjectId, QApplication::$objUser->UserId);

                if (!file_exists($strWorkPath) && !mkdir($strWorkPath)) {
                    $this->Output(3, sprintf(t('Could not create directory "%s" for the project "%s"'), $strWorkPath, $this->objProject->ProjectName));
                    return false;
                }

                exec('rm -rf ' . $strWorkPath . '/*');

                /**
                 * extract the files
                 */
                exec(sprintf('tar jxf %s', $strFile), $arrOutput, $retVal);
                if ($retVal != 0) {
                    $this->Output(3, sprintf(t('Error untaring: %s'), join("\n", $arrOutput)));
                    return false;
                }

                $this->ExportFromDirectory($strWorkPath);
            }
            elseif (file_exists($strFile) && is_dir($strFile))
                $this->ExportFromDirectory($strFile);
            else
                $this->ExportFromDirectory(sprintf('%s/%d', __IMPORT_PATH__, $this->objProject->ProjectId));

            $this->stopTimer();
        }

        public function ExportFromDirectory($strDirectory) {
            if (!chdir($strDirectory))
                throw new Exception(sprintf(t('Could not change to directory "%s"'), $strDirectory));

            if (!file_exists('en-US'))
                throw new Exception(sprintf(t('The directory "%s" should contain a directory named en-US to be used as a template for exporting.'), $strDirectory));

            /**
             * get the file list with complete paths
             * the file list is retrieved from en-US
             */
            $arrFiles = $this->ListDir($strDirectory . '/en-US');
            $intTotalFilesToProcess = count($arrFiles);
            $this->Output(1, sprintf(t('Starting to process %d files'), $intTotalFilesToProcess));

            $arrDirectories = array();

            foreach($arrFiles as $intFileNo=>$strFileToExport) {
                $arrFileParts = split('/', str_replace($strDirectory . '/en-US', '', $strFileToExport));
                $strFileName = $arrFileParts[count($arrFileParts)-1];
                unset($arrFileParts[count($arrFileParts)-1]);
                unset($arrFileParts[0]);

                $strPath = '';
                $intParentId = 0;
                $arrDirectories = array();

                foreach($arrFileParts as $intPos=>$strDir) {
                    $strPath = $strPath . '/' . $strDir;

                    if (!isset($arrDirectories[$strPath])) {
                        if ($intParentId)
                            $objFile = NarroFile::QuerySingle(
                                QQ::AndCondition(
                                    QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId),
                                    QQ::Equal(QQN::NarroFile()->FileName, $strDir),
                                    QQ::Equal(QQN::NarroFile()->TypeId, NarroFileType::Dosar),
                                    QQ::Equal(QQN::NarroFile()->ParentId, $intParentId)
                                )
                            );
                        else
                            $objFile = NarroFile::QuerySingle(
                                    QQ::AndCondition(
                                        QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId),
                                        QQ::Equal(QQN::NarroFile()->FileName, $strDir),
                                        QQ::Equal(QQN::NarroFile()->TypeId, NarroFileType::Dosar),
                                        QQ::IsNull(QQN::NarroFile()->ParentId)
                                    )
                            );

                        if (!$objFile instanceof NarroFile) {
                            $this->Output(2, sprintf(t('Could not find folder "%s" with parent id "%d" in the database.'), $strDir, $intParentId));
                            continue;
                        }

                        $arrDirectories[$strPath] = $objFile->FileId;
                    }
                    $intParentId = $arrDirectories[$strPath];
                }

                if (!$intFileType = $this->GetFileType($strFileName))
                    continue;

                $objFile = NarroFile::QuerySingle(QQ::AndCondition(QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId), QQ::Equal(QQN::NarroFile()->FileName, $strFileName), QQ::Equal(QQN::NarroFile()->ParentId, $intParentId)));

                if (!$objFile instanceof NarroFile) {
                    continue;
                }

                $strTranslatedFileToExport = str_replace($strDirectory . '/en-US', $strDirectory . '/' . QApplication::$objUser->Language->LanguageCode, $strFileToExport);

                if (!file_exists(dirname($strTranslatedFileToExport)))
                    mkdir(dirname($strTranslatedFileToExport), 0777, true);

                $this->Output(1, sprintf(t('Exporting file "%s" using template "%s"'), $objFile->FileName, $strTranslatedFileToExport));

                $this->ExportFile($objFile, $strFileToExport, $strTranslatedFileToExport);
                $this->arrStatistics['Exported files']++;

                if ($intFileNo % 10 === 0)
                    $this->Output(2, "Progres: " . ceil(($intFileNo*100)/$intTotalFilesToProcess) . "%");
            }

            $this->stopTimer();
        }

        public function ExportFile ($objFile, $strTemplateFile, $strTranslatedFile) {
            if (!$objFile instanceof NarroFile)
                return false;

            switch($objFile->TypeId) {
                case NarroFileType::DtdMozilla:
                        return $this->ExportDtdFile($objFile, $strTemplateFile, $strTranslatedFile);
                case NarroFileType::IniProperties:
                        return $this->ExportPropertiesFile($objFile, $strTemplateFile, $strTranslatedFile);
                case NarroFileType::IncMozilla:
                        return $this->ExportIncMozillaFile($objFile, $strTemplateFile, $strTranslatedFile);
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
                    $arrTemplate[trim($arrMatches[1])] = trim($arrMatches[2]);
                    $arrTemplateLines[trim($arrMatches[1])] = $arrMatches[0];
                }
                elseif (trim($strLine) != '' && $strLine[0] != '#')
                    $this->Output(1, sprintf(t('Skipped line "%s" from the template "%s".'), $strLine, $objFile->FileName));
            }

            $strTranslateContents = '';

            if (count($arrTemplate) < 1) return false;

            $arrTranslation = $this->GetTranslations($objFile, $arrTemplate);

            $strTranslateContents = $strTemplateContents;

            foreach($arrTemplate as $strKey=>$strOriginalText) {

                if (isset($arrTranslation[$strKey])) {

                    $arrResult = QApplication::$objPluginHandler->ExportSuggestion($strOriginalText, $arrTranslation[$strKey], $strKey, $objFile, $this->objProject);

                    if
                    (
                        trim($arrResult[1]) != '' &&
                        $arrResult[0] == $strOriginalText &&
                        $arrResult[2] == $strKey &&
                        $arrResult[3] == $objFile &&
                        $arrResult[4] == $this->objProject
                    ) {

                        $arrTranslation[$strKey] = $arrResult[1];
                    }
                    else
                        $this->Output(2, sprintf(t('A plugin returned an unexpected result while processing the suggestion "%s": %s'), $arrTranslation[$strKey], var_export($arrResult, true)));

                    if (preg_match('/[A-Z0-9a-z\.\_\-]+(\s*=\s*)/', $arrTemplateLines[$strKey], $arrMiddleMatches)) {
                        $strGlue = $arrMiddleMatches[1];
                    }
                    else {
                        $this->Output(2, sprintf(t('Glue faield: "%s"'), $arrTemplateLines[$strKey]));
                        $strGlue = '=';
                    }

                    if (strstr($strTranslateContents, $strKey . $strGlue . $strOriginalText))
                        $strTranslateContents = str_replace($strKey . $strGlue . $strOriginalText, $strKey . $strGlue . $arrTranslation[$strKey], $strTranslateContents);
                    else
                        $this->Output(2, sprintf(t('Can\'t find "%s" in the file "%s"'), $strKey . $strGlue . $strOriginalText, $objFile->FileName));

                }
                else {
                    $this->Output(1, sprintf(t('Couldn\'t find the key "%s" in the translations, using the original text.'), $strKey, $objFile->FileName));
                    $this->arrStatistics['Texts kept as original']++;
                }
            }

            if (file_exists($strTranslatedFile) && !unlink($strTranslatedFile)) {
                $this->Output(2, sprintf(t('Can\'t delete the file "%s"'), $strTranslatedFile));
            }
            if (!file_put_contents($strTranslatedFile, $strTranslateContents)) {
                $this->Output(2, sprintf(t('Can\'t write to file "%s"'), $strTranslatedFile));
            }


        }

        public function ExportIncMozillaFile($objFile, $strTemplateFile, $strTranslatedFile) {
            $strTemplateContents = file_get_contents($strTemplateFile);

            if (!$strTemplateContents)
                return false;

            $arrTemplateContents = split("\n", $strTemplateContents);

            foreach($arrTemplateContents as $intPos=>$strLine) {
                if (preg_match('/^#define\s+([^\s]+)\s+(.+)$/s', trim($strLine), $arrMatches)) {
                    $arrTemplate[trim($arrMatches[1])] = trim($arrMatches[2]);
                    $arrTemplateLines[trim($arrMatches[1])] = $arrMatches[0];
                }
                elseif (trim($strLine) != '' && $strLine[0] != '#')
                    $this->Output(1, sprintf(t('Skipped line "%s" from the template "%s".'), $strLine, $objFile->FileName));
            }

            $strTranslateContents = '';

            if (count($arrTemplate) < 1) return false;

            $arrTranslationObjects = NarroContextInfo::QueryArray(QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $objFile->FileId));

            foreach($arrTranslationObjects as $objNarroContextInfo) {
                if ($objNarroContextInfo->ValidSuggestionId > 0) {
                    $arrTranslation[$objNarroContextInfo->Context->Context] = $objNarroContextInfo->ValidSuggestion->SuggestionValue;
                    if ($objNarroContextInfo->TextAccessKey) {
                        if ($objNarroContextInfo->SuggestionAccessKey)
                            $strAccessKey = $objNarroContextInfo->SuggestionAccessKey;
                        else {
                            if (preg_match('/[a-zA-Z]/', $objNarroContextInfo->ValidSuggestion->SuggestionValue, $arrMatches)) {
                                $strAccessKey = $arrMatches[0];
                                $this->Output(2, sprintf(t('No access key found for context %s, text %s, using "%s"'), $objNarroContextInfo->Context->Context, $objNarroContextInfo->ValidSuggestion->SuggestionValue, $arrMatches[0]));
                                $this->arrStatistics['Texts with no access key set, but fixed']++;
                            }
                            else {
                                $this->Output(2, sprintf(t('No access key found for context %s, text %s and could not find a valid letter to use, dropping translation.'), $objNarroContextInfo->Context->Context, $objNarroContextInfo->ValidSuggestion->SuggestionValue));
                                unset($arrTranslation[$objNarroContextInfo->Context->Context]);
                                $this->arrStatistics['Texts without acceptable access keys']++;
                                $this->arrStatistics['Texts kept as original']++;
                            }

                        }

                        $arrTranslation[$objNarroContextInfo->Context->Context] = preg_replace('/' . $strAccessKey . '/', '&' . $strAccessKey, $arrTranslation[$objNarroContextInfo->Context->Context] , 1);
                        error_log($objNarroContextInfo->Context->Context . ':' . $strAccessKey . ':' . $arrTranslation[$objNarroContextInfo->Context->Context]);

                        $this->arrStatistics['Texts that have access keys']++;
                    }
                    else
                        $this->arrStatistics['Texts that don\'t have access keys']++;
                }
                else {
                    $this->Output(1, sprintf(t('In file "%s", the context "%s" does not have a valid suggestion.'), $objFile->FileName, $objNarroContextInfo->Context->Context));
                    $this->arrStatistics['Texts without valid suggestions']++;
                    $this->arrStatistics['Texts kept as original']++;
                }
            }


            $strTranslateContents = $strTemplateContents;

            foreach($arrTemplate as $strKey=>$strOriginalText) {

                if (isset($arrTranslation[$strKey])) {

                    $arrResult = QApplication::$objPluginHandler->ExportSuggestion($strOriginalText, $arrTranslation[$strKey], $strKey, $objFile, $this->objProject);

                    if
                    (
                        trim($arrResult[1]) != '' &&
                        $arrResult[0] == $strOriginalText &&
                        $arrResult[2] == $strKey &&
                        $arrResult[3] == $objFile &&
                        $arrResult[4] == $this->objProject
                    ) {

                        $arrTranslation[$strKey] = $arrResult[1];
                    }
                    else
                        $this->Output(2, sprintf(t('A plugin returned an unexpected result while processing the suggestion "%s": %s'), $arrTranslation[$strKey], var_export($arrResult, true)));

                    if (strstr($strTranslateContents, sprintf('#define %s %s', $strKey, $strOriginalText)))
                        $strTranslateContents = str_replace(sprintf('#define %s %s', $strKey, $strOriginalText), sprintf('#define %s %s', $strKey, $arrTranslation[$strKey]), $strTranslateContents);
                    else
                        $this->Output(2, sprintf(t('Can\'t find "%s" in the file "%s"'), $strKey . $strGlue . $strOriginalText, $objFile->FileName));

                }
                else {
                    $this->Output(1, sprintf(t('Couldn\'t find the key "%s" in the translations, using the original text.'), $strKey, $objFile->FileName));
                    $this->arrStatistics['Texts kept as original']++;
                }
            }

            if (file_exists($strTranslatedFile) && !unlink($strTranslatedFile)) {
                $this->Output(2, sprintf(t('Can\'t delete the file "%s"'), $strTranslatedFile));
            }
            if (!file_put_contents($strTranslatedFile, $strTranslateContents)) {
                $this->Output(2, sprintf(t('Can\'t write to file "%s"'), $strTranslatedFile));
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

            $strTranslateContents = '';

            $arrTranslation = $this->GetTranslations($objFile, $arrTemplate);

            foreach($arrTemplate as $strKey=>$strOriginalText) {
                if (isset($arrTranslation[$strKey])) {
                    $arrResult = QApplication::$objPluginHandler->ExportSuggestion($strOriginalText, $arrTranslation[$strKey], $strKey, $objFile, $this->objProject);
                    if
                    (
                        trim($arrResult[1]) != '' &&
                        $arrResult[0] == $strOriginalText &&
                        $arrResult[2] == $strKey &&
                        $arrResult[3] == $objFile &&
                        $arrResult[4] == $this->objProject
                    ) {

                        $arrTranslation[$strKey] = $arrResult[1];
                    }
                    else
                        $this->Output(2, sprintf(t('A plugin returned an unexpected result while processing the suggestion "%s": %s'), $arrTranslation[$strKey], print_r($arrResult, true)));

                    $strTranslatedLine = str_replace('"' . $arrTemplate[$strKey] . '"', '"' . $arrTranslation[$strKey] . '"', $arrTemplateLines[$strKey]);

                    if ($strTranslatedLine)
                        $strTemplateContents = str_replace($arrTemplateLines[$strKey], $strTranslatedLine, $strTemplateContents);
                    else
                        $this->Output(3, sprintf('In file "%s", failed to replace "%s"', 'str_replace("' . $arrTemplate[$strKey] . '"' . ', "' . $arrTranslation[$strKey] . '", ' . $arrTemplateLines[$strKey] . ');'));
                }
                else {
                    $this->Output(1, sprintf('Couldn\'t find the key "%s" in the translations for "%s" from the file "%s". Using the original text.', $strKey, $strOriginalText, $objFile->FileName));
                    $this->arrStatistics['Texts kept as original']++;
                }
            }

            $strTranslateContents = $strTemplateContents;

            if (file_exists($strTranslatedFile) && !unlink($strTranslatedFile)) {
                $this->Output(2, sprintf(t('Can\'t delete the file "%s"'), $strTranslatedFile));
            }
            if (!file_put_contents($strTranslatedFile, $strTranslateContents)) {
                $this->Output(2, sprintf(t('Can\'t write to file "%s"'), $strTranslatedFile));
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
                case 'inc':
                        return NarroFileType::IncMozilla;

                default:
                        return false;
            }
        }

        /**
         * This function looks for accesskey entries and creates po style texts, e.g. &File
         * @param array $arrTexts an array with context as keys and texts as values
         */
        protected function GetAccessKeys($arrTexts) {

            if (is_array($arrTexts))
                foreach($arrTexts as $strAccCtx=>$strAccKey) {
                    if (stristr($strAccCtx, 'accesskey')) {
                        /**
                         * if this is an accesskey, look for the label
                         * until now the following label and accesskeys are matched:
                         *
                         * ctx.label / ctx.acesskey
                         * ctxLabel / ctxAccesskey
                         * ctx / ctx.accesskey
                         *
                         * and so on
                         */
                        $arrMatches = array();
                        $strLabelCtx = false;
                        $strNewAcc = false;

                        if (preg_match('/([A-Z0-9a-z\.\_\-]+)([\.\-\_]a|[\.\-\_]{0,1}A)ccesskey$/s', $strAccCtx, $arrMatches)) {
                            $arrMatches[2] = str_replace('a', '', $arrMatches[2]);

                            if (isset($arrTexts[$arrMatches[1] . $arrMatches[2] . 'label']))
                                $strLabelCtx = $arrMatches[1] . $arrMatches[2] . 'label';
                            elseif (isset($arrTexts[$arrMatches[1] . $arrMatches[2] . 'title']))
                                $strLabelCtx = $arrMatches[1] . $arrMatches[2] . 'title';
                            elseif (isset($arrTexts[$arrMatches[1] . 'Label']))
                                $strLabelCtx = $arrMatches[1] . 'Label';
                            elseif (isset($arrTexts[$arrMatches[1]]))
                                $strLabelCtx = $arrMatches[1];
                            else {
                                $strLabelCtx = '';
                                $this->Output(2, sprintf(t('Found acesskey %s in context %s but didn\'t find any label to match "%s" (.label, Label, etc).'), $strAccKey, $strAccCtx, $arrMatches[1]));
                                continue;
                            }

                            if ($strLabelCtx) {
                                /**
                                 * search for the accesskey in the label
                                 */
                                $intPos = stripos( $arrTexts[$strLabelCtx], $strAccKey);
                                if ($intPos !== false)
                                    $strNewAcc = $arrTexts[$strLabelCtx][$intPos];
                                else {
                                    $this->Output(2, sprintf(t('Found access key %s does not exist in the label %s, using the first letter as accesskey'), $strAccKey, $arrTexts[$strLabelCtx]));
                                    $strNewAcc = $arrTexts[$strLabelCtx][0];
                                }

                                //$arrTexts[$strLabelCtx] = preg_replace('/' . preg_quote($strNewAcc) . '/', '&' . $strNewAcc, $arrTexts[$strLabelCtx], 1);
                                $arrAccKey[$strLabelCtx] = $strNewAcc;
                                unset($arrTexts[$strAccCtx]);
                            }
                            else
                                continue;
                        }
                    }
                    else
                        continue;
                }

            return array($arrTexts, $arrAccKey);


        }

        /**
         * This function does the opposite of GetAccessKeys
         * @param array $arrTemplate an array with context as keys and original texts as values
         * @param array $arrTranslation an array with context as keys and translations as values
         * @return array $arrTranslation an array with context as keys and translations as values
         */
        protected function GetTranslations($objFile, $arrTemplate) {
            $arrTranslationObjects = NarroContextInfo::QueryArray(QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $objFile->FileId));

            foreach($arrTranslationObjects as $objNarroContextInfo) {
                if ($objNarroContextInfo->ValidSuggestionId > 0) {
                    $arrTranslation[$objNarroContextInfo->Context->Context] = $objNarroContextInfo->ValidSuggestion->SuggestionValue;
                    if ($objNarroContextInfo->TextAccessKey) {
                        if ($objNarroContextInfo->SuggestionAccessKey)
                            $arrTranslationKeys[$objNarroContextInfo->Context->Context] = $objNarroContextInfo->SuggestionAccessKey;
                        else {
                            if (preg_match('/[a-zA-Z]/', $objNarroContextInfo->ValidSuggestion->SuggestionValue, $arrMatches)) {
                                $arrTranslationKeys[$objNarroContextInfo->Context->Context] = $arrMatches[0];
                                $this->Output(2, sprintf(t('No access key found for context %s, text %s, using "%s"'), $objNarroContextInfo->Context->Context, $objNarroContextInfo->ValidSuggestion->SuggestionValue, $arrMatches[0]));
                                $this->arrStatistics['Texts with no access key set, but fixed']++;
                            }
                            else {
                                $this->Output(2, sprintf(t('No access key found for context %s, text %s and could not find a valid letter to use, dropping translation.'), $objNarroContextInfo->Context->Context, $objNarroContextInfo->ValidSuggestion->SuggestionValue));
                                unset($arrTranslation[$objNarroContextInfo->Context->Context]);
                                $this->arrStatistics['Texts without acceptable access keys']++;
                                $this->arrStatistics['Texts kept as original']++;
                            }

                        }
                        $this->arrStatistics['Texts that have access keys']++;
                    }
                    else
                        $this->arrStatistics['Texts that don\'t have access keys']++;
                }
                else {
                    $this->Output(1, sprintf(t('In file "%s", the context "%s" does not have a valid suggestion.'), $objFile->FileName, $objNarroContextInfo->Context->Context));
                    $this->arrStatistics['Texts without valid suggestions']++;
                    $this->arrStatistics['Texts kept as original']++;
                }
            }

            foreach($arrTemplate as $strKey=>$strOriginalText) {
                if (trim($strKey) == '') continue;

                /**
                 * if this is an accesskey, look for the label
                 * until now the following label and accesskeys are matched:
                 *
                 * ctx.label / ctx.acesskey
                 * ctxLabel / ctxAccesskey
                 * ctx / ctx.accesskey
                 *
                 * and so on
                 */
                if (preg_match('/([A-Z0-9a-z\.\_\-]+)([\.\-\_]a|[\.\-\_]{0,1}A)ccesskey$/s', $strKey, $arrMatches)) {
                    $arrMatches[2] = str_replace('a', '', $arrMatches[2]);
                    if (isset($arrTranslation[$arrMatches[1] . $arrMatches[2] . 'label']))
                        $strMatchedKey = $arrMatches[1] . $arrMatches[2] . 'label';
                    elseif (isset($arrTranslation[$arrMatches[1] . $arrMatches[2] . 'title']))
                        $strMatchedKey = $arrMatches[1] . $arrMatches[2] . 'title';
                    elseif (isset($arrTranslation[$arrMatches[1] . 'Label']))
                        $strMatchedKey = $arrMatches[1] . 'Label';
                    elseif (isset($arrTranslation[$arrMatches[1]]))
                        $strMatchedKey = $arrMatches[1];
                    else {
                        $this->arrStatistics['Orphan translation access keys']++;
                        continue;
                    }

                    $arrTranslation[$strKey] = $arrTranslationKeys[$strMatchedKey];
                }
            }

            $this->arrStatistics['Contexts to export'] += count($arrTemplate);
            $this->arrStatistics['Exported contexts'] += count($arrTranslation);

            return $arrTranslation;
        }
    }
?>
