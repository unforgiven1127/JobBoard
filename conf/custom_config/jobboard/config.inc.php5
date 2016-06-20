<?php

  define('CONST_WEBSITE_LOGGEDOUT_LOGO','/media/picture/header.jpg');
  define('CONST_WEBSITE_LOGO_URL','http://www.slate.co.jp');

  define('CONST_WEBSITE_MAIN_CSS', '/common/style/jobboard.css');
  define('CONST_WEBSITE_GOOGLE_ANALYTICS', "<script>var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-9596545-5']);
  _gaq.push(['_setDomainName', 'slate.co.jp']);
  _gaq.push(['_trackPageview']);

  (function() {

    var elements = document.getElementsByClassName('labelClass');
    for (var i = 0; i < elements.length; i++) {
        elements[i].style.width=('100px');
    }

    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();</script>
");

  define('CONST_DISPLAY_SEARCH_MENU', true);
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
  define('CONST_HEADER_LOGO','/component/display/resources/jobboard/pictures/top_slate_logo.png');
  define('CONST_HEADER_FAVICON', '/media/picture/jobboard/favicon.ico');
  define('CONST_LOGIN_MESSAGE','Welcome Slate job boad, please login to access the application. ');
  define('CONST_TYPE_HOMEPAGE','home');

  define('CONST_WEBSITE_LOADING_PICTURE','/common/pictures/loading_jobboard.gif');



  //CONSTANT relative sospecific components (not all the platforms)
  define('CONST_JOBBOARD_MAIL_SITE_NAME', 'Slate job board');


  function getCustomWebsiteFooter($pasFooter)
  {
    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');

    $sHTML = $oHTML->getBlocStart('footerContainerId');

    $sHTML.= $oHTML->getBlocStart('footerDiv');

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'footerTopContainer footerBlock'));

        $sHTML.= $oHTML->getBlocStart('Language', array('class' => 'footerTopSection footerTopLanguage'));
          $sHTML.= $oHTML->getText('Language: ');
          $sHTML.= $oHTML->getLink(' Japanese ', '/?setLang=jp');
          //$sHTML.= $oHTML->getLink(' Chi ', '/?setLang=ch');
           $sHTML.= $oHTML->getText(' - ');
          $sHTML.= $oHTML->getLink(' English', '/?setLang=en');
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('', array('class' => 'footerTopSection footerTopAddress'));
          $sHTML.= $oHTML->getText(' 1-4-2 Minami Aoyama, Minato-ku, Tokyo, Japan 107-0062 ');
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('', array('class' => 'footerTopSection footerTopPhone'));
          $sHTML.= $oHTML->getLink(' +81-3-6666-4142 ', 'callto:+81-3-6666-4142');
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('', array('class' => 'footerTopSection footerTopMail'));
          $sHTML.= $oHTML->getLink('info@slate.co.jp', 'mailto:info@slate.co.jp');
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getFloatHack();

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getFloatHack();



      $sHTML.= $oHTML->getBlocStart('', array('class' => 'footerBottomContainer footerBlock'));

        $sHTML.= $oHTML->getBlocStart('copyright');
        $sHTML.= $oHTML->getLink('&copy; Copyright '.date('Y'),' Slate Consulting');
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('aboutus');
        $sHTML.= $oHTML->getLink('About Us ','http://www.slate.co.jp/about/');
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('terms');
        $sHTML.= $oHTML->getLink('Terms & Conditions ','http://www.slate.co.jp/terms-and-conditions/');
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('privacy');
        $sHTML.= $oHTML->getLink('Privacy ','http://www.slate.co.jp/privacy/');
        $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocStart('');
        $sHTML.= $oHTML->getLink('Powered by BC Media ','http://www.bulbouscell.com');
        $sHTML.= $oHTML->getBlocEnd();

        //if(in_array($_SERVER['REMOTE_ADDR'], array('192.168.81.93','118.243.81.245','183.77.226.168', '118.243.81.248', '122.135.62.20', '203.167.38.11')))
        if(in_array($_SERVER['REMOTE_ADDR'], array('192.168.81.93','118.243.81.245','118.243.81.246', '118.243.81.248','183.77.226.168',  '122.135.62.20', '203.167.38.11')))
        {
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
        }

      $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

?>
