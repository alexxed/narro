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
    class NarroMozillaMercurial extends NarroPlugin {

        public function __construct() {
            parent::__construct();
            $this->blnEnable = (in_array(QApplication::$TargetLanguage->LanguageCode, array('ro')));

            $this->strName = t('Commit to mercurial');
        }

        public function DisplayInProjectListInProgressColumn(NarroProject $objProject, $strText = '') {
            $strExportText = '';

            switch($objProject->ProjectName) {
                case 'Firefox Next (mozilla-central)':
                    $strExportText = sprintf('<a href="http://hg.mozilla.org/l10n-central/%s/summary">HG summary</a>', QApplication::$TargetLanguage->LanguageCode);
                case 'Firefox 3.6':
                case 'Thunderbird 3.1':
                    $strExportText = sprintf('<a href="http://hg.mozilla.org/releases/l10n-mozilla-1.9.2/%s/summary">HG summary</a>', QApplication::$TargetLanguage->LanguageCode);

            }

            return array($objProject, $strExportText);
        }

        public function DisplayExportMessage(NarroProject $objProject, $strText = '') {
            $strExportText = 'Don\'t know where to commit, please contact alexxed@gmail.com to set up a repository for this project';

            switch($objProject->ProjectName) {
                case 'Firefox Next (mozilla-central)':
                case 'Thunderbird Next (comm-central)':
                    $strExportText = sprintf('A commit is scheduled in the next minutes to ssh://hg.mozilla.org/l10n-central/%s', QApplication::$TargetLanguage->LanguageCode);
                    QApplication::$Cache->save(
                        array(
                            sprintf('ssh://hg.mozilla.org/l10n-central/%s', QApplication::$TargetLanguage->LanguageCode),
                            $objProject,
                            QApplication::$TargetLanguage,
                            QApplication::$User
                        ),
                        'hgcommit' . '_' . $objProject->ProjectId . '_' . QApplication::$TargetLanguage->LanguageId,
                        array('hgcommit'),
                        3600
                    );
                    break;
                case 'Firefox 3.6':
                case 'Thunderbird 3.1':
                case 'Fennec 1.1':
                    $strExportText = sprintf('A commit is scheduled in the next minutes to ssh://hg.mozilla.org/releases/l10n-mozilla-1.9.2/%s', QApplication::$TargetLanguage->LanguageCode);
                    QApplication::$Cache->save(
                        array(
                            sprintf('ssh://hg.mozilla.org/releases/l10n-mozilla-1.9.2/%s', QApplication::$TargetLanguage->LanguageCode),
                            $objProject,
                            QApplication::$TargetLanguage,
                            QApplication::$User
                        ),
                        'hgcommit' . '_' . $objProject->ProjectId . '_' . QApplication::$TargetLanguage->LanguageId,
                        array('hgcommit'),
                        3600
                    );
                    break;

            }

            return array($objProject, $strExportText);
        }
    }
?>