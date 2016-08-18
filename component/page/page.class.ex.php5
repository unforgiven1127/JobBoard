<?php

require_once('component/page/page.class.php5');

class CPageEx extends CPage
{
  private $coRight = null;

  private $casCustomJs = array();
  private $casJsFile = array();
  private $casCssFile = array();

  private $csRequestedUrl = '';
  private $csEmbedUrl = '';
  private $casUrlDetail = array();
  private $cbIsLogged = false;

  private $csPageKeywords = '';
  private $csPageTitle = '';
  private $csPageDesc = '';

  public function __construct()
  {
    if(isset($_GET['debug']))
    {
      if($_GET['debug'] == 'none')
        unset($_SESSION['debug']);
      else
        $_SESSION['debug'] = $_GET['debug'];
    }

    if($_SERVER['SERVER_PORT'] === '80')
      $this->csRequestedUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    else
      $this->csRequestedUrl = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    $this->casUrlDetail = parse_url($this->csRequestedUrl);

    if(!isset($this->casUrlDetail['query']))
      $this->casUrlDetail['query'] = '';

    if(empty($_SESSION['browser']))
    {
      //load the detection class and check if we're mobile
      require_once('component/page/resources/class/mobile_detect.class.php');
      $oDetector = new Mobile_Detect();

      $_SESSION['browser']['is_mobile'] = (bool)$oDetector->isMobile();
      $_SESSION['browser']['device_type'] = ($_SESSION['browser']['is_mobile'] ? ($oDetector->isTablet() ? CONST_PAGE_DEVICE_TYPE_TABLET : CONST_PAGE_DEVICE_TYPE_PHONE) : CONST_PAGE_DEVICE_TYPE_PC);
    }

    if(getValue('setPage') == 'mobile')
    {

      $_SESSION['browser']['is_mobile'] = 1;
      $_SESSION['browser']['device_type'] = CONST_PAGE_DEVICE_TYPE_PHONE;
    }

    /*$_SESSION['browser']['is_mobile'] = 1;
    $_SESSION['browser']['device_type'] = CONST_PAGE_DEVICE_TYPE_PHONE;
    //$_SESSION['browser']['device_type'] = CONST_PAGE_DEVICE_TYPE_PC;
     */
  }

  // Function for the cookie and session management
  public function init()
  {
    if(isset($_SESSION['userData']['pk']) && !empty($_SESSION['userData']['pk']))
    {
      //refresh the cookie for 4 extra hour
      if(isset($_COOKIE['login_userdata']))
      {
        setcookie('login_userdata', $_COOKIE['login_userdata'], time()+3600*3, '/');
      }
      else
      {
        //recreate the cookie
        $oLogin = CDependency::getComponentByName('login');
        $oLogin->rebuildCookie();
      }
    }

    return true;
  }

  //=============================
  //accessors

  public function getRequestedUid()
  {
    return $this->csUid;
  }

  public function getAction()
  {
    return $this->csAction;
  }
  public function getActionReturn()
  {
    return $this->csActionReturn;
  }

  public function getType()
  {
    return $this->csType;
  }

  public function getPk()
  {
    return $this->cnPk;
  }

  public function getMode()
  {
    return $this->csMode;
  }

  public function getRequestedUrl()
  {
    return $this->csRequestedUrl;
  }

  public function getEmbedUrl()
  {
    return $this->csEmbedUrl;
  }

  public function isMobileBrowser()
  {
    return (bool)$_SESSION['browser']['is_mobile'];
  }

  public function getDeviceType()
  {
    return $_SESSION['browser']['device_type'];
  }

