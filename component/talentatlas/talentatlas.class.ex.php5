<?php

require_once('component/talentatlas/talentatlas.class.php5');
require_once('component/taaggregator/resources/lib/encoding_converter.class.php5');


class CTalentatlasEx extends CTalentatlas
{
  public function __construct()
  {
    return true;
  }

  public function getDefaultType()
  {
    return CONST_TA_TYPE_JOB;
  }

  public function getDefaultAction()
  {
    return CONST_TALENT_HOME_PAGE;
  }

  //====================================================================
  //  accessors
  //====================================================================

  //====================================================================
  //  interface
  //====================================================================

  public function getPageActions($psAction = '', $psType = '', $pnPk = 0)
  {
    $asActions = array();

  /*@var $oPage CPageEx */
  $oPage = CDependency::getComponentByName('page');

  $asActions['ppav'][] = array('title'=>$this->casText['TALENT_HOMEPAGE'], 'url' => $oPage->getUrl($this->_getUid()));
  $asActions['ppav1'][] = array('title'=>' > '.$this->casText['TALENT_SEARCH_JOBS'], 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST,''));

  switch($psAction)
  {
    case CONST_ACTION_APPLY:

        $asActions['ppav2'][] = array('title'=>' > '.$this->casText['TALENT_JOB_DETAIL'], 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW, '', $pnPk));
        $asActions['ppav3'][] = array('title'=>' > '.$this->casText['TALENT_JOB_APPLY'].$pnPk, 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_APPLY, '', $pnPk));
      break;

      case CONST_ACTION_VIEW:
        $asActions['ppav2'][] = array('title'=>' > '.$this->casText['TALENT_JOB_DETAIL'], 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW, '', $pnPk));
        break;

      default: break;
    }
    return $asActions;
  }

  public function getAjax()
  {
    $this->_processUrl();

    switch($this->csType)
    {
       case CONST_TA_TYPE_JOB:

        switch($this->csAction)
        {
            case CONST_ACTION_LIST:
              return json_encode($this->_getAjaxJobSearchResult());
                break;

             case CONST_ACTION_EMAIL:
               return json_encode($this->_getMailFriendForm());
                break;

            case CONST_ACTION_SAVEADD:
              return json_encode($this->_getMailFriendSave());
               break;
        }
        break;
     }
  }

  //====================================================================
  //  public methods
  //====================================================================

  public function getHtml()
  {
    $this->_processUrl();

    switch($this->csType)
     {
       case CONST_TA_TYPE_JOB:

         switch($this->csAction)
         {
          case CONST_ACTION_LIST:
           return $this->getJobList();
            break;

          case CONST_ACTION_VIEW:
           return $this->getJobDetail($this->cnPk);
            break;

          case CONST_ACTION_APPLY:
           return $this->getJobApply($this->cnPk);
            break;

         case CONST_ACTION_SAVEADD:
           return $this->_getResumeSave($this->cnPk);
            break;

         default:
           case CONST_TALENT_HOME_PAGE:
            return $this->_getHomePage();
             break;
        }
      break;
    }
  }

  /**
   * Send Email to the friend
   * @return array
   */

  private function _getMailFriendForm()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $pnPk = getValue('positionpk');

    $oForm = $oHTML->initForm('mailFriendForm');
    $sFormId = $oForm->getFormId();
    $sURL = $oPage->getAjaxUrl($this->_getUid(), CONST_ACTION_SAVEADD, CONST_TA_TYPE_JOB, 0, array('positionpk'=>$pnPk));
    $sJs = $oHTML->getAjaxJs($sURL, 'body', $sFormId);
    $oForm->setFormParams('', false, array('submitLabel' => 'Send','action' => '', 'onsubmit' => 'event.preventDefault(); '.$sJs));
    $oForm->setFormDisplayParams(array('noCancelButton' => '1','noCloseButton' => '1'));

    $sHTML = $oHTML->getBlocStart('');

    $oForm->addField('input', 'name', array('label'=> 'Your Name', 'value' => ''));
    $oForm->setFieldControl('name', array('jsFieldNotEmpty' => ''));

    $oForm->addField('input', 'email', array('label'=> 'Your Email ', 'value' =>''));
    $oForm->setFieldControl('email', array('jsFieldTypeEmail' => '','jsFieldNotEmpty' => ''));

    $oForm->addField('input', 'femail1', array('label'=> 'Friend Email 1', 'value' =>''));
    $oForm->setFieldControl('femail1', array('jsFieldTypeEmail' => '','jsFieldNotEmpty' => ''));

    $oForm->addField('input', 'femail2', array('label'=> 'Friend Email 2', 'value' =>''));
    $oForm->setFieldControl('femail2', array('jsFieldTypeEmail' => ''));

    $oForm->addField('input', 'femail3', array('label'=> 'Friend Email 3', 'value' =>''));
    $oForm->setFieldControl('femail3', array('jsFieldTypeEmail' => ''));

    $oForm->addField('textarea', 'message', array('label'=> 'Message  ', 'value' =>'','style'=>'width:400px;height:110px;'));
    $oForm->setFieldControl('message', array('jsFieldNotEmpty' => ''));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();

    return $oPage->getAjaxExtraContent(array('data'=>$sHTML));
  }

  /**
   * Send Email to friend about the position and save record in database
   */

  private function _getMailFriendSave()
  {
    $oDB = CDependency::getComponentByName('database');
    $oMail = CDependency::getComponentByName('mail');
    $oPage = CDependency::getComponentByName('page');

    $nPositionPk   = getValue('positionpk');
    $sSenderName   = getValue('name');
    $sSenderEmail  = getValue('email');
    $sFriendEmail1 = getValue('femail1');
    $sFriendEmail2 = getValue('femail2');
    $sFriendEmail3 = getValue('femail3');
    $sMessage      = getValue('message');

    $sQuery = 'SELECT pos.*,cp.company_name,ind.name AS industry_name FROM position AS pos LEFT JOIN company ';
    $sQuery.= ' AS cp ON cp.companypk = pos.companyfk LEFT JOIN industry AS ind ON pos.industryfk = ind.industrypk WHERE pos.visibility <> 0 and pos.positionpk = '.$nPositionPk.'';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if($bRead)
    {
      $asData = $oDbResult->getData();

      $sCompanyName = $asData['company_name'];
      $sIndustryName = $this->casText[$asData['industry_name']];
      $sPositionName = $asData['position_title'];
      $sSalary = $asData['salary'];

    }
    //Get the content of the email

    $sSubject = 'Your friend '.$sSenderName.' has forwarded you a job  on talent atlas';
    $sLink = $oPage->getUrl('talentatlas',CONST_ACTION_VIEW,CONST_TA_TYPE_JOB,(int)$nPositionPk);

    $sContent = ' Hello, <br/>';
    $sContent.= ' This is a job suggestion coming from <a href="www.talentatlas.com">Talentatlas.com </a> <br/>';
    $sContent.= ' '.$sSenderName.' ('.$sSenderEmail.') has requested you to be  notified concerning the following position.<br/>';
    $sContent.= ' Message : <div style="margin:5px;padding:5px;">'.$sMessage.'</div>';
    $sContent.= ' <div style="font-family: Verdana,Helvetica,Arial; font-size: 11px; border: 1px solid #dedede; padding: 5px; margin: 5px;">';
    $sContent.= ' Url : <a href = "'.$sLink.'">'.$sLink.'</a> <br/> <br/>';

    $sContent.= ' Title : '.$sPositionName.'<br/>';
    $sContent.= ' Industry : '.$sIndustryName.'<br/>';
    if(!empty($sCompanyName))
      $sContent.= ' Company : '.$sCompanyName.'<br/>';

    $sContent.= ' Salary : '.$sSalary.'<br/>';
    $sContent.= ' Description:<br /><div style="margin: 5px; padding: 5px; ">'.nl2br($asData['position_desc']).'</div><br/><br/>';
    $sContent.= ' <div style ="font-size:10px;font-weight:bold;font-style:italic;">see the full description <a href = "'.$sLink.'"> here </a> </div>.<br/>';
    $sContent.= ' </div> <br/>';

    $sContent.= ' Best Regards <br/><br/>';
    $sContent.= ' If you refuse to receive any more email from our website, please click  <a href="#"> here.</a> ';

    $oMail->sendRawEmail($sSenderEmail,$sFriendEmail1,$sSubject,$sContent);

    if(!empty($sFriendEmail2))
      $oMail->sendRawEmail($sSenderEmail,$sFriendEmail2,$sSubject,$sContent);
    if(!empty($sFriendEmail3))
      $oMail->sendRawEmail($sSenderEmail,$sFriendEmail3,$sSubject,$sContent);

    $sQuery = 'INSERT INTO job_mailfriend ( `sender_name`, `sender_email`, `receiver_email1`, `receiver_email2`, `receiver_email3`, `message`, `positionfk`) ';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString($sSenderName).','.$oDB->dbEscapeString($sSenderEmail).',';
    $sQuery.= ''.$oDB->dbEscapeString($sFriendEmail1).','.$oDB->dbEscapeString($sFriendEmail2).',';
    $sQuery.= ''.$oDB->dbEscapeString($sFriendEmail3).','.$oDB->dbEscapeString($sMessage).',';
    $sQuery.= ''.$oDB->dbEscapeString($nPositionPk).')';

    $oDbResult = $oDB->ExecuteQuery($sQuery);

    if($oDbResult)
      return array('message' => 'Email successfully sent','reload'=>1);
  }

  /**
   * Function in Ajax  to get the jobs
   * @return array with data and message
   */

  private function _getAjaxJobSearchResult()
  {
    if(getValue('do_search', 0))
      $sSearchId = manageSearchHistory($this->csUid, CONST_TA_TYPE_JOB, true);
    else
    {
      if(getValue('searchId'))
        $sSearchId = manageSearchHistory($this->csUid, CONST_TA_TYPE_JOB);
      else
        $sSearchId = reloadLastSearch($this->csUid, CONST_TA_TYPE_JOB);
    }

    //Populate the sidebar things
    $sHiddenHTML = $this->_getJobSearchFilterSection(true);
    $avResult = $this->_getJobSearchResult('', $sSearchId);

    if(empty($avResult) || empty($avResult['nNbResult']) || empty($avResult['oData']))
    {
      $oHTML = CDependency::getComponentByName('display');
      $sMessage = $this->_getSearchMessage($avResult['nNbResult']);
      return array('data' => $oHTML->getBlocMessage($this->casText['TALENT_NO_RESULT']), 'action' => '$(\'#leftsectionSearch\').html(\''.addslashes($sHiddenHTML).'\');');
     }

    //in ajax, the dummy form should always be hidden
    $sData = $this->_getJobResultList($avResult, $sSearchId, false);

    if(empty($sData) || $sData == 'null' || $sData == null)
       return array('data' => $this->casText['TALENT_SORRY_ERROR'], 'action' => '$(\'.searchTitle\').html(\''.addslashes($sMessage).'\'); searchTitle(\'\', false, false); $(\'body\').scrollTop();');

    $sMessage = $this->_getSearchMessage($avResult['nNbResult'], true);
    $sData =  CEncoding::toUTF8($sData);

     return array('data' => $sData,'action' => '$(\'.searchTitle\').html(\''.addslashes($sMessage).'\');$(\'#leftsectionSearch\').html(\''.addslashes($sHiddenHTML).'\'); searchTitle(\'\', false, true); $(\'body\').scrollTop();');
   }

  /**
   * Function to display the search message
   * @param type $pnNbResult
   * @return type
   */

  private function _getSearchMessage($pnNbResult = 0)
  {
    $sMessage = $this->casText['TALENT_RESULTS_MATCHING'];

    $oHTML = CDependency::getComponentByName('display');
    return $oHTML->getText($pnNbResult.' ').$sMessage;
   }

   /**
    * Function to get the job search result
    * @param string $psQueryFilter
    * @param string $psSearchId
    * @return array
    */

   private function _getJobSearchResult($psSearchId = '')
   {
    $oDb = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');
    $sToday = date('Y-m-d');
    $sLang = $oPage->getLanguage();

    $sQuery = 'SELECT count(DISTINCT pos.positionpk) as nCount FROM position as pos';

    $nCompanyPk = (int)getValue('companypk', 0);
    $nIndustryPk = (int)getValue('industrypk', 0);
    $sExtaSql = '';

    if(!empty($nIndustryPk))
      $sQuery.= ' INNER JOIN industry AS ind ON (ind.industrypk = pos.industryfk AND ind.industrypk = '.$nIndustryPk.')';
    else
      $sQuery.= ' LEFT JOIN industry AS ind ON (ind.industrypk = pos.industryfk)';

    if(!empty($nCompanyPk))
      $sQuery.= ' INNER JOIN company AS cp ON (cp.companypk = pos.companyfk AND cp.companypk = '.$nCompanyPk.') ';
    else
      $sQuery.= ' LEFT JOIN company AS cp ON (cp.companypk = pos.companyfk) ';

    if($sExtaSql)
      $sQuery.= $sExtaSql;
    else
    {
      $asFilter = $this->_getSqlJobSearch();
      if(!empty($asFilter['where']))
        $sQuery.= ' WHERE (expiration_date IS NULL OR expiration_date = "" OR expiration_date > "'.$sToday.'") AND '.$asFilter['where'].' AND pos.parentfk != 0 AND pos.visibility <> 0 AND pos.lang = "'.$sLang.'"';
      else
        $sQuery.= ' WHERE (expiration_date IS NULL OR expiration_date = "" OR expiration_date > "'.$sToday.'") AND pos.parentfk != 0 AND pos.visibility <> 0 AND pos.lang = "'.$sLang.'"';
    }

    $oDbResult = $oDb->ExecuteQuery($sQuery);
    $bRead = $oDbResult->ReadFirst();
    $nNbResult = $oDbResult->getFieldValue('nCount', CONST_PHP_VARTYPE_INT);

    if($nNbResult == 0)
       return array('nNbResult' => 0, 'oData' => null);

    $sQuery = ' SELECT pos.*, ind.*,  IF(pos.company_label IS NOT NULL && pos.company_label <> "", pos.company_label, cp.company_name) as company_name FROM position AS pos ';

    if(!empty($nIndustryPk))
      $sQuery.= ' INNER JOIN industry AS ind ON (ind.industrypk = pos.industryfk AND ind.industrypk = '.$nIndustryPk.' )';
    else
      $sQuery.= ' LEFT JOIN industry AS ind ON (ind.industrypk = pos.industryfk)';

    if(!empty($nCompanyPk))
      $sQuery.= ' INNER JOIN company AS cp ON (cp.companypk = pos.companyfk AND cp.companypk = '.$nCompanyPk.') ';
    else
      $sQuery.= ' LEFT JOIN company AS cp ON (cp.companypk = pos.companyfk) ';

    if($sExtaSql)
      $sQuery.= $sExtaSql;
    else
    {
      if(!empty($asFilter['where']))
        $sQuery.= ' WHERE (expiration_date IS NULL OR expiration_date = "" OR expiration_date > "'.$sToday.'") AND '.$asFilter['where'].' AND pos.parentfk != 0 AND pos.visibility <> 0 AND pos.lang = "'.$sLang.'"';
      else
        $sQuery.= ' WHERE (expiration_date IS NULL OR expiration_date = "" OR expiration_date > "'.$sToday.'") AND pos.parentfk != 0 AND pos.visibility <> 0 AND pos.lang = "'.$sLang.'"';
    }

    $sOrder = getValue('sortfield');
    switch($sOrder)
    {
      case 'date_asc': $sQuery.= ' ORDER BY pos.positionpk, pos.visibility DESC '; break;
      case 'date_desc': $sQuery.= ' ORDER BY pos.positionpk DESC, pos.visibility DESC '; break;
      case 'salary_asc': $sQuery.= ' ORDER BY pos.salary_low, pos.salary_high, pos.visibility DESC '; break;
      case 'salary_desc': $sQuery.= ' ORDER BY pos.salary_high DESC, pos.salary_low DESC, pos.visibility DESC '; break;
      default:
        $sQuery.= ' ORDER BY pos.positionpk DESC ,pos.visibility ASC ';
    }

    $oPager = CDependency::getComponentByName('pager');
    $oPager->initPager();
    $sQuery.= ' LIMIT '.$oPager->getSqlOffset().','.$oPager->getLimit();

    $oDbResult = $oDb->ExecuteQuery($sQuery);
    $bRead= $oDbResult->readFirst();

    if(!$bRead)
    {
      assert('false; // no result but count query was ok ');
      return array('nNbResult' => 0, 'oData' => null);
    }

    return array('nNbResult' => $nNbResult, 'oData' => $oDbResult);
  }

  /**
   * Function to get the job results in list
   * @param array object $pavResult
   * @param string $psSearchId
   * @return string
   */

  private function _getJobResultList($pavResult, $psSearchId = '', $pbSearchFormOpen = false)
  {
    if(!assert('!empty($pavResult)'))
      return '';

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPager = CDependency::getComponentByName('pager');
    $oPage->addRequiredJsFile($this->getResourcePath().'js/talentatlas.js');

    $nNbResult = $pavResult['nNbResult'];
    $oDbResult = $pavResult['oData'];

    if(!$oDbResult)
      $bRead = false;
    else
      $bRead = $oDbResult->readFirst();

    $sHTML = '';

    if($nNbResult > 0)
    {
      $sUrl = $oPage->getAjaxUrl('addressbook', CONST_ACTION_LIST, CONST_TA_TYPE_JOB,0,array('searchId' => $psSearchId));
      $asPagerUrlOption = array('ajaxTarget' => 'mainJobContainer');

      if($pbSearchFormOpen)
        $sStyle = '';
      else
        $sStyle = 'display: none;';

      $sHTML.= $oHTML->getBlocStart('', array('class' =>'jobDummySearchForm', 'style' => $sStyle));
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchTitle subTitleOrange'));
      $sSearchMessage = $this->_getSearchMessage($nNbResult);

      if(!empty($sSearchMessage))
         $sHTML.= $sSearchMessage;

      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' =>'sortJobContainer'));
       $sHTML.= $oHTML->getBlocStart('', array('style' =>'float: right;'));

        $sJavascript = "if( jQuery(this).val() != '') ";
        $sJavascript.= "{ ";
        $sJavascript.= "  jQuery('form[name=advSearchForm]').find('input[name=sortfield]').val(jQuery(this).val()); ";
        $sJavascript.= "  jQuery('form[name=advSearchForm]').find('input[type=submit]').click(); ";
        $sJavascript.= "}";

        $sSortOrder = getValue('sortfield');
        $sHTML.= '<select name="job_sort" onchange="'.$sJavascript.'">';
          $sHTML.= '<option value="" class="sortJobDefault"> - '.$this->casText['TALENT_SORT_LANG'].' - </option>';
           $sHTML.= '<option value="date_asc" '.(($sSortOrder == 'date_asc')? ' selected="selected" ' : '').'>'.$this->casText['TALENT_DATE_ASC'].'</option>';
            $sHTML.= '<option value="date_desc" '.(($sSortOrder == 'date_desc')? ' selected="selected" ' : '').'>'.$this->casText['TALENT_DATE_DESC'].'</option>';
            $sHTML.= '<option value="salary_asc" '.(($sSortOrder == 'salary_asc')? ' selected="selected" ' : '').'>'.$this->casText['TALENT_SAL_ASC'].'</option>';
            $sHTML.= '<option value="salary_desc" '.(($sSortOrder == 'salary_desc')? ' selected="selected" ' : '').'>'.$this->casText['TALENT_SAL_DESC'].'</option>';
           $sHTML.= '</select>';
        $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oPager->getCompactDisplay($nNbResult, $sUrl, $asPagerUrlOption);
     }

     if($nNbResult == 0 || !$bRead)
       $sHTML.= $oHTML->getBlocMessage($this->casText['TALENT_NO_JOBS_MATCH']);
    else
    {
      while($bRead)
      {
        $asJobData = $oDbResult->getData();
        $sHTML.= $this->_getJobRow($asJobData);
        $bRead = $oDbResult->ReadNext();
      }
    }
    if($nNbResult > 0)
      $sHTML.= $oPager->getDisplay($nNbResult, $sUrl, $asPagerUrlOption);

    return $sHTML;
  }

  /**
   * Function to get all the job rows
   * @param array $pasJobData
   * @return string
   */

  private function _getJobRow($pasJobData,$psCompact=false)
  {
    if(!assert('!empty($pasJobData) && is_array($pasJobData)'))
      return '';

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = $oHTML->getBlocStart('', array('class' => 'jobContainer'));

      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobClassFirst'));
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobInnerContainer'));

      $sURL =  $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW, CONST_TA_TYPE_JOB,(int)$pasJobData['positionpk']);
      $sHTML.= $oHTML->getLink($oHTML->getText($pasJobData['position_title'],array('class'=>'subTitle')),$sURL);
      $sHTML.= $oHTML->getBlocEnd();

      //Company Logo Display Here
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'companyLogo'));
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('',array('class'=>'view_content'));
      $sHTML.= $oHTML->getListStart('',array('class'=>'list_items'));

      $sHTML.= $oHTML->getListItemStart();
      $sHTML.= $oHTML->getText($this->casText['TALENT_COMPANY'].' : '.$pasJobData['company_name']);
      $sHTML.= $oHTML->getListItemEnd();

      if(!$psCompact)
      {
       $sHTML.= $oHTML->getListItemStart();
       $sHTML.= $oHTML->getText($this->casText['TALENT_DATE'].' : '.$pasJobData['posted_date']);
       $sHTML.= $oHTML->getListItemEnd();
      }

      $sHTML.= $oHTML->getListItemStart();
      $sHTML.= $oHTML->getText($pasJobData['location']);
      $sHTML.= $oHTML->getListItemEnd();

      if(trim($pasJobData['salary'])!='0 - 0')
      {
       $sHTML.= $oHTML->getListItemStart();
       $sHTML.= $oHTML->getText($pasJobData['salary']);
       $sHTML.= $oHTML->getListItemEnd();
      }
      $sHTML.= $oHTML->getBlocEnd();

     //$sHTML.= $oHTML->getBlocStart('companyLogo');
     //Company Logo Display Here
     //$sHTML.= $oHTML->getBlocEnd();

     if($psCompact)
      $nDisplayString = 200;
     else
       $nDisplayString = 500;

     $sHTML.= $oHTML->getBlocStart('',array('class'=>'position_desc'));
     $sHTML.= $oHTML->getText($pasJobData['position_desc'],array(),$nDisplayString);
     $sHTML.= $oHTML->getBlocEnd();

     $sHTML.= $oHTML->getBlocStart('bottomBorder');
     $sHTML.= $oHTML->getSpace(2);
     $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Function to get the different search parameters to query
   * @return array
   */

  private function _getSqlJobSearch()
  {
    $oDb = CDependency::getComponentByName('database');

    $sKeyWord = strtolower(getValue('keyword'));
    $sOccupation = strtolower(getValue('occupation')); //search in position title.
    $sCompany = strtolower(getValue('company'));
    $sIndustry = getValue('industry_tree');
    $sLocation = strtolower(getValue('location'));
    $sCareer = strtolower(getValue('career'));

    $nEnglish = (int)getValue('english');
    $nJapanese = (int)getValue('japanese');
    $nJobType = (int)getValue('job_type', 0);
    $nSalaryType = (int)getValue('salary_type', 0);

    $asSalary = array();
    if($nJobType == 1)
    {
      if($nSalaryType == 1)
      {
        $sSalary = getValue('salary_year');
        $asSalary = explode('|', $sSalary);
        $nSalaryHigh = floor(((int)@$asSalary[1] * 1000000)/12);
        $nSalaryLow = floor(((int)@$asSalary[0] * 1000000)/12);
      }
      else
      {
        $sSalary = getValue('salary_month');
        $asSalary = explode('|', $sSalary);
        $nSalaryHigh = (int)@$asSalary[1] * 1000;
        $nSalaryLow = (int)@$asSalary[0] * 1000;
      }
    }
    else
    {
      $sSalary = getValue('salary_hour');
      $asSalary = explode('|', $sSalary);
      $nSalaryHigh = (int)@$asSalary[1];
      $nSalaryLow  = (int)@$asSalary[0];
    }

    $asResult = array();
    $asWhereSql = array();

    if(!empty($sKeyWord))
    {
      $sKeyWord = $oDb->dbEscapeString('%'.$sKeyWord.'%');
      $asWhereSql[] = ' (lower(cp.company_name) LIKE '.$sKeyWord.' OR lower(pos.position_desc) LIKE '.$sKeyWord.' OR lower(pos.requirements) LIKE '.$sKeyWord.') ';
    }

    if(!empty($sOccupation))
    {
      $sOccupation = $oDb->dbEscapeString('%'.$sOccupation.'%');
      $asWhereSql[] = ' lower(pos.position_title) LIKE '.$sOccupation.' ';
     }

    if(!empty($sCompany))
    {
      $sCompany = $oDb->dbEscapeString('%'.$sCompany.'%');
      $asWhereSql[] = ' lower(cp.company_name) LIKE '.$sCompany.' ';
     }

    if(!empty($sIndustry))
    {
      $asIndustry = explode(',', $sIndustry);
      foreach($asIndustry as $nKey => $sValue)
        $asIndustry[$nKey] = (int)$sValue;

      $asWhereSql[] = ' ind.industrypk in ('.implode(',', $asIndustry).')';
    }

    if(!empty($sLocation))
    {
      $sLocation = $oDb->dbEscapeString('%'.$sLocation.'%');
      $asWhereSql[] = ' lower(pos.location) LIKE '.$sLocation.'';
     }

    if(!empty($sCareer))
    {
      $sCareer = $oDb->dbEscapeString('%'.$sCareer.'%');
      $asWhereSql[] = ' lower(pos.career_level) LIKE '.$sCareer.' ';
     }

    if(!empty($nEnglish))
      $asWhereSql[] = ' pos.english  = "'.$nEnglish.'"';

    if(!empty($nJapanese))
      $asWhereSql[] = ' pos.japanese = "'.$nJapanese.'"';

    if(!empty($nSalaryLow) || !empty($nSalaryHigh))
      $asWhereSql[] = ' ((pos.salary_low = 0 OR salary_low >  "'.$nSalaryLow.'") AND  (pos.salary_high = 0 OR  salary_high < "'.$nSalaryHigh.'"))';

    $asResult['where'] = implode(' AND ', $asWhereSql);
    return $asResult;
  }

  /**
   * Function to display the home page
   * @return string
   */

  private function _getHomePage()
  {
   $oHTML = CDependency::getComponentByName('display');

   $sHTML = $oHTML->getBlocStart('',array('class'=>'homepageContainer'));
     $sHTML.= $this->_getHomePageLeftList();
     $sHTML.= $this->_getHomePageCentre();
    $sHTML.= $oHTML->getBlocEnd();

   return $sHTML;
  }

  /**
   * Function to display home page image and Search Form
   * @return string
   */

  private function _getHomePageCentre()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');
    $sLang = $oPage->getLanguage();

    $sJavascript = "$(document).ready(function() {
        $('#slider1').anythingSlider({
        autoPlay: true,
				pauseOnHover: true,
				stopAtEnd : false,
				delay: 3000
				});
      });";
    $oPage->addCustomJs($sJavascript);
    $oPage->addRequiredJsFile($this->getResourcePath().'js/jquery.anythingslider.js');
    $oPage->addCssFile(array($this->getResourcePath().'css/anythingslider.css'));

    $sHTML = $oHTML->getBlocStart('',array('class'=>'jobCentreSection'));

     $sHTML.= $oHTML->getBlocStart('',array('class'=>'homepageMainPic marginTop5'));

     $sHTML.= $oHTML->getListStart('slider1');
      $sHTML.= $oHTML->getListItemStart();
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/image2.jpg');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart();
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/image3.jpg');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart();
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/image4.jpg');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart();
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/image5.jpg');
      $sHTML.= $oHTML->getListItemEnd();

     $sHTML.= $oHTML->getListEnd();
     $sHTML.= $oHTML->getBlocEnd();

      //Form headline: title and link to fold/unfold the form
      $sHTML.= $oHTML->getBlocStart('jobSearchForm');

        $sHTML.= $oHTML->getBlocStart('jobSearchTitleLeft');
        $sHTML.= $oHTML->getText($this->casText['TALENT_START_JOB_SEARCH'],array('class'=>'boldTitle'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('advanced_search');
        $sHTML.= $oHTML->getText($this->casText['TALENT_START_ADVANCED'],array('style'=>'color:#D4662A;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('basic_search');
        $sHTML.= $oHTML->getText($this->casText['TALENT_START_BASIC'],array('style'=>'color:#D4662A;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('basic_search',array('class'=>'basicInnerDiv'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_START_BASIC'],array('style'=>'color:#D4662A;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
        $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocEnd();

      //folded form
      $sHTML.= $oHTML->getBlocStart('basic_search_form',array('class'=>'searchFormContainer'));

        $sURL = $oPage->getUrl($this->_getUid(),CONST_ACTION_LIST,CONST_TA_TYPE_JOB, 0);
        $oForm = $oHTML->initForm('basicSearchForm');
        $oForm->setFormParams('',false, array('action' => $sURL));
        $oForm->setFormDisplayParams(array('columns' => 2, 'noButton' => '1'));
        $oPage->addCssFile(array($this->getResourcePath().'css/talentatlas.css'));

        $oForm->addField('input', 'keyword', array('label' => $this->casText['TALENT_KEYWORDS'], 'value' => ''));
        $oForm->addField('misc', '', array('type' => 'text', 'text'=> '<div class ="searchButton"> <input type="button" name="btnSearch" id="btnSearch" Value="'.$this->casText['TALENT_SEARCH'].'"> </div>'));
        $oForm->addField('hidden', 'do_search', array('value' => 1));
        $sHTML.= $oForm->getDisplay();

        $sJavascript = " $(document).ready(function(){ $('#btnSearch').click(function(){ $(this).closest('form').submit(); }); }); ";
        $oPage->addCustomJs($sJavascript);

        $sJavascript = " $(document).ready(function(){ $('#advanced_search').click(function(){ showHide('basic_search','advanced_search','advanced_search_form','basic_search_form'); }); }); ";
        $oPage->addCustomJs($sJavascript);

        $sJavascript = " $(document).ready(function(){ $('#basic_search').click(function(){ showHide('advanced_search','basic_search','basic_search_form','advanced_search_form'); }); }); ";
        $oPage->addCustomJs($sJavascript);

      $sHTML.= $oHTML->getBlocEnd();

      //expanded form
      $sHTML.= $oHTML->getBlocStart('advanced_search_form',array('class'=>'searchFormContainer','style'=>'display:none;'));
      $sHTML.= $this->_getJobSearchForm('',false);
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('',array('style'=>'border-top:2px solid #2789BC;'));
      $sHTML.= $oHTML->getBlocEnd();

      //Section to display the icons

      $sHTML.= $oHTML->getBlocStart('recentJobs');
      $sHTML.= $oHTML->getText($this->casText['TALENT_RECENT_JOBS'],array('class'=>'boldTitle test'));
      $sHTML.= $oHTML->getBlocEnd();

      $sQuery = 'SELECT pos.*,cp.*  FROM position as pos LEFT JOIN company as cp on pos.companyfk = cp.companypk where pos.parentfk != 0 AND pos.visibility <> 0 AND pos.lang = "'.$sLang.'" ORDER BY positionpk DESC LIMIT 10 ';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      if($bRead)
      {
        while($bRead)
        {
          $asData =  $oDbResult->getData();
          $sHTML.= $this->_getJobRow($asData,true);
          $bRead = $oDbResult->readNext();
        }
      }
      else
      {
        //$sHTML.= $oHTML->getBlocMessage($this->casText['TALENT_NO_JOBS_MATCH']);
      }

    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Function to display the left section in the home page containing featured industries and featured companies
   * @return string HTML Contents
   */

  private function _getHomePageLeftList()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');
    $oForm = CDependency::getComponentByName('form');

    $sLang = $oPage->getLanguage();
    $sHTML = $oHTML->getBlocStart('',array('class'=>'jobLeftSection'));

    $sURL =  $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_TA_TYPE_JOB,0);
    $oForm = $oHTML->initForm('hiddenForm');
    $oForm->setFormParams('', false, array('action' => $sURL,'style'=>'display:none;'));

    $oForm->addField('input', 'companypk', array('value' => ''));
    $oForm->addField('input', 'companyname', array('value' => ''));

    $oForm->addField('input', 'industrypk', array('value' => ''));
    $oForm->addField('input', 'industryname', array('value' => ''));
    $oForm->addField('input', 'do_search', array('value' => '1'));

    $sHTML.= $oForm->getDisplay();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'featured_header'));
    $sHTML.= $oHTML->getText($this->casText['TALENT_FEATURED_INDUSTRIES'],array('class'=>'boldTitle'));
    $sHTML.= $oHTML->getBlocEnd();

    $sQuery = 'SELECT ind.name AS industry_name,count(pos.positionpk) AS nCount,industrypk  FROM industry AS ind INNER JOIN position AS pos WHERE ind.industrypk = pos.industryfk AND ind.status != 0 and pos.visibility != 0 and pos.lang = "'.$sLang.'" GROUP BY ind.industrypk ORDER BY industry_name  ASC LIMIT 15';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if($bRead)
    {
     while($bRead)
     {
       if(isset($this->casText[$oDbResult->getFieldValue('industry_name')]))
         $sIndustryName = $this->casText[$oDbResult->getFieldValue('industry_name')];
       else
         $sIndustryName = $oDbResult->getFieldValue('industry_name');

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'industryFeatured'));
         $sHTML.= $oHTML->getBlocStart('', array('class' => 'leftFeaturedName'));
          $sHTML.= $oHTML->getLink($sIndustryName,'javascript:;',array('onclick'=>'submitForm(this);','class'=>'middleTitle','industrypk'=>$oDbResult->getFieldValue('industrypk'),'industryname'=>$oDbResult->getFieldValue('industry_name')));
           $sHTML.= $oHTML->getBlocEnd();
           $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftFeaturedNumber'));
          $sHTML.= $oHTML->getText(' '.$oDbResult->getFieldValue('nCount'),array('style'=>'color:#D4662A;'));
         $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocEnd();

       $bRead = $oDbResult->readNext();
       }
       $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
       $sHTML.= $oHTML->getBlocEnd();
     }

    //Featured Companies

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'featured_header '));
    $sHTML.= $oHTML->getText($this->casText['TALENT_FEATURED_COMPANIES'] ,array('class'=>'boldTitle'));
    $sHTML.= $oHTML->getBlocEnd();

    $sQuery = 'SELECT cp.company_name AS company_name,count(pos.positionpk) AS nCount,companypk  FROM company AS cp INNER JOIN position AS pos WHERE cp.companypk = pos.companyfk AND cp.status != 0 and pos.visibility != 0 AND pos.lang = "'.$sLang.'" AND company_name != "" GROUP BY company_name ORDER BY nCount DESC LIMIT 10';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if($bRead)
    {
     while($bRead)
     {
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'industryFeatured'));
         $sHTML.= $oHTML->getBlocStart('', array('class' => 'leftFeaturedName'));
          $sHTML.= $oHTML->getLink($this->_removeJapanese($oDbResult->getFieldValue('company_name')),'javascript:;',array('onclick'=>'submitForm(this);','class'=>'middleTitle','companypk'=>$oDbResult->getFieldValue('companypk'),'companyname'=>$oDbResult->getFieldValue('company_name')));
           $sHTML.= $oHTML->getBlocEnd();
            $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftFeaturedNumber'));
           $sHTML.= $oHTML->getText(' '.$oDbResult->getFieldValue('nCount'),array('style'=>'color:#D4662A;'));
         $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocEnd();

        $bRead = $oDbResult->readNext();
       }

       $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
       $sHTML.= $oHTML->getBlocEnd();
     }

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Function to display the job search form
   * @param boolean $pbIsAjaxForm
   * @return string
   */

  private function _getJobSearchForm($pbIsAjaxForm = false)
  {
    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');
    $oForm = $oHTML->initForm('advSearchForm');

    if($pbIsAjaxForm)
    {
      $sURL = $oPage->getAjaxUrl($this->_getUid(),CONST_ACTION_LIST,CONST_TA_TYPE_JOB);
      $oForm->setFormParams('', true, array('action' => $sURL, 'submitLabel' => $this->casText['TALENT_SEARCH'],'ajaxTarget' => 'mainJobContainer', 'ajaxCallback' => "searchFormToggle(false);", 'onBeforeSubmit' => "jQuery(body).animate({scrollTop: '0px'}, 600, 'linear'); "));
    }
    else
    {
      $sURL = $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_TA_TYPE_JOB, 0);
      $oForm->setFormParams('', false, array('action' => $sURL, 'submitLabel' => $this->casText['TALENT_SEARCH']));
    }

    $oForm->setFormDisplayParams(array('columns' => 2, 'noCancelButton' => '1'));
    $oPage->addCssFile(array($this->getResourcePath().'css/talentatlas.css'));

    $oForm->addField('input', 'keyword', array('label' => $this->casText['TALENT_KEYWORDS'], 'value' => getValue('keyword')));
    $oForm->addField('input', 'occupation', array('label' => $this->casText['TALENT_OCCUPATION'], 'value' => getValue('occupation')));

    $oForm->addField('input', 'location', array('label' => $this->casText['TALENT_LOCATION'], 'value' => getValue('location')));
    $oForm->addField('input', 'career', array('label' => $this->casText['TALENT_CAREER'], 'value' => getValue('career')));

    $oForm->addField('select', 'english', array('label' => $this->casText['TALENT_ENGLISH_ABILITY']));

    $asLanguage= $this->getLanguages();
    $nLanguage = getValue('english');

    foreach($asLanguage as $nValue=>$vType)
    {
       if($nLanguage==$nValue)
         $oForm->addOption('english', array('value'=>$nValue, 'label' => $vType,'selected'=>'selected'));
       else
         $oForm->addOption('english', array('value'=>$nValue, 'label' => $vType));
    }

    $oForm->addField('select', 'japanese', array('label' => $this->casText['TALENT_JAP_ABILITY']));
    $asJapLanguage= $this->getLanguages();
    $nLanguage = getValue('japanese');

    foreach($asJapLanguage as $nValue=>$vType)
    {
       if($nLanguage==$nValue)
       $oForm->addOption('japanese', array('value'=>$nValue, 'label' => $vType,'selected'=>'selected'));
       else
       $oForm->addOption('japanese', array('value'=>$nValue, 'label' => $vType));
    }

    $sCompany = getValue('companyname');
    if(!isset($sCompany))
      $sCompany = getValue('company');

    $oForm->addField('input', 'company', array('label' => $this->casText['TALENT_COMPANY'],'value' => $sCompany));
    $asIndustries = $this->getIndustries(0, true, true);
    $nIndustry = getValue('industry_tree');
    $oForm->addField('tree', 'industry_tree', array('label' => $this->casText['TALENT_INDUSTRY'], 'elementLabel' => 'industries'));

    $asIndustrySelected = explode(',',$nIndustry);

    $nIndustryPk = getValue('industrypk');
    if(isset($nIndustryPk) && !empty($nIndustryPk))
      array_push($asIndustrySelected, $nIndustryPk);

    foreach($asIndustries as $sKey =>$vName)
    {
      if(in_array($vName['industrypk'],$asIndustrySelected))
        $oForm->addOption('industry_tree', array('title' => ''.$this->casText[$vName['name']].'','id' => $vName['industrypk'], 'parent' => $vName['parentfk'],'checked'=>'checked'));
      else
        $oForm->addOption('industry_tree', array('title' => ''.$this->casText[$vName['name']].'','id' => $vName['industrypk'], 'parent' => $vName['parentfk']));
    }

    $oForm->addField('select', 'job_type', array('label' => $this->casText['TALENT_JOB_TYPE'],'onchange'=>"showHideSalary(this.value);"));
    $oForm->addOption('job_type', array('value'=>1, 'label' => $this->casText['TALENT_FULLTIME']));
    $oForm->addOption('job_type', array('value'=>0, 'label' => $this->casText['TALENT_PARTTIME']));

    $oForm->addField('misc', '', array('type' => 'text','text'=> ' '));

    $oForm->addField('slider','salary_month', array('label' => $this->casText['TALENT_SALARY_RANGE'], 'keepNextInline' => '', 'min' => 50, 'max' => 1000, 'step' => 50, 'multiplier' => 1000, 'value_min' => 200, 'value_max' => 500, 'prefix' => '', 'suffix' => ' K¥', 'value_label' => '', 'value_label_before' => 1));
    $oForm->addField('slider','salary_year' , array('label' => $this->casText['TALENT_SALARY_RANGE'], 'keepNextInline' => '1', 'min' => 1, 'max' => 25, 'step' => 1, 'multiplier' => 1000000, 'value_min' => 2, 'value_max' => 10, 'prefix' => '', 'suffix' => ' M¥', 'value_label' => '', 'value_label_before' => 1));
    $oForm->addField('slider','salary_hour' , array('label' => $this->casText['TALENT_SALARY_RANGE'], 'keepNextInline' => '1', 'min' => 500, 'max' => 2000, 'step' => 100, 'value_min' => 800, 'value_max' => 1400, 'prefix' => '', 'suffix' => ' ¥', 'value_label' => '', 'value_label_before' => 1));

    $sRadio = '<div id="salary" class="formFieldContainer"> ';
    $sRadio.= '  <div class="formField"><label>'.$this->casText['TALENT_PAYMENT'].':&nbsp;&nbsp;&nbsp;</label>';
    $sRadio.= '  <input type="radio" name="salary_type" id="salary_type_Month" value="0" checked="checked" onclick="displaySalary(0);"/>';
    $sRadio.= '  <label for="salary_type_Month">&nbsp;'.$this->casText['TALENT_MONTHLY'].'</label><label>&nbsp;&nbsp;</label>';
    $sRadio.= '  <input type="radio" name="salary_type" id ="salary_type_year" value="1"  onclick="displaySalary(1);"/> ';
    $sRadio.= '  <label for="salary_type_year">&nbsp;'.$this->casText['TALENT_YEARLY'].'</label></div>';
    $sRadio.= '</div> ';
    $oForm->addField('misc', '', array('type' => 'text', 'text'=> $sRadio, 'keepNextInline' => '1'));

    $oForm->addField('hidden', 'do_search', array('value' => 1));
    $oForm->addField('hidden', 'sortfield');

    $sJavascript = " $(document).ready(function(){ showHideSalary(1); displaySalary(0); }); ";
    $oPage->addCustomJs($sJavascript);

    $sHTML = $oForm->getDisplay();
    return $sHTML;
  }

  /**
   * Function to display the job lists
   * @param string $psQueryFilter
   * @return string
   */

  public function getJobList($psQueryFilter = '')
  {
    $oHTML = CDependency::getComponentByName('display');

    if(getValue('do_search', 0))
    {
      $sSearchId = manageSearchHistory($this->csUid, CONST_TA_TYPE_JOB, true);
      if(isset($_POST['do_search']))
        unset($_POST['do_search']);
    }
    else
    {
      if(getValue('searchId'))
        $sSearchId = manageSearchHistory($this->csUid, CONST_TA_TYPE_JOB);
      else
        $sSearchId = reloadLastSearch($this->csUid, CONST_TA_TYPE_JOB);
     }

    if(empty($_POST))
      $bSearchFormOpen = true;
    else
      $bSearchFormOpen = false;

    $sHTML = $oHTML->getBlocStart('',array('class'=>'homepageContainer'));
    //left section
    $sHTML.= $this->_getJobListLeftSection($sSearchId, $bSearchFormOpen);
    //Middle Section
    $avResult = $this->_getJobSearchResult($sSearchId);

     $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobCentreSection'));
      $sHTML.= $oHTML->getBlocStart('mainJobContainer');
       $sHTML.= $this->_getJobResultList($avResult, $sSearchId, $bSearchFormOpen);
      $sHTML.= $oHTML->getBlocEnd();
     $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();

   return $sHTML;
  }

  /**
   * Function to display the job list floating left section
   * @param string $psSearchId
   * @param boolean $pbSearchFormOpen
   * @return string
   */

  private function _getJobListLeftSection($psSearchId, $pbSearchFormOpen)
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sJavascript = "jQuery(document).ready(function(){ ";
    $sJavascript.= "  var nOriginalPosition = 0; ";

    $sJavascript.= "  jQuery(document).scroll(function(){ ";
    $sJavascript.= "  var bFloating = jQuery('.jobLeftSectionInner').hasClass('menuFloating'); ";
    $sJavascript.= "  var oPosition = jQuery('.jobLeftSectionInner').offset(); ";
    $sJavascript.= "  var nPosTop = oPosition.top; ";
    $sJavascript.= "  var nScroll = jQuery('body').scrollTop(); ";

    $sJavascript.= "  if(!bFloating) ";
    $sJavascript.= "  { ";
    $sJavascript.= "    if(nScroll > (nPosTop + 15) ) ";
    $sJavascript.= "    {";
    $sJavascript.= "      jQuery('.jobLeftSectionInner').attr('style', 'position: fixed; top:10px;'); ";
    $sJavascript.= "      jQuery('.jobLeftSectionInner').addClass('menuFloating'); ";
    $sJavascript.= "      nOriginalPosition =  nPosTop";
    $sJavascript.= "    } ";
    $sJavascript.= "   } ";
    $sJavascript.= "   else ";
    $sJavascript.= "   { ";
    $sJavascript.= "    if(nScroll < nOriginalPosition ) ";
    $sJavascript.= "    { ";
    $sJavascript.= "      jQuery('.jobLeftSectionInner').removeClass('menuFloating'); ";
    $sJavascript.= "      jQuery('.jobLeftSectionInner').attr('style', 'position: relative;'); ";
    $sJavascript.= "     } ";
    $sJavascript.= "   } ";
    $sJavascript.= "  }); ";
    $sJavascript.= "}); ";

    $oPage->addCustomJs($sJavascript);

    $sHTML = $oHTML->getBlocStart('',array('class'=>'jobLeftSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobLeftSectionInner'));

    $sHTML.= $this->_getJobSearchSection($psSearchId, $pbSearchFormOpen);
    $sHTML.= $this->_getJobSearchFilterSection($pbSearchFormOpen);

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Function to display the left section of job detail page , displaying the sharing options of job
   * @return string HTML
   */

  private function _getLeftSectionJobList($pnPk)
  {
    if(!assert('is_integer($pnPk) && !empty($pnPk)'))
     return '';

    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');

    $sQuery = 'SELECT * FROM position WHERE positionpk = '.$pnPk.'';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if($bRead)
      $asData = $oDbResult->getData();

    $sTitle = $asData['position_title'];
    $sPageURL = curPageURL();

    $sHTML = $oHTML->getBlocStart('',array('class'=>'jobLeftSection'));
    $sHTML = $oHTML->getBlocStart('',array('class'=>'jobLeftSectionInner'));

    $sHTML.= $oHTML->getBlocStart('shareJob');
    $sHTML.= $oHTML->getText($this->casText['TALENT_SHARE_JOB'],array('class'=>'subTitle'));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'marginTop10 '));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'blueTitle'));

    $sHTML.= "<script>function fbs_click() {u=location.href;t=document.title;window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');return false;}</script>";

    $sFbPageUrl = 'http://www.facebook.com/share.php?u='.$sPageURL.'&t='.$sTitle.'';
    $sLinkedInUrl = 'http://www.linkedin.com/shareArticle?mini=true&url='.$sPageURL.'&title='.$sTitle.'';

    $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/facebook.png','Post on facebook');
    $sHTML.= $oHTML->getSpace(2);
    $sHTML.= $oHTML->getLink($this->casText['TALENT_POST_FB'],$sFbPageUrl,array('rel'=>"nofollow" ,'onclick'=> "return fbs_click()",'target'=>'_blank'));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'blueTitle'));
    $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/linkedin.png','Post on linkedin');
    $sHTML.= $oHTML->getSpace(2);
    $sHTML.= $oHTML->getLink($this->casText['TALENT_POST_LINKEDIN'],$sLinkedInUrl,array('target'=>'_blank'));
    $sHTML.= $oHTML->getBlocEnd();

    $sURL = $oPage->getAjaxUrl($this->_getUid(),CONST_ACTION_EMAIL,CONST_TA_TYPE_JOB,0,array('positionpk'=>$pnPk));
    $sAjax = $oHTML->getAjaxPopupJS($sURL, 'body','','350','500',1);

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'blueTitle'));
    $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/email.png','Email to friend');
    $sHTML.= $oHTML->getSpace(2);
    $sHTML.= $oHTML->getLink($this->casText['TALENT_EMAIL_FRIEND'],'javascript:;', array('onclick'=>$sAjax));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'blueTitle marginTop5'));
    $sURL = "'".$oPage->getURL('talentatlas',CONST_ACTION_LIST,CONST_TA_TYPE_JOB,0)."'";
    $sHTML.= $oHTML->getLink($this->casText['TALENT_RETURN_RESULT'],'',array('onclick'=>'document.location.href='.$sURL));
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Function to display searched the job filter
   * @param boolean $pbSearchFormOpen
   * @return string
   */

  private function _getJobSearchFilterSection($pbSearchFormOpen =false)
  {
    $oHTML = CDependency::getComponentByName('display');

    $asFormField['keyword'] = array('label' => $this->casText['TALENT_KEYWORDS'], 'resetValue' => '', 'type' => 'input');
    $asFormField['location'] = array('label' => $this->casText['TALENT_LOCATION'], 'resetValue' => '', 'type' => 'input');
    $asFormField['occupation'] = array('label' => $this->casText['TALENT_OCCUPATION'], 'resetValue' => '', 'type' => 'input');
    $asFormField['career'] = array('label' => $this->casText['TALENT_CAREER'], 'resetValue' => '', 'type' => 'input');
    $asFormField['company'] = array('label' => $this->casText['TALENT_COMPANY'], 'resetValue' => '', 'type' => 'input');

    $sFilter = '';

    $sFilter.=  $this->_showFilterSalary();
    $sFilter.=  $this->_showFilterEngLanguage();
    $sFilter.=  $this->_showFilterJapLanguage();
    $sFilter.=  $this->_showFilterIndustryTree();
    $sFilter.=  $this->_showFilterIndustry();
    $sFilter.=  $this->_showFilterCompany();

    foreach($_POST as $sFieldname => $sFieldValue)
    {
      if(isset($asFormField[$sFieldname]) && !empty($sFieldValue))
      {
         $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowContainer'));

          $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowTitle'));
          $sFilter.= $oHTML->getText($asFormField[$sFieldname]['label']);
          $sFilter.= $oHTML->getBlocEnd();

          $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowValue'));
          $sFilter.= $oHTML->getText($sFieldValue); //Display the text
          $sFilter.= $oHTML->getBlocEnd();

          $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowDelete'));
          $sPicture = $oHTML->getPicture($this->getResourcePath().'pictures/delete_filter.png',$this->casText['TALENT_REMOVE_FILTER']);

          $sJavascript = 'jQuery(this).closest(\'.jobLeftSection\').find(\''.$asFormField[$sFieldname]['type'].'[name='.$sFieldname.']\').val(\''.$asFormField[$sFieldname]['resetValue'].'\'); ';
          $sJavascript.= 'jQuery(this).closest(\'.filterRowContainer\').fadeOut();';
          $sJavascript.= 'clearFilter();';

          $sFilter.= $oHTML->getLink($sPicture, 'javascript:;', array('onclick'=> $sJavascript));
          $sFilter.= $oHTML->getBlocEnd();

          $sFilter.= $oHTML->getFloatHack();
        $sFilter.= $oHTML->getBlocEnd();

      }
    }

    if(empty($sFilter))
    {
      $sFilter.= $oHTML->getBlocStart();

        $sFilter.= $oHTML->getSpanStart('', array('class' => 'filterRemovalLoader hidden'));
        $sFilter.= '<img src="/component/talentatlas/resources/pictures/filter_loading.gif" />';
        $sFilter.= $oHTML->getSpanEnd();

        $sFilter.= $oHTML->getText($this->casText['TALENT_MAKE_SEARCH'],array('class'=>'smallTitle','onclick'=>'searchFormToggle();'));

      $sFilter.= $oHTML->getBlocEnd();

      $sClearSearch = '';
    }
    else
    {
      $sFilter.= $oHTML->getBlocStart();

        $sFilter.= $oHTML->getSpanStart('', array('class' => 'filterRemovalLoader hidden', 'style' => ''));
        $sFilter.= '<img src="/component/talentatlas/resources/pictures/filter_loading.gif" />';
        $sFilter.= $oHTML->getSpanEnd();

        $sFilter.= $oHTML->getText($this->casText['TALENT_EDIT_PARAM'],array('class'=>'smallTitle','onclick'=>'searchFormToggle();'));

      $sFilter.= $oHTML->getBlocEnd();

      $sClearSearch = $oHTML->getText($this->casText['TALENT_CLEAR'],array('class'=>'smallTitle','onclick'=>'resetJobSearch();'));
    }

   $sHTML = $oHTML->getBlocStart('leftsectionSearch');

   $sHTML.= $oHTML->getBlocStart('',array('class'=>'newSearchDiv'));
   $sHTML.= $oHTML->getText($this->casText['TALENT_NEW_SEARCH'],array('class'=>'subTitleOrange'));
   $sHTML.= $sClearSearch;
   $sHTML.= $oHTML->getBlocEnd();

   $sHTML.= $oHTML->getBlocStart('',array('class'=>'searchClass'));
   $sHTML.= $sFilter;
   $sHTML.= $oHTML->getBlocEnd();

   $sHTML.= $oHTML->getBlocEnd();
  return $sHTML;
  }

  /**
   * Function to display the company
   * @return string
   */

  private function _showFilterCompany()
  {
    $oHTML = CDependency::getComponentByName('display');

    $sFieldValue = getValue('companyname');
    if(!empty($sFieldValue))
    {
      $sFieldname = 'company';

      $sFilter = '';
      $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowContainer'));
      $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowTitle'));
      $sFilter.= $oHTML->getText($this->casText['TALENT_COMPANY']);
      $sFilter.= $oHTML->getBlocEnd();

      $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowValue'));
      $sFilter.= $oHTML->getText($sFieldValue);
      $sFilter.= $oHTML->getBlocEnd();

      $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowDelete'));
      $sPicture = $oHTML->getPicture($this->getResourcePath().'pictures/delete_filter.png',$this->casText['TALENT_REMOVE_FILTER']);
      $sJavascript = 'jQuery(this).closest(\'.jobLeftSection\').find(\'input[name='.$sFieldname.']\').val(\'\'); ';
      $sJavascript.= 'jQuery(this).closest(\'.filterRowContainer\').fadeOut();';
      $sJavascript.= 'clearFilter();';
      $sFilter.= $oHTML->getLink($sPicture, 'javascript:;', array('onclick'=> $sJavascript));
      $sFilter.= $oHTML->getBlocEnd();
      $sFilter.= $oHTML->getFloatHack();

      $sFilter.= $oHTML->getBlocEnd();
      return $sFilter;
     }
  }

  /**
   * Function to display the selected industry
   * @return string
   */

  private function _showFilterIndustry()
  {
     $oHTML = CDependency::getComponentByName('display');

     $nFieldValue = (int)getValue('industrypk',0);
     if(!empty($nFieldValue))
     {
        $sFieldname = 'industry_tree';

        $sFilter = '';
        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowContainer'));
        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowTitle'));
        $sFilter.= $oHTML->getText($this->casText['TALENT_INDUSTRY']);
        $sFilter.= $oHTML->getBlocEnd();

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowValue'));
        $sFilter.= $oHTML->getText($this->getIndustries($nFieldValue)); // Get the industry name
        $sFilter.= $oHTML->getBlocEnd();

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowDelete'));
        $sPicture = $oHTML->getPicture($this->getResourcePath().'pictures/delete_filter.png',$this->casText['TALENT_REMOVE_FILTER']);
        $sJavascript = 'jQuery(this).closest(\'.jobLeftSection\').find(\'input[name='.$sFieldname.']\').val(\'\'); ';
        $sJavascript.= 'jQuery(this).closest(\'.filterRowContainer\').fadeOut();';
        $sJavascript.= 'jQuery(\'#advSearchFormId\').find(\'.fieldTreeOpenLink\').html(\'Select Industries ... \');';
        $sJavascript.= 'clearFilter();';
        $sFilter.= $oHTML->getLink($sPicture, 'javascript:;', array('onclick'=> $sJavascript));
        $sFilter.= $oHTML->getBlocEnd();
        $sFilter.= $oHTML->getFloatHack();

        $sFilter.= $oHTML->getBlocEnd();
        return $sFilter;
     }
   }

  /**
   * Function to show the selected Industries
   * @return string
   */

  private function _showFilterIndustryTree()
  {
     $oHTML = CDependency::getComponentByName('display');

     $sFieldValue = getValue('industry_tree');
     if(!empty($sFieldValue))
     {
        $sFieldname = 'industry_tree';
        $asIndustry = explode(',',$sFieldValue);
        $nIndustryCount = count($asIndustry);

        $sFilter = '';
        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowContainer'));

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowTitle'));
        $sFilter.= $oHTML->getText($this->casText['TALENT_INDUSTRY']);
        $sFilter.= $oHTML->getBlocEnd();

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowValue'));
        $sFilter.= $oHTML->getText($nIndustryCount.' '.$this->casText['TALENT_INDUSTRIES']);
        $sFilter.= $oHTML->getBlocEnd();

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowDelete'));
        $sPicture = $oHTML->getPicture($this->getResourcePath().'pictures/delete_filter.png',$this->casText['TALENT_REMOVE_FILTER']);
        $sJavascript = 'jQuery(this).closest(\'.jobLeftSection\').find(\'input[name='.$sFieldname.']\').val(\'\'); ';
        $sJavascript.= 'jQuery(this).closest(\'.filterRowContainer\').fadeOut();';
        $sJavascript.= 'clearFilter();';
        $sFilter.= $oHTML->getLink($sPicture, 'javascript:;', array('onclick'=> $sJavascript));
        $sFilter.= $oHTML->getBlocEnd();
        $sFilter.= $oHTML->getFloatHack();

        $sFilter.= $oHTML->getBlocEnd();
        return $sFilter;
     }
  }

  /**
   * Function to show the selected english language level
   * @return string
   */

  private function _showFilterEngLanguage()
  {
    $oHTML = CDependency::getComponentByName('display');

    $nEngLanguageType = getValue('english',0);
    if(!empty($nEngLanguageType))
    {
        $sFieldname = 'english';

        $sFilter = '';
        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowContainer'));

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowTitle'));
        $sFilter.= $oHTML->getText($this->casText['TALENT_ENGLISH_ABILITY']);
        $sFilter.= $oHTML->getBlocEnd();

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowValue'));
        $sFilter.= $oHTML->getText(getLanguageLevel($nEngLanguageType)); // Get the language level
        $sFilter.= $oHTML->getBlocEnd();

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowDelete'));
        $sPicture = $oHTML->getPicture($this->getResourcePath().'pictures/delete_filter.png',$this->casText['TALENT_REMOVE_FILTER']);
        $sJavascript = 'jQuery(this).closest(\'.jobLeftSection\').find(\'select[name='.$sFieldname.']\').val(\'0\'); ';
        $sJavascript.= 'jQuery(this).closest(\'.filterRowContainer\').fadeOut();';
        $sJavascript.= 'clearFilter();';
        $sFilter.= $oHTML->getLink($sPicture, 'javascript:;', array('onclick'=> $sJavascript));
        $sFilter.= $oHTML->getBlocEnd();
        $sFilter.= $oHTML->getFloatHack();

        $sFilter.= $oHTML->getBlocEnd();
        return $sFilter;
    }
  }

   /**
   * Function to show the selected japanese language level
   * @return string
   */

  private function _showFilterJapLanguage()
  {
    $oHTML = CDependency::getComponentByName('display');

    $nJapLanguageType = getValue('japanese',0);
    if(!empty($nJapLanguageType))
    {
        $sFieldname = 'japanese';

        $sFilter = '';
        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowContainer'));

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowTitle'));
        $sFilter.= $oHTML->getText($this->casText['TALENT_JAP_ABILITY']);
        $sFilter.= $oHTML->getBlocEnd();

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowValue'));
        $sFilter.= $oHTML->getText(getLanguageLevel($nJapLanguageType)); // Get the language level
        $sFilter.= $oHTML->getBlocEnd();

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowDelete'));
        $sPicture = $oHTML->getPicture($this->getResourcePath().'pictures/delete_filter.png',$this->casText['TALENT_REMOVE_FILTER']);
        $sJavascript = 'jQuery(this).closest(\'.jobLeftSection\').find(\'select[name='.$sFieldname.']\').val(\'\'); ';
        $sJavascript.= 'jQuery(this).closest(\'.filterRowContainer\').fadeOut();';
        $sJavascript.= 'clearFilter();';
        $sFilter.= $oHTML->getLink($sPicture, 'javascript:;', array('onclick'=> $sJavascript));
        $sFilter.= $oHTML->getBlocEnd();
        $sFilter.= $oHTML->getFloatHack();

        $sFilter.= $oHTML->getBlocEnd();
        return $sFilter;
    }
  }

  /**
   * Function to show the selected salary range
   * @return string
   */

  private function _showFilterSalary()
  {
    $oHTML = CDependency::getComponentByName('display');

    $nJobType = (int)getValue('job_type', 0);
    $nSalType = getValue('salary_type');

    $sFilter = '';
    $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowContainer'));

    //Part time
    if($nJobType == 0)
    {
      $sFieldValue = getValue('salary_month');
      if(empty($sFieldValue))
        return '';

      $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowTitle'));
      $sFilter.= $oHTML->getText($this->casText['TALENT_SALARY'].' <br /> ('.$this->casText['TALENT_HOURLY'].')');
      $sFilter.= $oHTML->getBlocEnd();

      $sFieldname = 'salary_hour';
      $sFieldValue = getValue('salary_hour');
      $asSalary = explode('|',$sFieldValue);
      $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowValue'));
      $sFilter.= $oHTML->getText($asSalary[0].'¥ - '.$asSalary[1].'¥');
      $sFilter.= $oHTML->getBlocEnd();
    }
    elseif($nJobType == 1)
    {
      //Full time
      if($nSalType == 0)
      {
        $sFieldValue = getValue('salary_month');
        if(empty($sFieldValue))
          return '';

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowTitle'));
        $sFilter.= $oHTML->getText($this->casText['TALENT_SALARY'].'<br />('.$this->casText['TALENT_MONTHLY'].')');
        $sFilter.= $oHTML->getBlocEnd();

        $sFieldname = 'salary_month';
        $asSalary = explode('|',$sFieldValue);
        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowValue'));
        $sFilter.= $oHTML->getText($asSalary[0].'K¥ - '.$asSalary[1].'K¥');
        $sFilter.= $oHTML->getBlocEnd();
      }
      else
      {
        $sFieldValue = getValue('salary_year');
        if(empty($sFieldValue))
          return '';

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowTitle'));
        $sFilter.= $oHTML->getText($this->casText['TALENT_SALARY'].'<br />('.$this->casText['TALENT_YEARLY'].')');
        $sFilter.= $oHTML->getBlocEnd();
        $sFieldname = 'salary_year';
        $asSalary = explode('|',$sFieldValue);

        $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowValue'));
        $sFilter.= $oHTML->getText($asSalary[0].'M¥ - '.$asSalary[1].'M¥');
        $sFilter.= $oHTML->getBlocEnd();
      }
    }

    $sFilter.= $oHTML->getBlocStart('',array('class'=>'filterRowDelete'));
    $sPicture = $oHTML->getPicture($this->getResourcePath().'pictures/delete_filter.png',$this->casText['TALENT_REMOVE_FILTER']);
    $sJavascript = 'jQuery(this).closest(\'.jobLeftSection\').find(\'input[name='.$sFieldname.']\').val(\'0\'); ';
    $sJavascript.= 'jQuery(this).closest(\'.filterRowContainer\').fadeOut();';
    $sJavascript.= 'clearFilter();';
    $sFilter.= $oHTML->getLink($sPicture, 'javascript:;', array('onclick'=> $sJavascript));
    $sFilter.= $oHTML->getBlocEnd();
    $sFilter.= $oHTML->getFloatHack();

    $sFilter.= $oHTML->getBlocEnd();
    return $sFilter;
  }

  /**
   * FUnction to display the job search floating section
   * @param string $psSearchId
   * @param boolean $pbSearchFormOpen
   * @return string
   */

  private function _getJobSearchSection($psSearchId, $pbSearchFormOpen)
  {
    $oHTML = CDependency::getComponentByName('display');

    if('displayVertical' !== true)
      $sClass = ' jobSearchFloating ';
    else
      $sClass = ' jobSearchVertical ';

    if(!$pbSearchFormOpen)
      $sClass.= ' hidden ';

    $sHTML = $oHTML->getBlocStart('',array('class'=>'jobSearchContainer '.$sClass));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobSearchContainerInner'));

      $sHTML.= $oHTML->getBlocStart('',array('class'=>'newSearchDiv '));
      $sHTML.= $oHTML->getText($this->casText['TALENT_REFINE_SEARCH'],array('class'=>'subTitleOrange'));

       $sHTML.= $oHTML->getBlocStart('',array('style'=>'float: right;'));
       $sPicture = $oHTML->getPicture($this->getResourcePath().'pictures/close_search_form.png', $this->casText['TALENT_CLOSE_FORM'], '', array('style' => 'margin: 4px 4px 0 0;'));
       $sHTML.= $oHTML->getLink($sPicture, 'javascript:;', array('onclick'=> 'searchFormToggle(false);'));
       $sHTML.= $oHTML->getBlocEnd();

       $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('',array('style'=>'float: right;'));
      $sHTML.= $oHTML->getText('', array('class'=>'subTitleOrange'));
      $sHTML.= $oHTML->getBlocEnd();

      // Search Form
      $sHTML.= $oHTML->getBlocStart('advanced_search_form', array('class'=>'searchFormContainer'));
      $sHTML.= $this->_getJobSearchForm($psSearchId, true);
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('', array('class'=>'borderTop'));
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class'=>'floatHack'));
      $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Function to give Job Detail information
   * @param integer $pnPk
   * @return string HTML
   */

  public function getJobDetail($pnPk)
  {
     if(!assert('is_integer($pnPk) && !empty($pnPk)'))
       return '';

    $oHTML = CDependency::getComponentByName('display');

    $sHTML = $oHTML->getBlocStart('',array('class'=>'homepageContainer'));
    $sHTML.= $this->_getLeftSectionJobList($pnPk);
    $sHTML.= $this->_getJobDetailInformation($pnPk);
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

   /**
   * Function to get Job Detail Information
   * @param integer $pnPk
   * @return string HTML
   */

   private function _getJobDetailInformation($pnPk)
   {
     if(!assert('is_integer($pnPk) && !empty($pnPk)'))
        return '';

     $oHTML = CDependency::getComponentByName('display');
     $oPage = CDependency::getComponentByName('page');
     $oDB = CDependency::getComponentByName('database');

     $sHTML = $oHTML->getBlocStart('',array('class'=>'jobCentreSection'));

     $sQuery = 'SELECT pos.*, ind.*, IF(pos.company_label IS NOT NULL && pos.company_label <> "", pos.company_label, cp.company_name) as company_name, ind.name AS industry_name FROM position AS pos LEFT JOIN company ';
     $sQuery.= ' AS cp ON cp.companypk = pos.companyfk LEFT JOIN industry AS ind ON pos.industryfk = ind.industrypk WHERE pos.visibility <> 0 and pos.positionpk = '.$pnPk.'';
     $oDbResult = $oDB->ExecuteQuery($sQuery);
     $bRead = $oDbResult->readFirst();

     if($bRead)
     {
        $sPageTitle    = $oDbResult->getFieldValue('page_title');
        $sMetaKeywords = $oDbResult->getFieldValue('meta_keywords');
        $sMetaDescription = $oDbResult->getFieldValue('meta_desc');

        //Set meta keywords and meta description

        if(!empty($sPageTitle))
          $oPage->setPageTitle($sPageTitle);
        if(!empty($sMetaKeywords))
          $oPage->setPageKeywords($sMetaKeywords);
        if(!empty($sMetaDescription))
          $oPage->setPageDescription($sMetaDescription);

        $sHTML.= $oHTML->getBlocStart('position', array('style'=>'padding-top:10px;border-bottom:5px solid #2789BC;padding-bottom:6px;'));
        $sHTML.= $oHTML->getText($oDbResult->getFieldValue('position_title'), array('class'=>'boldTitle'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('style'=>'margin-top:10px;'));
        //Position Title

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
         $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
         $sHTML.= $oHTML->getText($this->casText['TALENT_POSITION_TITLE'],array('style'=>'font-weight:bold;'));
         $sHTML.= $oHTML->getBlocEnd('');

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= $oHTML->getText( $oDbResult->getFieldValue('position_title'));
          $sHTML.= $oHTML->getBlocEnd('');
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd('');

        //Company
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_COMPANY'],array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd('');

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          if($oDbResult->getFieldValue('company_name'))
            $sHTML.= $oHTML->getText($oDbResult->getFieldValue('company_name'));
          else
            $sHTML.= $oHTML->getText('Company Name not visible');

          $sHTML.= $oHTML->getBlocEnd('');
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd('');

        //Location
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_LOCATION'],array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd('');

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= $oHTML->getText($oDbResult->getFieldValue('location'));
          $sHTML.= $oHTML->getBlocEnd('');
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd('');

        //Posted Date
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_POST_DATE'],array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd('');

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= $oHTML->getText($oDbResult->getFieldValue('posted_date'));
          $sHTML.= $oHTML->getBlocEnd('');
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd('');

        //English Level

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_ENGLISH_ABILITY'],array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd('');

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sEnglish = $this->_getLanguageLevel((int)$oDbResult->getFieldValue('english'));
          $sHTML.= $oHTML->getText($sEnglish);
          $sHTML.= $oHTML->getBlocEnd('');
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd('');

        //Japanese Level

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_JAP_ABILITY'],array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd('');

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sJapanese = $this->_getLanguageLevel((int)$oDbResult->getFieldValue('japanese'));
          $sHTML.= $oHTML->getText($sJapanese);
          $sHTML.= $oHTML->getBlocEnd('');
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd('');

        //Industry

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_INDUSTRY'],array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd('');

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          if(!empty($this->casText[$oDbResult->getFieldValue('industry_name')]))
            $sHTML.= $oHTML->getText($this->casText[$oDbResult->getFieldValue('industry_name')]);
          else
            $sHTML.= $oHTML->getText('');
          $sHTML.= $oHTML->getBlocEnd('');
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd('');

        //Career Level

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
         $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_CAREER'],array('style'=>'font-weight:bold;'));
           $sHTML.= $oHTML->getBlocEnd('');
           $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
           $sHTML.= $oHTML->getText($oDbResult->getFieldValue('career_level'));
          $sHTML.= $oHTML->getBlocEnd('');
         $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd('');

        //Salary

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
         $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_SALARY'],array('style'=>'font-weight:bold;'));
           $sHTML.= $oHTML->getBlocEnd('');
           $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
           $sHTML.= $oHTML->getText($oDbResult->getFieldValue('salary'));
          $sHTML.= $oHTML->getBlocEnd('');
         $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd('');

        //Requirements

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
         $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
           $sHTML.= $oHTML->getText($this->casText['TALENT_REQUIREMENTS'],array('style'=>'font-weight:bold;'));
            $sHTML.= $oHTML->getBlocEnd('');
            $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
            $sHTML.= $oHTML->getText(nl2br($oDbResult->getFieldValue('requirements')));
           $sHTML.= $oHTML->getBlocEnd('');
         $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd('');

        //Description
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
         $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
           $sHTML.= $oHTML->getText($this->casText['TALENT_DESCRIPTION'], array('style'=>'font-weight:bold;'));
            $sHTML.= $oHTML->getBlocEnd('');
            $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
           $sHTML.= $oHTML->getText(nl2br($oDbResult->getFieldValue('position_desc')));
          $sHTML.= $oHTML->getBlocEnd('');
         $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd('');

        //Train Station Display if exists
        $sStation = $oDbResult->getFieldValue('station');
        if(!empty($sStation))
        {
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
            $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
            $sHTML.= $oHTML->getText($this->casText['TALENT_STAION'],array('style'=>'font-weight:bold;'));
            $sHTML.= $oHTML->getBlocEnd('');

            $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
            $sHTML.= $oHTML->getText($oDbResult->getFieldValue('station'));
            $sHTML.= $oHTML->getBlocEnd('');
            $sHTML.= $oHTML->getFloatHack();
          $sHTML.= $oHTML->getBlocEnd('');
        }

        //Holidays Display if exists
        $sHolidays = $oDbResult->getFieldValue('holidays');
        if(!empty($sHolidays))
        {
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
            $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
            $sHTML.= $oHTML->getText($this->casText['TALENT_HOLIDAYS'],array('style'=>'font-weight:bold;'));
            $sHTML.= $oHTML->getBlocEnd('');

            $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
            $sHTML.= $oHTML->getText($oDbResult->getFieldValue('holidays'));
            $sHTML.= $oHTML->getBlocEnd('');
            $sHTML.= $oHTML->getFloatHack();
          $sHTML.= $oHTML->getBlocEnd('');
        }

        //Work Hours Display if exists
        $sWorkHours = $oDbResult->getFieldValue('work_hours');
        if(!empty($sWorkHours))
        {
         $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_WORK_HOUR'],array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd('');

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= $oHTML->getText($oDbResult->getFieldValue('work_hours'));
          $sHTML.= $oHTML->getBlocEnd('');
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
          $sHTML.= $oHTML->getBlocEnd('');
         $sHTML.= $oHTML->getBlocEnd('');
        }

        //Apply Button
        $sURL =  "'".$oPage->getUrl($this->_getUid(), CONST_ACTION_APPLY, CONST_TA_TYPE_JOB, $pnPk)."'";
          $sHTML.= $oHTML->getBlocStart('',array('style'=>'margin-top:15px;'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText('&nbsp;');
          $sHTML.= $oHTML->getBlocEnd('');

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= "<input type='button' name='apply' id='apply' value=".$this->casText['TALENT_APPLY_NOW']." style='background-color:#0166ca;border-radius:0px;' onclick = \"document.location.href = ".$sURL."\">";
          $sHTML.= $oHTML->getBlocEnd('');
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
          $sHTML.= $oHTML->getBlocEnd('');
        $sHTML.= $oHTML->getBlocEnd('');
       }
       else
       {
         $sHTML.= $oHTML->getBlocMessage('Position may have been deleted or expired');
       }

       $sHTML.= $oHTML->getBlocEnd('');
       $sHTML.= $oHTML->getBlocEnd('');

    return $sHTML;
  }

  /**
   * Function to get the language level name
   * @param integer $pnPk
   * @return array with language level information
   */

  private function _getLanguageLevel($pnPk=0)
  {
    $asLanguage = array('0'=>$this->casText['TALENT_LANG_NONE'],'1'=>$this->casText['TALENT_LANG_BASIC'],'2'=>$this->casText['TALENT_LANG_CONV'],'3'=>$this->casText['TALENT_LANG_BUSINESS'],'4'=>$this->casText['TALENT_LANG_FLUENT'],'5'=>$this->casText['TALENT_LANG_NATIVE']);

    return $asLanguage[$pnPk];
  }

  /**
   * Function to return all the language levels
   * @return array
   */

  public function getLanguages()
  {
    $asLanguage = array('0'=>$this->casText['TALENT_LANG_NONE'],'1'=>$this->casText['TALENT_LANG_BASIC'],'2'=>$this->casText['TALENT_LANG_CONV'],'3'=>$this->casText['TALENT_LANG_BUSINESS'],'4'=>$this->casText['TALENT_LANG_FLUENT'],'5'=>$this->casText['TALENT_LANG_NATIVE']);
     return $asLanguage;
  }

   /**
   * Function to display the Job Application Form
   * @param integer $pnPk
   * @return string HTML
   */

  public function getJobApply($pnPk)
  {
    if(!assert('is_integer($pnPk) && !empty($pnPk)'))
     return '';

    $oHTML = CDependency::getComponentByName('display');

    $sHTML = $oHTML->getBlocStart('',array('class'=>'homepageContainer'));
    //Left Section
    $sHTML.= $this->_getLeftSectionJobList($pnPk);
    //Middle Section
    $sHTML.= $this->getJobApplyForm($pnPk);
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

   /**
   * Function to display the Job Application Form
   * @param integer $pnPk
   * @return string
   */

  public function getJobApplyForm($pnPk)
  {
   if(!assert('is_integer($pnPk) && !empty($pnPk)'))
     return '';

     $oHTML = CDependency::getComponentByName('display');
     $oPage = CDependency::getComponentByName('page');

     $sHTML = $oHTML->getBlocStart('',array('class'=>'jobCentreSection'));
     $sHTML.= $oHTML->getBlocStart('jobForm');
     $sHTML.= $oHTML->getBlocStart('',array('style'=>'padding-top:10px;border-bottom:5px solid #2789BC;padding-bottom:6px;'));

     if(!empty($pnPk))
     {
        $sHTML.= $oHTML->getText($this->casText['TALENT_COVER_LETTER'],array('class'=>'boldTitle'));
        $sHTML.= $oHTML->getSpace(2);
        $sHTML.= $oHTML->getText($this->casText['TALENT_FOR'].' '.$this->getPositionName($pnPk),array('class'=>'subTitle'));
      }
      else
       $sHTML.= $oHTML->getText($this->casText['TALENT_COVER_LETTER'],array('class'=>'boldTitle'));

      $sHTML.= $oHTML->getBlocEnd('');

      $sHTML.= $oHTML->getBlocStart('',array('style'=>'margin-top:20px;'));
      $oForm = $oHTML->initForm('jobApplyForm');

      $oPage->addCssFile(array($this->getResourcePath().'css/talentatlas.css'));
      $sURL = $oPage->getURL($this->_getUid(),CONST_ACTION_SAVEADD,CONST_TA_TYPE_JOB,$pnPk);

      $oForm->setFormParams('', false, array('submitLabel' => $this->casText['TALENT_APPLY_NOW'],'action' => $sURL,'onBeforeSubmit'=>'if(!$(\'#agree_terms_id:checked\').val()){ alert(\'Please accept terms and conditions \'); return true; };'));
      $oForm->setFormDisplayParams(array('noCancelButton' => 1, 'columns' => 1));

      $oForm->addField('input', 'name', array('label'=>$this->casText['TALENT_YOUR_NAME'], 'class' => '', 'value' => ''));
      $oForm->setFieldControl('name', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255, 'jsFieldNotEmpty' => ''));

      $oForm->addField('input', 'email', array('label'=>$this->casText['TALENT_YOUR_EMAIL'], 'class' => '', 'value' => ''));
      $oForm->setFieldControl('email', array('jsFieldMinSize' => '6', 'jsFieldTypeEmail' => '', 'jsFieldNotEmpty' => ''));

      $oForm->addField('textarea', 'coverletter', array('label'=>$this->casText['TALENT_YOUR_LETTER'], 'value' => '','style'=>'width:705px;height:250px;'));
      $oForm->setFieldControl('coverletter', array('jsFieldMaxSize' => 10000));

      $oForm->addField('input', 'documents[]', array('type' => 'file', 'label'=>$this->casText['TALENT_YOUR_RESUME'], 'value' => ''));
      $oForm->setFieldControl('documents[]', array('jsFieldNotEmpty' => ''));

      $oForm->addField('misc','',array('type'=>'br'));
      $sJavascript = '$(\'#privacyAgreement\').fadeToggle(); ';

      $oForm->addField('checkbox', 'agree_terms', array('type' => 'misc', 'label'=> $this->casText['TALENT_I_AGREE'].' <a href="javascript:;" onclick="'.$sJavascript.'">'.$this->casText['TALENT_TERMS'].' </a>'.$this->casText['TALENT_EXTRA'], 'value' => 1, 'style' =>'width:15px;', 'id' => 'agree_terms_id','errorMessage'=>'Please, accept terms and conditions'));
      $oForm->addField('misc','',array('type'=>'text', 'style'=>'display:none;', 'id'=>'privacyAgreement','text' =>'<pre>'.file_get_contents($_SERVER['DOCUMENT_ROOT'].$this->getResourcePath().'terms.php').'</pre>'));
      $oForm->addField('checkbox', 'accept_contact', array('type' => 'misc', 'checked'=>'checked', 'label'=> $this->casText['TALENT_CONTACT_ANYONE'], 'value' => 1, 'id' => 'accept_contact_id','style' => 'width:15px;'));

      $sHTML.= $oForm->getDisplay();

      $sHTML.= $oHTML->getBlocEnd('');
      $sHTML.= $oHTML->getBlocEnd('');
      $sHTML.= $oHTML->getBlocEnd('');

     return $sHTML;
  }

  /**
   * Function to save the resume for the applied position
   * @param integer $pnPositionPk
   * @return string HTML
   */

  private function _getResumeSave($pnPositionPk)
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oMail = CDependency::getComponentByName('mail');

    if(!assert('is_integer($pnPositionPk)'))
      return $oHTML->getErrorMessage('Incorrect Data.');

    $oDB = CDependency::getComponentByName('database');

    $sName = getValue('name');
    $sEmail = getValue('email');
    $sCoverLetter = getValue('coverletter');
    $ncanContact = getValue('accept_contact');
    $sAppliedDate = date('Y-m-d');

    $sQuery = 'SELECT pos.*,cp.company_name,ind.name AS industry_name FROM position AS pos LEFT JOIN company ';
    $sQuery.= ' AS cp ON cp.companypk = pos.companyfk LEFT JOIN industry AS ind ON pos.industryfk = ind.industrypk WHERE pos.visibility <> 0 and pos.positionpk = '.$pnPositionPk.'';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if($bRead)
    {
      $asData = $oDbResult->getData();

      $sCompanyName = $asData['company_name'];
      $sIndustryName = $this->casText[$asData['industry_name']];
      $sPositionName = $asData['position_title'];
      $sSalary = $asData['salary'];

      //Send automatic email to the  person who has applied for the job

      $sSubject = 'Talent Atlas confirmation ';
      $sContent = 'Dear '.$sName.', <br/><br/>';
      $sContent.= 'Thank you for applying for the position of <strong>'. $sPositionName.'</strong> on Talent Atlas.<br/>';
      $sContent.= 'We\'ve correctly received your resume.<br/><br/>';

      $sContent.= 'Find here a summary of the job you\'ve applied to: <br/>';
      $sContent.= '<div style="font-family: Verdana,Helvetica,Arial; font-size: 11px; border: 1px solid #dadada; padding: 5px; margin: 5px;">';
      $sContent.= 'Title : '.$sPositionName.'<br/>';
      $sContent.= 'Industry : '.$sIndustryName.'<br/>';
      if(!empty($sCompanyName))
        $sContent.= 'Company : '.$sCompanyName.'<br/>';

      $sContent.= 'Salary : '.$sSalary.'<br/>';
      if(!empty($asData['position_desc']))
        $sContent.= 'Description:<br /><div style="margin: 5px; padding: 5px; ">'.nl2br($asData['position_desc']).'</div><br/>';
      elseif(!empty($asData['requirements']))
        $sContent.= 'Requirements:<br /><div style="margin: 5px; padding: 5px; ">'.nl2br($asData['requirements']).'</div><br/>';

      $sContent.= '</div> <br/>';
      $sContent.= ' A member of our team will contact you shortly to start the application process.<br/>';
      $sContent.= ' Best regards.';

      $bResponse = $oMail->sendRawEmail('no-replay@talentatlas.com',$sEmail,$sSubject,$sContent);

      if($bResponse)
      {
      $sQuery = 'INSERT INTO job_application (`positionfk`,`name`,`email`,`coverletter`,`application_date`,`canContact`) VALUES (';
      $sQuery.=  ''.$oDB->dbEscapeString($pnPositionPk).','.$oDB->dbEscapeString($sName).','.$oDB->dbEscapeString($sEmail).','.$oDB->dbEscapeString($sCoverLetter).','.$oDB->dbEscapeString($sAppliedDate).','.$oDB->dbEscapeString($ncanContact).')';

      $oResult = $oDB->ExecuteQuery($sQuery);
      $nJobApplicationPk = (int)$oResult->getFieldValue('pk');

      if(!isset($_FILES) || !isset($_FILES['documents']) || !isset($_FILES['documents']['tmp_name']))
        return $oHTML->getErrorMessage(__LINE__.' - File can not be uploaded.');

        foreach($_FILES['documents']['tmp_name'] as $nKey => $sTmpFileName)
        {
          $sFileName = $_FILES['documents']['name'][$nKey];

          if(filesize($sTmpFileName) > (25*1024*1024))
            return $oHTML->getErrorMessage(__LINE__.' - The file is too big to upload.');

          $sNewPath = $_SERVER['DOCUMENT_ROOT'].CONST_PATH_UPLOAD_DIR.'job/document/'.$nJobApplicationPk.'/';
          $sNewName = date('YmdHis').'_'.$sFileName;

          if(!is_dir($sNewPath) && !makePath($sNewPath))
            return $oHTML->getErrorMessage(__LINE__.' - Destination folder doesn\'t exist.('.$sNewPath.')');

          if(!is_writable($sNewPath))
            return $oHTML->getErrorMessage(__LINE__.' - Can\'t write in the destination folder.');

          if(!move_uploaded_file($sTmpFileName, $sNewPath.$sNewName))
            return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t move the uploaded file.');

          $sQuery = 'UPDATE `job_application` SET `resume` = '.$oDB->dbEscapeString($sNewPath.$sNewName).' WHERE job_applicationpk = '.$nJobApplicationPk;
          $oResult = $oDB->ExecuteQuery($sQuery);
          if(!$oResult)
            return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save the uploaded file.');

          //---------------------------------------------------
          //application saved: Sending email about notification of someone applied
          $asPosition = $this->getPositionByPk($pnPositionPk);
          if(empty($sCoverLetter))
          {
            $sCoverLetter = '<span style="color: #444; font-style: italic;">No cover letter from the applicant</span>';
          }
          else
            $sCoverLetter = nl2br($sCoverLetter);

          $sSubject = 'TalentAtlas notifier: application to job #'.$pnPositionPk;
          $sFileUrl = CONST_CRM_DOMAIN.CONST_PATH_UPLOAD_DIR.'job/document/'.$nJobApplicationPk.'/'.$sNewName;
          $sUrl = $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW, CONST_TA_TYPE_JOB, $pnPositionPk);

          $sContent = '<div style="font-family: Verdana,Helvetica,Arial; font-size: 11px; border: 1px solid #dedede; padding: 5px; margin: 5px;">';
          $sContent.= 'We\'ve just received an application for the job offer #'.$pnPositionPk.': "'.$asPosition[$pnPositionPk]['position_title'].'".<br />Click <a href="'.$sUrl.'">here</a> to view the detail of the offer.<br/> <br/>';
          $sContent.= 'Application date : '.date('Y-m-d H:i:s').'<br/>';
          $sContent.= 'Applicant name : '.$sName.'<br/>';
          $sContent.= 'Email Address : '.$sEmail.'<br/><br/>';
          $sContent.= 'Cover Letter :<br /> <div style="margin: 5px; padding: 5px; border: 1px solid #aaaaaa;">'.$sCoverLetter.'</div><br/>';
          $sContent.= 'Resume: <a href="'.$sFileUrl.'">'.$sFileName.'</a> <br/><br/>';
          $sContent.= 'Thanks. </div>';

          $oMail->creatNewEmail();
          $oMail->setFrom('admin@talentatlas.com', 'TalentAtlas admin');

          if(CONST_DEV_SERVER)
          {
            //$oMail->addRecipient(CONST_DEV_EMAIL, 'dev 1');
            //$oMail->addRecipient(CONST_DEV_EMAIL_2, 'dev 2');
          }
          else
          {
            $oMail->addRecipient('info@slate.co.jp', 'info@slate.co.jp');
          }

          $oResult = $oMail->send($sSubject, $sContent);
          $sURL = $oPage->getUrl($this->_getUid(),CONST_ACTION_LIST,CONST_TA_TYPE_JOB);
          return $oHTML->getRedirection($sURL, 100);
         }
        }
      }
    }

    /**
     * Function to get position name from positionpk
     * @param integer $pnPositionPk
     * @return string
     */

    private function getPositionName($pnPositionPk)
    {
      $asPosition = $this->getPositionByPk($pnPositionPk);

      if(empty($asPosition) || empty($asPosition[$pnPositionPk]['position_title']))
        return '';

      return $asPosition[$pnPositionPk]['position_title'];
    }

    private function getPositionByPk($pvPositionPk)
    {
      if(!assert('(is_integer($pvPositionPk) || is_array($pvPositionPk))'))
        return array();

      if(is_array($pvPositionPk))
      {
        foreach($pvPositionPk as $nPositionPk)
        {
          if(!is_integer($nPositionPk))
          {
            assert('false; // not an array of integer');
            return array();
          }
        }
      }
      else
        $pvPositionPk = array($pvPositionPk);

      if(!assert('!empty($pvPositionPk)'))
        return array();

      $oDB = CDependency::getComponentByName('database');

      $sQuery = 'SELECT * FROM position WHERE positionpk IN ('.implode(',', $pvPositionPk).') ';
      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();
      if(!$bRead)
        return array();

      $asPosition = array();
      while($bRead)
      {
        $asPosition[$oResult->getFieldValue('positionpk')] = $oResult->getData();
        $bRead = $oResult->readNext();
      }

      return $asPosition;
    }

    /**
    * Function to return array of industries and single industry detail also
    * @param integer $pnIndustryPk
    * @return array/string
    */
    public function getIndustries($pnIndustryPk = 0, $pbParentFirst = false, $pbInUseOnly = false, $pbNonTranslated = false)
    {
      if(!assert('is_integer($pnIndustryPk) && is_bool($pbParentFirst) && is_bool($pbInUseOnly)'))
        return array();

      $oDB = CDependency::getComponentByName('database');
      $sExtraOrder = $sExtraJoin = $sExtraWhere = $sExtraGroup = '';

      if($pbParentFirst)
        $sExtraOrder = ' ind.parentfk ASC, ';

      if($pbInUseOnly)
      {
        $sExtraJoin = ' LEFT JOIN position as pos ON (pos.industryfk = industrypk AND pos.status = 1) ';
        $sExtraWhere = ' AND (ind.parentfk = 0 OR pos.industryfk IS NOT NULL) ';
        $sExtraGroup = ' GROUP BY ind.industrypk ';
      }


      if(empty($pnIndustryPk))
      {
        if($pbNonTranslated)
          $sStatus = ' ind.status > 0 ';
        else
          $sStatus = ' ind.status = 1 ';

        $sQuery = 'SELECT DISTINCT ind.* FROM industry as ind '.$sExtraJoin.' WHERE  '.$sStatus.' '.$sExtraWhere.' '.$sExtraGroup.' ORDER BY '.$sExtraOrder.' ind.name ASC ';
        $oResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oResult->readFirst();

        $asIndustries = array();
        while($bRead)
        {
          $asIndustries[$oResult->getFieldValue('industrypk')] = $oResult->getData();
          $bRead = $oResult->readNext();
        }
        return $asIndustries;
      }

      $sQuery = 'SELECT DISTINCT ind.* FROM industry as ind '.$sExtraJoin.' WHERE industrypk = '.$pnIndustryPk.' '.$sExtraWhere.' '.$sExtraGroup;
      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();

      $sIndustry = $oResult->getFieldValue('name');
      return $this->casText[$sIndustry];
    }

    /**
     * Function to remove the japanese characters
     * @param string $psString
     * @return string
     */

    private function _removeJapanese($psString)
    {
      if(!assert('!empty($psString)'))
        return '';

      $sEnglishString = preg_replace("/[^a-z A-Z -]/","", $psString);
        return $sEnglishString;
   }

  public function setLanguage($psLanguage)
  {
    include('component/talentatlas/language/language.inc.php5');

    if(isset($gasLang[$psLanguage]))
      $this->casText = $gasLang[$psLanguage];
    else
      $this->casText = $gasLang[CONST_DEFAULT_LANGUAGE];
  }

  public function getTranslation($psTextCode)
  {
    if(!assert('isset($this->casText["'.$psTextCode.'"])'))
      return '';

    return $this->casText[$psTextCode];
  }
}