<?php

require_once('component/taaggregator/resources/Agg_HTML.class.php5');

 class Agg_enjapan extends Agg_HTML
 {
  protected $sContainer = 'table[class=search_results] tr td a';
  protected $sPgContainer = 'div[id="search_results"] div[class="search_nav_top"] div a';
  protected $sJObDetailURL;
  protected $sFirstElementTag = 'div';
  protected $sFirstID  = 'content';
  protected $nWebsitePk = 2 ;


  public function __construct()
  {
    $poWebsite = $this->_getEnJapanDetails();

    $this->sEnJapanURL= $poWebsite->getFieldValue('search_url');
    $this->sEjURL = $poWebsite->getFieldValue('list_url');

    return true;
  }
  
  public function __destruct()
  {
   //Destructor
    $asError =  $this->error_get_line();
     if(!empty($asError))
       $this->notifyErrorMessage($asError);
   }
     
  protected function _getEnJapanDetails()
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM `website` WHERE `websitepk`='.$this->nWebsitePk;
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
        return NULL;
    else
        return $oDbResult;
    }
    
   /**
   * Function to get the Job Postion URL from Gaijinpot
   * @return boolean true
   */
  public function saveUrl()
  {
    //Get All the job urls for Gaijinpot
    $this->_getJobURL($this->sEnJapanURL,$this->sEjURL,$this->sContainer,$this->sPgContainer,$this->nWebsitePk);
    return true;
   }
   
      /**
    * Create the Jobs from the Gaijinpot URLS
    * @return boolean true
    */

   public function getJobs()
   {
    $oDB = CDependency::getComponentByName('database');
    $asJobsArray = $this->getAllJobURLs($this->nWebsitePk,$this->nRecords);

    if(!empty($asJobsArray))
    {
      $nCount = 0;
      foreach($asJobsArray as $sJObURL)
      {
        $oContent = $this->checkURL($sJObURL['url']);
        if($oContent)
          $oData = $this->_isDesiredPage($sJObURL['url'],$this->sFirstElementTag,$this->sFirstID,'ID');
        else
         {
            $sQuery = 'UPDATE website_joburl set status ="-1" where website_joburlpk='.$sJObURL['website_joburlpk'].'';
            $oDbResult = $oDB->ExecuteQuery($sQuery);
            if(!$oDbResult)
                return false;
         }
        if($oData)
           $nCount+= $this->getEnJapanJob($sJObURL['url'],$sJObURL['website_joburlpk']);
        else
        {
           $sQuery = 'UPDATE website_joburl set status ="-1" where website_joburlpk='.$sJObURL['website_joburlpk'].'';
           $oDbResult = $oDB->ExecuteQuery($sQuery);
           if(!$oDbResult)
               return false;
         }

        //Delay some time
        sleep($this->nDelay);
        }
       echo $nCount.' Jobs has been created successfully on Enjapan <br/><br/>';
      }
    else
       echo 'There are not any new jobs to create at the moment.<br/><br/>';

    return true;
   }
   
   /**
    * Create Job
    * @param string $sJObDetailURL
    * @param integer $nPnPk
    * @param integer $nWebsitefk
    * @return boolean
    */
   public function getEnJapanJob($psJObDetailURL, $pnPnPk)
   {
    $oDB = CDependency::getComponentByName('database');

    if(!assert('!empty($psJObDetailURL) && !empty($pnPnPk)'))
      return false;

    //Check if it is already created
    $bRecords = $this->_checkJobNotExist($pnPnPk,$this->nWebsitePk);
    if($bRecords)
    {
      $oDomHTML = $this->getHTMLContent($psJObDetailURL);
      if(!empty($oDomHTML))
      {
        $oElement =  $this->getDOMStructureByID($oDomHTML,$this->sFirstElementTag,$this->sFirstID);
     
        //Position
        $sString = $this->sanitize($this->getElementByClass($oElement,'h1','job_title'));
        
        $sPosition= $this->sanitize($sString);
        if(empty($sPosition))
          $sPosition = '';
       
        // Company Name will always be empty
        $sCompany = '';
                
        $ocElement = $this->getElementByClass($oElement,'table','view_job');
        $ocElement = $this->getDOMStructureByTag($ocElement,'tr td');
      
        // Job category
        
        $sString = $this->sanitize($ocElement);
        if($sString == 'Category')
        {
          $sJobCategory =  $this->getNextSibling($ocElement);
          $sJobCategory =  $this->sanitize($sJobCategory);
        }
 
        if(empty($sJobCategory))
            $sJobCategory = '';
      
        //Salary
      
        $osElement = $this->getElementByClass($oElement,'table','view_job');
        $osElement =  $this->getDOMStructureByTag($osElement,'tr td ');
        $osElement =  $this->getNextSibling($osElement);
          
        $osElement =  $this->getNextSibling($osElement);
        
        // Salary
         $sString =  $this->sanitize($osElement);
         if($sString =='Salary')
         {
           $sSalary =  $this->getNextSibling($osElement);
           $sSalary =  $this->sanitize($sSalary);
            
           //Calculate the salary range

           $asSalary = explode('-',$sSalary);
           if(!empty($asSalary))
           {
             $nSalaryHigh = preg_replace("/[^0-9]/","", $asSalary[1]);
             $nSalaryLow = $asSalary[0];
               
             if(preg_replace("/[^M]/","", $asSalary[1]))
             {
               $nSalaryLow = intval(($nSalaryLow*1000000)/12);
               $nSalaryHigh = intval(($nSalaryHigh*1000000)/12);
               }
             }
           }
           
          if(empty($sSalary))
            $sSalary = '';
           
          if(!isset($nSalaryLow) && empty($nSalaryLow))
            $nSalarylow = 0;
        
          if(!isset($nSalaryHigh) && empty($nSalaryHigh))
            $nSalaryHigh = 0; 
                 
        // Job Type
        $owElement = $this->getElementByClass($oElement,'table','view_job');
        $owElement =  $this->getDOMStructureByTag($owElement,'tr');
        $owElement =  $this->getNextSibling($owElement);
        
        $owTElement =  $this->getDOMStructureByTag($owElement,'td');
       
        $sString =  $this->sanitize($owTElement);
        if($sString =='Work Type')
        {
           $sWorkType =  $this->getNextSibling($owTElement);
           $sWorkType =  $this->sanitize($sWorkType);
           
            if(preg_match("/Permanent/",$sWorkType))
             $nJobType = 0;
           else
             $nJobType = 1;  
        }
        
        if(empty($sWorkType))
          $sWorkType = '';
        
        if(empty($nJobType))
          $nJobType = 1;        
        
        //Date 
        
        $odElement =  $this->getNextSibling($owTElement);
        $odElement =  $this->getNextSibling($odElement);
        
        $sString =  $this->sanitize($odElement);
        
        if($sString =='Date')
        {
          $sDate = $this->getNextSibling($odElement);
          $sDate =  $this->sanitize($sDate);
          $sPostDate = $this->_getFormattedDate($sDate);        
        }
        
        //If the position is two months old, do not create job .
        if(!empty($sPostDate))
        {  
          $bOldJob = $this->_checkTwoMonth($sPostDate,$pnPnPk);
          if($bOldJob)
            return true;
        }
       
        // Location
        $olElement = $this->getElementByClass($oElement,'table','view_job');
        $olElement =  $this->getDOMStructureByTag($olElement,'tr');
        $olElement =  $this->getNextSibling($olElement);
        $olElement =  $this->getNextSibling($olElement);
        
        $oLsElement =  $this->getDOMStructureByTag($olElement,'td');
        
        $sString = $this->sanitize($oLsElement);
        if($sString == 'Location')
        {
          $sLocation =  $this->getNextSibling($oLsElement);
          $sLocation =  $this->sanitize($sLocation);
        }
  
        if(empty($sLocation))
          $sLocation = '';
       
        // Description
        
        $oElement = $this->getElementByClass($oDomHTML,'div','job_text');
        $osElement = $this->getDOMStructureByTag($oElement,'h3');
        
        $sString = $this->sanitize($osElement);
        $sDescription = '';
        
        if($sString == 'Overview')
        {
           $sDescription.= $this->sanitize($this->getNextSibling($osElement));
           $sDescription.= '<br/>';
        }
        
        $oElement =  $this->getNextSibling($oElement);
        $osElement = $this->getDOMStructureByTag($oElement,'h3');
        $sString = $this->sanitize($osElement);

        if($sString =='Company Description')
        {
          $sDescription.= $this->sanitize($this->getNextSibling($osElement));
          $sDescription.= '<br/>';
         }
        
        $oElement =  $this->getNextSibling($oElement);
        $osElement = $this->getDOMStructureByTag($oElement,'h3');
        $sString = $this->sanitize($osElement);

        if($sString =='Responsibilities')
        {
           $sDescription.= $this->sanitize($this->getNextSibling($osElement));
           $sDescription.= '<br/>';
         }
         
         //Requirements
        
        $oElement =  $this->getNextSibling($oElement);
        $orElement = $this->getDOMStructureByTag($oElement,'h3');
        $sString = $this->sanitize($orElement);
        
        $sRequirements = '';
        
        if($sString =='Requirements')
        {
           $sRequirements = $this->sanitize($this->getNextSibling($orElement));
           $sRequirements.= '<br/>';
         }
         
        $oElement =  $this->getNextSibling($oElement);
        $orElement = $this->getDOMStructureByTag($oElement,'h3');
        $sString = $this->sanitize($orElement);

        if($sString =='Preferred Experience')
        {
           $sRequirements.= $this->sanitize($this->getNextSibling($orElement));
           $sRequirements.= '<br/>';
         }
         
        $oElement =  $this->getNextSibling($oElement);
        $orElement = $this->getDOMStructureByTag($oElement,'h3');
        $sString = $this->sanitize($orElement);

        if($sString =='Desired Traits')
        {
            $sRequirements.= $this->sanitize($this->getNextSibling($orElement));
            $sRequirements.= '<br/>';
         }
         
        $oElement =  $this->getNextSibling($oElement);
        $osElement = $this->getDOMStructureByTag($oElement,'h3');
        $sString = $this->sanitize($osElement);

        if($sString =='Description of Benefits')
        {
            $sDescription.= $this->sanitize($this->getNextSibling($osElement));
            $sDescription.= '<br/>';
         }
         
        $oElement =  $this->getNextSibling($oElement);
        $osElement = $this->getDOMStructureByTag($oElement,'h3');
        $sString = $this->sanitize($osElement);

        if($sString =='Additional Notes')
        {
            $sDescription.= $this->sanitize($this->getNextSibling($osElement));
            $sDescription.= '<br/>';
         }
         
        $sEnglishLevel = 0; 
        $sJapaneseLevel = 0;
                      
        $asData = array('position'      => $this->dosanitize($sPosition),
                        'company_name'  => $this->dosanitize($sCompany),
                        'location'      => $this->dosanitize($sLocation),
                        'post_date'     => $sPostDate,
                        'job_category'  => $this->dosanitize($sJobCategory),
                        'work_type'     => $this->dosanitize($sWorkType),
                        'job_type'      => $nJobType, 
                        'salary'        => $this->dosanitize($sSalary),
                        'salary_low'    => $nSalaryLow,
                        'salary_high'   => $nSalaryHigh,
                        'english'       => $this->dosanitize($sEnglishLevel),
                        'japanese'      => $this->dosanitize($sJapaneseLevel),
                        'requirements'  => $this->dosanitize($sRequirements),
                        'job_description'=> $this->dosanitize($sDescription),
                        'weburlfk'       => $pnPnPk
                        );
        
        $sData = serialize($asData);

        if(!empty($sData) && !empty($sPosition))
        {
            $sQuery = 'INSERT INTO job (data,date_create,raw_content,weburlfk,websitefk) VALUES ('.$oDB->dbEscapeString($sData).','.$oDB->dbEscapeString(date('Y-m-d')).','.$oDB->dbEscapeString($oDomHTML->outertext).','.$oDB->dbEscapeString($pnPnPk).','.$this->nWebsitePk.')';
            $oDbResult = $oDB->ExecuteQuery($sQuery);
            
            if(!$oDbResult)
              return false;
            
            $sQuery = 'UPDATE website_joburl SET status = 1 where website_joburlpk='.$pnPnPk.'';
           $oResult = $oDB->ExecuteQuery($sQuery);

            if(!$oResult)
                return false;
            else
                return true;
             }
           }
         }
        else
         return false;
     }
     
      /**
      * Create Position from the Gaijinpot Jobs
      * @return boolean true
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
            
             $nCompanyfk = 0;
             
             $sQuery = 'INSERT INTO position (jobfk,visibility,industryfk,career_level,position_title,position_desc,';
             $sQuery.= ' requirements,status,companyfk,location,posted_date,salary,salary_low,salary_high,';
             $sQuery.= ' english,japanese,job_type,temp_industry)';
             $sQuery.= ' VALUES ('.$oDB->dbEscapeString($this->sanitize($asRecords['jobpk'])).',1,';
             $sQuery.= ''.$oDB->dbEscapeString($nIndustryfk).',';
             $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['work_type'])).',';
             $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['position'])).',';
             $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['job_description'])).',';
             $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['requirements'])).',';
             $sQuery.= ' 0,'.$oDB->dbEscapeString($nCompanyfk).',';
             $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['location'])).',';
             $sQuery.= ''.$oDB->dbEscapeString($asPositions['post_date']).',';
             $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['salary'])).',';
             $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['salary_low'])).',';
             $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['salary_high'])).',';
             $sQuery.= ''.$oDB->dbEscapeString($asPositions['english']).',';
             $sQuery.= ''.$oDB->dbEscapeString($asPositions['japanese']).',';
             $sQuery.= ''.$oDB->dbEscapeString($asPositions['job_type']).',';
             $sQuery.= ''.$oDB->dbEscapeString($asPositions['job_category']).')';

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
        }
       
        if($nCount>0)
        {
           $this->_notifyModerator($asContent);
           echo $nCount.' new positions have been created on enjapan. <br/><br/>';
        }
        else
          echo 'There are no new positions to create today on enjapan <br/><br/>';

         $sQuery = 'UPDATE website SET last_update = "'.$sTime.'" , last_update_status= 1 WHERE websitepk = '.$this->nWebsitePk;
         $oDbResult = $oDB->ExecuteQuery($sQuery);

        return true;
      }
      
 }
 
?>