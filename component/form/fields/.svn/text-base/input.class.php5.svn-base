<?php

require_once('component/form/fields/field.class.php5');

class CInput extends CField
{

  public function __construct($psFieldName, $pasFieldParams = array())
  {
    parent::__construct($psFieldName, $pasFieldParams);
  }

  public function isVisible()
  {
    if(isset($this->casFieldParams['type']) && $this->casFieldParams['type'] == 'hidden')
      return false;

    return true;
  }

  public function getDisplay()
  {
    //--------------------------------
    //fetching field parameters

    $bDatePicker = false;
    $bTimePicker = false;
    $asPreviousFile = array();

    if(!isset($this->casFieldParams['id']))
      $this->casFieldParams['id'] = $this->csFieldName.'Id';

    //------------------------
    //add JScontrol classes
    if(isset($this->casFieldParams['required']) && !empty($this->casFieldParams['required']))
      $this->casFieldContol['jsFieldNotEmpty'] = '';

    if(isset($this->casFieldParams['type']))
    {
      $sFieldType = $this->casFieldParams['type'];
      unset($this->casFieldParams['type']);

      switch($sFieldType)
      {
        case 'date':
          $sFieldType = 'text';
          $bDatePicker = true;
          $oPage = CDependency::getComponentByName('page');
          $oPage->addRequiredJsFile(CONST_PATH_JS_DATEPICKER);
          $oPage->addCSSFile(CONST_PATH_CSS_DATEPICKER);
        break;

        case 'time':
          $sFieldType = 'text';
          $bTimePicker = true;
          $oPage = CDependency::getComponentByName('page');
          $oPage->addRequiredJsFile(CONST_PATH_JS_TIMEPICKER);
          $oPage->addCSSFile(CONST_PATH_CSS_DATEPICKER);
       break;
      
        case 'datetime':
          $sFieldType = 'text';
          $bDatePicker = true;
          $bTimePicker = true;
          $oPage = CDependency::getComponentByName('page');
          $oPage->addRequiredJsFile(CONST_PATH_JS_TIMEPICKER);
          $oPage->addCSSFile(CONST_PATH_CSS_DATEPICKER);
        break;

      case 'file':

          if(!isset($this->casFieldParams['maxfilesize']))
            $this->casFieldParams['maxfilesize'] = 10 * 1024 * 1024;

          ini_set('upload_tmp_dir', CONST_PATH_UPLOAD_DIR);

          if(isset($this->casFieldParams['value']) && !empty($this->casFieldParams['value']))
          {
            $asPreviousFile = (array)$this->casFieldParams['value'];
            unset($this->casFieldParams['value']);
          }

          /*$oPage = CDependency::getComponentByName('page');
          $oPage->addRequiredJsFile(CONST_PATH_JS_UPLOAD_PLUGIN);*/
          //$oPage->addCSSFile(CONST_PATH_CSS_UPLOAD_PLUGIN);

          /*$sJS = "$(document).ready(function() ";
          $sJS.= "{ ";
          $sJS.= "    alert('uploader on '+document.getElementById('".$this->casFieldParams['id']."').id); var uploader = new qq.FileUploader({element: document.getElementById('".$this->casFieldParams['id']."'), action: '".CONST_PATH_UPLOAD_DIR."', debug: true }); ";
          $sJS.= "});";
          $oPage->addCustomJS($sJS);*/

        break;
      }

    }
    else
      $sFieldType = 'text';

    if(isset($this->casFieldParams['label']))
    {
      $sLabel = $this->casFieldParams['label'];
      unset($this->casFieldParams['label']);
    }
    else
      $sLabel = '';

    if(!isset($this->casFieldParams['monthNum']))
    {
      $this->casFieldParams['monthNum'] = 2;
    }

    //--------------------------------

    $sHTML = '';

    if(!empty($sLabel) && $this->isVisible())
      $sHTML.= '<div class="formLabel">'.$sLabel.'</div>';
     $sHTML.= '<div class="formField"><input type="'.$sFieldType.'" name="'.$this->csFieldName.'" ';

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

    $nDefaultHour = (int)date('H');
    $nMinute = (int)date('i');
    $nDefaultMinute = ($nMinute - ($nMinute % 5));

    if($bDatePicker && $bTimePicker)
    {
      $sHTML.= '<img src="'.$this->getResourcesPath().'pictures/date-icon.png" onclick="$(\'#'.$this->casFieldParams['id'].'\').focus(); " width="16" height="16" />';

      $sHTML.= '<script> ';
      $sHTML.= '$(function() { $("#'.$this->casFieldParams['id'].'").datetimepicker({  numberOfMonths:'.$this->casFieldParams['monthNum'].' , showButtonPanel: true, changeYear: true, dateFormat: \'yy-mm-dd\', hourGrid: 4, minuteGrid: 10, stepMinute: 5, hour: '.$nDefaultHour.',	minute: '.$nDefaultMinute.'});  });';
      $sHTML.= '</script>';
    }
    elseif($bDatePicker)
    {
      $sHTML.= '<img src="'.$this->getResourcesPath().'pictures/date-icon.png" onclick="$(\'#'.$this->casFieldParams['id'].'\').focus(); " />';

      $sHTML.= '<script> ';
      $sHTML.= '$(function() { $("#'.$this->casFieldParams['id'].'").datepicker({  numberOfMonths: '.$this->casFieldParams['monthNum'].', showButtonPanel: true, changeYear: true, dateFormat: \'yy-mm-dd\'});  });';
      $sHTML.= '</script>';
    }
    elseif($bTimePicker)
    {
      $sHTML.= '<img src="'.$this->getResourcesPath().'pictures/date-icon.png" onclick="$(\'#'.$this->casFieldParams['id'].'\').focus(); " />';

      $sHTML.= '<script> ';
      $sHTML.= '$(function() { $("#'.$this->casFieldParams['id'].'").timepicker({stepMinute: 5, hour: '.$nDefaultHour.',	minute: '.$nDefaultMinute.'});  });';
      $sHTML.= '</script>';
    }

    if(!empty($asPreviousFile))
    {
      $sHTML.= '<div class="file_history">';
      $sHTML.= '<ul>';

      foreach($asPreviousFile as $sFileName)
      {
          $sHTML.= '</li><a href="javascript:;" onclick="alert(\'Sorry, not yet available. Please use the list page action.\');" >'.$sFileName.' <img src="'.CONST_PICTURE_DELETE.'" /></a></li>';
      }
      $sHTML.= '</ul>';
      $sHTML.= '</div>';
    }

    if($sFieldType == 'file')
      $sHTML.= '<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="'.$this->casFieldParams['maxfilesize'].'" />';

    $sHTML.= '</div>';

    return $sHTML;
  }
}