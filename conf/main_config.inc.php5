<?php

// PHP setup

//define assert statments
assert_options(ASSERT_ACTIVE, true);
assert_options(ASSERT_BAIL, false);
assert_options(ASSERT_WARNING, false);
assert_options(ASSERT_QUIET_EVAL, false);

//safety for cronjobs
if(empty($_SERVER['DOCUMENT_ROOT']))
  $_SERVER['DOCUMENT_ROOT'] = '/';

define('CONST_DEBUG_ASSERT_LOG_PATH', $_SERVER['DOCUMENT_ROOT'].'/assert.log');

// ==============================================
// PHP constants

//---------------------------------------
//---------------------------------------
//WEBSITE CONFIG

switch(trim($_SERVER['SERVER_NAME']))
{
  case 'bcm.bulbouscell.com':

    define('DB_NAME', 'bcmedia');
    define('DB_SERVER', '127.0.0.1');
    define('DB_USER', 'bccrm');
    define('DB_PASSWORD', 'bcmedia2011');

    define('CONST_WEBSITE', 'bcm');

    define('CONST_CRM_HOST', $_SERVER['SERVER_NAME']);
    define('CONST_CRM_DOMAIN', 'https://'.$_SERVER['SERVER_NAME']);
    define('CONST_CRM_MAIL_SENDER', 'no-reply@bulbouscell.com');
    define('CONST_DEV_SERVER', 0);
    define('CONST_DEV_EMAIL', 'sboudoux@bulbouscell.com');
    define('CONST_DEV_EMAIL_2', 'sboudoux@bulbouscell.com');
    define('CONST_SHOW_MENU_BAR',1);
    define('CONST_VERSION','2.3');
    define('CONST_DISPLAY_VERSION', 1);

    //---------------------------------------
    //Specific environment variables
    define('CONST_PHPMAILER_SMTP_DEBUG', false);
    define('CONST_PHPMAILER_EMAIL', 'crm@bulbouscell.com');
    define('CONST_PHPMAILER_DEFAULT_FROM', 'BCM - Notifier');
    define('CONST_PHPMAILER_ATTACHMENT_SIZE', 10485760);
    define('CONST_PHPMAILER_SMTP_HOST', 'mail.bulbouscell.com');
    define('CONST_PHPMAILER_SMTP_PORT', 465);
    define('CONST_PHPMAILER_SMTP_LOGIN', 'bcm');
    define('CONST_PHPMAILER_SMTP_PASSWORD', 'AB1gOne!');

    define('CONST_AVAILABLE_LANGUAGE', 'en,jp,ph');
    define('CONST_DEFAULT_LANGUAGE', 'en');


    assert_options(ASSERT_CALLBACK, 'mailAssert');
    break;

  case 'beta.bulbouscell.com':

    define('DB_NAME', 'bcmedia_beta');
    define('DB_SERVER', '127.0.0.1');
    define('DB_USER', 'bccrm');
    define('DB_PASSWORD', 'bcmedia2011');

    define('CONST_WEBSITE', 'bcm');

    define('CONST_CRM_HOST', $_SERVER['SERVER_NAME']);
    define('CONST_CRM_DOMAIN', 'https://'.$_SERVER['SERVER_NAME']);
    define('CONST_CRM_MAIL_SENDER', 'no-reply@bulbouscell.com');
    define('CONST_DEV_SERVER', 0);
    define('CONST_DEV_EMAIL', 'sboudoux@bulbouscell.com');
    define('CONST_DEV_EMAIL_2', 'sboudoux@bulbouscell.com');
    define('CONST_SHOW_MENU_BAR',1);
    define('CONST_VERSION','2.3');
    define('CONST_DISPLAY_VERSION', 1);

    //---------------------------------------
    //Specific environment variables
    define('CONST_PHPMAILER_SMTP_DEBUG', false);
    define('CONST_PHPMAILER_EMAIL', 'crm@bulbouscell.com');
    define('CONST_PHPMAILER_DEFAULT_FROM', 'BCM - Notifier');
    define('CONST_PHPMAILER_ATTACHMENT_SIZE', 10485760);
    define('CONST_PHPMAILER_SMTP_HOST', 'mail.bulbouscell.com');
    define('CONST_PHPMAILER_SMTP_PORT', 465);
    define('CONST_PHPMAILER_SMTP_LOGIN', 'bcm');
    define('CONST_PHPMAILER_SMTP_PASSWORD', 'AB1gOne!');

    define('CONST_AVAILABLE_LANGUAGE', 'en');
    define('CONST_DEFAULT_LANGUAGE', 'en');

    assert_options(ASSERT_CALLBACK, 'mailAssert');
    break;


  case 'talentatlas.com':
  case 'www.talentatlas.com':
  case 'japan.talentatlas.com':
  case 'tokyo.talentatlas.com':
  case 'philippines.talentatlas.com':

    define('DB_NAME', 'talent_atlas');
    define('DB_SERVER', '127.0.0.1');
    define('DB_USER', 'bccrm');
    define('DB_PASSWORD', 'bcmedia2011');

    define('CONST_WEBSITE', 'talentAtlas');

    define('CONST_CRM_HOST', $_SERVER['SERVER_NAME'].'');
    define('CONST_CRM_DOMAIN', 'http://'.$_SERVER['SERVER_NAME'].'');
    define('CONST_CRM_MAIL_SENDER', 'no-reply@talentatlas.com');
    define('CONST_DEV_SERVER', 0);
    define('CONST_DEV_EMAIL', 'abaral@bulbouscell.com');
    define('CONST_DEV_EMAIL_2', 'sboudoux@bulbouscell.com');
    define('CONST_SHOW_MENU_BAR', 1);
    define('CONST_VERSION','1.0');
    define('CONST_DISPLAY_VERSION',0);

    //---------------------------------------
    //Specific environment variables
    define('CONST_PHPMAILER_SMTP_DEBUG', false);
    define('CONST_PHPMAILER_EMAIL', 'crm@bulbouscell.com');
    define('CONST_PHPMAILER_DEFAULT_FROM', 'BCM - Notifier');
    define('CONST_PHPMAILER_ATTACHMENT_SIZE', 10485760);
    define('CONST_PHPMAILER_SMTP_HOST', 'mail.bulbouscell.com');
    define('CONST_PHPMAILER_SMTP_PORT', 465);
    define('CONST_PHPMAILER_SMTP_LOGIN', 'bcm');
    define('CONST_PHPMAILER_SMTP_PASSWORD', 'AB1gOne!');

    define('CONST_AVAILABLE_LANGUAGE', 'en,jp');
    define('CONST_DEFAULT_LANGUAGE', 'en');

    assert_options(ASSERT_CALLBACK, 'logAssert');
    break;

  case 'ta.devserv.com':
  case 'stef.talentatlas.com':
  case 'dev.talentatlas.com':
  case 'amit.talentatlas.com':

    define('DB_NAME', 'talent_atlas');
    define('DB_SERVER', '127.0.0.1');
    define('DB_USER', '');
    define('DB_PASSWORD', '');

    define('CONST_CRM_HOST', $_SERVER['SERVER_NAME'].'');
    define('CONST_CRM_DOMAIN', 'https://'.$_SERVER['SERVER_NAME'].'');
    define('CONST_CRM_MAIL_SENDER', 'no-reply@talentatlas.com');
    define('CONST_DEV_SERVER', 1);
    define('CONST_DEV_EMAIL', 'abaral@bulbouscell.com');
    define('CONST_DEV_EMAIL_2', 'sboudoux@bulbouscell.com');
    define('CONST_SHOW_MENU_BAR',0);
    define('CONST_VERSION','1.0');
    define('CONST_DISPLAY_VERSION',0);

    //---------------------------------------
    //Specific environment variables
    define('CONST_PHPMAILER_SMTP_DEBUG', false);
    define('CONST_PHPMAILER_EMAIL', 'crm@bulbouscell.com');
    define('CONST_PHPMAILER_DEFAULT_FROM', 'BCM - Notifier');
    define('CONST_PHPMAILER_ATTACHMENT_SIZE', 10485760);
    define('CONST_PHPMAILER_SMTP_HOST', 'mail.bulbouscell.com');
    define('CONST_PHPMAILER_SMTP_PORT', 465);
    define('CONST_PHPMAILER_SMTP_LOGIN', 'bcm');
    define('CONST_PHPMAILER_SMTP_PASSWORD', 'AB1gOne!');

    define('CONST_AVAILABLE_LANGUAGE', 'en,jp,ph');
    define('CONST_DEFAULT_LANGUAGE', 'en');

    assert_options(ASSERT_CALLBACK, 'displayAssert');




    define('CONST_WEBSITE', 'talentAtlas');
    break;

  case 'bcmedia.devserv.com':
  case 'stephane.bulbouscell.com':

    //define('DB_NAME', 'bcmedia');
    define('DB_NAME', 'bcmedia');
    define('DB_SERVER', '127.0.0.1');
    define('DB_USER', '');
    define('DB_PASSWORD', '');
    define('CONST_WEBSITE', 'bcm');

    define('CONST_CRM_HOST', $_SERVER['SERVER_NAME'].'');
    define('CONST_CRM_DOMAIN', 'https://'.$_SERVER['SERVER_NAME'].'');
    define('CONST_CRM_MAIL_SENDER', 'no-reply@bulbouscell.com');
    define('CONST_DEV_SERVER', 1);
    define('CONST_DEV_EMAIL', 'sboudoux@bulbouscell.com');
    define('CONST_DEV_EMAIL_2', 'abaral@bulbouscell.com');
    define('CONST_SHOW_MENU_BAR',1);
    define('CONST_VERSION','2.3');
    define('CONST_DISPLAY_VERSION',1);

    //---------------------------------------
    //Specific environment variables
    define('CONST_PHPMAILER_SMTP_DEBUG', false);
    define('CONST_PHPMAILER_EMAIL', 'crm@bulbouscell.com');
    define('CONST_PHPMAILER_DEFAULT_FROM', 'BCM - Notifier');
    define('CONST_PHPMAILER_ATTACHMENT_SIZE', 10485760);
    define('CONST_PHPMAILER_SMTP_HOST', 'mail.bulbouscell.com');
    define('CONST_PHPMAILER_SMTP_PORT', 465);
    define('CONST_PHPMAILER_SMTP_LOGIN', 'bcm');
    define('CONST_PHPMAILER_SMTP_PASSWORD', 'AB1gOne!');

    define('CONST_AVAILABLE_LANGUAGE', 'en');
    define('CONST_DEFAULT_LANGUAGE', 'en');

    assert_options(ASSERT_CALLBACK, 'displayAssert');
    //assert_options(ASSERT_CALLBACK, 'mailAssert');
    break;

  case 'job.slate.co.jp':
  case 'jobs.slate.co.jp':
  case 'job.dev.com':

    define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');

    define('DB_NAME', 'jobboard');
    define('DB_SERVER', '127.0.0.1');
    //define('DB_USER', '');
    //define('DB_PASSWORD', ''); //local
    define('DB_USER', 'jobboard');
    define('DB_PASSWORD', 'KCd7C56XJ8Nud7uF');  //online

    define('CONST_WEBSITE', 'jobboard');

    define('CONST_CRM_HOST', $_SERVER['SERVER_NAME'].'');
    define('CONST_CRM_DOMAIN', 'http://'.$_SERVER['SERVER_NAME'].'');
    define('CONST_CRM_MAIL_SENDER', 'no-reply@slate.co.jp');
    define('CONST_DEV_SERVER', 1);
    define('CONST_DEV_EMAIL', 'munir@slate-ghc.com');
    define('CONST_DEV_EMAIL_2', 'munir@slate-ghc.com');
    define('CONST_SHOW_MENU_BAR', 1);
    define('CONST_VERSION', '1');
    define('CONST_DISPLAY_VERSION', 0);

    //---------------------------------------
    //Specific environment variables
    define('CONST_PHPMAILER_SMTP_DEBUG', false);
    define('CONST_PHPMAILER_EMAIL', 'no_reply@slate.co.jp');
    define('CONST_PHPMAILER_DEFAULT_FROM', 'Slate Consulting');
    define('CONST_PHPMAILER_ATTACHMENT_SIZE', 10485760);
    //define('CONST_PHPMAILER_SMTP_HOST', 'zmail.aineo.net');
    define('CONST_PHPMAILER_SMTP_HOST', 'slatemail.slate.co.jp');
    define('CONST_PHPMAILER_SMTP_PORT', 465);
    define('CONST_PHPMAILER_SMTP_LOGIN', 'dba_request@slate.co.jp');
    define('CONST_PHPMAILER_SMTP_PASSWORD', 'RT!7000ics');

    define('CONST_AVAILABLE_LANGUAGE', 'en,jp');
    define('CONST_DEFAULT_LANGUAGE', 'en');

    assert_options(ASSERT_CALLBACK, 'logAssert');
    break;

  default:
    exit('error in website parameters');
}


