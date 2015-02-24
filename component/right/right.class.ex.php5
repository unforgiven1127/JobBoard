<?php

require_once('component/right/right.class.php5');

class CRightEx extends CRight
{
  private $casUserRights = array();
  private $cnUserPk = 0;
  private $cbIsAdmin = false;

  public function __construct()
  {
    $oLogin = CDependency::getComponentByName('login');
    $this->cnUserPk = $oLogin->getUserPk();
    $this->cbIsAdmin = $oLogin->isAdmin();

    $bRefreshRights = (bool)getValue('refresh_right', 0);

    if(!$bRefreshRights && isset($_SESSION['user_rights']) && !empty($_SESSION['user_rights']))
    {
      $this->casUserRights = $_SESSION['user_rights'];
    }
    else
      $this->loadUserRights();

    if($bRefreshRights)
      dump($_SESSION['user_rights']);
  }

  /**
   * Load all the user right from the database. If it's allready in session, we just reuse it.
   *
   * @param type $pnUserfk
   * @return boolean
   */
  public function loadUserRights($pnUserfk = 0)
  {
    if(!assert('is_integer($pnUserfk)'))
      return false;

    if(empty($pnUserfk))
    {
      $nUserPk = $this->cnUserPk;
    }
    else
      $nUserPk = $pnUserfk;

    $oDB = CDependency::getComponentByName('database');
    if($this->cbIsAdmin)
      $sQuery = 'SELECT *, "'.$nUserPk.'" as loginfk  FROM  `right` as r ';
    else
    {
      $sQuery = 'SELECT * FROM  `right` as r ';
      $sQuery.= ' LEFT JOIN right_user as ru  ON (ru.rightfk = r.rightpk AND loginfk = '.$nUserPk.') ';
    }

    $oDbResult = $oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
    {
      $_SESSION['user_rights'] = array();
      unset($_SESSION);
      return false;
    }

    while($bRead)
    {
      $asRightData = $oDbResult->getData();

      //user has right to access static rights and page he's got a specific right
      if($asRightData['type'] == 'static' || !empty($asRightData['loginfk']))
      {
        if(empty($asRightData['cp_action']))
          $asRightData['cp_action'] = '*';

        if(empty($asRightData['cp_type']))
          $asRightData['cp_type'] = '*';

        if(empty($asRightData['cp_pk']))
          $asRightData['cp_pk'] = '*';

        if(!empty($asRightData['callback']))
        {
          $asParams = unserialize($asRightData['callback_params']);
          $this->casUserRights[$asRightData['cp_uid']][$asRightData['cp_action']][$asRightData['cp_type']][$asRightData['cp_pk']] = array('callback' => $asRightData['callback'], 'callback_params' => $asParams);
        }
        else
          $this->casUserRights[$asRightData['cp_uid']][$asRightData['cp_action']][$asRightData['cp_type']][$asRightData['cp_pk']] = true;
      }

      $bRead = $oDbResult->readNext();
    }

    if(empty($this->casUserRights))
    {
      $_SESSION['user_rights'] = array();
      unset($_SESSION);
      return false;
    }

    $_SESSION['user_rights'] = $this->casUserRights;
    return true;
  }

  /**
   * check if the current user can access the request page (defined by uid, action, type, pk)
   * @param type $psUid
   * @param type $psAction
   * @param type $psType
   * @param type $pnPk
   * @param array $psCallback
   * @return boolean
   */
  public function canAccess($psUid, $psAction, $psType = '', $pnPk = 0, $psCallback = array())
  {
    if($this->cbIsAdmin)
      return true;

    if(empty($pnPk))
      $sPk = '*';
    else
      $sPk = $pnPk;


    //check first globals rights (component, action or type)
    if(isset($this->casUserRights[$psUid]['*']) || isset($this->casUserRights[$psUid][$psAction]['*']) || isset($this->casUserRights[$psUid][$psAction][$psType]['*']))
    {
      return true;
    }
    //check then the specific right matching exactly the paramneters
    elseif(isset($this->casUserRights[$psUid][$psAction][$psType][$sPk]) && !empty($this->casUserRights[$psUid][$psAction][$psType][$sPk]))
    {
      if($this->casUserRights[$psUid][$psAction][$psType][$sPk] === true)
        return true;
    }

    /* TODO: get the callback (closest from available parameters
     *
     * //in case the right is no
        $oComponent = CDependency::getComponentByUid($psUid);
        $bCallbackResponse = call_user_method($oComponent, $this->casUserRights[$psUid][$psAction][$psType][$sPk]['calback'], $this->casUserRights[$psUid][$psAction][$psType][$sPk]['calback_params']);

        if($bCallbackResponse)
          return true;
     *
     */

    //if the component calling this function specify a specific callback, we try it
    if(empty($psCallback) || !isset($psCallback['function']) || empty($psCallback['function']))
      return false;

    if(!isset($psCallback['params']))
      $psCallback['params'] = array();

    $oComponent = CDependency::getComponentByUid($psUid);
    return call_user_method($psCallback['function'], $oComponent, $psCallback['params']);
  }

