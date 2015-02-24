<?php

require_once('component/event/event.class.php5');

class CEventEx extends CEvent
{
  private $coCalendar = null;

  public function __construct()
  {
    $this->coCalendar = CDependency::getComponentByName('zimbra');
    return true;
  }

  public function getDefaultType()
  {
    return CONST_EVENT_TYPE_EVENT;
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
    return $asActions;
  }

  public function getAjax()
  {
    $this->_processUrl();

    switch($this->csType)
    {
      case CONST_EVENT_TYPE_EVENT:
        switch($this->csAction)
        {
          case CONST_ACTION_SAVEADD:
            return json_encode($this->_getEventSave($this->cnPk));
              break;

          case CONST_ACTION_DELETE:
           return json_encode($this->_getEventDelete($this->cnPk));
             break;

        }
        break;

      case CONST_EVENT_TYPE_REMINDER:
        switch($this->csAction)
        {
          case CONST_ACTION_DELETE:
           return json_encode($this->_getReminderDelete($this->cnPk));
             break;
        }
        break;
    }
  }


  public function getCronJob()
  {
    echo 'Event cron  <br />';

    $this->_sendReminders();

    return '';
  }


  //====================================================================
  //  Component core
  //====================================================================


  /**
   * Search for activity using keywords
   * @param string $psSearchWord
   * @return array
   */

  public function search($psSearchWord)
  {
    if(!assert('!empty($psSearchWord)'))
      return array();

    if(strlen($psSearchWord) < 3)
      return array('Search query is too short.');

    $asSearchWord = explode(' ', trim($psSearchWord));
    $asWhere = array();
    foreach($asSearchWord as $sSearchWord)
    {
      if(strlen($sSearchWord) >= 2)
      {
        $asWhere[] = '(title LIKE "%$psFilter%" OR content LIKE "%$psFilter%" ) ';
      }
    }

    if(empty($asWhere))
      return array('nb' => 0, 'data' => array());

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT COUNT(*) as nCount FROM event WHERE '.implode(' AND ', $asWhere).' LIMIT 100 ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    return array('nb' => $oDbResult->getFieldValue('nCount', CONST_PHP_VARTYPE_INT), 'data' => array());
  }

  //Not Implemented function
  private function _getSearchEventList($psFilter)
  {
    return array('nb' => 0, 'data' => array());
  }

  //====================================================================
  //  public methods
  //====================================================================

  public function getHtml()
  {
    $this->_processUrl();

    switch($this->csType)
    {
      case CONST_EVENT_TYPE_EVENT:
        switch($this->csAction)
        {
          case CONST_ACTION_LIST:
            return $this->getEventList($this->cnPk, getValue(CONST_EVENT_ITEM_UID), getValue(CONST_EVENT_ITEM_ACTION), getValue(CONST_EVENT_ITEM_TYPE), getValue(CONST_EVENT_ITEM_PK, 0));
             break;

          case CONST_ACTION_ADD:
          case CONST_ACTION_EDIT:
            return $this->_getEventForm($this->cnPk);
             break;
        }
        break;
    }
  }

  /**
   * Get the SQL realted to activities for connection of  addressbook component
   * @return array of records
   */

  public function getActivitySql()
  {
    $asResult = array();

    $asResult['select'] = 'event.title as title,event.content as content,event.date_display as date_display';
    $asResult['join'] = 'LEFT JOIN event_link AS evel ON evel.cp_pk = ct.contactpk and evel.cp_type = "ct" LEFT JOIN event AS event ON event.eventpk = evel.eventfk ';

    return $asResult;
  }

   /**
   * Get the SQL realted to activities for company of  addressbook component
   * @return array of records
   */

  /*
  public function getCompanyActivitySql()
  {
    $oDb = CDependency::getComponentByName('database');
    $asResult['select'] = 'evl.cp_pk as itempk,evl.cp_type as itemtype,eve.title as title,eve.content as content,eve.date_display as date_display';
    /*
    $asResult['select'] = 'eve.*,even.*';

    $sQuery1 = 'SELECT max(evel.eventfk) as eventfk,c.companypk as cp_pk,evel.cp_pk as cp_contactfk,evel.cp_type FROM event_link as evel INNER JOIN profil as prf ON prf.contactfk = evel.cp_pk
    INNER JOIN company c ON c.companypk = prf.companyfk
    WHERE evel.cp_type = "ct"  group by c.companypk ';

    $oDbResult = $oDb->ExecuteQuery($sQuery1);
    $bRead = $oDbResult->readFirst();
    $asCtEvents = array();
    if($bRead)
    {
      while($bRead)
      {
       $asCtEvents[$oDbResult->getFieldValue('eventfk')] = $oDbResult->getFieldValue('cp_pk');
       $asContactEvents[$oDbResult->getFieldValue('eventfk')] = $oDbResult->getFieldValue('cp_contactfk');
       $bRead = $oDbResult->readNext();
     }
    }

    $sQuery2 = ' SELECT max(eventfk) as eventfk ,cp_pk,0 as cp_contactfk,cp_type FROM event_link WHERE cp_type = "cp" GROUP BY cp_pk ';
    $oDbResult = $oDb->ExecuteQuery($sQuery2);
    $bRead = $oDbResult->readFirst();
    $asCpEvents = array();
    if($bRead)
    {
      while($bRead)
      {
       $asCpEvents[$oDbResult->getFieldValue('eventfk')] = $oDbResult->getFieldValue('cp_pk');
       $bRead = $oDbResult->readNext();
     }
    }

    $asResult['join'] = ' LEFT JOIN event_link as evl ON ( evl.cp_pk = cp.companypk  AND ( evl.cp_type = "cp" AND evl.eventfk IN ('.implode(',',array_keys($asCpEvents)).' )  AND evl.cp_pk IN ('.implode(',',$asCpEvents).' ) ) ) LEFT JOIN event as eve ON (evl.eventfk =  eve.eventpk)';
    $asResult['join'].= ' LEFT JOIN event_link as evel ON ( cp.companypk in ('.implode(',',$asCtEvents).' )  AND ( evel.cp_type = "ct" AND evel.eventfk IN ('.implode(',',array_keys($asCtEvents)).' )  AND evel.cp_pk IN ('.implode(',',$asContactEvents).' ) ) ) LEFT JOIN event as even ON (evel.eventfk =  even.eventpk)';

    return $asResult;

  }*/

  public function getCompanyActivitySql()
  {

    $asResult['select'] = 'evel2.cp_pk as itempk,evel2.cp_type as itemtype,event.title as title,event.content as content,event.date_display as date_display,event2.title as title2,event2.content as content2,event2.date_display as date_display2';
    $asResult['join'] = ' LEFT JOIN event_link AS evel ON evel.cp_pk = cp.companypk and evel.cp_type = "cp" LEFT JOIN event AS event ON event.eventpk = evel.eventfk';
    $asResult['join'].= ' LEFT JOIN event_link AS evel2 on  evel2.cp_pk = prf.contactfk and evel2.cp_type = "ct"  LEFT JOIN event AS event2 ON event2.eventpk = evel2.eventfk ';

    return $asResult;

  }

  /**
   * Delete the event
   * @param integer $pnPK
   * @return array with notice
   */

  private function _getEventDelete($pnPK)
  {
    if(!assert('is_integer($pnPK) && !empty($pnPK)'))
      return array('error' => __LINE__.' - No activity identifier.');

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM `event` WHERE eventpk = '.$pnPK.' ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array('error' => __LINE__.' - No event to delete.');

    $sQuery = 'DELETE FROM event WHERE eventpk = '.$pnPK.' ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);

    $sQuery = 'DELETE FROM event_link WHERE eventfk = '.$pnPK.' ';
    $oResult = $oDB->ExecuteQuery($sQuery);

    if(!$oResult)
      return array('error' => __LINE__.' - Couldn\'t delete the activity');

    return array('notice' => 'Activity deleted successfully.','reload' =>1);
  }

