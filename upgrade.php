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
 * uprade.php
 *
 * @category     Modules
 * @package      Modules_MultiLingual
 * @author       Werner v.d.Decken <wkl@isteam.de>
 * @author       Dietmar Wöllbrink <dietmar.woellbrink@websiteBaker.org>
 * @copyright    Werner v.d.Decken <wkl@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      1.6.9
 * @revision     $Revision: 2070 $
 * @link         $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/MultiLingual/upgrade.php $
 * @lastmodified $Date: 2014-01-03 02:21:42 +0100 (Fr, 03. Jan 2014) $
 * @since        File available since 09.01.2013
 * @description  provides a flexible posibility for changeing to a translated page
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if (!defined('SYSTEM_RUN')) { header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); flush(); exit; }
/* -------------------------------------------------------- */

if (!function_exists('mod_MultiLingual_upgrade')){
    function mod_MultiLingual_upgrade($bDebug=false) {
        global $OK ,$FAIL, $database, $oReg;
        $globalStarted = preg_match('/upgrade\-script\.php$/', $_SERVER["SCRIPT_NAME"]);
        $sWbVersion = ($globalStarted && defined('VERSION')?VERSION:WB_VERSION);
        if (version_compare($sWbVersion, '2.10.0', '<=')){
            throw new Exception ('It is not possible to install from WebsiteBaker Versions before 2.10.0');
        }
        if (is_writable(WB_PATH.'/temp/cache')) {
            Translate::getInstance()->clearCache();
        }
        $msg = array();
// change table structure
        $sTable = TABLE_PREFIX.'pages';
        $sFieldName = 'page_code';
        $sDescription = "INT NOT NULL DEFAULT '0' AFTER `language`";
        if(!$database->field_add($sTable,$sFieldName,$sDescription)) {
            $msg[] = ''.$database->get_error();
        } else {
            $msg[] = 'Field ( `page_code` ) description has been add successfully'." $OK";
        }
// Work-out if we should check old format for existing page_code
        $sql = 'DESCRIBE `'.TABLE_PREFIX.'pages` `page_code`';
        $field_sql = $database->query($sql);
//        $field_set = $field_sql->numRows();
        $format = $field_sql->fetchRow(MYSQLI_ASSOC) ;
        // upgrade only if old format
        if($format['Type'] == 'varchar(255)' )
        {
            $sql = 'SELECT `page_code`,`page_id` FROM `'.TABLE_PREFIX.'pages` ORDER BY `page_id`';
            if($query_code = $database->query($sql))
            {
                // extract page_id from old format
                $pattern = '/(?<=_)([0-9]{1,11})/s';
                while( $page  = $query_code->fetchRow(MYSQLI_ASSOC))
                {
                    preg_match($pattern, $page['page_code'], $array);
                    $page_code = $array[0];
                    $page_id =  $page['page_id'];
                    $sql  = 'UPDATE `'.TABLE_PREFIX.'pages` SET ';
                    $sql .= ((empty($array[0])) ? '`page_code` = 0 ' : '`page_code` = '.$page_code.' ');
                    $sql .= 'WHERE `page_id` = '.$page_id;
                    $database->query($sql);
                }
                $field_set = $database->field_modify($sTable,$sFieldName,$sDescription);
                $msg[] = 'Field ( `page_code` ) description has been changed successfully'." $OK";
// only for upgrade-script
                if($globalStarted) {
                    if($bDebug) {
                        echo '<strong>'.implode('<br />',$msg).'</strong><br />';
                    }
                }
            }  else {
                $msg[] = ''.$database->get_error();
            }
        }
        //
        $msg[] = 'MultiLingual upgrade successfull finished ';
        if($globalStarted) {
            echo "<strong>MultiLingual upgrade successfull finished $OK</strong><br />";
        }
        return ( ($globalStarted==true ) ? $globalStarted : $msg);
    }
}
// ------------------------------------
//$directory = dirname(__FILE__).'/'.'info.php';
//// update entry in table addons to new version
//load_module($directory, $install = false);

    $bDebugModus = ((isset($bDebugModus)) ? $bDebugModus : false);
    // Don't show the messages twice
    if( is_array($msg = mod_MultiLingual_upgrade($bDebugModus))) {
        echo '<strong>'.implode('<br />',$msg).'</strong><br />';
    }

// ------------------------------------

