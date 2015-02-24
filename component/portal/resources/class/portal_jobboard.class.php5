<?php

require_once('component/portal/portal.class.ex.php5');

class CPortalJobboardEx extends CPortalEx
{

  public function getHomePage()
  {
    $oPage = CDependency::getComponentByName('page');
    $oDisplay = CDependency::getComponentByName('display');
    $oDb = CDependency::getComponentByName('database');

    $sHTML = '';
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'portalContainer'));

      $sHTML.= $oDisplay->getTitleLine('Welcome to slate job board');

      $sHTML.= $oDisplay->getCarriageReturn(2);
      $sHTML.= $oDisplay->getText('Dashboard, stats, ... everything will be there  at some point.');
      $sHTML.= $oDisplay->getCarriageReturn(3);


      $sUrl = $oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_SHARE_JOB);
      $sHTML.= $oDisplay->getLink('- Share position on social networks', $sUrl);
      $sHTML.= $oDisplay->getCarriageReturn();

      $sUrl = $oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB);
      $sHTML.= $oDisplay->getLink('- Edit positions', $sUrl);
      $sHTML.= $oDisplay->getCarriageReturn();



      $sHTML.= $oDisplay->getCarriageReturn();

      $sHTML.= $oDisplay->getText('Statistics:');
      $sHTML.= $oDisplay->getBlocStart('', array('style' => 'margin: 10px 20px; border: 1px solid #aaa; width: 300px; padding: 10px;'));

      $sQuery = 'SELECT count(*) as nCount, p2.lang FROM position as p1  ';
      $sQuery.= ' LEFT JOIN position as p2 ON (p2.parentfk = p1.positionpk AND p1.parentfk = 0) ';
      $sQuery.= ' WHERE p1.parentfk = 0  ';
      $sQuery.= ' GROUP BY p2.lang ORDER BY p2.lang ';

      $oDbResult = $oDb->executeQuery($sQuery);
      $bRead = $oDbResult->readFirst();

      if($bRead)
      {
        while($bRead)
        {
          $asData = $oDbResult->getData();
          switch($asData['lang'])
          {
            case 'jp': $sMessage = $asData['nCount'].' positions moderated in Japanese '; break;
            case 'en': $sMessage = $asData['nCount'].' positions moderated in English '; break;
            case 'ph': $sMessage = $asData['nCount'].' positions moderated in Filipino '; break;
            default: $sMessage = $asData['nCount'].' positions awaiting moderations '; break;
          }

          $sHTML.= $oDisplay->getText($sMessage);
          $sHTML.= $oDisplay->getCarriageReturn();

          $bRead = $oDbResult->readNext();
        }
      }
      $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getBlocEnd();
    return $sHTML;
  }

}
?>
