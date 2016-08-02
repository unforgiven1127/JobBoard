<?php

require_once('component/settings/settings.class.php5');

class CSettingsEx extends CSettings
{
  private $casSettings = array();

  public function __construct()
  {
    if(isset($_SESSION['settings']) && !empty($_SESSION['settings']))
      $this->casSettings = $_SESSION['settings'];
    else
      $this->_loadSettings();

    return true;
  }

  public function __destruct()
  {
      return true;
  }

  public function getPageActions($psAction = '', $psType = '', $pnPk = 0)
  {
    $asActions = array();

    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $sPictureMenuPath = $this->getResourcePath().'/pictures/menu/';

   return $asActions;
  }

  // Normal functions

  public function getHtml()
  {
    $this->_processUrl();
    switch($this->csType)
    {
       case CONST_TYPE_SETTINGS:
        switch($this->csAction)
        {
           case CONST_ACTION_SAVE_CONFIG:
              return $this->_getSiteConfigSave();
                break;

           default:
             return $this->_getSettingsPage();
            break;
        }

        case CONST_TYPE_SETTING_BLACKLIST:

        switch($this->csAction)
        {
           case CONST_ACTION_SAVEADD:
             return $this->_getSaveBlackList();
               break;
         }

        case CONST_TYPE_SETTING_FOOTER:

        switch($this->csAction)
        {
            case CONST_ACTION_SAVEADD:
              return $this->_getSaveFooter();
                break;
         }

        case CONST_TYPE_SETTING_RIGHTUSR:

        switch($this->csAction)
        {
           case CONST_ACTION_SAVEEDIT:
              return $this->_getUserRightSave();
                break;
         }

        case CONST_TYPE_SETTING_MENU:

        switch($this->csAction)
        {
            case CONST_ACTION_SAVEADD:
              return $this->_getSaveMenu();
                break;
         }

      break;
    }
  }

  //Ajax function

  public function getAjax()
  {
    $this->_processUrl();

    switch($this->csType)
    {
       case CONST_TYPE_SETTINGS:

        switch($this->csAction)
        {
            case CONST_ACTION_ADD:
              return json_encode($this->_getAjaxSettingPage());
                break;

        }

        case CONST_TYPE_SETTING_USER:

        switch($this->csAction)
        {
            case CONST_ACTION_ADD:
              return json_encode($this->_getAjaxUserPage());
                break;
        }

        case CONST_TYPE_SETTING_USRIGHT:

        switch($this->csAction)
        {
            case CONST_ACTION_ADD:
              return json_encode($this->_getAjaxUserRightPage());
                break;

        }

        case CONST_TYPE_SETTING_RIGHTUSR:

        switch($this->csAction)
        {
            case CONST_ACTION_ADD:
              return json_encode($this->_getAjaxUserRightForm($this->cnPk));
                break;

        }

        case CONST_TYPE_SETTING_MENU:

        switch($this->csAction)
        {
            case CONST_ACTION_ADD:
              return json_encode($this->_getAjaxMenuPage());
                break;

         }

        case CONST_TYPE_SETTING_FOOTER:

        switch($this->csAction)
        {
            case CONST_ACTION_ADD:
              return json_encode($this->_getAjaxFooterPage());
                break;

         }

        case CONST_TYPE_SETTING_BLACKLIST:

        switch($this->csAction)
        {
            case CONST_ACTION_ADD:
              return json_encode($this->_getAjaxBlackListPage());
                break;
         }

        case CONST_TYPE_SETTING_CRON:

        switch($this->csAction)
        {
            case CONST_ACTION_ADD:
              return json_encode($this->_getAjaxCronPage());
                break;
          }
        break;
     }
  }

  /**
   * Function to display the setting form in ajax
   * @return array
   */

  private function _getAjaxSettingPage()
  {
    $sData = $this->_getSiteConfig();

    if(empty($sData) || $sData == 'null' || $sData == null)
       return array('data' => 'Sorry, an error occured while refreshing the list.');

    return array('data' =>$sData);
  }

  /**
   * Function to manage user add/edit/delete
   * @return array of data
   */

  private function _getAjaxUserPage()
  {
    $oLogin = CDependency::getComponentByName('login');
    $sData = $oLogin->getUserPageList();

    if(empty($sData) || $sData == 'null' || $sData == null)
      return array('data' => 'Sorry, an error occured while refreshing the list.');

    return array('data' =>$sData);
  }

  /**
   * Function to manage user rights
   * @return array of data
   */

  private function _getAjaxUserRightPage()
  {
    $sData = $this->_getUserRights();

    if(empty($sData) || $sData == 'null' || $sData == null)
      return array('data' => 'Sorry, an error occured while refreshing the list.');

    return array('data' =>$sData);

  }


  /**
   * Function to manage menu
   * @return array of data
   */

