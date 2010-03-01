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
    <?php $_CONTROL->pnlBreadcrumb->Render(); ?>
    <?php $_CONTROL->lblMessage->Render(); ?>
    <div style="text-align:right">
        <?php echo t('Show') ?>: <?php $_CONTROL->lstTextFilter->Render() ?> &nbsp;&nbsp;&nbsp;<?php echo t('Search') ?>: <?php $_CONTROL->txtSearch->Render(); $_CONTROL->lstSearchType->Render(); ?>&nbsp;
        <?php $_CONTROL->btnSearch->Render(); ?>
        <br />
        <?php $_CONTROL->btnMultiApprove->Render(); ?>
        <?php $_CONTROL->btnMultiApproveCancel->Render(); ?>
        <?php $_CONTROL->btnMultiTranslate->Render(); ?>
        <?php $_CONTROL->btnMultiTranslateCancel->Render(); ?>
    </div>
    <br />
    <?php $_CONTROL->dtgNarroContextInfo->Render() ?>
    <div style="text-align:right;padding:3px;">
        <?php $_CONTROL->btnMultiApproveBottom->Render(); ?>
        <?php $_CONTROL->btnMultiApproveCancelBottom->Render(); ?>
        <?php $_CONTROL->btnMultiTranslateBottom->Render(); ?>
        <?php $_CONTROL->btnMultiTranslateCancelBottom->Render(); ?>
    </div>
