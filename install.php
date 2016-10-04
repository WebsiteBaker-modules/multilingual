<?php
/**
 *
 * @category       modules
 * @package        mod_multilingual
 * @authors        WebsiteBaker Project
 * @copyright      WebsiteBaker Org. e.V.
 * @link           http://websitebaker.org/
 * @license        http://www.gnu.org/licenses/gpl.html
 * @platform       WebsiteBaker 2.8.3
 * @requirements   PHP 5.3.6 and higher
 * @version        $Id:  $
 * @filesource     $HeadURL:  $
 * @lastmodified   $Date:  $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */

$mod_path = dirname(__FILE__);
$mod_rel = str_replace($_SERVER['DOCUMENT_ROOT'],'',str_replace('\\', '/', $mod_path ));
$mod_name = basename($mod_path);
include('lang.functions.php');
include(get_module_language_file($mod_name));
$aMsgQueue = array();
// this function checks the basic configurations of an existing WB intallation
function status_msg($message, $class='check', $element='span') {
    // returns a status message
    return '<'.$element .' class="' .$class .'">' .$message .'</' .$element.'>';
}

/**********************************************************
 *  - Add field "page_code" to table "pages"
 */

$aMsgQueue[] = '<h3>Step 1: Updating database pages entrie</h3>';
$database->field_add( TABLE_PREFIX.'pages', 'page_code','INT(11) NOT NULL DEFAULT \'0\' AFTER `modified_by`');
$aMsgQueue[] = '<h3>Step 2: Updating field page_code with page_id by default language</h3>';
$lang_array = get_page_languages(); // check for page languages

if(count($lang_array))
{
            $entries = array();
            $entries = get_page_list( 0 );
           // fill page_code with $page_id for default_language
              while( list( $page_id, $val ) = each ( $entries ) )
                {
                  if( $val['language'] == DEFAULT_LANGUAGE )
                  {
                      db_update_field_entry($page_id, 'pages', (int)$page_id );
                  }
              }
}

// Print admin footer
// $admin->print_footer();
