<?php

require_once('component/portal/portal.class.ex.php5');

class CPortalBcmEx extends CPortalEx
{

  //Ajax function
  public function getAjax()
  {
    $this->_processUrl();

    switch($this->csType)
    {
      case CONST_PORTAL_STAT:
        $oPage = CDependency::getComponentByName('page');
        $sHTML = $this->_getUserStats(false, true);
        $asData = $oPage->getAjaxExtraContent(array('data' => $sHTML, 'action' => ' initChart(); '));
         return json_encode($asData);
          break;

      case CONST_PORTAL_CALENDAR:
        $oPage = CDependency::getComponentByName('page');
        $sHTML = $this->_getUserCalendar(false, true);
        $asData = $oPage->getAjaxExtraContent(array('data' => $sHTML));
         return json_encode($asData);
          break;
    }
  }

  // Function to display the home page tabs

  public function getHomePage()
  {
    /* TODO: at some point there will be custom page or preferences  */
    /* @var $oDisplay CDisplayEx */
    $oDisplay = CDependency::getComponentByName('display');
    $asHomepageTab = array();

    $sHTML = $oDisplay->getBlocStart('', array('class' => 'homepageContainer'));

     $asHomepageTab[] = array('tabtitle' => 'My BCM workspace', 'tabOptions' => array('tabId' => 'home_tab_mybcm','class' => ''), 'content' => $this->_getUserBcmTab(), 'contentOptions' => array('contentId'=>'home_tab_mybcm_content', 'class' => ''), 'tabstatus' => CONST_TAB_STATUS_SELECTED);
     $asHomepageTab[] = array('tabtitle' => 'What\'s new in BCM', 'tabOptions' => array('tabId' => 'home_tab_allbcm','class' => ''), 'content' => $this->_getAllBcmTab(), 'contentOptions' => array('contentId'=>'home_tab_allbcm_content', 'class' => 'hidden'), 'tabstatus' => CONST_TAB_STATUS_ACTIVE);
     $asHomepageTab[] = array('tabtitle' => 'Other apps & sites', 'tabOptions' => array('tabId' => 'home_tab_appsbcm','class' => ''), 'content' => $this->_getBcmAppsTab(), 'contentOptions' => array('contentId'=>'home_tab_appsbcm_content', 'class' => 'hidden'), 'tabstatus' => CONST_TAB_STATUS_ACTIVE);

     $sHTML.= $oDisplay->getTabs('', $asHomepageTab);

    $sHTML.= $oDisplay->getFloatHack();
    $sHTML.= $oDisplay->getBlocEnd();

    return $sHTML;
  }

  /**
   * Display the latest activities in BCM and display icons
   * @return type
   */

