<?php

require_once('component/portal/portal.class.php5');

class CPortalEx extends CPortal
{
  public function __construct()
  {
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'css/portal.css');
  }

  public function getPageActions($psAction = '', $psType = '', $pnPk = 0)
  {
    $asActions = array();
    return $asActions;
  }

  // Normal functions

  public function getHtml()
  {
    assert('false; // should not be here, there should be a custom portal for this website '.CONST_WEBSITE);
  }

  //Ajax function
  public function getAjax()
  {
    assert('false; // should not be here, there should be a custom portal for this website '.CONST_WEBSITE);
  }

}
