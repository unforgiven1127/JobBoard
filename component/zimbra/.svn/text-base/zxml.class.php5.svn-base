<?php
  class XmlSingleton
  {
    private static $oInstance;

   static function getXmlInstance()
   {
		 if(self::$oInstance === null)
			 self::$oInstance = new CZXml();

		 return self::$oInstance;
	 }
  }


class CZXml
{
  private $cbDebug = false;
  private $casError = array();
  private $csError;
  private $coCurl;
  private $cstack=array();
  private $cstack_ref;
  private $carrOutput = array();
  private $cresParser;
  private $cstrXmlData;
  protected $casChildId = array();
  protected $cbStopSearch = false;
  private $casXml = array();
  private $csUid = '';
  private $csSessionID = '';


  public function clearInstance()
  {
    $this->casError = array();
    $this->csError = '';
    $this->coCurl = null;
    $this->cstack = array();
    $this->cstack_ref = '';
    $this->carrOutput = array();
    $this->cresParser = null;
    $this->cstrXmlData = '';
    $this->casChildId = array();
    $this->cbStopSearch = false;
    $this->casXml = array();
    $this->csUid = '';

    return true;
  }

  public function getErrors($pbAsString = false, $pbEol = '<br />')
  {
    if($pbAsString)
      return '<span style="color: #888; font-style: italic;">'.implode($pbEol, $this->casError).'</span>';

    return $this->casError;
  }

    /**
    * Push the data into the stack
    * @param type $pos
    */
    public function push_pos(&$pos)
    {
      $this->cstack[count($this->cstack)] = &$pos;
      $this->cstack_ref = &$pos;
    }

    /**
    * Pop the data from stack
    */
    public function pop_pos()
    {
      unset($this->cstack[count($this->cstack)-1]);
      $this->cstack_ref = &$this->cstack[count($this->cstack)-1];
    }

    /**
    * Parse XML into array
    * @param string $psInputXML
    * @return array
    */

    public function parse($psInputXML)
    {
      $this->cresParser = xml_parser_create();
      xml_set_object($this->cresParser,$this);
      xml_set_element_handler($this->cresParser, "tagOpen", "tagClosed");
      xml_set_character_data_handler($this->cresParser, "tagData");

      $this->push_pos($this->carrOutput);

      $this->cstrXmlData = xml_parse($this->cresParser,$psInputXML);
      if(!$this->cstrXmlData)
      {
        die(sprintf("XML error: %s at line %d",
        xml_error_string(xml_get_error_code($this->cresParser)),
        xml_get_current_line_number($this->cresParser)));
      }

      xml_parser_free($this->cresParser);
      return $this->carrOutput;
    }

    /**
    * Peform the parsing on the values where the tag is open
    * @param string $sParser
    * @param string $sName
    * @param array $asAttrs
    */

    public function tagOpen($sParser, $sName, $asAttrs)
    {
    if (isset($this->cstack_ref[$sName]))
    {
      if (!isset($this->cstack_ref[$sName][0]))
        {
          $asTmp=$this->cstack_ref[$sName];
          unset($this->cstack_ref[$sName]);
          $this->cstack_ref[$sName][0] = $asTmp;
      }

      $nCount=count($this->cstack_ref[$sName]);
      $this->cstack_ref[$sName][$nCount]=array();

      if (isset($asAttrs))
        $this->cstack_ref[$sName][$nCount]=$asAttrs;

      $this->push_pos($this->cstack_ref[$sName][$nCount]);
    }
    else
      {
        $this->cstack_ref[$sName]=array();

        if (isset($asAttrs))
          $this->cstack_ref[$sName]=$asAttrs;

      $this->push_pos($this->cstack_ref[$sName]);
      }
  }

  /**
    * Parsing on the tag data
    * @param string $sParser
    * @param array $asTagData
    */

    public function tagData($sParser, $asTagData)
    {
      if(trim($asTagData))
      {
        if(isset($this->cstack_ref['DATA']))
          $this->cstack_ref['DATA'] .= $asTagData;
        else
          $this->cstack_ref['DATA'] = $asTagData;
       }
    }

    /**
    * Parsing on the tag close
    * @param string $sParser
    * @param string $psName
    */

    public function tagClosed($sParser, $psName)
    {
      $this->pop_pos();
    }

