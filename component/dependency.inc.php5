<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/conf/custom_config/'.CONST_WEBSITE.'/blacklist.inc.php5');

class CDependency
{
  static private $asInstancies;
  static private $asDependencies;
  static private $coPage = null;
  static private $asBlacklist = array();
  static private $asComponents = array
  (
    'login' => array('interfaces' => array('ajax' => 'json', 'cron' => 'cron')),
    '579-704' => array('interfaces' => array('ajax' => 'json', 'cron' => 'cron')),

    'addressbook' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action', 'searchable' =>'searchable')),
    '777-249' => array('interfaces' => array('ajax' => 'json','menuAction' => 'action', 'searchable' =>'searchable')),

    'form' => array('interfaces' => array('ajax' => 'json')),
    '668-313' => array('interfaces' => array('ajax' => 'json')),

    'project' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action', 'cron' => 'cron')),
    '456-789' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action', 'cron' => 'cron')),

    'sharedspace' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action', 'cron' => 'cron')),
    '999-111' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action', 'cron' => 'cron')),

    'search' => array('interfaces' => array('ajax' => 'json', 'menuGlobalAction' => 'action')),
    '898-775' => array('interfaces' => array('ajax' => 'json', 'menuGlobalAction' => 'action')),

    'event' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action', 'cron' => 'cron')),
    '007-770' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action', 'cron' => 'cron')),

    'webmail' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action')),
    '009-724' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action')),

    'settings' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action')),
    '665-544' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action')),

    'querybuilder' => array('interfaces' => array('ajax' => 'json')),
    '210-482' => array('interfaces' => array('ajax' => 'json')),

    'taaggregator' => array('interfaces' => array('ajax' => 'json', 'cron' => 'cron')),
    '100-603' => array('interfaces' => array('ajax' => 'json', 'cron' => 'cron')),

    'talentatlas' => array('interfaces' => array('ajax' => 'json', 'public' => 'publicContent', 'homepage' => 'homepage', 'menuAction' => 'action')),
    '150-163' => array('interfaces' => array('ajax' => 'json', 'public' => 'publicContent', 'homepage' => 'homepage', 'menuAction' => 'action')),

    'jobboard' => array('interfaces' => array('ajax' => 'json', 'public' => 'publicContent', 'homepage' => 'homepage', 'menuAction' => 'action')),
    '153-160' => array('interfaces' => array('ajax' => 'json', 'public' => 'publicContent', 'homepage' => 'homepage', 'menuAction' => 'action')),

    'jobboard_user' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action', 'cron' => 'cron')),
    '654-321' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action', 'cron' => 'cron')),

    'customfields' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action')),
    '180-290' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action')),

    'zimbra' => array('interfaces' => array('ajax' => 'json','cron' => 'cron')),
    '400-650' => array('interfaces' => array('ajax' => 'json','cron' => 'cron')),

    'portal' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action')),
    '111-111' => array('interfaces' => array('ajax' => 'json', 'menuAction' => 'action')),

    'charts' => array('interfaces' => array('ajax' => 'json')),
    '222-222' => array('interfaces' => array('ajax' => 'json'))
   );

  public function initialize()
  {
    self::$asInstancies = array();
    self::$asDependencies = array();

    global $gasComponentBlackList;
    self::$asBlacklist = $gasComponentBlackList;
  }

