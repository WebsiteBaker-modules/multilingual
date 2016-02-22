<?php
/**
 *
 * @category        modules
 * @package         multilingual
 * @author          WebsiteBaker Project
 * @copyright       2004-2009, Ryan Djurovich
 * @copyright       2009-2010, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.1
 * @requirements    PHP 5.1.0 and higher
 * @version         $Id:  $
 * @filesource        $HeadURL:  $
 * @lastmodified    $Date:  $
 *
 */

// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

include('lang.functions.php');

// Work-out if we should check for existing page_code
$sql = 'DESCRIBE `'.TABLE_PREFIX.'pages` `page_code`';
$field_sql = $database->query($sql);
$field_set = $field_sql->numRows();
// $field_set = $database->field_add('page_code', 'pages', 'INT(11) NOT NULL AFTER `modified_by`');

// extract page_id from old format
$pattern = '/(?<=_)([0-9]{1,11})/s';

$format = $field_sql->fetchRow(MYSQL_ASSOC) ;

// upgrade only if old format
if($format['Type'] == 'varchar(255)' )
{
    $sql = 'SELECT `page_code`,`page_id` FROM `'.TABLE_PREFIX.'pages` ORDER BY `page_id`';
    $query_code = $database->query($sql);
    while( $page  = $query_code->fetchRow(MYSQL_ASSOC))
    {
        preg_match($pattern, $page['page_code'], $array);
        $page_code = $array[0];
        $page_id =  $page['page_id'];
        $sql  = 'UPDATE `'.TABLE_PREFIX.'pages` SET ';
        $sql .= (empty($array[0])) ? '`page_code` = 0 ' : '`page_code` = '.$page_code.' ';
        $sql .= 'WHERE `page_id` = '.$page_id;
        $database->query($sql);
    }
    $sql = 'ALTER TABLE `'.TABLE_PREFIX.'pages` MODIFY COLUMN `page_code` INT(11) NOT NULL DEFAULT 0 ';
    $database->query($sql);
} else {
    $sql = 'ALTER TABLE `'.TABLE_PREFIX.'pages` MODIFY COLUMN `page_code` INT(11) NOT NULL DEFAULT 0 ';
    $database->query($sql);
    $entries = array();
    $entries = get_page_list( 0 );
    // fill page_code with page_id for default_language
    foreach( $entries as $key=>$value )
    {
        if ( $value['language'] == DEFAULT_LANGUAGE ) {
            $page_id = $key;
            db_update_field_entry((int)$page_id, 'pages', (int)$page_id );
        }
    }
}
//
$directory = dirname(__FILE__).'/'.'info.php';
// update entry in table addons to new version
load_module($directory, $install = false);
// Print admin footer
// $admin->print_footer();

