<?php

require_once('component/form/form.class.php5');

class CFormEx extends CForm
{
  private $csFormName = '';
  private $cbFormAjax = false;
  private $casFormParams = array();
  private $cbFormHidden = false;
  private $cnFormNbCol = 1;
  private $cbFormNoStyle = false;
  private $cbFormAddButtons = true;
  private $cbFormCancelButton = true;
  private $cbFormCloseButton = false;
  private $casCity = array();
  private $casCountry = array();
  private $cbFormInAjax = false;
  private $csAjaxFormTarget = '';
  private $csAjaxCallback = '';
  private $cbFloatingFields = false;


  private $caoFormFields = array();
  private $casFieldDisplayParams = array();
  private $casMultipleFields = array();

  public function __construct($psFormName = '')
  {
    if(empty($psFormName))
      $this->csFormName = uniqid('form_', true);
    else
      $this->csFormName = $psFormName;

  }



  //====================================================================
  //  Interfaces
  //====================================================================
  public function getAjax()
  {
    $this->_processUrl();

    switch($this->csType)
    {
      case CONST_FORM_TYPE_CITY:
        switch($this->csAction)
        {
          case CONST_ACTION_SEARCH:
            /* custom javascript array of json data encoded in the function */
            return $this->_getSelectorCity();
            break;

          case CONST_ACTION_ADD:
            return json_encode($this->_getCityForm());
            break;

          case CONST_ACTION_SAVEADD:
            return json_encode($this->_getCitySave());
            break;
        }
        break;

      case CONST_FORM_TYPE_COUNTRY:
        switch($this->csAction)
        {
          case CONST_ACTION_SEARCH:
            /* custom javascript array of json data encoded in the function */
            return $this->_getSelectorCountry();
            break;

          case CONST_ACTION_ADD:
            return json_encode($this->_getCountryForm());
            break;
        }
        break;
    }
  }


  //====================================================================
  //  public methods
  //====================================================================



  public function getFormId()
  {
    return $this->csFormName.'Id';
  }



  /*
   * Form managment
   * Goal: implement a totally automatic form creation, control and managment
   *
   */

  public function setFormParams($psFormName = '', $pbAjax = false, $pasParams = array())
  {
    if(!assert('is_string($psFormName)'))
      return false;

    if(!assert('is_bool($pbAjax)'))
      return false;

    if(!assert('is_array($pasParams)'))
      return false;


    if(!empty($psFormName))
      $this->csFormName = $psFormName;

    $this->cbFormAjax = $pbAjax;

    //extra custom parameters
    $this->casFormParams = $pasParams;

    if(!isset($this->casFormParams['method']))
      $this->casFormParams['method'] = 'POST';

    if(isset($this->casFormParams['inajax']) && !empty($this->casFormParams['inajax']))
      $this->cbFormInAjax = true;

    if(isset($this->casFormParams['ajaxTarget']) )
      $this->csAjaxFormTarget = $this->casFormParams['ajaxTarget'];

    if(isset($this->casFormParams['ajaxCallback']) )
      $this->csAjaxCallback = $this->casFormParams['ajaxCallback'];

    if(!isset($this->casFormParams['onBeforeSubmit']) )
      $this->casFormParams['onBeforeSubmit'] = '';

    return true;
  }


  public function setFormDisplayParams($pasParams)
  {
    if(!assert('is_array($pasParams)'))
      return false;

    if(empty($pasParams))
      return true;


    if(isset($pasParams['hidden']))
      $this->cbFormHidden = true;

    if(isset($pasParams['columns']) && is_numeric($pasParams['columns']) &&  (int)$pasParams['columns'] > 0)
      $this->cnFormNbCol = (int)$pasParams['columns'];

    if(isset($pasParams['noStyle']))
      $this->cbFormNoStyle = true;

    if(isset($pasParams['noCancelButton']))
      $this->cbFormCancelButton = false;

    if(isset($pasParams['noCloseButton']))
      $this->cbFormCloseButton = true;

    if(isset($pasParams['noButton']))
      $this->cbFormAddButtons = false;

     if(isset($pasParams['submitButtonHidden']))
      $this->cbSubmitHidden = false;

    if(isset($pasParams['fullFloating']))
      $this->cbFloatingFields = true;

    return true;
  }

