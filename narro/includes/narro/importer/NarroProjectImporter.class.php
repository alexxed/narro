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
        protected $blnCheckEqual;
        /**
         * whether to approve the imported suggestions
         */
        protected $blnApprove;
        /**
         * whether to import only suggestions, that is don't add anything else than suggestions
         */
        protected $blnOnlySuggestions = false;

        protected $blnImportUnchangedFiles = false;

        protected $objLogger;

        /**
         * what suggestions are exported
         * 1 = approved
         * 2 = approved and most voted
         * 3 = approved and most recent suggestion
         * 4 = approved and most voted and most recent suggestion
         * 5 = approved and current user's suggestion
         */
        protected $intExportedSuggestion = 1;
        /**
         * whether to make files inactive before import
         */
        protected $blnDeactivateFiles = true;
        /**
         * whether to make contexts inactive before import
         */
        protected $blnDeactivateContexts = true;
        /**
         * whether to copy unhandled files
         */
        protected $blnCopyUnhandledFiles = true;

        protected $strTranslationPath;
        protected $strTemplatePath;
        protected $intTotalContexts = 0;
        protected $intTotalFiles = 0;

        public function CleanImportDirectory() {
            if (file_exists($this->strTranslationPath  . '/import.progress'))
                unlink($this->strTranslationPath  . '/import.progress');
        }

        public function CleanExportDirectory() {
            if (file_exists($this->strTranslationPath  . '/export.progress'))
                unlink($this->strTranslationPath  . '/export.progress');
        }

        public function ImportProject() {
            $this->startTimer();

            if (function_exists('popen') && function_exists('escapeshellarg') && function_exists('escapeshellcmd') && file_exists(__DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objProject->ProjectId . '/import.sh')) {
                $this->objLogger->err('Found a before import script, trying to run it.');
                 $fp = popen(
                        sprintf(
                            '/bin/sh %s %s %d %s %d %d 2>&1',
                            escapeshellarg(__DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objProject->ProjectId . '/import.sh'),
                            escapeshellarg($this->objTargetLanguage->LanguageCode),
                            $this->objTargetLanguage->LanguageId,
                            escapeshellarg($this->objProject->ProjectName),
                            $this->objProject->ProjectId,
                            NarroApp::GetUserId()
                        ),
                        'r'
                );

                $strOutput = '';

                while(!feof($fp)) {
                    $strOutput .= fread($fp, 1024);
                }
                if (pclose($fp))
                    $this->objLogger->err("Before import script failed:\n" . $strOutput);
                else
                    $this->objLogger->err("Before import script finished successfully:\n" . $strOutput);
            }

            if ($this->objProject->ProjectName == 'Narro')
                $this->strTemplatePath = __DOCROOT__ . __SUBDIRECTORY__ . '/locale/en-US/LC_MESSAGES/';

            if (!file_exists($this->strTemplatePath))
                throw new Exception(sprintf('Template path %s does not exist.', $this->strTemplatePath));

            $this->objLogger->info(sprintf('Starting import for the project %s', $this->objProject->ProjectName));
            $this->objLogger->info(sprintf('Template path is %s', $this->strTemplatePath));
            $this->objLogger->info(sprintf('Translation path is %s', $this->strTranslationPath));


            if (is_dir($this->strTemplatePath))
                if ($this->ImportFromDirectory()) {
                    $this->stopTimer();
                    $this->objLogger->info(sprintf('Import finished successfully in %d seconds.', NarroImportStatistics::$arrStatistics['End time'] - NarroImportStatistics::$arrStatistics['Start time']));
                }
                else {
                    $this->objLogger->err('Import failed. See any messages above for details.');
                }
            else
                throw new Exception(sprintf('Template path "%s" is not a directory.', $this->strTemplatePath));
        }

        public function ImportFromDirectory() {

            $objDatabase = NarroApp::$Database[1];

            NarroLog::SetLogFile($this->objProject->ProjectId . '-' . $this->objTargetLanguage->LanguageCode . '-import.log');

            /**
             * get the file list with complete paths
             */
            $arrFiles = $this->ListDir($this->strTemplatePath);
            $intTotalFilesToProcess = count($arrFiles);

            $this->objLogger->debug(sprintf('Starting to process %d files using directory %s', $intTotalFilesToProcess, $this->strTemplatePath));

            if ($this->blnDeactivateFiles) {
                $strQuery = sprintf("UPDATE `narro_file` SET `active` = 0 WHERE project_id=%d", $this->objProject->ProjectId);
                try {
                    $objDatabase->NonQuery($strQuery);
                }catch (Exception $objEx) {
                    throw new Exception(sprintf(t('Error while executing sql query in file %s, line %d: %s'), __FILE__, __LINE__ - 4, $objEx->getMessage()));
                }
            }

            if ($this->blnDeactivateContexts) {
                $strQuery = sprintf("UPDATE `narro_context` SET `active` = 0 WHERE project_id=%d", $this->objProject->ProjectId);
                try {
                    $objDatabase->NonQuery($strQuery);
                }catch (Exception $objEx) {
                    throw new Exception(sprintf(t('Error while executing sql query in file %s, line %d: %s'), __FILE__, __LINE__ - 4, $objEx->getMessage()));
                }
            }

            $arrDirectories = array();
            NarroProgress::SetProgress(0, $this->objProject->ProjectId, 'import');

            if (is_array($arrFiles))
            foreach($arrFiles as $intFileNo=>$strFileToImport) {
                if (preg_match('/\/CVS|\/\.svn|\/\.hg|\/\.git/', $strFileToImport)) continue;

                $strFilePath = str_replace($this->strTemplatePath, '', $strFileToImport);
                $arrFileParts = split('/', $strFilePath);
                $strFileName = $arrFileParts[count($arrFileParts)-1];

                unset($arrFileParts[count($arrFileParts)-1]);
                unset($arrFileParts[0]);

                /**
                 * create directories
                 */
                $strPath = '';
                $intParentId = null;
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
                            $objFile->FilePath = $strPath;
                            $objFile->Modified = date('Y-m-d H:i:s');
                            $objFile->Save();
                            NarroApp::$PluginHandler->ActivateFolder($objFile, $this->objProject);
                        }
                        else {
                            /**
                             * add the file
                             */
                            $objFile = new NarroFile();
                            $objFile->FileName = $strDir;
                            $objFile->TypeId = NarroFileType::Folder;
                            if ($intParentId)
                                $objFile->ParentId = $intParentId;
                            $objFile->ProjectId = $this->objProject->ProjectId;
                            $objFile->FilePath = $strPath;
                            $objFile->Modified = date('Y-m-d H:i:s');
                            $objFile->Created = date('Y-m-d H:i:s');
                            $objFile->Active = 1;
                            $objFile->Save();
                            $this->objLogger->debug(sprintf('Added folder "%s" from "%s"', $strDir, $strPath));
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
                    $objFile->FilePath = $strFilePath;
                    $objFile->Modified = date('Y-m-d H:i:s');
                    $strMd5File = md5_file($strFileToImport);
                    if ($strMd5File == $objFile->FileMd5)
                        $blnSourceFileChanged = false;
                    else {
                        $objFile->FileMd5 = $strMd5File;
                        $blnSourceFileChanged = true;
                    }
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
                    $objFile->FilePath = $strFilePath;
                    $objFile->FileMd5 = md5_file($strFileToImport);
                    $objFile->Modified = date('Y-m-d H:i:s');
                    $objFile->Created = date('Y-m-d H:i:s');
                    $objFile->Save();
                    $blnSourceFileChanged = true;
                    NarroApp::$PluginHandler->ActivateFile($objFile, $this->objProject);
                    $this->objLogger->debug(sprintf('Added file "%s" from "%s"', $strFileName, $strPath));
                    NarroImportStatistics::$arrStatistics['Imported files']++;
                }

                $strTranslatedFileToImport = str_replace($this->strTemplatePath, $this->strTranslationPath, $strFileToImport);

                $intTime = time();
                if (file_exists($strTranslatedFileToImport)) {
                    if ($blnSourceFileChanged || $this->blnImportUnchangedFiles) {

                    }
                    else {
                        $this->blnOnlySuggestions = true;
                        $this->objLogger->debug(sprintf('Importing only suggestions from "%s" because it\'s unchanged from the last import', $strTranslatedFileToImport));
                        NarroImportStatistics::$arrStatistics['Unchanged template files']++;
                    }

                    $this->ImportFile($objFile, $strFileToImport, $strTranslatedFileToImport);

                }
                else {
                    // it's ok, equal strings won't be imported
                    $this->ImportFile($objFile, $strFileToImport);
                }

                $intElapsedTime = time() - $intTime;
                $this->objLogger->debug(sprintf('Processed file "%s" in %d seconds, %d files left', str_replace($this->strTemplatePath, '', $strFileToImport), $intElapsedTime, (count($arrFiles) - $intFileNo - 1)));

                NarroProgress::SetProgress((int) ceil(($intFileNo*100)/$intTotalFilesToProcess), $this->objProject->ProjectId, 'import');

            }

            return true;
        }

        public function ImportFile ($objFile, $strTemplateFile, $strTranslatedFile = false) {
            if (!$objFile instanceof NarroFile)
                return false;

            if (is_file($strTemplateFile) && filesize($strTemplateFile) > __MAXIMUM_FILE_SIZE_TO_IMPORT__) {
                $this->objLogger->err(sprintf('The file "%s" exceeds the maximum file size allowed to be imported, skipping it', $strTemplateFile));
                return false;
            }

            if (is_file($strTranslatedFile) && filesize($strTranslatedFile) > __MAXIMUM_FILE_SIZE_TO_IMPORT__) {
                $this->objLogger->err(sprintf('The file "%s" exceeds the maximum file size allowed to be imported, skipping it', $strTranslatedFile));
                return false;
            }

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
                case NarroFileType::Svg:
                        $objFileImporter = new NarroSvgFileImporter($this);
                        break;
                case NarroFileType::DumbGettextPo:
                        $objFileImporter = new NarroDumbGettextPoFileImporter($this);
                        break;
                case NarroFileType::PhpMyAdmin:
                        $objFileImporter = new NarroPhpMyAdminFileImporter($this);
                        break;
                default:
                        return false;
            }

            $objFileImporter->File = $objFile;

            return $objFileImporter->ImportFile($strTemplateFile, $strTranslatedFile);
        }

        public function ExportProject() {

            $this->objLogger->err(sprintf(t('Starting export for the project %s using as template %s'), $this->objProject->ProjectName, $this->strTemplatePath));

            $this->startTimer();

            if ($this->objProject->ProjectName == 'Narro')
                $this->strTemplatePath = __DOCROOT__ . __SUBDIRECTORY__ . '/locale/en-US/LC_MESSAGES/';

            if (file_exists($this->strTemplatePath) && is_dir($this->strTemplatePath))
                if ($this->ExportFromDirectory()) {
                    $this->stopTimer();
                    $this->objLogger->err(sprintf('Export finished successfully in %d seconds.', NarroImportStatistics::$arrStatistics['End time'] - NarroImportStatistics::$arrStatistics['Start time']));
                }
                else {
                    $this->objLogger->err('Export failed.');
                }

            else
                throw new Exception(sprintf('Template path "%s" does not exist or it is not a directory', $this->strTemplatePath));

            if (function_exists('popen') && function_exists('escapeshellarg') && function_exists('escapeshellcmd') && file_exists($this->strTemplatePath . '/../export.sh')) {
                $this->objLogger->err('Found an after export script, trying to run it.');
                 $fp = popen(
                        sprintf(
                            '/bin/sh %s %s %d %s %d %d 2>&1',
                            escapeshellarg(realpath($this->strTemplatePath . '/..') . '/export.sh'),
                            escapeshellarg($this->objTargetLanguage->LanguageCode),
                            $this->objTargetLanguage->LanguageId,
                            escapeshellarg($this->objProject->ProjectName),
                            $this->objProject->ProjectId,
                            NarroApp::GetUserId()
                        ),
                        'r'
                );

                $strOutput = '';

                while(!feof($fp)) {
                    $strOutput .= fread($fp, 1024);
                }
                if (pclose($fp))
                    $this->objLogger->err("After export script failed:\n" . $strOutput);
                else
                    $this->objLogger->err("After export script finished successfully:\n" . $strOutput);
            }

            if ($this->objProject->ProjectName == 'Narro') {
                $fp = popen(
                    sprintf(
                        'msgfmt -cv %s -o %s 2>&1',
                        $this->strTranslationPath . '/narro.po',
                        __DOCROOT__ . __SUBDIRECTORY__ . '/locale/' . $this->objTargetLanguage->LanguageCode . '/LC_MESSAGES/narro.mo'
                    ),
                    'r'
                );

                $strOutput = '';

                while(!feof($fp)) {
                    $strOutput .= fread($fp, 1024);
                }
                if (pclose($fp))
                    $this->objLogger->err("Exporting Narro's translation failed:\n" . $strOutput);
                else
                    $this->objLogger->err("Exported Narro's translation succesfully:\n" . $strOutput);

                chmod(__DOCROOT__ . __SUBDIRECTORY__ . '/locale/' . $this->objTargetLanguage->LanguageCode . '/LC_MESSAGES/narro.mo', 0666);
            }


        }


        public function ExportFromDirectory() {

            NarroLog::SetLogFile($this->objProject->ProjectId . '-' . $this->objTargetLanguage->LanguageCode . '-export.log');

            $this->objLogger->debug(sprintf('Starting to export in directory "%s"', $this->strTranslationPath));

            /**
             * get the file list with complete paths
             */
            $arrFiles = $this->ListDir($this->strTemplatePath);

            $intTotalFilesToProcess = count($arrFiles);

            $this->objLogger->debug(sprintf('Starting to process %d files', $intTotalFilesToProcess));

            $arrDirectories = array();
            NarroProgress::SetProgress(0, $this->objProject->ProjectId, 'export');

            if (is_array($arrFiles))
            foreach($arrFiles as $intFileNo=>$strFileToExport) {
                $arrFileParts = split('/', str_replace($this->strTemplatePath, '', $strFileToExport));
                $strFileName = $arrFileParts[count($arrFileParts)-1];
                unset($arrFileParts[count($arrFileParts)-1]);
                unset($arrFileParts[0]);

                $strPath = '';
                $intParentId = null;
                $arrDirectories = array();

                foreach($arrFileParts as $intPos=>$strDir) {
                    $strPath = $strPath . '/' . $strDir;

                    if (!isset($arrDirectories[$strPath])) {
                        if (!is_null($intParentId))
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
                            $this->objLogger->warn(sprintf('Could not find folder "%s" with parent id "%d" in the database.', $strDir, $intParentId));
                            continue;
                        }

                        $arrDirectories[$strPath] = $objFile->FileId;
                    }
                    $intParentId = $arrDirectories[$strPath];
                }

                $strTranslatedFileToExport = str_replace($this->strTemplatePath, $this->strTranslationPath, $strFileToExport);
                if (!file_exists(dirname($strTranslatedFileToExport))) {
                    if (!mkdir(dirname($strTranslatedFileToExport), 0777, true)) {
                        $this->objLogger->warn(sprintf('Failed to create the parent directories for the file %s', $strFileToExport));
                        return false;
                    }
                    NarroUtils::RecursiveChmod(dirname($strTranslatedFileToExport));
                }

                if (!$intFileType = $this->GetFileType($strFileName)) {
                    if ($this->blnCopyUnhandledFiles && !file_exists($strTranslatedFileToExport)) {
                        if (@copy($strFileToExport, $strTranslatedFileToExport)) {
                            $this->objLogger->warn(sprintf('Copying unhandled file type: %s', $strFileToExport));
                            NarroImportStatistics::$arrStatistics['Unhandled files that were copied from the source language']++;
                            chmod($strTranslatedFileToExport, 0666);
                        } else {
                            $this->objLogger->warn(sprintf('Failed to copy the unhandled file to %s', $strTranslatedFileToExport));
                            return false;
                        }
                    }

                    continue;
                }

                $objFile = NarroFile::QuerySingle(
                                QQ::AndCondition(
                                    QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId),
                                    QQ::Equal(QQN::NarroFile()->FileName, $strFileName),
                                    QQ::Equal(QQN::NarroFile()->ParentId, $intParentId),
                                    QQ::Equal(QQN::NarroFile()->Active, 1)
                                )
                );

                if (!$objFile instanceof NarroFile) {
                    continue;
                }

                $this->objLogger->debug(sprintf('Exporting file "%s" using template "%s"', $objFile->FileName, $strTranslatedFileToExport));

                $this->ExportFile($objFile, $strFileToExport, $strTranslatedFileToExport);
                NarroImportStatistics::$arrStatistics['Exported files']++;

                NarroProgress::SetProgress((int) ceil(($intFileNo*100)/$intTotalFilesToProcess), $this->objProject->ProjectId, 'export');

            }

            return true;
        }

        public function ExportFile ($objFile, $strTemplateFile, $strTranslatedFile) {
            if (!$objFile instanceof NarroFile) {
                $this->objLogger->warn(sprintf('Failed to find a corresponding file in the database for %s', $strTemplateFile));
                return false;
            }

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
                case NarroFileType::Svg:
                        $objFileImporter = new NarroSvgFileImporter($this);
                        break;
                case NarroFileType::DumbGettextPo:
                        $objFileImporter = new NarroDumbGettextPoFileImporter($this);
                        break;
                case NarroFileType::PhpMyAdmin:
                        $objFileImporter = new NarroPhpMyAdminFileImporter($this);
                        break;
                default:
                        $this->objLogger->warn(sprintf('Copying unhandled file type: %s', $strTemplateFile));
                        NarroImportStatistics::$arrStatistics['Unhandled files that were copied from the source language']++;
                        copy($strTemplateFile, $strTranslatedFile);
                        return false;
            }
            $objFileImporter->File = $objFile;
            return $objFileImporter->ExportFile($strTemplateFile, $strTranslatedFile);
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
                case 'svg':
                        return NarroFileType::Svg;
                case 'dpo':
                        return NarroFileType::DumbGettextPo;
                case 'php':
                        return NarroFileType::PhpMyAdmin;
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
                case "Approve": return $this->blnApprove;
                case "CheckEqual": return $this->blnCheckEqual;
                case "ImportUnchangedFiles": return $this->blnImportUnchangedFiles;
                case "OnlySuggestions": return $this->blnOnlySuggestions;
                case "DeactivateFiles": return $this->blnDeactivateFiles;
                case "DeactivateContexts": return $this->blnDeactivateContexts;
                case "ExportedSuggestion": return $this->intExportedSuggestion;
                case "CopyUnhandledFiles": return $this->blnCopyUnhandledFiles;
                case "Logger": return $this->objLogger;

                default: return false;
            }
        }

        /////////////////////////
        // Public Properties: SET
        /////////////////////////
        public function __set($strName, $mixValue) {

            switch ($strName) {
                case "TranslationPath":
                    if (file_exists($mixValue))
                        $this->strTranslationPath = $mixValue;
                    else {
                        if (mkdir($mixValue, 0777, true))
                            $this->strTranslationPath = $mixValue;
                        else
                            throw new Exception(sprintf(t('TranslationPath "%s" does not exist.'), $mixValue));

                        NarroUtils::RecursiveChmod($mixValue);
                    }

                    break;

                case "TemplatePath":
                    if (file_exists($mixValue))
                        $this->strTemplatePath = $mixValue;
                    else {
                        if (mkdir($mixValue, 0777, true))
                            $this->strTranslationPath = $mixValue;
                        else
                            throw new Exception(sprintf(t('TranslationPath "%s" does not exist.'), $mixValue));

                        NarroUtils::RecursiveChmod($mixValue);
                    }

                    break;

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


                case "Approve":
                    try {
                        $this->blnApprove = QType::Cast($mixValue, QType::Boolean);
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

                case "ImportUnchangedFiles":
                    try {
                        $this->blnImportUnchangedFiles = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "CopyUnhandledFiles":
                    try {
                        $this->blnCopyUnhandledFiles = QType::Cast($mixValue, QType::Boolean);
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

                case "DeactivateContexts":
                    try {
                        $this->blnDeactivateContexts = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "DeactivateFiles":
                    try {
                        $this->blnDeactivateFiles = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "ExportedSuggestion":
                    try {
                        $this->intExportedSuggestion = QType::Cast($mixValue, QType::Integer);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "Logger":
                    try {
                        $this->objLogger = QType::Cast($mixValue, 'Zend_Log');
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