  private function _getAjaxMenuPage()
  {
    $sData = $this->_getMenuSetting();

   if(empty($sData) || $sData == 'null' || $sData == null)
     return array('data' => 'Sorry, an error occured while refreshing the list.');

    return array('data' =>$sData);

  }

  /**
   * Function to manage footer
   * @return array of data
   */

  private function _getAjaxFooterPage()
  {
    $sData = $this->_getFooterSetting();

    if(empty($sData) || $sData == 'null' || $sData == null)
       return array('data' => 'Sorry, an error occured while refreshing the list.');

    return array('data' =>$sData);
  }


  /**
   * Function to manage blacklist components
   * @return array of data
   */
  private function _getAjaxBlackListPage()
  {
    $oPage = CDependency::getComponentByName('page');
    $sData = $this->_getBlackListForm();

    if(empty($sData) || $sData == 'null' || $sData == null)
       return array('data' => 'Sorry, an error occured while refreshing the list.');

    return $oPage->getAjaxExtraContent(array('data' =>$sData));
  }


  /**
   * Function to execute cron
   * @return array of data
   */

  private function _getAjaxCronPage()
  {
     $sData = $this->_getCronSettingForm();

    if(empty($sData) || $sData == 'null' || $sData == null)
       return array('data' => 'Sorry, an error occured while refreshing the list.');

     return array('data' =>$sData);
  }

   /**
   * Function to set the setting values in to session
   * @param array/string $pvString
   * @return boolean
   */

  private function _loadSettings($pvString = '')
  {
    $oDB = CDependency::getComponentByName('database');

    if(empty($pvString))
    {
      $sQuery = 'SELECT * FROM settings ';
    }
    elseif(is_string($pvString) && !empty($pvString))
    {
      $sQuery = 'SELECT * FROM settings where `fieldname` = "'.$pvString.'"';
    }
    elseif(is_array($pvString) && !empty($pvString))
    {
      $asSettings = $pvString;
      $sQuery = 'SELECT * FROM settings WHERE `fieldname` IN ('.implode(',',$asSettings).')';
    }

      $oDbResult = $oDB->executeQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      if($bRead)
      {
        $asSettingData = array();
        $asRecords = array();
        while($bRead)
        {
          $asSettingData = $oDbResult->getData();

            if($asSettingData['fieldtype']== 'serialized')
            {
              //fix issue with non-utf8 encoded characters
              if($asSettingData['fieldname'] == 'menu')
              {
                $asRecords[$asSettingData['fieldname']] = @unserialize(base64_decode($asSettingData['value']));
              }
              else
                $asRecords[$asSettingData['fieldname']] = @unserialize($asSettingData['value']);
            }
            else
              $asRecords[$asSettingData['fieldname']] = $asSettingData['value'];

            $this->casSettings = $asRecords;
          $bRead = $oDbResult->readNext();
        }

      if(empty($this->casSettings))
      {
        $_SESSION['settings'] = array();
        unset($_SESSION);
        return false;
      }
      $_SESSION['settings'] = $this->casSettings;
      return true;
     }
     else
       return false;
  }

  /**
  * Function to set the setting values in to session
  * @param array/string $pvString
  * @return array
  */
  public function getSettings($pvString)
  {
     if(!assert('!empty($pvString)'))
       return array();

     $asRecord = array();

     if(is_array($pvString) && !empty($pvString))
     {
       foreach ($pvString as $sValue)
       {
         if(!isset($_SESSION['settings'][$sValue]))
           assert('false; // setting ['.$sValue.'] not available');

         $asRecord[$sValue] = $_SESSION['settings'][$sValue];
       }
     }
     else
     {
       if(isset($_SESSION['settings'][$pvString]) && !empty($_SESSION['settings'][$pvString]))
         $asRecord[$pvString] = $_SESSION['settings'][$pvString];
     }

     return $asRecord;
   }

  /**
  * Allow to use private/system settings.
  * Less restricted than normal settings, no assert if not found
  * It's coupled with a function to setNew System settings
  * @param array/string $pvString
  * @return array
  */
  public function getSystemSettings($pvString)
  {
    if(empty($pvString))
      return array();

    $asRecord = array();

    if(is_array($pvString))
    {
      foreach ($pvString as $sValue)
      {
        if(isset($_SESSION['settings'][$sValue]))
          $asRecord[$sValue] = $_SESSION['settings'][$sValue];
      }
    }
    else
    {
      if(isset($_SESSION['settings'][$pvString]) && !empty($_SESSION['settings'][$pvString]))
        $asRecord[$pvString] = $_SESSION['settings'][$pvString];
    }

    return $asRecord;
  }