  public function getLanguage()
  {
    //language saved in attribute to make the detection once
    if(!empty($this->csLanguage))
      return $this->csLanguage;

    if(empty($this->coSettings))
      $this->coSettings = CDependency::getComponentByName('settings');

    //$asLanguage =  explode(',', CONST_AVAILABLE_LANGUAGE);
    $asSettings = $this->coSettings->getSettings('languages');
    if(empty($asSettings['languages']))
      $asLanguage = array(CONST_DEFAULT_LANGUAGE);

    //a request to set the language and save it in session
    $sRequestedLang = getValue('setLang', '');
    if(!empty($sRequestedLang) && in_array($sRequestedLang, $asSettings['languages']))
    {
      $_SESSION['lang'] = $sRequestedLang;
      $this->csLanguage = $sRequestedLang;
      return $sRequestedLang;
    }

    //a request to set the language for the current page
    $sRequestedLang = getValue('lg', '');
    if(!empty($sRequestedLang) && in_array($sRequestedLang, $asSettings['languages']))
    {
      $this->csLanguage = $sRequestedLang;
      return $sRequestedLang;
    }

    if(isset($_SESSION['lang']) && !empty($_SESSION['lang']))
    {
      $this->csLanguage = $_SESSION['lang'];
      return $_SESSION['lang'];
    }

    return CONST_DEFAULT_LANGUAGE;
  }

  //====================================================================
  //  public methods
  //====================================================================

