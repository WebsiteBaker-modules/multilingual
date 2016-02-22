<?php
/**
 *
 * @category        modules
 * @package         multilingual
 * @author          WebsiteBaker Project
 * @copyright       2004-2009, Ryan Djurovich
 * @copyright       2009-2010, WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.1
 * @requirements    PHP 5.1.0 and higher
 * @version         $Id:  $
 * @filesource      $HeadURL:  $
 * @lastmodified    $Date:  $
 *
 */

include_once('../../config.php');
$mod_path = dirname(__FILE__);
$mod_rel = str_replace($_SERVER['DOCUMENT_ROOT'],'',str_replace('\\', '/', $mod_path ));
$mod_name = basename($mod_path);

// Get page id
if(isset($_GET['page_id']))
{
    $temp_page_id =  intval( htmlentities($_GET['page_id'] ) );
}

// Include WB admin wrapper script
$update_when_modified = false; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');

include('lang.functions.php');
include(get_module_language_file($mod_name));

$lang_array = get_page_languages(); // check for page languages

            $entries = array();
            $entries = get_page_list( 0 );
            // fill page_code with menu_title for default_language
//            while( list( $page_id, $val ) = each ( $entries ) )
            foreach( $entries as $key=>$value )
            {
                if ( $value['language'] == DEFAULT_LANGUAGE ) {
                    $page_id = $key;
                    db_update_field_entry((int)$page_id, 'pages', (int)$page_id );
                }
            }

// Check if there is a db error, otherwise say successful
if($database->is_error())
{
    $admin->print_error($database->get_error(), ADMIN_URL.'/pages/index.php' );
} else {
    $admin->print_success($MESSAGE['PAGES']['UPDATE_SETTINGS'], ADMIN_URL.'/pages/settings.php?page_id='.$temp_page_id );
}