  /**
  * Allow to set private/system settings.
  * all those settings are serialized fields to give more flexibility
  * It's coupled with a function to setSystem settings
  * @param array/string $pvString
  * @return array
  */
  public function setSystemSettings($pvShortname, $pvValue)
  {
    if(!assert('!empty($pvShortname)'))
      return false;

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT settingspk FROM settings WHERE fieldname = '.$oDB->dbEscapeString($pvShortname);
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if($bRead)
    {
      $sQuery = 'UPDATE settings SET value = '.$oDB->dbEscapeString(serialize($pvValue)).' WHERE `settingspk` = '.$oDbResult->getFieldValue('settingspk', CONST_PHP_VARTYPE_INT);
    }
    else
    {

      $sQuery = 'INSERT INTO settings (`fieldname`,`fieldtype`,`value`,`description`)';
      $sQuery.= ' VALUES ('.$oDB->dbEscapeString($pvShortname).', "serialized",';
      $sQuery.= ''.$oDB->dbEscapeString(serialize($pvValue)).', '.$oDB->dbEscapeString('system setting auto generated').')';
    }

    echo $sQuery;
    return (bool)$oDB->ExecuteQuery($sQuery);
  }


  /**
   * Function to display the blacklist form
   * @return string
   */

  private function _getBlackListForm()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sFileName = file_get_contents("./conf/blacklist.inc.php5");

    $sHTML= $oHTML->getBlocStart();
     $sHTML.= $oHTML->getBlocStart('',array('class'=>'h2'));
      $sHTML.= $oHTML->getText('Manage Black List ');
     $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getCarriageReturn(2);

    $sURL = $oPage->getUrl($this->_getUid(), CONST_ACTION_SAVEADD,CONST_TYPE_SETTING_BLACKLIST);
      $oForm = $oHTML->initForm('blackListForm');
       $sFormId = $oForm->getFormId();
       $oForm->setFormParams('', false, array('action' => $sURL, 'submitLabel'=>'Save'));
       $oForm->addField('textarea', 'blacklist', array('label'=> 'Blacklist Contents ', 'value' =>$sFileName,'style'=>'width:680px;height:300px;'));
     $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();