  public function getPage($psUid = '', $psAction = '', $psType = '', $pnPK = 0, $psMode = 'pg')
  {
    if(!assert('is_string($psUid)'))
      return '';
    if(!assert('is_string($psAction)'))
      return '';
    if(!assert('is_string($psType)'))
      return '';
    if(!assert('is_integer($pnPK)'))
      return '';
    if(!assert('is_string($psMode)'))
      return '';

    if(empty($this->csUid))
      $this->csUid = $psUid;
    if(empty($this->csAction))
      $this->csAction = $psAction;
    if(empty($this->csType))
      $this->csType = $psType;
    if(empty($this->cnPk))
      $this->cnPk = $pnPK;
    if(empty($this->csMode))
      $this->csMode = $psMode;

    //load website settings
    $this->coSettings = CDependency::getComponentByName('settings');
    if(empty($this->coSettings))
      exit('Could not load settings component. Sorry, you can not go further.');

    $this->coRight = CDependency::getComponentByName('right');
    if(empty($this->coRight))
      exit('Could not load rights component. Sorry, you can not go further.');


    //*****************************************************************
    //*****************************************************************
    //gather and initialize some parameters

    $asMeta = $this->coSettings->getSettings(array('meta_tags', 'meta_desc', 'title'));
    $this->setPageDescription($asMeta['meta_desc']);
    $this->setPageKeywords($asMeta['meta_tags']);
    $this->setPageTitle($asMeta['title']);

    $this->csActionReturn = getValue(CONST_URL_ACTION_RETURN);
    $asPageParam = array('class' => $this->getDeviceType());

    $sHTML = '';

    //Check login status, accessrights...
    /* @var $oLogin CLoginEx */
    $oLogin = CDependency::getComponentByName('Login');
    $sLoginUid = $oLogin->getComponentUid();

    $bIsLogged = $oLogin->isLogged();
    $this->cbIsLogged = $bIsLogged;

    //*****************************************************************
    //*****************************************************************

    //if i'm logged, I must be using SSL
    if($bIsLogged && $this->casUrlDetail['scheme'] !== 'https')
    {
      @header('location:https://'.$this->casUrlDetail['host'].$this->casUrlDetail['path'].'?'.$this->casUrlDetail['query']);
      echo '<script>document.location.href = "https://'.$this->casUrlDetail['host'].$this->casUrlDetail['path'].'?'.$this->casUrlDetail['query'].'"; </script>';
      echo 'Being redirected to safer place. Click <a href=""/>here</a> if nothing happens in the next 5 seconds.';
      exit();
    }

    //--------------------------------------------------------------------
    //check if a component as a default homepageInterface to replace login
    if(empty($psUid) || empty($this->csUid))
    {
      $asHomeUid = CDependency::getComponentUidByInterface('homepage');

      if(!empty($asHomeUid))
        $this->csUid = $psUid = current($asHomeUid);
      else
        $this->csUid = $psUid = $oLogin->getComponentUid();
    }

    if(CDependency::hasInterfaceByUid($psUid, 'publicContent'))
      $bPublicContent = true;
    else
    {
      $bPublicContent = false;
      $this->_AccessLog($oLogin);
    }

    //--------------------------------------------------------------------
    //right management
    if(!$this->coRight->canAccess($this->csUid, $this->csAction, $this->csType, $this->cnPk))
    {
      $oHTML = CDependency::getComponentByName('display');
      if(empty($oHTML))
      {
        exit(__LINE__.' - No display library O_o');
      }

      $this->_getCustomUserFeature($bIsLogged);
      $sRestrictedPage = $oLogin->getRestrictedPage($bIsLogged);

      ChromePhp::log(debug_backtrace());

      $sHTML = $oHTML->getHeader($bIsLogged, $this->casJsFile, $this->casCustomJs, $this->casCssFile, $asMeta, $asPageParam);
      $sHTML.= $oHTML->getMenu($bIsLogged, $psUid);
      $sHTML.= $oHTML->getComponentStart($bIsLogged);
      $sHTML.= $sRestrictedPage;
      $sHTML.= $oHTML->getComponentEnd();
      $sHTML.= $oHTML->getFooter();
      return $sHTML;
    }

    switch($this->csMode)
    {
      /* TODO: manage JS inclusions in JSON !!!  */

      case CONST_URL_PARAM_PAGE_AJAX:

        if(empty($this->csUid))
          return json_encode(__LINE__.' - page: error bad uid');

        //The only ajax request allowed when not logged in, is to actually log in :)
        /*if(!$bPublicContent && !$bIsLogged && $this->csUid != $sLoginUid)
          return json_encode(__LINE__.' - page: not allowed');*/

        $oRequestedComponent = CDependency::getComponentByUid($this->csUid, 'ajax');
        if(empty($oRequestedComponent))
          return json_encode('error no interface for the uid requested('.$this->csUid.')');

         return $oRequestedComponent->getAjax();
         break;

      case CONST_URL_PARAM_PAGE_CRON:

        if(getValue('hashCron') != '1')
          exit();

        $sCpUid = getValue('custom_uid');
        $bSilent = (bool)getValue('cronSilent', 0);

        if(!$bSilent)
          echo 'Cron started at '.date('Y-m-d H:i:s').' '. microtime(true).'<br /><br />';

        $asComponentUid = CDependency::getComponentIdByInterface('cron');

        foreach($asComponentUid as $sUid)
        {
          if(empty($sCpUid) || $sCpUid == $sUid)
          {
            if(!$bSilent)
              echo '<br /><hr /><h1>'.$sUid.'</h1><br />';

            $oComponenent = CDependency::getComponentByUid($sUid);
            $oComponenent->getCronJob();
          }
        }

        if(!$bSilent)
          echo '<br/><br/><hr/>Cron finished at '.date('Y-m-d H:i:s').' '.  microtime(true).'';
        exit();

        break;

      case CONST_URL_PARAM_PAGE_EMBED:

        if($bIsLogged && isset($_GET[CONST_URL_EMBED]) && !empty($_GET[CONST_URL_EMBED]))
        {
          $this->csEmbedUrl = $_GET[CONST_URL_EMBED];
          $oHTML = CDependency::getComponentByName('display');

          $sHTML = $oHTML->getHeader($bIsLogged, $this->casJsFile, $this->casCustomJs, $this->casCssFile, $asMeta, $asPageParam);
          $sHTML.= $oHTML->getMenu($bIsLogged, $this->csUid);
          $sHTML.= $oHTML->getComponentStart($bIsLogged);
          $sHTML.= $oHTML->getEmbedPage(urldecode($_GET[CONST_URL_EMBED]));
          $sHTML.= $oHTML->getComponentEnd();
          $sHTML.= $oHTML->getFooter();
        }
        break;

      default:

        $oHTML = CDependency::getComponentByName('display');
        if(empty($oHTML))
        {
          assert('false; // big trouble ');
          exit();
        }

        if(!$bIsLogged && !$bPublicContent)
        {
          /*if(empty($_SESSION['urlRedirect']))
            $_SESSION['urlRedirect'] = CONST_CRM_DOMAIN.$_SERVER['REQUEST_URI'];  */

          //call component before creating header to allow file inclusions
          $oLogin->setType('restricted');
          $sComponentHtml = $oLogin->getHtml();

          $this->_getCustomUserFeature($bIsLogged);

          $sHTML = $oHTML->getHeader($bIsLogged, $this->casJsFile, $this->casCustomJs, $this->casCssFile, $asMeta, $asPageParam);
          $sHTML.= $oHTML->getComponentStart($bIsLogged);
          $sHTML.= $sComponentHtml;
          $sHTML.= $oHTML->getComponentEnd();
          $sHTML.= $oHTML->getFooter();
        }
        else
        {
          if(empty($this->csUid))
            $oRequestedComponent = CDependency::getComponentByName('login');
          else
            $oRequestedComponent = CDependency::getComponentByUid($this->csUid);

          $this->_getCustomUserFeature($bIsLogged);

          if(empty($oRequestedComponent))
          {
            $sHTML = '';
            $sHTML.= $oHTML->getHeader($bIsLogged, $this->casJsFile, $this->casCustomJs, $this->casCssFile, $asMeta, $asPageParam);
            $sHTML.= $oHTML->getMenu($bIsLogged, $this->csUid);
            $sHTML.= $oHTML->getComponentStart($bIsLogged);
            $sHTML.= $oHTML->getBlocMessage('Wrong parameters / component !! <br /> This url leads nowhere: '.$_SERVER['REQUEST_URI']);
            $sHTML.= $oHTML->getComponentEnd();
          }
          else
          {
            $sComponentHtml = $oRequestedComponent->getHtml();
            if(empty($sComponentHtml))
               $sComponentHtml = $oHTML->getNoContentMessage();

            //rebuild meta with what the component may have included/removed from it
            $asMeta['meta_tags'] = $this->getPageKeywords();
            $asMeta['meta_desc'] = $this->getPageDescription();
            $asMeta['title'] = $this->getPageTitle();

            $sHTML = '';
            $sHTML.= $oHTML->getHeader($bIsLogged, $this->casJsFile, $this->casCustomJs, $this->casCssFile, $asMeta, $asPageParam);
            $sHTML.= $oHTML->getMenu($bIsLogged, $this->csUid);
            $sHTML.= $oHTML->getComponentStart($bIsLogged);
            $sHTML.= $sComponentHtml;
            $sHTML.= $oHTML->getComponentEnd();
            $sHTML.= $oHTML->getFooter();
          }
        }
        break;
      }

    return $sHTML;
  }


