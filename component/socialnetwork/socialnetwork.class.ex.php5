<?php
require_once('component/socialnetwork/socialnetwork.class.php5');

class CSocialnetworkEx extends CSocialnetwork
{
  private $casConfig = array();
  private $coFacebook = null;
  private $coLinkedIn = null;

  function __contruct()
  {
    //TODO: Display and edit js to function based on this uniqId
    parent::__construct();
  }





  public function getHtml()
  {
    $this->_processUrl();
    $sPlatform = getValue('platform');

    switch($sPlatform)
    {
      case 'linkedin':
         return $this->_linkedInLogin();
         break;

      case 'facebook':
      default:
        return $this->_facebookLogin();
    }
  }






  private function _initFbConnector()
  {
    require_once('.'.$this->getResourcePath().'facebook/facebook.php');

    $this->casConfig['appId'] = '506245946082477';
    $this->casConfig['secret'] = '739ffccf9951c29fec2273186b6fd7dc';
    $this->casConfig['return_url'] = 'https://jobs.slate.co.jp/index.php5?uid=654-321&ppa=ppaa&ppt=shjob';
    $this->casConfig['home_url'] = CONST_CRM_DOMAIN.'/index.php5?uid=459-456&ppa=ppava';
    $this->casConfig['fileUpload'] = false; // optional
    $this->casConfig['cookie'] = true; // optional
    $this->casConfig['permission'] = 'publish_stream,manage_pages';

    $this->coFacebook = new Facebook($this->casConfig);
    if(!$this->coFacebook)
      return false;

    if(isset($_SESSION['fbToken']) && !empty($_SESSION['fbToken']))
    {
      $this->coFacebook->setAccessToken($_SESSION['fbToken']);
    }

    return true;
  }


  private function _fbConnect()
  {
    if(!$this->coFacebook)
      $this->_initFbConnector();

    if(!$this->coFacebook)
      return false;

    //we're supposed to be loggedin, let's check we've got everything
    if(isset($_SESSION['fbLogged']) && !empty($_SESSION['fbLogged']) && isset($_SESSION['fbToken']) && !empty($_SESSION['fbToken']))
    {
      $sToken = $this->coFacebook->getAccessToken();
      if(!empty($sToken))
        return true;
    }

    //check if we're just back from user login
    $sFbState = getValue('state');
    $sFbCode = getValue('code');
    $sPpk = '&ppk='.(int)getValue('ppk', 0);

    if(!empty($sFbState) && !empty($sFbCode))
    {
      //----------------------------------------
      //new connection process: get the fb code
      if($_SESSION['secure_state'] !== $sFbState)
        exit('Warning, something is weird with FB auth. ['.$sFbState.' /'.$_SESSION['secure_state'].']');


      //----------------------------------------
      //exchnage code against auth toke
      $sTokenUrl = "https://graph.facebook.com/oauth/access_token?"
          . "client_id=" . urlencode($this->casConfig['appId']) . "&redirect_uri=" . urlencode($this->casConfig['home_url'].$sPpk)
          . "&client_secret=" . urlencode($this->casConfig['secret']) . "&code=" . urlencode($sFbCode);

      $sResponse = file_get_contents($sTokenUrl);
      $asParams = null;
      parse_str($sResponse, $asParams);

      ////var_dump($asParams);
      //array(2) { ["access_token"]=> string(110) "AAAHMbZAHJBK0BAChUoi6kYnNf0i6eOeNTI1WQwu6ypTl59BsQzhy8MsaUNK5rcdcdLEnzV1w3JUuuhmxlOlZA8JVZCaMUclqLJtSpd55QZDZD" ["expires"]=> string(7) "5168861" }

      if(isset($asParams['access_token']) && !empty($asParams['access_token']))
      {
        //test everything is working
        $sGraphUrl = "https://graph.facebook.com/me?access_token=" . $asParams['access_token'];
        $oUser = json_decode(file_get_contents($sGraphUrl));
        if($oUser)
        {
          //echo("Logged in facebook as " . $oUser->name);
          $this->csFacebookToken = $asParams['access_token'];
          $this->coFacebook->setAccessToken($asParams['access_token']);
          $_SESSION['fbLogged'] = true;
          $_SESSION['fbToken'] = $asParams['access_token'];

          //echo 'use extended access token: '.(int)$this->coFacebook->setExtendedAccessToken();
          return true;
        }
      }

      return false;
    }


    //not currently logging in: check is session is ok
    if($this->coFacebook->getUser())
      return true;

    return false;
  }




