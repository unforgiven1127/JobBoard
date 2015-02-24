<?php

require_once('component/customfields/customfields.class.php5');

class CCustomfieldsEx extends CCustomfields
{
  public function __construct()
  {
    return true;
  }

  public function __destruct()
  {
    return true;
  }

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
      case  CONST_CF_TYPE_CUSTOMFIELD:
        switch($this->csAction)
        {
          case CONST_ACTION_ADD:
            return json_encode($this->_addCustomfield());
              break;

            case CONST_ACTION_EDIT:
              return json_encode($this->_addAjaxCustomfield($this->cnPk));
                break;

            case CONST_ACTION_SAVEADD:
              return json_encode($this->_saveCustomfield());
                break;

            case CONST_ACTION_UPDATE:
              return json_encode($this->_updateCustomfield($this->cnPk));
                break;

            default :
              break;

         }
      }
  }

  /**
  * Return the array of all the custom fields according to the parameter
  * @param string $psUid
  * @param string $psAction
  * @param string $psType
  * @param integer $pnPk
  * @param string $psFieldType
  * @return array
  */

  public function getCustomfields($psUid = '',$psAction = '',$psType = '',$pnPk = 0, $pnItemfk = 0, $psFieldType = '', $pasFields = array())
  {
    if(!assert('is_integer($pnPk)'))
      return array('error' => __LINE__.' pnPk not an integer');

    if(!assert('is_integer($pnItemfk)'))
      return array('error' => __LINE__.' itemfk not an integer');

    if(!assert('is_array($pasFields)'))
      return array('error' => __LINE__.' Not an array');

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM customfield as cf ';

    if(!empty($pnItemfk))
      $sQuery.= ' LEFT JOIN customfield_value as cfv ON (cfv.customfieldfk = cf.customfieldpk AND cfv.itemfk = '.$pnItemfk.') ';

    $sQuery.= ' WHERE 1 ';

    if(!empty($psUid))
      $sQuery.= 'AND cf.uid = '.$oDB->dbEscapeString($psUid);

    if(!empty($psAction))
      $sQuery.= ' AND cf.action = '.$oDB->dbEscapeString($psAction);

    if(!empty($psType))
      $sQuery.= 'AND  cf.type = '.$oDB->dbEscapeString($psType);

    if(!empty($pnPk))
      $sQuery.= ' AND cf.customfieldpk = '.$oDB->dbEscapeString($pnPk);

    if(isset($pasFields['cfPk']) && !empty($pasFields['cfPk']))
      $sQuery.= ' AND cf.customfieldpk = '.$oDB->dbEscapeString($pasParams['cfPk']);

    if(isset($pasFields['cfName']) && !empty($pasFields['cfName']))
      $sQuery.= ' AND cf.name = '.$oDB->dbEscapeString($pasParams['cfName']);

    if(!empty($psFieldType))
      $sQuery.= ' AND cf.fieldtype = '.$oDB->dbEscapeString($psFieldType);

    //echo '<br />'.$sQuery;
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
      return array();

    $asCustomData = array();
    while($bRead)
    {
      $asCustomData[] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }
    return $asCustomData;
  }

  /**
   * Alias function to return all the custom fields using itempk
   * @param integer $pnPk
   * @return array of data
   */

 public function getCustomfieldByPk($pnPk)
 {
   if(!assert('is_integer($pnPk) && !empty($pnPk)'))
     return 'Wrong Data Obtained';

   $asCustomFields = $this->getCustomfields('','','',$pnPk);
   return $asCustomFields;
 }

  /**
   * Alias function to return all the custom fields using field name
   * @param string $psName
   * @return array of data
   */

 public function getCustomfieldByName($psName)
 {
    if(!assert('is_string($psName) && !empty($psName)'))
    return 'Wrong Data Obtained';

    $asCustomFields = $this->getCustomfields('','','',0,0,array('cfName'=>$psName));
      return $asCustomFields;
  }

 /**
  * Updaste the custom field
  * @param integer $pnCustomPk
  * @return array
  */

  private function _updateCustomfield($pnCustomPk)
  {
    if(!assert('is_integer($pnCustomPk) && !empty($pnCustomPk)'))
      return array('error' => __LINE__.' - No custom field identifier.');

    $sNewValue = getValue('value');
    $nItemfk = (int)getValue('itemfk', 0);

    if(empty($nItemfk))
      return array('error' => __LINE__.' - No item identifier.');

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT customfield_valuepk FROM customfield_value WHERE customfieldfk = '.$pnCustomPk.' AND itemfk = '.$nItemfk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if($bRead)
    {
      $sQuery = 'UPDATE customfield_value SET value = '.$oDB->dbEscapeString($sNewValue).' WHERE customfieldfk = '.$pnCustomPk.' AND itemfk = '.$nItemfk;
      $oResult = $oDB->ExecuteQuery($sQuery);
      if(!$oResult)
        return array('error' => __LINE__.' - Couldn\'t update the new value');
    }
    else
    {
      $sQuery = 'INSERT INTO customfield_value (customfieldfk, itemfk, value) VALUES ('.$pnCustomPk.', '.$nItemfk.', '.$oDB->dbEscapeString($sNewValue).')';
      $oResult = $oDB->ExecuteQuery($sQuery);
      if(!$oResult)
        return array('error' => __LINE__.' - Couldn\'t update the new value');
    }

    return array('action' => '$(\'#csEdit_'.$pnCustomPk.'\').closest(\'.holderSection\').find(\'.rightSection\').html($(\'#csFileName_'.$pnCustomPk.'\').val());  $(\'#csEdit_'.$pnCustomPk.'\').remove();$(\'#csDiv_'.$pnCustomPk.'\').show();');
  }

 /**
  * Function to save the custom field data in the database
  * @return array with notice and reload page
  */