  public function addOptionHtml($psFieldName, $pasFieldParams)
  {
    if(!isset($this->caoFormFields[$psFieldName]))
    {
      assert('false; // field doesnt exist');
      return false;
    }

    return $this->caoFormFields[$psFieldName]->addOptionHtml($pasFieldParams);
  }

  public function addOptionHtml($psOptions)
  {
    if(!assert('!empty($psOptions)'))
      return null;

    $this->casOptionHtml[] = $psOptions;
    return $this;
  }

  public function addField($psFieldType, $psFieldName = '', $pasFieldParams = array())
  {
    if(!assert('is_string($psFieldType) && !empty($psFieldType)'))
      return false;

    if(!assert('is_string($psFieldName)'))
      return false;

    if(!assert('is_array($pasFieldParams)'))
      return false;

    if($psFieldType == 'misc' && empty($psFieldName))
      $psFieldName = uniqid();

    $oField = $this->getField($psFieldType, $psFieldName, $pasFieldParams);
    $this->caoFormFields[$psFieldName] = $oField;

    return true;
  }

  public function addSection($psFieldId = '', $pasFieldParams = array())
  {
    if(!assert('is_string($psFieldId)'))
      return false;

    if(!assert('is_array($pasFieldParams)'))
      return false;

    if(empty($psFieldId))
      $psFieldId = uniqid();

    $pasFieldParams['type']= 'open';

    $oField = $this->getField('section', $psFieldId, $pasFieldParams);
    $this->caoFormFields[$psFieldId] = $oField;

    return true;
  }

  public function closeSection($psFieldId = '', $pasFieldParams = array())
  {
    if(!assert('is_string($psFieldId)'))
      return false;

    if(!assert('is_array($pasFieldParams)'))
      return false;

    if(empty($psFieldId))
      $psFieldId = uniqid();

    $pasFieldParams['type']= 'close';

    $oField = $this->getField('section', $psFieldId, $pasFieldParams);
    $this->caoFormFields[$psFieldId] = $oField;

    return true;
  }

  public function addOption($psFieldName, $pasFieldParams)
  {
    if(!isset($this->caoFormFields[$psFieldName]))
    {
      assert('false; // field doesnt exist');
      return false;
    }

    return $this->caoFormFields[$psFieldName]->addOption($pasFieldParams);
  }


  public function setFieldRequired($psFieldName, $pbValue = true)
  {
    if(!isset($this->caoFormFields[$psFieldName]))
    {
      asset('false; // field doesnt exist');
      return false;
    }

    return $this->caoFormFields[$psFieldName]->setRequired($pbValue);
  }

  public function setFieldControl($psFieldName, $pasControl)
  {
    if(!isset($this->caoFormFields[$psFieldName]))
      assert('false; // field doesnt exist');

    return $this->caoFormFields[$psFieldName]->setFieldControl($pasControl);
  }

  public function setFieldDisplayParams($psFieldName, $pasFieldParams)
  {
    if(!isset($this->caoFormFields[$psFieldName]))
      assert('false; // field doesnt exist');

    return $this->casFieldDisplayParams[$psFieldName] = $pasFieldParams;
  }


