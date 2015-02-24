<?php
require_once ('Aggregator.class.php5');

class Agg_remote extends Aggregator
{
  public function __construct()
  {
    return true;
  }

  protected function _checkPosition($pnJobpk)
  {
    $oDB = CDependency::getComponentByName('database');

    if(!assert('!empty($pnJobpk)'))
       return false;

    $sQuery = 'SELECT * FROM position WHERE jobfk='.$pnJobpk.'';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if($bRead)
      return false;
    else
      return true;
  }

   protected function checkLanguageLevel($psString,$psType)
   {
     switch($psType)
      {
        case 'basic':
           $asBasic = preg_match('/(basic)/', $psString);
              break;

          case 'conversational':
             $asConversation = preg_match('/(conversational)/', $psString);
               break;

           case 'business':
             $asBusiness = preg_match('/(business)/', $psString);
                break;

            case 'fluent':
              $asFluent = preg_match('/(fluent)/', $psString);
                break;

          case 'native':
            $asNative = preg_match('/(native)/', $psString);
                break;

          default:
             return '';

          }

          if(!empty($asBasic))
              return 1;
          if(!empty($asConversation))
              return 2;
          if(!empty($asBusiness))
              return 3;
          if(!empty($asFluent))
              return 4;
          if(!empty($asNative))
              return 5;
       }

      protected function detectLangLevel($psString)
      {
        $psString = strtolower($psString);

        $nVal =  $this->checkLanguageLevel($psString,'native');
        if(!isset($nVal) || empty($nVal))
          $nVal =  $this->checkLanguageLevel($psString,'fluent');
        if(!isset($nVal) || empty($nVal))
          $nVal =  $this->checkLanguageLevel($psString,'business');
        if(!isset($nVal) || empty($nVal))
          $nVal =  $this->checkLanguageLevel($psString,'conversational');
        if(!isset($nVal) || empty($nVal))
          $nVal =  $this->checkLanguageLevel($psString,'basic');

        return $nVal;
      }


      protected function _getcheckIndustry($psIndustryName, $pnParentId = 0)
      {
        $oDB = CDependency::getComponentByName('database');

        if(empty($psIndustryName))
           return 0;

        $sQuery = 'SELECT * FROM industry WHERE name like "%'.$psIndustryName.'"';
        $oResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oResult->readFirst();

        if($bRead)
        {
          echo 'Industry '.$psIndustryName.' exists. <br />';
          return $oResult->getFieldValue('industrypk', CONST_PHP_VARTYPE_INT);
        }


        if(empty($pnParentId))
        {
          echo 'Industry not found. '.$psIndustryName.' but can not create it. <br />';
          return 0;
        }

        //----------------------------------------------------------------
        //function used only for slistem aggregator, protected by parentId
        echo 'Industry not found. '.$psIndustryName.' we are creating it. <br />';
        switch($pnParentId)
        {
          case 500: $nParentfk = 503; break;  //CNS

          case 501:
          case 505:
            $nParentfk = 504; break;          //Energy & AUTOM -> Industrial

          case 502: $nParentfk = 501; break;  // FInance
          case 503: $nParentfk = 502; break;  //IT
          case 504: $nParentfk = 506; break;  //healthcare

          default:  $nParentfk = 500; break;  //"other" an any non matching -> other
        }

        $sQuery = 'INSERT INTO  industry (`name`, `status`, `parentfk`) VALUES('.$oDB->dbEscapeString($psIndustryName).', 2, '.$nParentfk.')';
        $oResult = $oDB->ExecuteQuery($sQuery);
        $nIndustryPk = $oResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT);

        return $nIndustryPk;
      }

      protected function _getcheckCompany($psCompanyName)
      {
        $oDB = CDependency::getComponentByName('database');

        if(empty($psCompanyName))
           return 0;
        else
        {
          $sQuery = 'SELECT * FROM company WHERE company_name = "'.$psCompanyName.'"';
          $oResult = $oDB->ExecuteQuery($sQuery);
          $bRead = $oResult->readFirst();

          if($bRead)
              return $oResult->getFieldValue('companypk',CONST_PHP_VARTYPE_INT);
          else
          {
            $sQuery = 'INSERT INTO  company (`company_name`,`status`) VALUES("'.$psCompanyName.'",1)';
            $oResult = $oDB->ExecuteQuery($sQuery);

            $pnCompanyPk = $oResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT);

            return $pnCompanyPk;
            }
         }
      }

   }

?>