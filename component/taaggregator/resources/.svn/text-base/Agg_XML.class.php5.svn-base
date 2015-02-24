<?php
require_once ('Agg_remote.class.php5');

class Agg_XML extends Agg_remote
{

 public function __construct()
  {
    return true;
  }

  protected function _parseXMLData($oData)
  {
    return $oData;
  }

  protected function _loadSimpleXml($psUrl)
  {
    if(!assert(!empty($psUrl)))
      return null;

    $asStreamOptions = array('http'=>array('timeout' => 10,'method'=>"GET", 'user_agent' => 'PHP-XML'));
    $oCxContext = stream_context_create($asStreamOptions);
    $sXml = @file_get_contents($psUrl, false, $oCxContext);
 
    if(empty($sXml))
      return null;

    //echo htmlentities($sXml);
    $oXml = new SimpleXMLElement($sXml);

    if($oXml === false)
      return null;

    return $oXml;
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

}

?>