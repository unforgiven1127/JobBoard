<?php

require_once('component/taaggregator/resources/Agg_HTML.class.php5');

class Agg_daijob extends Agg_HTML
{          
   protected $sContainer = 'table[class=search_result form]  a[id=_job]';
   protected $sPgContainer = 'div[id=result_box] ul li span[class=current_page]';
   protected $nWebsitePk = 4; 
   protected $sFirstElementTag = 'div';
   protected $sFirstID = 'left_content';
   
  public function __construct()
  {    
    $poWebsite = $this->_getDaiJobDetails();
    
    $this->sDaijobURL= $poWebsite->getFieldValue('search_url');
    $this->sDjURL = $poWebsite->getFieldValue('list_url');
    
     return true;   
  }
  
  public function __destruct()
  {
   //Destructor	
    $asError =  $this->error_get_line();
     if(!empty($asError))
      $this->notifyErrorMessage($asError);
    }
  
  /**
   * Get the Daijob Job Search URL
   * @return URL 
   */
      
   protected function _getDaiJobDetails()
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
   * Function to get the Job Postion URL from Daijob
   * @return boolean true 
   */
  
  public function saveUrl()
  { 
   // Get the job of the daijob.com 
    $this->_getJobURL($this->sDaijobURL,$this->sDjURL,$this->sContainer,$this->sPgContainer,$this->nWebsitePk);
    return true; 
   }

