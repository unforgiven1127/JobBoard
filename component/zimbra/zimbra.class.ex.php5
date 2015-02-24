<?php

require_once('component/zimbra/zimbra.class.php5');
require_once('component/zimbra/zxml.class.php5');

class CZimbraEx extends CZimbra
{
  protected $coCurl;

  protected $cbConnected = false;
  protected static $cnSoapcalls = 0;
  protected $csPreAuthKey;

  protected $casLcached_assets = array();
  protected $cnPreauthExpiration = 0;
  protected $cbDev;
  protected $csProtocol;
  protected $csServer;
  protected $csPath = '/service/soap';
  protected $csTimestamp;
  protected $cnCalendars;

  protected $casAccountInfo = array();
  protected $cbAdmin = false;

  protected $csAuthtoken;
  protected $csSessionID;
  protected $cvIdm;
  protected $csUsername;
  protected $coZCalObj;
  protected $coXMLObj;
  protected $casError = array();
  protected $casZUser = array();


  public function __construct()
  {
    //global mail server authkey: generated manually on the server, allow a global access to all the soap api
    //the server needs to be restarted verytime we generate a new key
    $this->csPreAuthKey = 'd03c6db8b349a26a1cb144cc39d714ee98329e53694f60960408d9cc8f8b9675';
    $this->csProtocol = 'https://';

    //$this->sServer =  'imap.bulbouscell.com';
    //$this->Email =  'imap.bulbouscell.com';
    $this->csServer =  'mail.bulbouscell.com';
    $this->csEmail =  'bulbouscell.com';

    //when requesting a authentification, we need to pass the current time and the lifespan of the requested session
    // -300 minutes to avoid issues related to time difference between server, *1000 zimbra format
    //
    //$this->cnTimestamp = ((time()-300)*1000);
    $this->cnTimestamp = (time()*1000);
    $this->cnPreauthExpiration = ((time()+1200)*1000);

    //laod Data of zimbra users
    $this->casZUser = $this->_getZUserData(0, true);

    return true;
  }

  public function getErrors($pbAsString = false, $pbEol = '<br />')
  {
    if($pbAsString)
      return '<span style="color: #888; font-style: italic;">'.implode($pbEol, $this->casError).'</span>';

    return $this->casError;
  }


  public function initialize($psUsername = '', $pasParameter = array())
  {
    if(!assert('is_string($psUsername)'))
      return false;

    if(!assert('is_array($pasParameter)'))
      return false;

    //reset everything for a new connection
    $this->casError = array();
    $this->coCurl = null;
    $this->cbConnected = false;
    $this->cnSoapcalls = 0;
    $this->casAccountInfo = array();
    $this->cbAdmin = false;
    $this->csAuthtoken = '';
    $this->cvIdm = null;
    $this->csUsername = '';
    $this->coZCalObj = null;
    $this->coXMLObj = null;
    $this->casError = array();

    //Load zimbra with all the variables from the original object
    if(!empty($pasParameter))
    {
      foreach($pasParameter as $sVarname => $vValue)
      {
        if(!is_object($vValue))
          $this->$sVarname = $vValue;
      }

      //erase the XML obj in case it was previously used
      $oXml = XmlSingleton::getXmlInstance();
      //$oXml->clearInstance();
    }
    else
    {
      if(!assert('!empty($this->csUsername) || !empty($psUsername)'))
        return false;

      if(!empty($psUsername))
        $this->csUsername =  $psUsername;

      $sToken = $this->getUserAuthenticate();
      if(empty($sToken))
      {
        $this->casError[] = __LINE__.' - No token';
        return false;
      }
    }
    return true;
  }


   public function getHtml()
   {
      $this->_processUrl();

      $sUser = $this->getCurrentUser();
      $this->initialize($sUser);

      switch($this->csType)
      {
        case 'calendar':
        case CONST_ZCAL_EVENT:

          if(empty($this->coZCalObj))
            $this->getZimbraCalendar();

          switch($this->csAction)
          {
            case CONST_ACTION_ADD:
              return $this->coZCalObj->getCalendarForm();
            break;

            case CONST_ACTION_VIEW:
              return $this->coZCalObj->_getViewCalendar();
            break;
          }
        break;
      }
    }

