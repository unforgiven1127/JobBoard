<?php

require_once('component/login/login.class.php5');

class CLoginEx extends CLogin
{
  private $cbIsLogged;
  private $cbIsAdmin;
  private $casUserData;
  private $casRight;

  public function __construct()
  {
    if(isset($_SESSION['userData']) && !empty($_SESSION['userData']))
    {
      if(!isset($_SESSION['userData']['loginTime']))
      {
        $this->cbIsLogged = false;
      }
      else
        $this->cbIsLogged = true;

      $this->casUserData = $_SESSION['userData'];
      $this->casRight = array();

      if(!isset($_SESSION['userData']['admin']) || empty($_SESSION['userData']['admin']))
        $this->cbIsAdmin = false;
      else
        $this->cbIsAdmin = true;

    }
    else
    {
      if(isset($_COOKIE['login_userdata']) && !empty($_COOKIE['login_userdata']))
      {
        $asCookieUserData = unserialize(urldecode($_COOKIE['login_userdata']));
        if(isset($asCookieUserData['pk']) && !empty($asCookieUserData['pk']))
        {
          $this->cbIsLogged = $this->_getCookieIdentification($asCookieUserData);
          return $this->cbIsLogged;
        }
      }

      $this->cbIsLogged = false;
      $this->casUserData = array();
      $this->casRight = array();
      $this->cbIsAdmin = false;
    }

    return true;
  }

  //====================================================================
  //  accessors
  //====================================================================

  public function isLogged()
  {
    return $this->cbIsLogged;
  }

  public function isAdmin()
  {
    if($this->cbIsAdmin || (isset($this->casUserData['pk']) && $this->casUserData['pk'] === 1))
      return true;

    return false;
  }

  public function getUserName()
  {
    if(!assert('!empty($this->casUserData)'))
      return '';

    return $this->casUserData['firstname'].' '.$this->casUserData['lastname'];
  }

  public function getUserNameFromData($asUserData, $pbFriendly = false, $pbFullName = false, $pbDisplayPicture = false)
  {
    $oDisplay = CDependency::getComponentByName('display');

    if(!assert('is_array($asUserData) && !empty($asUserData) && is_bool($pbFriendly)'))
      return '';

    if($pbFriendly)
    {
      if(!empty($asUserData['pseudo']))
        return $asUserData['pseudo'];
      else
        return $asUserData['firstname'];
    }
    else
    {
      $sFullName = '';

      if($pbDisplayPicture)
      {
        if($asUserData['gender'] == 0)
          $sFullName.= $oDisplay->getPicture($this->getResourcePath().'/pictures/girl.png');
        else
          $sFullName.= $oDisplay->getPicture($this->getResourcePath().'/pictures/boy.png');
      }

      if($pbFullName)
        $sFullName.= $asUserData['courtesy'];

      $sFullName.= $asUserData['firstname'].' '.$asUserData['lastname'];
      return $sFullName;
    }
  }


  public function getUserAccountName($psUserLastname = "",$psUserFirstname = "", $pbFriendly = false, $pbFullName = false, $pbDisplayPicture = false)
  {
    if(!assert(' is_bool($pbFriendly)'))
      return '';

    if($pbFriendly)
    {
      if(!empty($asUserData['pseudo']))
        return $asUserData['pseudo'];
      else
        return $psUserFirstname;
    }
    else
    {
      $sFullName = '';

      if($pbDisplayPicture)
      {
        if($asUserData['gender'] == 0)
           $sFullName.= $oDisplay->getPicture($this->getResourcePath().'/pictures/girl.png');
        else
           $sFullName.= $oDisplay->getPicture($this->getResourcePath().'/pictures/boy.png');
      }

      if($pbFullName)
        $sFullName.= $asUserData['courtesy'];

      $sFullName.= $psUserFirstname.' '.$psUserLastname;
      return $sFullName;
    }

  }

  public function getUserPk()
  {
    if(!isset($this->casUserData['pk']))
      return 0;

    return (int)$this->casUserData['pk'];
  }


  public function getUserEmail()
  {
    if(!assert('!empty($this->casUserData)'))
      return '';

    return $this->casUserData['email'];
  }

  //====================================================================
  //  public methods
  //====================================================================


  public function getHtml()
  {
    $this->_processUrl();

    if($this->isLogged())
    {
      switch($this->csType)
      {
        case CONST_LOGIN_TYPE_USER:
           switch($this->csAction)
           {
            case CONST_ACTION_LIST:
              return $this->_getUserList();
               break;

             case CONST_ACTION_EDIT:
              return $this->_getUserAccountDetail($this->cnPk);
               break;

             case CONST_ACTION_MANAGE:
              return $this->_getEditUserDetail($this->cnPk);
               break;
            }
           break;

        default:
          //access home page
          return $this->_getHomePage();
      }
    }

    switch($this->csType)
    {
      case 'restricted':
        return $this->getRestrictedPage();
        break;

      case CONST_LOGIN_TYPE_PASSWORD:
        switch($this->csAction)
        {
         case CONST_ACTION_RESET:
              return $this->_getLoginResetPasswordForm();
          break;
        }
        break;

      default:

       switch($this->csAction)
       {
          default:
            return $this->_getLoginForm();
          break;
        }
        break;
    }
  }

 public function getAjax()
  {
    $this->_processUrl();

    switch($this->csType)
    {
      case CONST_LOGIN_TYPE_USER:
        switch($this->csAction)
        {
          case CONST_ACTION_SAVEADD:
          case CONST_ACTION_SAVEEDIT:

            //TODO
            if((bool)getValue('credentials', false))
              return json_encode($this->_getUserCredSave($this->cnPk));
            else if((bool)getValue('display', false))
              return json_encode($this->_getUserDisplaySave($this->cnPk));
            else
              return json_encode($this->_getUserInfoSave($this->cnPk));

            break;

           case CONST_ACTION_DELETE:
             return json_encode($this->_removeUser($this->cnPk));
              break;

            case CONST_ACTION_SEARCH:
              /* custom json encoding in function for token input selector */
              return $this->_getSelectorUser();
                break;

          }
          break;

      case CONST_LOGIN_TYPE_EXTERNAL_USER:
        switch($this->csAction)
        {
          case CONST_ACTION_VALIDATE:
            return json_encode($this->_checkUserLogged());
              break;
        }
        break;


      case CONST_LOGIN_TYPE_PASSWORD:
        switch($this->csAction)
        {
          case CONST_ACTION_SEND:
            return json_encode($this->_getSendPassword());
              break;

          case CONST_ACTION_SAVEEDIT:
            return json_encode($this->_setNewPassword());
              break;

          case CONST_ACTION_VALIDATE:
            return json_encode($this->_getIdentification(true));
              break;

        }
        break;

      default:

      switch($this->csAction)
      {
        case CONST_ACTION_LOGOUT:
          return json_encode($this->_getLogout(true));
            break;
      }
    }
  }


  public function getCronJob()
  {
    echo 'Login cron  <br />';

    $nHour = date('H');

    if(($nHour > 5 && $nHour < 6) || getValue('forcecron') == 'login')
    {
       $this->_cleanRecentActivity();
       $this->_checkBirthday();
       $this->_getCronEmail();
    }

    return '';
  }


  private function _getCronEmail()
  {
    $oAddress = CDependency::getComponentUidByName('addressbook');
    if($oAddress)
    {
     $oEvent = CDependency::getComponentByName('event');
     $oAddressBook = CDependency::getComponentByName('addressbook');

     $day = date('l');
     $time = (int)date('H');

     if((($day=='Monday' || $day=='Thursday' ) && $time == 6) || getValue('forcecron') == 'login'|| getValue('custom_uid') == '579-704')
     {
      $oDB = CDependency::getComponentByName('database');

      $sQuery = 'SELECT * FROM  `login_activity` WHERE status = 0 AND followerfk!=0 AND notifierfk!=0 AND loginfk!=notifierfk ';
      $sQuery.= ' AND sentemail = 0 AND log_date > '.date('Y-m-d', mktime(0, 0 , 0, date('m'), (date('d')-5), date('Y')));

      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();
      if(!$bRead)
        return false;

      $asToNotify = array();
      $asDocuments = array();

      while($bRead)
      {
        $asEmailData = $oResult->getData();
        $asRecipients = array($asEmailData['notifierfk']);

        foreach($asRecipients as $nLoginfk)
        {
          $asToNotify[$nLoginfk][] = $asEmailData['login_activitypk'];
        }

        $asEventDetail = $oEvent->getEventDataByPk((int)$asEmailData['cp_pk']);

        $sEventURL = $asEmailData['log_link'].'#ct_tab_eventId';
        $sLink = '<a href="'.$sEventURL.'"> Access the event in BCM </a>';

        $asUserData = $this->getUserDataByPk((int)$asEventDetail['created_by']);
        $sTargetName = $oAddressBook->getItemName('ct',(int)$asEmailData['followerfk']);
        $sContent = ' <h4>Activity Detail</h4> <br/>';
        $sContent.= ' <strong>Target : </strong>'.$sTargetName .'<br/>' ;
        $sContent.= ' <strong>Created on  :</strong> '.$asEventDetail['date_create'].' by '.$this->getUserNameFromData($asUserData).'<br/>';
        $sContent.= ' <strong>Type :</strong> '.ucwords($asEventDetail['type']).'<br/>';
        $sContent.= ' <strong>Title : </strong>'.$asEventDetail['title'].'<br/>';
        $sContent.= ' <strong>Description :</strong> <div style="border: 1px solid #BBBBBB;border-radius: 5px 5px 5px 5px;padding: 3px; ">'.$asEventDetail['content'].'</div><br />';
        $sContent.= ''.$sLink.'<br />';
        $asDocuments[$asEmailData['login_activitypk']] = $sContent;

        $bRead = $oResult->ReadNext();
       }

      $oMail = CDependency::getComponentByName('mail');
      if(!empty($oMail))
      {
      $nSent = 0;

      foreach($asToNotify as $nEmailfk => $anEmailToNotify)
      {
       if(!empty($nEmailfk))
        {
          $asReceiverEmail =  $this->getUserDataByPk((int)$nEmailfk);

          $sContent = '<h3> Hello '.$asReceiverEmail['firstname'].' '.$asReceiverEmail['lastname'].',</h3><br />';

          if(count($anEmailToNotify) > 1)
            $sContent.= count($anEmailToNotify)." events have been created in your connection in BCM.<br />";
          else
            $sContent.= "A event has been created in your connection in BCM. Click the link below.".$sLink."<br/>";

          foreach($anEmailToNotify as $nDocumentPk)
          {
            $sQuery = 'UPDATE login_activity SET sentemail = 1 where login_activitypk ='.$nDocumentPk;
            $oResult = $oDB->ExecuteQuery($sQuery);
            $sContent.= '<br />'.$asDocuments[$nDocumentPk].'<br />';
           }
           $sContent.= "Enjoy BCM.";

          $oMail->sendRawEmail('info@bcm.com',$asReceiverEmail['email'], 'BCM - Event has been created on your connection.', $sContent);
          $nSent++;
          }
        }
        echo $nSent.' email(s) have been sent.<br />';
       }
      }
      return true;
   }
 }