    /**
    * builOptionString
    * Make an option string that will be placed as attributes inside an XML tag
    * @access  public
    * @param   array $asOptions array of options to be parsed into a string
    * @return  string $sOptionsstring
    */

    public function buildOptionString($pasOptions)
    {
      if(!is_array($pasOptions))
        return '';

      $sOptionsstring = '';
      foreach($pasOptions as $k=>$v)
      {
        $sOptionsstring .= ' '.$k.'="'.$v.'"';
      }
      return $sOptionsstring;
    }

    /**
    * extractErrorCode
    * get the error code out of the XML
    * @access private
    * @param  tring $psXml xml to have the error code pulled from
    * @return int $sSessionId
    */

    private function extractErrorCode($psXml)
    {
        $sSessionId = strstr($psXml, "<Code");
        $sSessionId = strstr($sSessionId, ">");
        $sSessionId = substr($sSessionId, 1, strpos($sSessionId, "<") - 1);
        return $sSessionId;
    }

    /**
    * message
    * if debug is on, show a message
    * @access private
    * @param  string $message message for debug
    */

    private function message($psMessage, $pnType = 1, $psLine = '')
    {
      if($this->cbDebug)
      {
        $sMessage = str_ireplace('&gt;&lt;', "&gt;\n&lt;", $psMessage);

        if($pnType === 0)
          echo $psLine.' Soap error message: '.var_export($sMessage, true);
        else
          echo $psLine.' Soap log: '.var_export($sMessage, true);
      }
    }

    /**
    * soapRequest
    * make a SOAP request to Zimbra server, returns XML response
    * @access public
    * @param  string $body body of page
    * @param  boolean $header
    * @param  boolean $footer
    * @return string $response
    */
    public function soapRequest($psBody, $poCurl, $pbConnected = false, $pbconnecting = false, $psAuthToken = '',$psSessionId='')
    {
      if(!assert('is_string($psBody) && !empty($psBody)'))
        return '';

      if(!assert('!empty($poCurl)'))
        return '';

      if(!empty($poCurl))
        $this->coCurl = $poCurl;

      if(!empty($psSessionId))
        $this->csSessionID = $psSessionId;

      if(!$pbconnecting && !$pbConnected)
      {
        throw new Exception('zimbra.class: soapRequest called without a connection to Zimbra server');
      }

      if(!empty($psAuthToken) && !empty($psSessionId))
      {
          $sHeader = '<context xmlns="urn:zimbra">
                          <sessionId id="'.$this->csSessionID.'">'.$this->csSessionID.'</sessionId>
                          <authToken>'.$psAuthToken.'</authToken>
                      </context>';
      }
      else
        $sHeader  = '<context xmlns="urn:zimbra"><session/></context>';

      $sSoapmessage = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                          <soap:Header>'.$sHeader.'</soap:Header>
                          <soap:Body>'.$psBody.'</soap:Body>
                      </soap:Envelope>';
      //$this->message('SOAP message:<div style="border: 2px solid #999; padding: 5px; maring: 10px;"><pre>'.htmlentities($sSoapmessage).'</pre></div>');

      curl_setopt($this->coCurl, CURLOPT_POSTFIELDS, $sSoapmessage);

      if(!($sResponse = curl_exec($this->coCurl)))
      {
          $this->casError[] = __LINE__.' - curl_exec - ('.curl_errno($this->coCurl).') '.curl_error($this->coCurl);
          return false;
      }

      if(strpos($sResponse,'<soap:Body><soap:Fault>')!==false)
      {
        $sErrorcode = $this->extractErrorCode($sResponse);
        $this->casError[] = __LINE__.' - '.$sErrorcode.': <div style="border: 2px solid #999; padding: 5px; maring: 10px;">'.str_replace('><', '><br/><', htmlentities($sResponse)).'</div>';
        $this->casError[] = __LINE__.' - Soap request: <br /><div style="border: 2px solid #999; padding: 5px; maring: 10px;">'.str_replace('><', '><br/><', htmlentities($sSoapmessage)).'</div>';
        return false;
      }

      $this->cnSoapcalls++;
      return $sResponse;
    }

    /**
     * Add new node
     * @param array $pasNodeData
     * @return string
     */

   public function addNode($pasNodeData)
   {
     if(!assert('is_array($pasNodeData) && !empty($pasNodeData)'))
      return '';

     $this->cbStopSearch = false;
     $this->casXml = $this->insertNode('', $pasNodeData);

     return $this->casChildId[''];
   }