  static public function &getComponentByName($psComponentName, $psInterface = '')
  {
    if (!assert('!empty($psComponentName)'))
      exit('Error dependency');

    if(in_array($psComponentName, self::$asBlacklist))
    {
      assert('false; //Dependency error line '.__LINE__.': '.$psComponentName.' not available (Blacklisted) ');
      exit();
    }

    if(!empty($psInterface) && !isset(self::$asComponents[$psComponentName]['interface'][$psInterface]))
    {
      assert('false; //Dependency error line '.__LINE__.': '.$psComponentName.' '.$psInterface);
      exit();
    }

    //optimize compatibility
    $psComponentName = strtolower($psComponentName);

    if(isset(self::$asInstancies[$psComponentName]))
    {
      return self::$asInstancies[$psComponentName];
    }

    if(isset(self::$asInstancies['page']))
    {
      $sAction = self::$asInstancies['page']->getAction();
      $sType = self::$asInstancies['page']->getType();
      $nPK = self::$asInstancies['page']->getPk();
      $sMode = self::$asInstancies['page']->getMode();

    }
    else
    {
      $sAction = '';
      $sType = '';
      $nPK = '';
      $sMode = '';
    }

    switch($psComponentName)
    {
      case 'taaggregator':
        require_once('component/taaggregator/taaggregator.class.ex.php5');
        self::$asInstancies['taaggregator'] = new CTAaggregatorEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'jobboard_user':
        require_once('component/jobboard_user/jobboard_user.class.ex.php5');
        self::$asInstancies['jobboard_user'] = new CJobboarduserEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'talentatlas':
        require_once('component/talentatlas/talentatlas.class.ex.php5');
        self::$asInstancies['talentatlas'] = new CTalentatlasEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'jobboard':
        require_once('component/jobboard/jobboard.class.ex.php5');
        self::$asInstancies['jobboard'] = new CJobboardEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'customfields':
        require_once('component/customfields/customfields.class.ex.php5');
        self::$asInstancies['customfields'] = new CCustomfieldsEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'zimbra':
        require_once('component/zimbra/zimbra.class.ex.php5');
        self::$asInstancies['zimbra'] = new CZimbraEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'database':
        require_once('component/database/database.class.ex.php5');
        self::$asInstancies['database'] = new CDatabaseEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'page':
        require_once('component/page/page.class.ex.php5');
        self::$asInstancies['page'] = new CPageEx($sAction, $sType, $nPK, $sMode);

        //Page is special, it's the root component: store it as an attribute for further re use
        self::$coPage = self::$asInstancies['page'];

        self::$asInstancies['page']->init();
        return self::$asInstancies['page'];
        break;

      case 'display':
        require_once('component/display/display.class.ex.php5');
        self::$asInstancies['display'] = new CDisplayEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'form':
        require_once('component/form/form.class.ex.php5');
        self::$asInstancies['form'] = new CFormEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'login':
        require_once('component/login/login.class.ex.php5');
        self::$asInstancies['login'] = new CLoginEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'addressbook':
        require_once('component/addressbook/addressbook.class.ex.php5');
        self::$asInstancies['addressbook'] = new CAddressbookEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'project':
        require_once('component/project/project.class.ex.php5');
        self::$asInstancies['project'] = new CProjectEx($sAction, $sType, $nPK, $sMode);
        break;

       case 'sharedspace':
        require_once('component/sharedspace/sharedspace.class.ex.php5');
        self::$asInstancies['sharedspace'] = new CSharedspaceEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'mail':
        require_once('component/mail/mail.class.ex.php5');
        self::$asInstancies['mail'] = new CMailEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'search':
        require_once('component/search/search.class.ex.php5');
        self::$asInstancies['search'] = new CSearchEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'pager':
        require_once('component/pager/pager.class.ex.php5');
        self::$asInstancies['pager'] = new CPagerEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'event':
        require_once('component/event/event.class.ex.php5');
        self::$asInstancies['event'] = new CEventEx($sAction, $sType, $nPK, $sMode);
        break;

     case 'webmail':
        require_once('component/webmail/webmail.class.ex.php5');
        self::$asInstancies['webmail'] = new CWebMailEx($sAction, $sType, $nPK, $sMode);
        break;

     case 'querybuilder':
        require_once('component/querybuilder/querybuilder.class.ex.php5');
        self::$asInstancies['querybuilder'] = new CQuerybuilderEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'right':
        require_once('component/right/right.class.ex.php5');
        self::$asInstancies['right'] = new CRightEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'settings':
        require_once('component/settings/settings.class.ex.php5');
        self::$asInstancies['settings'] = new CSettingsEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'portal':
        //custom component, every website has its own specific class to manage the portal
        $sClass = strtolower(CONST_WEBSITE);
        require_once('component/portal/resources/class/portal_'.$sClass.'.class.php5');

        $sClass = 'CPortal'.ucfirst($sClass).'Ex';
        $oRefClass = new ReflectionClass($sClass);

        self::$asInstancies['portal'] = $oRefClass->newInstanceArgs(array($sAction, $sType, $nPK, $sMode));
        break;

      case 'charts':
        require_once('component/charts/charts.class.ex.php5');
        self::$asInstancies['charts'] = new CChartsEx($sAction, $sType, $nPK, $sMode);
        break;

      case 'socialnetwork':
        require_once('component/socialnetwork/socialnetwork.class.ex.php5');
        self::$asInstancies['socialnetwork'] = new CSocialnetworkEx($sAction, $sType, $nPK, $sMode);
        break;

      default:
        assert('false; //calling a component that doesn\'t exist. Dependency error line '.__LINE__.': '.$psComponentName.' '.$psInterface);
        exit();
        break;
      }

      if(!self::$coPage)
        self::getComponentByName('page');

      self::$asInstancies[$psComponentName]->setLanguage(self::$coPage->getLanguage());
      return self::$asInstancies[$psComponentName];
  }

