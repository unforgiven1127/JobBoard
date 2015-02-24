<?php

require_once('component/form/fields/field.class.php5');

class CMisc extends CField
{

  public function __construct($psFieldName = '', $pasFieldParams = array())
  {
    parent::__construct($psFieldName, $pasFieldParams);
  }

  public function isVisible()
  {
    if(isset($this->casFieldParams['type']) && !in_array($this->casFieldParams['type'], array('text', 'password', 'title')))
      return false;

    return true;
  }

  public function getDisplay()
  {
    //--------------------------------
    //fetching field parameters

    if(!isset($this->casFieldParams['id']))
      $this->casFieldParams['id'] = $this->csFieldName.'Id';

    if(isset($this->casFieldParams['type']))
    {
      $sFieldType = $this->casFieldParams['type'];
      unset($this->casFieldParams['type']);
    }
    else
      $sFieldType = '';


     //--------------------------------

    switch($sFieldType)
    {
      case 'br':

        if(isset($this->casFieldParams['number']))
        {
          $nCount = (int)$this->casFieldParams['number'];
          unset($this->casFieldParams['number']);
        }
        else
          $nCount = 1;

        $this->cbIsEndingLine = true;
        $this->cnSkippingLine = $nCount;
        //no break on purpose

      case 'text':

        if(isset($this->casFieldParams['text']))
        {
          $sText = (string)$this->casFieldParams['text'];
          unset($this->casFieldParams['text']);
        }
        else
          $sText = '';


        $sHTML = '<span ';

        foreach($this->casFieldParams as $sKey => $vValue)
          $sHTML.= ' '.$sKey.'="'.$vValue.'" ';

        $sHTML.= '>'.$sText.'</span>';

        break;

        case 'title':

        if(isset($this->casFieldParams['title']))
        {
          $sText = (string)$this->casFieldParams['title'];
          unset($this->casFieldParams['title']);
        }
        else
          $sText = '';


        $sHTML = '<div';

        foreach($this->casFieldParams as $sKey => $vValue)
          $sHTML.= ' '.$sKey.'="'.$vValue.'" ';

        $sHTML.= '>'.$sText.'</div>';

        break;

      default:
        assert('false; // Unknown form misc element');
        return '';
    }

    return $sHTML;
  }

}