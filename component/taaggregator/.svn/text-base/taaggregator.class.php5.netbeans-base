<?php

class CTAaggregator
{
  protected $csUid = '100-603';
  protected $csAction = '';
  protected $csType = '';
  protected $cnPk = 0;
  protected $csMode = '';
  protected $csLanguage;

  public function __construct()
  {
      return true;
  }

  public function getComponentUid()
  {
    return '100-603';
  }

  protected function _getUid()
  {
    return '100-603';
  }

  public function getComponentName()
  {
    return 'TAaggregator';
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
    $oPage = CDependency::getComponentByName('page');

    $this->csAction = $oPage->getAction();
    $this->csType = $oPage->getType();
    $this->cnPk = $oPage->getPk();
    $this->csMode = $oPage->getMode();

    if(empty($this->csAction))
      $this->csAction = $this->getDefaultAction();

    if(empty($this->csType))
      $this->csType = $this->getDefaultType();

    return true;
  }

  public function getPageActions($psAction = '', $psType = '', $pnPk = 0)
  {
    return array();
  }

   public function getResourcePath()
  {
    return '/component/taaggregator/resources/';
  }

  public function setLanguage($psLanguage)
  {
    $this->csLanguage = $psLanguage;
  }
}