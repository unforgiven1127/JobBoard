<?php

require_once('component/form/fields/field.class.php5');

class CRadio extends CField
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
  
  
  public function getDisplay()
  {
    $sHTML = '';
    
    foreach($this->casOptionData as $nKey => $asOption)
    {
      
      if(isset($asOption['label']))
      {
        $sLabel = $asOption['label'];
        unset($asOption['label']);
      }
      else
        $sLabel = '';
      
      if(isset($asOption['id']))
      {
        $sId = $asOption['id'];
        unset($asOption['id']);
      }
      else
        $sId = $this->csFieldName.'_'.$nKey.'_'.'Id';
      
      $sHTML.= '<input type="radio" id="'.$sId.'" name = "'.$this->csFieldName.'"';
      
      foreach($asOption as $sKey => $vValue)
        $sHTML.= ' '.$sKey.'="'.$vValue.'" ';
      
      $sHTML.= '/>';
      
      if(!empty($sLabel))
        $sHTML.='<label for="'.$sId.'">'.$sLabel.'</label>'; 
    }
  
    return $sHTML;
  }
  
}