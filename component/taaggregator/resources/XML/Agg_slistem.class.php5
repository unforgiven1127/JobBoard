<?php

require_once('component/taaggregator/resources/Agg_XML.class.php5');

class Agg_slistem extends Agg_XML
{
  //private $csBridgeUrl = 'https://squirrel.slate.co.jp/api/talentAtlas_bridge.php/api/talentAtlas_bridge.php?TA-ID-HASH=ta-123456-lentAT-7890-LAS';
  private $csBridgeUrl = 'https://slistem.slate.co.jp/api/talentAtlas_bridge.php/api/talentAtlas_bridge.php?TA-ID-HASH=ta-123456-lentAT-7890-LAS';

  protected $nWebsitePk = 5 ;
  protected $casUserToNotify = array('Ingrid Dubreuil' =>  'idubreuil@slate.co.jp');

  public function __destruct()
  {
    //Destructor
    //log what happened
    $asError =  $this->error_get_line();
    if(!empty($asError))
      $this->notifyErrorMessage($asError);

    return true;
  }

  /**
   * Check if the job already exist or not
   * @param integer $pnJobPk
   * @return boolean
   */

  private function isJobNotExist($pnJobPk)
  {
    if(!assert('!empty($pnJobPk) && is_integer($pnJobPk)'))
      return false;

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM job WHERE weburlfk ='.$pnJobPk.' AND websitefk = '.$this->nWebsitePk;
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if($bRead)
        return false;
    else
        return true;
   }

   /**
    * Create the jobs from the silistem
    * @param object $poWebsite
    * @return boolean
    */

  public function getJobs($poWebsite)
  {
    $oDB = CDependency::getComponentByName('database');

    //1. Construct the url
    /*$nLastUpdate = strtotime($poWebsite->getFieldValue('last_update'));
    if($nLastUpdate < 0)*/
      $nLastUpdate = strtotime('-2 weeks');

    $sUrl = $this->csBridgeUrl.'&seekPositionFrom='.$nLastUpdate;
    $oXml = $this->_loadSimpleXml($sUrl);

    echo 'search Slistem position since '.date('Y-m-d', $nLastUpdate).'<br /><br />';

    if(!$oXml)
    {
      echo 'No new job found (since last update: '.$poWebsite->getFieldValue('last_update').') <br/><br/>';
      return true;
    }
    else
    {
      $nCount = 0;
      foreach($oXml->newposition->position as $oNodePosition)
      {
        //Access the detail of every new position from slistem
        $bData = $this->isJobNotExist((int)$oNodePosition->id);
        if($bData)
        {
          $asSalary = explode('-',$oNodePosition->salary);
          if(!empty($asSalary))
          {
            $nSalary_Low = $asSalary[0];
            $nSalary_High = $asSalary[1];
          }
          else
          {
            $nSalary_Low = 0;
            $nSalary_High = 0;
          }

          $asData = array('position'       => $this->sanitize($oNodePosition->title),
                          'company_name'   => $this->sanitize($oNodePosition->company),
                          'jobID'          => $this->sanitize($oNodePosition->id),
                          'location'       => $this->sanitize($oNodePosition->location),
                          'post_date'      => date('Y-m-d H:i:s'),
                          'salary'         => $this->sanitize($oNodePosition->salary),
                          'salary_low'     => $this->sanitize($nSalary_Low),
                          'salary_high'    => $this->sanitize($nSalary_High),
                          'english'        => $this->sanitize($oNodePosition->english),
                          'japanese'       => $this->sanitize($oNodePosition->japanese),
                          'english_nb'     => $this->sanitize($oNodePosition->english_nb),
                          'japanese_nb'    => $this->sanitize($oNodePosition->japanese_nb),
                          'requirements'   => $this->sanitize($oNodePosition->requirements),
                          'job_description'=> $this->sanitize($oNodePosition->description),
                          'industry'       => $this->sanitize($oNodePosition->industry),
                          'industry_id'    => $this->sanitize($oNodePosition->industry_id),
                          'industry_parent'=> $this->sanitize($oNodePosition->industry_parent),
                          'career'         => $this->sanitize($oNodePosition->career),
                          'posted_date'    => $this->sanitize($oNodePosition->date_created),
                          'cons_name'    => $this->sanitize($oNodePosition->cons_name),
                          'cons_email'    => $this->sanitize($oNodePosition->cons_email)
                        );

          $sData = serialize($asData);
          if(!empty($sData))
          {
            $sQuery = 'INSERT INTO job (data,date_create,weburlfk,websitefk) VALUES ('.$oDB->dbEscapeString($sData).','.$oDB->dbEscapeString(date('Y-m-d')).','.$oDB->dbEscapeString($oNodePosition->id).','.$this->nWebsitePk.')';
            $oDbResult = $oDB->ExecuteQuery($sQuery);

            if(!$oDbResult)
              return false;
          }
          $nCount++;
        }
      }

      if($nCount >0)
        echo $nCount.' Jobs has been created successfully on Silistem <br/><br/>';
      else
        echo 'No jobs has been created in Silistem at the moment <br/><br/>';
    }
    return true;
  }