  /**
  * Function to list the events
  * @param integer eventpk $pnPk
  * @param string component $psUid
  * @param string action $psAction
  * @param string type $psType
  * @param integer value $pnKey
  * @return string HTML
  */
  public function getEventList($pnPk = 0, $psUid = '', $psAction = '', $psType = '', $pnKey = 0)
  {
    if(!assert('is_integer($pnPk) && is_integer($pnKey)'))
      return '';

    if(empty($pnPk) && empty($psUid))
    {
      assert('false; // Activity need PK or component to find an event.');
      return '';
    }

    if($this->coCalendar)
    {
      $sExtraSelect = ' , calLink.cp_params as calUserFk';
      $sExtraSql = ' LEFT JOIN event_link as calLink ON (calLink.eventfk = ev.eventpk AND calLink.cp_type = "'.CONST_ZCAL_EVENT.'") ';
    }
    else
    {
      $sExtraSelect = $sExtraSql = '';
    }

    $oDB = CDependency::getComponentByName('database');
    if(!empty($pnPk))
    {
      $sCountQuery = 'SELECT COUNT(*) as nCount FROM event as ev ';
      $sCountQuery.= 'INNER JOIN event_link as el ON (el.eventfk = ev.eventpk AND el.eventfk = '.$pnPk.') ';

      $sQuery = 'SELECT * FROM event as ev ';
      $sQuery.= ' INNER JOIN event_link as el ON (el.eventfk = ev.eventpk AND el.eventfk = '.$pnPk.') ';
      $sQuery.= ' INNER JOIN login as lo ON (lo.loginpk = ev.created_by) ';
      $sQuery.= ' LEFT JOIN event_reminder as evr ON (evr.eventfk = ev.eventpk) ';
    }
    else if($psType==CONST_AB_TYPE_CONTACT)
    {
      $sCountQuery = 'SELECT COUNT(*) as nCount FROM event as ev ';
      $sCountQuery.= ' INNER JOIN event_link as el ON (el.eventfk = ev.eventpk AND el.cp_uid = '.$oDB->dbEscapeString($psUid).' ';
      $sCountQuery.= ' AND el.cp_action = '.$oDB->dbEscapeString($psAction).' AND el.cp_type = '.$oDB->dbEscapeString($psType).' ';
      $sCountQuery.= ' AND el.cp_pk = '.$oDB->dbEscapeString($pnKey).') ';

      $sQuery = 'SELECT ev.*, lo.*, el.*, GROUP_CONCAT(evr.loginfk) as reminder_recipient '.$sExtraSelect.' FROM event as ev ';
      $sQuery.= ' INNER JOIN event_link as el ON (el.eventfk = ev.eventpk AND el.cp_uid = '.$oDB->dbEscapeString($psUid).' ';
      $sQuery.= ' AND el.cp_action = '.$oDB->dbEscapeString($psAction).' AND el.cp_type = '.$oDB->dbEscapeString($psType).' ';
      $sQuery.= ' AND el.cp_pk = '.$oDB->dbEscapeString($pnKey).') ';
      $sQuery.= $sExtraSql;
      $sQuery.= ' INNER JOIN login as lo ON (lo.loginpk = ev.created_by) ';
      $sQuery.= ' LEFT JOIN event_reminder as evr ON (evr.eventfk = ev.eventpk) ';
      $sQuery.= ' GROUP BY ev.eventpk order by ev.type, ev.date_create desc ';
    }
    else if($psType==CONST_AB_TYPE_COMPANY)
    {
      $sCountQuery = 'SELECT COUNT(*) as nCount FROM event as ev';
      $sCountQuery.= ' INNER JOIN event_link as el ON (el.eventfk = ev.eventpk )';

      $sCountQuery.= ' LEFT JOIN profil as prf on (prf.companyfk= '.$oDB->dbEscapeString($pnKey).' ) ';
      $sCountQuery.= ' WHERE (el.cp_uid = '.$oDB->dbEscapeString($psUid).' AND el.cp_action = '.$oDB->dbEscapeString($psAction).' AND  el.cp_type = '.$oDB->dbEscapeString($psType).' AND el.cp_pk = '.$oDB->dbEscapeString($pnKey).')';
      $sCountQuery.='  OR ( el.cp_type = "ct" AND el.cp_pk = prf.contactfk)';

      $sQuery = 'SELECT ev.*, prf.*, lo.*, el.*, GROUP_CONCAT(evr.loginfk) as reminder_recipient '.$sExtraSelect.' FROM event as ev';
      $sQuery.= ' INNER JOIN event_link as el ON (el.eventfk = ev.eventpk )';
      $sQuery.= ' LEFT JOIN profil as prf on (prf.companyfk= '.$oDB->dbEscapeString($pnKey).' ) ';
      $sQuery.= ' INNER JOIN login as lo ON (lo.loginpk = ev.created_by)';
      $sQuery.= ' LEFT JOIN event_reminder as evr ON (evr.eventfk = ev.eventpk) ';
      $sQuery.= $sExtraSql;
      $sQuery.= ' WHERE (el.cp_uid = '.$oDB->dbEscapeString($psUid).' AND el.cp_action = '.$oDB->dbEscapeString($psAction).' AND  el.cp_type = '.$oDB->dbEscapeString($psType).' AND el.cp_pk = '.$oDB->dbEscapeString($pnKey).')';
      $sQuery.='  OR ( el.cp_type = "ct" AND el.cp_pk = prf.contactfk)';
      $sQuery.= ' GROUP BY ev.eventpk order by ev.type,ev.date_create desc ';
    }

    $oResult = $oDB->ExecuteQuery($sCountQuery);
    $bRead = $oResult->readFirst();
    if(!$bRead)
      return '';

    $nCountEvent = $oResult->getFieldValue('nCount', CONST_PHP_VARTYPE_INT);
    if($nCountEvent == 0)
      return '';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
    {
      assert('false; // Count query found result ?_? ');
      return '';
    }

    $oHTML = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'css/event.css');

