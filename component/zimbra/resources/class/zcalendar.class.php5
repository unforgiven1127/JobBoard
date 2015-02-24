<?php

require_once('component/zimbra/zimbra.class.ex.php5');

class CZCalendar extends CZimbraEx
{
  private $cnDefaultUserCal = 10;
  private $cnSharedCalGlobal = 260;
  private $cnSharedCalSales = 263;
  private $casColor = array(3 => "#FFBF40", 29 => '#FFBA73', 28 => '#FFE373', 20 => '#BF7830', 2 => '#FFD073', 26 => '#FFD840', 12 => '#FF8100', 4 => '#466FD5', 5=> '#6C8AD5', 31 => '#216278', 22 => '#FFA860');


  ////////////////////////////// Functions related to display //////////////////////////////////////////


 /**
  * _getCheckStatus()
  * Check the free busy status of the attendees and organizer on the appointment date
  * @return type
  */
 protected function _getCheckStatus()
 {
   $oHTML = CDependency::getComponentByName('display');
   $oLogin = CDependency::getComponentByName('login');
   $sUser = getValue('user');
   $sDate = getValue('date');
   $sAction = '';

   if(empty($sDate) || $sDate =='undefined')
     return array('action'=>'alert("Please, select the date first");');
   else
   {
     $nDate = strtotime($sDate);
     $nDay = strtotime(date('Y-m-d', $nDate));

     $sYMD = date('Y-m-d', $nDate);

     $sStartTime = ($nDay +(8 * 3600)) * 1000;
     $sEndTime = ($nDay + (20*3600)) * 1000;
   }

   $asOrganizer = $oLogin->getUserDataByPk($oLogin->getUserPk());
   $asOrgData = array('starttime'=> ($sStartTime), 'endtime' => ($sEndTime), 'name' => $asOrganizer['id']);


   $sHTML = $oHTML->getBlocStart('status',array('class'=>'status'));

   //----------------------------------------
   //left Section
   $sHTML.= $oHTML->getBlocStart('',array('class'=>'left'));

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'zTime'));
    $sHTML.= $oHTML->getSpace();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'organizer'));
    $sHTML.= $oHTML->getText('me ('.$asOrganizer['id'].')');
    $sHTML.= $oHTML->getBlocEnd();


    $asUsers = $oLogin->getUserList(explode(',', $sUser), true);
    foreach($asUsers as $sKey => $asData)
    {
      $sHTML.= $oHTML->getBlocStart('', array('class'=>'organizer'));
      $sHTML.= $oHTML->getText($asData['firstname'].' '.$asData['lastname']);
      $sHTML.= $oHTML->getBlocEnd();
    }

    $sHTML.= $oHTML->getBlocEnd();

    //----------------------------------------
    //Right Section
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'right'));

    //Display header line with hours
    $sFirstClass = ' timedataStart ';
    for($nHour=8; $nHour <= 20; $nHour++)
    {
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'timedata timeheader'.$sFirstClass));
      $sHTML.= $oHTML->getText($nHour);
      $sHTML.= $oHTML->getBlocEnd();
      $sFirstClass = '';
    }

    //Display user availability: check the user live calendar
    $asOrgRecords = $this->getFreeBusyStatus($asOrgData);
    //dump($asOrgRecords);
    if(isset($asOrgRecords['B']) || isset($asOrgRecords['T']))
    {
      foreach($asOrgRecords as $sType => $asData)
      {
        if($sType == 'B' || $sType == 'T')
        {
          //only 1 event
          if(isset($asData['S']))
          {
            //echo 'an event for : '.$asOrganizer['id'].' //'.$asData["S"].'|'.$asData["E"].'<br />';
            $sAction.= ' jQuery(".'.$asOrganizer['id'].'").each(function(){ nTime = $(this).attr("time");
            if(nTime >= '.($asData["S"]/1000).' && nTime <= '.($asData["E"]/1000).')
            $(this).addClass("busy");  }); ';
          }
          else
          {
            //multiple events durring the day
            foreach($asData as $sKey => $asValue)
            {
              //echo 'an event for : '.$asOrganizer['id'].' //'.$asValue["S"].'|'.$asValue["E"].'<br />';
              $sAction.= 'console.log("update '.$asOrganizer['id'].'['.date('Y-m-d H:i:s', ($asValue["S"]/1000)).'/'.date('Y-m-d H:i:s', ($asValue["E"]/1000)).']");  jQuery(".'.$asOrganizer['id'].'").each(function(){ nTime = $(this).attr("time");
              if(nTime >= '.($asValue["S"]/1000).' && nTime <= '.($asValue["E"]/1000).')
              $(this).addClass("busy");   }); ';
            }
          }
        }
      }
    }

    for($nHour = 8; $nHour <= 20; $nHour++)
    {
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'timedata'));

      for($nQuarter=0; $nQuarter < 4; $nQuarter++)
      {
        $nTimeStamp = strtotime(date($sYMD.' '.$nHour.':'.($nQuarter*15).':00'));
        $sHTML.= $oHTML->getBlocStart('',array('time'=>''.$nTimeStamp.'','class'=>''.$asOrganizer['id'].' leftContainer'));
        $sHTML.= $oHTML->getText('&nbsp;');
        $sHTML.= $oHTML->getBlocEnd();
      }

      $sHTML.= $oHTML->getFloathack();
      $sHTML.= $oHTML->getBlocEnd();
    }

    //Display the availability for all the attendees
    //get all appointments all attendees
    $asBusySchedules = $this->getAttendeeStatus(array_keys($asUsers), $nDate);

    foreach($asUsers as $sKey => $asUserData)
    {
      if($asOrganizer['loginpk'] != $asUserData['loginpk'])
      {
        if(!empty($asBusySchedules[$asUserData['loginpk']]) && is_array($asBusySchedules[$asUserData['loginpk']]))
        {
          foreach($asBusySchedules[$asUserData['loginpk']] as $sKey => $asValue)
          {
            //echo 'an event for : '.$asUserData['id'].' //'.$asValue["starttime"].'|'.$asValue["endtime"].'<br />';
            $sAction.= 'console.log("update '.$asUserData['id'].'['.$asValue["starttime"].'/'.$asValue["endtime"].']"); jQuery(".'.$asUserData['id'].'").each(function()
                      {
                        nTime = $(this).attr("time");
                        if(nTime >= '.(strtotime($asValue["starttime"])).' && nTime <= '.(strtotime($asValue["endtime"])).')
                          $(this).addClass("busy");
                      }); ';
          }
        }

        for($nHour=8; $nHour <= 20; $nHour++)
        {
          $sHTML.= $oHTML->getBlocStart('',array('class'=>'timedata'));

          for($nQuarter=0; $nQuarter <4; $nQuarter++)
          {
            $nTimeStamp = strtotime(date($sYMD.' '.$nHour.':'.($nQuarter*15).':00'));
            $sHTML.= $oHTML->getBlocStart('',array('time'=>''.$nTimeStamp.'','class'=>''.$asUserData['id'].' leftContainer'));
            $sHTML.= $oHTML->getText('&nbsp;');
            $sHTML.= $oHTML->getBlocEnd();
          }
          $sHTML.= $oHTML->getBlocEnd();
        }
      }
    }

    $sHTML.= $oHTML->getFloathack();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getFloathack();
    $sHTML.= $oHTML->getBlocEnd();

    return array('action'=> $sAction.' $("input[type=submit]").removeClass("hidden"); $(".attendeeAvailability:not(:visible)").fadeIn(); ', 'data' => $sHTML);
  }

 /**
  * _getSaveAppointment()
  * Save the appointment on the calendar
  * @return array with success or error
  */

 protected function _getSaveAppointment()
 {
   $oLogin = CDependency::getComponentByName('login');

   $nStartTimeStamp = strtotime(getValue('date_start'));
   $nEndTimeStamp = strtotime(getValue('date_end'));
   $nStartTime =  date('Ymd\THis', $nStartTimeStamp);
   $nEndTime =  date('Ymd\THis', $nEndTimeStamp);

   $sLocation = getValue('calLocation');
   $sTitle = getValue('calTitle');
   $sContent = getValue('calContent');
   $asAttendees = getValue('attendees');
   $sAcceptance = getValue('accept');
   $bInAjax = (bool)getValue('inAjax', 0);

   $nOrganizerPk = $oLogin->getUserPk();
   $asOrganizerData = $oLogin->getUserDataByPk((int)$nOrganizerPk);

   $asOrganizer = array('email' => $asOrganizerData['email'],'name' => $asOrganizerData['firstname'].''.$asOrganizerData['lastname']);
   $asBCMOrganizer = array('email' => 'bcm@bulbouscell.com','name' => 'BC Master');

   $asAttendeesData = array();
   $sAttendeeName = '';
   if(!empty($asAttendees))
   {
     foreach($asAttendees as $value)
     {
       $asAttendeeData = $oLogin->getUserDataByPk((int)$value);
       $asAttendeesData[] = array('email' => $asAttendeeData['email'],'name' => $asAttendeeData['firstname'].''.$asAttendeeData['lastname'],'role' => 'REQ','ptst'=>$sAcceptance,'loginpk'=>$asAttendeeData['loginpk']);
       $sAttendeeName.= $asAttendeeData['firstname'].' ';
     }
   }

   //-----------------------------
   //TODO: properly check the form




   //Initialize mail server connection
   $sTimeZone = 'Asia/Tokyo';
   $asTemp = explode('@', $asOrganizerData['email']);
   if(count($asTemp) != 2 || empty($asTemp[0]))
     return array('error' => __LINE__.' - No mail server account found.');

   $bLogged = $this->initialize($asTemp[0]);
   if(!$bLogged)
     return array('error' => __LINE__.' - Error can\'t connect to the mail server. ['.$asTemp[0].']:'.$this->getErrors(true));


   $sContent = strip_tags(str_ireplace(array('</p>', '<br/>', '<br />'), "\n", $sContent));
   $sContent = str_ireplace('&nbsp;', ' ', $sContent);

   $asCreate = array( 'calendar'  =>  '10',
                      'title'     =>  $sTitle,
                      'eventname' =>  $sTitle,
                      'timezeone' =>  $sTimeZone,
                      'location'  =>  $sLocation,
                      'starttime' =>  $nStartTime,
                      'endtime'   =>  $nEndTime,
                      'notes'     =>  $sContent,
                      'organizer' =>  $asOrganizer,
                      'attendees' =>  $asAttendeesData);


   //create the appointement in the user calendar and in the local database
    $nEvemntPk = $this->getCreateAppointment($asCreate);
    if(!$nEvemntPk)
    {
      dump($asCreate);
      return array('error' => __LINE__.' - can\'t create the event in your calendar.');
    }

    //Connect now as BCM user to create shared calendars
    $bLogged = $this->initialize('bcm');
    if(!$bLogged)
      return array('error' => __LINE__.' - Error can\'t connect to the mail server. [bcm]:'.$this->getErrors(true));


    //testing purpose, me: sboudoux = 5
    if(in_array($asOrganizerData['teamfk'], array(1,3)))
      $bSalesTeam = true;
    else
      $bSalesTeam = false;


    $asGlobalCreate = array('calendar' => $this->cnSharedCalGlobal, 'title' => $sTitle, 'eventname' => $sTitle,
                          'timezeone' => $sTimeZone, 'location' => $sLocation, 'starttime' => $nStartTime,
                          'endtime'=> $nEndTime,'notes' => $sAttendeeName.$sContent, 'organizer' => $asBCMOrganizer);
    if($bSalesTeam)
    {
      $asSalesCreate = array('calendar' => $this->cnSharedCalSales, 'title' => $sTitle, 'eventname' => $sTitle, 'timezeone' => $sTimeZone,
                          'location' => $sLocation, 'starttime' =>  $nStartTime, 'endtime' => $nEndTime, 'notes' => $sAttendeeName.$sContent,
                          'organizer' => $asBCMOrganizer );

      $bSales = $this->getsubCreateAppointment($asSalesCreate);
      $bGlobal = $this->getsubCreateAppointment($asGlobalCreate);
    }
    else
    {
      $bSales = true;
      $bGlobal = $this->getsubCreateAppointment($asGlobalCreate);
    }

    if(!$bSales || !$bGlobal)
      return array('error' => __LINE__.' - Error Ocurred while creating event in shared calendar. ['.(int)$bSales.'/'.(int)$bGlobal.']'."<br />".$this->getErrors(true));

    if($bInAjax)
    {
      $sJavascript = '$(\'.addCalendarBox input\').val('.$nEvemntPk.').attr(\'readonly\',\'readonly\').attr(\'onchange\', \'this.checked = true;\'); ';
      $sJavascript.= ' setTimeout("removePopup();", 2000); ';

      return array('notice' =>'Event Created successfully', 'action' => $sJavascript);
    }

    $oPage = CDependency::getComponentByName('page');
    $sUrl = $oPage->getUrl('zimbra', CONST_ACTION_VIEW, CONST_ZCAL_EVENT, $oLogin->getUserPk());
    return array('notice' =>'Event Created successfully', 'url' => $sUrl);
  }

    /**
    * getCalendarForm()
    * Display the calendar form for inputting the information
    * return string
    */
    public function getCalendarForm($pbInAjax = false)
    {
      $oHTML = CDependency::getComponentByName('display');
      $oPage = CDependency::getComponentByName('page');
      $oLogin = CDependency::getComponentByName('login');


      $oPage->addCssFile(array($this->getResourcePath().'/css/zimbra.css'));

      $nMin = date('i', strtotime('+1 hour'));
      $nMin = $nMin - ($nMin % 5);
      if($nMin < 10)
        $nMin = '0'.$nMin.':00';
      else
        $nMin.= ':00';

      $sHTML = $oHTML->getBlocStart('calendarFormId', array('style' => 'margin: 0 auto; max-width: 1050px; min-width: 900px; width: 75%;'));
      $sHTML.= $oHTML->getTitleLine('Create a calendar event');

      $oForm = $oHTML->initForm('calendarFormData');
      $sURL = $oPage->getAjaxUrl('400-650',CONST_ACTION_SAVEADD,'calendar',0);
      $oForm->setFormParams('', true, array('submitLabel' => 'Save', 'action' => $sURL, 'inajax' => $pbInAjax));


      $oForm->setFormDisplayParams(array('noCancelButton' => 1, 'columns' => 2, 'submitButtonHidden' => 1));

      $oForm->addField('select', 'accept', array('label' => 'Participation', 'value' => ''));
      $oForm->setFieldControl('accept', array('jsFieldNotEmpty' => ''));
      $oForm->addOption('accept', array('label' => 'Accept', 'value' =>'AC'));
      $oForm->addOption('accept', array('label' => 'Need Action', 'value' =>'NE'));

      $oForm->addField('misc', '', array('type' => 'text'));

      $oForm->addField('input', 'date_start', array('type' => 'datetime', 'label'=>'Start Time', 'value' => date('Y-m-d H:', strtotime('+1 hour')).$nMin, 'monthNum' => 2));
      $oForm->setFieldControl('date_start', array('jsFieldNotEmpty' => ''));

      $oForm->addField('input', 'date_end', array('type' => 'datetime', 'label'=>'End Time', 'value' => date('Y-m-d H:'.$nMin, strtotime('+2 hour')),'monthNum' => 2));
      $oForm->setFieldControl('date_end', array('jsFieldNotEmpty' => ''));

      $oForm->addField('misc', '', array('type' => 'br'));

      $oForm->addField('input', 'calTitle', array('label' => ' Title ', 'value' => ''));
      $oForm->setFieldControl('calTitle', array('jsFieldMinSize' => '2','jsFieldMaxSize' => 255, 'jsFieldNotEmpty' => ''));
      $oForm->setFieldDisplayParams('calTitle', array('class' => 'calendarFormTitle'));

      $oForm->addField('input', 'calLocation', array('label' => 'Location', 'value' => '', 'keepNextInline' => '1'));
      $oForm->setFieldControl('calLocation', array('jsFieldMinSize' => '4','jsFieldMaxSize' => 255));
      $oForm->setFieldDisplayParams('calLocation', array('class' => 'calendarFormLocation'));

      $oForm->addField('misc', '', array('type' => 'br'));

      $oForm->addField('textarea', 'calContent', array('label' => 'Description', 'value' => '', 'isTinymce' => 1));
      $oForm->setFieldControl('calContent', array('jsFieldNotEmpty' => '', 'jsFieldMinSize' => '2','jsFieldMaxSize' => 4096));
      $oForm->setFieldDisplayParams('calContent', array('class' => 'calendarFormDescription'));

      $oForm->addField('misc', '', array('type' => 'br'));

      $oForm->addField('select', 'attendees[]', array('label' => 'Attendees', 'multiple' => 'multiple', 'allNoneLink' => 1));
      $oForm->setFieldDisplayParams('attendees[]', array('id' => 'attendeeListContainerId', 'style' => 'width: 100%;'));

      $asUsers = $oLogin->getUserList(0, true);
      $nCurrentUserPk = $oLogin->getUserPk();
      foreach($asUsers as $nUserPk => $asUserData)
      {
        if($nUserPk != $nCurrentUserPk)
          $oForm->addOption('attendees[]', array('value'=> $asUserData['loginpk'],'label' => $asUserData['firstname'].' '.$asUserData['lastname']));
      }

      $sURL = $oPage->getAjaxUrl('400-650','check','calendar');

      $sJavascript = '';

      if(!$pbInAjax)
        $sJavascript = '$(document).ready(function() { ';

      $sJavascript.= ' $("#btnCheckId").click(function() { ';
      $sJavascript.= ' var sUser = $("#attendeesId").val(); ';
      $sJavascript.= ' var sDate = $("#date_startId").val(); ';
      $sJavascript.= ' AjaxRequest("'.$sURL.'&user="+sUser+"&date="+sDate,"#body","","freeBusy"); ';
      $sJavascript.= ' }); ';

      if(!$pbInAjax)
      {
        $sJavascript.= '}); ';
        $oPage->addCustomJs($sJavascript);
      }
      else
        $oForm->addField('hidden', 'inAjax', array('label' => '', 'value' => '1'));


      $oForm->addField('misc', 'attendeeAvailability', array('type'=>'text','text'=>'<div id="freeBusy" style="min-width:640px; margin-left: 140px; height:auto;">&nbsp;</div>','keepNextInline' => '1'));
      $oForm->setFieldDisplayParams('attendeeAvailability', array('class' => 'attendeeAvailability'));

      $oForm->addField('misc', 'freebusyBlock', array('type' => 'text', 'text'=> '<div class ="checkButton"> <input type="button" name="btnCheck" id="btnCheckId" Value="Check availability"/> </div>'));
      $oForm->setFieldDisplayParams('freebusyBlock', array('class' => 'freebusyBtnContainer'));

      $sHTML.= $oForm->getDisplay();

      if($pbInAjax)
      {
        $sHTML.= '<script class="scriptToRun">'.$sJavascript.' ';
        $sHTML.= 'if($(\'#event_typeId\').val())';
        $sHTML.= '{ $(\'#calTitleId\').val($(\'#event_typeId\').val() +\': \'+ $(\'#titleId\').val()); }';
        $sHTML.= 'else { $(\'#calTitleId\').val($(\'#titleId\').val()); }';

        //$sHTML.= ' $(\'#calContentId\').val( $(\'#contentId\').val() ); ';
        $sHTML.= ' if(tinyMCE && tinyMCE.get(\'calContentId\')) ';
        $sHTML.= ' tinyMCE.get(\'calContentId\').setContent( tinyMCE.get(\'contentId\').getContent() ); ';

        $sHTML.= ' var asDate = $(\'#date_eventId\').val().split(\' \'); ';
        $sHTML.= ' var asTime = asDate[1].split(\':\'); ';
        $sHTML.= ' var nTime = parseInt(asTime[0]) + 1; ';
        $sHTML.= ' if(nTime < 10) nTime = \'0\'+nTime; ';
        $sHTML.= ' $(\'#date_startId\').val( asDate.join(\' \')); ';
        $sHTML.= ' $(\'#date_endId\').val( asDate[0]+\' \'+nTime+\':00:00\' ); ';
        $sHTML.= '</script>';
      }

      $sHTML.= $oHTML->getBlocEnd();
      return $sHTML;
    }

 //////////////////////////////// Functions related to XML generation and manipulation //////////////////////////////////

 /**
  * getCreateAppointment()
  * Create appointment and save to the database with success or failure
  * @param array $pasCreate
  * @return integer the eventpk
  */

  public function getCreateAppointment($pasCreate)
  {
   if(!assert('is_array($pasCreate) && !empty($pasCreate)'))
      return 0;

    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');
    $oXml = XmlSingleton::getXmlInstance();

    $nLoginPk = $oLogin->getUserPk();

    $sId = $oXml->addNode(array('name'=>'CreateAppointmentRequest','@attributes' =>array('comp'=>'"0"','xmlns'=>'"urn:zimbraMail"')));
    $sId1 = $oXml->addChildNode($sId, array('name'=>'m','@attributes' => array('l'=>'"'.$pasCreate['calendar'].'"')));
    $oXml->addChildNode($sId1, array('name'=>'su','@value'=>''.$pasCreate['title'].''));
    $oXml->addChildNode($sId1, array('name'=>'e','@attributes' => array('a'=>'"'.$pasCreate['organizer']['email'].'"','d'=>'"'.$pasCreate['organizer']['name'].'"','p'=>'"'.$pasCreate['organizer']['name'].'"','t'=>'"f"')));

    foreach($pasCreate['attendees'] as $asAttendee)
    {
      $oXml->addChildNode($sId1, array('name'=>'e','@attributes' => array('a'=>'"'.$asAttendee['email'].'"','p'=>'"'.$asAttendee['name'].'"','t'=>'"t"')));
    }

    $sId2 = $oXml->addChildNode($sId1, array('name'=>'inv'));
    $sId3 = $oXml->addChildNode($sId2, array('name'=>'comp','@attributes' => array('status'=>'"CONF"','fb'=>'"B"','fba'=> '"B"','class'=>'"PUB"','allDay'=>'"0"','name'=>'"'.$pasCreate['eventname'].'"','loc'=>'"'.$pasCreate['location'].'"','isOrg'=>'"1"')));
    $oXml->addChildNode($sId3, array('name'=>'s','@attributes' => array('d'=>'"'.$pasCreate['starttime'].'"','tz'=>'"'.$pasCreate['timezeone'].'"')));
    $oXml->addChildNode($sId3, array('name'=>'e','@attributes' => array('d'=>'"'.$pasCreate['endtime'].'"','tz'=>'"'.$pasCreate['timezeone'].'"')));
    $oXml->addChildNode($sId3, array('name'=>'descHtml','@value'=>''.$pasCreate['notes'].''));

    $oXml->addChildNode($sId3, array('name'=>'or','@attributes' => array('a'=>'"'.$pasCreate['organizer']['email'].'"','d'=>'"'.$pasCreate['organizer']['name'].'"','sentBy'=>'"'.$pasCreate['organizer']['email'].'"','dir'=>'"in:inbox"','lang'=>'"en-US"')));

    foreach($pasCreate['attendees'] as $asAttendee)
    {
      $oXml->addChildNode($sId3, array('name'=>'at','@attributes' => array('a'=>'"'.$asAttendee['email'].'"','d'=>'"'.$asAttendee['name'].'"','sentBy'=>'"'.$pasCreate['organizer']['email'].'"','dir'=>'"in:inbox"','lang'=>'"en-US"', 'role' => '"'.$asAttendee['role'].'"','ptst' => '"'.$asAttendee['ptst'].'"','rsvp'=> '"0"','cutype'=> '"IND"')));
    }

    if(!empty($pasModify['recurrent']))
    {
      $sId4 = $oXml->addChildNode($sId3, array('name'=>'recur'));
      $sId5 = $oXml->addChildNode($sId4, array('name'=>'add'));
      $sId6 = $oXml->addChildNode($sId5, array('name'=>'rule','@attributes' => array('freq'=>'"'.$pasCreate['recurrent']['frequency'].'"')));
      $oXml->addChildNode($sId6, array('name'=>'count','@attributes' => array('num'=>'"'.$pasCreate['recurrent']['count'].'"')));
      $oXml->addChildNode($sId6, array('name'=>'interval','@attributes' => array('ival'=>'"1"')));
      $oXml->addChildNode($sId6, array('name'=>'until','@attributes' => array('d' =>'"'.date('Y-m-d\THis\Z',strtotime('+'.$pasCreate['recurrent']['till'].'')).'"')));
    }

    // Get the response from the xml component
    $sResponse = $oXml->makeSoapRequest($this->coCurl, $this->cbConnected, true, $this->csAuthtoken, $this->csSessionID);

    //Parse the response
    $asRecords = $oXml->parse($sResponse);
    $sData = serialize($pasCreate);

    if(empty($asRecords) || !is_array($asRecords))
    {
      $this->casError[] = __LINE__.' - no data received from zimbra ';

      /*//save the event with a status to 0 to indicate there was a problem
      $sQuery = 'INSERT INTO `zimbra_cal`(`data`, `status`) VALUES ('.$oDB->dbEscapeString($sData).', 0)';
      $oDbResult = $oDB->ExecuteQuery($sQuery);*/
      return 0;
    }

    //if(!isset($asRecords['SOAP:ENVELOPE'][1]['SOAP:BODY']['CREATEAPPOINTMENTRESPONSE']))
    if(!isset($asRecords['SOAP:ENVELOPE']['SOAP:BODY']['CREATEAPPOINTMENTRESPONSE']))
    {
      $this->casError[] = __LINE__.' - asRecords [SOAP:ENVELOPE][1][SOAP:BODY][CREATEAPPOINTMENTRESPONSE] not define';
      dump($asRecords);
      return 0;
    }

    $asData = $asRecords['SOAP:ENVELOPE']['SOAP:BODY']['CREATEAPPOINTMENTRESPONSE'];

    $sQuery = 'INSERT INTO `zimbra_cal`( `creatorfk`, `invId`, `apptId`, `msId`, `data`,`starttime`,`endtime`, `status`) VALUES ';
    $sQuery.= '('.$oDB->dbEscapeString($nLoginPk).','.$oDB->dbEscapeString($asData['INVID']).','.$oDB->dbEscapeString($asData['APPTID']).',';
    $sQuery.= ' '.$oDB->dbEscapeString($asData['MS']).','.$oDB->dbEscapeString($sData).',';
    $sQuery.= ' '.$oDB->dbEscapeString(date("Y-m-d H:i:s",strtotime($pasCreate['starttime']))).','.$oDB->dbEscapeString(date("Y-m-d H:i:s",strtotime($pasCreate['endtime']))).', 1)';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $nZcalfk = $oDbResult->getFieldValue('pk',CONST_PHP_VARTYPE_INT);

    if(!empty($pasCreate['attendees']))
    {
      $asQuery = array();
      foreach($pasCreate['attendees'] as $asAttendee)
        $asQuery[] = '("'.$nZcalfk.'","'.$asAttendee['loginpk'].'","'.$asAttendee['email'].'")';

      $sQuery = 'INSERT INTO `zimbra_attendees`(`zcalfk`,`loginfk`,`email`) VALUES '.implode(',', $asQuery);
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      if(!$oDbResult)
      {
        $this->casError[] = __LINE__.' - couldn\'t save attendees';
        return 0;
      }
    }

    return $nZcalfk;
  }

    /**
     * Create the appointment
     * @param array $pasCreate
     * @return boolean
     */

  public function getsubCreateAppointment($pasCreate)
  {
    if(!assert('is_array($pasCreate) && !empty($pasCreate)'))
      return false;

    $oXml = XmlSingleton::getXmlInstance();
    $sId = $oXml->addNode(array('name'=>'CreateAppointmentRequest','@attributes' =>array('comp'=>'"0"','xmlns'=>'"urn:zimbraMail"')));
    $sId1 = $oXml->addChildNode($sId, array('name'=>'m','@attributes' => array('l'=>'"'.$pasCreate['calendar'].'"')));
    $oXml->addChildNode($sId1, array('name'=>'su','@value'=>''.$pasCreate['title'].''));
    $oXml->addChildNode($sId1, array('name'=>'e','@attributes' => array('a'=>'"'.$pasCreate['organizer']['email'].'"','d'=>'"'.$pasCreate['organizer']['name'].'"','p'=>'"'.$pasCreate['organizer']['name'].'"','t'=>'"f"')));

    $sId2 = $oXml->addChildNode($sId1, array('name' => 'inv'));
    $sId3 = $oXml->addChildNode($sId2, array('name' => 'comp','@attributes' => array('status'=>'"CONF"','fb'=>'"B"','fba'=> '"B"','class'=>'"PUB"','allDay'=>'"0"','name'=>'"'.$pasCreate['eventname'].'"','loc'=>'"'.$pasCreate['location'].'"','isOrg'=>'"1"')));
    $oXml->addChildNode($sId3, array('name' => 's','@attributes' => array('d'=>'"'.$pasCreate['starttime'].'"','tz'=>'"'.$pasCreate['timezeone'].'"')));
    $oXml->addChildNode($sId3, array('name' => 'e','@attributes' => array('d'=>'"'.$pasCreate['endtime'].'"','tz'=>'"'.$pasCreate['timezeone'].'"')));
    $oXml->addChildNode($sId3, array('name' => 'descHtml','@value'=>''.$pasCreate['notes'].''));
    $oXml->addChildNode($sId3, array('name' => 'or','@attributes' => array('a'=>'"'.$pasCreate['organizer']['email'].'"','d'=>'"'.$pasCreate['organizer']['name'].'"','sentBy'=>'"'.$pasCreate['organizer']['email'].'"','dir'=>'"in:inbox"','lang'=>'"en-US"')));

    // Get the response from the xml component
    $sResponse = $oXml->makeSoapRequest($this->coCurl, $this->cbConnected, true, $this->csAuthtoken, $this->csSessionID);
    if(empty($sResponse))
    {
      $this->casError[] = 'No soap response. ['.$sResponse.'] '.$oXml->getErrors(true);
      return false;
    }

    $asRecords = $oXml->parse($sResponse);
    if(empty($asRecords))
    {
      $this->casError[] = 'Couldn\'t parse xml while creating appointment in sub calendar.['.$sResponse.'] '.$oXml->getErrors(true);
      return false;
    }

    return true;
  }

    /**
     * getModifyAppointment()
     * Modify appointment and update the database
     * @param array $pasModify
     * @return boolean
     */

    public function getModifyAppointment($pasModify)
    {
      if(!assert('is_array($pasModify) && !empty($pasModify)'))
        return false;

      $oDB = CDependency::getComponentByName('database');
      $oXml = XmlSingleton::getXmlInstance();

      $sId = $oXml->addNode(array('name'=>'ModifyAppointmentRequest','@attributes' =>array('apptId'=>'"'.$pasModify['apptId'].'"','calItemId'=>'"'.$pasModify['calItemId'].'"','id'=>'"'.$pasModify['invId'].'"','invId'=>'"'.$pasModify['newinvId'].'"','xmlns'=>'"urn:zimbraMail"')));
      $sId1 =  $oXml->addChildNode($sId, array('name'=>'m','@attributes' => array('l'=>'"'.$pasModify['calendar'].'"')));
                $oXml->addChildNode($sId1, array('name'=>'su','@value'=>''.$pasModify['title'].''));

                $oXml->addChildNode($sId1, array('name'=>'e','@attributes' => array('a'=>'"'.$pasModify['organizer']['email'].'"','d'=>'"'.$pasModify['organizer']['name'].'"','p'=>'"'.$pasModify['organizer']['name'].'"','t'=>'"f"')));

                foreach($pasModify['attendees'] as $asAttendee)
                {
                   $oXml->addChildNode($sId1, array('name'=>'e','@attributes' => array('a'=>'"'.$asAttendee['email'].'"','p'=>'"'.$asAttendee['name'].'"','t'=>'"t"')));
                 }

                $sId2 = $oXml->addChildNode($sId1, array('name'=>'inv'));
                  $sId3 = $oXml->addChildNode($sId2, array('name'=>'comp','@attributes' => array('status'=>'"CONF"','fb'=>'"B"','fba'=> '"B"','class'=>'"PUB"','allDay'=>'"0"','name'=>'"'.$pasModify['eventname'].'"','loc'=>'"'.$pasModify['location'].'"','isOrg'=>'"1"')));
                          $oXml->addChildNode($sId3, array('name'=>'s','@attributes' => array('d'=>'"'.$pasModify['starttime'].'"','tz'=>'"'.$pasModify['timezeone'].'"')));
                          $oXml->addChildNode($sId3, array('name'=>'e','@attributes' => array('d'=>'"'.$pasModify['endtime'].'"','tz'=>'"'.$pasModify['timezeone'].'"')));
                          $oXml->addChildNode($sId3, array('name'=>'descHtml','@value'=>''.$pasModify['notes'].''));

                          $oXml->addChildNode($sId3, array('name'=>'or','@attributes' => array('a'=>'"'.$pasModify['organizer']['email'].'"','d'=>'"'.$pasModify['organizer']['name'].'"','sentBy'=>'"'.$pasModify['organizer']['email'].'"','dir'=>'"in:inbox"','lang'=>'"en-US"')));

                          foreach($pasModify['attendees'] as $asAttendee)
                          {
                            $oXml->addChildNode($sId3, array('name'=>'at','@attributes' => array('a'=>'"'.$asAttendee['email'].'"','d'=>'"'.$asAttendee['name'].'"','sentBy'=>'"'.$pasModify['organizer']['email'].'"','dir'=>'"in:inbox"','lang'=>'"en-US"', 'role' => '"'.$asAttendee['role'].'"','ptst' => '"AC"','rsvp'=> '"0"','cutype'=> '"IND"')));
                          }

                         if(!empty($pasModify['recurrent']))
                         {
                           $sId4 = $oXml->addChildNode($sId3, array('name'=>'recur'));
                             $sId5 = $oXml->addChildNode($sId4, array('name'=>'add'));
                               $sId6 = $oXml->addChildNode($sId5, array('name'=>'rule','@attributes' => array('freq'=>'"'.$pasModify['recurrent']['frequency'].'"')));
                                       $oXml->addChildNode($sId6, array('name'=>'count','@attributes' => array('num'=>'"'.$pasModify['recurrent']['count'].'"')));
                                       $oXml->addChildNode($sId6, array('name'=>'interval','@attributes' => array('ival'=>'"1"')));
                                       $oXml->addChildNode($sId6, array('name'=>'until','@attributes' => array('d' =>'"'.date('Y-m-d\THis\Z',strtotime('+'.$pasModify['recurrent']['till'].'')).'"')));
                          }

               // Get the response from the xml component

               $sResponse = $oXml->makeSoapRequest($this->coCurl,$this->cbConnected,true,$this->csAuthtoken,$this->csSessionID);

               //Parse the response
               $asRecords = $oXml->parse($sResponse);

               $sData = serialize($pasModify);

               if(!empty($asRecords) && is_array($asRecords))
               {
                 $asData = $asRecords['SOAP:ENVELOPE'][1]['SOAP:BODY']['MODIFYAPPOINTMENTRESPONSE'];

                 $nMsPk = $asData['MS'];
                 $sTimezone = 'Asia/Tokyo';

                 $sQuery = 'UPDATE `zimbra_cal` set `msId` = '.$oDB->dbEscapeString($nMsPk).', `data` = '.$oDB->dbEscapeString($sData).',`timezone` = '.$oDB->dbEscapeString($sTimezone).' ';
                 $sQuery.= ' WHERE invId = '.$oDB->dbEscapeString($pasModify['invId']);

                 $oDbResult = $oDB->ExecuteQuery($sQuery);

                 if(!$oDbResult)
                   return false;

                 return true;

               }
               else
               {
                 $nStatus = 0;

                 $sQuery = 'UPDATE `zimbra_cal` SET `data` = '.$oDB->dbEscapeString($sData).',`status` = '.$oDB->dbEscapeString($nStatus).'  ';
                 $sQuery.= ' WHERE invId = '.$oDB->dbEscapeString($pasModify['invId']);

                 $oDbResult = $oDB->ExecuteQuery($sQuery);

                 return false;
           }
      }

   /**
    * Remove the appointment
    * @param array $pasRemove
    * @return boolean
    */
    public function getRemoveAppointment($pasRemove, $pbDeleteLocal = true)
    {
      if(!assert('is_array($pasRemove) && !empty($pasRemove)'))
        return false;

      $oDB = CDependency::getComponentByName('database');
      $oXml = XmlSingleton::getXmlInstance();

      $sId = $oXml->addNode(array('name'=>'CancelAppointmentRequest','@attributes' =>array('id'=>'"'.$pasRemove['invId'].'"','comp'=>'"0"','xmlns'=>'"urn:zimbraMail"')));
      $sId1 =  $oXml->addChildNode($sId, array('name'=>'m'));
      $oXml->addChildNode($sId1, array('name'=>'su','@value'=>''.$pasRemove['message'].''));
      $sId2 = $oXml->addChildNode($sId1, array('name'=>'mp','@attributes'=>array('ct'=>'"multipart/alternative"')));
      $sId3 = $oXml->addChildNode($sId2, array('name'=>'mp','@attributes'=>array('ct'=>'"text/plain"')));
      $sId4 =  $oXml->addChildNode($sId3, array('name'=>'content'));

      $sId5 = $oXml->addChildNode($sId2, array('name'=>'mp','@attributes'=>array('ct'=>'"text/html"')));
      $sId4 =  $oXml->addChildNode($sId5, array('name'=>'content'));

      // Get the response from the xml component
      $sResponse = $oXml->makeSoapRequest($this->coCurl,$this->cbConnected,true,$this->csAuthtoken,$this->csSessionID);

      //Parse the response
      $asRecords = $oXml->parse($sResponse);

      if(empty($asRecords) || !is_array($asRecords))
        return false;

      if($pbDeleteLocal)
      {
        $sQuery = 'DELETE from `zimbra_cal` WHERE invId = '.$oDB->dbEscapeString($pasRemove['invId']);
        $oDbResult = $oDB->ExecuteQuery($sQuery);

        if(!$oDbResult)
          return false;
      }

      return true;
    }

   /**
    * Get Free Busy Status
    * @param array $pasData
    * @return boolean
    */

   public function getFreeBusyStatus($pasData)
   {
     if(!assert('is_array($pasData) && !empty($pasData)'))
       return false;

     $oXml = XmlSingleton::getXmlInstance();
     $sId = $oXml->addNode(array('name'=>'GetFreeBusyRequest','@attributes' =>array('s'=>'"'.$pasData['starttime'].'"','e'=>'"'.$pasData['endtime'].'"','xmlns'=>'"urn:zimbraMail"')));
     $oXml->addChildNode($sId, array('name'=>'usr','@attributes' =>array('name'=>'"'.$pasData['name'].'"')));

    $sResponse = $oXml->makeSoapRequest($this->coCurl, $this->cbConnected, true,$this->csAuthtoken, $this->csSessionID);
    $asRecords = $oXml->parse($sResponse);
    //dump($asRecords);

    $asData = array();
    if(isset($asRecords['SOAP:ENVELOPE']['SOAP:BODY']['GETFREEBUSYRESPONSE']['USR']))
      $asData = (array)$asRecords['SOAP:ENVELOPE']['SOAP:BODY']['GETFREEBUSYRESPONSE']['USR'];

    return $asData;
  }

   /**
    * Get the appointments
    * @param array $pasRecords
    * @return array data
    */

   public function getAppointments($pasRecords)
   {
      if(!assert('is_array($pasRecords) && !empty($pasRecords)'))
        return false;

      $oXml = XmlSingleton::getXmlInstance();

       $sId = $oXml->addNode(array('name'=>'BatchRequest','@attributes' =>array('onerror'=>'"continue"','xmlns'=>'"urn:zimbra"')));
       $oXml->addChildNode($sId, array('name'=>'GetApptSummariesRequest','@attributes' =>array('xmlns'=>'"urn:zimbraMail"','l'=>'"'.$pasRecords['calendar'].'"','s'=>'"'.$pasRecords['startdate'].'"','e'=>'"'.$pasRecords['enddate'].'"')));

       // Get the response from the xml component
       $sResponse = $oXml->makeSoapRequest($this->coCurl,$this->cbConnected,true,$this->csAuthtoken,$this->csSessionID);

       //Parse the response
       $asRecords = $oXml->parse($sResponse);

       if(isset($asRecords['SOAP:ENVELOPE']['SOAP:BODY']['BATCHRESPONSE']['GETAPPTSUMMARIESRESPONSE']['APPT']))
       {
         //if there's only one event, zimbra return it straight not in an array. Correcting it here
         if(isset($asRecords['SOAP:ENVELOPE']['SOAP:BODY']['BATCHRESPONSE']['GETAPPTSUMMARIESRESPONSE']['APPT']['UID']))
           return array($asRecords['SOAP:ENVELOPE']['SOAP:BODY']['BATCHRESPONSE']['GETAPPTSUMMARIESRESPONSE']['APPT']);
         else
           return $asRecords['SOAP:ENVELOPE']['SOAP:BODY']['BATCHRESPONSE']['GETAPPTSUMMARIESRESPONSE']['APPT'];
       }

      return array();
   }

   /**
    * Use this one to get the detail of the appointment
    * @param string $psId
    * @return array
    */

   public function getAppointment($psEventId)
   {
     if(!assert('!empty($psEventId)'))
        return array();

     $oXml = XmlSingleton::getXmlInstance();
     $sId1 = $oXml->addNode(array('name'=>'GetMsgRequest','@attributes' =>array('xmlns'=>'"urn:zimbraMail"')));
     $oXml->addChildNode($sId1, array('name'=>'m','@attributes' =>array('id'=>'"'.$psEventId.'"')));

     // Get the response from the xml component
     $sResponse = $oXml->makeSoapRequest($this->coCurl, $this->cbConnected, true, $this->csAuthtoken, $this->csSessionID);

     //Parse the response
     $asRecords = $oXml->parse($sResponse);
     $asData = array();

     if(isset($asRecords['SOAP:ENVELOPE']['SOAP:BODY']['GETMSGRESPONSE']['M']))
       $asData = $asRecords['SOAP:ENVELOPE']['SOAP:BODY']['GETMSGRESPONSE']['M'];

     return $asData;
   }

   /**
    * Get the status of the attendees
    * @param integer $pnLoginPk
    * @return array of data
    */

    protected function getAttendeeStatus($panLoginPk, $psTimestamp)
    {
     if(!assert('is_array($panLoginPk)') || empty($panLoginPk))
       return array();

     $oDB = CDependency::getComponentByName('database');

     $sQuery = 'SELECT zcal.starttime, zcal.endtime, zcal.creatorfk, za.loginfk FROM zimbra_cal AS zcal ';
     $sQuery.=' LEFT JOIN zimbra_attendees AS za ON (za.zcalfk = zcal.zimbra_calpk AND za.loginfk IN ('.implode(',', $panLoginPk).'))';
     $sQuery.=' WHERE zcal.starttime >= "'.date('Y-m-d', $psTimestamp).'" AND zcal.endtime < "'.date('Y-m-d', strtotime('+1 day', $psTimestamp)).'" ';
     //echo $sQuery;

     $oDbResult = $oDB->ExecuteQuery($sQuery);
     $bRead = $oDbResult->readFirst();

     $asBusy = array();
     while($bRead)
     {
       $asEventData = $oDbResult->getData();

       if(!empty($asEventData['loginfk']))
         $asBusy[$asEventData['loginfk']][] = $asEventData;

       $asBusy[$asEventData['creatorfk']][] = $asEventData;


       $bRead =  $oDbResult->readNext();
     }

     return $asBusy;
   }

   /**
    *@param array $pasCalendarEvent
    * @return boolean (display message during process
    */
    private function _refreshLocalCalendars($pasCalendarEvent)
    {
      if(!assert('is_array($pasCalendarEvent) && !empty($pasCalendarEvent)'))
      return false;


      $oLogin = CDependency::getComponentByName('login');
      $oDB = CDependency::getComponentByName('database');

      //Get all the live users in the bcm
      $asUsers = $oLogin->getUserList(0, true, false);
      $asUserEmail = array();
      foreach($asUsers as $asUserData)
        $asUserEmail[$asUserData['email']] = $asUserData['loginpk'];


      $asLocalData = array();
      $sQuery = 'SELECT * FROM zimbra_cal AS zcal ';
      $sQuery.= ' LEFT JOIN zimbra_attendees AS zatnd ON (zcal.zimbra_calpk = zatnd.zcalfk)  ';
      $sQuery.= ' WHERE starttime >= "'.date('Y-m-d', strtotime('-1 month')).'" AND starttime < "'.date('Y-m-d', strtotime('+2 month')).'" ';
      //echo $sQuery;

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      while($bRead)
      {
        $asLocalData[$oDbResult->getFieldValue('invId')] = $oDbResult->getData();
        $bRead = $oDbResult->readNext();
      }

      //dump($asLocalData);
      $asSqlUpdate = array();
      $asSqlInsert = array();
      $asSqlAttendees = array();

      //For every event found on the server, I update or create in BCM
      foreach($pasCalendarEvent as $vEventKey => $asEventData)
      {
        $nEventId = $asEventData['evID'];
        $sStartDate = $this->convertZimbraDate($asEventData['start']);
        $sEndDate = $this->convertZimbraDate($asEventData['end']);

        if(isset($asLocalData[$nEventId]))
        {
          //found the event in the local database
          $asData['calendar'] = $this->cnDefaultUserCal;
          $asData['title'] = $asEventData['evTitle'];
          $asData['eventname'] = $asEventData['evTitle'];
          $asData['description'] = $asEventData['evDesc'];
          $asData['location'] = '';

          $asLocalData[$nEventId]['synchedInBcm'] = true;

          $asSqlUpdate[] = 'UPDATE `zimbra_cal` SET `apptId` = '.$oDB->dbEscapeString($asEventData['evApptId']).',
            `msId` = '.$oDB->dbEscapeString($asEventData['evMsId']).', `data` = '.$oDB->dbEscapeString(serialize($asData)).',
            `starttime` = '.$oDB->dbEscapeString($sStartDate).',`endtime` = '.$oDB->dbEscapeString($sEndDate).',
            `status` = 1 WHERE zimbra_calpk = '.(int)$asLocalData[$nEventId]['zimbra_calpk'];

          //TODO: refresh list of attendees
        }
        else
        {
          //didn't find the eventy, we need to create it.
          $asData['calendar'] = $this->cnDefaultUserCal;
          $asData['title'] = $asEventData['evTitle'];
          $asData['eventname'] = $asEventData['evTitle'];
          $asData['description'] = $asEventData['evDesc'];
          $asData['location'] = '';
          $nOwnerfk = (int)$asEventData['evOwnerfk'];

          //check if there are attendees and get the sql ready
          if(count($asEventData['evAttendee']) > 1)
          {
            foreach($asEventData['evAttendee'] as $sUserIdentifier => $nTeamfk)
            {
              if(is_numeric($sUserIdentifier))
              {
                $nUserfk = (int)$sUserIdentifier;
                $sEmail = $asUsers[$sUserIdentifier]['email'];
              }
              else
              {
                $nUserfk = (int)$asUserEmail[$sUserIdentifier];
                $sEmail = $sUserIdentifier;
              }
              if($nUserfk != $nOwnerfk)
              {
                $asSqlAttendees[] = '((select zimbra_calpk FROM zimbra_cal WHERE invId = '.$oDB->dbEscapeString($nEventId).'),
                  '.$nUserfk.', '.$oDB->dbEscapeString($sEmail).')';

                $asData['attendees'][$nUserfk] = $sEmail;
              }
            }
          }

          $sQuery = '('.$nOwnerfk.','.$oDB->dbEscapeString($nEventId).','.$oDB->dbEscapeString($asEventData['evApptId']).',';
          $sQuery.= ' '.$oDB->dbEscapeString($asEventData['evMsId']).', '.$oDB->dbEscapeString(serialize($asData)).', ';
          $sQuery.= ' '.$oDB->dbEscapeString($sStartDate).','.$oDB->dbEscapeString($sEndDate).', 1)';
          $asSqlInsert[] = $sQuery;
        }
      }


      if(!empty($asSqlInsert))
      {
        $sQuery = 'INSERT INTO `zimbra_cal` (`creatorfk`, `invId`, `apptId`, `msId`, `data`,`starttime`,`endtime`, `status`) VALUES ';
        $sQuery.= implode(', ', $asSqlInsert);
        $oDbResult = $oDB->ExecuteQuery($sQuery);
        if(!$oDbResult)
        {
          assert('false; // couldn\'t insert the events found on the mail server');
          return false;
        }

        echo count($asSqlInsert).' local events inserted <br />';
      }

      if(!empty($asSqlAttendees))
      {
        $sQuery = 'INSERT INTO zimbra_attendees (`zcalfk`,`loginfk`,`email`) VALUES ';
        $sQuery.= implode(', ', $asSqlAttendees);
        echo $sQuery;
        $oDbResult = $oDB->ExecuteQuery($sQuery);
        if(!$oDbResult)
        {
          assert('false; // couldn\'t insert the events attendees found on the mail server');
          return false;
        }

        echo count($asSqlAttendees).' local events inserted <br />';
      }

      if(!empty($asSqlUpdate))
      {
        foreach($asSqlUpdate as $sQuery)
        {
          $oDbResult = $oDB->ExecuteQuery($sQuery);
          if(!$oDbResult)
          {
            assert('false; // couldn\'t update the events found on the mail server');
            return false;
          }
        }
        echo count($asSqlUpdate).' local events updated <br />';
      }

      //Delete events that have been deleted
      $anEventToDelete = array();
      foreach($asLocalData as $nEventId => $asEventData)
      {
        if(!isset($asEventData['synchedInBcm']) || empty($asEventData['synchedInBcm']))
        {
          $anEventToDelete[$asEventData['zimbra_calpk']] = (int)$asEventData['zimbra_calpk'];
          //echo var_export($asEventData, true).'<br /><br />';
        }
      }

      if(!empty($anEventToDelete))
      {
        $sQuery = 'DELETE FROM zimbra_cal WHERE zimbra_calpk IN ('.implode(',', $anEventToDelete).') ';
        //echo $sQuery.'<br />';
        $oDbDelCal = $oDB->ExecuteQuery($sQuery);

        $sQuery = 'DELETE FROM zimbra_attendees WHERE zcalfk IN ('.implode(',', $anEventToDelete).') ';
        //echo $sQuery.'<br />';
        $oDbDelAtt = $oDB->ExecuteQuery($sQuery);

        if(!$oDbDelCal || !$oDbDelAtt)
        {
          assert('false; // couldn\'t delete events');
          return false;
        }
        echo count($anEventToDelete).' local events not found on zimbra <br />';
      }

     // Call the subcreateappointment function by creating the array of it
     // repeat the process for everyone
     return true;
   }

  //Update BCM's shared calendar base on every user personal calendar
  protected function syncWithZimbra($pbRefreshShared = true, $pbUpdateLocal = false)
  {
    if(!$pbRefreshShared && !$pbUpdateLocal)
      return false;

    //get the list of events for all BCM's active users
    $asCalendarData = $this->_getEventsFromZimbra();

    if($pbRefreshShared)
    {
      $this->_refreshSharedCalendars($asCalendarData);
    }

    if($pbUpdateLocal)
    {
      $this->_refreshLocalCalendars($asCalendarData);
    }

    return true;
  }


  /**return an array with all the event details for every active user in BCM
   * @return array
  */
  private function _getEventsFromZimbra()
  {
    $oLogin = CDependency::getComponentByName('login');

    // Get the list of the live users
    $asUsers =  $oLogin->getUserList(0, true, false);

    $asUserEmail = array();
    foreach($asUsers as $skey => $asUser)
      $asUserEmail[$asUser['email']] = (int)$asUser['loginpk'];

    //specific cases
    $asUserEmail['ohade6@gmail.com'] = 26;
    $asUserEmail['sboudoux@gmail.com'] = 5;


    $asAppointments = array();
    $asCalendarData = array();
    $sStartTime = (int)strtotime('-2 week') * 1000;
    $sEndTime = (int)strtotime('+1 month') * 1000;

    foreach($asUsers as $skey => $asUser)
    {
      $asTmp = explode('@', $asUser['email']);
      $sUserId = $asTmp[0];

      $bLogged = $this->initialize($sUserId);
      if($bLogged)
      {
        echo '<hr /> logged to bcm cal as ['.$sUserId.']';

        if(isset($this->casZUser[$asUser['loginpk']]))
        {
          echo '<br />have specific calendars ['.$this->casZUser[$asUser['loginpk']]['calendarIds'].']';
          $asAppointments = array();
          foreach($this->casZUser[$asUser['loginpk']]['calendars'] as $sCalId)
          {
            $asAppointments = array_merge_recursive($asAppointments, $this->getAppointments(array('calendar'=> (int)$sCalId, 'startdate'=>$sStartTime, 'enddate'=>$sEndTime)));
          }
        }
        else
        {
          //get the user event list for the specified period
          $asAppointments = $this->getAppointments(array('calendar'=>'10', 'startdate'=>$sStartTime, 'enddate'=>$sEndTime));
        }
        echo '<br /> user has '.count($asAppointments).' events';

        if(!empty($asAppointments))
        {
          foreach($asAppointments as $skey => $asData)
          {
            if(isset($asData['INVID']) && !empty($asData['INVID']))
            {
              $sInvId = $asData['INVID'];
              $asAppointmentData = $this->getAppointment($sInvId);

              //if we can't get event details something is wrong.
              if(!isset($asAppointmentData['INV']['COMP']) || empty($asAppointmentData))
              {
                assert('false; //'.__LINE__.' - weidly formated or empty event ('.$sInvId.') ');
                dump($asAppointmentData);
                exit();
              }
              else
              {
                //-----------------------------------------------
                //get the particpant(s) of the current event: attendees or creator
                $asAttendee = array();
                if(isset($asAppointmentData['INV']['COMP']['AT']) && !empty($asAppointmentData['INV']['COMP']['AT']) && is_array($asAppointmentData['INV']['COMP']['AT']))
                {
                  //A 2 dimension array if multiple attendees, single dimension if 1
                  if(isset($asAppointmentData['INV']['COMP']['AT'][0]))
                  {
                    foreach($asAppointmentData['INV']['COMP']['AT'] as $asAtt)
                    {
                      if(!isset($asUserEmail[$asAtt['A']]))
                      {
                        dump($asAppointmentData);
                        assert('false; // don\'t know this attendee email address');
                      }
                      else
                        $asAttendee[$asUserEmail[$asAtt['A']]] = $asUser['teamfk'];
                    }
                  }
                  else
                    $asAppointmentData['INV']['COMP']['AT']['A'] = $asUser['teamfk'];
                }
                else
                  $asAttendee = array($asUser['email'] => $asUser['teamfk']);


                //-----------------------------------------------
                //if this event has already been treated: I simply add the new attendees
                if(isset($asCalendarData[$sInvId.'_'.$asAppointmentData['INV']['COMP']['S']['D'].'_'.$asAppointmentData['INV']['COMP']['E']['D']]['evAttendee']))
                {
                  $asPreviousAttendees = $asCalendarData[$sInvId.'_'.$asAppointmentData['INV']['COMP']['S']['D'].'_'.$asAppointmentData['INV']['COMP']['E']['D']]['evAttendee'];
                  $asCalendarData[$sInvId.'_'.$asAppointmentData['INV']['COMP']['S']['D'].'_'.$asAppointmentData['INV']['COMP']['E']['D']]['evAttendee'] = array_merge($asPreviousAttendees, $asAttendee);
                }
                else
                {
                  $sStartDate = $asAppointmentData['INV']['COMP']['S']['D'];
                  $sEndDate = $asAppointmentData['INV']['COMP']['E']['D'];
                  //echo $sInvId.': '.$sStartDate.' / '.$sEndDate.'<br />';

                  if(isset($asAppointmentData['INV']['COMP']['FR']['DATA']))
                    $sDescription = $asAppointmentData['INV']['COMP']['FR']['DATA'];
                  else
                    $sDescription = '';

                  $asCalendarData[$sInvId.'_'.$asAppointmentData['INV']['COMP']['S']['D'].'_'.$asAppointmentData['INV']['COMP']['E']['D']] =
                          array('evID' => $sInvId, 'evTitle' => $asAppointmentData['INV']['COMP']['NAME'], 'evDesc' => $sDescription,
                                'evAttendee' => $asAttendee, 'start' => $sStartDate, 'end' => $sEndDate, 'evOwnerfk' => $asUser['loginpk'],
                                'evApptId' => $asData['ID'], 'evMsId' => $asData['MS']);
                }
              }
            }
          }
        }
      }
      else
      {
        echo '<hr /> couldn\'t log to bcm as ['.$sUserId.'] ';
        if(!in_array($sUserId, array('siraa_lviv')))
        {
          assert('false; //can not log to bcm calendar acount');
        }
        echo $this->getErrors(true);
      }
    }

    //Remove the duplicate records
    //dump($asCalendarData);
    //exit('parsed everything. exit');

    if(empty($asCalendarData))
      echo '<br /><br />All user checked: no event found  O_o';

    echo '<hr /><hr />';

    return $asCalendarData;
  }


  private function _refreshSharedCalendars($pasCalendarEvent)
  {
    if(!assert('is_array($pasCalendarEvent) && !empty($pasCalendarEvent)'))
      return false;

    //------------------------------------
    //wipe global and sales calendar from all their events
    $this->_wipeUserCalendar('bcm', $this->cnSharedCalGlobal);
    $this->_wipeUserCalendar('bcm', $this->cnSharedCalSales);


    //Log as BCM to recreate all the events in Global and Sales calendars
    if(!$this->initialize('bcm'))
    {
      assert('false; //can not log to bcm calendar acount');
      echo $this->getErrors(true);
      return false;
    }


    //Recreate all the events we've gathered from users
    $sLocation = '';
    $sTimeZone = 'Asia/Tokyo';
    $asBCMOrganizer = array('email' => 'bcm@bulbouscell.com','name' => 'BCM');
    $nSalesEvent = $nGlobalEvent = 0;

    foreach($pasCalendarEvent as $asData)
    {
      $sEventName = implode(', ', array_keys($asData['evAttendee'])).': '.$asData['evTitle'];
      $sEventNote = $asData['evTitle'];
      if(isset($asData['evtDesc']) && !empty($asData['evtDesc']))
        $sEventNote.= ' - '.$asData['evtDesc'];

      //Check one of the attendee is in "sales" => team sales and management(1,3)
      if(in_array('1', $asData['evAttendee']) || in_array('3', $asData['evAttendee']))
      {
        $asSalesCreate = array('calendar'  =>  $this->cnSharedCalSales, 'title' => $sEventName, 'eventname' => $sEventName,
          'timezeone' => $sTimeZone, 'location' => $sLocation, 'starttime' => $asData['start'], 'endtime' => $asData['end'],
          'notes' => $sEventNote, 'organizer' => $asBCMOrganizer);

        if($this->getsubCreateAppointment($asSalesCreate))
          $nSalesEvent++;
        else
        {
          echo 'could not create a sales event: <br />';
          echo $this->getErrors(true);
          $this->casError = array();
        }
      }

      $asGlobalCreate = array('calendar'  =>  $this->cnSharedCalGlobal, 'title' => $sEventName, 'eventname' => $sEventName,
          'timezeone' => $sTimeZone, 'location' => $sLocation, 'starttime' => $asData['start'], 'endtime' => $asData['end'],
          'notes' => $sEventNote, 'organizer' => $asBCMOrganizer);

      if($this->getsubCreateAppointment($asGlobalCreate))
        $nGlobalEvent++;
      else
      {
        echo 'could not create a global event: <br />';
        echo $this->getErrors(true);
        $this->casError = array();
      }
    }

    echo '<br />'.$nSalesEvent.' Sales event created / '.$nGlobalEvent.' total events done';
    return true;
  }


  /**
   *delete all the event of a specific user in a specific calendar
   * @param string $psUserId
   * @param integer $pnCalendarId
   * @param interger $pnStartTime
   * @param interger $pnEndTime
   * @return boolean
  */
  private function _wipeUserCalendar($psUserId, $pnCalendarId, $pnStartTime = 0, $pnEndTime = 0)
  {
    if(!assert('!empty($psUserId) && is_integer($pnCalendarId) && !empty($pnCalendarId)'))
      return false;

    $bLogged = $this->initialize($psUserId);
    if(!$bLogged)
      return false;

    if(empty($pnStartTime) || empty($pnEndTime))
    {
      $pnStartTime = (int)strtotime('-3 month') * 1000;
      $pnEndTime = (int)strtotime('+3 month') * 1000;
    }

    $asAppointments = $this->getAppointments(array('calendar' => $pnCalendarId, 'startdate'=> $pnStartTime, 'enddate'=> $pnEndTime));
    echo '<hr />DELETING ==> logged to bcm cal as ['.$psUserId.']  <br /> user has '.count($asAppointments).' events in the calendar #'.$pnCalendarId.' ';

    if(empty($asAppointments))
      return true;

    $nDelete = 0;
    foreach($asAppointments as $asData)
    {
      if(isset($asData['INVID']) && !empty($asData['INVID']))
      {
        $asDelete['invId'] = $asData['INVID'];
        $asDelete['message'] = 'wipe your events';

        if($this->getRemoveAppointment($asDelete, 0))
          $nDelete++;
        else
          echo 'couldn\'t delete the event '.$asData['INVID'].'<br />';
      }
    }

    echo '<br />'.$nDelete.' events deleted ';

    return true;
  }


  public function getHomepageUserCalendar()
  {
    $oDisplay = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');
    $sHTML = '';

    $nLoginPk = $oLogin->getUserPk();

    $sQuery = 'SELECT *, DATE(starttime) as eventDate, DATE_FORMAT(starttime, "%V") as eventWeek ';
    $sQuery.= ', DATE_FORMAT(starttime, "%H:%i") as eventStart, DATE_FORMAT(endtime, "%H:%i") as eventEnd FROM zimbra_cal AS zcal ';
    $sQuery.= ' LEFT JOIN zimbra_attendees AS zatnd ON (zatnd.zcalfk = zcal.zimbra_calpk AND zatnd.loginfk = '.$nLoginPk.') ';
    $sQuery.= ' WHERE (zcal.creatorfk = '.$nLoginPk.' OR zatnd.loginfk = '.$nLoginPk.') ';
    $sQuery.= ' AND starttime >= "'.date('Y-m-d').'" AND starttime <  "'.date('Y-m-d H:i:s',strtotime('+14 days')).'" ';
    //$sQuery.= ' GROUP BY DATE(starttime) ASC ORDER BY starttime ASC LIMIT 0, 8 ';
    $sQuery.= ' GROUP BY invId, starttime ORDER BY starttime ASC  LIMIT 0, 8 ';
    //echo $sQuery;

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
    {
      $sHTML.= $oDisplay->getListStart('', array('class' => 'homeCalendarList'));
      $sHTML.= $oDisplay->getListItemStart('', array('class' => 'homeCalendarList'));
      $sHTML.= $oDisplay->getText('No events to display.');
      $sHTML.=  $oDisplay->getListItemEnd();
      $sHTML.=  $oDisplay->getListEnd();
      return $sHTML;
    }

    $sToday = date('Y-m-d');
    $sTomorrow = date('Y-m-d', strtotime('+1 day'));
    $sThisWeek = date('W');
    $sNextWeek = (int)$sThisWeek +1;
    if(strlen($sNextWeek) == 1)
      $sNextWeek = '0'.$sNextWeek;

    $asCalendar = array();
    while($bRead)
    {
      $sDate = $oDbResult->getFieldValue('eventDate');
      $sWeek = $oDbResult->getFieldValue('eventWeek');

      if($sDate == $sToday)
      {
        $asCalendar['Today'][] = $oDbResult->getData();
      }
      elseif($sDate == $sTomorrow)
      {
        $asCalendar['Tomorrow'][] = $oDbResult->getData();
      }
      elseif($sWeek == $sThisWeek)
      {
        $asCalendar['This week'][] = $oDbResult->getData();
      }
      else
        $asCalendar['2 weeks'][] = $oDbResult->getData();

      $bRead = $oDbResult->readNext();
    }

    $sHTML.= $oDisplay->getListStart('', array('class' => 'homeCalendarList'));

    $bFirst = true;
    foreach($asCalendar as $sPeriod => $asCalendarData)
    {
      //Title here
      if($bFirst)
      {
        $sHTML.= $oDisplay->getListItemStart('',array('class' => 'homeCalendarListTitle listTitleFirst'));
        $bFirst = false;
      }
      else
        $sHTML.= $oDisplay->getListItemStart('',array('class' => 'homeCalendarListTitle'));

      $sHTML.= $oDisplay->getText($sPeriod);
      $sHTML.=  $oDisplay->getListItemEnd();

      foreach($asCalendarData as $asEventData)
      {
        // Get all the activities of the date
        $sHTML.= $oDisplay->getListItemStart();
        $sHTML.= $oDisplay->getPicture('/common/pictures/items/calendar_16.png','Calendar','');

        $sHTML.= $oDisplay->getText(' '.$asEventData['eventStart'].' - '.$asEventData['eventEnd'].' | ');
        $sHTML.= $oDisplay->getText($this->_getEventTitle($asEventData['data']));
        $sHTML.= $oDisplay->getListItemEnd();
      }
    }

    $sHTML.= $oDisplay->getListItemStart();
      $sHTML.= $oDisplay->getCarriageReturn();
      $sHTML.= $oDisplay->getPicture("/common/pictures/items/calendar_add_16.png");
      $sURL = $oPage->getURL('zimbra', CONST_ACTION_ADD, 'calendar', 0);
      $sHTML.= $oDisplay->getSpace();
      $sHTML.= $oDisplay->getLink('Add a new event',$sURL);
    $sHTML.=  $oDisplay->getListItemEnd();

    $sHTML.= $oDisplay->getListEnd();

    return $sHTML;
  }

  // Get the event title from serialized string
  private function _getEventTitle($psData)
  {
    if(!assert('!empty($psData)'))
        return 'Empty string.';

    $asData = @unserialize($psData);

    if(!empty($asData))
      return $asData['title'];

    return '';
  }

  protected function _getViewCalendar()
  {
    $oDisplay = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');

    $sAjaxFeed = $oPage->getAjaxUrl('zimbra', CONST_ACTION_VIEW, CONST_ZCAL_EVENT);


    //$oPage->addRequiredJsFile($this->getResourcePath().'fullcalendar/fullcalendar.min.js');
    $oPage->addRequiredJsFile($this->getResourcePath().'fullcalendar/fullcalendar.js');
    $oPage->addCssFile($this->getResourcePath().'fullcalendar/fullcalendar.css');
    $oPage->addCssFile($this->getResourcePath().'css/zimbra.css');

    $sHTML = '';
    $sHTML.= $oDisplay->getBlocStart('mainCalendareContainer', array('style' => 'width: 100%; min-height: 650px; position: relative;'));

      $sHTML.= $oDisplay->getBlocStart('calendarFilterContainer', array('style' => 'width: 100%; border: 1px solid #ddd;'));

      $oForm = $oDisplay->initForm('calendarViewForm');
      $oForm->setFormParams('', true, array('action' => 'javascript:;'));
      $oForm->setFormDisplayParams(array('noCancelButton' => 1, 'columns' => 2, 'submitButtonHidden' => 1));

      $oForm->addField('select', 'calUsers', array('label' => 'Select users to display', 'value' => '', 'multiple' => 1, 'onchange' => " $('#eventPopupId:visible').fadeOut(); $('#calendarViewContainer').fullCalendar('refetchEvents'); "));

      $asUsers = $oLogin->getUserList(0, true);
      $nPreSelected = getValue('ppk', 0);

      //for top menu
      if($nPreSelected == -1)
        $nPreSelected = $oLogin->getUserPk();

      foreach($asUsers as $asUserData)
      {
        if($nPreSelected == $asUserData['loginpk'])
          $oForm->addOption('calUsers', array('label' => $asUserData['firstname'].' '.$asUserData['lastname'], 'value' => $asUserData['loginpk'], 'team' => $asUserData['teamfk'], 'selected' => 'selected'));
        else
          $oForm->addOption('calUsers', array('label' => $asUserData['firstname'].' '.$asUserData['lastname'], 'value' => $asUserData['loginpk'], 'team' => $asUserData['teamfk']));
      }

      $sHTML.= $oForm->getDisplay();
      $sHTML.= $oDisplay->getBlocEnd();

      $sJavascript = ' $(\'#calUsersId\').children().removeAttr(\'selected\').end().change(); ';
      $sHTML.= $oDisplay->getBlocStart('', array('style' => 'position: absolute; top: 1px; right: 15px; '));

      $sHTML.= '<a onclick="$(\'#calUsersId\').children().attr(\'selected\', \'selected\').end().change();" href="javascript:;">all</a>';
      $sHTML.= $oDisplay->getText(' - ');
      $sHTML.= '<a onclick="$(\'#calUsersId\').children().removeAttr(\'selected\').end().change();" href="javascript:;">none</a>';

      $sHTML.= $oDisplay->getText(' - ');
      $sHTML.= $oDisplay->getLink('Sales', 'javascript:;', array('onclick' => $sJavascript.'$(\'#calUsersId\').children(\'option[team=1],option[team=3]\').attr(\'selected\', \'selected\').end().change();'));
      $sHTML.= $oDisplay->getText(' - ');
      $sHTML.= $oDisplay->getLink('IT', 'javascript:;', array('onclick' => $sJavascript.'$(\'#calUsersId\').children(\'option[team=2]\').attr(\'selected\', \'selected\').end().change();'));
      $sHTML.= $oDisplay->getText(' - ');
      $sHTML.= $oDisplay->getLink('Prod', 'javascript:;', array('onclick' => $sJavascript.'$(\'#calUsersId\').children(\'option[team=4]\').attr(\'selected\', \'selected\').end().change();'));
      $sHTML.= $oDisplay->getBlocEnd();

      $sHTML.= $oDisplay->getBlocStart('calendarViewContainer', array('style' => 'width: 95%; margin: 10px auto 0 auto; border: 1px solid #eee; min-heigh: 600px;'));
      $sHTML.= $oDisplay->getBlocEnd();

    $sHTML.= $oDisplay->getBlocEnd();


    $sHTML.= '<script>
    $(document).ready(function()
    {
      var date = new Date();
      var d = date.getDate();
      var m = date.getMonth();
      var y = date.getFullYear();
      var nDocWidth = $(body).innerWidth();

      $("#calendarViewContainer").fullCalendar(
      {
        header: {left: "prev, next, today", center: "title", right: "month,agendaWeek,agendaDay"},
        editable: false,
        defaultView: "agendaWeek",
        theme: false,
        height: 660,
        contentHeight: 660,
        minTime: "8:00am",
        maxTime: "10:00pm",
        /*selectable: true,
        selectHelper: true,  //need jqueryUi
        editable: true,
        dragOpacity: 0.3,*/
        events: function(start, end, callback)
        {
          setLoadingScreen("#calendarViewContainer", true, true);
          var sUserId = $("#calUsersId").val();
          $.ajax(
          {
            url: "'.$sAjaxFeed.'",
            dataType: "json",
            data:
            {
                // our hypothetical feed requires UNIX timestamps
                start: Math.round(start.getTime() / 1000),
                end: Math.round(end.getTime() / 1000),
                users: sUserId
            },
            success: function(json)
            {
              //parse xml, to replace by json
              if(!json || json.error)
              {
                alert("Sorry an error occured");
                setLoadingScreen("", false);
                return false;
              }

              //console.log(json);
              callback(json.data);
              setLoadingScreen("", false);

            },
            error: function()
            {
              setLoadingScreen("", false);
            }
          });
        },
        eventAfterRender: function(event, element, view)
        {
          //console.log("after_render: "+event.eventColor);
          if(event.eventColor)
          {
            $(".fc-event-head", element).css("background-color", "#"+event.eventColor);

            if(event.attendees)
              $(".fc-event-title", element).append("<br />"+event.attendees);


            $(".fc-event-title", element).attr("style", "text-decoration: underline; color: #0D79BC;");
            $(element).addClass("eventClickable").attr("eventId", event.eventId);
            $(element).bind("mouseover",function()
            {
              if($("#eventPopupId:visible").attr("eventId") == $(this).attr("eventId"))
                return true;

              $("#eventPopupId").fadeOut("fast", function()
              {
                oPosition = element.offset();
                while((oPosition.left+350) > nDocWidth)
                  oPosition.left = oPosition.left-50;

                $("#eventPopupId").attr("eventId", event.eventId);
                $("#eventPopupId").html(event.eventTime+"<br />"+event.htmlTitle+"<br />");

                if(event.attendees)
                  $("#eventPopupId").append(event.attendees+"<br />");

                if(event.description)
                  $("#eventPopupId").append(event.description);

                $("#eventPopupId").attr("style", "top:"+(oPosition.top +10)+"px; left: "+(oPosition.left+50)+"px; ");
                $("#eventPopupId").fadeIn("fast");
              });
            });
          }
        }
      });
        /*
        $("#calendar").fullCalendar("option", "height", 700);
        $("#calendar").fullCalendar("option", "contentHeight", 650);

        */
    });
    </script>';

    $sHTML.= $oDisplay->getBlocStart('eventPopupId', array('onclick' => '$(this).fadeOut();'));
    $sHTML.= $oDisplay->getBlocEnd();

    return $sHTML;
  }

  protected function _getLocalEventsForCalendar()
  {
    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');

    $nStartTime = getValue('start', 0);
    $nEndTime = getValue('end', 0);

    $asUsers = $oLogin->getUserList(0, true);
    if(isset($_GET['users']) && !empty($_GET['users']))
      $asUserFk = $_GET['users'];
    else
      $asUserFk = array();

    //control the list of users
    foreach($asUserFk as $sUserKey)
    {
      if(!isset($asUsers[$sUserKey]))
        unset($asUserFk[$sUserKey]);
    }

    if(count($asUserFk) == 1)
      $bMultiple = false;
    else
      $bMultiple = true;

    if(empty($nStartTime))
      $sStartDate = date('Y-m-d H:i:s', now('this monday'));
    else
      $sStartDate = date('Y-m-d H:i:s', $nStartTime);

    if(empty($nEndTime))
      $sEndDate = date('Y-m-d H:i:s', strtotime('this sunday'));
    else
      $sEndDate = date('Y-m-d H:i:s', $nEndTime);




    $sQuery = 'SELECT *, GROUP_CONCAT(DISTINCT(zcal.creatorfk)) as creators, GROUP_CONCAT(DISTINCT(zatnd.loginfk)) as attend FROM zimbra_cal AS zcal ';


    if(!empty($asUserFk))
    {
      foreach($asUserFk as $sKey)
        $asUserFk[$sKey] = (int)$sKey;

      $sUsers = implode(',', (array)$asUserFk);
      $sQuery.= ' LEFT JOIN zimbra_attendees AS zatnd ON (zatnd.zcalfk = zcal.zimbra_calpk ) ';  //AND zatnd.loginfk IN ('.$sUsers.')
      $sQuery.= ' WHERE starttime >= "'.$sStartDate.'" AND starttime <  "'.$sEndDate.'" ';
      $sQuery.= ' AND (zcal.creatorfk IN ('.$sUsers.') OR zatnd.loginfk IN ('.$sUsers.')) ';
    }
    else
    {
      $sQuery.= ' LEFT JOIN zimbra_attendees AS zatnd ON (zatnd.zcalfk = zcal.zimbra_calpk) ';
      $sQuery.= ' WHERE starttime >= "'.$sStartDate.'" AND starttime <  "'.$sEndDate.'" ';
    }


    $sQuery.= ' GROUP BY zimbra_calpk ORDER BY starttime ASC ';
    //echo $sQuery;

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return json_encode(array('data' => '', 'msg' => 'No event found ['.$nStartTime.'/'.$nEndTime.']'));




    $asEvents = array();
    while($bRead)
    {
      $nCreatorFk = $oDbResult->getFieldValue('creatorfk');
      $asEventData = unserialize($oDbResult->getFieldValue('data'));


      //{"id":111,"title":"Event1","start":"2012-11-10","end":"2012-11-10","url":"http:\/\/yahoo.com\/"}
      $sAttendees = '';
      $asAttendees = explode(',', $oDbResult->getFieldValue('creators'));
      $asAttendees = array_merge($asAttendees, explode(',', $oDbResult->getFieldValue('attend')));

      foreach($asAttendees as $sKey => $sId)
      {
        if(empty($sId) || $sId == $nCreatorFk)
          unset($asAttendees[$sKey]);
        else
        {
          if($sId == $oLogin->getUserPk())
            $asAttendees[$sKey] = 'me';
          else
            $asAttendees[$sKey] = $oLogin->getUserNameFromData($asUsers[$sId], true);
        }
      }


      if($bMultiple)
      {
        $sCleanTitle = $oLogin->getUserNameFromData($asUsers[$nCreatorFk], true);
        $sTitle = '<span class=\'eventPopupTitle\'>Creator: </span>'.$sCleanTitle;
        $sDescription = $asEventData['title'];
      }
      else
      {
        $sCleanTitle = $asEventData['title'];
        $sTitle = '<span class=\'eventPopupTitle\'>Title: </span>'.$asEventData['title'];
        $sDescription = '';
      }

      if(isset($asEventData['notes']))
        $sDescription.= $asEventData['notes'];
      elseif(isset($asEventData['description']))
        $sDescription.=  $asEventData['description'];

      if(!empty($sDescription))
        $sDescription = '<span class=\'eventPopupDesc\'>Description:</span><br /><div class=\'eventPopupDesc\'>'.strip_tags($sDescription).'</div>';

      $sEventId = md5($oDbResult->getFieldValue('starttime').$oDbResult->getFieldValue('endtime').$asEventData['title'].$sDescription);

      if(isset($asEvents[$sEventId]))
      {
        //--------------------------------------------------------
        //event already exists: merge creator/attendees in 1 event
        if($asEvents[$sEventId]['id'] != $nCreatorFk)
          $asAttendees[] = $oLogin->getUserNameFromData($asUsers[$nCreatorFk], true);

        if(!empty($asAttendees))
        {
          if(empty($asEvents[$sEventId]['attendees']))
            $sAttendees = '<span class=\'eventPopupAttend\'>With:</span><div class=\'eventPopupAttend\'>'.implode(', ', $asAttendees).'</div>';
          else
          {
            $asEvents[$sEventId]['attendees'] = strip_tags($asEvents[$sEventId]['attendees']);
            $asEvents[$sEventId]['attendees'] = str_ireplace('With:', '', $asEvents[$sEventId]['attendees']);
            $asAttendees = array_merge($asAttendees, (array)explode(',', $asEvents[$sEventId]['attendees']));

            if(!empty($asAttendees))
            {
              $asAttendees = array_trim($asAttendees, true, true);
              $asEvents[$sEventId]['attendees'] = '<span class=\'eventPopupAttend\'>With: </span><div class=\'eventPopupAttend\'>'.implode(', ', $asAttendees).'</div>';
            }
          }
        }
      }
      else
      {
        if(!empty($asAttendees))
          $sAttendees = '<span class=\'eventPopupAttend\'>With:</span><div class=\'eventPopupAttend\'>'.implode(', ', $asAttendees).'</div>';

        $asEvents[$sEventId] = array(
          'id' => $nCreatorFk,
          'eventId' => $oDbResult->getFieldValue('zimbra_calpk'),
          'title' => $sCleanTitle,
          'htmlTitle' => $sTitle,
          'attendees' => $sAttendees,
          'description' => $sDescription,
          'start' => $oDbResult->getFieldValue('starttime'),
          'end' => $oDbResult->getFieldValue('endtime'),
          'eventTime' => date('H:i', strtotime($oDbResult->getFieldValue('starttime'))).' - '.date('H:i', strtotime($oDbResult->getFieldValue('endtime'))),
          'eventColor' => $this->casColor[$nCreatorFk],
          'url' => '',
          'multiple' => (int)$bMultiple,
          'allDay' => false);
      }

      $bRead = $oDbResult->readNext();
    }

    return json_encode(array('data' => array_values($asEvents), 'msg' => count($asEvents).' events have been found.'/*, 'query' => $sQuery*/));
  }
}

?>