   public function getAjax()
   {
     $this->_processUrl();

     $sUser = $this->getCurrentUser();
     $this->initialize($sUser);

      switch($this->csType)
      {
        case 'calendar':

          if(empty($this->coZCalObj))
            $this->getZimbraCalendar();

          switch($this->csAction)
          {
            case 'check':
              return json_encode($this->coZCalObj->_getCheckStatus());
            break;

            case  CONST_ACTION_SAVEADD:
              return  json_encode($this->coZCalObj->_getSaveAppointment());
            break;
          }
        break;

        case CONST_ZCAL_EVENT:

          if(empty($this->coZCalObj))
            $this->getZimbraCalendar();

          switch($this->csAction)
          {
            case CONST_ACTION_ADD:
              $oPage = CDependency::getComponentByName('page');
              $asJsonData = $oPage->getAjaxExtraContent(array('data' => $this->coZCalObj->getCalendarForm(true)));
              return json_encode($asJsonData);
              break;

            case CONST_ACTION_VIEW:
              return $this->coZCalObj->_getLocalEventsForCalendar();
              break;
          }
        break;
      }
    }

    /**
     * Get Current User i.e Organizer
     * @return user id
     */

     public function getCurrentUser()
     {
       $sUser = getValue('user');

       if(isset($sUser) && !empty($sUser))
         return $sUser;

        $oLogin = CDependency::getComponentByName('login');
        $asUser = $oLogin->getUserDataByPk($oLogin->getUserPk());

        return $asUser['id'];
     }

   /**
    * Function to instantiate the calendar object
    * @return object \CZCalendar
    */

    public function getZimbraCalendar()
    {
       require_once('component/zimbra/resources/class/zcalendar.class.php5');

       $asParams = get_object_vars($this);
       $oCal = new CZCalendar();
       $oCal->initialize('', &$asParams);
       $this->coZCalObj = $oCal;

       return $oCal;
     }

  /**
   * Cron job functions
   * @return boolean
   */

   public function getCronJob()
   {
      echo '<fieldset><legend>Zimbra cron options:</legend> &refreshShared=1  to refresh Global shared calendar <br /> &refreshBcm=1  to refresh the local database events </fieldset>';

      $this->getZimbraCalendar();

      if(getValue('refreshBcm', 0))
        $this->coZCalObj->syncWithZimbra(false, true);


      if(getValue('refreshShared', 0))
        $this->coZCalObj->syncWithZimbra(true, false);

      return true;
   }

    /**
    * _getPreAuth
    * @access private
    * @param  string $sUsername username
    * @return string preauthentication key in hmacsha1 format
    */
    private function _getPreAuth($psUsername)
    {
      if(!assert('is_string($psUsername)'))
        return '';

      $sAccount_identifier = $psUsername.'@'.$this->csServer;
      $sByvalue = 'name';
      $nExpires = $this->cnPreauthExpiration;
      $nTimestamp = $this->cnTimestamp;
      $sString = $sAccount_identifier.'|'.$sByvalue.'|'.$nExpires.'|'.$nTimestamp;

      return $this->_getHMACKey($this->csPreAuthKey,$sString);
    }


    /**
    * _getHMACKey
    * generate an HMAC using SHA1, required for preauth
    * @access private
    * @param  int $nKey encryption key
    * @param  string $sData data to encrypt
    * @return string converted to hmac sha1 format
    */
    private function _getHMACKey($pnKey='',$psData='')
    {
      $nBlocksize=64;
      $sHashfunc='sha1';

      if(strlen($pnKey)>$nBlocksize)
        $nkey=pack('H*', $sHashfunc($nkey));

      $nkey=str_pad($pnKey,$nBlocksize,chr(0x00));
      $sIpad=str_repeat(chr(0x36),$nBlocksize);
      $sOpad=str_repeat(chr(0x5c),$nBlocksize);
      $sHmac = pack('H*',$sHashfunc(($nkey^$sOpad).pack('H*',$sHashfunc(($nkey^$sIpad).$psData))));

      return bin2hex($sHmac);
    }

    /**
    * getUserAuthenticate
    * User Authentication function
    * connect to the Zimbra SOAP service
    * @access protected
    * @return array associative array of account information
    */