  static public function getComponentUidByName($psComponentName)
  {
    if(!in_array($psComponentName, self::$asBlacklist))
    {
      switch($psComponentName)
      {
        case 'talentatlas':
         return '150-163';
          break;

        case 'jobboard':
         return '153-160';
          break;

        case 'jobboard_user':
          return '654-321';
            break;

         case 'customfields':
          return '180-290';
            break;

        case 'database':
          return '124-546';
            break;

        case 'page':
          return '845-187';
            break;

        case 'display':
          return '569-741';
            break;

        case 'form':
          return '668-313';
            break;

        case 'login':
          return '579-704';
            break;

        case 'addressbook':
          return '777-249';
            break;

        case 'project':
          return '456-789';
            break;

        case 'sharedspace':
          return '999-111';
            break;

        case 'search':
          return '898-775';
            break;

        case 'pager':
          return '140-510';
            break;

        case 'event':
          return '007-770';
            break;

        case 'webmail':
          return '009-724';
            break;

        case 'mail':
          return '008-724';
            break;

        case 'querybuilder':
          return '210-482';
            break;

        case 'taaggregator':
          return '100-603';
           break;

        case 'right':
          return '998-877';
            break;

        case 'settings':
          return '665-544';
            break;

        case 'zimbra':
          return '400-650';
            break;

        case 'portal':
          return '111-111';
            break;

        case 'charts':
          return '222-222';
            break;

        case 'socialnetwork':
          return '459-456';
            break;

        default:
          return '';
            break;
        }
     }
    return '';
  }


  static public function getComponentNameByUid($psComponentID)
  {
    if(!in_array($psComponentID, self::$asBlacklist))
    {
      switch($psComponentID)
      {
        case '150-163':
          return 'talentatlas';
            break;

        case '153-160':
          return 'jobboard';
            break;

        case '654-321':
          return 'jobboard_user';
            break;

        case '124-546':
          return 'database';
            break;

        case '845-187':
          return 'page';
            break;

        case '569-741':
          return 'display';
            break;

        case '668-313':
          return 'form';
            break;

        case '579-704':
          return 'login';
            break;

        case '777-249':
          return 'addressbook';
            break;

        case '456-789':
          return 'project';
            break;

        case '999-111':
          return 'sharedspace';
            break;

        case '898-775':
          return 'search';
            break;

        case '140-510':
          return 'pager';
            break;

       case '007-770':
        return 'event';
          break;

      case '009-724':
        return 'webmail';
          break;

      case '008-724':
        return 'mail';
          break;

      case '210-482':
        return 'querybuilder';
          break;

      case '100-603':
        return 'taaggregator';
          break;

      case '998-877':
        return 'right';
          break;

      case '665-544':
        return 'settings';
          break;

      case '400-650':
        return 'zimbra';
           break;

      case '111-111':
        return 'portal';
           break;

      case '222-222':
        return 'charts';
          break;

      case '459-456':
        return 'socialnetwork';
          break;

      default:
        return '';
          break;
      }
    }

    return '';
  }


