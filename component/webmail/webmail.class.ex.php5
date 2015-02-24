<?php

require_once('component/webmail/webmail.class.php5');

class CWebMailEx extends CWebMail
{

  public function __construct()
  {

    return true;
  }

  public function getDefaultType()
  {
    return CONST_WEBMAIL;
  }

  public function getDefaultAction()
  {
    return CONST_ACTION_ADD;
  }

  public function getPageActions($psAction = '', $psType = '', $pnPk = 0)
  {
    $asActions = array();

    switch ($psType)
    {
      case CONST_WEBMAIL:
        switch ($psAction)
        {
          case CONST_ACTION_LIST:
          break;

          default: break;
        }
      break;
    }

    return $asActions;
  }

  public function getAjax()
  {
    $this->_processUrl();

    switch ($this->csType)
    {
      case CONST_WEBMAIL:
        switch ($this->csAction)
        {

          case CONST_ACTION_ADD:
            return json_encode($this->_getMailForm($this->cnPk));
            break;

          case CONST_ACTION_SAVEADD:
            return json_encode($this->_sendWebMail($this->cnPk));
            break;

        }
        break;
    }
  }

  public function getHtml()
  {
    $this->_processUrl();

    switch ($this->csType)
    {

      case CONST_WEBMAIL:
        switch ($this->csAction)
        {
           default: break;
        }
        break;
      default: break;
    }
  }

