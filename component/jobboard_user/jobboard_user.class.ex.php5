<?php

require_once('component/jobboard_user/jobboard_user.class.php5');
require_once('component/taaggregator/resources/lib/encoding_converter.class.php5');

class CJobboarduserEx extends CJobboarduser
{
  private $csBoardComponent = '';
  private $casAllowedIp = array('127.0.0.1', '192.168.81.93', '203.167.38.11', '118.243.81.245', '209.145.120.7');


  public function __construct()
  {
    //change the display component based on the platform name
    if(CONST_WEBSITE == 'talentAtlas')
      $this->csBoardComponent = 'talentatlas';
    else
      $this->csBoardComponent = 'jobboard';

    return true;
  }

  public function getDefaultType()
  {
    return CONST_TA_TYPE_LIST_JOB;
  }

  public function getDefaultAction()
  {
    return CONST_ACTION_LIST;
  }


  //====================================================================
  //  Interfaces
  //====================================================================

  public function getPageActions($psAction = '', $psType = '', $pnPk = 0)
  {
    $asActions = array();
    $oPage = CDependency::getComponentByName('page');

    $asActions['ppav'][] = array('title'=>'Job Listing', 'url' => $oPage->getUrl($this->_getUid(),CONST_ACTION_LIST,CONST_TA_TYPE_LIST_JOB));

    return $asActions;
  }

  public function getAjax()
  {
    $this->_processUrl();

    switch($this->csType)
    {
      case CONST_TA_TYPE_LIST_JOB:
        switch($this->csAction)
        {
          case CONST_ACTION_SAVEADD:
          case CONST_ACTION_SAVEEDIT:
            return json_encode($this->_getSavePosition($this->cnPk));
            break;

          case CONST_ACTION_DELETE:
            return json_encode($this->_getRemoveJobs($this->cnPk));
            break;
        }
        break;

       case CONST_LIST_COMPANY:

        switch ($this->csAction)
        {
          case CONST_ACTION_LIST:
            return $this->_getCompanySelector();
            break;
        }
        break;

      case CONST_TA_TYPE_SHARE_JOB:

        switch($this->csAction)
        {
          case CONST_ACTION_SAVEADD:
            return json_encode($this->_shareJob());
            break;
        }
        break;
      }
    }

  public function getHtml()
  {
    $this->_processUrl();

    switch($this->csType)
    {
      case CONST_TA_TYPE_LIST_JOB:

        switch($this->csAction)
        {
          case CONST_ACTION_LIST:
            return $this->_getManageJobs();
            break;

            case CONST_ACTION_ADD:
            case CONST_ACTION_EDIT:
              return $this->_getJobEditForm($this->cnPk);
              break;

            case CONST_ACTION_SEND:
              $this->_getCompanySend($this->cnPk);
              break;
          }
          break;

      case CONST_TA_TYPE_SHARE_JOB:

        switch($this->csAction)
        {
          case CONST_ACTION_LIST:
            return $this->_getManageJobs('share');
            break;

            case CONST_ACTION_ADD:
              return $this->_getJobSharingForm($this->cnPk);
              break;
          }
          break;
    }

    return '';
  }


  public function getCronJob()
  {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'])
    {
        $client_ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        $client_ip_address = $_SERVER['REMOTE_ADDR'];
    }

    if(!in_array($client_ip_address, $this->casAllowedIp))
    {
      echo 'buuuuuu!!!';
      return '';
    }

    if(CONST_WEBSITE == 'jobboard')
    {
      //load moderated Slistem positions from talentAtlas
      echo 'import english positions: <br />';
      $this->_importPositions('en');

      echo ' <br />import japanese positions: <br />';
      $this->_importPositions('jp');

      if((int)date('H') == 7)
      {
        $oSetting = CDependency::getComponentByName('settings');
        $asData = $oSetting->getSystemSettings('jb_user_cron_date');

        if(empty($asData) || $asData < date('Y-m-d'))
        {
          echo '<br />check position expiration date <br />';
          $this->_notifyExpiration();

          echo '<br /> save execution date ';
          $oSetting->setSystemSettings('jb_user_cron_date', date('Y-m-d'));
        }
      }
    }

    return true;
  }


  //====================================================================
  //  Other methods
  //====================================================================


  /**
  * Function to display the home page for the users after logged in
  * @return string
  */
  private function _getManageJobs($psType = '')
  {
    //echo"<br><br><br>";
    //echo'_getManageJobs';
    //ChromePhp::log('_getManageJobs');
    $oDB = CDependency::getComponentByName('database');
    $slistemDB = CDependency::getComponentByName('database');
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPager = CDependency::getComponentByName('pager');
    $oJobboard = CDependency::getComponentByName($this->csBoardComponent);
    $oPager->initPager();
    $oPage->addCssFile(array($this->getResourcePath().'css/jobboard_user.css'));

    $sLanguage = getValue('lang');
    $sAction = getValue('action');
    $bSlistemOnly = (bool)getValue('slistem', 0);
    $sSort = getValue('sort');
    $bDisplayFilter = true;

    $sSortField = getValue('sortfield');
    $sSortOrder = getValue('sortorder');

    //echo"<br><br><br> Log:";
    //echo $sSortField;

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
                     WHERE slpd.is_public = 1 ";

    if(!empty($sSortField))
    {
      $sOrder = ' ORDER BY '.$sSortField.' '.$sSortOrder;
      $slistemQuery .= ' ORDER BY '.$sSortField.' '.$sSortOrder;
    }
    else
    {
      $sOrder = ' ORDER BY external_key DESC';
      $slistemQuery .= " ORDER BY slp.date_created DESC";
    }

    $slistemQuery.= ' LIMIT '.$oPager->getSqlOffset().','.$oPager->getLimit();
//ChromePhp::log($slistemQuery);
//var_dump($slistemQuery);
//echo "<br><br>";
    $positionDataSlistem = $slistemDB->slistemGetAllData($slistemQuery);
//var_dump($positionDataSlistem);
    if($psType == 'share')
    {
      $bDisplayFilter = false;
      $sCountQuery = ' SELECT count(distinct pos.positionpk) as nCount FROM position as pos WHERE pos.parentfk IS NOT NULL AND lang = "en" ';

      $sQuery = ' SELECT pos.*, ind.*, cp.*, GROUP_CONCAT(psh.platform SEPARATOR ", ") as shared_platform, ind.status as indus_status FROM position as pos ';
      $sQuery.= ' LEFT JOIN industry AS ind ON (ind.industrypk = pos.industryfk)';
      $sQuery.= ' LEFT JOIN company AS cp ON (cp.companypk = pos.companyfk) ';
      $sQuery.= ' LEFT JOIN position_share AS psh ON (psh.positionfk = pos.positionpk) ';
      $sQuery.= ' WHERE pos.parentfk IS NOT NULL AND lang = "en" ';
      $sQuery.= ' GROUP BY pos.positionpk ';
    }
    else
    {
      $sCountQuery = ' SELECT count(distinct pos.positionpk) as nCount FROM position as pos';

      $sQuery = ' SELECT pos.*,cp.company_name,ind.name ,GROUP_CONCAT(pos1.lang SEPARATOR ",") as language, ind.status as indus_status FROM position as pos';
      $sQuery.= ' LEFT JOIN industry AS ind ON (ind.industrypk = pos.industryfk)';
      $sQuery.= ' LEFT JOIN company AS cp ON (cp.companypk = pos.companyfk) ';


      if($sAction == 'include' && !empty($sLanguage))
      {
        $sQuery.= ' INNER JOIN position AS pos1 ON (pos.positionpk = pos1.parentfk AND pos1.lang = "'.$sLanguage.'") ';
        $sCountQuery.= ' INNER JOIN position as pos1 ON (pos.positionpk = pos1.parentfk AND pos1.lang = "'.$sLanguage.'") ';
      }
      else
        $sQuery.= ' LEFT JOIN position AS pos1 ON (pos.positionpk = pos1.parentfk)';


      if($bSlistemOnly || CONST_WEBSITE == 'jobboard')
      {
        $sQuery.= ' LEFT JOIN job ON (job.jobpk = pos.jobfk) ';
        $sQuery.= ' WHERE pos.parentfk = 0 AND (job.websitefk = 5 OR pos.companyfk IN (226,113,292)) ';

        $sCountQuery.= ' LEFT JOIN job ON (job.jobpk = pos.jobfk) ';
        $sCountQuery.= ' WHERE pos.parentfk = 0 AND (job.websitefk = 5 OR pos.companyfk IN (226,113,292)) ';
      }
      else
      {
        $sQuery.= ' LEFT JOIN job ON (job.jobpk = pos.jobfk) ';
        $sQuery.= ' WHERE pos.parentfk = 0 AND ( job.jobpk IS NULL OR (job.websitefk <> 5 AND pos.companyfk NOT IN (226,113,292))) ';

        $sCountQuery.= ' LEFT JOIN job ON (job.jobpk = pos.jobfk) ';
        $sCountQuery.= ' WHERE pos.parentfk = 0 AND ( job.jobpk IS NULL OR (job.websitefk <> 5 AND pos.companyfk NOT IN (226,113,292))) ';
      }


      $sQuery.= ' GROUP BY pos.positionpk ';
      //$sCountQuery.= ' GROUP BY pos.positionpk ';

      if($sAction == 'exclude' && !empty($sLanguage))
      {
        $sCountQuery = 'SELECT count(distinct positionpk) as nCount FROM ('.$sQuery.') AS tbl WHERE ( tbl.language IS NULL OR tbl.language NOT LIKE "%'.$sLanguage.'%")';
        $sQuery = 'SELECT * FROM ('.$sQuery.') AS tbl WHERE ( tbl.language IS NULL OR tbl.language NOT LIKE "%'.$sLanguage.'%")';
      }
    }

    $sQuery.= $sOrder;
    $sQuery.= ' LIMIT '.$oPager->getSqlOffset().','.$oPager->getLimit();

    $sHTML = $oHTML->getBlocStart('',array('class'=>'homepageContainer'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'ta_user_list_container'));
    $sHTML.= $oHTML->getBlocStart('');
    $sHTML.= $oHTML->getTitleLine('Manage Positions', $this->getResourcePath().'/pictures/manage_48.png');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getCarriageReturn();

    if($bDisplayFilter)
    {
      $sHTML.= $oHTML->getBlocStart('',array('style'=>' margin: 10px 20px;'));
        $sHTML.= $oHTML->getBlocStart('',array('style'=>'float:left;width:200px;'));
          $sURL = $oPage->getURL('jobboard_user',CONST_ACTION_ADD, CONST_TA_TYPE_LIST_JOB);
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'imgClass'));
            $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/add_32.png','','', array('style' => 'height: 24px;'));
            $sHTML.= $oHTML->getLink($sPic, $sURL);
          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getLink('Add a new position',$sURL,array('style'=>'font-size:14px;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:200px;float:left;'));
        $sURL = $oPage->getURL('jobboard_user',CONST_ACTION_SEND, CONST_TA_TYPE_LIST_JOB);
        $sHTML.= $oHTML->getLink('Export Company List',$sURL);
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('',array('style'=>'float:right;min-width:400px;margin-right:10px;'));

        $bExclude = ($sAction == 'exclude');
        $bInclude = ($sAction == 'include');
        $bEnglish = ($sLanguage =='en');
        $bJapanese = ($sLanguage =='jp');
        $bPhilipino = ($sLanguage =='ph');