  public function getFooter()
  {
    $oHTML = CDependency::getComponentByName('display');

    //adding a div for the float hack css
    $sHTML = $oHTML->getBlocStart('', array('class' => 'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();
    //closing blocs opened in header
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    if(CONST_WEBSITE == 'bcm')
    {
     $sHTML.= $oHTML->getBlocStart('footerContainerId');

     $sHTML.= $oHTML->getBlocStart();
     $sHTML.= '<ul>';
     $sHTML.= '<li>';
     $sURL = $this->getUrl('login');
     $sHTML.= $oHTML->getLink('Home', $sURL);
     $sHTML.= '</li>';
     $sHTML.= '<li>';
     $sHTML.= $oHTML->getLink('Google', 'http://www.google.com', array('target' => '_blank'));
     $sHTML.= '</li>';
     $sHTML.= '<li>';
     $sHTML.= $oHTML->getLink('Report a bug', 'mailto:sboudoux@bulbouscell.com', array('target' => '_blank'));
     $sHTML.= '</li>';
     $sHTML.= '<li>';
     $sHTML.= '<a href="javascript:;" onclick="setCoverScreen(true, true);" >Loading</a>' ;
     $sHTML.= '</li>';
     $sHTML.= '</ul>';

     $sHTML.= $oHTML->getBlocEnd();
     $sHTML.= $oHTML->getBlocEnd();
    }
    else if(CONST_WEBSITE == 'talentAtlas')
    {
      $sHTML.= $oHTML->getBlocStart('footerDiv');
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'footerClass'));

      $sHTML.= $oHTML->getBlocStart();
      $sHTML.= $oHTML->getText('Copyright 2012 ');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart();
      $sHTML.= $oHTML->getText('About Us ');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart();
      $sHTML.= $oHTML->getText('Terms & Conditions ');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart();
      $sHTML.= $oHTML->getText('Privacy ');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart();
      $sHTML.= $oHTML->getText('Cookies ');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
    }

    $sHTML.= $oHTML->getBlocStart('ajaxErrorContainerId', array('class' => 'ajaxErrorBlock'));
    $sHTML.= $oHTML->getBlocStart('ajaxErrorInnerId', array('class' => 'notice2'));
    $sHTML.= $oHTML->getBlocStart();

    $sHTML.= $oHTML->getBlocStart('', array('style' => 'float:right; '));
    $sHTML.= $oHTML->getLink('Close', 'javascript:;', array('onclick' => "$('#ajaxErrorContainerId').hide();"));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getTitle('Oops, an error occured', 'h2', true);
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= $oHTML->getText("An unknown error occured while executing your last action.");
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= $oHTML->getText("If you're seeing this message for the first time, please try to reload the page or close your web browser before starting again.");
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= "In the other case, please contact the administrator or report the problem using <a href='javascript:;' onclick=' $(\"#dumpFormId\").submit();'>this form</a>.";
    $sHTML.= "<form name='dumpForm' id='dumpFormId' method='post' action='/error_report.php5' ><input type='hidden' name='dump' id='dumpId' /> </form>";
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('embedPopupId');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('popupBlockId', array('style' => 'display:none; position:absolute;'));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= '
      <div id="loadingScreenAnimation">
        <img src="/media/picture/loading.gif"/>
      </div>
    </div>

    <div id="loadingScreen">

      <div id="loadingScreenAnimation">
        <img src="/media/picture/loading.gif"/>
      </div>
    </div>

    <div id="popupContainer">
    <div id="popupInner">

      <div id="popupClose">
        <a href="javascript:;" onClick="setLoadingScreen(\'#slystemContainer\', false); removePopup();"><img src="/media/picture/close_24.png"/></a>
      </div>

      <div id="popupInnerContainer">
      </div>

     <div id="popupCloseBottom">
      <input type="button" value="Close" onClick="setLoadingScreen(\'#slystemContainer\', false); removePopup()">
     </div>

     <img id="loaderGif" src="/common/pictures/loading.gif" style="display: none;" border=0 />

     <div class="floatHack"></div>

   </div>
   </div>';

    return $sHTML;

  }