  private function _facebookLogin()
  {
    //1. check we've received data from facebook
    //2. request an auth token from it
    $this->_initFbConnector();
    $this->_fbConnect();


    if(!isset($_SESSION['fbLogged']) || empty($_SESSION['fbLogged']))
      return 'An error occured, not logged in facebook';

    $nPk = (int)getValue('ppk', 0);
    $oPage = CDependency::getComponentByName('page');
    $sUrl = $oPage->getUrl('jobboard_user', CONST_ACTION_ADD, CONST_TA_TYPE_SHARE_JOB, $nPk);

    $sHtml = 'Logged in facebook, you\'re going to be redirected in a few seconds.';
    $sHtml.= '<script>setLoadingScreen(\'body\', true, true); setTimeout("document.location.href = \''.$sUrl.'\'; ", 3000)</script>';
    return $sHtml;
  }



  public function isLoggedInFacebook()
  {
    if(!$this->coFacebook)
      $this->_initFbConnector();

    if(!$this->coFacebook)
      return false;

    if(isset($_SESSION['fbLogged']) && !empty($_SESSION['fbLogged']))
      return true;

    return false;
  }

  public function getFacebookLoginLink($pnPk = 0)
  {
    if(!assert('is_integer($pnPk)'))
      return '';

    if(!$this->coFacebook)
      $this->_initFbConnector();

    $sLoginUrl = $this->coFacebook->getLoginUrl(array(
          'canvas' => 1,
          'fbconnect' => 0,
          'scope' => 'read_stream,publish_stream,offline_access',
          'redirect_uri' => $this->casConfig['home_url'].'&ppk='.$pnPk
          ));

    $_SESSION['secure_state'] = uniqid('secure_state_');

    $sLoginUrl = str_ireplace('ppa%3Dppasa', 'ppa%3Dppaa', $sLoginUrl);
    $sLoginUrl = str_ireplace('pg%3Dajx', '', $sLoginUrl);
    $sLoginUrl.= '&state='.$_SESSION['secure_state'];
    $sLoginUrl.= '&platform=facebook';
    $sLoginUrl.= '&ppk='.$pnPk;

    return $sLoginUrl;
  }


  public function addFacebookWallPost()
  {
    if(!$this->_fbConnect())
      return false;

    if(empty($this->coFacebook))
      return false;

    //dump($this->coFacebook);

    $sToken = $this->coFacebook->getAccessToken();
    if(empty($sToken))
      return false;

    $sPicture = getValue('fbPicture');
    $sMessage = getValue('fbContent');
    $sLink = getValue('fbLink');
    $sLinkLabel = getValue('fbLinkLabel');
    $sCaption = getValue('fbCaption');
    $asPostParams = array(
        'access_token' => $sToken,
        'message' => $sMessage,
        'picture' => $sPicture,
        'link' => $sLink,
        'name' => $sLinkLabel,
        'caption' => $sCaption);

    //dump($asPostParams);

    $asPostResponse = $this->coFacebook->api("/me/feed", "post", $asPostParams);
    //dump($oPost);

    if(isset($asPostResponse['id']) && !empty($asPostResponse['id']))
      return true;

    return false;
  }








// ======================================================================================================
// ======================================================================================================
// ======================================================================================================


