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

    $strPageTitle = sprintf(t('Texts from the file "%s"'), $this->objNarroFile->FileName);
    require('includes/header.inc.php')
?>

    <?php $this->RenderBegin() ?>
        <?php $this->pnlHeader->Render() ?>
        <div>
        <?php echo
        NarroLink::ProjectList(t('Projects')) .
        ' / ' .
        NarroLink::ProjectTextList($this->objNarroFile->Project->ProjectId, 1, 1, '', $this->objNarroFile->Project->ProjectName) .
        ' / ' .
        NarroLink::ProjectFileList($this->objNarroFile->Project->ProjectId, null, t('Files'));
        if ($this->objNarroFile) {
            $arrPaths = explode('/', $this->objNarroFile->FilePath);
            $strProgressivePath = '';
            if (is_array($arrPaths)) {
                /**
                 * remove the first part that is empty because paths begin with /
                 * and the last part that will be displayed unlinked
                 */
                unset($arrPaths[count($arrPaths) - 1]);
                unset($arrPaths[0]);
                foreach($arrPaths as $strPathPart) {
                    $strProgressivePath .= '/' . $strPathPart;
                    echo ' / ' .
                        NarroLink::ProjectFileList(
                                $this->objNarroFile->ProjectId,
                                $strProgressivePath,
                                $strPathPart
                        );
                }
            }
        }
        echo ' / ' . $this->objNarroFile->FileName; ?>
        </div>
        <br />
        <div style="text-align:right">
            <?php echo t('Show') ?>: <?php $this->lstTextFilter->Render() ?> &nbsp;&nbsp;&nbsp;<?php echo t('Search') ?>: <?php $this->txtSearch->Render(); $this->lstSearchType->Render(); ?>&nbsp;
            <?php $this->btnSearch->Render(); ?>
        </div>
        <br />
        <?php $this->dtgNarroContextInfo->Render() ?>

    <?php $this->RenderEnd() ?>

<?php require('includes/footer.inc.php'); ?>
