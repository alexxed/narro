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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php _p(QApplication::$EncodingType); ?>" />
        <?php if (isset($strPageTitle)) { ?>
            <title><?php _p($strPageTitle); ?></title>
        <?php } ?>
        <link rel="stylesheet" type="text/css" href="<?php _p(__VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__); ?>/assets/css/style.css" />
        <link rel="stylesheet" type="text/css" href="<?php _p(__VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__); ?>/assets/css/tabs.css" />
        <link rel="stylesheet" type="text/css" href="<?php _p(__VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__); ?>/assets/css/font-<?php if (QApplication::$User instanceof NarroUser) echo QApplication::$User->getPreferenceValueByName('Font size'); else echo 'medium' ?>.css" />

        <?php if (QApplication::QueryString('p')) { ?>
            <link rel="alternate" type="application/rss+xml" title="<?php echo sprintf(t('Context changes for %s'), $this->objNarroProject->ProjectName) ?>" href="rss.php?t=context_info_changes&l=<?php echo QApplication::GetLanguageId() ?>&p=<?php echo $this->objNarroProject->ProjectId ?>" />
            <link rel="alternate" type="application/rss+xml" title="<?php echo sprintf(t('Texts to translate for %s'), $this->objNarroProject->ProjectName) ?>" href="rss.php?t=text&l=<?php echo QApplication::GetLanguageId() ?>&p=<?php echo $this->objNarroProject->ProjectId ?>" />
            <link rel="alternate" type="application/rss+xml" title="<?php echo sprintf(t('Comments on texts from %s'), $this->objNarroProject->ProjectName) ?>" href="rss.php?t=textcomment&l=<?php echo QApplication::GetLanguageId() ?>&p=<?php echo $this->objNarroProject->ProjectId ?>" />
            <link rel="alternate" type="application/rss+xml" title="<?php echo sprintf(t('Translations for %s'), $this->objNarroProject->ProjectName) ?>" href="rss.php?t=suggestion&l=<?php echo QApplication::GetLanguageId() ?>&p=<?php echo $this->objNarroProject->ProjectId ?>" />
        <?php } ?>
        <?php if (QApplication::$User instanceof NarroUser) { ?>
        <link rel="alternate" type="application/rss+xml" title="<?php echo t('Context changes for all projects') ?>" href="rss.php?t=context_info_changes&l=<?php echo QApplication::GetLanguageId() ?>" />
        <link rel="alternate" type="application/rss+xml" title="<?php echo t('Texts to translate for all projects') ?>" href="rss.php?t=text&l=<?php echo QApplication::GetLanguageId() ?>" />
        <link rel="alternate" type="application/rss+xml" title="<?php echo t('Comments on texts from all projects') ?>" href="rss.php?t=textcomment&l=<?php echo QApplication::GetLanguageId() ?>" />
        <link rel="alternate" type="application/rss+xml" title="<?php echo t('Translations for all projects') ?>" href="rss.php?t=suggestion&l=<?php echo QApplication::GetLanguageId() ?>" />
        <?php } ?>
        <link type="image/x-icon" href="<?php echo __HTTP_URL__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__ ?>/assets/images/narro.ico" rel="shortcut icon"/>
        <link type="image/x-icon" href="<?php echo __HTTP_URL__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__ ?>/assets/images/narro.ico" rel="icon"/>
        <script type="text/javascript" src="<?php echo __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__?>/assets/js/table_row_highlight.js"></script>
    </head>
    <body>
        <?php if (SERVER_INSTANCE == 'dev') { ?>
            <div style="background-color:red;color:white;font-weight:bold;padding:10px;text-align: center;display:block">THIS IS A DEVELOPMENT VERSION MEANT FOR TESTING ONLY ! PLEASE DON'T WASTE YOUR TIME TRANSLATING !</div>
        <?php } ?>
        <div id="main">