  static public function getComponentByUid($psUid, $psInterface = '')
  {
    if(!empty($psInterface) && !isset(self::$asComponents[$psUid]['interfaces'][$psInterface]))
      return null;

    if(!in_array($psUid, self::$asBlacklist))
    {
      switch($psUid)
      {
        case '150-163':
          return self::getComponentByName('talentatlas');
            break;

        case '153-160':
          return self::getComponentByName('jobboard');
            break;

        case '654-321':
          return self::getComponentByName('jobboard_user');
            break;

        case '124-546':
          return self::getComponentByName('database');
            break;

        case '845-187':
          return self::getComponentByName('page');
            break;

        case '569-741':
          return self::getComponentByName('display');
            break;

        case '668-313':
          return self::getComponentByName('form');
            break;

        case '579-704':
          return self::getComponentByName('login');
            break;

        case '777-249':
          return self::getComponentByName('addressbook');
            break;

        case '456-789':
          return self::getComponentByName('project');
            break;

        case '999-111':
          return self::getComponentByName('sharedspace');
            break;

        case '008-724':
          return self::getComponentByName('mail');
            break;

        case '009-724':
          return self::getComponentByName('webmail');
            break;

        case '898-775':
          return self::getComponentByName('search');
            break;

        case '140-510':
          return self::getComponentByName('pager');
            break;

        case '007-770':
          return self::getComponentByName('event');
            break;

        case '210-482':
          return self::getComponentByName('querybuilder');
            break;

        case '100-603':
          return self::getComponentByName('taaggregator');
            break;

        case '998-877':
          return self::getComponentByName('right');
            break;

        case '665-544':
          return self::getComponentByName('settings');
            break;

        case '180-290':
          return self::getComponentByName('customfields');
            break;

        case '400-650':
          return self::getComponentByName('zimbra');
            break;

        case '111-111':
          return self::getComponentByName('portal');
            break;

        case '222-222':
          return self::getComponentByName('charts');
            break;

        case '459-456':
          return self::getComponentByName('socialnetwork');
            break;

        default:
          assert('false; /* no uid available */');
           exit();
            break;
      }
    }
  }


  static public function getComponentIdByInterface($psInterface)
  {
    if(empty($psInterface))
      return array();

    $asUid = array();
    foreach(self::$asComponents as $sId => $asComponent)
    {
      if(isset($asComponent['interfaces'][$psInterface]) && preg_match('/^[0-9]{3}-[0-9]{3}$/', $sId))
      {
        if(!in_array($sId, self::$asBlacklist))
          $asUid[$sId] = $sId;
      }
    }
    return $asUid;
  }


  static public function hasInterfaceByUid($psUid, $psInterface)
  {
    if(empty($psUid) || empty($psInterface))
      return false;

    if(in_array($psUid, self::$asBlacklist))
      return false;

    if(!isset(self::$asComponents[$psUid]) || !isset(self::$asComponents[$psUid]['interfaces']))
      return false;

    if(!in_array($psInterface, self::$asComponents[$psUid]['interfaces']))
      return false;

    return true;
  }


  static public function getComponentUidByInterface($psInterface)
  {
    if(empty($psInterface))
      return array();

    $asComponents = array();
    foreach(self::$asComponents as $sKey => $asComponentParam)
    {
      if(preg_match('/[0-9]{3}-[0-9]{3}/', $sKey))
      {
        if(isset($asComponentParam['interfaces'][$psInterface]) && !in_array($sKey, self::$asBlacklist))
          $asComponents[] = $sKey;
      }
    }

    return $asComponents;
  }
}
?>
