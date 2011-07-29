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
    <?php if ($_CONTROL->NarroText->HasComments) { ?>
    <p><?php echo sprintf(t('This is a debate on the text "<i>%s</i>"'), NarroString::HtmlEntities($_CONTROL->NarroText->TextValue)); ?></p>
    <?php } ?>
    <?php $_CONTROL->dtgNarroTextComment->Render() ?>
    <?php if (QApplication::HasPermissionForThisLang('Can comment', QApplication::QueryString('p'))) { ?>
    <p>
        <?php _t('If you want to debate this text, add your opinion below.'); ?>
        <br />
        <?php $_CONTROL->txtNarroTextComment->Render(); ?>
        <br />
        <span class="instructions"><?php _t("If you have a valid email address in your profile, you'll receive notifications of future comments."); ?></span>
        <br />
        <?php $_CONTROL->btnAddTextComment->Render(); ?>
    </p>
    <?php } elseif (QApplication::GetUserId() == 0) {
              echo '<p>' . sprintf(t('You can debate texts if you are logged in. <a href="%s">Register</a> or <a href="%s">Log in</a> if you already have an account or an OpenId.'), 'narro_register.php?l=' . QApplication::$TargetLanguage->LanguageCode, 'login.php?l=' . QApplication::$TargetLanguage->LanguageCode) . '</p>';
          }
    ?>
