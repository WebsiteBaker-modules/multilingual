<?php
/**
 * Code snippet: Language Switcher
 *
 * This code snippets displays language flags which can be used to
 * switch the current displayed page to another language.
 *
 * Pls read following instruction to get language switcher working:
 *
 * it is required to activate the language page settings
 * by adding a page, set the page setting to the correct language
 *
 *
 *
 *
 * This file contains the language switcher function.
 *
 * LICENSE: GNU General Public License 3.0
 *
 * @author       Dietmar Wöllbrink
 * @copyright    Dietmar Wöllbrink (c) 2009 - 2011
 * @license      http://www.gnu.org/licenses/gpl.html
 * @version      0.1.0
 * @platform     Website Baker 2.8.x
 *
 * <?php if(function_exists('language_menu')) { language_menu(); } ?>
 *
 */


/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */

    $languageMenu = function ($sExt, $printOutput) use($database)
    {
        $retVal = '';
        if ($iArgs=func_num_args()){
            $aArgs = func_get_args();
            foreach ($aArgs as $key => $value){
                if (is_string($value)){$sExt=$value; $aArgs['ext']=$sExt;}
                if (is_bool($value)){$printOutput=$value; $aArgs['print']=$printOutput;}
            }
        }
        if (!isset($sExt)){$sExt='gif'; $aArgs['ext']=$sExt;}
        if (!isset($printOutput)){$printOutput=true; $aArgs['print']=$printOutput;}
        $sAddonPath = str_replace('\\', '/', __DIR__);
        include('lang.functions.php');
        $sAddonName = basename($sAddonPath);
        // Work-out we should check for existing page_code
        $field_set = $database->field_exists(TABLE_PREFIX.'pages', 'page_code');
        if (defined('PAGE_LANGUAGES') && (PAGE_LANGUAGES==true) && ($field_set==true))
        {
//            get_module_language_file($sAddonName);
            $oTrans = Translate::getInstance();
            $langIcons  = array();
            $langIcons = set_language_icon(PAGE_ID,$sExt );
            if( sizeof($langIcons) > 1 )
            {
                $retVal = '<div id="langmenu">'.PHP_EOL;
                foreach( $langIcons as $key=>$value )
                {
                    $retVal .= $value;
                }
                $retVal .= '</div>';
            }
        }
        if ($printOutput){ echo $retVal."<!-- echo -->\n";}else{$retVal .= "<!-- buffer -->\n";}
        return $retVal;
    };
//

/**
 */
     function language_menu($sExt='gif', $printOutput=true) {
       global $database, $languageMenu;
       return $languageMenu($sExt, $printOutput);
     }
