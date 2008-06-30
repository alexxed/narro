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

    require_once('includes/prepend.inc.php');
    require_once('includes/narro/importer/NarroRss.class.php');

    QApplication::$objUser->Language = NarroLanguage::Load(QApplication::QueryString('l'));

    switch(QApplication::QueryString('t')) {
        case 'suggestion':
            if (!$objRssFeed = QApplication::$Cache->load(sprintf('rssfeed_suggestion_%d_%d', $objProject->ProjectId, QApplication::QueryString('l')))) {
                $objRssFeed  = new QRssFeed(
                        sprintf(
                            t('New translation suggestions in %s'),
                            QApplication::$objUser->Language->LanguageName
                        ),
                        __HTTP_URL__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__,
                        sprintf(
                            t('Get the latest 100 translation suggestions in %s'),
                            QApplication::$objUser->Language->LanguageName
                        )
                );
                $objRssFeed->PubDate = new QDateTime(QDateTime::Now);
                $objRssFeed->Language = strtolower(str_replace('_', '-', QApplication::$objUser->Language->LanguageCode));

                $strDescription = '';
                foreach(NarroSuggestion::QueryArray(QQ::Equal(QQN::NarroSuggestion()->LanguageId, QApplication::$objUser->Language->LanguageId), array(QQ::OrderBy(QQN::NarroSuggestion()->Created, 0), QQ::LimitInfo(100, 0))) as $intKey=>$objSuggestion) {

                    $strDescription .= '<tr><td>' . $objSuggestion->Text->TextValue . '</td><td>' . $objSuggestion->SuggestionValue . '</td><td>' . $objSuggestion->User->Username . '</td></tr>';
                    if ($intKey % 10 == 0 && $intKey != 0) {
                        $objItem = new QRssItem(
                                        (strlen($objSuggestion->SuggestionValue)>124)?substr($objSuggestion->SuggestionValue, 0, 124) . '...':$objSuggestion->SuggestionValue,
                                        sprintf(__HTTP_URL__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__ . '/narro_project_list.php'),
                                        '<table border="1"><tr><td>' . t('Original text') . '</td><td>' . t('Suggestion') . '</td><td>' . t('Username') . '</td></tr>' . $strDescription . '</table>'
                        );

                        $objItem->Author = $objSuggestion->User->Username;

                        $objRssFeed->AddItem($objItem);
                        $strDescription = '';
                    }
                }

                QApplication::$Cache->save($objRssFeed, sprintf('rssfeed_suggestion_%d_%d', $objProject->ProjectId, QApplication::QueryString('l')), array(), 3600);
            }

            $objRssFeed->Run();
            break;
        case 'text':
            if (!$objRssFeed = QApplication::$Cache->load(sprintf('rssfeed_text_%d_%d', $objProject->ProjectId, QApplication::QueryString('l')))) {
                $objRssFeed  = new QRssFeed(
                        t('New texts to translate'),
                        __HTTP_URL__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__,
                        t('Get the latest 100 texts to translate')
                );
                $objRssFeed->PubDate = new QDateTime(QDateTime::Now);
                $objRssFeed->Language = strtolower(str_replace('_', '-', QApplication::$objUser->Language->LanguageCode));

                $strDescription = '';
                foreach(NarroText::LoadAll(array(QQ::OrderBy(QQN::NarroText()->Created, 0), QQ::LimitInfo(100, 0))) as $intKey=>$objText) {
                    $strDescription .= '<li>' . $objText->TextValue . '</li>';
                    if ($intKey % 10 == 0 && $intKey != 0) {
                        $objItem = new QRssItem(
                                        (strlen($objText->TextValue)>124)?substr($objText->TextValue, 0, 124) . '...':$objText->TextValue,
                                        sprintf(__HTTP_URL__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__ . '/narro_project_list.php'),
                                        '<ul>' . $strDescription . '</ul>'
                        );

                        $objRssFeed->AddItem($objItem);
                        $strDescription = '';
                    }
                }

                QApplication::$Cache->save($objRssFeed, sprintf('rssfeed_text_%d_%d', $objProject->ProjectId, QApplication::QueryString('l')), array(), 3600);
            }

            $objRssFeed->Run();
            break;
        case 'context':
            break;
        case 'vote':
            break;
        default:
            exit();
    }

?>
