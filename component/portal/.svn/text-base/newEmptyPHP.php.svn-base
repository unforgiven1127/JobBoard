private function _getHomePage()
  {
    /* TODO: at some point there will be custom page or preferences  */
    /* @var $oDisplay CDisplayEx */
    $oDisplay = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $asHomepageTab = array();
    $sHTML ='';

    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageContainer'));

      $asHomepageTab[] = array('tabtitle' => 'My BCM workspace', 'tabOptions'=>array('tabId'=>'home_tab_mybcm','class'=>''), 'content' => $this->_getUserBcmTab(), 'contentOptions'=>array('contentId'=>'home_tab_mybcm_content', 'class' => ''), 'tabstatus' =>CONST_TAB_STATUS_SELECTED);
      $asHomepageTab[] = array('tabtitle' => 'What\'s mew in BCM', 'tabOptions'=>array('tabId'=>'home_tab_allbcm','class'=>''), 'content' => $this->_getAllBcmTab(), 'contentOptions'=>array('contentId'=>'home_tab_allbcm_content', 'class' => 'hidden'), 'tabstatus' =>CONST_TAB_STATUS_ACTIVE);
      $asHomepageTab[] = array('tabtitle' => 'Other apps & sites', 'tabOptions'=>array('tabId'=>'home_tab_appsbcm','class'=>''), 'content' => $this->_getBcmAppsTab(), 'contentOptions'=>array('contentId'=>'home_tab_appsbcm_content', 'class' => 'hidden'), 'tabstatus' =>CONST_TAB_STATUS_ACTIVE);

      $sHTML.= $oDisplay->getTabs('', $asHomepageTab);

    $sHTML.= $oDisplay->getFloatHack();
    $sHTML.= $oDisplay->getBlocEnd();
    return $sHTML;
  }

  private function _getAllBcmTab()
  {
    $oDisplay = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oAddressBook = CDependency::getComponentByName('addressbook');
    $sHTML = '';

    if(!empty($oAddressBook))
    {
      //===================================================================
      //Bloc  Introduction
      //$sHTML.= $oDisplay->getTitle('What\'s new in BCM', 'h2', false, array('style' => 'margin-top: 0px;'));

      //$sHTML.= $oDisplay->getBlocStart('', array('class' => 'notice'));
      $sHTML.= $oDisplay->getBlocStart('', array('style' => 'margin-bottom: 20px;'));

        $asActivity = $this->_getRecentActivity();
        if(!empty($asActivity))
        {
          $sHTML.= $oDisplay->getBlocStart('', array('style' => 'float: left; width: 62%;'));
          $sHTML.= '<div id="homepage_activityId" class="homepageSection" ><div class="homepageSectionTitlte">Latest activities in BCM</div><div class="homepageSectionInner">';
          $sHTML.= implode('', $asActivity);
          $sHTML.= '</div></div>';
          $sHTML.= $oDisplay->getBlocEnd();
        }

        $sHTML.= $oDisplay->getBlocStart('', array('style' => 'float: right; width: 35%;'));
          $sHTML.= '<div class="homepageSection" ><div class="homepageSectionTitlte">CRM icons</div><div class="homepageSectionInner">';
          $sHTML.= "<img src='/component/display/resources/pictures/contact_48.png' height='24' width='24'> <strong>BC contact page:</strong><br /> get the contact informations of BC people. ".$oDisplay->getCarriageReturn(2);
          $sHTML.= "<img src='/component/display/resources/pictures/mail_48.png' height='24' width='24'> <strong>BC webmail:</strong><br /> access your email directly through the crm. ".$oDisplay->getCarriageReturn(2);
          $sHTML.= "<img src='/tmp_home_icons/component.png' height='24' width='24'> <strong>Shared folder:</strong><br /> access documents shared by other users. ".$oDisplay->getCarriageReturn(2);
          $sHTML.= "<img src='/tmp_home_icons/cp_view_16.png' height='24' width='24'> <strong>Company:</strong><br /> company database, core of the future CRM functions. ".$oDisplay->getCarriageReturn(2);
          $sHTML.= "<img src='/tmp_home_icons/ct_view_16.png' height='24' width='24'> <strong>Connection:</strong><br /> connection database, core of the future CRM functions. ".$oDisplay->getCarriageReturn(2);
          $sHTML.= "<img src='/tmp_home_icons/project_48.png' height='24' width='24'> <strong>Project:</strong><br /> define projects and affect tasks to other people. ".$oDisplay->getCarriageReturn(2);
          $sHTML.= "<img src='/tmp_home_icons/menu_task_add.png' height='24' width='24'> <strong>Task:</strong><br /> create your own task and request other people help. ".$oDisplay->getCarriageReturn(2);
          $sHTML.= $oDisplay->getCarriageReturn();
          $sHTML.= '</div></div>';
        $sHTML.= $oDisplay->getBlocEnd();



       $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageSection', 'style' => 'float: left; width: 62%;'));
       $sHTML.= 'Change log here ... & other news...';
       $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocEnd();
      $sHTML.= $oDisplay->getFloatHack();
    }

    return $sHTML;
  }

  private function _getUserBcmTab()
  {
    $oDisplay = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oAddress = CDependency::getComponentByName('addressbook');
    $sHTML = '';


    $sHTML.= '<div class="homepageMySpace" >';

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageMySpaceLink'));
        $sURL = $oPage->getUrl('addressbook', CONST_ACTION_LIST, CONST_AB_TYPE_COMPANY,0,array('loginpk'=>(int)$this->getUserPk()));
        $sHTML.= $oDisplay->getLink("<img src='/tmp_home_icons/cp_view_16.png' height='24' width='24'> My Companies", $sURL);
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageMySpaceLink'));
        $sURL = $oPage->getUrl('addressbook', CONST_ACTION_LIST, CONST_AB_TYPE_CONTACT,0,array('loginpk'=>(int)$this->getUserPk()));
        $sHTML.= $oDisplay->getLink("<img src='/tmp_home_icons/ct_view_16.png' height='24' width='24'> My Connections", $sURL);
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageMySpaceLink'));
        $sURL = $oPage->getUrl('sharedspace', CONST_ACTION_MANAGE);
        $sHTML.= $oDisplay->getLink("<img src='/tmp_home_icons/component.png' height='24' width='24'> My documents", $sURL);
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageMySpaceLink'));
        $sURL = $oPage->getUrl('project', CONST_ACTION_LIST, CONST_PROJECT_TYPE_TASK, 0, array(CONST_PROJECT_TASK_SORT_PARAM => 'project'));
        $sHTML.= $oDisplay->getLink("<img src='/tmp_home_icons/menu_task_add.png' height='24' width='24'> My tasks", $sURL);
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getFloatHack();

    $sHTML.= '</div >';
    $sHTML.= $oDisplay->getFloatHack();


    $sHTML.= $oDisplay->getBlocStart('', array('style' => 'width: 70%; float: left;'));

    //===================================================================
    //Activity on the follower
    if(!empty($oAddress))
    {
      $asContactActivity = $this->getContactRecentActivity((int)$this->getUserPk());
      $oPage->addCssFile($this->getResourcePath().'css/login.form.css');

      if(!empty($asContactActivity))
      {
        $sHTML.= $oDisplay->getBlocStart('homepage_contactactivityId', array('class' => 'homepageSection'));
        $sHTML.= '<div class="homepageSectionTitlte" style="color: #F97807">Recent activity on my connections</div>';
          $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageSectionInner', 'style' => 'min-height: 30px;'));
            foreach($asContactActivity as $asActivity)
            {
              $sHTML.= $oDisplay->getBlocStart('', array('style' => 'float: right; width: 100%;'));

              $asUserData = $this->getUserDataByPk((int)$asActivity['loginfk']);

              $sHTML.= $asUserData['firstname'].' '.$asUserData['lastname'].' :';
              $sHTML.= $oDisplay->getSpace(2);

              $sURL = $asActivity['log_link'].'#ct_tab_eventId';
              $sText = $oDisplay->getExtract(strip_tags($asActivity['text']), 25);
              $sHTML.= $oDisplay->getLink($sText, $sURL);
              $sHTML.= $oDisplay->getSpace(2);

              $asContactData = $oAddress->getContactByPk((int)$asActivity['followerfk']);
              $sHTML.= ' on <strong>'.$asContactData['firstname'].' '.$asContactData['lastname'].'</strong>';
              $sHTML.= $oDisplay->getBlocEnd();
            }
            $sHTML.= $oDisplay->getFloatHack();
          $sHTML.= $oDisplay->getBlocEnd();

          $sHTML.= $oDisplay->getFloatHack();
          $sHTML.= $oDisplay->getBlocEnd();
          $sHTML.= $oDisplay->getFloatHack();
        }



      $asUserActivity = $this->_getUserRecentActivity((int)$this->getUserPk());

      //===================================================================
      //Bloc  Recent activity
      if(!empty($asUserActivity))
      {
        //===================================================================
        //Bloc  Website
        $sHTML.= $oDisplay->getBlocStart('homepage_useractivityId');

          $sHTML.= $oDisplay->getBlocStart('homepage_activityId', array('style' => 'min-height: 150px; float: left; width:100%;'));
            $sHTML.= '<div class="homepageSection" ><div class="homepageSectionTitlte">My recent activity</div>';
            $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageSectionInner'));
            $sHTML.= $this->_getDisplayUserRecent($asUserActivity);
            $sHTML.= $oDisplay->getBlocEnd();
            $sHTML.= '</div >';
          $sHTML.= $oDisplay->getBlocEnd();

          $sHTML.= $oDisplay->getFloatHack();
        $sHTML.= $oDisplay->getBlocEnd();
       }

     }
     $sHTML.= $oDisplay->getFloatHack();
     $sHTML.= $oDisplay->getBlocEnd();

     //right section: calendar
     $sHTML.= $oDisplay->getBlocStart('', array( 'class' => 'homepageSection', 'style' => 'width: 27%; float: right;'));
     $sHTML.= '<div class="homepageSectionTitlte" style="color: #FC8A07s">My calendar</div>';

      $sHTML.= $oDisplay->getListStart('', array('class' => 'homeCalendarList'));

        $sHTML.= $oDisplay->getListItemStart('', array('class' => 'homeCalendarListTitle')) . 'Today:'. $oDisplay->getListItemEnd();
        $sHTML.= $oDisplay->getListItemStart() . '<img src="/common/pictures/items/calendar_16.png" /> 14:00: ... calendar entry ...'. $oDisplay->getListItemEnd();
        $sHTML.= $oDisplay->getListItemStart() . '<img src="/common/pictures/items/calendar_16.png" /> 18:30: ... calendar entry ...'. $oDisplay->getListItemEnd();

        $sHTML.= $oDisplay->getListItemStart('', array('class' => 'homeCalendarListTitle')) . 'Tomorrow:'. $oDisplay->getListItemEnd();
        $sHTML.= $oDisplay->getListItemStart() . '<img src="/common/pictures/items/calendar_16.png" /> 10:00: ... calendar entry ...'. $oDisplay->getListItemEnd();
        $sHTML.= $oDisplay->getListItemStart() . '<img src="/common/pictures/items/calendar_16.png" /> 11:30: ... calendar entry ...'. $oDisplay->getListItemEnd();

        $sHTML.= $oDisplay->getListItemStart('', array('class' => 'homeCalendarListTitle')) . 'Thursday:'. $oDisplay->getListItemEnd();
        $sHTML.= $oDisplay->getListItemStart() . '<img src="/common/pictures/items/calendar_16.png" /> 9:45: ... calendar entry ...'. $oDisplay->getListItemEnd();
        $sHTML.= $oDisplay->getListItemStart() . '<img src="/common/pictures/items/calendar_16.png" /> 17:15: ... calendar entry ...'. $oDisplay->getListItemEnd();

         $sHTML.= $oDisplay->getListItemStart('', array('class' => 'homeCalendarListTitle')) . ''. $oDisplay->getListItemEnd();
        $sHTML.= $oDisplay->getListItemStart() . '<img src="/common/pictures/items/calendar_add_16.png" /> Add an entry'. $oDisplay->getListItemEnd();

      $sHTML.= $oDisplay->getListEnd();
     $sHTML.= $oDisplay->getBlocEnd();

     return $sHTML;
  }


  private function _getBcmAppsTab()
  {
    $oDisplay = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = '';
    //===================================================================
    //Bloc  Website
    $sHTML.= $oDisplay->getTitle('Websites', 'h2', true, array('onclick' => '$(\'#homepage_siteId\').fadeToggle();', 'class' => 'clickable'));

      $sHTML.= $oDisplay->getBlocStart('homepage_siteId');
      $sUrl = $oPage->getUrlEmbed('http://www.tokyoweekender.com?from=bccrm&pk='.$this->getUserPk());
      $sPicture = $oDisplay->getPicture('/media/picture/tw.png', 'Tokyo Weekender', $sUrl);
      $sLink = $oDisplay->getLink('Tokyo Weekender', $sUrl, array('class'=>'homePageLink'));
      $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);

      $sUrl = $oPage->getUrlEmbed('http://www.asiadailywire.com?from=bccrm&pk='.$this->getUserPk());
      $sPicture = $oDisplay->getPicture('/media/picture/adw.png', 'Asia Daily Wire', $sUrl);
      $sLink = $oDisplay->getLink('Asia Daily wire', $sUrl,array('class'=>'homePageLink'));
      $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);

      $sUrl = $oPage->getUrlEmbed('http://www.bulbouscell.com?from=bccrm&pk='.$this->getUserPk());
      $sPicture = $oDisplay->getPicture('/media/picture/bc.png', 'Bulbouscell Media Group', $sUrl);
      $sLink = $oDisplay->getLink('Bulbouscell', $sUrl,array('class'=>'homePageLink'));
      $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);

      $sHTML.= $oDisplay->getFloatHack();
    $sHTML.= $oDisplay->getBlocEnd();

    //===================================================================
    //Bloc  Apps

    $sHTML.= $oDisplay->getTitle('Applications', 'h2', true, array('onclick' => '$(\'#homepage_appsId\').fadeToggle();', 'class' => 'clickable'));
    $sHTML.= $oDisplay->getBlocStart('homepage_appsId');

      $sUrl = $oPage->getUrlEmbed('http://www.bulbouscell.com/newsletter/admin/index.php?from=bccrm&pk='.$this->getUserPk());
      $sPicture = $oDisplay->getPicture('/media/picture/news.png', '', $sUrl);
      $sLink = $oDisplay->getLink('News letter', $sUrl);
      $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);

      $sUrl = $oPage->getUrlEmbed('http://crm.bulbouscell.com?from=bccrm&pk='.$this->getUserPk());
      $sPicture = $oDisplay->getPicture('/media/picture/crm.png', '', $sUrl);
      $sLink = $oDisplay->getLink('BCMedia CRM', $sUrl);
      $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);

      $sUrl = $oPage->getUrlEmbed('http://bulbouscell.com/distribution/?from=bccrm&pk='.$this->getUserPk());
      $sPicture = $oDisplay->getPicture('/media/picture/di.png', '', $sUrl);
      $sLink = $oDisplay->getLink('Distribution',  $sUrl);
      $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);

      $sHTML.= $oDisplay->getFloatHack();
    $sHTML.= $oDisplay->getBlocEnd();

    //===================================================================
    //Bloc  partner / IT
    $sHTML.= $oDisplay->getBlocStart('', array('style' => 'float:left; width: 100%;'));

      $sHTML.= $oDisplay->getBlocStart('', array('style' => 'width:100%; float:left; '));
        $sHTML.= $oDisplay->getTitle('Partner & other apps', 'h2', true, array('onclick' => '$(\'#homepage_partnerId\').fadeToggle();', 'class' => 'clickable')).$oDisplay->getCarriageReturn();

        $sHTML.= $oDisplay->getBlocStart('homepage_partnerId', array('style' => 'float: left; width: 35%;'));
          $sUrl = $oPage->getUrlEmbed('http://www.slate.co.jp?from=bccrm&pk='.$this->getUserPk());
          $sPicture = $oDisplay->getPicture('/media/picture/sl.png', '', $sUrl);
          $sLink = $oDisplay->getLink('Slate', $sUrl);
          $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);
          $sHTML.= $oDisplay->getFloatHack();
        $sHTML.= $oDisplay->getBlocEnd();

        $sHTML.= $oDisplay->getBlocStart('homepage_itId', array('style' => 'float: left; width: 35%;'));
          $sUrl = $oPage->getUrlEmbed('http://www.bulbouscell.com/infrastructure/');
          $sPicture = $oDisplay->getPicture('/media/picture/if.png', '', $sUrl);
          $sLink = $oDisplay->getLink('Infrastructure', $sUrl);
          $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);

          $sHTML.= $oDisplay->getFloatHack();
        $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocEnd();



    $sHTML.= $oDisplay->getFloatHack();
    $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getFloatHack();

    return $sHTML;
  }


  /**
   * Display the Title of the page
   * @param string $psLink
   * @param string $psPicture
   * @return HTML structure
   */
  private function _getHomePageTitleLink($psLink, $psPicture)
  {
    /* @var $oDisplay CDisplayEx */
    $oDisplay = CDependency::getComponentByName('display');

    $sHTML = $oDisplay->getBlocStart('', array('style' => 'height:100px; float:left;margin:10px;'));
    $sHTML.= $oDisplay->getBlocStart('', array('style' => 'float:left;'));
    $sHTML.= $psPicture;
    $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getBlocStart('', array('style' => 'float:left; vertical-align:middle; margin-left:10px;'));
    $sHTML.= $oDisplay->getCarriageReturn(2);
    $sHTML.= $psLink;
    $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getBlocEnd();

    return $sHTML;
  }

  /**
   * Display the recent activity of all users
   * @return HTML structure
   */

  private function _getRecentActivity()
  {
    //TODO: make it differently, that s crap
    $oDB = CDependency::getComponentByName('database');
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oProject = CDependency::getComponentUidByName('project');
    $oAddress = CDependency::getComponentUidByName('addressbook');
    $oSharedSpace = CDependency::getComponentUidByName('sharedspace');
    $oEvent = CDependency::getComponentUidByName('event');

    $asActivity = array();
    $asUsers = $this->getUserList(0,true,true);

    if(!empty($oAddress))
    {
      $sQuery = 'SELECT * FROM contact WHERE 1 ORDER BY date_create DESC LIMIT 3';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      while($bRead)
      {
        $sActivityString = $oHTML->getBlocStart('', array('class' => 'homepageSectionRow'));

          $sActivityString.= $oHTML->getBlocStart('', array('class' => 'hpsRowDate'));
          $sActivityString.= $oHTML->getNiceTime($oDbResult->getFieldValue('date_create'), 0, true).':';
          $sActivityString.= $oHTML->getBlocEnd();

          $sActivityString.= $oHTML->getBlocStart('', array('class' => 'hpsRowData'));
          $sActivityString.= $oHTML->getPicture('/common/pictures/items/ct_16.png').' ';
          $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $oDbResult->getFieldValue('contactpk', CONST_PHP_VARTYPE_INT));
          $sActivityString.= $oHTML->getLink($oDbResult->getFieldValue('lastname').' '.$oDbResult->getFieldValue('firstname'), $sURL);
          if(isset($asUsers[$oDbResult->getFieldValue('created_by')]))
          $sActivityString.= $oHTML->getText(' - by '.$this->getUserNameFromData($asUsers[$oDbResult->getFieldValue('created_by')]));
          $sActivityString.= $oHTML->getBlocEnd();

        $sActivityString.= $oHTML->getBlocEnd();
        $asActivity[] = $sActivityString;

        $bRead = $oDbResult->readNext();
      }

      $asActivity[] = $oHTML->getFloatHack();

      $sQuery = 'SELECT * FROM company WHERE 1 ORDER BY date_create DESC LIMIT 3 ';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      while($bRead)
      {
        $sActivityString = $oHTML->getBlocStart('', array('class' => 'homepageSectionRow'));

        $sActivityString.= $oHTML->getBlocStart('', array('class' => 'hpsRowDate'));
        $sActivityString.= $oHTML->getNiceTime($oDbResult->getFieldValue('date_create'), 0, true).':';
        $sActivityString.= $oHTML->getBlocEnd();

        $sActivityString.= $oHTML->getBlocStart('', array('class' => 'hpsRowData'));
        $sActivityString.= $oHTML->getPicture('/common/pictures/items/cp_16.png').' ';
        $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $oDbResult->getFieldValue('companypk', CONST_PHP_VARTYPE_INT));
        $sActivityString.= $oHTML->getLink($oDbResult->getFieldValue('company_name'), $sURL);
        $sActivityString.= $oHTML->getText(' - by '.$this->getUserNameFromData($asUsers[$oDbResult->getFieldValue('creatorfk')]));
        $sActivityString.= $oHTML->getBlocEnd();

        $sActivityString.= $oHTML->getBlocEnd();
        $asActivity[] = $sActivityString;

        $bRead = $oDbResult->readNext();
      }
    }

    if(!empty($oEvent))
    {
      $asActivity[] = $oHTML->getFloatHack();

      $sQuery = ' SELECT event.*, event_link.*, login.lastname, login.firstname FROM event';
      $sQuery.= ' INNER JOIN event_link ON (event_link.eventfk = eventpk) ';
      $sQuery.= ' INNER JOIN login ON (event.created_by = loginpk) ';
      $sQuery.= ' WHERE 1 ORDER BY date_create DESC LIMIT 3 ';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      while($bRead)
      {
        $sTitle = $oDbResult->getFieldValue('title');
        if(empty($sTitle))
          $sTitle = $oDbResult->getFieldValue('content');

        if(strlen($sTitle) > 25)
          $sTitle = substr($sTitle, 0, 25).'...';


        $sActivityString = $oHTML->getBlocStart('', array('class' => 'homepageSectionRow'));

        $sActivityString.= $oHTML->getBlocStart('', array('class' => 'hpsRowDate'));
        $sActivityString.= $oHTML->getNiceTime($oDbResult->getFieldValue('date_create'), 0, true).':';
        $sActivityString.= $oHTML->getBlocEnd();

        $sActivityString.= $oHTML->getBlocStart('', array('class' => 'hpsRowData'));
        $sActivityString.= $oHTML->getPicture('/common/pictures/items/event_16.png').' ';
        $sURL = $oPage->getUrl('addressbook', $oDbResult->getFieldValue('cp_action'), $oDbResult->getFieldValue('cp_type'), $oDbResult->getFieldValue('cp_pk', CONST_PHP_VARTYPE_INT));
        $sActivityString.= $oHTML->getLink($sTitle, $sURL);
        $sActivityString.= $oHTML->getText(' - by '.$this->getUserNameFromData($asUsers[$oDbResult->getFieldValue('created_by')]));
        $sActivityString.= $oHTML->getBlocEnd();
        $sActivityString.= $oHTML->getBlocEnd();
        $asActivity[] = $sActivityString;

        $bRead = $oDbResult->readNext();
      }
    }

    if(!empty($oSharedSpace))
    {
      $asActivity[] = $oHTML->getFloatHack();

      $sQuery = 'SELECT sd.*, sd2.shared_documentpk as parentpk, sd2.title as parent_title FROM shared_document as sd ';
      $sQuery.= ' LEFT JOIN shared_document as sd2 ON (sd2.shared_documentpk = sd.parentfk) ';
      $sQuery.= ' WHERE 1 ORDER BY sd.date_creation DESC LIMIT 3 ';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      while($bRead)
      {
        $sActivityString = $oHTML->getBlocStart('', array('class' => 'homepageSectionRow'));

        $sActivityString.= $oHTML->getBlocStart('', array('class' => 'hpsRowDate'));
        $sActivityString.= $oHTML->getNiceTime($oDbResult->getFieldValue('date_creation'), 0, true).':';
        $sActivityString.= $oHTML->getBlocEnd();

        $sActivityString.= $oHTML->getBlocStart('', array('class' => 'hpsRowData'));
        $sActivityString.= $oHTML->getPicture('/common/pictures/items/doc_16.png').' ';

        if($oDbResult->getFieldValue('title'))
        {
          $sTitle = $oDbResult->getFieldValue('title');
          $nPk = $oDbResult->getFieldValue('shared_documentpk', CONST_PHP_VARTYPE_INT);
        }
        else
        {
          $sTitle = $oDbResult->getFieldValue('parent_title');
          $nPk = $oDbResult->getFieldValue('parentfk', CONST_PHP_VARTYPE_INT);
        }

        if(strlen($sTitle) > 40)
          $sTitle = substr($sTitle, 0, 40).'...';

        $sURL = $oPage->getUrl('sharedspace', CONST_ACTION_SEND, CONST_SS_TYPE_DOCUMENT, $nPk);
        $sActivityString.= $oHTML->getLink(substr($sTitle, 0, 50), $sURL);
        if(isset($asUsers[$oDbResult->getFieldValue('creatorfk')]))
        $sActivityString.= $oHTML->getText(' - by '.$this->getUserNameFromData($asUsers[$oDbResult->getFieldValue('creatorfk')]));
        $sActivityString.= $oHTML->getBlocEnd();
        $sActivityString.= $oHTML->getBlocEnd();
        $asActivity[] = $sActivityString;

        $bRead = $oDbResult->readNext();
      }
    }
    if(!empty($oProject))
    {
      $asActivity[] = $oHTML->getFloatHack();

      $sQuery = ' SELECT * FROM task as t ';
      $sQuery.= ' LEFT JOIN project_task as pt ON (pt.taskfk = t.taskpk) ';
      $sQuery.= ' WHERE 1 ORDER BY date_created DESC LIMIT 3 ';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      while($bRead)
      {
        $sActivityString = $oHTML->getBlocStart('', array('class' => 'homepageSectionRow'));

        $sActivityString.= $oHTML->getBlocStart('', array('class' => 'hpsRowDate'));
        $sActivityString.= $oHTML->getNiceTime($oDbResult->getFieldValue('date_created'), 0, true).':';
        $sActivityString.= $oHTML->getBlocEnd();

        $sActivityString.= $oHTML->getBlocStart('', array('class' => 'hpsRowData'));
        $sActivityString.= $oHTML->getPicture('/common/pictures/items/task_16.png').' ';
        $sURL = $oPage->getUrl('project', CONST_ACTION_VIEW, CONST_PROJECT_TYPE_PROJECT, $oDbResult->getFieldValue('projectfk', CONST_PHP_VARTYPE_INT));
        $sActivityString.= $oHTML->getLink($oDbResult->getFieldValue('title'), $sURL);
        $sActivityString.= $oHTML->getText(' - by '.$this->getUserNameFromData($asUsers[$oDbResult->getFieldValue('creatorfk')]));
        $sActivityString.= $oHTML->getBlocEnd();
        $sActivityString.= $oHTML->getBlocEnd();
        $asActivity[] = $sActivityString;

        $bRead = $oDbResult->readNext();
      }
    }

    $asActivity[] = $oHTML->getBlocStart('', array('class' => 'floatHack')) . $oHTML->getBlocEnd();
    return $asActivity;
  }

  /**
   * Display the recent activities of user
   * @param array $asUserRecentData
   * @return string HTML structure
   */

  private function _getDisplayUserRecent($asUserRecentData)
  {
    if(!assert('is_array($asUserRecentData)'))
      return '';

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    if(empty($asUserRecentData))
       return $oHTML->getText('Nothing for now :\\');

    $sHTML = '';
    $sHTML.= $oHTML->getBlocStart('', array('style' => 'padding:2px;'));

    foreach ($asUserRecentData  as $asRecentData)
    {
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'homepageSectionRow'));
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'hpsRowDate'));
      $sHTML.= $oHTML->getNiceTime($asRecentData['log_date'], 0, true, false);
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'hpsRowData'));
      switch($asRecentData['cp_type'])
      {
        case CONST_AB_TYPE_COMPANY:
          $sHTML.= $oHTML->getText('Company - ').$oHTML->getLink($asRecentData['text'],$asRecentData['log_link']); break;

        case CONST_AB_TYPE_CONTACT:
          $sHTML.= $oHTML->getText('Connection - ').$oHTML->getLink($asRecentData['text'],$asRecentData['log_link']); break;

        case CONST_EVENT_TYPE_EVENT:
          $sHTML.= $oHTML->getText('Event - ').$oHTML->getLink($asRecentData['text'],$asRecentData['log_link']); break;
      }

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
    }
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack')).$oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;

  }
  /**
   * Display all the users from the system
   * @return string HTML
   */

  private function _getUserList()
  {
    /* @var $oDB  CDatabaseEx */
    /* @var $oDbResult  CDbResult */
    /* @var $oDisplay CDisplayEx */
    $oDisplay = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'css/login.form.css'));

    $oDB = CDependency::getComponentByName('database');
    $oDB->dbConnect();

    $sQuery = 'SELECT * FROM `login` WHERE  is_admin <> 1 ORDER BY is_admin, status DESC, firstname asc';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $sHTML = $oDisplay->getTitleLine($this->casText['LOGIN_USER_LIST'], $this->getResourcePath().'/pictures/contact_48.png');
    $sHTML.= $oDisplay->getCarriageReturn();


    $sHTML.= $oDisplay->getBlocStart('',array('style'=>'font-weight:bold;'));
    $sHTML.= $oDisplay->getLink($this->casText['LOGIN_SHOWHIDE_USER'],'javascript:;',array('onclick' =>'$(\'.hiddenUser\').fadeToggle(); '));
    $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getBlocStart('crazy', array('class' => 'ContactList'));
    $sHTML.= $oDisplay->getListStart('');

    //display list header
    $sHTML.= $oDisplay->getListItemStart('',array('class'=>'contactSheetHeader'));
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadRow'));

    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell'));
    $sHTML.= $oDisplay->getSpace(1);

    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell ContactListName'));
    $sHTML.= $oDisplay->getText($this->casText['LOGIN_NAME']);
    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell ContactListPosition'));
    $sHTML.= $oDisplay->getText($this->casText['LOGIN_POSITION']);
    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell ContactListEmail'));
    $sHTML.= $oDisplay->getText($this->casText['LOGIN_EMAIL']);
    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell ContactListPhone'));
    $sHTML.= $oDisplay->getText($this->casText['LOGIN_PHONE']);
    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell ContactListExt'));
    $sHTML.= $oDisplay->getText($this->casText['LOGIN_EXT']);
    $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListHeadCell ContactListPhone','style'=>'text-align:center;'));
    $sHTML.= $oDisplay->getText($this->casText['LOGIN_ACTION']);
    $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getBlocStart('', array('class' => 'floathack'));
    $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getListItemEnd('');

    while($bRead)
    {
      if((int)$oDbResult->getFieldValue('status') == 1)
       $sExtraClass = '';
      else
       $sExtraClass = 'hiddenUser';

      $sHTML.= $oDisplay->getListItemStart('',array('class'=>'contactSheet'));
      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListRow '.$sExtraClass ));

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell'));

      if((int)$oDbResult->getFieldValue('is_admin') == 1)
        $sHTML.= $oDisplay->getPicture($this->getResourcePath().'/pictures/admin_user.png');
      else
      {
        if((int)$oDbResult->getFieldValue('status') == 1)
          $sHTML.= $oDisplay->getPicture($this->getResourcePath().'/pictures/active_user.png');
        else
          $sHTML.= $oDisplay->getPicture($this->getResourcePath().'/pictures/inactive_user.png');
       }

      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('',array('class'=>$sExtraClass));

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell ContactListName'));
      $sHTML.= $this->getUserNamefromData($oDbResult->getData(),false,false,false);
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell ContactListPosition'));
      $sHTML.= $oDbResult->getFieldValue('position');
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell ContactListEmail'));
      $sHTML.= $oDisplay->getLink($oDbResult->getFieldValue('email'), 'mailto:'.$oDbResult->getFieldValue('email'));
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell ContactListPhone'));
      $sHTML.= $oDisplay->getLink($oDbResult->getFieldValue('phone'), 'callto&#58;'.$oDbResult->getFieldValue('phone'));
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell ContactListExt'));
      $sHTML.= $oDbResult->getFieldValue('phone_ext');
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'ContactListCell ContactListPhone' ,'style'=>'text-align:center;'));

      $sURL = $oPage->getUrl('login', CONST_ACTION_MANAGE, CONST_LOGIN_TYPE_USER, $oDbResult->getFieldValue('loginpk',CONST_PHP_VARTYPE_INT), array(CONST_URL_ACTION_RETURN => CONST_ACTION_LIST));
      $sHTML.= $oDisplay->getPicture(CONST_PICTURE_EDIT, $this->casText['LOGIN_EDIT_USER'], $sURL);
      $sHTML.= $oDisplay->getSpace(2);

      if($oDbResult->getFieldValue('status')==1)
      {
       $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_DELETE, CONST_LOGIN_TYPE_USER, $oDbResult->getFieldValue('loginpk',CONST_PHP_VARTYPE_INT),array('status'=>$oDbResult->getFieldValue('status')));
       $sPic= $oDisplay->getPicture(CONST_PICTURE_DELETE,$this->casText['LOGIN_DEACTIVATE_USER']);
       $sHTML.= ' '.$oDisplay->getLink($sPic, $sURL, array('onclick' => 'if(!window.confirm(\''.$this->casText['LOGIN_DEACTIVATE'].'\')){ return false; }'));
      }
      else
      {
        $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_DELETE, CONST_LOGIN_TYPE_USER, $oDbResult->getFieldValue('loginpk',CONST_PHP_VARTYPE_INT),array('status'=>$oDbResult->getFieldValue('status')));
        $sPic= $oDisplay->getPicture(CONST_PICTURE_REACTIVATE,$this->casText['LOGIN_REACTIVATE_USER']);
        $sHTML.= ' '.$oDisplay->getLink($sPic, $sURL, array('onclick' => 'if(!window.confirm(\''.$this->casText['LOGIN_REACTIVATE'].'\')){ return false; }'));
      }

      $sHTML.= $oDisplay->getBlocEnd();
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('', array('class' => 'floathack'));
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocEnd();
     $sHTML.= $oDisplay->getListItemEnd('');

    $bRead = $oDbResult->readNext();
   }

   $sHTML.= $oDisplay->getListEnd('');

   $sHTML.= $oDisplay->getBlocStart('', array('class' => 'floathack'));
   $sHTML.= $oDisplay->getBlocEnd();
   $sHTML.= $oDisplay->getBlocEnd();

  return $sHTML;
  }