<?php

function languageSlistemtoJobBoard($nineSystem)
{
  if($nineSystem == 9 || $nineSystem == 8)
  {
    return 5;
  }
  else if($nineSystem == 7)
  {
    return 4;
  }
  else if($nineSystem == 6)
  {
    return 3;
  }
  else if($nineSystem == 5)
  {
    return 2;
  }
  else if($nineSystem == 4)
  {
    return 1;
  }
  else
    return 0;
}

function languageJobBoardToSlistem($fiveSystem)
{
    if($fiveSystem == 1)
    {
      return 4;
    }
    else if($fiveSystem == 2)
    {
      return 5;
    }
    else if($fiveSystem == 3)
    {
      return 6;
    }
    else if($fiveSystem == 4)
    {
      return 7;
    }
    else if($fiveSystem == 5)
    {
      return 9;
    }
    else
      return 0;
}

function displayAssert($psFile, $pnLine, $psMessage)
{
  echo '<div style="width:100%;">
        <div>
          <strong>Assert spotted in '.$psFile.' line '.$pnLine.": <br /> \n".$psMessage." <br /> \n</strong>
        </div>";

  $asDebug = debug_backtrace();

  foreach($asDebug as $asLine)
  {
    echo ' -> ';

    if(isset($asLine['file']))
      echo 'called in '.$asLine['file'].' / ';

    echo $asLine['function'].' ';

    if(isset($asLine['file']))
      echo 'line '.$asLine['line'].' ';

    echo '<br />';
  }

  echo '</div>';
}

function logAssert($psFile, $pnLine, $psMessage)
{
  $sText = date('Y-m-d H:i:s')."\n".'Assert spotted in '.$psFile.' line '.$pnLine.": \n".$psMessage." \n ";

  $asDebug = debug_backtrace();

  foreach($asDebug as $asLine)
  {
    $sText.= ' -> ';

    if(isset($asLine['file']))
      $sText.= 'called in '.$asLine['file'].' / ';

    $sText.= $asLine['function'].' ';

    if(isset($asLine['file']))
      $sText.= 'line '.$asLine['line'].' ';

    //echo 'with['.implode(' | ', $asLine['args']).'] <br />';
    $sText.= "\n";
  }

  $sText.= "\n";

  $oFs = fopen(CONST_DEBUG_ASSERT_LOG_PATH, 'a+');
  if($oFs)
  {
    fwrite($oFs, $sText);
    fclose($oFs);

    //rotate file if size > 2Mo
    if(filesize(CONST_DEBUG_ASSERT_LOG_PATH) > (2*1024*1024))
    {
      copy(CONST_DEBUG_ASSERT_LOG_PATH, CONST_DEBUG_ASSERT_LOG_PATH.'-'.time());
    }
  }
}

function mailAssert($psFile, $pnLine, $psMessage)
{
  $sAssert = '<div style="width:100%;">
        <div>
          <strong>Assert spotted in '.$psFile.' line '.$pnLine.": <br /> \n".$psMessage." <br /> \n</strong>
        </div>";

  $asDebug = debug_backtrace();

  foreach($asDebug as $asLine)
  {
    $sAssert.= ' -> ';

    if(isset($asLine['file']))
      $sAssert.= 'called in '.$asLine['file'].' / ';

    $sAssert.= $asLine['function'].' ';

    if(isset($asLine['file']))
      $sAssert.= 'line '.$asLine['line'].' ';

    $sAssert.= '<br />';
  }

  $sAssert.= '</div>';

  $oMail = CDependency::getComponentByName('mail');
  $bSent = false;
  if($oMail)
    $bSent = $oMail->sendRawEmail('BCM assertion', CONST_DEV_EMAIL, 'Assert spotted in '.CONST_WEBSITE, $sAssert, CONST_DEV_EMAIL_2);

  if(!$bSent)
    displayAssert($psFile, $pnLine, $psMessage);
}