  /**
   * Return an instance of a form field
   * done that way so we can directly get a field without defining adding a form in the html
   *
   * @param string $psFieldType
   * @param string $psFieldName
   * @param array $pasFieldParams
   */
  public function getField($psFieldType, $psFieldName, $pasFieldParams)
  {
   if(!assert('is_string($psFieldType) && !empty($psFieldType)'))
      return false;

    if(!assert('is_string($psFieldName) && !empty($psFieldType)'))
      return false;

    if(!assert('is_array($pasFieldParams)'))
      return false;


    if(isset($this->caoFormFields[$psFieldName]) && !in_array($psFieldType, array('radio', 'checkbox', 'select')))
    {
      assert('false; // field name ['.$psFieldName.'] already used in this form ['.$this->csFormName.']');
      return false;
    }

    $pasFieldParams = array_merge($pasFieldParams, array('inajax' => $this->cbFormInAjax));

    switch($psFieldType)
    {
      case 'radio':

        if(isset($this->caoMultipleFields[$psFieldName]))
        {
          //get the existing field and add the next checkbok/radio to its parameters
          $oField = $this->caoMultipleFields[$psFieldName]->addOption($pasFieldParams);
        }
        else
        {
          require_once('component/form/fields/radio.class.php5');
          $oField = new CRadio($psFieldName, $pasFieldParams);
          $this->caoMultipleFields[$psFieldName] = $oField;
        }
        break;

      case 'checkbox':
        require_once('component/form/fields/checkbox.class.php5');
        $oField = new CCheckbox($psFieldName, $pasFieldParams);
        break;

      case 'select':
        require_once('component/form/fields/select.class.php5');
        $oField = new CSelect($psFieldName, $pasFieldParams);
        break;

      case 'hidden':
        require_once('component/form/fields/input.class.php5');
        $pasFieldParams['type'] = 'hidden';
        $oField = new CInput($psFieldName, $pasFieldParams);
        break;

      case 'input':
        require_once('component/form/fields/input.class.php5');
        $oField = new CInput($psFieldName, $pasFieldParams);
        break;

      case 'textarea':
        require_once('component/form/fields/textarea.class.php5');
        $oField = new CTextArea($psFieldName, $pasFieldParams);
        break;

      case 'misc':
        require_once('component/form/fields/misc.class.php5');
        $oField = new CMisc($psFieldName, $pasFieldParams);
        break;

      case 'button':
        require_once('component/form/fields/button.class.php5');
        $oField = new CRadio($psFieldName, $pasFieldParams);
        break;


      case 'selector_city':
      case 'selector_country':
      case 'selector':
        require_once('component/form/fields/autocomplete.class.php5');
        $oField = new CAutocomplete($psFieldName, $psFieldType, $pasFieldParams);
        break;

      case 'slider':
        require_once('component/form/fields/slider.class.php5');
        $oField = new CSlider($psFieldName, $pasFieldParams);
        break;

      case 'tree':
        require_once('component/form/fields/tree.class.php5');
        $oField = new CTree($psFieldName, $pasFieldParams);
        break;

      case 'section':
        require_once('component/form/fields/section.class.php5');
        $oField = new CSection($psFieldName, $pasFieldParams);
        break;

      default:
        assert('false; //no ['.$psFieldType.'] field available');
        return null;
    }

    return $oField;
  }


