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

<table>
    <tr>
        <td><?php _t('Name')?></td>
        <td><?php $_CONTROL->txtProjectName->Render(); ?></td>
    </tr>
    <tr>
        <td><?php _t('Category') ?></td>
        <td><?php $_CONTROL->lstProjectCategory->Render(); ?></td>
    </tr>
    <tr>
        <td><?php _t('Type')?></td>
        <td><?php $_CONTROL->lstProjectType->Render(); ?></td>
    </tr>
    <tr>
        <td><?php _t('Description')?></td>
        <td><?php $_CONTROL->txtProjectDescription->Render(); ?></td>
    </tr>
    <tr>
        <td><?php _t('Active')?></td>
        <td><?php $_CONTROL->txtActive->Render(); ?></td>
    </tr>
    <tr>
        <td colspan="2">
            <?php $_CONTROL->btnSave->Render() ?>
            &nbsp;&nbsp;&nbsp;
            <?php $_CONTROL->btnCancel->Render() ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php $_CONTROL->btnDelete->Render() ?>
        </td>
    </tr>
</table>
<?php $_CONTROL->lblMessage->Render();?>