//---------------------------------------
//url constants
define('CONST_URL_UID', 'uid');
define('CONST_URL_ACTION', 'ppa');
define('CONST_URL_ACTION_RETURN', 'ppar');
define('CONST_URL_TYPE', 'ppt');
define('CONST_URL_PK', 'ppk');
define('CONST_URL_MODE', 'pg');
define('CONST_URL_EMBED', 'embed');

define('CONST_URL_PARAM_PAGE_AJAX', 'ajx');
define('CONST_URL_PARAM_PAGE_NORMAL', 'pn');
define('CONST_URL_PARAM_PAGE_EMBED', 'emb');
define('CONST_URL_PARAM_PAGE_CRON', 'cron');

define('CLIENT_LOGIN_PAGE', 'clp');
define('CLIENT_LOGIN', 'cla');
define('CLIENT_CHANGE_INNER', 'cci');

define('CONST_TALENT_HOME_PAGE','ppah');
define('CONST_TA_TYPE_LIST_JOB','ppaj');
define('CONST_LIST_COMPANY','cmpl');
define('CONST_ACTION_LIST', 'ppal');
define('CONST_ACTION_VIEW', 'ppav');
define('CONST_ACTION_EMAIL','ppaem');
define('CONST_ACTION_VIEW_DETAILED', 'ppavd');
define('CONST_ACTION_EDIT', 'ppae');
define('CONST_ACTION_ADD', 'ppaa');
define('CONST_ACTION_SAVEADD', 'ppasa');
define('CONST_ACTION_SAVEEDIT', 'ppase');
define('CONST_ACTION_VALIDATE', 'ppava');
define('CONST_ACTION_DELETE', 'ppad');
define('CONST_ACTION_RESET', 'ppares');
define('CONST_ACTION_SEND', 'ppasen');
define('CONST_ACTION_DONE', 'ppado');
define('CONST_ACTION_DOWNLOAD', 'ppadown');
define('CONST_ACTION_SEARCH', 'ppasea');
define('CONST_ACTION_MANAGE', 'ppam');
define('CONST_ACTION_TRANSFER','ppat');
define('CONST_ACTION_SAVETRANSFER','ppast');
define('CONST_ACTION_SAVEMANAGE','ppasm');
define('CONST_ACTION_SAVECOMPANY_RELATION','ppacpr');
define('CONST_ACTION_SAVE_CONFIG','ppasc');
define('CONST_ACTION_LOGOUT','ppalgt');
define('CONST_ACTION_APPLY','ppaly');