  public function getHtml()
  {
    $oHTML = CDependency::getComponentByName('display');
    return $oHTML->getBlocMessage('Wrong parameters / component !! <br /> This url leads nowhere: '.$_SERVER['REQUEST_URI']);
  }

  public function getUrl($psComponent, $psAction = '', $psType = '', $pnPk = 0, $pasOptions = array(), $psHash = '')
  {
    if(!assert('is_string($psComponent)'))
      return '';
    if(!assert('is_string($psAction)'))
      return '';
    /*
    if(!assert('is_string($psType)'))
      return '';
    if(!assert('is_integer($pnPk)'))
      return '';
     *
     */
    if (preg_match('/^([0-9]{3})-([0-9]{3})$/i', $psComponent))
      $sUid = $psComponent;
    else
    {
      $sUid = CDependency::getComponentUidByName($psComponent);
    }

    if($this->cbIsLogged)
    {
      $sURL = 'https://'.CONST_CRM_HOST.'/index.php5?'.CONST_URL_UID.'='.$sUid.'&'.CONST_URL_ACTION.'='.$psAction;
      $sURL.= '&'.CONST_URL_TYPE.'='.$psType.'&'.CONST_URL_PK.'='.$pnPk;
    }
    else
    {
      $sURL = $this->casUrlDetail['scheme'].'://'.CONST_CRM_HOST.'/index.php5?'.CONST_URL_UID.'='.$sUid.'&'.CONST_URL_ACTION.'='.$psAction;
      $sURL.= '&'.CONST_URL_TYPE.'='.$psType.'&'.CONST_URL_PK.'='.$pnPk;
    }

    if(!empty($pasOptions))
    {
      foreach($pasOptions as $sOption => $sValue)
        $sURL.= '&'.urlencode($sOption).'='.urlencode($sValue);
    }
    if(!empty($psHash))
    {
      $sURL.='#'.$psHash;
    }

    return $sURL;
  }


