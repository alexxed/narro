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

    $objDateSpan = new QDateTimeSpan(time() - strtotime($_ITEM->Created));
    $strCreatedWhen = $objDateSpan->SimpleDisplay();

?>
    <div id="comment<?php echo $_CONTROL->CurrentItemIndex + 1; ?>" style="background: url(<?php echo __IMAGE_ASSETS__ ?>/../../images/bg_td1.jpg) repeat-x top;padding:3px;border:1px solid #DDDDDD;display:block">
        <span style="font-size:80%;color:gray;"><?php echo sprintf('<a href="#comment%d">#%d:</a> ', $_CONTROL->CurrentItemIndex + 1, $_CONTROL->CurrentItemIndex + 1) . sprintf(t('%s wrote %s ago:'), NarroLink::UserProfile($_ITEM->UserId, '<b>' . $_ITEM->User->Username . '</b>'), $strCreatedWhen) ?></span>
        <br />
        <span style="margin-left:5px;padding:3px;">
        <?php
        $strResult = QApplication::$PluginHandler->DisplayTextComment($_ITEM->CommentText);
        if (!QApplication::$PluginHandler->Error)
            echo nl2br(NarroString::HtmlEntities($strResult));
        else
            echo nl2br(NarroString::HtmlEntities($_ITEM->CommentText));
        ?>
        </span>
    </div>
