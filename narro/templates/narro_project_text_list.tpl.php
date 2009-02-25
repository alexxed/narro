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

    $strPageTitle = sprintf(t('Texts from the project "%s"'), $this->objNarroProject->ProjectName);

    require('includes/header.inc.php')
?>

    <?php $this->RenderBegin() ?>
        <?php $this->pnlHeader->Render() ?>
        <?php $this->pnlBreadcrumb->Render(); ?>
        <br />
        <?php $this->lblMessage->Render(); ?>
        <div style="text-align:right;padding:3px;">
            <?php echo t('Show') ?>: <?php $this->lstTextFilter->Render() ?>
            &nbsp;&nbsp;&nbsp;
            <?php echo t('Search') ?>: <?php $this->txtSearch->Render();?>&nbsp;
            <?php $this->lstSearchType->Render();?>&nbsp;
            <?php $this->btnSearch->Render(); ?>
            <br />
            <?php $this->btnMultiApprove->Render(); ?>
            <?php $this->btnMultiApproveCancel->Render(); ?>
        </div>
        <?php $this->dtgNarroContextInfo->Render() ?>
        <div style="text-align:right;padding:3px;">
            <?php $this->btnMultiApproveBottom->Render(); ?>
            <?php $this->btnMultiApproveCancelBottom->Render(); ?>
        </div>
        <?php NarroApp::ExecuteJavaScript("if (location.hash.match('p')) qc.pA('NarroProjectTextListForm', '" . $this->dtgNarroContextInfo->Paginator->ControlId . "', 'QClickEvent', location.hash.replace('#p', ''), '');"); ?>
    <?php $this->RenderEnd() ?>

<?php require('includes/footer.inc.php'); ?>
