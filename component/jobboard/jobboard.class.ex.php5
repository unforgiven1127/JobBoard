<?php

require_once('component/jobboard/jobboard.class.php5');
require_once('component/taaggregator/resources/lib/encoding_converter.class.php5');

class CJobboardEx extends CJobboard
{

  private $casConsultant = array('pam_thai.png', 'ray_pedersen.png', 'frank_henderson.png', 'ryan_marshall.png');
  private $casConsultantIndustries = array();

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

        case CONST_TA_TYPE_JOB_RSS:
          switch($this->csAction)
          {
            case CONST_ACTION_LIST:
              return $this->_getRssFeed();
              break;

            case CONST_ACTION_VIEW:
              return $this->_getRssDescription();
              break;
          }
          break;
     }

     return '';
  }

  //====================================================================
  //  public methods
  //====================================================================

  public function getHtml()
  {
    $this->_processUrl();

    $oPage = CDependency::getComponentByName('page');

    if(CONST_WEBSITE == 'jobboard')
    {
      $oPage->addCssFile(array('http://fonts.googleapis.com/css?family=Open+Sans', 'http://fonts.googleapis.com/css?family=Rokkitt'));
      $oPage->addCssFile(array($this->getResourcePath().'css/jobboard.css'));

      //TODO: manage multilinguale meta in settings. For the time being, it's here
      $oPage->setPageKeywords($this->casText['TALENT_PAGE_KEYWORDS'], true);
      $oPage->setPageTitle($this->casText['TALENT_PAGE_TITLE'], true);
      $oPage->setPageDescription($this->casText['TALENT_PAGE_DESCRIPTION'], true);
    }

    switch($this->csType)
    {
      case CONST_TA_TYPE_JOB:

        switch($this->csAction)
        {
          case CONST_TALENT_HOME_PAGE:
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
            $vResult = $this->_getResumeSave($this->cnPk);

            if(is_array($vResult) && isset($vResult['error']))
            {
              $oHTML = CDependency::getComponentByName('display');
              $sHTML = $oHTML->getErrorMessage($vResult['error']);
              $sHTML.= $this->getJobApply($this->cnPk);
              return $sHTML;
            }

            return $vResult;
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

    $oForm->addField('input', 'name', array('label'=> $this->casText['TALENT_MAILFRIEND_NAME']));
    $oForm->setFieldControl('name', array('jsFieldNotEmpty' => ''));

    $oForm->addField('input', 'email', array('label'=> $this->casText['TALENT_MAILFRIEND_EMAIL']));
    $oForm->setFieldControl('email', array('jsFieldTypeEmail' => '','jsFieldNotEmpty' => ''));

    $oForm->addField('input', 'femail1', array('label'=> $this->casText['TALENT_MAILFRIEND_FRIEND1']));
    $oForm->setFieldControl('femail1', array('jsFieldTypeEmail' => '','jsFieldNotEmpty' => ''));

    $oForm->addField('input', 'femail2', array('label'=> $this->casText['TALENT_MAILFRIEND_FRIEND2']));
    $oForm->setFieldControl('femail2', array('jsFieldTypeEmail' => ''));

    $oForm->addField('input', 'femail3', array('label'=> $this->casText['TALENT_MAILFRIEND_FRIEND3']));
    $oForm->setFieldControl('femail3', array('jsFieldTypeEmail' => ''));

    $oForm->addField('textarea', 'message', array('label'=> $this->casText['TALENT_MAILFRIEND_MESSAGE'], 'style'=>'width:400px;height:110px;'));
    $oForm->setFieldControl('message', array('jsFieldNotEmpty' => ''));


     $oForm->addField('misc', '', array('type'=> 'text', 'text' => ''));


    $nNumber = rand(100, 900);
    $nSum1 = rand(1, 9);
    $nSum2 = rand(1, 8);
    if($nSum1 == $nSum2)
      $nSum2++;

    $asName = array('cvb', 'abc', 'fgh', 'iop', 'zxc', 'wer', 'ghj', 'vbn', 'tyu', 'sdf');
    $sImage = $oHTML->getPicture($this->getResourcePath().'/pictures/captcha/'.$asName[$nSum1].'_b.png', '', '', array('style' => 'width: 14px; height: 14px;'));
    $sImage2 = $oHTML->getPicture($this->getResourcePath().'/pictures/captcha/'.$asName[$nSum2].'_w.png', '', '', array('style' => 'width: 14px; height: 14px;'));

    if(($nNumber % 2) == 0)
    {
      $_SESSION['MAIL_CONFIRM_HUMAN'] = ($nNumber + $nSum2);
      $sColor = $this->casText['TALENT_CONFIRM_WHITE'];
    }
    else
    {
      $_SESSION['MAIL_CONFIRM_HUMAN'] = ($nNumber + $nSum1);
      $sColor = $this->casText['TALENT_CONFIRM_BLACK'];
    }

    $oForm->addField('input', 'human', array('label'=> '<span style="line-height: 20px;">'.$this->casText['TALENT_CONFIRM_HUMAN'].' ('.$nNumber.' + <b>'.$sColor.'</b>) &nbsp;&nbsp;&nbsp;&nbsp;'.$sImage.'&nbsp;&nbsp;'.$sImage2.'</span>'));
    $oForm->setFieldControl('human', array('jsFieldNotEmpty' => ''));



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

    $nHuman      = (int)getValue('human', 0);

    if($nHuman != $_SESSION['MAIL_CONFIRM_HUMAN'])
      return array('error' => 'Looks like you\'re not a human. Please try again.');

    //reset the captcha
    unset($_SESSION['MAIL_CONFIRM_HUMAN']);

   if(empty($sSenderName))
      return array('error' => 'You need to input a name or pseudo.');

    if(empty($sFriendEmail1) && empty($sFriendEmail2) && empty($sFriendEmail3))
      return array('error' => 'You need to specify at least 1 friend email address.');

    if(empty($sSenderEmail) || !isValidEmail($sSenderEmail))
      return array('error' => 'Your email address is not correct.');

    if(!empty($sFriendEmail1) && !isValidEmail($sFriendEmail1))
      return array('error' => '1st friend email address is not correct.');

    if(!empty($sFriendEmail2) && !isValidEmail($sFriendEmail2))
      return array('error' => '2nd friend email address is not correct.');

    if(!empty($sFriendEmail3) && !isValidEmail($sFriendEmail3))
      return array('error' => '3rd friend email address is not correct.');


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

    if(CONST_WEBSITE == 'jobboard')
      $sSubject = 'Your friend '.$sSenderName.' has forwarded you a job from Slate Job board';
    else
      $sSubject = 'Your friend '.$sSenderName.' has forwarded you a job from Talent Atlas';

    $sLink = $oPage->getUrl('jobboard',CONST_ACTION_VIEW,CONST_TA_TYPE_JOB,(int)$nPositionPk);

    $sContent = ' Hello, <br/>';
    $sContent.= ' This is a job suggestion coming from <a href="'.CONST_CRM_HOST.'">'.CONST_CRM_HOST.'</a> <br/>';
    $sContent.= ' '.$sSenderName.' ('.$sSenderEmail.') has requested you to be  notified concerning the following position.<br/>';
    $sContent.= ' Message : <div style="margin:5px;padding:5px;">'.$sMessage.'</div>';
    $sContent.= ' <div style="font-family: Verdana,Helvetica,Arial; font-size: 11px; border: 1px solid #dedede; padding: 5px; margin: 5px;">';
    $sContent.= ' Url : <a href = "'.$sLink.'">'.$sLink.'</a> <br/> <br/>';

    $sContent.= ' Title : '.$sPositionName.'<br/>';
    $sContent.= ' Industry : '.$sIndustryName.'<br/>';
    if(!empty($sCompanyName))
      $sContent.= ' Company : '.$sCompanyName.'<br/>';

    $sContent.= ' Salary : '.$sSalary.'<br/>';
    $sContent.= ' Description:<br /><div style="margin: 5px; padding: 5px; ">'.nl2br(strip_tags($asData['position_desc'])).'</div><br/><br/>';
    $sContent.= ' <div style ="font-size:10px;font-weight:bold;font-style:italic;">see the full description <a href = "'.$sLink.'"> here </a> </div>.<br/>';
    $sContent.= ' </div> <br/>';

    $sContent.= ' Best Regards <br/><br/>';
    $sContent.= ' If you refuse to receive any more email from our website, please click  <a href="#"> here.</a> ';

    //save before sending email, in case there's a problem
    $sQuery = 'INSERT INTO job_mailfriend ( `sender_name`, `sender_email`, `receiver_email1`, `receiver_email2`, `receiver_email3`, `message`, `positionfk`) ';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString($sSenderName).','.$oDB->dbEscapeString($sSenderEmail).',';
    $sQuery.= ''.$oDB->dbEscapeString($sFriendEmail1).','.$oDB->dbEscapeString($sFriendEmail2).',';
    $sQuery.= ''.$oDB->dbEscapeString($sFriendEmail3).','.$oDB->dbEscapeString($sMessage).',';
    $sQuery.= ''.$oDB->dbEscapeString($nPositionPk).')';
    $oDbResult = $oDB->ExecuteQuery($sQuery);

    $bSent1 = $oMail->sendRawEmail($sSenderEmail,$sFriendEmail1,$sSubject,$sContent);

    if(!empty($sFriendEmail2))
    {
      $oMail->creatNewEmail();
      $bSent2 = $oMail->sendRawEmail($sSenderEmail,$sFriendEmail2,$sSubject,$sContent);
    }


    if(!empty($sFriendEmail3))
    {
      $oMail->creatNewEmail();
      $bSent3 = $oMail->sendRawEmail($sSenderEmail,$sFriendEmail3,$sSubject,$sContent);
    }



    if($oDbResult)
      return array('message' => 'Email successfully sent. ','reload'=>1);
      //return array('message' => 'Email successfully sent. '.(int)$bSent1.'-'.(int)$bSent2.'-'.(int)$bSent3,'reload'=>1);
  }

  /**
   * Function in Ajax  to get the jobs
   * @return array with data and message
   */

  private function _getAjaxJobSearchResult()
  {
    if(getValue('do_search', 0))
    {
      //ChromePhp::log('manageSearchHistory');
      $sSearchId = manageSearchHistory($this->csUid, CONST_TA_TYPE_JOB, true);
    }
    else
    {
      //ChromePhp::log('reloadLastSearch');
      if(getValue('searchId'))
        $sSearchId = manageSearchHistory($this->csUid, CONST_TA_TYPE_JOB);
      else
        $sSearchId = reloadLastSearch($this->csUid, CONST_TA_TYPE_JOB);
    }
//ChromePhp::log('before avResult');
    //Populate the sidebar things
//ChromePhp::log('before avResult');
    $avResult = $this->_getJobSearchResult('', $sSearchId);
//ChromePhp::log($avResult);
    if(empty($avResult) || empty($avResult['nNbResult']) || empty($avResult['oData']))
    {
      $oHTML = CDependency::getComponentByName('display');
      $sMessage = $this->_getSearchMessage($avResult['positionData'][0]['count'], true);
      return array('data' => $oHTML->getBlocMessage($this->casText['TALENT_NO_RESULT']/*.' || '.$avResult['sQuery']*/));
     }

    //in ajax, the dummy form should always be hidden
    $sData = $this->_getJobResultList($avResult, $sSearchId, false);

    if(empty($sData) || $sData == 'null' || $sData == null)
       return array('data' => $this->casText['TALENT_SORRY_ERROR'], 'action' => '$(\'.searchTitle\').html(\''.addslashes($sMessage).'\'); searchTitle(\'\', false, false); $(\'body\').scrollTop();');

     if(isset($avResult['positionData'][0]['count']))
     {
        $sMessage = $this->_getSearchMessage($avResult['positionData'][0]['count'], true);
     }
     else
     {
        $sMessage = $this->_getSearchMessage($avResult['positionData'][0]['count'], true);
     }

    $sData =  CEncoding::toUTF8($sData);

     return array('data' => $sData, 'action' => '$(\'.searchTitle\').html(\''.addslashes($sMessage).'\'); searchTitle(\'\', false, true); $(\'body\').scrollTop();');
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
    $sKeyWord = strtolower(getValue('keyword'));

    $leventOrderFlag = false;
    $levent = " ";
    if(isset($sKeyWord) && !empty($sKeyWord))
    {
      $levent = " , ROUND ( ( LENGTH(slpd.title) - LENGTH( REPLACE ( LOWER(slpd.title), '".$sKeyWord."', '') ) ) / LENGTH('".$sKeyWord."') ) AS ratio";
      $leventOrderFlag = true;
    }
    //$leventOrderFlag = false;

    $slistemDB = CDependency::getComponentByName('database');
    $slistemQuery = "SELECT FOUND_ROWS() as count ".$levent."
                     ,slp.sl_positionpk as positionpk, slp.sl_positionpk as jobfk,
                     slpd.is_public as visibility, slpd.category as category, slpd.career_level as career_level,
                     slpd.title as position_title, slpd.description as position_desc, slpd.requirements as requirements,
                     cp.sl_companypk as companyfk, slp.status as status, slp.date_created as posted_date, sll.location as location,
                     slpd.job_type as job_type, CONCAT(slp.salary_from,' - ',slp.salary_to) as salary, slp.salary_from as salary_low,
                     slp.salary_to as salary_high,  CONCAT(slp.age_from,' - ',slp.age_to) as age, slp.lvl_japanese as japanese,
                     slp.lvl_english as english, ind.sl_industrypk as industryfk, slpd.holidays as holidays, slpd.work_hours as work_hours,
                     slpd.language as lang, ind.sl_industrypk as temp_industry, slpd.title as page_title,
                     slpd.description as meta_desc, slpd.meta_keywords as meta_keywords, slpd.company_label as company_label,
                     slpd.to_jobboard as to_jobboard, slp.sl_positionpk as external_key, slpd.expiration_date as expiration_date,
                     ind.sl_industrypk as industrypk, ind.label as name, slp.status as status, ind.parentfk as parentfk,
                     cp.name as company_name, slpd.raw_data as raw_data
                     FROM sl_position slp
                     INNER JOIN sl_position_detail slpd on slpd.positionfk = slp.sl_positionpk AND slpd.is_public = '1' AND slpd.public_flag = 'a'";

//ChromePhp::log($slistemQuery);
    $oDb = CDependency::getComponentByName('database');
    $sToday = date('Y-m-d');
    $nCompanyPk = (int)getValue('companypk', 0);
    $nIndustryPk = (int)getValue('industrypk', 0);
    $sLocation = strtolower(getValue('location'));

    $asFilter = $this->_getSqlJobSearch();
    $filterSlistem = $this->_getSqlJobSearchSlistem();

    $sQuery = 'SELECT count(DISTINCT pos.positionpk) as nCount ';
    $sQuery.= ' FROM position as pos ';

    if(!empty($nIndustryPk))
    {
      $sQuery.= ' INNER JOIN industry AS ind ON (ind.industrypk = pos.industryfk AND ind.industrypk = '.$nIndustryPk.') ';
      $slistemQuery.= " INNER JOIN sl_industry ind on ind.sl_industrypk = slp.industryfk AND ind.sl_industrypk = ".$nIndustryPk;
    }
    else
    {
      $sQuery.= ' LEFT JOIN industry AS ind ON (ind.industrypk = pos.industryfk) ';
      $slistemQuery.= " INNER JOIN sl_industry ind on ind.sl_industrypk = slp.industryfk ";
    }
    if(!empty($sLocation))
    {
      $slistemQuery.= " INNER JOIN sl_location sll on sll.sl_locationpk = slpd.location AND sll.sl_locationpk = '".$sLocation."' ";
    }
    else
    {
      $slistemQuery.= " INNER JOIN sl_location sll on sll.sl_locationpk = slpd.location  ";
    }

    if(!empty($nCompanyPk))
    {
      $sQuery.= ' INNER JOIN company AS cp ON (cp.companypk = pos.companyfk AND cp.companypk = '.$nCompanyPk.') ';
      $slistemQuery.= " INNER JOIN sl_company cp on cp.sl_companypk = slp.companyfk AND cp.sl_companypk = ".$nCompanyPk;
    }
    else
    {
      $sQuery.= ' LEFT JOIN company AS cp ON (cp.companypk = pos.companyfk) ';
      $slistemQuery.= " INNER JOIN sl_company cp on cp.sl_companypk = slp.companyfk ";
    }


    $sQuery.= ' WHERE (expiration_date IS NULL OR expiration_date = "" OR expiration_date > "'.$sToday.'") ';

    $slistemQuery.=" WHERE slpd.public_flag = 'a' ";

    if(!empty($asFilter['where']))
    {
      //$exploded = explode('AND',$asFilter['where']);
      $sQuery.= ' AND '.$asFilter['where'];
    }
    if(!empty($filterSlistem['where']))
    {
      $slistemQuery.= ' AND '.$filterSlistem['where'];
    }

//ChromePhp::log($slistemQuery);
//ChromePhp::log($exploded);
//ChromePhp::log($filterSlistem['where']);
//ChromePhp::log($sQuery);
    $oDbResult = $oDb->ExecuteQuery($sQuery);
    $bRead = $oDbResult->ReadFirst();
    $nNbResult = $oDbResult->getFieldValue('nCount', CONST_PHP_VARTYPE_INT);

//ChromePhp::log($nNbResult);
    if($nNbResult == 0)
       return array('nNbResult' => 0, 'oData' => null, 'sQuery' => $sQuery);

    $sQuery = ' SELECT pos.*, ind.*, pos.company_label as company_name, job.data as raw_data ';

    if(isset($asFilter['select']) && !empty($asFilter['select']))
      $sQuery.= ', '.implode(', ', $asFilter['select']);

    $sQuery.= ' FROM position AS pos ';

    if(!empty($nIndustryPk))
      $sQuery.= ' INNER JOIN industry AS ind ON (ind.industrypk = pos.industryfk AND ind.industrypk = '.$nIndustryPk.' )';
    else
      $sQuery.= ' LEFT JOIN industry AS ind ON (ind.industrypk = pos.industryfk)';

    if(!empty($nCompanyPk))
      $sQuery.= ' INNER JOIN company AS cp ON (cp.companypk = pos.companyfk AND cp.companypk = '.$nCompanyPk.') ';
    else
      $sQuery.= ' LEFT JOIN company AS cp ON (cp.companypk = pos.companyfk) ';

    //join to the original job to fetch Slistem code
    $sQuery.= ' LEFT JOIN position AS parent_pos ON (parent_pos.positionpk = pos.parentfk) ';
    $sQuery.= ' LEFT JOIN job ON (job.jobpk = parent_pos.jobfk) ';


    $sQuery.= ' WHERE (pos.expiration_date IS NULL OR pos.expiration_date = "" OR pos.expiration_date > "'.$sToday.'") ';

    if(!empty($asFilter['where']))
      $sQuery.= ' AND '.$asFilter['where'];

    if(isset($asFilter['order']) && !empty($asFilter['order']))
      $sPriorityOrder = implode(', ', array_filter($asFilter['order'])).', ';
    else
      $sPriorityOrder = '';

    $sOrder = getValue('sortfield');
    switch($sOrder)
    {
      case 'date_asc': $sQuery.= ' ORDER BY '.$sPriorityOrder.' pos.visibility DESC, pos.positionpk  '; break;
      case 'date_desc': $sQuery.= ' ORDER BY '.$sPriorityOrder.' pos.visibility DESC, pos.positionpk DESC '; break;
      case 'salary_asc': $sQuery.= ' ORDER BY '.$sPriorityOrder.' pos.visibility DESC, pos.salary_low, pos.salary_high '; break;
      case 'salary_desc': $sQuery.= ' ORDER BY '.$sPriorityOrder.' pos.visibility DESC, pos.salary_high DESC, pos.salary_low DESC '; break;
      default:
        $sQuery.= ' ORDER BY '.$sPriorityOrder.' pos.visibility DESC, pos.positionpk DESC ';
    }
//ChromePhp::log($leventOrderFlag);
    if($leventOrderFlag)
    {
      $slistemQuery .= " order by ratio DESC, slp.sl_positionpk DESC";
    }
    else
    {
      $slistemQuery .= " order by slp.sl_positionpk DESC";
    }

ChromePhp::log($slistemQuery);
    $noLimitSql = $slistemQuery;
    $noLimitPositionData = $slistemDB->slistemGetAllData($slistemQuery); // neden anlamadim ama bunu ekleyince duzeldi....

//ChromePhp::log($slistemQuery);
//ChromePhp::log($positionData);

    $oPager = CDependency::getComponentByName('pager');
    $oPager->initPager();
    $sQuery.= ' LIMIT '.$oPager->getSqlOffset().','.$oPager->getLimit();
    $slistemQuery.= ' LIMIT '.$oPager->getSqlOffset().','.$oPager->getLimit();
    //echo $sQuery;

    $positionData = $slistemDB->slistemGetAllData($slistemQuery);

    $oDbResult = $oDb->ExecuteQuery($sQuery);
    $bRead= $oDbResult->readFirst();
//ChromePhp::log($oDbResult);
    if(!$bRead)
    {
      assert('false; // no result but count query was ok ');
      return array('nNbResult' => 0, 'oData' => null, 'sQuery' => $sQuery);
    }

    //return array('nNbResult' => $positionDataCount, 'oData' => $positionData, 'sQuery' => $slistemQuery);
    return array('nNbResult' => $nNbResult, 'oData' => $oDbResult, 'sQuery' => $sQuery, 'positionData' => $positionData);
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
    $oPage->addRequiredJsFile($this->getResourcePath().'js/jobboard.js');
    $bGlobalSearch = (bool)(getValue('global_search', 0));

    $sSearchWord = getValue('keyword');
    $asSearchWord = explode(' ', $sSearchWord);

    $asMatch = array();
    $asReplacement = array();
    foreach($asSearchWord as $nKey => $sWord)
    {
      if(empty($sWord) || strlen($sWord) < 3)
        unset($asSearchWord[$nKey]);
      else
      {
        $asMatch[] = '/([^<>]{1})'.$sWord.'([^<>]{1})/i';
        $asReplacement[] = '<span class=\'highlighted\'>$1'.$sWord.'$2</span>';
      }
    }


    $nNbResult = $pavResult['nNbResult'];
    $oDbResult = $pavResult['oData'];

    if(isset($pavResult['positionData']))
    {
      $positionData = $pavResult['positionData'];
      $positionDataCount = $pavResult['positionData'][0]['count'];
    }

    if(!$oDbResult)
      $bRead = false;
    else
      $bRead = $oDbResult->readFirst();

    $sHTML = '';
    $sHTML.= $oHTML->getBlocStart('jobListContainer');

    if($nNbResult > 0)
    {
      $sUrl = $oPage->getAjaxUrl('addressbook', CONST_ACTION_LIST, CONST_TA_TYPE_JOB, 0, array('searchId' => $psSearchId));
      $asPagerUrlOption = array('ajaxTarget' => 'jobListContainer');

      if($pbSearchFormOpen)
        $sStyle = '';
      else
        $sStyle = 'display: none;';



      $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchTitle'));

      if(isset($positionDataCount))
      {
        $sHTML.= $this->_getSearchMessage($positionDataCount);
      }
      else if(getValue('do_search'))
        $sHTML.= $this->_getSearchMessage($nNbResult);

      $sHTML.= $oHTML->getSpace();
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getFloatHack();

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
      if(isset($positionData))
      {
        foreach ($positionData as $key => $value)
        {
          $asJobData['position_desc'] = str_replace('\n', "\n", $value['position_desc']);
          $asJobData['position_title'] = preg_replace($asMatch, $asReplacement, $value['position_title']);
          $asJobData['position_desc'] = preg_replace($asMatch, $asReplacement, $value['position_desc']);
          $asJobData['company_name'] = preg_replace($asMatch, $asReplacement, $value['company_name']);
          $asJobData['location'] = preg_replace($asMatch, $asReplacement, $value['location']);

          $sHTML.= $this->_getJobRow($value, false, $sSearchWord);
        }
      }
      else
      {
        while($bRead)
        {
          $asJobData = $oDbResult->getData();
          $asJobData['position_desc'] = str_replace('\n', "\n", $asJobData['position_desc']);

          if(!empty($sSearchWord))
          {
            $asJobData['position_title'] = preg_replace($asMatch, $asReplacement, $asJobData['position_title']);
            $asJobData['position_desc'] = preg_replace($asMatch, $asReplacement, $asJobData['position_desc']);
            $asJobData['company_name'] = preg_replace($asMatch, $asReplacement, $asJobData['company_name']);
            $asJobData['location'] = preg_replace($asMatch, $asReplacement, $asJobData['location']);
          }

          $sHTML.= $this->_getJobRow($asJobData, false, $sSearchWord);
          $bRead = $oDbResult->ReadNext();
        }
      }

    }
    if($nNbResult > 0)
      $sHTML.= $oPager->getDisplay($nNbResult, $sUrl, $asPagerUrlOption);

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Function to get all the job rows
   * @param array $pasJobData
   * @return string
   */

  private function _getJobRow($pasJobData, $psCompact=false)
  {
    if(!assert('!empty($pasJobData) && is_array($pasJobData)'))
      return '';


    $jobDataClear = $pasJobData;
    //ChromePhp::log($jobDataClear);

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = $oHTML->getBlocStart('', array('class' => 'jobContainer'));

    //---------------------------------
    //display the consultant/industry picture on the left

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'consultantPicture'));
    $sHTML.= $this->_getConsultantPictureByIndustry((int)$pasJobData['industryfk']);
    $sHTML.= $oHTML->getBlocEnd();

    //---------------------------------
    //display the premium icon picture on the right
    if((int)$pasJobData['visibility'] == 2)
    {
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'jobPremiumIcon'));
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/premium.png');
      $sHTML.= $oHTML->getBlocEnd();
    }


    $sHTML.= $oHTML->getBlocStart('',array('class'=>'view_content'));

      //---------------------------------
      //job title block
      $nTitleLength = strlen(strip_tags($pasJobData['position_title']));
      if($nTitleLength > 75)
        $sTitle = substr(strip_tags($pasJobData['position_title']), 0, 72).'...';
      else
        $sTitle = $pasJobData['position_title'];


      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobClassFirst'));
        $sURL =  $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW, CONST_TA_TYPE_JOB,(int)$pasJobData['positionpk']);
        $sHTML.= $oHTML->getLink($oHTML->getText($sTitle, array('class'=>'subTitle')), $sURL, array('title' => $pasJobData['position_title']));
      $sHTML.= $oHTML->getBlocEnd();

      //---------------------------------
      //Some details (company, date...)
      $sHTML.= $oHTML->getListStart('',array('class'=>'list_items'));

      if(!empty($pasJobData['company_name']))
      {
        $sHTML.= $oHTML->getListItemStart();
          $sHTML.= $oHTML->getSpanStart();
          $sHTML.= $oHTML->getText('<strong>'.$this->casText['TALENT_COMPANY'].'</strong>: ');
          $sHTML.= $oHTML->getSpanEnd();
          //$sHTML.= $oHTML->getText($pasJobData['company_name']);
          $sHTML.= $oHTML->getText("Company Name not visible");
        $sHTML.= $oHTML->getListItemEnd();
      }

        $sHTML.= $oHTML->getListItemStart();
          $sHTML.= $oHTML->getSpanStart();
          $sHTML.= $oHTML->getText($this->casText['TALENT_LOCATION'].': ');
          $sHTML.= $oHTML->getSpanEnd();
          $sHTML.= $oHTML->getText($pasJobData['location']);
        $sHTML.= $oHTML->getListItemEnd();

        /*if(trim($pasJobData['salary']) != '0 - 0')
        {
          $sHTML.= $oHTML->getListItemStart();
            $sHTML.= $oHTML->getSpanStart();
            $sHTML.= $oHTML->getText('<strong>'.$this->casText['TALENT_SALARY'].'</strong>: ');
            $sHTML.= $oHTML->getSpanEnd();
            $sHTML.= $oHTML->getText($pasJobData['salary']);
          $sHTML.= $oHTML->getListItemEnd();
        }*/

        /*if(!$psCompact)
        {
          $sHTML.= $oHTML->getListItemStart();
            $sHTML.= $oHTML->getSpanStart();
            $sHTML.= $oHTML->getText($this->casText['TALENT_DATE'].': ');
            $sHTML.= $oHTML->getSpanEnd();
            $sHTML.= $oHTML->getText($pasJobData['posted_date']);
          $sHTML.= $oHTML->getListItemEnd();
        }*/

        $sJobData = $pasJobData['raw_data'];
        $asJobData = (array)@unserialize($sJobData);

