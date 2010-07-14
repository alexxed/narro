<?php
    /**
     * Narro is an application that allows online software translation and maintenance.
     * Copyright (C) 2008-2010 Alexandru Szasz <alexxed@gmail.com>
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
    class NarroProjectListDownloadExport extends NarroPlugin {

        public function __construct() {
            parent::__construct();
            $this->blnEnable = true;
            $this->strName = t('Export Archive Downloader');
        }

        public function DisplayInProjectListInProgressColumn(NarroProject $objProject) {
            $strExportArchive = __IMPORT_PATH__ . '/' . $objProject->ProjectId . '/' . $objProject->ProjectName . '-' . QApplication::$Language->LanguageCode . '.zip';

            if (file_exists($strExportArchive)) {
                // @todo replace this with a download method that can serve files from a non web public directory
                $strDownloadUrl = __HTTP_URL__ . __SUBDIRECTORY__ . str_replace(__DOCROOT__ . __SUBDIRECTORY__, '', __IMPORT_PATH__) . '/' . $objProject->ProjectId . '/' . $objProject->ProjectName . '-' . QApplication::$Language->LanguageCode . '.zip';
                $strExportText = sprintf('<a href="%s">%s</a>', $strDownloadUrl, $objProject->ProjectName . '-' . QApplication::$Language->LanguageCode . '.zip');
            }
            else {
                $strExportText = '';
            }


            return $strExportText;
        }
    }
?>