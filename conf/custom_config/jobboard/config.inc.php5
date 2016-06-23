<?php

  define('CONST_WEBSITE_LOGGEDOUT_LOGO','/media/picture/header.jpg');
  define('CONST_WEBSITE_LOGO_URL','http://www.slate.co.jp');

  define('CONST_WEBSITE_MAIN_CSS', '/common/style/jobboard.css');
  define('CONST_WEBSITE_GOOGLE_ANALYTICS', "<script>var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-9596545-5']);
  _gaq.push(['_setDomainName', 'slate.co.jp']);
  _gaq.push(['_trackPageview']);

  (function() {
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

    $useragent=$_SERVER['HTTP_USER_AGENT'];
      if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
      {
        $sHTML = '';
      }

    return $sHTML;
  }

?>