  private function _getMailForm($psMailPk)
  {
    if(!assert('is_integer($psMailPk) && !empty($psMailPk)'))
      return array('error' => 'No User found.');

    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath() . 'css/webmail.css'));

    $sItemType = getValue('ppaty');
    $sItemId = (int)getValue('ppaid', 0);

    $asResult = array('data' => $this->_getWebMailForm($psMailPk, $sItemType, $sItemId));
    return $oPage->getAjaxExtraContent($asResult);
  }

  /**
   * Return the HTML code of the emailing form
   * @param integer $psMailPk
   * @param string $psItemType
   * @param ineger $pnItemPk
   * @return string html
   */
  private function _getWebMailForm($pnMailPk, $psItemType = '', $pnItemPk = 0)
  {
    if(!assert('is_integer($pnMailPk) && !empty($pnMailPk)'))
      return '';

    if(!assert('is_integer($pnItemPk)'))
      return '';

    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    /*@var $oDB CDatabaseEx */
    $oDB = CDependency::getComponentByName('database');

    $oAB = CDependency::getComponentByName('addressbook');
    $oABComponent = CDependency::getComponentUidByName('addressbook');

    $oPage->addCssFile(array($this->getResourcePath().'css/webmail.css'));
    $sDisplayName = '';
    $sDisplayEmail = array();

    if($psItemType == CONST_AB_TYPE_COMPANY && !empty($oABComponent))
    {
      $asCompanyData = $oAB->getCompanyByPk($pnItemPk);
      $sTargetTitle = ' to ['.$asCompanyData['company_name'].']';
      $sDisplayEmail = $asCompanyData['email'];
      $bHasEvent = true;
    }
    elseif($psItemType == CONST_AB_TYPE_CONTACT && !empty($oABComponent))
    {
     $asContactData = $oAB->getContactByPk($pnItemPk);

      $sTargetTitle = ' to ['.$asContactData['firstname'].' '.$asContactData['lastname'].']';
      $sDisplayEmail = $asContactData['email'];
      $bHasEvent = true;
    }
    else
    {
      $sTargetTitle = ' to anybody (no event will be created)';
      $sDisplayEmail = '';
      $bHasEvent = false;
    }

    $oLogin = CDependency::getComponentByName('login');
    $asSender = $oLogin->getUserDataByPk($pnMailPk);
    $asSender['emailLabel'] = $oLogin->getUserNameFromData($asSender).'&lt;'.$asSender['email'].'&gt;' ;

    $sHTML = $oHTML->getBlocStart();

    //Start the send mail form
    $oForm = $oHTML->initForm('sendMailForm');
    $sFormId = $oForm->getFormId();

    //Get javascript for the popup
    $sURL = $oPage->getAjaxUrl('webmail', CONST_ACTION_SAVEADD, CONST_WEBMAIL, $pnMailPk);
    $sJs = $oHTML->getAjaxJs($sURL, 'body', $sFormId);
    $oForm->setFormParams('', false, array('action' => '', 'onsubmit' => 'event.preventDefault(); '.$sJs, 'inajax' => 1,'submitLabel' => 'Send'));

    //Close button on popup and remove cancel button
    $oForm->setFormDisplayParams(array('noCancelButton' => '1','noCloseButton' => '1'));
    $sHTML.= $oHTML->getBlocStart();

    $oForm->addField('misc', '', array('type' => 'text', 'text' => '<span class="h4">Send an email '.$sTargetTitle.'</span><hr />'));

    $sHtmlBtn = '<span style="padding-right:20px;"><a href="javascript:;" onclick="$(this).closest(\'form\').submit();"><img src='.$this->getResourcePath().'/pictures/send_button.jpg'.' width="32" height="32""></a></span>';
    $sHtmlBtn.= '<span style="padding-right:20px;"><img src='.$this->getResourcePath().'/pictures/attach_button.jpg'.'  width="32" height="32"></span>';
    $sHtmlBtn.= '<span><a href="javascript:;" onclick="removePopup();"><img src='.$this->getResourcePath().'/pictures/close_button.jpg'.' width="32" height="32"></a></span>';
    $oForm->addField('misc', '', array('type' => 'text', 'text' => $sHtmlBtn));

    if(!empty($asSender['webmail']))
    {
      $oForm->addField('select', 'sender', array('label' => 'From : ', 'id' => 'senderId', 'style' => 'width: 100%;'));
      $oForm->addOption('sender', array('label' => 'default bcm address &lt;crm@bulbouscell.com&gt;', 'value' => 'crm@bulbouscell.com'));
      $oForm->addOption('sender', array('label' => $asSender['emailLabel'], 'value' => $asSender['email'], 'selected' => 'selected'));
    }
    else
    {
      $oForm->addField('input', 'sender', array('label' => 'From : ', 'value' => 'crm@bulbouscell.com','style'=>'width:100%; font-style: italic; color:#666; ', 'readonly' => 'readonly'));
      $oForm->addField('misc', '', array('type' => 'text', 'text' => '<span style="font-size: 10px; font-style: italic; color:#666;">You can configure your own email account in \'My account\' menu.</span>'));
    }

     if($sDisplayEmail)
      $oForm->addField('input', 'receiver', array('label' => 'To', 'value' => $sDisplayEmail,'style'=>'width:100%;'));
     else
     {
      $oForm->addField('input', 'receiver', array('label' => 'To', 'value' => '', 'style'=>'width:100%;'));
      $oForm->setFieldControl('receiver', array('jsFieldNotEmpty' => '', 'jsFieldTypeEmail' => ''));
     }

    $oForm->addField('misc', '', array('type' => 'text', 'text' => 'CC or Bcc', 'onclick' => '$(\'.hiddenField\').fadeToggle(\'fast\');', 'style' => 'cursor: pointer;'));

    $oForm->addField('input', 'receiver_cc', array('value' => '', 'class' => 'hiddenField', 'style' => 'display:none; width:100%;'));
    $oForm->setFieldControl('receiver_cc', array('jsFieldTypeEmail' => ''));

    $oForm->addField('input', 'receiver_bcc', array( 'value' => '', 'class' => 'hiddenField', 'style' => 'display:none; width:100%;'));
    $oForm->setFieldControl('receiver_bcc', array( 'jsFieldTypeEmail' => ''));

    $oForm->addField('input', 'subject', array('label' => 'Subject', 'value' => '','style'=> 'width:100%;'));
    $oForm->setFieldControl('subject', array('jsFieldNotEmpty' => '', 'jsFieldNotEmpty' => ''));

    if(!empty($asSender['signature']))
    {
      $oForm->addField('textarea', 'message', array('label' => 'Message','style'=>'width:683px;height:180px;', 'value' =>' <p></p><p></p><p></p>'. $asSender['signature'], 'isTinymce' => 1));
    }
    else
      $oForm->addField('textarea', 'message', array('label' => 'Message', 'value' => '', 'style'=>'width:683px;height:229px;', 'isTinymce' => 1));

    if($bHasEvent)
      $oForm->addField('checkbox', 'create_event', array('label' => 'Log this email as an event', 'value' => '1', 'checked'=> 'checked'));

    $oForm->setFieldControl('message', array('jsFieldNotEmpty' => ''));

    $oForm->addField('hidden', 'itemtype', array('value' => $psItemType));
    $oForm->addField('hidden', 'itempk', array('value' => $pnItemPk));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return  $sHTML;
  }

  /**
   * Send the email, and create a matching event
   * @param imteger $psMailPk
   * @return array to be json_encoded
   */
  private function _sendWebMail($pnMailPk)
  {
    if(!assert('is_integer($pnMailPk) && !empty($pnMailPk)'))
      return array('error' => 'No User found.');

    /*@var $oDB CDatabaseEx */
    $oDB = CDependency::getComponentByName('database');

    $sItemType = getValue('itemtype', '');
    $nItemPk = (int)getValue('itempk', 0);
    $sSender = getValue('sender');
    $sReceiver = getValue('receiver');
    $sSubject = getValue('subject');
    $sContent = getValue('message');
    $sCC = getValue('receiver_cc');
    $sBCC = getValue('receiver_bcc');
    $nLogEvent = (int)getValue('create_event', 0);

    if(empty($sSender))
      return array('alert' => 'No email address for sender');

    if(!filter_var($sSender, FILTER_VALIDATE_EMAIL))
      return array('alert' => 'Invalid email address for sender');

    if(empty($sReceiver))
      return array('alert' => 'No email address for receiver');

    if(empty($sSubject) )
      return array('alert' => 'No subject found.');

    if(empty($sContent) )
     return array('alert' => 'No Message found');

    /*@var $oMail CMailEx */
    $oMail = CDependency::getComponentByName('mail');
    $oMailComponent = CDependency::getComponentUidByName('mail');
    if(!empty($oMailComponent))
      $nStatus = (int)$oMail->sendRawEmail($sSender, $sReceiver, $sSubject, $sContent, $sCC, $sBCC);
    else
     $nStatus = 0;

    if($nStatus == 1 && $nLogEvent > 0)
    {
      $sABUid = CDependency::getComponentUidByName('addressbook');
      $oEvent = CDependency::getComponentByName('event');
      $oEvent->quickAddEvent('email', 'Email sent', $sContent, $sABUid, $sItemType, CONST_ACTION_VIEW, $nItemPk);
    }

    $sDate = date('Y-m-d');

    $sQuery = 'INSERT INTO webmail (loginfk, subject, content,status,date_sent) ';
    $sQuery.= ' VALUES('.$pnMailPk.', ';
    $sQuery.= ''.$oDB->dbEscapeString($sSubject).',';
    $sQuery.= ''.$oDB->dbEscapeString($sContent).', ';
    $sQuery.= ''.$nStatus.', ';
    $sQuery.= '"'.$sDate.'"';
    $sQuery.= ') ';

    $oResult = $oDB->ExecuteQuery($sQuery);

    $this->_getRecipentInfo($oResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT),$pnMailPk, $sReceiver,'To');

    if($sCC)
      $this->_getRecipentInfo($oResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT),$pnMailPk, $sCC,'CC');

    if($sBCC)
      $this->_getRecipentInfo($oResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT),$pnMailPk, $sBCC,'BCC');

    return array('notice' => 'Your mail has been sent successfully.', 'action' => 'removePopup();', 'reload' => 1);
  }

   private function _getRecipentInfo($pnWebmailfk, $pnMailPk, $psRecipientList, $psType)
   {
     if(!assert('is_integer($pnWebmailfk) && is_integer($pnMailPk)'))
       return false;

     if(empty($pnWebmailfk) || empty($pnMailPk) || empty($psRecipientList) || empty($psType))
       return false;

     /*@var $oDB CDatabaseEx */
     $oDB = CDependency::getComponentByName('database');
     $sIndividual = explode(",", $psRecipientList);
     $sDate = date('Y-m-d');

     for($i=0;$i<sizeof($sIndividual);$i++)
     {
      $sQuery = 'INSERT INTO webmail_recipent (webmailfk,loginfk, email, type,date_sent) ';
      $sQuery.= ' VALUES('.$pnWebmailfk.', ';
      $sQuery.= ''.$pnMailPk.', ';
      $sQuery.= ''.$oDB->dbEscapeString($sIndividual[$i]).',';
      $sQuery.= ''.$oDB->dbEscapeString($psType).',';
      $sQuery.= '"'.$sDate.'"';
      $sQuery.= ') ';

      $oDB->ExecuteQuery($sQuery);
      }

      return true;
    }

  // Return url for other components

  public function getURL($psComponent, $psAction = '', $psType = '', $pnLoginPk = 0,$pasOptions = array())
  {
    /*@var $oLogin CLoginEx */
    $oLogin = CDependency::getComponentByName('login');
    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $pnLoginPk =$oLogin->getUserPk();

    $sURL = $oPage->getUrl($psComponent, $psAction, $psType, $pnLoginPk, $pasOptions);
    $sURL.= '&'.CONST_URL_MODE.'='.CONST_URL_PARAM_PAGE_AJAX;
    return $sURL;

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