<?php

  define('CONST_WEBSITE_LOGGEDOUT_LOGO','/media/picture/header.jpg');
  define('CONST_WEBSITE_LOGO_URL','https://bcm.bulbouscell.com');

  define('CONST_WEBSITE_MAIN_CSS', '/common/style/bcm.css');
  define('CONST_WEBSITE_GOOGLE_ANALYTICS','');

  define('CONST_DISPLAY_SEARCH_MENU',true);
  define('CONST_DISPLAY_HOMEPAGE_ICON', 'home_48.png');
  define('CONST_PAGER_NUM', '50');
  $gasMainMenu = array();

  //specific component constants
  define('CONST_SUB_HEADER_BAR', 'true');
  define('CONST_PROFILE_HEADER', 'true');
  define('CONST_LEFT_MENU', 'true');
  define('CONST_HOME_ICON_HEADER', 'true');
  define('CONST_CONTACT_ICON_HEADER', 'true');
  define('CONST_EMAIL_ICON_HEADER', 'true');
  define('CONST_HEADER_LOGO','/media/picture/bcm_logo.png');
  define('CONST_HEADER_FAVICON', '/media/picture/bcm/favicon.ico');
  define('CONST_LOGIN_MESSAGE','Welcome to BCMedia CRM, please login to access the application. ');
  define('CONST_TYPE_HOMEPAGE','home');

  define('CONST_WEBSITE_LOADING_PICTURE','/common/pictures/loading.gif');


  function getCustomWebsiteFooter($pasFooter)
  {
    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');

    $sHTML = $oHTML->getBlocStart('footerContainerId');
    $sHTML.= $oHTML->getBlocStart();

    $sHTML.= '<ul>';

    if(isset($pasFooter) && !empty($pasFooter))
    {
      foreach($pasFooter as $asFooterLinks)
      {
        if(isset($asFooterLinks['name']))
        {
          $sHTML.= '<li>';
          $sTitle = $asFooterLinks['name'];
          if(!empty($asFooterLinks['link']))
            $sLink = $asFooterLinks['link'];
          else
          {
            if(!empty($asFooterLinks['uid']))
            {
              $nPnPk = (int)$asFooterLinks['pk'];
              $sLink = $oPage->getUrl(''.$asFooterLinks['uid'].'',''.$asFooterLinks['type'].'',''.$asFooterLinks['action'].'',$nPnPk);
            }
              else
              $sLink = '#';
          }
          $sHTML.= $oHTML->getLink($sTitle, $sLink,array('target'=>$asFooterLinks['target']));
          $sHTML.= '</li>';
        }
    }
    }
    $sHTML.= '</ul>';

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

?>