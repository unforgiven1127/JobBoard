<?php
require_once('component/form/fields/field.class.php5');

class CCheckbox extends CField
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
    if(isset($this->casFieldParams['type']) && $this->casFieldParams['type'] == 'hidden')
      return false;

    return true;
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

      if(!$this->isVisible())
        $sClass = ' hidden ';
      else
        $sClass = '';

      if(isset($asOption['textbefore']))
      {
        $sHTML.=' <div class="formLabel '.$sClass.'">';

        if(!empty($sLabel))
          $sHTML.='<label for="'.$sId.'" >'.$sLabel.'</label>';

        $sHTML.= '</div> <div class="formField '.$sClass.'"><input type="checkbox" name="'.$this->csFieldName.'" id="'.$sId.'"';

        foreach($asOption as $sKey => $vValue)
          $sHTML.= ' '.$sKey.'="'.$vValue.'" ';

        $sHTML.= '/></div>';
      }
      else
      {
        $sHTML.=' <div class="formLabel '.$sClass.'">';

        $sHTML.= '</div> <div class="formField '.$sClass.'"><input type="checkbox" name="'.$this->csFieldName.'" id="'.$sId.'"';

        foreach($asOption as $sKey => $vValue)
          $sHTML.= ' '.$sKey.'="'.$vValue.'" ';

        $sHTML.= '/><label for="'.$sId.'">'.$sLabel.'</label></div>';
      }
    }

    return $sHTML;
  }

}