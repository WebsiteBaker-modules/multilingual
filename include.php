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
 * include.php
 *
 * @category     Modules
 * @package      Modules_mod_multilingual
 * @author       Werner v.d.Decken <wkl@isteam.de>
 * @author       Dietmar Wöllbrink <dietmar.woellbrink@websiteBaker.org>
 * @copyright    Werner v.d.Decken <wkl@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      0.0.1
 * @revision     $Revision: 2070 $
 * @link         $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/mod_multilingual/include.php $
 * @lastmodified $Date: 2014-01-03 02:21:42 +0100 (Fr, 03. Jan 2014) $
 * @since        File available since 09.01.2013
 * @description  provides a flexible posibility for changeing to a translated page
 */

if (!function_exists('LangPadeId')){
    function LangPadeId($lang){
        global $wb;
        if (is_readable(dirname(__DIR__).'/SimpleRegister.php')){
          require dirname(__DIR__).'/SimpleRegister.php';
        } else {
            throw new Exception('Call of an invalid WebsiteBaker Version ['.WB_VERSION.' '.WB_SP.'] failed!');
        }
        if (isset($oReg->App) && $oReg->App instanceof admin) { $oReg->App = $wb; }
        $oReg->PageLanguages = filter_var($oReg->PageLanguages, FILTER_VALIDATE_BOOLEAN);
        if (!$oReg->PageLanguages) {return false;}
        if (!class_exists('m_mod_multilingual_Lib',false)){require __DIR__.'/Lib.php';}
        $oPageLang = new m_mod_multilingual_Lib();
        $oPageLang->initialize($oReg);
        $aLink = $oPageLang->getPageLangDetails();
        return $aLink[$lang]['page_id'];
    }
}

if (!function_exists('language_menu'))
{
    function language_menu($sExtension = "auto", $bOutput=true)
    {
        global $wb;
        if (is_readable(dirname(__DIR__).'/SimpleRegister.php')){
          require dirname(__DIR__).'/SimpleRegister.php';
        } else {
            throw new Exception('Call of an invalid WebsiteBaker Version ['.WB_VERSION.' '.WB_SP.'] failed!');
        }
        if (isset($oReg->App) && $oReg->App instanceof admin) {
            $oReg->App = $wb;
        }
        $oReg->PageLanguages = filter_var($oReg->PageLanguages, FILTER_VALIDATE_BOOLEAN);
        if (!$oReg->PageLanguages) {return false;}
        $sExtension = strtolower($sExtension);
        switch($sExtension)
        {
            case 'gif':
            case 'png':
            case 'svg':
                break;
            default:
                $sExtension = 'auto';
        }
        if (!class_exists('m_mod_multilingual_Lib',false)){require __DIR__.'/Lib.php';}
        if ( ($oReg->PageId  < 1) ){ return false; }
        $oPageLang = new m_mod_multilingual_Lib();
        $oPageLang->initialize($oReg);
        $oPageLang->setExtension($sExtension);
        $sRetVal = trim($oPageLang->getLangMenu());
        if ($bOutput){echo $sRetVal;}
        return $sRetVal;
    }
}
