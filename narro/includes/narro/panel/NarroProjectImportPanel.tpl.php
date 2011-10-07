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
?>
<?php if ($_CONTROL->pnlTextsSource->Display) { ?>
    <div class="section_title"><?php _t('Texts')?></div>
    <div class="section">
    <?php _t('Where are the texts to translate?')?>
    <?php $_CONTROL->pnlTextsSource->Render() ?>
    </div>
<?php } ?>
<div class="section_title"><?php _t('Translations')?></div>
<div class="section">
<?php _t('Where are the translated texts?')?>
<?php $_CONTROL->pnlTranslationsSource->Render(); ?>
</div>

<div class="section_title"><?php _t('Options')?></div>
<div class="section">
<?php
$_CONTROL->chkApproveImportedTranslations->RenderWithName();
$_CONTROL->chkApproveOnlyNotApproved->RenderWithName();
$_CONTROL->chkImportOnlyTranslations->RenderWithName();
$_CONTROL->chkImportUnchangedFiles->RenderWithName();
$_CONTROL->chkDontCheckEqual->RenderWithName();
?>
</div>
<?php
$_CONTROL->pnlLogViewer->Render();
$_CONTROL->btnImport->Render();
$_CONTROL->btnKillProcess->Render();
$_CONTROL->objImportProgress->Render();
$_CONTROL->lblImport->Render()
?>