  return $sHTML;
 }

 /**
  * Save the black list content and store in file
  * @return redirection to the defined URL
  */

 private function _getSaveBlackList()
 {
   $oPage = CDependency::getComponentByName('page');
   $oHTML = CDependency::getComponentByName('display');

   $sFileName = './conf/blacklist.inc.php5';
    $sBlackList = getValue('blacklist');
    $sURL = $oPage->getUrl('settings', CONST_ACTION_ADD, CONST_TYPE_SETTINGS);
   file_put_contents($sFileName, $sBlackList);

   return $oHTML->getRedirection($sURL);
  }

 /**
  * Form to execute cronjob for the specific component
  * @return string
  */
  private function _getCronSettingForm()
  {
    $oHTML = CDependency::getComponentByName('display');

    $sHTML = $oHTML->getBlocStart();
     $sHTML.= $oHTML->getBlocStart('',array('class'=>'h2'));
      $sHTML.= $oHTML->getText('Execute Cron Jobs');
      $sHTML.= $oHTML->getBlocEnd();
     $sHTML.= $oHTML->getCarriageReturn(2);
    $asComponentUid = CDependency::getComponentIdByInterface('cron');

    foreach($asComponentUid as $sUid)
    {
      $sComponentName = CDependency::getComponentNameByUid($sUid);
       $sLink = CONST_CRM_DOMAIN.'/index.php5?pg=cron&hashCron=1&custom_uid='.$sUid.'';
       $sHTML.= $oHTML->getLink($sComponentName,$sLink);
      $sHTML.= $oHTML->getCarriageReturn(2);
     }

   $sHTML.= $oHTML->getBlocEnd();

  return $sHTML;
}

  /**
   * Function to display the form to manage user rights
   * @param integer $pnLoginPk
   * @return array of data
   */

  private function _getAjaxUserRightForm($pnLoginPk)
  {
    if(!assert('is_integer($pnLoginPk) && !empty($pnLoginPk)'))
       return array();

    $oRight = CDependency::getComponentByName('right');
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $asRightData = $oRight->getRightList(true,false,false);

    $asData = $oRight->getUserRightsPk($pnLoginPk);

    $sURL = $oPage->getUrl('settings',CONST_ACTION_SAVEEDIT, CONST_TYPE_SETTING_RIGHTUSR);
     $sHTML = $oHTML->getBlocStart('',array('style'=>'margin:5px;padding:5px;'));
      $oForm = $oHTML->initForm('usrRightForm');
      $sFormId = $oForm->getFormId();
     $oForm->setFormParams('', true, array('submitLabel' => 'Save','action' => $sURL));
    $oForm->setFormDisplayParams(array('noCancelButton' => '1'));

    $nCount = 0;
    foreach($asRightData  as $asUserRightData)
    {
      $asChildData = $oRight->getChildRights((int)$asUserRightData['rightpk']);
      $sChilds = $asChildData['label'];
      $sLink = $oHTML->getLink('detail','javascript:;',array('onclick'=>'$(\'#child_'.$asUserRightData['rightpk'].'\').fadeToggle();'));

      $sLabel = $oHTML->getBlocStart('', array('class' => 'rightListRow'));

        $sLabel.= $oHTML->getBlocStart('', array('class' => 'rightListLabel'));
        $sLabel.= $asUserRightData['label'];
        $sLabel.= $oHTML->getBlocEnd();

        $sLabel.= $oHTML->getBlocStart('', array('class' => 'rightListDescription'));
        $sLabel.= $asUserRightData['description'];
        $sLabel.= $oHTML->getBlocEnd();

        $sLabel.= $oHTML->getBlocStart('', array('class' => 'rightListLink'));
        $sLabel.= $sLink;
        $sLabel.= $oHTML->getBlocEnd();

        $sLabel.= $oHTML->getFloatHack();

        $sLabel.= $oHTML->getBlocStart('child_'.$asUserRightData['rightpk'], array('class' => 'childRightDiv'));
        $sLabel.= $sChilds;
        $sLabel.= $oHTML->getBlocEnd();

      $sLabel.= $oHTML->getBlocEnd();
      $sLabel.= $oHTML->getFloatHack();

      if($nCount == 0)
      {
        if(in_array($asUserRightData['rightpk'], $asData))
          $oForm->addField('checkbox', 'usrRight[]', array('type' => 'misc', 'label'=> $sLabel, 'value' => $asUserRightData['rightpk'], 'id' => 'usrRight_'.$asUserRightData['rightpk'],'checked'=>'checked'));
        else
          $oForm->addField('checkbox', 'usrRight[]', array('type' => 'misc', 'label'=> $sLabel, 'value' => $asUserRightData['rightpk'], 'id' => 'usrRight_'.$asUserRightData['rightpk']));

        $nCount = 1;
      }
      else
      {
        if(in_array($asUserRightData['rightpk'], $asData))
          $oForm->addOption( 'usrRight[]', array('type' => 'misc', 'label'=> $sLabel, 'value' => $asUserRightData['rightpk'], 'id' => 'usrRight_'.$asUserRightData['rightpk'],'checked'=>'checked'));
        else
          $oForm->addOption( 'usrRight[]', array('type' => 'misc', 'label'=> $sLabel, 'value' => $asUserRightData['rightpk'], 'id' => 'usrRight_'.$asUserRightData['rightpk']));
       }
     }

     $oForm->addField('hidden', 'userfk', array('value' => $pnLoginPk));

     $sHTML.= $oForm->getDisplay();
     $sHTML.= $oHTML->getBlocEnd();

     return array('data' =>$sHTML);
  }

 /**
  * Function to save the user rights
  * @return redirect to the next page
  */

  private function _getUserRightSave()
  {
   $oRight = CDependency::getComponentByName('right');
   $oHTML = CDependency::getComponentByName('display');
   $oPage = CDependency::getComponentByName('page');

   $sURL = $oPage->getUrl('settings', CONST_ACTION_ADD,CONST_TYPE_SETTINGS);
   $bRight = $oRight->getUserRightSave();

   if($bRight)
    return $oHTML->getRedirection($sURL);
   else
    $oHTML->getBlocMessage('Error Occured');
  }

  /**
   * Function to display the the user list to manage rights
   * @return string
   */

  private function _getUserRights()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');

    $asActiveUsers = $oLogin->getUserList(0,true,false);

    $sHTML = $oHTML->getBlocStart('');
     $sHTML.= $oHTML->getBlocStart('',array('class'=>'h2'));
     $sHTML.= $oHTML->getText('Manage User Rights');
    $sHTML.= $oHTML->getBlocEnd();

    foreach($asActiveUsers as $nUserPk => $asUserData)
    {
      $sUserRightUrl = $oPage->getAjaxUrl('settings',CONST_ACTION_ADD, CONST_TYPE_SETTING_RIGHTUSR,$nUserPk);
       $sHTML.= $oHTML->getBlocStart('user_'.$nUserPk,array('style'=>'border:1px solid #CECECE;margin:5px;padding:5px;','onclick'=>" if($('#userContainer_".$nUserPk."').html()==''){ AjaxRequest('".$sUserRightUrl."', 'body', '', 'userContainer_".$nUserPk."'); } else { $('#userContainer_".$nUserPk."').html(''); } "));
        $sHTML.= $oHTML->getText($oLogin->getUserNameFromData($asUserData));
         $sPicture = $oHTML->getPicture($this->getResourcePath().'/pictures/expand.png');
         $sHTML.= $oHTML->getText($sPicture,array('style'=>'float:right;margin-right:10px;'));
        $sHTML.= $oHTML->getBlocEnd();
       $sHTML.= $oHTML->getBlocStart('userContainer_'.$nUserPk.'');
      $sHTML.= $oHTML->getBlocEnd();
    }
    return $sHTML;
  }

  /**
   * Function to setup the site configuration manager
   * @return string
   */

  private function _getSettingsPage()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'css/settings.css');

    $sHTML = $oHTML->getTitleLine('Site Configuration Manager ', $this->getResourcePath().'/pictures/setting.jpg');
     $sHTML.= $oHTML->getCarriageReturn();
      $sHTML.= $oHTML->getBlocStart('',array('style'=>'min-height:150px;'));
       $sHTML.= $this->_getSettingCategory();
       $sHTML.= $oHTML->getBlocStart('settingContainer', array('class' => 'settingContainer'));
      $sHTML.= $this->_getSiteConfig();
     $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

   return $sHTML;
  }

  /**
   * Function to display the setting category options
   * @return type
   */

  private function _getSettingCategory()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = $oHTML->getListStart('settingListId');
    $asSettings = getSettingCategory();
    $sTabs = '';
    foreach($asSettings as $sData=>$vSetting)
    {
      $sTabs.= $oHTML->getListItemStart('setting_'.$sData, array('class'=>'settingCat','onclick'=>$vSetting['onclick']));
       $sTabs.= $oHTML->getText(''.$vSetting['Label'].'');
      $sTabs.= $oHTML->getListItemEnd();
     }
    $sHTML.= $oHTML->getListItemEnd();
    $sHTML.= $sTabs;

     $sJavascriptCode = "
       $(document).ready(function()
       {
        $('#setting_1').addClass('stgcatSelected');
         $('.settingCat').click(function()
         {
           if($(this).hasClass('stgcatSelected'))
             return true;
            $('.settingCat:not(this)').removeClass('stgcatSelected');
            $(this).addClass('stgcatSelected');
          });
        }); ";
      $oPage->addCustomJs($sJavascriptCode);

      $sHTML.= $oHTML->getListEnd();
      return $sHTML;
  }

  /**
   * Function to display the form for menu
   * @return string
   */

  private function _getMenuSetting()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');

    $sURL = $oPage->getUrl('settings', CONST_ACTION_SAVEADD, CONST_TYPE_SETTING_MENU);

    $sQuery = 'SELECT * FROM settings WHERE fieldname = "menu"';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $sHTML = $oHTML->getBlocStart();
    $oForm = $oHTML->initForm('menuSettingForm');
    $oForm->setFormParams('', false, array('action' => $sURL, 'submitLabel'=>'Save'));
    $oForm->setFormDisplayParams(array('noCancelButton' => '1'));

      if(isset($this->casSettings['languages']) && count($this->casSettings['languages']))
        $asLanguages = $this->casSettings['languages'];
      else
        $asLanguages = array(CONST_DEFAULT_LANGUAGE);

      foreach($asLanguages as $sLanguage)
      {
        $oForm->addField('textarea', 'menu_'.$sLanguage, array('label'=> 'Menu ['.$sLanguage.']', 'value' =>$oDbResult->getFieldValue('value'),'style'=>'width:680px;'));
        $oForm->setFieldControl('menu_'.$sLanguage, array('jsFieldNotEmpty' => ''));

        $oForm->addField('textarea', 'menu_userialized_'.$sLanguage, array('label'=> 'Menu unserialized ['.$sLanguage.'] ', 'value' => var_export(unserialize($oDbResult->getFieldValue('value')), true), 'readonly' => 'readonly', 'style'=>'width:680px;'));
        $oForm->addField('misc', '', array('type'=> 'br'));
      }

      $oForm->addField('misc', '', array('type'=> 'text', 'text' => 'use the manu_generator here: <a href="/component/settings/resources/menu_generator.php" target="_blank">menu_generator.php</a>'));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Save Menu
   * @return redirect to the next page
   */

  private function _getSaveMenu()
  {
    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');
    $oHTML = CDependency::getComponentByName('display');

    $sMenu = getValue('menu');
    $sURL = $oPage->getUrl('settings', CONST_ACTION_ADD, CONST_TYPE_SETTINGS);

    $sQuery = ' DELETE FROM SETTINGS where fieldname = "menu"';
    $oDbResult = $oDB->ExecuteQuery($sQuery);

    $sQuery = 'INSERT INTO settings (`fieldname`,`fieldtype`,`value`,`description`)';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString('menu').','.$oDB->dbEscapeString('serialized').',';
    $sQuery.= ''.$oDB->dbEscapeString($sMenu).','.$oDB->dbEscapeString('Menu Parameters').')';

    $oDbResult = $oDB->ExecuteQuery($sQuery);

    if($oDbResult)
      return $oHTML->getRedirection($sURL);
    else
      return $oHTML->getBlocMessage('Error Obtained');

   }

  /**
   * Function to display the form for footer
   * @return string
   */

  private function _getFooterSetting()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');

    $sURL = $oPage->getUrl('settings', CONST_ACTION_SAVEADD, CONST_TYPE_SETTING_FOOTER);

    $sQuery = 'SELECT * FROM settings WHERE fieldname = "footer"';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $sHTML = $oHTML->getBlocStart();
     $oForm = $oHTML->initForm('footerSettingForm');
      $sFormId = $oForm->getFormId();
       $oForm->setFormParams('', false, array('action' => $sURL, 'submitLabel'=>'Save'));
        $oForm->setFormDisplayParams(array('noCancelButton' => '1'));
       $oForm->addField('textarea', 'footer', array('label'=> 'Footer ', 'value' =>$oDbResult->getFieldValue('value'),'style'=>'width:680px;'));
      $oForm->setFieldControl('footer', array('jsFieldNotEmpty' => ''));
     $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();

   return $sHTML;
  }

  /**
   * Save Footer links
   * @return redirection URL
   */

  private function _getSaveFooter()
  {
    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');

    $sFooter = getValue('footer');
    $sURL = $oPage->getUrl('settings', CONST_ACTION_ADD, CONST_TYPE_SETTINGS);

    $sQuery = ' DELETE FROM SETTINGS where fieldname = "footer"';
    $oDbResult = $oDB->ExecuteQuery($sQuery);

    $sQuery = 'INSERT INTO settings (`fieldname`,`fieldtype`,`value`,`description`)';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString('footer').','.$oDB->dbEscapeString('serialized').',';
    $sQuery.= ''.$oDB->dbEscapeString($sFooter).','.$oDB->dbEscapeString('Footer Parameters').')';
    $oDbResult = $oDB->ExecuteQuery($sQuery);

    if($oDbResult)
      return $oHTML->getRedirection($sURL);
    else
      return $oHTML->getBlocMessage('Error Obtained');
   }

  /**
   * Form to save the site configuration parameters
   * @return string
   */

  private function _getSiteConfig()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    if(!isset($this->casSettings['css']))
      $this->casSettings['css'] = '';

    if(!isset($this->casSettings['meta_tags']))
      $this->casSettings['meta_tags'] = '';

    if(!isset($this->casSettings['title']))
      $this->casSettings['title'] = '';

    if(!isset($this->casSettings['logo']))
      $this->casSettings['logo'] = '';

    if(!isset($this->casSettings['sitename']))
      $this->casSettings['sitename'] = '';

    if(!isset($this->casSettings['site_email']))
      $this->casSettings['site_email'] = '';

    if(!isset($this->casSettings['languages']))
      $this->casSettings['languages'] = array();


    $sHTML= $oHTML->getBlocStart();
     $sURL = $oPage->getUrl($this->_getUid(), CONST_ACTION_SAVE_CONFIG,CONST_TYPE_SETTINGS);
      $oForm = $oHTML->initForm('siteConfigForm');
       $sFormId = $oForm->getFormId();
       $oForm->setFormParams('',false, array('action' => $sURL, 'submitLabel'=>'Save'));
        $oForm->addField('input', 'css', array('label'=>'Css Path ', 'value' =>$this->casSettings['css']));
         $oForm->addField('input', 'meta_tags', array('label'=>'Meta Keywords', 'value' => $this->casSettings['meta_tags']));
         $oForm->addField('input', 'meta_desc', array('label'=>'Meta Description', 'value' => $this->casSettings['meta_desc']));
         $oForm->addField('input', 'title', array('label'=>'Title', 'value' => $this->casSettings['title']));
        $oForm->setFieldControl('title', array('jsFieldNotEmpty' => ''));
       $oForm->addField('input', 'logo', array('label'=>'Logo ', 'value' => $this->casSettings['logo']));
      $oForm->addField('input', 'sitename', array('label'=>'Site Name', 'value' => $this->casSettings['sitename']));
     $oForm->addField('input', 'site_email', array('label'=>'Site Email', 'value' => $this->casSettings['site_email']));

     $oForm->addField('misc','',array('type'=>'text','text'=>'<br />Default landing page parameters after login'));

     $asUrlParam = $this->casSettings['urlparam'];

     $oForm->addField('input', 'cp_uid', array('label'=>'Uid', 'value' => $asUrlParam['cp_uid']));
     $oForm->addField('input', 'cp_action', array('label'=>'Action', 'value' => $asUrlParam['cp_action']));
     $oForm->addField('input', 'cp_type', array('label'=>'Type', 'value' => $asUrlParam['cp_type']));
     $oForm->addField('input', 'cp_pk', array('label'=>'Pk', 'value' => $asUrlParam['cp_pk']));


     $oForm->addField('misc', '', array('type'=>'text','text'=>'<br />Accessibility'));

     $oForm->addField('input', 'languages', array('label'=>'Available languages<br /> ("," separated)', 'value' => implode(',', $this->casSettings['languages'])));


    $oForm->addField('misc', '', array('type'=>'br'));
    $sHTML.= $oForm->getDisplay();
   $sHTML.= $oHTML->getBlocEnd();

  return $sHTML;
 }

  /**
   * Function to save the site configuration
   * @return redirection URL
   */

  private function _getSiteConfigSave()
  {
   $oPage = CDependency::getComponentByName('page');
   $oDB = CDependency::getComponentByName('database');
   $oHTML = CDependency::getComponentByName('display');

   $sCssPath = getValue('css');
   $sMetaTags = getValue('meta_tags');
   $sMetaDesc = getValue('meta_desc');
   $sTitle = getValue('title');
   $sLogo = getValue('logo');
   $sSiteName = getValue('sitename');
   $sSiteEmail = getValue('site_email');

   $sCpUid = getValue('cp_uid');
   $sCpAction = getValue('cp_action');
   $sCpType = getValue('cp_type');
   $sCpPk = getValue('cp_pk');

   $sLanguages = getValue('languages');
   $sLanguages = str_replace(' ', '', $sLanguages);
   $sLanguages = str_replace(';', ',', $sLanguages);
   $asLanguages = explode(',', $sLanguages);
   $sLanguages = serialize(array_trim($asLanguages, true));

   $asUrlParam = array('cp_uid'=>$sCpUid,'cp_action'=>$sCpAction,'cp_type'=>$sCpType,'cp_pk'=>$sCpPk);
   $sURLParams = serialize($asUrlParam);

   if(isset($this->casSettings['css']) && !empty($this->casSettings['css']))
    $sQuery = 'UPDATE settings SET value = '.$oDB->dbEscapeString($sCssPath).' WHERE `fieldname` = "css"';
   else
   {
    $sQuery = 'INSERT INTO settings (`fieldname`,`fieldtype`,`value`,`description`)';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString('css').','.$oDB->dbEscapeString('text').',';
    $sQuery.= ''.$oDB->dbEscapeString($sCssPath).','.$oDB->dbEscapeString('Name of the css of the website').')';
   }
   $oDB->ExecuteQuery($sQuery);

   if(isset($this->casSettings['meta_tags']) && !empty($this->casSettings['meta_tags']))
    $sQuery = 'UPDATE settings SET value = '.$oDB->dbEscapeString($sMetaTags).' WHERE `fieldname` = "meta_tags"';
   else
   {
     $sQuery = 'INSERT INTO settings (`fieldname`,`fieldtype`,`value`,`description`)';
     $sQuery.= ' VALUES ('.$oDB->dbEscapeString('meta_tags').','.$oDB->dbEscapeString('text').',';
     $sQuery.= ''.$oDB->dbEscapeString($sMetaTags).','.$oDB->dbEscapeString('Meta keywords for website').')';
    }
   $oDB->ExecuteQuery($sQuery);

   if(isset($this->casSettings['meta_desc']) && !empty($this->casSettings['meta_desc']))
     $sQuery = 'UPDATE settings SET value = '.$oDB->dbEscapeString($sMetaDesc).' WHERE `fieldname` = "meta_desc"';
   else
   {
    $sQuery = 'INSERT INTO settings (`fieldname`,`fieldtype`,`value`,`description`)';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString('meta_desc').','.$oDB->dbEscapeString('text').',';
    $sQuery.= ''.$oDB->dbEscapeString($sMetaDesc).','.$oDB->dbEscapeString('Meta description for website').')';
   }

   $oDB->ExecuteQuery($sQuery);

   if(isset($this->casSettings['title']) && !empty($this->casSettings['title']))
     $sQuery = 'UPDATE settings SET value = '.$oDB->dbEscapeString($sTitle).' WHERE `fieldname` = "title"';
   else
   {
     $sQuery = 'INSERT INTO settings (`fieldname`,`fieldtype`,`value`,`description`)';
     $sQuery.= ' VALUES ('.$oDB->dbEscapeString('title').','.$oDB->dbEscapeString('text').',';
     $sQuery.= ''.$oDB->dbEscapeString($sTitle).','.$oDB->dbEscapeString('Title of the website').')';
   }
   $oDB->ExecuteQuery($sQuery);

   if(isset($this->casSettings['logo']) && !empty($this->casSettings['logo']))
     $sQuery = 'UPDATE settings SET value = '.$oDB->dbEscapeString($sLogo).' WHERE `fieldname` = "logo"';
   else
   {
    $sQuery = 'INSERT INTO settings (`fieldname`,`fieldtype`,`value`,`description`)';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString('logo').','.$oDB->dbEscapeString('image').',';
    $sQuery.= ''.$oDB->dbEscapeString($sLogo).','.$oDB->dbEscapeString('Website logo').')';
   }
   $oDB->ExecuteQuery($sQuery);

   if(isset($this->casSettings['sitename']) && !empty($this->casSettings['sitename']))
    $sQuery = 'UPDATE settings SET value = '.$oDB->dbEscapeString($sSiteName).' WHERE `fieldname` = "sitename"';
   else
   {
    $sQuery = 'INSERT INTO settings (`fieldname`,`fieldtype`,`value`,`description`)';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString('sitename').','.$oDB->dbEscapeString('text').',';
    $sQuery.= ''.$oDB->dbEscapeString($sSiteName).','.$oDB->dbEscapeString('Site Name ').')';
   }
   $oDB->ExecuteQuery($sQuery);

   if(isset($this->casSettings['site_email']) && !empty($this->casSettings['site_email']))
    $sQuery = 'UPDATE settings SET value = '.$oDB->dbEscapeString($sSiteEmail).' WHERE `fieldname` = "site_email"';
   else
   {
    $sQuery = 'INSERT INTO settings (`fieldname`,`fieldtype`,`value`,`description`)';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString('site_email').','.$oDB->dbEscapeString('text').',';
    $sQuery.= ''.$oDB->dbEscapeString($sSiteEmail).','.$oDB->dbEscapeString('Site Email Address').')';
   }

   $oDB->ExecuteQuery($sQuery);

   if(isset($this->casSettings['urlparam']) && !empty($this->casSettings['urlparam']))
    $sQuery = 'UPDATE settings SET value = '.$oDB->dbEscapeString($sURLParams).' WHERE `fieldname` = "urlparam"';
   else
   {
    $sQuery = 'INSERT INTO settings (`fieldname`,`fieldtype`,`value`,`description`)';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString('urlparam').','.$oDB->dbEscapeString('text').',';
    $sQuery.= ''.$oDB->dbEscapeString($sURLParams).','.$oDB->dbEscapeString('Default page url after login ').')';
   }
   $oDB->ExecuteQuery($sQuery);

   if(isset($this->casSettings['languages']) && !empty($this->casSettings['languages']))
    $sQuery = 'UPDATE settings SET value = '.$oDB->dbEscapeString($sLanguages).' WHERE `fieldname` = "languages"';
   else
   {
    $sQuery = 'INSERT INTO settings (`fieldname`,`fieldtype`,`value`,`description`)';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString('languages').','.$oDB->dbEscapeString('serialized').',';
    $sQuery.= ''.$oDB->dbEscapeString($sLanguages).','.$oDB->dbEscapeString('List of available languages').')';
   }
   $oDB->ExecuteQuery($sQuery);

   //reload all settings inm session
   $this->_loadSettings();

   $sURL = $oPage->getUrl('settings', CONST_ACTION_ADD, CONST_TYPE_SETTINGS);
   return $oHTML->getRedirection($sURL);
  }












  /******************************************************************************************* */
  /******************************************************************************************* */
  /******************************************************************************************* */
  /******************************************************************************************* */
  // Something that will probably become a component later when we'll have an interface to manage those
  // manageable lists


  /**
   *Return an array with the element from the lists.
   * Ele;emnts could be anything since we can save serialized values
   *
   * @param integer $pnListPk ID of the list
   * @param string $psShortname name of the list (working with name may be easier in case of DB migration)
   * @return array of misc elements
   */
  public function getManageableList($pnListPk = 0, $psShortname = '')
  {
    if(!assert('is_integer($pnListPk) && is_string($psShortname)'))
      return array();

    if(empty($pnListPk) && empty($psShortname))
    {
      assert('false; // need a list pk or shortname');
      return array();
    }

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM manageable_list as ml ';
    $sQuery.= 'INNER JOIN manageable_list_item as mli ON (mli.manageable_listfk = ml.manageable_listpk) ';

    if(!empty($pnListPk))
    {
      $sQuery.= ' WHERE ml.manageable_listpk = '.$pnListPk;
    }
    else
    {
      $sQuery.= ' WHERE ml.shortname = '.$oDB->dbEscapeString($psShortname);
    }

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array();

    $sItemType = $oDbResult->getFieldValue('item_type');

    $asList = array();
    while($bRead)
    {
      switch($sItemType)
      {
        case 'integer': $vValue = (int)$oDbResult->getFieldValue('value'); break;
        case 'float': $vValue = (float)$oDbResult->getFieldValue('value'); break;
        case 'serialized': $vValue = unserialize($oDbResult->getFieldValue('value')); break;
        default:
          $vValue = $oDbResult->getFieldValue('value');
      }

      $asList[$oDbResult->getFieldValue('label')] = $vValue;
      $bRead = $oDbResult->readNext();
    }

    return $asList;
  }

}