  private function _initLiConnector()
  {
    require_once('component/socialnetwork/resources/linkedin/linkedin_3.2.0.class.php');

    $this->casConfig['appKey'] = '7ziqysb18914';
    $this->casConfig['appSecret'] = 'HEFgHFKZuULNtqVu';
    $this->casConfig['callbackUrl'] = 'https://jobs.slate.co.jp/index.php5?uid=459-456&ppa=ppva&ppt=shjob&platform=linkedin';

    $this->coLinkedIn = new LinkedIn($this->casConfig);
    if(!$this->coLinkedIn)
      return false;

    $nTime = (int)time();

    if(isset($_SESSION['liTokenTime']) && $_SESSION['liTokenTime'] > ($nTime-600) && $_SESSION['liTokenTime'] < ($nTime-420))
    {
      echo 'refresh linkedin token<br /><br />';

      $asRequestToken = $this->coLinkedIn->retrieveTokenRequest();
      if(!is_array($asRequestToken) && count($asRequestToken) < 2)
        return false;

      $asAccessToken = $this->coLinkedIn->getTokenAccess($asRequestToken);
      if(!is_array($asRequestToken) && count($asRequestToken) < 2)
        return false;

      $asAccessToken = $this->coLinkedIn->getToken($asRequestToken);

      $_SESSION['liToken'] = $asAccessToken['oauth_token'];
      $_SESSION['liToken_secret'] = $asAccessToken['oauth_token_secret'];
      $_SESSION['liTokenTime'] = time();
      $_SESSION['liLogged'] = true;
    }

    //if logged in, we use the access keys stored in session
    if(isset($_SESSION['liTokenTime']) && $_SESSION['liTokenTime'] > ($nTime-600) && isset($_SESSION['liToken']) && !empty($_SESSION['liToken']))
    {
      dump('Init: settoken');
      dump(array('oauth_token' => $_SESSION['liToken'], 'oauth_token_secret' => $_SESSION['liToken_secret']));
      $asAccessToken = $this->coLinkedIn->setTokenAccess(array('oauth_token' => $_SESSION['liToken'], 'oauth_token_secret' => $_SESSION['liToken_secret']));
      $_SESSION['liLogged'] = true;

      dump($this->coLinkedIn);

      return true;
    }

    $_SESSION['liLogged'] = false;
    $_SESSION['liTokenTime'] = $_SESSION['liToken_secret'] = null;
    return true;
  }



  public function isLoggedInLinkedin()
  {
    if(!$this->coLinkedIn)
      $this->_initLiConnector();

    if(!$this->coLinkedIn)
      return false;

    if(isset($_SESSION['liLogged']) && !empty($_SESSION['liLogged']))
      return true;

    return false;
  }


  public function getLinkedinLoginLink($pnPk = 0)
  {
    if(!assert('is_integer($pnPk)'))
      return '';

    if(!$this->coLinkedIn)
      $this->_initLiConnector();

    $asRequestToken = $this->coLinkedIn->retrieveTokenRequest();
    if(!is_array($asRequestToken) || count($asRequestToken) < 2 || !isset($asRequestToken['linkedin']['oauth_token']))
      return 'javascript:alert(\'Sorry, an error occured: no valid token.\');';

  	$_SESSION['li_secure'] = $asRequestToken['linkedin']['oauth_token'];
  	$_SESSION['li_secure_secret'] = $asRequestToken['linkedin']['oauth_token_secret'];
    $_SESSION['liTokenTime'] = 0;
    $this->casConfig['return_url'].= '&ppk='.$pnPk;

    //$sExtraParam = '&scope=read_stream,publish_stream,offline_access&ppk='.$pnPk.'&redirect_uri='.urlencode($this->casConfig['return_url']);
    $sExtraParam = '&scope=r_basicprofile+r_fullprofile+r_network+rw_nus+w_messages+r_contactinfo+r_emailaddress&ppk='.$pnPk.'&redirect_uri='.urlencode($this->casConfig['return_url']);
    return 'https://api.linkedin.com/uas/oauth/authorize?oauth_token='.$asRequestToken['linkedin']['oauth_token'].$sExtraParam;
  }



