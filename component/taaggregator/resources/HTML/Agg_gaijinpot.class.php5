<?php
require_once('component/taaggregator/resources/Agg_HTML.class.php5');

class Agg_gaijinpot extends Agg_HTML
{
  protected $sContainer = 'div[class=maincolumn] h2 a';
  protected $sPgContainer = '[class="pagination_pre"]';
  protected $sJObDetailURL;
  protected $sFirstElementTag = 'div';
  protected $sFirstClass  = 'title3';
  protected $nWebsitePk =1 ;


  public function __construct()
  {
    $poWebsite = $this->_getGaijinpotDetails();

    $this->sGaijinpotURL= $poWebsite->getFieldValue('search_url');
    $this->sGjURL = $poWebsite->getFieldValue('list_url');

    return true;
  }

  public function __destruct()
  {
   //Destructor
    $asError =  $this->error_get_line();
     if(!empty($asError))
       $this->notifyErrorMessage($asError);
   }
   
  protected function _getGaijinpotDetails()
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
    $this->_getJobURL($this->sGaijinpotURL,$this->sGjURL,$this->sContainer,$this->sPgContainer,$this->nWebsitePk);
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
          $oData = $this->_isDesiredPage($sJObURL['url'],$this->sFirstElementTag,$this->sFirstClass,'CLASS');
        else
         {
            $sQuery = 'UPDATE website_joburl set status ="-1" where website_joburlpk='.$sJObURL['website_joburlpk'].'';
            $oDbResult = $oDB->ExecuteQuery($sQuery);
            if(!$oDbResult)
                return false;
         }
        if($oData)
           $nCount+= $this->getGaijinpotJob($sJObURL['url'],$sJObURL['website_joburlpk']);
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
       echo $nCount.' Jobs has been created successfully on Gaijinpot <br/><br/>';
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
   public function getGaijinpotJob($psJObDetailURL, $pnPnPk)
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
        $oElement =  $this->getDOMStructureByClass($oDomHTML,$this->sFirstElementTag,$this->sFirstClass);
        // Position
        $sPosition= $this->sanitize($oElement);
        if(empty($sPosition))
          $sPosition = '';

        // Company
        $oElement =  $this->getNextSibling($oElement);
        $sString = $this->sanitize($this->getElementByClass($oElement,'em','detail_title'));
        if($sString=='Company')
        {
            $sCompany = $this->getElementByClass($oElement,'div','detail_cont');
            $sCompany =  $this->sanitize($sCompany);
         }
         if(empty($sCompany))
            $sCompany = '';

        // JOB ID
        $oElement =  $this->getNextSibling($oElement);
        $sString = $this->sanitize($this->getElementByClass($oElement,'em','detail_title'));
        if($sString=='Job ID')
        {
          $sJobID = $this->getElementByClass($oElement,'div','detail_cont');
          $sJobID =  $this->sanitize($sJobID);
        }
        if(empty($sJobID))
          $sJobID = '';

        // Location
        $oElement =  $this->getNextSibling($oElement);
        $sString = $this->sanitize($this->getElementByClass($oElement,'em','detail_title'));
        if($sString == 'Location')
        {
          $sLocation = $this->getElementByClass($oElement,'div','detail_cont');
          $sLocation =  $this->sanitize($sLocation);
        }

        if(empty($sLocation))
          $sLocation = '';

        // Post Date
        $oElement =  $this->getNextSibling($oElement);
        $sString = $this->sanitize($this->getElementByClass($oElement,'em','detail_title'));
        if($sString=='Post date')
        {
            $sPostDate = $this->getElementByClass($oElement,'div','detail_cont');
            $sPostDate =  $this->sanitize($sPostDate);
            
            $sPostDate = $this->_getFormattedDate($sPostDate);
           }
        
        if(empty($sPostDate))
           $sPostDate = '';
        
         //If the position is two months old, do not create job it.
         
        $bOldJob = $this->_checkTwoMonth($sPostDate,$pnPnPk);
        if($bOldJob)
          return true;
        
