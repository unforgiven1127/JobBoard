<?php
require_once('component/form/fields/field.class.php5');

class CAutocomplete extends CField
{
  private $casOptionData = array();

  public function __construct($psFieldName, $psFieldType = '', $pasFieldParams = array())
  {
    parent::__construct($psFieldName, $pasFieldParams);
  }

  public function addOption($pasFieldParams)
  {
    if(!assert('is_array($pasFieldParams)'))
      return null;

    $this->casOptionData[] = $pasFieldParams;
    return $this;
  }

  public function getDisplay()
  {
    //------------------------------------
    //Form component manages country and city selectors, for any other element, components have to provide custom url.
    if(!isset($this->casFieldParams['url']) || empty($this->casFieldParams['url']))
    {
      assert('false; //selector fields need an "url" parameter ');
      return '';
    }

    if($this->casFieldParams['url'] == CONST_FORM_SELECTOR_URL_CITY)
    {
      $oForm = CDependency::getComponentByName('form');
      $this->casFieldParams['url'] = $oForm->getCitySelectorAjaxUrl();
      $this->casFieldParams['addurl'] = $oForm->getCitySelectorAddUrl();
    }
    elseif($this->casFieldParams['url'] == CONST_FORM_SELECTOR_URL_COUNTRY)
    {
      $oForm = CDependency::getComponentByName('form');
      $this->casFieldParams['url'] = $oForm->getCountrySelectorAjaxUrl();
      //$this->casFieldParams['addurl'] = $oForm->getCountrySelectorAddUrl();
    }

    //------------------------------------

    if(!isset($this->casFieldParams['id']))
      $this->casFieldParams['id'] = $this->csFieldName.'Id';

    if(!isset($this->casFieldParams['value']))
      $this->casFieldParams['value'] = '';

    if(!isset($this->casFieldParams['nbresult']))
      $this->casFieldParams['nbresult'] = '1';

    if(!isset($this->casFieldParams['class']))
      $this->casFieldParams['class'] = 'autocompleteField';
    else
      $this->casFieldParams['class'].= ' autocompleteField ';

    if(isset($this->casFieldParams['addurl']) && !empty($this->casFieldParams['addurl']))
    {
      $sAddExtraClass = ' formAutocompleteHasAddLink ';
      $sAddLink = '<a href="javascript:;" onclick="AjaxPopup(\''.$this->casFieldParams['addurl'].'\', \'#body\', false, 475, 500, true);"><img src="'.CONST_PICTURE_ADD.'"/></a>';
    }
    else
      $sAddExtraClass = $sAddLink = '';

    $sHTML = '';

    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile('/component/form/resources/css/token-input-mac.css');

    if($this->cbFieldInAjax)
      $sJavascript = '$("#'.$this->casFieldParams['id'].'").tokenInput("'.$this->casFieldParams['url'].'"';
     else
       $sJavascript = '$(document).ready(function(){ $("#'.$this->casFieldParams['id'].'").tokenInput("'.$this->casFieldParams['url'].'"';

     $sJavascript.= ' ,{tokenLimit: "'.$this->casFieldParams['nbresult'].'"';

    if(!empty($this->casOptionData))
    {
      //add all the options
      $asOptions = array();
      foreach($this->casOptionData as $nKey => $asOption)
      {
        if(isset($asOption['label']) && isset($asOption['value']))
          $asOptions[] = '{id:"'.$asOption['value'].'",name:"'.$asOption['label'].'"}';
      }

      $sJavascript.= ', prePopulate:['.implode(',', $asOptions).']';
    }

    if($this->cbFieldInAjax)
      $sJavascript.= '});';
    else
      $sJavascript.= '}); });';
    //$oPage->addCustomJs($sJavascript);
    $sHTML.= '<script language="javascript">'.$sJavascript.' </script>';

    //------------------------
    //add JScontrol classes
    if(isset($this->casFieldParams['required']) && !empty($this->casFieldParams['required']))
      $this->casFieldContol['jsFieldNotEmpty'] = '';

    if(!empty($this->casFieldParams['label']) && $this->isVisible())
      $sHTML.= '<div class="formLabel">'.$this->casFieldParams['label'].'</div>';

    $sHTML.= '<div class="formField formAutocompleteContainer '.$sAddExtraClass.'"><input type="text" name="'.$this->csFieldName.'" ';

    foreach($this->casFieldParams as $sKey => $vValue)
      $sHTML.= ' '.$sKey.'="'.$vValue.'" ';

    if(!empty($this->casFieldContol))
    {
      $sHTML.= ' jsControl="';
      foreach($this->casFieldContol as $sKey => $vValue)
        $sHTML.= $sKey.'@'.$vValue.'|';

      $sHTML.= '" ';
    }

    $sHTML.= ' />';
    $sHTML.= $sAddLink;
    $sHTML.= '</div>';

    return $sHTML;
  }
}