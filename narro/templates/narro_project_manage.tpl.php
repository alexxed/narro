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

    $strPageTitle = sprintf('%s :: ' . t('Manage'), $this->objNarroProject->ProjectName);

    require('includes/header.inc.php')
?>

    <?php $this->RenderBegin() ?>
        <h3><?php echo t('Project management') ?></h3>
        <p><?php echo t('Here you can edit project properties or do management related tasks.'); ?></p>
        <div style="text-align:left">
        <?php
            echo
                '<a href="narro_project_list.php">' . t('Projects') . '</a>' .
                ' -> ' .
                '<a href="narro_project_text_list.php?p=' . $this->objNarroProject->ProjectId.'">'.$this->objNarroProject->ProjectName.'</a>' .
                ' -> ' .
                t('Manage');
        ?>
        </div>
        <br />
        <?php $this->pnlLogViewer->Render(); ?>
        <br />
        <div class="dotted_box">
        <div class="dotted_box_title"><?php echo t('Import and export options'); ?></div>
        <div class="dotted_box_content">
        <label for="<?php echo $this->chkForce->ControlId ?>"><?php echo $this->chkForce->Render(false) . ' ' . t('Force operation even if a previous operation is reported to be running'); ?></label>
        <p class="instructions"><?php echo t('Cleanup the files that are used during an import or export and allow starting another operation'); ?></p>
        </div>
        </div>
        <br />
        <div class="dotted_box">
        <div class="dotted_box_title"><?php echo t('Import project'); ?></div>
        <div class="dotted_box_content">
        <?php if ($this->objNarroProject->ProjectType != NarroProjectType::Narro) { ?>
            <?php echo $this->chkValidate->Render(false) . ' ' . t('Validate the imported translations'); ?>
            <p class="instructions"><?php echo t('Mark the imported suggestions as validated.'); ?></p>
            <?php echo $this->chkOnlySuggestions->Render(false) . ' ' . t('Import only suggestions'); ?>
            <p class="instructions"><?php echo t('Do not add files, texts or contexts. Import only translation suggestions for existing texts in existing files and contexts.'); ?></p>

            <?php echo t('From an archive') . ': ' . $this->flaImportFromFile->Render(false); ?>
            <p class="instructions"><?php echo sprintf(t('The archive must contain two directories, en-US and %s, each having the same file structure. Supported formats: zip, tar.bz2, tar.gz'), QApplication::$objUser->Language->LanguageCode); ?></p>
            <p class="instructions"><?php echo sprintf(t('If you don\'t upload an archive, the import will use the directory "%s", subdirectories "%s" and "%s". You could update those directories nightly from CVS, SVN or a web address.'), __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId, 'en-US', QApplication::$objUser->Language->LanguageCode); ?></p>
        <?php } ?>
        <?php $this->btnImport->Render(); $this->objImportProgress->Render();?>
        <?php $this->lblImport->Render(); ?>
        </div>
        </div>
        <?php if ($this->objNarroProject->ProjectType != NarroProjectType::Narro) { ?>
            <br />
            <div class="dotted_box">
            <div class="dotted_box_title"><?php echo t('Export project'); ?></div>
            <div class="dotted_box_content">
            <?php echo t('Export translations using') . ': ' . $this->lstExportedSuggestion->Render(false); ?>
            <p class="instructions"><?php echo t('If you chose to use your suggestion or the most voted suggestion for each text, if you have no suggestion for a text or there aren\'t any votes, the validated suggestion will be exported instead.'); ?></p>
            <?php echo t('To an archive') . ': ' . $this->objNarroProject->ProjectId . '-' . QApplication::$objUser->Language->LanguageCode . '.tar.bz2'; ?>
            <br /><br />
            <?php $this->btnExport->Render(); $this->objExportProgress->Render();?>
            <?php $this->lblExport->Render(); ?>
            <p class="instructions"><?php echo sprintf(t('You will get an archive containing two directories, en_US and %s, each having the same file structure.'), QApplication::$objUser->Language->LanguageCode); ?></p>
            </div>
            </div>
        <?php } ?>

    <?php $this->RenderEnd() ?>

<?php require('includes/footer.inc.php'); ?>
