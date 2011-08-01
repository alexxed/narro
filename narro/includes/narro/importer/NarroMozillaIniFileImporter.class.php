<?php
    /**
     * Narro is an application that allows online software translation and maintenance.
     * Copyright (C) 2008-2011 Alexandru Szasz <alexxed@gmail.com>
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

    class NarroMozillaIniFileImporter extends NarroMozillaFileImporter {
        protected function IsComment($strLine) {
            return preg_match('/^[^a-z0-9]/i', $strLine);
        }

        protected function PreProcessFile($strFile) {
            // some ini files spread across more lines, this fixed that so that the key and value are on one line.
            return str_replace(array("\\\n"), array(''), $strFile);
        }


        protected function PreProcessLine($strLine, $arrComment, $arrLinesBefore) {
            return array($strLine, $arrComment, $arrLinesBefore);
        }

        protected function ProcessLine($strLine, $arrComment, $arrLinesBefore) {
            if (count($arrComment) && strstr($arrComment[count($arrComment) - 1], 'END LICENSE BLOCK')) {
                $arrLinesBefore = array_merge($arrLinesBefore, $arrComment);
                $arrComment = array();
            }

            $arrData = explode('=', $strLine, 2);
            if (count($arrData) == 2) {
                list($strKey, $strValue) = $arrData;
                return array(array('key' => $strKey, 'value' => $strValue), $arrComment, $arrLinesBefore);
            }
            else
                return array(false, $arrComment, $arrLinesBefore);
        }

        public function ImportFile($strTemplateFile, $strTranslatedFile = null) {
            $intTime = time();

            if ($strTranslatedFile)
                $arrTransKey = $this->FileAsArray($strTranslatedFile);

            $arrSourceKey = $this->FileAsArray($strTemplateFile);

            $intElapsedTime = time() - $intTime;
            if ($intElapsedTime > 0)
                QApplication::LogDebug(sprintf('Preprocessing %s took %d seconds.', $this->objFile->FileName, $intElapsedTime));

            QApplication::LogDebug(sprintf('Found %d contexts in file %s.', count($arrSourceKey), $this->objFile->FileName));

            if (is_array($arrSourceKey)) {
                $arrSourceKey = $this->GetAccessKeys($arrSourceKey);
                if (isset($arrTransKey))
                    $arrTransKey = $this->GetAccessKeys($arrTransKey);

                foreach($arrSourceKey as $strKey=>$arrData) {
                    // if it's a matched access key, keep going
                    if (isset($arrData['label_ctx']))
                        continue;
                    $this->AddTranslation(
                                trim($arrData['text']),
                                isset($arrData['access_key'])?trim($arrData['access_key']):null,
                                isset($arrTransKey[$strKey])?trim($arrTransKey[$strKey]['text']):null,
                                isset($arrTransKey[$strKey])?(isset($arrTransKey[$strKey]['access_key'])?trim($arrTransKey[$strKey]['access_key']):null):null,
                                trim($strKey),
                                (isset($arrData['access_key_ctx']))?trim($arrData['comment']) . "\n" . trim($arrSourceKey[$arrData['access_key_ctx']]['comment']):trim($arrData['comment'])
                    );
                }
            }
            else {
                QApplication::LogWarn(sprintf('Found a empty template (%s), copying the original', $strTemplateFile));
                copy($strTemplateFile, $strTranslatedFile);
                chmod($strTranslatedFile, 0666);
            }
        }

        public function ExportFile($strTemplateFile, $strTranslatedFile) {
            $intTime = time();

            $arrSourceKey = $this->FileAsArray($strTemplateFile);

            $intElapsedTime = time() - $intTime;
            if ($intElapsedTime > 0)
                QApplication::LogDebug(sprintf('Preprocessing %s took %d seconds.', $this->objFile->FileName, $intElapsedTime));

            QApplication::LogDebug(sprintf('Found %d contexts in file %s.', count($arrSourceKey), $this->objFile->FileName));

            if (is_array($arrSourceKey)) {
                $arrSourceKey = $this->GetAccessKeys($arrSourceKey);
                $arrTranslation = $this->GetTranslations($arrSourceKey);

                $hndTranslationFile = fopen($strTranslatedFile, 'w');

                if ($this->objFile->Header)
                    fwrite($hndTranslationFile, $this->objFile->Header . "\n");

                $arrUsers = array();
                foreach($this->objFile->GetTranslatorArray($this->objTargetLanguage->LanguageId) as $objUser) {
                    $arrUsers[] = sprintf("# %s <%s>", $objUser->Username, $objUser->Email);
                }

                if (count($arrUsers))
                    fwrite($hndTranslationFile, sprintf("# Translator(s):\n#\n%s\n#\n", join("\n", $arrUsers)));

                $arrUsers = array();
                foreach($this->objFile->GetReviewerArray($this->objTargetLanguage->LanguageId) as $objUser) {
                    $arrUsers[] = sprintf("# %s <%s>", $objUser->Username, $objUser->Email);
                }

                if (count($arrUsers))
                    fwrite($hndTranslationFile, sprintf("# Reviewer(s):\n#\n%s\n#\n", join("\n", $arrUsers)));

                foreach($arrSourceKey as $strContext=>$arrData) {
                    $arrLine = array();

                    if (strlen($arrData['before_line']) > 0)
                        $arrLine[] = $arrData['before_line'];

                    if (!is_null($arrData['comment']))
                        $arrLine[] = $arrData['comment'] . "\n";

                    if (isset($arrTranslation[$strContext]))
                        $arrLine[] = str_replace($arrData['text'], $arrTranslation[$strContext], $arrData['full_line']) . "\n";
                    else
                        if ($this->blnSkipUntranslated == false)
                            $arrLine[] = $arrData['full_line'] . "\n";
                        else
                            continue;

                    fwrite($hndTranslationFile, join('', $arrLine));
                }

                fclose($hndTranslationFile);
                NarroUtils::Chmod($strTranslatedFile, 0666);
                return true;
            }
            else {
                QApplication::LogWarn(sprintf('Found a empty template (%s), copying the original', $strTemplateFile));
                copy($strTemplateFile, $strTranslatedFile);
                NarroUtils::Chmod($strTranslatedFile, 0666);
                return false;
            }
        }
    }
?>
