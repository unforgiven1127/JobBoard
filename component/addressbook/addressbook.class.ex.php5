<?php
require_once('component/addressbook/addressbook.class.php5');

class CAddressbookEx extends CAddressbook
{
  public function __construct()
  {
    return true;
  }

  public function getDefaultType()
  {
    return CONST_AB_TYPE_CONTACT;
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
    $oRight = CDependency::getComponentByName('right');

    $asActions = array();
    $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_DELETE,$this->getType(),0);

    switch($this->csType)
    {
      case CONST_AB_TYPE_CONTACT:

        /*@var $oPage CPageEx */
        $oPage = CDependency::getComponentByName('page');
        $sPictureMenuPath = $this->getResourcePath().'/pictures/menu/';

        switch($this->csAction)
        {
          case CONST_ACTION_VIEW:
            if(!empty($pnPk))
            {
              $asActions['ppal'][] = array('picture' => $sPictureMenuPath.'ct_list_32.png','title'=>'Back to the connection list', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_AB_TYPE_CONTACT));
              $asActions['ppaa'][] = array('picture' => $sPictureMenuPath.'ct_add_32.png','title'=>'Add connection', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_ADD, CONST_AB_TYPE_CONTACT));
              $asActions['ppae'][] = array('picture' => $sPictureMenuPath.'ct_edit_32.png','title'=>'Edit this connection', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_EDIT, CONST_AB_TYPE_CONTACT,$pnPk));
              if($sAccess)
              $asActions['ppad'][] = array('picture' => $sPictureMenuPath.'ct_delete_32.png','title'=>'Delete connection', 'url' => $oPage->getAjaxUrl($this->_getUid(), CONST_ACTION_DELETE, CONST_AB_TYPE_CONTACT, $pnPk), 'option' => array('onclick' => "if(!window.confirm('You are about to permanently delete this connection with all its linked data. \\nDo you really want to proceed ?')){ return false; }"));
            }
            break;

            case CONST_ACTION_LIST:
              $asActions['ppal'][] = array('picture' => $sPictureMenuPath.'ct_list_32.png','title'=>'Connections List', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_AB_TYPE_CONTACT));
              $asActions['ppaa'][] = array('picture' => $sPictureMenuPath.'ct_add_32.png','title'=>'Add Connection', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_ADD, CONST_AB_TYPE_CONTACT));

              $sSearchId = getValue('searchId');
              $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_LIST, CONST_AB_TYPE_CONTACT, 0, array('searchId' => $sSearchId, 'sortfield' => 'id'));
              $asActions['ppaasort'][] = array('picture' => $sPictureMenuPath.'list_sort_desc_32.png','title'=>'Sort by date', 'url' => 'javascript:;', 'option' => array('onclick'=> ' AjaxRequest(\''.$sURL.'\', \'body\', \'\', \'contactListContainer\'); '));
              break;

            case CONST_ACTION_ADD:
              $asActions['ppal'][] = array('picture' => $sPictureMenuPath.'ct_list_32.png','title'=>'Connections List', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_AB_TYPE_CONTACT));
              break;

            case CONST_ACTION_EDIT:
              $asActions['ppal'][] = array('picture' => $sPictureMenuPath.'ct_list_32.png','title'=>'Connections List', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_AB_TYPE_CONTACT));
              if(!empty($pnPk))
              {
                $asActions['ppav'][] = array('picture' => $sPictureMenuPath.'ct_view_32.png','title'=>'View connection', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_AB_TYPE_CONTACT));
              }
              break;

            default: break;
        }
      break;

      case CONST_AB_TYPE_COMPANY:

        /*@var $oPage CPageEx */
        $oPage = CDependency::getComponentByName('page');
        $sPictureMenuPath = $this->getResourcePath().'/pictures/menu/';

        //always displayed: list, add
        $asActions['ppal'][] = array('picture' => $sPictureMenuPath.'cp_list_32.png', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_AB_TYPE_COMPANY),'title'=>'Back to the company list');
        $asActions['ppaa'][] = array('picture' => $sPictureMenuPath.'cp_add_32.png', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_ADD, CONST_AB_TYPE_COMPANY),'title'=>'Add company');

        switch($this->csAction)
        {
          case CONST_ACTION_VIEW:
            if(!empty($pnPk))
            {
              if($sAccess)
              $asActions['ppad'][] = array('picture' => $sPictureMenuPath.'cp_delete_32.png', 'url' => $oPage->getAjaxUrl($this->_getUid(), CONST_ACTION_DELETE, CONST_AB_TYPE_COMPANY, $pnPk ),'title'=>'Delete company','option' => array('onclick' => 'if(!window.confirm(\'Delete this company ?\')){ return false; }'));
              $asActions['ppae'][] = array('picture' => $sPictureMenuPath.'cp_edit_32.png', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_EDIT, CONST_AB_TYPE_COMPANY,$pnPk),'title'=>'Edit this company');
              $asActions['ppaa'][] = array('picture' => $sPictureMenuPath.'ct_add_32.png', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_ADD, CONST_AB_TYPE_CONTACT, 0, array('cppk' => $pnPk)),'title'=>'Add a connection to this company');
            }
            break;

          case CONST_ACTION_LIST:

            $sSearchId = getValue('searchId');
            $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_LIST, CONST_AB_TYPE_COMPANY, 0, array('searchId' => $sSearchId, 'sortfield' => 'id'));
            $asActions['ppaasort'][] = array('picture' => $sPictureMenuPath.'list_sort_desc_32.png','title'=>'Sort by date', 'url' => 'javascript:;', 'option' => array('onclick'=> ' AjaxRequest(\''.$sURL.'\', \'body\', \'\', \'contactListContainer\'); '));
            break;
        }
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
      case CONST_AB_TYPE_CONTACT:

        switch($this->csAction)
        {
          case CONST_ACTION_SEARCH:
            /* custom json encoding in function for token input selector */
            return $this->_getSelectorContact();
            break;

          case CONST_ACTION_SAVEADD:
          case CONST_ACTION_SAVEEDIT:
           return json_encode($this->_getContactFormSaveAdd($this->cnPk));
            break;

          case CONST_ACTION_DELETE:
           return json_encode($this->_getContactDelete($this->cnPk));
            break;

          case CONST_ACTION_TRANSFER:
           return json_encode($this->_getContactTransfer($this->cnPk));
            break;

          case CONST_ACTION_SAVETRANSFER:
           return json_encode($this->_getContactTransferSave($this->cnPk));
            break;

          case CONST_ACTION_SAVEMANAGE:
            return json_encode($this->_getContactManageSave($this->cnPk));
              break;

          case CONST_ACTION_LIST:
            return json_encode($this->_getAjaxContactSearchResult());
              break;
          }
        break;

      case CONST_AB_TYPE_DOCUMENT:

      switch($this->csAction)
      {
        case CONST_ACTION_EDIT:
        case CONST_ACTION_ADD:
          return json_encode($this->_getDocumentForm($this->cnPk));
           break;

          case CONST_ACTION_DELETE:
           return json_encode($this->_getDocumentDelete($this->cnPk));
            break;

        default:
        break;
      }

      case CONST_AB_TYPE_COMPANY:
        switch($this->csAction)
        {
          case CONST_ACTION_SEARCH:
            /* custom json encoding in function for token input selector */
            return $this->_getSelectorCompany();
             break;

          case CONST_ACTION_SAVEADD:
            return json_encode($this->_getCompanySave($this->cnPk));
             break;

          case CONST_ACTION_SAVEEDIT:
            return json_encode($this->_getCompanyContactSave($this->cnPk));
             break;

          case CONST_ACTION_DELETE:
            return json_encode($this->_getCompanyDelete($this->cnPk));
            break;

          case CONST_ACTION_MANAGE:
            return json_encode($this->_getLinkCompanyContact($this->cnPk));
             break;

          case CONST_ACTION_TRANSFER:
            return json_encode($this->_getCompanyTransfer($this->cnPk));
             break;

          case CONST_ACTION_SAVETRANSFER:
            return json_encode($this->_getCompanyTransferSave($this->cnPk));
             break;

           case CONST_ACTION_LIST:
            return json_encode($this->_getAjaxCompanySearchResult());
              break;
         }

    case CONST_AB_TYPE_COMPANY_RELATION:
       switch($this->csAction)
      {
        case CONST_ACTION_SAVEADD:
          return json_encode($this->_getSaveCompanyRelation($this->cnPk));
           break;

        case CONST_ACTION_SAVEEDIT:
          return json_encode($this->_getSaveCompanyRelation($this->cnPk));
           break;

       case CONST_ACTION_DELETE:
         return json_encode($this->_getDeleteProfile($this->cnPk));
          break;
        }
      break;
      }
    }

    private function _getQuerybuilderFields()
    {
      $asQbFields = array();
      $asQbFields[] = array('label' => 'Firstname', 'name' => 'firstname',  'type' => 'string', 'controls' => array('jsFieldMinSize@3'), 'default' => '');
      $asQbFields[] = array('label' => 'Lastname', 'name' => 'lastname',  'type' => 'string', 'controls' => array('jsFieldMinSize@3'), 'default' => '');
      $asQbFields[] = array('label' => '\Gender', 'name' => 'courtesy',  'type' => 'select', 'controls' => array('jsFieldMinSize@3'), 'options' => array( array('label' => 'Mr', 'value' => 'mr'),  array('label' => 'Ms', 'value' => 'ms')) , 'default' => 'ms');

      return $asQbFields;
    }

  //====================================================================
  //  public methods
  //====================================================================

  public function getHtml()
  {
    $this->_processUrl();

    switch($this->csType)
    {
      case CONST_AB_TYPE_CONTACT:

        switch($this->csAction)
        {
          case CONST_ACTION_VIEW:
            return $this->_getContactView($this->cnPk);
             break;

          case CONST_ACTION_ADD:
          case CONST_ACTION_EDIT:
            return $this->_getContactForm($this->cnPk);
             break;

          case CONST_ACTION_MANAGE:
            return $this->_getNewContactForm($this->cnPk);
             break;

         case CONST_ACTION_SEND:
           return $this->_getDocumentSend($this->cnPk);
            break;

          default:
          case CONST_ACTION_LIST:
            return $this->_getContactList();
             break;
        }
        break;

      case CONST_AB_TYPE_DOCUMENT:

      switch($this->csAction)
      {
       case CONST_ACTION_SAVEADD:
         return $this->_getDocumentSave($this->cnPk);
          break;

    default:
      break;

    }
      case CONST_AB_TYPE_COMPANY:

        switch($this->csAction)
        {
          case CONST_ACTION_VIEW:
           return $this->_getCompanyView($this->cnPk);
            break;

          case CONST_ACTION_ADD:
            return $this->_getCompanyForm(0);
             break;

          case CONST_ACTION_EDIT:
           return $this->_getCompanyForm($this->cnPk);
            break;

          case CONST_ACTION_SAVEEDIT:
           return $this->_getCompanySave($this->cnPk);
            break;

         case CONST_ACTION_SEND:
          return $this->_getDocumentSend($this->cnPk);
           break;

          default:
          case CONST_ACTION_LIST:
           return $this->_getCompanyList();
            break;
        }

      case CONST_AB_TYPE_COMPANY_RELATION:
       switch($this->csAction)
      {
       case CONST_ACTION_ADD:
       case CONST_ACTION_EDIT:
        return $this->_getLinkCompanyForm($this->cnPk);
         break;

       }
        break;
    }
  }

  /**
   * Save the contact relation with company
   * @param int $pnContactPk
   * @return array message
   */

  private function _getSaveCompanyRelation($pnContactPk)
  {
    if(!assert('is_integer($pnContactPk) && !empty($pnContactPk)'))
      return array();

    $pnCompanyfk= getValue('parent');
    $pnCityfk= getValue('cityfk');
    $pnCountryfk= getValue('countryfk');
    $sEmail = getValue('email');
    $sPhone = getValue('phone');
    $sFax = getValue('fax');
    $sAddress = getValue('address');
    $sPostcode = getValue('postcode');
    $sPosition = getValue('position');
    $sDepartment = getValue('department');

    if(empty($sEmail) && empty($sPhone) && empty($sFax) && empty($sAddress) && empty($pnCompanyfk) && empty($sPosition))
      return array('alert' => 'You have to input at least one value');

    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'css/addressbook.css'));

    $nProfilePk = getValue('profilePk');

    if($nProfilePk)
    {
      $sQuery = 'UPDATE profil SET email ='.$oDB->dbEscapeString($sEmail).',phone='.$oDB->dbEscapeString($sPhone).',fax='.$oDB->dbEscapeString($sFax).',';
      $sQuery.= 'address_1 = '.$oDB->dbEscapeString($sAddress).',postcode = '.$oDB->dbEscapeString($sPostcode).',cityfk='.$oDB->dbEscapeString($pnCityfk).',department='.$oDB->dbEscapeString($sDepartment).',';
      $sQuery.= 'countryfk='.$oDB->dbEscapeString($pnCountryfk).',companyfk='.$oDB->dbEscapeString($pnCompanyfk).',position='.$oDB->dbEscapeString($sPosition).' WHERE profilpk ='.$nProfilePk;

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      if(!$oDbResult)
        return array();
    }
    else
    {
      $sQuery = 'INSERT INTO profil (contactfk,companyfk,email,phone,fax,address_1,postcode,cityfk,countryfk,position,department)';
      $sQuery.= 'VALUES("'.$pnContactPk.'","'.$pnCompanyfk.'",'.$oDB->dbEscapeString($sEmail).','.$oDB->dbEscapeString($sPhone).','.$oDB->dbEscapeString($sFax).','.$oDB->dbEscapeString($sAddress).','.$oDB->dbEscapeString($sPostcode).','.$oDB->dbEscapeString($pnCityfk).','.$oDB->dbEscapeString($pnCountryfk).','.$oDB->dbEscapeString($sPosition).','.$oDB->dbEscapeString($sDepartment).')';

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      if(!$oDbResult)
        return array();
    }
    $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $pnContactPk);

    return (array('notice'=>'Profile Information has been updated.', 'timedUrl' => $sURL));
  }

  /**
   * Update the account manager of contact
   * @param integer $pnContactPk
   * @return array
   */

  private function _getContactTransferSave($pnContactPk)
  {
   $asFollowers  = getValue('account_manager');
   $pnNewFollowerFk = (int)$asFollowers[0];

   if(!assert('is_integer($pnContactPk) && !empty($pnContactPk) && is_integer($pnNewFollowerFk) && !empty($pnNewFollowerFk)' ))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');

    $sQuery = 'UPDATE contact SET followerfk = '.$oDB->dbEscapeString($pnNewFollowerFk).', date_update = '.$oDB->dbEscapeString(date('Y-m-d H:i:s')).' ';
    $sQuery.= ' WHERE contactpk = '.$pnContactPk;

    $oDbResult = $oDB->ExecuteQuery($sQuery);

    array_shift($asFollowers);

    $sQuery = 'DELETE FROM account_manager WHERE contactfk='.$pnContactPk;
    $oDB->ExecuteQuery($sQuery);

    foreach($asFollowers as $asManagerData)
    {
       $sQuery = 'INSERT INTO account_manager(contactfk,loginfk) VALUES('.$pnContactPk.','.$asManagerData.')';
       $oDB->ExecuteQuery($sQuery);
    }

    if(!$oDbResult)
      return array();

    $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $pnContactPk);
    return (array('notice'=>'Account Manager has been changed', 'timedUrl' => $sURL));
  }

   /**
   * Update the account manager of company
   * @param integer $pnCompanyPk
   * @return array
   */

  private function _getCompanyTransferSave($pnCompanyPk)
  {
    $asFollowers = getValue('account_manager');
    $sCheckBox = getValue('cascading');
    $pnNewFollowerFk = (int)$asFollowers[0];

    if(!assert('is_integer($pnCompanyPk) && !empty($pnCompanyPk) && is_integer($pnNewFollowerFk) && !empty($pnNewFollowerFk)' ))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');

    $sQuery = 'UPDATE company SET followerfk = '.$oDB->dbEscapeString($pnNewFollowerFk).', date_update = '.$oDB->dbEscapeString(date('Y-m-d H:i:s')).' ';
    $sQuery.= ' WHERE companypk = '.$pnCompanyPk;

    $oDB->ExecuteQuery($sQuery);

    array_shift($asFollowers);

    $sQuery = 'DELETE FROM account_manager WHERE companyfk='.$pnCompanyPk;
    $oDB->ExecuteQuery($sQuery);

    foreach($asFollowers as $asManagerData)
    {
       $sQuery = 'INSERT INTO account_manager(companyfk,loginfk) VALUES('.$pnCompanyPk.','.$asManagerData.')';
       $oDB->ExecuteQuery($sQuery);
    }

   if($sCheckBox)
   {
    $asQuery = 'SELECT p.contactfk AS contactfk FROM profil p,contact c  WHERE p.contactfk = c.contactpk AND p.companyfk='.$pnCompanyPk;
    $oResult = $oDB->ExecuteQuery($asQuery);
    $bRead = $oResult->readFirst();
    while($bRead)
    {
      $arsQuery = 'UPDATE contact SET followerfk = '.$oDB->dbEscapeString($pnNewFollowerFk).', date_update = '.$oDB->dbEscapeString(date('Y-m-d H:i:s')).' ';
      $arsQuery.= ' WHERE contactpk = '.$oResult->getFieldValue('contactfk');

       $oDB->ExecuteQuery($arsQuery);
       $bRead = $oResult->readNext();
    }
   }
    $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $pnCompanyPk);
    return (array('notice'=>'Account Manager has been changed.','timedUrl'=>$sURL));

  }
  /**
   * Search for the keyword
   * @param string $psSearchWord
   * @return array
   */

  public function search($psSearchWord)
  {
    if(!assert('!empty($psSearchWord)'))
      return array();

    if(strlen($psSearchWord) < 3)
      return array('Search query is too short.');


    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');

    //===============================================================
    //Company & contact PK search first (exclusive search)
    if(preg_match('/^(cp_[0-9]{1,6})|(cp[0-9]{1,6})/i', $psSearchWord))
    {
      $sWord = (int)preg_replace('/[^0-9]/', '', $psSearchWord);

      $sQuery = 'SELECT * FROM company as cp WHERE (companypk = '.$oDB->dbEscapeString($sWord).') ';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->ReadFirst();
      if($bRead)
      {
        $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $sWord);
        return array('url' => $sURL);
      }
      else
        return array('notice' => 'No company matches this id.');
    }
    if(preg_match('/^(ct_[0-9]{1,6})|(ct[0-9]{1,6})/i', $psSearchWord))
    {
      $sWord = (int)preg_replace('/[^0-9]/', '', $psSearchWord);

      $sQuery = 'SELECT * FROM contact as ct WHERE (contactpk = '.$oDB->dbEscapeString($sWord).') ';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->ReadFirst();
      if($bRead)
      {
        $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $sWord);
        return array('url' => $sURL);
      }
      else
        return array('notice' => 'No contact matches this id.');
    }

    //===========================================================
    //Non exclusive cp and ct searches
    $asQueryFilter = array();

    if(preg_match('/^[0-9.+ ]{3,}$/i', $psSearchWord))
    {
      $sWord = preg_replace('/[^0-9]/', '', $psSearchWord);
      $sWord = $oDB->dbEscapeString('%'.$sWord.'%');
      $asQueryFilter['cp'] = 'WHERE (phone LIKE '.$sWord.' OR fax LIKE '.$sWord.')';
    }
    elseif(isValidEmail($psSearchWord))
    {
      $asQueryFilter['cp'] = 'WHERE (email LIKE '.$oDB->dbEscapeString('%'.$psSearchWord.'%').')';
    }
    else
    {
      $asWords = explode(' ', $psSearchWord);
      $asWhere = array();
      foreach($asWords as $sWord)
      {
        $sWord = $oDB->dbEscapeString('%'.$sWord.'%');
        $asWhere[] = '(company_name LIKE '.$sWord.' OR corporate_name LIKE '.$sWord.' OR address_1 LIKE '.$sWord.' OR address_2 LIKE '.$sWord.' OR postcode LIKE '.$sWord.')';
      }

      $asQueryFilter['cp'] = 'WHERE ('.implode('OR', $asWhere).')';
    }

    //===============================================================
    //Contact search
    $nNbResult = 0;
    $asResult = array();

    if(preg_match('/^[0-9.+ ]{3,}$/i', $psSearchWord))
    {
      $sWord = preg_replace('/[^0-9]/', '', $psSearchWord);
      $sWord = $oDB->dbEscapeString('%'.$sWord.'%');
      $asQueryFilter['ct'] = 'WHERE (phone LIKE '.$sWord.' OR fax LIKE '.$sWord.')';
    }
    elseif(isValidEmail($psSearchWord))
    {
      $asQueryFilter['ct'] = 'WHERE (email LIKE '.$oDB->dbEscapeString('%'.$psSearchWord.'%').')';
    }
    else
    {
      $asWords = explode(' ', $psSearchWord);
      $asWhere = array();
      foreach($asWords as $sWord)
      {
        $sWord = $oDB->dbEscapeString('%'.$sWord.'%');
        $asWhere[] = '(firstname LIKE '.$sWord.' OR lastname LIKE '.$sWord.' OR address_1 LIKE '.$sWord.' OR address_2 LIKE '.$sWord.' OR postcode LIKE '.$sWord.')';
      }

      $asQueryFilter['ct'] = 'WHERE ('.implode('OR', $asWhere).')';
    }

    if(isset($asQueryFilter['cp']))
    {
      $asSearch = $this->_getSearchCompanyList($asQueryFilter['cp']);
      if($asSearch['nb'] > 0)
      {
        $nNbResult+= $asSearch['nb'];
        $asResult[] = $asSearch['data'];
      }
    }


    if(isset($asQueryFilter['ct']))
    {
      $asSearch = $this->_getSearchContactList($asQueryFilter['ct']);
      if($asSearch['nb'] > 0)
      {
        $nNbResult+= $asSearch['nb'];
        $asResult[] = $asSearch['data'];
      }
    }

    return array('nb' => $nNbResult, 'data' => implode(' ', $asResult));
  }

  /**
   * Search for company
   * @param string $psFilter
   * @return array
   */

  private function _getSearchCompanyList($psFilter)
  {
    if(!assert('is_string($psFilter) && !empty($psFilter)'))
      return array();

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT count(*) as nCount FROM company as cp '.$psFilter.' ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->ReadFirst();
    if(!$bRead)
      return array('nb' => 0, 'data' => '');

    $nResult = $oDbResult->getFieldValue('nCount', CONST_PHP_VARTYPE_INT);
    if($nResult == 0)
      return array('nb' => 0, 'data' => '');

    return array('nb' => $nResult, 'data' => $this->_getCompanyList($psFilter));
  }

   /**
   * Search for contacts
   * @param string $psFilter
   * @return array
   */

  private function _getSearchContactList($psFilter)
  {
    if(!assert('is_string($psFilter) && !empty($psFilter)'))
     return array();

    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');

    $sQuery = 'SELECT count(*) as nCount FROM contact as ct '.$psFilter.' ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->ReadFirst();
    if(!$bRead)
      return array('nb' => 0, 'data' => '');

    $nResult = $oDbResult->getFieldValue('nCount', CONST_PHP_VARTYPE_INT);
    if($nResult == 0)
      return array('nb' => 0, 'data' => '');

    return array('nb' => $nResult, 'data' => $this->_getContactList($psFilter));
  }

  /**
   * Display the link company form
   * @param integer $pnPk
   * @return HTML structure
   */

  private function _getLinkCompanyForm($pnLoginPk)
  {
     if(!assert('is_integer($pnLoginPk) && !empty($pnLoginPk)' ))
      return '';

     $nProfilePk = getValue('profileId');

    /* @var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    /* @var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'css/addressbook.css'));

    if(!empty($nProfilePk))
    {
      $sQuery= 'SELECT * FROM profil WHERE profilpk ='.$nProfilePk;
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
     }
     else
       $oDbResult = new CDbResult();

     if(isset($nProfilePk))
      $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SAVEEDIT, CONST_AB_TYPE_COMPANY_RELATION, $pnLoginPk,array('profilePk'=>$nProfilePk));
     else
      $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SAVEADD, CONST_AB_TYPE_COMPANY_RELATION, $pnLoginPk);

    $sHTML= $oHTML->getBlocStart();
    //div including the form
    $sHTML.= $oHTML->getBlocStart('');
    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('ctLinkForm');
    $sFormId = $oForm->getFormId();
    $oForm->setFormParams('', true, array('submitLabel' => 'Save','action' => $sURL));
    $oForm->addField('misc', '', array('type' => 'text','text'=> '<span class="h4"> Add Profile details </span><br />'));

    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SEARCH, CONST_AB_TYPE_COMPANY);
    $oForm->addField('selector', 'parent', array('label'=> 'Company', 'url' => $sURL));
    $oForm->setFieldControl('parent', array('jsFieldTypeIntegerPositive' => ''));

    if($oDbResult->getFieldvalue('companyfk'))
    {
      $asCompany = $this->getCompanyByPk((int)$oDbResult->getFieldvalue('companyfk'));
      $oForm->addOption('parent', array('label' => $asCompany['company_name'], 'value' => $oDbResult->getFieldvalue('countryfk')));
    }

    $oForm->addField('input', 'position', array('label'=> 'Position', 'value' => $oDbResult->getFieldvalue('position')));
    $oForm->setFieldControl('position', array('jsFieldMinSize' =>2));
    $oForm->addField('input', 'email', array('label'=> 'Email Address', 'value' => $oDbResult->getFieldvalue('email')));
    $oForm->setFieldControl('email', array('jsFieldTypeEmail' => '','jsFieldMaxSize' => 255));
    $oForm->addField('input', 'phone', array('label'=> 'Phone', 'value' => $oDbResult->getFieldvalue('phone')));
    $oForm->setFieldControl('phone', array('jsFieldMinSize' => 8));
    $oForm->addField('input', 'fax', array('label'=> 'Fax', 'value' => $oDbResult->getFieldvalue('fax')));
    $oForm->setFieldControl('fax', array('jsFieldMinSize' => 8));
    $oForm->addField('textarea', 'address', array('label'=> 'Address ', 'value' =>$oDbResult->getFieldvalue('address_1')));
    $oForm->setFieldControl('address', array('jsFieldMinSize' => 8));
    $oForm->addField('input', 'postcode', array('label'=> 'Postcode', 'value' => $oDbResult->getFieldvalue('postcode')));
    $oForm->setFieldControl('postcode', array('jsFieldTypeIntegerPositive' => '', 'jsFieldMaxSize' => 12));
    $oForm->addField('input', 'department', array('label'=> 'Department', 'value' => $oDbResult->getFieldvalue('department')));
    $oForm->setFieldControl('department', array('jsFieldMaxSize' => 255));
    $oForm->addField('selector_city', 'cityfk', array('label'=> 'City', 'url' => CONST_FORM_SELECTOR_URL_CITY));
    $oForm->setFieldControl('cityfk', array('jsFieldTypeIntegerPositive' => ''));

    if($oDbResult->getFieldvalue('cityfk'))
    {
      $asCity = $oForm->getCityData((int)$oDbResult->getFieldvalue('cityfk'));
      $oForm->addOption('cityfk', array('label' => $asCity['name_full'].$asCity['name_kanji'], 'value' => $oDbResult->getFieldvalue('cityfk')));
    }

    $oForm->addField('selector_country', 'countryfk', array('label'=> 'Country', 'url' => CONST_FORM_SELECTOR_URL_COUNTRY));
    $oForm->setFieldControl('countryfk', array('jsFieldTypeIntegerPositive' => ''));
    if($oDbResult->getFieldvalue('countryfk'))
    {
      $asCountry = $oForm->getCountryData((int)$oDbResult->getFieldvalue('countryfk'));
      $oForm->addOption('countryfk', array('label' => $asCountry['country_name'], 'value' => $oDbResult->getFieldvalue('countryfk')));
    }

    $oForm->addField('misc', '', array('type'=> 'br'));
    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  public function getDepartment()
  {
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM department ORDER BY department_name desc';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    while($bRead)
    {
      $asDepartment[] = $oResult->getData();
      $bRead = $oResult->ReadNext();
    }
    return $asDepartment;
  }

  /**
   * Save the company contacts
   * @param integer $psPnPk
   * @return string
   */

  private function _getCompanyContactSave($psPnPk)
  {
    $oHTML = CDependency::getComponentByName('display');

    if(!assert('is_integer($psPnPk) && !empty($psPnPk)'))
     return array('error'=>__LINE__.' - Can not connect the user');

    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');

    $nContact = (int)getValue('contactfk');
    $sPosition = getValue('position');
    $sEmail = getValue('email');
    $sPhone = getValue('phone');
    $sFax = getValue('fax');
    $sAddress1 = getValue('address');
    $sPostcode = getValue('postcode');
    $nCountryfk = (int)getValue('countryfk', 0);
    $nCityfk = (int)getValue('cityfk', 0);

    if(empty($nContact))
     return array('alert'=>'Connection has not been added');

    $sQuery = 'INSERT INTO `profil` (`contactfk` ,`companyfk` ,`position` ,`email` ,`phone` ,`fax`,`address_1` ,`postcode` ,`cityfk` ,`countryfk`) ';
    $sQuery.= ' VALUES('.$oDB->dbEscapeString($nContact).','.$oDB->dbEscapeString($psPnPk).','.$oDB->dbEscapeString($sPosition).', '.$oDB->dbEscapeString($sEmail).','.$oDB->dbEscapeString($sPhone).', '.$oDB->dbEscapeString($sFax).',';
    $sQuery.= $oDB->dbEscapeString($sAddress1).', '.$oDB->dbEscapeString($sPostcode).', '.$oDB->dbEscapeString($nCityfk).', ';
    $sQuery.= $oDB->dbEscapeString($nCountryfk).')';

    $oResult = $oDB->ExecuteQuery($sQuery);
    if(!$oResult)
          return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t connect the connection now.');

     $sUrl = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY,$psPnPk);

    return array('notice' => 'Connection has been added', 'action' => 'removePopup();', 'reload' => 1);
   }

/**
 * Function to save the document uploaded
 * @param integer $psPnPk
 * @return URL redirection
 */

  private function _getDocumentSave($psPnPk)
  {
    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');

    if(!assert('is_integer($psPnPk) && !empty($psPnPk)'))
      return $oHTML->getErrorMessage(__LINE__.' - Can not upload the document, data mising');

    $sTitle = getValue('title');
    $sDescription = getValue('description');
    $sType = getValue('ppaty');
    $nDocumentfk = getValue('documentfk');

    if(empty($sTitle))
      return array('alert' => 'No title given');

    $oLogin = CDependency::getComponentByName('login');
    $oDB = CDependency::getComponentByName('database');

    $nCreatorPk = $oLogin->getUserPk();

    if(empty($nDocumentfk))
    {
      //Checking the file upload
    if(!isset($_FILES) || !isset($_FILES['attachment']) || !isset($_FILES['attachment']['tmp_name']))
      return $oHTML->getErrorMessage(__LINE__.' - File can not be uploaded.');

     //Ready for multiple files
      foreach($_FILES['attachment']['tmp_name'] as $nKey => $sTmpFileName)
      {
        $sFileName = $_FILES['attachment']['name'][$nKey];

         if(filesize($sTmpFileName) > (40*1024*1024))
           return $oHTML->getErrorMessage(__LINE__.' - The file is too big to upload.');

        $sDate = $oDB->dbEscapeString(date('Y-m-d H:i:s'));

        $sQuery = 'INSERT INTO `addressbook_document` (`title`, `description` ,`loginfk` ,`filename` ,`path_name`, `date_create`) ';
        $sQuery.= 'VALUES ('.$oDB->dbEscapeString($sTitle).', '.$oDB->dbEscapeString($sDescription).','.$oDB->dbEscapeString($nCreatorPk).',';
        $sQuery.= $oDB->dbEscapeString($sFileName).', '.$oDB->dbEscapeString('').','.$sDate.') ';

        $oResult = $oDB->ExecuteQuery($sQuery);
        if(!$oResult)
          return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save the uploaded file.');

        $nDocPk = $oResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT);

        $sQuery = 'INSERT INTO `addressbook_document_info` (`type`, `itemfk` ,`docfk` ) ';
        $sQuery.= 'VALUES ('.$oDB->dbEscapeString($sType).','.$oDB->dbEscapeString($psPnPk).','.$nDocPk.') ';
        $oResult = $oDB->ExecuteQuery($sQuery);

        $sNewPath = $_SERVER['DOCUMENT_ROOT'].CONST_PATH_UPLOAD_DIR.'addressbook/document/'.$nDocPk.'/';
        $sNewName = date('YmdHis').'_'.$oLogin->getUserPk().'_'.uniqid('doc'.$nDocPk.'_').'_'.$sFileName;

        if(!is_dir($sNewPath) && !makePath($sNewPath))
          return $oHTML->getErrorMessage(__LINE__.' - Destination folder doesn\'t exist.('.$sNewPath.')');

        if(!is_writable($sNewPath))
          return $oHTML->getErrorMessage(__LINE__.' - Can\'t write in the destination folder.');

        if(!move_uploaded_file($sTmpFileName, $sNewPath.$sNewName))
          return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t move the uploaded file.');

        $sQuery = 'UPDATE `addressbook_document` SET `path_name` = '.$oDB->dbEscapeString($sNewPath.$sNewName).' WHERE addressbook_documentpk = '.$nDocPk;
        $oResult = $oDB->ExecuteQuery($sQuery);
        if(!$oResult)
          return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save the uploaded file.');

        }

      }
      else
      {
         $sQuery = 'UPDATE `addressbook_document` SET `title` = '.$oDB->dbEscapeString($sTitle).', `description` = '.$oDB->dbEscapeString($sDescription).' ';
         $sQuery.= ' WHERE addressbook_documentpk = '.$nDocumentfk;

         $oResult = $oDB->ExecuteQuery($sQuery);
         if(!$oResult)
          return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save the uploaded file.');

      }
      //Redirect to the detail page

        $sUrl = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, $sType,$psPnPk,'',$sType.'_tab_documentId');
        return $oHTML->getRedirection($sUrl, 1500);
    }

   /**
    * Function to delete the document
    * @param type $pnPk
    * @return type
    */

   private function _getDocumentDelete($pnPk = 0)
   {
      if(!assert('is_integer($pnPk) && !empty($pnPk)'))
        return array('error'=>'No document found. It might have been removed already');

    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM addressbook_document as ad INNER JOIN addressbook_document_info as adi ON (adi.docfk = ad.addressbook_documentpk) WHERE addressbook_documentpk = '.$pnPk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
      return array('error' => __LINE__.' ERROR: no file found');

    $sType = $oResult->getFieldValue('type');
    $psPnPk = $oResult->getFieldValue('itemfk');

    //DELETE Attached file
    //Recreate the path to be sure there's no crazy delete
    $sAttchFolderPath = $_SERVER['DOCUMENT_ROOT'].CONST_PATH_UPLOAD_DIR.'addressbook/document/'.$pnPk;

    $sCommandLine = escapeshellcmd('rm -R ').escapeshellarg($sAttchFolderPath);
    $sLastLine = exec(escapeshellcmd($sCommandLine), $asCmdResult, $nCmdResult);

    if(!empty($sLastLine))
    {
      assert('false; // couldn\'t delete attachment folder. ['.$sAttchFolderPath.']');
       return array('error' => __LINE__.' - An error occured, can\'t delete the project #'.$pnPk);
    }

    //DELETE document in DB
    $sQuery = 'DELETE FROM addressbook_document_info WHERE docfk = '.$pnPk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    if(!$oResult)
      return array('error' => __LINE__.' - An error occured, can\'t delete the document #'.$pnPk);


    $oPage = CDependency::getComponentByName('page');
    $sUrl = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, $sType,$psPnPk,'',$sType.'_tab_documentId');

    return $oHTML->getRedirection($sUrl, 1000);
 }

    /**
     * Get the document Form
     * @param integer $psPnPk
     * @return string HTML
     */

   private function _getDocumentForm($psPnPk)
   {
     if(!assert('is_integer($psPnPk) && !empty($psPnPk)'))
      return 'No data found';

    $ndocumentPk = getValue('documentfk');

    if(isset($ndocumentPk) && !empty($ndocumentPk))
    {
      $oDB = CDependency::getComponentByName('database');

      $sQuery = 'SELECT * FROM addressbook_document as ad INNER JOIN addressbook_document_info as adi ON (adi.docfk = ad.addressbook_documentpk) WHERE addressbook_documentpk = '.$ndocumentPk;
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();

      if(!$bRead)
        return 'Document seems deleted';
      else
        $asDocument = $oDbResult->getData();
        $stype = $asDocument['type'];
      }
    else
    {
      $asDocument = array('title'=>'','description'=>'','filename'=>'');
      $stype= getValue(CONST_ACTION_ITEMTYPE);
    }

    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');

    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'css/addressbook.css'));
    $sHTML = $oHTML->getBlocStart('documentFormId');

    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('addDocumentForm');
    $sFormId = $oForm->getFormId();

    //Close button on popup and remove cancel button
    $oForm->setFormDisplayParams(array('noCancelButton' => '1','noCloseButton' => '1'));

    //Get javascript for the popup
    $sURL = $oPage->getUrl('addressbook', CONST_ACTION_SAVEADD,CONST_AB_TYPE_DOCUMENT, $psPnPk,array('ppaty'=>$stype,'documentfk'=>$ndocumentPk));
    $oForm->setFormParams('', false, array('submitLabel' => 'Save document','inajax'=> true,'action' => $sURL));

    $oForm->addField('misc', '', array('type' => 'text','text'=> '<br/><span class="h4">Upload the attachment</span><br />'));
    $oForm->addField('input', 'title', array('label'=>'Title', 'value' => $asDocument['title'],'style'=>'width:100%;'));
    $oForm->setFieldControl('title', array('jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

    $oForm->addField('textarea', 'description', array('label'=>'Description', 'value' => $asDocument['description'],'style'=>'width:100%;'));
    $oForm->setFieldControl('description', array('jsFieldMaxSize' => 10000));

    $oForm->addField('misc', '', array('type'=>'br'));
    if(empty($ndocumentPk))
    {
      $oForm->addField('input', 'attachment[]', array('type' => 'file', 'label'=>'Document', 'value' => ''));
      $oForm->setFieldControl('attachment[]', array('jsFieldNotEmpty' => ''));
    }
    /* else
     {
       $oForm->addField('input', 'attachment[]', array('type' => 'file', 'label'=>'Document', 'value' => ''));
       $sURL = $oPage->getUrl($this->_getUid(), CONST_ACTION_SEND,CONST_AB_TYPE_COMPANY,(int)$asDocument['addressbook_documentpk']);
       $sRevision = '- '.$oHTML->getLink($asDocument['filename'], $sURL, array('target' => '_blank')).'<br />';
       $oForm->addField('misc', '', array('type'=>'text', 'text' => $sRevision));
      }
    */

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();

    return $oPage->getAjaxExtraContent(array('data'=>$sHTML));
   }

  /**
   * List all the companies
   * @param string $psQueryFilter
   * @return array of records
   */

  private function _getCompanyList($psQueryFilter = '')
  {
    if(!assert('is_string($psQueryFilter)'))
      return '';

    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'/css/addressbook.css');

    // Check the session things here.

    $sSetTime =  getValue('settime');
    showHideSearchForm($sSetTime,'cp');

    $sHTML = $oHTML->getTitleLine('Company Search', $this->getResourcePath().'/pictures/company_48.png');
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'searchTitle'));
    $sHTML.= $oHTML->getBlocEnd();
    //===============================================================================
    // Insert the search form in the Contact list page
    $gbNewSearch = true;

    //if clear search: do not load anything from session and generate a new searchId
    //if do_search: do not load the last search, save a new one with new parameters
    if((getValue('clear') == 'clear_cp') || getValue('do_search', 0))
      $sSearchId = manageSearchHistory($this->csUid, CONST_AB_TYPE_COMPANY, true);
    else
    {
      //reload the last search using the ID passed in parameters, ou the last done
      if(getValue('searchId'))
        $sSearchId = manageSearchHistory($this->csUid, CONST_AB_TYPE_COMPANY);
      else
        $sSearchId = reloadLastSearch($this->csUid, CONST_AB_TYPE_COMPANY);
     }
    //$gbNewSearch = true only if it's a new search
    if($gbNewSearch)
    {
      $sDisplay = 'block';
      $sExtraClass = '';
    }
    else
    {
      $sDisplay = 'none';
      $sExtraClass = ' searchFolded ';
    }

    $avResult = $this->_getCompanySearchResult($psQueryFilter, $sSearchId);

    $sCompanyMessage = $this->_getCompanySearchMessage($avResult['nNbResult'],'');
    // This is the search block
    $sHTML.= $this->_getCompanySearchBloc($sSearchId, $avResult, $gbNewSearch);

    $sJavascript = " $(document).ready(function(){ $('.searchTitle').html('".$sCompanyMessage."') }); ";
    $oPage->addCustomJs($sJavascript);

    $sHTML.= $oHTML->getBlocStart('contactListContainer');
    $sHTML.= $this->_getCompanyResultList($avResult, $sSearchId);
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Search Company with the parameters
   * @param string $psQueryFilter
   * @param string $psSearchId
   * @return array
   */

  private function _getCompanySearchResult($psQueryFilter, $psSearchId = '')
  {
    $oEvent =  CDependency::getComponentByName('event');

    $sQuery = 'SELECT count(DISTINCT cp.companypk) as nCount FROM company as cp LEFT JOIN profil as prf ON prf.companyfk = cp.companypk ';
    if($psQueryFilter)
      $sQuery.= $psQueryFilter;
    else
    {
      $asFilter = $this->_getSqlCompanySearch();
      if(!empty($asFilter['join']))
        $sQuery.= $asFilter['join'];

      if(!empty($asFilter['where']))
        $sQuery.= ' WHERE '.$asFilter['where'];
    }

    $oDb = CDependency::getComponentByName('database');
    $oDbResult = $oDb->ExecuteQuery($sQuery);
    $oDbResult->ReadFirst();
    $nNbResult = $oDbResult->getFieldValue('nCount', CONST_PHP_VARTYPE_INT);

    if($nNbResult == 0)
      return array('nNbResult' => 0, 'oData' => null);

    $oPager = CDependency::getComponentByName('pager');
    $oPager->initPager();


    $asEventQuery = $oEvent->getCompanyActivitySql();
    if(empty($asEventQuery['select']))
       $asEventQuery['select'] = '1';

    $sQuery = 'SELECT cp.*,'.$asEventQuery['select'].', cp_parent.company_name as parent_company,
      group_concat(DISTINCT lg.lastname SEPARATOR ",") as userlastname,
      group_concat(DISTINCT lg.firstname SEPARATOR ",") as userfirstname,
      group_concat(DISTINCT cp_child.companypk SEPARATOR ",") as child_pk,
      group_concat(DISTINCT cp_child.company_name SEPARATOR ",") as child_name,
      ind.industry_name as industry_name
      FROM company as cp USE INDEX (PRIMARY,company_name) ';
    $sQuery.= ' LEFT JOIN  company as cp_parent ON(cp_parent.companypk = cp.parentfk) ';
    $sQuery.= ' LEFT JOIN  company as cp_child ON(cp_child.parentfk = cp.companypk) ';
    $sQuery.= ' LEFT JOIN  company_industry as ci on ci.companyfk=cp.companypk ';
    $sQuery.= ' LEFT JOIN industry AS ind ON ind.industrypk = ci.industryfk ';
    $sQuery.= ' LEFT JOIN profil as prf ON prf.companyfk=cp.companypk and prf.date_end is NULl ';
    $sQuery.= ' LEFT JOIN login AS lg ON (lg.loginpk = cp.followerfk)';

     if(!empty($asEventQuery['join']))
        $sQuery.= $asEventQuery['join'];

     if($psQueryFilter)
      $sQuery.= $psQueryFilter;
    else
    {
      if(!empty($asFilter['join']))
        $sQuery.= $asFilter['join'];

      if(!empty($asFilter['where']))
        $sQuery.= ' WHERE '.$asFilter['where'];
    }
    $sQuery.= ' GROUP BY cp.companypk ';
    $sQuery.= $this->_getCompanySearchOrder($psSearchId);

    $oPager = CDependency::getComponentByName('pager');
    $oPager->initPager();
    $sQuery.= ' LIMIT '.$oPager->getSqlOffset().','.$oPager->getLimit();

    $oDbResult = $oDb->ExecuteQuery($sQuery);
    if(!$oDbResult->readFirst())
    {
      assert('false; // no result but count query was ok ');
      return array('nNbResult' => 0, 'oData' => null);
    }

    return array('nNbResult' => $nNbResult, 'oData' => $oDbResult);
  }

  /**
   * Get the company Search Message
   * @global boolean $gbNewSearch
   * @param type $pnNbResult
   * @param type $pasOrderDetail
   * @param type $pbOnlySort
   * @return type
   */

  private function _getCompanySearchMessage($pnNbResult = 0, $pasOrderDetail = array(), $pbOnlySort = false)
  {
    $sMessage = '';

    global $gbNewSearch;
    $oHTML = CDependency::getComponentByName('display');

    if(isset($pasOrderDetail['sortfield']) && !empty($pasOrderDetail['sortfield']))
    {
      $sSortMsg = $oHTML->getText(' - sorted by '.$pasOrderDetail['sortfield'].' '.$pasOrderDetail['sortorder'], array('class'=>'searchTitleSortMsg'));
      if($pbOnlySort)
        return $sSortMsg;
    }
    else
      $sSortMsg = $oHTML->getText('', array('class'=>'searchTitleSortMsg'));

    $sField = getValue('company_name');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' Company : '.$sField, array('class'=>'normalText'));

    $sField = getValue('phone_cp');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' phone : '.$sField, array('class'=>'normalText'));

    $sField = getValue('email_cp');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' email : '.$sField, array('class'=>'normalText'));

    $sField = getValue('cpfollowerfk');
    if(!empty($sField))
    {
      $oLogin = CDependency::getComponentByName('login');
      $asLoginData= $oLogin->getUserDataByPk((int)$sField);
      $sMessage.= $oHTML->getText(' Account Manager : '.$oLogin->getUserNameFromData($asLoginData, true), array('class'=>'normalText'));
    }

    $sField = getValue('address');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' Address : '.$sField, array('class'=>'normalText'));

    /*
    $sField = getValue('company_industry');
    if(!empty($sField))
    {
     $asIndustry =  $this->_getIndustry($sField);
     $sIndustryName = '';
     foreach($asIndustry as $asIndustryName)
      {
        $sIndustryName.= $asIndustryName['industry_name'].', ';
      }
     $sMessage.= $oHTML->getText(' Industry : '.$sIndustryName, array('class'=>'normalText'));
    }
    */

    $asField = (array)getValue('company_industry');
     if(!empty($asField) && !empty($asField[0]))
       $sMessage.= $oHTML->getText(count($asField).' industries selected', array('class'=>'normalText'));

    $sField = getValue('synopsis');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' synopsis : '.$sField, array('class'=>'normalText'));

    $sField = getValue('company_relation');
    if(!empty($sField))
    {
      $sRelation = getCompanyRelation((int)$sField);
      $sMessage.= $oHTML->getText(' Company Relation : '.$sRelation['Label'],array('class'=>'normalText'));
    }

    $sField = getValue('event_cp');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' activity : '.$sField, array('class'=>'normalText'));

    $sField = getValue('date_eventStartcp');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' activities from: '.$sField, array('class'=>'normalText'));

    $sField = getValue('date_eventEndcp');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' to : '.$sField, array('class'=>'normalText'));

    $sField = getValue('event_type_cp');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' activity type : '.$sField, array('class'=>'normalText'));

    if(!empty($gbNewSearch) && !empty($sMessage))
    {
      $sMessage = $oHTML->getText(' for ').$sMessage;
    }

    return $oHTML->getText($pnNbResult.' results') . $sMessage.' '.$sSortMsg;
  }

/**
 * Display company result lists
 * @param object array $pavResult
 * @param string $psSearchId
 * @return string HTML
 */

  private function _getCompanyResultList($pavResult, $psSearchId)
  {
    /* if(!assert('!empty($pavResult) && !empty($psSearchId)'))
      return 'No data found';*/

    $oHTML = CDependency::getComponentByName('display');
    $oPager = CDependency::getComponentByName('pager');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addRequiredJsFile($this->getResourcePath().'js/addressbook.js');

    $sHTML = $oHTML->getBlocStart('', array('style'=>'padding: 0px;background-color:#FFFFFF;width: 100%;'));
    $sHTML.= $oHTML->getListStart('', array('class' => 'ablistContainer '));

    if($pavResult['nNbResult'] == 0)
    {
       $sHTML.= $oHTML->getListItemStart();
       $sHTML.= "Couldn't find company.";
       $sHTML.= $oHTML->getListItemEnd();
       $sHTML.= $oHTML->getListEnd();
       return $sHTML;
    }

    $sUrl = $oPage->getAjaxUrl('addressbook', CONST_ACTION_LIST, CONST_AB_TYPE_COMPANY);
    $asPagerUrlOption = array('ajaxTarget' => 'contactListContainer', 'ajaxCallback' => ' jQuery(\'.searchContainer\').fadeOut(); ');
    $sHTML.= $oPager->getCompactDisplay($pavResult['nNbResult'], $sUrl, $asPagerUrlOption);


    //Get all the events for the displayed companies
    $oEvent = CDependency::getComponentByName('event');
    if(!empty($oEvent))
      $asCompanyEventData = $oEvent->getEventInformation($this->_getUid(),CONST_ACTION_VIEW,$this->getType());

    $sHTML.= $oHTML->getListItemStart('', array('class' => 'ablistHeader'));
    $sHTML.=  $this->_getCompanyRowHeader();
    $sHTML.= $oHTML->getListItemEnd();

    $nCount = 1;
    $oDbResult = $pavResult['oData'];
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $sRowId = 'cpId_'.$oDbResult->getFieldValue('companypk');
      $asCompanyData = $oDbResult->getData();
      $sHTML.= $oHTML->getListItemStart($sRowId);
      $sHTML.=  $this->_getCompanyRow($asCompanyData, $nCount);
      $sHTML.= $oHTML->getListItemEnd();

      $nCount++;
      $bRead = $oDbResult->ReadNext();
    }

    $sHTML.= $oHTML->getListEnd();
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack')) . $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    if($pavResult['nNbResult'] > 0)
      $sHTML.= $oPager->getDisplay($pavResult['nNbResult'], $sUrl, $asPagerUrlOption);

    return $sHTML;
  }

  /**
   * Display details of the company
   * @param integer $pnPK companyId
   * @return array
   */

  private function _getCompanyView($pnPK)
  {
    if(!assert('is_integer($pnPK) && !empty($pnPK)'))
      return 'No data found';

    $oRight = CDependency::getComponentByName('right');
    $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_TRANSFER,$this->getType(),0);

    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'/css/addressbook.css');

    /*@var $oDB CDatabaseyEx */
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT cp.*, ci.*, co.*,group_concat(acm.loginfk SEPARATOR ",") as followers, l.lastname as follower_lastname,
      l.firstname as follower_firstname, GROUP_CONCAT(DISTINCT ind.industry_name SEPARATOR ", ")
      as industry_name FROM company as cp ';
    $sQuery.= ' LEFT JOIN city as ci ON (ci.citypk = cp.cityfk)';
    $sQuery.= ' LEFT JOIN country as co ON (co.countrypk = cp.countryfk)';
    $sQuery.= ' LEFT JOIN login as l ON (l.loginpk = cp.followerfk)';
    $sQuery.= ' LEFT JOIN company_industry as cid ON (cp.companypk = cid.companyfk)';
    $sQuery.= ' LEFT JOIN industry as ind ON (ind.industrypk = cid.industryfk)';
    $sQuery.= ' LEFT JOIN account_manager as acm ON (acm.companyfk=cp.companypk)';
    $sQuery.= ' WHERE cp.companypk = '.$pnPK.' group by cp.companypk';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    if(!$bRead)
      return $oHTML->getBlocMessage('No result found. ');

    $asCompanyData =  $oResult->getData();

    //For the logging the activity
    $oLogin = CDependency::getComponentByName('login');
    $sLink = $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY,$pnPK);
    $oLogin->getUserActivity($oLogin->getUserPk(), $this->_getUid(), CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $pnPK, '[view] '.$asCompanyData['company_name'], $sLink);

    //count employees
    $sQuery = 'SELECT count( distinct p.contactfk) as nCount FROM profil as p,contact as c WHERE p.contactfk=c.contactpk and p.companyfk = '.$pnPK.' ';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    $nEmployee = $oResult->getFieldValue('nCount', CONST_PHP_VARTYPE_INT);

    //Count events
    $oEvent = CDependency::getComponentByName('event');
    if(!empty($oEvent))
      $nEvents = $oEvent->getCountEventInformation($this->_getUid(), CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $pnPK);
    else
      $nEvents = '';


    //search forthe company documents, brings connections docs too
    $sQuery = 'SELECT adi.*,  ad.* , ct.contactpk, ct.firstname, ct.lastname, ct.courtesy
      FROM company as cp
      LEFT JOIN profil as pro ON (pro.companyfk = cp.companypk AND pro.companyfk = '.$pnPK.')
      LEFT JOIN contact as ct ON (ct.contactpk = pro.contactfk)

      LEFT JOIN addressbook_document_info as adi ON
      (
        (adi.itemfk = cp.companypk AND adi.type="cp" )
        OR (pro.contactfk IS NOT NULL AND adi.itemfk = pro.contactfk AND adi.type="ct" )
      )
      LEFT JOIN addressbook_document as ad ON (ad.addressbook_documentpk = adi.docfk)

      WHERE cp.companypk= '.$pnPK.'

      GROUP BY ad.addressbook_documentpk
      ORDER BY adi.type, ad.date_create DESC';

    $asDocument = array();
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    while($bRead)
    {
      $nDocfk = (int)$oResult->getFieldValue('docfk');
      if(!empty($nDocfk))
        $asDocument[$nDocfk] = $oResult->getData();

      $bRead = $oResult->readNext();
    }
    $nDocument = count($asDocument);

    $sHTML = $this->getCompanyCard(0, $asCompanyData);
    $sHTML.= $oHTML->getBlocStart('', array('class' =>'top_right_activity'));
    // Transfer
    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_TRANSFER, CONST_AB_TYPE_COMPANY,(int)$asCompanyData['companypk']);
    $sAjax = $oHTML->getAjaxPopupJS($sURL, 'body','','250','800',1);

    if(isset($asCompanyData['follower_lastname']) && !empty($asCompanyData['follower_lastname']))
    {
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/manager.png', 'Account manager', '', array('style' => 'height: 24px;'));
      $sHTML.= $oHTML->getText(' Account manager: ', array('class' => 'ab_account_manager'));
      if($sAccess)
      $sHTML.= $oHTML->getLink($asCompanyData['follower_firstname'].' '.$asCompanyData['follower_lastname'],'javascript:;', array('onclick'=>$sAjax));
      else
      $sHTML.= $oHTML->getText($asCompanyData['follower_firstname'].' '.$asCompanyData['follower_lastname']);

      if($asCompanyData['followers'])
      {
        $asFollowers = $asCompanyData['followers'];
        $asData = explode(',',$asFollowers);
        $sHTML.= $oHTML->getSpace(1);
        foreach($asData as $asFollow)
        {
          $sHTML.= $oHTML->getText(',');
          $sHTML.= $oHTML->getSpace(1);
          $asRecords = $oLogin->getUserDataByPk((int)$asFollow);

          if($sAccess)
            $sHTML.= $oHTML->getLink($asRecords['firstname'].' '.$asRecords['lastname'],'javascript:;', array('onclick'=>$sAjax));
          else
            $sHTML.= $oHTML->getText($asRecords['firstname'].' '.$asRecords['lastname']);

          $sHTML.= $oHTML->getSpace(1);
        }
      }
    }
    else
      $sHTML.= $oHTML->getLink(' < Define Manager >','javascript:;', array('onclick'=>$sAjax));

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('', array('class' =>'cp_top_activity'));

    if(!empty($oEvent))
    {
      //TODO: replace this function by a generic one. Amit put something specific to AB into Event (again)
      $asLatestEmails = $oEvent->getEventDetail('email', (int)$asCompanyData['companypk'],'cp');


      // put in the array all the "items" we should fetch events from:
      // the company, and all its employees
      $asEmployee = $this->getEmployeeList((int)$asCompanyData['companypk']);

      $asEventItem[] = array('type' => CONST_AB_TYPE_COMPANY, 'pk' => $asCompanyData['companypk']);
      foreach($asEmployee as $nContactPk => $avUseless)
      {
        $asEventItem[] = array('type' => CONST_AB_TYPE_CONTACT, 'pk' => $nContactPk);
      }

      $asLatestConnectionEvent = $oEvent->getEvents($this->csUid, CONST_ACTION_VIEW, '', 0, $asEventItem, 1);
      $asLatestConnectionEvent = current($asLatestConnectionEvent);

      if(!empty($asLatestConnectionEvent))
        $asLatestEvents = $oEvent->getEventDetail('other',(int)$asCompanyData['companypk'],'cp');
      else
        $asLatestEvents = $oEvent->getEventDetail('other',(int)$asCompanyData['companypk'],'cp', 2);

      if(!empty($asLatestConnectionEvent))
      {
        $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT,(int)$asLatestConnectionEvent['cp_pk'], array(''),'ct_tab_eventId');

        $sHTML.= $oHTML->getText('Latest Activity : ', array('class' => 'ab_view_strong'));
        $asUserData= $oLogin->getUserDataByPk((int)$asLatestConnectionEvent['created_by']);
        $sHTML.= $oHTML->getText('by  '.$asUserData['firstname'].' '.$asUserData['lastname']);
        $sHTML.= $oHTML->getText(' - ');
        $sHTML.= $oHTML->getNiceTime($asLatestConnectionEvent['date_display'],0,true);

        $sHTML.= $oHTML->getBlocStart('', array('class' => '','style'=>'width:100%; border:none;'));
        $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:100%;'));

        $sShortContent = strip_tags($asLatestConnectionEvent['content']);
        if(strlen($sShortContent) > 150)
        {
            $bContentCut = true;
            $sShortContent = substr($sShortContent, 0, 130).' ... <a href="javascript:;" class="expandClass italic">see more</a>';
            $sPic = 'event_detail_expand.png';
            $sClass = 'expandClass';
        }
        else
        {
            $bContentCut = false;
            $sPic = 'event_detail_expanded.png';
            $sClass = '';
        }

        $sHTML.= $oHTML->getBlocStart('', array('class' => 'smallDivs', 'style' => 'width: 99%'));
        $sTitle = $oHTML->getPicture($oEvent->getResourcePath().'pictures/'.$sPic);
        $sTitle.= $oHTML->getSpace(2);
        if(!empty($asLatestConnectionEvent['title']))
          $sTitle.= $oHTML->getText($asLatestConnectionEvent['title']).': ';
        else
          $sTitle.= $oHTML->getText('No Title', array('class'=>'light italic')).': ';

        $sTitle = $oHTML->getLink($sTitle, 'javascript:;', array('class' => $sClass));
        $sHTML.= $oHTML->getHtmlContainer($sTitle. $sShortContent, '', array('class'=> 'eventRowContent'));

        if($bContentCut)
         $sHTML.= $oHTML->getHtmlContainer(nl2br($sTitle.'<br /><br />'.$asLatestConnectionEvent['content']), '', array('class'=> 'eventRowFull', 'style' => 'display: none;'));

        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocEnd();
      }
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
      $sHTML.= $oHTML->getBlocEnd();

      if(!empty($asLatestEmails))
      {
        foreach ($asLatestEmails as $asLatestEmail)
        {
          $sHTML.= $oHTML->getText('Latest Email: ', array('class' => 'ab_view_strong'));
          $asUserData= $oLogin->getUserDataByPk((int)$asLatestEmail['created_by']);
          $sHTML.= $oHTML->getText('by  '.$asUserData['firstname'].' '.$asUserData['lastname']);
          $sHTML.= $oHTML->getText(' - ');
          $sHTML.= $oHTML->getNiceTime($asLatestEmail['date_display'],0,true);

          $sHTML.= $oHTML->getBlocStart('', array('class' => '','style'=>' 100%; border:none;'));
          $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:100%;'));

          $sShortContent = strip_tags($asLatestEmail['content']);
          if(strlen($sShortContent) > 150)
          {
              $bContentCut = true;
              $sShortContent = substr($sShortContent, 0, 130).' ... <a href="javascript:;" class="expandClass italic">see more</a>';
              $sPic = 'event_detail_expand.png';
              $sClass = 'expandClass';
          }
          else
          {
              $bContentCut = false;
              $sPic = 'event_detail_expanded.png';
              $sClass = '';
          }

          $sHTML.= $oHTML->getBlocStart('', array('class' => 'smallDivs', 'style' => 'width: 99%'));
          $sTitle = $oHTML->getPicture($oEvent->getResourcePath().'pictures/'.$sPic);
          $sTitle.= $oHTML->getSpace(2);

          if(!empty($asLatestEmail['title']))
           $sTitle.= $oHTML->getText($asLatestEmail['title']).': ';
          else
           $sTitle.= $oHTML->getText('No Title', array('class'=>'light italic')).': ';

          $sTitle = $oHTML->getLink($sTitle, 'javascript:;', array('class' => $sClass));
          $sHTML.= $oHTML->getHtmlContainer($sTitle. $sShortContent, '', array('class'=> 'eventRowContent'));

          if($bContentCut)
              $sHTML.= $oHTML->getHtmlContainer(nl2br($sTitle.'<br /><br />'.$asLatestEmail['content']), '', array('class'=> 'eventRowFull', 'style' => 'display: none;'));

          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getBlocEnd();
          }
      }
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
      $sHTML.= $oHTML->getBlocEnd();

      if(!empty($asLatestEvents))
      {
       foreach ($asLatestEvents as $asLatestEvent)
       {
        $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getText('Latest Update: ', array('class' => 'ab_view_strong'));
        $asUserData= $oLogin->getUserDataByPk((int)$asLatestEvent['created_by']);
        $sHTML.= $oHTML->getText('by  '.$asUserData['firstname'].' '.$asUserData['lastname']);
        $sHTML.= $oHTML->getText(' - ');
        $sHTML.= $oHTML->getNiceTime($asLatestEvent['date_display'],0,true);

        $sHTML.= $oHTML->getBlocStart('', array('class' => '','style'=>'width:450px;border:none;'));
        $sHTML.= $oHTML->getBlocStart('', array('style' => 'min-width:450px;'));

        $sShortContent = strip_tags($asLatestEvent['content']);
        if(strlen($sShortContent) > 150)
        {
            $bContentCut = true;
            $sShortContent = substr($sShortContent, 0, 130).' ... <a href="javascript:;" class="expandClass italic">see more</a>';
            $sPic = 'event_detail_expand.png';
            $sClass = 'expandClass';
        }
        else
        {
            $bContentCut = false;
            $sPic = 'event_detail_expanded.png';
            $sClass = '';
        }

        $sHTML.= $oHTML->getBlocStart('', array('class' => 'smallDivs', 'style' => 'width: 99%;'));
        $sTitle = $oHTML->getPicture($oEvent->getResourcePath().'pictures/'.$sPic);
        $sTitle.= $oHTML->getSpace(2);
        if(!empty($asLatestEvent['title']))
            $sTitle.= $oHTML->getText($asLatestEvent['title']).': ';
        else
            $sTitle.= $oHTML->getText('No Title', array('class'=>'light italic')).': ';

        $sTitle = $oHTML->getLink($sTitle, 'javascript:;', array('class' => $sClass));
        $sHTML.= $oHTML->getHtmlContainer($sTitle. $sShortContent, '', array('class'=> 'eventRowContent'));

        if($bContentCut)
            $sHTML.= $oHTML->getHtmlContainer(nl2br($sTitle.'<br /><br />'.$asLatestEvent['content']), '', array('class'=> 'eventRowFull', 'style' => 'display: none;'));

        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocEnd();
         }
       }
    }

    if(empty($asLatestEmail) && empty($asLatestConnectionEvent) && empty($asLatestEvents))
    {
        if(!empty($asCompanyData['date_update']))
        {
        if(date('Y',strtotime($asCompanyData['date_update']) == date('Y'))&& (int)$asCompanyData['updated_by'] !=  $oLogin->getUserPk() )
        {
         $sHTML.= $oHTML->getBlocStart('', array('style' =>'margin-top:10px;'));
         $sHTML.= $oHTML->getText('Last Edited: ', array('class' => 'ab_view_strong'));
         $sHTML.= ' - ';

         $asUserData = $oLogin->getUserList((int)$asCompanyData['updated_by'],false,true);
         $sUser = $oLogin->getUserNameFromData(current($asUserData));
         $sHTML.= $oHTML->getNiceTime($asCompanyData['date_update'],0,true). $oHTML->getText(' - by '.$sUser);
         }
        $sHTML.= $oHTML->getBlocEnd();
        }

        $asLatestDocument= $this->_getLatestDocument((int)$asCompanyData['companypk'],'cp');

        if(!empty($asLatestDocument['title'])&&(int)$asLatestDocument['loginfk'] !=  $oLogin->getUserPk())
        {
          $sHTML.= $oHTML->getBlocStart('', array('style' =>'margin-top:10px;'));
          $sHTML.= $oHTML->getText('Latest Document: ', array('class' => 'ab_view_strong'));
          $sHTML.= ' - ';

          $sHTML.= $oHTML->getText($asLatestDocument['title']. ' - ');
          $sHTML.= $oHTML->getNiceTime($asLatestDocument['date_create'],0,true);
          $sHTML.= $oHTML->getBlocEnd();
        }
        $sHTML.= $oHTML->getCarriageReturn();

        $sHTML.= $oHTML->getText('Company has been created ');
        $sHTML.= $oHTML->getSpace(2);
        $sHTML.= $oHTML->getNiceTime($asCompanyData['date_create'],0,true);

        $sHTML.= $oHTML->getCarriageReturn(2);

        if(!empty($oEvent))
        {
          $sUrl = $oPage->getUrl('event', CONST_ACTION_ADD, CONST_EVENT_TYPE_EVENT, 0, array(CONST_EVENT_ITEM_UID => $this->_getUid(), CONST_EVENT_ITEM_ACTION => CONST_ACTION_VIEW, CONST_EVENT_ITEM_TYPE => CONST_AB_TYPE_COMPANY, CONST_EVENT_ITEM_PK => $this->cnPk));
          $sHTML.= $oHTML->getLink(' Add notes / activities to this company', $sUrl);
          $sHTML.= $oHTML->getCarriageReturn(2);
        }
      }
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('', array('class' =>'cp_top_action'));

      if(!empty($oEvent))
      {
        //Add a event
        $sHTML.= $oHTML->getBlocStart('', array('class' =>'smallPaddingTop'));
        $sUrl = $oPage->getUrl('event', CONST_ACTION_ADD, CONST_EVENT_TYPE_EVENT, 0, array(CONST_EVENT_ITEM_UID => $this->_getUid(), CONST_EVENT_ITEM_ACTION => CONST_ACTION_VIEW, CONST_EVENT_ITEM_TYPE => CONST_AB_TYPE_COMPANY, CONST_EVENT_ITEM_PK => $this->cnPk));
        $sHTML.= $oHTML->getLink($oHTML->getPicture($oEvent->getResourcePath().'pictures/add_event_16.png', 'Add activity'), $sUrl);
        $sHTML.= $oHTML->getLink(' Add a note/activity', $sUrl);
        $sHTML.= $oHTML->getBlocEnd();
      }
      $sHTML.= $oHTML->getBlocEnd();

      //Float hack
      $sHTML.= $oHTML->getBlocStart('', array('class' =>'floatHack'));
      $sHTML.= $oHTML->getBlocEnd();

      $asTabs = array();

      if($nEmployee > 0)
      {
        $sEmployeeClass = 'tab_display';
        $sEmployeeTitle='Employees ('.$nEmployee.')';
      }
      else
      {
        $sEmployeeClass = 'tab_display_inactive';
        $sEmployeeTitle='Employees';
      }

      if($nEvents > 0)
      {
      $sEventClass = '';
      $sEventTitle='Activities ('.$nEvents.')';
      }
      else
      {
        $sEventClass = 'tab_display_inactive';
        $sEventTitle='Activities';
        }

      if($nDocument > 0)
      {
        $sDocumentClass = 'tab_display';
        $sDocumentTitle ='Documents ('.$nDocument.')';
      }
      else
      {
        $sDocumentClass = 'tab_display_inactive';
        $sDocumentTitle='Documents';
      }

      $asCpTabs = $oLogin->getPreferences('cptab');

      foreach($asCpTabs as $skCpTabs=>$svCpTabs)
      {
        $sUid = isUidAvailable($svCpTabs);
        if(empty($sUid))
          unset($asCpTabs[$skCpTabs]);
      }

      if($nEvents)
      {
        if($nEvents > 0)
          $asCpTabs[CONST_TAB_CP_EVENT] = array('class'=>'tab_selected','style'=>'display:block;');
        else
          $asCpTabs[CONST_TAB_CP_EVENT] = array('style'=>'display:none;');
      }
      elseif($nDocument)
      {
        if($nDocument > 0)
          $asCpTabs[CONST_TAB_CP_DOCUMENT] = array('class'=>'tab_selected','style'=>'display:block;');
        else
          $asCpTabs[CONST_TAB_CP_DOCUMENT] = array('style'=>'display:none;');
      }
      elseif($nEmployee)
      {
        if($nEmployee > 0)
          $asCpTabs[CONST_TAB_CP_EMPLOYEES] = array('class'=>'tab_selected','style'=>'display:block;');
        else
          $asCpTabs[CONST_TAB_CP_EMPLOYEES] = array('style'=>'display:none;');
      }
      else
        $asCpTabs[CONST_TAB_CP_DETAIL] = array('class'=>'tab_selected','style'=>'display:block;');

      if(!empty($oEvent))
        $asTabs[CONST_TAB_CP_EVENT] = array('tabtitle' => $sEventTitle,'tabOptions'=>array('tabId'=>'cp_tab_eventId','class'=>''.$sEventClass.' '.$asCpTabs['cp_tab_event']['class']),'content' => $this->_getCompanyEventTab($asCompanyData),'contentOptions'=>array('contentId'=>'cp_tab_event','class'=>'display_tab hidden','style'=>$asCpTabs['cp_tab_event']['style']), 'tabstatus' =>CONST_TAB_STATUS_ACTIVE);

      $asTabs[CONST_TAB_CP_EMPLOYEES] = array('tabtitle' => $sEmployeeTitle,'tabOptions'=>array('tabId'=>'cp_tab_employeeId','class'=>''.$sEmployeeClass.' '.$asCpTabs['cp_tab_employee']['class']),'content' => $this->_getCompanyEmployeeTab($asCompanyData),'contentOptions'=>array('contentId'=>'cp_tab_employee','class'=>'display_tab hidden','style'=>$asCpTabs['cp_tab_employee']['style']), 'tabstatus' =>CONST_TAB_STATUS_ACTIVE);
      $asTabs[CONST_TAB_CP_DOCUMENT] = array('tabtitle' => $sDocumentTitle,'tabOptions'=>array('tabId'=>'cp_tab_documentId','class'=>''.$sDocumentClass.' '.$asCpTabs['cp_tab_document']['class']),'content' => $this->_getCompanyDocumentTab($asCompanyData, $asDocument),'contentOptions'=>array('contentId'=>'cp_tab_document','class'=>'display_tab hidden','style'=>$asCpTabs['cp_tab_document']['style']), 'tabstatus' =>CONST_TAB_STATUS_ACTIVE);
      $asTabs[CONST_TAB_CP_DETAIL] = array('tabtitle' => 'Detail','tabOptions'=>array('tabId'=>'cp_tab_listId','class'=> $asCpTabs['cp_tab_detail']['class']),'content' => $this->_getCompanyDetailTab($asCompanyData),'contentOptions'=>array('contentId'=>'cp_tab_detail','class'=>'display_tab hidden','style'=>$asCpTabs['cp_tab_detail']['style']), 'tabstatus' =>CONST_TAB_STATUS_ACTIVE);

      foreach($asCpTabs as $sTabs=>$vTabs)
      {
         $asOrderTabs[]=  $asTabs[$sTabs];
      }

      $sHTML.= $oHTML->getBlocStart('', array('class' =>'bottom_container'));
      $sHTML.= $oHTML->getBlocStart('',  array('class' => 'tabs_container'));
      $sHTML.= $oHTML->getBlocStart('',  array('style' => 'margin:-1px;'));
      $sHTML.= $oHTML->getTabs('', $asOrderTabs);
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Get the company card information
   * @param integer $pnCompanyPk
   * @param array $pasCompanyData
   * @return HTML structure
   */

  public function getCompanyCard($pnCompanyPk = 0, $pasCompanyData = array())
  {
    if(!assert('is_integer($pnCompanyPk) && is_array($pasCompanyData)'))
      return '';

    if(empty($pnCompanyPk) && empty($pasCompanyData))
    {
      assert('false; // need company pk or company data to display the card');
      return '';
    }

    if(!empty($pasCompanyData))
      $asCompanyData = $pasCompanyData;
    else
      $asCompanyData = $this->getCompanyByPk($pnCompanyPk);

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = '';
    $sHTML = $oHTML->getBlocStart('', array('class' => 'cp_top_container shadow'));
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'cp_card_container'));
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'cp_top_name'));

    $sHTML.= $oHTML->getBlocStart('');
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'left'));
    $sHTML.= $oHTML->getTitle($asCompanyData['company_name'], 'h3', false);
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'right'));

    if($asCompanyData['corporate_name'])
      $sHTML.= $oHTML->getTitle($asCompanyData['corporate_name'], 'h4', false);

    if($asCompanyData['parentfk'])
    {
      $asParentCompanyData = $this->getCompanyByPk((int)$asCompanyData['parentfk']);
      if(!empty($asParentCompanyData))
      {
        $sURL = $oPage->getUrl($this->csUid, CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, (int)$asCompanyData['parentfk']);

        $sHTML.= $oHTML->getLink($asParentCompanyData['company_name'], $sURL, array('title' => 'holding company', 'class' => 'h4'));
      }
    }
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();

    if(isset($asCompanyData['industry_name']) && !empty($asCompanyData['industry_name']))
    {
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'left industryList '));
      $sHTML.= $oHTML->getText($asCompanyData['industry_name']);
      $sHTML.= $oHTML->getBlocEnd();
    }

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'ab_relation_row'));
    $sCompanyRelation = getCompanyRelation($asCompanyData['company_relation']);
    $sHTML.= $oHTML->getPicture($this->getResourcePath().'/pictures/'.$sCompanyRelation['icon'], 'Relation', '', array('style' => 'height: 24px'));
    $sHTML.= $oHTML->getBlocStart() . ' '.$sCompanyRelation['Label']. $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.=  $oHTML->getText('Synopsis: ', array('class' => 'ab_view_strong'));
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.=  $oHTML->getBlocStart('', array('class' => 'ab_card_comment'));

    if(!empty($asCompanyData['comments']))
      $sHTML.= $oHTML->getText(($asCompanyData['comments']));
    else
      $sHTML.= $oHTML->getText('No Synopsis', array('class'=>'light italic'));

    $sHTML.=  $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Search Form for the company
   * @return HTML structure
   */

   private function _getCompanySearchForm($psSearchId, $pbNewSearch = true)
   {
    $nLoginPk = (int)getValue('loginpk', 0);

    $asFormFields = array('company_name', 'followerfk', 'company_relation',  'phone_cp', 'email_cp','synopsis', 'event_cp', 'event_type_cp', 'date_eventStartcp', 'date_eventEndcp');

    $nFieldDisplayed = 0;
    foreach($asFormFields as $sFieldName)
    {
      $vValue = getValue($sFieldName);
      if(!empty($vValue))
        $nFieldDisplayed++;
    }
    $nFieldToDisplay = (6 - $nFieldDisplayed);

    $oHTML = CDependency::getComponentByName('display');
    $oPage= CDependency::getComponentByName('page');
    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_LIST, CONST_AB_TYPE_COMPANY);
    $oPage->addRequiredJsFile($this->getResourcePath().'js/addressbook.js');

    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('queryForm');
    $oForm->setFormParams('', true, array('action' => $sURL, 'submitLabel' => 'Search', 'ajaxTarget' => 'contactListContainer'));
    $oForm->setFormDisplayParams(array('columns' => 2, 'noCancelButton' => '1','fullFloating' => true));

    //Company Name

    $vField = getValue('company_name');
    $oForm->addField('input', 'company_name', array('label' =>'Company Name', 'value' => $vField ));
    $oForm->setFieldControl('company_name', array('jsFieldMinSize' => 2, 'jsFieldMaxSize' => 255));

    if(!$vField && $nFieldToDisplay)
    {
      //force displaying this field if less than 4 fields displayed
      $nFieldToDisplay--;
      $oForm->setFieldDisplayParams('company_name', array('class' => 'search_cname', 'fieldname' => 'search_cname'));
    }
    else
      $oForm->setFieldDisplayParams('company_name', array('class' => (($vField || $nFieldDisplayed++ < 4)?'':'hidden ').' company_name', 'fieldname' => 'search_cname'));

    $vField = ($nLoginPk || getValue('cpfollowerfk', 0));
    $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_SEARCH, CONST_LOGIN_TYPE_USER);
    $oForm->addField('selector', 'cpfollowerfk', array('label'=> 'Account Manager', 'url' => $sURL, 'onchange' =>'$(\'#cascading_id\').parent().parent().find(\'div\').show();'));
    $oForm->setFieldControl('cpfollowerfk', array('jsFieldTypeIntegerPositive' => ''));
    if(!$vField && $nFieldToDisplay)
    {
      //force displaying this field if less than 4 fields displayed
      $nFieldToDisplay--;
      $oForm->setFieldDisplayParams('cpfollowerfk', array('class' => 'search_manager', 'fieldname' => 'search_manager'));
    }
    else
      $oForm->setFieldDisplayParams('cpfollowerfk', array('class' => (($vField || $nFieldToDisplay < 1)?'':'hidden ').' search_manager', 'fieldname' => 'search_manager'));

    if(!empty($nLoginPk))
    {
      $oLogin = CDependency::getComponentByName('login');
      $asFolowerData = $oLogin->getUserDataByPk($nLoginPk);

      if(!empty($asFolowerData))
        $oForm->addOption('cpfollowerfk', array('value' => $nLoginPk, 'label' => $oLogin->getUsernameFromData($asFolowerData)));
    }
    else
    {
      $nFollwerfk = (int)getValue('cpfollowerfk', 0);
      if(!empty($nFollwerfk))
      {
        $oLogin =  CDependency::getComponentByName('login');
        $asFollowerData = $oLogin->getUserDataByPk($nFollwerfk);
        if(!empty($asFollowerData))
          $oForm->addOption('cpfollowerfk', array('value' => $nFollwerfk, 'label' => $oLogin->getUserNameFromData($asFollowerData)));
      }
    }

    //Company Relation

    $vField = getValue('company_relation');
    $oForm->addField('select', 'company_relation', array('label' => ' Relation'));
    if(!$vField && $nFieldToDisplay)
    {
      $nFieldToDisplay--;
      $oForm->setFieldDisplayParams('company_relation', array('class' => 'search_relation', 'fieldname' => 'search_relation'));
    }
    else
      $oForm->setFieldDisplayParams('company_relation', array('class' => (($vField)?'':'hidden ').' search_relation', 'fieldname' => 'search_relation'));

     $asCompanyRel= getCompanyRelation();
    $sRelation = getValue('company_relation');
    $oForm->addOption('company_relation', array('value'=>'', 'label' => 'Select'));
    foreach($asCompanyRel as $sType=>$vType)
    {
       if($sRelation==$sType)
       $oForm->addOption('company_relation', array('value'=>$sType, 'label' => $vType['Label'],'selected'=>'selected'));
       else
       $oForm->addOption('company_relation', array('value'=>$sType, 'label' => $vType['Label']));
    }


    $vField = (array)getValue('company_industry');
    $oForm->addField('select', 'company_industry[]', array('label' =>' Industry', 'multiple' => 'multiple'));
    if(!$vField && $nFieldToDisplay)
    {
      $nFieldToDisplay--;
      $oForm->setFieldDisplayParams('company_industry[]', array('class' => 'search_industry', 'fieldname' => 'search_industry'));
    }
    else
      $oForm->setFieldDisplayParams('company_industry[]', array('class' => (($vField)?'':'hidden ').' search_industry', 'fieldname' => 'search_industry'));

    $asIndustry = $this->_getIndustry();
    foreach($asIndustry as $nIndustryPk => $asIndustryData)
    {
      if(in_array($nIndustryPk, $vField))
       $oForm->addOption('company_industry[]', array('value'=> $nIndustryPk, 'label' => $asIndustryData['industry_name'], 'selected' => 'selected'));
      else
       $oForm->addOption('company_industry[]', array('value'=> $nIndustryPk, 'label' => $asIndustryData['industry_name']));
    }

    $vField = getValue('phone_cp');
    $oForm->addField('input', 'phone_cp', array('label' => 'Phone', 'value' => $vField));
    $oForm->setFieldControl('phone_cp', array('jsFieldMinSize' => 4, 'jsFieldMaxSize' => 20));
    $oForm->setFieldDisplayParams('phone_cp', array('class' => 'hidden search_phone'));
    $oForm->setFieldDisplayParams('phone_cp', array('class' => (($vField)?'':'hidden ').' search_phone', 'fieldname' => 'search_phone'));


    $vField = getValue('email_cp');
    $oForm->addField('input', 'email_cp', array('label' => 'Email', 'value' => $vField));
    $oForm->setFieldControl('email_cp', array('jsFieldMinSize' => 2));
    $oForm->setFieldDisplayParams('email_cp', array('class' => 'hidden search_email'));
    $oForm->setFieldDisplayParams('email_cp', array('class' => (($vField)?'':'hidden ').' search_email', 'fieldname' => 'search_email'));

    $vField = getValue('synopsis');
    $oForm->addField('input', 'synopsis', array('label' => 'Synopsis', 'value' => $vField));
    $oForm->setFieldControl('synopsis', array('jsFieldMinSize' => 2));
    $oForm->setFieldDisplayParams('synopsis', array('class' => 'hidden search_synopsis'));
    $oForm->setFieldDisplayParams('synopsis', array('class' => (($vField)?'':'hidden ').' search_synopsis', 'fieldname' => 'search_synopsis'));

    $vField = getValue('address');
    $oForm->addField('input', 'address', array('label' => 'Address', 'value' => $vField));
    $oForm->setFieldControl('address', array('jsFieldMinSize' => 4, 'jsFieldMaxSize' => 20));
    $oForm->setFieldDisplayParams('address', array('class' => (($vField)?'':'hidden ').' search_address', 'fieldname' => 'search_address'));

    $oEvent = CDependency::getComponentUidByName('event');
    if(!empty($oEvent))
    {
      $vField = getValue('event_type_cp');
      $oForm->addField('select', 'event_type_cp', array('label' => ' Type'));
      $oForm->setFieldDisplayParams('event_type_cp', array('class' => (($vField)?'':'hidden ').' search_evt_type', 'fieldname' => 'search_evt_type'));
      $oForm->addOption('event_type_cp', array('value'=>'', 'label' => 'Select'));

      $asEvent= getEventTypeList();
      $sEventTypes = getValue('event_type_cp');
      foreach($asEvent as $asEvents)
      {
        if($asEvents['value'] == $sEventTypes)
          $oForm->addOption('event_type_cp', array('value'=>$asEvents['value'], 'label' => $asEvents['label'], 'group' => $asEvents['group'], 'selected'=>'selected'));
        else
          $oForm->addOption('event_type_cp', array('value'=>$asEvents['value'], 'label' => $asEvents['label'], 'group' => $asEvents['group']));
      }

      $vField = getValue('event_cp');
      $oForm->addField('input', 'event_cp', array('label' => ' Activity Content', 'value' => $vField));
      $oForm->setFieldControl('event_cp', array('jsFieldMinSize' => 2));
      $oForm->setFieldDisplayParams('event_cp', array('class' => (($vField)?'':'hidden ').' search_evt_content', 'fieldname' => 'search_evt_content'));

      $vField = getValue('date_eventStartcp');
      $oForm->addField('input', 'date_eventStartcp', array('type' => 'date', 'label'=>'Activity From', 'value' => $vField));
      $oForm->setFieldDisplayParams('date_eventStartcp', array('class' => (($vField)?'':'hidden ').' search_evt_from', 'fieldname' => 'search_evt_from'));

      $vField = getValue('date_eventEndcp');
      $oForm->addField('input', 'date_eventEndcp', array('type' => 'date', 'label'=>' Activity To', 'value' => $vField));
      $oForm->setFieldDisplayParams('date_eventEndcp', array('class' => (($vField)?'':'hidden ').' search_evt_to', 'fieldname' => 'search_evt_to'));
    }

    $oCField = CDependency::getComponentByName('customfields');
    if(!empty($oCField))
    {
      $asCField = $oCField->getCustomfields($this->csUid, '', CONST_AB_TYPE_COMPANY);

      if(!empty($asCField))
      {
        $sOption = '<option value="">Custom field</option>';
        $vField = getValue('search_cf');

        foreach($asCField as $asFieldData)
        {
          if($vField == $asFieldData['customfieldpk'])
            $sOption.= '<option value="'.$asFieldData['customfieldpk'].'" selected="selected">'.$asFieldData['label'].'</option>';
          else
            $sOption.= '<option value="'.$asFieldData['customfieldpk'].'">'.$asFieldData['label'].'</option>';
        }

        $sLabel = '<select name="search_cf">'.$sOption.'</select>';
        $vField = getValue('search_cf_value');
        $oForm->addField('input', 'search_cf_value', array('type' => 'text', 'label'=> $sLabel, 'value' => $vField));
        $oForm->setFieldDisplayParams('search_cf_value', array('class' => (($vField)?'':'hidden ').' search_cf', 'fieldname' => 'search_cf'));
      }
    }

    if(isset($_POST['sortfield']))
      $sSortField = $_POST['sortfield'];
    else
      $sSortField = '';

    if(isset($_POST['sortorder']))
      $sSortOrder = $_POST['sortorder'];
    else
      $sSortOrder = '';

    $oForm->addField('hidden', 'sortfield', array('value' =>$sSortField));
    $oForm->addField('hidden', 'sortorder', array('value' => $sSortOrder));
    $oForm->addField('hidden', 'do_search', array('value' => 1));

    return $oForm->getDisplay();
  }

  /**
   * Get the query for company search
   * @return array
   */

  private function _getSqlCompanySearch()
 {

    $sName = getValue('company_name');
    $sPhone = getValue('phone_cp');
    $sEmail = getValue('email_cp');
    $anIndustry = getValue('company_industry',array());
    $nFollower = getValue('cpfollowerfk');
    $sSynopsis = getValue('synopsis');
    $sAddress = getValue('address');
    $sRelation = getValue('company_relation');
    $sEvent = getValue('event_cp');
    $sEventType = getValue('event_type_cp');
    $sStartDate = getValue('date_eventStartcp');
    $sEndDate = getValue('date_eventEndcp');
    $nLoginPk = getValue('loginpk');

    $sCFieldPk = getValue('search_cf');
    $sCFieldValue = getValue('search_cf_value');

    $sSearchMode = getValue('search_mode');

    $oDb = CDependency::getComponentByName('database');
    $asResult = array();
    $asResult['join'] = '';
    $asResult['where'] = '';
    $asWhereSql = array();

    if(!empty($sName))
      $asWhereSql[] = '(lower(cp.company_name) LIKE '.$oDb->dbEscapeString('%'.strtolower($sName).'%').' OR cp.corporate_name LIKE '.$oDb->dbEscapeString('%'.strtolower($sName).'%').')';

    if(!empty($sPhone))
      $asWhereSql[] = ' cp.phone LIKE '.$oDb->dbEscapeString('%'.$sPhone.'%');

    if(!empty($sSynopsis))
      $asWhereSql[] = ' cp.comments LIKE '.$oDb->dbEscapeString('%'.$sSynopsis.'%');

    if(!empty($sRelation))
      $asWhereSql[] = ' cp.company_relation = '.$oDb->dbEscapeString($sRelation);

    if(!empty($sEmail))
      $asWhereSql[] = ' lower(cp.email) LIKE '.$oDb->dbEscapeString('%'.strtolower($sEmail).'%');

    if(!empty($sAddress))
       $asWhereSql[] = ' lower(cp.address_1) LIKE '.$oDb->dbEscapeString('%'.strtolower($sAddress).'%').' OR lower(cp.address_2) LIKE '.$oDb->dbEscapeString('%'.strtolower($sAddress).'%');


    if(!empty($nFollower))
     $asResult['join'].= ' INNER JOIN account_manager as acmn ON (acmn.companyfk = cp.companypk AND acmn.loginfk='.$nFollower.') OR  cp.followerfk = '.$oDb->dbEscapeString($nFollower);

    if(!empty($nLoginPk))
      $asWhereSql[] = ' cp.followerfk = '.$oDb->dbEscapeString($nLoginPk);

     if(!empty($anIndustry))
     {
       $asResult['join'].= 'INNER JOIN company_industry as cid ON (cid.companyfk = cp.companypk)';
       $asResult['join'].= 'INNER JOIN industry as indr ON (indr.industrypk = cid.industryfk)';

      foreach($anIndustry as $vKey => $nIndustry)
        $anIndustry[$vKey] = $oDb->dbEscapeString($nIndustry);

      $asWhereSql[] = ' indr.industrypk IN ('.implode(',', $anIndustry).') ';
    }

    if(!empty($sEvent) || !empty($sEventType) || (!empty($sStartDate) && !empty($sEndDate)))
    {
      $asResult['join'].= 'LEFT JOIN event_link as evelnk ON (evelnk.cp_pk = cp.companypk and evelnk.cp_type="cp")';
      $asResult['join'].= 'LEFT JOIN event as even ON (even.eventpk = evelnk.eventfk)';
      $asResult['join'].= 'LEFT JOIN event_link as evelnk2 ON (evelnk2.cp_pk = prf.contactfk and evelnk2.cp_type="ct")';
      $asResult['join'].= 'LEFT JOIN event as even2 ON (even2.eventpk = evelnk2.eventfk)';

      if(!empty($sEvent))
        $asWhereSql[] = ' lower(even.title) like '.$oDb->dbEscapeString('%'.strtolower($sEvent).'%').' OR lower(even.content) like '.$oDb->dbEscapeString('%'.strtolower($sEvent).'%').' OR lower(even2.title) like '.$oDb->dbEscapeString('%'.strtolower($sEvent).'%').' OR lower(even2.content) like '.$oDb->dbEscapeString('%'.strtolower($sEvent).'%');

      if(!empty($sEventType))
      {
        $asWhereSql[] = 'lower(even.type) like '.$oDb->dbEscapeString('%'.strtolower($sEventType).'%');
        $asWhereSql[] = 'lower(even2.type) like '.$oDb->dbEscapeString('%'.strtolower($sEventType).'%');
      }
      if(!empty($sStartDate))

        $asWhereSql[] = ' date_format(even.date_display,"%Y-%m-%d") >= '.$oDb->dbEscapeString(date('Y-m-d',strtotime($sStartDate)));
      if(!empty($sEndDate))
        $asWhereSql[] = ' date_format(even.date_display,"%Y-%m-%d") >= '.$oDb->dbEscapeString(date('Y-m-d',strtotime($sEndDate)));
      if(!empty($sStartDate) && !empty($sEndDate))
      {
        $asWhereSql[] = ' date_format(even.date_display,"%Y-%m-%d") >= '.$oDb->dbEscapeString(date('Y-m-d',strtotime($sStartDate)));
        $asWhereSql[] = ' date_format(even.date_display,"%Y-%m-%d") <= '.$oDb->dbEscapeString(date('Y-m-d',strtotime($sEndDate)));
      }
    }

    if(!empty($sCFieldPk) && !empty($sCFieldValue))
    {
      $oCField = CDependency::getComponentByName('customfields');
      if(!empty($oCField))
      {
        $asCFSql = $oCField->getSearchSql((int)$sCFieldPk, $sCFieldValue);
        $asResult['join'].= $asCFSql['join'];
        if(!empty($asCFSql['where']))
          $asWhereSql[] = $asCFSql['where'];
      }
    }

    if($sSearchMode == 'or')
      $asResult['where'] =  implode(' OR ', $asWhereSql);
    else
      $asResult['where'] = implode(' AND ', $asWhereSql);

    return $asResult;
  }

   /**
   * Display the company detail information
   * @param array $pasCompanyData
   * @return string HTML
   */

  private function _getCompanyDetailTab($pasCompanyData)
  {
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');
    $oRight = CDependency::getComponentByName('right');
    $oCustomFields = CDependency::getComponentByName('customfields');

    if(!assert('is_array($pasCompanyData) && !empty($pasCompanyData)'))
      return $oHTML->getBlocMessage('No data available.');

    $sCustomFields = $oCustomFields->getCustomfieldDisplay($this->csUid, $this->csAction, $this->csType, $this->getPk());
    $sCustomFields.= $oCustomFields->getCustomfieldDisplay($this->csUid, $this->csAction, $this->csType);

    $sHTML =  $oHTML->getBlocStart('',array('class'=>'containerClass'));

    $asUserData = $oLogin->getUserList((int)$pasCompanyData['creatorfk'], false,true);
    $sUser = $oLogin->getUserNameFromData(current($asUserData));

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Creation date');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $oHTML->getText(getFormatedDate('Y-m-d',$pasCompanyData['date_create']).' - by '.$sUser);
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Phone');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $oHTML->getText($pasCompanyData['phone']);
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' =>'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();
    $asUserData = $oLogin->getUserList((int)$pasCompanyData['updated_by'],false,true);
    $sUser = $oLogin->getUserNameFromData(current($asUserData));

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Edited date');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $oHTML->getText(getFormatedDate('Y-m-d',$pasCompanyData['date_update']).' - by '.$sUser);
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Fax');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $oHTML->getText($pasCompanyData['fax']);
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' =>'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection addressBloc'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Address');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $this->_getAddress($pasCompanyData,',');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Website');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $oHTML->getText('<a href='.$pasCompanyData['website'].' target="_blank;">'.$pasCompanyData['website'].'</a>');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $sCustomFields;


    if($oRight->canAccess('180-290','ppaa',CONST_CF_TYPE_CUSTOMFIELD, 0))
      $sHTML.= $oCustomFields->getCustomFieldAddLink($this->_getUid(),'ppav','cp',(int)$pasCompanyData['companypk']);

    $sHTML.= $oHTML->getBlocStart('', array('class' =>'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Display the company employees
   * @param array $pasCompanyData
   * @return string HTML
   */

  private function _getCompanyEmployeeTab($pasCompanyData)
  {
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');

    if(!assert('is_array($pasCompanyData) && !empty($pasCompanyData)'))
      return $oHTML->getBlocMessage('No data available.');

    /*@var $oDB CDatabaseyEx */
    /*@var $oResult CDbResult */
    $oDB = CDependency::getComponentByName('database');

    //Search in database all the contacts in this company
    $nCompanyPk = (int)$pasCompanyData['companypk'];

    $sQuery = 'SELECT ct.*,eve.*,group_concat(DISTINCT CONCAT(lg.firstname) SEPARATOR ",") as userfirstname,group_concat(DISTINCT CONCAT(lg.lastname) SEPARATOR ",") as userlastname,group_concat(distinct prf.email) as profileEmail,GROUP_CONCAT(DISTINCT prf.position) AS position,GROUP_CONCAT(DISTINCT prf.department) AS department,group_concat(distinct prf.phone) as profilePhone,group_concat(DISTINCT cp.company_name) as company_name, count(DISTINCT cp.company_name) as ncount,group_concat(DISTINCT ind.industry_name SEPARATOR ",") as industry_name FROM contact as ct ';
    $sQuery.= ' INNER JOIN profil as prf ON (ct.contactpk = prf.contactfk and prf.companyfk='.$nCompanyPk.')';
    $sQuery.= ' INNER JOIN company as cp ON (cp.companypk = prf.companyfk and cp.companypk='.$nCompanyPk.' )';
    $sQuery.= ' LEFT JOIN event_link as evel ON (ct.contactpk = evel.cp_pk and evel.cp_type ="ct")';
    $sQuery.= ' LEFT JOIN event as eve ON (eve.eventpk = evel.eventfk)';
    $sQuery.= ' LEFT JOIN company_industry AS cmpid ON (cp.companypk = cmpid.companyfk)';
    $sQuery.= ' LEFT JOIN industry AS ind ON (cmpid.industryfk = ind.industrypk)';
    $sQuery.= ' LEFT JOIN login AS lg ON (lg.loginpk = ct.followerfk)';
    $sQuery.= ' GROUP BY ct.contactpk';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    $sHTML = '';
    $sURL  = $oPage->getUrl('addressbook', CONST_ACTION_ADD, CONST_AB_TYPE_CONTACT, 0, array('cppk' => $nCompanyPk));
    $sHTML.= $oHTML->getLink($oHTML->getPicture($this->getResourcePath().'pictures/ct_add_16.png', 'Add a contact'),$sURL);
    $sHTML.= $oHTML->getLink('Add connection', $sURL);

    /*// Link an existing contact

    $asURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_MANAGE, CONST_AB_TYPE_COMPANY, $nCompanyPk);
    $sAjax = $oHTML->getAjaxPopupJS($asURL, 'body','','650','600',1);
    $sHTML.= $oHTML->getLink('link an existing connection','javascript:;', array('onclick'=>$sAjax));
    */
    if(!$bRead)
    {
      $sHTML.= $oHTML->getBlocMessage('No employee in this company.<br />', true);
      return $sHTML;
    }

    $sHTML.= $this->_getContactRowSmallHeader();

    $nCount = 0;
    while($bRead)
    {
      $asContactData = $oResult->getData();
      $sHTML.= $this->_getContactRow($asContactData, $nCount,1);
      $nCount++;
      $bRead = $oResult->readnext();
    }
    return $sHTML;
  }

  /**
  * Tab to  Display the document for the connection
  * @param array $pasCompanyData
  * @return string HTML
  */

  private function _getContactDocumentTab($pasContactData)
  {
    /* @var $oPage CPageEx */
    $oHTML = CDependency::getComponentByName('display');
    if(!assert('is_array($pasContactData) && !empty($pasContactData)'))
      return $oHTML->getBlocMessage('No data available.');

    /* @var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oRight = CDependency::getComponentByName('right');
    $oLogin = CDependency::getComponentByName('login');
    /* @var $oDB CDatabaseEx */
    $oDB = CDependency::getComponentByName('database');
    $nItemPk = (int)$pasContactData['contactpk'];
        //Popup to upload the document

    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_ADD, 'doc', $nItemPk,array('ppaty'=>CONST_AB_TYPE_CONTACT));
    $sAjax = $oHTML->getAjaxPopupJS($sURL, 'body','','500','600',1);
    $sAddLink = $oHTML->getLink($oHTML->getPicture($this->getResourcePath().'pictures/doc_16.png', 'Add a new document'),'javascript:;', array('onclick'=>$sAjax));
    $sAddLink.= ' '.$oHTML->getLink('Add a new document','javascript:;', array('onclick'=>$sAjax));

    $sQuery = 'SELECT a.*, b.* FROM addressbook_document a, addressbook_document_info b ';
    $sQuery.= ' WHERE b.type="ct" and a.addressbook_documentpk= b.docfk  and b.itemfk = '.$nItemPk.' ';
    $sQuery.= 'ORDER BY a.date_create desc  ';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return $oHTML->getblocMessage('No documents available. <br /><br />'.$sAddLink, true);

    $sHTML = $oHTML->getBlocStart();
    $sHTML.= $sAddLink;
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getCarriageReturn();

    $asDocuments=array();
    while($bRead)
    {
      $asDocuments[$oDbResult->getFieldValue('addressbook_documentpk')] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    if($asDocuments)
    {
     $asUsers = $oLogin->getUserList(0,false,true);
     foreach($asDocuments as $asDocument)
      {
        $nDocumentPk = (int)$asDocument['addressbook_documentpk'];

        if(empty($asDocument['description']))
          $asDocument['description'] = '<span class="light italic"> No description.</span>';

        if(empty($asDocument['title']))
          $asDocument['title'] = '<span class="light italic"> No title.</span>';

         $sHTML.= $oHTML->getBlocStart('', array('class' => 'documentListCell'));
          $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:100%; padding:5px;'));

            $sHTML.= $oHTML->getBlocStart('',array('style'=>'min-width: 130px; padding:5px; float:left;'));
            $sHTML.= $oHTML->getNiceTime($asDocument['date_create'], 0, true, true);
            $sHTML.= $oHTML->getCarriageReturn();
            $sHTML.= $oHTML->getSpace();
            $sHTML.= $oHTML->getText('by '.$oLogin->getUserNameFromData($asUsers[(int)$asDocument['loginfk']], true));
            $sHTML.= $oHTML->getBlocEnd();


            $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:60%; padding:5px;float:left;'));
            $sContent = $asDocument['title'] . $oHTML->getCarriageReturn() . $asDocument['description'];
            $sHTML.= $oHTML->getHtmlContainer($sContent, '', array('style' => 'background-color: #F4F4F4;'));
            $sHTML.= $oHTML->getBlocEnd();

            $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_EDIT,CONST_AB_TYPE_DOCUMENT,0);
            if($sAccess)
            {
              $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:3%;float:left;margin-left:20px;margin-top:10px;'));
              $sUrl = $oPage->getAjaxUrl('addressbook', CONST_ACTION_EDIT, CONST_AB_TYPE_DOCUMENT,$nItemPk,array('documentfk'=>$nDocumentPk));
              $sPic = $oHTML->getPicture(CONST_PICTURE_EDIT, 'Edit attachment');
              $sAjax = $oHTML->getAjaxPopupJS($sUrl, 'body','','500','600',1);
              $sHTML.= $oHTML->getLink($sPic,'javascript:;',array('onclick'=>$sAjax));
               $sHTML.= $oHTML->getBlocEnd();
            }
            $sHTML.= $oHTML->getSpace(2);

            $sDAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_DELETE,CONST_AB_TYPE_DOCUMENT,0);
            if($sDAccess)
            {
              $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:3%;float:left;margin-left:2px;margin-top:10px;'));
              $sUrl = $oPage->getAjaxUrl('addressbook', CONST_ACTION_DELETE, CONST_AB_TYPE_DOCUMENT, $nDocumentPk);
              $sPic = $oHTML->getPicture(CONST_PICTURE_DELETE, 'Delete attachment');
              $sHTML.= $oHTML->getLink($sPic, $sUrl, array('onclick' => 'if(!window.confirm(\'Delete this attached document ?\')){ return false; }'));
              $sHTML.= $oHTML->getBlocEnd();
            }
            $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:14%; min-width: 150px; float:right;'));
            $sPic = $oHTML->getPicture(CONST_PICTURE_DOWNLOAD, 'Download file', '');
            $sUrl = $oPage->getUrl('addressbook', CONST_ACTION_SEND, CONST_AB_TYPE_CONTACT, $nDocumentPk);
            $sHTML.= $oHTML->getLink($asDocument['filename'], $sUrl, array('target' => '_blank', 'class' => 'dl_link'));
            $sHTML.= $oHTML->getSpace(2);
            $sHTML.= $oHTML->getLink($sPic, $sUrl, array('target' => '_blank', 'class' => 'dl_link'));
            $sHTML.= $oHTML->getBlocEnd();


          $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack')) . $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();


      }
    }
   return $sHTML;
 }

 /**
  * Tab to  Display the document for the company
  * @param array $pasCompanyData
  * @return string HTML
  */

  private function _getCompanyDocumentTab($pasCompanyData, $pasDocument)
  {
    /* @var $oPage CPageEx */
    $oHTML = CDependency::getComponentByName('display');
    $oRight = CDependency::getComponentByName('right');

    if(!assert('is_array($pasCompanyData) && !empty($pasCompanyData)'))
      return $oHTML->getBlocMessage('No data available.');


    if(!assert('is_array($pasDocument)') || empty($pasDocument))
      return $oHTML->getBlocMessage('No data available.');

    /* @var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oLogin = CDependency::getComponentByName('login');
    /* @var $oDB CDatabaseEx */
    $oDB = CDependency::getComponentByName('database');

    $nItemPk = (int)$pasCompanyData['companypk'];

    //Popup to upload the document
    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_ADD, 'doc', $nItemPk,array('ppaty'=>CONST_AB_TYPE_COMPANY));
    $sAjax = $oHTML->getAjaxPopupJS($sURL, 'body','','500','600',1);
    $sAddlink = $oHTML->getLink($oHTML->getPicture($this->getResourcePath().'pictures/doc_16.png', 'Add a new document'),'javascript:;', array('onclick'=>$sAjax));
    $sAddlink.= $oHTML->getLink('Add a new document','javascript:;', array('onclick'=>$sAjax));

    $sHTML = $oHTML->getBlocStart();
    $sHTML.= $sAddlink;
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getCarriageReturn();


    $asUsers = $oLogin->getUserList(0, false, true);

    foreach($pasDocument as $asDocument)
    {
      $nDocumentPk = (int)$asDocument['addressbook_documentpk'];

      if(empty($asDocument['description']))
        $asDocument['description'] = '<span class="light italic">No description.</span>';

      if(empty($asDocument['title']))
        $asDocument['title'] = '<span class="light italic">No title.</span>';

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'documentListCell light_shadow'));
        $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:100%; padding:5px;'));

          $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:12%; min-width: 150px; padding:5px; float:left;'));
          $sHTML.= $oHTML->getNiceTime($asDocument['date_create'], 0, true, true);
          $sHTML.= $oHTML->getCarriageReturn();
          $sHTML.= $oHTML->getSpace();

          if(!isset($asDocument['loginfk']) || empty($asDocument['loginfk']))
            $sHTML.= $oHTML->getText('by unknown');
          else
            $sHTML.= $oHTML->getText('by '.$oLogin->getUserNameFromData($asUsers[(int)$asDocument['loginfk']], true));

          $sHTML.= $oHTML->getBlocEnd();


          $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:55%; padding:5px;float:left;'));

          $sContent = $asDocument['title'] . $oHTML->getCarriageReturn() . $asDocument['description'];
          $sHTML.= $oHTML->getHtmlContainer($sContent, '', array('style' => 'background-color: #F4F4F4;'));

          if($asDocument['type'] == 'ct')
          {
            $sHTML.= $oHTML->getBlocStart('',array('class'=>'ab_doctab_employee_doc'));
            $sHTML.= $oHTML->getText(' Document uploaded on ');

            $sURL = $oPage->getUrl($this->csUid, CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, (int)$asDocument['contactpk']);
            $sName = $this->getContactName($asDocument);
            $sHTML.= $oHTML->getLink($sName, $sURL);

            $sHTML.= $oHTML->getText('\'s profile.');
            $sHTML.= $oHTML->getBlocEnd();
          }
          $sHTML.= $oHTML->getBlocEnd();

          $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_EDIT,CONST_AB_TYPE_DOCUMENT,0);
          if($sAccess)
          {
            $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:3%;float:left;margin-left:20px;margin-top:10px;'));
            $sUrl = $oPage->getAjaxUrl('addressbook', CONST_ACTION_EDIT, CONST_AB_TYPE_DOCUMENT,$nItemPk,array('documentfk'=>$nDocumentPk));
            $sPic = $oHTML->getPicture(CONST_PICTURE_EDIT, 'Edit attachment');
            $sAjax = $oHTML->getAjaxPopupJS($sUrl, 'body','','500','600',1);
            $sHTML.= $oHTML->getLink($sPic,'javascript:;',array('onclick'=>$sAjax));
            $sHTML.= $oHTML->getBlocEnd();
          }
          $sHTML.= $oHTML->getSpace(2);

          $sDAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_DELETE,CONST_AB_TYPE_DOCUMENT,0);
          if($sDAccess)
          {
            $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:3%;float:left;margin-left:2px;margin-top:10px;'));
            $sUrl = $oPage->getAjaxUrl('addressbook', CONST_ACTION_DELETE, CONST_AB_TYPE_DOCUMENT, $nDocumentPk);
            $sPic = $oHTML->getPicture(CONST_PICTURE_DELETE, 'Delete attachment');
            $sHTML.= $oHTML->getLink($sPic, $sUrl, array('onclick' => 'if(!window.confirm(\'Delete this attached document ?\')){ return false; }'));
            $sHTML.= $oHTML->getBlocEnd();
          }

          $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:20%; min-width: 150px; float:right;'));
            $sPic = $oHTML->getPicture(CONST_PICTURE_DOWNLOAD, 'Download file', '');
            $sUrl = $oPage->getUrl('addressbook', CONST_ACTION_SEND, CONST_AB_TYPE_CONTACT, $nDocumentPk);
            $sHTML.= $oHTML->getLink($sPic, $sUrl, array('target' => '_blank', 'class' => 'dl_link'));
            $sHTML.= $oHTML->getSpace(2);
            $sHTML.= $oHTML->getLink($asDocument['filename'], $sUrl, array('target' => '_blank', 'class' => 'dl_link'));
          $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getFloatHack();

      $sHTML.= $oHTML->getBlocEnd();
    }

    return $sHTML;
  }

  /**
   * Display event tab for the company
   * @param array $pasCompanyData
   * @return string HTML
   */

  private function _getCompanyEventTab($pasCompanyData)
  {
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');

    if(!assert('is_array($pasCompanyData) && !empty($pasCompanyData)'))
      return $oHTML->getBlocMessage('No data available to fetch activities.');

    $oPage = CDependency::getComponentByName('page');
    $oEvent = CDependency::getComponentByName('event');
    $sHTML = $oHTML->getBlocStart();

    if(!empty($oEvent))
    {
    $sEventList = $oEvent->getEventList(0, $this->_getUid(), CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $this->cnPk);
    $sUrl = $oPage->getUrl('event', CONST_ACTION_ADD, CONST_EVENT_TYPE_EVENT, 0, array(CONST_EVENT_ITEM_UID => $this->_getUid(), CONST_EVENT_ITEM_ACTION => CONST_ACTION_VIEW, CONST_EVENT_ITEM_TYPE => CONST_AB_TYPE_COMPANY, CONST_EVENT_ITEM_PK => $this->cnPk));
    $sAddLink = $oHTML->getLink($oHTML->getPicture($oEvent->getResourcePath().'pictures/add_event_16.png', 'Add a new event'),$sUrl,array('title'=>'Add a new event'));
    $sAddLink.= $oHTML->getLink(' Add a new activity', $sUrl);

    if(empty($sEventList))
     $sHTML.= $oHTML->getBlocMessage('No activities for this company. <br /><br />'.$sAddLink, true);
    else
    {
      $sHTML.= $sAddLink. $oHTML->getCarriageReturn(2);
      $sHTML.= $sEventList;
      }
    }
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
    }

  /**
   * Link the company to the connection
   * @param integer $pnCompanyPk
   * @return ajax data
   */
  private function _getLinkCompanyContact($pnCompanyPk)
  {
      $oHTML = CDependency::getComponentByName('display');
     if(!assert('is_integer($pnCompanyPk) && !empty($pnCompanyPk)'))
      return $oHTML->getBlocMessage('No company found.');

    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');

    $sHTML = $oHTML->getBlocStart();
    //Start the form
    $oForm = $oHTML->initForm('linkContactForm');
    $sFormId = $oForm->getFormId();

    //Get javascript for the popup
    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SAVEEDIT, CONST_AB_TYPE_COMPANY, $pnCompanyPk);
    $sJs = $oHTML->getAjaxJs($sURL, 'body', $sFormId);
    $oForm->setFormParams('', false, array('action' => '','inajax'=> 1, 'onsubmit' => 'event.preventDefault(); '.$sJs));

    //Close button on popup and remove cancel button
    $oForm->setFormDisplayParams(array('noCancelButton' => '1','noCloseButton' => '1'));
    $sHTML.= $oHTML->getBlocStart();
    $asCompanyData = $this->getCompanyByPk($pnCompanyPk);
    $oForm->addField('misc', '', array('type' => 'title', 'title' => '<span class="h4">Connect connection to '.$asCompanyData['company_name'].'</span><br />'));
    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SEARCH, CONST_AB_TYPE_CONTACT);
    $oForm->addField('selector', 'contactfk', array('label'=> 'Connection', 'url' => $sURL, 'nbresult' =>1));
    $oForm->setFieldControl('contactfk', array('jsFieldNotEmpty' => ''));
    $oForm->addField('input', 'position', array('label'=> 'Position', 'value' =>''));
    $oForm->setFieldControl('position', array('jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));
    $oForm->addField('input', 'email', array('label'=> 'Email Address', 'value' =>''));
    $oForm->setFieldControl('email', array('jsFieldTypeEmail' => '','jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

    $oForm->addField('input', 'phone', array('label'=> 'Phone', 'value' => ''));
    $oForm->setFieldControl('phone', array('jsFieldNotEmpty' => '','jsFieldMinSize' => 4));
    $oForm->addField('input', 'fax', array('label'=> 'Fax', 'value' => ''));
    $oForm->setFieldControl('fax', array('jsFieldMinSize' => 8));
    $oForm->addField('textarea', 'address', array('label'=> 'Address ', 'value' =>''));
    $oForm->setFieldControl('address', array('jsFieldNotEmpty' => ''));
    $oForm->addField('input', 'postcode', array('label'=> 'Postcode', 'value' => ''));
    $oForm->setFieldControl('postcode', array('jsFieldTypeIntegerPositive' => '', 'jsFieldMaxSize' => 12));
    $oForm->addField('selector_city', 'cityfk', array('label'=> 'City', 'url' => CONST_FORM_SELECTOR_URL_CITY));
    $oForm->setFieldControl('cityfk', array('jsFieldTypeIntegerPositive' => ''));
    $oForm->addField('selector_country', 'countryfk', array('label'=> 'Country', 'url' => CONST_FORM_SELECTOR_URL_COUNTRY));
    $oForm->setFieldControl('countryfk', array('jsFieldTypeIntegerPositive' => ''));
    $oForm->addField('misc', '', array('type'=> 'br'));
    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $asresult = array('data' => $sHTML);
    return $oPage->getAjaxExtraContent($asresult);
  }

  /**
   * Display the Header for the company listing
   * @param string $psSearchId
   * @return string HTML
   */

  private function _getCompanyRowHeader($psSearchId = '')
  {
      /*@var $oPage CPageEx */
      $oPage = CDependency::getComponentByName('page');
      /*@var $oHTML CDisplayEx */
      $oHTML = CDependency::getComponentByName('display');

      $sHTML = $oHTML->getBlocStart('', array('class' =>'listCp_row '));
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'listCp_row_data'));
      $sHTML.= $oHTML->getBlocStart('', array('class' =>'leftMedium'));

      //fetch sortorder from the history
      $asOrder = $this->_getHistorySearchOrder($psSearchId, $this->csUid, CONST_AB_TYPE_COMPANY);
      $sSortField = strtolower($asOrder['sortfield']);
      $sSortOrder = strtolower($asOrder['sortorder']);

      $sUrl = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_LIST, CONST_AB_TYPE_COMPANY, 0, array('searchId' => $psSearchId));

      $sHTML.= '<input type="checkbox" />';
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('cpName', array('class' => 'ab_list_cell cp_list_cell cp_search','sort_name'=>'company_name','style' =>'width:12%; padding-left: 2px;'));
      $sHTML.= $oHTML->getText('Name');
      $sHTML.= $oHTML->getSpace(2);

      if($sSortField == 'company_name' && $sSortOrder == 'asc')
        $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/up_orange.png', 'A - Z', '', array('class'=>'moveupCp'));
      else
      {
        $sSortUrl = $sUrl.'&sortfield=company_name&sortorder=asc';
        $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/up.png', 'A - Z', '', array('class'=>'moveupCp '));
        $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
      }

      if($sSortField == 'company_name' && $sSortOrder == 'desc')
        $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/down_orange.png', 'Z - A','',array('class'=>'movedownCp'));
      else
      {
        $sSortUrl = $sUrl.'&sortfield=company_name&sortorder=desc';
        $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/down.png', 'Z - A', '', array('class'=>'movedownCp'));
        $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
      }
      $sHTML.= $oHTML->getBlocEnd();


      $sHTML.= $oHTML->getBlocStart('', array('class' => 'cp_list_cell','style' =>'width:25%;'));
      $sHTML.= $oHTML->getText('Account Manager');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('cpIndustry', array('class' => ' ab_list_cell cp_list_cell cp_search','sort_name'=>'industry_name','style' =>'width:15%;'));
      $sHTML.= $oHTML->getText('Industry');
      $sHTML.= $oHTML->getSpace(2);

      if($sSortField == 'industry' && $sSortOrder == 'asc')
        $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/up_orange.png', 'A - Z', '', array('class'=>'moveupCp'));
      else
      {
        $sSortUrl = $sUrl.'&sortfield=industry&sortorder=asc';
        $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/up.png', 'A - Z', '', array('class'=>'moveupCp'));
        $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
      }

      if($sSortField == 'industry' && $sSortOrder == 'desc')
        $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/down_orange.png', 'Z - A','',array('class'=>'movedownCp'));
      else
      {
        $sSortUrl = $sUrl.'&sortfield=industry&sortorder=desc';
        $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/down.png', 'Z - A', '', array('class'=>'movedownCp'));
        $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
      }

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'ab_list_cell cp_list_cell', 'style' =>'width:20%;float:left;'));
      $sHTML.= $oHTML->getText('Recent activity');

      if($sSortField == 'activity' && $sSortOrder == 'asc')
        $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/up_orange.png', 'Oldest First', '', array('class'=>'moveupCp'));
      else
      {
        $sSortUrl = $sUrl.'&sortfield=activity&sortorder=asc';
        $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/up.png', 'Oldest First', '', array('class'=>'moveupCp'));
        $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
      }


      if($sSortField == 'activity' && $sSortOrder == 'desc')
        $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/down_orange.png', 'Recent First','',array('class'=>'movedownCp'));
      else
      {
        $sSortUrl = $sUrl.'&sortfield=activity&sortorder=desc';
        $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/down.png', 'Recent First', '', array('class'=>'movedownCp'));
        $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
      }

      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'cp_list_cell', 'style' =>'float:right;'));
      $sHTML.= $oHTML->getText('Action');
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('', array('class' =>'floatHack'));
      $sHTML.= $oHTML->getBlocEnd();

      return $sHTML;
  }

  /**
   * Return a company list row line with company details
   * @param type $asCompanyData
   * @param type $nRow
   * @return type string(html)
   */

  private function _getCompanyRow($pasCompanyData, $pnRow)
  {

    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');

    if(!assert('is_array($pasCompanyData) && !empty($pasCompanyData)'))
      return 'No company found.';

    $sId = 'id_'.$pasCompanyData['companypk'];

    if(($pnRow%2) == 0)
      $sRowClass = '';
    else
      $sRowClass = 'list_row_data_odd';

    if(!empty($pasCompanyData['child_pk']))
      $sChildClass = ' list_smaller_row ';
    else
      $sChildClass = '';

    $sHTML= $oHTML->getBlocStart($sId, array('class' =>'list_row '.$sChildClass));
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_row_data '.$sRowClass));

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'leftMedium '.$sRowClass));
    $sHTML.= '<input type="checkbox" />';
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell cp_list_name '.$sRowClass, 'style' =>'width:20%; padding-left: 2px;'));
    $sCompanyRelation = getCompanyRelation($pasCompanyData['company_relation']);
    $sHTML.= $oHTML->getBlocStart('',array('class' => 'imgClass '.$sRowClass));
    $sHTML.= $oHTML->getPicture($this->getResourcePath().'/pictures/'.$sCompanyRelation['icon_small'], 'Relation', '');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $this->_getCompanyRow_companyName($pasCompanyData);
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell '.$sRowClass,'style' =>'width:21%;'));
    $sHTML.= $this->_getCompanyRow_accountManager($pasCompanyData);
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell '.$sRowClass,'style' =>'width:17%;'));
    $sHTML.= $this->_getCompanyRow_IndustryInfo($pasCompanyData);
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell '.$sRowClass, 'style' =>'width:20%;float:left;'));
    $sHTML.= $this->_getCompanyRow_companyActivity($pasCompanyData);
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell '.$sRowClass, 'style' =>'float:right;'));
    $sHTML.= $this->_getCompanyRow_companyAction($pasCompanyData);
    $sHTML.= $oHTML->getBlocEnd();

    //display child companies links
    if(!empty($pasCompanyData['child_pk']))
    {
      /*@var $oPage CPageEx */
      $oPage = CDependency::getComponentByName('page');

      $sHTML.= $oHTML->getBlocStart('', array('style' =>'float:left; width: 80%; margin: 0 0 5px 35px; padding: 2px 5px; border-left: 2px solid #ccc; '));
      $sHTML.= $oHTML->getText('Child company: ', array('style' => 'cursor: help;', 'title' => 'Display the child/subsidery companies.'));

        $asChildCompany = explode(',', $pasCompanyData['child_pk']);
        $asChildCpName = explode(',', $pasCompanyData['child_name']);

        foreach($asChildCompany as $nKey => $sPk)
        {
          if(!isset($asChildCpName[$nKey]))
            $asChildCpName[$nKey] = ' ## ';

          $sUrl = $oPage->getUrl($this->csUid, CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, (int)$sPk);
          $asChildCompany[$nKey] = $oHTML->getLink($asChildCpName[$nKey], $sUrl);
        }

        $sHTML.= implode(', ', $asChildCompany);

      $sHTML.= $oHTML->getBlocEnd();
    }


    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getFloatHack();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Get the  name while listing company records
   * @param array $asCompanyData
   * @return string HTML
   */

  private function _getCompanyRow_companyName($asCompanyData)
  {
    if(!assert('is_array($asCompanyData) && !empty($asCompanyData)'))
      return 'Can not find the company';

    $nCompanyPK = (int)$asCompanyData['companypk'];

    if(isset($asCompanyData['company_name']) && !empty($asCompanyData['company_name']))
      $asCompany[] = $asCompanyData['company_name'];

    if(isset($asCompanyData['corporate_name']) && !empty($asCompanyData['corporate_name']))
      $asCompany[] = $asCompanyData['corporate_name'];

    $nParentfk = 0;
    if(isset($asCompanyData['parentfk']) && !empty($asCompanyData['parentfk']))
      $nParentfk = (int)$asCompanyData['parentfk'];

    if(!assert('!empty($asCompany)'))
       return 'Can not find the company';

    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $sHTML = '';


    $sURL = $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $nCompanyPK);
    $sHTML.= $oHTML->getLink($asCompany[0], $sURL, array('class' => 'h4'));

    if(isset($asCompany[1]))
      $sHTML.= $oHTML->getCarriageReturn() . $oHTML->getLink($asCompany[1], $sURL, array('class' => 'h5'));

    if(!empty($nParentfk))
    {
      $sHTML.= $oHTML->getCarriageReturn();
      $sHTML.= $oHTML->getBlocStart('', array('style' => 'margin: 3px 0 5px 0; padding: 2px 5px; border-left: 2px solid #ccc;'));
        $sURL = $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $nParentfk);
        $sHTML.= $oHTML->getText('Holding: ', array('class' => 'text_normal text_small ', 'style' =>'cursor: help;',  'title' => 'Display the current holding company.'));
        $sHTML.= $oHTML->getLink($asCompanyData['parent_company'], $sURL);
      $sHTML.= $oHTML->getBlocEnd();
    }

    return $sHTML;
  }

/**
 * Display the full name of the account manager of the company
 * @param array $pasCompanyData
 * @return string HTML
 */

  private function _getCompanyRow_accountManager($pasCompanyData)
  {
    if(!assert('is_array($pasCompanyData) && !empty($pasCompanyData)'))
     return '';

    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');

    $sHTML = $oLogin->getUserAccountName($pasCompanyData['userlastname'],$pasCompanyData['userfirstname']);

    return $sHTML;
  }

  /**
   * Display the activity of the company
   * @param array $asCompanyData
   * @return string HTML
   */

  private function _getCompanyRow_companyActivity($pasCompanyData)
  {
    //dump($pasCompanyData);

    if(!assert('is_array($pasCompanyData) && !empty($pasCompanyData)'))
     return '';

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = '';

    if(!empty($pasCompanyData['title']))
       $sEventTitle = $pasCompanyData['title'];
    else
       $sEventTitle = '';

    if(!empty($pasCompanyData['content']))
       $sEventContent = $oHTML->getExtract(strip_tags($pasCompanyData['content']),200);
    else
       $sEventContent = '';

    if(!empty($pasCompanyData['date_display']))
       $sDateDisplay = $pasCompanyData['date_display'];
    else
       $sDateDisplay = '';

    if($pasCompanyData['itemtype']=='ct')
    {
        $nContactPk = $pasCompanyData['itempk'];
        $asContactDetails = $this->getContactByPk((int)$nContactPk);
     }

     if(!empty($asContactDetails))
       $sEvent = '<strong>Latest Activity on '.$asContactDetails['firstname'].' '.$asContactDetails['lastname'].'</strong><br/>';
      else
       $sEvent = '';

     if(!empty($sEventTitle))
       $sEvent.= $sEventTitle.'<br/>';

      $sEvent.= $sEventContent;


    if(!empty($sEvent))
    {
     $sHTML.= $oHTML->getBlocStart('',array('style'=>'float:left;width:40px;'));
     $sHTML.= $oHTML->getText(date('m/y',strtotime($sDateDisplay)));
     $sHTML.= $oHTML->getBlocEnd();

     $sHTML.= $oHTML->getBlocStart('',array('class' => 'imgClass  activityClass','title'=>$sEvent));
     $sHTML.= $oHTML->getPicture($this->getResourcePath().'/pictures/list_event.png', 'Activities', '',array('onmouseover'=>'showActivityPopup(this);','onmouseout'=>"hideActivityPopup();"));
     $sHTML.= $oHTML->getBlocEnd();
    }
    else
     $sHTML.=  $oHTML->getText('-', array('class' => 'light italic spanCenteredCompany'));

    return $sHTML;

  }

  /**
   * Display the action buttons for company records
   * @param array $asCompanyData
   * @return string HTML
   */

  private function _getCompanyRow_companyAction($asCompanyData)
  {
     if(!assert('is_array($asCompanyData) && !empty($asCompanyData)'))
      return '';

    $oEvent = CDependency::getComponentUidByName('event');

    $oRight = CDependency::getComponentByName('right');
    $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_DELETE,$this->getType(),0);
     /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oEvent = CDependency::getComponentByName('event');

    $nCompanyPk = (int)$asCompanyData['companypk'];
    $sHTML = '';
    $sPic = $this->getResourcePath().'/pictures/ct_add_16.png';
    $sURL = $oPage->getUrl('addressbook', CONST_ACTION_ADD, CONST_AB_TYPE_CONTACT, 0, array('cppk' => $nCompanyPk));
    $sHTML.= $oHTML->getPicture($sPic, 'Add connection', $sURL);
    $sHTML.= $oHTML->getSpace(2);

    if(!empty($oEvent))
    {
     $sUrl = $oPage->getUrl('event', CONST_ACTION_ADD, CONST_EVENT_TYPE_EVENT, 0, array(CONST_EVENT_ITEM_UID => $this->_getUid(), CONST_EVENT_ITEM_ACTION => CONST_ACTION_VIEW, CONST_EVENT_ITEM_TYPE => CONST_AB_TYPE_COMPANY, CONST_EVENT_ITEM_PK => $nCompanyPk));
     $sHTML.= $oHTML->getLink($oHTML->getPicture($oEvent->getResourcePath().'pictures/add_event_16.png', 'Add activity'),$sUrl,array('title'=>'Add activity'));
     $sHTML.= $oHTML->getSpace(2);
     }

     $sURL = $oPage->getUrl('addressbook', CONST_ACTION_EDIT, CONST_AB_TYPE_COMPANY, $nCompanyPk, array(CONST_URL_ACTION_RETURN => CONST_ACTION_LIST));
     $sHTML.= $oHTML->getPicture(CONST_PICTURE_EDIT, 'Edit company', $sURL);
     $sHTML.= $oHTML->getSpace(2);

    if($sAccess)
    {
     $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_DELETE, CONST_AB_TYPE_COMPANY, $nCompanyPk);
     $sPic= $oHTML->getPicture(CONST_PICTURE_DELETE,'Delete company');
     $sHTML.= ' '.$oHTML->getLink($sPic, $sURL, array('onclick' => 'if(!window.confirm(\'Delete this company ?\')){ return false; }'));
    }
    return $sHTML;
  }

  /**
   * Display the form to add/edit the company details
   * @param integer $pnPK
   * @return string HTML
   */

  private function _getCompanyForm($pnPK)
  {
    if(!assert('is_integer($pnPK)'))
      return '';

    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oLogin = CDependency::getComponentByName('login');
    $oPage->addCssFile(array($this->getResourcePath().'css/addressbook.css'));

    $oDB = CDependency::getComponentByName('database');
    $oDB->dbConnect();

    $oRight = CDependency::getComponentByName('right');
    $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_TRANSFER,$this->getType(),0);

    $sQuery = 'SELECT * from industry order by industry_name asc';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $asIndustries = array();
    $asSelectIndustry = array();
    while($bRead)
    {
      $asIndustries[] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
     }
     $asSelectManager = array();
    //If editing the company
    if(!empty($pnPK))
    {
      $sQuery = 'SELECT cp.*, l.lastname as follower_lastname, l.firstname as follower_firstname FROM `company` as cp';
      $sQuery.= ' LEFT JOIN login as l ON (l.loginpk = cp.followerfk) ';
      $sQuery.= ' WHERE companypk = '.$this->cnPk.' ';

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();

      if(!$bRead)
        return __LINE__.' - The company doesn\'t exist.';

      $sQuery = 'SELECT industryfk FROM company_industry WHERE companyfk='.$pnPK;
      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();

      $asSelectIndustry = array();
      while($bRead)
      {
        $asSelectIndustry[] = $oResult->getFieldValue('industryfk');
        $bRead = $oResult->readNext();
       }
     }
    else
      $oDbResult = new CDbResult();

    if($oPage->getActionReturn())
      $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SAVEADD, CONST_AB_TYPE_COMPANY, $pnPK, array(CONST_URL_ACTION_RETURN => $oPage->getActionReturn()));
    else
      $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SAVEADD, CONST_AB_TYPE_COMPANY,$pnPK);

    $sHTML= $oHTML->getBlocStart();

    //div including the form
    $sHTML.= $oHTML->getBlocStart('');

    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('cpAddForm');
    $sFormId = $oForm->getFormId();
    $oForm->setFormParams('', true, array('submitLabel' => 'Save','action' => $sURL));

    $sHTML.= $oHTML->getBlocStart('',array('style'=>'border:1px solid #CECECE;float:right;width:300px;font-size:13px;position:absolute;top:165px;right:60px;background-color:#FAF9FB;padding:10px;border-radius:20px;'));
    $sHTML.= $oHTML->getText('Synopsis should include the following points',array('class'=>'h4'));
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= $oHTML->getText('1. What kind of media buying has the company done in the past (print, digital)');
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= $oHTML->getText('2. Does it have a sophisticated understanding of media ');
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= $oHTML->getText('3. Would they be interested to purchase; SEO, wed design, ad campaigns, co-branded weekender, etc. ');
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= $oHTML->getText('4. What would they like to see change in the Weekender ');
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= $oHTML->getText('5. What are the other ways in which we can work with them?');
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= $oHTML->getText('6. Are they high potential client? ');
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= $oHTML->getText('7. Are they a potential collaborator? ');
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= $oHTML->getBlocEnd();

    $oForm->addField('misc', '', array('type' => 'text','text'=> '<br/><span class="h4">Company details</span><hr />'));
    $oForm->addField('input', 'doubleChecked', array('type' => 'hidden', 'value' => (int)!empty($pnPk), 'id' => 'doubleCheckedId'));

    $oForm->addField('input', 'name', array('label'=>'<strong>Public Name</strong>', 'class' => '', 'value' => $oDbResult->getFieldValue('company_name')));
    $oForm->setFieldControl('name', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255, 'jsFieldNotEmpty' => ''));

    $oForm->addField('input', 'corporate', array('label'=> 'Legal Name', 'value' => $oDbResult->getFieldValue('corporate_name')));
    $oForm->setFieldControl('corporate', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255));

    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SEARCH, CONST_AB_TYPE_COMPANY);
    $oForm->addField('selector', 'parent', array('label'=> 'Holding company', 'url' => $sURL));
    $nParentFk = $oDbResult->getFieldValue('parentfk', CONST_PHP_VARTYPE_INT);
    if($nParentFk > 0)
    {
      $asCompanyData = $this->getCompanyByPk($nParentFk);
      $sLabel = $asCompanyData['company_name'];
      if(isset($asCompanyData['corporate_name']))
        $sLabel.= ' - '.$asCompanyData['corporate_name'];

      $oForm->addOption('parent', array('label' => $sLabel, 'value' => $nParentFk));
    }

    $oForm->addField('select', 'industries[]', array('label' => 'Industries', 'multiple' => 'multiple'));
    $oForm->setFieldControl('industries[]', array('jsFieldNotEmpty' => ''));

    foreach($asIndustries as $asIndustryData)
    {
      if(in_array($asIndustryData['industrypk'],$asSelectIndustry))
      $oForm->addOption('industries[]', array('value'=>$asIndustryData['industrypk'], 'label' => $asIndustryData['industry_name'], 'selected' => 'selected'));
      else
       $oForm->addOption('industries[]', array('value'=>$asIndustryData['industrypk'], 'label' => $asIndustryData['industry_name']));
     }

    if($sAccess || empty($pnPK))
    {
    $oForm->addField('select', 'account_manager[]', array('label' => 'Account Manager', 'multiple' => 'multiple'));
    $oForm->setFieldControl('account_manager[]', array('jsFieldNotEmpty' => ''));

    $asManagers = $oLogin->getUserList(0,false,true);

    if($oDbResult->getFieldValue('companypk'))
     $asSelectManager = $this->_getAccountManager($oDbResult->getFieldValue('companypk'),'cp');
    else
     $asSelectManager = $this->_getAccountManager('', '');
    foreach($asManagers as $asManagerData)
    {
      if(in_array($asManagerData['loginpk'],$asSelectManager))
        $oForm->addOption('account_manager[]', array('value'=>$asManagerData['loginpk'], 'label' => $asManagerData['firstname'].' '.$asManagerData['lastname'], 'selected' => 'selected'));
      else
       $oForm->addOption('account_manager[]', array('value'=>$asManagerData['loginpk'], 'label' => $asManagerData['firstname'].' '.$asManagerData['lastname']));
     }
    }
    // Drop down for the company relation
    $oForm->addField('select', 'type', array('label' => 'Company Type'));
    $oForm->setFieldControl('type', array('jsFieldNotEmpty' => ''));
    $asCompanyRel= getCompanyRelation();
    $oForm->addOption('type', array('value'=>'', 'label' => 'Select'));
    foreach($asCompanyRel as $sType=>$vType)
    {
      if($sType == $oDbResult->getFieldValue('company_relation'))
      $oForm->addOption('type', array('value'=>$sType, 'label' => $vType['Label'],'selected'=>'selected'));
      else
      $oForm->addOption('type', array('value'=>$sType, 'label' => $vType['Label']));
    }
    $oForm->addField('textarea', 'comments', array('label'=> 'Synopsis ', 'value' =>$oDbResult->getFieldValue('comments')));
    $oForm->setFieldControl('comments', array('jsFieldMinSize' => 5));

    $oForm->addField('misc', '', array('type' => 'title','title'=> '<br/><span class="h4">Contact information</span><hr /><br />'));
    $oForm->addField('input', 'email', array('label'=> 'Email', 'value' => $oDbResult->getFieldValue('email')));
    $oForm->setFieldControl('email', array('jsFieldTypeEmail' => ''));

    $oForm->addField('input', 'website', array('label'=> 'Website', 'value' => $oDbResult->getFieldValue('website')));
    $oForm->setFieldControl('website', array('jsFieldTypeUrl' => ''));

    $oForm->addField('input', 'phone', array('label'=> 'Phone', 'value' => $oDbResult->getFieldValue('phone')));
    $oForm->addField('input', 'fax', array('label'=> 'Fax', 'value' => $oDbResult->getFieldValue('fax')));
    $oForm->addField('misc', '', array('type'=> 'br'));
    $oForm->addField('input', 'address_1', array('label'=> 'Adress 1', 'value' => $oDbResult->getFieldValue('address_1')));
    $oForm->addField('input', 'address_2', array('label'=> 'Adress 2', 'value' => $oDbResult->getFieldValue('address_2')));
    $oForm->addField('input', 'postcode', array('label'=> 'Postcode', 'value' => $oDbResult->getFieldValue('postcode')));

    $oForm->addField('selector_city', 'cityfk', array('label'=> 'City', 'url' => CONST_FORM_SELECTOR_URL_CITY));
    $oForm->setFieldControl('cityfk', array('jsFieldTypeIntegerPositive' => ''));
    $nCityFk = $oDbResult->getFieldValue('cityfk', CONST_PHP_VARTYPE_INT);
    if(!empty($nCityFk))
      $oForm->addCitySelectorOption('cityfk', $nCityFk);

    $oForm->addField('selector_country', 'countryfk', array('label'=> 'Country', 'url' => CONST_FORM_SELECTOR_URL_COUNTRY));
    $oForm->setFieldControl('countryfk', array('jsFieldTypeIntegerPositive' => ''));
    $nCountryFk = $oDbResult->getFieldValue('countryfk', CONST_PHP_VARTYPE_INT);

    if(!empty($nCountryFk))
      $oForm->addCountrySelectorOption('countryfk', $nCountryFk);
    else
       $oForm->addCountrySelectorOption('countryfk',107);

    $oForm->addField('misc', '', array('type'=> 'br'));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Find the account manager
   * @param integer $pnItemPk
   * @param string $psType
   * @return array of account manager data
   */

  private function _getAccountManager($pnItemPk,$psType)
  {
    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');
     if(empty($pnItemPk))
     {
         $asSelectManager[] = $oLogin->getUserPk();
        return  $asSelectManager;
     }
     else
     {
     if($psType == CONST_AB_TYPE_COMPANY)
     {
        $sQuery= 'SELECT * FROM account_manager where companyfk='.$pnItemPk;
        $oResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oResult->readFirst();
        while($bRead)
        {
           $asSelectManager[] = $oResult->getFieldValue('loginfk');
           $bRead = $oResult->readNext();
        }

        $sQuery= 'SELECT followerfk FROM company where companypk='.$pnItemPk;
        $oResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oResult->readFirst();
        while($bRead)
        {
          $asSelectedManager[] = $oResult->getFieldValue('followerfk');
          $bRead = $oResult->readNext();
        }

        if(!empty($asSelectManager))
           $asSelectedManager = array_merge ($asSelectedManager,$asSelectManager) ;

        return $asSelectedManager;
      }
    else if($psType == CONST_AB_TYPE_CONTACT)
     {
        $sQuery= 'SELECT * FROM account_manager where contactfk='.$pnItemPk;
        $oResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oResult->readFirst();
        while($bRead)
        {
           $asSelectManager[] = $oResult->getFieldValue('loginfk');
           $bRead = $oResult->readNext();
        }

        $sQuery= 'SELECT followerfk FROM contact where contactpk='.$pnItemPk;
        $oResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oResult->readFirst();
        while($bRead)
        {
          $asSelectedManager[] = $oResult->getFieldValue('followerfk');
          $bRead = $oResult->readNext();
        }

        if(!empty($asSelectManager))
           $asSelectedManager = array_merge ($asSelectedManager,$asSelectManager) ;

        return $asSelectedManager;
        }
     }
  }

  /**
   * Save the company information
   * @param integer $pnPK
   * @return string
   */
  private function _getCompanySave($pnPK = 0)
  {
    if(!assert('is_integer($pnPK)'))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');

    $sCompanyName = getValue('name');
    $sCorporateName = getValue('corporate');
    $sEmail = getValue('email');
    $sWebsite = getValue('website');
    $sPhone = getValue('phone');
    $sFax = getValue('fax');
    $sAddress = getValue('address_1');
    $sComments = getValue('comments');
    $asFollowers = getValue('account_manager');
    $asIndustry = getValue('industries');
    $sCompanyRelation = getValue('type');
    $bDoubleEntryControl = (bool)getValue('doubleChecked', 0);

    if(empty($pnPK)&& $bDoubleEntryControl==0)
      $sPopupHtml= $this->_getCheckDuplicates('cp',$sEmail,$sCompanyName,$sCorporateName,$sAddress,$sPhone);

    if(!empty($sPopupHtml))
      return array('action' => ' setPopup("'.$sPopupHtml.'", "", "", 0, 450); ');

    //TODO: check parameters !!!!
    if(!empty($sEmail) && !isValidEmail($sEmail))
      return array('error' => 'The website url is not valid. ');

    if(!empty($sWebsite))
    {
      $sWebsite = formatUrl($sWebsite);
      $nUrlCheck = isValidUrl($sWebsite, false, true);

      if($nUrlCheck === 0)
        return array('error' => 'The url is not valid.');
      elseif($nUrlCheck === -1)
        return array('error' => 'The url points towards a site that is not responding.');
    }

    //TODO: check parameters !!!!
    //TODO: check parameters !!!!
    //TODO: check parameters !!!!
    //TODO: check parameters !!!!
    //TODO: check parameters !!!!

    //Editing the company
    if(!empty($pnPK))
    {
      $sQuery = 'SELECT * FROM `company` WHERE companypk = '.$pnPK.' ';

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
        return array('error' => __LINE__.' - Company doesn\'t exist.');

      $asCompanyData = $oDbResult->getData();
      $bCascading = (bool)getValue('cascading', false);

      $sCompanyName = getValue('name');

      $sQuery = 'UPDATE company SET  ';
      $sQuery.= ' company_name = '.$oDB->dbEscapeString($sCompanyName).', ';
      $sQuery.= ' corporate_name = '.$oDB->dbEscapeString(getValue('corporate')).', ';
      $sQuery.= ' email = '.$oDB->dbEscapeString(getValue('email')).', ';
      $sQuery.= ' parentfk = '.$oDB->dbEscapeString(getValue('parent', 0)).', ';
      $sQuery.= ' phone = '.$oDB->dbEscapeString(getValue('phone')).', ';
      $sQuery.= ' fax = '.$oDB->dbEscapeString(getValue('fax')).', ';
      $sQuery.= ' website = '.$oDB->dbEscapeString($sWebsite).', ';
      $sQuery.= ' address_1 = '.$oDB->dbEscapeString(getValue('address_1')).', ';
      $sQuery.= ' address_2 = '.$oDB->dbEscapeString(getValue('address_2')).', ';
      $sQuery.= ' postcode = '.$oDB->dbEscapeString(getValue('postcode')).', ';
      $sQuery.= ' cityfk = '.$oDB->dbEscapeString(getValue('cityfk', 0)).', ';
      $sQuery.= ' countryfk = '.$oDB->dbEscapeString(getValue('countryfk', 0)).', ';
      $sQuery.= ' date_update = '.$oDB->dbEscapeString(date('Y-m-d H:i:s')).',';
      $sQuery.= ' company_relation = '.$oDB->dbEscapeString($sCompanyRelation).',';
      $sQuery.= ' updated_by = '.$oDB->dbEscapeString($oLogin->getUserPk()).',';
      $sQuery.= ' comments = '.$oDB->dbEscapeString($sComments).'';
      $sQuery.= ' WHERE companypk = '.$pnPK.' ';

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      if(!$oDbResult)
        return array('error' => __LINE__.' - Cant edit the company');

      if(isset($asFollowers)&& !empty($asFollowers))
      {
        $nFollowerFk = (int)$asFollowers[0];
        $sQuery = ' UPDATE company SET followerfk ='.$nFollowerFk.' WHERE companypk = '.$pnPK;

        $oDB->ExecuteQuery($sQuery);
        array_shift($asFollowers);
        $sQuery = 'DELETE FROM account_manager WHERE companyfk='.$pnPK;
        $oDB->ExecuteQuery($sQuery);

        $asManagerQuery = array();
        foreach($asFollowers as $asManagerData)
        {
          $asManagerQuery[] = '('.$pnPK.','.$asManagerData.')';
        }

        if(!empty($asManagerQuery))
        {
          $sQuery = 'INSERT INTO account_manager(companyfk,loginfk) VALUES ';
          $sQuery.= implode(',',$asManagerQuery);
          $oDB->ExecuteQuery($sQuery);
        }
      }
      $sQuery = 'DELETE FROM company_industry WHERE companyfk='.$pnPK;
      $oDB->ExecuteQuery($sQuery);
      $sMysqlQuery = array();

      foreach($asIndustry as $asIndustryData)
      {
        $sMysqlQuery[] = '('.$pnPK.','.$asIndustryData.')';
      }

      if(!empty($sMysqlQuery))
      {
        $sQuery = 'INSERT INTO company_industry(companyfk,industryfk) VALUES';
        $sQuery.= implode(',',$sMysqlQuery);
        $oDB->ExecuteQuery($sQuery);
      }

      if($bCascading && (int)$asCompanyData['followerfk'] != $nFollowerFk)
      {
        $bUpdated = $this->_updateEmployeesFollower($pnPK, $nFollowerFk, (int)$asCompanyData['followerfk']);
        if(!$bUpdated)
          return array('error' => __LINE__.' - Can\'t update contact follower');
      }
      $nCompanyPk = $pnPK;

       /* @var $oPage CPageEx */
     $oPage = CDependency::getComponentByName('page');

      if($oPage->getActionReturn())
        $sURL = $oPage->getUrl('addressbook', $oPage->getActionReturn(), CONST_AB_TYPE_COMPANY, $nCompanyPk);
      else
        $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $nCompanyPk);


      $sLink = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $nCompanyPk);
      $oLogin->getUserActivity($oLogin->getUserPk(), $this->_getUid(), CONST_ACTION_SAVEEDIT, CONST_AB_TYPE_COMPANY, $nCompanyPk, '[upd] '.$sCompanyName, $sLink);

      return array('notice' => 'Company saved successfully.', 'timedUrl' => $sURL);
    }

    /* @var $oLogin CLoginEx */
    $oLogin = CDependency::getComponentByName('login');
    $nUserPk = $oLogin->getUserPk();

    $nFollowerFk = (int)$asFollowers[0];
    if(empty($nFollowerFk))
      $nFollowerFk = $nUserPk;

    $sCompanyName = getValue('name');

    $sQuery = 'INSERT INTO company (company_name, corporate_name, email, parentfk, followerfk, phone, fax, website, address_1, address_2, postcode, cityfk, countryfk, creatorfk, date_create,company_relation,comments,updated_by) ';
    $sQuery.= ' VALUES('.$oDB->dbEscapeString($sCompanyName).', ';
    $sQuery.= ''.$oDB->dbEscapeString(getValue('corporate')).', ';
    $sQuery.= ''.$oDB->dbEscapeString(getValue('email')).', ';
    $sQuery.= ''.$oDB->dbEscapeString(getValue('parent', 0)).', ';
    $sQuery.= ''.$oDB->dbEscapeString($nFollowerFk).', ';
    $sQuery.= ''.$oDB->dbEscapeString(getValue('phone')).', ';
    $sQuery.= ''.$oDB->dbEscapeString(getValue('fax')).', ';
    $sQuery.= ''.$oDB->dbEscapeString($sWebsite).', ';
    $sQuery.= ''.$oDB->dbEscapeString(getValue('address_1')).', ';
    $sQuery.= ''.$oDB->dbEscapeString(getValue('address_2')).', ';
    $sQuery.= ''.$oDB->dbEscapeString(getValue('postcode')).', ';
    $sQuery.= ''.$oDB->dbEscapeString(getValue('cityfk', 0)).', ';
    $sQuery.= ''.$oDB->dbEscapeString(getValue('countryfk', 0)).', ';
    $sQuery.= ''.$oDB->dbEscapeString($nUserPk).', ';
    $sQuery.= ''.$oDB->dbEscapeString(date('Y-m-d H:i:s')).',';
    $sQuery.= ''.$oDB->dbEscapeString(getValue('type')).',';
    $sQuery.= ''.$oDB->dbEscapeString($sComments).',';
    $sQuery.= ''.$oDB->dbEscapeString($nUserPk).'';
    $sQuery.= ') ';

    $oDbResult = $oDB->ExecuteQuery($sQuery);

    if(!$oDbResult || !$oDbResult->getFieldValue('pk'))
      return array('error' =>__LINE__.' - Can\'t save company. '.$sQuery);

    $nCompanyPk = (int)$oDbResult->getFieldValue('pk');
    array_shift($asFollowers);
    foreach($asFollowers as $asManagerData)
    {
      $sQuery = 'INSERT INTO account_manager(companyfk,loginfk) VALUES('.$pnPK.','.$asManagerData.')';
      $oDB->ExecuteQuery($sQuery);
    }

    $sMysqlQuery = array();
    foreach($asIndustry as $asIndustryData)
    {
      $sMysqlQuery[] = '('.$nCompanyPk.','.$asIndustryData.')';
    }

    if(!empty($sMysqlQuery))
    {
      $sQuery = 'INSERT INTO company_industry(companyfk,industryfk) VALUES';
      $sQuery.= implode(',',$sMysqlQuery);
      $oDB->ExecuteQuery($sQuery);
    }

    /* @var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $sURL = $oPage->getUrl('addressbook',CONST_ACTION_ADD, CONST_AB_TYPE_CONTACT, 0, array('cppk'=>$nCompanyPk));

    $sLink = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $nCompanyPk);
    $oLogin->getUserActivity($oLogin->getUserPk(), $this->_getUid(), CONST_ACTION_SAVEADD, CONST_AB_TYPE_COMPANY, $nCompanyPk, '[new] '.$sCompanyName, $sLink);

    return array('notice' => 'Company saved. Please add connection.', 'timedUrl' => $sURL);
  }

  /*
   * When updating a company follower, we apply the follower modification to all the employees
   * If previous follower specified, only to contact having this follower (keep custom contact followers).
   * @return boolean
   */
  private function _updateEmployeesFollower($pnCompanyPK, $pnNewFollowerFk, $pnPreviousFollowerFk = 0)
  {
    if(!assert('is_integer($pnCompanyPK) && !empty($pnCompanyPK) && is_integer($pnNewFollowerFk) && !empty($pnNewFollowerFk)'))
      return false;

    $oDB = CDependency::getComponentByName('database');

    $sQuery = ' SELECT ct.contactpk FROM contact as ct ';
    $sQuery.= ' INNER JOIN profil as p ON (p.contactfk  = ct.contactpk AND p.companyfk = '.$oDB->dbEscapeString($pnCompanyPK).') ';
    if(!empty($pnPreviousFollowerFk))
      $sQuery.= ' WHERE followerfk = 0 OR followerfk = '.$oDB->dbEscapeString($pnPreviousFollowerFk);

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return true;

    $asContactToUpdate = array();
    while($bRead)
    {
      $asContactToUpdate[] = $oDbResult->getFieldValue('contactpk', CONST_PHP_VARTYPE_INT);
      $bRead = $oDbResult->readNext();
    }

    if(empty($asContactToUpdate))
      return true;

    $sQuery = 'UPDATE contact SET followerfk = '.$oDB->dbEscapeString($pnNewFollowerFk).', date_update = '.$oDB->dbEscapeString(date('Y-m-d H:i:s')).' ';
    $sQuery.= ' WHERE contactpk IN ('.implode(',',$asContactToUpdate).' ) ';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    if(!$oDbResult)
      return false;

    return true;
  }

  /**
   * Remove the company
   * @param integer $pnPK
   * @return array
   */

  private function _getCompanyDelete($pnPK)
  {
    if(!assert('is_integer($pnPK) && !empty($pnPK)'))
      return array('error' => __LINE__.' - No company identifier.');

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM `company` WHERE companypk = '.$pnPK.' ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array('error' => __LINE__.' - No company to delete.');

    $sQuery = 'DELETE FROM company WHERE companypk = '.$pnPK.' ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    if(!$oDbResult)
      return array('error' => __LINE__.' - Could\'t delete the company');

    $oPage = CDependency::getComponentByName('page');
    return array('notice' => 'Company deleted.', 'timedUrl' => $oPage->getUrl('addressbook', CONST_ACTION_LIST, CONST_AB_TYPE_COMPANY));
  }

  /**
   * Fetch all data of the Pked company(ies)
   * @param variant $pvCompanyPk
   * @return array of company data
   */
  public function getCompanyByPk($pvCompanyPk)
  {
    if(!assert('(is_integer($pvCompanyPk) || is_array($pvCompanyPk)) && !empty($pvCompanyPk)'))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $oDB->dbConnect();

    $bIsArray = is_array($pvCompanyPk);

    if($bIsArray)
      $sQuery = 'SELECT * FROM `company` WHERE companypk IN ('.implode(',', $pvCompanyPk).') ';
    else
      $sQuery = 'SELECT * FROM `company` WHERE companypk = '.$pvCompanyPk;

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array();

    if(!$bIsArray)
      return $oDbResult->getData();

    $asResult = array();
    while($bRead)
    {
      $asResult[$oDbResult->getFieldValue('companypk')] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asResult;
  }

  /**
   * Get the Company in the autocomplete
   * @return jsondata
   */

  private function _getSelectorCompany()
  {
    $sSearch = getValue('q');
    if(empty($sSearch))
      return json_encode(array());

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM company WHERE lower(company_name) LIKE '.$oDB->dbEscapeString('%'.strtolower($sSearch).'%').' OR lower(corporate_name) LIKE '.$oDB->dbEscapeString('%'.strtolower($sSearch).'%').' ORDER BY company_name ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return json_encode(array());

    $asJsonData = array();
    while($bRead)
    {
      $asData['id'] = $oDbResult->getFieldValue('companypk');
      $asData['name'] = '#'.$asData['id'].' - '.$oDbResult->getFieldValue('company_name').' - '.$oDbResult->getFieldValue('corporate_name');
      $asJsonData[] = json_encode($asData);
      $bRead = $oDbResult->readNext();
    }
    echo '['.implode(',', $asJsonData).']';
  }

  /**
   * Display the company account manager transfer form
   * @param integer $pnCompanyPk
   * @return array of ajax data
   */

  private function _getCompanyTransfer($pnCompanyPk)
  {
    if(!assert('is_integer($pnCompanyPk) && !empty($pnCompanyPk)'))
      return 'No data found.';

    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');

    $asCompanyData = $this->getCompanyByPk($pnCompanyPk);
    if(empty($asCompanyData))
      return array('error'=> 'Company doesn\'t exist');

    $oForm = $oHTML->initForm('companyTransferForm');
    $sFormId = $oForm->getFormId();
    //Get javascript for the popup
    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SAVETRANSFER, CONST_AB_TYPE_COMPANY, $pnCompanyPk);
    $sJs = $oHTML->getAjaxJs($sURL, 'body', $sFormId);
    $oForm->setFormParams('', false, array('submitLabel' => 'Save','action' => '', 'onsubmit' => 'event.preventDefault(); '.$sJs, 'inajax' => 1));

    $oForm->setFormDisplayParams(array('noCancelButton' => '1','noCloseButton' => '1'));
    $sHTML= $oHTML->getBlocStart();
    $oForm->addField('misc', '', array('type' => 'text', 'text' => '<span class="h4">Assign account manager: </span><br />'));
    $oForm->addField('select', 'account_manager[]', array('label' => 'Account Manager', 'multiple' => 'multiple'));
    $oForm->setFieldControl('account_manager[]', array('jsFieldNotEmpty' => ''));

    $asManagers = $oLogin->getUserList(0, true, true);

    if(!empty($pnCompanyPk))
    $asSelectManager = $this->_getAccountManager($pnCompanyPk, 'cp');
    else
    $asSelectManager = $this->_getAccountManager('', '');

    foreach($asManagers as $asManagerData)
    {
      if(in_array($asManagerData['loginpk'], $asSelectManager))
       $oForm->addOption('account_manager[]', array('value'=>$asManagerData['loginpk'], 'label' => $asManagerData['firstname'].' '.$asManagerData['lastname'], 'selected' => 'selected'));
      else
       $oForm->addOption('account_manager[]', array('value'=>$asManagerData['loginpk'], 'label' => $asManagerData['firstname'].' '.$asManagerData['lastname']));
     }

    $oForm->addField('checkbox', 'cascading', array('type' => 'misc', 'label'=> 'Apply manager to employees ?', 'value' => 1, 'id' => 'cascading_id'));
    $oForm->addField('misc', '', array('type'=> 'br'));
    $oForm->addField('misc', '', array('type'=> 'br'));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();

    return $oPage->getAjaxExtraContent(array('data'=>$sHTML));
   }

    /* ******************************************************************************** */
    /* ************************ C O N T A C T ***************************************** */
    /* ******************************************************************************** */

    /**
     * Display the connection event tab
     * @param type $pasContactData
     * @return type
     */

  private function _getContactEventTab($pasContactData)
  {
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');

    if(!assert('is_array($pasContactData) && !empty($pasContactData)'))
      return $oHTML->getBlocMessage('No data available to fetch activity.');

    $oPage = CDependency::getComponentByName('page');
    $oEvent = CDependency::getComponentByName('event');
    if(!empty($oEvent))
    {
    $sEventList = $oEvent->getEventList(0, $this->_getUid(), CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $this->cnPk);
    $sUrl = $oPage->getUrl('event', CONST_ACTION_ADD, CONST_EVENT_TYPE_EVENT, 0, array(CONST_EVENT_ITEM_UID => $this->_getUid(), CONST_EVENT_ITEM_ACTION => CONST_ACTION_VIEW, CONST_EVENT_ITEM_TYPE => CONST_AB_TYPE_CONTACT, CONST_EVENT_ITEM_PK => $this->cnPk));
    $sAddLink = $oHTML->getLink($oHTML->getPicture($oEvent->getResourcePath().'pictures/add_event_16.png', 'Add a new activity'),$sUrl,array('title'=>'Add a new activity'));
    $sAddLink.= $oHTML->getLink(' Add a new activity', $sUrl);

    if(empty($sEventList))
      return $oHTML->getBlocMessage('No activities for this contact.<br /><br />'.$sAddLink, true);

      $sHTML = $oHTML->getBlocStart();
      $sHTML.= $sAddLink;
      $sHTML.= $oHTML->getCarriageReturn(2);
      $sHTML.= $sEventList;
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack')) . $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
    }
  }
  /**
   * return the form fom an ajax popup that allow user to  change follower
   * @param integer $pnContactPk
   * @return array to be encode in json
   */
  private function _getContactTransfer($pnContactPk)
  {
    if(!assert('is_integer($pnContactPk) && !empty($pnContactPk)'))
      return array('error' => 'No data found.');

    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');

    $oForm = $oHTML->initForm('contactTransferForm');
    $sFormId = $oForm->getFormId();

    //Get javascript for the popup
    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SAVETRANSFER, CONST_AB_TYPE_CONTACT, $pnContactPk);
    $sJs = $oHTML->getAjaxJs($sURL, 'body', $sFormId);
    $oForm->setFormParams('', false, array('submitLabel' => 'Save','action' => '', 'onsubmit' => 'event.preventDefault(); '.$sJs));

    $oForm->setFormDisplayParams(array('noCancelButton' => '1','noCloseButton' => '1'));
    $sHTML= $oHTML->getBlocStart();
    $oForm->addField('misc', '', array('type' => 'text', 'text' => '<span class="h4">Assign the account manager: </span><br />'));
    $oForm->addField('select', 'account_manager[]', array('label' => 'Account Manager', 'multiple' => 'multiple'));
    $oForm->setFieldControl('account_manager[]', array('jsFieldNotEmpty' => ''));
    $asManagers = $oLogin->getUserList(0,true,true);
    if($pnContactPk)
     $asSelectManager = $this->_getAccountManager($pnContactPk,'ct');
    else
     $asSelectManager = $this->_getAccountManager('', '');

    foreach($asManagers as $asManagerData)
    {
      if(in_array($asManagerData['loginpk'],$asSelectManager))
       $oForm->addOption('account_manager[]', array('value'=>$asManagerData['loginpk'], 'label' => $asManagerData['firstname'].' '.$asManagerData['lastname'], 'selected' => 'selected'));
      else
       $oForm->addOption('account_manager[]', array('value'=>$asManagerData['loginpk'], 'label' => $asManagerData['firstname'].' '.$asManagerData['lastname']));
     }

    $oForm->addField('misc', '', array('type'=> 'br'));
    $oForm->addField('misc', '', array('type'=> 'br'));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();

    $asFormData = $oPage->getAjaxExtraContent(array('data'=>$sHTML));

    return $asFormData;
  }

   /**
   * Small Header for the Connection listing displayed in detail pages
   * @param string $psSearchId
   * @return string HTML
   */

  private function _getContactRowSmallHeader()
  {
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $sHTML = $oHTML->getBlocStart('ct_coworker_header');

    $sHTML.= $oHTML->getBlocStart('', array('style' =>'width:22px; float:left;'));
    $sHTML.= '';
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('style' =>'width:18%;float:left;text-align:center;color:#FFFFFF;'));
    $sHTML.= $oHTML->getText('Name');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('style' =>'width:14%;float:left;color:#FFFFFF;'));
    $sHTML.= $oHTML->getText('Department');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('style' =>'width:12%;float:left;color:#FFFFFF;'));
    $sHTML.= $oHTML->getText('Position');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('style' =>'width:13%;float:left;color:#FFFFFF;'));
    $sHTML.= $oHTML->getText('Industry');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array( 'style' =>'width:10%;float:left;color:#FFFFFF;'));
    $sHTML.= $oHTML->getText('Recent activity');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array( 'style' =>'width:20%;float:left;color:#FFFFFF;'));
    $sHTML.= $oHTML->getText('Account Manager');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('style' =>'float:right;padding-right:30px;color:#FFFFFF;'));
    $sHTML.= $oHTML->getText('Action');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' =>'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Header for the Connection listing
   * @param string $psSearchId
   * @return string HTML
   */

  private function _getContactRowHeader($psSearchId = '')
  {
    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oEvent = CDependency::getComponentUidByName('event');

    $sHTML = $oHTML->getBlocStart('', array('class' =>'listCp_row '));
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'listCp_row_data'));

    //fetch sortorder from the history
    $asOrder = $this->_getHistorySearchOrder($psSearchId, $this->csUid, CONST_AB_TYPE_CONTACT);
    $sSortField = strtolower($asOrder['sortfield']);
    $sSortOrder = strtolower($asOrder['sortorder']);


    $sUrl = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_LIST, CONST_AB_TYPE_CONTACT, 0, array('searchId' => $psSearchId));

    $sHTML.= $oHTML->getBlocStart('', array('style' =>'width:12px; float:left;'));
    $sHTML.= '<input type="checkbox" />';
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'ab_list_cell cp_list_cell ct_search','sort_name'=>'lastname','style' =>'width:8%; padding-left: 2px;'));
    $sHTML.= $oHTML->getText('Lastname ');
    $sHTML.= $oHTML->getSpace(2);

    if($sSortField == 'lastname' && $sSortOrder == 'asc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/up_orange.png', 'A - Z', '', array('class'=>'moveup '));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=lastname&sortorder=asc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/up.png', 'A - Z', '', array('class'=>'moveup '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
    }

    if($sSortField == 'lastname' && $sSortOrder == 'desc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/down_orange.png', 'Z - A','',array('class'=>'movedown '));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=lastname&sortorder=desc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/down.png', 'Z - A', '', array('class'=>'movedown '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
    }
    $sHTML.= $oHTML->getBlocEnd();


    $sHTML.= $oHTML->getBlocStart('cpName', array('class' => 'ab_list_cell cp_list_cell ct_search','sort_name'=>'lastname','style' =>'width:4%; padding-left: 2px;'));
    $sHTML.= $oHTML->getText(' First ');
    $sHTML.= $oHTML->getSpace(2);

    if($sSortField == 'firstname' && $sSortOrder == 'asc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/up_orange.png', 'A - Z','',array('class'=>'moveup', 'sortfield' => ''));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=firstname&sortorder=asc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/up.png', 'A - Z', '', array('class'=>'moveup '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
    }

    if($sSortField == 'firstname' && $sSortOrder == 'desc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/down_orange.png', 'Z - A','',array('class'=>'movedown '));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=firstname&sortorder=desc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/down.png', 'Z - A', '', array('class'=>'movedown '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
    }
    $sHTML.= $oHTML->getBlocEnd();


    $sHTML.= $oHTML->getBlocStart('cpCompany', array('class' => 'ab_list_cell cp_list_cell ct_search','sort_name'=>'company_name','style' =>'width:8%; padding-left: 2px;'));
    $sHTML.= $oHTML->getText('Company');
    $sHTML.= $oHTML->getSpace(2);

    if($sSortField == 'company' && $sSortOrder == 'asc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/up_orange.png', 'A - Z','',array('class'=>'moveup', 'sortfield' => ''));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=company&sortorder=asc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/up.png', 'A - Z', '', array('class'=>'moveup '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
    }

    if($sSortField == 'company' && $sSortOrder == 'desc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/down_orange.png', 'Z - A','',array('class'=>'movedown '));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=company&sortorder=desc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/down.png', 'Z - A', '', array('class'=>'movedown '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
    }
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('cpDepartment', array('class' => 'ab_list_cell cp_list_cell ct_search','sort_name'=>'department','style' =>'width:13%;'));
    $sHTML.= $oHTML->getText('Department');
    $sHTML.= $oHTML->getSpace(2);
    if($sSortField == 'department' && $sSortOrder == 'asc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/up_orange.png', 'A - Z','',array('class'=>'moveup', 'sortfield' => ''));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=department&sortorder=asc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/up.png', 'A - Z', '', array('class'=>'moveup '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
    }

    if($sSortField == 'department' && $sSortOrder == 'desc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/down_orange.png', 'Z - A','',array('class'=>'movedown '));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=department&sortorder=desc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/down.png', 'Z - A', '', array('class'=>'movedown '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
    }
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'ab_list_cell cp_list_cell ct_search','sort_name'=>'position','style' =>'width:10%;'));
    $sHTML.= $oHTML->getText('Position');
    $sHTML.= $oHTML->getSpace(2);
    if($sSortField == 'position' && $sSortOrder == 'asc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/up_orange.png', 'A - Z','',array('class'=>'moveup', 'sortfield' => ''));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=position&sortorder=asc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/up.png', 'A - Z', '', array('class'=>'moveup '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
      }

    if($sSortField == 'position' && $sSortOrder == 'desc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/down_orange.png', 'Z - A','',array('class'=>'movedown '));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=position&sortorder=desc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/down.png', 'Z - A', '', array('class'=>'movedown '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
     }
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('cpIndusty', array('class' => 'ab_list_cell cp_list_cell ct_search','sort_name'=>'industry_name','style' =>'width:10%;'));
    $sHTML.= $oHTML->getText('Industry');
    $sHTML.= $oHTML->getSpace(2);
    if($sSortField == 'industry' && $sSortOrder == 'asc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/up_orange.png', 'A - Z','',array('class'=>'moveup', 'sortfield' => ''));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=industry&sortorder=asc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/up.png', 'A - Z', '', array('class'=>'moveup '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
      }

    if($sSortField == 'industry' && $sSortOrder == 'desc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/down_orange.png', 'Z- A','',array('class'=>'movedown '));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=industry&sortorder=desc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/down.png', 'Z - A', '', array('class'=>'movedown '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
      }
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'ab_list_cell cp_list_cell', 'style' =>'width:11%;'));
    if(!empty($oEvent))
      $sHTML.= $oHTML->getText('Recent activity');
    $sHTML.= $oHTML->getSpace(2);
    if($sSortField == 'activity' && $sSortOrder == 'asc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/up_orange.png', 'Oldest First','',array('class'=>'moveup', 'sortfield' => ''));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=activity&sortorder=asc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/up.png', 'Oldest First', '', array('class'=>'moveup '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
      }

    if($sSortField == 'activity' && $sSortOrder == 'desc')
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/down_orange.png', 'Recent First','',array('class'=>'movedown '));
    else
    {
      $sSortUrl = $sUrl.'&sortfield=activity&sortorder=desc';
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/down.png', 'Recent First', '', array('class'=>'movedown '));
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' =>  "AjaxRequest('".$sSortUrl."', 'body', '', 'contactListContainer'); ") );
    }

    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'ab_list_cell cp_list_cell', 'style' =>'width:8%;'));
    $sHTML.= $oHTML->getText('Account Manager');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'ab_list_cell cp_list_cell', 'style' =>'float:right;'));
    $sHTML.= $oHTML->getText('Action');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' =>'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Display the male or female icon
   * @param array $pasContactData
   * @return string
   */

  private function _getDisplayIcon($pasContactData)
  {
    if(!assert('is_array($pasContactData)&& !empty($pasContactData)'))
      return 'Incorrect or empty data found';

    $oHTML = CDependency::getComponentByName('display');

    if($pasContactData['courtesy'] == 'ms')
      $sHTML = $oHTML->getPicture($this->getResourcePath().'/pictures/ct_f_10.png');
    else
      $sHTML = $oHTML->getPicture($this->getResourcePath().'/pictures/ct_m_10.png');

    return $sHTML;
  }

  /**
   * Display the connection records
   * @param array $pasContactData
   * @param integer $pnRow
   * @param string $psVariable
   * @return string HTML
   */

  private function _getContactRow($pasContactData, $pnRow,$psVariable='')
  {
      if(!assert('is_array($pasContactData)&& !empty($pasContactData)'))
        return '';

      /*@var $oPage CPageEx */
      $oPage = CDependency::getComponentByName('page');
      /*@var $oHTML CDisplayEx */
      $oHTML = CDependency::getComponentByName('display');
      $oEvent = CDependency::getComponentUidByName('event');

      $nContactPk = (int)$pasContactData['contactpk'];
      $sId = 'id_'.$nContactPk;

      if(($pnRow%2) == 0)
        $sRowClass = '';
      else
       $sRowClass = 'listCt_row_data_odd';

      if($psVariable==1)
       $sPaddingTop= 'padding-top: 0px;';
      else
       $sPaddingTop= '';

      $sHTML= $oHTML->getBlocStart($sId, array('class' =>'listCt_row '));
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'listCt_row_data '.$sRowClass));

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_checkbox '.$sRowClass));
      $sHTML.= '<input type="checkbox" />';
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'ct_list_cell ct_list_name  '.$sRowClass, 'style' =>'width:13%; padding-left: 2px; '.$sPaddingTop.''));
      $sContactRelation = getCompanyRelation($pasContactData['relationfk']);
      $sHTML.= $oHTML->getBlocStart('',array('style'=>'width:38px;','class' => 'imgClass '.$sRowClass));
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'/pictures/'.$sContactRelation['icon_small'], 'Relation', '');
      $sHTML.= $oHTML->getSpace();
      $sHTML.= $this->_getDisplayIcon($pasContactData);
      $sHTML.= $oHTML->getBlocEnd();
      $sURL =  $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $nContactPk);
      $sHTML.= $oHTML->getLink($pasContactData['lastname'].' '.$pasContactData['firstname'], $sURL);
      $sHTML.= $oHTML->getBlocEnd();

      if($psVariable<>1)
      {
       $sHTML.= $oHTML->getBlocStart('', array('class' => 'ct_list_cell '.$sRowClass,'style' =>'width:10%; '.$sPaddingTop.''));
       $sHTML.= $this->_getContactRow_companyDetail($pasContactData);
       $sHTML.= $oHTML->getBlocEnd();
      }

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'ct_list_cell '.$sRowClass,'style' =>'width:12%; '.$sPaddingTop.''));
      if(!empty($pasContactData['department']))
      $sHTML.= $oHTML->getText($pasContactData['department']);
      else
      $sHTML.= $oHTML->getText('-', array('class' => 'light italic spanCentered'));
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'ct_list_cell '.$sRowClass,'style' =>'width:10%; '.$sPaddingTop.''));
      if(!empty($pasContactData['position']))
      $sHTML.= $oHTML->getText($pasContactData['position']);
      else
      $sHTML.= $oHTML->getText('-', array('class' => 'light italic spanCentered'));
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'ct_list_cell '.$sRowClass,'style' =>'width:10%; '.$sPaddingTop.''));
      $sHTML.= $this->_getContactRow_IndustryInfo($pasContactData);
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'ct_list_cell '.$sRowClass,'style' =>'width:9%; '.$sPaddingTop.''));
      if(!empty($oEvent))
        $sHTML.= $this->_getContactRow_Activity($pasContactData);
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'ct_list_cell '.$sRowClass, 'style' =>'width;10% '.$sPaddingTop.''));
      $sHTML.= $this->_getContactAccountManager($pasContactData);
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'ct_list_cell '.$sRowClass, 'style' =>'float:right; '.$sPaddingTop.''));
      $sHTML.= $this->_getContactRowAction($pasContactData);
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('', array('class' =>'floatHack'));
      $sHTML.= $oHTML->getBlocEnd();

      return $sHTML;
  }

   /**
   * Get the company industry information
   * @param array $pasContactData
   * @return string HTML
   */

   private function _getCompanyRow_IndustryInfo($pasCompanyData)
  {
     if(!assert('is_array($pasCompanyData) && !empty($pasCompanyData)'))
     return '';

    $oHTML = CDependency::getComponentByName('display');
    if(!empty($pasCompanyData['industry_name']))
     $sHTML= $oHTML->getText($pasCompanyData['industry_name']);
    else
      $sHTML= $oHTML->getText('-', array('class' => 'light italic spanCentered'));

    return $sHTML;
  }

  /**
   * Get the connection industry information
   * @param array $pasContactData
   * @return string HTML
   */

  private function _getContactRow_IndustryInfo($pasContactData)
  {
    if(!assert('is_array($pasContactData) && !empty($pasContactData)'))
      return '';

    $oHTML = CDependency::getComponentByName('display');
    if(!empty($pasContactData['industry_name']))
     $sHTML= $oHTML->getText($pasContactData['industry_name']);
    else
      $sHTML= $oHTML->getText('-', array('class' => 'light italic spanCentered'));

    return $sHTML;
  }

  /**
   * Display the activities of the connection
   * @param array $pasContactData
   * @return string HTML
   */

  private function _getContactRow_Activity($pasContactData)
  {
   if(!assert('is_array($pasContactData) && !empty($pasContactData)'))
     return '';

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW,CONST_AB_TYPE_CONTACT,(int)$pasContactData['contactpk']);
    $sHTML = '';

    if(!empty($pasContactData['title']))
       $sEventTitle = $pasContactData['title'];
    else
       $sEventTitle = '';

    if(!empty($pasContactData['content']))
       $sEventContent =  $oHTML->getExtract(strip_tags($pasContactData['content']),200);
    else
       $sEventContent = '';

     if(!empty($sEventTitle))
       $sEvent = $sEventTitle.'<br/>';
     else
       $sEvent = '';

     $sEvent.= $sEventContent;

    if(!empty($sEvent))
    {
     $sHTML.= $oHTML->getBlocStart('',array('style'=>'float:left;width:40px;'));
     $sHTML.= $oHTML->getText(date('m/y',strtotime($pasContactData['date_display'])));
     $sHTML.= $oHTML->getBlocEnd();

     $sHTML.= $oHTML->getBlocStart('',array('class' => 'imgClass  activityClass','title'=>$sEvent));
     $sHTML.= $oHTML->getPicture($this->getResourcePath().'/pictures/list_event.png', 'Activities', '',array('onmouseover'=>'showActivityPopup(this);','onmouseout'=>"hideActivityPopup();"));
     $sHTML.= $oHTML->getBlocEnd();
    }
    else
     $sHTML.=  $oHTML->getText('-', array('class' => 'light italic spanCentered'));

    return $sHTML;
  }

  /**
   * Get Full name of the account manager of connection
   * @param type $pasContactData
   * @return type
   */

  private function _getContactAccountManager($pasContactData)
  {
     if(!assert('is_array($pasContactData) && !empty($pasContactData)'))
     return '';

     /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');

    $sHTML = $oLogin->getUserAccountName($pasContactData['userlastname'],$pasContactData['userfirstname']);

    return $sHTML;
  }

  /**
   * Get company of the connection
   * @param array $pasContactData
   * @return string HTML
   */

  private function _getContactRow_companyDetail($pasContactData)
  {
    if(!assert('is_array($pasContactData) && !empty($pasContactData)'))
      return '';

    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');

    if(!empty($pasContactData['ncount']))
    {
      if($pasContactData['ncount']<'2')
      {
        $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, (int)$pasContactData['companypk']);
        $sHTML = $oHTML->getLink($pasContactData['company_name'], $sURL);
      }
      else
      {
        $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, (int)$pasContactData['contactpk']);
        $sHTML = $oHTML->getLink( $pasContactData['ncount'].' Companies', $sURL);
      }
    }
    else
      $sHTML = $oHTML->getText('No companies ', array('class' => 'light italic'));
    return $sHTML;
  }

  /** This function is not used at the moment */

  private function _getContactRow_contactInfo($pasContactData)
  {
    if(!assert('is_array($pasContactData) && !empty($pasContactData)'))
     return '';

    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $sHTML = '';

    if(isset($pasContactData['cellphone']) && !empty($pasContactData['cellphone']))
     $sPhone=$pasContactData['cellphone'];
    else if(isset($pasContactData['phone']) && !empty($pasContactData['phone']))
     $sPhone=$pasContactData['phone'];
    else if(isset($pasContactData['profilePhone'])&&!empty($pasContactData['profilePhone']))
      $sPhone=$pasContactData['profilePhone'];
    else
     $sPhone = '';

    $sHTML.= $oHTML->getLink($sPhone, 'callto:'.$sPhone);
    if(!empty($pasContactData['fax']))
    {
      if(!empty($sHTML))
        $sHTML.= ' / ';

      $sFax = $pasContactData['fax'];
    }
    else if(!empty($pasContactData['profileFax']))
    {
      if(!empty($sHTML))
        $sHTML.= ' / ';
      $sFax = $pasContactData['profileFax'];
    }
    else
      $sFax = '';

    $sHTML.= $oHTML->getText($sFax);

    if(!empty($sHTML))
      $sHTML.= $oHTML->getCarriageReturn();

    if(isset($pasContactData['email']) && !empty($pasContactData['email']))
     $sEmail = $pasContactData['email'];
    else if(isset($pasContactData['profileEmail'])&&!empty($pasContactData['profileEmail']))
     $sEmail = $pasContactData['profileEmail'];
    else
     $sEmail= '';

    $sHTML.= $oHTML->getLink($sEmail, 'mailto:'.$sEmail);
    if(empty($sHTML))
      $sHTML.= $oHTML->getSpace();

    return $sHTML;
  }

  /**
   * Get the connection list
   * @param string $psQueryFilter
   * @return string HTML
   */

  private function _getContactList($psQueryFilter = '')
  {
    if(!assert('is_string($psQueryFilter)'))
     return '';

    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'/css/addressbook.css');

    $sSetTime =  getValue('settime');
    showHideSearchForm($sSetTime,'ct');

    $sHTML = $oHTML->getTitleLine('Connection Search', $this->getResourcePath().'/pictures/contact_48.png');
    $sHTML.= $oHTML->getCarriageReturn();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'searchTitle'));
    $sHTML.= $oHTML->getBlocEnd();

   // Insert the search form in the Contact list page
    $gbNewSearch = true;

    //if clear search: do not load anything from session and generate a new searchId
    //if do_search: do not load the last search, save a new one with new parameters

    if((getValue('clear') == 'clear_ct') || getValue('do_search', 0))
      $sSearchId = manageSearchHistory($this->csUid, CONST_AB_TYPE_CONTACT, true);
    else
    {
      //reload the last search using the ID passed in parameters, ou the last done
      if(getValue('searchId'))
        $sSearchId = manageSearchHistory($this->csUid, CONST_AB_TYPE_CONTACT);
      else
        $sSearchId = reloadLastSearch($this->csUid, CONST_AB_TYPE_CONTACT);
    }

    //execute the search and bring a multi dimension array with context data and search result
    $avResult = $this->_getContactSearchResult($psQueryFilter, $sSearchId);
    $sMessage = $this->_getSearchMessage($avResult['nNbResult'],'');

    //get the search bloc: title, floating icon, form
    $sHTML.= $this->_getContactSearchBloc($sSearchId, $avResult, $gbNewSearch);
    $sJavascript = " $(document).ready(function(){ $('.searchTitle').html('".$sMessage."') }); ";
    $oPage->addCustomJs($sJavascript);

    //display the result
    $sHTML.= $oHTML->getBlocStart('contactListContainer');
    $sHTML.= $this->_getContactResultList($avResult, $sSearchId);
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

   /**
   * Ajax function to get the company search records
   * @global boolean $gbNewSearch
   * @return array
   */

  private function _getAjaxContactSearchResult()
  {
    global $gbNewSearch;

    //if clear search: do not load anything from session and generate a new searchId
    //if do_search: do not load the last search, save a new one with new parameters
    if((getValue('clear') == 'clear_ct') || getValue('do_search', 0))
    {
      $gbNewSearch = true;
      unset($_POST['clear']); unset($_POST['do_search']);
      unset($_GET['clear']);  unset($_GET['do_search']);
      $sSearchId = manageSearchHistory($this->csUid, CONST_AB_TYPE_CONTACT, true);
    }
    else
    {
      //reload the last search using the ID passed in parameters, ou the last done
      if(getValue('searchId'))
        $sSearchId = manageSearchHistory($this->csUid, CONST_AB_TYPE_CONTACT);
      else
        $sSearchId = reloadLastSearch($this->csUid, CONST_AB_TYPE_CONTACT);
    }
    $avResult = $this->_getContactSearchResult('', $sSearchId);
    $asOrder = $this->_getHistorySearchOrder($sSearchId, $this->csUid, CONST_AB_TYPE_CONTACT);

    if(empty($avResult) || empty($avResult['nNbResult']) || empty($avResult['oData']))
    {
      $sMessage = $this->_getSearchMessage($avResult['nNbResult'], $asOrder);
      $oDisplay = CDependency::getComponentByName('display');
      return array('data' => $oDisplay->getBlocMessage('No result to your search query'), 'action' => '$(\'.searchTitle\').html(\''.addslashes($sMessage).'\'); jQuery(\'.searchContainer:not(:visible)\').fadeIn(); $(\'.searchMenuIcon span\').html(\''.$avResult['nNbResult'].'\'); $(\'body\').scrollTop(0);');
    }

    $sData = $this->_getContactResultList($avResult, $sSearchId, true);

    if(empty($sData) || $sData == 'null' || $sData == null)
      return array('data' => 'Sorry, an error occured while refreshing the list.');

    if($gbNewSearch)
    {
       $sMessage = $this->_getSearchMessage($avResult['nNbResult'], $asOrder, true);
         return array('data' => mb_convert_encoding($sData,'utf8'), 'action' => '$(\'.searchTitle\').html(\''.addslashes($sMessage).'\'); jQuery(\'.searchContainer\').fadeOut(); $(\'.searchMenuIcon span\').html(\''.$avResult['nNbResult'].'\');');
    }

    $sMessage = $this->_getSearchMessage($avResult['nNbResult'], $asOrder, true);
    return array('data' => mb_convert_encoding($sData, 'utf8'), 'action' => '$(\'.searchTitle .searchTitleSortMsg\').html(\''.addslashes($sMessage).'\'); jQuery(\'.searchContainer\').fadeOut(); $(\'.searchMenuIcon span\').html(\''.$avResult['nNbResult'].'\'); $(\'body\').scrollTop(0);');
  }

  /**
   * Ajax function to get the company search records
   * @global boolean $gbNewSearch
   * @return array
   */

  private function _getAjaxCompanySearchResult()
  {
    global $gbNewSearch;

    //if clear search: do not load anything from session and generate a new searchId
    //if do_search: do not load the last search, save a new one with new parameters
    if((getValue('clear') == 'clear_cp') || getValue('do_search', 0))
    {
      $gbNewSearch = true;
      unset($_POST['clear']); unset($_POST['do_search']);
      unset($_GET['clear']);  unset($_GET['do_search']);
      $sSearchId = manageSearchHistory($this->csUid, CONST_AB_TYPE_COMPANY, true);
    }
    else
    {
      //reload the last search using the ID passed in parameters, ou the last done
      if(getValue('searchId'))
        $sSearchId = manageSearchHistory($this->csUid, CONST_AB_TYPE_COMPANY);
      else
        $sSearchId = reloadLastSearch($this->csUid, CONST_AB_TYPE_COMPANY);
    }

    //Do the search and return an array with all the data
    $avResult = $this->_getCompanySearchResult('', $sSearchId);
    $asOrder = $this->_getHistorySearchOrder($sSearchId, $this->csUid, CONST_AB_TYPE_COMPANY);

    if(empty($avResult) || empty($avResult['nNbResult']) || !$avResult['oData'])
      return array('message' => 'No result to your search query', 'action' => '$(\'.searchTitle\').html(\' No result \');jQuery(\'.searchContainer:not(:visible)\').fadeIn(); $(\'.searchMenuIcon span\').html(\''.$avResult['nNbResult'].'\'); $(\'body\').scrollTop(0);');

    $sData = $this->_getCompanyResultList($avResult, $sSearchId, true);

    if(empty($sData) || $sData == 'null' || $sData == null)
      return array('message' => 'Sorry, an error occured while refreshing the list.');

    if($gbNewSearch)
    {
      $sMessage = $this->_getCompanySearchMessage($avResult['nNbResult'], $asOrder);
      return array('data' => mb_convert_encoding($sData, 'utf8'), 'action' => '$(\'.searchTitle \').html(\''.addslashes($sMessage).'\');jQuery(\'.searchContainer\').fadeOut(); $(\'.searchMenuIcon span\').html(\''.$avResult['nNbResult'].'\');');
      }

      $sMessage = $this->_getCompanySearchMessage($avResult['nNbResult'], $asOrder, true);
      return array('data' => mb_convert_encoding($sData, 'utf8'), 'action' => '$(\'.searchTitle .searchTitleSortMsg\').html(\''.addslashes($sMessage).'\'); jQuery(\'.searchContainer\').fadeOut(); $(\'body\').scrollTop(0);jQuery(\'.searchContainer\').fadeOut(); $(\'.searchMenuIcon span\').html(\''.$avResult['nNbResult'].'\');');

  }

  /**
   * List the connection results
   * @param type $pavResult: search data formated nbressult / odbresult
   * @param type $pbNewSearch, if it s a new search (or sorting, comoing back if false)
   * @return type
   */
  private function _getContactResultList($pavResult, $psSearchId = '', $pbNewSearch = false)
  {
    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');
    $oPager = CDependency::getComponentByName('pager');

    $nNbResult = $pavResult['nNbResult'];
    $oDbResult = $pavResult['oData'];

    if(!$oDbResult)
      $bRead = false;
    else
      $bRead = $oDbResult->readFirst();

    $sHTML = '';

    if($nNbResult > 0)
    {
      $sUrl = $oPage->getAjaxUrl('addressbook', CONST_ACTION_LIST, CONST_AB_TYPE_CONTACT);
      $asPagerUrlOption = array('ajaxTarget' => 'contactListContainer', 'ajaxCallback' => ' jQuery(\'.searchContainer\').fadeOut(); ');
      $sHTML.= $oPager->getCompactDisplay($nNbResult, $sUrl, $asPagerUrlOption);
     }

    $sHTML.= $oHTML->getBlocStart('', array('class'=>'homePageContainer','style' =>'padding: 0px;background-color:#FFFFFF;width: 100%;'));
    $sHTML.= $oHTML->getListStart('', array('class' => 'ablistContainer'));

    if($nNbResult == 0 || !$bRead)
    {
      $sHTML.= $oHTML->getListItemStart();
      $sHTML.= "No connection matching your search parameters.";
      $sHTML.= $oHTML->getListItemEnd();
     }
    else
    {
      $sHTML.= $oHTML->getListItemStart('', array('class' => 'ablistHeader'));
      $sHTML.= $this->_getContactRowHeader($psSearchId);
      $sHTML.= $oHTML->getListItemEnd();

      $nCount = 1;
      while($bRead)
      {
        $sRowId = 'ctId_'.$oDbResult->getFieldValue('contactpk');
        $asContactData = $oDbResult->getData();
        $sHTML.= $oHTML->getListItemStart($sRowId);
        $sHTML.= $this->_getContactRow($asContactData, $nCount);
        $sHTML.= $oHTML->getListItemEnd();

        $nCount++;
        $bRead = $oDbResult->ReadNext();
      }
    }
    $sHTML.= $oHTML->getListEnd();
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack')).$oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    if($nNbResult > 0)
      $sHTML.= $oPager->getDisplay($nNbResult, $sUrl, $asPagerUrlOption);

    return $sHTML;
  }
  /**
   *
   * @param type $psQueryFilter
   * @return an array formatted as follow:  nNbResult => global nb of result , oData: dbObject containing the results
   */
  private function _getContactSearchResult($psQueryFilter = '',$psSearchId = '')
  {

    $sQuery = 'SELECT count(DISTINCT ct.contactpk) as nCount FROM contact as ct ';
    $sQuery.= ' LEFT JOIN profil AS prf ON (ct.contactpk = prf.contactfk and prf.date_end IS NULL)';
    $sQuery.= ' LEFT JOIN company AS cp ON (cp.companypk = prf.companyfk)';
    $sQuery.= ' LEFT JOIN company_industry AS cmpid ON (cp.companypk = cmpid.companyfk)';
    $sQuery.= ' LEFT JOIN industry AS ind ON (cmpid.industryfk = ind.industrypk)';
    $sQuery.= ' LEFT JOIN login AS lg ON (lg.loginpk = ct.followerfk )';

    if($psQueryFilter)
      $sQuery.= $psQueryFilter;
    else
    {
      $asFilter = $this->_getSqlContactSearch();
      if(!empty($asFilter['join']))
        $sQuery.= $asFilter['join'];

      if(!empty($asFilter['where']))
        $sQuery.= ' WHERE '.$asFilter['where'];
    }

    $oDb = CDependency::getComponentByName('database');
    $oEvent = CDependency::getComponentByName('event');
    $oDbResult = $oDb->ExecuteQuery($sQuery);
    $bRead = $oDbResult->ReadFirst();
    $nNbResult = $oDbResult->getFieldValue('nCount', CONST_PHP_VARTYPE_INT);

    if($nNbResult == 0)
      return array('nNbResult' => 0, 'oData' => null);

    $asEventQuery = $oEvent->getActivitySql();
    if(empty($asEventQuery['select']))
        $asEventQuery['select'] = '1';

    $sQuery = ' SELECT ct.*,cp.*,'.$asEventQuery['select'].',group_concat(DISTINCT CONCAT(lg.lastname) SEPARATOR ",") as userlastname,group_concat(DISTINCT CONCAT(lg.firstname) SEPARATOR ",") as userfirstname ,';
    $sQuery.= ' GROUP_CONCAT(DISTINCT prf.position) AS position, GROUP_CONCAT(DISTINCT prf.email) AS profileEmail,';
    $sQuery.= ' GROUP_CONCAT(DISTINCT prf.department) AS department, GROUP_CONCAT(DISTINCT prf.phone) AS profilePhone, GROUP_CONCAT(DISTINCT prf.fax) AS profileFax,';
    $sQuery.= ' GROUP_CONCAT(DISTINCT ind.industry_name) AS industry_name,GROUP_CONCAT(DISTINCT cp.company_name) AS company_name, COUNT( DISTINCT cp.company_name) AS ncount';
    $sQuery.= ' FROM contact AS ct USE INDEX (lastname_idx)';
    $sQuery.= ' LEFT JOIN profil AS prf ON (ct.contactpk = prf.contactfk and prf.date_end IS NULL)';
    $sQuery.= ' LEFT JOIN company AS cp ON (cp.companypk = prf.companyfk)';
    $sQuery.= ' LEFT JOIN company_industry AS cmpid ON (cp.companypk = cmpid.companyfk)';
    $sQuery.= ' LEFT JOIN industry AS ind ON (cmpid.industryfk = ind.industrypk)';
    $sQuery.= ' LEFT JOIN login AS lg ON (lg.loginpk = ct.followerfk )';
    if(!empty($asEventQuery['join']))
    $sQuery.= $asEventQuery['join'];

    if($psQueryFilter)
      $sQuery.= $psQueryFilter;
    else
    {
      if(!empty($asFilter['join']))
        $sQuery.= $asFilter['join'];

      if(!empty($asFilter['where']))
        $sQuery.= ' WHERE '.$asFilter['where'];
      }

    $sQuery.= ' GROUP BY ct.contactpk ';
    $sQuery.= $this->_getContactSearchOrder($psSearchId);

    //Debugging going from here //

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
   * Display the contact informations
   * @param integer $pnPK
   * @return array of contact data
   */

  private function _getContactView($pnPK)
  {
    $oRight = CDependency::getComponentByName('right');
    $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_TRANSFER,$this->getType(),0);

    if(!assert('is_integer($pnPK) && !empty($pnPK)'))
      return '';

    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oWEBMAIL = CDependency::getComponentByName('webmail');
    $oEvent  = CDependency::getComponentByName('event');
    $oLogin = CDependency::getComponentByName('login');
    $oWebmail = CDependency::getComponentUidByName('webmail');

    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'/css/addressbook.css');

    /*@var $oDB CDatabaseyEx */
    /*@var $oResult CDbResult */
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT ct.*, ci.*,ct.postcode as ctpostcode, prf.department as department_name,';
    $sQuery.= ' group_concat(DISTINCT CONCAT(l.firstname) SEPARATOR ",") as userfirstname,group_concat(DISTINCT CONCAT(l.lastname) SEPARATOR ",") as userlastname ,';
    $sQuery.= ' group_concat(DISTINCT acm.loginfk) as followers,group_concat(DISTINCT prf.email SEPARATOR ",")as prfEmail,';
    $sQuery.= ' group_concat(DISTINCT prf.phone SEPARATOR ",") as prfPhone,';
    $sQuery.= ' group_concat(DISTINCT prf.address_1 SEPARATOR ",") as prfAddress,';
    $sQuery.= ' co.*,prf.companyfk as companyfk,ct.contactpk as contactfk,l.lastname as follower_lastname, l.firstname as follower_firstname,';
    $sQuery.= ' group_concat(DISTINCT prf.position SEPARATOR ",") as position,';
    $sQuery.= ' group_concat(distinct ind.industry_name SEPARATOR " , ") as industry_name FROM contact as ct ';
    $sQuery.= ' LEFT JOIN city as ci ON (ci.citypk = ct.cityfk) ';
    $sQuery.= ' LEFT JOIN country as co ON (co.countrypk = ct.countryfk) ';
    $sQuery.= ' LEFT JOIN login as l ON (l.loginpk = ct.followerfk) ';
    $sQuery.= ' LEFT JOIN profil as prf ON (ct.contactpk = prf.contactfk and prf.date_end  IS NULL)';
    $sQuery.= ' LEFT JOIN company as cp ON (cp.companypk = prf.companyfk) ';
    $sQuery.= ' LEFT JOIN company_industry AS cmpid ON (cp.companypk = cmpid.companyfk)';
    $sQuery.= ' LEFT JOIN industry AS ind ON (cmpid.industryfk = ind.industrypk)';
    $sQuery.= ' LEFT JOIN account_manager as acm ON (acm.contactfk=ct.contactpk)';
    $sQuery.= ' WHERE ct.contactpk = '.$pnPK.' group by ct.contactpk';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    if(!$bRead)
      return $oHTML->getBlocMessage('No result found. ');

    $asContactData =  $oResult->getData();

    if(!empty($oEvent))
      $asContactEventData = $oEvent->getEventInformation($this->_getUid(),CONST_ACTION_VIEW,$this->getType());

    if(!empty($asContactEventData[$oResult->getFieldValue('contactpk')]))
      $asContactData = array_merge($asContactEventData[$oResult->getFieldValue('contactpk')],$asContactData);

    //fetch all profiles
    $sQuery = 'SELECT  p.*, DATE_FORMAT(p.date_update, \'%Y-%m-%d\') as date_update FROM profil as p ';
    $sQuery.= ' WHERE p.contactfk = '.$pnPK.' and p.date_end IS NULL ';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
    {
      $nProfil = 0;
      $asProfil = array();
    }
    else
    {
      while($bRead)
      {
        $asProfil[] = $oResult->getData();
        $bRead = $oResult->readNext();
      }
      $nProfil = count($asProfil);
    }

    $nCoworkers = 0;
    $asCoworkers = array();

    //Fetch co-workers if the connection has a company
    if(isset($asContactData['companyfk']) && $asContactData['companyfk'] != 0)
    {

      $sQuery = 'SELECT count(distinct p.contactfk) as nCount, c.*, p.* FROM profil as p ';
      $sQuery.= ' INNER JOIN contact as ct ON (ct.contactpk = p.contactfk)';
      $sQuery.= ' LEFT JOIN company as c ON(c.companypk = p.companyfk )  ';
      $sQuery.= ' WHERE p.companyfk = '.$asContactData['companyfk'].' and p.date_end IS NULL ';

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      while($bRead)
      {
        $asCoworkers[] = $oDbResult->getData();
        $bRead = $oDbResult->readNext();
      }

      $nCoworkers = $asCoworkers[0]['nCount'];
    }

    //Count the documents
    $asDocument = array();

    $sQuery = 'SELECT a.* from addressbook_document a,addressbook_document_info b WHERE a.addressbook_documentpk = b.docfk and b.itemfk = '.$pnPK.' and b.type="ct" order BY date_create desc ';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    while($bRead)
    {
      $asDocument[] = $oResult->getData();
      $bRead = $oResult->readNext();
    }

    $nDocument = count($asDocument);


    //For the logging the activity
    $sLink = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $pnPK);
    $oLogin->getUserActivity($oLogin->getUserPk(), $this->_getUid(), CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $pnPK, '[view] '.$asContactData['firstname'].' '.$asContactData['lastname'], $sLink);

    if(!empty($oEvent))
      $nEvents = $oEvent->getCountEventInformation($this->_getUid(), CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $pnPK);
    else
      $nEvents = '';

    $asCpCard = array();

    if(trim($asContactData['courtesy'])=='ms')
     $sHTML = $oHTML->getBlocStart('', array('class' => 'ct_top_container_female shadow'));
    else
     $sHTML = $oHTML->getBlocStart('', array('class' => 'ct_top_container_male shadow'));

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'ct_card_container'));

    if(trim($asContactData['courtesy'])=='ms')
        $sHTML.= $oHTML->getBlocStart('', array('class' => 'ct_top_name_female'));
    else
       $sHTML.= $oHTML->getBlocStart('', array('class' => 'ct_top_name_male'));

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'left'));
    $sHTML.= $oHTML->getTitle($asContactData['lastname'].' '.$asContactData['firstname'], 'h3', false, array('float' => 'left;'));
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'right'));


    foreach($asCoworkers as $asCompanyData)
    {
      if(!empty($asCompanyData['companypk']))
      {
        $nCompanyPk = (int)$asCompanyData['companypk'];
        $asCpCard[$nCompanyPk] = $this->getCompanyCard($nCompanyPk, $asCompanyData);
        $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $nCompanyPk);
        $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/detail_16.png', 'View company informations', 'javascript:;', array('onclick' => '$(\'.popupCpCard:not(#cp_card_'.$nCompanyPk.')\').hide(0); $(\'#cp_card_'.$nCompanyPk.'\').fadeToggle(); ', 'style' => 'height:14px;'));
        $sLink = $oHTML->getLink($asCompanyData['company_name'], $sURL);
        $sHTML.= $oHTML->getTitle($sPic.' '.$sLink, 'h4', false, array('style' => 'float:right;', 'isHtml' => 1));
      }
    }

   $sHTML.= $oHTML->getBlocEnd();
   $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
   $sHTML.= $oHTML->getBlocEnd();

   $sHTML.= $oHTML->getBlocStart('', array('class' => 'left;'));
   $sHTML.= $oHTML->getText($asContactData['position'], array('style'=>'float:left;padding-left:5px;color:#0D79BC;','class'=>'h4'));
   $sHTML.= $oHTML->getBlocEnd();

   $sHTML.= $oHTML->getBlocStart('', array('class' => 'right'));
   $sHTML.= $oHTML->getText($asContactData['department_name']);
   $sHTML.= $oHTML->getBlocEnd();

   $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
   $sHTML.= $oHTML->getBlocEnd();
   $sHTML.= $oHTML->getBlocEnd();

   $sHTML.= $oHTML->getBlocStart('', array('class' => 'ct_top_contact','style'=>'height:160px; '));
   $sHTML.= $oHTML->getBlocStart('', array('class' => 'ab_relation_row'));
   $sContactRelation = getCompanyRelation($asContactData['relationfk']);
   $sHTML.= $oHTML->getPicture($this->getResourcePath().'/pictures/'.$sContactRelation['icon'], 'Relation', '', array('style' => 'height: 24px'));
   $sHTML.= $oHTML->getBlocStart() . ' '.$sContactRelation['Label']. $oHTML->getBlocEnd();
   $sHTML.= $oHTML->getBlocEnd();

   $sHTML.= $oHTML->getText('Character: ', array('class' => 'ab_view_strong'));
   $sHTML.= $oHTML->getBlocStart('', array('class' => 'ab_card_comment'));
   if(!empty($asContactData['comments']))
    $sHTML.= $oHTML->getText(($asContactData['comments']));
    else
    $sHTML.= $oHTML->getText('No Character', array('class'=>'light italic'));

   $sHTML.= $oHTML->getBlocEnd();
   $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
   $sHTML.= $oHTML->getBlocEnd();

   $sHTML.= $oHTML->getBlocEnd();
   $sHTML.= $oHTML->getBlocEnd();
   $sHTML.= $oHTML->getBlocEnd();

   foreach($asCpCard as $nProfileCpPk => $sCard)
   {
      $sId = 'cp_card_'.$nProfileCpPk;
      $sHTML.= $oHTML->getBlocStart($sId, array('class' => 'popupCpCard hidden'));
      $sHTML.= $oHTML->getBlocStart('', array('style' => 'float:right; '));
      $sHTML.= $oHTML->getPicture($this->getResourcePath().'/pictures/close_16.png', 'Close', 'javascript:;', array('onclick' => '$(\'#'.$sId.'\').fadeOut(); ', 'style' => ' position:relative; top: 8px; right: 24px; '));
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $sCard;
      $sHTML.= $oHTML->getBlocEnd();
     }

     $sHTML.= $oHTML->getBlocStart('', array('class' =>'top_right_activity'));
     $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_TRANSFER, CONST_AB_TYPE_CONTACT,(int)$asContactData['contactpk']);
     $sAjax = $oHTML->getAjaxPopupJS($sURL, 'body','','250','800',1);

     if(isset($asContactData['follower_lastname']) && $asContactData['follower_lastname']!='' )
     {
        $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/manager.png', 'Account manager', '', array('style' => 'height: 24px;'));
        $sHTML.= $oHTML->getText(' Account manager: ', array('class' => 'ab_account_manager'));
        if($sAccess)
            $sHTML.= $oHTML->getLink($oLogin->getUserAccountName($asContactData['userlastname'],$asContactData['userfirstname']),'javascript:;', array('onclick'=>$sAjax));
        else
            $sHTML.= $oHTML->getText($oLogin->getUserAccountName($asContactData['userlastname'],$asContactData['userfirstname']));

       if($asContactData['followers'])
       {
        $asFollowers = $asContactData['followers'];
        $asData = explode(',',$asFollowers);
         $sHTML.= $oHTML->getSpace(1);
         foreach($asData as $asFollow)
         {
            $sHTML.= $oHTML->getText(',');
            $sHTML.= $oHTML->getSpace(1);
            $asRecords = $oLogin->getUserDataByPk((int)$asFollow);
            if($sAccess)
            $sHTML.= $oHTML->getLink($oLogin->getUserAccountName($asRecords['lastname'],$asRecords['firstname']),'javascript:;', array('onclick'=>$sAjax));
            else
            $sHTML.= $oHTML->getText($oLogin->getUserAccountName($asRecords['lastname'],$asRecords['firstname']));
            $sHTML.= $oHTML->getSpace(1);
         }
       }
     }
     else
      $sHTML.= $oHTML->getLink('< Define Manager >','javascript:;', array('onclick'=>$sAjax));
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('', array('class' =>'cp_top_activity'));

      if(!empty($oEvent))
      {
        $asEventEmails = $oEvent->getEventDetail('email',(int)$asContactData['contactpk'],'ct');

        if(!empty($asEventEmails))
          $asLatestEvents = $oEvent->getEventDetail('other',(int)$asContactData['contactpk'],'ct');
        else
          $asLatestEvents = $oEvent->getEventDetail('other',(int)$asContactData['contactpk'],'ct',2);

        if(!empty($asEventEmails))
        {
          foreach($asEventEmails as $asEventEmail)
          {
            $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW,CONST_AB_TYPE_CONTACT,(int)$asContactData['contactpk'],array('class'=>''),'ct_tab_eventId');

            $sHTML.= $oHTML->getText('Latest email: ', array('class' => 'ab_view_strong'));
            $asUserData= $oLogin->getUserDataByPk((int)$asEventEmail['created_by']);

            $sHTML.= $oHTML->getText('by  ');
            $sHTML.= $oLogin->getUserNameFromData($asUserData);
            $sHTML.= $oHTML->getText(' - ');
            $sHTML.= $oHTML->getNiceTime($asEventEmail['date_display'],0,true);
            $sHTML.= $oHTML->getBlocStart('', array('class' => ''));
            $sShortContent = strip_tags($asEventEmail['content']);
            if(strlen($sShortContent) > 150)
            {
              $bContentCut = true;
              $sShortContent = substr($sShortContent, 0, 130).' ... <a href="javascript:;" class="expandClass italic">see more</a>';
              $sPic = 'event_detail_expand.png';
              $sClass = 'expandClass';
            }
            else
            {
              $bContentCut = false;
              $sPic = 'event_detail_expanded.png';
              $sClass = '';
            }

            $sHTML.= $oHTML->getBlocStart('', array('class' => 'smallDivs', 'style' => 'width: 99%'));
            $sTitle = $oHTML->getPicture($oEvent->getResourcePath().'pictures/'.$sPic);
            $sTitle.= $oHTML->getSpace(2);

            if(!empty($asEventEmail['title']))
              $sTitle.= $oHTML->getText($asEventEmail['title']).': ';
            else
              $sTitle.= $oHTML->getText('No Title', array('class'=>'light italic')).': ';

            $sTitle = $oHTML->getLink($sTitle, 'javascript:;', array('class' => $sClass));
            $sHTML.= $oHTML->getHtmlContainer($sTitle. $sShortContent, '', array('class'=> 'eventRowContent'));

            if($bContentCut)
              $sHTML.= $oHTML->getHtmlContainer(nl2br($sTitle.'<br /><br />'.$asEventEmail['content']), '', array('class'=> 'eventRowFull', 'style' => 'display: none;'));

            $sHTML.= $oHTML->getBlocEnd();
            $sHTML.= $oHTML->getBlocEnd();
         }
      }

      $sHTML.= $oHTML->getBlocStart('',array('class'=>'floatHack'));
      $sHTML.= $oHTML->getBlocEnd();

      if(!empty($asLatestEvents))
      {
        foreach($asLatestEvents as $asLatestEvent)
        {
          $sHTML.= $oHTML->getText('Latest Activity: ', array('class' => 'ab_view_strong'));
          $asUserData= $oLogin->getUserDataByPk((int)$asLatestEvent['created_by']);
          $sHTML.= $oHTML->getText('by  ');
          $sHTML.= $oLogin->getUserNameFromData($asUserData);
          $sHTML.= $oHTML->getText(' - ');

          $sHTML.= $oHTML->getNiceTime($asLatestEvent['date_display'],0,true);

          $sHTML.= $oHTML->getBlocStart('', array('class' => '','style'=>'width:100%; border:none;'));
          $sHTML.= $oHTML->getBlocStart('', array('style' => 'width: 100%;'));

          $sShortContent = strip_tags($asLatestEvent['content']);
          if(strlen($sShortContent) > 150)
          {
            $bContentCut = true;
            $sShortContent = substr($sShortContent, 0, 130).' ... <a href="javascript:;" class="expandClass italic">see more</a>';
            $sPic = 'event_detail_expand.png';
            $sClass = 'expandClass';
          }
          else
          {
            $bContentCut = false;
            $sPic = 'event_detail_expanded.png';
            $sClass = '';
          }

          $sHTML.= $oHTML->getBlocStart('', array('class' => 'smallDivs', 'style' => 'width: 99%'));
          $sTitle = $oHTML->getPicture($oEvent->getResourcePath().'pictures/'.$sPic);
          $sTitle.= $oHTML->getSpace(2);
          if(!empty($asLatestEvent['title']))
            $sTitle.= $oHTML->getText($asLatestEvent['title']).': ';
          else
            $sTitle.= $oHTML->getText('No Title', array('class'=>'light italic')).': ';

          $sTitle = $oHTML->getLink($sTitle, 'javascript:;', array('class' => $sClass));
          $sHTML.= $oHTML->getHtmlContainer($sTitle. $sShortContent, '', array('class'=> 'eventRowContent'));

          if($bContentCut)
            $sHTML.= $oHTML->getHtmlContainer(nl2br($sTitle.'<br /><br />'.$asLatestEvent['content']), '', array('class'=> 'eventRowFull', 'style' => 'display: none;'));

          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getBlocEnd();

          $sHTML.= $oHTML->getCarriageReturn();
        }
      }
    }

    if(empty($asEventEmails) && empty($asLatestEvents))
    {
      if(!empty($asContactData['date_update']))
      {
        if(date('Y-m',strtotime($asContactData['date_update']) == date('Y-m'))&& (int)$asContactData['updated_by'] !=  $oLogin->getUserPk() )
        {
          $sHTML.= $oHTML->getBlocStart('', array('style' =>'margin-top:5px;'));
          $sHTML.= $oHTML->getText('Last Edited: ', array('class' => 'ab_view_strong'));
          $sHTML.= ' - ';

          $asUserData = $oLogin->getUserList((int)$asContactData['updated_by'],false,true);
          $sUser = $oLogin->getUserNameFromData(current($asUserData));
          $sHTML.= $oHTML->getNiceTime($asContactData['date_update'],0,true).$oHTML->getText(' - by '.$sUser);
          $sHTML.= $oHTML->getBlocEnd();
          $sHTML.= $oHTML->getCarriageReturn();
        }
      }

      $asLatestDocument= $this->_getLatestDocument((int)$asContactData['contactpk'],'ct');

      if(!empty($asLatestDocument['title'])&&(int)$asLatestDocument['loginfk'] !=  $oLogin->getUserPk())
      {
        $sHTML.= $oHTML->getCarriageReturn(2);
        $sHTML.= $oHTML->getBlocStart('', array('style' =>'margin-top:10px;'));
        $sHTML.= $oHTML->getText('Latest Document: ', array('class' => 'ab_view_strong'));
        $sHTML.= ' - ';

        $sHTML.= $oHTML->getText($asLatestDocument['title']. ' - '.$oHTML->getNiceTime($asLatestDocument['date_create'],0,true));
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getCarriageReturn();
      }

      $sHTML.= $oHTML->getText('Connection has been created ');
      $sHTML.= $oHTML->getSpace(2);
      $sHTML.= $oHTML->getNiceTime($asContactData['date_create'],0,true);
      $sHTML.= $oHTML->getCarriageReturn(2);
      $sUrl = $oPage->getUrl('event', CONST_ACTION_ADD, CONST_EVENT_TYPE_EVENT, 0, array(CONST_EVENT_ITEM_UID => $this->_getUid(), CONST_EVENT_ITEM_ACTION => CONST_ACTION_VIEW, CONST_EVENT_ITEM_TYPE => CONST_AB_TYPE_CONTACT, CONST_EVENT_ITEM_PK => $this->cnPk));
      $sHTML.= $oHTML->getLink(' Add notes / activities to this connection', $sUrl);
      $sHTML.= $oHTML->getCarriageReturn(2);

    }
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('', array('class' =>'cp_top_action'));

    if(!empty($oWebmail))
    {
      //Send Email
      $sHTML.= $oHTML->getBlocStart('');
      $sURL = $oWEBMAIL->getURL('webmail', CONST_ACTION_ADD, CONST_WEBMAIL,(int)$asContactData['contactpk'],array('ppaty'=>CONST_AB_TYPE_CONTACT,'ppaid'=>$asContactData['contactpk']));
      $sAjax = $oHTML->getAjaxPopupJS($sURL, 'body','','650','800',1);
      $sHTML.= $oHTML->getLink($oHTML->getPicture($oEvent->getResourcePath().'pictures/email.png', 'Send Email'),'javascript:;', array('onclick'=>$sAjax));
      $sHTML.= $oHTML->getLink(' Send Email to this connection','javascript:;', array('onclick'=>$sAjax));
      $sHTML.= $oHTML->getBlocEnd();
    }

    if(!empty($oEvent))
    {
      //Add a event
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'smallPaddingTop'));
      $sUrl = $oPage->getUrl('event', CONST_ACTION_ADD, CONST_EVENT_TYPE_EVENT, 0, array(CONST_EVENT_ITEM_UID => $this->_getUid(), CONST_EVENT_ITEM_ACTION => CONST_ACTION_VIEW, CONST_EVENT_ITEM_TYPE => CONST_AB_TYPE_CONTACT, CONST_EVENT_ITEM_PK => $this->cnPk));
      $sHTML.= $oHTML->getLink($oHTML->getPicture($oEvent->getResourcePath().'pictures/add_event_16.png', 'Add activity'), $sUrl);
      $sHTML.= $oHTML->getLink(' Add a note/activity', $sUrl);
      $sHTML.= $oHTML->getBlocEnd();
    }

    //Add new contact details
    $sUrl = $oPage->getUrl('addressbook', CONST_ACTION_MANAGE, CONST_AB_TYPE_CONTACT, (int)$asContactData['contactpk']);
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'smallPaddingTop'));
    if(empty($asContactData['contactfk']))
     $sHTML.= $oHTML->getLink('+ Add new details', $sUrl);
    else if(empty($asContactData['companyfk']))
     $sHTML.= $oHTML->getLink('+ Link the connection to the company', $sUrl);
    else
     $sHTML.= '';
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    //float hack
    $sHTML.= $oHTML->getBlocStart('', array('class' =>'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();

    $asTabs = array();

    if($nEvents > 0)
    {
      $sEventClass = 'tab_display';
      $sEventTitle='Activities ('.$nEvents.')';
      }
      else
      {
      $sEventClass = 'tab_display_inactive';
      $sEventTitle='Activities';
      }
    if($nDocument > 0)
    {
      $sDocumentTitle='Documents ('.$nDocument.')';
      $sDocumentClass = 'tab_display';
     }
    else
    {
      $sDocumentTitle='Documents';
      $sDocumentClass = 'tab_display_inactive';
    }

    if($nCoworkers > 1)
      $sCoworkerClass = 'tab_display';
    else
      $sCoworkerClass = 'tab_display_inactive';

    if($nProfil > 1)
      $sProfilClass = 'tab_display';
    else
      $sProfilClass = 'tab_display_inactive';

    $asCtTabs = $oLogin->getPreferences('cttab');

    //remove the tabs that are linked to blacklisted component
    foreach($asCtTabs as $sTabId => $sComponentId)
    {
      $sUid = isUidAvailable($sComponentId);
      if(empty($sUid))
        unset($asCtTabs[$sTabId]);
        }

    if(isset($asCtTabs[CONST_TAB_CT_DETAIL]))
      $asCtTabs[CONST_TAB_CT_DETAIL] = array('class'=>'tab_selected','style'=>'display:block;');
    else
    {
      if($nEvents)
      {
        if($nEvents > 0)
          $asCtTabs[CONST_TAB_CT_EVENT] = array('class'=>'tab_selected','style'=>'display:block;');
        else
          $asCtTabs[CONST_TAB_CT_EVENT] = array('style'=>'display:none;');
         }
       else if($nDocument)
       {
        if($nDocument > 0)
          $asCtTabs[CONST_TAB_CT_DOCUMENT] = array('class'=>'tab_selected','style'=>'display:block;');
        else
          $asCtTabs[CONST_TAB_CT_DOCUMENT] = array('style'=>'display:none;');
        }
      else if($nCoworkers)
      {
        if($nCoworkers > 1)
          $asCtTabs[CONST_TAB_CT_COWORKERS] = array('class'=>'tab_selected','style'=>'display:block;');
        else
          $asCtTabs[CONST_TAB_CT_COWORKERS] = array('style'=>'display:none;');
        }
      else if($nProfil)
      {
        if($nProfil > 1)
          $asCtTabs[CONST_TAB_CT_PROFILE] = array('class'=>'tab_selected','style'=>'display:block;');
        else
          $asCtTabs[CONST_TAB_CT_PROFILE] = array('style'=>'display:none;');
      }
    }

    if($nCoworkers > 0)
        $nCoworkers = $nCoworkers-1;

    if(empty($asCoworkers))
         $asCoworkers = array();

    //laod the tabs in a global array
    $asTabs[CONST_TAB_CT_DETAIL] = array('tabtitle' => 'Detail','tabOptions'=>array('tabId'=>'ct_tab_listId','class'=>'tab_display '.$asCtTabs['ct_tab_detail']['class']),'content' => $this->_getContactDetailTab($asContactData),'contentOptions'=>array('contentId'=>'ct_tab_detail','class'=>'display_tab hidden','style'=>$asCtTabs['ct_tab_detail']['style']), 'tabstatus' => CONST_TAB_STATUS_ACTIVE);
    $asTabs[CONST_TAB_CT_COWORKERS] = array('tabtitle' => 'Co-workers ('.($nCoworkers).')','tabOptions'=>array('tabId'=>'ct_tab_coworkersId','class'=>''.$sCoworkerClass.' '.$asCtTabs['ct_tab_coworkers']['class']),'content' => $this->_getContactCoworkersTab($asContactData,$asCoworkers),'contentOptions'=>array('contentId'=>'ct_tab_coworkers','class'=>'display_tab hidden ','style'=>$asCtTabs['ct_tab_coworkers']['style']), 'tabstatus' =>CONST_TAB_STATUS_ACTIVE);

    if(!empty($oEvent))
      $asTabs[CONST_TAB_CT_EVENT] = array('tabtitle' => $sEventTitle,'tabOptions'=>array('tabId'=>'ct_tab_eventId','class'=>''.$sEventClass.' '.$asCtTabs['ct_tab_event']['class']),'content' => $this->_getContactEventTab($asContactData),'contentOptions'=>array('contentId'=>'ct_tab_event','class'=>'display_tab hidden','style'=>$asCtTabs['ct_tab_event']['style']), 'tabstatus' =>CONST_TAB_STATUS_ACTIVE);

    $asTabs[CONST_TAB_CT_DOCUMENT] = array('tabtitle' => $sDocumentTitle,'tabOptions'=>array('tabId'=>'ct_tab_documentId','class'=>''.$sDocumentClass.' '.$asCtTabs['ct_tab_document']['class']),'content' => $this->_getContactDocumentTab($asContactData),'contentOptions'=>array('contentId'=>'ct_tab_document','class'=>'display_tab hidden','style'=>$asCtTabs['ct_tab_document']['style']), 'tabstatus' =>CONST_TAB_STATUS_ACTIVE);

    //remove currently displayed (on top) profile. But if there's none, we fix the counter
    $nProfil--;
    if(($nProfil<0))
      $nProfil = 0;

    $asTabs[CONST_TAB_CT_PROFILE] = array('tabtitle' => 'Profiles ('.$nProfil.')','tabOptions'=>array('tabId'=>'ct_tab_profileId','class'=>''.$sProfilClass.' '.$asCtTabs['ct_tab_profile']['class']),'content' => $this->_getContactProfileTab($asContactData, $asProfil),'contentOptions'=>array('contentId'=>'ct_tab_profile','class'=>'display_tab hidden','style'=>$asCtTabs['ct_tab_profile']['style']), 'tabstatus' =>CONST_TAB_STATUS_ACTIVE);

    foreach($asCtTabs as $sTabs => $vTabs)
    {
      $asOrderTabs[] = $asTabs[$sTabs];
     }

    $sHTML.= $oHTML->getBlocStart('', array('class' =>'bottom_container'));
    $sHTML.= $oHTML->getBlocStart('',  array('class' => 'tabs_container'));
    $sHTML.= $oHTML->getTabs('', $asOrderTabs);
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Get the search order for connection
   * @param string $psSearchId
   * @param boolen $pbWithKeyword
   * @return string
   */

  private function _getContactSearchOrder($psSearchId = '', $pbWithKeyword = true)
  {
    if($pbWithKeyword)
      $sKeyword = ' ORDER BY ';
    else
      $sKeyword = '';

    //try to find the previous sort field/order from saved history, if not, try to get it from the url
    $asOrder = $this->_getHistorySearchOrder($psSearchId, $this->csUid, CONST_AB_TYPE_CONTACT);

    switch($asOrder['sortfield'])
    {
      case 'id':
      case 'pk': return $sKeyword.' contactpk '.$asOrder['sortorder'].' '; break;
      case 'relation': return $sKeyword.' relationfk '.$asOrder['sortorder'].' '; break;
      case 'firstname': return $sKeyword.' firstname '.$asOrder['sortorder'].', lastname ASC, contactpk ASC ,event.date_display DESC'; break;
      case 'lastname': return $sKeyword.' lastname '.$asOrder['sortorder'].', firstname ASC, contactpk ASC ,event.date_display DESC'; break;
      case 'company': return $sKeyword.' company_name '.$asOrder['sortorder'].', firstname ASC, lastname ASC, contactpk ASC ,event.date_display DESC'; break;
      case 'department': return $sKeyword.' department '.$asOrder['sortorder'].', firstname ASC, lastname ASC, contactpk ASC ,event.date_display DESC'; break;
      case 'position': return $sKeyword.' position '.$asOrder['sortorder'].', firstname ASC, lastname ASC, contactpk ASC ,event.date_display DESC'; break;
      case 'industry': return $sKeyword.' industry_name '.$asOrder['sortorder'].', firstname ASC, lastname ASC, contactpk ASC ,event.date_display DESC'; break;
      case 'activity': return $sKeyword.' event.date_display '.$asOrder['sortorder'].', firstname ASC, lastname ASC, contactpk ASC '; break;

      default:
        return $sKeyword.' ct.contactpk desc '; break;
        break;
    }
    return '';
  }

  /**
   * Get the search order for company
   * @param string $psSearchId
   * @param boolen $pbWithKeyword
   * @return string
   */

  private function _getCompanySearchOrder($psSearchId = '', $pbWithKeyword = true)
  {
    if($pbWithKeyword)
      $sKeyword = ' ORDER BY ';
    else
      $sKeyword = '';

    //try to find the previous sort field/order from saved history, if not, try to get it from the url
    $asOrder = $this->_getHistorySearchOrder($psSearchId, $this->csUid, CONST_AB_TYPE_COMPANY);

    switch($asOrder['sortfield'])
    {
      case 'id':
      case 'pk': return $sKeyword.' companypk '.$asOrder['sortorder'].' '; break;
      case 'company_name': return $sKeyword.' company_name '.$asOrder['sortorder'].', companypk ASC,date_display DESC'; break;
      case 'industry': return $sKeyword.' industry_name '.$asOrder['sortorder'].', company_name ASC, companypk ASC,date_display DESC'; break;
      case 'activity': return $sKeyword.' date_display '.$asOrder['sortorder'].', company_name ASC, companypk ASC'; break;

      default:
        return $sKeyword.' cp.companypk desc '; break;
        break;
      }
    return '';
  }

  /**
   * Get the search order if the search has been already done
   * @param string $psSearchId
   * @param boolen $pbWithKeyword
   * @return string
   */

  private function _getHistorySearchOrder($psSearchId = '', $psGuid = '', $psType = '')
  {
    $asSortHistory = getSearchHistory($psSearchId, $psGuid, $psType);
    if(!empty($psSearchId) && !empty($asSortHistory) && !empty($asSortHistory['sortfield']))
    {
      $sOrderField = $asSortHistory['sortfield'];
      $sOrderType = $asSortHistory['sortorder'];
     }
    else
    {
      $sOrderField = getValue('sortfield', '');
      $sOrderType = getValue('sortorder', '');
      }

    return array('sortfield' => $sOrderField, 'sortorder' => $sOrderType);
  }

  /**
   * Get Latest Document of company or connection
   * @param integer $psDocumentPk
   * @param string $psItemType
   * @return array
   */

  private function _getLatestDocument($psDocumentPk, $psItemType)
  {
    if(!assert('is_integer($psDocumentPk) && !empty($psDocumentPk)'))
      return array('');

    if(!assert('is_string($psItemType) &&!empty($psItemType)'))
      return array('');

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT a.*,b.* FROM addressbook_document a, addressbook_document_info b WHERE b.type="'.$psItemType.'" and a.addressbook_documentpk= b.docfk  and b.itemfk = '.$psDocumentPk.' ';
    $sQuery.= 'ORDER BY a.date_create desc  limit 1';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

   if(!$bRead)
     return array('');

   return  $oResult->getData();
 }

 /**
   * Display the full search bloc including title, search form, flded icon for company
   * @param string $psSearchId
   * @param array $pavSearchResult
   * @param boolean $pbNewSearch
   * @return string html
   */
  private function _getCompanySearchBloc($psSearchId, $pavSearchResult, $pbNewSearch = true)
  {
    $oHTML = CDependency::getComponentByName('display');

    global $gbNewSearch;
    if($gbNewSearch)
      $sExtraClass = '';
    else
      $sExtraClass = ' hidden ';

    $sHTML = '';
    $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchOutterContainer'));

     $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchMenuIcon', 'onclick' => 'jQuery(this).parent().find(\'.searchContainer\').fadeToggle();'));
       $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/main_search_icon.png', 'Search Companies');
        $sHTML.= $oHTML->getCarriageReturn();
        $sHTML.= $oHTML->getSpanStart();
        $sHTML.= $oHTML->getText($pavSearchResult['nNbResult']);
       $sHTML.= $oHTML->getSpanEnd();
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('searchResult_'.$psSearchId, array('class' =>'searchContainer '.$sExtraClass));

        //Search title.
        $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchFormHeader'));
          $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchTitle'));
            $sSearchMessage = $this->_getCompanySearchMessage($pavSearchResult['nNbResult']);
            if(!empty($sSearchMessage))
              $sHTML.= $sSearchMessage;
            else
              $sHTML.= $oHTML->getText('Search Companies');
          $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchOption'));

         $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchOptionClose', 'onclick' => 'jQuery(\'.searchContainer\').fadeOut();'));
          $sHTML.= $oHTML->getBlocStart();
            $sHTML.= $oHTML->getText('Close');
          $sHTML.= $oHTML->getBlocEnd();
         $sHTML.= $oHTML->getBlocEnd();

         $sHTML.= $oHTML->getListStart('', array('onclick' => 'jQuery(\'li:not(:first)\', this).fadeToggle();'));

           $sHTML.= $oHTML->getListItemStart('', array('class' => ''));
            $sHTML.= 'Options ';
           $sHTML.= $oHTML->getListItemEnd();

            $sHTML.= $oHTML->getListItemStart('', array('class' => 'hidden', 'onclick' => 'alert(\'Coming soon.\');'));
             $sHTML.= '- Save search';
            $sHTML.= $oHTML->getListItemEnd();

            $sHTML.= $oHTML->getListItemStart('', array('class' => 'hidden', 'onclick' => 'alert(\'Coming soon.\');'));
             $sHTML.= '- Preferences';
            $sHTML.= $oHTML->getListItemEnd();

          $sHTML.= $oHTML->getListEnd();
        $sHTML.= $oHTML->getBlocEnd();

        //display search form
        $sHTML.= $oHTML->getBlocStart('companysearchForm', array('class' =>'queryBuilderContainer'));
          $sHTML.= $oHTML->getBlocStart('', array('class' => 'searchFormSidebar'));
            $sHTML.= $this->_getCompanySearchFormSidebar();
          $sHTML.= $oHTML->getBlocEnd();
           $sHTML.= $oHTML->getBlocStart('', array('class' => 'searchFormContainer'));

            $sHTML.= $this->_getCompanySearchForm($psSearchId);

          $sHTML.= $oHTML->getBlocEnd();
         $sHTML.= $oHTML->getFloatHack();
       $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Company search side bar
   * @return type
   */

  private function _getCompanySearchFormSidebar()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sJavascript = 'jQuery(document).ready(function(){ ';
    $sJavascript.= '  jQuery(\'.searchFormFieldSelector li\').click(function(){ ';
    $sJavascript.= '    if(!jQuery(this).hasClass(\'fieldUsed\')) ';
    $sJavascript.= '      jQuery(this).addClass(\'fieldUsed\'); ';
    $sJavascript.= '    else ';
    $sJavascript.= '      jQuery(this).fadeOut(350, function(){ jQuery(this).css(\'border\', \'1px solid orange\'); }).
      fadeIn(350).fadeOut(350, function(){ jQuery(this).css(\'border\', \'1px solid orange\'); })
      .fadeIn(350, function(){ jQuery(this).css(\'border\', \'\'); }); ';
    $sJavascript.= '    var sFieldContainer = jQuery(this).attr(\'fieldname\'); ';
    $sJavascript.= '    var oFormContainer = jQuery(\'.\'+sFieldContainer).closest(\'.innerForm\'); ';
    $sJavascript.= '   if(sFieldContainer == \'none\') ';
    $sJavascript.= '   { removeFormField(null, \'.formFieldContainer\'); return true; }';
    $sJavascript.= '    var sFieldContainer = sFieldContainer.split(\' \').join(\', .\'); ';
    $sJavascript.= '    jQuery(oFormContainer).find(\'script\').html(\'\'); ';
    $sJavascript.= '    var oFieldContainer = jQuery(\'.\'+sFieldContainer+\':not(.formFieldHidden)\'); ';
    $sJavascript.= '    jQuery(oFieldContainer).each(function() ';
    $sJavascript.= '    { ';
    $sJavascript.= '      if(sFieldContainer == \'formFieldContainer\' ) ';
    $sJavascript.= '      {  displayFormField(this, null, true); } ';
    $sJavascript.= '      else ';
    $sJavascript.= '      { displayFormField(this);  jQuery(this).find(\'input,select,textarea\').focus(); } ';
    $sJavascript.= '    }); ';
    $sJavascript.= '  }); ';

    //when loading the page in php, we refresh the sidebar and add X link
    $sJavascript.= ' refreshFormField(); ';
    $sJavascript.= '}); ';
    $oPage->addCustomJs($sJavascript);

    $sConnectionPic = $oHTML->getPicture($this->getResourcePath().'/pictures/connection.png');
    $sCompanyPic = $oHTML->getPicture($this->getResourcePath().'/pictures/company.png');
    $sEventPic = $oHTML->getPicture($this->getResourcePath().'/pictures/event.png');

    $sHTML = $oHTML->getListStart('', array('class' =>'searchFormFieldSelector'));

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_cname '));
      $sHTML.= $oHTML->getLink($sCompanyPic.' Company name', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_manager'));
      $sHTML.= $oHTML->getLink($sCompanyPic.' Account manager', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_relation'));
      $sHTML.= $oHTML->getLink($sCompanyPic.'Relation', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_industry'));
      $sHTML.= $oHTML->getLink($sCompanyPic.'Industry', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_phone'));
      $sHTML.= $oHTML->getLink($sCompanyPic.' Phone', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_email'));
      $sHTML.= $oHTML->getLink($sCompanyPic.' Email', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_synopsis'));
      $sHTML.= $oHTML->getLink($sCompanyPic.' Synopsis', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_address'));
      $sHTML.= $oHTML->getLink($sCompanyPic.' Address', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_evt_type'));
      $sHTML.= $oHTML->getLink($sEventPic.' Activity type', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_evt_content'));
      $sHTML.= $oHTML->getLink($sEventPic.' Activity content', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_evt_from search_evt_to'));
      $sHTML.= $oHTML->getLink($sEventPic.' Activities date', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_cf'));
      $sHTML.= $oHTML->getLink($sCompanyPic.' Custom field', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'formFieldContainer', 'onclick' => 'jQuery(this).siblings(\':not(#clear_value)\').addClass(\'fieldUsed\'); ')); //$(\'#clear_value\').removeClass(\'fieldUsed\');
      $sHTML.= $oHTML->getLink('All Fields', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'none', 'onclick' => 'jQuery(this).siblings().removeClass(\'fieldUsed\');'));
      $sHTML.= $oHTML->getLink('Hide all Fields', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('clear_value', array('fieldname' => 'formFieldContainer', 'onclick' => ' jQuery(this).siblings().removeClass(\'fieldUsed\');resetContactSearch();'));
      $sHTML.= $oHTML->getLink('Clear Values', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemEnd();

    $sHTML.= $oHTML->getListEnd();

    return $sHTML;
  }

  /**
   * Display the full search bloc including title, search form, flded icon
   * @param string $psSearchId
   * @param array $pavSearchResult
   * @param boolean $pbNewSearch
   * @return string html
   */
  private function _getContactSearchBloc($psSearchId, $pavSearchResult, $pbNewSearch = true)
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    //$gbNewSearch = true only if it's a new search (opposite of paging up/down and sorting)
    global $gbNewSearch;
    if($gbNewSearch)
    {
      $sExtraClass = '';
    }
    else
    {
      $sExtraClass = ' hidden ';
    }

    $sHTML = '';
    $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchOutterContainer'));

      $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchMenuIcon', 'onclick' => 'jQuery(this).parent().find(\'.searchContainer\').fadeToggle();'));
        $sHTML.= $oHTML->getPicture($this->getResourcePath().'pictures/main_search_icon.png', 'Search connections');
        $sHTML.= $oHTML->getCarriageReturn();
        $sHTML.= $oHTML->getSpanStart();
        $sHTML.= $oHTML->getText($pavSearchResult['nNbResult']);
        $sHTML.= $oHTML->getSpanEnd();
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('searchResult_'.$psSearchId, array('class' =>'searchContainer '.$sExtraClass));

        //Search title.
        $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchFormHeader'));
          $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchTitle'));
            $sSearchMessage = $this->_getSearchMessage($pavSearchResult['nNbResult']);
            if(!empty($sSearchMessage))
              $sHTML.= $sSearchMessage;
            else
              $sHTML.= $oHTML->getText('Search Connections');
          $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchOption'));

         $sHTML.= $oHTML->getBlocStart('', array('class' =>'searchOptionClose', 'onclick' => 'jQuery(\'.searchContainer\').fadeOut();'));
          $sHTML.= $oHTML->getBlocStart();
            $sHTML.= $oHTML->getText('Close');
          $sHTML.= $oHTML->getBlocEnd();
         $sHTML.= $oHTML->getBlocEnd();

          $sHTML.= $oHTML->getListStart('', array('onclick' => 'jQuery(\'li:not(:first)\', this).fadeToggle();'));

            $sHTML.= $oHTML->getListItemStart('', array('class' => ''));
            $sHTML.= 'Options ';
            $sHTML.= $oHTML->getListItemEnd();

            $sHTML.= $oHTML->getListItemStart('', array('class' => 'hidden', 'onclick' => 'alert(\'Coming soon.\');'));
            $sHTML.= '- Save search';
            $sHTML.= $oHTML->getListItemEnd();

            $sHTML.= $oHTML->getListItemStart('', array('class' => 'hidden', 'onclick' => 'alert(\'Coming soon.\');'));
            $sHTML.= '- Preferences';
            $sHTML.= $oHTML->getListItemEnd();

          $sHTML.= $oHTML->getListEnd();

        $sHTML.= $oHTML->getBlocEnd();

        //display search form
        $sHTML.= $oHTML->getBlocStart('contactsearchForm', array('class' =>'queryBuilderContainer'));

          $sHTML.= $oHTML->getBlocStart('', array('class' => 'searchFormSidebar'));
            $sHTML.= $this->_getContactSearchFormSidebar();
          $sHTML.= $oHTML->getBlocEnd();

          $sHTML.= $oHTML->getBlocStart('', array('class' => 'searchFormContainer'));
            $sHTML.= $this->_getContactSearchForm($psSearchId);
          $sHTML.= $oHTML->getBlocEnd();

          $sHTML.= $oHTML->getFloatHack();

        $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Search form for the contact
   * @return array of HTML
   */
  private function _getContactSearchForm($psSearchId, $pbNewSearch = true)
  {
    $nLoginPk = (int)getValue('loginpk', 0);

    $asFormFields = array('firstname', 'lastname', 'company', 'followerfk', 'contact_relation', 'position', 'tel', 'email', 'refID','search_bcmid', 'event', 'event_type', 'date_eventStart', 'date_eventEnd');

    $nFieldDisplayed = 0;
    foreach($asFormFields as $sFieldName)
    {
      $vValue = getValue($sFieldName);
      if(!empty($vValue))
        $nFieldDisplayed++;
    }
    $nFieldToDisplay = (6 - $nFieldDisplayed);

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oEvent = CDependency::getComponentUidByName('event');
    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_LIST, CONST_AB_TYPE_CONTACT);

    $oPage->addRequiredJsFile($this->getResourcePath().'js/addressbook.js');

    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('queryForm');
    $oForm->setFormParams('', true, array('action' => $sURL, 'submitLabel' => 'Search', 'ajaxTarget' => 'contactListContainer', 'ajaxCallback' => ' $(\".searchTitle\").click();'));
    $oForm->setFormDisplayParams(array('columns' => 2, 'noCancelButton' => '1', 'fullFloating' => true));

    $vField = getValue('firstname');
     $oForm->addField('input', 'firstname', array('label' => 'Firstname', 'value' =>$vField));
    $oForm->setFieldControl('firstname', array('jsFieldMinSize' => 2, 'jsFieldMaxSize' => 255));
    if(!$vField && $nFieldToDisplay)
    {
      //force displaying this field if less than 4 fields displayed
      $nFieldToDisplay--;
      $oForm->setFieldDisplayParams('firstname', array('class' => 'search_fname', 'fieldname' => 'search_fname'));
    }
    else
      $oForm->setFieldDisplayParams('firstname', array('class' => (($vField || $nFieldDisplayed++ < 4)?'':'hidden ').' search_fname', 'fieldname' => 'search_fname'));

    $vField = getValue('lastname');
    $oForm->addField('input', 'lastname', array('label' => 'Lastname', 'value' => $vField));
    $oForm->setFieldControl('lastname', array('jsFieldMinSize' => 2, 'jsFieldMaxSize' => 255));
    if(!$vField && $nFieldToDisplay)
    {
      $nFieldToDisplay--;
      $oForm->setFieldDisplayParams('lastname', array('class' => 'search_lname', 'fieldname' => 'search_lname'));
    }
    else
      $oForm->setFieldDisplayParams('lastname', array('class' => (($vField || $nFieldToDisplay < 3)?'':'hidden ').' search_lname', 'fieldname' => 'search_lname'));


    $vField = getValue('company');
    $oForm->addField('input', 'company', array('label' => 'Company', 'value' => $vField));
    $oForm->setFieldControl('company', array('jsFieldMinSize' => 2, 'jsFieldMaxSize' => 255));
    if(!$vField && $nFieldToDisplay)
    {
      $nFieldToDisplay--;
      $oForm->setFieldDisplayParams('company', array('class' => 'search_company', 'fieldname' => 'search_company'));
    }
    else
      $oForm->setFieldDisplayParams('company', array('class' => (($vField || $nFieldToDisplay < 2)?'':'hidden ').' search_company', 'fieldname' => 'search_company'));

    $vField = ($nLoginPk || getValue('followerfk', 0));
    $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_SEARCH, CONST_LOGIN_TYPE_USER);
    $oForm->addField('selector', 'followerfk', array('label'=>'Account Manager', 'url' => $sURL, 'onchange' =>'$(\'#cascading_id\').parent().parent().find(\'div\').show();'));
    $oForm->setFieldControl('followerfk', array('jsFieldTypeIntegerPositive' => ''));
    if(!$vField && $nFieldToDisplay)
    {
      //force displaying this field if less than 4 fields displayed
      $nFieldToDisplay--;
      $oForm->setFieldDisplayParams('followerfk', array('class' => 'search_manager', 'fieldname' => 'search_manager'));
    }
    else
      $oForm->setFieldDisplayParams('followerfk', array('class' => (($vField || $nFieldToDisplay < 1)?'':'hidden ').' search_manager', 'fieldname' => 'search_manager'));

    if(!empty($nLoginPk))
    {
      $oLogin = CDependency::getComponentByName('login');
      $asFolowerData = $oLogin->getUserDataByPk($nLoginPk);

      if(!empty($asFolowerData))
        $oForm->addOption('followerfk', array('value' => $nLoginPk, 'label' => $oLogin->getUsernameFromData($asFolowerData)));
    }
    else
    {
      $nFollwerfk = (int)getValue('followerfk', 0);
      if(!empty($nFollwerfk))
      {
        $oLogin =  CDependency::getComponentByName('login');
        $asFollowerData = $oLogin->getUserDataByPk($nFollwerfk);
        if(!empty($asFollowerData))
          $oForm->addOption('followerfk', array('value' => $nFollwerfk, 'label' => $oLogin->getUserNameFromData($asFollowerData)));
      }
    }

    $vField = getValue('contact_relation');
    $oForm->addField('select', 'contact_relation', array('label' => ' Relation'));
    if(!$vField && $nFieldToDisplay)
    {
      $nFieldToDisplay--;
      $oForm->setFieldDisplayParams('contact_relation', array('class' => 'search_relation', 'fieldname' => 'search_relation'));
    }
    else
      $oForm->setFieldDisplayParams('contact_relation', array('class' => (($vField)?'':'hidden ').' search_relation', 'fieldname' => 'search_relation'));

    $vField = (array)getValue('contact_industry');
    $oForm->addField('select', 'contact_industry[]', array('label' =>' Industry', 'multiple' => 'multiple'));
    if(!$vField && $nFieldToDisplay)
    {
      $nFieldToDisplay--;
      $oForm->setFieldDisplayParams('contact_industry[]', array('class' => 'search_industry', 'fieldname' => 'search_industry'));
    }
    else
      $oForm->setFieldDisplayParams('contact_industry[]', array('class' => (($vField)?'':'hidden ').' search_industry', 'fieldname' => 'search_industry'));

    $asIndustry = $this->_getIndustry();

    foreach($asIndustry as $nIndustryPk => $asIndustryData)
    {
      if(in_array($nIndustryPk, $vField))
        $oForm->addOption('contact_industry[]', array('value'=> $nIndustryPk, 'label' => $asIndustryData['industry_name'], 'selected' => 'selected'));
      else
        $oForm->addOption('contact_industry[]', array('value'=> $nIndustryPk, 'label' => $asIndustryData['industry_name']));
    }

    $asCompanyRel = getCompanyRelation();
    $sRelation = getValue('contact_relation');
    $oForm->addOption('contact_relation', array('value'=>'', 'label' => 'Select'));
    foreach($asCompanyRel as $sType => $vType)
    {
      if($sRelation==$sType)
        $oForm->addOption('contact_relation', array('value'=>$sType, 'label' => $vType['Label'],'selected'=>'selected'));
      else
        $oForm->addOption('contact_relation', array('value'=>$sType, 'label' => $vType['Label']));
    }

    $vField = getValue('position');
    $oForm->addField('input', 'position', array('label' => 'Position', 'value' => $vField));
    $oForm->setFieldControl('position', array('jsFieldMinSize' => 2, 'jsFieldMaxSize' => 255));
    $oForm->setFieldDisplayParams('position', array('class' => (($vField)?'':'hidden ').' search_position', 'fieldname' => 'search_position'));

    $vField = getValue('tel');
    $oForm->addField('input', 'tel', array('label' => 'Phone', 'value' => $vField));
    $oForm->setFieldControl('tel', array('jsFieldMinSize' => 4, 'jsFieldMaxSize' => 20));
    $oForm->setFieldDisplayParams('tel', array('class' => (($vField)?'':'hidden ').' search_phone', 'fieldname' => 'search_phone'));

    $vField = getValue('address');
    $oForm->addField('input', 'address', array('label' => 'Address', 'value' => $vField));
    $oForm->setFieldControl('address', array('jsFieldMinSize' => 4, 'jsFieldMaxSize' => 20));
    $oForm->setFieldDisplayParams('address', array('class' => (($vField)?'':'hidden ').' search_address', 'fieldname' => 'search_address'));

    $vField = getValue('email');
    $oForm->addField('input', 'email', array('label' => 'Email', 'value' => $vField));
    $oForm->setFieldControl('email', array('jsFieldMinSize' => 2));
    $oForm->setFieldDisplayParams('email', array('class' => 'hidden search_email'));
    $oForm->setFieldDisplayParams('email', array('class' => (($vField)?'':'hidden ').' search_email', 'fieldname' => 'search_email'));

    $vField = getValue('refID');
    $oForm->addField('input','refID', array('label' => 'Old CRM Ref. ID', 'value' => $vField));
    $oForm->setFieldControl('refID', array('jsFieldMinSize' => 1, 'jsFieldMaxSize' => 10));
    $oForm->setFieldDisplayParams('refID', array('class' => (($vField)?'':'hidden ').' search_refid', 'fieldname' => 'search_refid'));

    $vField = getValue('bcmPK');
    $oForm->addField('input','bcmPK', array('label' => 'BCM ConnectionID', 'value' => $vField));
    $oForm->setFieldControl('bcmPK', array('jsFieldMinSize' => 1, 'jsFieldMaxSize' => 10));
    $oForm->setFieldDisplayParams('bcmPK', array('class' => (($vField)?'':'hidden ').' search_bcmid', 'fieldname' => 'search_bcmid'));


    $oForm->addField('hidden', 'do_search', array('value' => 1));
    $oForm->addField('hidden', 'hidden_first'); $oForm->addField('hidden', 'hidden_second');

    if(!empty($oEvent))
    {
      $vField = getValue('event_type');
      $oForm->addField('select', 'event_type', array('label' => ' Type'));
      $oForm->setFieldDisplayParams('event_type', array('class' => (($vField)?'':'hidden ').' search_evt_type', 'fieldname' => 'search_evt_type'));
      $oForm->addOption('event_type', array('value'=>'', 'label' => 'Select'));

      $asEvent= getEventTypeList();
      $sEventTypes = getValue('event_type');
      foreach($asEvent as $asEvents)
      {
        if($asEvents['value'] == $sEventTypes)
          $oForm->addOption('event_type', array('value'=>$asEvents['value'], 'label' => $asEvents['label'], 'group' => $asEvents['group'], 'selected'=>'selected'));
        else
          $oForm->addOption('event_type', array('value'=>$asEvents['value'], 'label' => $asEvents['label'], 'group' => $asEvents['group']));
      }

      $vField = getValue('event');
      $oForm->addField('input', 'event', array('label' => ' Activity Content', 'value' => $vField));
      $oForm->setFieldControl('event', array('jsFieldMinSize' => 2));
      $oForm->setFieldDisplayParams('event', array('class' => (($vField)?'':'hidden ').' search_evt_content', 'fieldname' => 'search_evt_content'));

      $vField = getValue('date_eventStart');
      $oForm->addField('input', 'date_eventStart', array('type' => 'date', 'label'=>'Activity From', 'value' => $vField));
      $oForm->setFieldDisplayParams('date_eventStart', array('class' => (($vField)?'':'hidden ').' search_evt_from', 'fieldname' => 'search_evt_from'));

      $vField = getValue('date_eventEnd');
      $oForm->addField('input', 'date_eventEnd', array('type' => 'date', 'label'=>'Activity To', 'value' => $vField));
      $oForm->setFieldDisplayParams('date_eventEnd', array('class' => (($vField)?'':'hidden ').' search_evt_to', 'fieldname' => 'search_evt_to'));
    }

    if(isset($_POST['sortfield']))
        $sSortField = $_POST['sortfield'];
    else
        $sSortField = '';

    if(isset($_POST['sortorder']))
        $sSortOrder = $_POST['sortorder'];
    else
        $sSortOrder = '';

    $oForm->addField('hidden', 'sortfield', array('value' =>$sSortField));
    $oForm->addField('hidden', 'sortorder', array('value' => $sSortOrder));
    $oForm->addField('hidden', 'sortItem', array('value' =>'0'));

    return $oForm->getDisplay();
  }


  private function _getContactSearchFormSidebar()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sJavascript = 'jQuery(document).ready(function(){ ';

    $sJavascript.= '  jQuery(\'.searchFormFieldSelector li\').click(function(){ ';

    $sJavascript.= '    if(!jQuery(this).hasClass(\'fieldUsed\')) ';
    $sJavascript.= '      jQuery(this).addClass(\'fieldUsed\'); ';
    $sJavascript.= '    else ';
    $sJavascript.= '      jQuery(this).fadeOut(350, function(){ jQuery(this).css(\'border\', \'1px solid orange\'); }).
      fadeIn(350).fadeOut(350, function(){ jQuery(this).css(\'border\', \'1px solid orange\'); })
      .fadeIn(350, function(){ jQuery(this).css(\'border\', \'\'); }); ';

    $sJavascript.= '    var sFieldContainer = jQuery(this).attr(\'fieldname\'); ';
    $sJavascript.= '    var oFormContainer = jQuery(\'.\'+sFieldContainer).closest(\'.innerForm\'); ';

    $sJavascript.= '   if(sFieldContainer == \'none\') ';
    $sJavascript.= '   { removeFormField(null, \'.formFieldContainer\'); return true; }';

    $sJavascript.= '    var sFieldContainer = sFieldContainer.split(\' \').join(\', .\'); ';
    $sJavascript.= '    jQuery(oFormContainer).find(\'script\').html(\'\'); ';

    $sJavascript.= '    var oFieldContainer = jQuery(\'.\'+sFieldContainer+\':not(.formFieldHidden)\'); ';

    $sJavascript.= '    jQuery(oFieldContainer).each(function() ';
    $sJavascript.= '    { ';
    $sJavascript.= '      if(sFieldContainer == \'formFieldContainer\' ) ';
    $sJavascript.= '      {  displayFormField(this, null, true); } ';
    $sJavascript.= '      else ';
    $sJavascript.= '      { displayFormField(this);  jQuery(this).find(\'input,select,textarea\').focus(); } ';
    $sJavascript.= '    }); ';

    $sJavascript.= '  }); ';

    //when loading the page in php, we refresh the sidebar and add X link
    $sJavascript.= ' refreshFormField(); ';

    $sJavascript.= '}); ';
    $oPage->addCustomJs($sJavascript);
    $sConnectionPic = $oHTML->getPicture($this->getResourcePath().'/pictures/connection.png');
    $sCompanyPic = $oHTML->getPicture($this->getResourcePath().'/pictures/company.png');
    $sEventPic = $oHTML->getPicture($this->getResourcePath().'/pictures/event.png');

    $sHTML = $oHTML->getListStart('', array('class' =>'searchFormFieldSelector'));

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_lname search_fname'));
      $sHTML.= $oHTML->getLink($sConnectionPic.' Connection name', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_refid'));
      $sHTML.= $oHTML->getLink($sConnectionPic.'Old CRM RefID', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_bcmid'));
      $sHTML.= $oHTML->getLink($sConnectionPic.'Connection ID', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_company'));
      $sHTML.= $oHTML->getLink($sCompanyPic.' Company', 'javascript:;');

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_manager'));
      $sHTML.= $oHTML->getLink($sConnectionPic.' Account manager', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_relation'));
      $sHTML.= $oHTML->getLink($sConnectionPic.' Relation', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_industry'));
      $sHTML.= $oHTML->getLink($sConnectionPic.' Industry', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_position'));
      $sHTML.= $oHTML->getLink($sConnectionPic.' Position', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_phone'));
      $sHTML.= $oHTML->getLink($sConnectionPic.' Phone', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_address'));
      $sHTML.= $oHTML->getLink($sConnectionPic.' Address', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_email'));
      $sHTML.= $oHTML->getLink($sConnectionPic.' Email', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_evt_type'));
      $sHTML.= $oHTML->getLink($sEventPic.' Activity type', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_evt_content'));
      $sHTML.= $oHTML->getLink($sEventPic.' Activity content', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'search_evt_from search_evt_to'));
      $sHTML.= $oHTML->getLink($sEventPic.' Activities date', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'formFieldContainer', 'onclick' => 'jQuery(this).siblings(\':not(#clear_value)\').addClass(\'fieldUsed\'); ')); //$(\'#clear_value\').removeClass(\'fieldUsed\');
      $sHTML.= $oHTML->getLink('All Fields', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('', array('fieldname' => 'none', 'onclick' => 'jQuery(this).siblings().removeClass(\'fieldUsed\');'));
      $sHTML.= $oHTML->getLink('Hide all Fields', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemStart('clear_value', array('fieldname' => 'formFieldContainer', 'onclick' => ' jQuery(this).siblings().removeClass(\'fieldUsed\');resetContactSearch();'));
      $sHTML.= $oHTML->getLink('Clear Values', 'javascript:;');
      $sHTML.= $oHTML->getListItemEnd();

      $sHTML.= $oHTML->getListItemEnd();

    $sHTML.= $oHTML->getListEnd();

    return $sHTML;
  }

  /**
   * Based on search Parameters, return a string explaining the search
   * @return string: message
  */
  private function _getSearchMessage($pnNbResult = 0, $pasOrderDetail = array(), $pbOnlySort = false)
  {
    $sMessage = '';
    global $gbNewSearch;
    $oHTML = CDependency::getComponentByName('display');

    if(isset($pasOrderDetail['sortfield']) && !empty($pasOrderDetail['sortfield']))
    {
      $sSortMsg = $oHTML->getText(' - sorted by '.$pasOrderDetail['sortfield'].' '.$pasOrderDetail['sortorder'], array('class'=>'searchTitleSortMsg'));
      if($pbOnlySort)
        return $sSortMsg;
     }
     else
      $sSortMsg = $oHTML->getText('', array('class'=>'searchTitleSortMsg'));

    $sField = getValue('lastname');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' Last name : '.$sField, array('class'=>'normalText'));

    $sField = getValue('firstname');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' First name : '.$sField, array('class'=>'normalText'));

    $sField = getValue('tel');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' tel : '.$sField, array('class'=>'normalText'));

    $sField = getValue('email');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' email : '.$sField, array('class'=>'normalText'));

    $sField = getValue('address');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' Address : '.$sField, array('class'=>'normalText'));

    $sField = getValue('contact_relation');
    if(!empty($sField))
    {
      $asRelation = getCompanyRelation((int)$sField);
      $sMessage.= $oHTML->getText(' Contact Relation : '.$asRelation['Label'], array('class'=>'normalText'));
      }

    $sField = getValue('company');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' company : '.$sField, array('class'=>'normalText'));

    $sField = getValue('followerfk');
    if(!empty($sField))
    {
      $oLogin = CDependency::getComponentByName('login');
      $asLoginData = $oLogin->getUserDataByPk((int)$sField);
      $sMessage.= $oHTML->getText(' Account Manager : '.$oLogin->getUserNameFromData($asLoginData, true), array('class'=>'normalText'));
      }

     /*
    $sField = getValue('contact_industry');
    if(!empty($sField))
    {
     $asIndustry =  $this->_getIndustry($sField);
     $sIndustryName = '';
     foreach($asIndustry as $asIndustryName)
      {
        $sIndustryName.= $asIndustryName['industry_name'].', ';
      }
     $sMessage.= $oHTML->getText(' Industry : '.$sIndustryName, array('class'=>'normalText'));
    }*/

    $sField = getValue('position');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' position : '.$sField, array('class'=>'normalText'));

    $sField = getValue('character');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' position : '.$sField, array('class'=>'normalText'));

    $asField = (array)getValue('contact_industry');
    if(!empty($asField) && !empty($asField[0]))
      $sMessage.= $oHTML->getText(count($asField).' industries selected', array('class'=>'normalText'));

    $sField = getValue('event');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' event : '.$sField, array('class'=>'normalText'));

    $sField = getValue('date_eventStart');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' avtivities from : '.$sField, array('class'=>'normalText'));

    $sField = getValue('date_eventEnd');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' to: '.$sField, array('class'=>'normalText'));

    $sField = getValue('event_type');
    if(!empty($sField))
      $sMessage.= $oHTML->getText(' activity type : '.$sField, array('class'=>'normalText'));

    if(!empty($gbNewSearch) && !empty($sMessage))
      $sMessage = $oHTML->getText(' for ').$sMessage;

    return $oHTML->getText($pnNbResult.' results') . $sMessage.' '.$sSortMsg;
  }

  /**
   * Search Query for the contact
   * @return array of query
   */

  private function _getSqlContactSearch()
  {
    $srefID = getValue('refID');
    $sbcmPk = getValue('bcmPK');
    $sLastame = getValue('lastname');
    $sFirstame = getValue('firstname');
    $sTel = getValue('tel');
    $sEmail = getValue('email');
    $sCompany = getValue('company');
    $nFollowerfk = getValue('followerfk');
    $sPosition = getValue('position');
    $sAddress = getValue('address');
    $sCharacter = getValue('character');
    $sEvent = getValue('event');
    $sStartDate = getValue('date_eventStart');
    $sEndDate = getValue('date_eventEnd');
    $sEventType = getValue('event_type');
    $sRelation = getValue('contact_relation');
    $anIndustry = (array)getValue('contact_industry', array());
    $nLoginPk = getValue('loginpk');

    $sSearchMode = getValue('search_mode');
    $oDb = CDependency::getComponentByName('database');

    $asResult = array();
    $asResult['join'] = '';
    $asResult['where'] = '';
    $asResult['groupby'] = '';
    $asWhereSql = array();

    if(!empty($srefID))
      $asWhereSql[] = ' ct.externalkey = '.$oDb->dbEscapeString($srefID);

    if(!empty($sbcmPk))
      $asWhereSql[] = ' ct.contactpk = '.$oDb->dbEscapeString($sbcmPk);

    if(!empty($sLastame))
      $asWhereSql[] = ' lower(ct.lastname) LIKE '.$oDb->dbEscapeString('%'.  strtolower($sLastame).'%');

    if(!empty($sFirstame))
      $asWhereSql[] = ' lower(ct.firstname) LIKE '.$oDb->dbEscapeString('%'.strtolower($sFirstame).'%');

    if(!empty($sTel))
      $asWhereSql[] = ' ct.phone LIKE '.$oDb->dbEscapeString('%'.$sTel.'%');

    if(!empty($sEmail))
      $asWhereSql[] = ' lower(ct.email) LIKE '.$oDb->dbEscapeString('%'.strtolower($sEmail).'%');

    if(!empty($sAddress))
       $asWhereSql[] = ' lower(ct.address_1) LIKE '.$oDb->dbEscapeString('%'.strtolower($sAddress).'%').' OR lower(ct.address_2) LIKE '.$oDb->dbEscapeString('%'.strtolower($sAddress).'%');

    if(!empty($sCharacter))
      $asWhereSql[] = ' lower(ct.comments) like '.$oDb->dbEscapeString('%'.strtolower($sCharacter).'%');

    if(!empty($sRelation))
      $asWhereSql[] = ' ct.relationfk = '.$oDb->dbEscapeString($sRelation);

    if(!empty($anIndustry))
    {
      foreach($anIndustry as $vKey => $nIndustry)
        $anIndustry[$vKey] = $oDb->dbEscapeString($nIndustry);

      $asWhereSql[] = ' ind.industrypk IN ('.implode(',', $anIndustry).') ';
    }

    if(!empty($nFollowerfk))
      $asResult['join'].= ' INNER JOIN account_manager as acmn ON (acmn.contactfk = ct.contactpk AND acmn.loginfk='.$nFollowerfk.') OR  ct.followerfk = '.$oDb->dbEscapeString($nFollowerfk);

    if(!empty($nLoginPk))
      $asWhereSql[] = ' ct.followerfk = '.$oDb->dbEscapeString($nLoginPk);

    if(!empty($sCompany))
    {
      $asWhereSql[] = ' lower(cpt.company_name) like '.$oDb->dbEscapeString('%'.strtolower($sCompany).'%');
      $asResult['join'].= ' LEFT JOIN profil as p ON (p.contactfk = ct.contactpk and p.date_end IS NULL) ';
      $asResult['join'].= ' INNER JOIN company as cpt ON (cpt.companypk = p.companyfk AND lower(cpt.company_name) LIKE '.$oDb->dbEscapeString('%'.strtolower($sCompany).'%').') ';
     }
    if(!empty($sPosition))
     {
      $asResult['join'].= ' LEFT JOIN profil as pfl ON (pfl.contactfk = ct.contactpk and pfl.date_end IS NULL) ';
      $asWhereSql[] = ' lower(pfl.position) like '.$oDb->dbEscapeString('%'.strtolower($sPosition).'%');
     }

    if(!empty($sEvent) || !empty($sEventType) || (!empty($sStartDate) && !empty($sEndDate)))
    {
     $asResult['join'].= 'INNER JOIN event_link as evelnk ON (evelnk.cp_pk = ct.contactpk and evelnk.cp_type="ct")';
     $asResult['join'].= 'INNER JOIN event as even ON (even.eventpk = evelnk.eventfk)';
     if(!empty($sEvent))
      $asWhereSql[] = 'lower(even.title) like '.$oDb->dbEscapeString('%'.strtolower($sEvent).'%').' OR lower(even.content) like '.$oDb->dbEscapeString('%'.strtolower($sEvent).'%');
     if(!empty($sEventType))
      $asWhereSql[] = 'lower(even.type) like '.$oDb->dbEscapeString('%'.strtolower($sEventType).'%');

     if(!empty($sStartDate))
      $asWhereSql[] = ' date_format(even.date_display,"%Y-%m-%d") >= '.$oDb->dbEscapeString(date('Y-m-d',strtotime($sStartDate)));

     if(!empty($sEndDate))
      $asWhereSql[] = ' date_format(even.date_display,"%Y-%m-%d") <= '.$oDb->dbEscapeString(date('Y-m-d',strtotime($sEndDate)));

     if(!empty($sStartDate) && !empty($sEndDate))
     {
      $asWhereSql[] = ' date_format(even.date_display,"%Y-%m-%d") >= '.$oDb->dbEscapeString(date('Y-m-d',strtotime($sStartDate)));
      $asWhereSql[].= ' date_format(even.date_display,"%Y-%m-%d") <= '.$oDb->dbEscapeString(date('Y-m-d',strtotime($sEndDate)));
     }
    }

    if($sSearchMode == 'or')
      $asResult['where'] =  implode(' OR ', $asWhereSql);
    else
      $asResult['where'] = implode(' AND ', $asWhereSql);

    return $asResult;
  }

  /**
   * Display the Detail information of connection
   * @param array $pasContactData
   * @return string HTML
   */

  private function _getContactDetailTab($pasContactData)
  {
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');
    $oRight = CDependency::getComponentByName('right');
    $oCustomFields = CDependency::getComponentByName('customfields');
    $oWEBMAIL = CDependency::getComponentByName('webmail');
    $oWebmail = CDependency::getComponentUidByName('webmail');


    if(!assert('is_array($pasContactData) && !empty($pasContactData)'))
      return $oHTML->getBlocMessage('No data available.');

    $sCustomFields = $oCustomFields->getCustomfieldDisplay($this->csUid, $this->csAction, $this->csType, $this->getPk());
    $sCustomFields.= $oCustomFields->getCustomfieldDisplay($this->csUid, $this->csAction, $this->csType);

    $sHTML =  $oHTML->getBlocStart('',array('class'=>'containerClass'));

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Industry');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $oHTML->getText($pasContactData['industry_name']);
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Grade');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $oHTML->getText(getContactGrade($pasContactData['grade']));
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Nationality');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    if(!empty($pasContactData['nationalityfk']))
      $sHTML.= $oHTML->getText($this->getNationalityName((int)$pasContactData['nationalityfk']));
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('English');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    if($pasContactData['language'] == 1)
      $sLanguage = 'Yes';
    else
      $sLanguage = 'No';

    $sHTML.= $oHTML->getText($sLanguage);
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    if($pasContactData['phone'])
      $sPhone = $pasContactData['phone'];
    else
      $sPhone = $pasContactData['prfPhone'];

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Phone');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $oHTML->getText($sPhone);
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

   if(!empty($pasContactData['email']) || !empty($oWebmail))
    {
      $sJavascript = " $(document).ready(function(){ $('.webmailLink').mouseover(function(){ showEmailPopup($(this)); }); }); ";
      $oPage->addCustomJs($sJavascript);

      $sURL = $oWEBMAIL->getURL('webmail', CONST_ACTION_ADD, CONST_WEBMAIL,(int)$pasContactData['contactpk'],array('ppaty'=>CONST_AB_TYPE_CONTACT,'ppaid'=>(int)$pasContactData['contactpk']));
      $sAjax = $oHTML->getAjaxPopupJS($sURL, 'body', '', '650', '800', 1);
      $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/mailto.png', '', '', array('style' => 'float: right; margin-right: 20px;'));

      $sMailLink = $oHTML->getLink($sPic, 'javascript:;', array('onclick' => $sAjax, 'email' => $pasContactData['email'], 'class' => 'webmailLink', 'style' => 'color:#0D79BC;'));
    }
    else
      $sMailLink = '';

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Email');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
      if($pasContactData['email'])
        $sHTML.= $oHTML->getText($pasContactData['email'], array('style'=>'color:#0D79BC;'));
      else
        $sHTML.= $oHTML->getText($pasContactData['prfEmail'], array('style'=>'color:#0D79BC;'));

      $sHTML.= $sMailLink;

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Mobile');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $oHTML->getText($pasContactData['cellphone']);
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText(' Department');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $oHTML->getText($pasContactData['department_name']);
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $asUserData = $oLogin->getUserDataByPk((int)$pasContactData['updated_by']);
    $sUpdater = $oLogin->getUserNameFromData($asUserData);

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Last Edited');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $oHTML->getText(getFormatedDate('Y-m-d',$pasContactData['date_update']).' - by '.$sUpdater);
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Approx Age');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));

    if(!empty($pasContactData['birthdate']) && $pasContactData['birthdate']!='0000-00-00' )
    {
      $sToday = date('Y-m-d');
      $sAge = floor((strtotime($sToday)-strtotime($pasContactData['birthdate']))/(365*60*60*24));
     }
     else
      $sAge = '';

    $sHTML.= $oHTML->getText('').$sAge;
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $asUserData = $oLogin->getUserList((int)$pasContactData['created_by'],false,true);
    $sUser = $oLogin->getUserNameFromData(current($asUserData));

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Creation date');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $oHTML->getText(getFormatedDate('Y-m-d',$pasContactData['date_create']).' - by '.$sUser);
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection  addressBloc'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection'));
    $sHTML.= $oHTML->getText('Address');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
    $sHTML.= $this->_getAddress($pasContactData,',');
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $sCustomFields;

    $sHTML.= $oHTML->getBlocStart('', array('class' =>'floatHack'));

    if($oRight->canAccess('180-290','ppaa',CONST_CF_TYPE_CUSTOMFIELD,0))
      $sHTML.= $oCustomFields->getCustomFieldAddLink($this->_getUid(),'ppav','ct',(int)$pasContactData['contactpk']);

    $sHTML.= $oHTML->getBlocEnd();


     $sHTML.= $oHTML->getBlocEnd();
       return $sHTML;
  }

  /**
   * Get Profile data from database
   * @param integer $pnContactpk
   * @return array
  */
  private function _getProfileInfo($pnContactpk)
  {
    if(!assert('is_integer($pnContactpk) && !empty($pnContactpk)'))
      return array();

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM profil WHERE contactfk = '.$pnContactpk.' and companyfk=0 and date_end  IS NULL';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    $asResult = array();

    while($bRead)
    {
      $asResult[] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asResult;
  }

 /**
 * Display different profile of the connection
 * @param array $pasContactData
 * @param array $pasProfile
 * @return string HTML
 */
  private function _getContactProfileTab($pasContactData, $pasProfile)
  {
    if(!assert('is_array($pasContactData) && !empty($pasContactData)'))
      return '';

    if(!assert('is_array($pasProfile)'))
      return '';

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $nContactPk = (int)$pasContactData['contactpk'];
    $sURL = $oPage->geturl('addressbook', CONST_ACTION_ADD, CONST_AB_TYPE_COMPANY_RELATION, $nContactPk);

    $sAddLink = $oHTML->getPicture(CONST_PICTURE_ADD, 'Add a new business or personal profile', $sURL);
    $sAddLink.= $oHTML->getSpace(2);
    $sAddLink.= $oHTML->getLink('Add a new business or personal profile', $sURL);

    if(empty($pasProfile) || count($pasProfile) < 2)
      return $oHTML->getBlocMessage('No other profiles for this connection.<br /><br />'.$sAddLink , true);

    $sHTML = $oHTML->getBlocStart();
    $sHTML.= $sAddLink;
    $sHTML.= $oHTML->getCarriageReturn(2);
    $sHTML.= $oHTML->getBlocEnd();

    //Sort it and remove the first profile because it is default

    krsort($pasProfile);
    unset($pasProfile[0]);

    foreach($pasProfile as $asProfileData)
    {
      $nCompanyPk = (int)$asProfileData['companyfk'];
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'abTabProfile_container shadow'));

      //actions float on the right
      $sHTML.= $oHTML->getBlocStart('', array('style'=>'float:right; margin:5px;'));

        $sUrl = $oPage->getUrl('addressbook', CONST_ACTION_EDIT, CONST_AB_TYPE_COMPANY_RELATION, (int)$asProfileData['contactfk'],array('profileId'=>(int)$asProfileData['profilpk']));
        $sHTML.= $oHTML->getPicture(CONST_PICTURE_EDIT, 'Edit the profile', $sUrl);
        $sHTML.= $oHTML->getSpace(2);

        $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_DELETE, CONST_AB_TYPE_COMPANY_RELATION,(int)$asProfileData['contactfk'],array('profileId'=>(int)$asProfileData['profilpk']));
        $sPic = $oHTML->getPicture(CONST_PICTURE_DELETE, 'Delete profile' );
        $sHTML.= ' '.$oHTML->getLink($sPic, $sURL, array('onclick' => 'if(!window.confirm(\'Delete this profile ?\')){ return false; }'));

      $sHTML.= $oHTML->getBlocEnd('');

      //Top section business or personal data
      $sHTML.= $oHTML->getBlocStart();

      $sDate = $oHTML->getText(' - '.$asProfileData['date_update'], array('class' => 'abTabProfile_title_date'));

        if(!empty($nCompanyPk))
        {
          //business profile
          $sURL = $oPage->geturl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $nCompanyPk);
          $asCompany = $this->getCompanyByPk($nCompanyPk);
          $sCompanyName = $asCompany['company_name'];

          $sHTML.= $oHTML->getText('Business profile', array('class' => 'abTabProfile_title')).$sDate;
          $sHTML.= $oHTML->getCarriageReturn(2);
          $sHTML.= $oHTML->getText('Company: ');
          $sHTML.= $oHTML->getLink($sCompanyName, $sURL);
          if(!empty($asProfileData['position']))
          {
            $sHTML.= $oHTML->getCarriageReturn();
            $sHTML.= $oHTML->getText('working as : '.$asProfileData['position']);
          }

          $sHTML.= $oHTML->getCarriageReturn(2);
        }
        else
        {
          //other personal contact details
          $sHTML.= $oHTML->getText('Personal contact details', array('class' => 'abTabProfile_title')).$sDate;
          $sHTML.= $oHTML->getCarriageReturn(2);
        }

      $sHTML.= $oHTML->getBlocEnd('');



    $sHTML.= $oHTML->getBlocStart('', array('class' => 'abTabProfile_details'));

      if(!empty($asProfileData['comment']))
      {
        $sHTML.= $oHTML->getText('Comment: '.$asProfileData['comment']);
        $sHTML.= $oHTML->getCarriageReturn();
      }

      if($asProfileData['phone'])
      {
        $sHTML.= $oHTML->getText('Phone: ', array('class' => 'ab_view_strong')) . $asProfileData['phone'];
        $sHTML.= $oHTML->getCarriageReturn();
      }

      if($asProfileData['fax'])
      {
        $sHTML.= $oHTML->getText('Fax: ', array('class' => 'ab_view_strong')) . $asProfileData['fax'];
        $sHTML.= $oHTML->getCarriageReturn();
      }

      if($asProfileData['email'])
      {
        $sHTML.= $oHTML->getText('Email: ', array('class' => 'ab_view_strong')) . $asProfileData['email'];
        $sHTML.= $oHTML->getCarriageReturn();
      }

      $sAddress = $this->_getAddress($asProfileData);
      if(!empty($sAddress))
      {
        $sHTML.= $oHTML->getText('Address: ', array('class' => 'ab_view_strong'));
        $sHTML.= $oHTML->getCarriageReturn();
        $sHTML.= $oHTML->getBlocStart('', array('class' => 'abTabProfile_address'));
        $sHTML.= $sAddress;
        $sHTML.=  $oHTML->getBlocEnd();
      }

    $sHTML.=  $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
  }

  $sHTML.= $oHTML->getFloatHack();
  return $sHTML;
}

/**
 * Delete the connection profile
 * @param integer $pnPk
 * @return string|boolean
 */

  private function _getDeleteProfile($pnPk)
  {
    //update the status of the profile
     if(!assert('is_integer($pnPk) && !empty($pnPk)'))
      return array('');

     $oDB = CDependency::getComponentByName('database');
     $oPage = CDependency::getComponentByName('page');

     $nprofilePk = getValue('profileId');

     $sQuery = 'UPDATE profil SET date_end = "'.date('Y-m-d').'" WHERE profilpk='.$nprofilePk;
     $oDbResult = $oDB->ExecuteQuery($sQuery);

     if(!$oDbResult)
       return array('');

       $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $pnPk);
      return (array('notice'=>'Profile has been removed.', 'timedUrl' => $sURL));
  }

  /**
   * Display Co workes tab
   * @param type $pasContactData
   * @param type $pasProfile
   * @return string
   */

  private function _getContactCoworkersTab($pasContactData, $pasProfile)
  {


    if(!assert('is_array($pasContactData) && !empty($pasContactData)'))
      return '';

    if(!assert('is_array($pasProfile) '))
      return '';

    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');

    $nContactPk = (int)$pasContactData['contactpk'];

    $sHTML = '';
    $sHTML.= $oHTML->getBlocStart('', array('style' => ''));

    $nProfile = count($pasProfile);

    if(!empty($pasProfile))
    {
    foreach($pasProfile as $asProfileData)
    {
      $nCompanyPk = (int)$asProfileData['companypk'];
      $asCoworkers = $this->_getCompanyEmployees(array($nCompanyPk), array($nContactPk));

      if(!empty($asCoworkers))
      {
        $sURL = $oPage->geturl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_COMPANY, $nCompanyPk);
        $sHTML.= $oHTML->getBlocStart();
        $sTitle = $oHTML->getPicture($this->getResourcePath().'/pictures/detail_16.png', '', 'javascript:;', array('class' => 'co_worker_container',  'style' => 'height:9px;', 'onclick' => '$(\'.coworker_list:not(#coworkers_list_'.$nCompanyPk.')\').hide(); $(\'#coworkers_list_'.$nCompanyPk.'\').fadeToggle(); '));
        $sTitle.= $oHTML->getSpace(2);
        $sTitle.= $oHTML->getLink($asProfileData['company_name'], $sURL);
        $sHTML.= $oHTML->getTitle($sTitle, 'h4', false);

        if($nProfile > 1)
          $sHTML.= $oHTML->getBlocStart('coworkers_list_'.$nCompanyPk, array('class' => 'coworker_list hidden'));
        else
          $sHTML.= $oHTML->getBlocStart('coworkers_list_'.$nCompanyPk);

       $sHTML.= $this->_getContactRowSmallHeader();

        $nRow = 0;
        foreach($asCoworkers as $asContactData)
        {
          $sHTML.= $this->_getContactRow($asContactData, $nRow,1);
          $nRow++;
        }

        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocEnd();
       }
       else
        $sHTML.= $oHTML->getBlocMessage( 'No co-workers obtained for this connection');
      }
    }
    else
      $sHTML.= $oHTML->getBlocMessage( 'This connection doesn`t have company .');

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Display the company employees
   * @param array $panCompanyPk
   * @param array $panExcludedContact
   * @return array of records
   */

  private function _getCompanyEmployees($panCompanyPk = array(), $panExcludedContact = array())
  {
    if(!assert('is_array($panCompanyPk) && is_array($panExcludedContact)'))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT c.*,eve.*, group_concat(DISTINCT CONCAT(lg.firstname) SEPARATOR ",") as userfirstname, group_concat(DISTINCT CONCAT(lg.lastname) SEPARATOR ",") as userlastname,group_concat(DISTINCT ind.industry_name) as industry_name,';
    $sQuery.= ' GROUP_CONCAT(DISTINCT prf.position) AS position,GROUP_CONCAT(DISTINCT prf.department) AS department,';
    $sQuery.= ' GROUP_CONCAT(DISTINCT prf.email) AS profileEmail, GROUP_CONCAT(DISTINCT prf.phone) AS profilePhone,';
    $sQuery.= ' GROUP_CONCAT(DISTINCT prf.fax) AS profileFax, cp.company_name FROM contact as c ';
    $sQuery.= ' LEFT JOIN login AS lg ON (lg.loginpk = c.followerfk)';
    $sQuery.= ' INNER JOIN profil as prf ON (prf.contactfk = c.contactpk and prf.date_end IS NULL ';

    if(!empty($panExcludedContact))
       $sQuery.= ' AND c.contactpk NOT IN ('.implode(',', $panExcludedContact).') ';

    if(empty($panCompanyPk))
      $sQuery.= ' ) ';
    else
      $sQuery.= ' AND prf.companyfk IN ('.implode(',', $panCompanyPk).') ) ';

    $sQuery.= ' INNER JOIN company as cp ON (cp.companypk = prf.companyfk) ';
    $sQuery.= ' LEFT JOIN event_link as evel ON (c.contactpk = evel.cp_pk and evel.cp_type ="ct")';
    $sQuery.= ' LEFT JOIN event as eve ON (eve.eventpk = evel.eventfk)';
    $sQuery.= ' LEFT JOIN company_industry AS cmpid ON (cp.companypk = cmpid.companyfk)';
    $sQuery.= ' LEFT JOIN industry AS ind ON (cmpid.industryfk = ind.industrypk)';
    $sQuery.= ' GROUP BY c.contactpk ORDER BY cp.company_name, c.lastname, c.firstname';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    $asResult = array();
    while($bRead)
    {
      $asResult[] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asResult;
  }

  private function _getContactRowAction($pasContactData)
  {
     if(!assert('is_array($pasContactData) && !empty($pasContactData)'))
       return '';

    $oRight = CDependency::getComponentByName('right');
    $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_DELETE,$this->getType(),0);

    /*@var $oPage CPageEx */
    $oPage = CDependency::getComponentByName('page');
    /*@var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oEvent = CDependency::getComponentByName('event');

    $nContactPk = (int)$pasContactData['contactpk'];

    $sHTML = '';
    if(!empty($oEvent))
    {
      $sURL = $oPage->getUrl('event', CONST_ACTION_ADD, CONST_EVENT_TYPE_EVENT, 0, array(CONST_URL_ACTION_RETURN => CONST_ACTION_LIST));
      $sURL = $oPage->addUrlParams($sURL, array('cp_uid' => $this->_getUid(), 'cp_pk' => $nContactPk));
      $sHTML.= $oHTML->getPicture($oEvent->getResourcePath().'pictures/add_event_16.png', 'Add activity', $sURL);
      $sHTML.= $oHTML->getSpace(2);
    }
    $sURL = $oPage->getUrl('addressbook', CONST_ACTION_EDIT, CONST_AB_TYPE_CONTACT, $nContactPk, array(CONST_URL_ACTION_RETURN => CONST_ACTION_LIST));
    $sHTML.= $oHTML->getPicture(CONST_PICTURE_EDIT, 'Edit connection', $sURL);
    $sHTML.= $oHTML->getSpace(2);

    if($sAccess)
    {
      $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_DELETE, CONST_AB_TYPE_CONTACT, $nContactPk);
      $sPic= $oHTML->getPicture(CONST_PICTURE_DELETE);
      $sHTML.= ' '.$oHTML->getLink($sPic, $sURL, array('onclick' => 'if(!window.confirm(\'You are about to permanently delete this connection with all its linked data. \\nDo you really want to proceed ?\')){ return false; }'));
    }
    return $sHTML;
  }

  /**
   * Function to link the connecton to company
   * @param integer $pnPk
   * @return string
   */

  private function _getNewContactForm($pnPk)
  {
    if(!assert('is_integer($pnPk) && !empty($pnPk)'))
      return '';

    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'css/addressbook.css'));

    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SAVEMANAGE, CONST_AB_TYPE_CONTACT, $pnPk);

    $sHTML= $oHTML->getBlocStart();
    //div including the form
    $sHTML.= $oHTML->getBlocStart('');
    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('ctAddForm');
    $sFormId = $oForm->getFormId();
    $oForm->setFormParams('', true, array('submitLabel' => 'Save','action' => $sURL));

    $oForm->addField('misc', '', array('type' => 'text','text'=> '<span class="h4">Link the connection to existing Company </span><br /><br />'));

    $oForm->addField('input', 'email', array('label'=> 'Email Address', 'value' =>''));
    $oForm->setFieldControl('email', array('jsFieldTypeEmail' => '','jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

    $oForm->addField('input', 'phone', array('label'=> 'Phone', 'value' => ''));
    $oForm->setFieldControl('phone', array('jsFieldNotEmpty' => '','jsFieldMinSize' => 4));

    $oForm->addField('input', 'fax', array('label'=> 'Fax', 'value' => ''));
    $oForm->setFieldControl('fax', array('jsFieldMinSize' => 8));

    $oForm->addField('textarea', 'address', array('label'=> 'Address ', 'value' =>''));
    $oForm->setFieldControl('address', array('jsFieldNotEmpty' => ''));

    $oForm->addField('input', 'postcode', array('label'=> 'Postcode', 'value' => ''));
    $oForm->setFieldControl('postcode', array('jsFieldTypeIntegerPositive' => '', 'jsFieldMaxSize' => 12));

    $oForm->addField('selector_city', 'cityfk', array('label'=> 'City', 'url' => CONST_FORM_SELECTOR_URL_CITY));
    $oForm->setFieldControl('cityfk', array('jsFieldTypeIntegerPositive' => ''));

    $oForm->addField('selector_country', 'countryfk', array('label'=> 'Country', 'url' => CONST_FORM_SELECTOR_URL_COUNTRY));
    $oForm->setFieldControl('countryfk', array('jsFieldTypeIntegerPositive' => ''));
    $oForm->addField('misc', '', array('type'=> 'br'));

    $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SEARCH, CONST_AB_TYPE_COMPANY);
    $oForm->addField('selector', 'companyfk', array('label'=> 'Company', 'url' => $sURL, 'nbresult' => 1));

    $sHTML.= $oForm->getDisplay();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }


  private function _getContactForm($pnPk)
  {
    if(!assert('is_integer($pnPk)'))
      return '';

    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oLogin = CDependency::getComponentByName('login');
    $oPage->addCssFile(array($this->getResourcePath().'css/addressbook.css'));

    $oDB = CDependency::getComponentByName('database');
    $oDB->dbConnect();

    $oRight = CDependency::getComponentByName('right');
    $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_TRANSFER,$this->getType(),0);

    $asSelectManager = array();
    //If editing the contact
    if(!empty($pnPk))
    {
      $sQuery = 'SELECT c.*,p.department as department, GROUP_CONCAT(DISTINCT p.companyfk SEPARATOR ",") as profiles, GROUP_CONCAT(p.position SEPARATOR ",") as positions ';
      $sQuery.= ',l.lastname as follower_lastname, l.firstname as follower_firstname ';
      $sQuery.= ' FROM contact as c ';
      $sQuery.= ' INNER JOIN profil as p ON (p.contactfk = c.contactpk and p.companyfk !=0 and p.date_end is NULL) ';
      $sQuery.= ' LEFT JOIN login as l ON (l.loginpk = c.followerfk) ';
      $sQuery.= ' WHERE contactpk = '.$pnPk;

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
        return __LINE__.' - The contact doesn\'t exist.';
       }
     else
     {
      $oDbResult = new CDbResult();
      $nConpanyFk = (int)getValue('cppk', 0);
      $oDbResult->setFieldValue('profiles', $nConpanyFk);
     if(!empty($nConpanyFk))
      $asCompanyDetail = $this->getCompanyByPk($nConpanyFk);
      }

    if($oPage->getActionReturn())
      $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SAVEADD, CONST_AB_TYPE_CONTACT, $pnPk, array(CONST_URL_ACTION_RETURN => $oPage->getActionReturn()));
    else
      $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SAVEADD, CONST_AB_TYPE_CONTACT, $pnPk);

    $sHTML= $oHTML->getBlocStart();
    $sHTML.= $oHTML->getBlocStart('');

    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('ctAddForm');
    $sFormId = $oForm->getFormId();
    $oForm->setFormParams('', true, array('action' => $sURL,'submitLabel' => 'Save'));
    $oForm->addField('misc', '', array('type' => 'title','title'=> '<span class="h4">Add a connection to the company</span><hr>'));
    $oForm->addField('misc', 'profile', array('type' => 'text','text'=> '<span class="h4">Profile</span><hr>'));
    $oForm->addField('input', 'doubleChecked', array('type' => 'hidden', 'value' => (int)!empty($pnPk), 'id' => 'doubleCheckedId'));

    $sCourtesy = $oDbResult->getFieldValue('courtesy');
    $oForm->addField('select', 'courtesy', array('label'=>'Courtesy', 'class' => '', 'value' => $oDbResult->getFieldValue('courtesy')));
    $oForm->setFieldControl('courtesy', array('jsFieldNotEmpty' => ''));

    if($sCourtesy == 'mr')
      $oForm->addOption('courtesy', array('label' => ' Mr ', 'value' =>'mr', 'selected' =>'selected'));
    else
      $oForm->addOption('courtesy', array('label' => ' Mr ', 'value' =>'mr'));

    if($sCourtesy == 'ms')
      $oForm->addOption('courtesy', array('label' => ' Ms ', 'value' =>'ms', 'selected' =>'selected'));
    else
      $oForm->addOption('courtesy', array('label' => ' Ms ', 'value' =>'ms'));

    $oForm->addField('input', 'firstname', array('label'=> 'Firstname', 'value' => $oDbResult->getFieldValue('firstname')));
    $oForm->setFieldControl('firstname', array('jsFieldMinSize' => '2', 'jsFieldMaxSize' => 255));

    $oForm->addField('input', 'lastname', array('label'=>'Lastname', 'class' => '', 'value' => $oDbResult->getFieldValue('lastname')));
    $oForm->setFieldControl('lastname', array('jsFieldMinSize' => '2', 'jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

    if($pnPk)
    {
      $sToday = date('Y-m-d');
      $sBirthYear= $oDbResult->getFieldValue('birthdate');
      if(isset($sBirthYear)&& $sBirthYear!='0000-00-00')
          $sAge = floor((strtotime($sToday)-strtotime($sBirthYear))/(365*60*60*24));
      else
          $sAge = '';
    }
    else
      $sAge = '';

    $oForm->addField('input', 'birthdate', array('label'=> 'Approx Age', 'value' => $sAge ));
    $oForm->setFieldControl('birthdate', array('jsFieldMaxSize' => 2));

    $oForm->addField('input', 'email', array('label'=> 'Email', 'value' => $oDbResult->getFieldValue('email')));
    $oForm->setFieldControl('email', array('jsFieldTypeEmail' => '', 'jsFieldMaxSize' => 255));

    $oForm->addField('input', 'phone', array('label'=> 'Phone', 'value' => $oDbResult->getFieldValue('phone')));
    $oForm->setFieldControl('phone', array('jsFieldMinSize' => 4));

    $oForm->addField('input', 'cellphone', array('label'=> 'Mobile phone', 'value' => $oDbResult->getFieldValue('cellphone')));
    $oForm->setFieldControl('cellphone', array('jsFieldMinSize' => 8));

    $oForm->addField('input', 'fax', array('label'=> 'Fax', 'value' => $oDbResult->getFieldValue('fax')));
    $oForm->setFieldControl('fax', array('jsFieldMinSize' => 8));

    $oForm->addField('select', 'nationality', array('label' => 'Nationality'));
    $asNationality= $this->getNationality();

    $oForm->addOption('nationality', array('value'=>'', 'label' => 'Select'));
    foreach($asNationality as $kNationality=>$vNationality)
    {
    if($kNationality == $oDbResult->getFieldValue('nationalityfk'))
      $oForm->addOption('nationality', array('value'=>$kNationality, 'label' => $vNationality,'selected'=>'selected'));
      else
      $oForm->addOption('nationality', array('value'=>$kNationality, 'label' => $vNationality));
     }
    $oForm->addField('misc', '', array('type'=> 'br'));
    $oForm->addField('select', 'language', array('label' => 'English'));
    if($oDbResult->getFieldValue('language')==1)
        $oForm->addOption('language', array('value'=>1, 'label' => 'Yes','selected'=>'selected'));
    else
        $oForm->addOption('language', array('value'=>1, 'label' => 'Yes'));

    if($oDbResult->getFieldValue('language')==0)
        $oForm->addOption('language', array('value'=>0, 'label' => 'No','selected'=>'selected'));
    else
        $oForm->addOption('language', array('value'=>0, 'label' => 'No'));

    $oForm->addField('misc', '', array('type'=> 'br'));
    $oForm->addField('misc', '', array('type' => 'title', 'title'=> '<span class="h4">Business Profile</span><hr>'));
    $asProfiles = explode(',', $oDbResult->getFieldValue('profiles'));

    if(count($asProfiles) <= 1)
    {
        $sURL = $oPage->getAjaxUrl('addressbook', CONST_ACTION_SEARCH, CONST_AB_TYPE_COMPANY);
        $oForm->addField('selector', 'companyfk', array('label'=> 'Company', 'url' => $sURL, 'nbresult' => 1));

    //TODO: manage different profiles
    if($oDbResult->getFieldValue('profiles'))
    {
      $asCompany = $this->getCompanyByPk($asProfiles);

      foreach($asProfiles as $nCompany)
        $oForm->addOption('companyfk', array('label' => $asCompany[$nCompany]['company_name'], 'value' =>$nCompany));

      if(count($asProfiles) > 1)
        $oForm->addField('misc', '', array('type' => 'text', 'text'=> '!! Multi profile, half implemented: Display will be weird if you link a contact to multiple companies.<br />'));
    }
    $oForm->addField('input', 'position', array('label'=> 'Position', 'value' => $oDbResult->getFieldValue('positions')));
    $oForm->addField('misc', '', array('type'=> 'br'));

    $oForm->addField('input', 'department', array('label'=> 'Department', 'value' => $oDbResult->getFieldValue('department')));
    $oForm->setFieldControl('department', array('jsFieldMaxSize' => 255));

    if($sAccess || empty($pnPk))
    {
    $oForm->addField('select', 'account_manager[]', array('label' => 'Account Manager', 'multiple' => 'multiple'));
    $oForm->setFieldControl('account_manager[]', array('jsFieldNotEmpty' => ''));

    $asManagers = $oLogin->getUserList(0,false,true);
    if($oDbResult->getFieldValue('contactpk'))
     $asSelectManager = $this->_getAccountManager($oDbResult->getFieldValue('contactpk'),'ct');
    else
      $asSelectManager = $this->_getAccountManager('','');

    foreach($asManagers as $asManagerData)
    {
      if(in_array($asManagerData['loginpk'],$asSelectManager))
       $oForm->addOption('account_manager[]', array('value' => $asManagerData['loginpk'],'label' => $asManagerData['firstname'].' '.$asManagerData['lastname'], 'selected' => 'selected'));
       else
       $oForm->addOption('account_manager[]', array('value' => $asManagerData['loginpk'],'label' => $asManagerData['firstname'].' '.$asManagerData['lastname']));
     }
    }
    $oForm->addField('select', 'type', array('label' => 'Contact Relation'));
    $oForm->setFieldControl('type', array('jsFieldNotEmpty' => ''));

    if(!empty($asCompanyDetail))
    $sCompanyRelation = $asCompanyDetail['company_relation'];

    $asContactRelation= getCompanyRelation();
    $oForm->addOption('type', array('value'=>'', 'label' => 'Select'));
    foreach($asContactRelation as $sRelationType=>$vRelationType)
    {
     if(!empty($sCompanyRelation) && $sRelationType == $sCompanyRelation)
       $oForm->addOption('type', array('value'=>$sRelationType, 'label' => $vRelationType['Label'],'selected'=>'selected'));
     else if($sRelationType == $oDbResult->getFieldValue('relationfk'))
      $oForm->addOption('type', array('value'=>$sRelationType, 'label' => $vRelationType['Label'],'selected'=>'selected'));
     else
      $oForm->addOption('type', array('value'=>$sRelationType, 'label' => $vRelationType['Label']));
     }
    $oForm->addField('misc', '', array('type'=> 'br'));
    }
    else
      $oForm->addField('misc', '', array('type' => 'title', 'title'=> 'There are multiple profiles for this contact,please go to profile tab to edit profiles.<br/><br/>'));
      $oForm->addField('select', 'grade', array('label' => 'Grade'));
      $oForm->setFieldControl('grade', array('jsFieldNotEmpty' => ''));

      $asGrade= getContactGrade();

      foreach($asGrade as $sGradeType=>$vGradeType)
      {
      if($sGradeType == $oDbResult->getFieldValue('grade'))
        $oForm->addOption('grade', array('value'=>$sGradeType, 'label' => $vGradeType,'selected'=>'selected'));
        else
        $oForm->addOption('grade', array('value'=>$sGradeType, 'label' => $vGradeType));
        }
      $oForm->addField('misc', '', array('type'=> 'br'));
      $oForm->addField('textarea', 'comments', array('label'=> 'Character ', 'value' =>$oDbResult->getFieldValue('comments')));
      $oForm->setFieldControl('comments', array('jsFieldMinSize' => 5));
      $oForm->addField('misc', '', array('type' => 'title','title'=> '<span class="h4">Address</span><hr>'));

      $oForm->addField('textarea', 'address_1', array('label'=> 'Adress 1', 'value' => $oDbResult->getFieldValue('address_1')));
      $oForm->addField('textarea', 'address_2', array('label'=> 'Adress 2', 'value' => $oDbResult->getFieldValue('address_2')));
      $oForm->addField('input', 'postcode', array('label'=> 'Postcode', 'value' => $oDbResult->getFieldValue('postcode')));
      $oForm->setFieldControl('postcode', array('jsFieldTypeIntegerPositive' => '', 'jsFieldMaxSize' => 12));

      $oForm->addField('selector_city', 'cityfk', array('label'=> 'City', 'url' => CONST_FORM_SELECTOR_URL_CITY));
      $oForm->setFieldControl('cityfk', array('jsFieldTypeIntegerPositive' => ''));
      $nCityFk = $oDbResult->getFieldValue('cityfk', CONST_PHP_VARTYPE_INT);
      if(!empty($nCityFk))
        $oForm->addCitySelectorOption('cityfk', $nCityFk);

      $oForm->addField('selector_country', 'countryfk', array('label'=> 'Country', 'url' => CONST_FORM_SELECTOR_URL_COUNTRY));
      $oForm->setFieldControl('countryfk', array('jsFieldTypeIntegerPositive' => ''));
      $nCountryFk = $oDbResult->getFieldValue('countryfk', CONST_PHP_VARTYPE_INT);
      if(!empty($nCountryFk))
      {
        $asCountry = $oForm->getCountryData($nCountryFk);
        $oForm->addCountrySelectorOption ('countryfk', $nCountryFk);
      }
      else
        $oForm->addCountrySelectorOption ('countryfk', 107);

    $sHTML.= $oForm->getDisplay();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Get the Nationality
   * @return array of records
   */

  public function getNationality()
  {
      $oDB = CDependency::getComponentByName('database');

      $sQuery = 'SELECT * from nationality where 1 order by nationality_name asc';
      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();
      $asResult = array();
      while($bRead)
       {
          $asResult[$oResult->getFieldValue('nationalitypk')] = $oResult->getFieldValue('nationality_name');
          $bRead = $oResult->readNext();
       }
      return $asResult;
  }

  /**
   * Save the contact details
   * @param integer $pnContactPk
   * @return array
   */

  private function _getContactFormSaveAdd($pnContactPk)
  {
    if(!assert('is_integer($pnContactPk)'))
      return array('error' => 'No connection found.');

    $sCourtesy = getValue('courtesy');
    $sFirstname = getValue('firstname');
    $sLastname = getValue('lastname');

    if(empty($sCourtesy) || empty($sLastname))
      return array('error' => __LINE__.' - Courtesy and Lastname are required.');

    $sEmail = trim(getValue('email'));
    if(!empty($sEmail) && !filter_var($sEmail, FILTER_VALIDATE_EMAIL))
      return array('error' => __LINE__.' - Email format is incorrect.');

    $sPhone = getValue('phone');
    $sCellphone = getValue('cellphone');
    $sFax = getValue('fax');

    $sAddress1 = getValue('address_1');
    $sAddress2 = getValue('address_2');
    $sPostcode = getValue('postcode');
    $sComments = getValue('comments');
    $sPosition = getValue('position');
    $nNationality = getValue('nationality');
    $nLanguage = getValue('language');
    $sDepartment = getValue('department');
    $nCountryfk = (int)getValue('countryfk', 0);

    $asFollowers = getValue('account_manager');
    $sContactRelation = getValue('type');
    $sGrade = getValue('grade');
    $sBirthAge = (int)getValue('birthdate');
    $bDoubleEntryControl = (bool)getValue('doubleChecked', 0);
    if(!empty($sBirthAge))
    {
     $sYear = date('Y');
     $sBirthYear = date('Y-m-d',strtotime($sYear-$sBirthAge));
    }
    else
     $sBirthYear = '0000-00-00';

    $nCityfk = (int)getValue('cityfk', 0);
    if(!empty($nCityfk))
    {
      $oForm = CDependency::getComponentByName('form');
      $asCityData = $oForm->getCityData($nCityfk);

      if(empty($asCityData))
        return array('error' => __LINE__.' - Couldn\'t find the city you\'ve selected.');
    }

    $sCompanyFk = getValue('companyfk');

    if((!empty($sPosition) || !empty($sDepartment)) && empty($sCompanyFk))
     //if(empty($sCompanyFk))
       return array('alert'=>'Please select the company');

    if(empty($sPostcode) && !empty($nCityfk))
     $sPostcode = $asCityData['postcode'];
    if(empty($nCountryfk) && !empty($nCityfk))
     $nCountryfk = (int)$asCityData['countryfk'];

    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');
    $nUserFk = $oLogin->getUserPk();
    $bEdit = false;
    $asContactData = array();

    if(empty($pnContactPk)&& $bDoubleEntryControl==0)
    $sPopupHtml= $this->_getCheckDuplicates('ct',$sEmail,$sFirstname,$sLastname,$sAddress1,$sPhone);

    if(isset($sPopupHtml))
    return array('action' => ' setPopup("'.$sPopupHtml.'", "", "", 0, 450); ');

    if(!empty($pnContactPk))
    {
      $bEdit = true;

      $sQuery = 'SELECT *, GROUP_CONCAT(p.companyfk SEPARATOR ",") as profiles FROM contact as c ';
      $sQuery.= ' LEFT JOIN profil as p ON (p.contactfk = c.contactpk) ';
      $sQuery.= ' WHERE contactpk = '.$pnContactPk;
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();

      $asContactData = $oDbResult->getData();

      if(!$bRead)
        return array('error' => __LINE__.' - Couldn\'t find the contact you want to edit. It may have been deleted.');

      $sQuery = 'UPDATE contact SET courtesy = '.$oDB->dbEscapeString($sCourtesy).', ';
      $sQuery.= ' firstname = '.$oDB->dbEscapeString($sFirstname).', ';
      $sQuery.= ' lastname = '.$oDB->dbEscapeString($sLastname).', ';
      $sQuery.= ' email = '.$oDB->dbEscapeString($sEmail).', ';
      $sQuery.= ' birthdate = '.$oDB->dbEscapeString($sBirthYear).', ';
      $sQuery.= ' address_1 = '.$oDB->dbEscapeString($sAddress1).', ';
      $sQuery.= ' address_2 = '.$oDB->dbEscapeString($sAddress2).', ';
      $sQuery.= ' postcode = '.$oDB->dbEscapeString($sPostcode).', ';
      $sQuery.= ' cityfk = '.$oDB->dbEscapeString($nCityfk).', ';
      $sQuery.= ' countryfk = '.$oDB->dbEscapeString($nCountryfk).', ';
      $sQuery.= ' phone = '.$oDB->dbEscapeString($sPhone).', ';
      $sQuery.= ' cellphone = '.$oDB->dbEscapeString($sCellphone).', ';
      $sQuery.= ' fax = '.$oDB->dbEscapeString($sFax).', ';
      $sQuery.= ' grade = '.$oDB->dbEscapeString($sGrade).',';
      $sQuery.= ' date_update = '.$oDB->dbEscapeString(date('Y-m-d H:i:s')).', ';
      $sQuery.= ' updated_by = '.$oDB->dbEscapeString($nUserFk).',';
      $sQuery.= ' comments = '.$oDB->dbEscapeString($sComments).',';
      $sQuery.= ' language = '.$oDB->dbEscapeString($nLanguage).',';
      $sQuery.= ' nationalityfk = '.$oDB->dbEscapeString($nNationality).'';
      $sQuery.= ' WHERE contactpk = '.$pnContactPk;

      $oResult = $oDB->ExecuteQuery($sQuery);

      if(isset($sContactRelation) && !empty($sContactRelation))
      {
        $sQuery = 'UPDATE contact SET ';
        $sQuery.= ' relationfk = '.$oDB->dbEscapeString($sContactRelation).'';
        $sQuery.= ' WHERE contactpk = '.$pnContactPk;

        $oResult = $oDB->ExecuteQuery($sQuery);
      }

      if(isset($sCompanyFk) && !empty($sCompanyFk))
        $sQuery = 'UPDATE  `profil` SET position ='.$oDB->dbEscapeString($sPosition).',department ='.$oDB->dbEscapeString($sDepartment).',companyfk = '.$oDB->dbEscapeString($sCompanyFk).'  where contactfk='.$pnContactPk;
      else
       $sQuery = 'UPDATE  `profil` SET position ='.$oDB->dbEscapeString($sPosition).',department ='.$oDB->dbEscapeString($sDepartment).'  where contactfk='.$pnContactPk;

      $oDB->ExecuteQuery($sQuery);

      if(isset($asFollowers)&& !empty($asFollowers))
      {
        $nFollowerFk = (int)$asFollowers[0];
        $sQuery = ' UPDATE contact SET followerfk ='.$nFollowerFk.' WHERE contactpk = '.$pnContactPk;
        $oDB->ExecuteQuery($sQuery);

        array_shift($asFollowers);

        $sQuery = 'DELETE FROM account_manager WHERE contactfk='.$pnContactPk;
        $oDB->ExecuteQuery($sQuery);

        foreach($asFollowers as $asManagerData)
        {
          $sQuery = 'INSERT INTO account_manager(contactfk,loginfk) VALUES('.$pnContactPk.','.$asManagerData.')';
          $oDB->ExecuteQuery($sQuery);
        }
      }

      $sLink = $oPage->getUrl($this->csUid, CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $pnContactPk);
      $oLogin->getUserActivity($oLogin->getUserPk(), $this->csUid, CONST_ACTION_SAVEEDIT, CONST_AB_TYPE_CONTACT, $pnContactPk, '[upd] '.$sFirstname.' '.$sLastname, $sLink);
    }
    else
    {
      $sDateCreate = date('Y-m-d H:i:s');
      $nFollowerFk = (int)$asFollowers[0];
      if(empty($nFollowerFk))
        $nFollowerFk = $nUserFk;

      $sQuery = 'INSERT INTO `contact` (`courtesy` ,`firstname` ,`lastname` ,`birthdate` ,`email` ,`address_1` ,`address_2` ';
      $sQuery.= ' ,`postcode` ,`cityfk` ,`countryfk` ,`phone` ,`cellphone` ,`fax`, `followerfk`, `date_create`, `created_by`,';
      $sQuery.= ' `date_update`, `updated_by`,`comments`,`relationfk`,`grade`,language,nationalityfk) ';
      $sQuery.= ' VALUES('.$oDB->dbEscapeString($sCourtesy).', '.$oDB->dbEscapeString($sFirstname).', ';
      $sQuery.= $oDB->dbEscapeString($sLastname).', '.$oDB->dbEscapeString($sBirthYear).', ';
      $sQuery.= $oDB->dbEscapeString($sEmail).', ';
      $sQuery.= $oDB->dbEscapeString($sAddress1).', '.$oDB->dbEscapeString($sAddress2).', ';
      $sQuery.= $oDB->dbEscapeString($sPostcode).', '.$oDB->dbEscapeString($nCityfk).', ';
      $sQuery.= $oDB->dbEscapeString($nCountryfk).', '.$oDB->dbEscapeString($sPhone).', ';
      $sQuery.= $oDB->dbEscapeString($sCellphone).', '.$oDB->dbEscapeString($sFax).', '.$oDB->dbEscapeString($nFollowerFk).',';
      $sQuery.= $oDB->dbEscapeString($sDateCreate).', '.$oDB->dbEscapeString($nUserFk).', ';
      $sQuery.= $oDB->dbEscapeString($sDateCreate).', '.$oDB->dbEscapeString($nUserFk).','.$oDB->dbEscapeString($sComments).','.$oDB->dbEscapeString($sContactRelation).','.$oDB->dbEscapeString($sGrade).','.$oDB->dbEscapeString($nLanguage).','.$oDB->dbEscapeString($nNationality).') ';

      $oInsResult = $oDB->ExecuteQuery($sQuery);
      $pnContactPk = $oInsResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT);

      if(!$oInsResult)
        return array('error' => __LINE__.' - Couldn\'t save the contact');

      array_shift($asFollowers);

      foreach($asFollowers as $asManagerData)
      {
        $sQuery = 'INSERT INTO account_manager(contactfk,loginfk) VALUES('.$pnContactPk.','.$asManagerData.')';
        $oDB->ExecuteQuery($sQuery);
      }

      $asCompanyFk = explode(',', $sCompanyFk);
      $asSql = array();
      if(!empty($asCompanyFk))
      {
        if(isset($asContactData['profiles']))
          $asProfiles = explode(',', $asContactData['profiles']);
        else
          $asProfiles = array();

        foreach($asCompanyFk as $nCompanyfk)
        {
          if($nCompanyfk > 0)
          {
            if($bEdit)
            {
              //check if company changed
              if(!in_array($nCompanyfk, $asProfiles))
              {
                //add a new first profile
                $asSql[] = '('.$oDB->dbEscapeString($pnContactPk).', '.$oDB->dbEscapeString($nCompanyfk).', '.$oDB->dbEscapeString($sPosition).','.$oDB->dbEscapeString($sDepartment).')';
              }
            }
            else
            {
              //create first profile
              $asSql[] = '('.$oDB->dbEscapeString($pnContactPk).', '.$oDB->dbEscapeString($nCompanyfk).', '.$oDB->dbEscapeString($sPosition).','.$oDB->dbEscapeString($sDepartment).')';
            }
          }
        }

        if(!empty($asSql))
        {
          $sQuery = 'INSERT INTO `profil` (`contactfk` ,`companyfk` ,`position`,`department`) ';
          $sQuery.= ' VALUES '.implode(',', $asSql);

          $oResult = $oDB->ExecuteQuery($sQuery);
          if(!$oResult)
            return array('error' => __LINE__.' - Couldn\'t save the connection profile');
         }

         $nContactPk = (int)$oInsResult->getFieldValue('pk');
         $sLink = $oPage->getUrl($this->csUid, CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $nContactPk);
         $oLogin->getUserActivity($oLogin->getUserPk(), $this->csUid, CONST_ACTION_SAVEADD, CONST_AB_TYPE_CONTACT, $nContactPk, '[new] '.$sFirstname.' '.$sLastname, $sLink);
       }
    }

    $oPage = CDependency::getComponentByName('page');

    if($oPage->getActionReturn())
      $sURL = $oPage->getUrl('addressbook', $oPage->getActionReturn(), CONST_AB_TYPE_CONTACT, $pnContactPk);
    else
      $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $pnContactPk);

    return array('notice' => 'Connection saved.', 'url' => $sURL);
  }


  private function _getContactManageSave($pnContactPk)
  {
    if(!assert('is_integer($pnContactPk) && !empty($pnContactPk)'))
      return array('error' => 'No connection found.');

    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');

    $sEmail = getValue('email');
    $sPhone = getValue('phone');
    $sFax = getValue('fax');
    $sAddress1 = getValue('address');
    $sPostcode = getValue('postcode');
    $nCountryfk = (int)getValue('countryfk', 0);
    $nCityfk = (int)getValue('cityfk', 0);
    $nCompanyfk = (int)getValue('companyfk',0);

    if(empty($nCompanyfk))
      return array('alert'=>'Please select the company');

    $sQuery = 'SELECT * FROM `profil` WHERE contactfk='.$pnContactPk.'';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if($bRead)
      $nExistCompanyfk =  $oDbResult->getFieldValue('companyfk');

    if(empty($nExistCompanyfk) || !empty($nCompanyfk))
    {
      $sQuery = 'UPDATE profil SET companyfk = '.$oDB->dbEscapeString($nCompanyfk).',email = '.$oDB->dbEscapeString($sEmail).',phone = '.$oDB->dbEscapeString($sPhone).',fax = '.$oDB->dbEscapeString($sFax).',';
      $sQuery.= ' address_1 = '.$oDB->dbEscapeString($sAddress1).',postcode = '.$oDB->dbEscapeString($sPostcode).',cityfk = '.$oDB->dbEscapeString($nCityfk).', countryfk = '.$oDB->dbEscapeString($nCountryfk).' WHERE ';
      $sQuery.= ' contactfk = '.$pnContactPk;

      $oResult = $oDB->ExecuteQuery($sQuery);
      }
    else
    {
     $sQuery = 'INSERT INTO `profil` (`contactfk` ,`companyfk`,`email` ,`phone` ,`fax`,`address_1` ,`postcode` ,`cityfk` ,`countryfk`) ';
     $sQuery.= ' VALUES('.$oDB->dbEscapeString($pnContactPk).','.$oDB->dbEscapeString($nCompanyfk).', '.$oDB->dbEscapeString($sEmail).','.$oDB->dbEscapeString($sPhone).', '.$oDB->dbEscapeString($sFax).',';
     $sQuery.= $oDB->dbEscapeString($sAddress1).', '.$oDB->dbEscapeString($sPostcode).', '.$oDB->dbEscapeString($nCityfk).' ';
     $sQuery.= $oDB->dbEscapeString($nCountryfk).')';

     $oResult = $oDB->ExecuteQuery($sQuery);
    }
    if(!$oResult)
      return array('error' => __LINE__.' - Couldn\'t save the connection details');

    $sURL = $oPage->getUrl('addressbook', CONST_ACTION_VIEW, CONST_AB_TYPE_CONTACT, $pnContactPk);
    return array('notice' => 'Connection details has been updated', 'url' => $sURL);
  }

  /**
   * Fetch all data of the Pked company
   * @param integer $pnCompanyPk
   * @return array of company data
   */
  public function getContactByPk($pnContactPk)
  {
    if(!assert('is_integer($pnContactPk) && !empty($pnContactPk)'))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $oDB->dbConnect();

    $sQuery = 'SELECT * FROM `contact` WHERE contactpk = '.$pnContactPk;
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
      return array();

    return  $oDbResult->getData();
  }

  /**
   * Remove the contact from the system
   * @param integer $pnPk
   * @return array of message
   */

  private function _getContactDelete($pnContactPk)
  {
    if(!assert('is_integer($pnContactPk) && !empty($pnContactPk)'))
      return array('error' => 'No connection found. It may have already been deleted.');

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM `contact` WHERE contactpk = '.$pnContactPk.' ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array('error' => __LINE__.' - No connection to delete.');

    $sQuery = 'DELETE FROM contact WHERE contactpk = '.$pnContactPk.' ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    if(!$oDbResult)
      return array('error' => __LINE__.' - Couldn\'t delete the contact');
    else
    {
      $sQuery = 'DELETE FROM profil WHERE contactfk = '.$pnContactPk.' ';
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      if(!$oDbResult)
        return array('error' => __LINE__.' - Couldn\'t delete profile');

    }
    $oPage = CDependency::getComponentByName('page');
    return array('notice' => 'Connection has been deleted.', 'timedUrl' => $oPage->getUrl('addressbook', CONST_ACTION_LIST, CONST_AB_TYPE_CONTACT));

  }

/* ******************************************************************************** */
/* ******************************************************************************** */
/* ******************************************************************************** */
/* ******************************************************************************** */
/* ******************************************************************************** */
/* ******************************************************************************** */
/* *********************** Global / generic functions ***************************** */
/* ******************************************************************************** */
/* ******************************************************************************** */
/* ******************************************************************************** */
/* ******************************************************************************** */
/* ******************************************************************************** */


  private function _getCheckDuplicates($psType,$psEmail,$psFirstName,$psLastName,$psAddress,$psPhone)
  {
    $oDB = CDependency::getComponentByName('database');
    $oHTML = CDependency::getComponentByName('display');

    if($psType == CONST_AB_TYPE_COMPANY)
    {
     if(!empty($psFirstName))
     {
      $sQuery = 'SELECT company.*, ';
      $sQuery.= ' IF(lower(company_name) = '.$oDB->dbEscapeString(strtolower($psFirstName)).', 5, 0) as r1 ';

      $nCount = 1;
      $asQuery = array();

      if(!empty($psLastName))
        $asQuery['corporate_name'] = ', IF(lower(corporate_name) LIKE '.$oDB->dbEscapeString(strtolower($psLastName)).', 1, 0) as r'.++$nCount;

      if(!empty($psEmail))
        $asQuery['email'] = ', IF(email = '.$oDB->dbEscapeString($psEmail).', 5, 0) as r'.++$nCount;

      if(!empty($psPhone))
        $asQuery['phone'] = ', IF(phone LIKE '.$oDB->dbEscapeString($psPhone).', 3, 0) as r'.++$nCount;

      $sQuery.= implode($asQuery, ' ').' FROM company WHERE  ';
      $sQuery.= ' lower(company_name) LIKE '.$oDB->dbEscapeString(strtolower($psFirstName)).'  ';

      if(!empty($psEmail))
        $sQuery.= ' OR email = '.$oDB->dbEscapeString($psEmail).' ';

      if(!empty($psPhone))
        $sQuery.= ' OR phone LIKE '.$oDB->dbEscapeString($psPhone).' ';

      $sMainQuery = 'SELECT (r1';
      for($nKey=2; $nKey <= $nCount; $nKey++)
        $sMainQuery.= '+r'.$nKey;

      $sMainQuery.= ') as nTotal, T.* FROM ('.$sQuery.') as T HAVING nTotal > 4 ORDER BY nTotal DESC, company_name, date_create LIMIT 10 ';

      $oDbResult = $oDB->ExecuteQuery($sMainQuery);
      $bRead = $oDbResult->readFirst();
      $asMatch = array();
      $asField = array_keys($asQuery);

      while($bRead)
      {
        $nMatching = $oDbResult->getFieldValue('nTotal');
        $sName = $oDbResult->getFieldValue('company_name');
        if($oDbResult->getFieldValue('corporate_name'))
          $sName.= ' ('.$oDbResult->getFieldValue('corporate_name').')';

         $sLine = "<div style='width:350px;min-height:30px;border-radius:5px 5px 5px 5px;box-shadow: 4px 4px 5px #DDDDDD;border:1px solid #93C0FF;'> <div > <span class='h4'> Name : ".$sName."</div></div> ";

        $asMatch[] = $sLine.'<br />';
        $bRead = $oDbResult->readNext();
       }

      if(count($asMatch) > 0)
      {
        if(count($asMatch) >= 10)
          $asMatch[] = '... more results ...';

        $sPopupHtml = '<div class=\'doubleEntryContainer\'><strong>Multiple entries are matching with this company.</strong><br /> ';
        $sPopupHtml.= "<div style ='margin-top:5px;'><span class='h4'>".$psFirstName." </span></div>";
        $sPopupHtml.= "<div style='margin-top:5px;margin-bottom:10px;'> Matches With following records </div>";
        $sPopupHtml.= implode('',$asMatch);
        $sPopupHtml.= '<br /></div><br />';
        $sPopupHtml.= 'Are you sure to create this new company ?<br /><br />';
        $sPopupHtml.= '<strong><a href=\'javascript:;\' onclick=\'$(\"#doubleCheckedId\").val(1); alert($(\"#doubleCheckedId\").val());  $(\"form[name=cpAddForm] input[type=submit]\").click();\'>Yes </a></strong>';
        $sPopupHtml.= $oHTML->getSpace(4);
        $sPopupHtml.= '<strong><a href=\'javascript:;\' onclick = \'removePopup();\'> No </a></strong>';
        if(count($asMatch) > 0)
          return $sPopupHtml;

        }
      }
   }

   if($psType == CONST_AB_TYPE_CONTACT)
    {
      $sQuery = 'SELECT contact.*, ';
      $sQuery.= 'IF(lower(lastname) = '.$oDB->dbEscapeString(strtolower($psLastName)).', 5, 0) as r1 ';

      $nCount = 1;
      $asQuery = array();

      if(!empty($psFirstName))
        $asQuery['firstname'] = ', IF(lower(firstname) = '.$oDB->dbEscapeString(strtolower($psFirstName)).', 1, 0) as r'.++$nCount;

      if(!empty($psEmail))
        $asQuery['email'] = ', IF(email LIKE '.$oDB->dbEscapeString($psEmail).', 5, 0) as r'.++$nCount;

      if(!empty($psPhone))
        $asQuery['phone'] = ', IF(phone LIKE '.$oDB->dbEscapeString($psPhone).', 3, 0) as r'.++$nCount;

      $sQuery.= implode($asQuery, ' ').' FROM contact WHERE  ';
      $sQuery.= ' lower(lastname) = '.$oDB->dbEscapeString(strtolower($psLastName)).'  ';

      if(!empty($psFirstName))
        $sQuery.= ' AND lower(firstname) = '.$oDB->dbEscapeString(strtolower($psFirstName)).' ';

      if(!empty($psEmail))
        $sQuery.= ' OR email LIKE '.$oDB->dbEscapeString($psEmail).' ';

      if(!empty($psPhone))
        $sQuery.= ' OR phone LIKE '.$oDB->dbEscapeString($psPhone).' ';

      $sMainQuery = 'SELECT (r1';
      for($nKey=2; $nKey <= $nCount; $nKey++)
        $sMainQuery.= '+r'.$nKey;

      $sMainQuery.= ') as nTotal, T.* FROM ('.$sQuery.') as T HAVING nTotal > 4 ORDER BY nTotal DESC, lastname, firstname LIMIT 10 ';

      $oDbResult = $oDB->ExecuteQuery($sMainQuery);
      $bRead = $oDbResult->readFirst();
      $asMatch = array();
      $asField = array_keys($asQuery);

      while($bRead)
      {
        $nMatching = $oDbResult->getFieldValue('nTotal');
        $sName = $oDbResult->getFieldValue('courtsey').' '.$oDbResult->getFieldValue('firstname').' '.$oDbResult->getFieldValue('lastname');
        $asContactDetail = $this->getContactByPk($oDbResult->getFieldValue('contactpk',CONST_PHP_VARTYPE_INT));

        $nCompanyPk = $this->_getContactCompany($oDbResult->getFieldValue('contactpk',CONST_PHP_VARTYPE_INT));
        if(!empty($nCompanyPk))
        $asCompanyDetail = $this->getCompanyByPk($nCompanyPk);

        $sLine = "<div style='width:350px;min-height:40px;border-radius:5px 5px 5px 5px;box-shadow: 4px 4px 5px #DDDDDD;border:1px solid #93C0FF;'> <div > <span class='h4'> Name : ".$asContactDetail['lastname']." ".$asContactDetail['firstname']."</div> ";
        if(!empty($asCompanyDetail))
        $sLine.= "<div><span class='h4'> Company Name : ".$asCompanyDetail['company_name']."</span></div></div>";

        $asMatch[] = $sLine.'<br />';
        $bRead = $oDbResult->readNext();
      }

      if(count($asMatch) > 0)
      {
        if(count($asMatch) >= 10)
          $asMatch[] = '... more results ...';

        $sPopupHtml = '<div class=\'doubleEntryContainer\'><strong>Multiple entries are matching with this connection.</strong><br /> ';
        $sPopupHtml.= "<div style ='margin-top:5px;'><span class='h4'>".$psLastName." ".$psFirstName." </span></div>";
        $sPopupHtml.= "<div style='margin-top:5px;margin-bottom:10px;'> Matches With following records </div>";
        $sPopupHtml.= implode('<br />',$asMatch);
        $sPopupHtml.= '<br /></div><br />';
        $sPopupHtml.= 'Are you sure to create this new connection ?<br /><br />';
        $sPopupHtml.= '<strong><a href=\'javascript:;\' onclick=\'$(\"#doubleCheckedId\").val(1); $(\"[name=ctAddForm]\").submit();\'>  Yes  </a></strong>';
        $sPopupHtml.= $oHTML->getSpace(4);
        $sPopupHtml.= '<strong><a href=\'javascript:;\' onclick = \'removePopup();\'> No </a></strong>';

        if(count($asMatch) > 0)
          return $sPopupHtml;

      }
    }
 }


  private function _getContactCompany($pnContactPk)
  {
    if(!assert('is_integer($pnContactPk) && !empty($pnContactPk)'))
      return 0;

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * from profil where contactfk ='.$pnContactPk.' and companyfk != 0';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if($bRead)
      return $oDbResult->getFieldValue('companyfk', CONST_PHP_VARTYPE_INT);

    return 0;
  }

  /**
   * Get the Name of company or conenction
   * @param string $psItemType
   * @param integer $pnPk
   * @return string
   */

  public function getItemName($psItemType,$pnPk)
  {
    if(!empty($pnPk))
    {
     if(!assert('is_integer($pnPk)'))
       return '';
     $oDB = CDependency::getComponentByName('database');

     if($psItemType==CONST_AB_TYPE_COMPANY)
     {
        $sQuery = 'select company_name from company where companypk='.$pnPk.'';
        $oResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oResult->readFirst();
        $asData = $oResult->getData();
        $sItemName = $asData['company_name'];
     }
     else
     {
        $sQuery = 'select firstname,lastname from contact where contactpk='.$pnPk.'';
        $oResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oResult->readFirst();
        $asData = $oResult->getData();
        $sItemName = $asData['firstname'].' '.$asData['lastname'];
      }
    }
    else
      $sItemName = '';

     return $sItemName;
  }


  /**
   * Function to return the address
   * @param array $pasData
   * @param string $psSeparator
   * @return string
   */

  private function _getAddress($pasData, $psSeparator = 'br')
  {
    if(!assert('is_array($pasData) && !empty($pasData)'))
      return '';

    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oHTML = CDependency::getComponentByName('display');
    $sHTML = '';
    $psSeparator = 'br';
    switch($psSeparator)
    {
      case 'space':
      case ' ': $psSeparator = $oHTML->getSpace(2); break;
      case 'br': $psSeparator = $oHTML->getCarriageReturn(); break;
      case ',': $psSeparator = $oHTML->getText(" , "); break;
    }

    if(isset($pasData['address_1']) && !empty($pasData['address_1']))
      $sHTML.= $oHTML->getText($pasData['address_1']);
    else if(isset($pasData['prfAddress']) && !empty($pasData['prfAddress']))
       $sHTML.= $oHTML->getText($pasData['prfAddress']);
    else
        $sHTML.= '';

    if(isset($pasData['address_2']) && !empty($pasData['address_2']))
    {
      $sHTML.= $psSeparator;
      $sHTML.= $oHTML->getText($pasData['address_2']);
    }

    if(isset($pasData['ctpostcode']) && !empty($pasData['ctpostcode']))
    {
      $sHTML.= $oHTML->getText($pasData['ctpostcode']);
      $sHTML.= $oHTML->getSpace(2);
    }

    if(isset($pasData['EngLocal']) && !empty($pasData['EngLocal']))
    {
     $sHTML.= ',';
     $sHTML.= $oHTML->getText($pasData['EngLocal'].' '.$pasData['EngCity']);
    }

    if(isset($pasData['country_name']) && !empty($pasData['country_name']))
    {
      if(!empty($pasData['country_name']))
        $sHTML.= $psSeparator;

      $sHTML.= $oHTML->getText($pasData['country_name']);
    }

    return $sHTML;
  }

  /**
   * Function to give autocomplete with connection data
   * @return string
   */

  private function _getSelectorContact()
  {
    $sSearch = getValue('q');
    if(empty($sSearch))
      return json_encode(array());

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM contact WHERE lastname LIKE '.$oDB->dbEscapeString('%'.$sSearch.'%').' OR firstname LIKE '.$oDB->dbEscapeString('%'.$sSearch.'%').' ORDER BY lastname, firstname ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return json_encode(array());

    $asJsonData = array();
    while($bRead)
    {
      $asData['id'] = $oDbResult->getFieldValue('contactpk');
      $asData['name'] = '#'.$asData['id'].' - '.$oDbResult->getFieldValue('firstname').' '.$oDbResult->getFieldValue('lastname');
      $asJsonData[] = json_encode($asData);
      $bRead = $oDbResult->readNext();
    }

    echo '['.implode(',', $asJsonData).']';
  }

  /**
   * Function to display company/connection details from pk
   * @param type $psItemType
   * @param type $pnPk
   * @return string
   */

  public function getItemCardByPk($psItemType,$pnPk)
  {
     if(!assert('is_integer($pnPk) && !empty($pnPk)'))
       return array();

     $oDB = CDependency::getComponentByName('database');
     $oHTML = CDependency::getComponentByName('display');

     if($psItemType==CONST_AB_TYPE_COMPANY)
     {
        $sQuery = ' SELECT * FROM company WHERE companypk='.$pnPk.'';
        $oResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oResult->readFirst();
        $asData = $oResult->getData();

        $sHTML = $oHTML->getBlocStart('',array('style'=>'border:1px solid #CECECE;margin:5px;padding:5px;'));
        $sHTML.= $oHTML->getBlocStart('');
        $sHTML.= $oHTML->getText('Company Name :'.$asData['company_name']);
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocStart('');
        $sHTML.= $oHTML->getText('Phone :'.$asData['phone']);
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocStart('');
        $sHTML.= $oHTML->getText('Address :');
        $sHTML.= $this->_getAddress($asData,',');
        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= $oHTML->getBlocEnd();

     }
     else if($psItemType==CONST_AB_TYPE_CONTACT)
     {
       $sQuery = 'SELECT ct.phone as phonenum,prf.companyfk as companyfk,ct.*  FROM contact as ct LEFT JOIN profil as prf ON prf.contactfk = ct.contactpk  WHERE  ct.contactpk= '.$pnPk.'';
       $oResult = $oDB->ExecuteQuery($sQuery);
       $bRead = $oResult->readFirst();
       $asData = $oResult->getData();

       $sHTML = $oHTML->getBlocStart('',array('style'=>'border:1px solid #CECECE;margin:5px;padding:5px;'));
       $sHTML.= $oHTML->getBlocStart('');
       $sHTML.= $oHTML->getText(' Name :'.$this->getItemName('ct',$pnPk));
       $sHTML.= $oHTML->getBlocEnd();

       if(isset($asData['companyfk']) && !empty($asData['companyfk']))
       {
        $sHTML.= $oHTML->getBlocStart('');
        $sHTML.= $oHTML->getText('Company Name:'.$this->getItemName('cp',(int)$asData['companyfk']) );
        $sHTML.= $oHTML->getBlocEnd();
       }

       if(isset($asData['phonenum']) && !empty($asData['phonenum']))
       {
        $sHTML.= $oHTML->getBlocStart('');
        $sHTML.= $oHTML->getText('Phone :'.$asData['phonenum']);
        $sHTML.= $oHTML->getBlocEnd('');
       }

       $sHTML.= $oHTML->getBlocStart('');
       $sHTML.= $oHTML->getText('Address :');
       $sHTML.= $this->_getAddress($asData,',');
       $sHTML.= $oHTML->getBlocEnd();
       $sHTML.= $oHTML->getBlocEnd();
     }
     else
      $sHTML.= '';

    return  $sHTML;
  }

  /**
   * Get the Nationality name
   * @param integer $pnPK
   * @return string
   */

  public function getNationalityName($pnPK)
  {
    if(!assert('is_integer($pnPK) && !empty($pnPK)'))
     return '';

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM nationality WHERE nationalitypk = '.$pnPK;
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!empty($bRead))
      return $oDbResult->getFieldValue('nationality_name');

    return '';
  }

 private function _getDocumentSend($pnPk)
 {
    if(empty($pnPk))
      exit( __LINE__.' ERROR: no pk');

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM addressbook_document WHERE addressbook_documentpk = "'.$pnPk.'" ';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
      exit(__LINE__.' ERROR: no file found');

    $sFilePath = $oResult->getFieldValue('path_name');
    $sFileName = $oResult->getFieldValue('filename');

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
      case "pdf": $ctype="application/pdf"; break;
      case "exe": $ctype="application/octet-stream"; break;
      case "zip": $ctype="application/zip"; break;
      case "doc": $ctype="application/msword"; break;
      case "docx": $ctype="application/msword"; break;
      case "xls": $ctype="application/vnd.ms-excel"; break;
      case "xlsx": $ctype="application/vnd.ms-excel"; break;
      case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
      case "pptx": $ctype="application/vnd.ms-powerpoint"; break;
      case "gif": $ctype="image/gif"; break;
      case "png": $ctype="image/png"; break;
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


  private function _getIndustry($pvIndustryPk = 0)
  {
    if(!assert('is_array($pvIndustryPk) || is_integer($pvIndustryPk)'))
      return false;

    if(empty($pvIndustryPk))
    {
      $pvIndustryPk = array();
    }
    else
    {
      if(is_array($pvIndustryPk))
      {
        foreach($pvIndustryPk as $nValue)
        {
          if(!is_numeric($nValue))
            !assert('false; // not a numeric value ');
        }
      }
      else
        $pvIndustryPk = (array)$pvIndustryPk;
    }


    $oDb = CDependency::getComponentByName('database');
    $sQuery = 'Select * FROM industry ';
    if(!empty($pvIndustryPk))
      $sQuery.= ' WHERE industrypk IN ('.implode(',', $pvIndustryPk).')';

    $sQuery.= '  ORDER BY industry_name';
    $oDbResult = $oDb->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $asIndustry = array();
    while($bRead)
    {
      $asIndustry[$oDbResult->getFieldValue('industrypk', CONST_PHP_VARTYPE_INT)] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asIndustry;
  }

  public function setLanguage($psLanguage)
  {
    require_once('language/language.inc.php5');
    if(isset($gasLang[$psLanguage]))
      $this->casText = $gasLang[$psLanguage];
    else
      $this->casText = $gasLang[CONST_DEFAULT_LANGUAGE];
  }

  public function getContactName($pasContactData)
  {
    if(!assert('is_array($pasContactData)'))
      return '';

    $sName = '';

    if(isset($pasContactData['courtesy']))
      $sName.= ucfirst($pasContactData['courtesy']).' ';

    if(isset($pasContactData['firstname']))
      $sName.= ucfirst($pasContactData['firstname']).' ';

    if(isset($pasContactData['lastname']))
      $sName.= ucfirst($pasContactData['lastname']).' ';

    return $sName;
  }


  /**
   *Return an array with all the employee of a company
   * fetched by companypk or by an an employee pk. If extended is true, return employees of holding/child companies
   * @param integer $pnCompanyPk
   * @param integer $pnContactPk
   * @param boolean $pbExtended
   * @return array
  */
  public function getEmployeeList($pnCompanyPk = 0, $pnContactPk = 0, $pbExtended = false)
  {
    if(!assert('is_integer($pnCompanyPk) && is_integer($pnContactPk) && is_bool($pbExtended)'))
      return array();

    if((empty($pnCompanyPk) && empty($pnContactPk)) || (!empty($pnCompanyPk) && !empty($pnContactPk)))
    {
      assert('false; // trying to get employee list but no cp or ct pk given');
      return array();
    }

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT ct.*, prf.*, cp.company_name FROM profil as prf ';
    $sQuery.= ' INNER JOIN company as cp ON (cp.companypk = prf.companyfk) ';
    $sQuery.= ' INNER JOIN contact as ct ON (ct.contactpk = prf.contactfk) ';


    if(!empty($pnCompanyPk))
    {
      if($pbExtended)
      {
        $anCompany = $this->getRelatedCompanies($pnCompanyPk);
        $sQuery.= ' WHERE prf.companyfk IN ('.implode(',', $anCompany).') ';
      }
      else
        $sQuery.= ' WHERE prf.companyfk = "'.$pnCompanyPk.'" ';
    }
    else
    {
      if($pbExtended)
      {
        $nCompanyPk = $this->_getContactCompany($pnContactPk);
        if(empty($nCompanyPk))
          $sQuery.= ' WHERE false ';
        else
        {
          $anCompany = $this->getRelatedCompanies($nCompanyPk);
          if(empty($anCompany))
            $sQuery.= ' WHERE false ';
          else
            $sQuery.= ' WHERE prf.companyfk IN ('.implode(',', $anCompany).') ';
        }
      }
      else
        $sQuery.= ' WHERE  prf.companyfk IN (SELECT prf.companyfk FROM profil as prf WHERE contactfk = "'.$pnContactPk.'")';
    }

    $sQuery.= ' ORDER BY cp.company_name, ct.lastname, ct.firstname ';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
      return array();

    $asEmployee = array();
    while($bRead)
    {
      $asEmployee[(int)$oResult->getFieldValue('contactpk')] = $oResult->getData();
      $bRead = $oResult->readNext();
    }

    return $asEmployee;
  }

  /**
   *Return a array with all the companies related to each other: holiding and childs
   * @param integer $pnComapnyPk
   * @return array
   */
  public function getRelatedCompanies($pnComapnyPk)
  {
    if(!assert('is_integer($pnComapnyPk) && !empty($pnComapnyPk)'))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT cp.companypk as cp1, cp.parentfk as cp2, cp_parent.companypk as cp3, cp_parent.parentfk as cp4, cp_child.companypk as cp5, cp_child.parentfk as cp6  FROM company as cp ';
    $sQuery.= ' LEFT JOIN company as cp_parent ON (cp_parent.companypk = cp.parentfk) ';
    $sQuery.= ' LEFT JOIN company as cp_child ON (cp_child.parentfk = cp.companypk) ';
    $sQuery.= ' WHERE cp.companypk = "'.$pnComapnyPk.'" OR cp.parentfk = "'.$pnComapnyPk.'" ';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
      return array();

    $asCompany = array();
    while($bRead)
    {
      $asData = $oResult->getData();
      foreach($asData as $sCompanyPk)
      {
        if(!empty($sCompanyPk))
          $asCompany[(int)$sCompanyPk] = (int)$sCompanyPk;
      }

      $bRead = $oResult->readNext();
    }

    return $asCompany;
  }

  public function getContactNameFromData($pasContactData, $pbWithIcon = false)
  {
    if(!assert('is_array($pasContactData) && !empty($pasContactData)'))
      return '';

    if(!isset($pasContactData['courtesy']) || !isset($pasContactData['lastname']) || !isset($pasContactData['firstname']))
    {
      assert('false; // contact data missing');
      return '';
    }

    if($pbWithIcon)
      $sName = $this->_getDisplayIcon($pasContactData).' ';
    else
      $sName = '';

    return $sName.ucfirst($pasContactData['courtesy']).' '.$pasContactData['lastname'].' '.$pasContactData['firstname'];
  }
}

