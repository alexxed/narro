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
    require('../prepend.inc.php');
    require_once(dirname(__FILE__) . '/narro_file_importer.class.php');

    class NarroSelfFileImporter extends NarroFileImporter {

        public function Import() {

            $strDirectory = __DOCROOT__ . __SUBDIRECTORY__;
            $arrFiles = $this->ListDir(__DOCROOT__ . __SUBDIRECTORY__);


            $objDatabase = QApplication::$Database[1];

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
                if (!preg_match('/\.php$/', $strFileToImport)) {
                    unset($arrFiles[$intFileNo]);
                    continue;
                }

                if (preg_match('/\/data\//', $strFileToImport)) {
                    unset($arrFiles[$intFileNo]);
                    continue;
                }


                $strFileContents = file_get_contents($strFileToImport);
                $strFileContents = str_replace("\'", "&&&simplequote&&&", $strFileContents);
                $strFileContents = str_replace('\"', "&&&doublequote&&&", $strFileContents);

                if ($strFileContents) {

                    preg_match_all('/([^a-zA-Z]t|QApplication::Translate|__t)\s*\(\s*[\']([^\']{2,})[\']\s*\)/', $strFileContents, $arrMatches1);

                    preg_match_all('/([^a-zA-Z]t|QApplication::Translate|__t)\s*\(\s*[\"]([^\"]{2,})[\"]\s*\)/', $strFileContents, $arrMatches2);

                    if ( !isset($arrMatches1[2]) && !isset($arrMatches2[2]) || (count($arrMatches1[2]) == 0 && count($arrMatches2[2]) == 0))
                        unset($arrFiles[$intFileNo]);

                }


            }

            $intCurFile = 0;
            foreach($arrFiles as $intFileNo=>$strFileToImport) {
                $intCurFile++;

                $arrFileParts = split('/', str_replace($strDirectory, '', $strFileToImport));
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

                $objFile = NarroFile::QuerySingle(
                                QQ::AndCondition(
                                    QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId),
                                    QQ::Equal(QQN::NarroFile()->FileName, $strFileName),
                                    QQ::Equal(QQN::NarroFile()->ParentId, $intParentId)
                                )
                );

                if ($objFile instanceof NarroFile) {
                    $objFile->Active = 1;
                    $objFile->TypeId = NarroFileType::Php;
                    $objFile->Save();
                    $this->arrStatistics['Kept files']++;
                }
                else {
                    /**
                     * add the file
                     */
                    $objFile = new NarroFile();
                    $objFile->FileName = $strFileName;
                    $objFile->TypeId = NarroFileType::Php;
                    if ($intParentId)
                        $objFile->ParentId = $intParentId;
                    $objFile->ProjectId = $this->objProject->ProjectId;
                    $objFile->Active = 1;
                    $objFile->Encoding = 'UTF-8';
                    $objFile->Save();
                    $this->Output(1, sprintf(t('Added file "%s" from "%s"'), $strFileName, $strPath));
                    $this->arrStatistics['Imported files']++;
                }

                $intTime = time();
                $this->ImportFile($objFile, $strFileToImport);
                $intElapsedTime = time() - $intTime;
                $this->Output(1, sprintf(t('Processed file "%s" in %d seconds, %d files left'), str_replace($strDirectory, '', $strFileToImport), $intElapsedTime, (count($arrFiles) - $intCurFile)));

                if ($intFileNo % 10 === 0)
                    $this->Output(1, sprintf(t("Progress: %s%%"), ceil(($intFileNo*100)/$intTotalFilesToProcess)));
            }
        }

        public function ImportFile($objFile, $strFileName) {

            $strFile = file_get_contents($strFileName);
            $strFile = str_replace("\'", "&&&escapedsimplequote&&&", $strFile);
            $strFile = str_replace('\"', "&&&escapeddoublequote&&&", $strFile);

            if ($strFile) {
                preg_match_all('/([^a-zA-Z]t|QApplication::Translate|__t)\s*\(\s*[\']([^\']{2,})[\']\s*\)/', $strFile, $arrMatches);
                if (!isset($arrMatches[2]))
                    preg_match_all('/([^a-zA-Z]t|QApplication::Translate|__t)\s*\(\s*[\"]([^\"]{2,})[\"]\s*\)/', $strFile, $arrMatches);
                if (isset($arrMatches[2])) {
                    foreach($arrMatches[2] as $intMatchNo=>$strText) {
                        if (trim($strText) != '') {
                            $strText = str_replace(
                                array(
                                    "&&&escapedsimplequote&&&",
                                    "&&&escapeddoublequote&&&",
                                ),
                                array(
                                    "'",
                                    '\"',
                                ),
                                $strText
                            );

                            $this->AddTranslation($objFile, $strText, false, sprintf('Used somewhere in the file "%s"', str_replace(__DOCROOT__ . __SUBDIRECTORY__ . '/', '', $strFileName)));
                        }
                    }
                }
            }
        }
    }

    $objNarroImporter = new NarroSelfFileImporter();
    $objNarroImporter->Language = NarroLanguage::LoadByLanguageCode('ro');
    $objNarroImporter->Project = NarroProject::LoadByProjectName('Narro');
    $objNarroImporter->Import();
?>