  /**
   * return an array of the user rights
   * @param integer $pnPk
   * @param bool $pbGetRight
   * @param bool $pbGetAlias
   * @param bool $pbGetStatic
   *
   * @return an array containing rights and aliases of a specific or all users
   */
  public function getUserRights($pnPk = 0, $pbGetRight = true, $pbGetAlias = false, $pbGetStatic = false)
  {
    if(!assert('is_integer($pnPk)'))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM right_user as ru ';
    $sQuery.= ' INNER JOIN `right` as r ON (r.rightpk = ru.rightfk ';

    if(!$pbGetRight)
      $sWhere = ' AND r.type <> "right" ';

    if(!$pbGetAlias)
      $sWhere = ' AND r.type <> "alias" ';

    if(!$pbGetStatic)
      $sWhere = ' AND r.type <> "static" ';

     $sQuery.= ' )';

    if(!empty($pnPk))
      $sQuery.= ' WHERE ru.loginfk = '.$pnPk;

    $sQuery.= ' ORDER BY loginfk ASC, cp_uid, cp_action, cp_type, cp_pk ';

    $oDbResult = $oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
      return array();

    $asRightData = array();
    while($bRead)
    {
      $asRightData[$oDbResult->getFieldValue('loginfk', CONST_PHP_VARTYPE_INT)][] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asRightData;
  }

  /**
   * return an array with user rightpk
   *
   * @param integer $pnPk
   * @param bool $pbGetRight
   * @param bool $pbGetAlias
   * @param bool $pbGetStatic
   *
   * @return an array of rightpk and aliases of a specific or all users
   */

   public function getUserRightsPk($pnPk = 0, $pbGetRight = true, $pbGetAlias = false, $pbGetStatic = false)
   {
     $asRights = $this->getUserRights($pnPk, $pbGetRight, $pbGetAlias, $pbGetStatic);

     $asRightData = array();
     foreach($asRights as $nUserfk => $asUserRights)
     {
         foreach($asUserRights as $nKey => $asRight)
          $asRightData[$asRight['rightfk']] = $asRight['rightfk'];
     }
     return $asRightData;
   }

  /**
   * return an array of the request rights
   *
   * @param bool $pbGetRight
   * @param bool $pbGetAlias
   * @param bool $pbGetStatic
   *
   * @return array containing rights data
   */
  public function getRightList($pbGetRight = true, $pbGetAlias = true, $pbGetStatic = false)
  {
    if(!assert('is_bool($pbGetRight) && is_bool($pbGetAlias) && is_bool($pbGetStatic)'))
      return array();

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM `right` as r ';
    $sQuery.= ' LEFT JOIN `right_tree` as rt ON (rt.parentfk = r.rightpk) ';
    $sWhere = '';

    if(!$pbGetRight)
      $sWhere.= ' AND r.type <> "right" ';

    if(!$pbGetAlias)
      $sWhere.= ' AND r.type <> "alias" ';

    if(!$pbGetStatic)
      $sWhere.= ' AND r.type <> "static" ';

    if(!empty($sWhere))
      $sQuery.= ' WHERE 1 '.$sWhere;

    $sQuery.= ' ORDER BY cp_uid, parentfk, label';

    $oDbResult = $oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
      return array();

    $asRightData = array();
    while($bRead)
    {
      $asRightData[$oDbResult->getFieldValue('rightpk', CONST_PHP_VARTYPE_INT)] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asRightData;
  }

  /**
   * Function to get child rights of parent
   * @param integer $pnRightPk
   * @return array
   */

  public function getChildRights($pnRightPk)
   {
     if(!assert('is_integer($pnRightPk) && !empty($pnRightPk)'))
       return array();

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT group_concat(distinct r.label SEPARATOR ",") as label,group_concat(distinct rt.rightfk SEPARATOR ",") as rightfk FROM `right` as r INNER JOIN `right_tree` as rt ON (rt.rightfk = r.rightpk) WHERE rt.parentfk = '.$pnRightPk.' group by rt.parentfk';
    $oDbResult = $oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
      return array();

    $asRightChildData = array();
    while($bRead)
    {
      $asRightChildData = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
     }

    return $asRightChildData;
   }

  /**
   * Function to save the user rights
   * @return boolean
   */

  public function getUserRightSave()
  {
    $oDB = CDependency::getComponentByName('database');
    $asRights = getValue('usrRight');
    $nUserfk = getValue('userfk');

    $sQuery = 'DELETE FROM right_user WHERE loginfk = '.$nUserfk;
    $oDB->ExecuteQuery($sQuery);

    if(!empty($asRights))
    {
      $asMysqlQuery = array();
      foreach($asRights as $nRights)
      {
        $sQuery = 'SELECT * FROM right_user where rightfk = '.$nRights.' AND loginfk = '.$nUserfk.'';
        $oDbResult = $oDB->ExecuteQuery($sQuery);
        $bRead = $oDbResult->readFirst();
        if(!$bRead)
        {
           $asMysqlQuery[] = '('.$nRights.','.$nUserfk.')';
         }

         $asChilds = $this->getChildRights((int)$nRights);
         $asChildren = explode(',',$asChilds['rightfk']);

         $asChildQuery = array();
         foreach($asChildren as $nChild)
         {
          $sQuery = 'SELECT * FROM right_user where rightfk = '.$nChild.' AND loginfk = '.$nUserfk.'';
           $oDbResult = $oDB->ExecuteQuery($sQuery);
           $bRead = $oDbResult->readFirst();
            if(!$bRead)
            {
              $asChildQuery[] = '('.$nChild.','.$nUserfk.') ';
             }
           }

           if(!empty($asChildQuery))
           {
             $sQuery = 'INSERT INTO right_user (`rightfk`,`loginfk`) VALUES ';
             $sQuery.= implode(',',$asChildQuery);
             $oDbResult = $oDB->ExecuteQuery($sQuery);
           }
         }

         if(!empty($asMysqlQuery))
         {
           $sQuery = 'INSERT INTO right_user (`rightfk`,`loginfk`) VALUES ';
           $sQuery.= implode(',',$asMysqlQuery);
           $oDbResult = $oDB->ExecuteQuery($sQuery);
         }
       }
     return true;
   }

  /**
   * Save the rights of a specific user
   * Requires post or get values: loginfk user to set the rights for, anRight array of the rights pk
   * @return boolean
   */
  public function saveUserRights()
  {
    $nLoginfk = (int)getValue('loginfk', 0);
    $anRights = getValue('anRight', array());

    if(!assert('!empty($nLoginfk)'))
      return false;

    if(!assert('is_array($anRights)'))
      return false;

    if(empty($anRights))
      return true;

    //check that every rights is a proper numeric value
    foreach($anRights as $nRightfk)
    {
      if(!is_numeric($nRightfk))
      {
        assert('false; //right pk is not a number ');
        return false;
      }
    }

    $oDB = CDependency::getComponentByName('database');

    //grabe all the aliases od the selected rights
    $sQuery = 'SELECT * FROM right_tree WHERE parentfk IN ('.implode(',', $anRights);
    $oDbResult = $oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if($bRead)
    {
      while($bRead)
      {
        $anRights[] = $oDbResult->getFieldValue('rightfk', CONST_PHP_VARTYPE_INT);
        $bRead = $oDbResult->readNext();
      }
    }

    //create the insert query: 1 query for all rights
    $asInsert = array();
    foreach($anRights as $nRightfk)
    {
      $asInsert[] = '('.$nRightfk.', '.$nLoginfk.')';
    }

    $sQuery = 'DELETE FROM right_user WHERE loginfk = '.$nLoginfk;
    $oDbResult = $oDB->executeQuery($sQuery);
    if(!$oDbResult)
    {
      assert('false; //can not delete user rights.');
      return false;
    }

    $sQuery = 'INSERT INTO right_user (rightfk, loginfk) VALUES '.implode(',', $asInsert);
    $oDbResult = $oDB->executeQuery($sQuery);
    if(!$oDbResult)
    {
      assert('false; //can not delete user rights.');
      return false;
    }

    //if changing current user rights, we reload it
    if($nLoginfk == $this->cnUserPk)
      $this->loadUserRights();

    return true;
  }
 }
