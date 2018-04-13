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
 * Lib.php
 *
 * @category     Modules
 * @package      Modules_mod_multilingual
 * @author       Werner v.d.Decken <wkl@isteam.de>
 * @author       Dietmar Wöllbrink <dietmar.woellbrink@websiteBaker.org>
 * @copyright    Werner v.d.Decken <wkl@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      1.6.8
 * @revision     $Revision: 2070 $
 * @link         $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/mod_multilingual/Lib.php $
 * @lastmodified $Date: 2014-01-03 02:21:42 +0100 (Fr, 03. Jan 2014) $
 * @since        File available since 09.01.2013
 * @description  provides a flexible posibility for changeing to a translated page
 */

class m_mod_multilingual_Lib {
/** holds the active singleton instance */
    private static $oInstance      = null;
/** @var object instance of the WbAdaptor object */
    protected $_oReg = null;
/** @var object instance of the application object */
    private $_oApp     = null;
/** @var object instance of the database object */
    private $_oDb      = null;

    private $_defaultPageId = 0;

/** @var array holds several values from the default.ini */
    private $_config      = array();
/** @var array set several values for Twig_Environment */
    private $_aTwigEnv    = array();
/** @var array set several values for Twig_Loader */
    private $_aTwigLoader = array();
/** @var string set icon extension */
    private $_sExtension  = array();
/**
 * constructor used to import some application constants and objects
 */
    public function __construct(){;}

