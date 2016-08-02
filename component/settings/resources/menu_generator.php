<?php

mb_internal_encoding('utf-8');

/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
//BCM menu
$asArray = array();

  $asArray['en'] = array (
  0 =>
  array (
    'name' => 'Home',
    'link' => '',
    'icon' => 'pictures/home_48.png',
    'target' => '_parent',
    'uid' => '579-704',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array ('logged')
  ),
  1 =>
  array (
    'name' => 'Connections List',
    'link' => '',
    'icon' => 'pictures/connection_48.png',
    'target' => '_parent',
    'uid' => '777-249',
    'type' => 'ct',
    'action' => 'ppal',
    'pk' => 0,
    'right' => array ('logged'),
    'child' =>
    array (
      0 =>
      array (
        'name' => 'My Connections',
        'link' => '',
        'target' => '_parent',
        'uid' => '777-249',
        'type' => 'ct',
        'action' => 'ppal',
        'pk' => 0,
        'loginpk' => 1,
        'onclick' => '',
        'right' => array ('logged')
      ),
      1 =>
      array (
        'name' => 'Search Connections',
        'link' => '',
        'target' => '_parent',
        'uid' => '777-249',
        'type' => 'ct',
        'action' => 'ppal',
        'pk' => 0,
        'onclick' => 'addParameter(this);',
        'right' => array ('logged')
      ),
      2 =>
      array (
        'name' => 'Add Connections',
        'link' => '',
        'target' => '_parent',
        'uid' => '777-249',
        'type' => 'ct',
        'action' => 'ppaa',
        'pk' => 0,
        'onclick' => '',
        'icon' => '/component/addressbook/resources/pictures/ct_add_16.png',
        'right' => array ('logged')
      )
    ),
  ),
  2 =>
  array (
    'name' => 'Companies List',
    'link' => '',
    'icon' => 'pictures/company_48.png',
    'target' => '_parent',
    'uid' => '777-249',
    'type' => 'cp',
    'action' => 'ppal',
    'pk' => 0,
    'right' => array ('logged'),
    'child' =>
    array (
      0 =>
      array (
        'name' => 'My Companies',
        'link' => '',
        'target' => '_parent',
        'uid' => '777-249',
        'type' => 'cp',
        'action' => 'ppal',
        'pk' => 0,
        'loginpk' => 1,
        'onclick' => '',
        'right' => array ('logged')
      ),
      1 =>
      array (
        'name' => 'Search Companies',
        'link' => '',
        'target' => '_parent',
        'uid' => '777-249',
        'type' => 'cp',
        'action' => 'ppal',
        'pk' => 0,
        'onclick' => ' addParameter(this); resetCompanySearch(); ',
        'right' => array ('logged')
      ),
      2 =>
      array (
        'name' => 'Add Company',
        'link' => '',
        'target' => '_parent',
        'uid' => '777-249',
        'type' => 'cp',
        'action' => 'ppaa',
        'pk' => 0,
        'onclick' => '',
        'icon' => '/component/addressbook/resources/pictures/cp_add_16.png',
        'right' => array ('logged')
      )
    ),
  ),
  3 =>
  array (
    'name' => 'Projects',
    'link' => '',
    'icon' => 'pictures/project_48.png',
    'target' => '_parent',
    'uid' => '456-789',
    'type' => 'prj',
    'action' => 'ppal',
    'pk' => 0,
    'right' => array ('logged'),
    'child' =>
    array (
      0 =>
      array (
        'name' => 'My tasks',
        'link' => '',
        'target' => '_parent',
        'uid' => '456-789',
        'type' => 'task',
        'action' => ' ppal',
        'pk' => 0,
        'loginpk' => 1,
        'onclick' => '',
        'right' => array ('logged')
      ),
      1 =>
      array (
        'name' => 'Projects List',
        'link' => '',
        'target' => '_parent',
        'uid' => '456-789',
        'type' => ' prj',
        'action' => 'ppal',
        'pk' => 0,
        'onclick' => '',
        'right' => array ('logged')
      ),
      3 =>
      array (
        'name' => 'Project Users',
        'link' => '',
        'target' => '_parent',
        'uid' => '456-789',
        'type' => 'prjacr',
        'action' => 'ppae',
        'pk' => 0,
        'onclick' => '',
        'right' => array ('logged')
      ),
    ),
  ),
  4 =>
  array (
    'name' => 'Shared document List',
    'link' => '',
    'icon' => 'pictures/shared_space_48.png',
    'target' => '_parent',
    'uid' => '999-111',
    'type' => 'shdoc',
    'action' => 'ppal',
    'pk' => 0,
    'right' => array ('logged')
  ),
  5 =>
  array (
    'name' => 'Contacts',
    'link' => '',
    'icon' => 'pictures/contact_48.png',
    'target' => '_parent',
    'uid' => '579-704',
    'type' => 'usr',
    'action' => ' ppal',
    'pk' => 0,
    'right' => array ('logged')
  ),
  8 =>
  array (
    'name' => 'Calendar',
    'link' => '',
    'icon' => 'pictures/calendar_48.png',
    'target' => '',
    'uid' => '400-650',
    'type' => 'calendar',
    'action' => 'ppav',
    'pk' => -1,
    'ajaxpopup' => 0,
    'loginpk' => 1,
    'right' => array ('logged'),
    'child' =>
    array (
      0 =>
      array (
        'name' => 'My calendar',
        'uid' => '400-650',
        'type' => 'calendar',
        'action' => 'ppav',
        'pk' => -1,
        'ajaxpopup' => 0,
        'icon' => '/common/pictures/items/calendar_16.png',
        'right' => array ('logged')
      ),
      1 =>
      array (
        'name' => 'Shared calendar',
        'uid' => '400-650',
        'type' => 'calendar',
        'action' => 'ppav',
        'pk' => 0,
        'ajaxpopup' => 0,
        'icon' => '/common/pictures/items/calendar_16.png',
        'right' => array ('logged')
      ),
      2 =>
      array (
        'name' => 'Add an event in your calendar',
        'uid' => '400-650',
        'type' => 'calendar',
        'action' => 'ppaa',
        'pk' => 0,
        'ajaxpopup' => 0,
        'icon' => '/common/pictures/items/calendar_add_16.png',
        'right' => array ('logged')
      )
    )
  ),
  9 =>
  array (
    'name' => 'Other features',
    'link' => 'javascript:;',
    'icon' => 'pictures/menu_other_48.png',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'ajaxpopup' => 0,
    'loginpk' => 1,
    'right' => array ('logged'),
    'child' =>
    array (
      0 =>
      array (
        'name' => 'Webmail',
        'link' => 'https://mail.bulbouscell.com',
        'icon' => 'pictures/mail_24.png',
        'target' => '_blank',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'embedLink' => 0,
        'right' => array ('logged')
      ),
      1 =>
      array (
        'name' => 'BCM Mail',
        'link' => '',
        'icon' => 'pictures/webmail_24.png',
        'target' => '',
        'uid' => '009-724',
        'type' => 'webmail',
        'action' => 'ppaa',
        'pk' => 0,
        'ajaxpopup' => 1,
        'loginpk' => 1,
        'right' => array ('logged')
      ),
    ),
  ),
);

 $sString = base64_encode(serialize($asArray));
 echo 'Serialize menu:<br />'.$sString;

 echo '<br /><hr><br />Detail:<br /><pre>';
 //var_dump($asArray);
 echo '</pre>';