////var_dump($jobDataClear);
//exit;

        if(isset($jobDataClear['positionpk']) && !empty($jobDataClear['positionpk']))
        {
          //$sIdentfier = 'JB'.$pasJobData['positionpk'];
          $sIdentfier = $jobDataClear['positionpk'];
        }
        else
        {
          $sIdentfier = 'SL'.(int)$asJobData['jobID'];
        }

        $sHTML.= $oHTML->getListItemStart();
          $sHTML.= $oHTML->getSpanStart();
          $sHTML.= $oHTML->getText('ID: ');
          $sHTML.= $oHTML->getSpanEnd();
          $sHTML.= $oHTML->getText($sIdentfier);
        $sHTML.= $oHTML->getListItemEnd();

        $sHTML.= $oHTML->getListItemStart();
          $sHTML.= $oHTML->getSpanStart();
          $sHTML.= $oHTML->getText('Industry: ');
          $sHTML.= $oHTML->getSpanEnd();
          $sHTML.= $oHTML->getText($pasJobData['name']);
        $sHTML.= $oHTML->getListItemEnd();


      $sHTML.= $oHTML->getListEnd();

      $sHTML.= $oHTML->getFloatHack();
      //---------------------------------
      //Description with short and extended versions
      $sLink = ' '.$oHTML->getLink($this->casText['TALENT_MORE_DETAILS'], $sURL);

      $sHTML.= $oHTML->getBlocStart('',array('class'=>'position_desc'));
      $sHTML.= $oHTML->getText($pasJobData['position_desc'], array('extra_open_content' => $sLink, 'open_content_nl2br' => 1), 305);
      $sHTML.= $oHTML->getBlocEnd();

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

  private function _getSqlJobSearchSlistem()
  {
    $oDb = CDependency::getComponentByName('database');

    $asResult = array();
    $asWhereSql = array();

    $sKeyWord = strtolower(getValue('keyword'));
    $bGlobalSearch = (bool)(getValue('global_search', 0));

    //----------------------------------------------------
    //Control fields and build the sql from it
    // TODO: Need field controls and escape striing !!!

    $sKeyWord = str_replace(array('\\', '\'', '"', '=', ''), '', $sKeyWord);

    if(!empty($sKeyWord) && strlen($sKeyWord) >= 2)
    {
      $asKeywordSql = array();
      $allKeyword = $sKeyWord;

      $sOneKeyword = $oDb->dbEscapeString($sKeyWord);
      $asResult['select'][] = ' if( ( (lower(cp.name) LIKE '.$sOneKeyword.' OR lower(slpd.description) LIKE '.$sOneKeyword.' OR lower(slpd.requirements) LIKE '.$sOneKeyword.' OR lower(slpd.title) LIKE '.$sOneKeyword.')), 1, 0) as exactExpression ';

      //$asKeywordSql[] = " slpd.title = '$allKeyword'";

      $asResult['order'][] = ' exactExpression DESC ';

      $sOneKeyword = $oDb->dbEscapeString('%'.$sKeyWord.'%');
      $asResult['select'][] = ' if( ( (lower(cp.name) LIKE '.$sOneKeyword.' OR lower(slpd.description) LIKE '.$sOneKeyword.' OR lower(slpd.requirements) LIKE '.$sOneKeyword.' OR lower(slpd.title) LIKE '.$sOneKeyword.')), 1, 0) as fullExpression ';
      $asResult['order'][] = ' fullExpression DESC ';

      $asKeywords = explode(' ', $sKeyWord);
      foreach($asKeywords as $sOneKeyword)
      {
        if(!empty($sOneKeyword) && strlen($sOneKeyword) > 2)
        {
          $sOneKeyword = $oDb->dbEscapeString('%'.$sOneKeyword.'%');
          $asKeywordSql[] = '( (lower(cp.name) LIKE '.$sOneKeyword.' OR lower(slpd.description) LIKE '.$sOneKeyword.' OR lower(slpd.requirements) LIKE '.$sOneKeyword.' OR lower(slpd.title) LIKE '.$sOneKeyword.') ) ';
        }
      }

      $asWhereSql[] = implode(' OR ', $asKeywordSql);
    }

    //if not a global search (compact form) control the fields from the full form
    if(!$bGlobalSearch)
    {
      $nEnglish = languageJobBoardToSlistem((int)getValue('english')); // slistem da 10 luk sistem burada 5 onedenle *2
      $nJapanese = languageJobBoardToSlistem((int)getValue('japanese')); // slistem da 10 luk sistem burada 5 onedenle *2
      $sOccupation = strtolower(getValue('occupation'));
      $sCompany = strtolower(getValue('company'));
      $sIndustry = getValue('industry');
      //$sLocation = strtolower(getValue('location'));
ChromePhp::log((int)getValue('english'));
ChromePhp::log($nEnglish);
      //field possibly desactivated (value = -1)
      $nCareer = (int)(getValue('career', -1));

      $asCareer = array(1 => 'Graduate', 2 => 'Mid-Level', 3 => 'Executive', 4 => 'Senior');
      if(isset($asCareer[$nCareer]))
        $sCareer = $asCareer[$nCareer];
      else
        $sCareer = '';

      $asSalary = array();
      $sSalary = getValue('salary_year');
      $asSalary = explode('|', $sSalary);
      if(count($asSalary) != 2)
      {
        $nSalaryHigh = 9999999999;
        $nSalaryLow = 0;
      }
      else
      {
        //convert yearly salary to monthly (500 000 because there are 2 steps/mill in the slider)
        $nSalaryHigh = floor(((int)@$asSalary[1] * 500000)/12);
        $nSalaryLow = floor(((int)@$asSalary[0] * 500000)/12);
      }


      if(!empty($sOccupation))
      {
        $sOccupation = $oDb->dbEscapeString('%'.$sOccupation.'%');
        $asWhereSql[] = ' lower(slpd.title) LIKE '.$sOccupation.' ';
      }

      if(isset($sIndustry) && !empty($sIndustry))
      {
        $asWhereSql[] = ' slp.industryfk = '.$sIndustry.' ';
      }

      if(!empty($sCompany))
      {
        $sCompany = $oDb->dbEscapeString('%'.$sCompany.'%');
        $asWhereSql[] = ' lower(cp.name) LIKE '.$sCompany.' ';
      }

      if(!empty($sCareer))
      {
        $sCareer = $oDb->dbEscapeString('%'.$sCareer.'%');
        $asWhereSql[] = ' lower(slpd.career_level) LIKE '.$sCareer.' ';
      }

      if(($nEnglish >= 0 && $nJapanese >= 0))
      {
        $asWhereSql[] = ' (slp.lvl_english  <= "'.$nEnglish.'" OR  slp.lvl_japanese <= "'.$nJapanese.'") ';
      }
      else
      {
        if($nEnglish >= 0)
        {
          $asWhereSql[] = " slp.lvl_english  <= $nEnglish ";
        }

        if($nJapanese >= 0)
          $asWhereSql[] = " slp.lvl_japanese <= $nJapanese ";
      }

      //the salary field minimum value is 41666Y, .
      if(!empty($nSalaryLow) || !empty($nSalaryHigh))
      {
        //> 12mil/year ==> we take all
        if($nSalaryHigh >= 1000000)
          $nSalaryHigh = 9999999999;

        if($nSalaryLow < 42000)
          $asWhereSql[] = ' (slp.salary_to = 0 OR slp.salary_to <= "'.$nSalaryHigh.'")';
        else
          $asWhereSql[] = ' ((slp.salary_from = 0 OR slp.salary_from >=  "'.$nSalaryLow.'") AND (slp.salary_to = 0 OR slp.salary_to <= "'.$nSalaryHigh.'"))';
      }

      if($nJapanese == 0)
        $asWhereSql[] = ' slpd.language <> "jp" ';

      if($nEnglish == 0)
        $asWhereSql[] = ' slpd.language <> "en" ';
    }

    //system condition, always required
    //$asWhereSql[] = ' pos.parentfk != 0 AND pos.visibility <> 0 ';
    //$asWhereSql[] = ' pos.visibility <> 0 ';

    $asResult['where'] = implode(' AND ', $asWhereSql);
    return $asResult;
  }

  private function _getSqlJobSearch()
  {
    $oDb = CDependency::getComponentByName('database');

    $asResult = array();
    $asWhereSql = array();

    $sKeyWord = strtolower(getValue('keyword'));
    $bGlobalSearch = (bool)(getValue('global_search', 0));

    //----------------------------------------------------
    //Control fields and build the sql from it
    // TODO: Need field controls and escape striing !!!

    $sKeyWord = str_replace(array('\\', '\'', '"', '=', ''), '', $sKeyWord);

    if(!empty($sKeyWord) && strlen($sKeyWord) >= 2)
    {
      $asKeywordSql = array();


      $sOneKeyword = $oDb->dbEscapeString($sKeyWord);
      $asResult['select'][] = ' if( ( (lower(cp.company_name) LIKE '.$sOneKeyword.' OR lower(pos.position_desc) LIKE '.$sOneKeyword.' OR lower(pos.requirements) LIKE '.$sOneKeyword.' OR lower(pos.position_title) LIKE '.$sOneKeyword.')), 1, 0) as exactExpression ';
      $asResult['order'][] = ' exactExpression DESC ';

      $sOneKeyword = $oDb->dbEscapeString('%'.$sKeyWord.'%');
      $asResult['select'][] = ' if( ( (lower(cp.company_name) LIKE '.$sOneKeyword.' OR lower(pos.position_desc) LIKE '.$sOneKeyword.' OR lower(pos.requirements) LIKE '.$sOneKeyword.' OR lower(pos.position_title) LIKE '.$sOneKeyword.')), 1, 0) as fullExpression ';
      $asResult['order'][] = ' fullExpression DESC ';

      $asKeywords = explode(' ', $sKeyWord);
      foreach($asKeywords as $sOneKeyword)
      {
        if(!empty($sOneKeyword) && strlen($sOneKeyword) > 2)
        {
          $sOneKeyword = $oDb->dbEscapeString('%'.$sOneKeyword.'%');
          $asKeywordSql[] = '( (lower(cp.company_name) LIKE '.$sOneKeyword.' OR lower(pos.position_desc) LIKE '.$sOneKeyword.' OR lower(pos.requirements) LIKE '.$sOneKeyword.' OR lower(pos.position_title) LIKE '.$sOneKeyword.') ) ';
        }
      }

      $asWhereSql[] = implode(' OR ', $asKeywordSql);
    }

    //if not a global search (compact form) control the fields from the full form
    if(!$bGlobalSearch)
    {
      $nEnglish = (int)getValue('english', -1);
      $nJapanese = (int)getValue('japanese', -1);
      $sOccupation = strtolower(getValue('occupation'));
      $sCompany = strtolower(getValue('company'));
      $sIndustry = getValue('industry_tree');
      //$sLocation = strtolower(getValue('location'));

      //field possibly desactivated (value = -1)
      $nCareer = (int)(getValue('career', -1));

      $asCareer = array(1 => 'Graduate', 2 => 'Mid-Level', 3 => 'Executive', 4 => 'Senior');
      if(isset($asCareer[$nCareer]))
        $sCareer = $asCareer[$nCareer];
      else
        $sCareer = '';

      $asSalary = array();
      $sSalary = getValue('salary_year');
      $asSalary = explode('|', $sSalary);
      if(count($asSalary) != 2)
      {
        $nSalaryHigh = 9999999999;
        $nSalaryLow = 0;
      }
      else
      {
        //convert yearly salary to monthly (500 000 because there are 2 steps/mill in the slider)
        $nSalaryHigh = floor(((int)@$asSalary[1] * 500000)/12);
        $nSalaryLow = floor(((int)@$asSalary[0] * 500000)/12);
      }


      /*if(!empty($sOccupation))
      {
        $sOccupation = $oDb->dbEscapeString('%'.$sOccupation.'%');
        $asWhereSql[] = ' lower(pos.position_title) LIKE '.$sOccupation.' ';
      }*/

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

        $sIndustry = implode(',', $asIndustry);

        $asWhereSql[] = ' (ind.industrypk IN ('.$sIndustry.') OR ind.parentfk IN ('.$sIndustry.')) ';
      }

      /*if(!empty($sLocation))
      {
        $sLocation = $oDb->dbEscapeString('%'.$sLocation.'%');
        $asWhereSql[] = ' lower(pos.location) LIKE '.$sLocation.'';
      }*/

      if(!empty($sCareer))
      {
        $sCareer = $oDb->dbEscapeString('%'.$sCareer.'%');
        $asWhereSql[] = ' lower(pos.career_level) LIKE '.$sCareer.' ';
      }

      if(($nEnglish >= 0 && $nJapanese >= 0))
      {
        $asWhereSql[] = ' (pos.english  <= "'.$nEnglish.'" OR  pos.japanese <= "'.$nJapanese.'") ';
      }
      else
      {
        if($nEnglish >= 0)
          $asWhereSql[] = ' pos.english  <= "'.$nEnglish.'"';

        if($nJapanese >= 0)
          $asWhereSql[] = ' pos.japanese <= "'.$nJapanese.'"';
      }

      //the salary field minimum value is 41666Y, .
      if(!empty($nSalaryLow) || !empty($nSalaryHigh))
      {
        //> 12mil/year ==> we take all
        if($nSalaryHigh >= 1000000)
          $nSalaryHigh = 9999999999;

        if($nSalaryLow < 42000)
          $asWhereSql[] = ' (pos.salary_high = 0 OR pos.salary_high <= "'.$nSalaryHigh.'")';
        else
          $asWhereSql[] = ' ((pos.salary_low = 0 OR pos.salary_low >=  "'.$nSalaryLow.'") AND (pos.salary_high = 0 OR pos.salary_high <= "'.$nSalaryHigh.'"))';
      }

      if($nJapanese == 0)
        $asWhereSql[] = ' pos.lang <> "jp" ';

      if($nEnglish == 0)
        $asWhereSql[] = ' pos.lang <> "en" ';
    }

    //system condition, always required
    //$asWhereSql[] = ' pos.parentfk != 0 AND pos.visibility <> 0 ';
    $asWhereSql[] = ' pos.visibility <> 0 ';

    $asResult['where'] = implode(' AND ', $asWhereSql);
    return $asResult;
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
    $sLang = $oPage->getLanguage();

    $sJavascript = '';
    $sJavascript.= 'function manageFieldContextBox(poElement, pbStatus)';
    $sJavascript.= '{';

    $sJavascript.= '  if(!jQuery(poElement).hasClass("formFieldContainer")) ';
    $sJavascript.= '    poElement = jQuery(poElement).closest(".formFieldContainer"); ';

    $sJavascript.= '  if(pbStatus && !jQuery(".formContextBox", poElement).length && !jQuery(poElement).hasClass("fieldInactive")) ';
    $sJavascript.= '  {';
    $sJavascript.= '    sHiddenFieldId = jQuery(poElement).attr("fieldid"); ';
    $sJavascript.= '    jQuery(poElement).append(\'<div class="formContextBox hidden"><div onclick="var oContainer = jQuery(this).closest(\\\'.formFieldContainer\\\'); jQuery(\\\'#\'+sHiddenFieldId+\'\\\').val(-1); jQuery(oContainer).addClass(\\\'fieldInactive\\\');  manageFieldContextBox(oContainer, false); ">Ignore criteria</div></div>\'); ';
    $sJavascript.= '    jQuery(".formContextBox").fadeIn(); ';

    $sJavascript.= '    jQuery(".activable .formField").children().unbind("click"); ';
    $sJavascript.= '    jQuery(".activable .formField *").children().click(function(){ activateField(this); }); ';
    $sJavascript.= '    return true; ';
    $sJavascript.= '  }';

    $sJavascript.= '  if(!pbStatus && jQuery(".formContextBox", poElement).length) ';
    $sJavascript.= '  {';
    $sJavascript.= '    jQuery(".formContextBox").clearQueue(); ';
    $sJavascript.= '    jQuery(".formContextBox").delay(250).fadeOut(function(){ jQuery(this).remove(); }); ';
    $sJavascript.= '  }';

    $sJavascript.= '  return true; ';
    $sJavascript.= '}';

    $sJavascript.= 'function activateField(poElement)';
    $sJavascript.= '{';
    $sJavascript.= '  var oContainer = jQuery(poElement).closest(".formFieldContainer"); ';
    $sJavascript.= '  if(jQuery(oContainer).hasClass("fieldInactive")) ';
    $sJavascript.= '  {';
    $sJavascript.= '    jQuery(".formField div", oContainer).css("opacity", "1");
                        jQuery(oContainer).removeClass("fieldInactive"); ';
    $sJavascript.= '    jQuery(".formContextBox:visible", oContainer).fadeOut(); ';
    $sJavascript.= '    return true; ';
    $sJavascript.= '  } ';
    $sJavascript.= '} ';

    $sJavascript.= 'jQuery(document).ready(function()
      {
        jQuery(".activable").mouseenter(function(){ manageFieldContextBox(this, true); });
        jQuery(".activable").mouseleave(function(){ manageFieldContextBox(this, false); });
        setTimeout(\'jQuery(".activable .formField *").bind("focus", function(){ activateField(this); }); \', 1500);

      });';

    $oPage->addCustomJs($sJavascript);


    $oForm = $oHTML->initForm('advSearchForm');

    if($pbIsAjaxForm)
    {
      $sURL = $oPage->getAjaxUrl($this->_getUid(), CONST_ACTION_LIST, CONST_TA_TYPE_JOB);
      //echo phpversion();
      //ChromePhp::log($sURL);
      $oForm->setFormParams('', true, array('action' => $sURL, 'submitLabel' => $this->casText['TALENT_SEARCH'],'ajaxTarget' => 'jobListContainer', 'ajaxCallback' => "/*searchFormToggle(false);*/", 'onBeforeSubmit' => "jQuery(body).animate({scrollTop: '0px'}, 600, 'linear'); "));
    }
    else
    {
      $sURL = $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_TA_TYPE_JOB, 0);
      $oForm->setFormParams('', false, array('action' => $sURL, 'submitLabel' => $this->casText['TALENT_SEARCH']));
    }

    $oForm->setFormDisplayParams(array('columns' => 2, 'noCancelButton' => '1'));

    $oForm->addField('input', 'keyword', array('label' => $this->casText['TALENT_KEYWORDS'], 'value' => getValue('keyword')));