  /**
   * Create the Jobs from the Daijob URLS
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
         $nCount+= $this->getDaijobJob($sJObURL['url'],$sJObURL['website_joburlpk']);
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
      echo $nCount.' Jobs has been created successfully on Daijob <br/><br/>';
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
      
   public function getDaijobJob($psJObDetailURL,$pnPnPk)
   {
     $oDB = CDependency::getComponentByName('database');  
     if(!assert('!empty($psJObDetailURL) && !empty($pnPnPk)'))
        return false;     
    //Check if it is already created
    $bRecords = $this->_checkJobNotExist($pnPnPk,$this->nWebsitePk); 
    if($bRecords)
    {    
      $oDomHTML= $this->getHTMLContent($psJObDetailURL);
      if(!empty($oDomHTML))
      {    
        $oElement =  $this->getDOMStructureByID($oDomHTML,$this->sFirstElementTag,$this->sFirstID); 
     
        $sDate = $this->getElementByID($oElement,'div','update')->innertext;
        $sDate= $this->sanitize(trim($sDate));
        $asData = preg_split('/ /', $sDate);
    
        if(!empty($asData))
        {    
           if($asData[0]=='Updated')
            $sUpdatedDate = $asData[1];
           if($asData[3]=='Activated')
            $sPostedDate = $asData[4];
         }

         if(!empty($sUpdatedDate))
           $sPostedDate = $this->_getFormattedDate($sUpdatedDate);
         else
           $sPostedDate = $this->_getFormattedDate($sPostedDate);
        
         //If the position is two months old, do not create job it.
         
        $bOldJob = $this->_checkTwoMonth($sPostedDate,$pnPnPk);
        if($bOldJob)
          return true;
        
        //Position
        $oNewElement = $this->getElementByID($oElement,'table','position');
        $sString =  $this->sanitize($this->getDOMStructureByTag($oNewElement,'th')->innertext);
        if($sString=='Position')
        { 
            $sPosition =  $this->getDOMStructureByTag($oNewElement,'td');
            $sPosition= $this->sanitize($sPosition); 
           }

        // Recruiter 
        $oNewElement = $this->getElementByClass($oElement,'table','form','tr');
        $sString =  $this->sanitize($this->getDOMStructureByTag($oNewElement,'th'));
        if($sString=='Recruiter')
        {          
            $sRecruiter = $this->getElementByID($oNewElement,'p','company_name1');
            $sRecruiter =  $this->sanitize($sRecruiter);  
        }
        else if($sString=='Company Name')
        {
            $sCompanyName = $this->getDOMStructureByTag($oNewElement,'td');
            $sCompanyName =  $this->sanitize($sCompanyName);
        }

        //Company
        if(!empty($sRecruiter))
        {
            $oNewElement =  $this->getNextSibling($oNewElement); 
            $sString =  $this->sanitize($this->getDOMStructureByTag($oNewElement,'th'));
            if($sString=='Company Name')
            {   
                $sCompanyName = $this->getDOMStructureByTag($oNewElement,'td');
                $sCompanyName =  $this->sanitize($sCompanyName);  
              }
         }

        // Job Type
        $oElement =  $this->getNextSibling($oNewElement);
        $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
        if($sString=='Job Type')
            $sJobType = $this->sanitize($this->getDOMStructureByTag($oElement,'td'));

        // Industry
        $oElement =  $this->getNextSibling($oElement);
        $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
        if($sString=='Industry')
        {  
            $sIndustry = $this->getDOMStructureByTag($oElement,'td');
            $sIndustry =  $this->sanitize($sIndustry);  
         }

        if(!empty($sIndustry))
            $oElement =  $this->getNextSibling($oElement);  
        else
        {  
            $oElement =  $this->getPrevSibling($oElement);
            $oElement =  $this->getNextSibling($oElement);

         }
        // Location 
        $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
        if($sString=='Location')
        {  
            $sLocation = $this->getDOMStructureByTag($oElement,'td');
            $sLocation =  $this->sanitize($sLocation); 
           }

        //Job Description
        $oElement =  $this->getNextSibling($oElement);
        $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
        if($sString=='Job Description')
            $sDescription = $this->sanitize($this->getDOMStructureByTag($oElement,'td')); 

        $oElement =  $this->getNextSibling($oElement);
        $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
        if($sString=='Company Info')
        {   
            $oElement =  $this->getNextSibling($oElement);
            $sString = $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
         }
         
        if($sString=='Working Hours')
        {  
            $sWorkHours = $this->getDOMStructureByTag($oElement,'td');
            $sWorkHours =  $this->sanitize($sWorkHours); 
         }
         
        if($sString=='Qualifications')
        {          
            $sQualifications = $this->sanitize($this->getDOMStructureByTag($oElement,'td')); 
            $oElement =  $this->getNextSibling($oElement); 
            $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
        }
        
        if($sString=='English Level')
        {  
            $sEnglishLevel = $this->getDOMStructureByTag($oElement,'td');
            $nEnglishLevel =  $this->sanitize($sEnglishLevel); 
        }

        // Qualifications 
        if(empty($sQualifications))
        {   
            $oElement =  $this->getNextSibling($oElement);
            $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
            if($sString=='Qualifications')
            {          
                $sQualifications = $this->sanitize($this->getDOMStructureByTag($oElement,'td')); 
                $oElement =  $this->getNextSibling($oElement); 
                $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
            }
            
            if($sString=='English Level')
            {  
                $sEnglishLevel = $this->getDOMStructureByTag($oElement,'td');
                $nEnglishLevel =  $this->sanitize($sEnglishLevel); 
            }
        }
        
        if(!empty($sEnglishLevel))
            $oElement =  $this->getNextSibling($oElement);
        else
        { 
            $oElement =  $this->getPrevSibling($oElement);
            $oElement =  $this->getNextSibling($oElement);
            }
        // Japanese Level
        if(!empty($oElement))
        {
            $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
            if($sString=='Japanese Level')
            {  
                $sJapaneseLevel = $this->getDOMStructureByTag($oElement,'td');
                $nJapaneseLevel =  $this->sanitize($sJapaneseLevel); 
            }   

            $oElement =  $this->getNextSibling($oElement);
            $sString = $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
            if($sString=='Chinese Level')
            {  
                $sChineseLevel = $this->getDOMStructureByTag($oElement,'td');
                $sChineseLevel = $this->sanitize($sChineseLevel); 
            }
        }

        // Salary 
        if(!empty($oElement))
        {
            $oElement =  $this->getNextSibling($oElement);
            $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
            if($sString!='Salary')
            { 
                $oElement =  $this->getNextSibling($oElement);
                $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
            }
            if($sString!='Salary')
            {
                $oElement =  $this->getNextSibling($oElement);
                $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));  
            }
            if($sString=='Salary')
            {
                $sSalary = $this->getDOMStructureByTag($oElement,'td');
                $sSalary =  $this->sanitize($sSalary); 
            }
        }

        // Other Salary Description 
        if(!empty($oElement))
        {     
            if(!empty($oElement))
            {  
                $sNewString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
                if($sNewString!='Other Salary Description')
                {
                    $oElement =  $this->getNextSibling($oElement);
                    if(!empty($oElement))
                        $sNewString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));  
                 }
                if($sNewString!='Other Salary Description')
                {
                    $oElement =  $this->getNextSibling($oElement);
                    if(!empty($oElement))
                        $sNewString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));  
                 }
                if($sNewString!='Other Salary Description')
                {   
                    $oElement =  $this->getNextSibling($oElement);
                    if(!empty($oElement))
                        $sNewString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));  
                }
                if($sNewString!='Other Salary Description')
                {   
                    $oElement =  $this->getNextSibling($oElement);
                    if(!empty($oElement))
                        $sNewString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));  
                }
                if($sNewString!='Other Salary Description')
                { 
                    $oElement =  $this->getNextSibling($oElement);
                    if(!empty($oElement))
                        $sNewString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));  
                }
                if($sNewString!='Other Salary Description')
                {
                    $oElement =  $this->getNextSibling($oElement);
                    if(!empty($oElement))
                        $sNewString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));  
                }
                if($sNewString=='Other Salary Description')
                    $sSalaryDescription = $this->sanitize($this->getDOMStructureByTag($oElement,'td'));
              }
         }

        // Holidays 
        if(!empty($oElement))
        {
            $oElement =  $this->getNextSibling($oElement);
            $oElement =  $this->getNextSibling($oElement);
            if(!empty($oElement))
            {
                $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
                if($sString=='Holidays')
                {  
                    $sHolidays = $this->getDOMStructureByTag($oElement,'td');
                    $sHolidays =  $this->sanitize($sHolidays); 
                }
            }
        }

        // Job Contract Period

        if(!empty($oElement))
        { 
            $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
            if(empty($sString))
                $oElement =  $this->getNextSibling($oElement);  
                if(!empty($oElement))
                    $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
                    if($sString=='Job Contract Period')
                    {  
                        $sJobPeriod = $this->getDOMStructureByTag($oElement,'td');
                        $sJobPeriod =  $this->sanitize($sJobPeriod); 
                       }
                    else if($sString=='Nearest Station')
                        $sStation = $this->sanitize($this->getDOMStructureByTag($oElement,'td'));
              }

        // Nearest Station 

        if(empty($sStation))
        {    
            $oElement =  $this->getNextSibling($oElement);
            if(!empty($oElement))
            {   
                $sString= $this->sanitize($this->getDOMStructureByTag($oElement,'th'));
                if($sString=='Nearest Station')
                    $sStation = $this->sanitize($this->getDOMStructureByTag($oElement,'td'));
             }
        }
     
        if(!isset($sLocation))
           $sLocation= '';
        if(!isset($sIndustry))
           $sIndustry= '';
        if(!isset($sDescription))
           $sDescription = '';
        if(!isset($sRecruiter))
            $sRecruiter= '';
        if(!isset($sStation))
            $sStation = '';
        if(!isset($sJobPeriod))
            $sJobPeriod = '';
        if(!isset($sQualifications))
            $sQualifications = '';
       if(!isset($sHolidays))
            $sHolidays = '';
        if(!isset($sWorkHours))
            $sWorkHours = '';
        if(!isset($sSalaryDescription))
            $sSalaryDescription = '';
        if(!isset($sSalary))
            $sSalary = '';
         if(!isset($nEnglishLevel))
            $nEnglishLevel = 0;
          if(!isset($nJapaneseLevel))
            $nJapaneseLevel = 0;
       
        if(!empty($sSalary))
        {
          //Calculate the salary range
            
          $asSalary = explode('-',$sSalary);
                   
          if(!empty($asSalary) && isset($asSalary[1]))
          {   
            if(isset($asSalary[1]))
                $sLowerRange = $asSalary[1];
            
            if(isset($asSalary[2]))
               $sUpperRange  = $asSalary[2];
            
            if(!empty($sLowerRange))
              $nSalarylow = preg_replace("/[^0-9]/","",$sLowerRange);
             
            if(isset($nSalarylow) && !empty($nSalarylow))
            { 
              if($nSalarylow > 600)  
                $nSalarylow = ($nSalarylow*100)/12;
              else
                $nSalarylow = ($nSalarylow*100);  
             }
            else
            $nSalarylow = 0;   

            if(!empty($sUpperRange))
              $nSalaryHigh = preg_replace("/[^0-9]/","",$sUpperRange);
            
            if(isset($nSalaryHigh) && !empty($nSalaryHigh))
            {
              if($nSalaryHigh > 600) 
                $nSalaryHigh = ($nSalaryHigh*100)/12;
              else
                $nSalaryHigh = $nSalaryHigh*100;  
            }
             else
            $nSalaryHigh = 0;   
          }
        }
        
       if(empty($sSalary))
           $sSalary = '';
        
        if(!isset($nSalarylow) && empty($nSalarylow))
         $nSalarylow = 0;
        
        if(!isset($nSalaryHigh) && empty($nSalaryHigh))
          $nSalaryHigh = 0;        
          
        $nEnglishLevel = $this->detectLangLevel($nEnglishLevel);
        if(empty($nEnglishLevel))
           $nEnglishLevel = 0; 
        
        $nJapaneseLevel = $this->detectLangLevel($nJapaneseLevel);
        if(empty($nJapaneseLevel))
          $nJapaneseLevel = 0;
         
          $asData = array(
                          'position'          => $this->dosanitize($sPosition),						 
                          'company_name'      => $this->dosanitize($sCompanyName),
                          'location'          => $this->dosanitize($sLocation),
                          'post_date'         => $this->dosanitize($sPostedDate),
                          'updated_date'      => $this->dosanitize($sUpdatedDate),
                          'job_category'      => $this->dosanitize($sJobPeriod),
                          'job_type'          => $this->dosanitize($sJobType),
                          'work_type'         => 1,
                          'salary'            => $this->dosanitize($sSalary),
                          'salary_low'        => $nSalarylow,
                          'salary_high'       => $nSalaryHigh,
                          'english'           => $nEnglishLevel,
                          'japanese'          => $nJapaneseLevel,
                          'requirements'      => $this->dosanitize($sQualifications),
                          'job_description'   => $this->dosanitize($sDescription),
                          'salary_desc'       => $this->dosanitize($sSalaryDescription),
                          'holidays'          => $this->dosanitize($sHolidays),
                          'Industry'          => $this->dosanitize($sIndustry),
                          'recruiter'         => $this->dosanitize($sRecruiter),
                          'work_hours'        => $this->dosanitize($sWorkHours),
                          'near_station'      => $this->dosanitize($sStation),
                          'weburlfk'          => $pnPnPk
                           );
        
        

       $sData = serialize($asData);
        
       if(!empty($sData) && !empty($sCompanyName))
       {
            $sQuery = 'INSERT INTO job (data,date_create,raw_content,weburlfk,websitefk) VALUES ('.$oDB->dbEscapeString($sData).','.$oDB->dbEscapeString(date('Y-m-d')).','.$oDB->dbEscapeString($oDomHTML->outertext).','.$oDB->dbEscapeString($pnPnPk).','.$this->nWebsitePk.')';
            $oDbResult = $oDB->ExecuteQuery($sQuery);
            
            $sQuery = 'UPDATE website_joburl SET status = 1 where website_joburlpk='.$pnPnPk.'';
            $oDbResult = $oDB->ExecuteQuery($sQuery);
            
            if(!$oDbResult)
                return false;
            else
                return true;
             }
          }
       }
      else 
         return false;;
     }
     
    /**
      * Create Position from the Daijob Jobs
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
            $sResult = $this->_checkPosition((int)$asRecords['jobpk']);
            if($sResult)
            {          
                $asPositions = unserialize($asRecords['data']);  
                $nIndustryfk = 0;
                //$nIndustryfk = $this->_getcheckIndustry($asPositions['Industry']);
                $nCompanyfk = $this->_getcheckCompany($asPositions['company_name']);
                
                $sQuery = 'INSERT INTO position (jobfk,visibility,industryfk,career_level,position_title,';
                $sQuery.= ' position_desc,requirements,status,companyfk,location,posted_date,salary,english,japanese,';
                $sQuery.= ' salary_low,salary_high,holidays,station,work_hours,job_type,temp_industry)';
                $sQuery.= 'VALUES ('.$oDB->dbEscapeString($asRecords['jobpk']).',1,'.$oDB->dbEscapeString($nIndustryfk).',';
                $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['job_type'])).',';
                $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['position'])).'';
                $sQuery.= ','.$oDB->dbEscapeString($this->sanitize($asPositions['job_description'])).',';
                $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['requirements'])).',';
                $sQuery.= ' 0,'.$oDB->dbEscapeString($nCompanyfk).',';
                $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['location'])).',';
                $sQuery.= ''.$oDB->dbEscapeString($asPositions['post_date']).',';
                $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['salary'])).',';
                $sQuery.= ''.$oDB->dbEscapeString($asPositions['english']).',';
                $sQuery.= ''.$oDB->dbEscapeString($asPositions['japanese']).',';
                $sQuery.= ''.$oDB->dbEscapeString($asPositions['salary_low']).',';
                $sQuery.= ''.$oDB->dbEscapeString($asPositions['salary_high']).',';
                $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['holidays'])).',';
                $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['near_station'])).',';
                $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['work_hours'])).',';
                $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['work_type'])).',';
                $sQuery.= ''.$oDB->dbEscapeString($this->sanitize($asPositions['Industry'])).')';
                
                         
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
           echo $nCount.' new positions have been created on Daijob. <br/><br/>';
         }
         else
            echo 'There are no new positions to create today on Daijob <br/><br/>';
        
         $sQuery = 'UPDATE website SET last_update = "'.$sTime.'" , last_update_status= 1 WHERE websitepk = '.$this->nWebsitePk;
         $oDbResult = $oDB->ExecuteQuery($sQuery); 
        
        return true;
      }           
   }

?>