  public function getUrlHome()
  {
    $asParams = $this->coSettings->getSettings('urlparam');

    if(isset($asParams['cp_uid']) && !empty($asParams['cp_uid']))
      $this->csUid = $asParams['cp_uid'];

    if(isset($asParams['cp_action']) && !empty($asParams['cp_action']))
      $this->csAction = $asParams['cp_action'];

    if(isset($asParams['cp_type']) && !empty($asParams['cp_type']))
      $this->csType = $asParams['cp_type'];

    if(isset($asParams['cp_pk']) && !empty($asParams['cp_pk']))
      $this->cnPk = (int)$asParams['cp_pk'];

    if(!empty($this->csUid) && !empty($this->csAction))
      $sURL = $this->getUrl($this->csUid, $this->csAction,$this->csType,$this->cnPk);
    else
    {
      $oLogin = CDependency::getComponentByName('login');
      $sURL = $this->getUrl($oLogin->getComponentUid(),'',$oLogin->getDefaultType(),0);
    }

    return $sURL;
  }


  public function getUrlEmbed($psUrl)
  {
    $sUrl = $this->getUrl($this->_getUid(), CONST_ACTION_VIEW, '', 0, array(CONST_URL_MODE => CONST_URL_PARAM_PAGE_EMBED, CONST_URL_EMBED =>$psUrl));
    return $sUrl;
  }

  public function getAjaxUrl($psComponent, $psAction = '', $psType = '', $pnPk = 0, $pasOptions = array())
  {
    $sURL = $this->getUrl($psComponent, $psAction, $psType, $pnPk, $pasOptions);
    $sURL.= '&'.CONST_URL_MODE.'='.CONST_URL_PARAM_PAGE_AJAX;
    return $sURL;
  }

  public function isAjaxUrl($psUrl)
  {
    if(!assert('is_string($psUrl)') || empty($psUrl))
      return false;

    if(preg_match('/'.'&'.CONST_URL_MODE.'='.CONST_URL_PARAM_PAGE_AJAX.'/', $psUrl) == false)
      return false;

    return true;
  }

  public function redirect($psURL)
  {
    @header('location: '.$psURL);
    $sHTML = '<script type="text/javascript">document.location.href = "'.$psURL.'";</script>';
    $sHTML.= 'You\'re gonna be redirected in a few seconds. You can click on <a href="'.$psURL.'">this link </a> to access the page right now.';

    return $sHTML;
  }

  public function addUrlParams($psUrl, $pasParams)
  {
    if(!assert('!empty($psUrl)'))
      return '';

    if(empty($pasParams))
      return $psUrl;

    $asUrl = parse_url($psUrl);
    if(!isset($asUrl['host']))
      $asUrl['host'] = CONST_CRM_DOMAIN;

    if(!isset($asUrl['path']))
      $asUrl['path'] = '/';

    $asQuery = array();
    foreach($pasParams as $sParam => $sValue)
      $asQuery[] = $sParam.'='.$sValue;

    if(!isset($asUrl['query']))
    {
      $asUrl['query'] = '?'.implode('&', $asQuery);
    }
    else
    {
       $asUrl['query'].= '&'.implode('&', $asQuery);
    }

    $sScheme   = isset($asUrl['scheme']) ? $asUrl['scheme'] . '://' : '';
    $sHost     = isset($asUrl['host']) ? $asUrl['host'] : '';
    $sPort     = isset($asUrl['port']) ? ':' . $asUrl['port'] : '';
    $sUser     = isset($asUrl['user']) ? $asUrl['user'] : '';
    $sPass     = isset($asUrl['pass']) ? ':' . $asUrl['pass']  : '';
    $sPass     = ($sUser || $sPass) ? "$sPass@" : '';
    $sPath     = isset($asUrl['path']) ? $asUrl['path'] : '';
    $sQuery    = isset($asUrl['query']) ? '?'.$asUrl['query'] : '';
    $sFragment = isset($asUrl['fragment']) ? '#' . $asUrl['fragment'] : '';

    return "$sScheme$sUser$sPass$sHost$sPort$sPath$sQuery$sFragment";
  }

