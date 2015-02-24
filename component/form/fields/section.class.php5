<?php
require_once('component/form/fields/field.class.php5');

class CSection extends CField
{
  private $casOptionData = array();

  public function __construct($psFieldName, $pasFieldParams = array())
  {
    parent::__construct($psFieldName, $pasFieldParams);

    $this->casOptionData[] = $pasFieldParams;
  }

  public function addOption($pasFieldParams)
  {
    $this->casOptionData[] = $pasFieldParams;
    return $this;
  }

  public function isVisible()
  {
    return true;
  }

  public function getDisplay()
  {
    if($this->isSectionStart())
    {
     foreach($this->casOptionData as $nKey => $asOption)
     {
      $sHTML = '<div class="floatHack"></div><div ';
      
        foreach($asOption as $sKey => $vValue)
          $sHTML.= ' '.$sKey.'="'.$vValue.'" ';
        
         $sHTML.= '/></div>';
      }
      
    }

    if($this->isSectionEnd())
      return '</div><div class="floatHack"></div>';

    return '';
  }

  public function isSectionStart()
  {
    if(isset($this->casOptionData['type']) && $this->casOptionData['type'] =='open')
      return true;
  }

  public function isSectionEnd()
  {
     if(isset($this->casOptionData['type']) && $this->casOptionData['type'] =='close')
      return true;
  }

}