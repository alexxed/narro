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

    $strPageTitle = sprintf(t('%s :: File list'), $this->objNarroProject->ProjectName);
    if (QApplication::QueryString('pf')>0)
        $strPageTitle = sprintf(t('%s :: %s :: File list'), $this->objNarroProject->ProjectName, $this->objParentFile->FileName);

    require('includes/header.inc.php')
?>

    <?php $this->RenderBegin() ?>
        <?php $this->pnlHeader->Render() ?>
        <div>
            <div style="text-align:left">
            <?php
                $strText =
                    NarroLink::ProjectList(t('Projects')) .
                    ' / ' . NarroLink::ProjectTextList($this->objNarroProject->ProjectId, 1, 1, '', $this->objNarroProject->ProjectName) .
                    ' / ' . NarroLink::ProjectFileList($this->objNarroProject->ProjectId, null, t('Files'));

                if ($this->objParentFile) {
                    $arrPaths = explode('/', $this->objParentFile->FilePath);
                    $strProgressivePath = '';
                    if (is_array($arrPaths))
                        foreach($arrPaths as $strPathPart) {
                            if (!$strPathPart || $strPathPart == $this->objParentFile->FileName) continue;
                            $strProgressivePath .= '/' . $strPathPart;
                            $strText .= ' / ' .
                                NarroLink::ProjectFileList(
                                        $this->objNarroProject->ProjectId,
                                        $strProgressivePath,
                                        $strPathPart
                                );
                        }
                }

                echo $strText . ' / ' . $this->objParentFile->FileName;
            ?>
            </div>
            <div style="text-align:right;">
                <?php $this->chkShowHierarchy->Render();?> <label for="<?php echo $this->chkShowHierarchy->ControlId; ?>"><?php echo t('Show hierarchy') ?></label>
                &nbsp;&nbsp;&nbsp;
                <?php $this->chkShowFolders->Render();?>  <label for="<?php echo $this->chkShowFolders->ControlId; ?>"><?php echo t('Show folders') ?></label>
            </div>
            <br />
        </div>
        <br />

        <?php $this->dtgNarroFile->Render() ?>

    <?php $this->RenderEnd() ?>

<?php require('includes/footer.inc.php'); ?>