define('CONST_PAGE_DEVICE_TYPE_PHONE', 'page_phone');
define('CONST_PAGE_DEVICE_TYPE_PC', 'page_pc');
define('CONST_PAGE_DEVICE_TYPE_TABLET', 'page_tablet');
define('CONST_PAGE_NO_LOGGEDIN_CSS', 'ffpcss');

define('CONST_ACTION_ITEMTYPE', 'ppaty');
define('CONST_ACTION_ITEMID', 'ppaid');

define('CONST_PHP_VARTYPE_INT','int');
define('CONST_PHP_VARTYPE_FLOAT','float');
define('CONST_PHP_VARTYPE_BOOL','bool');
define('CONST_PHP_VARTYPE_ARRAY','array');
define('CONST_PHP_VARTYPE_SERIALIZED','serial');
define('CONST_PHP_VARTYPE_JSON','json');
define('CONST_PHP_VARTYPE_STR','str');

define('CONST_TAB_CP_DETAIL','cp_tab_detail');
define('CONST_TAB_CP_EVENT','cp_tab_event');
define('CONST_TAB_CP_DOCUMENT','cp_tab_document');
define('CONST_TAB_CP_EMPLOYEES','cp_tab_employee');

define('CONST_TAB_CT_DETAIL','ct_tab_detail');
define('CONST_TAB_CT_COWORKERS','ct_tab_coworkers');
define('CONST_TAB_CT_DOCUMENT','ct_tab_document');
define('CONST_TAB_CT_EVENT','ct_tab_event');
define('CONST_TAB_CT_PROFILE','ct_tab_profile');

