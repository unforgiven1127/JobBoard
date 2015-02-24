<?php
  define('CONST_WEBSITE_LOGGEDOUT_LOGO', '');
  define('CONST_WEBSITE_LOGO_URL','http://www.talentatlas.com');

  define('CONST_WEBSITE_MAIN_CSS', '/common/style/talentatlas.css');
  define('CONST_WEBSITE_GOOGLE_ANALYTICS',
          "<script type='text/javascript'>
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-9596545-6']);
            _gaq.push(['_trackPageview']);

            (function() {
              var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
              ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
              var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
           </script>");

  define('CONST_DISPLAY_SEARCH_MENU', false);
  define('CONST_DISPLAY_HOMEPAGE_ICON', 'home_48.png');
  define('CONST_LOGIN_MESSAGE','Welcome to Talent Atlas, please enter your login details ');
  $gasMainMenu = array();


  //specific component constants
  define('CONST_SUB_HEADER_BAR', 'false');
  define('CONST_PROFILE_HEADER', 'false');
  define('CONST_LEFT_MENU', 'false');
  define('CONST_HOME_ICON_HEADER', 'false');
  define('CONST_CONTACT_ICON_HEADER', 'false');
  define('CONST_EMAIL_ICON_HEADER', 'false');
  define('CONST_HEADER_LOGO', '/media/picture/talent_logo.png');
  define('CONST_HEADER_FAVICON', '/media/picture/talentatlas/favicon.ico');
  define('CONST_TYPE_HOMEPAGE','home');

  define('CONST_WEBSITE_LOADING_PICTURE','/common/pictures/loading.gif');





  //CONSTANT relative sospecific components (not all the platforms)
  define('CONST_JOBBOARD_MAIL_SITE_NAME', 'Talent Atlas');

  define('CONST_AGGREGATOR_CRON', 'cron');
  define('CONST_CRON_FIRST', 'first');
  define('CONST_CRON_SECOND', 'second');
  define('CONST_CRON_THIRD','third');
  define('CONST_PAGER_NUM', '20');



  function getCustomWebsiteFooter()
  {
    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');

    $sHTML = $oHTML->getBlocStart('footerContainerId');

    $sHTML.= $oHTML->getBlocStart('footerDiv');
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'footerClass'));

    $sHTML.= $oHTML->getBlocStart('copyright');
    $sHTML.= $oHTML->getLink('&copy; Copyright 2012 ','');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('aboutus');
    $sHTML.= $oHTML->getLink('About Us ','');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('terms');
    $sHTML.= $oHTML->getLink('Terms & Conditions ','');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('privacy');
    $sHTML.= $oHTML->getLink('Privacy ','');
    $sHTML.= $oHTML->getBlocEnd();

    $oLogin = CDependency::getComponentByName('login');
    if($oLogin->isLogged())
    {
      $sHTML.= $oHTML->getBlocStart('admin');
      $sHTML.= $oHTML->getLink('Admin Page','/index.php5?uid=654-321&ppa=ppal&ppt=ppaj');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('logout');
      $sHTML.= $oHTML->getLink('Logout','/index.php5?uid=579-704&ppa=ppalgt&ppt=&ppk=0&pg=ajx');
      $sHTML.= $oHTML->getBlocEnd();
    }
    else
    {
      $sHTML.= $oHTML->getBlocStart('');
      $sHTML.= $oHTML->getLink('Management','/index.php5?uid=579-704');
      $sHTML.= $oHTML->getBlocEnd();
    }

    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

?>