  public function getRestrictedPage($pbIsLogged = false)
  {
    $oHTML = CDependency::getComponentByName('display');

    if(!$pbIsLogged)
    {
      $sHTML = $oHTML->getBlocStart('', array('style'=>'width:400px; margin:50px auto;'));
      $sHTML.= $oHTML->getText($this->casText['LOGIN_ACCESS_RESTRICTED']);
      $sHTML.= $oHTML->getCarriageReturn(2);
      $sHTML.= $this->_getLoginForm();
    }
    else
    {
      $sHTML = $oHTML->getBlocStart('', array('class' => 'restrictedAccessBloc',  'style'=>'width:600px; margin:50px auto;'));
      $sHTML.= $oHTML->getBlocMessage($this->casText['USER_ACCESS_RESTRICTED']);
    }

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Display the user account details
   *
   * @param integer $pnLoginPk
   * @return string html
  */
  private function _getUserAccountDetail($pnLoginPk)
  {

  /*@var $oHTML CDisplayEx */
   $oHTML = CDependency::getComponentByName('display');

   $sHTML= $oHTML->getBlocStart();
   $sHTML = $oHTML->getTitleLine($this->casText['LOGIN_MY_ACCOUNT'], $this->getResourcePath().'/pictures/user.png');
   $sHTML.= $oHTML->getCarriageReturn();
   $sHTML.= $this->_getUserForm($pnLoginPk);
   $sHTML.= $oHTML->getBlocEnd();

    return  $sHTML;
  }

  /**
   * Check the credentials of the user
   * @param $pnLoginPk integer
   * @param $psEmail string
   * @param $psPassword string
   * @param $pnPort string
   * @param $psIMAP string
   * @return boolean with true or false
   */

  private function _checkCredentials($pnLoginPk = 0,$psEmail,$psPassword,$pnPort,$psIMAP)
  {
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');

    ini_set('error_reporting', E_ALL & ~E_NOTICE);
    //Check if the credentials given are correct.
    if(!empty($pnLoginPk))
    {
      $asCredentials =   $this->getUserDataByPk($pnLoginPk);
      $sUserLogin =  $asCredentials['webmail'];
      $sUserPwd  =  $asCredentials['webpassword'];

      $sHost ='{'.BCMAIL_HOST.':'.BCMAIL_PORT.'/imap/ssl/novalidate-cert}SENT';
    }
    else
    {
      $sUserLogin = $psEmail;
      $sUserPwd  =  $psPassword;
      $sHost ='{'.$psIMAP.'/imap/ssl/notls/norsh/novalidate-cert}Sent';
    }

     if(empty($sUserLogin))
      return array('error' => __LINE__.' - '.$this->casText['LOGIN_EMAIL_INVALID']);

     if(empty($sUserPwd))
       return array('checked' => 0, 'error' => $this->casText['LOGIN_PASSWD_REQD']);

     $nTimeout = imap_timeout(IMAP_OPENTIMEOUT, 5);
     if($sConn = @imap_open($sHost, $sUserLogin, $sUserPwd, OP_READONLY))
     {
       imap_close($sConn);
       return array('checked' => 1);
     }
     else
       return array('checked' => 0, 'error' => $this->casText['LOGIN_COULDNOT_CONNECT'], 'detail' => $sHost.' // '.$nTimeout.' // '.  imap_last_error());
   }

   /**
    * Save the User credentials
    * @param $pnLoginPk integer
    * @return array
    */

  private function _getUserCredSave($pnLoginPk)
  {
    if(!assert('is_integer($pnLoginPk) && !empty($pnLoginPk)'))
      return array('error' => $this->casText['LOGIN_NO_USER']);

    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');

    $sMail = getValue('login');
    $sPassword = getValue('password');
    $nPort = (int)getValue('port', 0);
    $sIMAP = getValue('imap');
    $sAlias = getValue('alias');
    $sSignature= getValue('signature');

    if(empty($sMail) && !filter_var($sMail, FILTER_VALIDATE_EMAIL))
      return array('error' => $oHTML->getBlocMessage($this->casText['LOGIN_NO_EMAIL']));

    if(empty($sPassword) && strlen($sPassword) > 4)
      return array('error' => $oHTML->getBlocMessage($this->casText['LOGIN_NO_PASSWD']));

    if(empty($nPort))
      return array('error' => $oHTML->getBlocMessage($this->casText['LOGIN_NO_PORT']));

    if(empty($sIMAP))
      return array('error' => $oHTML->getBlocMessage($this->casText['LOGIN_NO_IMAP']));

    if(empty($sAlias))
      return array('error' => $oHTML->getBlocMessage($this->casText['LOGIN_NO_ALIAS']));

    if(empty($sSignature))
      return array('error' => $oHTML->getBlocMessage($this->casText['LOGIN_NO_SIGN']));

    $asCredentialCheck = $this->_checkCredentials(0,$sMail, $sPassword, $nPort, $sIMAP);
    if($asCredentialCheck['checked'] == 0)
      return array('error' => __LINE__.' - '.$asCredentialCheck['error'], 'detail' => $asCredentialCheck['detail'] );

    /*@var $oDB CDatabaseEx */
    $oDB = CDependency::getComponentByName('database');
    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');

    $sQuery = 'UPDATE login SET ';
    $sQuery.= ' webmail = '.$oDB->dbEscapeString($sMail).', ';
    $sQuery.= ' webpassword = '.$oDB->dbEscapeString($sPassword).', ';
    $sQuery.= ' mailport = '.$oDB->dbEscapeString($nPort).', ';
    $sQuery.= ' Imap = '.$oDB->dbEscapeString($sIMAP).', ';
    $sQuery.= ' aliasName = '.$oDB->dbEscapeString($sAlias).',';
    $sQuery.= ' signature = '.$oDB->dbEscapeString($sSignature).' ';
    $sQuery.= ' WHERE loginPk = '.$pnLoginPk;

    $oDB->ExecuteQuery($sQuery);

    $sURL = $oPage->getUrl('login', '', '', $pnLoginPk);
    return array('notice' => $this->casText['LOGIN_CREDENTIAL_SAVE'], 'url' => $sURL);
  }

  /**
   * Save the User Display tabs information
   * @param $pnLoginPk integer
   * @return array
   */

  private function _getUserDisplaySave($pnLoginPk)
  {
    if(!assert('is_integer($pnLoginPk) && !empty($pnLoginPk)'))
      return array('error' => 'No User found.');

    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');

    if(count(getValue('company'))!=4)
      return array('error'=>$this->casText['LOGIN_SELECT_COMPANY_TABS']);

    if(count(getValue('connection'))!=5)
      return array('error'=>$this->casText['LOGIN_SELECT_CONNECTION_TABS']);

    $asCompanyData = array(CONST_TAB_CP_DETAIL=>'777-249',CONST_TAB_CP_EMPLOYEES=>'777-249',CONST_TAB_CP_DOCUMENT=>'777-249',CONST_TAB_CP_EVENT=>'007-770');
    $asContactData = array(CONST_TAB_CT_DETAIL=>'777-249',CONST_TAB_CT_COWORKERS=>'777-249',CONST_TAB_CT_DOCUMENT=>'777-249',CONST_TAB_CT_EVENT=>'007-770',CONST_TAB_CT_PROFILE=>'777-249');

    $sCompany = getValue('company');
    $sConnection = getValue('connection');
    $asCompanyValue = array();
    $asConnectionValue = array();

    foreach($sCompany as $sCompanyTab)
    {
      $asCompanyValue[$sCompanyTab] = $asCompanyData[$sCompanyTab];
    }

    foreach($sConnection as $sConnectionTab)
    {
      $asConnectionValue[$sConnectionTab] = $asContactData[$sConnectionTab];
     }

    $sCompanyTabs = serialize($asCompanyValue);
    $sConnectionTabs = serialize($asConnectionValue);

    $nList = getValue('list');
    $sDate = date('Y-m-d H:i:s');

    if(!empty($sCompanyTabs))
    {
     $sQuery = 'SELECT * FROM login_preference WHERE loginfk='.$pnLoginPk.' AND user_preferencefk=1';
     $oResult=$oDB->ExecuteQuery($sQuery);
     $bRead = $oResult->readFirst();

     if(!empty($bRead))
      {
        $sQuery = 'UPDATE login_preference set value = '.$oDB->dbEscapeString($sCompanyTabs).' where loginfk='.$pnLoginPk.' AND user_preferencefk=1';
        $oDB->ExecuteQuery($sQuery);
      }
      else
      {
        $sQuery = 'INSERT INTO login_preference(`loginfk`,`user_preferencefk`,`value`,`date_create`)';
        $sQuery.= ' VALUES('.$oDB->dbEscapeString($pnLoginPk).',1,'.$oDB->dbEscapeString($sCompanyTabs).','.$oDB->dbEscapeString($sDate).')';
        $oDB->ExecuteQuery($sQuery);
      }
     }

     if(!empty($sConnectionTabs))
     {
      $sQuery = 'SELECT * FROM login_preference WHERE loginfk='.$pnLoginPk.' AND user_preferencefk=2';
      $oResult=$oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();
      if(!empty($bRead))
      {
       $sQuery = 'UPDATE login_preference set value = '.$oDB->dbEscapeString($sConnectionTabs).' where loginfk='.$pnLoginPk.' AND user_preferencefk=2';
       $oDB->ExecuteQuery($sQuery);
      }
      else
      {
       $sQuery = 'INSERT INTO login_preference(`loginfk`,`user_preferencefk`,`value`,`date_create`)';
       $sQuery.= ' VALUES('.$oDB->dbEscapeString($pnLoginPk).',2,'.$oDB->dbEscapeString($sConnectionTabs).','.$oDB->dbEscapeString($sDate).')';
       $oDB->ExecuteQuery($sQuery);
       }
     }
    if(!empty($nList))
     {
      $sQuery = 'SELECT * FROM login_preference WHERE loginfk='.$pnLoginPk.' AND user_preferencefk=3';
      $oResult=$oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();
      if(!empty($bRead))
      {
       $sQuery = 'UPDATE login_preference set value = '.$oDB->dbEscapeString($nList).' where loginfk='.$pnLoginPk.' AND user_preferencefk=3';
       $oDB->ExecuteQuery($sQuery);
      }
      else
      {
       $sQuery = 'INSERT INTO login_preference(`loginfk`,`user_preferencefk`,`value`,`date_create`)';
       $sQuery.= ' VALUES('.$oDB->dbEscapeString($pnLoginPk).',3,'.$oDB->dbEscapeString($nList).','.$oDB->dbEscapeString($sDate).')';
       $oDB->ExecuteQuery($sQuery);
      }
    }

    $sURL = $oPage->getUrl('login', '', '', $pnLoginPk);
    return array('notice' => $this->casText['LOGIN_DISPLAY_SAVE'], 'url' => $sURL);
  }

  /**
   * Save the User Information
   * @param $pnLoginPk integer
   * @return array
   */

  private function _getUserInfoSave($pnLoginPk)
  {
    if(!assert('is_integer($pnLoginPk) && !empty($pnLoginPk)'))
      return array('error' => 'No User found.');

    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
     /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');

    $sFirstname = getValue('firstname');
    $sLastname = getValue('lastname');
    $sEmail = getValue('email');
    $nPhone = getValue('phone');
    $nExt = getValue('ext');
    $sPosition = getValue('position');

    if(empty($sEmail) && !filter_var($sEmail, FILTER_VALIDATE_EMAIL))
      return array('error' => $oHTML->getBlocMessage($this->casText['LOGIN_NO_EMAIL']));

    if(empty($sFirstname) )
      return array('error' => $oHTML->getBlocMessage($this->casText['LOGIN_NO_FIRSTNAME']));

    if(empty($sLastname) )
      return array('error' => $oHTML->getBlocMessage($this->casText['LOGIN_NO_LASTNAME']));

    if(empty($nPhone))
      return array('error' => $oHTML->getBlocMessage($this->casText['LOGIN_PHONE_NUMBER']));

    if(empty($nExt))
      return array('error' => $oHTML->getBlocMessage($this->casText['LOGIN_NO_EXTENSION']));

    if(empty($sPosition))
      return array('error' => $oHTML->getBlocMessage($this->casText['LOGIN_NO_POSITION']));

    /*@var $oDB CDatabaseEx */
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'UPDATE login SET ';
    $sQuery.= ' firstname = '.$oDB->dbEscapeString($sFirstname).', ';
    $sQuery.= ' lastname = '.$oDB->dbEscapeString($sLastname).', ';
    $sQuery.= ' email = '.$oDB->dbEscapeString($sEmail).', ';
    $sQuery.= ' phone = '.$oDB->dbEscapeString($nPhone).', ';
    $sQuery.= ' phone_ext = '.$oDB->dbEscapeString($nExt).', ';
    $sQuery.= ' position = '.$oDB->dbEscapeString($sPosition).'';
    $sQuery.= ' WHERE loginPk = '.$pnLoginPk;

    $oDB->ExecuteQuery($sQuery);

    $sURL = $oPage->getUrl('login', '', '', $pnLoginPk);

    return array('notice' => $this->casText['LOGIN_ACCOUNT_SAVE'], 'url' => $sURL);
  }

  /**
   * Display the user form
   * @param  $pnPK User key
   * @return string with HTML content of form
   */

  private function _getUserForm($pnPK)
  {
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'/css/login.form.css');
    /*@var $oDB CDatabaseEx */
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * from login WHERE loginpk = '.$pnPK.' ';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    if(!$bRead)
      return $oHTML->getBlocMessage($this->casText['LOGIN_NO_RESULT']);

    //float hack
    $sHTML= $oHTML->getBlocStart('', array('class' =>'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();

    //**************************************************
    //Tabs section of the user view

    $asTabs = array();
    $asTabs[] = array('tabtitle' => $this->casText['LOGIN_ACCOUNT'],'tabOptions'=>array('tabId'=>'tab_account','class'=>'tab_display tab_selected','onclick' => '$(\'.display_tab\').fadeOut(\'fast\'); $(\'li\').removeClass(\'tab_selected\'); $(this).addClass(\'tab_selected\'); $(\'#cred_tab_account\').fadeIn(\'fast\');'),'content' =>$this->_getUserInfoTab($oResult),'contentOptions'=>array('contentId'=>'cred_tab_account','class'=>'display_tab','style'=>'display:block;'), 'tabstatus' =>CONST_TAB_STATUS_ACTIVE);
    $asTabs[] = array('tabtitle' => $this->casText['LOGIN_DISPLAY'],'tabOptions'=>array('tabId'=>'tab_setting','class'=>'tab_display ','onclick' => '$(\'.display_tab\').fadeOut(\'fast\'); $(\'li\').removeClass(\'tab_selected\'); $(this).addClass(\'tab_selected\'); $(\'#cred_tab_setting\').fadeIn(\'fast\');'),'content' =>$this->_getUserDisplayTab($oResult),'contentOptions'=>array('contentId'=>'cred_tab_setting','class'=>'display_tab hidden','style'=>'display:none;'), 'tabstatus' =>CONST_TAB_STATUS_INACTIVE);
    $asTabs[] = array('tabtitle' => $this->casText['LOGIN_CREDENTIALS'],'tabOptions'=>array('tabId'=>'tab_credential','class'=>'tab_display ','onclick' => '$(\'.display_tab\').fadeOut(\'fast\'); $(\'li\').removeClass(\'tab_selected\'); $(this).addClass(\'tab_selected\'); $(\'#cred_tab_credential\').fadeIn(\'fast\');'),'content' =>$this->_getUserCredentialTab($oResult),'contentOptions'=>array('contentId'=>'cred_tab_credential','class'=>'display_tab hidden','style'=>'display:none;'), 'tabstatus' =>CONST_TAB_STATUS_INACTIVE);

    $sHTML.= $oHTML->getBlocStart('', array('class' =>'bottom_container'));
    $sHTML.= $oHTML->getBlocStart('',  array('class' => 'tabs_container'));

    $sHTML.= $oHTML->getTabs('', $asTabs);

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Function to display form to edit user information
   * @param integer $pnPk
   * @return string
   */

  private function _getEditUserDetail($pnPk)
  {
    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');

   if(!assert('is_integer($pnPk) && !empty($pnPk)'))
     return $this->casText['LOGIN_NO_RESULT'];

    $sQuery = 'SELECT * from login WHERE loginpk = '.$pnPk.' ';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead  = $oResult->readFirst();

    $sHTML = $this->_getUserInfoTab($oResult);

   return $sHTML;
  }

  /**
   * Function to remove the user
   * @param int $pnUserPk
   * @return array
   */

  private function _removeUser($pnUserPk)
  {
    if(!assert('is_integer($pnUserPk) && !empty($pnUserPk)'))
       return array('error' => $this->casText['LOGIN_USER_DELETED']);

    $nStatus =(int)getValue('status');

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM `login` WHERE loginpk = '.$pnUserPk.' ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array('error' => __LINE__.' - '.$this->casText['LOGIN_CONNECTION_DELETE']);

    if($nStatus == 1)
      $sQuery = 'UPDATE login SET status = 0 WHERE loginpk = '.$pnUserPk.' ';
    else
      $sQuery = 'UPDATE login SET status = 1 WHERE loginpk = '.$pnUserPk.' ';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    if(!$oDbResult)
       return array('error' => __LINE__.' - '.$this->casText['LOGIN_CANT_DELETE']);

    $oPage = CDependency::getComponentByName('page');
    return array('notice' => $this->casText['LOGIN_STATUS_CHANGED'], 'timedUrl' => $oPage->getUrl('login', CONST_ACTION_LIST, CONST_LOGIN_TYPE_USER));

  }

/**
 * Display the User Information tab
 * @param  $poUserData array
 * @return string with HTML
 */

 private function _getUserInfoTab($poUserData)
 {
   /*@var $oHTML CDisplayEx */
   $oHTML = CDependency::getComponentByName('display');

   if(!assert('is_object($poUserData) && !empty($poUserData)'))
     return $oHTML->getBlocMessage($this->casText['LOGIN_NO_RESULT']);

    /*@var $oPage CpageEx */
   $oPage = CDependency::getComponentByName('page');
   $nLoginPk = $poUserData->getFieldValue('loginpk', CONST_PHP_VARTYPE_INT);

   if($oPage->getActionReturn())
     $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_SAVEEDIT, CONST_LOGIN_TYPE_USER, $nLoginPk, array(CONST_URL_ACTION_RETURN => $oPage->getActionReturn()));
   else
     $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_SAVEADD, CONST_LOGIN_TYPE_USER, $nLoginPk);

   // Start the user info edit form
   $oForm = $oHTML->initForm('userEditForm');
   $oForm->setFormParams('', true, array('action' => $sURL));
   $sHTML= $oHTML->getBlocStart();

   $oForm->addField('misc', '', array('type' => 'title','title'=> '<span class="h4">'.$this->casText['LOGIN_EDIT_USER'].'</span><hr /><br />'));

   $oForm->addField('input', 'firstname', array('label'=>$this->casText['LOGIN_FIRSTNAME'], 'class' => '', 'value' => $poUserData->getFieldValue('firstname')));
   $oForm->setFieldControl('firstname', array('jsFieldMinSize' => '2', 'jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

   $oForm->addField('input', 'lastname', array('label'=>$this->casText['LOGIN_LASTNAME'], 'class' => '', 'value' => $poUserData->getFieldValue('lastname')));
   $oForm->setFieldControl('lastname', array('jsFieldMinSize' => '2', 'jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

   $oForm->addField('input', 'email', array('label'=> $this->casText['LOGIN_EMAIL'], 'value' => $poUserData->getFieldValue('email')));
   $oForm->setFieldControl('email', array('jsFieldTypeEmail' => '','jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

   $oForm->addField('input', 'phone', array('label'=> $this->casText['LOGIN_PHONE'], 'value' => $poUserData->getFieldValue('phone')));
   $oForm->setFieldControl('phone', array('jsFieldNotEmpty' => '','jsFieldMinSize' => 8));

   $oForm->addField('input', 'ext', array('label'=> $this->casText['LOGIN_EXTENSION'], 'value' => $poUserData->getFieldValue('phone_ext')));
   $oForm->setFieldControl('ext', array('jsFieldMinSize' => 4));

   $oForm->addField('input', 'position', array('label'=> $this->casText['LOGIN_POSITION'], 'value' => $poUserData->getFieldValue('position')));
   $oForm->setFieldControl('position', array('jsFieldNotEmpty' => '','jsFieldMinSize' => 8));

   $sHTML.= $oForm->getDisplay();
   $sHTML.= $oHTML->getBlocEnd();

   return $sHTML;
  }

  /**
   * Display the user display setting information
   * @param $poUserData array
   * @return string HTML
   */

  private function _getUserDisplayTab($poUserData)
  {
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_SAVEEDIT, CONST_LOGIN_TYPE_USER,$poUserData->getFieldValue('loginpk',CONST_PHP_VARTYPE_INT), array('display' => 1));
    $sHTML = $oHTML->getBlocStart('');

    //Start the credential form
    $oForm = $oHTML->initForm('userDisplayForm');
    $oForm->setFormParams('', true, array('action' => $sURL));

    $asCompanyData = array(CONST_TAB_CP_DETAIL=>'Detail',CONST_TAB_CP_EMPLOYEES=>'Employees',CONST_TAB_CP_DOCUMENT=>'Documents',CONST_TAB_CP_EVENT=>'Activities');
    $asContactData = array(CONST_TAB_CT_DETAIL=>'Detail',CONST_TAB_CT_COWORKERS=>'Co-workers',CONST_TAB_CT_DOCUMENT=>'Documents',CONST_TAB_CT_EVENT=>'Activities',CONST_TAB_CT_PROFILE=>'Profiles');

    $oForm->addField('misc', '', array('type' => 'title','title'=> '<span class="h4">'.$this->casText['LOGIN_DISPLAY_TAB_SETTING'].'</span><hr /><br />'));
    $oForm->addField('select', 'company[]', array('label'=>$this->casText['LOGIN_COMPANY_TABS'], 'multiple' => 'multiple'));
    if(isset($_SESSION['userPreference']['cptab']) && !empty($_SESSION['userPreference']['cptab']))
    {
    foreach($_SESSION['userPreference']['cptab'] as $skCpPreference=>$svCpPreference)
    {
      if(in_array($svCpPreference,$_SESSION['userPreference']['cptab']))
      {
         $oForm->addOption('company[]', array('label' =>$asCompanyData[$skCpPreference], 'value' =>$skCpPreference,'selected'=>'selected'));
         }
       }
    }

    $oForm->addField('select', 'connection[]', array('label'=>$this->casText['LOGIN_CONNECTION_TABS'], 'multiple' => 'multiple'));
    if(isset($_SESSION['userPreference']['cttab']) && !empty($_SESSION['userPreference']['cttab']))
    {
      foreach($_SESSION['userPreference']['cttab'] as $skCtPreference=>$svCtPreference)
      {
        if(in_array($svCtPreference,$_SESSION['userPreference']['cttab']))
          $oForm->addOption('connection[]', array('label' =>$asContactData[$skCtPreference], 'value' => $skCtPreference, 'selected' => 'selected'));
      }
    }

    $oForm->addField('misc', '', array('type' => 'text','text'=> '<span class="h4">'.$this->casText['LOGIN_NUMBER_RECORDS'].'</span><hr /><br />'));

    $oForm->addField('select', 'list', array('label'=>''.$this->casText['LOGIN_NUMBER_OF_RECORDS'], 'class' => '', 'value' => ''));
    $oForm->addOption('list', array('label' => 10, 'value' =>10));
    $oForm->addOption('list', array('label' => 25, 'value' =>25));
    $oForm->addOption('list', array('label' => 50, 'value' =>50));
    $oForm->addOption('list', array('label' => 100, 'value' =>100));
    $oForm->addOption('list', array('label' => 200, 'value' =>200));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd('');

    return  $sHTML;
   }

  /**
  * Display the user email credentials information
  * @param $poUserData array
  * @return string with form details
  */

  private function _getUserCredentialTab($poUserData)
  {
    /*@var $oHTML CDisplayEx */
   $oHTML = CDependency::getComponentByName('display');
   /*@var $oPage CPageEx */
   $oPage = CDependency::getComponentByName('page');

   if(!assert('is_object($poUserData) && !empty($poUserData)'))
     return $oHTML->getBlocMessage($this->casText['LOGIN_NO_RESULT']);

    $nLoginPk = $poUserData->getFieldValue('loginpk', CONST_PHP_VARTYPE_INT);
    $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_SAVEEDIT, CONST_LOGIN_TYPE_USER, $nLoginPk, array('credentials' => 1));

    $sHTML= $oHTML->getBlocStart();
    $sHTML.= $oHTML->getBlocStart('');

    //Start the credential form
    $oForm = $oHTML->initForm('userCredentialForm');
    $oForm->setFormParams('', true, array('action' => $sURL));

    //div including the form
    $oForm->addField('misc', '', array('type' => 'text','text'=> '<span class="h4">'.$this->casText['LOGIN_UPDATE_CREDENTIALS'].'</span><hr /><br />'));

    $oForm->addField('input', 'login', array('label'=>$this->casText['LOGIN_LOGIN'], 'class' => '', 'value' => $poUserData->getFieldValue('webmail')));
    $oForm->setFieldControl('login', array('jsFieldMinSize' => '4', 'jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

    $oForm->addField('input', 'password', array('label'=>$this->casText['LOGIN_PASSWORD'], 'type'=> 'password','class' => '', 'value' => $poUserData->getFieldValue('webpassword')));
    $oForm->setFieldControl('password', array('jsFieldMinSize' => '6', 'jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

    $oForm->addField('input', 'alias', array('label'=> $this->casText['LOGIN_ALIAS'], 'value' => $poUserData->getFieldValue('aliasName')));
    $oForm->setFieldControl('alias', array('jsFieldMinSize' => 4));

    $oForm->addField('input', 'port', array('label'=> $this->casText['LOGIN_PORT'],'readonly'=>'readonly', 'value' => '143'));
    $oForm->setFieldControl('port', array('jsFieldMaxSize' => 255));

    $oForm->addField('input', 'imap', array('label'=> $this->casText['LOGIN_IMAP'],'readonly'=>'readonly', 'value' => 'mail.bulbouscell.com'));
    $oForm->setFieldControl('imap', array('jsFieldMaxSize' => 255,'jsFieldNotEmpty' => ''));

    $oForm->addField('textarea', 'signature', array('label'=> $this->casText['LOGIN_SIGNATURE'], 'value' => $poUserData->getFieldValue('signature'), 'isTinymce' => 1));
    $oForm->setFieldControl('signature', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 4096));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Display the login form
   * @return fom structure
   */

  private function _getLoginForm()
  {
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'css/login.form.css'));
    $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_VALIDATE, CONST_LOGIN_TYPE_PASSWORD);

    //force redirection for external identification

    $sRedirectAfterLogin = getValue('redirect');
    if(!empty($sRedirectAfterLogin))
      $sURL.= '&redirect='.urlencode($sRedirectAfterLogin);

    $sHTML = $oHTML->getSpace(2);
    $sHTML.= $oHTML->getBlocStart();

      //div receiving error message after loging attempts
      $sHTML.= $oHTML->getBlocStart('loginMsgId', array('class' => 'notice fontError'));
      $sHTML.= $oHTML->getText(CONST_LOGIN_MESSAGE);
      $sHTML.= $oHTML->getBlocEnd();

      //div including the form
      $sHTML.= $oHTML->getBlocStart('loginFormId');

        /* @var $oForm CFormEx */
        $oForm = $oHTML->initForm('loginFormData');
        $oForm->setFormParams('', true, array('submitLabel' => $this->casText['LOGIN_SIGNIN'], 'action' => $sURL));
        $oForm->setFormDisplayParams(array('noCancelButton' => 1, 'columns' => 1));

        $oForm->addField('input', 'login', array('label'=>$this->casText['LOGIN_LOGIN'], 'class' => 'loginWideField'));
        $oForm->addField('input', 'password', array('label'=>$this->casText['LOGIN_PASSWORD'], 'type'=> 'password', 'class' => 'loginWideField'));
        $sURL = $oPage->getUrl('login', CONST_ACTION_RESET, CONST_LOGIN_TYPE_PASSWORD);

        $oForm->addField('misc', '', array('type'=> 'text', 'text'=>'<a href="'.$sURL.'" >'.$this->casText['LOGIN_FORGOT_PASSWORD'].'</a>'));
        $sHTML.= $oForm->getDisplay();

      $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
  * Form to reset the password
  * @return form structure
  */

  private function _getLoginResetPasswordForm()
  {
    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'css/login.form.css'));

    $sHashCode = getValue('hshc');
    if(empty($sHashCode))
    {
      //Form asking to input email address
      $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_SEND, CONST_LOGIN_TYPE_PASSWORD);
      $sHTML= $oHTML->getBlocStart();

      //div receiving error message after reset attempts
      $sHTML.= $oHTML->getBlocStart('resetMsgId', array('class' => 'notice fontError'));
      $sHTML.= $oHTML->getText($this->casText['LOGIN_STEP_FORGOT_1']);
      $sHTML.= $oHTML->getBlocEnd();

      //div including the form
      $sHTML.= $oHTML->getBlocStart('resetFormId');

      /* @var $oForm CFormEx */
      $oForm = $oHTML->initForm('resetFormData');
      $sFormId = $oForm->getFormId();
      $oForm->setFormParams('', false);
      $oForm->setFormDisplayParams(array('noButton' => 1, 'columns' => 1));

      $oForm->addField('input', 'email', array('label'=>$this->casText['LOGIN_EMAIL'], 'class' => 'loginWideField'));
      $oForm->addField('input', 'btn', array('type'=> 'button', 'value'=>$this->casText['LOGIN_SEND_RESETEMAIL'], 'onclick' => "setLoadingScreen('body', true); setTimeout('setLoadingScreen(\\'body\\', false);', 5000); AjaxRequest('".$sURL."', '', 'resetFormDataId', 'resetMsgId');"));

      $sHTML.= $oForm->getDisplay();

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
    }
    else
    {
      //Got hashcode from email, check then display form reset

      if(empty($this->cnPk))
        return __LINE__.' - '.$this->casText['LOGIN_PARAMETER_INCORRECT'];

      $oDB = CDependency::getComponentByName('database');
      $oDB->dbConnect();
      $sQuery = 'SELECT * FROM `login` WHERE loginpk = '.$this->cnPk.' AND hashcode = '.$oDB->dbEscapeString($sHashCode).' ';

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();

      if(!$bRead)
        return __LINE__.' - '.$this->casText['LOGIN_PARAMETER_INCORRECT'];

      $sResetDate = $oDbResult->getFieldValue('date_reset');
      $sMinDate = date('Y-m-d H:i:s', mktime(((int)date('H')-3), date('i'), date('s'), date('m'), date('d'), date('Y')));

      if(empty($sResetDate) || $sResetDate < $sMinDate)
        return __LINE__.' - '.$this->casText['LOGIN_REQ_EXPIRE'];

      $sHTML= $oHTML->getBlocStart();

      //div receiving error message after reset attempts
      $sHTML.= $oHTML->getBlocStart('resetMsgId', array('class' => 'notice fontError'));
      $sHTML.= $oHTML->getText($this->casText['LOGIN_STEP_FORGOT_3']);
      $sHTML.= $oHTML->getBlocEnd();

      //div including the form
      $sHTML.= $oHTML->getBlocStart('resetFormId');

      /* @var $oForm CFormEx */
      $oForm = $oHTML->initForm('resetFormData');
      $oForm->setFormParams('', false);
      $oForm->setFormDisplayParams(array('noButton' => 1, 'columns' => 1));

      $oForm->addField('input', 'hashcode', array('type'=>'hidden', 'value' => $oDB->dbEscapeString($sHashCode, '', true)));
      $oForm->addField('input', 'password', array('label'=>$this->casText['LOGIN_NEW_PASSWORD'], 'type' => 'password', 'class' => 'loginWideField'));
      $oForm->addField('input', 'confirm', array('label'=>$this->casText['LOGIN_CONFIRM_PASSWORD'], 'type' => 'password', 'class' => 'loginWideField'));

      $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_SAVEEDIT, CONST_LOGIN_TYPE_PASSWORD, $this->cnPk);
      $oForm->addField('input', 'btn', array('type'=> 'button', 'value'=>$this->casText['LOGIN_SAVE_PASSWORD'], 'onclick' => "AjaxRequest('".$sURL."', 'body', 'resetFormDataId', 'resetMsgId');"));

      $sHTML.= $oForm->getDisplay();
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
    }
    return $sHTML;
  }

  /**
   * Function to send the new password
   * @return array message after sending email
   */

  private function _getSendPassword()
  {
    $asResult = array();
    $sEmailAddress = getValue('email', '', 'post');
    if(empty($sEmailAddress) || filter_var($sEmailAddress, FILTER_VALIDATE_EMAIL) === false)
    {
      return array('message' => $this->casText['LOGIN_EMPTY_EMAIL_TYPED']);
    }

    $oDB = CDependency::getComponentByName('database');

    $sHashCode = uniqid('id', true);
    //save the hash code in database + date

    $sSQL = 'SELECT loginpk FROM login WHERE email = "'.$sEmailAddress.'" ';
    $oDbResult = $oDB->ExecuteQuery($sSQL);
    $bRead = $oDbResult->ReadFirst();

    if(!$bRead)
      return array('message' => '('.__LINE__.')'.$this->casText['LOGIN_COULDNT_MATCH']);

    $nPk = (int)$oDbResult->getFieldValue('loginpk');
    if(empty($nPk))
      return array('message' => '('.__LINE__.')'.$this->casText['LOGIN_ERROR_OCCURED']);


    $sSQL = 'UPDATE login SET hashcode = "'.$sHashCode.'", date_reset = "'.date('Y-m-d H:i:s').'" WHERE loginpk = '.$nPk;
    $bRead = $oDB->ExecuteQuery($sSQL);
    if(!$bRead)
      return array('message' => '('.__LINE__.')'.$this->casText['LOGIN_ERROR_OCCURED']);

    $oPage = CDependency::getComponentByName('page');
    $oMail = CDependency::getComponentByName('mail');

    if(!empty($oMail))
    {
      $sURL = $oPage->getUrl('login', CONST_ACTION_RESET, CONST_LOGIN_TYPE_PASSWORD, (int)$oDbResult->getFieldValue('loginpk'));
      $sURL.= '&hshc='.$sHashCode;
      $sSubject = 'BCM message: Reset your password';
      $sContent = 'Reseting your password step 2/3: <br /><br />Please click on the link below to reset your password:  <br /><br />'."\n\n";
      $sContent.= '<a href="'.$sURL.'">http://bcm.bulbouscell.com/reset-password</a>';
      $sContent.= '<br /><br /> Regards, BCM Administrator.';

      $bSent = $oMail->sendRawEmail('info@bcm.com',$sEmailAddress, $sSubject, $sContent);
    }

    if($bSent)
      return array('message' => $this->casText['LOGIN_EMAIL_SENT'].$sEmailAddress);
    else
      return array('message' => $this->casText['LOGIN_TRY_LATER']);

  }

  /**
   * Set the new password
   * @return array message after setting password
   */

  private function _setNewPassword()
  {
    $sPassword = getValue('password', '', 'post');
    $sConfirm = getValue('confirm', '', 'post');
    $sHashcode = getValue('hashcode', '', 'post');

    if(empty($this->cnPk) || empty($sHashcode))
      return array('error' => $this->casText['LOGIN_BAD_PARAMETER']);

    if(empty($sPassword) || empty($sConfirm))
      return array('error' => $this->casText['LOGIN_INPUT_NEW_PASSWORD']);

    if($sPassword != $sConfirm)
      return array('error' => $this->casText['LOGIN_PASSWORD_CONFIRM_DIFF']);

    if(strlen($sPassword) < 5 )
      return array('error' => $this->casText['LOGIN_PASSWORD_SHORT']);

    $bHasUpper = preg_match('/[A-Z]/', $sPassword);
    $bHasLower = preg_match('/[a-z]/', $sPassword);
    $bHasNumber = preg_match('/[0-9]/', $sPassword);
    $bHasSymbole = preg_match('/[^A-Za-z0-9]/', $sPassword);
    $nSecurityCheck = (int)$bHasUpper+ (int)$bHasLower+ (int)$bHasNumber+ (int)$bHasSymbole;

    if($nSecurityCheck < 3)
      return array('message' => $this->casText['LOGIN_PASSWD_EXAMPLE']);

    $oDB = CDependency::getComponentByName('database');

    $sSQL = 'UPDATE login SET password = '.$oDB->dbEscapeString($sPassword).', hashcode = NULL WHERE loginpk = '.$this->cnPk.' AND hashcode = "'.$sHashcode.'" ';
    $bRead = $oDB->ExecuteQuery($sSQL);

    if($bRead)
      return array('message' => $this->casText['LOGIN_NEW_PASSWD_CHANGED'], 'timedUrl' =>"index.php5");
    else
      return array('message' => $this->casText['LOGIN_ERROR_OCCURED']);
  }

  /**
   * Get the user identification
   * @param boolen $pbIsAjax
   * @return array
   */

  private function _getIdentification($pbIsAjax = false, $pnCookiePk = 0)
  {
    $oDB = CDependency::getComponentByName('database');

    if(!empty($pnCookiePk) && is_integer($pnCookiePk))
       $sQuery = 'SELECT * FROM `login` WHERE loginpk = '.$pnCookiePk.' AND status = 1 ';
    else
    {
      if(empty($_POST) || !isset($_POST['login']) || !isset($_POST['password']))
        return array('error' => __LINE__.' - '.$this->casText['LOGIN_PASSWORD_REQD']);

      if(empty($_POST['login']) || empty($_POST['password']))
        return array('error' => __LINE__.' - '.$this->casText['LOGIN_PASSWORD_REQD']);

      $sQuery = 'SELECT * FROM `login` WHERE (`id` = '.$oDB->dbEscapeString($_POST['login']).' ';
      $sQuery.= ' AND `password` = '.$oDB->dbEscapeString($_POST['password']).') ';
      $sQuery.= ' OR ( `email` = '.$oDB->dbEscapeString($_POST['login']).' ';
      $sQuery.= ' AND `password` = '.$oDB->dbEscapeString($_POST['password']).') ';
    }

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
      return array('error' => __LINE__.' -'.$this->casText['LOGIN_PASSWORD_INCORRECT']);

    $nStatus = $oDbResult->getFieldValue('status', CONST_PHP_VARTYPE_INT);
    if($nStatus == 0)
      return array('error' => __LINE__.' -'.$this->casText['LOGIN_ACCOUNT_DEACTIVATED']);

    if($nStatus == 2)
      return array('error' => __LINE__.' - '.$this->casText['LOGIN_ACCOUNT_SUSPENDED']);

    //set session
    $_SESSION['userData']['pk'] = (int)$oDbResult->getFieldValue('loginpk', CONST_PHP_VARTYPE_INT);
    $_SESSION['userData']['id'] = $oDbResult->getFieldValue('id');
    $_SESSION['userData']['admin'] = $oDbResult->getFieldValue('is_admin', CONST_PHP_VARTYPE_BOOL);
    $_SESSION['userData']['password'] = $oDbResult->getFieldValue('password');
    $_SESSION['userData']['email'] = $oDbResult->getFieldValue('email');
    $_SESSION['userData']['lastname'] = $oDbResult->getFieldValue('lastname');
    $_SESSION['userData']['firstname'] = $oDbResult->getFieldValue('firstname');
    $_SESSION['userData']['loginTime'] = time();
    $_SESSION['userRight'] = array();

    /* TODO: manage rights & profile/preferences  */
    $asPreferences = $this->_getPreferences((int)$_SESSION['userData']['pk']);
    if(!empty($asPreferences))
    {
      $_SESSION['userPreference']= array();
      $_SESSION['userPreference']['cptab']= $asPreferences[1];
      $_SESSION['userPreference']['cttab']= $asPreferences[2];
      $_SESSION['userPreference']['list']= $asPreferences[3];
    }

    $oRight = CDependency::getComponentByName('right');
    $oRight->loadUserRights($_SESSION['userData']['pk']);

    $sHash = sha1($_SESSION['userData']['pk'].'|@|'.uniqid('cook_', true).'|@|'.rand(1000000, 1000000000));
    $sQuery = 'UPDATE login SET date_last_log = "'.date('Y-m-d H:i:s').'", log_hash = "'.$sHash.'" WHERE loginpk = '.$_SESSION['userData']['pk'];
    $oDB->ExecuteQuery($sQuery);

    //Create a 3 hour cookie (will be refresh as long as user browse pages)
    //@setcookie('login_userdata', serialize(array('pk' => $_SESSION['userData']['pk'], 'hash' => $sHash)), mktime(date('H')+3, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y')), '/');
    @setcookie('login_userdata', serialize(array('pk' => $_SESSION['userData']['pk'], 'hash' => $sHash)), time()+3600*3, '/');
    //redirections
    $sRedirectUrl = getValue('redirect');

    if(!empty($sRedirectUrl))
    {
      //manage requested redirection after login
      $asUrl = parse_url($sRedirectUrl);

      if(empty($asUrl['query']))
        $sUrl = $_GET['redirect'].'?from=bccrm&pk='.$_SESSION['userData']['pk'];
      else
        $sUrl = $_GET['redirect'].'&from=bccrm&pk='.$_SESSION['userData']['pk'];
    }

    elseif(!empty($_SESSION['urlRedirect']))
    {
      //manage automatic redirection after login
       $sUrl = $_SESSION['urlRedirect'];
    }
    else
    {
      //no redirection => homepage
      $oPage = CDependency::getComponentByName('page');
      $sUrl = $oPage->getUrlHome();
    }

    if($pbIsAjax)
      return array('url' => $sUrl);

    $this->_redirectUser($sUrl);
    exit;
  }

  public function rebuildCookie()
  {
    $oDB = CDependency::getComponentByName('database');

    $sHash = sha1($_SESSION['userData']['pk'].'|@|'.uniqid('cook_', true).'|@|'.rand(1000000, 1000000000));
    $sQuery = 'UPDATE login SET date_last_log = "'.date('Y-m-d H:i:s').'", log_hash = "'.$sHash.'" WHERE loginpk = '.$_SESSION['userData']['pk'];
    $oDB->ExecuteQuery($sQuery);

    @setcookie('login_userdata', serialize(array('pk' => $_SESSION['userData']['pk'], 'hash' => $sHash)), time()+3600*3, '/');

    return true;
  }


  private function _getPreferences($pnLoginPk)
  {
    if(!assert('is_integer($pnLoginPk) && !empty($pnLoginPk)'))
     return array();

    $oDB = CDependency::getComponentByName('database');
    $asData = array();

    $sQuery = 'SELECT lp.*,up.name as name FROM `login_preference` AS lp INNER JOIN `user_preference` AS up ON up.preferencepk = lp.user_preferencefk AND lp.loginfk = '.$pnLoginPk;
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
    {
      $sQuery = 'SELECT lp.*,up.name as name FROM `login_preference` AS lp INNER JOIN `user_preference` AS up ON up.preferencepk = lp.user_preferencefk AND lp.loginfk = 0';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      while($bRead)
      {
        $asUnserialized = @unserialize($oDbResult->getFieldValue('value'));
        if($asUnserialized !== false)
          $asData[$oDbResult->getFieldValue('user_preferencefk')] =  $asUnserialized;
        else
          $asData[$oDbResult->getFieldValue('user_preferencefk')] =  $oDbResult->getFieldValue('value');

        $bRead = $oDbResult->readNext();
      }
    }
    else
    {
     while($bRead)
     {
       $sRecords = $oDbResult->getFieldValue('value');
       $asData[$oDbResult->getFieldValue('user_preferencefk')] =  @unserialize($sRecords);
       $bRead = $oDbResult->readNext();
     }
    }
    return $asData;
   }

   public function getPreferences($psName)
   {
      if(!assert('is_string($psName) && !empty($psName)'))
      return array();

      return $_SESSION['userPreference'][''.$psName.''];

   }

  /**
   * Check the identification of cookie
   * @param integer $pnPK
   * @return boolean
   */

  private function _getCookieIdentification($pasCookieData)
  {
    if(!assert('is_array($pasCookieData) && !empty($pasCookieData)'))
      return false;

    $nUserPk = (int)$pasCookieData['pk'];
    $sSecurityHash = $pasCookieData['hash']; //$sHash = sha1(['pk'].'|@|'.['id'].'|@|'.['password'].'|@|'.['email']);

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM `login` WHERE loginpk = '.$nUserPk.' AND status = 1 ';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return false;

    $nStatus = $oDbResult->getFieldValue('status', CONST_PHP_VARTYPE_INT);
    if($nStatus != 1)
      return false;

    $sHash = $oDbResult->getFieldValue('log_hash');
    if($sHash != $sSecurityHash)
      return false;

    //lauch the standard login process passing the user pk
    $this->_getIdentification(false, $nUserPk);
    //exit(__LINE__.' - error cookie');

   return true;
  }

  /**
   * Check user logged or not
   * @return array of records
   */

  private function _checkUserLogged()
  {
    $asAllowedClient['127.0.0.1'] = '';
    $asAllowedClient['192.168.10.24'] = '32435354vf234b42n4gf2n4rt4y5r4ne3';
    $asAllowedClient['203.167.38.24'] = '32435354vf234b42n4gf2n4rt4y5r4ne3';

    $asAllowedClient['192.168.10.25'] = 'SDA354SADasd4das45a6788sa4124';
    $asAllowedClient['203.167.38.25'] = 'SDA354SADasd4das45a6788sa4124';
    $asAllowedClient['192.168.10.29'] = 'SDA354SADasd4das45a6788sa4124';
    $asAllowedClient['203.167.38.29'] = 'SDA354SADasd4das45a6788sa4124';

    $sClientIp = $_SERVER['REMOTE_ADDR'];
    $sHash = getValue('extIdHash');
    $sReturnUrl = getValue('url');

    if(!isset($asAllowedClient[$sClientIp]) || $asAllowedClient[$sClientIp] != $sHash)
      exit('DN/'.$sClientIp.'/'.$sHash);

    $sEmail = getValue('email');
    $nPk = (int)getValue('pk');
    if(empty($sEmail) && empty($nPk))
      return array('status' => -1, 'pk' => 0, 'email' => '', 'date' => '', 'url' => '', 'msg'=>'bad parameters to identify the user');

    $oDB = CDependency::getComponentByName('database');
    $oDB->dbConnect();

    if(!empty($nPk))
     $sQuery = 'SELECT * FROM `login` WHERE `loginpk` = "'.$nPk.'" ';
    else
     $sQuery = 'SELECT * FROM `login` WHERE `email` = '.$oDB->dbEscapeString($sEmail).' ';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
    {
      $oPage = CDependency::getComponentByName('page');
      $sUrl = $oPage->getUrlHome();
      return array('status' => 0, 'pk' => 0, 'email' => '', 'date' => '', 'url' => $sUrl.'&redirect='.urlencode($sReturnUrl), 'msg'=>'user unknown');
    }

    //Not identified if identified more than 4 hours ago, or if it was the day before
    $asDbDate = explode(' ', $oDbResult->getFieldValue('date_last_log'));

    if($asDbDate < date('Y-m-d') || $oDbResult->getFieldValue('date_last_log') < date('Y-m-d H:i:s', (time()-(4*3600))))
    {
      $oPage = CDependency::getComponentByName('page');
      $sUrl = $oPage->getUrlHome();

      return array('status' => 0, 'pk' => $oDbResult->getFieldValue('loginpk'), 'email' => $oDbResult->getFieldValue('email'),
        'date' => $oDbResult->getFieldValue('loginpk'), 'url' => $sUrl.'&redirect='.urlencode($sReturnUrl), 'msg'=>'not identified ');
    }

    return array('status' => 1, 'pk' => $oDbResult->getFieldValue('loginpk'), 'email' => $oDbResult->getFieldValue('email'),
        'date' => $oDbResult->getFieldValue('loginpk'), 'url' => '', 'msg'=>'identified');
  }

  /**
   * Disconnect the user and redirect him to the login screen
   * @param boolean $pbIsAjax
   */
  private function _getLogout($pbIsAjax = false)
  {
    $oPage = CDependency::getComponentByName('page');

    $oDb = CDependency::getComponentByName('database');
    $sQuery = 'UPDATE login SET log_hash = \'\' WHERE loginpk = '.$this->casUserData['pk'];
    $oDb->executeQuery($sQuery);

        //unset cookie
    setcookie('login_userdata', '', time()-36000, '/');

    //unset session
    session_destroy();

    $sUrl = $oPage->getUrlHome();

    if($pbIsAjax)
      return array('url' => $sUrl); //'message' => 'login ok',

    return $this->_redirectUser($sUrl);
  }

  private function _redirectUser($psUrl = '')
  {
    $oPage = CDependency::getComponentByName('page');
    if(empty($psUrl))
    {
      //redirect the user on the portal
      $sURl = $oPage->getUrlHome();
      return $sURl = $oPage->redirect($sURl);
    }

    return $oPage->redirect($psUrl);
  }

  /**
   * Display home page contents
   * @return HTML structure
   */



  /**
  * Function to get the user list selected from the parameters
  * @param integer $pvPk
  * @param boolean $pbOnlyActive
  * @param boolean $pbOnlyExist
  * @param boolean $pbIncludeRoot
  * @return array
  */
  public function getUserList($pvPk = 0, $pbOnlyActive = true, $pbIncludeRoot = false)
  {
    if(!assert('is_integer($pvPk) || is_array($pvPk)'))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM `login` ';

    if($pbOnlyActive)
       $sQuery.= ' WHERE status > 0';
    else
      $sQuery.= ' WHERE 1 ';

    if(!$pbIncludeRoot)
    {
      $sQuery.= ' AND is_admin <> 1 ';
    }

    if(!empty($pvPk))
    {
      if(is_integer($pvPk))
        $sQuery.= ' AND loginpk = '.$pvPk;
      else
        $sQuery.= ' AND loginpk IN ('.implode(',', $pvPk).') ';
    }

    $sQuery.= ' ORDER BY firstname';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
      return array();

    $asResult = array();

    while($bRead)
    {
      $asResult[$oDbResult->getFieldValue('loginpk')] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asResult;
  }

  /**
   * Selector of the users of BCM
   * @return type
   */

  private function _getSelectorUser()
  {
    $sSearch = getValue('q');
    if(empty($sSearch))
      return json_encode(array());

    $bDisplayTeam = (bool)getValue('team', 0);
    $asJsonData = array();

    $oDB = CDependency::getComponentByName('database');
    //status = 1 AND
    $sQuery = 'SELECT * FROM login WHERE  (lower(lastname) LIKE '.$oDB->dbEscapeString('%'.strtolower($sSearch).'%').' OR lower(firstname) LIKE '.$oDB->dbEscapeString('%'.strtolower($sSearch).'%').') ORDER BY lastname, firstname ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if($bRead)
    {
      while($bRead)
      {
        $asData['id'] = $oDbResult->getFieldValue('loginpk');
        $asData['name'] = '#'.$asData['id'].' - '.$oDbResult->getFieldValue('firstname').' '.$oDbResult->getFieldValue('lastname');
        $asJsonData[] = json_encode($asData);
        $bRead = $oDbResult->readNext();
      }
    }

    if($bDisplayTeam)
    {
      $oSettings = CDependency::getComponentByName('settings');
      $asTeam = $oSettings->getManageableList(0, 'team_users_compact');
      $asAll = array();


      $asData['class_selected'] = 'login_team_selected';
      $bFirst = true;

      foreach($asTeam as $sTeamName => $asTeamMembers)
      {
        $asAll = array_merge($asAll, $asTeamMembers);

        //add a class on the first Grp to separate with single users
        if($bFirst)
        {
          $bFirst = false;
          $asData['class_result'] = 'login_team_selector_separator';
        }
        else
          $asData['class_result'] = 'login_team_selector';

        $asData['id'] = implode(',', $asTeamMembers);
        $asData['name'] = '[Group] - '.$sTeamName;
        $asJsonData[] = json_encode($asData);
      }

      $asData['id'] = implode(',', array_unique($asAll));
      $asData['name'] = '[Group] - Everybody';
      $asJsonData[] = json_encode($asData);
    }

    echo '['.implode(',', $asJsonData).']';
  }

  /**
   * Display all information of user
   * @param integer $pnLoginPk
   * @return array
   */

  public function getUserDataByPk($pnLoginPk = 0)
  {
    if(!assert('is_integer($pnLoginPk)'))
      return array();

    if(empty($pnLoginPk))
      $pnLoginPk = $this->getUserPk();

    $oDB = CDependency::getComponentByName('database');
    $sQuery='SELECT * FROM login WHERE loginpk = '.$pnLoginPk;
    $oDbResult = $oDB->ExecuteQuery($sQuery);

    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array();

    return $oDbResult->getData();
  }

  /**
   * Log the user recent activity
   * @param integer $pnLoginPk
   * @param string $psItemUid
   * @param string $psItemAction
   * @param string $psItemType
   * @param string $pnItemPk
   * @param string $sText
   * @param string $sLink
   * @return boolean
  */
  public function getUserActivity($pnLoginPk, $psItemUid, $psItemAction, $psItemType, $pnItemPk, $psText, $psLink, $pnFollowerfk=0, $pnNotifierfk=0)
  {
    if(!assert('is_integer($pnLoginPk)'))
        return false;

    $sDate = date('Y-m-d H:i:s');
    $oDB = CDependency::getComponentByName('database');

    //search any activity on this element in the last N hours
    $sQuery = 'SELECT * from `login_activity` WHERE loginfk="'.$pnLoginPk.'" AND followerfk= "'.$pnFollowerfk.'" ';
    //$sQuery.= ' AND cp_uid= "'.$psItemUid.'" AND cp_action="'.$psItemAction.'" AND cp_pk="'.$pnItemPk.'" ';
    $sQuery.= ' AND cp_uid= "'.$psItemUid.'" AND cp_type="'.$psItemType.'" AND cp_pk="'.$pnItemPk.'" ';
    $sQuery.= ' AND TIMESTAMPDIFF(HOUR, log_date, "'.$sDate.'") < 4  ';
    $sQuery.= ' LIMIT 10 ';
    //echo $sQuery;

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    if($bRead)
    {
      //if action is view and we've found recent entry(ies), no need to log
      if($psItemAction == CONST_ACTION_VIEW)
      {
        //echo 'action view, but already recent view';
        return false;
      }

      // if 1 existing entry has action=VIEW, and the current one isn't,
      // we update the view by a more important one
      while($bRead)
      {
        $nActivityPk = (int)$oResult->getFieldValue('login_activitypk');
        //echo 'found activity : '.$nActivityPk.'<br />';

        if(!empty($nActivityPk) && $psItemAction != CONST_ACTION_VIEW && $oResult->getFieldValue('cp_action') == CONST_ACTION_VIEW)
        {
          //echo 'update activity with : '.$psItemAction;

          $sQuery= 'UPDATE `login_activity` SET `text` = '.$oDB->dbEscapeString($psText).', ';
          $sQuery.= ' `log_date` = "'.$sDate.'", `log_link` = '.$oDB->dbEscapeString($psLink).' ';
          $sQuery.= ' WHERE login_activitypk = '.$nActivityPk;

          if($oDB->ExecuteQuery($sQuery))
            return true;

          assert('false; //could not update activity');
          return false;
        }

        $bRead = $oResult->readNext();
      }
    }

    //no result or nothing updated: we insert the new avtivity log
    //echo 'No activity found or updated: create a new one ';

    $sQuery= 'INSERT INTO `login_activity`(`loginfk`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`,`text`,`log_date`,`log_link`,`followerfk`,`notifierfk`)';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString($pnLoginPk).', '.$oDB->dbEscapeString($psItemUid).', '.$oDB->dbEscapeString($psItemAction).',';
    $sQuery.= ''.$oDB->dbEscapeString($psItemType).', '.$oDB->dbEscapeString($pnItemPk).', '.$oDB->dbEscapeString($psText).', '.$oDB->dbEscapeString($sDate).',';
    $sQuery.= ''.$oDB->dbEscapeString($psLink).','.$oDB->dbEscapeString($pnFollowerfk).','.$oDB->dbEscapeString($pnNotifierfk).') ';

    if($oDB->ExecuteQuery($sQuery))
      return true;

    return false;
  }


  /**
   * Clean the user activity table: remove > 2months old entries
   * @return boolean indicating if entries have been removed
   */
  private function _cleanRecentActivity()
  {
    $oDB = CDependency::getComponentByName('database');

    $sDate = date('Y-m-d', mktime(0, 0, 0, (int)date('m')-2, date('d'), date('Y')));

    $sQuery = ' SELECT `login_activitypk` FROM `login_activity` WHERE `log_date` < "'.$sDate.'" ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
    {
      echo 'No activity to clean <br />';
      return true; // nothing to clean
    }

    $anToDelete = array();
    while($bRead)
    {
      $anToDelete[] = $oDbResult->getFieldValue('login_activitypk', CONST_PHP_VARTYPE_INT);
      $bRead = $oDbResult->readNext();
    }

    $sQuery = ' DELETE FROM login_activity WHERE login_activitypk IN ('.implode(',', $anToDelete).') ';
    $oDbResult = $oDB->executeQuery($sQuery);

    if(!$oDbResult)
    {
      echo 'could\'nt delete user activity T_T';
      return false;
    }

    echo count($anToDelete).' activity entries have been deleted <br />';
    return true;
  }

  /**
   * Function to notify about the birthday to the users and birthday boy
   * @return boolean
   */

  private function _checkBirthday()
  {
    //TODO : Add the field called birthdate in the login table
    $oDB = CDependency::getComponentByName('database');
    $oMail = CDependency::getComponentByName('mail');

    $sMonth = date('m');
    $sTomorrow = date('d',strtotime("+1 days"));
    $sToday = date('d');

    //Sending Email to others except birthday boy

    $sQuery = 'SELECT * FROM login WHERE MONTH(birthdate) = "'.$sMonth.'" AND DAY(birthdate) = "'.$sTomorrow.'"';
    $oDbResult = $oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if($bRead)
     {
      $asBirthdayBoys = array();
      while($bRead)
      {
        $asBirthdayBoys[$oDbResult->getFieldValue('loginpk',CONST_PHP_VARTYPE_INT)] = $oDbResult->getData();
        $bRead = $oDbResult->readNext();
      }

      //All users
      $asUserList = $this->getUserList(0,true,false);

      $asNames = array();
      foreach($asBirthdayBoys as $nKey => $asValue)
      {
         array_push($asNames,$this->getUserNameFromData($asValue));
       }

      foreach($asUserList as $asUsers)
      {
        if(!in_array(array_keys($asBirthdayBoys), $asUsers))
        {
          $sContent = 'Hello Everyone, <br/>';
          $sContent.= 'Tomorrow is birthday of '.implode(',',$asNames).'  <br/>';
          $sContent.= 'Have fun <br/>';
          $oMail->sendRawEmail('info@bcm.com',$asUsers['email'],'Birthday Tomorrow',$sContent);

          }
        }
      }

     //Send wishes to birthday person on birthdate

    $sQuery = 'SELECT * FROM login WHERE MONTH(birthdate) = "'.$sMonth.'" AND DAY(birthdate) = "'.$sToday.'"';
    $oDbResult = $oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if($bRead)
     {
      $asBirthdayBoys = array();
      while($bRead)
      {
        $asBirthdayBoys[$oDbResult->getFieldValue('loginpk',CONST_PHP_VARTYPE_INT)] = $oDbResult->getData();
        $bRead = $oDbResult->readNext();
        }

        foreach($asBirthdayBoys as $nKey=>$asValue)
        {
          $sName = $this->getUserNameFromData($asValue);

          $sContent = 'Dear '.$sName.' , <br/>';
          $sContent.= 'Happy Birthday !!! We want to wish your a successful year ahead. <br/>';
          $sContent.= 'May all your wishes come true <br/>';
          $sContent.= 'Enjoy!!! <br/>';

          $oMail->sendRawEmail('info@bcm.com',$asValue['email'],'Happy Birthday ',$sContent);

          }
       }
       return true;
    }


   public function getCheckRecentActivity($pnItemPk)
   {
    if(!assert('!empty($pnItemPk) && is_integer($pnItemPk)'))
      return 0;

    $pnLoginPk = $this->getUserPk();
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM login_activity where cp_pk ='.$pnItemPk.' and followerfk!=0 and notifierfk ='.$pnLoginPk.'  AND status=0';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

     if(!$bRead)
      return 0;
     else
       return $oResult->getFieldValue('login_activitypk', CONST_PHP_VARTYPE_INT);
  }

  /**
   * Function to display userlist for other component
   * @return type
   */
  public function getUserPageList()
  {
    $sHTML = $this->_getUserList();
    return $sHTML;
  }

  public function getUpdateRecentActivity($pnPK)
  {
    if(!assert('!empty($pnPK) && is_integer($pnPK)'))
      return false;

     $oDB = CDependency::getComponentByName('database');
     $sQuery = 'UPDATE  login_activity SET status = 1 WHERE login_activitypk ='.$pnPK;
     $oResult = $oDB->ExecuteQuery($sQuery);
     if($oResult)
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

  /**
   * Display all the users from the system
   * @return string HTML
   */
  private function _getUserList()
  {
    /* @var $oDB  CDatabaseEx */
    /* @var $oDbResult  CDbResult */
    /* @var $oDisplay CDisplayEx */
    $oDisplay = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'css/login.form.css'));

    $oDB = CDependency::getComponentByName('database');
    $oDB->dbConnect();

    $sQuery = 'SELECT * FROM `login` WHERE  is_admin <> 1 ORDER BY is_admin, status DESC, firstname asc';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $sHTML = $oDisplay->getTitleLine($this->casText['LOGIN_USER_LIST'], $this->getResourcePath().'/pictures/contact_48.png');
    $sHTML.= $oDisplay->getCarriageReturn();


    $sHTML.= $oDisplay->getBlocStart('',array('style'=>'font-weight:bold;'));
    $sHTML.= $oDisplay->getLink($this->casText['LOGIN_SHOWHIDE_USER'],'javascript:;',array('onclick' =>'$(\'.hiddenUser\').fadeToggle(); '));
    $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getBlocStart('crazy', array('class' => 'ContactList'));
    $sHTML.= $oDisplay->getListStart('');

    //display list header
    $sHTML.= $oDisplay->getListItemStart('',array('class'=>'contactSheetHeader'));
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadRow'));

    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell'));
    $sHTML.= $oDisplay->getSpace(1);

    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell ContactListName'));
    $sHTML.= $oDisplay->getText($this->casText['LOGIN_NAME']);
    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell ContactListPosition'));
    $sHTML.= $oDisplay->getText($this->casText['LOGIN_POSITION']);
    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell ContactListEmail'));
    $sHTML.= $oDisplay->getText($this->casText['LOGIN_EMAIL']);
    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell ContactListPhone'));
    $sHTML.= $oDisplay->getText($this->casText['LOGIN_PHONE']);
    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell ContactListExt'));
    $sHTML.= $oDisplay->getText($this->casText['LOGIN_EXT']);
    $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell ContactListPhone','style'=>'text-align:center;'));
    $sHTML.= $oDisplay->getText($this->casText['LOGIN_ACTION']);
    $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'floathack'));
    $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getListItemEnd('');

    while($bRead)
    {
      if((int)$oDbResult->getFieldValue('status') == 1)
       $sExtraClass = '';
      else
       $sExtraClass = 'hiddenUser';

      $sHTML.= $oDisplay->getListItemStart('',array('class'=>'contactSheet'));
      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListRow '.$sExtraClass ));

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell'));

      if((int)$oDbResult->getFieldValue('is_admin') == 1)
        $sHTML.= $oDisplay->getPicture($this->getResourcePath().'/pictures/admin_user.png');
      else
      {
        if((int)$oDbResult->getFieldValue('status') == 1)
          $sHTML.= $oDisplay->getPicture($this->getResourcePath().'/pictures/active_user.png');
        else
          $sHTML.= $oDisplay->getPicture($this->getResourcePath().'/pictures/inactive_user.png');
       }

      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('',array('class'=>$sExtraClass));

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell ContactListName'));
      $sHTML.= $this->getUserNamefromData($oDbResult->getData(),false,false,false);
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell ContactListPosition'));
      $sHTML.= $oDbResult->getFieldValue('position');
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell ContactListEmail'));
      $sHTML.= $oDisplay->getLink($oDbResult->getFieldValue('email'), 'mailto:'.$oDbResult->getFieldValue('email'));
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell ContactListPhone'));
      $sHTML.= $oDisplay->getLink($oDbResult->getFieldValue('phone'), 'callto&#58;'.$oDbResult->getFieldValue('phone'));
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell ContactListExt'));
      $sHTML.= $oDbResult->getFieldValue('phone_ext');
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell ContactListPhone' ,'style'=>'text-align:center;'));

      $sURL = $oPage->getUrl('login', CONST_ACTION_MANAGE, CONST_LOGIN_TYPE_USER, $oDbResult->getFieldValue('loginpk',CONST_PHP_VARTYPE_INT), array(CONST_URL_ACTION_RETURN => CONST_ACTION_LIST));
      $sHTML.= $oDisplay->getPicture(CONST_PICTURE_EDIT, $this->casText['LOGIN_EDIT_USER'], $sURL);
      $sHTML.= $oDisplay->getSpace(2);

      if($oDbResult->getFieldValue('status')==1)
      {
       $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_DELETE, CONST_LOGIN_TYPE_USER, $oDbResult->getFieldValue('loginpk',CONST_PHP_VARTYPE_INT),array('status'=>$oDbResult->getFieldValue('status')));
       $sPic= $oDisplay->getPicture(CONST_PICTURE_DELETE,$this->casText['LOGIN_DEACTIVATE_USER']);
       $sHTML.= ' '.$oDisplay->getLink($sPic, $sURL, array('onclick' => 'if(!window.confirm(\''.$this->casText['LOGIN_DEACTIVATE'].'\')){ return false; }'));
      }
      else
      {
        $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_DELETE, CONST_LOGIN_TYPE_USER, $oDbResult->getFieldValue('loginpk',CONST_PHP_VARTYPE_INT),array('status'=>$oDbResult->getFieldValue('status')));
        $sPic= $oDisplay->getPicture(CONST_PICTURE_REACTIVATE,$this->casText['LOGIN_REACTIVATE_USER']);
        $sHTML.= ' '.$oDisplay->getLink($sPic, $sURL, array('onclick' => 'if(!window.confirm(\''.$this->casText['LOGIN_REACTIVATE'].'\')){ return false; }'));
      }

      $sHTML.= $oDisplay->getBlocEnd();
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'floathack'));
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocEnd();
     $sHTML.= $oDisplay->getListItemEnd('');

    $bRead = $oDbResult->readNext();
   }

   $sHTML.= $oDisplay->getListEnd('');

   $sHTML.= $oDisplay->getBlocStart('', array('class' => 'floathack'));
   $sHTML.= $oDisplay->getBlocEnd();
   $sHTML.= $oDisplay->getBlocEnd();

   return $sHTML;
  }


  private function _getHomePage()
  {
    $oPortal = CDependency::getComponentByName('portal');
    return $oPortal->getHomePage();
  }
}
