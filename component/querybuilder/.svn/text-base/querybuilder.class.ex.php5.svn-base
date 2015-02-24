<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pager
 *
 * @author root
 */
require_once('component/querybuilder/querybuilder.class.php5');

class CQuerybuilderEx extends CQuerybuilder
{

  function __contruct()
  {
    //TODO: Display and edit js to function based on this uniqId
    parent::__construct();
  }




  public function getDisplay()
  {
    $oHTML = CDependency::getComponentByName('display');

    $sHTML = $oHTML->getBlocStart('', array('class' => 'queryBuilderContainer'));
    $sHTML.= $oHTML->getText('Querybuilder right here ');
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }
  
  


}
?>