  private function _getAllBcmTab()
  {
    $oDisplay = CDependency::getComponentByName('display');
    $oAddressBook = CDependency::getComponentByName('addressbook');
    $sHTML = '';

    if(!empty($oAddressBook))
    {
       $sHTML.= $oDisplay->getBlocStart('', array('style' => 'margin-bottom: 20px;'));

       $asActivity = $this->_getRecentActivity();
       if(!empty($asActivity))
        {
          $sHTML.= $oDisplay->getBlocStart('', array('style' => 'float: left; width: 62%;'));
            $sHTML.= $oDisplay->getBlocStart('homepage_activityId',array('class'=>'homepageSection'));

              $sHTML.= $oDisplay->getBlocStart('',array('class'=>'homepageSectionTitlte'));
              $sHTML.= $oDisplay->getText('Latest entries in BCM');
              $sHTML.= $oDisplay->getBlocEnd();

              $sHTML.= $oDisplay->getBlocStart('',array('class'=>'homepageSectionInner'));
              $sHTML.= implode('', $asActivity);
              $sHTML.= $oDisplay->getBlocEnd();

              $sHTML.= $oDisplay->getFloatHack();

            $sHTML.= $oDisplay->getBlocEnd();
          $sHTML.= $oDisplay->getBlocEnd();
        }

        $sHTML.= $oDisplay->getBlocStart('', array('style' => 'float:right;width:35%;'));
        $sHTML.= $oDisplay->getBlocStart('',array('class'=>'homepageSection'));

        $sHTML.= $oDisplay->getBlocStart('',array('class'=>'homepageSectionTitlte'));
        $sHTML.= $oDisplay->getText('CRM icons');
        $sHTML.= $oDisplay->getBlocEnd();

        $sHTML.= $oDisplay->getBlocStart('',array('class'=>'homepageSectionInner'));

          $sHTML.= $oDisplay->getPicture('/component/display/resources/pictures/contact_48.png','BC Contact','',array('height'=>'24','width'=>'24'));
          $sHTML.= $oDisplay->getText(" <strong>BC contact page:</strong><br /> get the contact informations of BC people.");
          $sHTML.= $oDisplay->getCarriageReturn(2);

          $sHTML.= $oDisplay->getPicture('/component/display/resources/pictures/mail_48.png','BC Webmail','',array('height'=>'24','width'=>'24'));
          $sHTML.= $oDisplay->getText(" <strong>BC webmail:</strong><br /> access your email directly through the crm.");
          $sHTML.= $oDisplay->getCarriageReturn(2);

          $sHTML.= $oDisplay->getPicture('/tmp_home_icons/component.png','Shared folder','',array('height'=>'24','width'=>'24'));
          $sHTML.= $oDisplay->getText(" <strong>Shared folder:</strong><br /> access documents shared by other users.");
          $sHTML.= $oDisplay->getCarriageReturn(2);

          $sHTML.= $oDisplay->getPicture('/tmp_home_icons/cp_view_16.png','Company','',array('height'=>'24','width'=>'24'));
          $sHTML.= $oDisplay->getText(" <strong>Company:</strong><br /> company database, core of the future CRM functions.");
          $sHTML.= $oDisplay->getCarriageReturn(2);

          $sHTML.= $oDisplay->getPicture('/tmp_home_icons/ct_view_16.png','Connection','',array('height'=>'24','width'=>'24'));
          $sHTML.= $oDisplay->getText(" <strong>Connection:</strong><br /> connection database, core of the future CRM functions.");
          $sHTML.= $oDisplay->getCarriageReturn(2);

          $sHTML.= $oDisplay->getPicture('/tmp_home_icons/project_48.png','Project','',array('height'=>'24','width'=>'24'));
          $sHTML.= $oDisplay->getText(" <strong>Project:</strong><br /> define projects and affect tasks to other people.");
          $sHTML.= $oDisplay->getCarriageReturn(2);

          $sHTML.= $oDisplay->getPicture('/tmp_home_icons/menu_task_add.png','Task','',array('height'=>'24','width'=>'24'));
          $sHTML.= $oDisplay->getText(" <strong>Task:</strong><br /> create your own task and request other people help.");
          $sHTML.= $oDisplay->getCarriageReturn();

        $sHTML.= $oDisplay->getBlocEnd();
        $sHTML.= $oDisplay->getBlocEnd();
        $sHTML.= $oDisplay->getBlocEnd();

       $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageSection', 'style' => 'float:left;width:62%; '));
       $sHTML.= $oDisplay->getBlocStart('', array('style' => 'width: 98%; margin: 5px auto;'));
        $sHTML.= $oDisplay->getText('BCM v2.3:', array('class' => 'strong'));
        $sHTML.= $oDisplay->getCarriageReturn(2);

        $sHTML.= $oDisplay->getText('This update contains a lot of fixes and tweeks in all the different parts of the applications.
          We hope you\'ll feel more comfortable with BCM from now on. List of changes:');
        $sHTML.= $oDisplay->getCarriageReturn(2);

        $sHTML.= $oDisplay->getText('- Home page - Recent activity:');
        $sHTML.= $oDisplay->getCarriageReturn();
        $sHTML.= $oDisplay->getText('&nbsp;&nbsp;&nbsp;&nbsp;&#164; Slightly improve the design of the homepage');
        $sHTML.= $oDisplay->getCarriageReturn();
        $sHTML.= $oDisplay->getText('&nbsp;&nbsp;&nbsp;&nbsp;&#164; Display a longer history, including now views, adds, and updates recently done.');
        $sHTML.= $oDisplay->getCarriageReturn();
        $sHTML.= $oDisplay->getText('&nbsp;&nbsp;&nbsp;&nbsp;&#164; History messages are longer and more accurate.');

        $sHTML.= $oDisplay->getCarriageReturn(2);
        $sHTML.= $oDisplay->getText('- Connections and companies:');
        $sHTML.= $oDisplay->getCarriageReturn();
        $sHTML.= $oDisplay->getText('&nbsp;&nbsp;&nbsp;&nbsp;&#164; Display now parent and child companies in the company search results (demo company #4860 and #4861) ');
        $sHTML.= $oDisplay->getCarriageReturn();
        $sHTML.= $oDisplay->getText('&nbsp;&nbsp;&nbsp;&nbsp;&#164; The connections profile tab has been fixed and redesigned (demo connection #6197)  ');
        $sHTML.= $oDisplay->getCarriageReturn();
        $sHTML.= $oDisplay->getText('&nbsp;&nbsp;&nbsp;&nbsp;&#164; Company detail page: the document tab gathers the documents of the employees. (demo company #4860) ');

        $sHTML.= $oDisplay->getCarriageReturn(2);
        $sHTML.= $oDisplay->getText('- Activities:');
        $sHTML.= $oDisplay->getCarriageReturn();
        $sHTML.= $oDisplay->getText('&nbsp;&nbsp;&nbsp;&nbsp;&#164; Set reminders when creating/updating activities and receive an email when the reminder date is approaching');

        $sHTML.= $oDisplay->getCarriageReturn(2);
        $sHTML.= $oDisplay->getText('- Shared space:');
        $sHTML.= $oDisplay->getCarriageReturn();
        $sHTML.= $oDisplay->getText('&nbsp;&nbsp;&nbsp;&nbsp;&#164; You can now specify a type when adding/updating a document');
        $sHTML.= $oDisplay->getCarriageReturn();
        $sHTML.= $oDisplay->getText('&nbsp;&nbsp;&nbsp;&nbsp;&#164; You can filter the document list by type');


        $sHTML.= $oDisplay->getCarriageReturn(2);
       $sHTML.= $oDisplay->getBlocEnd();
       $sHTML.= $oDisplay->getBlocEnd();



      $sHTML.= $oDisplay->getBlocEnd();
      $sHTML.= $oDisplay->getFloatHack();
    }

    return $sHTML;
  }

  //Display user's bcm data about company, connection, project and task