  /**
   * Create position from the jobs stored
   * @return boolean
   */

   public function getPosition()
   {
      $oDB = CDependency::getComponentByName('database');
      $sTime = date('Y-m-d H:i:s');
      $asData = $this->getJObForPosition($this->nWebsitePk);
      $nCount = 0;
      $asContent = array();

      if(!empty($asData))
      {
        foreach($asData as $asRecords)
        {
          //Check if the position already exists
          $oResult = $this->_checkPosition((int)$asRecords['jobpk']);
          if($oResult)
          {
            $asPositions = unserialize($asRecords['data']);
            $nIndustryfk = 0;
            $nIndustryfk = $this->_getcheckIndustry($asPositions['industry'], $asPositions['industry_parent']);
            $nCompanyfk = $this->_getcheckCompany($asPositions['company_name']);

            $sQuery = 'INSERT INTO position (jobfk,visibility,industryfk,position_title,position_desc,requirements,';
            $sQuery.= ' status,companyfk,location,posted_date,salary,salary_low,salary_high,english,japanese,temp_industry)';
            $sQuery.= ' VALUES ('.$oDB->dbEscapeString($asRecords['jobpk']).',2,'.$oDB->dbEscapeString($nIndustryfk).',';
            $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['position'])).','.$oDB->dbEscapeString($this->sanitize($asPositions['job_description'])).','.$oDB->dbEscapeString($this->sanitize($asPositions['requirements'])).',';
            $sQuery.= ' 0,'.$oDB->dbEscapeString($nCompanyfk).','.$oDB->dbEscapeString($this->sanitize($asPositions['location'])).','.$oDB->dbEscapeString($asPositions['posted_date']).',';
            $sQuery.= ''.$oDB->dbEscapeString($asPositions['salary']).',';
            $sQuery.= ''.$oDB->dbEscapeString($asPositions['salary_low']).',';
            $sQuery.= ''.$oDB->dbEscapeString($asPositions['salary_high']).',';
            $sQuery.= ''.$oDB->dbEscapeString($asPositions['english_nb']).',';
            $sQuery.= ''.$oDB->dbEscapeString($asPositions['japanese_nb']).',';
            $sQuery.= ''.$oDB->dbEscapeString($asPositions['industry']).')';

            $oDbResult = $oDB->ExecuteQuery($sQuery);
            $this->_logPosition($asPositions['position'],$asRecords['jobpk'],$asPositions['post_date']);
            $nCount ++;

            $asContent[] = $asPositions['position'].' position has been created';

            $sQuery = 'UPDATE job SET status = 1 WHERE jobpk='.$asRecords['jobpk'].'';
            $oDbResult = $oDB->ExecuteQuery($sQuery);

            if(!$oDbResult)
              return false;
          }
        }

        if($nCount > 0)
        {
          $this->_notifyModerator($asContent);
          echo $nCount.' new positions have been created on silistem. <br/><br/>';

          //notify moderator that new positions have to me moderated
          $oMail = CDependency::getComponentByName('mail');
          $oMail->creatNewEmail();
          $oMail->setFrom('no-reply@talentatlas.com', 'TalentAtlas notifyer');

          $oMail->addRecipient('sboudoux@bulbouscell.com', 'Stephane boudoux');
          foreach($this->casUserToNotify as $sUserName => $sUserEmail)
          {
            $oMail->addRecipient($sUserEmail, $sUserName);
          }



          $sContent = $nCount.' important positions have been updated on TalentAtlas and need moderation.';
          $sContent.= '<br />Please access the admin section to validate / translate those.';
          $sContent.= '<br /><br />http://www.talentatlas.com';
          $sContent.= '<br /><br />Best Regards.';

          $oResult = $oMail->send($nCount.' important position(s) to moderate on TalentAtlas', $sContent);
        }
        else
          echo 'There are no new positions to create today on silistem <br/><br/>';

         $sQuery = 'UPDATE website SET last_update = "'.$sTime.'" , last_update_status= 1 WHERE websitepk = '.$this->nWebsitePk;
         $oDbResult = $oDB->ExecuteQuery($sQuery);

        return true;
      }
      else
        echo 'There are no new positions to create today on silistem <br/><br/>';

      $sQuery = 'UPDATE website SET last_update = "'.$sTime.'" , last_update_status= 1 WHERE websitepk = '.$this->nWebsitePk;
      $oDbResult = $oDB->ExecuteQuery($sQuery);

      return true;
    }
}
?>
