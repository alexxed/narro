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
    <p>
        <?php printf(t('This is a list of texts used in "%s". Click on any translated text to start translating or approving one by one or use Mass Translate or Mass Approve to do it here in this list.'), $this->objNarroFile->FileName) ?>
        <?php echo ($_CONTROL->pnlImportFile->Display)?'<br />' . t('If you rather work offline, use the import and export buttons below. Optionally, you can upload your own source file when exporting.'):''?>
    </p>
    <?php $_CONTROL->lblMessage->Render(); ?>
    <table width="100%">
    <tr>
    <td style="text-align:left;">
        <?php
            $_CONTROL->pnlImportFile->Render();
            $_CONTROL->pnlExportFile->Render();
        ?>
    </td>
    <td style="text-align:right">
        <?php echo t('Show') ?>: <?php $_CONTROL->lstTextFilter->Render() ?> &nbsp;&nbsp;&nbsp;<?php echo t('Search') ?>: <?php $_CONTROL->txtSearch->Render(); $_CONTROL->lstSearchType->Render(); ?>&nbsp;
        <?php $_CONTROL->btnSearch->Render(); ?>
        <br />
        <?php
            $_CONTROL->btnMultiApprove->Render();
            $_CONTROL->btnMultiApproveCancel->Render();
            echo '&nbsp;&nbsp;';
            $_CONTROL->btnMultiTranslate->Render();
            $_CONTROL->btnMultiTranslateCancel->Render();
        ?>
    </td>
    </tr>
    </table>
    <?php $_CONTROL->dtgNarroContextInfo->Render() ?>
    <div style="text-align:right;padding:3px;">
        <?php $_CONTROL->btnMultiApproveBottom->Render(); ?>
        <?php $_CONTROL->btnMultiApproveCancelBottom->Render(); ?>
        &nbsp;
        <?php $_CONTROL->btnMultiTranslateBottom->Render(); ?>
        <?php $_CONTROL->btnMultiTranslateCancelBottom->Render(); ?>
    </div>