define('CONST_PATH_JS_DATEPICKER', '/common/js/jquery-ui/datepicker-redmond/js/jquery-ui-1.8.16.custom.min.js');
define('CONST_PATH_JS_TIMEPICKER', '/common/js/jquery-ui/timepicker-redmond/js/jquery-ui-1.8.16.custom.min.js');
define('CONST_PATH_JS_DRAGDROP', '/common/js/jquery-ui/dragdrop-redmond/jquery-ui.min.js');
define('CONST_PATH_JS_MULTIDRAG', '/common/js/jquery-ui/dargdrop-redmond/ui.multidraggable.js');
define('CONST_PATH_JS_SLIDER', '/common/js/jquery-ui/slider-redmond/jquery-ui-slider.min.js');

define('CONST_PATH_CSS_DATEPICKER', '/common/js/jquery-ui/datepicker-redmond/css/redmond/jquery-ui-1.8.16.custom.css');
define('CONST_PATH_CSS_SLIDER', '/common/js/jquery-ui/slider-redmond/css/redmond/jquery-ui-1.8.16.slider.css');
define('CONST_PATH_PICTURE_COMMON', '/common/pictures/');
define('CONST_PATH_UPLOAD_DIR', '/common/upload/');

define('CONST_FORM_SELECTOR_URL_COUNTRY', 'fsuco');
define('CONST_FORM_SELECTOR_URL_CITY', 'fsuci');

