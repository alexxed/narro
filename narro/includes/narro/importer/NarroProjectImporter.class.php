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

    class NarroProjectImporter {
        /**
         * the user object used for import
         */
        protected $objUser;
        /**
         * the language object used to import in
         */
        protected $objSourceLanguage;
        /**
         * the language object used to import from
         */
        protected $objTargetLanguage;
        /**
         * the project object that is imported
         */
        protected $objProject;
        /**
         * whether to check if the suggestion value is the same as the original text
         * if it's true, the suggestions that are the same as the original text are not imported
         */
        protected $blnCheckEqual = true;
        /**
         * whether to validate the imported suggestions
         */
        protected $blnValidate = true;
        /**
         * whether to import only suggestions, that is don't add anything else than suggestions
         */
        protected $blnOnlySuggestions = false;
        /**
         * whether to make files inactive before import
         */
        protected $blnDeactivateFiles = true;
        /**
         * whether to make contexts inactive before import
         */
        protected $blnDeactivateContexts = true;

        public function ImportProjectArchive($strFile) {

            NarroLog::LogMessage(1, sprintf(t('Starting import for the project %s from the file %s'), $this->objProject->ProjectName, $strFile));
            $this->startTimer();

            /**
             * work with tar.bz2 archives
             */
            if (preg_match('/\.tar.bz2$/', $strFile)) {
                NarroLog::LogMessage(1, sprintf(t('Got an archive, processing file "%s"'), $strFile));
                $strWorkPath = sprintf('%s/%d', __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__, $this->objProject->ProjectId);

                if (!file_exists($strWorkPath) && !mkdir($strWorkPath)) {
                    NarroLog::LogMessage(3, sprintf(t('Could not create import directory "%s" for the project "%s"'), $strWorkPath, $this->objProject->ProjectName));
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
                    NarroLog::LogMessage(3, sprintf(t('Error untaring: %s'), join("\n", $arrOutput)));
                    return false;
                }

                $this->ImportFromDirectory($strWorkPath);
            }
            elseif(is_dir($strFile))
                $this->ImportFromDirectory($strFile);
            else
                NarroLog::LogMessage(3, sprintf(t('"%s" is not a tar.bz2 archive nor an existing directory.'), $strFile));

            $this->stopTimer();
        }

        public function ImportFromDirectory($strDirectory) {

            $objDatabase = QApplication::$Database[1];

            NarroLog::$strLogFile = $strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/import.log';

            if (file_exists($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/import.pid'))
                throw new Exception(sprintf(t('An export process is already running in the directory "%s" with pid %d'), $strDirectory, file_get_contents($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/import.pid')));

            if (file_exists($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/import.status'))
                throw new Exception(sprintf(t('An import process is already running in the directory "%s" although no pid is recorded. Status is: "%s"'), $strDirectory, file_get_contents($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/import.status')));


            $hndPidFile = fopen($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/import.pid', 'w');

            if (!$hndPidFile)
                throw new Exception(sprintf(t('Cannot create %s in %s.'), 'import.pid', $strDirectory . '/' . $this->objTargetLanguage->LanguageCode));

            $hndStatusFile = fopen($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/import.status', 'w');
            if (!$hndStatusFile)
                throw new Exception(sprintf(t('Cannot create %s in %s.'), 'import.status', $strDirectory . '/' . $this->objTargetLanguage->LanguageCode));

            fputs($hndPidFile, getmypid());
            fclose($hndPidFile);

            fputs($hndStatusFile, '0');


            /**
             * get the file list with complete paths
             */
            $arrFiles = $this->ListDir($strDirectory . '/' . $this->objSourceLanguage->LanguageCode);
            $intTotalFilesToProcess = count($arrFiles);

            NarroLog::LogMessage(1, sprintf(t('Starting to process %d files using directory %s'), $intTotalFilesToProcess, $strDirectory));

            if ($this->blnDeactivateFiles) {
                $strQuery = sprintf("UPDATE `narro_file` SET `active` = 0 WHERE project_id=%d", $this->objProject->ProjectId);
                try {
                    $objDatabase->NonQuery($strQuery);
                }catch (Exception $objEx) {
                    NarroLog::LogMessage(3, sprintf(t('Error while executing sql query in file %s, line %d: %s'), __FILE__, __LINE__ - 4, $objEx->getMessage()));
                    return false;
                }
            }

            if ($this->blnDeactivateContexts) {
                $strQuery = sprintf("UPDATE `narro_context` SET `active` = 0 WHERE project_id=%d", $this->objProject->ProjectId);
                try {
                    $objDatabase->NonQuery($strQuery);
                }catch (Exception $objEx) {
                    NarroLog::LogMessage(3, sprintf(t('Error while executing sql query in file %s, line %d: %s'), __FILE__, __LINE__ - 4, $objEx->getMessage()));
                    return false;
                }
            }

            $arrDirectories = array();
            foreach($arrFiles as $intFileNo=>$strFileToImport) {
                $arrFileParts = split('/', str_replace($strDirectory . '/' . $this->objSourceLanguage->LanguageCode, '', $strFileToImport));
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
                            NarroImportStatistics::$arrStatistics['Kept folders']++;
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
                            $objFile->TypeId = NarroFileType::Folder;
                            if ($intParentId)
                                $objFile->ParentId = $intParentId;
                            $objFile->ProjectId = $this->objProject->ProjectId;
                            $objFile->ContextCount = 0;
                            $objFile->Active = 1;
                            $objFile->Save();
                            NarroLog::LogMessage(1, sprintf(t('Added folder "%s" from "%s"'), $strDir, $strPath));
                            NarroImportStatistics::$arrStatistics['Imported folders']++;
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
                    NarroImportStatistics::$arrStatistics['Kept files']++;
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
                    NarroLog::LogMessage(1, sprintf(t('Added file "%s" from "%s"'), $strFileName, $strPath));
                    NarroImportStatistics::$arrStatistics['Imported files']++;
                }

                $strTranslatedFileToImport = str_replace($strDirectory . '/' . str_replace('_', '-', $this->objSourceLanguage->LanguageCode), $strDirectory . '/' . str_replace('_', '-', $this->objTargetLanguage->LanguageCode), $strFileToImport);

                $intTime = time();
                if (file_exists($strTranslatedFileToImport))
                    $this->ImportFile($objFile, $strFileToImport, $strTranslatedFileToImport);
                else {
                    // it's ok, equal strings won't be imported
                    $this->ImportFile($objFile, $strFileToImport);
                }
                $intElapsedTime = time() - $intTime;
                NarroLog::LogMessage(1, sprintf(t('Processed file "%s" in %d seconds, %d files left'), str_replace($strDirectory . '/' . $this->objSourceLanguage->LanguageCode, '', $strFileToImport), $intElapsedTime, (count($arrFiles) - $intFileNo)));

                ftruncate($hndStatusFile, 0);
                fputs($hndStatusFile, (int) ceil(($intFileNo*100)/$intTotalFilesToProcess));

                if ($intFileNo % 10 === 0) {
                    NarroLog::LogMessage(3, sprintf(t("Progress: %s%%"), ceil(($intFileNo*100)/$intTotalFilesToProcess)));
                }
            }

            fclose($hndStatusFile);

            if (file_exists($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/import.pid'))
                unlink($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/import.pid');
            if (file_exists($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/import.status'))
                unlink($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/import.status');


        }

        public function ImportFile ($objFile, $strTemplateFile, $strTranslatedFile = false) {
            if (!$objFile instanceof NarroFile)
                return false;

            switch($objFile->TypeId) {
                case NarroFileType::MozillaDtd:
                        $objFileImporter = new NarroMozillaDtdFileImporter($this);
                        break;
                case NarroFileType::MozillaIni:
                        $objFileImporter = new NarroMozillaIniFileImporter($this);
                        break;
                case NarroFileType::MozillaInc:
                        $objFileImporter = new NarroMozillaIncFileImporter($this);
                        break;
                case NarroFileType::GettextPo:
                        $objFileImporter = new NarroGettextPoFileImporter($this);
                        break;
                case NarroFileType::OpenOfficeSdf:
                        $objFileImporter = new NarroOpenOfficeSdfFileImporter($this);
                        break;
                default:
                        return false;
            }

            return $objFileImporter->ImportFile($objFile, $strTemplateFile, $strTranslatedFile);
        }

        public function ExportProjectArchive($strFile = null) {
            if ($strFile)
                NarroLog::LogMessage(1, sprintf(t('Starting export for the project %s using as template the file %s'), $this->objProject->ProjectName, $strFile));
            else
                NarroLog::LogMessage(1, sprintf(t('Starting export for the project %s using as template a previous import'), $this->objProject->ProjectName));

            $this->startTimer();

            /**
             * work with tar.bz2 archives
             */
            if ($strFile && preg_match('/\.tar.bz2$/', $strFile)) {
                NarroLog::LogMessage(1, sprintf(t('Got an archive, processing file "%s"'), $strFile));
                $strWorkPath = sprintf('%s/%d/%d', __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__, $this->objProject->ProjectId, QApplication::$objUser->UserId);

                if (!file_exists($strWorkPath) && !mkdir($strWorkPath)) {
                    NarroLog::LogMessage(3, sprintf(t('Could not create directory "%s" for the project "%s"'), $strWorkPath, $this->objProject->ProjectName));
                    return false;
                }

                exec('rm -rf ' . $strWorkPath . '/*');

                /**
                 * extract the files
                 */
                exec(sprintf('tar jxf %s', $strFile), $arrOutput, $retVal);
                if ($retVal != 0) {
                    NarroLog::LogMessage(3, sprintf(t('Error untaring: %s'), join("\n", $arrOutput)));
                    return false;
                }

                $this->ExportFromDirectory($strWorkPath);
            }
            /**
             * this would help if we export in the same directory where we import from
            */
            elseif (file_exists($strFile) && is_dir($strFile))
                $this->ExportFromDirectory($strFile);
            else
                $this->ExportFromDirectory(sprintf('%s/%d', __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__, $this->objProject->ProjectId));

            $this->stopTimer();
        }

        public function ExportFromDirectory($strDirectory) {
            if (!file_exists($strDirectory))
                throw new Exception(sprintf(t('Could not change to directory "%s"'), $strDirectory));

            chdir($strDirectory);

            NarroLog::$strLogFile = $strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/export.log';

            if (file_exists($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/export.pid'))
                throw new Exception(sprintf(t('An export process is already running in the directory "%s" with pid %d'), $strDirectory, file_get_contents($strDirectory . '/export.pid')));

            if (file_exists($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/export.status'))
                throw new Exception(sprintf(t('An export process is already running in the directory "%s" although no pid is recorded. Status is: "%s"'), file_get_contents($strDirectory . '/export.status')));


            $hndPidFile = fopen($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/export.pid', 'w');

            if (!$hndPidFile)
                throw new Exception(sprintf(t('Cannot create %s in %s.'), 'export.pid', $strDirectory));

            $hndStatusFile = fopen($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/export.status', 'w');
            if (!$hndStatusFile)
                throw new Exception(sprintf(t('Cannot create %s in %s.'), 'export.status', $strDirectory));

            fputs($hndPidFile, getmypid());
            fclose($hndPidFile);

            fputs($hndStatusFile, '0');

            if (!file_exists($this->objSourceLanguage->LanguageCode))
                throw new Exception(sprintf(t('The directory "%s" should contain a directory named %s to be used as a template for exporting.'), $strDirectory, $this->objSourceLanguage->LanguageCode));

            /**
             * get the file list with complete paths
             */
            $arrFiles = $this->ListDir($strDirectory . '/' . $this->objSourceLanguage->LanguageCode);
            $intTotalFilesToProcess = count($arrFiles);

            NarroLog::LogMessage(1, sprintf(t('Starting to process %d files'), $intTotalFilesToProcess));

            $arrDirectories = array();

            foreach($arrFiles as $intFileNo=>$strFileToExport) {
                $arrFileParts = split('/', str_replace($strDirectory . '/' . $this->objSourceLanguage->LanguageCode, '', $strFileToExport));
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
                                    QQ::Equal(QQN::NarroFile()->TypeId, NarroFileType::Folder),
                                    QQ::Equal(QQN::NarroFile()->ParentId, $intParentId)
                                )
                            );
                        else
                            $objFile = NarroFile::QuerySingle(
                                    QQ::AndCondition(
                                        QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId),
                                        QQ::Equal(QQN::NarroFile()->FileName, $strDir),
                                        QQ::Equal(QQN::NarroFile()->TypeId, NarroFileType::Folder),
                                        QQ::IsNull(QQN::NarroFile()->ParentId)
                                    )
                            );

                        if (!$objFile instanceof NarroFile) {
                            NarroLog::LogMessage(2, sprintf(t('Could not find folder "%s" with parent id "%d" in the database.'), $strDir, $intParentId));
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

                $strTranslatedFileToExport = str_replace($strDirectory . '/' . $this->objSourceLanguage->LanguageCode, $strDirectory . '/' . $this->objTargetLanguage->LanguageCode, $strFileToExport);

                if (!file_exists(dirname($strTranslatedFileToExport)))
                    mkdir(dirname($strTranslatedFileToExport), 0777, true);

                NarroLog::LogMessage(1, sprintf(t('Exporting file "%s" using template "%s"'), $objFile->FileName, $strTranslatedFileToExport));

                $this->ExportFile($objFile, $strFileToExport, $strTranslatedFileToExport);
                NarroImportStatistics::$arrStatistics['Exported files']++;

                ftruncate($hndStatusFile, 0);
                fputs($hndStatusFile, (int) ceil(($intFileNo*100)/$intTotalFilesToProcess));

                if ($intFileNo % 10 === 0) {
                    NarroLog::LogMessage(3, sprintf(t("Progress: %s%%"), ceil(($intFileNo*100)/$intTotalFilesToProcess)));
                }
            }

            fclose($hndStatusFile);

            if (file_exists($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/export.pid'))
                unlink($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/export.pid');
            if (file_exists($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/export.status'))
                unlink($strDirectory . '/' . $this->objTargetLanguage->LanguageCode  . '/export.status');

            chdir($strDirectory);
            if (file_exists(sprintf('%s-%s.tar.bz2', $this->objProject->ProjectName, $this->objTargetLanguage->LanguageCode)))
                unlink(sprintf('%s-%s.tar.bz2', $this->objProject->ProjectName, $this->objTargetLanguage->LanguageCode));
            exec(sprintf('tar cjvf "%s-%s.tar.bz2" %s/* %s/*', $this->objProject->ProjectName, $this->objTargetLanguage->LanguageCode, $this->objSourceLanguage->LanguageCode, $this->objTargetLanguage->LanguageCode));
            NarroLog::LogMessage(2, sprintf('"%s/%s-%s.tar.bz2" created.', $strDirectory, $this->objProject->ProjectName, $this->objTargetLanguage->LanguageCode));

            $this->stopTimer();
        }

        public function ExportFile ($objFile, $strTemplateFile, $strTranslatedFile) {
            if (!$objFile instanceof NarroFile)
                return false;

            switch($objFile->TypeId) {
                case NarroFileType::MozillaDtd:
                        $objFileImporter = new NarroMozillaDtdFileImporter($this);
                        break;
                case NarroFileType::MozillaIni:
                        $objFileImporter = new NarroMozillaIniFileImporter($this);
                        break;
                case NarroFileType::MozillaInc:
                        $objFileImporter = new NarroMozillaIncFileImporter($this);
                        break;
                case NarroFileType::GettextPo:
                        $objFileImporter = new NarroGettextPoFileImporter($this);
                        break;
                case NarroFileType::OpenOfficeSdf:
                        $objFileImporter = new NarroOpenOfficeSdfFileImporter($this);
                        break;
                default:
                        return false;
            }

            return $objFileImporter->ExportFile($objFile, $strTemplateFile, $strTranslatedFile);
        }

        public function GetFileType($strFile) {
            if (!preg_match('/^.+\.(.+)$/', $strFile, $arrMatches))
                return false;

            if (!isset($arrMatches[1]))
                return false;

            switch($arrMatches[1]) {
                case 'dtd':
                        return NarroFileType::MozillaDtd;
                case 'properties':
                        return NarroFileType::MozillaIni;
                case 'ini':
                        return NarroFileType::MozillaIni;
                case 'inc':
                        return NarroFileType::MozillaInc;
                case 'po':
                        return NarroFileType::GettextPo;
                case 'sdf':
                        return NarroFileType::OpenOfficeSdf;

                default:
                        return false;
            }
        }

        /////////////////////////
        // Public Properties: GET
        /////////////////////////
        public function __get($strName) {
            switch ($strName) {
                case "User": return $this->objUser;
                case "Project": return $this->objProject;
                case "SourceLanguage": return $this->objSourceLanguage;
                case "TargetLanguage": return $this->objTargetLanguage;
                case "Validate": return $this->blnValidate;
                case "CheckEqual": return $this->blnCheckEqual;
                case "OnlySuggestions": return $this->blnOnlySuggestions;

                default: return false;
            }
        }

        /////////////////////////
        // Public Properties: SET
        /////////////////////////
        public function __set($strName, $mixValue) {

            switch ($strName) {
                case "User":
                    if ($mixValue instanceof NarroUser)
                        $this->objUser = $mixValue;
                    else
                        throw new Exception(t('User should be set with an instance of NarroUser'));

                    break;

                case "Project":
                    if ($mixValue instanceof NarroProject)
                        $this->objProject = $mixValue;
                    else
                        throw new Exception(t('Project should be set with an instance of NarroProject'));

                    break;

                case "TargetLanguage":
                    if ($mixValue instanceof NarroLanguage)
                        $this->objTargetLanguage = $mixValue;
                    else
                        throw new Exception(t('TargetLanguage should be set with an instance of NarroLanguage'));

                    break;

                case "SourceLanguage":
                    if ($mixValue instanceof NarroLanguage)
                        $this->objSourceLanguage = $mixValue;
                    else
                        throw new Exception(t('SourceLanguage should be set with an instance of NarroLanguage'));

                    break;


                case "Validate":
                    try {
                        $this->blnValidate = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "CheckEqual":
                    try {
                        $this->blnCheckEqual = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "OnlySuggestions":
                    try {
                        $this->blnOnlySuggestions = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                default:
                    return false;
            }
        }

        protected function startTimer() {
            NarroImportStatistics::$arrStatistics['Start time'] = time();
        }

        protected function stopTimer() {
            NarroImportStatistics::$arrStatistics['End time'] = time();
        }

        protected function ListDir($strDir='.') {

            $arrFiles = array();
            if (is_dir($strDir)) {
                $hndFile = opendir($strDir);
                while (($strFile = readdir($hndFile)) !== false) {
                    // loop through the files, skipping . and .., and recursing if necessary
                    if (strcmp($strFile, '.')==0 || strcmp($strFile, '..')==0) continue;

                    $strFilePath = $strDir . '/' . $strFile;

                    if ( is_dir($strFilePath) )
                        $arrFiles = array_merge($arrFiles, $this->ListDir($strFilePath));
                    else
                        array_push($arrFiles, $strFilePath);
                }
                closedir($hndFile);
            } else {
                // false if the function was called with an invalid non-directory argument
                $arrFiles = false;
            }
            return $arrFiles;
        }


    }
?>
