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

    $strPageTitle = $this->objUser->name;

    require('includes/header.inc.php');
?>

    <?php $this->RenderBegin() ?>
        <img style="float:right; border:1px dotted black; padding:5px;" src="/<?php echo $this->objUser->picture ?>" />
        <h1><?php echo $user->name ?></h1>
        <br /><br /><br />
        <?php
            $intSuggestionCount = NarroSuggestion::CountByUserId($this->objUser->uid);
            $strQuery = sprintf("SELECT COUNT(DISTINCT c.valid_suggestion_id) as cnt FROM narro_context c, narro_suggestion s WHERE c.valid_suggestion_id=s.suggestion_id AND s.user_id=%d", $this->objUser->uid);

            if ($objResult = db_query($strQuery)) {
                if ($arrDbRow = db_fetch_array($objResult)) {
                    $intValidSuggestionCount = $arrDbRow['cnt'];
                }
            }

            if ($intSuggestionCount && $intValidSuggestionCount) {
        ?>
                Dintr-un total de <?php echo $intSuggestionCount; ?> sugestii făcute de <b><?php echo $this->objUser->name; ?></b>,
                <?php echo $intValidSuggestionCount; ?> au fost validate.<br />
        <?php
            }
        ?>
        Sugestii făcute de <b><?php echo $this->objUser->name; ?></b>:
        <?php $this->pnlUserSuggestions->Render() ?>
    <?php $this->RenderEnd() ?>

<?php require('includes/footer.inc.php'); ?>
