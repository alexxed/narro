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

    $strPageTitle = sprintf((QApplication::HasPermissionForThisLang('Can suggest', $this->pnlContextSuggest->NarroContextInfo->Context->ProjectId))?t('Translate "%s"'):t('See suggestions for "%s"'),
        (strlen($this->pnlContextSuggest->NarroContextInfo->Context->Text->TextValue)>30)?mb_substr($this->pnlContextSuggest->NarroContextInfo->Context->Text->TextValue, 0, 30) . '...':$this->pnlContextSuggest->NarroContextInfo->Context->Text->TextValue);

    require('configuration/header.inc.php')
?>

    <?php $this->RenderBegin() ?>
    <?php $this->pnlHeader->Render() ?>
    <?php $this->pnlBreadcrumb->Render() ?>
    <?php $this->pnlMainTab->Render() ?>
    <?php $this->RenderEnd() ?>

<?php require('configuration/footer.inc.php'); ?>
        