private function _saveCustomfield()
{
  $sUid = getValue('cp_uid');
  $sAction = getValue('cp_action');
  $sType = getValue('cp_type');
  $nPk = getValue('cp_pk');
  $sFieldType = getValue('field_type');
  $sLabel = getValue('labelname');
  $sDescription = getValue('field_desc');
  $nChecked = getValue('cascading');

  if($nChecked == 1)
    $nPk = 0;

  $oDB = CDependency::getComponentByName('database');

  $sQuery = 'INSERT INTO customfield (`uid`,`action`,`type`,`pk`,`fieldtype`,`label`, `description`) VALUES ';
  $sQuery.= '('.$oDB->dbEscapeString($sUid).','.$oDB->dbEscapeString($sAction).','.$oDB->dbEscapeString($sType).',';
  $sQuery.= ''.$oDB->dbEscapeString($nPk).','.$oDB->dbEscapeString($sFieldType).',';
  $sQuery.= ''.$oDB->dbEscapeString($sLabel).','.$oDB->dbEscapeString($sDescription).')';

  $oResult = $oDB->executeQuery($sQuery);
  if(!$oResult)
    return array('error'=>'Can not save the custom field');

  $nCustomPk = $oResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT);
  $sFieldName = 'cfName_'.$nCustomPk;

  $sQuery = 'UPDATE customfield SET name = '.$oDB->dbEscapeString($sFieldName).' where customfieldpk = '.$nCustomPk;
  $oDbResult = $oDB->ExecuteQuery($sQuery);

  if($oDbResult)
    return array('notice'=>'Custom field has been saved.', 'reload'=>1);
  else
    return array('error'=>'Can not save the custom field');
}

 /**
  * Ajax function to add customfield
  * @param type $pnPk
  * @param type $psValue
  */

  private function _addAjaxCustomfield($pnPk)
  {
    $sValue = getValue('value');
    $nItemfk = (int)getValue('itemfk');

    if(empty($nItemfk))
      return array('error' => 'No item identifer');

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sURL = $oPage->getAjaxUrl('customfields', CONST_ACTION_UPDATE, CONST_CF_TYPE_CUSTOMFIELD,$pnPk);

    $sJavascript = "var sValue = $('#csFileName_".$pnPk."').val();";
    $sJavascript.= "AjaxRequest('".$sURL."&itemfk=".$nItemfk."&value='+encodeURI(sValue),'#body');";

    $sHTML = $oHTML->getBlocStart('csEdit_'.$pnPk);
    $sHTML.= '<input id="csFileName_'.$pnPk.'" type="text"  value="'.$sValue.'" name="csFileName_'.$pnPk.'" style="width:300px;padding:5px;margin:5px;">';
    $sPic = $oHTML->getPicture(CONST_PICTURE_SAVE);
    $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' => $sJavascript));
    $sHTML.= $oHTML->getBlocEnd();

    return array('action' => '$(\'#csDiv_'.$pnPk.'\').hide();', 'data'=> $sHTML);

  }

 /**
  * Function to display customfield add link in the another component
  * @param string $psUid
  * @param string $psAction
  * @param string $psType
  * @param integer $pnPk
  * @return string
  */

 public function getCustomFieldAddLink($psUid,$psAction,$psType,$pnPk = 0)
 {
   if(!assert('is_string($psUid) && !empty($psUid)'))
    return 'Error Obtained';

   if(!assert('is_string($psAction) && !empty($psAction)'))
    return 'Error Obtained';

   if(!assert('is_string($psType) && !empty($psType)'))
    return 'Error Obtained';

   if(!assert('is_integer($pnPk)'))
    return 'Error Obtained';

    //return html of a link to add a customfield
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $sHTML = $oHTML->getBlocStart('',array('style'=>'padding:5px;'));
    $sURL =  $oPage->getAjaxURL($this->_getUid(),CONST_ACTION_ADD, CONST_CF_TYPE_CUSTOMFIELD,0,array('cp_uid'=>$psUid,'cp_action'=>$psAction,'cp_type'=>$psType,'cp_pk'=>$pnPk));
    $sAjax = $oHTML->getAjaxPopupJS($sURL, 'body','','250','800',1);
    $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/add_16.png');
    $sHTML.= $oHTML->getLink($sPic.' Add Custom Field','javascript:;',array('onclick' => $sAjax));
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
 }

 /**
  * Function to display the form to add custom field
  * @param integer $pnPk
  * @return array of data
  */

 private function _addCustomfield($pnPk = 0)
 {
    if(!assert('is_integer($pnPk)'))
      return array('error' => 'Error Obtained');

   $oHTML = CDependency::getComponentByName('display');
   $oPage = CDependency::getComponentByName('page');
   $oRight = CDependency::getComponentByName('right');

   $bAccess = $oRight->canAccess($this->_getUid(),'ppaall',$this->getType(),0);

   $sUid = getValue('cp_uid');
   $sAction = getValue('cp_action');
   $sType = getValue('cp_type');
   $nPk = getValue('cp_pk');

   $oForm = $oHTML->initForm('csAddForm');
   $sFormId = $oForm->getFormId();

   if($pnPk)
     $sURL = $oPage->getAjaxURL($this->_getUid(),CONST_ACTION_SAVEEDIT, CONST_CF_TYPE_CUSTOMFIELD,$pnPk);
   else
     $sURL = $oPage->getAjaxURL($this->_getUid(),CONST_ACTION_SAVEADD, CONST_CF_TYPE_CUSTOMFIELD,0,array('cp_uid'=>$sUid,'cp_action'=>$sAction,'cp_type'=>$sType,'cp_pk'=>$nPk));

   $sJs = $oHTML->getAjaxJs($sURL, 'body', $sFormId);
   $oForm->setFormParams('', false, array('action' => '','inajax'=> 1,'submitLabel' => 'Save', 'onsubmit' => 'event.preventDefault(); '.$sJs));
   $oForm->setFormDisplayParams(array('columns' => 2, 'noCancelButton' => '1'));

   $oForm->addField('input', 'labelname', array('label'=> 'Field Label ', 'value'   => ''));
   $oForm->setFieldControl('labelname', array('jsFieldNotEmpty' => ''));

   /*$oForm->addField('input', 'fieldvalue', array('label'=> 'Field Value','value' => ''));
   $oForm->setFieldControl('fieldvalue', array('jsFieldNotEmpty' => ''));*/

   $oForm->addField('select', 'field_type', array('label'=> 'Field type'));
   $oForm->setFieldControl('field_type', array('jsFieldNotEmpty' => ''));

   $oForm->addOption('field_type', array('label' => 'raw text field', 'value' => 'text'));
   $oForm->addOption('field_type', array('label' => 'big text area', 'value' => 'text area', 'disabled' => 'disabled', 'class' => 'optionInactive'));
   $oForm->addOption('field_type', array('label' => 'list of elements', 'value' => 'select', 'disabled' => 'disabled', 'class' => 'optionInactive'));
   $oForm->addOption('field_type', array('label' => 'email', 'value' => 'email', 'disabled' => 'disabled', 'class' => 'optionInactive'));
   $oForm->addOption('field_type', array('label' => 'url', 'value' => 'url', 'disabled' => 'disabled', 'class' => 'optionInactive'));
   $oForm->addOption('field_type', array('label' => 'integer number', 'value' => 'int', 'disabled' => 'disabled', 'class' => 'optionInactive'));
   $oForm->addOption('field_type', array('label' => 'float number', 'value' => 'float', 'disabled' => 'disabled', 'class' => 'optionInactive'));


   $oForm->addField('textarea', 'field_desc', array('label'=> 'Field legend'));
   $oForm->setFieldControl('field_type', array('jsFieldMaxSize' => '255'));


   if($bAccess)
   {
     $oForm->addField('misc','',array('type'=>'br','text'=>'&nbsp;'));
     $oForm->addField('checkbox', 'cascading', array('type' => 'misc', 'label'=> 'Add this custom field to all records ?', 'value' => 1, 'id' => 'cascading_id'));
   }

   $sHTML = $oForm->getDisplay();

  return $oPage->getAjaxExtraContent(array('data'=>$sHTML));
 }

 /**
  * Display the custom field depending upon the data passed
  * @param string $psUid
  * @param string $psAction
  * @param string $psType
  * @param integer $pnPk
  * @return string HTML
  */

 public function getCustomfieldDisplay($psUid, $psAction, $psType, $pnPk=0)
 {
   $oHTML =  CDependency::getComponentByName('display');
   $oPage =  CDependency::getComponentByName('page');
   $oRight =  CDependency::getComponentByName('right');

   $nCurrentItemfk = $oPage->getPk();
   $sAccess = $oRight->canAccess($this->_getUid(),'ppae',CONST_CF_TYPE_CUSTOMFIELD,0);
   $asCustomFields = $this->getCustomfields($psUid, $psAction, $psType, $pnPk, $nCurrentItemfk);

   $sHTML = '';
   foreach($asCustomFields as $asFields)
   {
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'holderSection'));

      $sHTML.= $oHTML->getBlocStart('csDiv_'.$asFields['customfieldpk']);
      $sHTML.= $oHTML->getBlocStart('',array('class'=>'leftSection','style'=>'width:80px; cursor:help;', 'title' => $asFields['description']));
      $sHTML.= $oHTML->getText($asFields['label']);
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('',array('class'=>'rightSection'));
      $sHTML.= $oHTML->getText($asFields['value']);
      $sHTML.= $oHTML->getBlocEnd();

      $sURL = $oPage->getAjaxUrl('customfields', CONST_ACTION_EDIT, CONST_CF_TYPE_CUSTOMFIELD, (int)$asFields['customfieldpk'], array('value'=>  urlencode($asFields['value']), 'itemfk' => $nCurrentItemfk)) ;
      $sJavascript = "AjaxRequest('".$sURL."','#body', '', 'appenDiv_".$asFields['customfieldpk']."');";

      if($sAccess)
        $sHTML.= $oHTML->getLink($oHTML->getPicture(CONST_PICTURE_EDIT, 'Edit field value'),'javascript:;',array('onclick' => $sJavascript));

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocStart('appenDiv_'.$asFields['customfieldpk']);
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocEnd();
    }
    return $sHTML;
  }

  public function getSearchSql($pnCFieldPk, $pvCFieldValue)
  {
    $asSql = array('join' => '', 'where' => '');

    if(!assert('is_integer($pnCFieldPk)') || empty($pnCFieldPk) || empty($pvCFieldValue))
      return $asSql;

    //TODO check cf exists, get type and check value is in the correct format, convert stored data if needed
    $asCustomField = $this->getCustomfields('', '', '', $pnCFieldPk);
    if(empty($asCustomField))
      return $asSql;

    $oDb = CDependency::getComponentByName('database');
    $pvCFieldValue = $oDb->dbEscapeString('%'.$pvCFieldValue.'%');

    switch($asCustomField[0]['type'])
    {
      case CONST_AB_TYPE_COMPANY:
        $asSql['join'] = ' INNER JOIN customfield_value as cfv ON (cfv.itemfk = cp.companypk AND cfv.customfieldfk = '.$pnCFieldPk.' AND cfv.value LIKE '.$pvCFieldValue.' ) ';
        $asSql['where'] = '';
        break;

      case CONST_AB_TYPE_CONTACT:
        $asSql['join'] = ' INNER JOIN customfield_value as cfv ON (cfv.itemfk = co.contactpk AND cfv.customfieldfk = '.$pnCFieldPk.'  AND cfv.value LIKE '.$pvCFieldValue.' ) ';
        $asSql['where'] = '';
        break;

      default: exit(__LINE__.' - searching unknown type of custom field.');
        break;
    }

    return $asSql;
  }
}