  private function _getUserBcmTab()
  {
    $oLogin = CDependency::getComponentByName('login');
    $oDisplay = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oAddress = CDependency::getComponentByName('addressbook');

    $sHTML = $oDisplay->getBlocStart('', array('class' => 'homepageMySpace'));

     $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageMySpaceLink'));
     $sURL = $oPage->getUrl('addressbook', CONST_ACTION_LIST, CONST_AB_TYPE_COMPANY,0,array('loginpk'=>(int)$oLogin->getUserPk()));
     $sPic = $oDisplay->getPicture('/tmp_home_icons/company_24.png','My companies','',array('height'=>'24','width'=>'24'));
     $sHTML.= $oDisplay->getLink($sPic.$oDisplay->getText('My Companies'), $sURL);
     $sHTML.= $oDisplay->getBlocEnd();

     $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageMySpaceLink'));
     $sURL = $oPage->getUrl('addressbook', CONST_ACTION_LIST, CONST_AB_TYPE_CONTACT,0,array('loginpk'=>(int)$oLogin->getUserPk()));
     $sPic = $oDisplay->getPicture('/tmp_home_icons/ct_view_24.png','My Connections','',array('height'=>'24','width'=>'24'));
     $sHTML.= $oDisplay->getLink($sPic.$oDisplay->getText('My Connections'), $sURL);
     $sHTML.= $oDisplay->getBlocEnd();

     $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageMySpaceLink'));
     $sURL = $oPage->getUrl('sharedspace', CONST_ACTION_MANAGE, CONST_SS_TYPE_DOCUMENT);
     $sPic = $oDisplay->getPicture('/tmp_home_icons/component.png','My documents','',array('height'=>'24','width'=>'24'));
     $sHTML.= $oDisplay->getLink($sPic.$oDisplay->getText(" My documents"), $sURL);
     $sHTML.= $oDisplay->getBlocEnd();

     $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageMySpaceLink'));
     $sURL = $oPage->getUrl('project', CONST_ACTION_LIST, CONST_PROJECT_TYPE_TASK, 0, array(CONST_PROJECT_TASK_SORT_PARAM => 'project'));
     $sPic = $oDisplay->getPicture('/tmp_home_icons/menu_task_add.png','My tasks','',array('height'=>'24','width'=>'24'));
     $sHTML.= $oDisplay->getLink($sPic.$oDisplay->getText("My tasks"), $sURL);
     $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getFloatHack();
    $sHTML.= $oDisplay->getBlocEnd();
    $sHTML.= $oDisplay->getFloatHack();

    $sHTML.= $oDisplay->getBlocStart('', array('style' => 'width: 65%; float: left;'));

    //===================================================================
    //Activity on the follower
    if(!empty($oAddress))
    {
      $asContactActivity = $this->getContactRecentActivity((int)$oLogin->getUserPk());
      $oPage->addCssFile($oLogin->getResourcePath().'css/login.form.css');

      if(!empty($asContactActivity))
      {
        $sHTML.= $oDisplay->getBlocStart('homepage_contactactivityId', array('class' => 'homepageSection'));

         $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageSectionTitlte','style' => 'color: #F97807;'));
         $sHTML.= $oDisplay->getText(' Recent activity on my connections');
         $sHTML.= $oDisplay->getBlocEnd();

          $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageSectionInner', 'style' => 'min-height: 30px;'));
            foreach($asContactActivity as $asActivity)
            {
              $sHTML.= $oDisplay->getBlocStart('', array('style' => 'float: right; width: 100%;'));
              $asUserData = $oLogin->getUserDataByPk((int)$asActivity['loginfk']);
              $sHTML.= $asUserData['firstname'].' '.$asUserData['lastname'].' :';
              $sHTML.= $oDisplay->getSpace(2);
              $sURL = $asActivity['log_link'].'#ct_tab_eventId';
              $sText = $oDisplay->getExtract(strip_tags($asActivity['text']), 25);
              $sHTML.= $oDisplay->getLink($sText, $sURL);
              $sHTML.= $oDisplay->getSpace(2);

              $asContactData = $oAddress->getContactByPk((int)$asActivity['followerfk']);
              $sHTML.= $oDisplay->getText('on <strong>'.$asContactData['firstname'].' '.$asContactData['lastname'].'</strong>');
              $sHTML.= $oDisplay->getBlocEnd();
            }
            $sHTML.= $oDisplay->getFloatHack();
          $sHTML.= $oDisplay->getBlocEnd();

          $sHTML.= $oDisplay->getFloatHack();
          $sHTML.= $oDisplay->getBlocEnd();
          $sHTML.= $oDisplay->getFloatHack();
        }

      $asUserActivity = $this->_getUserRecentActivity((int)$oLogin->getUserPk(), 20);

      //Bloc  Recent activity
      if(empty($asUserActivity))
      {
        $sHTML.= $oDisplay->getBlocStart('homepage_contactactivityId', array('class' => 'homepageSection', 'style' => 'min-height: 300px;'));
        $sHTML.= $oDisplay->getText('Nothing done so far :/');
        $sHTML.= $oDisplay->getBlocEnd();
      }
      else
      {
        //===================================================================
        $sHTML.= $oDisplay->getBlocStart('homepage_useractivityId');

          $sHTML.= $oDisplay->getBlocStart('homepage_activityId', array('style' => 'min-height: 350px; float: left; width:100%;'));
          $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageSection'));

          $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageSectionTitlte'));
          $sHTML.= $oDisplay->getText('My recent activity');
          $sHTML.= $oDisplay->getBlocEnd();

          $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageSectionInner'));
          $sHTML.= $this->_getDisplayUserRecent($asUserActivity);
          $sHTML.= $oDisplay->getBlocEnd();
          $sHTML.= $oDisplay->getBlocEnd();
          $sHTML.= $oDisplay->getBlocEnd();

          $sHTML.= $oDisplay->getFloatHack();
        $sHTML.= $oDisplay->getBlocEnd();
       }

     }
     $sHTML.= $oDisplay->getFloatHack();
     $sHTML.= $oDisplay->getBlocEnd();

     $sHTML.= $this->_getUserCalendar(true);
     $sHTML.= $this->_getUserStats(true);

     $sHTML.= $oDisplay->getFloatHack();

     return $sHTML;
  }