        // Job category
        $oElement =  $this->getNextSibling($oElement);
        $sString = $this->sanitize($this->getElementByClass($oElement,'em','detail_title'));
        if($sString =='Job Category')
        {
          $sJobCategory = $this->getElementByClass($oElement,'div','detail_cont');
          $sJobCategory =  $this->sanitize($sJobCategory);
        }

        if(empty($sJobCategory))
            $sJobCategory = '';
        // Work Type
        $oElement =  $this->getNextSibling($oElement);
        $sString = $this->sanitize($this->getElementByClass($oElement,'em','detail_title'));
        if($sString =='Work Type')
        {
          $sWorkType = $this->getElementByClass($oElement,'div','detail_cont');
          $sWorkType =  $this->sanitize($sWorkType);
        }

        if(empty($sWorkType))
          $sWorkType = '';
        
         // Job type ,i.e part time or full time
        $sString = $this->sanitize($this->getElementByClass($oElement,'em','detail_title'));
        if($sString =='Work Type')
        {
          $sJobType = $this->getElementByClass($oElement,'div','detail_cont');
          $sJobType =  $this->sanitize($sJobType);
          
          if(preg_match("/part/",$sJobType))
             $nJobType = 0;
           else
             $nJobType = 1;  
          
        }

        if(empty($nJobType))
          $nJobType = 1;        
        

        // Salary
        $oElement =  $this->getNextSibling($oElement);
        $sString = $this->sanitize($this->getElementByClass($oElement,'em','detail_title'));
        if($sString =='Salary')
        {
            $sSalary = $this->getElementByClass($oElement,'div','detail_cont');
            $sSalary =  $this->sanitize($sSalary);
            
           //Calculate the salary range
            
            $asSalary = explode('/',$sSalary);
           
            if(!empty($asSalary) && isset($asSalary[1]))
            {                
             if(preg_match("/year/",strtolower($asSalary[1])) )
             {
               $asSalaryRange = explode('~',$asSalary[0]);
                         
               if(!empty($asSalaryRange))
               {
                $nSalarylow = preg_replace("/[^0-9]/","", $asSalaryRange[0]); 
                $nSalarylow = ($nSalarylow*10000)/12;
                 
                if(isset($asSalaryRange[1]) && !empty($asSalaryRange[1]))
                { 
                  $nSalaryHigh = preg_replace("/[^0-9]/","", $asSalaryRange[1]);
                  $nSalaryHigh = ($nSalaryHigh*10000)/12;
                }   
                else
                {   
                  $nSalaryHigh = ($nSalarylow*10000)/12; 
                  $nSalaryHigh = $nSalaryHigh+ ($nSalaryHigh*10);
                  }
                }               
              }
               
             if(preg_match("/month/", strtolower($asSalary[1])) )
             {
               $asSalaryRange = explode('~',$asSalary[0]);
                         
               if(!empty($asSalaryRange))
               {
                $nSalarylow = preg_replace("/[^0-9]/","", $asSalaryRange[0]); 
                $nSalarylow = ($nSalarylow);
                 
                if(isset($asSalaryRange[1]) && !empty($asSalaryRange[1]))
                { 
                  $nSalaryHigh = preg_replace("/[^0-9]/","", $asSalaryRange[1]);
                  $nSalaryHigh = ($nSalaryHigh);
                }   
                else
                {   
                  $nSalaryHigh = ($nSalarylow); 
                  $nSalaryHigh = $nSalaryHigh+ ($nSalaryHigh*10);
                  }
                }              
               }
                              
             if(preg_match("/hour/", strtolower($asSalary[1])) )
             { 
               $asSalaryRange = explode('~',$asSalary[0]);
                   
               if(!empty($asSalaryRange))
               {
                $nSalarylow = preg_replace("/[^0-9]/","", $asSalaryRange[0]); 
                                
                if(isset($asSalaryRange[1]) && !empty($asSalaryRange[1]))
                { 
                  $nSalaryHigh = preg_replace("/[^0-9]/","", $asSalaryRange[1]);
                 }   
                else
                {
                  $nSalaryHigh = $nSalarylow+ ($nSalarylow*10);
                    }
                  }                
                }
              }
           }
           
          if(empty($sSalary))
            $sSalary = '';
           
          if(!isset($nSalarylow) && empty($nSalarylow))
           $nSalarylow = 0;
        
