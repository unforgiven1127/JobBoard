<?php
require_once('component/taaggregator/taaggregator.class.php5');

class CTAaggregatorEx extends CTAaggregator
{
  private $casAllowedIp = array('127.0.0.1', '192.168.81.93', '203.167.38.11', '118.243.81.245');


  //====================================================================
  //  Interfaces
  //====================================================================


  public function getCronJob()
  {
    if(!in_array($_SERVER['REMOTE_ADDR'], $this->casAllowedIp))
      exit('buuuu');

    if(getValue('hash') == '5248746286c66ea42db129ddae88d2e1')
      return $this->_exportPositions();

    $nNow = time();
    $bDoAggregation = false;

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM website WHERE status = 1 ORDER BY last_update ASC ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);

    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $sLastUpdate = $oDbResult->getFieldValue('last_update');
      $nLastUpdate = strtotime($sLastUpdate);
      $nUpdateFreq = $oDbResult->getFieldValue('update_frequency', CONST_PHP_VARTYPE_INT);

      if(($nLastUpdate+$nUpdateFreq) < $nNow)
      {
        $bDoAggregation = true;
        $bRead = false;
      }
      else
        $bRead = $oDbResult->readNext();
    }

    if(!$bDoAggregation)
    {
      echo 'No website to treat right now... see you in five :)';
      return true;
    }

    echo 'TalentAtlas Aggregator: treat website <strong>'.$oDbResult->getFieldValue('name').'</strong> <br /><br />';

    //Include the specific aggregator required files
    $this->loadAggregator($oDbResult);

    //Instanciate the specific aggregator base on the parser name in the website table
    $sAggName = $oDbResult->getFieldValue('name_parser');
    $asFileData = pathinfo($sAggName);

    $sAggName = str_ireplace('.class', '', $asFileData['filename']);
    $oAggregator = new $sAggName;

    if(!$oAggregator)
    {
      assert('false; // could not create an aggregator named '.$sAggName );
      exit();
    }

    //Import the URLs
    echo '<strong>TalentAtlas Aggregator First CronJob </strong> <br /><br />';
    $oAggregator->saveUrl($oDbResult);

    // Access the website, fetch the data and create entries in the job table
    echo '<strong>TalentAtlas Aggregator Second CronJob</strong> <br/><br/>';
    $oAggregator->getJobs($oDbResult);

    //Create Positions to be moderated and published based on the job entries
    echo '<strong>TalentAtlas Aggregator Third CronJob</strong> <br /><br />';
    $oAggregator->getPosition();

    return true;
  }

  //====================================================================
  //  Other methods
  //====================================================================




  /**
   * Load desired parser files for the desired aggregator
   * @param integer $nWebsitePk
   */

  public function loadAggregator($poDbWebsite)
  {
    if(!assert('!empty($poDbWebsite) && is_object($poDbWebsite)'))
      return false;

    if($poDbWebsite->getFieldValue('status', CONST_PHP_VARTYPE_INT) !== 1)
    {
      echo ' Parser inactive ('.$poDbWebsite->getFieldValue('name').')';
      return false;
    }

    $sClassName = $poDbWebsite->getFieldValue('name_parser');

    if(empty($sClassName))
    {
      assert('false; // no parser define for this website ('.$poDbWebsite->getFieldValue('name').') ');
      return false;
    }

    $sPath = $_SERVER['DOCUMENT_ROOT'].$this->getResourcePath().$sClassName;
    return include_once($sPath);
  }


  private function _exportPositions()
  {
    //filters to define what to export. Will add more parameters when needed
    $bDebug = (bool)getValue('debug', false);
    $nSourcePk = (int)getValue('sourcepk', 0);

    if(!$bDebug && empty($nSourcePk))
      exit(__LINE__.' - Need to select a source ');

    $sLanguage = getValue('language');
    if(!$bDebug && empty($sLanguage))
      exit(__LINE__.' - Need to select a language. ');

    if(!$bDebug &&  !in_array($sLanguage, array('en', 'jp', 'ph', 'ch')))
      exit(__LINE__.' - Language not available. ');

    $nStartTime = (int)getValue('starttime', 0);
    $nEndTime = (int)getValue('endtime', 0);


    $oDB = CDependency::getComponentByName('database');
    $oTA = CDependency::getComponentByName('talentatlas');
    $oTA->setLanguage($sLanguage);

    $sQuery = 'SELECT pos.*, job.jobpk, cp.company_name, ind.name as indus_code, ind.status as indus_status, ind.parentfk as indus_parent, job.websitefk, job.data FROM position as pos  ';
    $sQuery.= ' LEFT JOIN position as pos2 ON (pos2.positionpk = pos.parentfk) ';
    $sQuery.= ' LEFT JOIN job ON (job.jobpk = pos2.jobfk) ';
    $sQuery.= ' LEFT JOIN company as cp ON (cp.companypk = pos.companyfk AND cp.status = 1) ';
    $sQuery.= ' LEFT JOIN industry as ind ON (ind.industrypk = pos.industryfk) ';

    if(!$bDebug)
    {
      $sQuery.= ' WHERE (job.websitefk = '.$nSourcePk.' OR pos.to_jobboard = 1 OR pos.companyfk IN (226,113,292) OR pos2.companyfk IN (226,113,292) ) ';
      $sQuery.= ' AND pos.lang = "'.$sLanguage.'" ';
      $sQuery.= ' AND pos.parentfk <> 0 ';
      $sQuery.= ' AND pos.visibility > 0 ';
    }
    else
    {
      $sQuery.= ' WHERE pos.lang = "'.$sLanguage.'" ';
      $sQuery.= ' AND pos.parentfk <> 0 ';
    }

    if(!empty($nStartTime))
    {
      $sQuery.= ' AND pos.posted_date >= "'.date('Y-m-d H:i:s', $nStartTime).'" ';
    }

    if(!empty($nEndTime))
    {
      $sQuery.= ' AND pos.posted_date <= "'.date('Y-m-d H:i:s', $nEndTime).'" ';
    }

    $sQuery.= ' ORDER BY pos.posted_date DESC ';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    header('Content-Type: text/xml');
    $oXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><TalentAtlasJobs></TalentAtlasJobs>');
    $oXml->addAttribute('generated_date', date('Y-m-d H:i:s'));

    //$oLog = $oXml->addChild('debug', $sQuery);

    while($bRead)
    {
      $asPositionData = $oDbResult->getData();
      $asRawData = @unserialize($asPositionData['data']);

      if((int)$asPositionData['indus_status'] != 1)
        $asPositionData['industry_name'] = $asPositionData['indus_code'];
      else
        $asPositionData['industry_name'] = $oTA->getTranslation($asPositionData['indus_code']);

      $oNewJob = $oXml->addChild('position');
      $oNewJob->addAttribute('uid', $asPositionData['positionpk']);

      foreach($asPositionData as $sFieldName => $vValue)
      {
        $oNewJob->addChild($sFieldName, htmlspecialchars($vValue, ENT_QUOTES, 'UTF-8'));
      }

      if(isset($asRawData['cons_name']))
        $oNewJob->addChild('cons_name', htmlspecialchars($asRawData['cons_name'], ENT_QUOTES, 'UTF-8'));

      if(isset($asRawData['cons_email']))
        $oNewJob->addChild('cons_email', htmlspecialchars($asRawData['cons_email'], ENT_QUOTES, 'UTF-8'));

      $bRead = $oDbResult->readNext();
    }

    exit($oXml->asXML());
  }
}

?>