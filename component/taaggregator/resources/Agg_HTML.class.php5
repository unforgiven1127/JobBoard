<?php

require_once ('Agg_remote.class.php5');
require_once ('HTMLDOM/simple_html_dom.php');

class Agg_HTML extends Agg_remote
{
   protected $csHTMLElement;
   protected $csCompanyID;
   protected $csContainer;
   protected $csPgContainer;
   protected $csPosition;
   protected $csCompanyName;
   protected $csLocation;
   protected $csPostedDate;
   protected $csCategory;
   protected $csWorkType;
   protected $csSalary;
   protected $csEnglishLevel;
   protected $csJapaneseLevel;
   protected $csRequirements;
   protected $csDescription;
   
  public function __construct()
  {
    return true;
   }
  
 /**
  * Get the Job Detail URL
  * @param string $sURL
  * @param string $sListURL
  * @param string $sContainer
  * @param string $sPgContainer 
  * @return boolean
  */ 
  protected function _getJobURL($psURL,$psListURL,$psContainer,$psPgContainer,$pnWebsitefk)
  { 
   $oDB = CDependency::getComponentByName('database');   
   
   if(!assert('!empty($psURL) && !empty($psListURL) && !empty($psContainer) && !empty($psPgContainer) && !empty($pnWebsitefk)'))
      return false;
 
   $oDomHTML= $this->getHTMLContent($psURL);
   $bResult = $this->checkURLNotExist($psURL);
   $pnCounter = 0;
   
   if($bResult)
   { 
     $sQuery = 'INSERT INTO website_joburl (websitefk,parentfk,url) VALUES ('.$pnWebsitefk.',0,"'.$psURL.'")';
     $oDbResult = $oDB->ExecuteQuery($sQuery);
     $nInsertId = $oDbResult->getFieldValue('pk');
     if(!$oDbResult)
        return false;
      
     $sQuery = 'UPDATE website SET search_url = "'.$psURL.'" where websitepk ='.$pnWebsitefk;
     $oDbResult = $oDB->ExecuteQuery($sQuery);
     if(!$oDbResult)
        return false;
    
     //Delay 
     sleep($this->nDelay);
     
     foreach($oDomHTML->find($psContainer) as $oElement)
        $asArray[]= $oElement->href;
     
     foreach($asArray as $sJobURL)
     {  
       if($pnWebsitefk==1)
         $sNoDuplicate= $this->getCheckDuplicate($pnWebsitefk,$psListURL.$sJobURL);
       else 
         $sNoDuplicate = true;
       
       if($pnWebsitefk!=2)
       { 
        if($sNoDuplicate)
        {    
          $sResult = $this->checkURLNotExist($psListURL.$sJobURL);
          if($sResult)
          {   
              $sQuery = 'INSERT INTO website_joburl (websitefk,parentfk,url) VALUES ('.$pnWebsitefk.','.$nInsertId.',"'.$psListURL.$sJobURL.'")';
              $oDbResult = $oDB->ExecuteQuery($sQuery);

              if(!$oDbResult)
                return false;

              $this->_logMessage($psListURL.$sJobURL);
              $pnCounter++;
            }   
          }
        }
        else
        {
          $sResult = $this->checkURLNotExist($sJobURL);
          if($sResult)
          {   
              $sQuery = 'INSERT INTO website_joburl (websitefk,parentfk,url) VALUES ('.$pnWebsitefk.','.$nInsertId.',"'.$sJobURL.'")';
              $oDbResult = $oDB->ExecuteQuery($sQuery);

              if(!$oDbResult)
                return false;

              $this->_logMessage($sJobURL);
              $pnCounter++;
            }
          }
        }
     }
    else
     {
        //If the parent page already exists , check for all the childs
        $sQuery = 'SELECT * FROM  website_joburl WHERE url ="'.$psURL.'"';
        $oDbResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oDbResult->readFirst();
        if(!$bRead)
            return false;
        else
            $nParentfk = $oDbResult->getFieldValue('website_joburlpk');
      
        $sQuery = 'SELECT url FROM website_joburl WHERE parentfk ="'.$nParentfk.'"';
        $oDbResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oDbResult->readFirst();
        
        while($bRead)
        {
          $asRecords =  $oDbResult->getData(); 
          $bRead = $oDbResult->readNext();
          }
        
        if(!empty($asRecords))
         {
            foreach($oDomHTML->find($psContainer) as $oElement)
                $asArray[]= $psListURL.$oElement->href;
      
            foreach($asRecords as $sJobURLs)
                $asJobURLArray[] = $sJobURLs['url'];
      
            foreach($asArray as $sJobURL)
            { 
              if(!in_array($sJobURL,$asJobURLArray))
                {
                if($pnWebsitefk==1)   
                   $bNoDuplicate= $this->getCheckDuplicate($pnWebsitefk,$psListURL.$sJobURL);
                else 
                    $bNoDuplicate = true; 
        
                 if($bNoDuplicate)
                   {   
                    $sResult = $this->checkURLNotExist($sJobURL);   
                    if($sResult)
                       { 
                         $sQuery = 'INSERT INTO website_joburl (websitefk,parentfk,url) VALUES ('.$pnWebsitefk.','.$nParentfk.',"'.$sJobURL.'")';
                         $oDbResult = $oDB->ExecuteQuery($sQuery);
                       
                         if(!$oDbResult)
                            return false;
                          
                        $this->_logMessage($sJobURL);
                        $pnCounter++;
                     }
                  }
               }
            }
        }
    }
   
  if($pnWebsitefk != 2)
  {   
    $sNext = $this->_getNextPage($oDomHTML,$psPgContainer);
    if(!empty($sNext))
      $sNextURL= $psListURL.$sNext;
  }
  else
  {
    //Get the offset value
    $nOffset = substr($psURL,75);
    $sNextURL = $psListURL.'&offset='.($nOffset+30).'';
    
    if($nOffset >1000)
    {
      $sResult=$this->_getResetSearchURL($pnWebsitefk);
        if($sResult)
          echo 'Job URLs Importation has been completed <br/><br/>';
        
          return true;
     }
   }
    
  if(!empty($sNextURL) && ($sNextURL!=$psURL))
  {
     $sQuery = 'UPDATE website SET search_url = "'.$sNextURL.'" where websitepk ='.$pnWebsitefk;
     $oDbResult = $oDB->ExecuteQuery($sQuery);
     if(!$oDbResult)
       return false;
      
      echo $pnCounter.' Job URLs has been saved <br/><br/>';      
     }
     else
     {    
        $sResult=$this->_getResetSearchURL($pnWebsitefk);
        if($sResult)
           echo 'Job URLs Importation has been completed <br/><br/>';
     }
     return true;
   }
   