        if(CONST_WEBSITE != 'jobboard')
        {
          $sHTML.= '<select name="position_sort" onchange=" if($(this).val()){ document.location.href = $(this).val(); } " >';
            $sHTML.= '<option value="" > - Filter positions - </option>';
            $sHTML.= '<option value='.$oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB, 0, array('lang'=>'en','action'=>'exclude')).' '.((!$bSlistemOnly && $bExclude && $bEnglish)? ' selected="selected" ' : '').' > Need English description</option>';
            $sHTML.= '<option value='.$oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB, 0, array('lang'=>'jp','action'=>'exclude')).' '.((!$bSlistemOnly && $bExclude && $bJapanese)? ' selected="selected" ' : '').'> Need Japanese description</option>';
            $sHTML.= '<option value='.$oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB, 0, array('lang'=>'ph','action'=>'exclude')).' '.(($bSlistemOnly && $bExclude && $bPhilipino)? ' selected="selected" ' : '').'> Need Filipino description</option>';
            $sHTML.= '<option value="" > -- </option>';
            $sHTML.= '<option value='.$oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB, 0, array('lang'=>'en','action'=>'include')).' '.((!$bSlistemOnly && $bInclude && $bEnglish)? ' selected="selected" ' : '').'> Have English description</option>';
            $sHTML.= '<option value='.$oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB, 0, array('lang'=>'jp','action'=>'include')).' '.((!$bSlistemOnly && $bInclude && $bJapanese)? ' selected="selected" ' : '').'> Have Japanese description</option>';
            $sHTML.= '<option value='.$oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB, 0, array('lang'=>'ph','action'=>'include')).' '.(($bSlistemOnly && $bInclude && $bPhilipino)? ' selected="selected" ' : '').'> Have Filipino description</option>';
          $sHTML.= '</select>';

          $sHTML.= $oHTML->getSpace(3);
        }

          $sHTML.= '<select name="position_sort2" onchange=" if($(this).val()){ document.location.href = $(this).val(); } " >';
            $sHTML.= '<option value="" > - Slistem specific filters - </option>';
            $sHTML.= '<option value='.$oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB, 0, array('lang'=>'en', 'slistem' => 1, 'action'=>'exclude')).' '.(($bSlistemOnly && $bExclude && $bEnglish)? ' selected="selected" ' : '').' > Slistem - Need English description</option>';
            $sHTML.= '<option value='.$oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB, 0, array('lang'=>'jp', 'slistem' => 1, 'action'=>'exclude')).' '.(($bSlistemOnly && $bExclude && $bJapanese)? ' selected="selected" ' : '').' > Slistem - Need Japanese description</option>';
            $sHTML.= '<option value="" > -- </option>';
            $sHTML.= '<option value='.$oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB, 0, array('lang'=>'en', 'slistem' => 1, 'action'=>'include')).' '.(($bSlistemOnly && $bInclude && $bEnglish)? ' selected="selected" ' : '').'> Slistem - Have English description</option>';
            $sHTML.= '<option value='.$oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB, 0, array('lang'=>'jp', 'slistem' => 1, 'action'=>'include')).' '.(($bSlistemOnly && $bInclude && $bJapanese)? ' selected="selected" ' : '').'> Slistem - Have Japanese description</option>';
          $sHTML.= '</select>';

        $sHTML.= $oHTML->getBlocEnd();
      }

      $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getListStart('', array('class' => 'positionList ablistContainer'));
      $sHTML.= $oHTML->getListItemStart('', array('class' => 'ablistHeader'));
      $sHTML.= $this->_getJobsRowHeader();
      $sHTML.= $oHTML->getListItemEnd();


      //count positions
      $oResult = $oDB->ExecuteQuery($sCountQuery);
      $bRead = $oResult->readFirst();
      $nTotal = (int)$oResult->getFieldValue('nCount', 0);
      $asRecords = array();


    //if($nTotal> 0)
    if(isset($positionDataSlistem) && !empty($positionDataSlistem))
    {
      //ChromePhp::log($positionDataSlistem);
      //fetch positions
      /*$oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();
      while($bRead)
      {
        $asRecords[$oResult->getFieldValue('positionpk', CONST_PHP_VARTYPE_INT)] = $oResult->getData();
        $bRead = $oResult->readNext();
      }

//var_dump($asRecords);

      if(!empty($asRecords))
      {
        $sQuery = ' SELECT group_concat(CONCAT(pos1.lang,"|",pos1.positionpk,"|",pos1.visibility, "|",ind.status) SEPARATOR ",") as language, group_concat(pos1.lang SEPARATOR ",") as lg, pos1.positionpk FROM position as pos1';
        $sQuery.= ' LEFT JOIN industry as ind ON (ind.industrypk = pos1.industryfk) ';
        $sQuery.= ' WHERE pos1.positionpk IN ('.implode(',', array_keys($asRecords)).')';
        $sQuery.= ' GROUP BY pos1.positionpk ORDER BY pos1.positionpk DESC ';

        $oResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oResult->readFirst();
        $asChilds = array();

        while($bRead)
        {
          $asChilds[$oResult->getFieldValue('positionpk',CONST_PHP_VARTYPE_INT)][] = $oResult->getData();
          $bRead = $oResult->readNext();
        }
      }*/

      if(!empty($positionDataSlistem))
      {
        foreach($positionDataSlistem as $asJobDetail)
        {
          $sHTML.= $oHTML->getListItemStart();
          $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_row_data '));

            $sHTML.= $oHTML->getBlocStart('',array('class' => 'list_cell ','style' => ' width:10%;'));
            $sHTML.= $oHTML->getText('#'.$asJobDetail['external_key']);
            $sHTML.= $oHTML->getCarriageReturn();
            $sHTML.= $oHTML->getText($asJobDetail['posted_date']);
            $sHTML.= $oHTML->getBlocEnd();

            $sHTML.= $oHTML->getBlocStart('',array('class' => 'list_cell ','style' => ' width:15%;'));
            $sHTML.= $oHTML->getText($asJobDetail['position_title']);
            $sHTML.= $oHTML->getBlocEnd();

            $sHTML.= $oHTML->getBlocStart('',array('class' => 'list_cell ','style' => ' width:18%;'));
            $sHTML.= $oHTML->getText($asJobDetail['company_name']);
            $sHTML.= $oHTML->getCarriageReturn();

            $sHTML.= $oHTML->getBlocStart('',array('class' => 'list_cell ','style' => ' width:18%;'));//industry
            $sHTML.= $oHTML->getText($asJobDetail['name']);
            $sHTML.= $oHTML->getCarriageReturn();

            if((int)$asJobDetail['indus_status'] == 2)
              $sHTML.= $oHTML->getText('<em style="font-size: 0.8em;color:orange;">'.$asJobDetail['name'].' (need Trans.)</em>');
            else
            {
              if(isset($asJobDetail['name']) && !empty($asJobDetail['name']))
                $sHTML.= $oHTML->getText($oJobboard->getTranslation($asJobDetail['name']));
              else
                $sHTML.= $oHTML->getText($asJobDetail['temp_industry']);
            }
            $sHTML.= $oHTML->getBlocEnd();

            //---------------------------------------------------------------------
            //Actions based on the type (nothing: edit, share: social network ....)
            $sHTML.= $this->_getListActions($psType, $asJobDetail, $asChilds);

          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getListItemEnd();
        }
      }
    }

    $sLang = getValue('lang');
    if(isset($sLang) && !empty($sLang))
      $_SESSION['lang'] = $sLang;

    $sAction = getValue('action');
    if(isset($sAction) && !empty($sAction))
      $_SESSION['action'] = $sAction;

    $nbResult = getValue('nbResult');
    if(isset($nbResult) && !empty($nbResult))
      $_SESSION['myResult'] = $nbResult;

    $nPageOffset = getValue('pageoffset');
    if(isset($nPageOffset) && !empty($nPageOffset))
      $_SESSION['myoffset'] = $nPageOffset;

    $sUrl = $oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB, 0, array('lang' => $sLang,'action' => $sAction, 'slistem' => (int)$bSlistemOnly));

    if($nTotal > 0)
      $sHTML.= $oPager->getDisplay($nTotal, $sUrl);

    $sHTML.= $oHTML->getFloatHack();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }


  private function _getListActions($psType, $pasJobDetail, $pasChild)
  {
    switch($psType)
    {
      case 'share':  return $this->_getListShareActions($pasJobDetail, $pasChild); break;
      default:
        return $this->_getListModerateActions($pasJobDetail, $pasChild); break;
    }

    return '';
  }

  private function _getListModerateActions($pasJobDetail, $pasChild)
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sJavascript = "if( jQuery(this).val() != '') ";
    $sJavascript.= "  $(this).closest('div').find('.add_position').show();";
    $sJavascript.= "else";
    $sJavascript.= "  $('.add_position').hide();";

    //get a list of links, 1 for each avalable languages, to the add position form
    $asLangArray = $this->getMyLanguages($pasJobDetail['positionpk']);
    $asLang = array();

    //find wich languages are available for the select field (add new description)
    if(isset($pasChild[$pasJobDetail['positionpk']]) && !empty($pasChild[$pasJobDetail['positionpk']]))
    {
      foreach($asLang as $asVirtualLang)
      {
        if(in_array($asVirtualLang['lg'], $asLangArray));
          unset($asLangArray[$asVirtualLang['lg']]);
      }
    }

    $sHTML = $oHTML->getBlocStart('', array('class' => 'list_cell', 'style' => ' width:43%;'));

      $sHTML.= $oHTML->getBlocStart('',array('style' => 'float:left;width:115px;'));
        if(!empty($asLangArray))
        {
          $sHTML.= '<select name="select_lang" onchange="'.$sJavascript.'">';
          $sHTML.= '<option value="" class=""> Select </option>';
          foreach($asLangArray as $asSelectLang)
          {
            $sHTML.= '<option value = "'.$asSelectLang['value'].'">'.$asSelectLang['label'].'</option>';
          }
          $sHTML.= '</select>';
        }
        $sJavascript = 'document.location.href = $(this).parent().find(\'select\').val();';

        $sHTML.= $oHTML->getSpace(2);
        $sPic = $oHTML->getPicture(CONST_PICTURE_ADD, 'Create Position','',array('class'=>'add_position hidden'));
        $sHTML.= $oHTML->getLink($sPic,'',array('onclick'=>$sJavascript));

      $sHTML.= $oHTML->getBlocEnd();

      // Edit position links
      $sHTML.= $oHTML->getBlocStart('', array('style'=>'float:left;'));

      if(isset($pasChild[$pasJobDetail['positionpk']]) && !empty($pasChild[$pasJobDetail['positionpk']]))
      {
        foreach($pasChild[$pasJobDetail['positionpk']] as $asEachLang)
        {
          //data: 0=> lang, 1=> pk, 2=> visibility, 3=> indus status
          $asMyLang = explode('|',$asEachLang['language']);

          $sHTML.= $oHTML->getBlocStart('',array('class'=>'editLangClass'));

            if((int)$asMyLang[3] != 1)
            {
              $sHTML.= $oHTML->getText($asMyLang[0].' !', array('title' => 'Industry need translation', 'style' => 'cursor: help; color: red;'));
            }
            else
              $sHTML.= $oHTML->getText($asMyLang[0]);

            //Edit Position
            $sURL =  $oPage->getUrl('jobboard_user', CONST_ACTION_EDIT, CONST_TA_TYPE_LIST_JOB, (int)$asMyLang[1], array('lang'=>$asMyLang[0],'child'=>1));
            $sPic =  $oHTML->getPicture(CONST_PICTURE_EDIT, 'Edit Position');
            $sHTML.= $oHTML->getLink($sPic, $sURL);

            //View Position
            if((int)$asMyLang[2] == 2)
            {
              $sLegend = 'Prioritary position - preview';
            }
            elseif((int)$asMyLang[2] == 1)
            {
              $sLegend = 'Standard position - preview';
            }
            else
              $sLegend = 'Draft position - preview (not public)';

            $sURL =  $oPage->getUrl($this->csBoardComponent, CONST_ACTION_VIEW, CONST_TA_TYPE_JOB,(int)$asMyLang[1],array('setLang'=>$asMyLang[0]));
            $sPic =  $oHTML->getPicture($this->getResourcePath().'pictures/view_'.$asMyLang[2].'.png', $sLegend);
            $sHTML.= $oHTML->getLink($sPic, $sURL, array('target'=>'_blank', 'style' => 'cursor: help;'));
          $sHTML.= $oHTML->getBlocEnd();
        }
      }

      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('',array('style'=>'float:right;'));
        $sUrl = $oPage->getAjaxUrl('jobboard_user', CONST_ACTION_DELETE, CONST_TA_TYPE_LIST_JOB,(int)$pasJobDetail['positionpk']);
        $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/delete_24.png', 'Delete position');
        $sHTML.= $oHTML->getLink($sPic, $sUrl, array('onclick' => 'if(!window.confirm(\'Are you sure to delete this position ?\')){ return false; }'));
      $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   *Display actions and link to share the position on social network
   * @param type $pasJobDetail
   * @param type $pasChild
   * @return type
   */
  private function _getListShareActions($pasJobDetail)
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $sHTML = '';

    if(!empty($pasJobDetail['shared_platform']))
    {
      $sHTML = $oHTML->getBlocStart('', array('class' => 'list_cell', 'style' => 'width:20%; float: left;'));
      $sHTML.= ' shared on <strong>'.$pasJobDetail['shared_platform'].'</strong> ';
      $sHTML.= $oHTML->getBlocEnd();
    }


    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell', 'style' => 'width:5%; float: right;'));

      $sUrl = $oPage->getUrl('jobboard_user', CONST_ACTION_ADD, CONST_TA_TYPE_SHARE_JOB, (int)$pasJobDetail['positionpk']);
      $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/share_16.png', 'Share on social networks');
      $sHTML.= $oHTML->getLink($sPic, $sUrl);

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Function to save created position
   * @param integer $pnPositionPk
   * @return array
   */

  private function _getSavePosition($pnPositionPk)
  {
    if(!assert('is_integer($pnPositionPk)'))
      return 'No position obtained.';

    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');

    $sLangauge      = getValue('job_language');
    $sPositionTitle = getValue('position_title');
    $sPositionDesc  = getValue('position_desc');
    $sRequirements  = getValue('requirements');
    $sCareerLevel   = getValue('career_level');
    $nCompany       = (int)getValue('company', 0);
    $sCompany       = getValue('company_name');
    $sCompanyLabel  = getValue('company_label', NULL);
    $sLocation      = getValue('location');
    $sPostedDate    = getValue('posted_date');
    $nJobType       = getValue('job_type');
    $sSalary        = getValue('salary');
    $nSalaryLow     = getValue('salary_low');
    $nSalaryHigh    = getValue('salary_high');
    $nEnglishLevel  = getValue('english');
    $nJapaneseLevel = getValue('japanese');
    $nIndustry      = getValue('industry');
    $sHolidays      = getValue('holidays');
    $sStation       = getValue('station');
    $sWorkHours     = getValue('work_hours');
    $nVisibility    = getValue('visibility');
    $nCategory      = getValue('category');
    $nToJobboard    = (int)getValue('to_jobboard', 0);
    $sExpirationDate= getValue('expiration_date');

    $sPageTitle     = getValue('page_title');
    $sMetaDesc = getValue('meta_desc');
    $sMetaKeywords = getValue('meta_keywords');


    if(empty($nCompany) && empty($sCompany))
      return array('error' => 'You have to select a company or input a new company name');

    if(!empty($nCompany) && !empty($sCompany))
      return array('error' => 'You have selected a company AND want to create a new one. Make up your mind.');

    if(!empty($sCompany))
    {
      if(strlen($sCompany) < 3)
        return array('error' => 'Company name should contain at least 3 characters.');

      $sQuery = 'INSERT INTO company (company_name, status) VALUES ('.$oDB->dbEscapeString($sCompany).',1)';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $nCompany = $oDbResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT);

      if(empty($nCompany))
        return array('error' => 'Sorry an error occured: couldn\'t create a new company.');
    }

    if(empty($sPageTitle) || empty($sMetaDesc) || empty($sMetaKeywords))
      return array('error' => 'All the meta tags are required. Please input a page title, keywords and meta description.');


    //Get the page title
    if(CONST_WEBSITE == 'talentAtlas')
    {
      if(!preg_match("/^(talentatlas|talent atlas)(.*)/i", $sPageTitle))
        $sPageTitle = 'Talent Atlas: '.$sPageTitle;
    }
    else
    {
      if(!preg_match("/^(Slate job board)(.*)/i", $sPageTitle))
        $sPageTitle = 'Slate job board: '.$sPageTitle;
    }


    //Checkthe meta keywords
    $sMetaKeywords = str_replace(array('/', '\\', ')', '(', ']', '[', '*', '.', '\'', '"', '{', '}', '&'), ' ', $sMetaKeywords);

    $asMetaKeywords = explode(',', $sMetaKeywords);
    if(count($asMetaKeywords) > 15)
      return array('error' => 'Too many keywords.');

    if(count($asMetaKeywords) < 3)
      return array('error' => 'Not enough keywords.');

    //For each keyword found in the description, we add a strong tag to improve SEO
    if(!isJapanese($sPositionDesc))
    {
      foreach($asMetaKeywords as $sKeyword)
      {
        $sKeyword = trim($sKeyword);
        $nKWLength = strlen($sKeyword);
        if($nKWLength > 2)
        {

          //echo 'checking this keyword: '.$sKeyword.'<br/>';
          if($nKWLength < 5)
          {
            //echo 'simple replace ';
            $sPositionDesc = preg_replace('/[^a-z0-9>]'.$sKeyword.'[^a-z0-9<] /i', '<strong class="seo_keyword">'.$sKeyword.'</strong>', $sPositionDesc);
            //dump(htmlentities($sPositionDesc));
          }
          else
          {
            $asMatches = array();
            preg_match_all('/[^a-z0-9>]('.$sKeyword.'[a-z0-9]{0,6})[^a-z0-9>]/i', $sPositionDesc, $asMatches);
            //dump($asMatches);
            if(!empty($asMatches[0]))
            {
              //echo '['.$sKeyword.'] ===> ';
              $asMatches = array_unique($asMatches[0]);
              foreach($asMatches as $sMatch)
              {
                if(strlen($sMatch) > 2)
                {
                  //echo 'match: '.$sMatch.'<br />';
                  $sSeoOptimized = @preg_replace('/'.trim($sMatch).'/i', '<strong class="seo_keyword">'.trim($sMatch).'</strong>', $sPositionDesc);
                  if($sSeoOptimized === null)
                  {
                    assert('false; /* error preg_replace for ['.$sMatch.']*/');
                  }
                  else
                    $sPositionDesc = $sSeoOptimized;
                }
              }
            }
          }

        }
      }
    }


    //Get the meta description
    if(strlen($sMetaDesc) > 200)
      $sMetaDesc = substr($sMetaDesc, 0, 197).'...';

    if(!empty($pnPositionPk))
    {
      $sQuery = 'SELECT * FROM position WHERE positionpk = '.$pnPositionPk.' AND parentfk != 0 ';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      if($bRead)
      {
        $sQuery = 'UPDATE position SET visibility = '.$oDB->dbEscapeString($nVisibility).',';
        $sQuery.= ' category = '.$oDB->dbEscapeString($nCategory).',';
        $sQuery.= ' career_level = '.$oDB->dbEscapeString($sCareerLevel).',';
        $sQuery.= ' company_label = '.$oDB->dbEscapeString($sCompanyLabel).',';
        $sQuery.= ' position_title = '.$oDB->dbEscapeString($sPositionTitle).',';
        $sQuery.= ' position_desc = '.$oDB->dbEscapeString($sPositionDesc).',';
        $sQuery.= ' requirements = '.$oDB->dbEscapeString($sRequirements).',';
        $sQuery.= ' posted_date = '.$oDB->dbEscapeString($sPostedDate).',';
        $sQuery.= ' location = '.$oDB->dbEscapeString($sLocation).',';
        $sQuery.= ' job_type = '.$oDB->dbEscapeString($nJobType).',';
        $sQuery.= ' salary = '.$oDB->dbEscapeString($sSalary).',';
        $sQuery.= ' salary_low = '.$oDB->dbEscapeString($nSalaryLow).',';
        $sQuery.= ' salary_high = '.$oDB->dbEscapeString($nSalaryHigh).',';
        $sQuery.= ' english = '.$oDB->dbEscapeString($nEnglishLevel).',';
        $sQuery.= ' japanese = '.$oDB->dbEscapeString($nJapaneseLevel).',';
        $sQuery.= ' industryfk = '.$oDB->dbEscapeString($nIndustry).',';
        $sQuery.= ' holidays = '.$oDB->dbEscapeString($sHolidays).',';
        $sQuery.= ' station = '.$oDB->dbEscapeString($sStation).',';
        $sQuery.= ' work_hours = '.$oDB->dbEscapeString($nCategory).',';
        $sQuery.= ' page_title = '.$oDB->dbEscapeString($sPageTitle).',';
        $sQuery.= ' meta_keywords = '.$oDB->dbEscapeString($sMetaKeywords).',';
        $sQuery.= ' meta_desc = '.$oDB->dbEscapeString($sMetaDesc).',';
        $sQuery.= ' to_jobboard = '.$oDB->dbEscapeString($nToJobboard).',';
        $sQuery.= ' expiration_date = '.$oDB->dbEscapeString($sExpirationDate).'';
        $sQuery.= ' WHERE positionpk  = '.$pnPositionPk ;
      }
      else
      {
        $sQuery = 'INSERT INTO `position`(`visibility`, `category`, `career_level`, `company_label`, `position_title`,  ';
        $sQuery.= ' `position_desc`, `requirements`, `companyfk`, `posted_date`, `location`, `job_type`,';
        $sQuery.= ' `salary`, `salary_low`, `salary_high`, `english`, `japanese`, `industryfk`, `holidays`, `station`, `work_hours`, ';
        $sQuery.= ' `lang`, `parentfk`,`page_title`,`meta_keywords`,`meta_desc`, `to_jobboard`, `expiration_date`) VALUES (';
        $sQuery.= ''.$oDB->dbEscapeString($nVisibility).','.$oDB->dbEscapeString($nCategory).','.$oDB->dbEscapeString($sCareerLevel).','.$oDB->dbEscapeString($sCompanyLabel, 'NULL').',';
        $sQuery.= ''.$oDB->dbEscapeString($sPositionTitle).','.$oDB->dbEscapeString($sPositionDesc).','.$oDB->dbEscapeString($sRequirements).',';
        $sQuery.= ''.$oDB->dbEscapeString($nCompany).','.$oDB->dbEscapeString($sPostedDate).',';
        $sQuery.= ''.$oDB->dbEscapeString($sLocation).','.$oDB->dbEscapeString($nJobType).','.$oDB->dbEscapeString($sSalary).',';
        $sQuery.= ''.$oDB->dbEscapeString($nSalaryLow).','.$oDB->dbEscapeString($nSalaryHigh).','.$oDB->dbEscapeString($nEnglishLevel).',';
        $sQuery.= ''.$oDB->dbEscapeString($nJapaneseLevel).','.$oDB->dbEscapeString($nIndustry).','.$oDB->dbEscapeString($sHolidays).',';
        $sQuery.= ''.$oDB->dbEscapeString($sStation).','.$oDB->dbEscapeString($sWorkHours).','.$oDB->dbEscapeString($sLangauge).',';
        $sQuery.= ''.$oDB->dbEscapeString($pnPositionPk).','.$oDB->dbEscapeString($sPageTitle).','.$oDB->dbEscapeString($sMetaKeywords).',';
        $sQuery.= ''.$oDB->dbEscapeString($sMetaDesc).', '.$oDB->dbEscapeString($nToJobboard).', '.$oDB->dbEscapeString($sExpirationDate).')';
      }

      $oDbResult = $oDB->ExecuteQuery($sQuery);

      if(isset($_SESSION['action']) && isset($_SESSION['lang']) && isset($_SESSION['myResult']) && isset($_SESSION['myoffset']))
        $sURL = $oPage->getUrl('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB,0,array('lang' => $_SESSION['lang'], 'action' => $_SESSION['action'],'nbresult'=> $_SESSION['myResult'],'pageoffset'=> $_SESSION['myoffset']));
      else
        $sURL = $oPage->getURL('jobboard_user', CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB);

      if($oDbResult)
        return array('url' => $sURL, 'notice'=>'Position edited successfully');
    }
    else
    {
      //Insert first without the language
      $sQuery = 'INSERT INTO `position`(`visibility`, `category`, `career_level`, `company_label`, `position_title`, ';
      $sQuery.= ' `position_desc`, `requirements`, `companyfk`,`posted_date`, `location`, `job_type`, `salary`,';
      $sQuery.= ' `salary_low`, `salary_high`, `english`, `japanese`, `industryfk`, `holidays`, `station`,';
      $sQuery.= ' `work_hours`,`page_title`,`meta_keywords`,`meta_desc`, `to_jobboard`) VALUES (';
      $sQuery.= ''.$oDB->dbEscapeString($nVisibility).','.$oDB->dbEscapeString($nCategory).','.$oDB->dbEscapeString($sCareerLevel).','.$oDB->dbEscapeString($sCompanyLabel, 'NULL').',';
      $sQuery.= ''.$oDB->dbEscapeString($sPositionTitle).','.$oDB->dbEscapeString($sPositionDesc).','.$oDB->dbEscapeString($sRequirements).',';
      $sQuery.= ''.$oDB->dbEscapeString($nCompany).','.$oDB->dbEscapeString($sPostedDate).',';
      $sQuery.= ''.$oDB->dbEscapeString($sLocation).','.$oDB->dbEscapeString($nJobType).','.$oDB->dbEscapeString($sSalary).',';
      $sQuery.= ''.$oDB->dbEscapeString($nSalaryLow).','.$oDB->dbEscapeString($nSalaryHigh).','.$oDB->dbEscapeString($nEnglishLevel).',';
      $sQuery.= ''.$oDB->dbEscapeString($nJapaneseLevel).','.$oDB->dbEscapeString($nIndustry).','.$oDB->dbEscapeString($sHolidays).',';
      $sQuery.= ''.$oDB->dbEscapeString($sStation).','.$oDB->dbEscapeString($sWorkHours).','.$oDB->dbEscapeString($sPageTitle).',';
      $sQuery.= ''.$oDB->dbEscapeString($sMetaKeywords).','.$oDB->dbEscapeString($sMetaDesc).','.$oDB->dbEscapeString($nToJobboard).'';
      $sQuery.= ')';

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $nInsertPk = $oDbResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT);

      //Second time with the language
      $sQuery = 'INSERT INTO `position`(`visibility`, `category`, `career_level`, `company_label`, `position_title`, ';
      $sQuery.= ' `position_desc`, `requirements`, `companyfk`, `posted_date`, `location`, `job_type`, `salary`,';
      $sQuery.= ' `salary_low`, `salary_high`, `english`, `japanese`, `industryfk`, `holidays`, `station`, `work_hours`, ';
      $sQuery.= ' `lang`, `parentfk`,`page_title`,`meta_keywords`,`meta_desc`, `to_jobboard`, `expiration_date`) VALUES (';
      $sQuery.= ''.$oDB->dbEscapeString($nVisibility).','.$oDB->dbEscapeString($nCategory).','.$oDB->dbEscapeString($sCareerLevel).','.$oDB->dbEscapeString($sCompanyLabel, 'NULL').',';
      $sQuery.= ''.$oDB->dbEscapeString($sPositionTitle).','.$oDB->dbEscapeString($sPositionDesc).','.$oDB->dbEscapeString($sRequirements).',';
      $sQuery.= ''.$oDB->dbEscapeString($nCompany).','.$oDB->dbEscapeString($sPostedDate).',';
      $sQuery.= ''.$oDB->dbEscapeString($sLocation).','.$oDB->dbEscapeString($nJobType).','.$oDB->dbEscapeString($sSalary).',';
      $sQuery.= ''.$oDB->dbEscapeString($nSalaryLow).','.$oDB->dbEscapeString($nSalaryHigh).','.$oDB->dbEscapeString($nEnglishLevel).',';
      $sQuery.= ''.$oDB->dbEscapeString($nJapaneseLevel).','.$oDB->dbEscapeString($nIndustry).','.$oDB->dbEscapeString($sHolidays).',';
      $sQuery.= ''.$oDB->dbEscapeString($sStation).','.$oDB->dbEscapeString($sWorkHours).','.$oDB->dbEscapeString($sLangauge).',';
      $sQuery.= ''.$oDB->dbEscapeString($nInsertPk).','.$oDB->dbEscapeString($sPageTitle).',';
      $sQuery.= ''.$oDB->dbEscapeString($sMetaKeywords).','.$oDB->dbEscapeString($sMetaDesc).','.$oDB->dbEscapeString($nToJobboard).',';
      $sQuery.= ''.$oDB->dbEscapeString($sExpirationDate).'';
      $sQuery.= ')';

      $oDbResult = $oDB->ExecuteQuery($sQuery);

      if(isset($_SESSION['action']) && isset($_SESSION['lang']) && isset($_SESSION['myResult']) && isset($_SESSION['myoffset']))
        $sURL = $oPage->getUrl('jobboard_user', CONST_ACTION_LIST,CONST_TA_TYPE_LIST_JOB,0,array('lang' => $_SESSION['lang'],'action' => $_SESSION['action'],'nbresult'=> $_SESSION['myResult'],'pageoffset'=> $_SESSION['myoffset']));
      else
        $sURL = $oPage->getURL('jobboard_user',CONST_ACTION_LIST,CONST_TA_TYPE_LIST_JOB);

      if($oDbResult)
        return array('url' => $sURL,'notice' => 'Position created successfully');

      }
    }

  /**
   * Function to show the job header
   * @return string
   */

  public function _getJobsRowHeader($psSearchId = '')
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sURL = $oPage->getUrl($this->csUid, CONST_ACTION_LIST, CONST_TA_TYPE_LIST_JOB, 0, array('searchId' => $psSearchId));


    $sHTML = $oHTML->getBlocStart('', array('class' =>'list_row '));
    $sHTML.= $oHTML->getBlocStart('', array('class' =>'list_row_data'));

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell', 'style' => 'width:9%;'));
      $sSortUrl = $sURL.'&sortfield=external_key&sortorder=desc';
      $sHTML.= $oHTML->getLink('Position ID', $sSortUrl);
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell', 'style' => 'width:11%;'));
      $sHTML.= $oHTML->getText('Position Title ');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell', 'style' => 'width:15%;'));
      $sSortUrl = $sURL.'&sortfield=company_name&sortorder=asc';
      $sHTML.= $oHTML->getLink('Company', $sSortUrl);
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell', 'style' => 'width:15%;'));
      $sHTML.= $oHTML->getText('Industry');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'width:30%;'));
      $sHTML.= $oHTML->getText('Action');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Function to create/edit form
   * @param type $pnPk
   * @return string
   */

  private function _getJobEditForm($pnPk)
  {
    if(!assert('is_integer($pnPk)'))
      return 'Wrong data obtained.';

    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');

    $sJobLang = getValue('lang');
    $nChild = (int)getValue('child', 0);
    $sRealPageUrl = '';

    if(!empty($pnPk))
    {
      if(!empty($nChild))
      {
        $sQuery = 'SELECT url FROM website_joburl AS wj ';
        $sQuery.= ' INNER JOIN job AS jb ON (jb.weburlfk = wj.website_joburlpk)';
        $sQuery.= ' INNER JOIN position AS pos ON (pos.jobfk = jb.jobpk) ';
        $sQuery.= ' INNER JOIN position as post ON (post.parentfk = pos.positionpk) ';
        $sQuery.= ' WHERE post.positionpk = '.$pnPk;
      }
      else
      {
        $sQuery = 'SELECT url FROM website_joburl AS wj INNER JOIN job AS jb ON (jb.weburlfk = wj.website_joburlpk)';
        $sQuery.= ' INNER JOIN position AS pos ON (pos.jobfk = jb.jobpk) WHERE pos.positionpk = '.$pnPk;
      }

      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();
      if($bRead)
        $sRealPageUrl = $oResult->getFieldValue('url');
    }

    $asVisibilityArray = array(0 => 'Draft (hidden)', 1 => 'Published', 2 => 'Top Priority');

    $oJobboard = CDependency::getComponentByName($this->csBoardComponent);
    $oPage->addCssFile(array($this->getResourcePath().'css/jobboard_user.css'));


    //Trying to update a translated position or creating a new translation, parent pk provided
    // we pre-load the form
    $sQuery = 'SELECT pos.*, jb.raw_content, cp.company_name, jb.jobpk, jb.websitefk FROM position as pos ';
    $sQuery.= ' LEFT JOIN company as cp ON (cp.companypk = companyfk AND cp.status > 0) ';
    $sQuery.= ' LEFT JOIN job as jb ON (pos.jobfk = jb.jobpk) WHERE positionpk = '.$pnPk;

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    if(!$bRead)
    {
      $asRecord = array('visibility'=>'','category'=>'','position_title'=>'','position_desc'=>'','requirements'=>'','career_level'=>'',
          'companyfk'=>'','company_name'=>'','company_label'=>'','location'=>'','posted_date'=>'','job_type'=>'','salary'=>'','salary_low'=>'',
          'salary_high'=>'','status'=>'0','english'=>'','japanese'=>'','industryfk'=>'','holidays'=>'','station'=>'','work_hours'=>'',
          'raw_content'=>'','page_title'=>'','meta_desc'=>'','meta_keywords'=>'','temp_industry'=>'', 'websitefk' => 0, 'to_jobboard' => 0,
          'expiration_date' => '');

      $sURL = $oPage->getAjaxURL('jobboard_user', CONST_ACTION_SAVEADD, CONST_TA_TYPE_LIST_JOB);
    }
    else
    {
      $asRecord = $oResult->getData();

      if(empty($asRecord['page_title']))
        $asRecord['page_title'] = cutStringByWords($asRecord['position_title'], 5);

      if(empty($asRecord['meta_desc']))
        $asRecord['meta_desc'] = cutStringByWords(strip_tags($asRecord['position_desc']), 20);

      if(empty($asRecord['meta_keywords']))
      {
        $sText = $asRecord['position_title'].' '.$asRecord['position_desc'];
        $sText = str_replace(array('\n', "\r\n","\n", "\r"), ' ', $sText);
        $asRecord['meta_keywords'] = $this->_getMetaKeywords(strip_tags($sText), 10);
      }

      $sURL = $oPage->getAjaxURL('jobboard_user', CONST_ACTION_SAVEEDIT, CONST_TA_TYPE_LIST_JOB, $pnPk);
    }

    $sHTML = $oHTML->getBlocStart('', array('class'=>'homepageContainer'));
    $sHTML.= $oHTML->getBlocStart('', array('style'=>'margin:0 auto;'));

    //Left section
    $sHTML.= $oHTML->getBlocStart('', array('class'=>'jobContainerClass'));
    $oPage->addCssFile(array($this->getResourcePath().'css/jobboard_user.css'));

    $oForm = $oHTML->initForm('posEditForm');
    $oForm->setFormParams('', true, array('submitLabel' => 'Save', 'action' => $sURL));

    $oForm->addField('misc', 'title', array('type'=>'text', 'text'=>'<strong> Edit the Position details </strong>'));


    $asLangArray = $this->getMyLanguages();
    if(!$nChild)
    {
      $oForm->addField('select', 'job_language', array('label' => 'Language'));

      foreach($asLangArray as $skey => $avLanguages)
      {
        if($skey == $sJobLang)
          $oForm->addOption('job_language', array('value'=> $skey, 'label' => $avLanguages['label'] ,'selected'=>'selected'));
        else
          $oForm->addOption('job_language', array('value'=> $skey, 'label' => $avLanguages['label'] ));
      }
    }
    else
    {
      $sLanguage = $asRecord['lang'];
      $oForm->addField('input', 'help_language', array('label' => 'This position is in', 'value' => $asLangArray[$sLanguage]['label'].' (not editable)', 'disabled' => 'disabled', 'style' =>'background-color: #dedede; font-style: italic;'));
    }

    $oForm->addField('select', 'visibility', array('label' => 'Publishing status'));
    $nVisibilty = $asRecord['visibility'];

    foreach($asVisibilityArray as $nVisibile => $sVisibile)
    {
      if($nVisibilty == $nVisibile)
        $oForm->addOption('visibility', array('value'=> $nVisibile, 'label' => $sVisibile,'selected'=>'selected'));
      else
        $oForm->addOption('visibility', array('value'=> $nVisibile, 'label' => $sVisibile));
    }


    if(empty($asRecord['expiration_date']))
      $asRecord['expiration_date'] = date('Y-m-d', strtotime('+3 months'));

    $oForm->addField('input', 'expiration_date', array('type' => 'date', 'label' => 'Expiration date', 'value' => $asRecord['expiration_date'], 'style' => 'width: 280px;'));


    //possibility to push a non slistem position to the job board
    if((int)$asRecord['websitefk'] == 5 || (int)$asRecord['to_jobboard'] == 1)
    {
      $oForm->addField('input', 'to_jobboard', array('type'=>'hidden', 'value' => 1));
    }
    else
    {
      $oForm->addField('select', 'to_jobboard', array('label'=>'Add to Slate Jobboard', 'onchange' => 'if($(this).val() && !$(this).attr(\'alerted\')){ alert(\'Are you sure you want to send this position to Slate jobboard ? \'); }'));
      $oForm->addOption('to_jobboard', array('value'=> 0, 'label' => 'Not a slate position'));
      $oForm->addOption('to_jobboard', array('value'=> 1, 'label' => 'Display on Slate jobboard'));
    }

    $oForm->addField('misc', '', array('type' => 'br'));


    if(empty($pnPk))
    {
      $oForm->addField('selector', 'company', array('label'=> 'Select an existing company', 'url' => $oPage->getAjaxURL('jobboard_user',CONST_ACTION_LIST,CONST_LIST_COMPANY)));
      $oForm->setFieldControl('company', array('jsFieldTypeIntegerPositive' => ''));

      $oForm->addField('input', 'company_name', array('label'=>'or create a new one', 'class' => '', 'value' => ''));
      $oForm->setFieldControl('company_name', array('jsFieldMinSize' => '4', 'jsFieldMaxSize' => 255));
    }
    else
    {
      $oForm->addField('input', 'company_display_name', array('label'=>'Company name', 'disabled' => 'disabled', 'style' =>'background-color: #dedede; font-style: italic;',  'value' => $asRecord['company_name'].'   (non editable)'));
      $oForm->addField('hidden', 'company', array('value' => $asRecord['companyfk']));
    }

    if(empty($asRecord['company_label']))
      $asRecord['company_label'] = 'Company name not publicy visible';

    $oForm->addField('input', 'company_label', array('label'=> 'Displayed company name', 'class' => '', 'value' => $asRecord['company_label']));
    $oForm->setFieldControl('company_label', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255));

    $sCompanyHelp = '<div class="position_form_salary_help" style="top: -60px;">If the company name is sensitive, please input the text that will be displayed instead on the jobboard. A generic description of the company is recommended. Example: Leading electronic company in Japan, International bank & insurance company ...</div><br /> ';
    $oForm->addField('misc', '', array('type' => 'text', 'text'=> $sCompanyHelp));

    $oForm->addField('input', 'position_title', array('label'=>'Position Title', 'class' => '', 'value' => $asRecord['position_title']));
    $oForm->setFieldControl('position_title', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255, 'jsFieldNotEmpty' => ''));

    $sText = str_replace('\n', "\n", strip_tags($asRecord['position_desc']));
    $oForm->addField('textarea', 'position_desc', array('label'=>'Position Description', 'class' => 'description', 'value' => $sText));
    $oForm->setFieldControl('position_desc', array('jsFieldMinSize' => '2', 'jsFieldNotEmpty' => ''));

    $sText = str_replace('\n', "\n", strip_tags($asRecord['requirements']));
    $oForm->addField('textarea', 'requirements', array('label'=>'Requirements', 'class' => 'description', 'value' => $sText));
    $oForm->setFieldControl('requirements', array('jsFieldMinSize' => '2', 'jsFieldNotEmpty' => ''));

    $oForm->addField('input', 'career_level', array('label'=>'Career Level', 'class' => '', 'value' => $asRecord['career_level']));
    $oForm->setFieldControl('career_level', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255));



    $oForm->addField('input', 'location', array('label'=>'Location', 'class' => '', 'value' => $asRecord['location']));
    $oForm->setFieldControl('location', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255, 'jsFieldNotEmpty' => ''));

    $oForm->addField('input', 'posted_date', array('type'=>'date', 'label'=>'Posted Date', 'class' => '', 'value' =>  $asRecord['posted_date'],'monthNum'=>1));
    $oForm->setFieldControl('posted_date', array('jsFieldDate' => ''));

    $oForm->addField('select', 'job_type', array('label' => 'Job Type'));
    $nJobType = $asRecord['job_type'];

    if($nJobType == 1)
    {
      $oForm->addOption('job_type', array('value'=> 1, 'label' => 'Full time','selected'=>'selected'));
      $oForm->addOption('job_type', array('value'=> 0, 'label' => 'Part time'));
     }
    else
    {
      $oForm->addOption('job_type', array('value'=> 1,'label' => 'Full time'));
      $oForm->addOption('job_type', array('value'=> 0, 'label' => 'Part time','selected'=>'selected'));
     }

    $oForm->addField('input', 'salary', array('label'=>'Salary (displayed)', 'class' => '', 'value' => $asRecord['salary']));
    $oForm->setFieldControl('salary', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255, 'jsFieldNotEmpty' => ''));

    $oForm->addField('input', 'salary_low', array('label'=>'Lower salary (in ¥)', 'class' => '', 'value' => $asRecord['salary_low']));
    $oForm->addField('input', 'salary_high', array('label'=>'Higher salary (in ¥)', 'class' => '', 'value' => $asRecord['salary_high']));

    $sSalaryHelp = '<div class="position_form_salary_help" style="top: -105px;">Salary:<br />The displayed salary is a text value where you can write any range of salary and any other information such as "negocialble", or "to be discussed", "starting from"...
      In those cases where the salary isn\'t a simple value, you have to estimated a low and high values that the search engine will use while searching.<br />Example: "3 Mil. yen + Bonus. Negociable" => lower salary: 3 000 000, higher: 4 000 000  </div><br /> ';
    $oForm->addField('misc', '', array('type' => 'text', 'text'=> $sSalaryHelp));

    $oForm->addField('select', 'english', array('label' => 'English Level'));

    $asLanguage= $oJobboard->getLanguages();
    $nLanguage = $asRecord['english'];

    foreach($asLanguage as $nValue=>$vType)
    {
      if($nLanguage==$nValue)
        $oForm->addOption('english', array('value'=>$nValue, 'label' => $vType,'selected'=>'selected'));
      else
        $oForm->addOption('english', array('value'=>$nValue, 'label' => $vType));
    }

    $oForm->addField('select', 'japanese', array('label' => 'Japanese Level'));
    $asJapLanguage = $oJobboard->getLanguages();
    $nLanguage = $asRecord['japanese'];

    foreach($asJapLanguage as $nValue=>$vType)
    {
      if($nLanguage == $nValue)
        $oForm->addOption('japanese', array('value' => $nValue, 'label' => $vType,'selected'=>'selected'));
      else
        $oForm->addOption('japanese', array('value' => $nValue, 'label' => $vType));
    }

    $sIndustryName = $asRecord['temp_industry'];
    $asIndustries = $oJobboard->getIndustries(0, false, false, true);

    $oForm->addField('select', 'industry', array('label' => 'Industry'));
    $nIndustry = $asRecord['industryfk'];

    $oForm->addOption('industry', array('value'=> '', 'label' => 'Select Industries'));
    $nParentIndus = 0;
    foreach($asIndustries as $nValue => $avType)
    {
      if((int)$avType['status'] == 2)
      {
        $sLabel = $avType['name'].' (need trans.)';
        $sStyle = ' color: red; ';
      }
      else
      {
        $sLabel = $oJobboard->getTranslation($avType['name']);
        $sStyle = '';
      }

      if($nIndustry == $avType['industrypk'])
      {
        $oForm->addOption('industry', array('value' => $avType['industrypk'], 'label' => $sLabel, 'selected'=>'selected', 'style' => $sStyle));

        if((int)$avType['status'] == 2)
          $nParentIndus = $avType['parentfk'];
      }
      else
        $oForm->addOption('industry', array('value' => $avType['industrypk'], 'label' => $sLabel, 'style' => $sStyle));
     }

    $oForm->setFieldControl('industry', array('jsFieldNotEmpty' => ''));

    $sIndustryTip = '';
    if(!empty($sIndustryName))
      $sIndustryTip = 'Industry found by the job aggregator: <em style="color: blue;">'.$sIndustryName.'</em>.<br /> ';

    if($nParentIndus)
    {
      $sIndustryTip.= 'If not changed, it needs to be <em style="color: blue;">translated</em> to be displayed on the jobboard. ';
      $sIndustryTip.= 'For the time being it will be displayed as the parent category <strong style="color: green;">'.$oJobboard->getTranslation($asIndustries[$nParentIndus]['name']).'</strong>';
    }
    else
      $sIndustryTip.= 'If you need industries that are not displayed in the current list, please request it by email <a href="mailto:sboudoux@bulbouscell.com">here</a>.';

    $oForm->addField('misc','',array('type' => 'text','text' => '<div class="position_form_salary_help" style="top: -65px;">'.$sIndustryTip.'</div>'));


    $oForm->addField('input', 'holidays', array('label' => 'Holidays', 'class' => '', 'value' => $asRecord['holidays']));
    $oForm->setFieldControl('holidays', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255));

    $oForm->addField('input', 'station', array('label' => 'Nearest Station', 'class' => '', 'value' => $asRecord['station']));
    $oForm->setFieldControl('station', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255));

    $oForm->addField('input', 'work_hours', array('label' => 'Work Hours', 'class' => '', 'value' => $asRecord['work_hours']));


    $sTip = '<div class="position_form_salary_help">Search engine optimization:<br />
      The 3 last fields are required to make the position appear properly in Google searches.<br />
      - Page title is referring to the text that appears in the tab of your web browser when looking at the position.<br />
      - Keywords is a "," separated list of the most important words found in the position description. (3-10 words or groups of words)<br />
      - Description is a short description of the content of the page. In our case it should most of the time be an extract of the description. (about 20 words)<br />
      </div><br />';
    $oForm->addField('misc', '', array('type' => 'text', 'text'=> $sTip));

    $oForm->addField('input', 'page_title', array('label' => 'Page Title', 'class' => '', 'value' => $asRecord['page_title']));
    $oForm->setFieldControl('page_title', array('jsFieldNotEmpty' => ''));

    $oForm->addField('textarea', 'meta_keywords', array('label'=> 'Meta Keywords ', 'class' => 'description', 'style' => 'max-height: 50px;', 'value' => $asRecord['meta_keywords']));
    $oForm->setFieldControl('meta_keywords', array('jsFieldNotEmpty' => ''));

    $sText = str_replace(array('\n', "\r\n","\n", "\r"), ' ', $asRecord['meta_desc']);
    $oForm->addField('textarea', 'meta_desc', array('label' => 'Meta Description', 'class' => 'description', 'value' => $sText));
    $oForm->setFieldControl('meta_desc', array('jsFieldNotEmpty' => ''));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getFloatHack();


    //Div for controlling Iframe
    if(!empty($sRealPageUrl))
    {
      $sHTML.= $oHTML->getBlocStart('frame',array('class'=>'iframeBar'));

        $sHTML.= $oHTML->getBlocStart('closeiframe', array('class'=>'hidden','style'=>' cursor: pointer;'));
        $sHTML.= $oHTML->getText('Close',array('style'=>'font-weight:bold; z-index:1000;'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('openiframe', array('style'=>' cursor: pointer;', 'title' => 'View original position'));
        $sHTML.= $oHTML->getSpace();
        $sHTML.= $oHTML->getBlocEnd();

        $sJavascript = '
          $(document).ready(function()
          {
            $("#openiframe").click(function()
            {
              if($("#originalViewFrame").attr("src") != "'.$sRealPageUrl.'")
                $("#originalViewFrame").attr("src", "'.$sRealPageUrl.'");

              $(".position_form_salary_help").fadeOut(function()
              {
                $("link[href=\'/component/jobboard_user/resources/css/taljobboard_users\']").attr({href : "/component/jobboard_user/resources/css/jobboard_user_new.css"});

                $("#originalViewFrame").fadeIn();
                $("#closeiframe").fadeIn();
              });
            });

            $("#closeiframe").click(function()
            {
              $("#originalViewFrame").fadeOut(function()
              {
                $(".position_form_salary_help").fadeIn();
              });

              $("link[href=\'/component/jobboard_user/resources/css/jobboard_user.css\']").attr({href : "/component/jobboard_user/resources/css/jobboard_user.css"});
              $("#closeiframe").hide();
            });
          }); ';

        $oPage->addCustomJs($sJavascript);
        $sHTML.= $oHTML->getText('<iframe src="" id="originalViewFrame" height="660" width="780" border="0" style="display:none;"></iframe>');

      $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();
    }

    // Close holding divs
    $sHTML.= $oHTML->getFloatHack();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getFloatHack();

    return $sHTML;
  }

  /**
   * Function for the company autocomplete list
   * @return type
   */

  private function _getCompanySelector()
  {
    $sSearch = getValue('q');
    if(empty($sSearch))
      return json_encode(array());

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM company WHERE status = 1 AND lower(company_name) LIKE '.$oDB->dbEscapeString('%'.strtolower($sSearch).'%').' ORDER BY company_name desc';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return json_encode(array());

    $asJsonData = array();
    while($bRead)
    {
      $asData['name'] = $oDbResult->getFieldValue('company_name');
      $asJsonData[] = json_encode($asData);
      $bRead = $oDbResult->readNext();
    }
    echo '['.implode(',', $asJsonData).']';
  }

  /**
   * Function to remove the jobs
   * @param type $pnPositionPk
   * @return array
  */
  private function _getRemoveJobs($pnPositionPk)
  {
    if(!assert('is_integer($pnPositionPk) && !empty($pnPositionPk)'))
      return array('error' => 'No Position Obtained. It may have already been deleted.');

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM position WHERE positionpk = '.$pnPositionPk.' ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array('error' => __LINE__.' - No position to delete.');

    $sQuery = 'DELETE FROM position WHERE parentfk = '.$pnPositionPk.' ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    if(!$oDbResult)
      return array('error' => __LINE__.' - Couldn\'t delete the position');

    $sQuery = 'DELETE FROM position WHERE positionpk = '.$pnPositionPk.' ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    if(!$oDbResult)
      return array('error' => __LINE__.' - Couldn\'t delete the position');

    return array('notice' => 'Position has been deleted.', 'reload' => 1);
  }


   /**
    * Function to send the company information
    */
   private function _getCompanySend()
   {
     $oDB = CDependency::getComponentByName('database');

     $sQuery = 'SELECT cp.company_name AS company_name,count(pos.positionpk) AS nCount FROM company AS cp INNER JOIN position AS pos WHERE cp.companypk = pos.companyfk AND cp.status != 0 and pos.visibility != 0 GROUP BY company_name ORDER BY company_name ASC';
     $oDbResult = $oDB->ExecuteQuery($sQuery);
     $bRead = $oDbResult->readFirst();

     if($bRead)
     {
       $asCompanyRecords = array();
       while($bRead)
       {
          $asCompanyRecords[] =  $oDbResult->getData();
          $bRead = $oDbResult->readNext();
       }

       if(!empty($asCompanyRecords))
       {
         $fp = fopen('company.csv', 'w');

          foreach($asCompanyRecords as $skey =>$asCompany)
          {
            fputcsv($fp, $asCompany);
          }
          fclose($fp);

          $sFileName = 'company.csv';

          if(headers_sent())
            exit(__LINE__.' Headers already sent');

          // Required for some browsers
          if(ini_get('zlib.output_compression'))
            ini_set('zlib.output_compression', 'Off');

          $ext = 'csv';

          // Determine Content Type
          switch ($ext)
          {
            case "pdf":  $ctype="application/pdf"; break;
            case "exe":  $ctype="application/octet-stream"; break;
            case "zip":  $ctype="application/zip"; break;
            case "doc":  $ctype="application/msword"; break;
            case "docx": $ctype="application/msword"; break;
            case "csv":  $ctype="application/vnd.ms-excel"; break;
            case "xls":  $ctype="application/vnd.ms-excel"; break;
            case "xlsx": $ctype="application/vnd.ms-excel"; break;
            case "ppt":  $ctype="application/vnd.ms-powerpoint"; break;
            case "pptx": $ctype="application/vnd.ms-powerpoint"; break;
            case "gif":  $ctype="image/gif"; break;
            case "png":  $ctype="image/png"; break;
            case "jpeg":
            case "jpg": $ctype="image/jpg"; break;
            default: $ctype="application/force-download";
          }

          header("Pragma: public"); // required
          header("Expires: 0");
          header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
          header("Cache-Control: private",false); // required for certain browsers
          header("Content-Type: $ctype");
          header("Content-Disposition: attachment; filename=\"".basename($sFileName)."\";" );
          header("Content-Transfer-Encoding: binary");

          ob_clean();
          flush();
          readfile($sFileName);

          exit();
       }
     }
   }

   /**
    * Function to get the languages
    * @param type $pnPositionPk
    * @return array
    */
   public function getMyLanguages($pnPositionPk = 0, $psAction = CONST_ACTION_EDIT, $psType = CONST_TA_TYPE_LIST_JOB)
   {
     $oPage = CDependency::getComponentByName('page');
     $asLangArray = array();

     $asLangArray = array('en'=> array('label'=>'English','lg'=>'en', 'value' => $oPage->getUrl('jobboard_user', $psAction, $psType,(int)$pnPositionPk,array('lang'=>'en'))),
                          'jp'=> array('label'=>'Japanese','lg'=>'jp', 'value' => $oPage->getUrl('jobboard_user', $psAction, $psType,(int)$pnPositionPk,array('lang'=>'jp'))),
                          'ph'=> array('label'=>'Filipino','lg'=>'ph', 'value' => $oPage->getUrl('jobboard_user', $psAction, $psType,(int)$pnPositionPk,array('lang'=>'ph'))));

     return $asLangArray;
   }


  /**
  * Very basic function that picks up some words from the description
  * @param type $psString
  * @param type $pnNumber
   *
   * return a comma separated list of words
  */
  function _getMetaKeywords($psString, $pnWords = 20)
  {
    if(!assert('!empty($psString) && is_integer($pnWords)'))
      return '';

    $psString = preg_replace('/[^a-zA-Z]/', ' ', $psString);
    $psString = str_replace('  ', ' ', $psString);
    $asWords = explode(' ', $psString);

    $asWords = array_unique($asWords);
    uasort($asWords, array($this, '_getSortWords'));

    return implode(', ', array_slice($asWords, 0, $pnWords));
  }

  private function _getSortWords($pvElement1, $pvElement2)
  {
    $nLength1 = strlen($pvElement1);
    $nLength2 = strlen($pvElement2);

    if($nLength1 == $nLength2)
      return 0;

    return ($nLength1 < $nLength2) ? 1 : -1;
  }

  private function _getJobSharingForm($pnPk)
  {
    if(!assert('is_integer($pnPk) && !empty($pnPk)'))
      return '';

    $asJobData = $this->getPositionByPk($pnPk);
    if(empty($asJobData))
      return 'couldn\'t find the position to share.';

    $asJobShare = $this->getPositionSharingByPk($pnPk);

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oSocialNet = CDependency::getComponentByName('socialnetwork');

    $oPage->addCssFile($this->getResourcePath().'css/jobboard_user.css');

    $sPositionUrl = $oPage->getUrl($this->csBoardComponent, CONST_ACTION_VIEW, CONST_TA_TYPE_JOB, $pnPk, array(CONST_PAGE_NO_LOGGEDIN_CSS => 1));

    $sHtml = '';
    $sHtml.= $oHTML->getBlocStart('', array('style' => 'min-width: 1050px; border: 1px solid #ddd; margin: 5px 25px; height: 350px; overflow: hidden;'));
    $sHtml.= '<iframe src="'.$sPositionUrl.'" border="0" width="99%" height="350" style="overflow-x: hidden; "></iframe>';
    $sHtml.= $oHTML->getBlocEnd();

    //add a link to login in facebook
    $bLoggedInFb = $oSocialNet->isLoggedInFacebook();
    if(!$bLoggedInFb)
    {
      $sHtml.= '<br /><strong>Login to facebook to post positions there. <a href="'.$oSocialNet->getFacebookLoginLink($pnPk).'" >Log in</a></strong><br />';
    }

    //add a link to login in LinkedIn
    $bLoggedInLi = $oSocialNet->isLoggedInLinkedIn();
    if(!$bLoggedInLi)
    {
      $sHtml.= '<br /><strong>Login to linkedIn to post positions there. <a href="'.$oSocialNet->getLinkedinLoginLink($pnPk).'" >Log in</a></strong><br />';
    }

    $sUrl = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_SAVEADD, CONST_TA_TYPE_SHARE_JOB, $pnPk);
    $sContent = $asJobData['position_title']."\n".$asJobData['position_desc'];

    $oForm = $oHTML->initForm('posShareForm');
    $oForm->setFormParams('', true, array('submitLabel' => 'Share', 'action' => $sUrl));

    if(isset($asJobShare['facebook']))
    {
      $oForm->addField('misc', '', array('type' => 'text', 'text' =>'<br /><br /><strong>Already shared on Facebook</strong>'));
    }
    elseif($bLoggedInFb)
    {
      $oForm->addField('misc', '', array('type' => 'text', 'text' =>'<br /><br /><hr /><strong>Facebook</strong>'));
      $oForm->addField('checkbox', 'fbShare', array('label' => 'Check to share on facebook', 'onchange' => '$(this).closest(\'.innerForm\').find(\'.fbField\').fadeToggle(); '));


      $oForm->addField('textarea', 'fbContent', array('label' => 'Content', 'class' => 'sharingContentField', 'value' => $sContent));
      $oForm->setFieldDisplayParams('fbContent', array('class' => 'fbField hidden'));

      if(strlen($asJobData['position_title']) > 40)
        $sLinkLabel = substr($asJobData['position_title'], 0, 37).'...';
      else
        $sLinkLabel = $asJobData['position_title'];

      $oForm->addField('input', 'fbLink', array('value' => $sPositionUrl));
      $oForm->setFieldDisplayParams('fbLink', array('class' => 'fbField hidden'));

      $oForm->addField('input', 'fbLinkLabel', array('value' => $sLinkLabel));
      $oForm->setFieldDisplayParams('fbLinkLabel', array('class' => 'fbField hidden'));

      $oForm->addField('input', 'fbPicture', array('value' => 'http://jobs.slate.co.jp/component/socialnetwork/resources/facebook/slate_facebook.png'));
      $oForm->setFieldDisplayParams('fbPicture', array('class' => 'fbField hidden'));

      $oForm->addField('input', 'fbCaption', array('value' => $asJobData['position_title']));
      $oForm->setFieldDisplayParams('fbCaption', array('class' => 'fbField hidden'));
    }

    if(isset($asJobShare['linkedin']))
    {
      $oForm->addField('misc', '', array('type' => 'text', 'text' =>'<br /><br /><strong>Already shared on LinkedIn</strong>'));
    }
    elseif($bLoggedInLi)
    {
      $oForm->addField('misc', '', array('type' => 'text', 'text' =>'<br /><br /><strong>LinkedIn</strong>'));
      $oForm->addField('checkbox', 'liShare', array('label' => 'Check to share on LinkedIn', 'onchange' => '$(this).closest(\'.innerForm\').find(\'.liField\').fadeToggle(); '));

      $oForm->addField('textarea', 'liComment', array('label' => 'Content', 'class' => 'sharingContentField', 'value' => $sContent));
      $oForm->setFieldDisplayParams('liComment', array('class' => 'liField hidden'));

      if(strlen($asJobData['position_title']) > 40)
        $sTitle = substr($asJobData['position_title'], 0, 37).'...';
      else
        $sTitle = $asJobData['position_title'];

      $oForm->addField('textarea', 'liTitle', array('label' => 'Title', 'class' => 'sharingContentField', 'value' => $sTitle));
      $oForm->setFieldDisplayParams('liTitle', array('class' => 'liField hidden'));

      $oForm->addField('textarea', 'liDescription', array('label' => 'Description', 'class' => 'sharingContentField', 'value' => $asJobData['position_desc']));
      $oForm->setFieldDisplayParams('liDescription', array('class' => 'liField hidden'));

      $oForm->addField('textarea', 'liLink', array('label' => 'Link', 'class' => 'sharingContentField', 'value' => $sPositionUrl));
      $oForm->setFieldDisplayParams('liLink', array('class' => 'liField hidden'));

      $oForm->addField('textarea', 'liPicture', array('label' => 'Picture', 'class' => 'sharingContentField', 'value' => 'http://jobs.slate.co.jp/component/socialnetwork/resources/linkedin/slate_linkedin.png'));
      $oForm->setFieldDisplayParams('liPicture', array('class' => 'liField hidden'));
    }

    $oForm->addField('misc', '', array('type' => 'text', 'text' =>'<br /><br />'));
    $sHtml.= $oForm->getDisplay();
    return $sHtml;
  }

  private function _shareJob()
  {
    $nPositionPk = (int)getValue(CONST_URL_PK, 0);
    if(empty($nPositionPk))
      return array('error' => 'No position id');

    $asPosition = $this->getPositionByPk($nPositionPk);
    if(empty($asPosition))
      return array('error' => 'Can not find the position #'.$nPositionPk);

    $oPage = CDependency::getComponentByName('page');
    $oSocialMedia = CDependency::getComponentByName('socialnetwork');
    $oDb = CDependency::getComponentByName('database');
    $asNotice = array();


    //----------------------------------------------------------
    //----------------------------------------------------------
    //Post on facebook

    $bShareFb = (bool)getValue('fbShare');
    $sContentFb = getValue('fbContent');

    if($bShareFb && !empty($sContentFb))
    {
      $bAdded = $oSocialMedia->addFacebookWallPost();
      if(!$bAdded)
        return array('error' => 'An error occured, impossible to post on facebbok for now.');

      $sQuery = 'INSERT INTO position_share (positionfk, platform, date) VALUES ('.$nPositionPk.', "facebook", "'.date('Y-m-d H:i:s').'")';
      $oDbResult = $oDb->ExecuteQuery($sQuery);
      if(!$oDbResult)
        return array('error' => 'Job posted on facebook but could not saved it locally.');


      $asNotice[] = 'Job posted on facebook.';
    }



    //----------------------------------------------------------
    //----------------------------------------------------------
    //Post on linkedIn

    $sContentLi = getValue('liComment');
    $bShareLi = (bool)getValue('liShare');

    if($bShareLi && !empty($sContentLi))
    {
      $asResult = $oSocialMedia->addLinkedinPost();
      if(isset($asResult['error']))
        return array('error' => 'An error occured, impossible to post on linkedIn for now. Details: '.$asResult['error']);

      $sQuery = 'INSERT INTO position_share (positionfk, platform, date) VALUES ('.$nPositionPk.', "linkedIn", "'.date('Y-m-d H:i:s').'")';
      $oDbResult = $oDb->ExecuteQuery($sQuery);
      if(!$oDbResult)
        return array('error' => 'Job posted on linkedIn but could not saved it locally.');


      $asNotice[] = 'Job posted on linkedIn.';
    }


    //----------------------------------------------------------
    //result

    if(empty($asNotice))
      return array('notice' => 'nothing has been posted');

    return array('notice' => implode('<br />', $asNotice), 'reload' => 1);
  }

  /**
   * Get a position data by its primary key
   * @param integer $pnPk
   * @return array with all the position data
   */
  public function getPositionByPk($pnPk)
  {
    if(!assert('is_integer($pnPk) && !empty($pnPk)'))
      return array();

    $oDb = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM position WHERE positionpk = '.$pnPk.' ';
    $oDbResult = $oDb->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array();

    return $oDbResult->getData();
  }

  /**
   * Get a position data, including the neworks it's been shared on by its primary key
   * @param integer $pnPk
   * @return array with all the position data
   */
  public function getPositionSharingByPk($pnPk)
  {
    if(!assert('is_integer($pnPk) && !empty($pnPk)'))
      return array();

    $oDb = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM position_share WHERE positionfk = '.$pnPk.' ';

    $oDbResult = $oDb->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array();

    $asShares = array();
    while($bRead)
    {
      $asShares[$oDbResult->getFieldValue('platform')] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asShares;
  }

  private function _importPositions($language = 'en')
  {
    $start_time = strtotime('-2 year');
    $end_time = strtotime('+1 month');

    $url = 'https://slistem.slate.co.jp/index.php5?pg=cron&cronSilent=1&hashCron=1&custom_uid=555-005&export_position=1&';
    $url .= 'cronSilent=1&language='.$language.'&start_time='.$start_time.'&end_time='.$end_time;
    // $url .= 'cronSilent=1&language='.$language.'&start_time=1363593824&end_time=1363598000';

    echo '<br /><br />Load positions from: '.$url.'   <br />';

    try
    {
      $xml = simplexml_load_file($url);
      if(!$xml)
        throw new Exception('Xml is stream empty.');
    }
    catch(Exception $exception)
    {
      echo $exception->getMessage();
      /*dump('XML received from '.$url.':');
      dump(file_get_contents($url));*/
      return false;
    }

    $db_object = CDependency::getComponentByName('database');
    $jobboard_object = CDependency::getComponentByName('jobboard');
    $industry_list = $jobboard_object->getIndustries(0, false, false, true);
    $company_list = $jobboard_object->getCompanies();

    $sExpirationDate = date('Y-m-d', strtotime('+3 months'));

    $new_position = $xml->new_position;

    foreach($new_position->position as $position_data)
    {
      $position_data = (array)$position_data;

      //we store TA positionpk in the external_key field
      echo '<br />Check if position '.$position_data['position_id'].' exist';
      $query = 'SELECT positionpk FROM position WHERE external_key = '.$position_data['position_id'].' ';
      $db_result = $db_object->ExecuteQuery($query);
      $read = $db_result->readFirst();
      if(!$read)
      {
        //we made sure the position hasn't been treated yet, we check if the parent is already here
        //check if a sister (other translation) exists using the parentfk
        //in this case, we hget the JB parentpk back
        /*echo '<br />Check if position '.$position_data['position_id'].' has a parent ['.$position_data['parentfk'].'] already here ';
        $sQuery = 'SELECT positionpk FROM position WHERE parentfk = 0 AND external_key = '.$position_data['parentfk'].' ';
        $oDbResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oDbResult->readFirst();
        if($bRead)
        {
          $nParentPK = (int)$oDbResult->getFieldValue('positionpk');
          echo '<br />Parent exist: pk = '.$nParentPK;
        }*/
          //in the other case, we create a new parent  position
          $error = false;
          // echo '<br />Parent doesnt exist: we need to create it';

          if(!isset($company_list[$position_data['company_id']]))
          {
            echo '<br />need to create a company: '.$position_data['company_id'].' / '.$position_data['company_name'];

            $new_company_id = $this->_addCompany( (int)$position_data['company_id'], $position_data['company_name']);
            $position_data['company_id'] = (int)$new_company_id;

            if(!$new_company_id)
            {
              $error = true;
              assert('false; // need to create a company to sync the jobboaard and TA ');
            }
          }

          if(!isset($industry_list[$position_data['industry_id']]))
          {
            if(empty($position_data['industry_parent']))
            {
              $error = true;
              assert('false; // Cannot create an "hidden" industry in the jobboard if there\'s no parentfk. ');
            }
            else
            {
              //create a hidden industry (status 2) to match TA DB
              $create_industry = addIndustry((int)$position_data['industry_id'], $position_data['industry_name'], 2, $position_data['industry_parent']);
              if($create_industry)
              {
                echo '<br />Created a new "hidden" industryto match TA:'.$position_data['industry_id'].' - '.$position_data['industry_name'].' - '.$position_data['industry_parent'];
              }
              else
              {
                assert('false; // Error: Could not create an "hidden" industry in the jobboard  ');
              }
            }
          }

          if(!$error)
          {
            $query = 'INSERT INTO `job`(`data`, `date_create`, `websitefk`) VALUES ';
            $query.= ' ('.$db_object->dbEscapeString($position_data['data']).', "'.date('Y-m-d H:i:s').'", 5) ';

            $db_result = $db_object->ExecuteQuery($query);
            $job_id = (int)$db_result->getFieldValue('pk');
            if(!$db_result || empty($job_id))
            {
              echo '<br />'.$query.'<br /><br />';
              assert('false; //'.__LINE__.' - error, could not create the job from TA to the joabbord');
            }
            else
            {
              $query = 'INSERT INTO `position`(`jobfk`, `status`, `visibility`, `category`, `career_level`, `position_title`,';
              $query.= ' `position_desc`, `requirements`, `companyfk`, `posted_date`, `location`, `job_type`,';
              $query.= ' `salary`, `salary_low`, `salary_high`, `english`, `japanese`, `industryfk`, `holidays`, `station`, `work_hours`, ';
              $query.= ' `lang`, `parentfk`,`page_title`,`meta_keywords`,`meta_desc`, `company_label`, `external_key`) VALUES (';

              $query.= ''.$db_object->dbEscapeString($job_id).', 1 ,';
              $query.= '1,'.$db_object->dbEscapeString($position_data['category']).', ';
              $query.= ''.$db_object->dbEscapeString($position_data['career']).','.$db_object->dbEscapeString($position_data['position_title']).', ';
              $query.= ''.$db_object->dbEscapeString($position_data['position_desc']).','.$db_object->dbEscapeString($position_data['requirements']).', ';
              $query.= ''.$db_object->dbEscapeString($position_data['company_id']).','.$db_object->dbEscapeString($position_data['date_created']).', ';
              $query.= ''.$db_object->dbEscapeString($position_data['location']).','.$db_object->dbEscapeString($position_data['job_type']).', ';
              $query.= ''.$db_object->dbEscapeString($position_data['salary']).','.$db_object->dbEscapeString($position_data['salary_low']).', ';
              $query.= ''.$db_object->dbEscapeString($position_data['salary_high']).','.$db_object->dbEscapeString($position_data['english']).', ';
              $query.= ''.$db_object->dbEscapeString($position_data['japanese']).','.$db_object->dbEscapeString($position_data['industry_id']).', ';
              $query.= ''.$db_object->dbEscapeString($position_data['holidays']).','.$db_object->dbEscapeString($position_data['station']).', ';
              $query.= ''.$db_object->dbEscapeString($position_data['work_hours']).','.$db_object->dbEscapeString($language).', ';
              $query.= ' 0, '.$db_object->dbEscapeString($position_data['page_title']).', ';
              $query.= ''.$db_object->dbEscapeString($position_data['meta_keywords']).','.$db_object->dbEscapeString($position_data['meta_desc']).',';
              $query.= ''.$db_object->dbEscapeString($position_data['company_name']).', '.$db_object->dbEscapeString($position_data['position_id']).' ';
              $query.= ')';

              $db_result = $db_object->ExecuteQuery($query);
              $parent_id = (int)$db_result->getFieldValue('pk');
              if(!$db_result || empty($parent_id))
              {
                assert('false; //'.__LINE__.' - error, could not create position from TA to the joabbord');
              }
              /*else
                echo '<br /> Parent position Created successfully ! ';*/
            }
          }

        //no  matter if it s a new of old parent position, we should have a parentpk here
        /*if(!empty($nParentPK))
        {
          $sQuery = 'INSERT INTO `position`(`jobfk`, `status`,`visibility`, `category`, `career_level`, `position_title`,';
          $sQuery.= ' `position_desc`, `requirements`, `companyfk`, `posted_date`, `location`, `job_type`,';
          $sQuery.= ' `salary`, `salary_low`, `salary_high`, `english`, `japanese`, `industryfk`, `holidays`, `station`, `work_hours`, ';
          $sQuery.= ' `lang`, `parentfk`,`page_title`,`meta_keywords`,`meta_desc`, `company_label`, `external_key`, `expiration_date`) VALUES (';

          $sQuery.= ' 0, 1 ,';
          $sQuery.= '1,'.$oDB->dbEscapeString($asPositionData['category']).', ';
          $sQuery.= ''.$oDB->dbEscapeString($asPositionData['career_level']).','.$oDB->dbEscapeString($asPositionData['position_title']).', ';
          $sQuery.= ''.$oDB->dbEscapeString($asPositionData['position_desc']).','.$oDB->dbEscapeString($asPositionData['requirements']).', ';
          $sQuery.= ''.$oDB->dbEscapeString($asPositionData['companyfk']).','.$oDB->dbEscapeString($asPositionData['posted_date']).', ';
          $sQuery.= ''.$oDB->dbEscapeString($asPositionData['location']).','.$oDB->dbEscapeString($asPositionData['job_type']).', ';
          $sQuery.= ''.$oDB->dbEscapeString($asPositionData['salary']).','.$oDB->dbEscapeString($asPositionData['salary_low']).', ';
          $sQuery.= ''.$oDB->dbEscapeString($asPositionData['salary_high']).','.$oDB->dbEscapeString($asPositionData['english']).', ';
          $sQuery.= ''.$oDB->dbEscapeString($asPositionData['japanese']).','.$oDB->dbEscapeString($asPositionData['industryfk']).', ';
          $sQuery.= ''.$oDB->dbEscapeString($asPositionData['holidays']).','.$oDB->dbEscapeString($asPositionData['station']).', ';
          $sQuery.= ''.$oDB->dbEscapeString($asPositionData['work_hours']).','.$oDB->dbEscapeString($asPositionData['lang']).', ';
          $sQuery.= ''.$oDB->dbEscapeString($nParentPK).','.$oDB->dbEscapeString($asPositionData['page_title']).', ';
          $sQuery.= ''.$oDB->dbEscapeString($asPositionData['meta_keywords']).', '.$oDB->dbEscapeString($asPositionData['meta_desc']).',';
          $sQuery.= ''.$oDB->dbEscapeString($asPositionData['company_label']).', '.$oDB->dbEscapeString($asPositionData['positionpk']).',';
          $sQuery.= ''.$oDB->dbEscapeString($sExpirationDate).'';
          $sQuery.= ')';

          $oDbResult = $oDB->ExecuteQuery($sQuery);
          if(!$oDbResult)
          {
            assert('false; // '.__LINE__.' - error, could not create position from TA to the joabbord');
          }
          else
            echo '<br /> Translation Created successfully !<br /> ';
        }*/
      }
      else
      {
        echo '<br />position TA['.$position_data['position_id'].'] already exists JB['.$db_result->getFieldValue('positionpk').'] ';
      }
    }

    echo 'imported';
    return true;
  }


  private function _addCompany($pnCompanyPk, $psCompanyName)
  {
    if(!assert('is_integer($pnCompanyPk) && !empty($pnCompanyPk) && !empty($psCompanyName)'))
      return 0;

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'INSERT INTO company (companypk, company_name, status) ';
    $sQuery.= 'VALUES ('.$oDB->dbEscapeString($pnCompanyPk).', '.$oDB->dbEscapeString($psCompanyName).', 1) ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);

    if(!$oDbResult)
      return 0;

    return $oDbResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT);
  }


  private function _notifyExpiration()
  {
    $oDB = CDependency::getComponentByName('database');

    $sDate = date('Y-m-d', strtotime('+5 days'));

    $sQuery = 'SELECT pos.*, cp.company_name, ind.name AS industry_name, ind_parent.name as parent_industry, job.data ';
    $sQuery.= ' FROM position AS pos  ';
    $sQuery.= ' LEFT JOIN company AS cp ON (cp.companypk = pos.companyfk) ';
    $sQuery.= ' LEFT JOIN industry AS ind ON (pos.industryfk = ind.industrypk) ';
    $sQuery.= ' LEFT JOIN industry AS ind_parent ON (ind_parent.industrypk = ind.parentfk) ';

    $sQuery.= ' LEFT JOIN position as parent_pos ON (parent_pos.positionpk = pos.parentfk) ';
    $sQuery.= ' LEFT JOIN job ON (job.jobpk = parent_pos.jobfk) ';

    $sQuery.= ' WHERE pos.visibility <> 0 AND pos.parentfk <> 0 AND pos.expiration_date > "'.$sDate.' 00:00:00" AND pos.expiration_date <= "'.$sDate.' 23:59:59" ' ;

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
    {
      echo '<br /> no job expiring today ';
      return true;
    }

    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');

    //store in an array what notification to send to each consultant
    while($bRead)
    {
      $asData = $oDbResult->getData();

      $asRawData = unserialize($asData['data']);

      if(is_array($asRawData) && isset($asRawData['cons_email']) && !empty($asRawData['cons_email']))
      {
        $sEmail = $asRawData['cons_email'];
        $sName = $asRawData['cons_name'];
      }
      else
      {
        $sEmail = 'info@slate.co.jp';
        $sName = 'Job board admin';
      }

      $asJobs[$sEmail]['name'] = $sName;

      $sUrl = $oPage->getUrl('jobboard', CONST_ACTION_VIEW, CONST_TA_TYPE_JOB, (int)$asData['positionpk']);
      $sContent = 'Position '.$oHTML->getLink('#'.$asData['positionpk'], $sUrl);
      $sContent.= ' will expire in 5 days. ';

      $sContent.= $oHTML->getCarriageReturn();
      $sContent.= 'Title: '.$asData['position_title'];

      $sContent.= $oHTML->getCarriageReturn();
      $sContent.= 'Published: '.$asData['posted_date'];

      $asJobs[$sEmail]['jobs'][] = $sContent;

      $bRead = $oDbResult->readNext();
    }

    $oMail = CDependency::getComponentByName('mail');

    foreach($asJobs as $sEmail => $asJobData)
    {
      $oMail->creatNewEmail();
      $oMail->setFrom(CONST_CRM_MAIL_SENDER, 'Slate job board');

      $oMail->addRecipient($sEmail, $asJobData['name']);
      //echo 'supposely sent to oMail->addRecipient('.$sEmail.', '.$asJobData['name'].')<br />';
      //$oMail->addRecipient('sboudoux@bulbouscell.com', 'stef');


      $sContent = 'Dear '.$asJobData['name'].', <br /><br />';
      $sContent.= count($asJobData['jobs']).' of your position(s) are going to expire from the jobboard soon. Please contact the <a href="mailto:info@slate.co.jp">moderator</a> if you want those to be extended. <br/> <br />';
      $sContent.= ' List of position(s):<br/><br/>';
      $sContent.= implode('<br />', $asJobData['jobs']);

      $oMail->send('Slate job board: position(s) expiring soon', $sContent);
    }

    return true;
  }

}