  /**
  * Add child nodes
  * @param string $psParentId
  * @param array $pasNodeData
  * @return string
  */

  public function addChildNode($psParentId, $pasNodeData)
  {
    if(!assert('is_string($psParentId) && !empty($psParentId)'))
     return '';

    if(!assert('is_array($pasNodeData) && !empty($pasNodeData)'))
     return '';

    $this->cbStopSearch = false;
    $this->casXml = $this->insertNode($psParentId, $pasNodeData);

    if(!isset($this->casChildId[$psParentId]))
    {
      assert('false; // no parent node found ['.$psParentId.']');
      return '';
    }
    return $this->casChildId[$psParentId];
   }

  /**
   * Insert new node
   * @param string $psParentId
   * @param array $pasNodeData
   * @param array $pasXml
   * @return array
   */

  public function insertNode($psParentId = '',$pasNodeData, $pasXml = null, $pbRecursive = false)
  {
    if(!assert('is_string($psParentId)'))
     return '';

    if(!assert('is_array($pasNodeData) && !empty($pasNodeData)'))
     return '';

    //flag telling us the node has been inserted, we return the array we've received unchanged
    if($this->cbStopSearch)
      return $pasXml;

    //first call of the function, get the root XML array
    if($pasXml === null)
      $pasXml = $this->casXml;

    if(empty($pasXml) && !empty($psParentId))
    {
      assert('false; // can\'t add a child when there\'s no element in the array');
       return array();
    }

    $sId = uniqid();

    //insert a parent node
    if(empty($psParentId))
    {
      $pasXml[$sId] = $pasNodeData;
      $pasXml[$sId]['child'] = array();
      $this->cbStopSearch = true;
      $this->casChildId[''] = $sId;
       return $pasXml;
    }

    foreach($pasXml as $sKey => $vData)
    {
      //if we ve found the parent node
      if($sKey == $psParentId)
      {
        $pasXml[$sKey]['child'][$sId] = $pasNodeData;
        $pasXml[$sKey]['child'][$sId]['child'] = array();
        $this->cbStopSearch = true;
        $this->casChildId[$sKey] = $sId;
        return $pasXml;
      }

      if(isset($vData['child']) && is_array($vData['child']) &&!empty($vData['child']))
      {
        //re-launch the function on the subsection of the array. It will be eventually updated then returned
        $pasXml[$sKey]['child'] = $this->insertNode($psParentId, $pasNodeData, $vData['child'], true);
       }
     }

    return $pasXml;
  }

  /**
  * Generate xml response from array
  * @param object $poCurl
  * @param boolean $pbConnected
  * @param boolean $pbConnecting
  * @param string $psAuthToken
  * @param string $psSessionId
  * @return string
  */
  public function makeSoapRequest($poCurl, $pbConnected, $pbConnecting, $psAuthToken='', $psSessionId='')
  {
    if(!assert('!empty($poCurl) && !empty($pbConnecting)'))
     return '';

    $asXML = $this->casXml;
    $sXML = $this->makeXML($asXML);
    $this->clearInstance();

    $sResponse =  $this->soapRequest($sXML, $poCurl, $pbConnected, $pbConnecting, $psAuthToken, $psSessionId);
    return $sResponse;
  }

  /**
  * Function to make convert array to XML
  * @param array $asRecord
  * @param string $psXML
  * @return string
  */
   public function makeXML($asRecord,$psXML = null)
   {
     if(!assert('is_array($asRecord) && !empty($asRecord)'))
       return 'Wrong data';

     $sXML = '';

     if(is_array($asRecord) && !empty($asRecord))
     {
       foreach($asRecord as $sKey => $asValue)
       {
         $sXML.= '<'.$asValue['name'].' ';

         if(isset($asValue['@attributes']) && !empty($asValue['@attributes']))
         {
           foreach($asValue['@attributes'] as $sAttKey => $vAtt)
            $sXML.= $sAttKey.'='.$vAtt.' ';
           }

         $sXML.= '>';

         if(isset($asValue['@value']) && !empty($asValue['@value']))
         {
             $sXML.= ''.$asValue['@value'].'';
           }

          if(isset($asValue['child']) && !empty($asValue['child']))
          {
            $sXML.= $this->makeXML($asValue['child'],$sXML);
           }

          $sXML.= '</'.$asValue['name'].'>';
         }
      }
      return $sXML;
    }

}
?>