   /**
    * Get the jobs 
    * @param integer $pnWebsitPk
    * @return array with jobs data
    */
   
   public function getJObForPosition($pnWebsitePk)
   {
     if(!assert('!empty($pnWebsitePk) && is_integer($pnWebsitePk)'))
        return false;
     
     $oDB = CDependency::getComponentByName('database');
     $asData = array();
     if(!empty($pnWebsitePk))
     {
        $sQuery = 'SELECT * FROM job WHERE websitefk ='.$pnWebsitePk.' AND status = 0 ORDER BY jobpk ASC limit '.$this->nLimit;
        $oDbResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oDbResult->readFirst();
        while($bRead)
        {
           $asData[] = $oDbResult->getData(); 
           $bRead = $oDbResult->readNext(); 
        }
        return $asData;
      }
    }
   
   /**
     * Check if it is the correct page
     * @param string  $sURL
     * @return boolean 
     */
    protected function _isDesiredPage($psURL,$psFirstElementTag,$psFirstItem,$psElementType)
    {
     if(!assert('!empty($psURL) && !empty($psFirstElementTag) && !empty($psFirstItem) && !empty($psElementType)'))
        return false;
      
      $oDomHTML= $this->getHTMLContent($psURL);
      if($psElementType=='CLASS')
        $oElement =  $this->getDOMStructureByClass($oDomHTML,$psFirstElementTag,$psFirstItem);  
      else  if($psElementType=='ID')
        $oElement =  $this->getDOMStructureByID($oDomHTML,$psFirstElementTag,$psFirstItem);
      else
        $oElement = NULL;  
      
     if(!empty($oElement))  
       return true;
     else
       return false;
    }    
    
   /**
    * Reset the search URL  
    * @param object $poWebsite 
    */
   protected function _getResetSearchURL($pnWebsitePk)
   {   
     if(!assert('!empty($pnWebsitePk) '))
        return false;
       
     $oDB = CDependency::getComponentByName('database');
     
     $asURLArray = array('1'=>'https://jobs.gaijinpot.com/index/index/page/','2'=>'enworld.com/jp-en/jobs/search/?sort=date&order=desc&limit=30','3'=>'http://jobs.gecareers.com/search?q=japan','4'=>'http://www.daijob.com/en/jobs/search?li=1&pg=0&fl=1'); 
     $sSearchURL = $asURLArray[$pnWebsitePk];
    
     $sQuery = 'UPDATE website SET search_url = "'.$sSearchURL.'" where websitepk ='.$pnWebsitePk.'';
     $oDbResult = $oDB->ExecuteQuery($sQuery);
        if(!$oDbResult)
           return false;
     
     return true;
   }
   
   /**
    * Get Next Page URL
    * @param object $poDomHTML
    * @param string $psPgContainer
    * @return link string
    */
   
   protected function _getNextPage($poDomHTML,$psPgContainer)
   { 
     if(!assert('!empty($poDomHTML) && !empty($psPgContainer)'))
        return '';
     
     $oPagination = $poDomHTML->find($psPgContainer,0);
     
     if(!empty($oPagination))
     {
        $oElement =  $oPagination->next_sibling();
        if($oElement)
          {   
            $sNext = $oPagination->next_sibling()->href;
               return $sNext;
           }       
        }
        else
          return '';        
     }
    