define('CONST_PICTURE_EXPAND', CONST_PATH_PICTURE_COMMON.'expanded.png');
define('CONST_PICTURE_NORMAL', CONST_PATH_PICTURE_COMMON.'grey.png');
define('CONST_PICTURE_ADD', CONST_PATH_PICTURE_COMMON.'add_16.png');
define('CONST_PICTURE_DELETE', CONST_PATH_PICTURE_COMMON.'delete_16.png');
define('CONST_PICTURE_REACTIVATE', CONST_PATH_PICTURE_COMMON.'activate_16.png');
define('CONST_PICTURE_EDIT', CONST_PATH_PICTURE_COMMON.'edit_16.png');
define('CONST_PICTURE_VIEW', CONST_PATH_PICTURE_COMMON.'view_16.png');
define('CONST_PICTURE_LOADING', CONST_PATH_PICTURE_COMMON.'loading.gif');
define('CONST_PICTURE_DOWNLOAD', CONST_PATH_PICTURE_COMMON.'download_24.png');
define('CONST_PICTURE_SAVE', CONST_PATH_PICTURE_COMMON.'save_16.png');
define('CONST_PICTURE_CHECK_OK', CONST_PATH_PICTURE_COMMON.'check_ok_16.png');
define('CONST_PICTURE_CHECK_NOT_OK', CONST_PATH_PICTURE_COMMON.'check_nok_16.png');
define('CONST_PICTURE_CHECK_INACTIVE', CONST_PATH_PICTURE_COMMON.'check_inactive_16.png');
define('CONST_PICTURE_SORT', CONST_PATH_PICTURE_COMMON.'sort_16.png');

define('CONST_PICTURE_MENU_ADD', CONST_PATH_PICTURE_COMMON.'menu/add.png');
define('CONST_PICTURE_MENU_EDIT', CONST_PATH_PICTURE_COMMON.'menu/edit.png');
define('CONST_PICTURE_MENU_DELETE', CONST_PATH_PICTURE_COMMON.'menu/delete.png');
define('CONST_PICTURE_MENU_VIEW', CONST_PATH_PICTURE_COMMON.'menu/view.png');
define('CONST_PICTURE_MENU_LIST', CONST_PATH_PICTURE_COMMON.'menu/list.png');