function dump($var, $label=null, $echo=true)
{
  // format the label
  $label = ($label===null) ? '' : rtrim($label) . ' ';

  // //var_dump the variable into a buffer and keep the output
  ob_start();
  ////var_dump($var);
  $output = ob_get_clean();

  // neaten the newlines and indents
  $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);

  $output = '<pre>'
  . $label
  . htmlspecialchars($output, ENT_QUOTES)
  . '</pre>';

  if ($echo)
  {
    echo('<div style="padding-left: 100px;">'.$output.'</div>');
  }
  return '<div style="padding-left: 100px;">'.$output.'</div>';
}

function debugLine($pvVariable, $pnLine = 0, $psFile = '', $pbHtml = true)
{
  if($pbHtml)
  {
    echo '<hr />line '.$pnLine.' '.$psFile.':<br /><pre>';
    dump($pvVariable);
    echo '</pre><br />';
  }
  else
  {
    echo "-------------------------------\nline '.$pnLine.' '.$psFile.':\n";
    dump($pvVariable);
    echo "\n";
  }

  }


  /**
   * get the value of a superglobal field
   * Control if isset, as default value, and manage post, get, session, cookie
   */
  function getValue($psVarName, $pvDefaultValue = '', $psSpecificVar = '', $pbStoreInSession = false)
  {

    $avDataSrc = array();

    if(!empty($psSpecificVar))
    {
      switch(strtolower($psSpecificVar))
      {
        case 'post':
          $avDataSrc = $_POST;
          break;

        case 'get':
          $avDataSrc = $_GET;
          break;

        case 'session':
          $avDataSrc = $_SESSION;
          break;

        case 'cookie':
          $avDataSrc = $_COOKIE;
          break;

        default:
          assert('false; // wrong type');
          return '';

      }

      if(empty($avDataSrc) || !isset($avDataSrc[$psVarName]))
      {
        if($pbStoreInSession)
          $_SESSION[$psVarName] = $pvDefaultValue;

        return $pvDefaultValue;
      }
      else
      {
        if($pbStoreInSession)
          $_SESSION[$psVarName] = $avDataSrc[$psVarName];

        return $avDataSrc[$psVarName];
      }

    }

    //try to fetch the value from POSt, then GEt, then Session, then cookie

    if(isset($_POST[$psVarName]))
    {
      if($pbStoreInSession)
          $_SESSION[$psVarName] = $_POST[$psVarName];

      return $_POST[$psVarName];
    }

    if(isset($_GET[$psVarName]))
    {
      if($pbStoreInSession)
          $_SESSION[$psVarName] = $_GET[$psVarName];

      return $_GET[$psVarName];
    }

    if(isset($_SESSION[$psVarName]))
      return $_SESSION[$psVarName];

    if(isset($_COOKIE[$psVarName]))
    {
      if($pbStoreInSession)
        $_SESSION[$psVarName] = $_COOKIE[$psVarName];

      return $_COOKIE[$psVarName];
    }

    return $pvDefaultValue;
  }


  function getFormatedDate($psFormat, $psDate = '')
  {
    if(!assert('!empty($psFormat)'))
      return '';

    if(empty($psDate))
      return date($psFormat);

    $psDate = trim($psDate);

    switch($psFormat)
    {
      case 'Y-m-d':
        $asDate = explode(' ', $psDate);
        $asDate[0] = str_replace('/', '-', $asDate[0]);
        return $asDate[0];
        break;

      case 'Y-m-d H:i:s':
        $asDate = explode(' ', $psDate);
        $asDate[0] = str_replace('/', '-', $asDate[0]);

        if(!isset($asDate[1]) || empty($asDate[1]))
        $asDate[1] = '00:00:00';

        return implode(' ', $asDate);
        break;

      default:
        assert('formatedDate doesn\'t know this format. ');
        break;
    }
  }

  function getRelativeUploadPath($sPath)
  {
    return str_ireplace($_SERVER['DOCUMENT_ROOT'], '', $sPath);
  }


  function makePath($psPath)
  {
    if(!assert('is_string($psPath) && !empty($psPath)'))
      return false;

    //remove filename if present
    $asPath = pathinfo($psPath);
    if(isset($asPath['extension']))
      $psPath = $asPath['dirname'];

    // be sure we won't try to create forlders outside the website directory
    // if given a absolute path
    $psPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $psPath);
    if(!$psPath)
    {
      assert('false; // path is not valid.');
      return false;
    }

    if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/'.$psPath, 0755, true))
    {
      assert('false; // couldn\'t create directories.');
      return false;
    }

    return true;
  }

  function isValidEmail($psString)
  {
    if(empty($psString))
      return false;

    return filter_var($psString, FILTER_VALIDATE_EMAIL) !== false;
  }

  function formatUrl($psString)
  {
    $psString = trim($psString);

    if(empty($psString) || strlen($psString) < 4)
      return '';

    $asUrl = parse_url($psString);

    //based on scheme presence in the url, the hostname goes in path or host
    if(!$asUrl || ( (!isset($asUrl['host']) || empty($asUrl['host'])) && (!isset($asUrl['path']) || empty($asUrl['path'])) ))
      return '';

    if(!isset($asUrl['port']) || empty($asUrl['port']))
      $asUrl['port'] = '';

    $bHasScheme = true;
    if(!isset($asUrl['scheme']) || empty($asUrl['scheme']))
    {
      $bHasScheme = false;

      switch($asUrl['port'])
      {
        case 443:
          $asUrl['scheme'] = 'https'; break;

        case 22:
          $asUrl['scheme'] = 'ssh'; break;

        case 21:
          $asUrl['scheme'] = 'ftp'; break;

        default:
          $asUrl['scheme'] = 'http'; break;
      }
    }

    if( ($bHasScheme && substr_count($asUrl['host'], '.') == 1) || (!$bHasScheme && substr_count($asUrl['path'], '.') == 1))
    {
      if($bHasScheme)
        $asUrl['host'] = 'www.'.$asUrl['host'];
      else
        $asUrl['path'] = 'www.'.$asUrl['path'];
    }

    //rebuild url from update array
    $sUser = isset($asUrl['user']) ? $asUrl['user'] : '';
    $sPass = isset($asUrl['pass']) ? ':' . $asUrl['pass']  : '';
    $sPass = ($sUser || $sPass) ? $sPass.'@' : '';

    $sUrl = isset($asUrl['scheme']) ? $asUrl['scheme'] . '://' : '';
    $sUrl.= $sUser.$sPass;

    //no port if we can't detect the hostname
    if(isset($asUrl['host']) && !empty($asUrl['host']))
    {
      $sUrl.= isset($asUrl['host']) ? $asUrl['host'] : '';
      $sUrl.= isset($asUrl['port']) && !empty($asUrl['port']) ? ':' . $asUrl['port'] : '';
    }

    $sUrl.= isset($asUrl['path']) ? $asUrl['path'] : '';
    $sUrl.= isset($asUrl['query']) ? '?' . $asUrl['query'] : '';
    $sUrl.= isset($asUrl['fragment']) ? '#' . $asUrl['fragment'] : '';
    return $sUrl;
  }

  /**
   * Return an integer to indicate if the url is correct.
   * 1: ok, 0: bad format url, -1: when we can't reach the destination
   * @param string $psString
   * @param boolean $pbFormatbefore
   * @param boolean $pbLiveTestUrl
   * @return integer
   */
  function isValidUrl($psString, $pbFormatbefore = false, $pbLiveTestUrl = false)
  {
    $psString = trim($psString);
    if(empty($psString) || strlen($psString) < 4)
      return false;

    if($pbFormatbefore)
      $psString = formatUrl($psString);

    $bUrlOk = filter_var($psString, FILTER_VALIDATE_URL) !== false;
    if(!$pbLiveTestUrl)
      return (int)$bUrlOk;

    if(checkUrlAvailability($psString))
      return 1;

    return -1;
  }

  function checkUrlAvailability($psUrl)
  {
    if(empty($psUrl) || strlen($psUrl) < 4)
      return false;

    $oCurl = curl_init($psUrl);
    curl_setopt($oCurl,  CURLOPT_RETURNTRANSFER, true);
    curl_setopt($oCurl,  CURLOPT_FAILONERROR, true);
    curl_setopt($oCurl,  CURLOPT_FRESH_CONNECT, true);
    curl_setopt($oCurl,  CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($oCurl,  CURLOPT_TIMEOUT, 3);
    curl_setopt($oCurl,  CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($oCurl,  CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($oCurl,  CURLOPT_AUTOREFERER, true);

    /* Get the HTML or whatever is linked to the $url. */
    $oCurlResponse = curl_exec($oCurl);

    /* Check for 404 (file not found). */
    $nHttpCode = (int)curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
    curl_close($oCurl);

    if($nHttpCode >= 200 && $nHttpCode < 300)
      return true;

    return false;
  }


  function isDevelopment()
  {
    if(stripos($_SERVER['HTTP_HOST'], 'devserv.com') !== false)
      return true;

    return false;
  }

  function getEventTypeList($pbOnlyValues = false)
  {
    if($pbOnlyValues)
    {
      $asEvent[] = 'article';
      $asEvent[] = 'email';
      $asEvent[] = 'meeting';
      $asEvent[] = 'phone';
      $asEvent[] = 'update';
      $asEvent[] = 'deal';
      $asEvent[] = 'proposal';
      $asEvent[] = 'invoice';
      $asEvent[] = 'payment';
    }
    else
    {
      $asEvent[] = array('label' => 'Article', 'value' => 'article', 'group' => 'Info');
      $asEvent[] = array('label' => 'Email', 'value' => 'email', 'group' => 'Info');
      $asEvent[] = array('label' => 'Meeting', 'value' => 'meeting', 'group' => 'Info');
      $asEvent[] = array('label' => 'Phone call', 'value' => 'phone', 'group' => 'Info');
      $asEvent[] = array('label' => 'Update', 'value' => 'update', 'group' => 'Info');
      $asEvent[] = array('label' => 'Deal', 'value' => 'deal', 'group' => 'Business');
      $asEvent[] = array('label' => 'Proposal', 'value' => 'proposal', 'group' => 'Business');
      $asEvent[] = array('label' => 'Invoice', 'value' => 'invoice', 'group' => 'Finance');
      $asEvent[] = array('label' => 'Payment', 'value' => 'payment', 'group' => 'Finance');
    }

    return $asEvent;
  }

  function getCompanyRelation($pnPk=0)
  {
    $asCompany = array(1 =>array('Label'=>'Client','icon'=>'client.png','icon_small'=>'client_small.png'),
        2 =>array('Label'=>'Supplier','icon'=>'supplier.png','icon_small'=>'supplier_small.png'),
        3 =>array('Label'=>'Candidate','icon'=>'candidate.png','icon_small'=>'candidate_small.png'),
        4 =>array('Label'=>'Collaborator','icon'=>'collaborator.png','icon_small'=>'collaborator_small.png'),
        5 =>array('Label'=>'Prospect','icon'=>'prospect.png','icon_small'=>'prospect_small.png'));

    if($pnPk)
     return $asCompany[$pnPk];
    else
     return $asCompany;
  }


  function getContactGrade($pnPk=0)
  {
    $asGrade = array('1'=>'Top decision maker', '2'=>'Mid management, senior staff', '4'=>'Staff, Junior ', '5'=>'Unknown');
    if($pnPk)
      return $asGrade[$pnPk];
    else
      return $asGrade;
  }

  function getSettingCategory($pnPk = 0)
  {
    $oPage = CDependency::getComponentByName('page');

    $sSiteUrl      = $oPage->getAjaxUrl('settings',CONST_ACTION_ADD, CONST_TYPE_SETTINGS);
    $sUserUrl      = $oPage->getAjaxUrl('settings',CONST_ACTION_ADD, CONST_TYPE_SETTING_USER);
    $sUserRightUrl = $oPage->getAjaxUrl('settings',CONST_ACTION_ADD, CONST_TYPE_SETTING_USRIGHT);
    $sMenuUrl      = $oPage->getAjaxUrl('settings',CONST_ACTION_ADD, CONST_TYPE_SETTING_MENU);
    $sFooterUrl    = $oPage->getAjaxUrl('settings',CONST_ACTION_ADD, CONST_TYPE_SETTING_FOOTER);
    $sBlackListUrl = $oPage->getAjaxUrl('settings',CONST_ACTION_ADD, CONST_TYPE_SETTING_BLACKLIST);
    $sCronJobUrl   = $oPage->getAjaxUrl('settings',CONST_ACTION_ADD, CONST_TYPE_SETTING_CRON);

    $asSetting = array(1=>array('Label'=>'Site Settings','onclick'=>"AjaxRequest('".$sSiteUrl."', 'body', '', 'settingContainer');"),2=>array('Label'=>'Users','onclick'=>"AjaxRequest('".$sUserUrl."', 'body', '', 'settingContainer'); "),3=>array('Label'=>'User Rights','onclick'=>"AjaxRequest('".$sUserRightUrl."', 'body', '', 'settingContainer'); "),4=>array('Label'=>'Menus','onclick'=>"AjaxRequest('".$sMenuUrl."', 'body', '', 'settingContainer'); "),5=>array('Label'=>'Footer','onclick'=>"AjaxRequest('".$sFooterUrl."', 'body', '', 'settingContainer'); "),6=>array('Label'=>'Blacklist','onclick'=>"AjaxRequest('".$sBlackListUrl."', 'body', '', 'settingContainer'); "),7=>array('Label'=>'Cron','onclick'=>"AjaxRequest('".$sCronJobUrl."', 'body', '', 'settingContainer'); "));

    if($pnPk)
      return $asSetting[$pnPk];
    else
     return $asSetting;
  }

  function isUidAvailable($psUid)
  {
      $asDependency = array('database'=>'124-546','page'=>'845-187','display'=>'569-741','form'=>'668-313','login'=>'579-704'
          , 'addressbook'=>'777-249','project'=>'456-789','sharedspace'=>'999-111','search'=>'898-775','pager'=>'140-510',
          'event'=>'007-770','webmail'=>'009-724','mail'=>'008-724','querybuilder'=>'210-482');
      if(in_array($psUid,$asDependency))
          return true;
      else
         return false;
  }

  //================================================
  //SearchHistory functions
  function manageSearchHistory($psGuid='', $psType='', $pbForceNew = false)
  {
    //starts with maintenance: keep session array size under 50 for every component/type
    if(isset($_SESSION['searchHistory'][$psGuid][$psType]) && count($_SESSION['searchHistory'][$psGuid][$psType]) > 50)
    {
      $_SESSION['searchHistory'][$psGuid][$psType] = array_reverse($_SESSION['searchHistory'][$psGuid][$psType], true);
      //array_pop($_SESSION['searchHistory']);
      array_pop($_SESSION['searchHistory'][$psGuid][$psType]);
      $_SESSION['searchHistory'][$psGuid][$psType] = array_reverse($_SESSION['searchHistory'][$psGuid][$psType], true);
    }

    //to prevent dev errors, let's check lower case name too
    $sSearchId = getValue('searchId', 0);

    if(empty($sSearchId))
       $sSearchId = getValue('searchid', 0);

    //Create a new id and save current post/get datas (undefined too prevent javascript errors)
    if($pbForceNew || empty($sSearchId) || $sSearchId == 'undefined' || !isset($_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]) || empty($_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]))
    {
      $sSearchId = uniqid('search_');
      $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['post'] = $_POST;
      $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['get'] = $_GET;
      $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortfield'] = '';
      $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortorder'] = '';
      return $sSearchId;
    }

    global $gbNewSearch;
    $gbNewSearch = false;

    //store sorting and pager options
    $sSortField = getValue('sortfield');
    $sSortOrder = getValue('sortorder');
    $nPageOffset = (int)getValue('pageoffset', 0);
    $nNbResult = (int)getValue('nbresult', 0);

    if(!empty($sSortField))
    {
      if(!empty($sSortOrder))
      {
        $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortfield'] = $sSortField;
        $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortorder'] = $sSortOrder;
      }
      else
      {
        //check for sort order in history
        if($sSortField == $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortfield'])
        {
          if($_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortorder'] == 'ASC')
            $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortorder'] = 'DESC';
          else
            $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortorder'] = 'ASC';
        }
        else
        {
          $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortfield'] = $sSortField;
          $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['sortorder'] = 'ASC';
        }
      }
    }

    //update the page number and page result
    if($nPageOffset)
      $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['post']['pageoffset'] = $nPageOffset;

    if($nNbResult)
      $_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['post']['nbresult'] = $nNbResult;


    //we ve got an id: overwrite current POST/GET data with saved ones
    //exclude sortfield if passed in parameters
    foreach($_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['post'] as $sParamName => $sParamValue)
    {
      if(empty($sSortField) || $sParamName != 'sortfield')
        $_POST[$sParamName] = $sParamValue;
    }

    foreach($_SESSION['searchHistory'][$psGuid][$psType][$sSearchId]['get'] as $sParamName => $sParamValue)
    {
      if(empty($sSortField) || $sParamName != 'sortfield')
        $_GET[$sParamName] = $sParamValue;
    }

    //restore the new offset if erased just above
    /*$_POST['pageoffset'] = $nPageOffset;
    $_POST['nbresult'] = $nNbResult;*/

    return $sSearchId;
  }

  function saveSearchHistorySql($psSearchId, $psSql, $psGuid='', $psType='')
  {
    if(empty($psSearchId) || empty($psSql))
      return false;

    if(!isset($_SESSION['searchHistory'][$psGuid][$psType][$psSearchId]))
      return false;

    $_SESSION['searchHistory'][$psSearchId]['sql'] = $psSql;
    return true;
  }

  function getSearchHistory($psSearchId, $psGuid='', $psType='')
  {
    if(empty($psSearchId))
      return array();

    if(!isset($_SESSION['searchHistory'][$psGuid][$psType][$psSearchId]))
      return array();

    return $_SESSION['searchHistory'][$psGuid][$psType][$psSearchId];
  }

  function setSearchHistory($asHistoryData, $psSearchId, $psGuid='', $psType='')
  {
    if(empty($psSearchId) || !is_array($asHistoryData))
      return false;

    if(!isset($_SESSION['searchHistory'][$psGuid][$psType][$psSearchId]))
      return false;

    $_SESSION['searchHistory'][$psGuid][$psType][$psSearchId] = $asHistoryData;
    return true;
  }

  function reloadLastSearch($psGuid='', $psType='')
  {
    $sId = getLastSearchId($psGuid, $psType);
    $_POST['searchId'] = $sId;

    manageSearchHistory($psGuid, $psType);
      return $sId;
   }

  function getLastSearchId($psGuid='', $psType='')
  {
    if(!isset($_SESSION['searchHistory'][$psGuid][$psType]))
      return '';

    $asSearchId = array_keys($_SESSION['searchHistory'][$psGuid][$psType]);
      return end($asSearchId);
  }

  function getLanguageLevel($pnLanguagelevel)
  {
    $asLangArray =   array(0 => 'None',1 =>'Basic',2 => 'Conversational',3 =>'Business',4 => 'Fluent',5 =>'Native');
    return  $asLangArray[$pnLanguagelevel];
  }

  function showHideSearchForm($psSetTime,$psType)
  {
    $oPage = CDependency::getComponentByName('page');

    if(!isset($_SESSION['storetime'][$psType]))
      $_SESSION['storetime'][$psType] = 0;

    if(!empty($psSetTime) && ($_SESSION['storetime'][$psType] != $psSetTime))
    {
      $_SESSION['storetime'][$psType] = $psSetTime;
      $sJavascript = " $(document).ready(function(){ $('.searchContainer').show(); }); ";
      $oPage->addCustomJs($sJavascript);
    }
    else
    {
      $sJavascript = " $(document).ready(function(){ $('.searchContainer').hide(); }); ";
      $oPage->addCustomJs($sJavascript);
     }
   }

   function curPageURL()
   {
      $sPageURL = 'http';
      if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$sPageURL.= "s";}
      $sPageURL .= "://";
      if($_SERVER["SERVER_PORT"] != "80")
      {
        $sPageURL.= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
      }
      else
      {
        $sPageURL.= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
      }
     return $sPageURL;
  }

  //Check for kanji

 function isKanji($sStr)
 {
    return preg_match('/[\x{4E00}-\x{9FBF}]/u', $sStr) > 0;
 }

 //Check for Hiragana

 function isHiragana($sStr)
 {
    return preg_match('/[\x{3040}-\x{309F}]/u', $sStr) > 0;
  }

  //Check for Katakana

 function isKatakana($sStr)
 {
    return preg_match('/[\x{30A0}-\x{30FF}]/u', $sStr) > 0;
  }

  //Check for all japanese

 function isJapanese($sStr)
 {
   return isKanji($sStr) || isHiragana($sStr) || isKatakana($sStr);
 }


  function cutStringByWords($psStringToCut, $pnWords = 5)
  {
    if(isJapanese($psStringToCut))
      return $psStringToCut;

    $asString = explode(' ', $psStringToCut);
    if(count($asString) <= $pnWords)
      return $psStringToCut;

    $asString = array_slice($asString, 0, $pnWords);
    return implode(' ', $asString);
  }

  function array_trim($pasArray, $pbRemoveEmpty = false, $pbUnique = false)
  {
    if(!assert('is_array($pasArray)'))
      return array();

    foreach($pasArray as $vKey => $vValue)
    {
      if(!is_object($vValue) || !is_array($vValue))
      {
        $vValue = trim($vValue);
        if($pbRemoveEmpty && empty($pbRemoveEmpty))
          unset($pasArray[$vKey]);
        else
          $pasArray[$vKey] = $vValue;
      }
    }

    if($pbUnique)
      return array_unique($pasArray);

    return $pasArray;
  }

  function addIndustry($pnPk, $psLabel, $pnStatus, $pnParentfk)
  {
    if(!assert('is_integer($pnPk) && is_integer($pnStatus) && is_integer($pnStatus)'))
      return false;

    $oDb = CDependency::getComponentByName('database');

    if(!empty($pnPk))
    {
      $sQuery = 'INSERT INTO `industry` (`industrypk`, `name`, `status`, `parentfk`) VALUES ';
      $sQuery.= ' ('.$pnPk.', '.$oDb->dbEscapeString($psLabel).', '.$oDb->dbEscapeString($pnStatus).', '.$oDb->dbEscapeString($pnParentfk).') ';
    }
    else
    {
      $sQuery = 'INSERT INTO `industry` (`name`, `status`, `parentfk`) VALUES ';
      $sQuery.= ' ('.$oDb->dbEscapeString($psLabel).', '.$oDb->dbEscapeString($pnStatus).', '.$oDb->dbEscapeString($pnParentfk).') ';
    }

    $oDbResult = $oDb->ExecuteQuery($sQuery);
    if(!$oDbResult)
      return false;

    return true;
  }