// OCCUPATION
    $oForm->addField('input', 'occupation', array('label' => $this->casText['TALENT_OCCUPATION'], 'value' => getValue('occupation')));
    /*$oForm->addField('select', 'occupation', array('class' => 'public_important_field', 'label' => 'Occupation'));
    $locations = $this->getOccupationList();

    $oForm->addOption('occupation', array('value'=>'', 'label' => 'Select Occupation','selected'=>'selected'));
    foreach($locations as $nValue => $vType)
    {
      $oForm->addOption('occupation', array('value'=>$nValue, 'label' => $vType));
    }

    $oForm->addOption('occupation', $this->getOccupationList());*/
// OCCUPATION

    //$oForm->addField('input', 'location', array('label' => $this->casText['TALENT_LOCATION'], 'value' => getValue('location')));
    $oForm->addField('select', 'location', array('class' => 'public_important_field', 'label' => 'Location'));
    $locations = $this->getLocationOption();

    $oForm->addOption('location', array('value'=>'', 'label' => 'Select Location','selected'=>'selected'));
    foreach($locations as $nValue => $vType)
    {
      $oForm->addOption('location', array('value'=>$nValue, 'label' => $vType));
    }

    //$oForm->addOption('location', $this->getLocationOption());

    //industry tree
    $oForm->addField('select', 'industry', array('class' => 'public_important_field', 'label' => 'Industry'));
    $industries = $this->getIndustryList();

    $oForm->addOption('industry', array('value'=>'', 'label' => 'Select Industry','selected'=>'selected'));
    foreach($industries as $nValue => $vType)
    {
      $oForm->addOption('industry', array('value'=>$nValue, 'label' => $vType));
    }

    //$oForm->addOption('industry', $this->getIndustryList());
    /*$asIndustries = $this->getIndustries(0, true, true);
    $nIndustry = getValue('industry_tree');

    $asIndustrySelected = explode(',',$nIndustry);
    $nIndustryPk = getValue('industrypk');

    $oForm->addField('tree', 'industry_tree', array('label' => $this->casText['TALENT_INDUSTRY'], 'defaultLabel' => $this->casText['TALENT_SELECT_INDUSTRY'], 'selectedLabel' => $this->casText['TALENT_SELECTED_INDUSTRY']));

    if(isset($nIndustryPk) && !empty($nIndustryPk))
      array_push($asIndustrySelected, $nIndustryPk);

    foreach($asIndustries as $sKey =>$asData)
    {
      if(in_array($asData['industrypk'], $asIndustrySelected))
        $oForm->addOption('industry_tree', array('title' => ''.$this->casText[$asData['name']].'','id' => $asData['industrypk'], 'parent' => $asData['parentfk'],'checked'=>'checked'));
      else
        $oForm->addOption('industry_tree', array('title' => ''.$this->casText[$asData['name']].'','id' => $asData['industrypk'], 'parent' => $asData['parentfk']));
    }*/


    //languages slider legends
    $oForm->addField('select', 'english', array('class' => 'public_important_field', 'label' => 'English Ability'));
    $languagesEng = $this->getLanguageList();

    //$oForm->addOption('english', array('value'=>'', 'label' => 'Select Level','selected'=>'selected'));
    foreach($languagesEng as $nValue => $vType)
    {
      if($nValue == 5)
      {
        $oForm->addOption('english', array('value'=>$nValue, 'label' => $vType,'selected'=>'selected'));
      }
      else
      {
        $oForm->addOption('english', array('value'=>$nValue, 'label' => $vType));
      }
    }

    //$oForm->addOption('english', $this->getIndustryList());


    $oForm->addField('select', 'japanese', array('class' => 'public_important_field', 'label' => 'Japanese Ability'));
    $languagesJap = $this->getLanguageList();

    //$oForm->addOption('english', array('value'=>'', 'label' => 'Select Level','selected'=>'selected'));
    foreach($languagesJap as $nValue => $vType)
    {
      $oForm->addOption('japanese', array('value'=>$nValue, 'label' => $vType));
    }


    /*$asSliderLegend = array(0 => $this->casText['TALENT_LANG_LVL0'], 1 => $this->casText['TALENT_LANG_LVL1'], 2 => $this->casText['TALENT_LANG_LVL2'], 3 => $this->casText['TALENT_LANG_LVL3'], 4 => $this->casText['TALENT_LANG_LVL4']);

    //english level
    if($sLang == 'en')
      $nFieldDefaultValue = 3;
    else
      $nFieldDefaultValue = -1;

    $nFieldValue = (int)getValue('english', $nFieldDefaultValue);
    $oForm->addField('slider', 'english', array('label' => $this->casText['TALENT_ENGLISH_ABILITY'], 'value' => $nFieldValue, 'min' => 0, 'max' => 4, 'range' => 'min', 'legend' => $asSliderLegend));

    if($nFieldValue > -1)
      $oForm->setFieldDisplayParams('english', array('fieldid' => 'englishId', 'class' => 'clickable activable'));
    else
      $oForm->setFieldDisplayParams('english', array('fieldid' => 'englishId', 'class' => 'clickable activable fieldInactive'));

    if($sLang == 'en')
      $nFieldDefaultValue = -1;
    else
      $nFieldDefaultValue = 3;

    $nFieldValue = (int)getValue('japanese', $nFieldDefaultValue);
    $oForm->addField('slider', 'japanese', array('label' => $this->casText['TALENT_JAP_ABILITY'], 'value' => $nFieldValue, 'min' => 0, 'max' => 4, 'range' => 'min', 'legend' => $asSliderLegend));

    if($nFieldValue > -1)
      $oForm->setFieldDisplayParams('japanese', array('fieldid' => 'japaneseId', 'class' => 'clickable activable'));
    else
      $oForm->setFieldDisplayParams('japanese', array('fieldid' => 'japaneseId', 'class' => 'clickable activable fieldInactive'));
*/
    /*$asSliderLegend = array(1 => 'Graduate', 2 => 'Mid-Level', 3 => 'Executive', 4 => 'Senior');
    $oForm->addField('slider', 'career', array('label' => $this->casText['TALENT_CAREER'], 'value' => (int)getValue('career', 2), 'min' => 1, 'max' => 4, 'range' => 'min', 'legend' => $asSliderLegend));
    $oForm->setFieldDisplayParams('career', array('fieldid' => 'careerId', 'class' => 'clickable activable'));
    */

    $asSliderLegend = array(1 => '1m', 2 => '4m', 3 => '7m', 4 => '10m', 6 => '+12m');

    $asSalary = explode('|', getValue('salary_year'));
    if(count($asSalary) != 2)
      $asSalary = array(1, 27);

    $oForm->addField('slider','salary_year' , array('label' => $this->casText['TALENT_SALARY_RANGE'], 'keepNextInline' => '1', 'min' => 1, 'max' => 28, 'step' => 1, 'value_min' => $asSalary[0], 'value_max' => $asSalary[1], 'legend' => $asSliderLegend));

    $oForm->addField('hidden', 'do_search', array('value' => 1));
    $oForm->addField('hidden', 'sortfield');

    $sHTML = $oForm->getDisplay();
    return $sHTML;
  }

  /**
  * Function to display the job search form
  * @param boolean $pbIsAjaxForm
  * @return string
  */
  private function _getBasicJobSearchForm($psSearchId)
  {
    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');

    $sURL = $oPage->getAjaxUrl($this->_getUid(), CONST_ACTION_LIST, CONST_TA_TYPE_JOB);
    $oForm = $oHTML->initForm('basicSearchForm');

    $oForm->setFormParams('basicSearchForm', true, array('action' => $sURL, 'submitLabel' => $this->casText['TALENT_SEARCH'], 'ajaxTarget' => 'jobListContainer', 'ajaxCallback' => "/*searchFormToggle(false);*/", 'onBeforeSubmit' => "jQuery(body).animate({scrollTop: '0px'}, 600, 'linear'); "));
    $oForm->setFormDisplayParams(array('columns' => 2, 'noButton' => '1'));

    $oForm->addField('input', 'keyword', array('label' => $this->casText['TALENT_FIND_SEARCH'], 'value' => getValue('keyword')));
    $oForm->setFieldDisplayParams('keyword',  array('class' => ' basicSearchcustomWidth '));

    $oForm->addField('misc', 'searchBtn', array('type' => 'text', 'text' => '<input type="button" value="'.$this->casText['TALENT_SEARCH'].'" onclick="jQuery(this).closest(\'form\').submit();"/>'));
    $oForm->setFieldDisplayParams('searchBtn',  array('class' => ' basicSearchcustomWidth2 '));

    $oForm->addField('hidden', 'do_search', array('value' => 1));
    $oForm->addField('hidden', 'global_search', array('value' => 1));
    $oForm->addField('hidden', 'sortfield');

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
    $oPage = CDependency::getComponentByName('page');
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


      //Middle Section
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobCentreSection'));
        $sHTML.= $oHTML->getBlocStart('mainJobContainer', array('class' => 'redBorderTop'));

        //search form
        $sHTML.= $this->_getJobSearchSection($sSearchId, true);

        //list of jobs
        $avResult = $this->_getJobSearchResult($sSearchId);
        //ChromePhp::log($avResult);
        $sHTML.= $this->_getJobResultList($avResult, $sSearchId, $bSearchFormOpen);


        $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();


      //right section with slider (if not mobile devices)
      if($oPage->getDeviceType() != CONST_PAGE_DEVICE_TYPE_PHONE)
        $sHTML.= $this->_getJobListSideSection($sSearchId, $bSearchFormOpen);

    $sHTML.= $oHTML->getFloatHack();
    $sHTML.= $oHTML->getBlocEnd();

   return $sHTML;
  }

  /**
   * Function to display the job list floating left section
   * @param string $psSearchId
   * @param boolean $pbSearchFormOpen
   * @return string
   */

  private function _getJobListSideSection($psSearchId, $pbSearchFormOpen)
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sJavascript = "jQuery(document).ready(function(){ ";
    $sJavascript.= "  var nOriginalPosition = 0; ";

    $sJavascript.= "  jQuery(document).scroll(function(){ ";
    $sJavascript.= "  var oContainer = jQuery('.jobSideSectionInner'); ";
    $sJavascript.= "  var bFloating = oContainer.hasClass('menuFloating'); ";
    $sJavascript.= "  var oPosition = oContainer.offset(); ";
    $sJavascript.= "  var nPosTop = oPosition.top; ";
    $sJavascript.= "  if(jQuery.browser.msie) ";
    $sJavascript.= "    var nScroll = jQuery('html').scrollTop(); ";
    $sJavascript.= "  else ";
    $sJavascript.= "    var nScroll = jQuery('body').scrollTop(); ";

    $sJavascript.= "  if(!bFloating) ";
    $sJavascript.= "  { ";
    $sJavascript.= "    if(nScroll > (nPosTop + 15) ) ";
    $sJavascript.= "    {";
    $sJavascript.= "      oContainer.attr('style', 'position: fixed; top:10px;'); ";
    $sJavascript.= "      oContainer.addClass('menuFloating'); ";
    $sJavascript.= "      nOriginalPosition =  nPosTop;";
    $sJavascript.= "      $('#backToTop').fadeIn(); ";
    $sJavascript.= "    } ";
    $sJavascript.= "   } ";
    $sJavascript.= "   else ";
    $sJavascript.= "   { ";
    $sJavascript.= "    if(nScroll < nOriginalPosition ) ";
    $sJavascript.= "    { ";
    $sJavascript.= "      oContainer.removeClass('menuFloating'); ";
    $sJavascript.= "      oContainer.attr('style', 'position: relative;'); ";
    $sJavascript.= "      $('#backToTop').hide(0); ";
    $sJavascript.= "     } ";
    $sJavascript.= "   } ";
    $sJavascript.= "  }); ";
    $sJavascript.= "}); ";

    $oPage->addCustomJs($sJavascript);

    $sHTML = $oHTML->getBlocStart('',array('class'=>'jobSideSection floatRight'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobSideSectionInner'));

    $sHTML.= $this->_getJobSlider($psSearchId);
    $sHTML.= $oHTML->getFloatHack();

    $sHTML.= $this->_getLanguageSection();

    $sHTML.= $this->_getSocialMediaSection();
    $sHTML.= $oHTML->getFloatHack();

    //go on top link
    $sHTML.= $oHTML->getBlocStart('backToTop', array('onclick' => '$(\'html,body\').animate({scrollTop: 0}, 450); '));

      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/back_to_top.png');

      $sHTML.= $oHTML->getBlocStart();
      $sHTML.= $oHTML->getLink($this->casText['TALENT_BACK_TO_TOP'], 'javascript:;');
      $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getFloatHack();
    $sHTML.= $oHTML->getBlocEnd();



    /*$sHTML.= $this->_getJobSearchSection($psSearchId, $pbSearchFormOpen);
    $sHTML.= $this->_getJobSearchFilterSection($pbSearchFormOpen);*/

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
  * Function to display the left section of job detail page ,
  * displaying the sharing options of job
  * @return string HTML
  */
  private function _getSideSectionJobList($pnPk)
  {
    if(!assert('is_integer($pnPk)'))
      return '';

    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');

    $asData = array();

    $sQuery = 'SELECT * FROM position WHERE positionpk = '.$pnPk.'';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if($bRead)
      $asData = $oDbResult->getData();



    $sHTML = $oHTML->getBlocStart('',array('class'=>'jobSideSection floatLeft jobSharingSection'));


    if(!empty($asData))
    {
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'simpleRedBorderTop', 'style' => 'margin-right: 10px;'));

        $sHTML.= $oHTML->getBlocStart();
        $sHTML.= $oHTML->getText($this->casText['TALENT_SHARE_JOB'],array('class'=>'sideBarTitle'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'sharingContainer marginTop10 ', 'style' => 'margin-bottom: 30px;'));
        $sHTML.= $oHTML->getBlocStart('');

          $sTitle = $asData['position_title'];
          $sPageURL = curPageURL();

          $sHTML.= "<script>function fbs_click() {u=location.href;t=document.title;window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');return false;}</script>";

          $sFbPageUrl = 'http://www.facebook.com/share.php?u='.$sPageURL.'&t='.$sTitle.'';
          $sLinkedInUrl = 'http://www.linkedin.com/shareArticle?mini=true&url='.$sPageURL.'&title='.$sTitle.'';

          $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/facebook.png','Post on facebook');
          $sHTML.= $oHTML->getSpace(2);
          $sHTML.= $oHTML->getLink($this->casText['TALENT_POST_FB'], $sFbPageUrl, array('rel'=>"nofollow" ,'onclick'=> "return fbs_click()",'target'=>'_blank'));
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

        $sHTML.= $oHTML->getBlocEnd();
      }
      else
        $sHTML.= $oHTML->getBlocStart();

        $sHTML.= $oHTML->getBlocStart('',array('class' => 'simpleRedBorderTop'));
          $sURL = "'".$oPage->getURL('jobboard', CONST_ACTION_LIST, CONST_TA_TYPE_JOB, 0)."'";
          $sHTML.= $oHTML->getLink($this->casText['TALENT_RETURN_RESULT'],'',array('onclick'=>'document.location.href='.$sURL, 'class' => 'sideBarTitle'));
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

          $sJavascript = 'jQuery(this).closest(\'.jobSideSection\').find(\''.$asFormField[$sFieldname]['type'].'[name='.$sFieldname.']\').val(\''.$asFormField[$sFieldname]['resetValue'].'\'); ';
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
        $sFilter.= $oHTML->getPicture($this->getResourcePath().'/pictures/filter_loading.gif');
        $sFilter.= $oHTML->getSpanEnd();

        $sFilter.= $oHTML->getText($this->casText['TALENT_MAKE_SEARCH'],array('class'=>'smallTitle','onclick'=>'searchFormToggle();'));

      $sFilter.= $oHTML->getBlocEnd();

      $sClearSearch = '';
    }
    else
    {
      $sFilter.= $oHTML->getBlocStart();

        $sFilter.= $oHTML->getSpanStart('', array('class' => 'filterRemovalLoader hidden', 'style' => ''));
        $sFilter.= $oHTML->getPicture($this->getResourcePath().'/pictures/filter_loading.gif');
        $sFilter.= $oHTML->getSpanEnd();

        $sFilter.= $oHTML->getText($this->casText['TALENT_EDIT_PARAM'],array('class'=>'smallTitle','onclick'=>'searchFormToggle();'));

      $sFilter.= $oHTML->getBlocEnd();

      $sClearSearch = $oHTML->getText($this->casText['TALENT_CLEAR'],array('class'=>'smallTitle','onclick'=>'resetJobSearch();'));
    }

    $sHTML = $oHTML->getBlocStart('leftsectionSearch');

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'newSearchDiv'));
    $sHTML.= $oHTML->getText($this->casText['TALENT_NEW_SEARCH']);
    $sHTML.= $sClearSearch;
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'searchClass'));
    $sHTML.= $sFilter;
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
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

    $sClass = ' jobSearchVertical ';

    if((bool)getValue('global_search'))
    {
      $sFullSearchClass = ' hidden ';
      $sGlobalSearchClass = '';
    }
    else
    {
      $sFullSearchClass = '';
      $sGlobalSearchClass = ' hidden ';
    }

    $sHTML = $oHTML->getBlocStart('',array('class'=>'jobSearchContainer '.$sClass));
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobSearchContainerInner'.$sFullSearchClass));

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'newSearchDiv '));

          $sHTML.= $oHTML->getBlocStart('', array('class' => 'sideBarTitle floatLeft', 'style' => 'margin-top: 5px; width: 170px;'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_REFINE_SEARCH']);
          $sHTML.= $oHTML->getBlocEnd();

          $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatRight', 'style' => 'margin-top: 5px; width: 150px;'));
          $sPicture = $oHTML->getPicture($this->getResourcePath().'pictures/close_search_form.png', $this->casText['TALENT_CLOSE_FORM'], '', array('style' => 'position: absolute; right: 5px; top: -1px;'));
          $sHTML.= $oHTML->getLink($this->casText['TALENT_CLOSE_SEARCH'].' '.$sPicture, 'javascript:;', array('onclick'=> '$(this).closest(\'.jobSearchContainerInner\').fadeOut(\'fast\', function(){ $(this).closest(\'.jobSearchContainer\').find(\'.jobSearchContainerFolded\').fadeIn(); });', 'style' => 'line-height: 20px; position: relative; padding-right: 25px; color: #555;'));
          $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();


        // full Search Form
        $sHTML.= $oHTML->getBlocStart('advanced_search_form', array('class'=>'searchFormContainer'));
        $sHTML.= $this->_getJobSearchForm(true);
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getFloatHack();

      $sHTML.= $oHTML->getBlocEnd();


      //compact form displayed here
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobSearchContainerFolded '.$sGlobalSearchClass));

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'newSearchDiv '));

          $sHTML.= $oHTML->getBlocStart('', array('class' => 'sideBarTitle floatLeft'));
          $sHTML.= $this->_getBasicJobSearchForm($psSearchId);
          $sHTML.= $oHTML->getBlocEnd();

          $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatRight', 'style' => 'margin-top: 5px;'));
          $sPicture = $oHTML->getPicture($this->getResourcePath().'pictures/open_search_form.png', $this->casText['TALENT_CLOSE_FORM'], '', array('style' => 'position: absolute; right: 5px; top: -1px '));
          $sHTML.= $oHTML->getLink($this->casText['TALENT_OPEN_SEARCH'].' '.$sPicture, 'javascript:;', array('onclick'=> '$(this).closest(\'.jobSearchContainerFolded\').fadeOut(\'fast\', function(){ $(this).closest(\'.jobSearchContainer\').find(\'.jobSearchContainerInner\').fadeIn(); });', 'style' => 'color: #555; line-height: 20px; position: relative; padding-right: 25px; display: block;'));
          $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getFloatHack();

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

    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');

    $sHTML = $oHTML->getBlocStart('',array('class'=>'homepageContainer'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetail'));

      $sHTML.= $this->_getSideSectionJobList($pnPk);

      //$sHTML.= $this->_getJobDetailInformation($pnPk);
      $sHTML.= $this->_getJobDetailInformationSlistemDB($pnPk);

      //right section
      if($oPage->getDeviceType() != CONST_PAGE_DEVICE_TYPE_PHONE)
        $sHTML.= $this->_getJobListSideSection('', false);

      $sHTML.= $oHTML->getFloatHack();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
  * Function to get Job Detail Information
  * @param integer $pnPk
  * @return string HTML
  */

  private function _getJobDetailInformationSlistemDB($pnPk)
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = $oHTML->getBlocStart('',array('class'=>'jobCentreSection simpleRedBorderTop'));

    $positionData = $this->getPositionDetailSlistem($pnPk);

    /*$slistemDB = CDependency::getComponentByName('database');

    $slistemQuery = "SELECT FOUND_ROWS() as count, slp.sl_positionpk as positionpk, slp.sl_positionpk as jobfk,
                 slpd.is_public as visibility, slpd.category as category, slpd.career_level as career_level,
                 slpd.title as position_title, slpd.description as position_desc, slpd.requirements as requirements,
                 cp.sl_companypk as companyfk, slp.status as status, slp.date_created as posted_date, sll.location as location,
                 slpd.job_type as job_type, CONCAT(slp.salary_from,' - ',slp.salary_to) as salary, slp.salary_from as salary_low,
                 slp.salary_to as salary_high,  CONCAT(slp.age_from,' - ',slp.age_to) as age, slp.lvl_japanese as japanese,
                 slp.lvl_english as english, ind.sl_industrypk as industryfk, slpd.holidays as holidays, slpd.work_hours as work_hours,
                 slpd.language as lang, ind.sl_industrypk as temp_industry, slpd.title as page_title,
                 slpd.description as meta_desc, slpd.meta_keywords as meta_keywords, slpd.company_label as company_label,
                 slpd.to_jobboard as to_jobboard, slp.sl_positionpk as external_key, slpd.expiration_date as expiration_date,
                 ind.sl_industrypk as industrypk, ind.label as name, slp.status as status, ind.parentfk as parentfk,
                 cp.name as company_name, slpd.raw_data as raw_data
                 FROM sl_position slp
                 INNER JOIN sl_position_detail slpd on slpd.positionfk = slp.sl_positionpk
                 INNER JOIN sl_industry ind on ind.sl_industrypk = slp.industryfk
                 INNER JOIN sl_location sll on sll.sl_locationpk = slpd.location
                 INNER JOIN sl_company cp on cp.sl_companypk = slp.companyfk
                 WHERE slp.sl_positionpk = ".$pnPk;


    $positionData = $slistemDB->slistemGetAllData($slistemQuery);*/

    if(isset($positionData) && $positionData != null)
    {

      //$positionData = $positionData[0];
      ////var_dump($positionData);
      /*if($positionData['lang'] == 'jp')
      {
        $asSibling = $this->_getSiblingPosition($pnPk, 'jp');
        //$sPic = '/pictures/lang_japanese.png';
        $sText = 'in Japanese';
      }
      else
      {
        $asSibling = $this->_getSiblingPosition($pnPk, 'en');
        //$sPic = '/pictures/lang_english.png';
        $sText = 'in English';
      }

      if(!empty($asSibling))
      {
        $asTranslation = current($asSibling);
        $sUrl = $oPage->getUrl($this->csUid, CONST_ACTION_VIEW, CONST_TA_TYPE_JOB, (int)$asTranslation['positionpk']);

        $sHTML.= $oHTML->getBlocStart('', array('class'=>'switchLanguage'));
        //$sHTML.= $oHTML->getPicture($this->getResourcePath().$sPic, 'View translation', $sUrl);
        $sHTML.= $oHTML->getLink($sText, $sUrl);
        $sHTML.= $oHTML->getBlocEnd();
      }*/

      $sPageTitle    = $positionData['page_title'];
      $sMetaKeywords = $positionData['meta_keywords'];
      $sMetaDescription = $positionData['meta_desc'];

      $sJobData = $positionData['raw_data'];
      $asJobData = (array)@unserialize($sJobData);

      if(empty($asJobData) || !isset($asJobData['jobID']) || empty($asJobData['jobID']))
        $sIdentfier = $pnPk;
      else
        $sIdentfier = 'SL'.(int)$asJobData['jobID'];

      //Set meta keywords and meta description
      if(!empty($sPageTitle))
        $oPage->setPageTitle($sPageTitle);
      if(!empty($sMetaKeywords))
        $oPage->setPageKeywords($sMetaKeywords);
      //if(!empty($sMetaDescription))
        //$oPage->setPageDescription($sMetaDescription);

      $sHTML.= $oHTML->getBlocStart('position');
      $sHTML.= $oHTML->getText($positionData['position_title'], array('class'=>'boldTitle'));
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('',array('style'=>'margin-top:10px;'));

      //Position Title
      $sHTML.= $oHTML->getBlocStart('', array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('', array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_POSITION_ID'], array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sHTML.= $oHTML->getText($sIdentfier);
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      //Company
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_COMPANY'],array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        //if($positionData['company_name'])
          //$sHTML.= $oHTML->getText($positionData['company_name']);
        //else
          $sHTML.= $oHTML->getText('Company Name not visible'); // simdilik hepsini not wisible yaptik

        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      //Location
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_LOCATION'],array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sHTML.= $oHTML->getText($positionData['location']);
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      /*//Posted Date
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_POST_DATE'],array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sHTML.= $oHTML->getText($oDbResult->getFieldValue('posted_date'));
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();*/

      //English Level
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_ENGLISH_ABILITY'],array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sEnglish = languageSlistemtoJobBoard((int)$positionData['english']);
        $sHTML.= $oHTML->getText($sEnglish);
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      //Japanese Level
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_JAP_ABILITY'],array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sJapanese = languageSlistemtoJobBoard((int)$positionData['japanese']);
        $sHTML.= $oHTML->getText($sJapanese);
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      //Industry
      if(isset($positionData['name']))
      {
        $sIndustry = $positionData['name'];
      }
      else
        $sIndustry = ' - ';

      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText('<strong>'.$this->casText['TALENT_INDUSTRY'].'</strong>', array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= $oHTML->getText($sIndustry);
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      //Career Level
      $sCareer = $positionData['career_level'];
      if(!empty($sCareer))
      {
        $sHTML.= $oHTML->getBlocStart('', array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('', array('class'=>'left_section'));
          $sHTML.= $oHTML->getText('<strong>'.$this->casText['TALENT_CAREER'].'</strong>',array('style'=>'font-weight:bold;'));
            $sHTML.= $oHTML->getBlocEnd();
            $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
            $sHTML.= $oHTML->getText($sCareer);
          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();
      }

      /*//Salary
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText('<strong>'.$this->casText['TALENT_SALARY'].'</strong>', array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= $oHTML->getText($oDbResult->getFieldValue('salary'));
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();*/

      //Train Station Display if exists
      $sStation = $positionData['station'];
      if(!empty($sStation))
      {
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_STAION'],array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd();

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= $oHTML->getText($positionData['station']);
          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();
      }

      //Holidays Display if exists
      $sHolidays = $positionData['holidays'];
      if(!empty($sHolidays))
      {
        $sHTML.= $oHTML->getBlocStart('', array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('', array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_HOLIDAYS'], array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd();

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= $oHTML->getText($positionData['holidays']);
          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();
      }

      //Work Hours Display if exists
      $sWorkHours = $oDbResult->$positionData['work_hours'];
      if(!empty($sWorkHours))
      {
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_WORK_HOUR'],array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sHTML.= $oHTML->getText($positionData['work_hours']);
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocEnd();
      }

      //Description
      $sDescription = $positionData['position_desc'];
      if(!empty($sDescription))
      {
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_DESCRIPTION'],array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section jodDetailDescription'));
          $sHTML.= $oHTML->getText(nl2br($sDescription));
          $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();
      }

       //Requirements
      $sRequirements = $positionData['requirements'];
      if(!empty($sRequirements))
      {
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
            $sHTML.= $oHTML->getText($this->casText['TALENT_REQUIREMENTS'],array('style'=>'font-weight:bold;'));
            $sHTML.= $oHTML->getBlocEnd();
           $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
            $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section jodDetailDescription'));
            $sHTML.= $oHTML->getText(nl2br($positionData['requirements']));
            $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();
      }


      //Apply Button
      $sURL =  "'".$oPage->getUrl($this->_getUid(), CONST_ACTION_APPLY, CONST_TA_TYPE_JOB, $pnPk)."'";
        $sHTML.= $oHTML->getBlocStart('',array('style'=>'margin-top:15px;'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText('&nbsp;');
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sHTML.= "<input type='button' name='apply' id='apply' value=".$this->casText['TALENT_APPLY_NOW']." onclick = \"document.location.href = ".$sURL."\">";
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
        $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
    }
    else
    {
      $sHTML.= $oHTML->getBlocMessage('Position may have been deleted or expired');
    }

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;

  }

  private function _getJobDetailInformation($pnPk)
  {
    if(!assert('is_integer($pnPk) && !empty($pnPk)'))
      return '';

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');

    $sHTML = $oHTML->getBlocStart('',array('class'=>'jobCentreSection simpleRedBorderTop'));

    //$sQuery = 'SELECT pos.*, pos.*, ind.*,  IF(pos.company_label IS NOT NULL && pos.company_label <> "", pos.company_label, cp.company_name) as company_name, ind.name AS industry_name, ind_parent.name as parent_industry ';
    $sQuery = 'SELECT pos.*, ind.*, pos.company_label as company_name, ind.name AS industry_name, ind_parent.name as parent_industry, job.data as raw_data ';
    $sQuery.= ' FROM position AS pos  ';
    $sQuery.= ' LEFT JOIN company AS cp ON (cp.companypk = pos.companyfk) ';
    $sQuery.= ' LEFT JOIN industry AS ind ON (pos.industryfk = ind.industrypk) ';
    $sQuery.= ' LEFT JOIN industry AS ind_parent ON (ind_parent.industrypk = ind.parentfk) ';

    $sQuery.= ' LEFT JOIN position AS parent_pos ON (parent_pos.positionpk = pos.parentfk) ';
    $sQuery.= ' LEFT JOIN job ON (job.jobpk = parent_pos.jobfk) ';

    $sQuery.= ' WHERE pos.visibility <> 0 and pos.positionpk = '.$pnPk.'';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();



////var_dump($positionData);
    //ChromePhp::log($positionData);

    if($bRead)
    {
      if($oDbResult->getFieldValue('lang') == 'en')
      {
        $asSibling = $this->_getSiblingPosition($pnPk, 'jp');
        //$sPic = '/pictures/lang_japanese.png';
        $sText = 'in Japanese';
      }
      else
      {
        $asSibling = $this->_getSiblingPosition($pnPk, 'en');
        //$sPic = '/pictures/lang_english.png';
        $sText = 'in English';
      }

      if(!empty($asSibling))
      {
        $asTranslation = current($asSibling);
        $sUrl = $oPage->getUrl($this->csUid, CONST_ACTION_VIEW, CONST_TA_TYPE_JOB, (int)$asTranslation['positionpk']);

        $sHTML.= $oHTML->getBlocStart('', array('class'=>'switchLanguage'));
        //$sHTML.= $oHTML->getPicture($this->getResourcePath().$sPic, 'View translation', $sUrl);
        $sHTML.= $oHTML->getLink($sText, $sUrl);
        $sHTML.= $oHTML->getBlocEnd();
      }

      $sPageTitle    = $oDbResult->getFieldValue('page_title');
      $sMetaKeywords = $oDbResult->getFieldValue('meta_keywords');
      $sMetaDescription = $oDbResult->getFieldValue('meta_desc');

      $sJobData = $oDbResult->getFieldValue('raw_data');
      $asJobData = (array)@unserialize($sJobData);

      if(empty($asJobData) || !isset($asJobData['jobID']) || empty($asJobData['jobID']))
        $sIdentfier = 'JB'.$pnPk;
      else
        $sIdentfier = 'SL'.(int)$asJobData['jobID'];

      //Set meta keywords and meta description
      if(!empty($sPageTitle))
        $oPage->setPageTitle($sPageTitle);
      if(!empty($sMetaKeywords))
        $oPage->setPageKeywords($sMetaKeywords);
      if(!empty($sMetaDescription))
        $oPage->setPageDescription($sMetaDescription);

      $sHTML.= $oHTML->getBlocStart('position');
      $sHTML.= $oHTML->getText($oDbResult->getFieldValue('position_title'), array('class'=>'boldTitle'));
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('',array('style'=>'margin-top:10px;'));

      //Position Title
      $sHTML.= $oHTML->getBlocStart('', array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('', array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_POSITION_ID'], array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sHTML.= $oHTML->getText($sIdentfier);
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      //Company
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_COMPANY'],array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        if($oDbResult->getFieldValue('company_name'))
          $sHTML.= $oHTML->getText($oDbResult->getFieldValue('company_name'));
        else
          $sHTML.= $oHTML->getText('Company Name not visible');

        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      //Location
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_LOCATION'],array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sHTML.= $oHTML->getText($oDbResult->getFieldValue('location'));
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      /*//Posted Date
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_POST_DATE'],array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sHTML.= $oHTML->getText($oDbResult->getFieldValue('posted_date'));
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();*/

      //English Level
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_ENGLISH_ABILITY'],array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sEnglish = $this->_getLanguageLevel((int)$oDbResult->getFieldValue('english'));
        $sHTML.= $oHTML->getText($sEnglish);
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      //Japanese Level
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_JAP_ABILITY'],array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sJapanese = $this->_getLanguageLevel((int)$oDbResult->getFieldValue('japanese'));
        $sHTML.= $oHTML->getText($sJapanese);
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      //Industry
      if(isset($this->casText[$oDbResult->getFieldValue('industry_name')]))
      {
        $sIndustry = $this->casText[$oDbResult->getFieldValue('industry_name')];
      }
      elseif(isset($this->casText[$oDbResult->getFieldValue('parent_industry')]))
      {
        $sIndustry = $this->casText[$oDbResult->getFieldValue('parent_industry')];
      }
      else
        $sIndustry = ' - ';

      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText('<strong>'.$this->casText['TALENT_INDUSTRY'].'</strong>', array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= $oHTML->getText($sIndustry);
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      //Career Level
      $sCareer = $oDbResult->getFieldValue('career_level');
      if(!empty($sCareer))
      {
        $sHTML.= $oHTML->getBlocStart('', array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('', array('class'=>'left_section'));
          $sHTML.= $oHTML->getText('<strong>'.$this->casText['TALENT_CAREER'].'</strong>',array('style'=>'font-weight:bold;'));
            $sHTML.= $oHTML->getBlocEnd();
            $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
            $sHTML.= $oHTML->getText($sCareer);
          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();
      }

      /*//Salary
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText('<strong>'.$this->casText['TALENT_SALARY'].'</strong>', array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= $oHTML->getText($oDbResult->getFieldValue('salary'));
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();*/

      //Train Station Display if exists
      $sStation = $oDbResult->getFieldValue('station');
      if(!empty($sStation))
      {
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_STAION'],array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd();

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= $oHTML->getText($oDbResult->getFieldValue('station'));
          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();
      }

      //Holidays Display if exists
      $sHolidays = $oDbResult->getFieldValue('holidays');
      if(!empty($sHolidays))
      {
        $sHTML.= $oHTML->getBlocStart('', array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('', array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_HOLIDAYS'], array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd();

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
          $sHTML.= $oHTML->getText($oDbResult->getFieldValue('holidays'));
          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();
      }

      //Work Hours Display if exists
      $sWorkHours = $oDbResult->getFieldValue('work_hours');
      if(!empty($sWorkHours))
      {
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText($this->casText['TALENT_WORK_HOUR'],array('style'=>'font-weight:bold;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sHTML.= $oHTML->getText($oDbResult->getFieldValue('work_hours'));
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocEnd();
      }

       //Requirements
      $sRequirements = $oDbResult->getFieldValue('requirements');
      if(!empty($sRequirements))
      {
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
            $sHTML.= $oHTML->getText($this->casText['TALENT_REQUIREMENTS'],array('style'=>'font-weight:bold;'));
            $sHTML.= $oHTML->getBlocEnd();
           $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
            $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section jodDetailDescription'));
            $sHTML.= $oHTML->getText(nl2br($oDbResult->getFieldValue('requirements')));
            $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();
      }

      //Description
      $sDescription = $oDbResult->getFieldValue('position_desc');
      if(!empty($sDescription))
      {
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
          $sHTML.= $oHTML->getText($this->casText['TALENT_DESCRIPTION'],array('style'=>'font-weight:bold;'));
          $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetailRow'));
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section jodDetailDescription'));
          $sHTML.= $oHTML->getText(nl2br($sDescription));
          $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getBlocEnd();
      }

      //Apply Button
      $sURL =  "'".$oPage->getUrl($this->_getUid(), CONST_ACTION_APPLY, CONST_TA_TYPE_JOB, $pnPk)."'";
        $sHTML.= $oHTML->getBlocStart('',array('style'=>'margin-top:15px;'));
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'left_section'));
        $sHTML.= $oHTML->getText('&nbsp;');
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('class'=>'right_section'));
        $sHTML.= "<input type='button' name='apply' id='apply' value=".$this->casText['TALENT_APPLY_NOW']." onclick = \"document.location.href = ".$sURL."\">";
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
        $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
    }
    else
    {
      $sHTML.= $oHTML->getBlocMessage('Position may have been deleted or expired');
    }

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
  * Function to get the language level name
  * @param integer $pnPk
  * @return array with language level information
  */

  private function _getLanduageLevelSlistem($level)
  {
    if($level == 0)
    {
      return $this->casText['TALENT_LANG_NONE'];
    }
    else if($level == 1 || $level == 2)
    {
      return $this->casText['TALENT_LANG_BASIC'];
    }
    else if($level == 3 || $level == 4)
    {
      return $this->casText['TALENT_LANG_CONV'];
    }
    else if($level == 5 || $level == 6 || $level == 7)
    {
      return $this->casText['TALENT_LANG_BUSINESS'];
    }
    else if($level == 8)
    {
      return $this->casText['TALENT_LANG_FLUENT'];
    }
    else if($level == 9)
    {
      return $this->casText['TALENT_LANG_NATIVE'];
    }
  }

  private function _getLanguageLevel($pnPk=0)
  {
    $asLanguage = array('0'=>$this->casText['TALENT_LANG_NONE'], '1'=>$this->casText['TALENT_LANG_BASIC'], '2'=>$this->casText['TALENT_LANG_CONV'], '3'=>$this->casText['TALENT_LANG_BUSINESS'], '4'=>$this->casText['TALENT_LANG_FLUENT'], '5'=>$this->casText['TALENT_LANG_NATIVE']);
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
    if(!assert('is_integer($pnPk)'))
      return '';

    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');

    $sHTML = $oHTML->getBlocStart('',array('class'=>'homepageContainer'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'jobDetail'));


      $sHTML.= $this->_getSideSectionJobList($pnPk);

      $sHTML.= $this->getJobApplyForm($pnPk);

      //right section
      if($oPage->getDeviceType() != CONST_PAGE_DEVICE_TYPE_PHONE)
        $sHTML.= $this->_getJobListSideSection('', false);

      $sHTML.= $oHTML->getFloatHack();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
  * Function to display the Job Application Form
  * @param integer $pnPk
  * @return string
  */

  public function getPositionDetailSlistem($pnPk)
  {
    $slistemDB = CDependency::getComponentByName('database');

    $slistemQuery = "SELECT FOUND_ROWS() as count, slp.sl_positionpk as positionpk, slp.sl_positionpk as jobfk,
                 slpd.is_public as visibility, slpd.category as category, slpd.career_level as career_level,
                 slpd.title as position_title, slpd.description as position_desc, slpd.requirements as requirements,
                 cp.sl_companypk as companyfk, slp.status as status, slp.date_created as posted_date, sll.location as location,
                 slpd.job_type as job_type, CONCAT(slp.salary_from,' - ',slp.salary_to) as salary, slp.salary_from as salary_low,
                 slp.salary_to as salary_high,  CONCAT(slp.age_from,' - ',slp.age_to) as age, slp.lvl_japanese as japanese,
                 slp.lvl_english as english, ind.sl_industrypk as industryfk, slpd.holidays as holidays, slpd.work_hours as work_hours,
                 slpd.language as lang, ind.sl_industrypk as temp_industry, slpd.title as page_title,
                 slpd.description as meta_desc, slpd.meta_keywords as meta_keywords, slpd.company_label as company_label,
                 slpd.to_jobboard as to_jobboard, slp.sl_positionpk as external_key, slpd.expiration_date as expiration_date,
                 ind.sl_industrypk as industrypk, ind.label as name, slp.status as status, ind.parentfk as parentfk,
                 cp.name as company_name, slpd.raw_data as raw_data, CONCAT(l.firstname,' ',l.lastname) as cons_name, l.email as cons_email
                 FROM sl_position slp
                 INNER JOIN sl_position_detail slpd on slpd.positionfk = slp.sl_positionpk
                 INNER JOIN sl_industry ind on ind.sl_industrypk = slp.industryfk
                 INNER JOIN sl_location sll on sll.sl_locationpk = slpd.location
                 INNER JOIN sl_company cp on cp.sl_companypk = slp.companyfk
                 INNER JOIN login l on l.loginpk = slp.created_by
                 WHERE slp.sl_positionpk = ".$pnPk;

    $positionData = $slistemDB->slistemGetAllData($slistemQuery);

    if(isset($positionData))
    {
      $positionData = $positionData[0];

      return $positionData;
    }
    else
    {
      return null;
    }
  }

  public function getJobApplyForm($pnPk)
  {
   if(!assert('is_integer($pnPk)'))
     return '';

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = $oHTML->getBlocStart('', array('class'=>'jobCentreSection'));

    $sHTML.= $oHTML->getBlocStart('jobForm', array('class' => 'simpleRedBorderTop'));
    $sHTML.= $oHTML->getBlocStart();

    if(!empty($pnPk))
    {
      //$sHTML.= $oHTML->getText($this->getPositionName($pnPk),array('class'=>'subTitle'));
      $positionDetail = $this->getPositionDetailSlistem($pnPk);
      $sHTML.= $oHTML->getText($positionDetail['position_title'],array('class'=>'subTitle'));
    }
    else
      $sHTML.= $oHTML->getText($this->casText['TALENT_COVER_LETTER'],array('class'=>'boldTitle'));

    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('style'=>'margin-top:20px;'));
    $oForm = $oHTML->initForm('jobApplyForm');

    $oPage->addCssFile(array($this->getResourcePath().'css/jobboard.css'));
    $sURL = $oPage->getURL($this->_getUid(),CONST_ACTION_SAVEADD,CONST_TA_TYPE_JOB,$pnPk);

    $oForm->setFormParams('', false, array('submitLabel' => $this->casText['TALENT_APPLY_NOW'],'action' => $sURL,'onBeforeSubmit'=>'if(!$(\'#agree_terms_id:checked\').val()){ alert(\'Please accept terms and conditions \'); return false; };'));
    $oForm->setFormDisplayParams(array('noCancelButton' => 1, 'columns' => 1));

    $oForm->addField('input', 'name', array('label'=>$this->casText['TALENT_YOUR_NAME'], 'class' => '', 'value' => getValue('name')));
    $oForm->setFieldControl('name', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255, 'jsFieldNotEmpty' => ''));

    $oForm->addField('input', 'email', array('label'=>$this->casText['TALENT_YOUR_EMAIL'], 'class' => '', 'value' => getValue('email')));
    $oForm->setFieldControl('email', array('jsFieldMinSize' => '6', 'jsFieldTypeEmail' => '', 'jsFieldNotEmpty' => ''));

    $oForm->addField('textarea', 'coverletter', array('label'=>$this->casText['TALENT_YOUR_LETTER'], 'value' => getValue('coverletter'),'style'=>'width:450px;height:250px;'));
    $oForm->setFieldControl('coverletter', array('jsFieldMaxSize' => 10000));

    $oForm->addField('input', 'documents[]', array('type' => 'file', 'label'=>$this->casText['TALENT_YOUR_RESUME'], 'value' => ''));
    $oForm->setFieldControl('documents[]', array('jsFieldNotEmpty' => ''));

    $oForm->addField('misc','',array('type'=>'br'));
    $sJavascript = '$(\'#privacyAgreement\').fadeToggle(); ';

    $oForm->addField('checkbox', 'agree_terms', array('type' => 'misc', 'label'=> $this->casText['TALENT_I_AGREE'].' <a href="javascript:;" onclick="'.$sJavascript.'">'.$this->casText['TALENT_TERMS'].' </a>'.$this->casText['TALENT_EXTRA'], 'value' => 1, 'style' =>'width:15px;', 'id' => 'agree_terms_id','errorMessage'=>'Please, accept terms and conditions'));

    $sTerms = file_get_contents($_SERVER['DOCUMENT_ROOT'].$this->getResourcePath().'terms_'.CONST_WEBSITE.'.php');
    $sTerms = nl2br($sTerms);
    $sTerms = '<div style="border:1px solid #E3E3E3; padding: 3px; border-radius: 10px; width:480px;">
               <div style="margin: 5px 3px; height:300px; width:480px; overflow-x: hidden;">'.$sTerms.'</div></div>';

    $oForm->addField('misc','',array('type'=>'text', 'style'=>'display:none;', 'id'=>'privacyAgreement','text' =>$sTerms));
    //$oForm->addField('checkbox', 'accept_contact', array('type' => 'misc', 'checked'=>'checked', 'label'=> $this->casText['TALENT_CONTACT_ANYONE'], 'value' => 1, 'id' => 'accept_contact_id','style' => 'width:15px;'));


     //Custom made simple Captcha
    $nNumber = rand(100, 900);
    $nSum1 = rand(1, 9);
    $nSum2 = rand(1, 8);
    if($nSum1 == $nSum2)
      $nSum2++;

    $asName = array('cvb', 'abc', 'fgh', 'iop', 'zxc', 'wer', 'ghj', 'vbn', 'tyu', 'sdf');
    $sImage = $oHTML->getPicture($this->getResourcePath().'/pictures/captcha/'.$asName[$nSum1].'_b.png', '', '', array('style' => 'width: 14px; height: 14px;'));
    $sImage2 = $oHTML->getPicture($this->getResourcePath().'/pictures/captcha/'.$asName[$nSum2].'_w.png', '', '', array('style' => 'background-color: #fff; width: 14px; height: 14px;'));

    if(($nNumber % 2) == 0)
    {
      $_SESSION['MAIL_CONFIRM_HUMAN'] = ($nNumber + $nSum2);
      $sColor = $this->casText['TALENT_CONFIRM_WHITE'];
    }
    else
    {
      $_SESSION['MAIL_CONFIRM_HUMAN'] = ($nNumber + $nSum1);
      $sColor = $this->casText['TALENT_CONFIRM_BLACK'];
    }

    $oForm->addField('input', 'human', array('label'=> '<span style="line-height: 20px;">'.$this->casText['TALENT_CONFIRM_HUMAN'].' ('.$nNumber.' + <b>'.$sColor.'</b>) &nbsp;&nbsp;&nbsp;&nbsp;'.$sImage.'&nbsp;&nbsp;'.$sImage2.'</span>'));
    $oForm->setFieldControl('human', array('jsFieldNotEmpty' => ''));

    $oForm->addField('misc','',array('type'=>'br'));
    $oForm->addField('misc','',array('type'=>'br'));

    $sHTML.= $oForm->getDisplay();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

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
    if(empty($sName))
      return array('error' => 'You need to input your name.');

    $sEmail = getValue('email');
    if(!isValidEmail($sEmail))
      return array('error' => 'Email invalid.');

    $sCoverLetter = getValue('coverletter');
    $ncanContact = getValue('agree_terms', 0);
    if(empty($ncanContact))
       return array('error' => 'You need to accept the Terms and conditions.');

    //============================================================================================
    // check resume is here

    if(!isset($_FILES) || !isset($_FILES['documents']) || !isset($_FILES['documents']['tmp_name']))
        return $oHTML->getErrorMessage(__LINE__.' - File can not be uploaded.');

    foreach($_FILES['documents']['tmp_name'] as $nKey => $sTmpFileName)
    {
      $sFileName = $_FILES['documents']['name'][$nKey];

      if(empty($sFileName) || !file_exists($sTmpFileName))
        return array('error' => __LINE__.' - File not found. An error might have occured while uploading the resume.');

      if(filesize($sTmpFileName) > (25*1024*1024))
        return array('error' => __LINE__.' - The uploaded file is too big.');
    }

    //============================================================================================
    // Captcha
    $nHuman      = (int)getValue('human', 0);
    if($nHuman != $_SESSION['MAIL_CONFIRM_HUMAN'])
      return array('error' => 'Looks like you\'re not a human. Please try again.');

    //reset the captcha
    unset($_SESSION['MAIL_CONFIRM_HUMAN']);


    $sAppliedDate = date('Y-m-d');
    $asRawData = array();

    $positionDetail = $this->getPositionDetailSlistem($pnPositionPk);

    /*$sQuery = 'SELECT pos.*, cp.company_name, ind.name AS industry_name, ind_parent.name as parent_industry, job.data ';
    $sQuery.= ' FROM position AS pos  ';
    $sQuery.= ' LEFT JOIN company AS cp ON (cp.companypk = pos.companyfk) ';
    $sQuery.= ' LEFT JOIN industry AS ind ON (pos.industryfk = ind.industrypk) ';
    $sQuery.= ' LEFT JOIN industry AS ind_parent ON (ind_parent.industrypk = ind.parentfk) ';

    $sQuery.= ' LEFT JOIN position as parent_pos ON (parent_pos.positionpk = pos.parentfk) ';
    $sQuery.= ' LEFT JOIN job ON (job.jobpk = parent_pos.jobfk) ';

    $sQuery.= ' WHERE pos.visibility <> 0 and pos.positionpk = '.$pnPositionPk.'';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();*/
    if(isset($positionDetail))
    {
      $asData = $positionDetail;

      //$sCompanyName = $asData['company_label'];
      //if(empty($sCompanyName))
        $sCompanyName = 'Company name not publicy visible';

      $sIndustryName = ' - ';
      if(isset($asData['name']))
      {
        $sIndustryName = $asData['name'];
      }
      /*elseif(isset($this->casText[$asData['parent_industry']]))
      {
        $sIndustryName = $this->casText[$asData['parent_industry']];
      }*/

      /*$asRawData = (array)@unserialize($asData['data']);
      if(isset($asRawData['cons_name']) && !empty($asRawData['cons_name']))
      {
        $sConsultantName = $asRawData['cons_name'];
        $sConsultantEmail = $asRawData['cons_email'];
      }*/
      if(!empty($asData['cons_name']) && !empty($asData['cons_email']))
      {
        $sConsultantName = $asData['cons_name'];
        $sConsultantEmail = $asData['cons_email'];
      }
      else
      {
        $sConsultantName = $sConsultantEmail = '';
      }

      $sPositionName = $asData['position_title'];
      $sSalary = $asData['salary'];

      //Send automatic email to the  person who has applied for the job
      $sSubject = CONST_JOBBOARD_MAIL_SITE_NAME.' confirmation ';
      $sContent = 'Dear '.$sName.', <br/><br/>';
      $sContent.= 'Thank you for applying for the position of <strong>'. $sPositionName.'</strong> on '.CONST_JOBBOARD_MAIL_SITE_NAME.'.<br/>';
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

      if(!empty($asData['requirements']) && strlen($asData['position_desc']) < 400)
        $sContent.= 'Requirements:<br /><div style="margin: 5px; padding: 5px; ">'.nl2br($asData['requirements']).'</div><br/>';

      $sContent.= '</div> <br/>';
      $sContent.= 'A member of our team will contact you if your profile matches any of our available offers.<br/>';
      $sContent.= 'Best regards.';
    }
    else
    {
      //independent application (no position)
      $sSubject = CONST_JOBBOARD_MAIL_SITE_NAME.' confirmation ';

      $sContent = 'Dear '.$sName.', <br/><br/>';
      $sContent.= 'Thank you for submitting your resume on '.CONST_JOBBOARD_MAIL_SITE_NAME.'<br/>';
      $sContent.= 'We\'ve correctly saved your resume and notified one of our consultants.<br/><br/>';
      $sContent.= '<br />';
      $sContent.= 'A member of our team will contact you if your profile matches any of our available offers.<br/>';
      $sContent.= 'Best regards.';
    }

    $bResponse = $oMail->sendRawEmail(CONST_CRM_MAIL_SENDER, $sEmail, $sSubject, $sContent);

    //if($bResponse)
    {
      $sQuery = 'INSERT INTO job_application (`positionfk`,`name`,`email`,`coverletter`,`application_date`,`canContact`,`slistemID`) VALUES (';
      $sQuery.=  ''.$oDB->dbEscapeString($pnPositionPk).','.$oDB->dbEscapeString($sName).','.$oDB->dbEscapeString($sEmail).','.$oDB->dbEscapeString($sCoverLetter).','.$oDB->dbEscapeString($sAppliedDate).','.$oDB->dbEscapeString($ncanContact).','.$oDB->dbEscapeString($pnPositionPk).')';

      $oResult = $oDB->ExecuteQuery($sQuery);
      $nJobApplicationPk = (int)$oResult->getFieldValue('pk');


      foreach($_FILES['documents']['tmp_name'] as $nKey => $sTmpFileName)
      {
        $sFileName = $_FILES['documents']['name'][$nKey];

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


        $sFileUrl = CONST_CRM_DOMAIN.CONST_PATH_UPLOAD_DIR.'job/document/'.$nJobApplicationPk.'/'.$sNewName;
        $sUrl = $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW, CONST_TA_TYPE_JOB, $pnPositionPk);

        $sContent = '<div style="font-family: Verdana,Helvetica, Arial; font-size: 11px; border: 1px solid #dedede; padding: 5px; margin: 5px;">';

        if(empty($pnPositionPk))
        {
          $sSubject = CONST_JOBBOARD_MAIL_SITE_NAME.' notifier: new resume uploaded ';
          $sContent.= 'We\'ve just received a new resume throught the website.<br/>';
        }
        else
        {
          $sSubject = CONST_JOBBOARD_MAIL_SITE_NAME.' notifier: application to job #'.$pnPositionPk;
          $sContent.= 'We\'ve just received an application for the job offer #'.$pnPositionPk.': "'.$sPositionName.'".<br />';
          $sContent.= 'Click <strong><a href="'.$sUrl.'">here</a></strong> to view the detail of the offer.<br/> <br/>';

          if(isset($asRawData['jobID']))
            $sContent.= 'Slistem position : '.$asRawData['jobID'].'<br/><br/>';
        }

        $sContent.= 'Application date : '.date('Y-m-d H:i:s').'<br/>';
        $sContent.= 'Applicant name : '.$sName.'<br/>';
        $sContent.= 'Email Address : '.$sEmail.'<br/><br/>';
        $sContent.= 'Cover Letter :<br /> <div style="margin: 5px; padding: 5px; border: 1px solid #aaaaaa;">'.$sCoverLetter.'</div><br/>';
        $sContent.= 'Resume: <a href="'.$sFileUrl.'">'.$sFileName.'</a> <br/><br/>';
        $sContent.= 'Thank you. </div>';

        $oMail->creatNewEmail();
        $oMail->setFrom(CONST_CRM_MAIL_SENDER, 'Slate job board');

        $oMail->addRecipient(CONST_DEV_EMAIL, 'dev 1');

        if(CONST_CRM_HOST == 'jobs.slate.co.jp')
        {
          $oMail->addRecipient('info@slate.co.jp', 'info@slate.co.jp');

          if(!empty($sConsultantEmail))
            $oMail->addRecipient($sConsultantEmail, $sConsultantName);
        }

        $oResult = $oMail->send($sSubject, $sContent);
        $sURL = $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_TA_TYPE_JOB);

        $sHTML = $oHTML->getRedirection($sURL, 3000, 'Thank you for submitting your resume. In a few seconds you\'ll redirected to the job search page.');
        return $sHTML;
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
  * Function to return array of companies
  * @param integer $pnCompanyPk
  * @return array/string
  */
  public function getCompanies($pnCompanyPk = 0)
  {
    if(!assert('is_integer($pnCompanyPk)'))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $asCompanies = array();

    if(empty($pnCompanyPk))
    {
      $sQuery = 'SELECT * FROM company WHERE status = 1 ORDER BY company_name ASC';
      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();

      while($bRead)
      {
        $asCompanies[$oResult->getFieldValue('companypk')] = $oResult->getData();
        $bRead = $oResult->readNext();
      }

      return $asCompanies;
    }


    $sQuery = 'SELECT * FROM company WHERE companypk = '.$pnCompanyPk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    return $oResult->getFieldValue('company_name');
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
    require_once('language/language.inc.php5');
    if(isset($gasLang[$psLanguage]))
      $this->casText = $gasLang[$psLanguage];
    else
      $this->casText = $gasLang[CONST_DEFAULT_LANGUAGE];
  }

  /*
   * display a scroller containing the last posted jobs
   * @param string $psSearchId
   * @return string html content
   */
  private function _getJobSlider($psSearchId)
  {
    $oDB = CDependency::getComponentByName('database');
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $slistemDB = CDependency::getComponentByName('database');

    $sLanguage = $oPage->getLanguage();
    $oPage->addRequiredJsFile($this->getResourcePath().'/js/jquery.totemticker.js');

    $sJavascript = ' $(document).ready(function()
      {
			  $("#jobScrollerListId").totemticker({
          row_height	:	"75px",
          next		:	"#ticker-next",
          previous	:	"#ticker-previous",
          stop		:	"#stop",
          start		:	"#start",
          mousestop	:	true,
          speed :   1250,
          interval : 3500,
          max_items : 30,
          direction	:	"down"
        });
      });
      ';
    $oPage->addCustomJs($sJavascript);

    //TODO: use psSearchId to not display same as in the result list
    /*$sQuery = 'SELECT * FROM position as pos ';
    $sQuery.= ' WHERE pos.status > 0 AND pos.parentfk <> 0 AND pos.visibility <> 0 AND pos.lang = "'.$sLanguage.'" ';
    $sQuery.= ' AND pos.position_desc <> "" ';
    $sQuery.= ' ORDER BY posted_date DESC LIMIT 10 ';*/

    $slistemQuery = "SELECT FOUND_ROWS() as count, slp.sl_positionpk as positionpk, slp.sl_positionpk as jobfk,
                     slpd.is_public as visibility, slpd.category as category, slpd.career_level as career_level,
                     slpd.title as position_title, slpd.description as position_desc, slpd.requirements as requirements,
                     cp.sl_companypk as companyfk, slp.status as status, slp.date_created as posted_date, sll.location as location,
                     slpd.job_type as job_type, CONCAT(slp.salary_from,' - ',slp.salary_to) as salary, slp.salary_from as salary_low,
                     slp.salary_to as salary_high,  CONCAT(slp.age_from,' - ',slp.age_to) as age, slp.lvl_japanese as japanese,
                     slp.lvl_english as english, ind.sl_industrypk as industryfk, slpd.holidays as holidays, slpd.work_hours as work_hours,
                     slpd.language as lang, ind.sl_industrypk as temp_industry, slpd.title as page_title,
                     slpd.description as meta_desc, slpd.meta_keywords as meta_keywords, slpd.company_label as company_label,
                     slpd.to_jobboard as to_jobboard, slp.sl_positionpk as external_key, slpd.expiration_date as expiration_date,
                     ind.sl_industrypk as industrypk, ind.label as name, slp.status as status, ind.parentfk as parentfk,
                     cp.name as company_name, slpd.raw_data as raw_data
                     FROM sl_position slp
                     INNER JOIN sl_position_detail slpd on slpd.positionfk = slp.sl_positionpk
                     INNER JOIN sl_industry ind on ind.sl_industrypk = slp.industryfk
                     INNER JOIN sl_location sll on sll.sl_locationpk = slpd.location
                     INNER JOIN sl_company cp on cp.sl_companypk = slp.companyfk
                     WHERE slpd.is_public = 1 AND slpd.public_flag = 'a' order by slp.date_created DESC LIMIT 0,10";

    $oResult = $oDB->ExecuteQuery($sQuery);

    $positionData = $slistemDB->slistemGetAllData($slistemQuery);

    /*$bRead = $oResult->readFirst();
    if(!$bRead)
      return '';*/

    $sHtml = $oHTML->getBlocStart('', array('class' => 'redBorderTop'));

      $sHtml.= $oHTML->getBlocStart('', array('class' => 'sideBarTitle'));
      $sHtml.= $oHTML->getText('<b>Latest Jobs</b>');
      $sHtml.= $oHTML->getBlocEnd();

      $sHtml.= $oHTML->getBlocStart('jobScrollerId');
      $sHtml.= $oHTML->getListStart('jobScrollerListId');

      //while($bRead)
      foreach ($positionData as $key => $asJobData)
      {
        //$asJobData = $oResult->getData();
        $asJobData['position_title'] = strip_tags($asJobData['position_title']);
        $asJobData['position_desc'] = strip_tags($asJobData['position_desc']);

        $sText = $asJobData['position_desc'].' '.$asJobData['requirements'];
        if(!empty($sText))
        {
          $sEncoding =  mb_detect_encoding($asJobData['position_title']);
          if(isJapanese($sText))
          {
            $nTitleSize = 15;
            $nDescSize = 43;
          }
          else
          {
            $nTitleSize = 25;
            $nDescSize = 78;
          }
          //echo $sEncoding.' '.$nTitleSize;

          if(mb_strlen($asJobData['position_title'], $sEncoding) > $nTitleSize)
          {
            $asJobData['position_title'] = mb_substr($asJobData['position_title'],0, ($nTitleSize-3), $sEncoding).' ...';
          }

          if(mb_strlen($asJobData['position_desc'], $sEncoding) > $nDescSize)
          {
            $asJobData['position_desc'] = mb_substr($asJobData['position_desc'],0, ($nDescSize-3), $sEncoding).' ...';
          }

          $sUrl = $oPage->getUrl('jobboard', CONST_ACTION_VIEW, CONST_TA_TYPE_JOB, (int)$asJobData['positionpk']);
          $sHtml.= $oHTML->getListItemStart('', array('class' => 'scrollerJobContainer', 'onclick' => 'document.location.href = \''.$sUrl.'\';'));

            $sHtml.= $oHTML->getBlocStart('', array('class' => 'scrollerPicture'));
            $sHtml.= $this->_getConsultantPictureByIndustry((int)$asJobData['industryfk']);
            $sHtml.= $oHTML->getBlocEnd();

            $sHtml.= $oHTML->getBlocStart('', array('class' => 'scrollerTitle'));
            $sHtml.= $oHTML->getLink($asJobData['position_title'], $sUrl);
            $sHtml.= $oHTML->getBlocEnd();

            $sHtml.= $oHTML->getBlocStart('', array('class' => 'scrollerDesc'));
            $sHtml.= $oHTML->getText(substr(strip_tags($asJobData['position_desc']), 0, 110));
            $sHtml.= $oHTML->getBlocEnd();

            $sHtml.= $oHTML->getFloathack();

          $sHtml.= $oHTML->getListItemEnd();
        }

        $bRead = $oResult->readNext();
      }

      $sHtml.= $oHTML->getListEnd();
      $sHtml.= $oHTML->getBlocEnd();

    $sHtml.= $oHTML->getBlocEnd();

    return $sHtml;
  }

  private function _shortenText($psString, $pnWordNumber = 10, $pnMaxLetter = 50, $psMoreText = '...')
  {
    $asString = explode(' ', strip_tags($psString));
    if(count($asString) < $pnWordNumber)
      return $psString;

    $asString = array_slice($asString, 0, $pnWordNumber);
    $sString = implode(' ', $asString);

    if(!empty($pnMaxLetter))
    {
      $nLength = strlen($sString);

      while($nLength > $pnMaxLetter)
      {
        $pnWordNumber--;
        $sString = implode(' ', array_slice($asString, 0, $pnWordNumber));
        $nLength = strlen($sString);
      }
    }

    return $sString.' '.$psMoreText;
  }

  private function _getSocialMediaSection()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');


    $sJavascript = ' $(document).ready(function()
      {
			  $("#socialMediaId ul li img").bind("mouseenter", function()
        {
          $(this).animate({opacity: 0}, 10, function(){ $(this).attr("src", $(this).attr("over_pic")).animate({opacity: 1}, 350); });
        });

        $("#socialMediaId ul li img").bind("mouseleave", function()
        {
          $(this).animate({opacity: 0}, 10, function(){ $(this).attr("src", $(this).attr("default_pic")).animate({opacity: 1}, 50); });
        });
      });
      ';
    $oPage->addCustomJs($sJavascript);


    $sHtml = $oHTML->getBlocStart('socialMediaId', array('class' => 'redBorderTop'));

      $sHtml.= $oHTML->getBlocStart('', array('class' => 'sideBarTitle'));
      $sHtml.= $oHTML->getText($this->casText['TALENT_FOLLOW_US']);
      $sHtml.= $oHTML->getBlocEnd();

      $sHtml.= $oHTML->getListStart();

        $sHtml.= $oHTML->getListItemStart('', array('class' => 'scrollerJobContainer'));
        $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/facebook.png', '', '', array('over_pic' => $this->getResourcePath().'pictures/facebook_over.png', 'default_pic' => $this->getResourcePath().'pictures/facebook.png'));
        $sHtml.= $oHTML->getLink($sPic, 'http://www.facebook.com/SlateExecutiveSearchGroup', array('target' => '_blank'));
        $sHtml.= $oHTML->getListItemEnd();

        $sHtml.= $oHTML->getListItemStart('', array('class' => 'scrollerJobContainer'));
        $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/youtube.png', '', '', array('over_pic' => $this->getResourcePath().'pictures/youtube_over.png', 'default_pic' => $this->getResourcePath().'pictures/youtube.png'));
        $sHtml.= $oHTML->getLink($sPic, 'http://www.youtube.com/SlateConsulting', array('target' => '_blank'));
        $sHtml.= $oHTML->getListItemEnd();

        $sHtml.= $oHTML->getListItemStart('', array('class' => 'scrollerJobContainer'));
        $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/linkedin.png', '', '', array('over_pic' => $this->getResourcePath().'pictures/linkedin_over.png', 'default_pic' => $this->getResourcePath().'pictures/linkedin.png'));
        $sHtml.= $oHTML->getLink($sPic, 'http://www.linkedin.com/company/slate-consulting-k.k.', array('target' => '_blank'));
        $sHtml.= $oHTML->getListItemEnd();

      $sHtml.= $oHTML->getListEnd();


    $sHtml.= $oHTML->getFloatHack();
    $sHtml.= $oHTML->getBlocEnd();

    return $sHtml;
  }

  private function _getLanguageSection()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHtml = $oHTML->getBlocStart('sideLanguageId', array('class' => 'redBorderTop'));

      $sHtml.= $oHTML->getBlocStart('', array('class' => 'sideBarTitle'));
      $sHtml.= $oHTML->getText($this->casText['TALENT_SELECT_LANGUAGE']);
      $sHtml.= $oHTML->getBlocEnd();

      //$sUrl = $oPage->getUrl($this->csUid, CONST_ACTION_LIST, CONST_TA_TYPE_JOB);
      $sUrl = $oPage->getRequestedUrl();
      if(strpos($sUrl, '?') === false)
      {
        $sEnUrl = $sUrl.'?setLang=en';
        $sJpUrl = $sUrl.'?setLang=jp';
      }
      else
      {
        $sEnUrl = $sUrl.'&setLang=en';
        $sJpUrl = $sUrl.'&setLang=jp';
      }

      $sHtml.= $oHTML->getListStart();

        $sHtml.= $oHTML->getListItemStart('', array('class' => 'scrollerJobContainer'));
        $sHtml.= $oHTML->getPicture($this->getResourcePath().'pictures/lang_english.png', 'Set job board in english', $sEnUrl);
        $sHtml.= $oHTML->getListItemEnd();

        $sHtml.= $oHTML->getListItemStart('', array('class' => 'scrollerJobContainer'));
        $sHtml.= $oHTML->getPicture($this->getResourcePath().'pictures/lang_japanese.png', 'Set job board in japanese', $sJpUrl);
        $sHtml.= $oHTML->getListItemEnd();

      $sHtml.= $oHTML->getListEnd();

    $sHtml.= $oHTML->getFloatHack();
    $sHtml.= $oHTML->getBlocEnd();

    return $sHtml;
  }

  private function _getConsultantPictureByIndustry($pnIndustryPk, $pbLinkOnly = false)
  {
    if(!assert('is_integer($pnIndustryPk) && is_bool($pbLinkOnly)'))
      return '';

    if(empty($pnIndustryPk))
      return $this->getResourcePath().'pictures/consultant/unknown.jpg';

    if(empty($this->casConsultantIndustries))
    {
      $this->casConsultantIndustries = $this->getIndustries(0, false, false, true);

      $this->casConsultantIndustries[0]['picture'] = 'other.png';
      $this->casConsultantIndustries[0]['legend'] = $this->casText['TALENT_GROUP_OTHER_LEGEND'];
      $this->casConsultantIndustries[0]['link'] = 'javascript:;';

      foreach($this->casConsultantIndustries as $asIndustry)
      {
        if(!empty($asIndustry['industrypk']))
        {
          $nIndustryPk = (int)$asIndustry['industrypk'];
          $nParentFk = (int)$asIndustry['parentfk'];
          switch($nParentFk)
          {
            //then parenty industry ones
            case 501:
              $this->casConsultantIndustries[$nIndustryPk]['picture'] = 'finance.png';
              $this->casConsultantIndustries[$nIndustryPk]['legend'] = $this->casText['TALENT_GROUP_FIN_LEGEND'];
              $this->casConsultantIndustries[$nIndustryPk]['link'] = 'http://www.slate.co.jp/expertise?show=financial_services';
              break;

            case 502:
              $this->casConsultantIndustries[$nIndustryPk]['picture'] = 'it.png';
              $this->casConsultantIndustries[$nIndustryPk]['legend'] = $this->casText['TALENT_GROUP_IT_LEGEND'];
              $this->casConsultantIndustries[$nIndustryPk]['link'] = 'http://www.slate.co.jp/expertise?show=it_services';
              break;

            case 503:
              $this->casConsultantIndustries[$nIndustryPk]['picture'] = 'consumer_goods.png';
              $this->casConsultantIndustries[$nIndustryPk]['legend'] = $this->casText['TALENT_GROUP_CNS_LEGEND'];
              $this->casConsultantIndustries[$nIndustryPk]['link'] = 'http://www.slate.co.jp/expertise?show=consumer_goods';
              break;

            case 504:
              $this->casConsultantIndustries[$nIndustryPk]['picture'] = 'industrial.png';
              $this->casConsultantIndustries[$nIndustryPk]['legend'] = $this->casText['TALENT_GROUP_INDUS_LEGEND'];
              $this->casConsultantIndustries[$nIndustryPk]['link'] = 'http://www.slate.co.jp/expertise?show=industrial';
              break;

            case 506:
              $this->casConsultantIndustries[$nIndustryPk]['picture'] = 'healthcare.png';
              $this->casConsultantIndustries[$nIndustryPk]['legend'] = $this->casText['TALENT_GROUP_LS_LEGEND'];
              $this->casConsultantIndustries[$nIndustryPk]['link'] = 'http://www.slate.co.jp/expertise?show=life_sciences';
              break;

            case 507:
              $this->casConsultantIndustries[$nIndustryPk]['picture'] = 'accounting.png';
              $this->casConsultantIndustries[$nIndustryPk]['legend'] = $this->casText['TALENT_GROUP_ACC_LEGEND'];
              $this->casConsultantIndustries[$nIndustryPk]['link'] = 'http://www.slate.co.jp/expertise?show=finance_and_accounting';
              break;


            default:
              $this->casConsultantIndustries[$nIndustryPk]['picture'] = 'other.png';
              $this->casConsultantIndustries[$nIndustryPk]['legend'] = $this->casText['TALENT_GROUP_OTHER_LEGEND'];
              $this->casConsultantIndustries[$nIndustryPk]['link'] = 'javascript:;';
              break;
          }
        }
      }
    }

    if(!isset($this->casConsultantIndustries[$pnIndustryPk]))
      $pnIndustryPk = 0;

    if($pbLinkOnly)
      return CONST_CRM_DOMAIN.$this->getResourcePath().'pictures/consultant/'.$this->casConsultantIndustries[$pnIndustryPk]['picture'];

    $oDisplay = CDependency::getComponentByName('display');
    return $oDisplay->getPicture(CONST_CRM_DOMAIN.$this->getResourcePath().'pictures/consultant/'.$this->casConsultantIndustries[$pnIndustryPk]['picture'], $this->casConsultantIndustries[$pnIndustryPk]['legend'], $this->casConsultantIndustries[$pnIndustryPk]['link']);
  }


  private function _getRssDescription()
  {
    //header('Content-Type: application/rss+xml; charset=utf-8');
    header('Content-Type: text/xml');

    $oXml = new SimpleXMLElement('<SlateJobRss></SlateJobRss>');
    $oXml->addAttribute('date_generate', date('Y-m-d H:i:s'));

    $oNewParam = $oXml->addChild('param');
    $oNewParam->addAttribute('name', 'language');
    $oNewParamVal = $oNewParam->addChild('values', 'the language the positions should be displayed in. Accepted values: [en, jp] / Default: en');

    $oNewParam = $oXml->addChild('param');
    $oNewParam->addAttribute('name', 'salary_low');
    $oNewParamVal = $oNewParam->addChild('values', 'an positive integer representing the lowest monthly salary acceptable (in yen). / Default: not filtered. ');

    $oNewParam = $oXml->addChild('param');
    $oNewParam->addAttribute('name', 'salary_high');
    $oNewParamVal = $oNewParam->addChild('values', 'an positive integer representing the highest monthly salary acceptable (in yen). / Default: not filtered. ');

    $oNewParam = $oXml->addChild('param');
    $oNewParam->addAttribute('name', 'sort_field');
    $oNewParamVal = $oNewParam->addChild('values', 'the field the result can be sorted on. Accepted values: [pos.posted_date, pos.salary, ind.name] / Default: date');

    $oNewParam = $oXml->addChild('param');
    $oNewParam->addAttribute('name', 'sort_order');
    $oNewParamVal = $oNewParam->addChild('values', 'the sort order. Accpeted values: [ ASC, DESC]. / Default: DESC ');

    $oNewParam = $oXml->addChild('param');
    $oNewParam->addAttribute('name', 'industry_id');
    $oNewParamVal = $oNewParam->addChild('values', 'a positive integer value matching one of the following');

    $asIndustry = $this->getIndustries(0, false, false, true);
    foreach($asIndustry as $asIndusData)
    {
      if(isset($this->casText[$asIndusData['name']]))
      {
        $oIndustry = $oNewParamVal->addChild('industry');
        $oIndustry->addAttribute('name', $this->casText[$asIndusData['name']]);
        $oIndustry->addAttribute('id', $asIndusData['industrypk']);
      }
    }

    return $oXml->asXML();
  }

  private function _getRssFeed()
  {
    $sLanguage = getValue('language', 'en');
    $nIndustryId = (int)getValue('industry_id', 0);

    $nSalaryLow = (int)getValue('salary_low', 0);
    $nSalaryHigh = (int)getValue('salary_high', 0);

    $sSortField = getValue('sort_field', 'pos.posted_date');
    $sSortOrder = getValue('sort_order', 'DESC');

    $nLimit = (int)getValue('limit', 0);

    //init the XML object
    header('Content-Type: text/xml');
    $oXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><SlateJobRss></SlateJobRss>');
    $oXml->addAttribute('date_generate', date('Y-m-d H:i:s'));

    //TODO add control !!!!
    if(!in_array($sLanguage, array('en', 'jp')))
    {
      $oNewJob = $oXml->addChild('error', 'Language should be [en,jp]. Given: '.$sLanguage);
      return $oXml->asXML();
    }

    if($nIndustryId < 0 || $nIndustryId > 999)
    {
      $oNewJob = $oXml->addChild('error', 'Industry ID seems incorrect. Given: '.$nIndustryId);
      return $oXml->asXML();
    }

    $asIndustry = $this->getIndustries(0, false, false, true);
    if(!empty($nIndustryId) && !isset($asIndustry[$nIndustryId]))
    {
      $oNewJob = $oXml->addChild('error', 'The industry ID could not be found. Given: '.$nIndustryId);
      return $oXml->asXML();
    }


    if(!empty($nSalaryLow) && ($nSalaryLow < 10000 || $nSalaryLow > 100000000))
    {
      $oNewJob = $oXml->addChild('error', 'Salary low should be > 10000Yens  and < 100000000 . Given: '.$nSalaryLow);
      return $oXml->asXML();
    }

    if(!empty($nSalaryHigh) && ($nSalaryHigh < 10000 || $nSalaryHigh > 100000000))
    {
      $oNewJob = $oXml->addChild('error', 'Salary high should be > 10000Yens  and < 100000000 . Given: '.$nSalaryHigh);
      return $oXml->asXML();
    }

    if($nSalaryLow > $nSalaryHigh)
    {
      $nTmp = $nSalaryHigh;
      $nSalaryHigh = $nSalaryLow;
      $nSalaryLow = $nTmp;
    }

    if(!in_array($sSortField, array('pos.posted_date', 'pos.salary', 'ind.name')))
    {
      $oNewJob = $oXml->addChild('error', 'Sort field should be [pos.posted_date, pos.salary, ind.name]. Given: '.$sSortField);
      return $oXml->asXML();
    }

    if(!in_array($sSortOrder, array('ASC', 'DESC')))
    {
      $oNewJob = $oXml->addChild('error', 'Sort field should be [ASC, DESC]. Given: '.$sSortOrder);
      return $oXml->asXML();
    }

    if(!empty($nLimit) && ($nLimit < 5 || $nLimit > 200))
    {
      $oNewJob = $oXml->addChild('error', 'Limit value should be between 5 and 200. Given: '.$nLimit);
      return $oXml->asXML();
    }

    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');

    $sQuery = 'SELECT pos.*, ind.name, parent_ind.name as parent_industry FROM position as pos ';
    $sQuery.= ' INNER JOIN industry as ind on (ind.industrypk = pos.industryfk) ';
    $sQuery.= ' LEFT JOIN industry as parent_ind on (parent_ind.industrypk = ind.parentfk) ';
    $sQuery.= ' WHERE lang = "'.$sLanguage.'" ';

    if(!empty($nIndustryId))
      $sQuery.= ' AND industryfk = '.$nIndustryId;

    if(!empty($nSalaryLow))
      $sQuery.= ' AND salary_low >= '.$nSalaryLow;

    if(!empty($nSalaryHigh))
      $sQuery.= ' AND salary_high <= '.$nSalaryHigh;

    if($sSortField == 'pos.salary')
    {
      if($sSortOrder == 'ASC')
        $sQuery.= ' ORDER BY pos.salary_low, pos.salary_high ';
      else
        $sQuery.= ' ORDER BY pos.salary_high DESC, pos.salary_low DESC ';
    }
    else
    {
      if(!empty($sSortField))
        $sQuery.= ' ORDER BY '.$sSortField.' ';
      else
        $sQuery.= ' ORDER BY pos.posted_date ';

      if(!empty($sSortOrder))
        $sQuery.= ' '.$sSortOrder.' ';
    }

    if(!empty($nLimit))
      $sQuery.= ' LIMIT '.$nLimit.' ';
    else
      $sQuery.= ' LIMIT 10 ';

    //$oXml->addChild('query', mb_convert_encoding(($sQuery), 'utf8'));

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
    {
      $oXml->addChild('no_result', 'no result');
      return $oXml->asXML();
    }

    // TODO: should use ENT_DISALLOWED or ENT_XML1, but only available after php 5.4
    while($bRead)
    {
      $asJobData = $oDbResult->getData();
      $sDescription = strip_tags($asJobData['position_desc']);
      if(strlen($sDescription) < 40)
        $sDescription.= ' '.strip_tags($asJobData['requirements']);

      $oNewJob = $oXml->addChild('position');
      $oNewJob->addAttribute('uid', $asJobData['positionpk']);

      $oNewJob->addChild('uid', $asJobData['positionpk']);
      $oNewJob->addChild('date', $asJobData['posted_date']);
      $oNewJob->addChild('title', htmlspecialchars($asJobData['position_title'], ENT_QUOTES, 'UTF-8'));
      $oNewJob->addChild('description', htmlspecialchars($sDescription, ENT_QUOTES, 'UTF-8'));
      $oNewJob->addChild('salary', htmlspecialchars($asJobData['salary'], ENT_QUOTES, 'UTF-8'));
      $oNewJob->addChild('industry_id', htmlspecialchars($asJobData['industryfk'], ENT_QUOTES, 'UTF-8'));

      if(isset($this->casText[$asJobData['name']]))
        $sIndustry = $this->casText[$asJobData['name']];
      else
      {
        if(!isset($this->casText[$asJobData['parent_industry']]))
        {
          assert('false; // there should be a parent industry no matter what.');
          $sIndustry = ' - ';
        }
        else
          $sIndustry = $this->casText[$asJobData['parent_industry']];
      }


      $oNewJob->addChild('industry', htmlspecialchars($sIndustry));

      $sUrl = $oPage->geturl('jobboard', CONST_ACTION_VIEW, CONST_TA_TYPE_JOB, (int)$asJobData['positionpk']);
      $sUrl.= '&setLang='.$sLanguage;
      $oNewJob->addChild('url', htmlspecialchars($sUrl, ENT_QUOTES, 'UTF-8'));

      $sUrl = $this->_getConsultantPictureByIndustry((int)$asJobData['industryfk'], true);
      $oNewJob->addChild('picture', htmlspecialchars($sUrl, ENT_QUOTES, 'UTF-8'));

      $bRead = $oDbResult->readNext();
    }

    //https://job.slate.co.jp/index.php5?uid=153-160&ppa=ppav&ppt=jrss&pg=ajx
    //https://job.slate.co.jp/index.php5?uid=153-160&ppa=ppal&ppt=jrss&pg=ajx
    return $oXml->asXML();
  }


  private function _getSiblingPosition($pnCurrentPk, $psLanguage = 'en')
  {
    if(!assert('is_integer($pnCurrentPk) && !empty($pnCurrentPk)'))
      return array();

    if(!assert('in_array($psLanguage, array("en", "jp"))'))
      return array();

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT sibling.* FROM position as pos';
    $sQuery.= ' INNER JOIN position as parent ON (parent.positionpk = pos.parentfk AND pos.positionpk = '.$pnCurrentPk.') ';
    $sQuery.= ' INNER JOIN position as sibling ON (sibling.parentfk = parent.positionpk AND sibling.positionpk <> '.$pnCurrentPk.' AND sibling.lang = "'.$psLanguage.'") ';
    $sQuery.= ' WHERE pos.positionpk = '.$pnCurrentPk.' ';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $asResult = array();

    while($bRead)
    {
      $asResult[$oDbResult->getFieldValue('positionpk', CONST_PHP_VARTYPE_INT)] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asResult;
  }

  public function getLocationOption($psValue = '')
  {
    $asList = $this->getLocationList();

    /*$sOption = '<option value=""> - </option>';
    foreach($asList as $sValue => $sLabel)
    {
      if($sValue == $psValue)
        $sOption.= '<option value="'.$sValue.'" selected="selected">'.$sLabel.'</option>';
      else
        $sOption.= '<option value="'.$sValue.'">'.$sLabel.'</option>';
    }*/
    return $asList;
  }

  public function getLocationList()
  {
    //$oDb = CDependency::getComponentByName('database');
    $slistemDB = CDependency::getComponentByName('database');
    $slistemQuery = 'SELECT * FROM sl_location ORDER BY location ';

    $positionData = $slistemDB->slistemGetAllData($slistemQuery);

    //$oDbResult = $oDb->executeQuery($sQuery);
    //$bRead = $oDbResult->readFirst();

    $asLocation = array();
    //while($bRead)
    foreach ($positionData as $key => $value)
    {
      $asLocation[$value['sl_locationpk']] = $value['location'];
      //$bRead = $oDbResult->readNext();
    }

    //$_SESSION['sl_location_list'] = $asLocation;
    return $asLocation;
  }

  public function getLanguageList()
  {
    $language = array('None','Beginner','Conversational','Business','Fluent','Native');
    $i = 0;
    $asLocation = array();

    foreach ($language as $key => $value)
    {
      $asLocation[$i] = $value;
      $i++;
    }

    return $asLocation;
  }

  public function getIndustryList()
  {
    //$oDb = CDependency::getComponentByName('database');
    $slistemDB = CDependency::getComponentByName('database');
    $slistemQuery = 'SELECT * FROM sl_industry WHERE parentfk > 0 ORDER BY label ';

    $positionData = $slistemDB->slistemGetAllData($slistemQuery);

    //$oDbResult = $oDb->executeQuery($sQuery);
    //$bRead = $oDbResult->readFirst();

    $asLocation = array();
    //while($bRead)
    foreach ($positionData as $key => $value)
    {
      $asLocation[$value['sl_industrypk']] = $value['label'];
      //$bRead = $oDbResult->readNext();
    }

    //$_SESSION['sl_location_list'] = $asLocation;
    return $asLocation;
  }

  public function getOccupationList()
  {
    //$oDb = CDependency::getComponentByName('database');
    $slistemDB = CDependency::getComponentByName('database');
    $slistemQuery = 'SELECT * FROM sl_occupation WHERE parentfk > 0 ORDER BY label ';

    $positionData = $slistemDB->slistemGetAllData($slistemQuery);

    //$oDbResult = $oDb->executeQuery($sQuery);
    //$bRead = $oDbResult->readFirst();

    $asLocation = array();
    //while($bRead)
    foreach ($positionData as $key => $value)
    {
      $asLocation[$value['label']] = $value['label'];
      //$bRead = $oDbResult->readNext();
    }

    //$_SESSION['sl_location_list'] = $asLocation;
    return $asLocation;
  }

  public function change_language_system($fiveSystem)
  {ChromePhp::log($fiveSystem);
    if($fiveSystem == 0)
    {
      return 0;
    }
    else if($fiveSystem == 1)
    {
      return 2;
    }
    else if($fiveSystem == 2)
    {
      return 4;
    }
    else if($fiveSystem == 3)
    {
      return 5;
    }
    else if($fiveSystem == 4)
    {
      return 7;
    }
    else if($fiveSystem == 5)
    {
      return 9;
    }
    else
      return 0;
  }

  public function getTranslation($psTextCode)
  {
    if(!assert('isset($this->casText["'.$psTextCode.'"])'))
      return '';

    return $this->casText[$psTextCode];
  }

}