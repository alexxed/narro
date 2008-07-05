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
        case 'context_info_changes':
            if (!$objRssFeed = QApplication::$Cache->load(sprintf('rssfeed_context_info_changes_%d_%d', $objProject->ProjectId, QApplication::QueryString('l')))) {
                $objRssFeed  = new QRssFeed(
                        sprintf(
                            t('Context information changes in %s'),
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
                foreach(NarroContextInfo::QueryArray(QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::$objUser->Language->LanguageId), array(QQ::OrderBy(QQN::NarroContextInfo()->Modified, 0), QQ::LimitInfo(100, 0))) as $intKey=>$objNarroContextInfo) {
                    $strContextLink = sprintf(
                            __HTTP_URL__ . 
                            __VIRTUAL_DIRECTORY__ . 
                            __SUBDIRECTORY__ . 
                            '/narro_context_suggest.php?p=%d&c=%d', 
                            $objNarroContextInfo->Context->ProjectId, $objNarroContextInfo->ContextId
                    );
                    
                    $strProjectLink = sprintf(
                            __HTTP_URL__ . 
                            __VIRTUAL_DIRECTORY__ . 
                            __SUBDIRECTORY__ . 
                            '/narro_project_text_list.php?p=%d', 
                            $objNarroContextInfo->Context->ProjectId
                    );
                    
                    $strUserLink = sprintf(
                            __HTTP_URL__ . 
                            __VIRTUAL_DIRECTORY__ . 
                            __SUBDIRECTORY__ . 
                            '/narro_user_profile.php?u=%d', 
                            $objNarroContextInfo->ValidatorUserId
                    );
                    
                    $objItem = new QRssItem(
                        $objNarroContextInfo->Context->Project->ProjectName . 
                            ' :: ' . 
                            ((strlen($objNarroContextInfo->Context->Text->TextValue)>124)?substr($objNarroContextInfo->Context->Text->TextValue, 0, 124) . '...':$objNarroContextInfo->Context->Text->TextValue),
                        $strContextLink
                    );
                    
                    $objItem->Description = 
                        sprintf('<p>' . t('Project') . ': <a href="%s">%s</a></p>', $strProjectLink, $objNarroContextInfo->Context->Project->ProjectName) .
                        sprintf('<p>' . t('Context') . ': <a href="%s">%s</a></p>', $strContextLink, $objNarroContextInfo->Context->Context) .
                        sprintf('<p>' . t('Original text') . ': %s</p>', 
                            (
                                ($objNarroContextInfo->TextAccessKey)?
                                    NarroString::Replace($objNarroContextInfo->TextAccessKey, '<u>' . $objNarroContextInfo->TextAccessKey . '</u>', $objNarroContextInfo->Context->Text->TextValue, 1):
                                    $objNarroContextInfo->Context->Text->TextValue
                            )
                        ) .
                        (
                            ($objNarroContextInfo->ValidSuggestionId)?
                                sprintf('<p>' . t('Validated suggestion') . ': %s</p>', 
                                    (
                                        ($objNarroContextInfo->TextAccessKey)?
                                            NarroString::Replace($objNarroContextInfo->SuggestionAccessKey, '<u>' . $objNarroContextInfo->SuggestionAccessKey . '</u>', $objNarroContextInfo->ValidSuggestion->SuggestionValue, 1):
                                            $objNarroContextInfo->ValidSuggestion->SuggestionValue
                                    )
                                )
                                :
                                ''
                        ) .
                        (($objNarroContextInfo->HasSuggestions)?
                            sprintf(t('The text has %s suggestions'), NarroSuggestion::CountByTextId($objNarroContextInfo->Context->TextId)):
                            t('The text has no suggestions')) .
                        (
                            ($objNarroContextInfo->ValidSuggestionId)?
                                sprintf('<p>' . t('Validated by') . ': <a href="%s">%s</a>', $strUserLink, ($objNarroContextInfo->ValidSuggestionId)?$objNarroContextInfo->ValidatorUser->Username:''):
                                ''
                        );
                        
                    if ($objNarroContextInfo->HasComments)
                        $objItem->Comments = sprintf(t('%d comments'), NarroContextComment::QueryCount(QQ::AndCondition(QQ::Equal(QQN::NarroContextComment()->ContextId, $objNarroContextInfo->ContextId), QQ::Equal(QQN::NarroContextComment()->LanguageId, $objNarroContextInfo->LanguageId))));
                    
                    $objItem->PubDate = new QDateTime($objNarroContextInfo->Modified);

                    $objItem->Author = ($objNarroContextInfo->ValidSuggestionId)?$objNarroContextInfo->ValidatorUser->Username:'';

                    $objRssFeed->AddItem($objItem);
                    $strDescription = '';
                }

                QApplication::$Cache->save($objRssFeed, sprintf('rssfeed_context_info_changes_%d_%d', $objProject->ProjectId, QApplication::QueryString('l')), array(), 3600);
            }

            $objRssFeed->Run();
            break;
        case 'vote':
            break;
            
        default:
            exit();
    }

?>