  /**
   *
   * Allow a component to request specific JS files to be included in the page header
   * @param array $pasJsFile
   */

  public function addJsFile($pvJsFile)
  {
    return $this->addRequiredJsFile($pvJsFile);
  }
  public function addRequiredJsFile($pvJsFile)
  {
    if(empty($pvJsFile))
      return false;

    if(is_array($pvJsFile))
    {
      foreach($pvJsFile as $sFileName)
        $this->casJsFile[$sFileName] = $sFileName;
    }
    else
    {
       $this->casJsFile[$pvJsFile] = $pvJsFile;
    }

    return true;
  }

  /**
  * Allow a component to request specific JS files to be included in the page header
  * @param array $pasJsFile
  */
  public function addCustomJs($pvJavascript)
  {
    if(empty($pvJavascript))
      return false;

    if(is_array($pvJavascript))
    {
      foreach($pvJavascript as $sJavascript)
        $this->casCustomJs[$sJavascript] = $sJavascript;
    }
    else
    {
       $this->casCustomJs[$pvJavascript] = $pvJavascript;
    }

    return true;
  }

  /**
  *
  * Allow a component to request specific CSS files to be included in the page header
  * @param array $pasJsFile
  */
  public function addCssFile($pvCssFile)
  {
    if(empty($pvCssFile))
      return false;

    if(is_array($pvCssFile))
    {
      foreach($pvCssFile as $sFileName)
        $this->casCssFile[$sFileName] = $sFileName;
    }
    else
    {
       $this->casCssFile[$pvCssFile] = $pvCssFile;
    }

    return true;
  }

  /**
  * Log when an user logs in the crm
  * @param object $poLogin dbresult of login information
  */
  private function _AccessLog($poLogin)
  {
    /*@var $poLogin CLoginEx */
    $sIP = $_SERVER['REMOTE_ADDR'];
    if(empty($sIP))
      $sIP = 'unKnown_ip';

    $bFirstConnection = false;

    if(empty($poLogin) || !$poLogin->isLogged())
      $nUserPk = 0;
    else
      $nUserPk = $poLogin->getUserPk();

    if(!isset($_SESSION['accessLogStart']))
    {
      $bFirstConnection = true;
      $_SESSION['accessLogStart'] = date('Y-m-d H:i:s');
      $_SESSION['accessLogTime'] = time();
      $_SESSION['accessLogUid'] = uniqid('sess_', true);
      $_SESSION['accessLogCount'] = 1;
      $_SESSION['accessLogLogged'] = (int)$poLogin->isLogged();
    }
    else
    {
      $_SESSION['accessLogCount']++;
    }

    $sMessage = '';

    //basic security check / protection
    if($_SESSION['accessLogCount'] > 20)
    {
        $fNbPagePerSec = ($_SESSION['accessLogCount'] / (time() - $_SESSION['accessLogTime']));
        if($fNbPagePerSec > 1)
        {
          sleep(3);
          exit('too many requests');
        }

        if($fNbPagePerSec > 0.5)
        {
          //echo "usleep(5000000*$fNbPagePerSec)";
          usleep(5000000*$fNbPagePerSec);
          $sMessage = 'Lot of requests from that user ['.$fNbPagePerSec.' pages per second] ';
        }
    }

    if($bFirstConnection)
    {
      $sQuery = 'INSERT INTO login_access_history (ip_address, loginfk, date_start, nb_page, session_uid) ';
      $sQuery.=  ' VALUES ("'.$sIP.'", "'.$nUserPk.'", "'.$_SESSION['accessLogStart'].'", "'.$_SESSION['accessLogCount'].'", "'.$_SESSION['accessLogUid'].'") ';
    }
    else
    {
      $sQuery = 'UPDATE login_access_history SET loginfk = "'.$nUserPk.'", nb_page = "'.$_SESSION['accessLogCount'].'" ';

      if(!empty($sMessage))
        $sQuery.= ' , history = CONCAT(history, " || ", "'.$sMessage.'") ';

      $sQuery.= ' WHERE session_uid = "'.$_SESSION['accessLogUid'].'" ';
      //echo $sQuery;
    }

    /*@var $oDb CDatabaseEx */
    $oDb = CDependency::getComponentByName('database');
    $oDb->dbConnect();
    $bResult =  $oDb->ExecuteQuery($sQuery);

    if(!$bResult)
      assert('false; // couldn\'t log activity ');

    return $bResult;
  }

