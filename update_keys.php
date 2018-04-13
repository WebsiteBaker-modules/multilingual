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
 * update_keys.php
 *
 * @category     Modules
 * @package      Modules_MultiLingual
 * @author       Werner v.d.Decken <wkl@isteam.de>
 * @author       Dietmar Wöllbrink <dietmar.woellbrink@websiteBaker.org>
 * @copyright    Werner v.d.Decken <wkl@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      1.6.8
 * @revision     $Revision: 2090 $
 * @link         $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/MultiLingual/update_keys.php $
 * @lastmodified $Date: 2014-01-19 23:08:22 +0100 (So, 19. Jan 2014) $
 * @since        File available since 09.01.2013
 * @description  xyz
 */

// Create new admin object
if(!defined('WB_URL'))
{
    $config_file = dirname(dirname(__DIR__)).'/config.php';
    if(file_exists($config_file) && !defined('SYSTEM_RUN'))
    {
        require($config_file);
        if (!is_readable(WB_PATH.'/modules/admin.php')){
            throw new Exception('Cannot read /modules/admin.php');
        }
        if (!is_readable(WB_PATH.'/modules/SimpleRegister.php')){
            throw new Exception('Cannot read /modules/SimpleRegister.php');
        }
    }
}

$sAddonAbsPath = __DIR__;
$sAddonName = basename($sAddonAbsPath);

// Get page id
if(!class_exists('admin')){ include(WB_PATH.'/framework/class.admin.php'); }
// Include WB admin wrapper script
// Tells script to update when this page was last updated
$update_when_modified = false;
require(WB_PATH.'/modules/admin.php');
$oTrans = Translate::getInstance();
$oTrans->enableAddon( 'modules/'.basename(__DIR__) );

require(WB_PATH.'/modules/SimpleRegister.php');

$temp_page_id = intval( $page_id );
$sBacklink = ADMIN_URL.'/pages/settings.php?page_id='.$temp_page_id ;
if (!class_exists('m_mod_multilingual_Lib',false)){require __DIR__.'/Lib.php';}
// check for page languages
$oPageLang = new m_mod_multilingual_Lib();
$oPageLang->initialize($oReg);

$Result = $oPageLang->updateDefaultPagesCode();

if($database->is_error())
{
    $admin->print_error($database->get_error(), $sBacklink);
} else {
    $admin->print_success($oTrans->MESSAGE_PAGES_UPDATE_SETTINGS, $sBacklink );
}
/**
 * Create repeated string
 * @param integer $iRepeats  number of repetitions
 * @param string  $sString   string to use for one indent (default: \t)
 * @return string created string with repetitions of $sString
 * @description create a string depending on number of repeats and a string for each repeat<br />
 *              Gives a way to generate pretty formatted HTML code being outputted, by providing<br />
 *              a certain number of TABs or SPACEs, according to the indent level.
 */
    function spacer($iRepeats = 1, $sString = "\t"){
        return str_repeat($sString, max(0, intval($iRepeats)));
    }