  private function _linkedInLogin()
  {
    //1. check we've received data from facebook
    //2. request an auth token from it
    $this->_initLiConnector();
    $this->_liConnect();


    if(!isset($_SESSION['liLogged']) || empty($_SESSION['liLogged']))
      return 'An error occured, not logged in linkedIn';

    $nPk = (int)getValue('ppk', 0);
    $oPage = CDependency::getComponentByName('page');

    if(empty($nPk))
      $sUrl = $oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_SHARE_JOB);
    else
      $sUrl = $oPage->getUrl('jobboard_user', CONST_ACTION_ADD, CONST_TA_TYPE_SHARE_JOB, $nPk);

    $sHtml = 'Logged in linkedIn, you\'re going to be redirected in a few seconds.';
    $sHtml.= '<script>setLoadingScreen(\'body\', true, true); setTimeout("document.location.href = \''.$sUrl.'\'; ", 3000)</script>';

    return $sHtml;
  }


  private function _liConnect()
  {
    if(!$this->coLinkedIn)
      $this->_initLiConnector();

    if(!$this->coLinkedIn)
      return false;

    if(isset($_SESSION['liLogged']) && !empty($_SESSION['liLogged']))
      return true;

    //check if we're just back from user login on linkedin
    //&oauth_token=297e6b1b-4299-4faf-ae15-c865673bf586&oauth_verifier=76565
    $sLiToken = getValue('oauth_token');
    $sLiVerify = getValue('oauth_verifier');
    if(!empty($sLiToken) && !empty($sLiVerify))
    {
      //----------------------------------------
      //new connection process: get the fb code
      if($_SESSION['li_secure'] !== $sLiToken)
      {
        echo ('Warning, something is weird with LinkedIn auth. ['.$sLiToken.' /'.$_SESSION['li_secure'].']');
        return false;
      }

      //request access token from connection token
      $this->coLinkedIn->setToken(array($_SESSION['li_secure'], $_SESSION['li_secure_secret']));
      $asAccessToken = $this->coLinkedIn->getTokenAccess();

      if(empty($asAccessToken) || count($asAccessToken) < 2)
        return false;

      dump('Connect: save token');
      dump($asAccessToken);

      $_SESSION['liLogged'] = true;
      $_SESSION['liVerify'] = $sLiVerify;

      $_SESSION['liToken'] = $asAccessToken[0];
      $_SESSION['liToken_secret'] = $asAccessToken[1];

      $_SESSION['liTokenTime'] = time();
      return true;
    }

    return false;
  }




  public function addLinkedinPost()
  {
    //yum install php-pecl-oauth php-cli
    $sError = '<br />add linkedInPost';
    echo 'start  ';
    $this->_initLiConnector();
    echo 'load';
    $this->_liConnect();
    echo '  connect  ';

    if(empty($this->coLinkedIn))
      return array('error' => 'no linkedIn connector available.');

    echo '  ready to share  ';

    $sComment = ' comment ';
    $sTitle = ' title ';
    $sLink = 'jobs.slate.co.jp';
    $sPicture = $sDescription = 'aaaa';


    $asData['comment'] = $sComment;
    $asData['title'] = $sTitle;
    $asData['description'] = $sDescription;
    $asData['submitted-url'] = $sLink;
    $asData['submitted-image-url'] = $sPicture;

    $data = $this->coLinkedIn->share('new', $asData, false, false);
    //$data = $this->coLinkedIn->profile('~:(first-name,last-name,site-standard-profile-request)');
    //$data = $this->coLinkedIn->updates();
    dump($data);
    dump($this->coLinkedIn);

    if(isset($response_info) && !empty($response_info))
      return array('notice' => 'Job posted on linkedIn');

    return array('error' => '<div style="width: 700px; height: 500px;overflow: auto;">error  from linkedIn: '.$sError.'</div>');
  }

}
?>