    protected function getUserAuthenticate()
    {
      if($this->cbConnected)
         return $this->csAuthtoken;

      $sPreauth = $this->_getPreAuth($this->csUsername);
      if(empty($sPreauth))
      {
        $this->casError[] = __LINE__.' - No preauth available for ['.$this->csUsername.']';
        return false;
      }

      $this->coCurl = curl_init();
      curl_setopt($this->coCurl, CURLOPT_URL, $this->csProtocol.$this->csServer.$this->csPath);
      curl_setopt($this->coCurl, CURLOPT_POST,           true);
      curl_setopt($this->coCurl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($this->coCurl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($this->coCurl, CURLOPT_SSL_VERIFYHOST, false);



      $oXml = XmlSingleton::getXmlInstance();
      $sId = $oXml->addNode(array('name'=>'AuthRequest','@attributes' =>array('xmlns'=>'"urn:zimbraAccount"')));
      $oXml->addChildNode($sId, array('name'=>'account','@attributes' => array('by'=>'"name"'),'@value'=>''.$this->csUsername.'@'.$this->csServer.''));
      $oXml->addChildNode($sId, array('name'=>'preauth','@attributes'=> array('timestamp' =>'"'.$this->cnTimestamp.'"',' expires'=>'"'.$this->cnPreauthExpiration.'"'),'@value'=>$sPreauth));

      $sResponse = $oXml->makeSoapRequest($this->coCurl, $this->cbConnected, true);
      if(empty($sResponse))
      {
        $this->casError[] = __LINE__.' - No authentication response at ['.$this->csProtocol.$this->csServer.$this->csPath.']';
        $this->casError[] = __LINE__.' - oXml error: '.$oXml->getErrors(true);
        $this->bconnected = false;
        return false;
      }

      $asTmp = $oXml->parse($sResponse);
      if(empty($asTmp))
      {
        $this->casError[] = __LINE__.' - Can\'t parse authenticate response.';
        return false;
      }


      $asEnvelop = $asTmp['SOAP:ENVELOPE'];

      if(!isset($asEnvelop['SOAP:HEADER']))
        $asHeader = $asEnvelop[0]['SOAP:HEADER'];
      else
        $asHeader = $asEnvelop['SOAP:HEADER'];

      $asContext = $asHeader['CONTEXT'];
      if(!empty($asContext))
      {
        $this->csAuthtoken = $this->extractAuthToken($sResponse);
        $this->csSessionID = $asContext['SESSION']['ID'];
      }

      $sResponse = '';
      if(!empty($this->csAuthtoken))
      {
        $this->cbconnected = true;
        return $this->csAuthtoken;
      }

      $this->casError[] = __LINE__.' - Couldn\'t extract authkey from response.';
      $this->cbconnected = false;
      return false;
    }

    /**
  * extractAuthToken
  * get the Auth Token out of the XML
  * @access  protected
  * @param string $psXml xml to have the auth token pulled from
  * @return string $sAuthtoken
  */

  protected function extractAuthToken($psXml)
  {
    if(!assert('is_string($psXml) && !empty($psXml)'))
    return '';

    $sAuthtoken = strstr($psXml, "<authToken");
    $sAuthtoken = strstr($sAuthtoken, ">");
    $sAuthtoken = substr($sAuthtoken, 1, strpos($sAuthtoken, "<") - 1);
    return $sAuthtoken;
  }

  /**
   *convert a zimbra formated date to american/mysql format
   * @param: $psZimbraDate a string
   * @param: $psFormat date format
   * return a string
  */
  public function convertZimbraDate($psZimbraDate, $psFormat = 'Y-m-d H:i:s')
  {
    if(!assert('!empty($psZimbraDate) && (strlen($psZimbraDate) >=15 || strlen($psZimbraDate) == 8)'))
    {
      dump($psZimbraDate);
      return '';
    }

    if(substr($psZimbraDate, -1, 1) == 'Z')
     $psZimbraDate = substr($psZimbraDate, 0, (strlen($psZimbraDate)-1));

    return date($psFormat, strtotime($psZimbraDate));
  }


  /**
   *convert american/mysql date to zimbra format
   * @param: $psZimbraDate a string
   * @param: $psFormat date format
   * return a string
  */
  public function convertToZimbraDate($psDate)
  {
    if(!assert('!empty($psDate)'))
      return '';

    return date('Ymd\THis', strtotime($psDate));
  }

  protected function _getZUserData($pvPk = 0, $pbOnlyActive = true)
  {
    if(!assert('is_integer($pvPk) || is_array($pvPk)'))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM `zimbra_user` ';

    if($pbOnlyActive)
       $sQuery.= ' WHERE status > 0';
    else
      $sQuery.= ' WHERE 1 ';

    if(!empty($pvPk))
    {
      if(is_integer($pvPk))
        $sQuery.= ' AND loginpk = '.$pvPk;
      else
        $sQuery.= ' AND loginpk IN ('.implode(',', $pvPk).') ';
    }

    $sQuery.= ' ORDER BY username';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
      return array();

    $asResult = array();
    while($bRead)
    {
      $nLoginfk = $oDbResult->getFieldValue('loginfk', CONST_PHP_VARTYPE_INT);
      $asResult[$nLoginfk] = $oDbResult->getData();
      $asResult[$nLoginfk]['calendars'] = explode(',', $asResult[$nLoginfk]['calendarIds']);

      $bRead = $oDbResult->readNext();
    }

    return $asResult;
  }

}

?>