define('CONST_PICTURE_MENU_SEARCH', CONST_PATH_PICTURE_COMMON.'menu/search.png');
define('CONST_PICTURE_MENU_SEPARATOR', CONST_PATH_PICTURE_COMMON.'menu/separator.png');
define('CONST_PICTURE_MENU_MULTIPLE', CONST_PATH_PICTURE_COMMON.'menu/extend_arrow.png');
define('CONST_PICTURE_MENU_FAVORITE', CONST_PATH_PICTURE_COMMON.'menu/favorite.png');

//---------------------------------------
//TYPE of element of evey component

define('CONST_AB_TYPE_COMPANY', 'cp');
define('CONST_AB_TYPE_COMPANY_RELATION', 'cpr');
define('CONST_AB_TYPE_CONTACT', 'ct');
define('CONST_AB_TYPE_EVENT', 'evt');
define('CONST_EVENT_TYPE_REMINDER', 'evtrem');
define('CONST_AB_TYPE_DOCUMENT', 'doc');
define('CONST_CF_TYPE_CUSTOMFIELD','csm');

define('CONST_TYPE_SETTINGS', 'stg');
define('CONST_TYPE_SETTING_USER','stgusr');
define('CONST_TYPE_SETTING_USRIGHT','stgusrt');
define('CONST_TYPE_SETTING_RIGHTUSR','stgusrht');
define('CONST_TYPE_SETTING_MENU','stgmnu');
define('CONST_TYPE_SETTING_FOOTER','stgft');
define('CONST_TYPE_SETTING_BLACKLIST','stgblk');
define('CONST_TYPE_SETTING_CRON','stgcrn');

define('CONST_LOGIN_TYPE_USER', 'usr');
define('CONST_LOGIN_TYPE_EXTERNAL_USER', 'exusr');
define('CONST_LOGIN_TYPE_PASSWORD', 'pswd');

define('CONST_PROJECT_TYPE_PROJECT', 'prj');
define('CONST_PROJECT_TYPE_TASK', 'task');
define('CONST_PROJECT_TYPE_ACTOR', 'prjacr');
define('CONST_PROJECT_TYPE_ATTACHMENT', 'attch');
define('CONST_PROJECT_ACTION_UPDATE', 'ppaupd');
define('CONST_PROJECT_TASK_SORT_PARAM', 'tsksort');

//EVENT component, specific parameters
define('CONST_EVENT_ITEM_UID', 'cp_uid');
define('CONST_EVENT_ITEM_ACTION', 'cp_act');
define('CONST_EVENT_ITEM_TYPE', 'cp_ty');
define('CONST_EVENT_ITEM_PK', 'cp_pk');

//Login component, specific parameters
define('CONST_LOGIN_ITEM_UID', 'cp_uid');
define('CONST_LOGIN_ITEM_ACTION', 'cp_act');
define('CONST_LOGIN_ITEM_TYPE', 'cp_ty');
define('CONST_LOGIN_ITEM_PK', 'cp_pk');

//Talentatlas Component, specific parameters
define('CONST_TA_TYPE_JOB', 'job');
define('CONST_TA_TYPE_JOB_RSS', 'jrss');
define('CONST_TA_TYPE_SHARE_JOB', 'shjob');


//Custom field Component,specific parameters
define('CONST_ACTION_UPDATE','ppau');


define('CONST_PORTAL_STAT','portstat');
define('CONST_PORTAL_CALENDAR','portcal');

define('CONST_ZCAL_EVENT','zcalevt');


//Display component ,generating tabs

define('CONST_TAB_STATUS_ACTIVE',1);
define('CONST_TAB_STATUS_INACTIVE',0);
define('CONST_TAB_STATUS_SELECTED',2);
define('CONST_TAB_STATUS_IMPORTANT',3);

define('CONST_FORM_TYPE_CITY', 'fcity');
define('CONST_FORM_TYPE_COUNTRY', 'fcountry');
define('CONST_SS_TYPE_DOCUMENT', 'shdoc');
define('CONST_SEARCH_TYPE_SEARCH', 'search');
define('CONST_EVENT_TYPE_EVENT', 'event');
define('CONST_WEBMAIL', 'webmail');
define('BCMAIL_HOST','mail.bulbouscell.com');
define('BCMAIL_PORT','143');
define('DEFAULT_WEBMAIL_ADDRESS','crm@bulbouscell.com');

?>