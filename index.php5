<?php
session_start();
header('Cache-Control: no-cache');
echo "TEST TEST";
require_once('./conf/main_config.inc.php5');
require_once('./conf/custom_config/'.CONST_WEBSITE.'/config.inc.php5');
require_once('./common/lib/global_func.inc.php5');
require_once('./component/dependency.inc.php5');
require_once './common/lib/ChromePhp.php';

CDependency::initialize();


$sUid = getValue(CONST_URL_UID);
$sAction = getValue(CONST_URL_ACTION);
$sType = getValue(CONST_URL_TYPE);
$nPk = (int)getValue(CONST_URL_PK, 0);
$sPg = getValue(CONST_URL_MODE, CONST_URL_PARAM_PAGE_NORMAL);

$oPage = CDependency::getComponentByName('page');
echo $oPage->getPage($sUid, $sAction, $sType, $nPk, $sPg);

?>