  public function getPageRequiredJsFile()
  {
    return $this->casJsFile;
  }
  public function getPageRequiredCssFile()
  {
    return $this->casCssFile;
  }
  public function getPageCustomJs()
  {
    return $this->casCustomJs;
  }

  public function getAjaxExtraContent($pasAjaxData)
  {
    if(!empty($this->casCustomJs))
    {
      if(isset($pasAjaxData['js']))
        $pasAjaxData['js'] = "\n ".implode("\n ", $this->casCustomJs);
      else
        $pasAjaxData['js'] = implode("\n ", $this->casCustomJs);
    }

    if(!empty($this->casJsFile))
    {
      if(isset($pasAjaxData['jsfile']))
        $pasAjaxData['jsfile'] = array_merge((array)$pasAjaxData['jsfile'], $this->casJsFile);
      else
        $pasAjaxData['jsfile'] = $this->casJsFile;
    }

    if(!empty($this->casCssFile))
    {
      if(isset($pasAjaxData['cssfile']))
        $pasAjaxData['cssfile'] = array_merge((array)$pasAjaxData['cssfile'], $this->casCssFile);
      else
        $pasAjaxData['cssfile'] = $this->casCssFile;
    }

    return $pasAjaxData;
  }

  public function setPageKeywords($psKeywords, $pbEraseDefault = false)
  {
    if(!assert('is_string($psKeywords)'))
      return false;

    if($pbEraseDefault)
      $this->csPageKeywords = $psKeywords;
    else
      $this->csPageKeywords.= ' '.$psKeywords;

    return true;
  }
  public function getPageKeywords()
  {
    return $this->csPageKeywords;
  }

  public function setPageDescription($psDescription, $pbEraseDefault = false)
  {
    if(!assert('is_string($psDescription)'))
      return false;

    if($pbEraseDefault)
      $this->csPageDesc = $psDescription;
    else
      $this->csPageDesc.= ' '.$psDescription;

    return true;
  }
  public function getPageDescription()
  {
    return $this->csPageDesc;
  }

  public function setPageTitle($psTitle, $pbEraseDefault = false)
  {
    if(!assert('is_string($psTitle)'))
      return false;

    if($pbEraseDefault)
      $this->csPageTitle = $psTitle;
    else
      $this->csPageTitle.= ' '.$psTitle;

    return true;
  }

  public function getPageTitle()
  {
    return $this->csPageTitle;
  }



  private function _getCustomUserFeature($bIsLogged = false)
  {
    $oLogin = CDependency::getComponentByName('login');

    if($bIsLogged && $oLogin->getUserEmail() == 'nnakazawa@bulbouscell.com')
      $this->addCssFile('/common/style/custom_style.css');

    if(isDevelopment())
        $this->addCssFile ('/common/style/dev.css');

    return true;
  }

  public function setLanguage($psLanguage)
  {
    require_once('language/language.inc.php5');
    if(isset($gasLang[$psLanguage]))
      $this->casText = $gasLang[$psLanguage];
    else
      $this->casText = $gasLang[CONST_DEFAULT_LANGUAGE];
  }

  public function getUrlDetail()
  {
    return $this->casUrlDetail;
  }
}