    $sHTML = $this->_getEventLeft();
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'eventListContainer'));
    $sType = '';
    $asAllTabData = array();

    while($bRead)
    {
      $asEventData = $oDbResult->getData();
      $isRecentActivity = $oLogin->getCheckRecentActivity((int)$asEventData['eventpk'],(int)$asEventData['cp_pk']);

      if($isRecentActivity != 0)
        $oLogin->getUpdateRecentActivity($isRecentActivity);

      if($sType != $asEventData['type'])
      {
        if(!empty($sType))
          $sHTML.= $oHTML->getListEnd();

        $sHTML.= $oHTML->getListStart('eventId_'.$asEventData['type'],array('class'=>'eventData', 'style'=>'min-height:85px; display:none;'));
        $sType = $asEventData['type'];
      }

      $asAllTabData[$asEventData['date_display'].'_'.$asEventData['eventpk']] = $asEventData;

      $sHTML.= $oHTML->getListItemStart();
      $sHTML.= $this->_getEventRow($asEventData);
      $sHTML.= $oHTML->getListItemEnd();

      $bRead = $oDbResult->readNext();
    }

    $sHTML.= $oHTML->getListEnd();

    if(!empty($asAllTabData))
    {
      arsort($asAllTabData);
      $sHTML.= $oHTML->getListStart('eventId_All',array('class'=>'eventData', 'style'=>'min-height:85px;'));

      foreach($asAllTabData as $asEventData)
      {
        $sHTML.= $oHTML->getListItemStart();
        $sHTML.= $this->_getEventRow($asEventData);
        $sHTML.= $oHTML->getListItemEnd();
      }

      $sHTML.= $oHTML->getListEnd();
    }

    $sJavascriptCode = "
      $(document).ready(function()
      {
        $('.expandClass').click(function()
        {
          var oRow  = $(this).closest('.smallDivs');
          var nHeight = oRow.find('.eventRowFull').height() + 25;

          oRow.find('.eventRowFull:not(:visible)').attr('style', 'display: none; height:'+nHeight+'px;');
          oRow.find('.eventRowContent:visible').fadeToggle(350, function(){  oRow.find('.eventRowFull').fadeToggle(650); });
          oRow.find('.eventRowFull:visible').fadeToggle(350, function(){  oRow.find('.eventRowContent').fadeToggle(650); });
        });
       });";
    $oPage->addCustomJs($sJavascriptCode);

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Get the Left Hand Side of Event
   * @return string HTML
   */

  private function _getEventLeft()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = $oHTML->getListStart('eventTypeListId');
    $asEventTypes = getEventTypeList();

    $sComponent = getValue('uid', '', '', true);
    $sItemAction = getValue('ppa', '', '', true);
    $sItemType = getValue('ppt', '', '', true);
    $nItemPk = getValue('ppk', '', '', true);
    $nTotal = 0;
    $sTabs = '';

    foreach($asEventTypes as $asEventData)
    {
      $sEventType = $asEventData['value'];
      $vEvents = $asEventData['label'];

      $sTabs.= $oHTML->getListItemStart('event_'.$vEvents, array('class'=>'eventType', 'tab'=>'eventId_'.$sEventType));
      $sTabs.= $oHTML->getText(''.$sEventType.'');
      $nCount = $this->getCountEventInformation($sComponent, $sItemAction, $sItemType, (int)$nItemPk, $sEventType);
      $nTotal+= $nCount;
      $sTabs.= $oHTML->getText(' ('.$nCount.')');
      $sTabs.= $oHTML->getListItemEnd();
     }

    $sHTML.= $oHTML->getListItemStart('event_all', array('style' => '','class'=>'eventType eventTypeSelected','tab'=> 'eventId_All'));
    $sHTML.= $oHTML->getText('All ('.$nTotal.')');
    $sHTML.= $oHTML->getListItemEnd();
    $sHTML.= $sTabs;

     $sJavascriptCode = "
       $(document).ready(function()
       {
         $('.eventType').click(function()
         {
           if($(this).hasClass('eventTypeSelected'))
             return true;

            var sId = $(this).attr('tab');

            $('.eventType:not(this)').removeClass('eventTypeSelected');
            $(this).addClass('eventTypeSelected');

            $('.eventData').fadeOut(250);
            $('#'+sId).delay(300).fadeIn(600);
          });
        }); ";
      $oPage->addCustomJs($sJavascriptCode);

      $sHTML.= $oHTML->getListEnd();
      return $sHTML;
  }

  /**
   * Get the Event Rows
   * @param array $pasEventData
   * @return string HTML
   */

  private function _getEventRow($pasEventData)
  {
    if(!assert('is_array($pasEventData) && !empty($pasEventData)'))
      return '';

    $oHTML = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');

    $nUserPk = $oLogin->getUserPk();

    $sHTML = $oHTML->getBlocStart('', array('class' => 'eventDetailContainer'));
    //--------------------------------
    //Display the first of the 2 rows
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'eventDetailRow'));

      //Date and Creator
      $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:16%; min-width: 190px; '));
      $sHTML.= $oHTML->getText($pasEventData['date_display']);
      $sHTML.= $oHTML->getCarriageReturn();
      $sHTML.= $oHTML->getText('by '.$oLogin->getUserNameFromData($pasEventData));

      if($this->coCalendar && $pasEventData['calUserFk'] == $nUserPk)
      {
        $sHTML.= $oHTML->getCarriageReturn().$oHTML->getSpace(3);
        $sHTML.= $oHTML->getPicture($this->coCalendar->getResourcePath().'pictures/calendar_16.png');
        $sHTML.= $oHTML->getText(' <i>in my calendar</i>');
      }
      $sHTML.= $oHTML->getBlocEnd();

      //----------------------------------------
      //display reminder icon
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'eventListReminder'));

      if(empty($pasEventData['reminder_recipient']))
         $sHTML.= $oHTML->getSpace();
      else
      {
        $asRecipient = explode(',', $pasEventData['reminder_recipient']);
        if(in_array($oLogin->getUserPk(), $asRecipient))
          $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/reminder_16.png', 'You have a reminder linked to this activity', '', array('class' => 'hasLegend'));
        else
          $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/reminder_inactive_16.png', 'Other user(s) have set reminder(s) on this activity', '', array('class' => 'hasLegend'));
      }

      $sHTML.= $oHTML->getBlocEnd();

      $sShortContent = strip_tags($pasEventData['content']);
      if(strlen($sShortContent) > 190)
      {
        $bContentCut = true;
        $sShortContent = substr($sShortContent, 0, 170).' ...<a href="javascript:;" class="expandClass italic">see more</a>';
        $sPic = 'event_detail_expand.png';
        $sClass = 'expandClass';
      }
      else
      {
        $bContentCut = false;
        $sPic = 'event_detail_expanded.png';
        $sClass = '';
      }

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'smallDivs', 'style' => 'width: 65%'));
      $sTitle = $oHTML->getPicture($this->getResourcePath().'pictures/'.$sPic);
      $sTitle.= $oHTML->getSpace(2);
      if(!empty($pasEventData['title']))
      {
        $sEventTitle = $pasEventData['title'].': ';
        $sTitle = $oHTML->getLink($sTitle.$sEventTitle, 'javascript:;', array('class' => $sClass.''));
      }
      else
      {
        $sEventTitle = 'No Title: ';
        $sTitle = $oHTML->getLink($sTitle.$sEventTitle, 'javascript:;', array('class' => $sClass.' light italic'));
      }

      $sHTML.= $oHTML->getHtmlContainer($sTitle. $sShortContent, '', array('class'=> 'eventRowContent'));
      if($bContentCut)
      {
        $sTitle = $oHTML->getLink($sEventTitle, 'javascript:;', array('class' => $sClass));
        $sHTML.= $oHTML->getHtmlContainer($sTitle.'<br /><br />'.$pasEventData['content'], '', array('class'=> 'eventRowFull ', 'style' => 'display: none;'));
      }

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:10%;'));
      $sHTML.= $this->_getEventRowAction($pasEventData);
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack')) . $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack')) . $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Get Action for the Events
   * @param array $pasEventData
   * @return string HTML
   */

  private function _getEventRowAction($pasEventData)
  {
    if(!assert('is_array($pasEventData)'))
      return '';

    $oRight = CDependency::getComponentByName('right');
    $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_DELETE,CONST_EVENT_TYPE_EVENT,0);

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = $oHTML->getBlocStart('', array('style' => 'float:right;margin-top:10px;'));
    $sUrl = $oPage->getUrl('event', CONST_ACTION_EDIT, CONST_EVENT_TYPE_EVENT, (int)$pasEventData['eventpk'], array(CONST_EVENT_ITEM_UID => $pasEventData['cp_uid'], CONST_EVENT_ITEM_ACTION => $pasEventData['cp_action'], CONST_EVENT_ITEM_TYPE => $pasEventData['cp_type'], CONST_EVENT_ITEM_PK =>(int)$pasEventData['cp_pk']));
    $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/edit_event_16.png', 'Edit event',$sUrl,array('title'=>'Edit activity'));
    $sHTML.= $oHTML->getSpace(2);

    if($sAccess)
    {
      $sURL = $oPage->getAjaxUrl('event', CONST_ACTION_DELETE, CONST_EVENT_TYPE_EVENT,(int)$pasEventData['eventpk']);
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/delete_event_16.png','Delete event');
      $sHTML.= ' '.$oHTML->getLink($sPic, $sURL, array('onclick' => 'if(!window.confirm(\'Delete this activity ?\')){ return false; }'));
     }

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
  * Display the event form
  * @param integer  $pnPk
  * @return string HTML
  */
  private function _getEventAddForm($pnPk)
  {
    if(!assert('is_integer($pnPk)'))
      return '';

    $oHTML = CDependency::getComponentByName('display');

    //Fetch the data from the calling component
    $sCp_Uid = getValue(CONST_EVENT_ITEM_UID, 0);
    if(empty($sCp_Uid))
      return $oHTML->getBlocMessage(__LINE__.' - Oops, missing some informations to create an activity.');

    $sCp_Action = getValue(CONST_EVENT_ITEM_ACTION);
    $sCp_Type = getValue(CONST_EVENT_ITEM_TYPE);
    $nCp_Pk = (int)getValue(CONST_EVENT_ITEM_PK, 0);

    if($sCp_Type == CONST_AB_TYPE_COMPANY)
    {
      $nCompanyPk = $nCp_Pk;
      $nContactPk = 0;
    }
    else
    {
      $nCompanyPk = 0;
      $nContactPk = $nCp_Pk;
    }

    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'/css/event.css'));

    $oLogin = CDependency::getComponentByName('login');
    $oDB = CDependency::getComponentByName('database');
    $oAddressBook = CDependency::getComponentByName('addressbook');
    $sABookUid = $oAddressBook->getComponentUid();

    $nUser = $oLogin->getUserPk();
    $asLinkedItems = array();

    //If editing the contact
    if(!empty($pnPk))
    {
      $sQuery = 'SELECT * FROM event as ev ';
      $sQuery.= 'INNER JOIN event_link as el ON (el.eventfk = ev.eventpk AND el.eventfk = '.$pnPk.') ';

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
        return __LINE__.' - The contact doesn\'t exist.';
      while($bRead)
      {
        $asEventLink = $oDbResult->getData();
        $asLinkedItems[] = $asEventLink['cp_uid'].'&'.$asEventLink['cp_action'].'&'.$asEventLink['cp_type'].'&'.$asEventLink['cp_pk'];
        $bRead = $oDbResult->readNext();
      }

      $asReminder = $this->getEventReminderByPk($pnPk);
    }
    else
    {
      $oDbResult = new CDbResult();
      $asReminder = array();
    }

    if($oPage->getActionReturn())
      $sURL = $oPage->getAjaxUrl('event', CONST_ACTION_SAVEADD, CONST_EVENT_TYPE_EVENT, $pnPk, array(CONST_URL_ACTION_RETURN => $oPage->getActionReturn()));
    else
      $sURL = $oPage->getAjaxUrl('event', CONST_ACTION_SAVEADD, CONST_EVENT_TYPE_EVENT, $pnPk);

    $sHTML= $oHTML->getBlocStart();

    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('evtAddForm');
    $oForm->setFormParams('', true, array('action' => $sURL, 'class' => 'fullPageForm','submitLabel'=>'Save'));

    $oForm->addField('input', CONST_EVENT_ITEM_UID, array('type' => 'hidden', 'value' => $sCp_Uid));
    $oForm->addField('input', CONST_EVENT_ITEM_ACTION, array('type' => 'hidden', 'value' => $sCp_Action));
    $oForm->addField('input', CONST_EVENT_ITEM_TYPE, array('type' => 'hidden', 'value' => $sCp_Type));
    $oForm->addField('input', CONST_EVENT_ITEM_PK, array('type' => 'hidden', 'value' => $nCp_Pk));

    $sEventItemName = $oAddressBook->getItemName(getValue(CONST_EVENT_ITEM_TYPE),(int)getValue(CONST_EVENT_ITEM_PK));
    $oForm->addField('misc', '', array('type' => 'text','text'=> '<span class="h4">Activity description on '.$sEventItemName.'</span><hr /><br />'));

    $sDate = $oDbResult->getFieldValue('date_display');
    if(empty($sDate))
      $sDate = date('Y-m-d H:i');
    else
       $sDate = date('Y-m-d H:i', strtotime($sDate));

    $oForm->addField('input', 'date_event', array('type' => 'datetime', 'label'=>'Date', 'value' => $sDate));
    $oForm->addField('select', 'event_type', array('label'=>'Activity type'));
    $oForm->setFieldControl('event_type', array('jsFieldNotEmpty' => ''));

    $oForm->addOption('event_type', array('value'=> '', 'label' => 'Select', 'group' => ''));
    $asEvent= getEventTypeList();
    foreach($asEvent as $asEvents)
    {
      if($asEvents['value'] == $oDbResult->getFieldValue('type'))
        $oForm->addOption('event_type', array('value'=>$asEvents['value'], 'label' => $asEvents['label'], 'group' => $asEvents['group'], 'selected'=>'selected'));
      else
        $oForm->addOption('event_type', array('value'=>$asEvents['value'], 'label' => $asEvents['label'], 'group' => $asEvents['group']));
     }

    $oForm->addField('input', 'title', array('label'=>'Activity title', 'value' => $oDbResult->getFieldValue('title')));
    $oForm->setFieldControl('title', array('jsFieldMinSize' => '2','jsFieldMaxSize' => 255));

    $oForm->addField('textarea', 'content', array('label'=>'Description', 'value' => $oDbResult->getFieldValue('content'), 'isTinymce' => 1));
    $oForm->setFieldControl('content', array('jsFieldMinSize' => '2','jsFieldMaxSize' => 4096));


    if($sABookUid == $sCp_Uid)
    {
      $asEmployees = $oAddressBook->getEmployeeList($nCompanyPk, $nContactPk, true);

      if(!empty($asEmployees))
      {
        $oForm->addField('select', 'link_to[]', array('label'=> 'Also involved', 'multiple' => 'multiple'));

        //Put compani(es) first (holding / child)
        $anCompanyTreated = array();
        foreach($asEmployees as $nPk => $asEmployeeData)
        {
          if(!empty($asEmployeeData['companyfk']) && $asEmployeeData['companyfk'] != $nCompanyPk && !in_array($asEmployeeData['companyfk'], $anCompanyTreated))
          {
            $sValue = $sABookUid.'&'.CONST_ACTION_VIEW.'&'.CONST_AB_TYPE_COMPANY.'&'.$asEmployeeData['companyfk'];
            $sLabel = '&copy; '.$asEmployeeData['company_name'].' (#'.$asEmployeeData['companyfk'].')';

            if(in_array($sValue, $asLinkedItems))
              $oForm->addOption('link_to[]', array('value'=> $sValue, 'label' => $sLabel, 'class' => 'bsmOptionCompany', 'selected' => 'selected'));
            else
              $oForm->addOption('link_to[]', array('value'=> $sValue, 'label' => $sLabel, 'class' => 'bsmOptionCompany'));

            $anCompanyTreated[] = $asEmployeeData['companyfk'];
          }
        }

        $bDisplayCpName = (count($anCompanyTreated) > 1);
        foreach($asEmployees as $nPk => $asEmployeeData)
        {
          if($asEmployeeData['contactpk'] != $nContactPk)
          {
            $sValue = $sABookUid.'&'.CONST_ACTION_VIEW.'&'.CONST_AB_TYPE_CONTACT.'&'.$asEmployeeData['contactpk'];
            $sLabel = $oAddressBook->getContactNameFromData($asEmployeeData);
            if($bDisplayCpName)
              $sLabel.= '&nbsp;&nbsp;&nbsp;&nbsp;('.$asEmployeeData['company_name'].')';

            if(in_array($sValue, $asLinkedItems))
              $oForm->addOption('link_to[]', array('value'=> $sValue, 'label' => $sLabel, 'class' => 'bsmOptionContact', 'selected' => 'selected'));
            else
              $oForm->addOption('link_to[]', array('value'=> $sValue, 'label' => $sLabel, 'class' => 'bsmOptionContact'));
          }
        }
      }
    }

    $oForm->addField('misc', '', array('type' => 'text', 'text'=> '<br/><span class="h4">Notification & reminder </span><hr /><br />'));

    //===================================================================
    // Notification section
    $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_SEARCH, CONST_LOGIN_TYPE_USER, 0, array('team' => 1));
    $oForm->addField('selector', 'notify', array('label'=> 'Share this event with ', 'url' => $sURL, 'nbresult' => 12));

    $oForm->addField('misc', '', array('type' => 'text', 'text'=> '<div class="eventFormSeparator">&nbsp;</div>'));


    //===================================================================
    // Reminder section
    if(!empty($asReminder) )
    {
      $asPreviousReminder = array();
      $asUsers = $oLogin->getUserList(0, false);

      foreach($asReminder as $asData)
      {
        if(isset($asUsers[$asData['loginfk']]))
        {
          $sReminder = 'To '.$asUsers[$asData['loginfk']]['firstname'].' on the '.$asData['date_reminder'];

          if((int)$asData['loginfk'] == $nUser)
          {
            $sUid = uniqid('evt_del_');
            $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_DELETE, CONST_EVENT_TYPE_REMINDER, $asData['event_reminderpk'], array('html_uid' => $sUid));
            $sReminder = '<span id="'.$sUid.'">'.$sReminder.'&nbsp;&nbsp; <a href="javascript:;" onclick="if(window.confirm(\'Delete this reminder ?\')){ AjaxRequest(\''.$sURL.'\', \'body\'); }">XXX</a></span>';
          }

          $sReminder.= '<br />';
          $asPreviousReminder[] = $sReminder;
        }
      }

      if(count($asPreviousReminder) > 0)
      {
        $sReminderInfo = $oHTML->getBlocStart('', array('class' => 'previousReminderBloc '));
          $sReminderInfo.= $oHTML->getLink(count($asPreviousReminder).' existing reminder(s)', 'javascript:;', array('onclick' => '$(this).parent().find(\'.previousReminders\').fadeToggle();'));

          $sReminderInfo.= $oHTML->getBlocStart('', array('class' => 'previousReminders hidden'));
          $sReminderInfo.= implode('', $asPreviousReminder);
          $sReminderInfo.= $oHTML->getBlocEnd();
        $sReminderInfo.= $oHTML->getBlocEnd();

        $oForm->addField('misc', '', array('type'=> 'text', 'text' => $sReminderInfo));
      }
    }

    $oForm->addField('input', 'reminder_date', array('type' => 'datetime', 'label'=>'Reminder date', 'keepNextInline'=> 1));
    $oForm->setFieldDisplayParams('reminder_date', array('class' => 'eventFieldInline'));

    $oForm->addField('select', 'reminder_before', array('label'=>'Reminder sent', 'keepNextInline'=> 1));
    $oForm->setFieldDisplayParams('reminder_before', array('class' => 'eventFieldInline'));

      $oForm->addOption('reminder_before', array('value'=> '1h', 'label' => '1 hour before'));
      $oForm->addOption('reminder_before', array('value'=> '2h', 'label' => '2 hours before'));
      $oForm->addOption('reminder_before', array('value'=> 'halfday', 'label' => 'half a day before'));
      $oForm->addOption('reminder_before', array('value'=> 'fullday', 'label' => 'a full day before'));

    $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_SEARCH, CONST_LOGIN_TYPE_USER, 0);
    $oForm->addField('selector', 'reminder_user', array('label'=> 'Recipient', 'url' => $sURL, 'nbresult' => 1));
    $oForm->addOption('reminder_user', array('value'=> $nUser, 'label' => $oLogin->getUsername(true)));

    $oForm->addField('textarea', 'reminder_message', array('label'=>'Message'));
    $oForm->setFieldControl('content', array('jsFieldMinSize' => '2','jsFieldMaxSize' => 4096));

    //===================================================================
    // Calendar section
    if($this->coCalendar)
    {
      $oForm->addField('misc', '', array('type' => 'text', 'text'=> '<div class="eventFormSeparator">&nbsp;</div>'));

      $sUrl = $oPage->getAjaxUrl('zimbra', CONST_ACTION_ADD, CONST_ZCAL_EVENT, 0);
      $oForm->addField('checkbox', 'addCalendar', array('label'=> 'Add to my calendar', 'value' => 1, 'textbefore' => 1, 'onchange' => 'if(this.checked){ AjaxPopup(\''.$sUrl.'\', \'#componentContainerId\', false, 0, 950); } '));
      $oForm->setFieldDisplayParams('addCalendar', array('class' => 'addCalendarBox'));
      $oForm->addField('misc', '', array('type'=> 'br'));
    }

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }


  /**
   * Display the event form
   * @param integer  $pnPk
   * @return string HTML
   */

  private function _getEventForm($pnPk)
  {
    if(!assert('is_integer($pnPk)'))
      return '';

    $oHTML = CDependency::getComponentByName('display');

    //Fetch the data from the calling component
    $sCp_Uid = getValue(CONST_EVENT_ITEM_UID);
    if(empty($sCp_Uid))
     return $oHTML->getBlocMessage(__LINE__.' - Oops, missing some informations to create an activity.');

    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');

    //If editing the contact
    if(!empty($pnPk))
    {
      $sQuery = 'SELECT * FROM event as ev ';
      $sQuery.= 'INNER JOIN event_link as el ON (el.eventfk = ev.eventpk AND el.eventfk = '.$pnPk.') ';

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
        return __LINE__.' - The contact doesn\'t exist.';
    }
    else
      $oDbResult = new CDbResult();

    if($oPage->getActionReturn())
      $sURL = $oPage->getAjaxUrl('event', CONST_ACTION_SAVEADD, CONST_EVENT_TYPE_EVENT, $pnPk, array(CONST_URL_ACTION_RETURN => $oPage->getActionReturn()));
    else
      $sURL = $oPage->getAjaxUrl('event', CONST_ACTION_SAVEADD, CONST_EVENT_TYPE_EVENT, $pnPk);

    $sHTML= $oHTML->getBlocStart();
      $sHTML.= $oHTML->getBlocStart('', array('class' =>'bottom_container'));
      $sHTML.= $this->_getEventAddForm($pnPk);
      $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Save the activity
   * @param integer $pnPk
   * @return type array
   */

  private function _getEventSave($pnPk = 0)
  {
    if(!assert('is_integer($pnPk)'))
      return array('error' => __LINE__.' - Wrong parameter');

    //component calling event
    $sItemUid = getValue(CONST_EVENT_ITEM_UID);
    $sItemAction = getValue(CONST_EVENT_ITEM_ACTION);
    $sItemType = getValue(CONST_EVENT_ITEM_TYPE);
    $nItemPk = (int)getValue(CONST_EVENT_ITEM_PK, 0);

    $sEventDate = getValue('date_event');
    $sEventDate = date('Y-m-d H:i:s',strtotime($sEventDate));

    $sType = getValue('event_type');
    $sTitle = getValue('title');
    $sContent = getValue('content');
    $asCoworkerInvolved = (array)getValue('link_to', array());
    $sNotification = getValue('notify');
    $nCalendarFk = getValue('addCalendar', 0);

    if(empty($sTitle) && empty($sContent))
      return array('alert' =>'Enter activity title or content.');

    if(empty($sEventDate) || !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}/', $sEventDate))
      return array('error' => __LINE__.' - Date is invalid.');

    if(empty($sTitle) && empty($sContent))
      return array('error' => __LINE__.' - Fill title or description.');

    $sReminderDate = getvalue('reminder_date');
    $nReminderTime = strtotime($sReminderDate);
    $sReminderBefore = getvalue('reminder_before');
    $nReminderUser = (int)getvalue('reminder_user', 0);
    $sReminderMsg = getvalue('reminder_message');

    if(!empty($sReminderDate) || !empty($sReminderMsg))
    {
      if(empty($nReminderUser))
        return array('error' => __LINE__.' - You need to specify who the reminder recipient is.');

      if(empty($sReminderBefore))
        return array('error' => __LINE__.' - The delay before the reminder is sent is required.');

      if(!$nReminderTime || $nReminderTime < (time()+3600))
        return array('error' => __LINE__.' - Reminder should be set at least 1 hour ahead of now.');
    }

    $oLogin = CDependency::getComponentByName('login');
    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');
    $oMail = CDependency::getComponentByName('mail');
    $oAddressBook = CDependency::getComponentByName('addressbook');
    $oHTML = CDependency::getComponentByName('display');

    if(empty($pnPk))
    {
      $sQuery = 'INSERT INTO `event` (`type`, `title`, `content`, `date_create`, `date_display`, `created_by`) ';
      $sQuery.= ' VALUES ('.$oDB->dbEscapeString($sType).', '.$oDB->dbEscapeString($sTitle).', '.$oDB->dbEscapeString($sContent).'';
      $sQuery.= ', NOW(), '.$oDB->dbEscapeString($sEventDate).', '.$oLogin->getUserPk().') ';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      if(!$oDbResult)
        return array('error' => __LINE__.' - Sorry, could not save the activity.');

      $bRead = $oDbResult->readFirst();
      $nEventfk = (int)$oDbResult->getFieldValue('pk');

      //link the event to the uid/action/type/pk from the url
      $asLink = array();
      $asLink[] = ' ('.$oDB->dbEscapeString($nEventfk).', '.$oDB->dbEscapeString($sItemUid).', '.$oDB->dbEscapeString($sItemAction).', '.$oDB->dbEscapeString($sItemType).', '.$oDB->dbEscapeString($nItemPk).') ';

      if(!empty($asCoworkerInvolved))
      {
        //link to this event the connections the user has selected
        foreach($asCoworkerInvolved as $sLinkParam)
        {
          $asLinkData = explode('&', $sLinkParam);

          if(count($asLinkData) != 4)
            return array('error' => __LINE__.' - link parameters incorrect.');

            $asLink[] = ' ('.$oDB->dbEscapeString($nEventfk).', '.$oDB->dbEscapeString($asLinkData[0]).', '.$oDB->dbEscapeString($asLinkData[1])
                  .', '.$oDB->dbEscapeString($asLinkData[2]).', '.$oDB->dbEscapeString($asLinkData[3]).') ';
        }
      }

      $sQuery = 'INSERT INTO `event_link` (`eventfk`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`) VALUES '.implode(',', $asLink);
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      if(!$oDbResult)
        return array('error' => __LINE__.' - Sorry, could not save the activity.');

      if(!empty($nCalendarFk) && is_numeric($nCalendarFk))
      {
        if($this->coCalendar)
        {
          $sQuery= 'INSERT INTO `event_link` (`eventfk`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`, `cp_params`)';
          $sQuery.= ' VALUES ('.$oDB->dbEscapeString($nEventfk).', '.$oDB->dbEscapeString($this->coCalendar->getComponentUid()).',
                              '.$oDB->dbEscapeString(CONST_ACTION_VIEW).','.$oDB->dbEscapeString(CONST_ZCAL_EVENT).',
                              '.$oDB->dbEscapeString((int)$nCalendarFk).', '.$oDB->dbEscapeString($oLogin->getUserPk()).') ';
          $oDbResult = $oDB->ExecuteQuery($sQuery);
          if(!$oDbResult)
            return array('error' => __LINE__.' - Sorry, could not save the activity.');
        }
      }

      $sTitleEvent = '[new] '.$sTitle.' '.substr(strip_tags($sContent), 0, 100);
      $sTitleEvent = trim($sTitleEvent);

      $sUrl = $oPage->getUrl($sItemUid, $sItemAction, $sItemType, $nItemPk);
      $oLogin->getUserActivity($oLogin->getUserPk(), $this->csUid, $this->getAction(), CONST_EVENT_TYPE_EVENT, $nItemPk, $sTitleEvent, $sUrl);

      if($sItemType == 'ct')
      {
        $sQuery='SELECT followerfk FROM contact WHERE contactpk ='.$nItemPk.'';
        $oDbResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oDbResult->readFirst();

        $nNotifierfk = $oDbResult->getFieldValue('followerfk');
        $oLogin->getUserActivity($oLogin->getUserPk(),$this->_getUid(),$this->getAction(),CONST_EVENT_TYPE_EVENT,$nEventfk,$sTitleEvent,$sUrl,$nItemPk,$nNotifierfk);

        $sQuery = 'SELECT loginfk FROM account_manager WHERE contactfk ='.$nItemPk.'';
        $oDbResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oDbResult->readFirst();
        while($bRead)
        {
          $nNotifierfk = $oDbResult->getFieldValue('loginfk');
          $oLogin->getUserActivity($oLogin->getUserPk(),$this->_getUid(),$this->getAction(),CONST_EVENT_TYPE_EVENT,$nEventfk,$sTitleEvent,$sUrl,$nItemPk,$nNotifierfk);
          $bRead = $oDbResult->readNext();
        }
      }

      //Section to send notification about the activity
      if(!empty($sNotification))
      {
        $asRecipients = explode(',', $sNotification);

        foreach($asRecipients as $nLoginfk)
        {
          $asUserData = $oLogin->getUserDataByPk((int)$nLoginfk);
          $asCreator = $oLogin->getUserDataByPk($oLogin->getUserPk());
          $sMailContent = ' <strong> Hello '.$oLogin->getUserNameFromData($asUserData).',</strong> <br/><br/>';
          $sMailContent.= ' An activity has been shared with you on <strong>'.$oAddressBook->getItemName($sItemType,$nItemPk).'</strong> <br/> ';
          $sMailContent.=  $oAddressBook->getItemCardByPk($sItemType,$nItemPk);

           $sMailContent.= ' <strong> Activity Details </strong> <br/> <br/>';
           $sMailContent.= $oHTML->getBlocStart('',array('style'=>'border:1px solid #CECECE;margin:5px;padding:5px;'));
           $sMailContent.= ' Created by : '. $oLogin->getUserNameFromData($asCreator).'<br/> <br/>';
           $sMailContent.= ' Date :'.$sEventDate.'<br/> <br/>' ;
           $sMailContent.= ' <strong> Title </strong> :'.$sTitle .' <br/>' ;
           $sMailContent.= ' <strong> Description </strong> :'.$sContent.' <br/>' ;
           $sMailContent.= $oHTML->getBlocEnd();

          // Get the latest 3 events and remove the first because it is already displayed
          $asEvents = $this->getEventDetail('',$nItemPk,$sItemType,3);
          array_shift($asEvents);

          $sMailContent.= '<strong> Previous Activities </strong> <br/>';

          foreach($asEvents as $asEventDetail)
          {
            $sMailContent.= $oHTML->getBlocStart('',array('style'=>'border:1px solid #CECECE;padding:5px;margin-top:5px;'));
            $sMailContent.= ' Date :'.$asEventDetail['date_display'].'<br/> <br/>' ;
            $sMailContent.= ' <strong> Title </strong> :'.$asEventDetail['title'] .' <br/>' ;
            $sMailContent.= ' <strong> Description </strong> :'.$asEventDetail['content'].' <br/>' ;
            $sMailContent.= $oHTML->getBlocEnd();

          }
          $sMailContent.= ' Enjoy BCM <br/> <br/>';
          $sMailContent.= '<a href="'.$sUrl.'#'.$sItemType.'_tab_eventId'.'"> Click here to view all the activities</a>';

          $sMailContent.= $oHTML->getBlocEnd();

          $oMail-> sendRawEmail('info@bcm.com',$asUserData['email'],' BCM - Notifier: an activity has been shared with you.', $sMailContent);
        }
      }
    }
    else
    {
      $sQuery = 'UPDATE `event` SET `type` = '.$oDB->dbEscapeString($sType).', `title` = '.$oDB->dbEscapeString($sTitle).',';
      $sQuery.= '`content` = '.$oDB->dbEscapeString($sContent).', `date_display` = '.$oDB->dbEscapeString($sEventDate).', ';
      $sQuery.= '`date_update` = NOW(), `updated_by` = '.$oLogin->getUserPk().' WHERE eventpk = '.$pnPk;

      $nEventfk = $pnPk;
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      if($oDbResult)
      {
        $sTitleEvent = '[upd] '.$sTitle.' '.substr(strip_tags($sContent), 0, 100);
        $sTitleEvent = trim($sTitleEvent);

        $sUrl = $oPage->getUrl($sItemUid, $sItemAction, $sItemType, $pnPk);
        $oLogin->getUserActivity($oLogin->getUserPk(), $this->csUid, CONST_ACTION_SAVEEDIT, CONST_EVENT_TYPE_EVENT, $pnPk, $sTitleEvent, $sUrl);
      }

      //------------------------------------------
      //Delete existing links and add the potential new ones
      $asLink = array();
      $asLink[] = ' ('.$oDB->dbEscapeString($pnPk).', '.$oDB->dbEscapeString($sItemUid).', '.$oDB->dbEscapeString($sItemAction).', '.$oDB->dbEscapeString($sItemType).', '.$oDB->dbEscapeString($nItemPk).') ';

      if(!empty($asCoworkerInvolved))
      {
        //link to this event the connections the user has selected
        foreach($asCoworkerInvolved as $sLinkParam)
        {
          $asLinkData = explode('&', $sLinkParam);

          if(count($asLinkData) != 4)
            return array('error' => __LINE__.' - link parameters incorrect.');

            $asLink[] = ' ('.$oDB->dbEscapeString($pnPk).', '.$oDB->dbEscapeString($asLinkData[0]).', '.$oDB->dbEscapeString($asLinkData[1])
                  .', '.$oDB->dbEscapeString($asLinkData[2]).', '.$oDB->dbEscapeString($asLinkData[3]).') ';
        }
      }

      $sQuery = 'DELETE FROM event_link WHERE eventfk = '.$pnPk;
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      if(!$oDbResult)
        return array('error' => __LINE__.' - Sorry, could recreate links with connections and companies.');

      $sQuery = 'INSERT INTO `event_link` (`eventfk`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`) VALUES '.implode(',', $asLink);
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      if(!$oDbResult)
        return array('error' => __LINE__.' - Sorry, could not save the activity.');

    }

    if(!$oDbResult)
      return array('error' => __LINE__.' - Oops. couldn\'t save the activity.');

    if(!empty($sReminderDate))
    {
      $bSaved = $this->_saveReminder($nEventfk, $sReminderDate, $sReminderBefore, $nReminderUser, $sReminderMsg);
      if(!$bSaved)
        assert('false; // Adding event: reminder could not be saved. ');
    }

    if(empty($pnPk))
    {
      $sUrl = $oPage->getUrl($sItemUid, $sItemAction, $sItemType,$nItemPk,'',$sItemType.'_tab_eventId');
      return array('notice' => 'Activity saved successfully.', 'timedUrl' => $sUrl);
    }

    $sUrl = $oPage->getUrl($sItemUid, $sItemAction, $sItemType,$nItemPk,'',$sItemType.'_tab_eventId');
    return array('notice' => 'Activity updated successfully.', 'timedUrl' => $sUrl);
  }

  /**
   * Create a new event
   * @param string $psEventType
   * @param string $psTitle
   * @param string $psContent
   * @param string $psGuid
   * @param string $psType
   * @param string $psAction
   * @param integer $pnPk
   * @return integer eventpk
   */
  public function quickAddEvent($psEventType, $psTitle, $psContent, $psGuid, $psType = '', $psAction = '', $pnPk = 0)
  {
    if(!assert('!empty($psEventType) && !empty($psContent) && !empty($psGuid) && is_integer($pnPk)'))
      return 0;

    $asEvent = getEventTypeList(true);
    if(!in_array($psEventType, $asEvent))
    {
      assert('false; // Activity type does not exist');
      return 0;
    }

    $oLogin = CDependency::getComponentByName('login');
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'INSERT INTO `event` (`type`, `title`, `content`, `date_create`, `date_display`, `created_by`) ';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString($psEventType).', '.$oDB->dbEscapeString($psTitle).', '.$oDB->dbEscapeString($psContent).'';
    $sQuery.= ', NOW(), NOW(), '.$oLogin->getUserPk().') ';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    if(!$oDbResult)
      return 0;

    $nEventfk = $oDbResult->getFieldValue('pk');

    $sQuery= 'INSERT INTO `event_link` (`eventfk`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`)';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString($nEventfk).', '.$oDB->dbEscapeString($psGuid).', '.$oDB->dbEscapeString($psAction).',';
    $sQuery.= ''.$oDB->dbEscapeString($psType).', '.$oDB->dbEscapeString($pnPk).') ';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    if(!$bRead)
      return 0;

    return $nEventfk;
  }

  /**
   * Return an array with all the event datas matching the passed parameters
   * @param string $psUid
   * @param string $psType
   * @param string $psAction
   * @param integer $pnPk
   * @param string $psEventType
   * @return array
   */
  public function getEventInformation($psUid, $psAction, $psType, $psEventType = '')
  {
    if(!assert('!empty($psUid)'))
      return array();

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM `event_link` as el ';
    if(!empty($psEventType))
      $sQuery.= ' INNER JOIN event as ev ON (ev.eventpk = el.eventfk AND type = "'.$psEventType.'")';
    else
      $sQuery.= ' INNER JOIN event as ev ON (ev.eventpk = el.eventfk)';

    $sQuery.= ' WHERE cp_uid = "'.$psUid.'" AND cp_action = "'.$psAction.'" AND cp_type="'.$psType.'"';
    $sQuery.= ' GROUP BY cp_pk ORDER BY ev.date_display desc ';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    if(!$bRead)
      return array();

    $asResult = array();
    while($bRead)
    {
      $asResult[$oResult->getFieldValue('cp_pk', CONST_PHP_VARTYPE_INT)] = $oResult->getData();
      $bRead = $oResult->readNext();
    }

    return $asResult;
  }
  /**
   * Return the number of events matching the passed parameters
   * @param string $psUid
   * @param string $psType
   * @param string $psAction
   * @param integer $pnPk
   * @param string $psEventType
   * @return integer
   */
  public function getCountEventInformation($psUid, $psAction, $psType, $pnPk, $psEventType = '')
  {
    if(!assert('!empty($psUid) && is_integer($pnPk)'))
      return 0;

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT count(distinct ev.eventpk) as nCount FROM `event_link` as el ';

    if($psType==CONST_AB_TYPE_COMPANY)
    {
       if(!empty($psEventType))
        $sQuery.= ' INNER JOIN event as ev ON (eventpk = eventfk AND type = "'.$psEventType.'")';
       else
        $sQuery.= ' INNER JOIN event as ev ON (eventpk = eventfk)';
        $sQuery.= ' LEFT JOIN profil as prf on (prf.companyfk= '.$pnPk.' ) ';
        $sQuery.=  ' WHERE (el.cp_uid = "'.$psUid.'" AND el.cp_action = "'.$psAction.'" AND  el.cp_type = "'.$psType.'" AND el.cp_pk = '.$pnPk.')';
        $sQuery.=  ' OR ( el.cp_type = "ct" AND el.cp_pk = prf.contactfk) ';
    }
    else
    {
     if(!empty($psEventType))
      $sQuery.= ' INNER JOIN event as ev ON (eventpk = eventfk AND type = "'.$psEventType.'")';
     else
      $sQuery.= ' INNER JOIN event as ev ON (eventpk = eventfk)';
     $sQuery.= ' WHERE cp_uid = "'.$psUid.'" AND cp_action = "'.$psAction.'" AND cp_type="'.$psType.'" AND cp_pk = '.$pnPk;
    }

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    if(!$bRead)
      return 0;
    else
    return $oResult->getFieldValue('nCount', CONST_PHP_VARTYPE_INT);
  }

  /**
   * Get the detail of the event with matching parameters
   * @param string $psEventType
   * @param integer $pnItemPk
   * @param string $psType
   * @param integer $pnLimit
   * @return array of data
   */

  public function getEventDetail($psEventType='',$pnItemPk,$psType,$pnLimit=0)
  {
    if(!assert('!empty($pnItemPk) && is_integer($pnItemPk)&& is_integer($pnLimit)'))
      return 0;

    $oDB = CDependency::getComponentByName('database');

    if(!empty($psEventType))
    {
     if($psEventType == 'other')
      $sEvent = ' AND ev.type <> "email"';
     else
      $sEvent = ' AND ev.type = "'.$psEventType.'"';
     }
    else
      $sEvent = '';

    $sQuery = 'SELECT ev.*,evel.* FROM event AS ev,event_link AS evel WHERE evel.eventfk = ev.eventpk AND evel.cp_type="'.$psType.'" '.$sEvent.' and evel.cp_pk='.$pnItemPk.' order by ev.date_create desc';
    if($pnLimit==0)
      $sQuery.= ' limit 1';
    else
      $sQuery.= ' limit '.$pnLimit.'';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
      return array();
    $asEvents = array();
    while($bRead)
    {
      $asEvents[] = $oResult->getData();
      $bRead = $oResult->readNext();
    }
    return $asEvents;
  }

  /**
  * Get the latest activity related to the company
  * @param integer $pnItemPk
  * @return array of activity data
  */
  //public function getLatestConnectionEvent($pnItemPk)
  public function getEvents($psUid, $psAction, $psType = '', $pnPk = 0, $pasItem = array(), $pnLimit = 200)
  {

    $oDB = CDependency::getComponentByName('database');

    $sCondition = '(el.cp_uid = "'.$psUid.'" AND el.cp_action = "'.$psAction.'" ';

    if(empty($pasItem))
    {
      if(!empty($psType))
        $sCondition.= ' AND el.cp_type = "'.$psType.'" ';

      if(!empty($pnPk))
        $sCondition.= ' AND el.cp_pk = "'.$pnPk.'" ';
    }
    else
    {
      $asCondition = array();
      $sCondition.= ' AND ( ';
      foreach($pasItem as $asItem)
      {
        $asCondition[] = ' (el.cp_type = "'.$asItem['type'].'" AND el.cp_pk = "'.$asItem['pk'].'") ';
      }
      $sCondition.= implode(' OR ', $asCondition).' )';
    }

    $sCondition.= ') ';

    $sQuery = ' SELECT ev.*,el.* from event as ev ';
    $sQuery.= ' INNER JOIN event_link as el ON (el.eventfk = ev.eventpk AND '.$sCondition.') ';
    $sQuery.= ' ORDER BY ev.date_display desc LIMIT '.$pnLimit;
    //echo $sQuery;

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    $asEvent = array();

    while($bRead)
    {
      $asEvent[] = $oResult->getData();
      $bRead = $oResult->readNext();
    }

    return  $asEvent;
  }

  public function getEventDataByPk($pnItemPk)
  {
    if(!assert('!empty($pnItemPk) && is_integer($pnItemPk)'))
      return 0;

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * from event WHERE eventpk = '.$pnItemPk.'';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
      return array();
    else
      return  $oResult->getData();

  }

  public function setLanguage($psLanguage)
  {
    require_once('language/language.inc.php5');
    if(isset($gasLang[$psLanguage]))
      $this->casText = $gasLang[$psLanguage];
    else
      $this->casText = $gasLang[CONST_DEFAULT_LANGUAGE];
  }

  /**
   * Return an array containing the reminders data
   *
   * @param integer $pnEventPk
   * @param integer $pnReminderPk
   * @return array
   */
  public function getEventReminderByPk($pnEventPk = 0, $pnReminderPk = 0)
  {
    if(!assert('is_integer($pnEventPk) && is_integer($pnReminderPk)'))
      return array();

    if(empty($pnEventPk) && empty($pnReminderPk))
    {
      assert('false; // at least one pk has to be passed. ');
      return array();
    }

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM event_reminder WHERE ';

    if(!empty($pnEventPk))
      $sQuery.= 'eventfk = '.$pnEventPk;
    else
      $sQuery.= 'event_reminderpk = '.$pnReminderPk;

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    if(!$bRead)
      return array();

    //return an array of reminders
    $asReminder = array();
    while($bRead)
    {
      $asReminder[]= $oResult->getData();
      $bRead = $oResult->readNext();
    }

    return $asReminder;
  }


  private function _saveReminder($nEventfk, $sReminderDate, $sReminderBefore, $nReminderUser, $sReminderMsg = '')
  {
    if(!assert('is_integer($nEventfk) && is_integer($nReminderUser) && !empty($sReminderDate) && !empty($sReminderBefore)'))
      return false;

    $nTime = strtotime($sReminderDate);
    if($nTime === false)
    {
      assert('false; // date not valid');
      return false;
    }

    if($nTime < (time()+3600))
    {
      assert('false; // can\'t send a reminder in less than an hour ');
      return false;
    }

    if(!in_array($sReminderBefore, array('1h', '2h', 'halfday', 'fullday')))
    {
      assert('false; // delay before the reminder is sent doesn\'t exist.');
      return false;
    }

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'INSERT INTO event_reminder (eventfk, date_created, date_reminder, notify_delay, loginfk, message, sent) ';
    $sQuery.= ' VALUES ('.$oDB->dbEscapeString($nEventfk).', '.$oDB->dbEscapeString(date('Y-m-d H:i:s')).', ';
    $sQuery.= $oDB->dbEscapeString(date('Y-m-d H:i:s', $nTime)).',  '.$oDB->dbEscapeString($sReminderBefore).', ';
    $sQuery.= $oDB->dbEscapeString($nReminderUser).', '.$oDB->dbEscapeString($sReminderMsg).', 0 ) ';

    return (bool) $oDB->ExecuteQuery($sQuery);
  }


  /**
   * Send reminders to users
   *
   * @return boolean
   */
  private function _sendReminders()
  {
    $oDB = CDependency::getComponentByName('database');

    $sDate = date('Y-m-d H:i:s', strtotime('+2 days'));

    $sQuery = 'SELECT evr.*, ev.*, evl.*, IF(ct.contactpk IS NULL, cp.company_name, CONCAT(ct.firstname, " ", ct.lastname)) as item_label ';
    $sQuery.= ' FROM event_reminder evr  ';
    $sQuery.= ' INNER JOIN event as ev ON (ev.eventpk = evr.eventfk) ';
    $sQuery.= ' INNER JOIN event_link as evl ON (evl.eventfk = ev.eventpk) ';

    $sQuery.= ' LEFT JOIN contact as ct ON (evl.cp_type = "ct" AND evl.cp_pk = ct.contactpk) ';
    $sQuery.= ' LEFT JOIN company as cp ON (evl.cp_type = "cp" AND evl.cp_pk = cp.companypk) ';

    $sQuery.= ' WHERE sent = 0 AND date_reminder < "'.$sDate.'" ';
    $sQuery.= ' GROUP BY evr.eventfk ORDER BY date_reminder ';


    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    if(!$bRead)
      return true;

    $oMail = CDependency::getComponentByName('mail');
    $oLogin = CDependency::getComponentByName('login');
    $asUsers = $oLogin->getUserList();

    $asGroupedReminders = array();
    $nTime = (int)date('H');
    $nYear = (int)date('Y');
    $nMonth = (int)date('m');
    $nDay = (int)date('d');

    while($bRead)
    {
      $asReminder = $oResult->getData();

      switch($asReminder['notify_delay'])
      {
        case '1h': $sDateRef = date('Y-m-d H:i:s', strtotime('+ 1 hour 30 minutes')); break;
        case '2h': $sDateRef = date('Y-m-d H:i:s', strtotime('+ 2 hour 30 minutes')); break;

        case 'halfday':

          if($nTime > 12)
            $sDateRef = date('Y-m-d H:i:00', mktime(12, 0, 0, $nMonth, $nDay+1, $nYear));
          else
            $sDateRef = date('Y-m-d H:i:00', mktime(23, 59, 59, $nMonth, $nDay, $nYear));
          break;

        default:
           $sDateRef = date('Y-m-d H:i:00', mktime(23, 59, 59, $nMonth, $nDay+1, $nYear));
      }

      //echo $asReminder['notify_delay'].' ==> '.$asReminder['date_reminder'].' <= '.$sDateRef;

      if(isset($asUsers[$asReminder['loginfk']]) && !empty($asUsers[$asReminder['loginfk']]) && $asReminder['date_reminder'] <= $sDateRef)
      {
        $asReminder['email'] = $asUsers[$asReminder['loginfk']]['email'];
        if(!empty($asReminder['email']))
        {
          $asReminder['name'] = $oLogin->getUserNameFromData($asUsers[$asReminder['loginfk']], true);
          $asGroupedReminders[(int)$asReminder['loginfk']][] = $asReminder;
        }
      }


      $bRead = $oResult->readNext();
    }

    if(empty($asGroupedReminders))
      return true;

    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');
    $asTreatedReminder = array();

    foreach($asGroupedReminders as $nLoginfk => $asReminders)
    {
      $oMail->creatNewEmail();
      $oMail->setFrom('crm@bulbouscell.com', 'CRM notifyer');

      $asFirstReminder = current($asReminders);
      $oMail->addRecipient($asFirstReminder['email'], $asFirstReminder['name']);

      $sSubject = 'BCM reminder - '.count($asReminders).' event(s) approaching ';
      $sContent = '';

      foreach($asReminders as $asData)
      {
        $sEventUrl = $oPage->getUrl($asData['cp_uid'], $asData['cp_action'], $asData['cp_type'], $asData['cp_pk'], $asData['cp_params']);
        $asTreatedReminder[] = (int)$asData['event_reminderpk'];


        $sContent.= $oHTML->getBlocStart('', array('style' => 'border-bottom: 1px solid #666; margin: 5px 5px 10px 5px; padding: 5px 5px 15px 20px; font-family: arial; font-size: 12px;  '));
          $sContent.= $oHTML->getText('You\'ve requested a reminder:');
          $sContent.= $oHTML->getCarriageReturn(2);

          $sContent.= $oHTML->getBlocStart('', array('style' => 'border-left: 3px solid #aaa; padding: 3px 0 3px 5px; background-color: #ececec;'));

            $sContent.= $oHTML->getText('Reminder date: '.$asReminder['date_reminder']);
            $sContent.= $oHTML->getCarriageReturn();

            if(!empty($asReminder['message']))
              $sContent.= $oHTML->getText('Reminder message: <br /><br />'.$asReminder['message']);
            else
              $sContent.= $oHTML->getText('no message', array('style' => 'font-style: italic; color: #888;'));

            $sContent.= $oHTML->getCarriageReturn(2);

          $sContent.= $oHTML->getBlocEnd();

          $sContent.= $oHTML->getCarriageReturn();

          if($asData['cp_type'] == 'ct')
            $sContent.= $oHTML->getText('This reminder is related to the connection');
          else
            $sContent.= $oHTML->getText('This reminder is related to the company');

          $sContent.= $oHTML->getLink(' #'.$asData['cp_pk'].': '.$asData['item_label'], $sEventUrl);
          $sContent.= $oHTML->getCarriageReturn(2);

          $sContent.= $oHTML->getBlocStart('', array('style' => 'border-left: 3px solid #aaa; padding: 3px 0 3px 5px; background-color: #ececec;'));

            $sContent.= $oHTML->getText('Event date: '.$asData['date_display']);
            $sContent.= $oHTML->getCarriageReturn();
            $sContent.= $oHTML->getText('Event Type: '.ucfirst($asData['type']));
            $sContent.= $oHTML->getCarriageReturn();
            $sContent.= $oHTML->getText('Content: ');
            $sContent.= $oHTML->getCarriageReturn();
            $sContent.= $oHTML->getText($asData['content'], array('style' => 'font-style: italic;'));
            $sContent.= $oHTML->getCarriageReturn();

          $sContent.= $oHTML->getBlocEnd();

        $sContent.= $oHTML->getBlocEnd();

      }

      $oResult = $oMail->send($sSubject, $sContent, strip_tags($sContent));
      if($oResult)
      {
        echo '--> reminder email sent to '.$asFirstReminder['email'].' - '.$asFirstReminder['name'].' with '.count($asReminders).' reminders <br />';
      }
    }


    if(!empty($asTreatedReminder))
    {
      $sQuery = 'UPDATE event_reminder SET sent = 1 WHERE event_reminderpk IN ('.implode(',', $asTreatedReminder).')';
      $oResult = $oDB->ExecuteQuery($sQuery);
    }

    return true;
  }


  private function _getReminderDelete($pnReminderPk)
  {
    if(!assert('is_integer($pnReminderPk) && !empty($pnReminderPk)'))
      return array();

    $sHtmlElementId = getValue('html_uid');
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'DELETE FROM event_reminder WHERE event_reminderpk = '.$pnReminderPk;

    if(!$oDB->ExecuteQuery($sQuery))
      return array('error' => 'Sorry, we could not delete the reminder.');

    return array('data' => 'ok', 'action' => '$("#'.$sHtmlElementId.' a").remove(); $("#'.$sHtmlElementId.'").attr(\'style\', \'text-decoration: line-through;\'); ');
  }
}