/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
//slate website  menu (multilingual)
 $asArray = array();

 $asArray['en'] = array (
  0 =>
  array (
    'name' => 'ABOUT',
    'link' => 'http://www.slate.co.jp/about',
    'legend' => 'Slate Consulting',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*'),
    'child' => array (
      0 =>
      array (
        'name' => 'Message from the CEO',
        'link' => 'http://www.slate.co.jp/about?show=ceo_message',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      1 =>
      array (
        'name' => 'Our Mission',
        'link' => 'http://www.slate.co.jp/about?show=our_mission',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      2 =>
      array (
        'name' => 'Practice Group',
        'link' => 'http://www.slate.co.jp/about?show=practice_group',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      3 =>
      array (
        'name' => 'Code Of Ethics',
        'link' => 'http://www.slate.co.jp/expertise?team=code_of_ethics',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      4 =>
      array (
        'name' => 'Search Process',
        'link' => 'http://www.slate.co.jp/about?show=search_process',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      )
    )
  ),
  1 =>
  array (
    'name' => 'EXPERTISE',
    'link' => 'http://www.slate.co.jp/expertise/',
    'legend' => 'What we specialise in',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*'),
    'child' =>
    array (
      0 =>
      array (
        'name' => 'Financial Services',
        'link' => 'http://www.slate.co.jp/expertise?show=financial_services',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      1 =>
      array (
        'name' => 'Fincance & Accounting',
         'link' => 'http://www.slate.co.jp/expertise?show=finance_and_accounting',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      2 =>
      array (
        'name' => 'IT Services',
        'link' => 'http://www.slate.co.jp/expertise?show=it_services',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      3 =>
      array (
        'name' => 'Healthcare',
        'link' => 'http://www.slate.co.jp/expertise?show=life_sciences',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      4 =>
      array (
        'name' => 'Industrial',
        'link' => 'http://www.slate.co.jp/expertise?show=industrial',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      5 =>
      array (
        'name' => 'Consumer Goods',
        'link' => 'http://www.slate.co.jp/expertise?show=consumer_goods',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      )
    )
  ),
  2 =>
  array (
    'name' => 'PEOPLE',
    'legend' => 'Meet the team',
    'link' => 'http://slate.co.jp/people',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*')
  ),
  3 =>
  array (
    'name' => 'JOB BOARD',
    'legend' => 'Candidate Services',
    'link' => 'http://jobs.slate.co.jp',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*'),
    'child' =>
    array
    (
      0 => array
      (
      'name' => 'Career Advice',
      'legend' => '',
      'link' => 'http://slate.co.jp/career-advice/',
      'icon' => '',
      'target' => '',
      'uid' => '',
      'type' => '',
      'action' => '',
      'pk' => 0,
      'right' => array ('*')
     ),
     1 => array
      (
      'name' => 'Employment Opportunities',
      'legend' => '',
      'link' => 'http://jobs.slate.co.jp',
      'icon' => '',
      'target' => '',
      'uid' => '',
      'type' => '',
      'action' => '',
      'pk' => 0,
      'right' => array ('*')
     )
    )
  ),
  4 =>
  array (
    'name' => 'TESTIMONIALS.',
    'legend' => 'Why people chose us',
    'link' => 'http://slate.co.jp/about',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*')
  ),
  5 => array (
    'name' => 'CONTACT',
    'legend' => 'Find out more',
    'link' => 'http://slate.co.jp/about',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*')
  ),
  6 => array (
    'name' => 'Admin section',
    'legend' => 'Manage jobboard',
    'link' => '',
    'icon' => '',
    'target' => '',
    'uid' => '579-704',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array ('logged'),
    'child' =>
    array
    (
      0 => array
      (
      'name' => 'Homepage',
      'legend' => '',
      'link' => '',
      'icon' => '',
      'target' => '',
      'uid' => '579-704',
      'type' => '',
      'action' => '',
      'pk' => 0,
      'right' => array ('logged')
     ),
     1 => array
     (
      'name' => 'Social network',
      'legend' => '',
      'link' => '',
      'icon' => '',
      'target' => '',
      'uid' => '654-321',
      'type' => 'shjob',
      'action' => 'ppal',
      'pk' => 0,
      'right' => array ('logged')
     ),
     2 => array
     (
      'name' => 'Edit positions',
      'legend' => '',
      'link' => '',
      'icon' => '',
      'target' => '',
      'uid' => '654-321',
      'type' => 'ppaj',
      'action' => 'ppal',
      'pk' => 0,
      'right' => array ('logged')
     )
    )
  )
);

$asArray['jp'] = array (
  0 =>
  array (
    'name' => 'ABOUT',
    'link' => 'http://www.slate.co.jp/about',
    'legend' => 'Slate Consulting',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*'),
    'child' => array (
      0 =>
      array (
        'name' => 'Message from the CEO',
        'link' => 'http://www.slate.co.jp/about?show=ceo_message',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      1 =>
      array (
        'name' => 'Our Mission',
        'link' => 'http://www.slate.co.jp/about?show=our_mission',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      2 =>
      array (
        'name' => 'Practice Group',
        'link' => 'http://www.slate.co.jp/about?show=practice_group',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      3 =>
      array (
        'name' => 'Code Of Ethics',
        'link' => 'http://www.slate.co.jp/expertise?team=code_of_ethics',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      4 =>
      array (
        'name' => 'Search Process',
        'link' => 'http://www.slate.co.jp/about?show=search_process',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      )
    )
  ),
  1 =>
  array (
    'name' => 'EXPERTISE',
    'link' => 'http://www.slate.co.jp/expertise/',
    'legend' => 'What we specialise in',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*'),
    'child' =>
    array (
      0 =>
      array (
        'name' => 'Financial Services',
        'link' => 'http://www.slate.co.jp/expertise?show=financial_services',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      1 =>
      array (
        'name' => 'Fincance & Accounting',
         'link' => 'http://www.slate.co.jp/expertise?show=finance_and_accounting',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      2 =>
      array (
        'name' => 'IT Services',
        'link' => 'http://www.slate.co.jp/expertise?show=it_services',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      3 =>
      array (
        'name' => 'Healthcare',
        'link' => 'http://www.slate.co.jp/expertise?show=life_sciences',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      4 =>
      array (
        'name' => 'Industrial',
        'link' => 'http://www.slate.co.jp/expertise?show=industrial',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      5 =>
      array (
        'name' => 'Consumer Goods',
        'link' => 'http://www.slate.co.jp/expertise?show=consumer_goods',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      )
    )
  ),
  2 =>
  array (
    'name' => 'PEOPLE',
    'legend' => 'Meet the team',
    'link' => 'http://slate.co.jp/people',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*')
  ),
  3 =>
  array (
    'name' => 'JOB BOARD',
    'legend' => 'Candidate Services',
    'link' => 'http://jobs.slate.co.jp',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*'),
    'child' =>
    array
    (
      0 => array
      (
      'name' => 'Career Advice',
      'legend' => '',
      'link' => 'http://slate.co.jp/career-advice/',
      'icon' => '',
      'target' => '',
      'uid' => '',
      'type' => '',
      'action' => '',
      'pk' => 0,
      'right' => array ('*')
     ),
     1 => array
      (
      'name' => 'Employment Opportunities',
      'legend' => '',
      'link' => 'http://jobs.slate.co.jp',
      'icon' => '',
      'target' => '',
      'uid' => '',
      'type' => '',
      'action' => '',
      'pk' => 0,
      'right' => array ('*')
     )
    )
  ),
  4 =>
  array (
    'name' => 'TESTIMONIALS..',
    'legend' => 'Why people chose us',
    'link' => 'http://slate.co.jp/about',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*')
  ),
  5 => array (
    'name' => 'CONTACT',
    'legend' => 'Find out more',
    'link' => 'http://slate.co.jp/about',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*')
  ),
  6 => array (
    'name' => 'Admin section',
    'legend' => 'Manage jobboard',
    'link' => '',
    'icon' => '',
    'target' => '',
    'uid' => '579-704',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array ('logged'),
    'child' =>
    array
    (
      0 => array
      (
      'name' => 'Homepage',
      'legend' => '',
      'link' => '',
      'icon' => '',
      'target' => '',
      'uid' => '579-704',
      'type' => '',
      'action' => '',
      'pk' => 0,
      'right' => array ('logged')
     ),
     1 => array
     (
      'name' => 'Social network',
      'legend' => '',
      'link' => '',
      'icon' => '',
      'target' => '',
      'uid' => '654-321',
      'type' => 'shjob',
      'action' => 'ppal',
      'pk' => 0,
      'right' => array ('logged')
     ),
     2 => array
     (
      'name' => 'Edit positions',
      'legend' => '',
      'link' => '',
      'icon' => '',
      'target' => '',
      'uid' => '654-321',
      'type' => 'ppaj',
      'action' => 'ppal',
      'pk' => 0,
      'right' => array ('logged')
     )
    )
  )
);

 $sString = base64_encode(serialize($asArray));
 echo 'Serialize menu:<br />'.$sString;

 echo '<br /><hr><br />Detail:<br /><pre>';
 //var_dump($asArray);
 echo '</pre>';








/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
//TALENT ATLAS  menu (multilingual later for jp moderator)
 $asArray = array();

 $asArray['en'] = array (
  0 =>
  array (
    'name' => 'Home',
    'link' => '',
    'legend' => '',
    'icon' => '',
    'target' => '',
    'uid' => '579-704',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('logged')
  ),
 1 =>
  array (
    'name' => 'Position',
    'legend' => '',
    'link' => '',
    'icon' => '',
    'target' => '',
    'uid' => '654-321',
    'type' => 'ppaj',
    'action' => 'ppal',
    'pk' => 0,
    'right' => array('logged')
  )
);



$sString = base64_encode(serialize($asArray));
 echo 'Serialize menu:<br />'.$sString;

 echo '<br /><hr><br />Detail:<br /><pre>';
 //var_dump($asArray);
 echo '</pre>';












































 // temporary menu for the jobboard

 $asArray = array();

 $asArray['en'] = array (
  0 =>
  array (
    'name' => 'ABOUT',
    'link' => 'http://www.slate.co.jp/about',
    'legend' => 'Slate Consulting',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*'),
    'child' => array (
      0 =>
      array (
        'name' => 'Message from the CEO',
        'link' => 'http://www.slate.co.jp/about?show=ceo_message',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      1 =>
      array (
        'name' => 'Our Mission',
        'link' => 'http://www.slate.co.jp/about?show=our_mission',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      2 =>
      array (
        'name' => 'Practice Group',
        'link' => 'http://www.slate.co.jp/about?show=practice_group',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      3 =>
      array (
        'name' => 'Code Of Ethics',
        'link' => 'http://www.slate.co.jp/expertise?team=code_of_ethics',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      4 =>
      array (
        'name' => 'Search Process',
        'link' => 'http://www.slate.co.jp/about?show=search_process',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      )
    )
  ),
  1 =>
  array (
    'name' => 'EXPERTISE',
    'link' => 'http://www.slate.co.jp/expertise/',
    'legend' => 'What We specialise in',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*'),
    'child' =>
    array (
      0 =>
      array (
        'name' => 'Financial Services',
        'link' => 'http://www.slate.co.jp/expertise?show=financial_services',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      1 =>
      array (
        'name' => 'Fincance & Accounting',
         'link' => 'http://www.slate.co.jp/expertise?show=finance_and_accounting',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      2 =>
      array (
        'name' => 'IT Services',
        'link' => 'http://www.slate.co.jp/expertise?show=it_services',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      3 =>
      array (
        'name' => 'Healthcare',
        'link' => 'http://www.slate.co.jp/expertise?show=life_sciences',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      4 =>
      array (
        'name' => 'Industrial',
        'link' => 'http://www.slate.co.jp/expertise?show=industrial',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      5 =>
      array (
        'name' => 'Consumer Goods',
        'link' => 'http://www.slate.co.jp/expertise?show=consumer_goods',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      )
    )
  ),
  2 =>
  array (
    'name' => 'PEOPLE',
    'legend' => 'Meet the team',
    'link' => 'http://slate.co.jp/people',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*')
  ),
  3 =>
  array (
    'name' => 'JOB BOARD',
    'legend' => 'Candidate Services',
    'link' => 'http://jobs.slate.co.jp',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*'),
    'child' =>
    array
    (
      0 => array
      (
      'name' => 'Career Advice',
      'legend' => '',
      'link' => 'http://slate.co.jp/career-advice/',
      'icon' => '',
      'target' => '',
      'uid' => '',
      'type' => '',
      'action' => '',
      'pk' => 0,
      'right' => array ('*')
     ),
     1 => array
      (
      'name' => 'Employment Opportunities',
      'legend' => '',
      'link' => 'http://jobs.slate.co.jp',
      'icon' => '',
      'target' => '',
      'uid' => '',
      'type' => '',
      'action' => '',
      'pk' => 0,
      'right' => array ('*')
     )
    )
  ),
  4 =>
  array (
    'name' => 'TESTIMONIALS...',
    'legend' => 'Why people chose us',
    'link' => 'http://slate.co.jp/about',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*')
  ),
  5 => array (
    'name' => 'CONTACT',
    'legend' => 'Find out more',
    'link' => 'http://slate.co.jp/about',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*')
  ),
  6 => array (
    'name' => 'Admin section',
    'legend' => 'Manage jobboard',
    'link' => '',
    'icon' => '',
    'target' => '',
    'uid' => '579-704',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array ('logged'),
    'child' =>
    array
    (
      0 => array
      (
      'name' => 'Homepage',
      'legend' => '',
      'link' => '',
      'icon' => '',
      'target' => '',
      'uid' => '579-704',
      'type' => '',
      'action' => '',
      'pk' => 0,
      'right' => array ('logged')
     ),
     1 => array
     (
      'name' => 'Social network',
      'legend' => '',
      'link' => '',
      'icon' => '',
      'target' => '',
      'uid' => '654-321',
      'type' => 'shjob',
      'action' => 'ppal',
      'pk' => 0,
      'right' => array ('logged')
     ),
     2 => array
     (
      'name' => 'Edit positions',
      'legend' => '',
      'link' => '',
      'icon' => '',
      'target' => '',
      'uid' => '654-321',
      'type' => 'ppaj',
      'action' => 'ppal',
      'pk' => 0,
      'right' => array ('logged')
     )
    )
  )
);

$asArray['jp'] = array (
  0 =>
  array (
    'name' => mb_convert_encoding('会社案内', 'utf-8'),
    'link' => 'http://www.slate.co.jp/about_slate.html',
    'legend' => '&nbsp;',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*'),
    'child' => array (
      0 =>
      array (
        'name' => mb_convert_encoding('CEOからのメッセージ', 'utf-8'),
        'link' => 'http://www.slate.co.jp/jp/about/ceo_message.html',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      1 =>
      array (
        'name' => '私たちのミッション',
        'link' => 'http://www.slate.co.jp/jp/about/our_mission.html',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      2 =>
      array (
        'name' => '専門分野',
        'link' => 'http://www.slate.co.jp/jp/about/practice_groups.html',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      3 =>
      array (
        'name' => 'サーチプロセス',
        'link' => 'http://www.slate.co.jp/jp/about/search_process.html',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      )
    )
  ),
  1 =>
  array (
    'name' => 'サービス',
    'link' => 'http://www.slate.co.jp/jp/client_services.html',
    'legend' => '&nbsp;',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*'),
    'child' =>
    array (
      0 =>
      array (
        'name' => '金融',
        'link' => 'http://www.slate.co.jp/jp/group/finance.html',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      1 =>
      array (
        'name' => '財務経理',
         'link' => 'http://www.slate.co.jp/jp/group/finance_accounting.html',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      2 =>
      array (
        'name' => 'ITサービス',
        'link' => 'http://www.slate.co.jp/jp/group/it_services.html',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      3 =>
      array (
        'name' => 'ライフサイエンス',
        'link' => 'http://www.slate.co.jp/jp/group/life_sciences.html',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      4 =>
      array (
        'name' => '産業界',
        'link' => 'http://www.slate.co.jp/jp/group/industrial.html',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      ),
      5 =>
      array (
        'name' => '消費財',
        'link' => 'http://www.slate.co.jp/jp/group/consumer_goods.html',
        'target' => '',
        'uid' => '',
        'type' => '',
        'action' => '',
        'pk' => 0,
        'loginpk' => 0,
        'onclick' => '',
        'right' => array('*')
      )
    )
  ),
  2 =>
  array (
    'name' => '私達のスタッフ',
    'legend' => '&nbsp;',
    'link' => 'http://www.slate.co.jp/jp/our_people.html',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*')
  ),
  3 =>
  array (
    'name' => 'Jobs',
    'legend' => '&nbsp;',
    'link' => 'http://jobs.slate.co.jp?setLang=jp',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*'),
    'child' =>
    array
    (
      0 => array
      (
      'name' => '候補者の皆様へ',
      'legend' => '',
      'link' => 'http://www.slate.co.jp/jp/candidate_services.html',
      'icon' => '',
      'target' => '',
      'uid' => '',
      'type' => '',
      'action' => '',
      'pk' => 0,
      'right' => array ('*')
     )
    )
  ),
  5 => array (
    'name' => 'コンタクト',
    'legend' => '&nbsp;',
    'link' => 'http://www.slate.co.jp/jp/contact.html',
    'icon' => '',
    'target' => '',
    'uid' => '',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array('*')
  ),
  6 => array (
    'name' => 'Admin section',
    'legend' => 'Manage jobboard',
    'link' => '',
    'icon' => '',
    'target' => '',
    'uid' => '579-704',
    'type' => '',
    'action' => '',
    'pk' => 0,
    'right' => array ('logged'),
    'child' =>
    array
    (
      0 => array
      (
      'name' => 'Homepage',
      'legend' => '',
      'link' => '',
      'icon' => '',
      'target' => '',
      'uid' => '579-704',
      'type' => '',
      'action' => '',
      'pk' => 0,
      'right' => array ('logged')
     ),
     1 => array
     (
      'name' => 'Social network',
      'legend' => '',
      'link' => '',
      'icon' => '',
      'target' => '',
      'uid' => '654-321',
      'type' => 'shjob',
      'action' => 'ppal',
      'pk' => 0,
      'right' => array ('logged')
     ),
     2 => array
     (
      'name' => 'Edit positions',
      'legend' => '',
      'link' => '',
      'icon' => '',
      'target' => '',
      'uid' => '654-321',
      'type' => 'ppaj',
      'action' => 'ppal',
      'pk' => 0,
      'right' => array ('logged')
     )
    )
  )
);



 $sString = base64_encode(serialize($asArray));

 /*//fix size for non utf8 characters
 $sString = preg_replace_callback(
    '!(?<=^|;)s:(\d+)(?=:"(.*?)";(?:}|a:|s:|b:|d:|i:|o:|N;))!s',
    'serialize_fix_callback',
    $sString
);

function serialize_fix_callback($match) {
    return 's:' . strlen($match[2]);
}*/



echo ' Serialize menu: (fixed) <br />'.$sString;
echo '<br /> - 私達のスタッフ: '.strlen('私達のスタッフ');

if(!unserialize(base64_decode($sString)))
  echo 'gaaaaaaaaaaaaaa<br /><br /><br />';
else
  echo 'ok<br />';

 echo '<br /><hr><br />Detail :<br /><pre>';
 //var_dump($asArray);
 echo '</pre>';











































 /*full english towards old site

 a:2:{s:2:"en";a:6:{i:0;a:11:{s:4:"name";s:5:"ABOUT";s:4:"link";s:39:"http://www.slate.co.jp/about_slate.html";s:6:"legend";s:16:"Slate Consulting";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}s:5:"child";a:5:{i:0;a:10:{s:4:"name";s:20:"Message from the CEO";s:4:"link";s:45:"http://www.slate.co.jp/about/ceo_message.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:1;a:10:{s:4:"name";s:11:"Our Mission";s:4:"link";s:45:"http://www.slate.co.jp/about/our_mission.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:2;a:10:{s:4:"name";s:14:"Practice Group";s:4:"link";s:49:"http://www.slate.co.jp/about/practice_groups.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:3;a:10:{s:4:"name";s:14:"Code Of Ethics";s:4:"link";s:40:"http://www.slate.co.jp/group/ethics.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:4;a:10:{s:4:"name";s:14:"Search Process";s:4:"link";s:48:"http://www.slate.co.jp/about/search_process.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}}}i:1;a:11:{s:4:"name";s:9:"EXPERTISE";s:4:"link";s:43:"http://www.slate.co.jp/client_services.html";s:6:"legend";s:21:"What we specialise in";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}s:5:"child";a:6:{i:0;a:10:{s:4:"name";s:18:"Financial Services";s:4:"link";s:41:"http://www.slate.co.jp/group/finance.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:1;a:10:{s:4:"name";s:21:"Fincance & Accounting";s:4:"link";s:52:"http://www.slate.co.jp/group/finance_accounting.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:2;a:10:{s:4:"name";s:11:"IT Services";s:4:"link";s:45:"http://www.slate.co.jp/group/it_services.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:3;a:10:{s:4:"name";s:10:"Healthcare";s:4:"link";s:47:"http://www.slate.co.jp/group/life_sciences.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:4;a:10:{s:4:"name";s:10:"Industrial";s:4:"link";s:44:"http://www.slate.co.jp/group/industrial.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:5;a:10:{s:4:"name";s:14:"Consumer Goods";s:4:"link";s:48:"http://www.slate.co.jp/group/consumer_goods.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}}}i:2;a:10:{s:4:"name";s:6:"PEOPLE";s:6:"legend";s:13:"Meet the team";s:4:"link";s:38:"http://www.slate.co.jp/our_people.html";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}i:3;a:11:{s:4:"name";s:9:"JOB BOARD";s:6:"legend";s:18:"Candidate Services";s:4:"link";s:23:"http://jobs.slate.co.jp";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}s:5:"child";a:2:{i:0;a:10:{s:4:"name";s:13:"Career Advice";s:6:"legend";s:0:"";s:4:"link";s:46:"http://www.slate.co.jp/candidate_services.html";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}i:1;a:10:{s:4:"name";s:24:"Employment Opportunities";s:6:"legend";s:0:"";s:4:"link";s:23:"http://jobs.slate.co.jp";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}}}i:5;a:10:{s:4:"name";s:7:"CONTACT";s:6:"legend";s:13:"Find out more";s:4:"link";s:35:"http://www.slate.co.jp/contact.html";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}i:6;a:11:{s:4:"name";s:13:"Admin section";s:6:"legend";s:15:"Manage jobboard";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"579-704";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}s:5:"child";a:3:{i:0;a:10:{s:4:"name";s:8:"Homepage";s:6:"legend";s:0:"";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"579-704";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}}i:1;a:10:{s:4:"name";s:14:"Social network";s:6:"legend";s:0:"";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"654-321";s:4:"type";s:5:"shjob";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}}i:2;a:10:{s:4:"name";s:14:"Edit positions";s:6:"legend";s:0:"";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"654-321";s:4:"type";s:4:"ppaj";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}}}}}s:2:"jp";a:6:{i:0;a:11:{s:4:"name";s:5:"ABOUT";s:4:"link";s:39:"http://www.slate.co.jp/about_slate.html";s:6:"legend";s:16:"Slate Consulting";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}s:5:"child";a:5:{i:0;a:10:{s:4:"name";s:20:"Message from the CEO";s:4:"link";s:45:"http://www.slate.co.jp/about/ceo_message.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:1;a:10:{s:4:"name";s:11:"Our Mission";s:4:"link";s:45:"http://www.slate.co.jp/about/our_mission.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:2;a:10:{s:4:"name";s:14:"Practice Group";s:4:"link";s:49:"http://www.slate.co.jp/about/practice_groups.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:3;a:10:{s:4:"name";s:14:"Code Of Ethics";s:4:"link";s:40:"http://www.slate.co.jp/group/ethics.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:4;a:10:{s:4:"name";s:14:"Search Process";s:4:"link";s:48:"http://www.slate.co.jp/about/search_process.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}}}i:1;a:11:{s:4:"name";s:9:"EXPERTISE";s:4:"link";s:43:"http://www.slate.co.jp/client_services.html";s:6:"legend";s:21:"What we specialise in";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}s:5:"child";a:6:{i:0;a:10:{s:4:"name";s:18:"Financial Services";s:4:"link";s:41:"http://www.slate.co.jp/group/finance.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:1;a:10:{s:4:"name";s:21:"Fincance & Accounting";s:4:"link";s:52:"http://www.slate.co.jp/group/finance_accounting.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:2;a:10:{s:4:"name";s:11:"IT Services";s:4:"link";s:45:"http://www.slate.co.jp/group/it_services.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:3;a:10:{s:4:"name";s:10:"Healthcare";s:4:"link";s:47:"http://www.slate.co.jp/group/life_sciences.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:4;a:10:{s:4:"name";s:10:"Industrial";s:4:"link";s:44:"http://www.slate.co.jp/group/industrial.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:5;a:10:{s:4:"name";s:14:"Consumer Goods";s:4:"link";s:48:"http://www.slate.co.jp/group/consumer_goods.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}}}i:2;a:10:{s:4:"name";s:6:"PEOPLE";s:6:"legend";s:13:"Meet the team";s:4:"link";s:38:"http://www.slate.co.jp/our_people.html";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}i:3;a:11:{s:4:"name";s:9:"JOB BOARD";s:6:"legend";s:18:"Candidate Services";s:4:"link";s:23:"http://jobs.slate.co.jp";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}s:5:"child";a:2:{i:0;a:10:{s:4:"name";s:13:"Career Advice";s:6:"legend";s:0:"";s:4:"link";s:46:"http://www.slate.co.jp/candidate_services.html";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}i:1;a:10:{s:4:"name";s:24:"Employment Opportunities";s:6:"legend";s:0:"";s:4:"link";s:23:"http://jobs.slate.co.jp";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}}}i:5;a:10:{s:4:"name";s:7:"CONTACT";s:6:"legend";s:13:"Find out more";s:4:"link";s:35:"http://www.slate.co.jp/contact.html";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}i:6;a:11:{s:4:"name";s:13:"Admin section";s:6:"legend";s:15:"Manage jobboard";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"579-704";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}s:5:"child";a:3:{i:0;a:10:{s:4:"name";s:8:"Homepage";s:6:"legend";s:0:"";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"579-704";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}}i:1;a:10:{s:4:"name";s:14:"Social network";s:6:"legend";s:0:"";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"654-321";s:4:"type";s:5:"shjob";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}}i:2;a:10:{s:4:"name";s:14:"Edit positions";s:6:"legend";s:0:"";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"654-321";s:4:"type";s:4:"ppaj";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}}}}}}

 //half japanese
 a:2:{s:2:"en";a:7:{i:0;a:11:{s:4:"name";s:5:"ABOUT";s:4:"link";s:28:"http://www.slate.co.jp/about";s:6:"legend";s:16:"Slate Consulting";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}s:5:"child";a:5:{i:0;a:10:{s:4:"name";s:20:"Message from the CEO";s:4:"link";s:45:"http://www.slate.co.jp/about?show=ceo_message";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:1;a:10:{s:4:"name";s:11:"Our Mission";s:4:"link";s:45:"http://www.slate.co.jp/about?show=our_mission";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:2;a:10:{s:4:"name";s:14:"Practice Group";s:4:"link";s:48:"http://www.slate.co.jp/about?show=practice_group";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:3;a:10:{s:4:"name";s:14:"Code Of Ethics";s:4:"link";s:52:"http://www.slate.co.jp/expertise?team=code_of_ethics";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:4;a:10:{s:4:"name";s:14:"Search Process";s:4:"link";s:48:"http://www.slate.co.jp/about?show=search_process";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}}}i:1;a:11:{s:4:"name";s:9:"EXPERTISE";s:4:"link";s:33:"http://www.slate.co.jp/expertise/";s:6:"legend";s:21:"What we specialise in";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}s:5:"child";a:6:{i:0;a:10:{s:4:"name";s:18:"Financial Services";s:4:"link";s:56:"http://www.slate.co.jp/expertise?show=financial_services";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:1;a:10:{s:4:"name";s:21:"Fincance & Accounting";s:4:"link";s:60:"http://www.slate.co.jp/expertise?show=finance_and_accounting";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:2;a:10:{s:4:"name";s:11:"IT Services";s:4:"link";s:49:"http://www.slate.co.jp/expertise?show=it_services";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:3;a:10:{s:4:"name";s:10:"Healthcare";s:4:"link";s:51:"http://www.slate.co.jp/expertise?show=life_sciences";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:4;a:10:{s:4:"name";s:10:"Industrial";s:4:"link";s:48:"http://www.slate.co.jp/expertise?show=industrial";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:5;a:10:{s:4:"name";s:14:"Consumer Goods";s:4:"link";s:52:"http://www.slate.co.jp/expertise?show=consumer_goods";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}}}i:2;a:10:{s:4:"name";s:6:"PEOPLE";s:6:"legend";s:13:"Meet the team";s:4:"link";s:25:"http://slate.co.jp/people";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}i:3;a:11:{s:4:"name";s:9:"JOB BOARD";s:6:"legend";s:18:"Candidate Services";s:4:"link";s:23:"http://jobs.slate.co.jp";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}s:5:"child";a:2:{i:0;a:10:{s:4:"name";s:13:"Career Advice";s:6:"legend";s:0:"";s:4:"link";s:33:"http://slate.co.jp/career-advice/";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}i:1;a:10:{s:4:"name";s:24:"Employment Opportunities";s:6:"legend";s:0:"";s:4:"link";s:23:"http://jobs.slate.co.jp";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}}}i:4;a:10:{s:4:"name";s:12:"TESTIMONIALS";s:6:"legend";s:19:"Why people chose us";s:4:"link";s:24:"http://slate.co.jp/about";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}i:5;a:10:{s:4:"name";s:7:"CONTACT";s:6:"legend";s:13:"Find out more";s:4:"link";s:24:"http://slate.co.jp/about";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}i:6;a:11:{s:4:"name";s:13:"Admin section";s:6:"legend";s:15:"Manage jobboard";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"579-704";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}s:5:"child";a:3:{i:0;a:10:{s:4:"name";s:8:"Homepage";s:6:"legend";s:0:"";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"579-704";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}}i:1;a:10:{s:4:"name";s:14:"Social network";s:6:"legend";s:0:"";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"654-321";s:4:"type";s:5:"shjob";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}}i:2;a:10:{s:4:"name";s:14:"Edit positions";s:6:"legend";s:0:"";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"654-321";s:4:"type";s:4:"ppaj";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}}}}}s:2:"jp";a:6:{i:0;a:11:{s:4:"name";s:12:"会社案内";s:4:"link";s:39:"http://www.slate.co.jp/about_slate.html";s:6:"legend";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}s:5:"child";a:4:{i:0;a:10:{s:4:"name";s:27:"CEOからのメッセージ";s:4:"link";s:48:"http://www.slate.co.jp/jp/about/ceo_message.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:1;a:10:{s:4:"name";s:27:"私たちのミッション";s:4:"link";s:48:"http://www.slate.co.jp/jp/about/our_mission.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:2;a:10:{s:4:"name";s:12:"専門分野";s:4:"link";s:52:"http://www.slate.co.jp/jp/about/practice_groups.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:3;a:10:{s:4:"name";s:21:"サーチプロセス";s:4:"link";s:51:"http://www.slate.co.jp/jp/about/search_process.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}}}i:1;a:11:{s:4:"name";s:30:"クライアントの皆様へ";s:4:"link";s:46:"http://www.slate.co.jp/jp/client_services.html";s:6:"legend";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}s:5:"child";a:6:{i:0;a:10:{s:4:"name";s:6:"金融";s:4:"link";s:44:"http://www.slate.co.jp/jp/group/finance.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:1;a:10:{s:4:"name";s:12:"財務経理";s:4:"link";s:55:"http://www.slate.co.jp/jp/group/finance_accounting.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:2;a:10:{s:4:"name";s:14:"ITサービス";s:4:"link";s:48:"http://www.slate.co.jp/jp/group/it_services.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:3;a:10:{s:4:"name";s:24:"ライフサイエンス";s:4:"link";s:50:"http://www.slate.co.jp/jp/group/life_sciences.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:4;a:10:{s:4:"name";s:9:"産業界";s:4:"link";s:47:"http://www.slate.co.jp/jp/group/industrial.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}i:5;a:10:{s:4:"name";s:9:"消費財";s:4:"link";s:51:"http://www.slate.co.jp/jp/group/consumer_goods.html";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:7:"loginpk";i:0;s:7:"onclick";s:0:"";s:5:"right";a:1:{i:0;s:1:"*";}}}}i:2;a:10:{s:4:"name";s:21:"私達のスタッフ";s:6:"legend";s:0:"";s:4:"link";s:41:"http://www.slate.co.jp/jp/our_people.html";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}i:3;a:11:{s:4:"name";s:4:"Jobs";s:6:"legend";s:0:"";s:4:"link";s:34:"http://jobs.slate.co.jp?setLang=jp";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}s:5:"child";a:1:{i:0;a:10:{s:4:"name";s:21:"候補者の皆様へ";s:6:"legend";s:0:"";s:4:"link";s:49:"http://www.slate.co.jp/jp/candidate_services.html";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}}}i:5;a:10:{s:4:"name";s:23:"Slateのコンタクト";s:6:"legend";s:0:"";s:4:"link";s:38:"http://www.slate.co.jp/jp/contact.html";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:1:"*";}}i:6;a:11:{s:4:"name";s:13:"Admin section";s:6:"legend";s:15:"Manage jobboard";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"579-704";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}s:5:"child";a:3:{i:0;a:10:{s:4:"name";s:8:"Homepage";s:6:"legend";s:0:"";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"579-704";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}}i:1;a:10:{s:4:"name";s:14:"Social network";s:6:"legend";s:0:"";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"654-321";s:4:"type";s:5:"shjob";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}}i:2;a:10:{s:4:"name";s:14:"Edit positions";s:6:"legend";s:0:"";s:4:"link";s:0:"";s:4:"icon";s:0:"";s:6:"target";s:0:"";s:3:"uid";s:7:"654-321";s:4:"type";s:4:"ppaj";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"right";a:1:{i:0;s:6:"logged";}}}}}}

*/
 ?>

