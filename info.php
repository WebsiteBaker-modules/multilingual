<?php
/**
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS HEADER.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * info.php
 *
 * @category     Modules
 * @package      Modules_MultiLingual
 * @author       Werner v.d.Decken <wkl@isteam.de>
 * @author       Dietmar Wöllbrink <dietmar.woellbrink@websiteBaker.org>
 * @copyright    Werner v.d.Decken <wkl@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      1.6.9
 * @revision     $Revision: 2070 $
 * @link         $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/MultiLingual/info.php $
 * @lastmodified $Date: 2014-01-03 02:21:42 +0100 (Fr, 03. Jan 2014) $
 * @since        File available since 09.01.2013
 * @description  provides a flexible posibility for changeing to a translated page
 */
if (!defined('SYSTEM_RUN')) { header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); flush(); exit; }

$module_directory = 'mod_multilingual';
$module_name      = 'MultiLingual Switcher Add-on 2.1.0-dev.16';
$module_function  = 'snippet';
$module_version   = '2.1.0-dev.16';
$module_status    = '';
$module_platform  = '2.10.0';
$module_author    = 'Luisehahne';
$module_license   = 'GNU General Public License';
$module_requirements = 'min. PHP 5.6.x and WB 2.10.0 or higher';
$module_description  = 'This snippet switches between different languages';
