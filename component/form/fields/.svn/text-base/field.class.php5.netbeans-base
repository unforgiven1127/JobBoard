<?php

class CField
{
  protected $csFieldName;
  protected $casFieldParams;

  protected $casFieldControls = array('jsFieldNotEmpty', 'jsFieldMinSize', 'jsFieldMaxSize', 'jsFieldMinValue', 'jsFieldMaxValue', 'jsFieldTypeInteger', 'jsFieldDate', 'jsFieldTypeIntegerPositive', 'jsFieldTypeIntegerNegative', 'jsFieldTypeFloat', 'jsFieldTypeCurrency', 'jsFieldTypeEmail', 'jsFieldGreaterThan', 'jsFieldSmallerThan', 'jsFieldTypeUrl');
  protected $casFieldContol = array();

  protected $cbIsEndingLine = false;
  protected $cnSkippingLine = 0;

  protected $cbFieldInAjax = false;


  public function __construct($psFieldName, $pasFieldParams = array())
  {
    if(!assert('is_string($psFieldName)'))
      return false;

    if(!assert('is_array($pasFieldParams)'))
     return false;

    if(isset($pasFieldParams['required']))
      $this->cbFieldRequired = (bool)$pasFieldParams['required'];

    if(isset($pasFieldParams['inajax']))
      $this->cbFieldInAjax = (bool)$pasFieldParams['inajax'];

    $this->csFieldName = $psFieldName;
    $this->casFieldParams = $pasFieldParams;
  }

 public function getFieldParams($poField)
 {
     if(!empty($poField->casFieldParams))
       return $poField->casFieldParams;
  }

  public function getDisplay()
  {
    assert('false; //you must use a derived field class, the parent has no display');
    return '';
  }


  //-------------------------------------------------------
  //Used to define how the fields are dispalyed in the form



  public function isVisible()
  {
    //to be redifined by any field that can be hidden / invisible
    return true;
  }

  public function isEndingLine()
  {
    return $this->cbIsEndingLine;
  }

  public function isKeepInline()
  {
    if(isset($this->casFieldParams['keepNextInline']) && !empty($this->casFieldParams['keepNextInline']))
      return true;

    return false;
  }

  public function getSkippingLine()
  {
    return $this->cnSkippingLine;
  }

  public function getResourcesPath()
  {
    return '/component/form/resources/';
  }

  public function setRequired($pbValue)
  {
    return $this->casFieldParams['required'] = (int)$pbValue;
  }

  public function setFieldControl($pasControl)
  {
    if(!assert('is_array($pasControl)') || empty($pasControl))
      return false;

    foreach($pasControl as $sControl => $sValue)
    {
      if(!in_array($sControl, $this->casFieldControls))
        assert('false; // control unregistered ('.$sControl.')');
      else
         $this->casFieldContol[$sControl] = $sValue;
    }

    return true;
  }

  public function isSectionStart()
  {
    return false;
  }

  public function isSectionEnd()
  {
    return false;
  }
}
?>