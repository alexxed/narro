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

    $strPageTitle = sprintf((user_access('narro suggest'))?QApplication::Translate('Translate "%s"'):QApplication::Translate('See suggestions for "%s"'),
        (strlen($this->objNarroTextContext->Text->TextValue)>30)?substr($this->objNarroTextContext->Text->TextValue, 0, 30) . '...':$this->objNarroTextContext->Text->TextValue);

    require('includes/header.inc.php')
?>

    <?php $this->RenderBegin() ?>
        <div class="title_action" style="width:100%;display:block;">
            <div style="float:left">
            <?php $this->pnlNavigator->Render(); ?>
            </div>
            <br style="clear:both" />
        </div>
        <br class="item_divider" />

        <?php _t('Text to translate:'); ?>
        <div style="border:1px dotted black;padding:5px;background-color:lightgreen;" title="Textul original">
            <?php $this->pnlOriginalText->Render(); ?>
        </div>
        <div style="border:1px dotted black;border-top:0px;font-size:80%;padding:5px;background-color:white;" title="Detalii despre locul în care apare textul">
            <?php $this->pnlContext->Render(); ?>
        </div>

        <?php $this->pnlSuggestionList->Render(); ?>

        <?php if (user_access('narro suggest')) { ?>
            <?php _t('Your suggestion:'); ?>
            <br />
            <?php $this->pnlSpellcheckText->Render(); ?>

            <table cellspacing="3" border="0" style="border-width:0px;border-collapse:separate;width:100%;margin:0px">
            <tr>
            <td width="60%" valign="top" style="padding-left:0px;border:0px">
                <?php $this->txtSuggestionValue->Render("Rows=10"); ?>
                <?php $this->pnlDiacritics->Render(); ?>
            </td>
            <td width="40%" valign="top" style="padding-left:0px;border:0px">
                <?php $this->btnSave->Render() ?>
                <br />
                <?php $this->chkGoToNext->Render() ?> <label for="<?php echo $this->chkGoToNext->ControlId ?>"><?php _t('After, go to the next text') ?></label>
                <br />
                <?php $this->chkIgnoreSpellcheck->Render() ?> <label for="<?php echo $this->chkIgnoreSpellcheck->ControlId ?>"><?php _t('Ignore spellchecking') ?></label>
                <?php if (user_access('validate')) { ?>
                    <br />
                    <?php $this->chkValidate->Render() ?> <label for="<?php echo $this->chkValidate->ControlId ?>"><?php _t('Validate') ?></label>
                <?php } ?>
            </td>
            </tr>
            </table>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php $this->btnPrevious100->Render() ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php $this->btnPrevious->Render() ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php $this->btnNext->Render(); ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php $this->btnNext100->Render(); ?>

            <?php if (user_access('validate')) { ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php //$this->btnKeepUntranslated->Render() ?>
            <?php } ?>
            <?php $this->lblMessage->Render() ?>
        <?php } ?>

        <?php if($this->txtSuggestionValue->Display) $this->txtSuggestionValue->Focus(); ?>

    <?php $this->RenderEnd() ?>

<?php require('includes/footer.inc.php'); ?>