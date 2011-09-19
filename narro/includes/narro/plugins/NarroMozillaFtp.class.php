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

    class NarroMozillaFtp extends NarroPlugin {

        public function __construct() {
            parent::__construct();
            $this->blnEnable = false;
            $this->strName = t('Mozilla FTP');
            $this->Enable();
            $this->blnEnable = $this->blnEnable && extension_loaded('ftp');
        }
        
        public function DisplayInProjectListInProgressColumn(NarroProject $objProject, $strText = '') {
            $strExportText = '';

            switch($objProject->ProjectName) {
                case 'Fennec Aurora':
                    $strCacheId = __CLASS__ . QApplication::GetLanguageId() . 'p' . $objProject->ProjectId;
                    $arrData = QApplication::$Cache->load($strCacheId);
                    if ($arrData)
                        return $arrData;
                    // set up basic connection
                    $conn_id = ftp_connect('ftp.mozilla.org');
                    
                    // login with username and password
                    $login_result = ftp_login($conn_id, 'anonymous', 'user@domain.com');
                    
                    // get contents of the current directory
                    $contents = ftp_nlist($conn_id, "/pub/mobile/nightly/latest-mozilla-aurora-linux-l10n");
                    foreach($contents as $strFile) {
                        if (preg_match('/\/fennec\-(.*)\.' . preg_quote(QApplication::$TargetLanguage->LanguageCode). '\.linux\-i686\.tar\.bz2$/', $strFile, $arrMatches)) {
                            $objDateSpan = new QDateTimeSpan(time() - ftp_mdtm($conn_id, $strFile));
                            $strLinuxNightly = sprintf('<a title="Download a localized nightly build %s built %s ago" href="ftp://ftp.mozilla.org%s">linux i686</a>', $arrMatches[1], $objDateSpan->SimpleDisplay(), $strFile);
                        }
                    }
                    
                    $contents = ftp_nlist($conn_id, "/pub/mobile/nightly/latest-mozilla-aurora-win32-l10n");
                    foreach($contents as $strFile) {
                        if (preg_match('/\/fennec\-(.*)\.' . preg_quote(QApplication::$TargetLanguage->LanguageCode). '\.win32\.zip$/', $strFile, $arrMatches)) {
                            $objDateSpan = new QDateTimeSpan(time() - ftp_mdtm($conn_id, $strFile));
                            $strWindowsNightly = sprintf('<a title="Download a localized nightly build %s built %s ago" href="ftp://ftp.mozilla.org%s">win32</a>', $arrMatches[1], $objDateSpan->SimpleDisplay(), $strFile);
                        }
                    }   
                    
                    $contents = ftp_nlist($conn_id, "/pub/mobile/nightly/latest-mozilla-aurora-macosx-l10n");
                    foreach($contents as $strFile) {
                        if (preg_match('/\/fennec\-(.*)\.' . preg_quote(QApplication::$TargetLanguage->LanguageCode). '\.mac\.dmg$/', $strFile, $arrMatches)) {
                            $objDateSpan = new QDateTimeSpan(time() - ftp_mdtm($conn_id, $strFile));
                            $strMacNightly = sprintf('<a title="Download a localized nightly build %s built %s ago" href="ftp://ftp.mozilla.org%s">mac</a>', $arrMatches[1], $objDateSpan->SimpleDisplay(), $strFile);
                        }
                    }                
                    
                    $contents = ftp_nlist($conn_id, "/pub/mobile/nightly/latest-mozilla-aurora-android");
                    foreach($contents as $strFile) {
                        if (preg_match('/\/fennec\-(.*)\.multi\.android\-arm\.apk$/', $strFile, $arrMatches)) {
                            $objDateSpan = new QDateTimeSpan(time() - ftp_mdtm($conn_id, $strFile));
                            $strAndroidNightly = sprintf('<a title="Download a localized nightly build %s built %s ago" href="ftp://ftp.mozilla.org%s">android</a>', $arrMatches[1], $objDateSpan->SimpleDisplay(), $strFile);
                        }
                    }
                    
                    $arrData = array($objProject, sprintf('nightly: %s, %s, %s, %s', @$strWindowsNightly, @$strLinuxNightly, @$strAndroidNightly, @$strMacNightly));
                    
                    QApplication::$Cache->save($arrData, $strCacheId, array(), 3600);
    
                    return $arrData;
                    
                case 'Firefox Aurora':
                    $strCacheId = __CLASS__ . QApplication::GetLanguageId() . 'p' . $objProject->ProjectId;
                    $arrData = QApplication::$Cache->load($strCacheId);
                    if ($arrData)
                        return $arrData;
                    // set up basic connection
                    $conn_id = ftp_connect('ftp.mozilla.org');
                
                    // login with username and password
                    $login_result = ftp_login($conn_id, 'anonymous', 'user@domain.com');
                
                    // get contents of the current directory
                    $contents = ftp_nlist($conn_id, "/pub/firefox/nightly/latest-mozilla-aurora-l10n/");
                    foreach($contents as $strFile) {
                        if (preg_match('/\/firefox\-(.*)\.' . preg_quote(QApplication::$TargetLanguage->LanguageCode). '\.linux\-i686\.tar\.bz2$/', $strFile, $arrMatches)) {
                            $objDateSpan = new QDateTimeSpan(time() - ftp_mdtm($conn_id, $strFile));
                            $strLinuxNightly = sprintf('<a title="Download a localized nightly build %s built %s ago" href="ftp://ftp.mozilla.org%s">linux i686</a>', $arrMatches[1], $objDateSpan->SimpleDisplay(), $strFile);
                        }
                    }
                
                    foreach($contents as $strFile) {
                        if (preg_match('/\/firefox\-(.*)\.' . preg_quote(QApplication::$TargetLanguage->LanguageCode). '\.linux\-x86\_64\.tar\.bz2$/', $strFile, $arrMatches)) {
                            $objDateSpan = new QDateTimeSpan(time() - ftp_mdtm($conn_id, $strFile));
                            $strLinux64Nightly = sprintf('<a title="Download a localized nightly build %s built %s ago" href="ftp://ftp.mozilla.org%s">linux x86_64</a>', $arrMatches[1], $objDateSpan->SimpleDisplay(), $strFile);
                        }
                    }
                
                    foreach($contents as $strFile) {
                        if (preg_match('/\/firefox\-(.*)\.' . preg_quote(QApplication::$TargetLanguage->LanguageCode). '\.win32\.zip$/', $strFile, $arrMatches)) {
                            $objDateSpan = new QDateTimeSpan(time() - ftp_mdtm($conn_id, $strFile));
                            $strWindowsNightly = sprintf('<a title="Download a localized nightly build %s built %s ago" href="ftp://ftp.mozilla.org%s">win32</a>', $arrMatches[1], $objDateSpan->SimpleDisplay(), $strFile);
                        }
                    }
                
                    foreach($contents as $strFile) {
                    if (preg_match('/\/firefox\-(.*)\.' . preg_quote(QApplication::$TargetLanguage->LanguageCode). '\.mac\.dmg$/', $strFile, $arrMatches)) {
                            $objDateSpan = new QDateTimeSpan(time() - ftp_mdtm($conn_id, $strFile));
                            $strMacNightly = sprintf('<a title="Download a localized nightly build %s built %s ago" href="ftp://ftp.mozilla.org%s">mac</a>', $arrMatches[1], $objDateSpan->SimpleDisplay(), $strFile);
                        }
                    }
                
                    $arrData = array($objProject, sprintf('nightly: %s, %s, %s, %s', @$strWindowsNightly, @$strLinuxNightly, @$strLinux64Nightly, @$strMacNightly));
                
                    QApplication::$Cache->save($arrData, $strCacheId, array(), 3600);
                
                    return $arrData;     
                case 'Thunderbird Aurora':
                    $strCacheId = __CLASS__ . QApplication::GetLanguageId() . 'p' . $objProject->ProjectId;
                    $arrData = QApplication::$Cache->load($strCacheId);
                    if ($arrData)
                    return $arrData;
                    // set up basic connection
                    $conn_id = ftp_connect('ftp.mozilla.org');
                
                    // login with username and password
                    $login_result = ftp_login($conn_id, 'anonymous', 'user@domain.com');
                
                    // get contents of the current directory
                    $contents = ftp_nlist($conn_id, "/pub/thunderbird/nightly/latest-comm-aurora-l10n/");
                    foreach($contents as $strFile) {
                        if (preg_match('/\/thunderbird\-(.*)\.' . preg_quote(QApplication::$TargetLanguage->LanguageCode). '\.linux\-i686\.tar\.bz2$/', $strFile, $arrMatches)) {
                            $objDateSpan = new QDateTimeSpan(time() - ftp_mdtm($conn_id, $strFile));
                            $strLinuxNightly = sprintf('<a title="Download a localized nightly build %s built %s ago" href="ftp://ftp.mozilla.org%s">linux i686</a>', $arrMatches[1], $objDateSpan->SimpleDisplay(), $strFile);
                        }
                    }
                
                    foreach($contents as $strFile) {
                        if (preg_match('/\/thunderbird\-(.*)\.' . preg_quote(QApplication::$TargetLanguage->LanguageCode). '\.linux\-x86\_64\.tar\.bz2$/', $strFile, $arrMatches)) {
                            $objDateSpan = new QDateTimeSpan(time() - ftp_mdtm($conn_id, $strFile));
                            $strLinux64Nightly = sprintf('<a title="Download a localized nightly build %s built %s ago" href="ftp://ftp.mozilla.org%s">linux x86_64</a>', $arrMatches[1], $objDateSpan->SimpleDisplay(), $strFile);
                        }
                    }
                
                    foreach($contents as $strFile) {
                        if (preg_match('/\/thunderbird\-(.*)\.' . preg_quote(QApplication::$TargetLanguage->LanguageCode). '\.win32\.zip$/', $strFile, $arrMatches)) {
                            $objDateSpan = new QDateTimeSpan(time() - ftp_mdtm($conn_id, $strFile));
                            $strWindowsNightly = sprintf('<a title="Download a localized nightly build %s built %s ago" href="ftp://ftp.mozilla.org%s">win32</a>', $arrMatches[1], $objDateSpan->SimpleDisplay(), $strFile);
                        }
                    }
                
                    foreach($contents as $strFile) {
                        if (preg_match('/\/thunderbird\-(.*)\.' . preg_quote(QApplication::$TargetLanguage->LanguageCode). '\.mac\.dmg$/', $strFile, $arrMatches)) {
                            $objDateSpan = new QDateTimeSpan(time() - ftp_mdtm($conn_id, $strFile));
                            $strMacNightly = sprintf('<a title="Download a localized nightly build %s built %s ago" href="ftp://ftp.mozilla.org%s">mac</a>', $arrMatches[1], $objDateSpan->SimpleDisplay(), $strFile);
                        }
                    }
                
                    $arrData = array($objProject, sprintf('nightly: %s, %s, %s, %s', @$strWindowsNightly, @$strLinuxNightly, @$strLinux64Nightly, @$strMacNightly));
                
                    QApplication::$Cache->save($arrData, $strCacheId, array(), 3600);
                
                    return $arrData;
                default:
            }
        }
    }
?>