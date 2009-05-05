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
    class NarroProgress {

        public static function GetProgress($intProjectId, $strOperation) {
            if (file_exists(__TMP_PATH__ . '/' . $strOperation . '-' . $intProjectId . '-' . NarroApp::$Language->LanguageCode))
                return trim(file_get_contents(__TMP_PATH__ . '/' . $strOperation . '-' . $intProjectId . '-' . NarroApp::$Language->LanguageCode));
            else
                return 0;
        }

        public static function SetProgress($intValue, $intProjectId, $strOperation) {
            if (!@file_put_contents(__TMP_PATH__ . '/' . $strOperation . '-' . $intProjectId . '-' . NarroApp::$Language->LanguageCode, $intValue)) {
                require_once('Zend/Log.php');
                require_once('Zend/Log/Writer/Stream.php');

                $objLogger = new Zend_Log(new Zend_Log_Writer_Stream(__TMP_PATH__ . '/' . $intProjectId . '-' . NarroApp::$Language->LanguageCode . '-' . $strOperation . '.log'));

                $objLogger->warn(sprintf('Can\'t write progress file %s', __TMP_PATH__ . '/' . $strOperation . '-' . $intProjectId . '-' . NarroApp::$Language->LanguageCode));
            }
            @chmod(__TMP_PATH__ . '/' . $strOperation . '-' . $intProjectId . '-' . NarroApp::$Language->LanguageCode, $intValue, 0666);
        }

    }
?>