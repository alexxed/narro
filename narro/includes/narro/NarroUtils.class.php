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
    class NarroUtils {
        public static function RecursiveDelete($strFilePath) {
            if (is_dir($strFilePath) && !is_link($strFilePath))
            {
                if ($hndDir = opendir($strFilePath))
                {
                    while (($strFileName = readdir($hndDir)) !== false)
                    {
                        if ($strFileName == '.' || $strFileName == '..')
                        {
                            continue;
                        }
                        if (!self::RecursiveDelete($strFilePath.'/'.$strFileName))
                        {
                            throw new Exception($strFilePath.'/'.$strFileName.' could not be deleted.');
                        }
                    }
                    closedir($hndDir);
                }
                return @rmdir($strFilePath);
            }
            return @unlink($strFilePath);
        }

        public static function RecursiveChmod($strFilePath, $intFileMode = 0666, $intDirMode = 0777) {
            if (is_dir($strFilePath) && !is_link($strFilePath))
            {
                if ($hndDir = opendir($strFilePath))
                {
                    while (($strFileName = readdir($hndDir)) !== false)
                    {
                        if ($strFileName == '.' || $strFileName == '..')
                        {
                            continue;
                        }
                        if (!self::RecursiveChmod($strFilePath.'/'.$strFileName, $intFileMode, $intDirMode))
                        {
                            throw new Exception($strFilePath.'/'.$strFileName.' could not be chmoded.');
                        }
                    }
                    closedir($hndDir);
                }
                return @chmod($strFilePath, $intDirMode);
            }
            return @chmod($strFilePath, $intFileMode);
        }
    }
?>