    public function initialize($oReg){
        // import global vars and objects
        if(!defined('ADMIN_REL')) { define('ADMIN_REL', WB_REL.'/'.ADMIN_DIRECTORY); }
        $this->_oReg        = $oReg;
        $this->_oApp        = $oReg->App;
        $this->_oDb         = $oReg->Db;
        $this->_config      = $this->_aConfig = $this->getConfig((dirname(__FILE__)).'/default.ini');
        $this->_aTwigLoader = $this->_config['twig-loader-file'];
        $this->_aTwigEnv    = $this->_config['twig-environment'];
        $this->_config['twig-environment']['cache'] = filter_var(trim((string)$this->_config['twig-environment']['cache']), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * methode to update a var/value-pair into table
     * @param integer $iPageId which page shall be updated
     * @param string $sTable the pages table
     * @param integer $iEntry
     * @return bool
     */
    private function updatePageCode($iPageId, $sTable, $iNewPageCode = null)
    {
        // if new Pagecode is missing then set the own page ID
        $entry = ( !isset($iNewPageCode) ? $iPageId : $iNewPageCode);
        $sql = 'UPDATE `'.TABLE_PREFIX.$sTable.'` '
             . 'SET `page_code`='.$entry.', '
             .     '`modified_when` = '.time().' '
             . 'WHERE `page_id` = '.$iPageId;
        return (bool)$this->_oDb->query($sql);
    }

   public function getLanguagesInUsed()
    {
        $aRetval = [];
        $sql = 'SELECT DISTINCT `language`, `page_id` '
             . 'FROM `'.$this->_oDb->sTablePrefix.'pages` '
             . 'WHERE `level`=0 AND `visibility` NOT IN(\'none\', \'hidden\') '
             . 'ORDER BY `language`, `position`';
        if (($oResult = $this->_oDb->query($sql))) {
            while ( $aRow = $oResult->fetchRow( MYSQLI_ASSOC)) {
                if( !$this->_oApp->isPageVisible($aRow['page_id'])) { continue; }
                $aRetval[] = $aRow['language'];
            }
        }
        return implode(',', array_unique($aRetval));
    }

    /**
     * compose the needed SQL statement
     * @param integer $sLangKey
     * @return database object with given SQL statement
     */
    private function getAvailableLanguages ($sLangKey='')
    {
        $sql = 'SELECT DISTINCT `language`'
             . ',`page_id`,`level`,`parent`,`root_parent`,`page_code`,`link`'
             . ',`visibility`,`viewing_groups`,`viewing_users`,`position`'
             . ',`page_title`,`tooltip` '
             . 'FROM `'.TABLE_PREFIX.'pages` '
             . 'WHERE `level`= \'0\' '
             .   'AND `parent`= 0 '
             .   'AND `visibility` NOT IN(\'none\',\'hidden\') '
             .   ' '
             .   (($sLangKey!='') ? ' AND `language` = \''.$sLangKey.'\' ' : ' ')
             .   'ORDER BY `language`, `position` '
             .   '';
        return ($this->_oDb->query($sql));
    }

    /**
    *
    * search for pages with given page code and create a DB result object
    * @param integer Pagecode to search for
    * @return object result object or null on error
    */
    private function getPageCodeDbResult( $iPageCode )
    {
        $sql = 'SELECT `language`,'
             .        '`page_id`,`level`,`parent`,`root_parent`,`page_code`,`link`,'
             .        '`visibility`,`viewing_groups`,`viewing_users`,`position`,'
             .        '`page_title`,`tooltip` '
             .  'FROM `'.TABLE_PREFIX.'pages`'
             .  'WHERE `page_code` = '.$iPageCode.' '
             .  'ORDER BY `position`';
        $oRes = $this->_oDb->query($sql);
        return $oRes;
    }

    /**
     * compose the needed SQL statement
     * @param integer $sLangKey
     * @return database object with given SQL statementt
     */
    private function getAvailableLanguagesObjectInstance ( $sLangKey='' )
    {
        $sql = 'SELECT `directory`,`name`  FROM `'.TABLE_PREFIX.'addons` '
             . 'WHERE `type` = \'language\' '
             . ( ($sLangKey!='') ? ' AND `directory` = \''.$langKey.'\' ' : '')
             . 'ORDER BY `directory`';
        $oRes = $this->_oDb->query($sql);
        return $oRes;
    }

    /**
     *
     * @param integer $parent
     * @return database object with given SQL statement
     */
    private function getPageListDbResult ( $parent )
    {
        $sql = 'SELECT `language`,`tooltip`,'
             .        '`page_id`,`page_title`,`menu_title`, `page_code`, `parent` '
             . 'FROM `'.TABLE_PREFIX.'pages` '
             . 'WHERE `parent` = '.$parent. ' '
             . 'ORDER BY `position`';
        $oRes = $this->_oDb->query($sql);
        return $oRes;
    }

    private function getPageCodeValues(  $iPageCode=0 )
    {
        $aRetval = [];
        if( ($oRes = $this->getPageCodeDbResult($iPageCode)) )
        {
            while($page = $oRes->fetchRow(MYSQL_ASSOC))
            {
                if(!$this->_oApp->page_is_visible($page)) {continue;}
                $aRetval[$page['language']] = $page;
            }
        }
        return $aRetval;
    }

    private function getPageList($parent, $this_page=0 )
    {
        static $entries = [];
        if( ($oLang = $this->getPageListDbResult($parent)) )
        {
            while($value = $oLang->fetchRow(MYSQLI_ASSOC))
            {
                if (( $value['page_id'] != $this_page ) )
                {
                    $entries [$value['page_id']]['language'] = $value['language'];
                    $entries [$value['page_id']]['menu_title'] = $value['menu_title'];
                    $this->getPageList($value['page_id'], $this_page );
                }
            }
        }
        return $entries;
    }

    protected function getConfig($sFilename)
    {
        if(is_readable($sFilename)){
            return parse_ini_file($sFilename, true);
        }else {
            return null;
        }
    }

    private function getAllowedLanguagesFromAddons($sLangKey='')
    {
        $aLangAddons = [];
        if (($oLang = $this->getAvailableLanguagesObjectInstance($sLangKey))){
            while($aLang = $oLang->fetchRow(MYSQLI_ASSOC)){
                $aLangAddons[$aLang['directory']] = $aLang['name'];
            }
        }
        return $aLangAddons;
    }

    /**
     *
     *
     * @param
     * @return array of first visible language pages with defined fields
     */
    private function getLanguagesDetailsInUsed ( $sLangKey='' )
    {
        $aRetval = [];
        if ((!$oRes = $this->getAvailableLanguages($sLangKey))){
            throw new Exception($this->_oDb->get_error());
        }
        while($page = $oRes->fetchRow(MYSQLI_ASSOC))
        {
            $aRetval[$page['language']] = $page;
        }
        return $aRetval;
    }

      public function getAvaiblePagesLanguages (){
          // removed do nothing
      }

      public function getPageLangDetails(){
          $aLangData = [];
          $aPossiblePages = [];
// get root pages for all used languages
          $aAllowedRootLanguages = $this->getLanguagesDetailsInUsed();
          if(sizeof($aAllowedRootLanguages)>1) {
// get all pages witch the same page_code
              $aPossiblePages = $this->getPageCodeValues( $this->_oApp->page['page_code']);
// remove all pages from list with not avaliable languages
// add Allowed root pages to possible matches
              $aLangData = array_merge($aAllowedRootLanguages, $aPossiblePages);
        }
        return $aLangData;
      }

    /**
    * m_mod_multilingual_Lib::getLangMenuData()
    *
    * @param mixed $config
    * @param mixed $oApp
    * @return
    */
    private function getLangMenuData ( )
    {
        $aTplData = [];
        $aAvailablePages = $this->getPageLangDetails();
        if (sizeof($aAvailablePages)>1) {
            foreach ( $aAvailablePages as $aPage)
            {
                $aTplData[] = array(
                    'sIconUrl'         => $this->_oReg->AppUrl . 'modules/'
                                        . basename(dirname(__FILE__)) . '/',
                    'bCurrentLanguage' => (($aPage['language'] == $this->_oReg->Language) ? true : false),
                    'sTargetPageUrl'   => $this->_oReg->AppUrl . $this->_oReg->PagesDir
                                        . trim($aAvailablePages[$aPage['language']]['link'],'/')
                                        . $this->_oReg->PageExtension,
                    'sPageTitle'       => $aAvailablePages[$aPage['language']]['page_title'],
                    'sFilename'        => strtolower($aAvailablePages[$aPage['language']]['language']),
                    'sImageType'       => $this->_sExtension,
                    'sToolTip'         => $aAvailablePages[$aPage['language']]['tooltip']
                );
            }
        }
        return $aTplData;
    }

    /**
    * m_mod_multilingual_Lib::getLangMenu()
    *
    * @param mixed $config
    * @param mixed $oApp
    * @return
    */
    private function getLangMenuTwig ( )
    {
        $loader = new Twig_Loader_Filesystem( dirname(__FILE__).$this->_aTwigLoader['templates_dir'] );
        $twig   = new Twig_Environment( $loader );
        $data['aTargetList']   = $this->getLangMenuData( );
        $sRetval = (sizeof($data)?$twig->render($this->_aTwigLoader['default_template'], $data):'');
        return $sRetval;
    }

    private function _detectIE()
    {
        preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $aMatches);
        if (count($aMatches)>1){
          return true;
        }
        return false;
    }

    public function setExtension($sExtension = 'auto')
    {
        if($sExtension == 'auto' || $sExtension == 'svg') {
            $this->_sExtension = ($this->_detectIE() == true) ? 'png' : 'svg';
        } else {
            $this->_sExtension = 'png';
        }
        return;
    }

    public function getLangMenu()
    {
        $sRetval = trim($this->getLangMenuTwig ());
        return $sRetval;
    }

    public function updateDefaultPagesCode (  )
    {
        $retVal  = false;
        $aLangs  = $this->getLanguagesDetailsInUsed(  );
        $entries = $this->getPageList( 0 );
// fill page_code with page_id for default_language
        while( list( $page_id, $val ) = each ( $entries ) )
        {
            if( $val['language'] == $this->_oReg->DefaultLanguage ) {
                if( ($retVal = $this->updatePageCode((int)$page_id, 'pages', (int)$page_id ))==false ){ break;  }
            }
        }
        return $retVal;
    }

}
