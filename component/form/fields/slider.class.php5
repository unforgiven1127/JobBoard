<?php
require_once('component/form/fields/field.class.php5');

class CSlider extends CField
{
  private $cbIsRange = false;

  public function __construct($psFieldName, $pasFieldParams = array())
  {
    parent::__construct($psFieldName, $pasFieldParams);

    if(!isset($pasFieldParams['step']))
      $this->casFieldParams['step'] = 1;
    else
    {
      if(!is_numeric($pasFieldParams['step']))
      {
        assert('false; // slider step is not a number');
        return false;
      }

      $this->casFieldParams['step'] = (float)$pasFieldParams['step'];
    }

    if(!isset($pasFieldParams['min']) || !isset($pasFieldParams['max']))
    {
      assert('false; // slider missing parameters');
      return false;
    }

    if(isset($pasFieldParams['range']) || isset($this->casFieldParams['value_min']) || isset($this->casFieldParams['value_max']) )
    {
      $this->cbIsRange = true;

      if(isset($pasFieldParams['range']) && !empty($pasFieldParams['range']))
        $this->casFieldParams['range_type'] = strtolower($pasFieldParams['range']);
      else
        $this->casFieldParams['range_type'] = 'true';
    }

    $this->casFieldParams['min'] = (float)$pasFieldParams['min'];
    $this->casFieldParams['max'] = (float)$pasFieldParams['max'];

    if(!isset($this->casFieldParams['value']))
      $this->casFieldParams['value'] = $this->casFieldParams['min'];

    /*if($this->casFieldParams['value'] < $this->casFieldParams['min'])
      $this->casFieldParams['value'] = $this->casFieldParams['min'];*/

    if(!isset($this->casFieldParams['value_min']))
      $this->casFieldParams['value_min'] = $this->casFieldParams['min'];

    if($this->casFieldParams['value_min'] < $this->casFieldParams['min'])
      $this->casFieldParams['value_min'] = $this->casFieldParams['min'];

    if(!isset($this->casFieldParams['value_max']))
      $this->casFieldParams['value_max'] = $this->casFieldParams['max'];

    if($this->casFieldParams['value_max'] > $this->casFieldParams['max'])
      $this->casFieldParams['value_max'] = $this->casFieldParams['max'];

    if(!isset($this->casFieldParams['multiplier']))
      $this->casFieldParams['multiplier'] = 1;


    $this->casFieldParams['has_label'] = false;
    $this->casFieldParams['has_legend'] = false;

    if($this->cbIsRange && isset($pasFieldParams['legend']) &&
       is_array($pasFieldParams['legend']) && !empty($pasFieldParams['legend']))
    {
      $this->casFieldParams['has_legend'] = true;
      $this->casFieldParams['legends'] = $pasFieldParams['legend'];
    }
    else
    {
      if(!isset($this->casFieldParams['value_label']))
        $this->casFieldParams['value_label'] = '';
      else
        $this->casFieldParams['has_label'] = true;

      if(!isset($this->casFieldParams['prefix']))
        $this->casFieldParams['prefix'] = '';
      else
        $this->casFieldParams['has_label'] = true;

      if(!isset($this->casFieldParams['suffix']))
        $this->casFieldParams['suffix'] = '';
      else
        $this->casFieldParams['has_label'] = true;

      if(!isset($this->casFieldParams['value_label_before']))
        $this->casFieldParams['value_label_before'] = false;
      else
        $this->casFieldParams['value_label_before'] = true;
    }

  }