          if(!isset($nSalaryHigh) && empty($nSalaryHigh))
          $nSalaryHigh = 0; 
        
        //Requirements
        $oElement =  $this->getNextSibling($oElement);
        $sString = $this->sanitize($this->getElementByClass($oElement,'em','detail_title'));
        if($sString == 'Requirements')
        {
          $sEnglish = $this->getCheckTextFromList($oElement,'requirements_list','English:');
          if(!empty($sEnglish[0]))
              $sEnglishLevel = $sEnglish[0];
          else if(!empty($sEnglish[1]))
              $sEnglishLevel = $sEnglish[1];
          if(!empty($sEnglishLevel))
              $sEnglishLevel =  $this->sanitize($sEnglishLevel);
          else
              $sEnglishLevel = '';

          $sJapanese = $this->getCheckTextFromList($oElement,'requirements_list','Japanese:');
          if(!empty($sJapanese[0]))
              $sJapaneseLevel = $sJapanese[0];
          else if(!empty($sJapanese[1]))
              $sJapaneseLevel = $sJapanese[1];
          if(!empty($sJapaneseLevel))
              $sJapaneseLevel =  $this->sanitize($sJapaneseLevel);
          else
              $sJapaneseLevel = '';

          $sRequirements = $this->getElementByClass($oElement,'ul','requirements_list');
          if(empty($sRequirements))
              $sRequirements = '';
        }
        // Description
        $oElement =  $this->getNextSibling($oElement);
        $sString = $this->sanitize($this->getElementByClass($oElement,'em','detail_title'));

        if($sString =='Description')
            $sDescription = $this->getElementByClass($oElement,'div','detail_cont');
        if(empty($sDescription))
            $sDescription = '';
               
        $sEnglishLevel = $this->detectLangLevel($sEnglishLevel);
        if(empty($sEnglishLevel))
           $sEnglishLevel = 0; 
        
        $sJapaneseLevel = $this->detectLangLevel($sJapaneseLevel);
        if(empty($sJapaneseLevel))
           $sJapaneseLevel = 0;
                
        $asData = array('position'      => $this->dosanitize($sPosition),
                        'company_name'  => $this->dosanitize($sCompany),
                        'jobID'         => $sJobID,
                        'location'      => $this->dosanitize($sLocation),
                        'post_date'     => $sPostDate,
                        'job_category'  => $this->dosanitize($sJobCategory),
                        'work_type'     => $this->dosanitize($sWorkType),
                        'job_type'      => $nJobType, 
                        'salary'        => $this->dosanitize($sSalary),
                        'salary_low'    => $nSalarylow,
                        'salary_high'   => $nSalaryHigh,
                        'english'       => $this->dosanitize($sEnglishLevel),
                        'japanese'      => $this->dosanitize($sJapaneseLevel),
                        'requirements'  => $this->dosanitize($sRequirements),
                        'job_description'=> $this->dosanitize($sDescription),
                        'weburlfk'      => $pnPnPk
                        );
      
        $sData = serialize($asData);

        if(!empty($sData) && !empty($sCompany))
        {
            $sQuery = 'INSERT INTO job (data,date_create,raw_content,weburlfk,websitefk) VALUES ('.$oDB->dbEscapeString($sData).','.$oDB->dbEscapeString(date('Y-m-d')).','.$oDB->dbEscapeString($oDomHTML->outertext).','.$oDB->dbEscapeString($pnPnPk).','.$this->nWebsitePk.')';
            $oDbResult = $oDB->ExecuteQuery($sQuery);

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
             //$nIndustryfk = $this->_getcheckIndustry($asPositions['job_category']);
             $nCompanyfk = $this->_getcheckCompany($asPositions['company_name']);
             
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
           echo $nCount.' new positions have been created on gaijinpot. <br/><br/>';
        }
        else
          echo 'There are no new positions to create today on gaijinpot <br/><br/>';

         $sQuery = 'UPDATE website SET last_update = "'.$sTime.'" , last_update_status= 1 WHERE websitepk = '.$this->nWebsitePk;
         $oDbResult = $oDB->ExecuteQuery($sQuery);

        return true;
      }
   }

?>
