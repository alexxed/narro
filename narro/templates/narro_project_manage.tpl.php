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

    $strPageTitle = sprintf(t('%s :: Manage'), $this->objNarroProject->ProjectName);

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
        <div class="dotted_box">
        <div class="dotted_box_title"><?php echo t('Project properties'); ?></div>
        <div class="dotted_box_content">
        <?php echo t('Name') . ': ' ?>
        <?php $this->txtProjectName->Render(); ?>
        <br />
        <?php echo t('Type') . ': ' ?>
        <?php $this->lstProjectType->Render(); ?>
        <br />
        <?php echo t('Active') . ': ' ?>
        <?php $this->lstProjectActive->Render(); ?>
        <br />
        <br />
        <?php $this->btnSaveProject->Render(); ?>
        </div>
        </div>
        <br />
        <div class="dotted_box">
        <div class="dotted_box_title"><?php echo t('Import and export options'); ?></div>
        <div class="dotted_box_content">
        <?php echo $this->chkForce->Render(false) . ' ' . t('Force operation even if a previous operation is reported to be running'); ?>
        <p class="instructions"><?php echo t('Cleanup the files that are used during an import or export and allow starting another operation'); ?></p>
        </div>
        </div>
        <br />
        <div class="dotted_box">
        <div class="dotted_box_title"><?php echo t('Import project'); ?></div>
        <div class="dotted_box_content">
        <?php echo $this->chkValidate->Render(false) . ' ' . t('Validate the imported translations'); ?>
        <p class="instructions"><?php echo t('Mark the imported suggestions as validated.'); ?></p>
        <?php echo $this->chkOnlySuggestions->Render(false) . ' ' . t('Import only suggestions'); ?>
        <p class="instructions"><?php echo t('Do not add files, texts or contexts. Import only translation suggestions for existing texts.'); ?></p>

        <p class="instructions"><?php echo t('You might want to choose to import from a directory where you have a fresh checkout. You can have a cron to do this on a regular basis.'); ?></p>
        <?php echo t('From a directory') . ': ' . $this->txtImportFromDirectory->Render(false); ?>
        <p class="instructions"><?php echo t('Since option one is not available to most people, uploading an archive might be the best option.'); ?></p>
        <?php echo t('From an archive') . ': ' . $this->filImportFromFile->Render(false); ?>
        <br /><br />
        <?php $this->btnImport->Render(); $this->objImportProgress->Render();?>
        <?php $this->lblImport->Render(); ?>
        </div>
        </div>
        <br />
        <div class="dotted_box">
        <div class="dotted_box_title"><?php echo t('Export project'); ?></div>
        <div class="dotted_box_content">
        <?php echo t('Export translations using') . ': ' . $this->lstExportedSuggestion->Render(false); ?>
        <p class="instructions"><?php echo t('If you chose to use your suggestion or the most voted suggestion for each text, if you have no suggestion for a text or there aren\'t any votes, the validated suggestion will be exported instead.'); ?></p>

        <p class="instructions"><?php echo t('You might want to choose to export to a directory where you have a fresh checkout. After the export is done, just go
        to that directory and commit your changes to the versioning system.'); ?>
        </p>
        <?php echo t('To a directory') . ': ' . $this->txtExportToDirectory->Render(false); ?>
        <p class="instructions"><?php echo t('Most probably option one isn\'t available to most people, so downloading an archive to copy it over a fresh local checkout
        might be the best option.'); ?></p>
        <?php echo t('To an archive') . ': '; ?>
        <br /><br />
        <?php $this->btnExport->Render(); $this->objExportProgress->Render();?>
        <?php $this->lblExport->Render(); ?>
        </div>
        </div>

        <?php if (QApplication::$objUser->hasPermission('Can delete project')) { ?>
            <br />
            <div class="dotted_box">
            <div class="dotted_box_title">Project maintenance</div>
            <div class="dotted_box_content">
            <p class="instructions">Sometimes, it might help to delete contexts to clean up the database a bit. Before doing this, please export your work, you will loose all your validations.
            You will also loose context comments for this project. Translations and texts are kept, and you can import your project to recreate the contexts any time you want.
            </p>
            <?php $this->btnDelProjectContexts->Render(); ?>
            <p class="instructions">Sometimes, it might help to delete files to clean up the database a bit. Before doing this, please export your work, you will loose all your validations.
            You will also loose contexts and context comments for this project. Translations and texts are kept, and you can import your project to recreate the contexts any time you want.
            </p>
            <?php $this->btnDelProjectFiles->Render(); ?>
            <p class="instructions">By deleting a project, you delete the files and contexts associated with it. You will also loose context comments for this project.
            Translations and texts are kept, and you can import your project to recreate the contexts any time you want.
            </p>
            <?php $this->btnDelProject->Render(); ?>
            </div>
            </div>
        <?php } ?>


    <?php $this->RenderEnd() ?>

<?php require('includes/footer.inc.php'); ?>
