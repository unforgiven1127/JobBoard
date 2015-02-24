<?php

require_once('component/sharedspace/sharedspace.class.php5');

class CSharedspaceEx extends CSharedspace
{
  public function __construct()
  {
    return true;
  }

  public function getDefaultType()
  {
    return CONST_SS_TYPE_DOCUMENT;
  }

  public function getDefaultAction()
  {
    return CONST_ACTION_LIST;
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
    switch($this->csType)
    {
      case CONST_SS_TYPE_DOCUMENT:

        /*@var $oPage CPageEx */
        $oPage = CDependency::getComponentByName('page');
        $sPictureMenuPath = $this->getResourcePath().'/pictures/menu/';

        //always displayed: list, add
        $asActions['ppal'][] = array('picture' => $sPictureMenuPath.'doc_list_32.png','title'=>'List Documents', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_SS_TYPE_DOCUMENT));
        $asActions['ppaa'][] = array('picture' => $sPictureMenuPath.'doc_add_32.png','title'=>'Add Documents', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_ADD, CONST_SS_TYPE_DOCUMENT));
        $asActions['ppam'][] = array('picture' => $sPictureMenuPath.'doc_manage_32.png','title'=>'Manage Documents', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_MANAGE, CONST_SS_TYPE_DOCUMENT));

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
     case CONST_SS_TYPE_DOCUMENT:

       switch($this->csAction)
        {
          case CONST_ACTION_DELETE:
           echo json_encode($this->_getDocumentDelete($this->cnPk));
            exit();
            break;
        }
        break;
    }
  }


  public function getCronJob()
   {
    $this->_processUrl();
    echo 'Sharedspace cron <br />';

    //notify users a document has been shared with them
    $this->_getCronDocument();

  }

  //====================================================================
  //  public methods
  //====================================================================

  public function getHtml()
  {
    $this->_processUrl();

    switch($this->csType)
    {
      case CONST_SS_TYPE_DOCUMENT:

        switch($this->csAction)
        {
          case CONST_ACTION_ADD:
          case CONST_ACTION_EDIT:
           return $this->_getDocumentForm($this->cnPk);
            break;

          case CONST_ACTION_SAVEADD:
          case CONST_ACTION_SAVEEDIT:
           return $this->_getDocumentSave($this->cnPk);
            break;

          case CONST_ACTION_SEND:
           $this->_getDocumentSend($this->cnPk);
            break;

          case CONST_ACTION_MANAGE:
           return $this->_getManageDocument();
            break;


          case CONST_ACTION_LIST:
          default:
            return $this->_getDocumentList();
              break;
        }
        break;
    }
  }

  /**
   * Function to send the notification email twice in a week about the shared document to them
   * @return boolean value
   */

  private function _getCronDocument()
  {
    $day = date('l');
    $time = (int)date('H');

    if((($day=='Monday' || $day=='Thursday' ) && $time == 6) || getValue('forcecron') == 'sharedspace' || getValue('custom_uid') == '999-111')
    {
      $oLogin = CDependency::getComponentByName('login');
      $oDB = CDependency::getComponentByName('database');

      $sQuery = 'SELECT * FROM  `shared_document` AS sd ';
      $sQuery.= ' LEFT JOIN shared_document_user AS sdu ON ( sdu.documentfk = sd.shared_documentpk) ';
      $sQuery.= ' LEFT JOIN shared_document_log AS sl ON (sd.shared_documentpk = sl.docfk and sd.shared_documentpk) ';
      $sQuery.= ' WHERE sd.parentfk = 0  AND sl.docfk IS NULL ';
      $sQuery.= ' AND date_creation > '.date('Y-m-d', mktime(0, 0 , 0, date('m'), (date('d')-14), date('Y')));

      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();
      if(!$bRead)
        return false;

      //get the list of all the users who are active
      $asCrmUsers = $oLogin->getUserList(0, true);

      $asToNotify = array();
      $asDocuments = array();

      $oPage = CDependency::getComponentByName('page');
      $oMail = CDependency::getComponentByName('mail');
      $oMailComponent = CDependency::getComponentUidByName('mail');

      while($bRead)
      {
        $asDocData = $oResult->getData();

        if($oResult->getFieldValue('is_public', CONST_PHP_VARTYPE_INT))
          $asRecipients = array_keys($asCrmUsers);
        else
          $asRecipients = array($asDocData['userfk']);

        //get the list of the doc for every user
        foreach($asRecipients as $nLoginfk)
        {
          $asToNotify[$nLoginfk][] = $asDocData['shared_documentpk'];
        }

        if(isset($asCrmUsers[$asDocData['creatorfk']]))
        {
          $sContent = '<strong>Created on : </strong> '.$asDocData['date_creation'].' by '.$oLogin->getUserNameFromData($asCrmUsers[$asDocData['creatorfk']]).'<br />';
          $sContent.= '<strong>Title:</strong> '.$asDocData['title'].'<br />';
          $sContent.= '<strong>Description:</strong> '.$asDocData['description'].'<br />';
          $sContent.= '<strong>File name:</strong>'.$asDocData['file_name'].'<br /><br />';
          $asDocuments[$asDocData['shared_documentpk']] = $sContent;
        }
        $bRead = $oResult->ReadNext();
        unset($asRecipients);
      }

    if(!empty($oMailComponent))
    {
      $sSharespaceURL = $oPage->getUrl('sharedspace', CONST_ACTION_LIST, CONST_SS_TYPE_DOCUMENT);
      $sLink = '<a href="'.$sSharespaceURL.'">Shared space</a>';
      $nSent = 0;

      foreach($asToNotify as $nLoginfk => $anDocumentToNotify)
      {
        if(!empty($nLoginfk) && !empty($asCrmUsers[$nLoginfk]))
        {
          $sContent = '<h3>Dear '.$oLogin->getUserNameFromData($asCrmUsers[$nLoginfk]).',</h3><br /><br />';

          if(count($anDocumentToNotify) > 1)
            $sContent.= count($anDocumentToNotify)." documents have been shared with you on the  CRM. You can access your shared space by clicking on the followinfg link : '.$sLink.'<br /><br />";
          else
            $sContent.= "A document has been shared with you on the  CRM. You can access your shared space by clicking on the followinfg link:<br /><br />";

          $sContent.= " + Document information:<br /><br />";

          foreach($anDocumentToNotify as $nDocumentPk)
          {
            $sContent.= '<br />'.$asDocuments[$nDocumentPk].'<hr />';
          }

          $sContent.= "Enjoy BCM.";

          $oMail-> sendRawEmail('',$asCrmUsers[$nLoginfk]['email'], 'BCM - Notifier: a file has been shared with you.', $sContent);
          $this->_getDocumentLogSave($anDocumentToNotify, $nLoginfk);
          $nSent++;

         }
       }
        echo $nSent.' email(s) have been sent.<br />';
      }
    }

    return true;
  }

  /**
   * Return the list of users able to see a specific file
   *
   * @param object $poResult
   * @return array containing user data
   */
  public function getSharedUserList($poResult)
  {
    $oDB = CDependency::getComponentByName('database');
    $asUserList= array();

    if($poResult->getFieldValue('is_public') == '1')
    {
      $sQuery='select loginpk from login where status=1';
      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();

      if(!$bRead)
       return false;

      while($bRead)
      {
        $asUserList[$oResult->getFieldValue('loginpk')] = $oResult->getData();
        $bRead = $oResult->readNext();
      }
      return $asUserList;
    }
    else
    {
      $sQuery='select userfk from shared_document_user where documentfk='.$poResult->getFieldValue('shared_documentpk');
      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();

      if(!$bRead)
        return false;

      while($bRead)
      {
        $asUserList[$oResult->getFieldValue('userfk')] = $oResult->getData();
        $bRead = $oResult->readNext();
      }
    }
    return $asUserList;
  }

  /**
   * Function to save the document log
   * @param array $panDocfk
   * @param integer $pnLoginPk
   * @return string
   */

  private function _getDocumentLogSave($panDocfk, $pnLoginPk)
  {
    if(!assert('is_array($panDocfk) && !empty($panDocfk)'))
      return 'Document is not obtained';

    if(!assert('is_integer($pnLoginPk) && !empty($pnLoginPk)'))
      return 'User is not obtained';

    $oDB = CDependency::getComponentByName('database');
    $sDate = date('Y-m-d H:i:s');

    foreach($panDocfk as $nDocFk)
    {
      $asValues[] = ' ('.$nDocFk.', '.$pnLoginPk.', 1, "'.$sDate.'") ';
    }

    $sQuery = 'INSERT INTO `shared_document_log` (`docfk`, `loginfk`, `status`, `date`) VALUES '.implode(', ', $asValues);
    $oResult = $oDB->ExecuteQuery($sQuery);

    if($oResult)
      $sHTML = 'Email has been sent and information logged.';
    else
      $sHTML = ' Can not save information.';

    return $sHTML;
   }

 /**
   * Function for listing of the documents
   * @return string
   */

  private function _getDocumentList()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'css/sharedspace.css');

    $sHTML = $oHTML->getTitleLine('Shared documents', $this->getResourcePath().'/pictures/component.png');
    $sHTML.= $oHTML->getCarriageReturn();

    $nCurrentUserPk = $oLogin->getUserPk();
    $asUser = $oLogin->getUserList(0,false,true); //Exclude inactive user but include admin
    $sSort = getValue('sort');

    if(!empty($sSort))
    {
      $_SESSION['ss_sort']['field'] = $sSort;

      if(isset($_SESSION['ss_sort']['order']) && $_SESSION['ss_sort']['order'] == 'asc')
      {
        $_SESSION['ss_sort']['order'] = 'desc';
      }
      else
        $_SESSION['ss_sort']['order'] = 'asc';
    }
    else
    {
      $_SESSION['ss_sort']['field'] = 'date_update';
      $_SESSION['ss_sort']['order'] = 'desc';
    }
    //count the number of documents that user can access

    $sQuery = 'SELECT SUM(nCount) as nTotal FROM ( ';
    $sQuery.= 'SELECT count(*) as nCount FROM `shared_document` as sd ';
    $sQuery.= ' LEFT JOIN shared_document_user as sdu ON (sdu.documentfk = sd.shared_documentpk) ';
    $sQuery.= ' WHERE parentfk = 0 AND (sd.is_public = 1 OR sd.creatorfk = '.$nCurrentUserPk.' OR sdu.userfk = '.$nCurrentUserPk.') ';
    $sQuery.= ' GROUP BY shared_documentpk) as q';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    $nNbDoc = $oResult->getFieldValue('nTotal', CONST_PHP_VARTYPE_INT);

    if(!$bRead || $nNbDoc == 0 )
    {
      $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_ADD, CONST_SS_TYPE_DOCUMENT);

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'notice2'));
      $sHTML.= $oHTML->getText('No shared document available. Upload ');
      $sHTML.= $oHTML->getLink('your first document', $sUrl);
      $sHTML.= $oHTML->getText(' now.');
      $sHTML.= $oHTML->getBlocEnd();
      return $sHTML;
    }

    $oPager = CDependency::getComponentByName('pager');
    $oPagerComponent = CDependency::getComponentUidByName('pager');
    if(!empty($oPagerComponent))
    $oPager->initPager();

    //Select the latest  revision of all documents
    $sQuery = 'SELECT sd.*, GROUP_CONCAT(userfk SEPARATOR ",") as viewers FROM `shared_document` as sd ';
    $sQuery.= ' LEFT JOIN shared_document_user as sdu ON (sdu.documentfk = sd.shared_documentpk)';
    $sQuery.= ' WHERE parentfk = 0 AND (sd.is_public = 1 OR sd.creatorfk = '.$nCurrentUserPk.' OR sdu.userfk = '.$nCurrentUserPk.') ';
    $sQuery.= ' GROUP BY shared_documentpk ';
    $sQuery.= ' ORDER BY '.$_SESSION['ss_sort']['field'].' '.$_SESSION['ss_sort']['order'].' ';
    $sQuery.= '  LIMIT '.$oPager->getSqlOffset().', '.$oPager->getLimit();

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    $asDocuments = array();
    while($bRead)
    {
      $asDocuments[$oResult->getFieldValue('shared_documentpk')] = $oResult->getData();
      $bRead = $oResult->readNext();
    }

    //Select the Revisions of the previous docs
    $sQuery = 'SELECT sd.* FROM `shared_document` as sd  ';
    $sQuery.= ' WHERE parentfk IN ('.implode(',', array_keys($asDocuments)).') ORDER BY date_creation DESC';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    $asRevisions = array();
    while($bRead)
    {
      $asRevisions[$oResult->getFieldValue('parentfk', CONST_PHP_VARTYPE_INT)][] = $oResult->getData();
      $bRead = $oResult->readNext();
    }

    $sHTML.= $oHTML->getTitle($nNbDoc.' Shared documents', 'h2');
    $sHTML.= $oHTML->getCarriageReturn();

    //Header Container
    $sHTML.= $oHTML->getBlocStart('', array('class'=>'homePageContainer','style' =>'padding: 0px;background-color:#FFFFFF;width: 100%;'));
    $sHTML.= $oHTML->getListStart('', array('class' => 'ablistContainer'));

    $sHTML.= $oHTML->getListItemStart('', array('class' => 'ablistHeader'));
    $sHTML.= $this->_getSharedRowHeader();
    $sHTML.= $oHTML->getListItemEnd();

    $pnRow =0;

    foreach($asDocuments as $asDocument)
    {
      if(($pnRow%2) == 0)
        $sRowClass = '';
      else
        $sRowClass = 'list_row_data_odd';

       $nDocumentPk = (int)$asDocument['shared_documentpk'];
       $sHTML.= $oHTML->getListItemStart('');

       $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_row_data '.$sRowClass));
       $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell '.$sRowClass,'style' => 'width:7%'));
       $sHTML.= $oHTML->getNiceTime($asDocument['date_update']);
       $sHTML.= $oHTML->getBlocEnd();

       $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell '.$sRowClass,'style' => 'width:5%'));
       if((bool)$asDocument['is_public'])
         $sHTML.= $oHTML->getText('Public');
       else
       {
         if(empty($asDocument['viewers']))
           $sHTML.= $oHTML->getText('Private');
         else
           $sHTML.= $oHTML->getText('Specific share');
       }
       $sHTML.= $oHTML->getBlocEnd();

       $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell '.$sRowClass,'style' => 'width:10%;'));
       $sUserName = $oLogin->getUserNameFromData($asUser[$asDocument['creatorfk']]);
       $sHTML.= $oHTML->getText($sUserName);
       $sHTML.= $oHTML->getBlocEnd();
       $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell '.$sRowClass,'style' => 'width:12%'));

       $sHTML.= $oHTML->getText($asDocument['title'], array(), 40);
       $sHTML.= $oHTML->getBlocEnd();

       $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell '.$sRowClass,'style' => 'width:29%'));
       $sHTML.= $oHTML->getText($asDocument['description'], array(), 70);
       $sHTML.= $oHTML->getBlocEnd();

       $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell '.$sRowClass,'style' => 'width: 21%;float:right;'));
       $sPic = $oHTML->getPicture(CONST_PICTURE_DOWNLOAD, 'Download file', '', array('style' => 'float: right; margin-right:10px;'));
       if(!isset($asRevisions[$nDocumentPk]))
       {
        $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_SEND, CONST_SS_TYPE_DOCUMENT, $nDocumentPk);
        $sHTML.= $oHTML->getLink($sPic.' '.$asDocument['file_name'], $sUrl, array('target' => '_blank', 'class' => 'dl_link'));
        }
        else
        {
          $nCount = count($asRevisions[$nDocumentPk]);

          foreach($asRevisions[$nDocumentPk] as $asRevision)
          {
            $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_SEND, CONST_SS_TYPE_DOCUMENT, (int)$asRevision['shared_documentpk']);
            $sHTML.= $oHTML->getLink('rev #'.$nCount.': '.$asRevision['file_name'], $sUrl, array('target' => '_blank', 'class' => 'dl_link'));
            $sHTML.= $oHTML->getCarriageReturn();
            $nCount--;
          }
        }

       $sHTML.= $oHTML->getBlocEnd();
       $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
       $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getListItemEnd();
      $pnRow ++;

      $bRead = $oResult->readNext();
    }

    $sHTML.= $oHTML->getListEnd();
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack')).$oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_LIST, CONST_SS_TYPE_DOCUMENT);
    $sHTML.= $oPager->getDisplay($nNbDoc, $sUrl);

    return $sHTML;
  }

  /**
   * Header for the listing for shared space documents
   * @return type
   */

  private function _getSharedRowHeader()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = $oHTML->getBlocStart('', array('class' =>'list_row '));
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_row_data'));

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'width:5%;'));
    $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_LIST, CONST_SS_TYPE_DOCUMENT, 0, array('sort' => 'sd.date_update'));
    $sHTML.= $oHTML->getLink('Date', $sUrl,array('style'=>'color:#FFFFFF;'));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'width:6%;'));
    $sHTML.= $oHTML->getText('Sharing options');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'width:8%;'));
    $sHTML.= $oHTML->getText('Owner');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'width:12%;'));
    $sHTML.= $oHTML->getText('Title');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'min-width: 29%;text-align:left;'));
    $sHTML.= $oHTML->getText('Description');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell ','style' => 'min-width: 10%;'));
    $sHTML.= $oHTML->getText('Document');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;

  }

  /**
    * Function to manage document
    * @return string
    */

  private function _getManageDocument()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');
    $bAdmin = $oLogin->isAdmin();

    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'css/sharedspace.css');

    $sHTML = $oHTML->getTitleLine('Manage Shared Documents', $this->getResourcePath().'/pictures/component.png');
    $sHTML.= $oHTML->getCarriageReturn();

    $nCurrentUserPk = $oLogin->getUserPk();
    $asUser = $oLogin->getUserList();

    if($bAdmin)
    {
     $sQuery = 'SELECT count(sd.shared_documentpk) as nCount FROM `shared_document` as sd ';
     $sQuery.= ' LEFT JOIN shared_document_editor as sde ON (sde.documentfk = sd.shared_documentpk)';
     $sQuery.= ' WHERE parentfk = 0 ';
    }
    else
    {
     $sQuery = 'SELECT count(sd.shared_documentpk) as nCount FROM `shared_document` as sd ';
     $sQuery.= ' LEFT JOIN shared_document_editor as sde ON (sde.documentfk = sd.shared_documentpk)';
     $sQuery.= ' WHERE parentfk = 0 AND (sd.is_edit_public = 1 OR sd.creatorfk = '.$nCurrentUserPk.' OR sde.userfk = '.$nCurrentUserPk.') ';
    }

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    $nNbDoc = $oResult->getFieldValue('nCount', CONST_PHP_VARTYPE_INT);

    if(!$bRead || $nNbDoc == 0 )
    {
      $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_ADD, CONST_SS_TYPE_DOCUMENT);

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'notice2'));
      $sHTML.= $oHTML->getText('No shared document available. Upload ');
      $sHTML.= $oHTML->getLink('our first document', $sUrl);
      $sHTML.= $oHTML->getText(' now.');
      $sHTML.= $oHTML->getBlocEnd();
      return $sHTML;
    }

    $oPager = CDependency::getComponentByName('pager');
    $oPagerComponent = CDependency::getComponentUidByName('pager');
    if(!empty($oPagerComponent))
    $oPager->initPager();

    //Select the last revision of all documents
    if($bAdmin)
    {
      $sMainQuery = 'SELECT sd.*, sd1.file_name as rev_name FROM `shared_document` as sd ';
      $sMainQuery.= ' LEFT JOIN `shared_document` as sd1 ON (sd1.shared_documentpk = sd.parentfk AND sd1.date_creation = sd.date_update) ';
      $sMainQuery.= ' LEFT JOIN shared_document_editor as sde ON (sde.documentfk = sd.shared_documentpk)';
      $sMainQuery.= ' WHERE sd.parentfk = 0 GROUP BY shared_documentpk ' ;
    }
    else
    {
      $sMainQuery = 'SELECT sd.*, sd1.file_name as rev_name FROM `shared_document` as sd ';
      $sMainQuery.= ' LEFT JOIN `shared_document` as sd1 ON (sd1.shared_documentpk = sd.parentfk AND sd1.date_creation = sd.date_update) ';
      $sMainQuery.= ' LEFT JOIN shared_document_editor as sde ON (sde.documentfk = sd.shared_documentpk)';
      $sMainQuery.= ' WHERE sd.parentfk = 0 AND (sd.is_edit_public = 1 OR sd.creatorfk = '.$nCurrentUserPk.' OR sde.userfk = '.$nCurrentUserPk.') ';
      $sMainQuery.= ' GROUP BY shared_documentpk ' ;
    }

    $sMainQuery.= ' ORDER BY sd.date_update DESC LIMIT '.$oPager->getSqlOffset().','.$oPager->getLimit();
    $oDbResult = $oDB->ExecuteQuery($sMainQuery);
    $bRead = $oDbResult->readFirst();
    $asDocuments = array();

    if($bRead)
    {
     while($bRead)
     {
      $asDocuments[$oDbResult->getFieldValue('shared_documentpk')] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
     }
    }

    //Select the Revisions of the previous docs
    $sQuery = 'SELECT sd.* FROM `shared_document` as sd  ';
    $sQuery.= ' WHERE parentfk IN ('.implode(',', array_keys($asDocuments)).') ORDER BY date_creation DESC';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    $asRevisions = array();
    while($bRead)
    {
      $asRevisions[$oResult->getFieldValue('parentfk', CONST_PHP_VARTYPE_INT)][] = $oResult->getData();
      $bRead = $oResult->readNext();
    }

    $sHTML.= $oHTML->getBlocStart('', array('class'=>'homePageContainer','style' =>'padding: 0px;background-color:#FFFFFF;width: 100%;'));
    $sHTML.= $oHTML->getListStart('', array('class' => 'ablistContainer'));

    $sHTML.= $oHTML->getListItemStart('', array('class' => 'ablistHeader'));
    $sHTML.= $this->_getSharedManageRowHeader();
    $sHTML.= $oHTML->getListItemEnd();

    // Just for the header till now.

    $pnRow =0;
    foreach($asDocuments as $asDocument)
    {
      if(($pnRow%2) == 0)
        $sRowClass = '';
      else
        $sRowClass = 'list_row_data_odd';

      $nDocumentPk = (int)$asDocument['shared_documentpk'];

      $sHTML.= $oHTML->getListItemStart('');
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'sharedListRow sharedManageRow '.$sRowClass));
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'sharedListCell ','style' => 'width:110px;'));
      $sHTML.= $oHTML->getNiceTime($asDocument['date_creation']);

      if($bAdmin)
      {
        $sHTML.= $oHTML->getCarriageReturn();
        $asUserData = $oLogin->getUserDataByPk((int)$asDocument['creatorfk']);
        $sHTML.= $oHTML->getText('By :'.$oLogin->getUserNameFromData($asUserData), array('style' => 'color: #2A6991;'));
      }

      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'sharedListCell','style' => 'width:150px;'));
      $sHTML.= substr($oHTML->getText($asDocument['title']), 0, 20);
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'sharedListCell','style' => 'width:150px;'));
      $sHTML.= substr($oHTML->getText($asDocument['description']), 0, 20);
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'sharedListCell','style' => 'width:115px;'));
      if($asDocument['is_public'] == 1)
        $sHTML.= $oHTML->getText('Public');
      else if($asDocument['is_public'] == 0)
        $sHTML.= $oHTML->getText('Private');
      else
        $sHTML.= $oHTML->getText('Restricted access');

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'sharedListCell','style' => 'width:340px;'));

      if(isset($asRevisions[$nDocumentPk]))
      {
        $sHTML.= $oHTML->getBlocStart('revId_'.$nDocumentPk, array('class' => ''));

        //Get the number of revisions: nb file -1 (current one)
        $nCount = count($asRevisions[$nDocumentPk]);
        $nKey = 0;
        $bFirst = true;

        foreach($asRevisions[$nDocumentPk] as $asRevData)
        {
          $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_SEND, CONST_SS_TYPE_DOCUMENT, (int)$asRevData['shared_documentpk']);

          if($bFirst)
          {
            $sHTML.= $oHTML->getSpanStart();
            $sHTML.= $oHTML->getPicture(CONST_PICTURE_DOWNLOAD, 'Download file', $sUrl, array('style' => 'float: right; margin-right:10px;'));
            $sHTML.= $oHTML->getText('Revision #'.$nCount.': ', array('class' => 'strong'));
            $sHTML.= $oHTML->getSpace(2);
            $sHTML.= $oHTML->getLink($asRevData['file_name'], $sUrl, array('target' => '_blank'));
            $sHTML.= $oHTML->getText('  -  '). $oHTML->getNiceTime($asRevData['date_creation']);
            $sHTML.= $oHTML->getCarriageReturn();
            $sHTML.= $oHTML->getSpanEnd();
          }
          else
          {
            //Second line: multiple revisions
            if($nKey == 1)
            {
              $sHTML.= $oHTML->getSpanStart();
              $sHTML.= $oHTML->getLink('Other revisions...', 'javascript:;', array('onclick' => '$(\'#revId_'.$nDocumentPk.' .rev_hidden\').fadeToggle(); '));
              $sHTML.= $oHTML->getCarriageReturn();
              $sHTML.= $oHTML->getSpanEnd();

              $sHTML.= $oHTML->getBlocStart('', array('class' => 'rev_hidden'));
            }

            $sHTML.= $oHTML->getSpanStart();
            $sHTML.= $oHTML->getText('rev. #'.$nCount.': ');
            $sHTML.= $oHTML->getLink($asRevData['file_name'], $sUrl, array('target' => '_blank'));
            $sHTML.= $oHTML->getText('  -  '). $oHTML->getNiceTime($asRevData['date_creation']);
            $sHTML.= $oHTML->getCarriageReturn();
            $sHTML.= $oHTML->getSpanEnd();
          }

          $nCount--;
          $nKey++;
          $bFirst = false;
        }

        $sHTML.= $oHTML->getText('rev. #0: ');
        $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_SEND, CONST_SS_TYPE_DOCUMENT, $nDocumentPk);
        $sHTML.= $oHTML->getLink($asDocument['file_name'], $sUrl, array('target' => '_blank'));

        if($nKey >= 1)
          $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocEnd();
      }
      else
      {
        $sPic = $oHTML->getPicture(CONST_PICTURE_DOWNLOAD, 'Download file', '', array('style' => 'float: right; margin-right:10px;'));
        $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_SEND, CONST_SS_TYPE_DOCUMENT, $nDocumentPk);
        $sHTML.= $oHTML->getLink($sPic.' '.$asDocument['file_name'], $sUrl, array('target' => '_blank'));
      }

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'sharedListCell','style' => 'width:100px; float:right;'));
      $sPic = $oHTML->getPicture(CONST_PICTURE_EDIT, 'Edit document');
      $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_EDIT, CONST_SS_TYPE_DOCUMENT, $nDocumentPk);
      $sHTML.= $oHTML->getLink($sPic, $sUrl);
      $sHTML.= $oHTML->getSpace(2);
      $sUrl = $oPage->getAjaxUrl('sharedspace', CONST_ACTION_DELETE, CONST_SS_TYPE_DOCUMENT, $nDocumentPk);
      $sPic = $oHTML->getPicture(CONST_PICTURE_DELETE, 'Delete project');
      $sHTML.= $oHTML->getLink($sPic, $sUrl, array('onclick' => 'if(!window.confirm(\'Delete this shared document ?\')){ return false; }'));

      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getListItemEnd();

      $pnRow++;
    }

    $sHTML.= $oHTML->getListEnd();
    $sHTML.= $oHTML->getBlocEnd();
    $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_MANAGE, CONST_SS_TYPE_DOCUMENT);
    $sHTML.= $oPager->getDisplay($nNbDoc, $sUrl);

    return $sHTML;
  }

  /**
   * Function to display the sharedspace manage section header
   * @return string
   */

  private function _getSharedManageRowHeader()
  {
    $oHTML = CDependency::getComponentByName('display');

    $sHTML = $oHTML->getBlocStart('', array('class' =>'list_row '));
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_row_data'));

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'width:5%;'));
    $sHTML.= $oHTML->getText('Date Creation', array('style'=>'color:#FFFFFF;'));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'width:9%;'));
    $sHTML.= $oHTML->getText('Title');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'min-width: 15%;'));
    $sHTML.= $oHTML->getText('Description');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'width:115px;'));
    $sHTML.= $oHTML->getText('Sharing options');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell ','style' => 'min-width: 10%;'));
    $sHTML.= $oHTML->getText('Document');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell ','style' => 'float:right;min-width: 10%;'));
    $sHTML.= $oHTML->getText('Action');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
   }

   /**
    * Form to add/edit document
    * @param integer $pnPk
    * @return string
    */

  private function _getDocumentForm($pnPk = 0)
  {
    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');

    $oPage->addCssFile($this->getResourcePath().'css/sharedspace.css');
    $oPage->addRequiredJsFile($this->getResourcePath().'js/sharedspace.js');

    if(empty($pnPk))
    {
      $asFieldValue = array('title'=>'','description'=>'', 'documents'=> array(), 'users'=> array());
      $sURL = $oPage->getUrl('sharedspace', CONST_ACTION_SAVEADD, CONST_SS_TYPE_DOCUMENT);
      $sTitle = 'Add a shared document';
    }
    else
    {
      $oDB = CDependency::getComponentByName('database');
      $sQuery = 'SELECT sd.*,GROUP_CONCAT(sdu.userfk SEPARATOR ",") as viewers,GROUP_CONCAT(sde.userfk SEPARATOR ",") as editors FROM shared_document AS sd LEFT JOIN shared_document_editor AS sde ON (sde.documentfk = sd.shared_documentpk) ';
      $sQuery.= ' LEFT JOIN shared_document_user as sdu ON (sdu.documentfk = sd.shared_documentpk)';
      $sQuery.= ' WHERE sd.shared_documentpk = "'.$pnPk.'" OR sd.parentfk = "'.$pnPk.'" ';
      $sQuery.= ' ORDER BY sd.parentfk ASC, sd.date_creation DESC ';

      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();

      if(!$bRead)
      {
        $sHTML.= $oHTML->getBlocStart('', array('class' => 'notice2'));
        $sHTML.= $oHTML->getText('Can\'t find the document. It may have been deleted.');
        $sHTML.= $oHTML->getBlocEnd();
        return $sHTML;
      }

      $asFieldValue = $oResult->getData();
      $asFieldValue['documents'] = $asFieldValue['file_name'];
      $sURL = $oPage->getUrl('sharedspace', CONST_ACTION_SAVEADD, CONST_SS_TYPE_DOCUMENT, $pnPk);
      $sTitle = 'Edit this document';

      $bRead = $oResult->readNext();
      $asRevisions = array();
      while($bRead)
      {
        $asRevisions[] = $oResult->getData();
        $bRead = $oResult->readNext();
      }
    }

    $asUser = $oLogin->getUserList(0, true, false);
    $sHTML = $oHTML->getBlocStart('documentFormId');

    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('documentFormData');
    $oForm->setFormParams('', false, array('submitLabel' => 'Save document', 'action' => $sURL));

    $oForm->addField('misc', '', array('type' => 'text', 'text'=>$sTitle, 'class' => 'h2'));
    $oForm->addField('misc', '', array('type'=>'br'));

    $oForm->addField('input', 'title', array('label'=>'Title', 'value' => $asFieldValue['title']));
    $oForm->setFieldControl('title', array('jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

    $oForm->addField('textarea', 'description', array('label'=>'Description', 'value' => $asFieldValue['description']));
    $oForm->setFieldControl('description', array('jsFieldMaxSize' => 255));

    $oForm->addField('misc', '', array('type'=>'br'));

    if(empty($pnPk))
    {
      $oForm->addField('input', 'documents[]', array('type' => 'file', 'label'=>'Document', 'value' => $asFieldValue['documents']));
      $oForm->setFieldControl('documents[]', array('jsFieldNotEmpty' => ''));

      $asViewers = array();
      $asEditors = array();
    }
     else
    {
      $oForm->addField('input', 'documents[]', array('type' => 'file', 'label'=>'New revision of the document', 'value' => $asFieldValue['documents']));
      if(!empty($asRevisions))
      {
        $sRevisions = '<br /><strong><u>Previous revisions:</u></strong><br /><br />';
        foreach($asRevisions as $asFileData)
        {
          $sURL = $oPage->getUrl($this->_getUid(), CONST_ACTION_SEND, CONST_SS_TYPE_DOCUMENT, (int)$asFileData['shared_documentpk']);
          $sRevisions.= '- '.$oHTML->getLink($asFileData['file_name'], $sURL, array('target' => '_blank')).'&nbsp;&nbsp;-&nbsp;&nbsp;'.$oHTML->getNiceTime($asFileData['date_creation']).'<br />';
        }

        $sRevisions.= '<br /><br />';
        $oForm->addField('misc', '', array('type'=>'text', 'text' => $sRevisions));
      }

      $sViewers = $asFieldValue['viewers'];
      $asViewers = explode(',',$sViewers);

      $sEditors = $asFieldValue['editors'];
      $asEditors = explode(',',$sEditors);
    }

    $oForm->addField('hidden', 'MAX_FILE_SIZE', array('value' => (25*1024*1024)));

    //Manage the visibility from here
    $asVisibility = array(0 =>array('label'=>'Private'),1=>array('label'=>'Public'),2=>array('label'=>'Custom'));
    $oForm->addField('select', 'visibility', array('label' => 'Visibility ','onchange'=>'showHideUserList(this.value);'));

    if(isset($asFieldValue['is_public']))
      $nPublic = $asFieldValue['is_public'];
    else
      $nPublic = '-1';

    foreach($asVisibility as $nKey => $asVisible)
    {
      if($nKey == $nPublic)
        $oForm->addOption('visibility', array('value'=> $nKey, 'label' => $asVisible['label'],'selected'=>'selected'));
      else
        $oForm->addOption('visibility', array('value'=> $nKey, 'label' => $asVisible['label']));
    }

    if(isset($asFieldValue['is_public']) && $asFieldValue['is_public'] == 2)
      $sClass = '';
    else
      $sClass = 'hidden';

    $oForm->addField('select', 'users[]', array('label' => 'Shared with', 'multiple' => 'multiple'));

    $oForm->setFieldDisplayParams('users[]', array('class'=>$sClass.' userList'));

    foreach($asUser as $asUserData)
    {
     if(in_array($asUserData['loginpk'],$asViewers))
        $oForm->addOption('users[]', array('value'=>$asUserData['loginpk'], 'label' => $asUserData['firstname'].' '.$asUserData['lastname'], 'selected' => 'selected'));
      else
        $oForm->addOption('users[]', array('value'=>$asUserData['loginpk'], 'label' => $asUserData['firstname'].' '.$asUserData['lastname']));
    }

    $oForm->addField('select', 'user_editors[]', array('label' => ' Can be edited by ', 'multiple' => 'multiple'));
    $oForm->setFieldControl('user_editors[]', array('jsFieldNotEmpty' => ''));

    if(isset($asFieldValue['is_edit_public']) && $asFieldValue['is_edit_public'] == 1)
      $oForm->addOption('user_editors[]', array('value'=>0, 'label' => '--Everyone--','selected'=>'selected'));
    else
      $oForm->addOption('user_editors[]', array('value'=>0, 'label' => '--Everyone--'));

    foreach($asUser as $asUserData)
    {
      if(in_array($asUserData['loginpk'], $asEditors))
        $oForm->addOption('user_editors[]', array('value'=>$asUserData['loginpk'], 'label' => $asUserData['firstname'].' '.$asUserData['lastname'], 'selected' => 'selected'));
      else
        $oForm->addOption('user_editors[]', array('value'=>$asUserData['loginpk'], 'label' => $asUserData['firstname'].' '.$asUserData['lastname']));
    }

    $oForm->addField('checkbox', 'cascading', array('type' => 'misc', 'label'=> 'Notify the users about this document ?', 'value' => 1, 'id' => 'cascading_id'));
    $oForm->addField('misc', '', array('type'=>'br'));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Function to save the uploaded document
   * @param integer $pnPk
   * @return redirection to the another page/error message if halted
   */

  private function _getDocumentSave($pnPk = 0)
  {
    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oHTML = CDependency::getComponentByName('display');

    if(!assert('is_integer($pnPk)'))
      return $oHTML->getErrorMessage (__LINE__.' - Can\t save the document: bad parameters.');

    $sTitle = getValue('title');
    $sDescription = getValue('description');
    $asUser = getValue('users');
    $asEditUsers = getValue('user_editors');
    $nVisibility = getValue('visibility');

    if(empty($sTitle))
      return $oHTML->getErrorMessage (__LINE__.' - Document title is required.');

    if(empty($asUser))
      $asUser[] = '0';

    if(in_array('0', $asEditUsers))
      $nEditPublic = 1;
    else
      $nEditPublic = 0;

    $oLogin = CDependency::getComponentByName('login');
    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');

    $nCreatorPk = $oLogin->getUserPk();
    $asSavedFile = array();

    //Checking the file upload
    if(!isset($_FILES) || !isset($_FILES['documents']) || !isset($_FILES['documents']['tmp_name']))
      return $oHTML->getErrorMessage(__LINE__.' - File upload seems to be desactivated.');

    if(empty($pnPk))
    {
      //Ready for multiple files
      foreach($_FILES['documents']['tmp_name'] as $nKey => $sTmpFileName)
      {
        $sFileName = $_FILES['documents']['name'][$nKey];

        //security control it s not a file pushed by a nasty script
        if(empty($pnPk) &&  !is_uploaded_file($sTmpFileName))
          return $oHTML->getErrorMessage(__LINE__.' - No file found on the server... T_T [pk: '.$pnPk.' | error: '.$_FILES['documents']['error'][$nKey].' ');

        //checkExtension / mime /  filesize ...
        $oFinfo = finfo_open(FILEINFO_MIME_TYPE);
        $sMimeType = finfo_file($oFinfo, $sTmpFileName);

        if(filesize($sTmpFileName) > (25*1024*1024))
          return $oHTML->getErrorMessage(__LINE__.' - Sorry, the file is too big.');

        //TRICK:
        //create the db row to get a PK, with no path name
        $sDate = $oDB->dbEscapeString(date('Y-m-d H:i:s'));
        $sQuery = 'INSERT INTO `shared_document` (`title`, `description` ,`mime_type` ,`file_name` ,`file_path`, `creatorfk`, `is_public`, `date_creation`, `date_update`,`is_edit_public`) ';
        $sQuery.= 'VALUES ('.$oDB->dbEscapeString($sTitle).', '.$oDB->dbEscapeString($sDescription, NULL).', '.$oDB->dbEscapeString($sMimeType).', ';
        $sQuery.= $oDB->dbEscapeString($sFileName).', '.$oDB->dbEscapeString('').', '.$oDB->dbEscapeString($nCreatorPk).', ';
        $sQuery.= $oDB->dbEscapeString($nVisibility).', '.$sDate.', '.$sDate.','.$oDB->dbEscapeString($nEditPublic).') ';

        $oResult = $oDB->ExecuteQuery($sQuery);
        if(!$oResult)
          return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save the uploaded file.');

        $nDocPk = $oResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT);

        //if the document is not public, add all the allowed users
        if($nVisibility == 2)
        {
          $asQuery = array();
          $asRecipient = array();
          $sDate = date('Y-m-d H:i:s');
          foreach($asUser as $sUserKey)
          {
            $asQuery[] = '('.$nDocPk.', '.(int)$sUserKey.', "'.$sDate.'")';
            $asRecipient[(int)$sUserKey] = (int)$sUserKey;
          }

          if(!empty($asQuery))
          {
            $sQuery = 'INSERT INTO `shared_document_user` (`documentfk`, `userfk`, `date`) VALUES '.implode(', ',$asQuery);
            $oResult = $oDB->ExecuteQuery($sQuery);
            if(!$oResult)
              return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save users');
          }
        }
        //If the edited user is not empty
        if(!$nEditPublic)
        {
          $asEditQuery = array();
          $sDate = date('Y-m-d');

          foreach($asEditUsers as $sEditorKey)
          {
            $asEditQuery[] = '('.$nDocPk.', '.(int)$sEditorKey.', "'.$sDate.'")';
          }

          if(!empty($asEditQuery))
          {
            $sQuery = 'INSERT INTO `shared_document_editor` (`documentfk`, `userfk`, `date`) VALUES '.implode(', ',$asEditQuery);
            $oResult = $oDB->ExecuteQuery($sQuery);
            if(!$oResult)
              return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save edit users');
          }
        }

        $sNewPath = $_SERVER['DOCUMENT_ROOT'].CONST_PATH_UPLOAD_DIR.'sharedspace/document/'.$nDocPk.'/';
        $sNewName = date('YmdHis').'_'.$oLogin->getUserPk().'_'.uniqid('doc'.$nDocPk.'_').'_'.$sFileName;

        if(!is_dir($sNewPath) && !makePath($sNewPath))
          return $oHTML->getErrorMessage(__LINE__.' - Destination folder doesn\'t exist.('.$sNewPath.')');

        if(!is_writable($sNewPath))
          return $oHTML->getErrorMessage(__LINE__.' - Can\'t write in the destinmation folder.');

        if(!move_uploaded_file($sTmpFileName, $sNewPath.$sNewName))
          return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t move the uploaded file.');

        $sQuery = 'UPDATE `shared_document` SET `file_path` = '.$oDB->dbEscapeString($sNewPath.$sNewName).' WHERE shared_documentpk = '.$nDocPk;
        $oResult = $oDB->ExecuteQuery($sQuery);
        if(!$oResult)
          return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save the uploaded file.');

        $asSavedFile[$nDocPk] = $sNewPath.$sNewName;
      }

      $nNotify = getValue('cascading');
      if(!empty($nNotify) && !empty($asUser))
        $this->_notifyUsers($nVisibility,$nDocPk);

      $sURL = $oPage->getUrl('sharedspace', CONST_ACTION_LIST, CONST_SS_TYPE_DOCUMENT);
      return $oHTML->getRedirection($sURL, 2500, count($asSavedFile).' files saved.');
    }
    else
    {
      //Edit document access options
      //if the document is not public, add all the all0owed users
      if($nVisibility == 2)
      {
        $sQuery = 'DELETE FROM `shared_document_user`  WHERE documentfk = '.$pnPk;
        $oResult = $oDB->ExecuteQuery($sQuery);
        if(!$oResult)
          return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save the access rights.');

        $asQuery = array();
        $sDate = date('Y-m-d H:i:s');
        foreach($asUser as $sUserKey)
        {
          $asQuery[] = '('.$pnPk.', '.(int)$sUserKey.', "'.$sDate.'")';
        }

        if(!empty($asQuery))
        {
          $sQuery = 'INSERT INTO `shared_document_user` (`documentfk`, `userfk`, `date`) VALUES '.implode(', ',$asQuery);
          $oResult = $oDB->ExecuteQuery($sQuery);
          if(!$oResult)
            return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save users');
        }
      }
      //If the edited user is not empty
       if(!$nEditPublic)
        {
          $asEditQuery = array();
          $sDate = date('Y-m-d');

          foreach($asEditUsers as $sEditorKey)
          {
            $asEditQuery[] = '('.$pnPk.', '.(int)$sEditorKey.', "'.$sDate.'")';
          }

          if(!empty($asEditQuery))
          {
            $sQuery = 'INSERT INTO `shared_document_editor` (`documentfk`, `userfk`, `date`) VALUES '.implode(', ',$asEditQuery);
            $oResult = $oDB->ExecuteQuery($sQuery);
            if(!$oResult)
            return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save edit users');
          }
        }

      //Add a new revisions of the document
      $nUpdateDocuments = 0;
      $sUpdateTime = date('Y-m-d H:i:s');

      foreach($_FILES['documents']['tmp_name'] as $nKey => $sTmpFileName)
      {
        $sFileName = $_FILES['documents']['name'][$nKey];
        //security control it  not a file pushed by a nasty script
        if(is_uploaded_file($sTmpFileName))
        {
          //checkExtension / mime /  filesize ...
          $oFinfo = finfo_open(FILEINFO_MIME_TYPE);
          $sMimeType = finfo_file($oFinfo, $sTmpFileName);

          if(filesize($sTmpFileName) < (25*1024*1024))
          {
            $sNewPath = $_SERVER['DOCUMENT_ROOT'].CONST_PATH_UPLOAD_DIR.'sharedspace/document/'.$pnPk.'/';
            $sNewName = date('YmdHis').'_'.$oLogin->getUserPk().'_'.uniqid('docrev'.$pnPk.'_').'_'.$sFileName;

            if(!is_dir($sNewPath) && !makePath($sNewPath))
              return $oHTML->getErrorMessage(__LINE__.' - Destination folder doesn\'t exist.('.$sNewPath.')');

            if(!is_writable($sNewPath))
              return $oHTML->getErrorMessage(__LINE__.' - Can\'t write in the destinmation folder.');

            if(!move_uploaded_file($sTmpFileName, $sNewPath.$sNewName))
              return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t move the uploaded file.');

            $sQuery = 'INSERT INTO `shared_document` (`parentfk` ,`mime_type` ,`file_name` ,`file_path`, `creatorfk`, `date_creation`) ';
            $sQuery.= 'VALUES ('.$oDB->dbEscapeString($pnPk).', '.$oDB->dbEscapeString($sMimeType).', ';
            $sQuery.= $oDB->dbEscapeString($sFileName).', '.$oDB->dbEscapeString($sNewPath.$sNewName).', '.$oDB->dbEscapeString($nCreatorPk).', ';
            $sQuery.= $oDB->dbEscapeString($sUpdateTime).') ';

            $oResult = $oDB->ExecuteQuery($sQuery);
            if(!$oResult)
              return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save the new revision file.');

            $nUpdateDocuments++;
          }
        }
      }

      //If a new revision has been uploaded, change the date_update too
      if($nUpdateDocuments > 0)
      {
        $sQuery = 'UPDATE `shared_document` SET `title` = '.$oDB->dbEscapeString($sTitle).', `description` = '.$oDB->dbEscapeString($sDescription).' ';
        $sQuery.= ', `is_public` = '.$oDB->dbEscapeString($nVisibility).', `date_update` = '.$oDB->dbEscapeString($sUpdateTime).' ';
        $sQuery.= ', `is_edit_public` = '.$oDB->dbEscapeString($nEditPublic).' ';
        $sQuery.= ' WHERE shared_documentpk = '.$pnPk;
        $oResult = $oDB->ExecuteQuery($sQuery);
        if(!$oResult)
          return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save the uploaded file.');
      }
      else
      {
        //Edit documents descriptions
        $sQuery = 'UPDATE `shared_document` SET `title` = '.$oDB->dbEscapeString($sTitle).', `description` = '.$oDB->dbEscapeString($sDescription).' ';
        $sQuery.= ',`is_edit_public` = '.$oDB->dbEscapeString($nEditPublic).' ';
        $sQuery.= ',`is_public` = '.$oDB->dbEscapeString($nVisibility).' WHERE shared_documentpk = '.$pnPk;
        $oResult = $oDB->ExecuteQuery($sQuery);
        if(!$oResult)
          return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save the uploaded file.');
      }

      $nNotify = getValue('cascading');
      if(!empty($nNotify) && !empty($asUser))
        $this->_notifyUsers($nVisibility,$pnPk);

      $sURL = $oPage->getUrl('sharedspace', CONST_ACTION_MANAGE, CONST_SS_TYPE_DOCUMENT);
     return $oHTML->getRedirection($sURL, 2500, $nUpdateDocuments.' files saved.');
    }
  }

  /**
   * Function to send notification about the uploaded document to shared users
   * @param integer $pVisibility
   * @param integer $pnPk
   * @return boolean
   */
  private function _notifyUsers($pVisibility,$pnPk)
  {
    if(!assert('is_integer($pnPk) && !empty($pnPk)'))
      return false;

    //Notify every person (except owner) that a file has been shared

    $oMail = CDependency::getComponentByName('mail');
    $oHTML = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');
    $oMailComponent = CDependency::getComponentUidByName('mail');

    $asUsers = $oLogin->getUserList(0, true, false);
    $nCreatorPk = $oLogin->getUserPk();

    $sQuery = 'SELECT sd.*, GROUP_CONCAT(userfk SEPARATOR ",") as viewers FROM `shared_document` as sd ';
    $sQuery.= ' LEFT JOIN shared_document_user as sdu ON (sdu.documentfk = sd.shared_documentpk)';
    $sQuery.= ' WHERE parentfk = 0 AND  shared_documentpk ='.$pnPk;

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(empty($bRead))
      return false;
    else
    {
      $sViewers = $oResult->getFieldValue('viewers');
      $asRecipient = explode(',',$sViewers);
      $sTitle = $oResult->getFieldValue('title');
      $sDescription = $oResult->getFieldValue('description');
      $sFileName = $oResult->getFieldValue('file_name');
      $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_SEND, CONST_SS_TYPE_DOCUMENT, (int)$pnPk);
      $sFileLink = $oHTML->getLink($sFileName,$sUrl);
    }

    if($pVisibility == 1)
      $asRecipient = array_keys($asUsers);

    $sURL = $oPage->getUrl('sharedspace', CONST_ACTION_LIST, CONST_SS_TYPE_DOCUMENT);
    if(!empty($oMailComponent))
    {
     foreach($asRecipient as $nUserPk)
     {
      if($nUserPk != $nCreatorPk)
       {
         $sEmail = $asUsers[$nUserPk]['email'];
          $sContent = "<h3>Dear ".$asUsers[$nUserPk]['firstname'].",</h3><br/><br />";
           $sContent.= "A document has been shared with you on the new CRM. You can access your shared space by clicking on the followinfg link:<br /><br />";
            $sContent.= "<a href='".$sURL."'>Shared documents</a><br/><br/>";
            $sContent.= "<strong>File informations:</strong><br/><br/>";
            $sContent.= '<strong>Title:</strong> '.$sTitle.'<br/>';
           $sContent.= '<strong>Description:</strong> '.$sDescription.'<br/>';
          $sContent.= '<strong>File(s) name:</strong> '.$sFileLink.'<br/><br/>';
         $sContent.= "Cheers. ";

        $oMail->sendRawEmail('info@bcm.com',$sEmail, "BCM Notifier: A new file has been shared with you.", $sContent);

        }
      }
    }
    return true;
  }

  /**
   * Function to remove the document
   * @param integer $pnPk
   * @return array
   */
  private function _getDocumentDelete($pnPk = 0)
  {
   if(!assert('is_integer($pnPk) && !empty($pnPk)'))
      return array('error'=>'No document found. It might have been removed already');

    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM shared_document WHERE shared_documentpk = "'.$pnPk.'" ';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
      return array('error' => __LINE__.' ERROR: no file found');

    //DELETE Attached file
    //Recreate the path to be sure there's no crazy delete
    $sAttchFolderPath = $_SERVER['DOCUMENT_ROOT'].CONST_PATH_UPLOAD_DIR.'sharedspace/document/'.$pnPk;

    $sCommandLine = escapeshellcmd('rm -R ').escapeshellarg($sAttchFolderPath);
    $sLastLine = exec(escapeshellcmd($sCommandLine), $asCmdResult, $nCmdResult);

    if(!empty($sLastLine))
    {
      assert('false; // couldn\'t delete attachment folder. ['.$sAttchFolderPath.']');
       return array('error' => __LINE__.' - An error occured, can\'t delete the project #'.$pnPk);
    }

    //DELETE document_user in DB
    $sQuery = 'DELETE FROM shared_document_user WHERE documentfk = '.$pnPk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    if(!$oResult)
      return array('error' => __LINE__.' - An error occured, can\'t delete the document #'.$pnPk);

    //DELETE document in DB
    $sQuery = 'DELETE FROM shared_document WHERE shared_documentpk = '.$pnPk.' OR parentfk = '.$pnPk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    if(!$oResult)
      return array('error' => __LINE__.' - An error occured, can\'t delete the document #'.$pnPk);

    $oPage = CDependency::getComponentByName('page');
    $sUrl = $oPage->getUrl('sharedspace', CONST_ACTION_MANAGE, CONST_SS_TYPE_DOCUMENT);

    return array('notice' => 'Document deleted.', 'url' => $sUrl);
 }

 /**
  * Function to download the document
  * @param integer $pnPk
  * @return string
  */

  private function _getDocumentSend($pnPk)
  {
    if(!assert('is_integer($pnPk) && !empty($pnPk)'))
      return 'No document found. It might have been removed already';

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM shared_document WHERE shared_documentpk = "'.$pnPk.'" ';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
      exit(__LINE__.' ERROR: no file found');

    $sFilePath = $oResult->getFieldValue('file_path');
    $sFileName = $oResult->getFieldValue('file_name');

    // Must be fresh start
    if(headers_sent())
      exit(__LINE__.' Headers already sent');

    // Required for some browsers
    if(ini_get('zlib.output_compression'))
      ini_set('zlib.output_compression', 'Off');

    // File Exists?
    if(!file_exists($sFilePath) )
      exit(__LINE__.' File doesn\'t exist on the server');

    // Parse Info / Get Extension
    $fsize = filesize($sFilePath);
    $path_parts = pathinfo($sFilePath);
    $ext = strtolower($path_parts["extension"]);

    // Determine Content Type
    switch ($ext)
    {
      case "pdf":  $ctype="application/pdf"; break;
      case "exe":  $ctype="application/octet-stream"; break;
      case "zip":  $ctype="application/zip"; break;
      case "doc":  $ctype="application/msword"; break;
      case "docx": $ctype="application/msword"; break;
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
    header("Content-Length: ".$fsize);
    ob_clean();
    flush();
    readfile($sFilePath);

    exit();
  }

  public function setLanguage($psLanguage)
  {
    require_once('language/language.inc.php5');
    if(isset($gasLang[$psLanguage]))
      $this->casText = $gasLang[$psLanguage];
    else
      $this->casText = $gasLang[CONST_DEFAULT_LANGUAGE];
  }
}