  public function getDisplay()
  {

    $oHTML = CDependency::getComponentByName('display');

    //-----------------------------------
    // Fetching form parameters
    if($this->cbFormNoStyle)
    {
      if(isset($this->casFormParams['style']))
        unset($this->casFormParams['style']);

      if(isset($this->casFormParams['class']))
        unset($this->casFormParams['class']);
    }

    if(isset($this->casFormParams['submitLabel']))
    {
      $sSubmitLabel = $this->casFormParams['submitLabel'];
      unset($this->casFormParams['submitLabel']);
    }
    else
      $sSubmitLabel = 'Validate';


    if(isset($this->casFormParams['onSubmit']))
    {
      $sOnSubmit = $this->casFormParams['onSubmit'];
      unset($this->casFormParams['onSubmit']);
    }
    else
      $sOnSubmit = '';

    if(!isset($this->casFormParams['id']) || empty($this->casFormParams['id']))
      $this->casFormParams['id'] = $this->csFormName.'Id';
    //-----------------------------------
    $sHtml = '';

    //------------------------------
    //adding controls

    /* @var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'css/form.css');
    $oPage->addRequiredJsFile(array('/common/js/jquery-ui.js'));

    $sJavascript = '';
    $sOnClick = '';

    //cbFormInAjax ==> Where the form is displayed
    //if its a form in ajax, we put the js in the submit/onsubmit
    if($this->cbFormAjax)
    {
      if(!$this->cbFormInAjax)
        $sJavascript = "$(document).ready(function() {";

      $sJavascript.= "  $('form[name=".$this->csFormName."]').submit(function(event) ";
      $sJavascript.= "  { ";
    }

    //About how the form is submitted
    if($this->cbFormAjax)
    {
      $sJavascript.= "    event.preventDefault(); ";    //stop the submit event
      $sJavascript.= ' '.$this->casFormParams['onBeforeSubmit'].' ';

      $sJavascript.= "    if(checkForm('".$this->csFormName."')) ";
      $sJavascript.= "    { ";
      $sJavascript.= "      var sURL = $('form[name=".$this->csFormName."]').attr('action'); ";
      $sJavascript.= "      var sFormId = $('form[name=".$this->csFormName."]').attr('id'); ";
      $sJavascript.= "      var sAjaxTarget = '".$this->csAjaxFormTarget."'; ";

      //(psUrl, psLoadingScreen, psFormToSerialize, psZoneToRefresh, pbReloadPage,  pbSynch, psCallback, pbWithAnimation)
      $sJavascript.= "      setCoverScreen(true, true); ";
      // The string in the $this->csAjaxCallback must be string with \ and double quote.
      $sJavascript.= "      setTimeout(\" AjaxRequest('\"+sURL+\"', 'body', '\"+sFormId+\"', '\"+sAjaxTarget+\"', '', '', 'setCoverScreen(false); ".$this->csAjaxCallback." '); \", 350); ";
      $sJavascript.= "    } ";
      $sJavascript.= "    return false; ";    //stop the submit event
    }
    else
    {
      $sJavascript.= ' '.$this->casFormParams['onBeforeSubmit'].' ';
      $sJavascript.= "    if(!checkForm('".$this->csFormName."')) ";
      $sJavascript.= "    { event.preventDefault(); return false; } ";
      $sJavascript.= "    else{ setCoverScreen(true, true); } ";
    }

    //Where the form is displayed
    if($this->cbFormAjax)
    {
      $sJavascript.= "  }); ";

      if(!$this->cbFormInAjax)
        $sJavascript.= "}); ";

      //add the js to the page
      $oPage->addCustomJs($sJavascript);
    }
    else
    {
      if(!$this->cbFormInAjax)
        $sOnSubmit.= ' '.$sJavascript;
    }

    $oPage->addRequiredJsFile($this->getResourcePath().'js/fieldControl.js');

    if($this->cbFormHidden)
       $sHtml.= $oHTML->getBlocStart('',array('style'=>'display:none;'));

    $sHtml.= '<form name="'.$this->csFormName.'" enctype="multipart/form-data" submitAjax="'.(int)$this->cbFormAjax.'" ';
    foreach ($this->casFormParams as $sKey => $vValue)
    {
      $sHtml.= ' '.$sKey.'="'.$vValue.'" ';
    }

    $sHtml.= ' onsubmit="'.$sOnSubmit.'">';
    $sHtml.= $oHTML->getBlocStart($this->csFormName.'InnerId',array('class'=>'innerForm'));


    if(!empty($this->caoFormFields))
    {
      $nField = 0;
      $bNewline = false;
      $bPreviousKIL = false;    //need to know if the previous field was exceptionaly kept in line

      foreach($this->caoFormFields as $sFieldName => $oField)
      {
        if($oField->isSectionStart())
        {
          $sHtml.= $oField->getDisplay();
          $nField = 0;
        }
        else
        {
          if($oField->isSectionEnd())
          {
            $sHtml.= $oField->getDisplay();
            $nField = 0;
          }
          else
          {
            $bKeepInLine = ($this->cbFloatingFields || $oField->isKeepInline() );

            //after a seie of Keep InlineFields, the first normal field triggers a floatHack
            //and reset the counter
            if($bPreviousKIL && !$bKeepInLine)
            {
              $sHtml.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
              $sHtml.= $oHTML->getBlocEnd();

              $nField = 0;
            }

            $bVisible = $oField->isVisible();
            if(!$bVisible)
              $sExtraClass = ' formFieldHidden ';
            else
            {
              $sExtraClass = '';
              $nField++;
            }

            $asFieldParams = CField::getFieldParams($oField);

            //Check if there are custom display parameters and add those on the field container
            if(isset($this->casFieldDisplayParams[$sFieldName]))
              $asDisplayParam = $this->casFieldDisplayParams[$sFieldName];
            else
              $asDisplayParam = array();

            if(isset($asDisplayParam['class']))
            {
              $sExtraClass.= ' '.$asDisplayParam['class'];
              unset($asDisplayParam['class']);
            }

            if(isset($asFieldParams['type']) && $asFieldParams['type'] == 'title')
            {
              $asOption = array_merge(array('class'=>'formFieldContainer formFieldWidth1 '.$sExtraClass), $asDisplayParam);
            }
            else
            {
              $asOption = array_merge(array('class'=>'formFieldContainer formFieldWidth'.$this->cnFormNbCol.' '.$sExtraClass), $asDisplayParam);
            }
            $sHtml.= $oHTML->getBlocStart('',$asOption);

            $sHtml.= $oField->getDisplay();
            $sHtml.= $oHTML->getBlocStart('', array('class'=>'floatHack'));
            $sHtml.= $oHTML->getBlocEnd();
            $sHtml.= $oHTML->getBlocEnd();

            if($oField->isEndingLine())
            {
              $sHtml.= $oHTML->getBlocStart('',array('class'=>'formFieldLineBreaker formFieldWidth1'));
              $sHtml.= '&nbsp;';
              $sHtml.= $oHTML->getBlocEnd();

              $bNewline = true;
            }

            //Add empty lines in the form
            $nLineToSkip = $oField->getSkippingLine();
            for($nCount=0; $nCount < $nLineToSkip; $nCount++)
            {
              $sHtml.= $oHTML->getBlocStart('',array('class'=>'formFieldSeparator formFieldWidth1'));
              $sHtml.= '&nbsp;';
              $sHtml.= $oHTML->getBlocEnd();

              $bNewline = true;
            }

            //detect if we put a float hack div to align fields
            if(!$bKeepInLine && ($bNewline || ($this->cnFormNbCol > 1 && ($nField % $this->cnFormNbCol) == 0)))
            {
              $sHtml.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
              $sHtml.= $oHTML->getBlocEnd();

              $bNewline = false;
            }

            //update the previousKIL for next field
            $bPreviousKIL = $bKeepInLine;
          }
        }
      }
    }

    $sHtml.= $oHTML->getBlocStart('',array('class'=>'formFieldLinebreaker formFieldWidth1'));
    $sHtml.= '&nbsp;';
    $sHtml.= $oHTML->getBlocEnd();

    if($this->cbFormAddButtons)
    {
      $sHtml.= ' <div class="submitBtnClass formFieldWidth1">';
      if(isset($this->cbSubmitHidden))
        $sHtml.= ' <input type="submit" value="'.$sSubmitLabel.'" onclick="'.$sOnClick.'" class="hidden"/>';
      else
        $sHtml.= ' <input type="submit" value="'.$sSubmitLabel.'" onclick="'.$sOnClick.'" />';

      if($this->cbFormCancelButton)
        $sHtml.= ' <input type="button" value="Cancel" onclick="window.history.go(-1)" />';

      if($this->cbFormCloseButton)
        $sHtml.= ' <input type="button" value="Cancel" onclick="removePopup();" />';

      $sHtml.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
      $sHtml.= $oHTML->getBlocEnd();
      $sHtml.= ' </div>';
    }

    $sHtml.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
    $sHtml.= $oHTML->getBlocEnd();

    $sHtml.= $oHTML->getBlocEnd();
    $sHtml.= '</form>';

    if($this->cbFormHidden)
      $sHtml.= $oHTML->getBlocEnd();

    return $sHtml;
  }

  public function addRequiredJsFile($pasJsFile)
  {
    if(!assert('is_array($pasJsFile)') || empty($pasJsFile))
      return false;

    $oPage = CDependency::getComponentByName('page');
    return $oPage->addRequiredJsFile($pasJsFile);
  }

  public function addCustomJs($pasJavascript)
  {
    if(!assert('is_array($pasJavascript)') || empty($pasJavascript))
       return false;

    $oPage = CDependency::getComponentByName('page');
    return $oPage->addCustomJs($pasJavascript);
  }


  //*************************************************************************************************
  //*************************************************************************************************
  //*************************************************************************************************
  //Selectors management methods

  /**
   * return the ajax url used for the country selector
  */
  public function getCountrySelectorAjaxUrl()
  {
    $oPage = CDependency::getComponentByName('page');
    return $oPage->getAjaxurl('form', CONST_ACTION_SEARCH, CONST_FORM_TYPE_COUNTRY);
  }
  /**
   * return the ajax url used to add a country
  */
  public function getCountrySelectorAddUrl()
  {
    $oPage = CDependency::getComponentByName('page');
    return $oPage->getAjaxurl('form', CONST_ACTION_ADD, CONST_FORM_TYPE_COUNTRY);
  }

  /**
   * return the ajax url used for the city selector
  */
  public function getCitySelectorAjaxUrl()
  {
    $oPage = CDependency::getComponentByName('page');
    return $oPage->getAjaxurl('form', CONST_ACTION_SEARCH, CONST_FORM_TYPE_CITY);
  }
  /**
   * return the ajax url used for to add a new city to the list
  */
  public function getCitySelectorAddUrl()
  {
    $oPage = CDependency::getComponentByName('page');
    return $oPage->getAjaxurl('form', CONST_ACTION_ADD, CONST_FORM_TYPE_CITY);
  }

  /**
   * return the name of a country (save the country list for later usage)
   * @param integer $pnCountryFk
   * @return string
   */
  public function getCountryData($pnCountryFk)
  {
    if(!assert('is_integer($pnCountryFk) && !empty($pnCountryFk)'))
      return array();

    if(!empty($this->casCountry))
    {
      if(isset($this->casCountry[$pnCountryFk]))
        return $this->casCountry[$pnCountryFk];
      else
        return array();
    }

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM country WHERE 1';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
    {
      assert('false; //no country found');
      return '';
    }

    while($bRead)
    {
      $this->casCountry[$oDbResult->getFieldValue('countrypk', CONST_PHP_VARTYPE_INT)] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    if(isset($this->casCountry[$pnCountryFk]))
      return $this->casCountry[$pnCountryFk];
    else
      return array();
  }

  public function addCountrySelectorOption($psFieldName, $pnCountryFk)
  {
    if(!assert('is_integer($pnCountryFk) && !empty($pnCountryFk)'))
      return false;

    if(!assert('!empty($psFieldName)'))
      return false;

    $asCountryData = $this->getCountryData($pnCountryFk);

    if(empty($asCountryData))
      $this->addOption($psFieldName, array('label' => ' unknown ', 'value' => 0));

    if(isset($asCountryData['printable_name']) && !empty($asCountryData['printable_name']))
      $sLabel = $asCountryData['printable_name'];
    else
      $sLabel = $asCountryData['country_name'];

    if(isset($asCountryData['iso3']) && !empty($asCountryData['iso3']))
      $sLabel.= ' - '.$asCountryData['iso3'];

    return $this->addOption($psFieldName, array('label' => $sLabel, 'value' => $pnCountryFk));
  }

  private function _getSelectorCountry()
  {
    $sSearch = getValue('q');
    if(empty($sSearch))
      return json_encode(array());

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM country WHERE lower(country_name) LIKE '.$oDB->dbEscapeString('%'.strtolower($sSearch).'%').' OR lower(printable_name) LIKE '.$oDB->dbEscapeString('%'.strtolower($sSearch).'%').' OR lower(iso3) = '.$oDB->dbEscapeString(strtolower($sSearch)).' ORDER BY printable_name ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return json_encode(array());

    $asJsonData = array();
    while($bRead)
    {
      $asData['id'] = $oDbResult->getFieldValue('countrypk');
      $sPrintableName = $oDbResult->getFieldValue('printable_name');
      $sName = $oDbResult->getFieldValue('country_name');
      $sIso = $oDbResult->getFieldValue('iso3');

      if(!empty($sPrintableName))
        $asData['name'] = $sPrintableName;
      else
        $asData['name'] = $sName;

      if(!empty($sIso))
        $asData['name'].= ' - '.$sIso;

      $asJsonData[] = json_encode($asData);
      $bRead = $oDbResult->readNext();
    }
    echo '['.implode(',', $asJsonData).']';
  }

  /**
   * return the name of a city
   * @param integer $pnCityFk
   * @return string
   */
  public function getCityData($pnCityFk)
  {
    if(!assert('is_integer($pnCityFk) && !empty($pnCityFk)'))
      return array();

    if(!empty($this->casCity) && isset($this->casCity[$pnCityFk]))
      return $this->casCity[$pnCityFk];


    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM city WHERE citypk = '.$pnCityFk;
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
    {
      assert('false; //no city found');
      return array();
    }

    $nCityPk = $oDbResult->getFieldValue('citypk', CONST_PHP_VARTYPE_INT);
    $this->casCity[$nCityPk] = $oDbResult->getData();

    if(isset($this->casCity[$pnCityFk]))
      return $this->casCity[$pnCityFk];
    else
      return array();
  }

  public function addCitySelectorOption($psFieldName, $pnCityFk)
  {
    if(!assert('is_integer($pnCityFk) && !empty($pnCityFk)'))
      return false;

    if(!assert('!empty($psFieldName)'))
      return false;

    $asCityData = $this->getCityData($pnCityFk);

     $sFullname = ucfirst($asCityData['EngLocal']).' '.ucfirst($asCityData['EngCity']);
      $sFullname.= ' '.ucfirst($asCityData['EngStreet']);
      $asData['name'] = $sFullname.' - '.$asCityData['postcode'].' - '.$asCityData['KanjiCity'];

    if(empty($asCityData))
      $this->addOption($psFieldName, array('label' => ' unknown ', 'value' => 0));
    else
      $sLabel = $asData['name'];

    return $this->addOption($psFieldName, array('label' => $sLabel, 'value' => $pnCityFk));
  }


  private function _getSelectorCity()
  {
    $sSearch = getValue('q');
    if(empty($sSearch))
      return json_encode(array());

    $sCleanSearch = str_replace('-', '', $sSearch);
    $sCleanSearch = str_replace(' ', '', $sCleanSearch);
    $oDB = CDependency::getComponentByName('database');

    if(preg_match('/^[0-9]{1,9}$/', $sCleanSearch))
    {
      $sQuery = 'SELECT * FROM city WHERE postcode LIKE '.$oDB->dbEscapeString($sCleanSearch.'%').' ORDER BY postcode, EngLocal, EngCity LIMIT 200';
    }
    else
    {
      $sQuery = 'SELECT * FROM city WHERE EngLocal LIKE '.$oDB->dbEscapeString('%'.$sSearch.'%').' OR EngCity LIKE '.$oDB->dbEscapeString('%'.$sSearch.'%').' OR EngStreet LIKE '.$oDB->dbEscapeString('%'.$sSearch.'%').' ';
      $sQuery.= ' OR KanaLocal LIKE '.$oDB->dbEscapeString('%'.$sSearch.'%').' OR KanaCity LIKE '.$oDB->dbEscapeString('%'.$sSearch.'%').' OR KanaStreet LIKE '.$oDB->dbEscapeString('%'.$sSearch.'%').' ';
      $sQuery.= ' OR KanjiLocal LIKE '.$oDB->dbEscapeString('%'.$sSearch.'%').' OR KanjiCity LIKE '.$oDB->dbEscapeString('%'.$sSearch.'%').' OR KanjiStreet LIKE '.$oDB->dbEscapeString('%'.$sSearch.'%').' ';
      $sQuery.= ' ORDER BY postcode, EngLocal, EngCity LIMIT 200';
    }

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return json_encode(array());

    $asJsonData = array();
    while($bRead)
    {
      $asData['id'] = $oDbResult->getFieldValue('citypk');
      $sFullname = ucfirst($oDbResult->getFieldValue('EngLocal')).' '.ucfirst($oDbResult->getFieldValue('EngCity'));
      $sFullname.= ' '.ucfirst($oDbResult->getFieldValue('EngStreet'));


      $asData['name'] = $sFullname.' - '.$oDbResult->getFieldValue('postcode').' - '.$oDbResult->getFieldValue('KanjiCity');

      $asJsonData[] = json_encode($asData);
      $bRead = $oDbResult->readNext();
    }

    if(count($asJsonData) >= 200)
    {
      $asData['id'] = 0;
      $asData['name'] = 'More than 200 results...';
      $asJsonData[] = json_encode($asData);
    }
    echo '['.implode(',', $asJsonData).']';
  }


  private function _getCityForm()
  {
    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $sURL = $oPage->getAjaxUrl('form', CONST_ACTION_SAVEADD, CONST_FORM_TYPE_CITY);
    $sHTML= $oHTML->getBlocStart();

    //div including the form
    $sHTML.= $oHTML->getBlocStart('');

    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('cityAddForm');
    $sFormId = $oForm->getFormId();

    //TODO
    //Ajax form doesnt work since we re already in ajax popup!!
    //make it manually for now
    $sJs = $oHTML-> getAjaxJs( $sURL, 'body', $sFormId);
    $oForm->setFormParams('', false, array('action' => '', 'onsubmit' => 'event.preventDefault(); '.$sJs));
    $oForm->setFormDisplayParams(array('noCancelButton' => 1));


    $oForm->addField('misc', '', array('type' => 'text','text'=> '<br/><span class="h4">Add a new city</span><hr /><br />'));

    $oForm->addField('input', 'perfecture_name', array('label'=>'Perfecture'));
    $oForm->setFieldControl('perfecture_name', array('jsFieldMinSize' => '3', 'jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

    $oForm->addField('input', 'name_city', array('label'=> 'City name'));
    $oForm->setFieldControl('name_city', array('jsFieldMinSize' => '3', 'jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

    $oForm->addField('input', 'sub_city', array('label'=> 'Sub-City Name'));
    $oForm->setFieldControl('sub_city', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255));

    $oForm->addField('input', 'postcode', array('label'=> 'Postcode'));
    $oForm->setFieldControl('postcode', array('jsFieldTypeIntegerPositive' => '', 'jsFieldMinValue' => 1000));

    $oForm->addField('selector_country', 'countrykey', array('label'=> 'Country', 'url' => CONST_FORM_SELECTOR_URL_COUNTRY));
    $oForm->setFieldControl('countrykey', array('jsFieldTypeIntegerPositive' => ''));

    $oForm->addField('misc', '', array('type'=> 'br'));
    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return array('data' => $sHTML);
  }

  private function _getCitySave()
  {

    $sNameCity = getValue('name_city');
    if(empty($sNameCity))
      return array('alert' => 'City name is required.');

    $sPostcode = getValue('postcode');
    if(empty($sPostcode))
      return array('alert' => 'Postcode is required.');

    $sNamePerfecture = getValue('perfecture_name');
    $sNameSubCity = getValue('sub_city');
    $countryfk = (int)getValue('countrykey', 0);

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'INSERT INTO city (EngLocal, EngCity, EngStreet,postcode, countryfk) VALUES ';
    $sQuery.= '('.$oDB->dbEscapeString($sNamePerfecture).', '.$oDB->dbEscapeString($sNameCity).','.$oDB->dbEscapeString($sNameSubCity).', ';
    $sQuery.= $oDB->dbEscapeString($sPostcode).', '.$oDB->dbEscapeString($countryfk).') ';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    if(!$oDbResult)
      return array('error' => 'Could not add the city.['.$sQuery.']');

    return array('notice' => 'City added successfully', 'action' => 'removePopup()');
  }

  private function _getCountryForm()
  {
    return array('data' => 'No need for now, got already most of them :)');
  }



}