  /**
   * Applications display function
   * @return string of HTML
   */

  private function _getBcmAppsTab()
  {
    $oLogin = CDependency::getComponentByName('login');
    $oDisplay = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = $oDisplay->getTitle('Websites', 'h2', true, array('onclick' => '$(\'#homepage_siteId\').fadeToggle();', 'class' => 'clickable'));

      $sHTML.= $oDisplay->getBlocStart('homepage_siteId');
      $sUrl = $oPage->getUrlEmbed('http://www.tokyoweekender.com?from=bccrm&pk='.$oLogin->getUserPk());
      $sPicture = $oDisplay->getPicture('/media/picture/tw.png', 'Tokyo Weekender', $sUrl);
      $sLink = $oDisplay->getLink('Tokyo Weekender', $sUrl, array('class'=>'homePageLink'));
      $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);

      $sUrl = $oPage->getUrlEmbed('http://www.asiadailywire.com?from=bccrm&pk='.$oLogin->getUserPk());
      $sPicture = $oDisplay->getPicture('/media/picture/adw.png', 'Asia Daily Wire', $sUrl);
      $sLink = $oDisplay->getLink('Asia Daily wire', $sUrl,array('class'=>'homePageLink'));
      $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);

      $sUrl = $oPage->getUrlEmbed('http://www.bulbouscell.com?from=bccrm&pk='.$oLogin->getUserPk());
      $sPicture = $oDisplay->getPicture('/media/picture/bc.png', 'Bulbouscell Media Group', $sUrl);
      $sLink = $oDisplay->getLink('Bulbouscell', $sUrl,array('class'=>'homePageLink'));
      $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);

      $sHTML.= $oDisplay->getFloatHack();
    $sHTML.= $oDisplay->getBlocEnd();

    //===================================================================
    //Bloc  Apps

    $sHTML.= $oDisplay->getTitle('Applications', 'h2', true, array('onclick' => '$(\'#homepage_appsId\').fadeToggle();', 'class' => 'clickable'));
    $sHTML.= $oDisplay->getBlocStart('homepage_appsId');

      $sUrl = $oPage->getUrlEmbed('http://www.bulbouscell.com/newsletter/admin/index.php?from=bccrm&pk='.$oLogin->getUserPk());
      $sPicture = $oDisplay->getPicture('/media/picture/news.png', '', $sUrl);
      $sLink = $oDisplay->getLink('News letter', $sUrl);
      $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);

      $sUrl = $oPage->getUrlEmbed('http://crm.bulbouscell.com?from=bccrm&pk='.$oLogin->getUserPk());
      $sPicture = $oDisplay->getPicture('/media/picture/crm.png', '', $sUrl);
      $sLink = $oDisplay->getLink('BCMedia CRM', $sUrl);
      $sHTML.= $this->_getHomePageTitleLink($sLink, $sPicture);

      $sUrl = $oPage->getUrlEmbed('http://bulbouscell.com/distribution/?from=bccrm&pk='.$oLogin->getUserPk());
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
         $sUrl = $oPage->getUrlEmbed('http://www.slate.co.jp?from=bccrm&pk='.$oLogin->getUserPk());
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
    $oLogin = CDependency::getComponentByName('login');
    $oDB = CDependency::getComponentByName('database');
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oProject = CDependency::getComponentUidByName('project');
    $oAddress = CDependency::getComponentUidByName('addressbook');
    $oSharedSpace = CDependency::getComponentUidByName('sharedspace');
    $oEvent = CDependency::getComponentUidByName('event');

    $asActivity = array();
    $asUsers = $oLogin->getUserList(0,true,true);


    if(!empty($oAddress))
    {
      $sQuery = 'SELECT * FROM contact WHERE created_by <> '.$oLogin->getUserPk().' ORDER BY date_create DESC LIMIT 3';
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
          $sActivityString.= $oHTML->getText(' - by '.$oLogin->getUserNameFromData($asUsers[$oDbResult->getFieldValue('created_by')]));
          $sActivityString.= $oHTML->getBlocEnd();

        $sActivityString.= $oHTML->getBlocEnd();
        $asActivity[] = $sActivityString;

        $bRead = $oDbResult->readNext();
      }

      $asActivity[] = $oHTML->getFloatHack();
      $asActivity[] = $oHTML->getBlocStart('', array('class' => 'hps_separator_top')).$oHTML->getBlocEnd();

      $sQuery = 'SELECT * FROM company WHERE creatorfk <> '.$oLogin->getUserPk().' ORDER BY date_create DESC LIMIT 3 ';
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
        $sActivityString.= $oHTML->getText(' - by '.$oLogin->getUserNameFromData($asUsers[$oDbResult->getFieldValue('creatorfk')]));
        $sActivityString.= $oHTML->getBlocEnd();

        $sActivityString.= $oHTML->getBlocEnd();
        $asActivity[] = $sActivityString;

        $bRead = $oDbResult->readNext();
      }
    }

    if(!empty($oEvent))
    {
      $asActivity[] = $oHTML->getFloatHack();
      $asActivity[] = $oHTML->getBlocStart('', array('class' => 'hps_separator_top')).$oHTML->getBlocEnd();

      $sQuery = ' SELECT event.*, event_link.*, login.lastname, login.firstname FROM event';
      $sQuery.= ' INNER JOIN event_link ON (event_link.eventfk = eventpk) ';
      $sQuery.= ' INNER JOIN login ON (event.created_by = loginpk) ';
      $sQuery.= ' WHERE created_by <> '.$oLogin->getUserPk().' ORDER BY date_create DESC LIMIT 3 ';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      while($bRead)
      {
        $sTitle = $oDbResult->getFieldValue('title').' - '.$oDbResult->getFieldValue('content');
        $sTitle = strip_tags($sTitle);
        if(strlen($sTitle) > 60)
          $sTitle = substr($sTitle, 0, 57).'...';

        $sActivityString = $oHTML->getBlocStart('', array('class' => 'homepageSectionRow'));

          $sActivityString.= $oHTML->getBlocStart('', array('class' => 'hpsRowDate'));
          $sActivityString.= $oHTML->getNiceTime($oDbResult->getFieldValue('date_create'), 0, true).':';
          $sActivityString.= $oHTML->getBlocEnd();

          $sActivityString.= $oHTML->getBlocStart('', array('class' => 'hpsRowData'));
            $sActivityString.= $oHTML->getPicture('/common/pictures/items/event_16.png').' ';
            $sURL = $oPage->getUrl('addressbook', $oDbResult->getFieldValue('cp_action'), $oDbResult->getFieldValue('cp_type'), $oDbResult->getFieldValue('cp_pk', CONST_PHP_VARTYPE_INT));
            $sActivityString.= $oHTML->getLink($sTitle, $sURL);
            $sActivityString.= $oHTML->getText(' - by '.$oLogin->getUserNameFromData($asUsers[$oDbResult->getFieldValue('created_by')]));
          $sActivityString.= $oHTML->getBlocEnd();

        $sActivityString.= $oHTML->getBlocEnd();
        $asActivity[] = $sActivityString;

        $bRead = $oDbResult->readNext();
      }
    }

    if(!empty($oSharedSpace))
    {
      $asActivity[] = $oHTML->getFloatHack();
      $asActivity[] = $oHTML->getBlocStart('', array('class' => 'hps_separator_top')).$oHTML->getBlocEnd();

      $sQuery = 'SELECT sd.*, sd2.shared_documentpk as parentpk, sd2.title as parent_title FROM shared_document as sd ';
      $sQuery.= ' LEFT JOIN shared_document as sd2 ON (sd2.shared_documentpk = sd.parentfk) ';
      $sQuery.= ' WHERE sd.creatorfk <> '.$oLogin->getUserPk().' ORDER BY sd.date_creation DESC LIMIT 3 ';
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
          $sActivityString.= $oHTML->getText(' - by '.$oLogin->getUserNameFromData($asUsers[$oDbResult->getFieldValue('creatorfk')]));
          $sActivityString.= $oHTML->getBlocEnd();

         $sActivityString.= $oHTML->getBlocEnd();
         $asActivity[] = $sActivityString;

        $bRead = $oDbResult->readNext();
      }
    }

    if(!empty($oProject))
    {
      $asActivity[] = $oHTML->getFloatHack();
      $asActivity[] = $oHTML->getBlocStart('', array('class' => 'hps_separator_top')).$oHTML->getBlocEnd();

      $sQuery = ' SELECT * FROM task as t ';
      $sQuery.= ' LEFT JOIN project_task as pt ON (pt.taskfk = t.taskpk) ';
      $sQuery.= ' WHERE creatorfk <> '.$oLogin->getUserPk().' ORDER BY date_created DESC LIMIT 3 ';
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
        $sActivityString.= $oHTML->getText(' - by '.$oLogin->getUserNameFromData($asUsers[$oDbResult->getFieldValue('creatorfk')]));
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

    if(empty($asUserRecentData))
      return $oHTML->getText('Nothing for now :\\');

    $sHTML = '';
    $sHTML.= $oHTML->getBlocStart('', array('style' => 'padding:2px;'));

    $sCompanyIcon = $oHTML->getPicture('/common/pictures/items/cp_16.png').' ';
    $sContactIcon = $oHTML->getPicture('/common/pictures/items/ct_16.png').' ';
    $sEventIcon = $oHTML->getPicture('/common/pictures/items/event_16.png').' ';

    $bFirst = true;
    foreach($asUserRecentData  as $sType => $asRecentData)
    {
      if($bFirst)
      {
        $bFirst = false;
        $sExtraClass = '';
      }
      else
        $sExtraClass = ' hps_separator_top ';

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'homepageSectionRow '.$sExtraClass));

      foreach($asRecentData as $asData)
      {
        $sHTML.= $oHTML->getBlocStart('', array('class' => 'hpsRowDate'));
        $sHTML.= $oHTML->getNiceTime($asData['log_date'], 0, true, false);
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('', array('class' => 'hpsRowData'));
        switch($sType)
        {
          case CONST_AB_TYPE_COMPANY:
            $sHTML.= $sCompanyIcon.$oHTML->getLink($asData['text'], $asData['log_link']); break;

          case CONST_AB_TYPE_CONTACT:
            $sHTML.= $sContactIcon.$oHTML->getLink($asData['text'], $asData['log_link']); break;

          case CONST_EVENT_TYPE_EVENT:
            $sHTML.= $sEventIcon;

            if(strlen($asData['text']) > 60)
              $asData['text'] = substr($asData['text'], 0, 57).'...';

            $sHTML.= $oHTML->getLink($asData['text'], $asData['log_link']);
            break;
        }

        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();
      }
      $sHTML.= $oHTML->getBlocEnd();

    }
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack')).$oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Get connection recent activity
   * @param integer $pnLoginPk
   * @return array of data
   */

  public function getContactRecentActivity($pnLoginPk)
  {
    if(!assert('is_integer($pnLoginPk)'))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM login_activity WHERE status = 0 AND followerfk!=0 AND notifierfk='.$pnLoginPk.' AND loginfk!='.$pnLoginPk;
    $oResult = $oDB->ExecuteQuery($sQuery);

    $bRead = $oResult->readFirst();
    $asContactActivities = array();
    while($bRead)
    {
      $asContactActivities[]= $oResult->getData();
      $bRead = $oResult->readNext();
    }

    return $asContactActivities;
  }

  /**
  * Display the recent activity of user
  * @param integer $pnLoginPk
  * @return array
  */

  private function _getUserRecentActivity($pnLoginPk, $pnLimit = 15)
  {
    if(!assert('is_integer($pnLoginPk) && is_integer($pnLimit)'))
        return array();

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM login_activity WHERE loginfk='.$pnLoginPk.' and followerfk=0 ';
    //$sQuery.= ' GROUP BY cp_uid, cp_action, cp_type, cp_pk ORDER BY log_date desc LIMIT 0, '.$pnLimit;
    $sQuery.= '  ORDER BY log_date desc LIMIT 0, '.$pnLimit;

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->ReadFirst();
    $asActivites = array();
    while($bRead)
    {
      $asActivites[$oResult->getFieldValue('cp_type')][] = $oResult->getData();
      $bRead = $oResult->ReadNext();
    }

    $asResultData = array();
    $asResultData['cp'] = $asResultData['ct'] = $asResultData['event'] = array();
    $nKey = 0;
    $nCount = 0;

    //limit to 15 results, prioritizing cp, ct , then events
    while($nCount < $pnLimit && $nKey < $pnLimit)
    {
      if(!empty($asActivites['cp']))
      {
        //get the next first entry of the array
        reset($asActivites['cp']);
        $vKey = key($asActivites['cp']);
        array_push($asResultData['cp'], $asActivites['cp'][$vKey]);

        //remove the element for next loop
        unset($asActivites['cp'][$vKey]);
        $nCount++;
      }

      if(!empty($asActivites['ct']))
      {
        //get the next first entry of the array
        reset($asActivites['ct']);
        $vKey = key($asActivites['ct']);
        array_push($asResultData['ct'], $asActivites['ct'][$vKey]);

        //remove the element for next loop
        unset($asActivites['ct'][$vKey]);
        $nCount++;
      }

      if(!empty($asActivites['event']))
      {
        //get the next first entry of the array
        reset($asActivites['event']);
        $vKey = key($asActivites['event']);
        array_push($asResultData['event'], $asActivites['event'][$vKey]);

        //remove the element for next loop
        unset($asActivites['event'][$vKey]);
        $nCount++;
      }
      $nKey++;
    }

    return $asResultData;
  }

  /**
    * Get user monthly stats
    * @return array
    */

  private function _getMonthlyUserStat()
  {
    $oLogin = CDependency::getComponentByName('login');
    $oDb = CDependency::getComponentByName('database');
    $anResult = array();
    $nLoginFk = $oLogin->getUserPk();

    //init a 4 months scale array
    $asData = array();
    $asData[(int)date('m', mktime(0, 0, 0, ((int)date('m')-3), 1, date('Y')))] = 0;
    $asData[(int)date('m', mktime(0, 0, 0, ((int)date('m')-2), 1, date('Y')))] = 0;
    $asData[(int)date('m', mktime(0, 0, 0, ((int)date('m')-1), 1, date('Y')))] = 0;
    $asData[(int)date('m')] = 0;

    $sStartDate = date('Y-m-d', mktime(0, 0, 0, ((int)date('m')-3), 1, date('Y')));

    //--------------------------------------------------------------
    //count connections

    $sQuery = 'SELECT count(*) as nCount, DATE_FORMAT(date_create, \'%c\') as grp_month FROM contact ';
    $sQuery.= ' WHERE created_by = '.$nLoginFk.' AND date_create > "'.$sStartDate.'" ';
    $sQuery.= ' GROUP BY grp_month ORDER BY grp_month DESC ';

    $oDbResult = $oDb->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $asTmp = $asData;
    while($bRead)
    {
      $asTmp[(int)$oDbResult->getFieldValue('grp_month')] = (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }
    $anResult['contact'] = $asTmp;

    //--------------------------------------------------------------
    //count Companies
    $sQuery = 'SELECT count(*) as nCount, DATE_FORMAT(date_create, \'%c\') as grp_month FROM company ';
    $sQuery.= ' WHERE creatorfk = '.$nLoginFk.' AND date_create > "'.$sStartDate.'" ';
    $sQuery.= ' GROUP BY grp_month ORDER BY grp_month DESC ';

    $oDbResult = $oDb->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $asTmp = $asData;
    while($bRead)
    {
      $asTmp[(int)$oDbResult->getFieldValue('grp_month')] = (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }
    $anResult['company'] = $asTmp;

    //--------------------------------------------------------------
    //count Companies
    $sQuery = 'SELECT count(*) as nCount, DATE_FORMAT(date_create, \'%c\') as grp_month FROM event ';
    $sQuery.= ' WHERE created_by = '.$nLoginFk.' AND date_create > "'.$sStartDate.'" ';
    $sQuery.= ' GROUP BY grp_month ORDER BY grp_month DESC ';

    $oDbResult = $oDb->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $asTmp = $asData;
    while($bRead)
    {
      $asTmp[(int)$oDbResult->getFieldValue('grp_month')] = (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }
    $anResult['event'] = $asTmp;

    return $anResult;
  }

   /**
    * Get user weekly stats
    * @return array
    */

  private function _getWeeklyUserStat()
  {
    $oLogin = CDependency::getComponentByName('login');
    $oDb = CDependency::getComponentByName('database');
    $anResult = array();
    $nLoginFk = $oLogin->getUserPk();

    //init a 1 week scale array (for now)
    $asData = array();
    $asData[(int)date('W', strtotime('-3 week'))] = 0;
    $asData[(int)date('W', strtotime('-2 week'))] = 0;
    $asData[(int)date('W', strtotime('last week'))] = 0;
    $asData[(int)date('W')] = 0;

    $sStartDate = date('Y-m-d', mktime(0, 0, 0, ((int)date('m')-1), 1, date('Y')));

    //--------------------------------------------------------------
    //count connections
    $sQuery = 'SELECT count(*) as nCount, DATE_FORMAT(date_create, \'%U\') as grp_week FROM contact ';
    $sQuery.= ' WHERE created_by = '.$nLoginFk.' AND date_create > "'.$sStartDate.'" ';
    $sQuery.= ' GROUP BY grp_week ORDER BY grp_week DESC ';
    //echo $sQuery;
    $oDbResult = $oDb->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $asTmp = $asData;
    while($bRead)
    {
      $asTmp[(int)$oDbResult->getFieldValue('grp_week')] = (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }
    $anResult['contact'] = $asTmp;

    //--------------------------------------------------------------
    //count Companies
    $sQuery = 'SELECT count(*) as nCount, DATE_FORMAT(date_create, \'%U\') as grp_week FROM company ';
    $sQuery.= ' WHERE creatorfk = '.$nLoginFk.' AND date_create > "'.$sStartDate.'" ';
    $sQuery.= ' GROUP BY grp_week ORDER BY grp_week DESC ';

    $oDbResult = $oDb->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $asTmp = $asData;
    while($bRead)
    {
      $asTmp[(int)$oDbResult->getFieldValue('grp_week')] = (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }
    $anResult['company'] = $asTmp;

    //--------------------------------------------------------------
    //count Companies
    $sQuery = 'SELECT count(*) as nCount, DATE_FORMAT(date_create, \'%U\') as grp_week FROM event ';
    $sQuery.= ' WHERE created_by = '.$nLoginFk.' AND date_create > "'.$sStartDate.'" ';
    $sQuery.= ' GROUP BY grp_week ORDER BY grp_week DESC ';

    $oDbResult = $oDb->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $asTmp = $asData;
    while($bRead)
    {
      $asTmp[(int)$oDbResult->getFieldValue('grp_week')] = (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }
    $anResult['event'] = $asTmp;

    return $anResult;
  }

  /**
   * Function to display the user calendar
   * @param boolean $pbLoadInAjax
   * @param boolean $pbOnlyAjaxContent
   * @return string HTML
  */
  private function _getUserCalendar($pbLoadInAjax = false, $pbOnlyAjaxContent = false)
  {
    $oDisplay = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $sHTML = '';

    if($pbLoadInAjax)
    {
      $oPage = CDependency::getComponentByName('page');
      $sUniqId = uniqid();
      $sUrl = $oPage->getAjaxUrl($this->_getUid(), CONST_ACTION_VIEW, CONST_PORTAL_CALENDAR);

      $sHTML = $oDisplay->getBlocStart($sUniqId, array( 'class' => 'homepageSection','style' => 'width: 32%; float: right;'));

      $sHTML.= $oDisplay->getBlocStart('',array('style'=>'width: 100%; text-align:center;'));
      //$sHTML.= $oDisplay->getPicture('/common/pictures/notice_loading.gif/','Loading','');
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= '<script>$(document).ready(function()
       {
          AjaxRequest(\''.$sUrl.'\', \'\', \'\', \''.$sUniqId.'\');
        });</script>';
      $sHTML.= $oDisplay->getBlocEnd();

      return $sHTML;
    }

    if(!$pbOnlyAjaxContent)
      $sHTML.= $oDisplay->getBlocStart('', array( 'class' => 'homepageSection', 'style' => 'width: 32%; float: right;'));

    $sHTML.= $oDisplay->getBlocStart('',array('class'=>'homepageSectionTitlte','style'=>'color: #FC8A07s;'));
    $sHTML.= $oDisplay->getText('My Calendar');
    $sHTML.= $oDisplay->getBlocEnd();

    $oZimbra = CDependency::getComponentByName('zimbra');
    $oCal = $oZimbra->getZimbraCalendar();
    $sHTML.= $oCal->getHomepageUserCalendar();

    if(!$pbOnlyAjaxContent)
      $sHTML.= $oDisplay->getBlocEnd();

     return $sHTML;
   }

  /**
    * Display the user stats
    * @param boolean $pbLoadInAjax
    * @param boolean $pbOnlyAjaxContent
    * @return HTML
    */

  private function _getUserStats($pbLoadInAjax = false, $pbOnlyAjaxContent = false)
  {
    $oDisplay = CDependency::getComponentByName('display');
    $oChart = CDependency::getComponentByName('charts');
    $sHTML = '';

    if($pbLoadInAjax)
    {
      //load in the homepage the chart js
      $oChart->includeChartsJs();

      $oPage = CDependency::getComponentByName('page');
      $sUniqId = uniqid();
      $sUrl = $oPage->getAjaxUrl($this->_getUid(), CONST_ACTION_VIEW, CONST_PORTAL_STAT);

      $sHTML.= $oDisplay->getCarriageReturn();
      $sHTML.= $oDisplay->getBlocStart($sUniqId, array( 'class' => 'homepageSection', 'style' => 'width: 32%; float: right; '));

        $sHTML.= '<div style="width: 100%; text-align: center;"><img src="/common/pictures/notice_loading.gif" /></div>';
        $sHTML.= '<script>$(document).ready(function()
          {
            AjaxRequest(\''.$sUrl.'\', \'\', \'\', \''.$sUniqId.'\');
          });</script>';

      $sHTML.= $oDisplay->getBlocEnd();
    }
    else
    {
      if(!$pbOnlyAjaxContent)
      {
        $sHTML.= $oDisplay->getCarriageReturn();
        $sHTML.= $oDisplay->getBlocStart('', array('class' => 'homepageSection', 'style' => 'width: 32%; float: right;'));
      }

      $asStats = $this->_getMonthlyUserStat();
      $asWeekStat = $this->_getWeeklyUserStat();
      $nWeek = (int)date('W');
      $nLastWeek = (int)date('W', strtotime('last week'));

      $sHTML.= $oDisplay->getBlocStart('', array('style' => 'width: 98%; margin: 0 auto;'));

        $sHTML.= $oDisplay->getListStart('', array('class' => 'homeStatList', 'style' => 'width: 48%; float: left;'));
          $sHTML.= $oDisplay->getListItemStart('', array('class' => 'homeStatListTitle')) . 'This week:'. $oDisplay->getListItemEnd();
          $sHTML.= $oDisplay->getListItemStart() . $asWeekStat['company'][$nWeek].' companies'. $oDisplay->getListItemEnd();
          $sHTML.= $oDisplay->getListItemStart() . $asWeekStat['contact'][$nWeek].' connections'. $oDisplay->getListItemEnd();
          $sHTML.= $oDisplay->getListItemStart() . $asWeekStat['event'][$nWeek].' activities'. $oDisplay->getListItemEnd();
        $sHTML.= $oDisplay->getListEnd();

        $sHTML.= $oDisplay->getListStart('', array('class' => 'homeStatList', 'style' => 'width: 48%; float: right;'));
          $sHTML.= $oDisplay->getListItemStart('', array('class' => 'homeStatListTitle')) . 'Last week:'. $oDisplay->getListItemEnd();
          $sHTML.= $oDisplay->getListItemStart() . $asWeekStat['company'][$nLastWeek].' companies'. $oDisplay->getListItemEnd();
          $sHTML.= $oDisplay->getListItemStart() . $asWeekStat['contact'][$nLastWeek].' connections'. $oDisplay->getListItemEnd();
          $sHTML.= $oDisplay->getListItemStart() . $asWeekStat['event'][$nLastWeek].' activities'. $oDisplay->getListItemEnd();
        $sHTML.= $oDisplay->getListEnd();

        $sHTML.= $oDisplay->getFloatHack();

        $asAxis = array();
        $nYear = (int)date('Y');
        foreach($asStats['contact'] as $sMonth => $vUseless)
        {
          $asAxis[] = date('M', mktime(0, 0, 0, (int)$sMonth, 1, $nYear));
        }

        $oChart->createChart('column', '', 'Added to bcm');
        $oChart->setChartLegendPosition('horizontal', 0, -5);
        $oChart->setChartAxis($asAxis);
        $oChart->setChartData('companies', $asStats['company']);
        $oChart->setChartData('connections', $asStats['contact']);
        $oChart->setChartData('activities', $asStats['event']);

        //if in ajax or get the content from ajax, I have to not include highcharts.js again
        $sHTML.= $oChart->getChartDisplay(($pbLoadInAjax||$pbOnlyAjaxContent));

      $sHTML.= $oDisplay->getBlocEnd();
      $sHTML.= $oDisplay->getFloatHack();
      $sHTML.= $oDisplay->getCarriageReturn();

      if(!$pbOnlyAjaxContent)
        $sHTML.= $oDisplay->getBlocEnd();
    }

    return $sHTML;
  }
}
?>