    /**
     * Get HTML from Web URL
     * @param string URL $sURL
     * @return object
     */
    
    protected function getHTMLContent($psURL)
    {
      if(!assert('!empty($psURL)'))
      return NULL;
      
      ini_set('max_execution_time',0); 
      $asOpts = array('http'=>array('timeout' => 40,'method'=>"GET"));

      $sContext = stream_context_create($asOpts);
        
      $oDomHTML = new simple_html_dom();
      $oDomHTML = @file_get_html($psURL,false,$sContext);
        
      return $oDomHTML;
     }      
    
    /**
     * Get the HTML DOM Structure
     * @param string $sElement
     * @param string $sElementTag
     * @param string $sClassName
     * @return string 
     */
    protected function getDOMStructureByTag($poElement,$psElementTag)
    {
      if(!assert('!empty($poElement) && !empty($psElementTag)'))
         return NULL;
       
      return $poElement->find($psElementTag,0);
        
    }    
    
    /**
     * Get the HTML DOM Structure
     * @param string $sElement
     * @param string $sElementTag
     * @param string $sClassName
     * @return string 
     */
    protected function getDOMStructureByClass($poElement,$psElementTag,$psClassName)
    {  
       if($poElement) 
          return $poElement->find($psElementTag.'[class='.$psClassName.']',0); 
       else
          return NULL; 
     }
    
    /**
     * Get the HTML DOM Structure
     * @param string $sElement
     * @param string $sElementTag
     * @param string $sClassName
     * @return string 
     */
    protected function getDOMStructureByID($poElement,$psElementTag,$psIDName)
    { 
      if($poElement)  
         return  $poElement->find($psElementTag.'[id='.$psIDName.']',0);
      else
         return NULL; 
     }
    
    /**
     * Get the First Child of the element
     * @param string $sElement
     * @return string 
     */
    
    protected function getFirstChild($poElement)
    {
      if(!assert('!empty($poElement)'))
        return NULL;
        
      return  $poElement->first_child();
     }
    
    /**
     * Get the Next Sibling of the element
     * @param string $sElement
     * @return string 
     */
    
    protected function getNextSibling($poElement)
    {
       if($poElement) 
          return $poElement->next_sibling();
       else 
          return NULL; 
     }
    
     /**
     * Get the Previous Sibling of the element
     * @param string $sElement
     * @return string 
     */
    
    protected function getPrevSibling($poElement)
    {
      if($poElement)  
        return  $poElement->prev_sibling();
      else
        return NULL;  
    }
   
     /**
     * Get the Parent of the element
     * @param string $sElement
     * @return string 
     */
    protected function getParent($poElement)
    {
      if(!assert('!empty($poElement)'))
        return NULL;
      
      return $poElement->parent();
     }
    
     /**
     * Get the Element using class name
     * @param string $sElement
     * @param string $sElementTag
     * @param string $sClassName
     * @return string 
     */
    protected function getElementByClass($poElement,$psElementTag,$psClassName,$psSmallTag='')
    {
      if(!assert('!empty($poElement) && !empty($psElementTag) && !empty($psClassName)'))
        return NULL;
      
      return  $poElement->find($psElementTag.'[class='.$psClassName.'] '.$psSmallTag.'',0);
     }
     
    /**
     * Get the Element using ID
     * @param string $sElement
     * @param string $sElementTag
     * @param string $sIDName
     * @return string 
     */
    protected function getElementByID($poElement,$psElementTag,$psIDName)
    {
       if(!assert('!empty($poElement) && !empty($psElementTag) && !empty($psIDName)'))
          return NULL;
       
        return $poElement->find($psElementTag.'[id='.$psIDName.']',0);
      }
     
     /**
      * Get all the lists of ul
      * @param string $sElement
      * @param string $sClassName 
      */
     
     public function getCheckTextFromList($poElement,$psClassName,$psTextName)
     {
       if(!assert('!empty($poElement) && !empty($psClassName) && !empty($psTextName)'))
         return array(''); 
         
       foreach($poElement->find('ul[class='.$psClassName.']') as $sList)
       {       
         foreach($sList->find('li') as $sListItem)
         {             
             $asList[] =  $this->checkTextNReturnValue($psTextName,$sListItem); 
          }
         return $asList;
        }
     }
    
    /**
     * Return the value after comparing text
     * @param type $sString
     * @param type $sElement
     * @return type string
     */
    
    protected function checkTextNReturnValue($psString,$poElement)
    { 
      if(!assert('!empty($poElement) && !empty($psString)'))
        return ''; 
      
       if(preg_match('/'.$psString.'/',$poElement))
       {          
         $sValString = preg_replace('/'.$psString.'/', '',$poElement);
            return strip_tags($sValString);
         }
         else
            return '';
      }      
      
     
  }

?>