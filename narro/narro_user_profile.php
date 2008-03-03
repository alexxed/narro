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

    require_once('narro/narro_user_suggestions_panel.class.php');
    class NarroUserProfileForm extends QForm {
        protected $pnlUserSuggestions;
        protected $objUser;

        protected function Form_Create() {
            $strQuery = sprintf("SELECT * FROM users WHERE uid=%d", QApplication::QueryString('user'));

            if ($objResult = db_query($strQuery)) {
                $this->objUser = db_fetch_object($objResult);
            }

            $this->pnlUserSuggestions = new NarroUserSuggestionsPanel($this->objUser->uid, $this);
        }


    }

    NarroUserProfileForm::Run('NarroUserProfileForm', 'templates/narro_user_profile.tpl.php');
?>