  public function getDisplay()
  {
    $sHTML = '';

    if(!isset($this->casFieldParams['id']))
    {
      $this->casFieldParams['id'] = str_replace('[', '', $this->csFieldName.'Id');
      $this->casFieldParams['id'] = str_replace(']', '', $this->casFieldParams['id']);
    }
    $sSliderId = 'slider_'.$this->casFieldParams['id'];

    if(!empty($this->casFieldParams['label']) && $this->isVisible())
      $sHTML.= '<div class="formLabel">'.$this->casFieldParams['label'].'</div>';


    //grab the field default value depending of the type of slider
    if($this->cbIsRange && $this->casFieldParams['range_type'] != 'min' && $this->casFieldParams['range_type'] != 'max')
    {
      $sFieldValue = $this->casFieldParams['value_min'].'|'.$this->casFieldParams['value_max'];
    }
    else
      $sFieldValue = $this->casFieldParams['value'];



    //TODO: put value on default just here ==>
    $sHTML.= '<div class="formField"><input type="hidden" name="'.$this->csFieldName.'" id="'. $this->casFieldParams['id'].'" value="'.$sFieldValue.'" default="'.$this->casFieldParams['min'].'|'.$this->casFieldParams['max'].'" min="'.$this->casFieldParams['min'].'" max="'.$this->casFieldParams['max'].'" />';

    $sHTML.= '<div class="fieldSliderContainer" ';

    foreach($this->casFieldParams as $sKey => $vValue)
    {
        $sHTML.= ' '.$sKey.'="'.$vValue.'" ';
    }

    if(!empty($this->casFieldContol))
    {
      $sHTML.= ' jsControl="';
      foreach($this->casFieldContol as $sKey => $vValue)
        $sHTML.= $sKey.'@'.$vValue.'|';

      $sHTML.= '" ';
    }

    $sHTML.= '>';

    //include slider files
    $oPage = CDependency::getComponentByName('page');
    $oPage->addcssFile(array('/component/form/resources/css/slider.css'));
    $sExtraJs = '';
    $sField = '';

    //TODO: add multiplier on saved/displayed value

    if($this->cbIsRange)
    {
      if($this->casFieldParams['has_label'])
        $sExtraJs = 'jQuery(this).parent().find(\'.sliderLabelValue\').html(\''.$this->casFieldParams['prefix'].'\'+ui.values[0]+\''.$this->casFieldParams['suffix'].' - '.$this->casFieldParams['prefix'].'\'+ ui.values[1]+\''.$this->casFieldParams['suffix'].'\') ';

      if($this->casFieldParams['has_label'] && $this->casFieldParams['value_label_before'])
        $sField.= '<div class="sliderLabel">'.$this->casFieldParams['value_label'].'<span class="sliderLabelValue">'.$this->casFieldParams['prefix'].$this->casFieldParams['value_min'].$this->casFieldParams['suffix'].' - '.$this->casFieldParams['prefix'].$this->casFieldParams['value_max'].$this->casFieldParams['suffix'].'</span></div>';

      $sField.= '<div id="'.$sSliderId.'" class="sliderBar" >&nbsp;</div>';

      switch($this->casFieldParams['range_type'])
      {
        case 'min':
          $sField.= '<script>jQuery(function(){ jQuery(\'#'.$sSliderId.'\').slider({ range: "min", value: '.$this->casFieldParams['value'].', min: '.$this->casFieldParams['min'].', max: '.$this->casFieldParams['max'].', step:'.$this->casFieldParams['step'].', slide: function(event, ui) {  jQuery(\'#'.$this->casFieldParams['id'].'\').val(ui.value); '.$sExtraJs.' } }); }); </script>';
          break;

        case 'max':
          $sField.= '<script>jQuery(function(){ jQuery(\'#'.$sSliderId.'\').slider({ range: "max", value: '.$this->casFieldParams['value'].', min: '.$this->casFieldParams['min'].', max: '.$this->casFieldParams['max'].', step:'.$this->casFieldParams['step'].', slide: function(event, ui) {  jQuery(\'#'.$this->casFieldParams['id'].'\').val(ui.value); '.$sExtraJs.' } }); }); </script>';
          break;

        default:
          $sField.= '<script>jQuery(function(){ jQuery(\'#'.$sSliderId.'\').slider({ range: true, values: [ '.$this->casFieldParams['value_min'].', '.$this->casFieldParams['value_max'].' ], min: '.$this->casFieldParams['min'].', max: '.$this->casFieldParams['max'].', step:'.$this->casFieldParams['step'].', slide: function(event, ui) {  jQuery(\'#'.$this->casFieldParams['id'].'\').val(ui.values[0]+\'|\'+ ui.values[1]); '.$sExtraJs.' } }); }); </script>';
          break;
      }


      if($this->casFieldParams['has_label'] &&  !$this->casFieldParams['value_label_before'])
        $sField.= '<div class="sliderLabel">'.$this->casFieldParams['value_label'].'<span class="sliderLabelValue">'.$this->casFieldParams['prefix'].$this->casFieldParams['value_min'].$this->casFieldParams['suffix'].' - '.$this->casFieldParams['prefix'].$this->casFieldParams['value_max'].$this->casFieldParams['suffix'].'</span></div>';

      if($this->casFieldParams['has_legend'])
      {
        $sField.= '<div class="sliderLegend"><ul>';

        $nLabel = count($this->casFieldParams['legends']);
        $nWidth = round( (100/$nLabel), 1, PHP_ROUND_HALF_DOWN);

        $nCount = 0;
        foreach($this->casFieldParams['legends'] as $nVal => $sLabel)
        {
          if($nCount == 0 || $nCount == ($nLabel-1))
            $sField.= '<li style="width: '.(0.70*$nWidth).'%;">'.$sLabel.'</li>';
          else
            $sField.= '<li style="width: '.((0.95+(($nLabel-2)/$nLabel)/5) * $nWidth).'%;">'.$sLabel.'</li>';

          $nCount++;
        }

        $sField.= '</ul><div class="floatHack"></div>';
        $sField.= '</div>';
      }
    }
    else
    {
      if($this->casFieldParams['has_label'])
        $sExtraJs = 'jQuery(this).parent().find(\'.sliderLabelValue\').html(\''.$this->casFieldParams['prefix'].'\'+ui.value+\''.$this->casFieldParams['suffix'].'\') ';

      if($this->casFieldParams['has_label'] && $this->casFieldParams['value_label_before'])
        $sField.= '<div class="sliderLabel">'.$this->casFieldParams['value_label'].'<span class="sliderLabelValue">'.$this->casFieldParams['prefix'].$this->casFieldParams['value'].$this->casFieldParams['suffix'].'</span></div>';

      $sField.= '<div id="'.$sSliderId.'" class="sliderBar">&nbsp;</div>';
      $sField.= '<script>jQuery(function(){ jQuery(\'#'.$sSliderId.'\').slider({ range: false, values: '.$this->casFieldParams['value'].', min: '.$this->casFieldParams['min'].', max: '.$this->casFieldParams['max'].', step:'.$this->casFieldParams['step'].', slide: function(event, ui) {  jQuery(\'#'.$this->casFieldParams['id'].'\').val(ui.value); '.$sExtraJs.' } }); }); </script>';

      if($this->casFieldParams['has_label'] && !$this->casFieldParams['value_label_before'])
        $sField.= '<div class="sliderLabel">'.$this->casFieldParams['value_label'].'<span class="sliderLabelValue">'.$this->casFieldParams['prefix'].$this->casFieldParams['value'].$this->casFieldParams['suffix'].'</span></div>';
    }

    $sHTML.= $sField.'<div class="floatHack"></div>';
    $sHTML.= '</div></div>';
    return $sHTML;
  }

}

?>
