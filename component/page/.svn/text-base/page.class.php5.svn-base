<?php

class CPage
{
  protected $csUid;
  protected $csAction;
  protected $csActionReturn;
  protected $csType;
  protected $cnPk;
  protected $csMode;
  protected $csLanguage;

  public function __construct()
  {
  }

  public function __destruct()
  {
    flush();
    @ob_flush();
  }

  public function getComponentUid()
  {
    return '845-187';
  }

  protected function _getUid()
  {
    return '845-187';
  }

  public function getComponentName()
  {
    return 'page';
  }

  public function getDefaultType()
  {
    return '';
  }

  public function getDefaultAction()
  {
    return '';
  }

  public function getAction()
  {
    return $this->csAction;
  }
  public function setAction($psAction)
  {
    if(!assert('!empty($psAction)'))
    return '';

    return $this->csAction = $psAction;
  }


  public function getType()
  {
    return $this->csType;
  }
  public function setType($psType)
  {
    if(!assert('!empty($psType)'))
    return '';

    return $this->csType = $psType;
  }


  public function getPk()
  {
    return $this->cnPk;
  }
  public function setPk($pnPk)
  {
    if(!assert('!empty($pnPk)'))
    return '';

    return $this->cnPk = $pnPk;
  }


  public function getMode()
  {
    return $this->csMode;
  }
  public function setMode($psMode)
  {
    if(!assert('!empty($psMode)'))
    return '';

    return $this->csMode = $psMode;
  }

  protected function _processUrl()
  {

    if(empty($this->csAction))
      $this->csAction = $this->getDefaultAction();

    if(empty($this->csType))
      $this->csType = $this->getDefaultType();

    return true;
  }

  public function getResourcePath()
  {
    return '/component/page/resources/';
  }

  public function setLanguage($psLanguage)
  {
    $this->csLanguage = $psLanguage;
  }

  public function getLanguage()
  {
    return 'en';
  }
}