<?php

require_once('component/display/display.class.php5');

class CDisplayEx extends CDisplay
{

  public function __construct()
  {
      return true;
  }

  /*
   * Librairie of function to "draw" the page element
   * All components must use this librairy to display elements of the page.
   * Use ogf HTML is forbiden except here.
   */

  //****************************************************************************
  //****************************************************************************
  //Low level display functions
  //****************************************************************************
  //****************************************************************************
  public function render($filename, $data = array())
  {
    $file = __DIR__.'/resources/html/'.$filename.'.php';

    try
    {
      if( !is_readable($file) )
      {
          throw new Exception("View $file not found!", 1);
      }

      ob_start() && extract($data, EXTR_SKIP);
      include $file;
      $content = ob_get_clean();
      ob_flush();

      return $content;
    }
    catch (Exception $e)
    {
        return $e->getMessage();
    }
  }

  public function getLink($psLabel, $psUrl='', $pasOptions = array())
  {
   /*
    * TODO: finish the popup and manage the mouseover
    */

    $oPage = CDependency::getComponentByName('page');
    if(empty($psUrl))
      $psUrl = 'javascript:;';

    if($oPage->isAjaxUrl($psUrl))
    {
      if(isset($pasOptions['ajaxLoadingScreen']))
        $sLoadingScreen = $pasOptions['ajaxLoadingScreen'];
      else
        $sLoadingScreen = 'body';

      if(isset($pasOptions['ajaxFormToSerialize']))
        $sForm = $pasOptions['ajaxFormToSerialize'];
      else
        $sForm = '';

      if(isset($pasOptions['ajaxTarget']))
        $sRefresh = $pasOptions['ajaxTarget'];
      else
        $sRefresh = '';

      if(isset($pasOptions['ajaxReload']))
        $bRelaod = true;
      else
        $bRelaod = false;

      if(isset($pasOptions['ajaxCallback']))
        $sCallback = $pasOptions['ajaxCallback'];
      else
        $sCallback = '';

      $sAjaxJs = $this->getAjaxJs($psUrl, $sLoadingScreen, $sForm, $sRefresh, $bRelaod, false, $sCallback);
      $psUrl = 'javascript:;';

      if(isset($pasOptions['onclick']))
        $pasOptions['onclick'] .= ' '.$sAjaxJs;
      else
        $pasOptions['onclick'] = $sAjaxJs;
    }

    $sHTML = '<a href="'.$psUrl.'"';

    if(!empty($pasOptions))
    {
      foreach($pasOptions as $sOption => $sValue)
      {
        $sHTML.= ' '.$sOption.'="'.$sValue.'" ';
      }
    }

    $sHTML.= '>'.$psLabel.'</a>';

    return $sHTML;
  }

  public function getAjaxJs($psUrl, $psLoadingScreen = '', $psFormToSerialize = '', $psZoneToRefresh = '', $psReloadPage = '',  $pbSynch = false, $psCallback = '')
  {
    $sHTML = 'AjaxRequest(\''.$psUrl.'\', \''.$psLoadingScreen.'\', \''.$psFormToSerialize.'\'';
    $sHTML.= ', \''.$psZoneToRefresh.'\', \''.$psReloadPage.'\', \''.$pbSynch.'\', \''.addslashes($psCallback).'\'); ';
    return $sHTML;
  }


  public function getAjaxPopupJS($psUrl, $psLoadingScreen, $pbSynch = false, $psHeight='',$psWidth='',$psFooter='')
  {
    if($pbSynch)
      $pbSynch = 'true';
    else
      $pbSynch = 'false';

    return 'AjaxPopup(\''.$psUrl.'\', \''.$psLoadingScreen.'\', '.$pbSynch.', '.$psHeight.', '.$psWidth.', '.$psFooter.')';
  }

  //--------------------------------------
  //--------------------------------------
  // make div functions

  public function getBlocStart($psID = '', $pasOptions = array())
  {
    $sHTML = '<div id="'.$psID.'" ';
    if(!empty($pasOptions))
    {
      foreach($pasOptions as $sOption=> $sValue)
      {
        $sHTML.= ' '.$sOption.'="'.$sValue.'" ';
      }
    }
    $sHTML.= '>';

    return $sHTML;
  }

  public function getBlocEnd()
  {
    return '</div>';
  }

  public function getFloatHack()
  {
    return '<div class="floatHack"></div>';
  }

  public function getHtmlContainer($psContent, $psID = '', $pasOptions = array())
  {
    if(isset ($pasOptions['class']))
      $pasOptions['class'].= ' htmlContainer ';
    else
      $pasOptions['class'] = ' htmlContainer ';

    return $this->getBlocStart($psID, $pasOptions) .$psContent. $this->getBlocEnd($psID = '', $pasOptions);
  }


  //--------------------------------------
  //--------------------------------------
  // make div functions

  public function getSpanStart($psID = '', $asOptions = array())
  {
    $sHTML = '<span id="'.$psID.'" ';

    if(!empty($asOptions))
    {
      foreach($asOptions as $sOption=> $sValue)
      {
        $sHTML.= ' '.$sOption.'="'.$sValue.'" ';
      }
    }

    $sHTML.= '>';

    return $sHTML;
  }

  public function getSpanEnd()
  {
    return '</span>';
  }

  public function getFittingBlocStart($psID = '', $asOptions = array())
  {
    if(isset($asOptions['class']))
      $asOptions['class'].= ' fittingBloc';

    return $this->getSpanStart($psID = '', $asOptions);
  }
  public function getFittingBlocEnd()
  {
    return '</span>';
  }

  //--------------------------------------
  //--------------------------------------
  // Images related functions

  public function getPicture($psPath, $psTitle = '', $psUrl = '',  $asOptions = array())
  {
   if(preg_match('|^(http)|', $psPath))
      $psPath = $psPath;
   else if(!preg_match('|^('.CONST_CRM_DOMAIN.')|', $psPath))
      $psPath = CONST_CRM_DOMAIN.''.$psPath;

    $sHTML = '<img src="'.$psPath.'" title="'.$psTitle.'" ';

    if(isset($asOptions['onclick']))
    {
      $asLinkOption = array('onclick' => $asOptions['onclick']);
      unset($asOptions['onclick']);
    }
    else
      $asLinkOption = array();

    if(!empty($asOptions))
    {
      foreach($asOptions as $sOption => $sValue)
      {
        $sHTML.= ' '.$sOption.'="'.$sValue.'" ';
      }
    }

    $sHTML.= ' />';

    if(empty($psUrl))
      return $sHTML;
    else
      return $this->getLink($sHTML, $psUrl, $asLinkOption);
  }

  //--------------------------------------
  //--------------------------------------

  public function getListStart($psID = '', $pasOptions = array())
  {
    $sHTML = '<ul id="'.$psID.'" ';

    foreach($pasOptions as $sOption=> $sValue)
    {
      $sHTML.= ' '.$sOption.'="'.$sValue.'" ';
    }

    return $sHTML.'>';
  }

  public function getListEnd()
  {
    return '</ul>';
  }

  public function getListItemStart($psID = '', $pasOptions = array())
  {
    $sHTML = '<li id="'.$psID.'" ';

    foreach($pasOptions as $sOption=> $sValue)
    {
      $sHTML.= ' '.$sOption.'="'.$sValue.'" ';
    }

    return $sHTML.'>';
  }

  public function getListItemEnd()
  {
    return '</li>';
  }

  //--------------------------------------
  //--------------------------------------
  // Text functions

  public function getText($psText, $pasOptions = array(), $pnShortenTo = 0)
  {
    if(!assert('(is_string($psText) || is_float($psText) || is_integer($psText)) && is_array($pasOptions) && is_integer($pnShortenTo)'))
      return '';

    // maybe replace nl by <br /> ...
    $sHTML = '';
    $nLength = strlen($psText);

    if(isset($pasOptions['extra_open_content']))
    {
      $sExtraContent = $pasOptions['extra_open_content'];
      unset($pasOptions['extra_open_content']);
    }

    if(isset($pasOptions['open_content_nl2br']))
    {
      $bNl2br = (bool)$pasOptions['open_content_nl2br'];
    }
    else
      $bNl2br = false;

    if(empty($pnShortenTo) || $nLength < $pnShortenTo)
    {

      if(!empty($pasOptions))
      {
        $sHTML.= '<span ';
        foreach($pasOptions as $sOption=> $sValue)
        {
          $sHTML.= ' '.$sOption.'="'.$sValue.'" ';
        }

        return $sHTML.='>'.$psText.'</span>';
      }
      else
          return $psText;
    }
    else
    {
      $sId = uniqid('CDisplayEx_');
      $sFullContentId = $sId.'_full';
      $sExtraContent = '';

      $sFoldedPic = $this->getResourcePath().'/pictures/details_folded.png';
      $sPicture = $this->getPicture($sFoldedPic);

      $sDisplayedText = $this->getSpanStart($sId, $pasOptions);
      $sDisplayedText.= $this->getLink($sPicture.' '.substr(strip_tags($psText), 0, $pnShortenTo).'...', 'javascript:;', array('class' => 'display_shortened_text',
          'onclick' => '$(\'#'.$sId.'\').fadeToggle(\'fast\', function(){ $(\'#'.$sFullContentId.'\').fadeToggle(\'fast\'); }); '));
      $sDisplayedText.= $this->getSpanEnd();


      $asOptions = $pasOptions;
      if(isset($asOptions['style']))
        $asOptions['style'].= ' display:none; ';
      else
        $asOptions['style'] = ' display:none; ';

      $sOpenedPic = $this->getResourcePath().'/pictures/details_opened.png';
      $sPicture = $this->getPicture($sOpenedPic);

      if($bNl2br)
        $psText = nl2br($psText);

      $sDisplayedText.= $this->getSpanStart($sFullContentId, $asOptions);
      $sDisplayedText.= $this->getLink($sPicture.' '.$psText, 'javascript:;', array('class' => 'display_shortened_text',
          'onclick' => '$(\'#'.$sId.'\').fadeToggle(\'fast\', function(){ $(\'#'.$sFullContentId.'\').fadeToggle(\'fast\'); }); '));

      $sDisplayedText.= $sExtraContent;
      $sDisplayedText.= $this->getSpanEnd();

      return $sDisplayedText;
    }
  }

  public function getSpacedText($pnSliceSize, $psText, $pasOptions = array(), $pnShortenTo = 0)
  {
    if(!assert('is_integer($pnSliceSize) && !empty($psText)'))
      return '';

    $nSize = strlen($psText);
    if($nSize < $pnSliceSize)
      return $this->getText($psText, $pasOptions, $pnShortenTo);

    $sExtractedText = substr($psText, 0, $pnSliceSize);

    if(strpos($sExtractedText, ' ') === false)
      $sSlicedText = $sExtractedText.' '.substr($psText, $pnSliceSize+1, -1);
    else
      $sSlicedText = $psText;

    return $this->getText($sSlicedText, $pasOptions, $pnShortenTo);
  }

  public function getTitle($psText, $psTitleType = 'h3', $pbFullLine = false, $pasOptions = array())
  {
    $sClass = 'title ';

    if(isset($pasOptions['class']))
      $sClass.= $pasOptions['class'];

    if(isset($pasOptions['onclick']))
      $sClass.= ' titleToggle ';

    $sHTML = '<div class="'.$sClass.'" ';

    if(!isset($pasOptions['float']))
      $sFloat = 'left';
    else
      $sFloat = $pasOptions['float'];

    foreach($pasOptions as $sOption=> $sValue)
    {
      $sHTML.= ' '.$sOption.'="'.$sValue.'" ';
    }

    if(isset($pasOptions['isHtml']) || !empty($pasOptions['isHtml']))
     $psText = html_entity_decode($psText);

    if($pbFullLine)
      return $sHTML.'><div class = "'.$psTitleType.'" style="float:'.$sFloat.'; width:98%;">'.$psText.'</div><div class="floatHack"></div></div>';

    return $sHTML.'><div class = "'.$psTitleType.'" style="float:'.$sFloat.';" >'.$psText.'</div><div class="floatHack"></div></div>';
  }

 public function getTabs($psId = '', $pasTabs = array(), $pbDisplayTabAll = false)
 {
    if(!assert('is_array($pasTabs) && !empty($pasTabs)'))
      return '';

    $sAllIds = '';
    $asContentArray = array();
    $asTabArray = array();

    foreach($pasTabs as $sKey => $asTabs)
    {
      if(isset($asTabs['contentOptions']))
        $asContentOptions = $asTabs['contentOptions'];
      else
        $asContentOptions = '';

      if(isset($asTabs['tabOptions']) && isset($asTabs['tabOptions']['tabId']))
        $sTabId = $asTabs['tabOptions']['tabId'];
      else
        $sTabId = uniqid();

      if(isset($asTabs['contentOptions']) && isset($asTabs['contentOptions']['contentId']))
        $sContentId = $asTabs['contentOptions']['contentId'];
      else
        $sContentId = uniqid();

      //for auto generated JS
      $asTabs['tabOptions']['tab_content_id'] = $sContentId;
      if(empty($sAllIds))
        $sAllIds.= $sContentId;
      else
        $sAllIds.= ',#'.$sContentId;

      if(isset($asTabs['content']) && !empty($asTabs['content']))
        $sContent = $asTabs['content'];
      else
        $sContent = '';

      if(isset($asTabs['tabtitle']) && !empty($asTabs['tabtitle']))
        $sTitle = $asTabs['tabtitle'];
      else
        $sTitle = '';

      if(!isset($asTabs['tabOptions']['class']))
        $asTabs['tabOptions']['class'] = '';

      if(isset($asTabs['tabstatus']))
      {
        switch($asTabs['tabstatus'])
        {
          case CONST_TAB_STATUS_SELECTED: $asTabs['tabOptions']['class'].= ' tab_selected'; break;
          case CONST_TAB_STATUS_INACTIVE: $asTabs['tabOptions']['class'].= ' display_tab_inactive'; break;
        }
      }

      if(!isset($asContentOptions['class']))
        $asContentOptions['class'] = 'display_tab';
      else
        $asContentOptions['class'].= ' display_tab';

      $asTabArray[] = $this->getListItemStart($sTabId, $asTabs['tabOptions']).$this->getBlocStart('',array('class'=>'tab_left')).$sTitle.$this->getBlocEnd().$this->getBlocStart('',array('class'=>'tab_right')).'&nbsp;'.$this->getBlocEnd().$this->getListItemEnd();
      $asContentArray[] = $this->getBlocStart($sContentId, $asContentOptions).$sContent.$this->getBlocEnd();
    }

    if($pbDisplayTabAll)
    {
      $asTabs = array('tabOptions' => array('tab_content_id' => $sAllIds));
      $sTitle = 'All';
      $asTabArray[] = $this->getListItemStart($sTabId, $asTabs['tabOptions'], '').$this->getBlocStart('',array('class'=>'tab_left')).$sTitle.$this->getBlocEnd().$this->getBlocStart('',array('class'=>'tab_right')).'&nbsp;'.$this->getBlocEnd().$this->getListItemEnd();
    }

    if(!empty($asTabArray))
    {
      if(empty($psId))
        $psId = uniqid('tab_');

      $sJavascript = "$(document).ready(function(){ ";
      $sJavascript.= "   $('#".$psId." ul li').click(function(){ ";
      $sJavascript.= "     $('#".$psId." ul li:not(this)').removeClass('tab_selected'); $(this).addClass('tab_selected'); ";
      $sJavascript.= "     var sIdToDisplay = '#'+$(this).attr('tab_content_id'); ";
      $sJavascript.= "     if(sIdToDisplay.split(',').length > 1){ $('.display_tab:not(:visible)').fadeIn(); } else{ $('.display_tab:not('+sIdToDisplay+')').fadeOut('fast', function(){ $(sIdToDisplay).fadeIn(); }); }";
      $sJavascript.= "   }); ";
      $sJavascript.= "}); ";
      $oPage = CDependency::getComponentByName('page');
      $oPage->addCustomJs($sJavascript);

      $sHTML = $this->getBlocStart($psId, array('class' => 'tabs_list'));
      $sHTML.= $this->getListStart();
      $sHTML.= implode(' ',$asTabArray);
      $sHTML.= $this->getListEnd();
      $sHTML.= $this->getBlocStart('', array('class' => 'floatHack'));
      $sHTML.= $this->getBlocEnd();
      $sHTML.= $this->getBlocEnd();
     }

    if(!empty($asContentArray))
    {
      $sHTML.= $this->getBlocStart('', array('class' => 'tab_content_container'));
      $sHTML.= implode(' ',$asContentArray);
      $sHTML.= $this->getBlocEnd();
    }

    return $sHTML;
  }


  public function getTitleLine($psText, $psPicture = '', $pasOptions = array())
  {
    if(empty($psPicture))
     return $this->getTitle ($psText, 'h1', true, $pasOptions);

    if(!isset($pasOptions['isHtml']) || !$pasOptions['isHtml'])
      $psText = htmlentities($psText);

    if(!isset($pasOptions['class']) || empty($pasOptions['class']))
      $pasOptions['class'] = 'h1';

      $sHTML = '<div class="titleLine shadow">';
      $sHTML.= '<div class="titleLinePicture">'.$this->getPicture($psPicture).'</div>';
      $sHTML.= '<div class="titleLineText">';

        $sHTML.= '<div ';
        foreach($pasOptions as $sOption=> $sValue)
        {
          $sHTML.= ' '.$sOption.'="'.$sValue.'" ';
        }
        $sHTML.= '>'.$psText.'</div>';

      $sHTML.= '</div>';
      $sHTML.= '<div class="floatHack"></div>';
    $sHTML.= '</div>';

    return $sHTML;
  }


  public function getCarriageReturn($pnNumber = 1)
  {
    $sHTML = '';

    for($nCount = 0; $nCount < $pnNumber; $nCount++)
      $sHTML.= '<br />';

    return $sHTML;
  }

  public function getSpace($pnNumber = 1)
  {
    $sHTML = '';

    for($nCount = 0; $nCount < $pnNumber; $nCount++)
      $sHTML.= '&nbsp;';

    return $sHTML;
  }

  public function getRedirection($psUrl, $psTimer = 0, $psMessage = '')
  {
    if(empty($psTimer))
    {
      @header('location:'.$psUrl);
      return '<script> document.location.href= \''.$psUrl.'\'; </script><a href="'.$psUrl.'">Click here to be redirected</a>';
    }

    $oHTML = CDependency::getComponentByName('display');
    $sHTML = '';

    if(!empty($psMessage))
      $sHTML.= $oHTML->getBlocMessage($psMessage);

    $sHTML.= $oHTML->getCarriageReturn(5);
    $sHTML.= '<script> setTimeout("document.location.href = \''.$psUrl.'\';", '.$psTimer.'); </script>';
    $sHTML.= '<span class="system_redirect_message"><a href="'.$psUrl.'">Click here</a> if nothing happens in the next 15 seconds.</span>';

    $sHTML.= $oHTML->getCarriageReturn(2);
    $sHTML.= $oHTML->getBlocStart('', array('style' => 'text-align: center;'));
    $sHTML.= $oHTML->getPicture(CONST_PICTURE_LOADING);
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  //****************************************************************************
  //****************************************************************************
  //High level display functions
  //****************************************************************************
  //****************************************************************************

  public function getHeader($pbIsLogged = false, $pasJsFile = array(), $pasCustomJs = array(), $pasCssFile = array(), $pasMeta = array(), $pasPageParam = array())
  {
    $oPage = CDependency::getComponentByName('page');

    if($pasMeta['title'])
      $sCustomPageTitle = $pasMeta['title'];
    else
      $sCustomPageTitle = '';

    if($pasMeta['meta_desc'])
      $sCustomDescription = $pasMeta['meta_desc'];
    else
      $sCustomDescription = '';

    if($pasMeta['meta_tags'])
      $sCustomKeywords = $pasMeta['meta_tags'];
    else
      $sCustomKeywords = '';

    $sHTML = '<html>';
    $sHTML.= '<head>';
    $sHTML.= '<title>'.$sCustomPageTitle.'</title>';
    $sHTML.= '<meta name="description" content="'.$sCustomDescription.'"/>';
    $sHTML.= '<meta name="keywords" content="'.$sCustomKeywords.'"/>';
    $sHTML.= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
    $sHTML.= '<meta http-equiv="Cache-control" content="private">';

    $sHTML.= '<link rel="shortcut icon" href="'.CONST_HEADER_FAVICON.'" type="image/vnd.microsoft.icon" />';
    $sHTML.= '<link rel="shortcut icon" href="'.CONST_HEADER_FAVICON.'" type="image/x-icon" />';
    $sHTML.= '<link rel="icon" href="'.CONST_HEADER_FAVICON.'" type="image/vnd.microsoft.icon" />';
    $sHTML.= '<link rel="icon" href="'.CONST_HEADER_FAVICON.'" type="image/x-icon" />';

    $sHTML.= '<link rel="stylesheet" href="/common/style/style.css?n='.time().'" type="text/css" media="screen" />';
    $sHTML.= '<link rel="stylesheet" href="common/lib/font-awesome/css/font-awesome.min.css">';
    $sHTML.= '<link rel="stylesheet" href="common/lib/bootstrap/css/bootstrap.min.css">';

    if($pbIsLogged && !getValue(CONST_PAGE_NO_LOGGEDIN_CSS))
      $sHTML.= '<link rel="stylesheet" href="/common/style/private.css?n='.time().'" type="text/css" media="screen" />';

    if(CONST_WEBSITE_MAIN_CSS)
    {
      $sHTML.= '<link rel="stylesheet" href="'.CONST_WEBSITE_MAIN_CSS.'?n='.time().'" type="text/css" media="screen" />';

      if($pbIsLogged)
        $sHTML.= '<link rel="stylesheet" href="/common/style/'.CONST_WEBSITE.'_private.css?n='.time().'" type="text/css" media="screen" />';
    }

    $asCssFile = array();
    foreach($pasCssFile as $sFileName)
    {
      $sHTML.= '<link rel="stylesheet" href="'.$sFileName.'" />';

      $asFileDate = parse_url($sFileName);
      $asCssFile[] = $asFileDate['path'];
    }

    //css gradient hack for ie9
    $sHTML.= '<!--[if gte IE 9]><style type="text/css">.gradient { filter: none; }</style><![endif]-->';


    $sHTML.= '<script type="text/javascript" src="/common/js/jquery.js"></script>';
    $sHTML.= '<script type="text/javascript" src="/common/js/yepnope.1.5.4-min.js"></script>';
    $sHTML.= '<script type="text/javascript" src="/component/form/resources/js/tiny_mce/jquery.tinymce.js"></script>';
    $sHTML.= '<script type="text/javascript" src="/component/form/resources/js/tiny_mce/tiny_mce.js"></script>';
    $sHTML.= '<script type="text/javascript" src="/component/form/resources/js/jquery.tokeninput.js"></script>';
    $sHTML.= '<script type="text/javascript" src="/common/js/common.js"></script>';

    $asJsFile = array();
    foreach($pasJsFile as $sFileName)
    {
      $sHTML.= '<script type="text/javascript" src="'.$sFileName.'"></script>';
      $asFileDate = parse_url($sFileName);
      $asJsFile[] = $asFileDate['path'];
    }

    if(!empty($pasCustomJs))
    {
      $sHTML.= '<script type="text/javascript">';
      foreach($pasCustomJs as $sJsCode)
      {
        $sHTML.= "\n".$sJsCode."\n";
      }
      $sHTML.= '</script>';
    }

    $sHTML.= '<script type="text/javascript">';
    $sHTML.= 'var gasJsFile = ["'.implode('", "', $asJsFile).'"];';
    $sHTML.= 'var gasCssFile = ["'.implode('", "', $asCssFile).'"];';
    $sHTML.= '</script>';

    //For controlling the anchor tag
    $sHTML.= '<script type="text/javascript">';
    $sHTML.= 'var anchorId = window.location.hash;';
    $sHTML.= 'if(anchorId){';
    $sHTML.= '  $(document).ready( function(){';
    $sHTML.= '$(anchorId).click();';
    $sHTML.= '});';
    $sHTML.= '}';
    $sHTML.= '</script>';
    $sHTML.= CONST_WEBSITE_GOOGLE_ANALYTICS;

    $sHTML.= '</head>';

    //Page structure to close in the footer
    $sHTML.= '<body id="body" ';

    foreach($pasPageParam as $sParam => $vValue)
      $sHTML.= ' '.$sParam.'="'.$vValue.'" ';

    $sHTML.= '>';

    $sHTML.= $this->getBlocStart('pageContainerId');
    $sHTML.= $this->getBlocStart('pageMainId');

    if(!$pbIsLogged && CONST_WEBSITE_LOGGEDOUT_LOGO)
    {
      $sHTML.= $this->getBlocStart('headerId', array('style'=>'width:100%; '));

      $sPicture = $this->getPicture(CONST_WEBSITE_LOGGEDOUT_LOGO, $sCustomPageTitle);
      $sHTML.= $this->getLink($sPicture, CONST_WEBSITE_LOGO_URL);
      $sHTML.= $this->getBlocEnd();
    }
    return $sHTML;
  }


  public function getMenu($pbIsLogged, $psComponentUid = '')
  {
    if(empty($psComponentUid) || !CDependency::hasInterfaceByUid($psComponentUid, 'publicContent'))
      $bPublic = false;
    else
      $bPublic = true;

    if(!$pbIsLogged && !$bPublic)
      return '';

    /*@var $oPage CpageEx */
    $oPage = CDependency::getComponentByName('page');
    $oLogin = CDependency::getComponentByName('login');
    $oSettings = CDependency::getComponentByName('settings');

    //-----------------------------------------------------------------
    //horizontal top menu
    $sHTML = $this->getBlocStart('menuContainerId');
    $sHTML.= $this->getBlocStart('menuNavContainerId');

    //Display header
    $sHTML.= $this->_getLogo();

    $sHTML.= '<script language="javascript">';
    $sHTML.= '$(document).ready(function() ';
    $sHTML.= '{ ';
    $sHTML.= '$(".subMenuOwner").each(function(nKey, oElem) ';
    $sHTML.= '{ ';
    $sHTML.= '  var sSubMenuId = $(oElem).attr("subMenu"); ';

    $sHTML.= '  $(oElem).bind("click", function() ';
    $sHTML.= '  { ';
    $sHTML.= '    $(".subMenu").hide(1, function(){  $("#"+sSubMenuId).show(1);  }); ';
    $sHTML.= '  }); ';

    $sHTML.= '}); ';
    $sHTML.= '}); ';
    $sHTML.= '</script>';

    //get an array fromn the conf file, and display the menu based on the values
    //array[component_ui/name][action][type][pk][] = array('label' => '', 'link option' => array() );

    $asSetting = $oSettings->getSettings('menu');
    $sLanguage = $oPage->getLanguage();

    $newMenuElement = array();
    $newMenuElement['name'] = "CLIENT";
    $newMenuElement['legend'] = "Login";
    $newMenuElement['link'] = "https://jobs.slate.co.jp/index.php5?uid=153-160&ppa=clp&ppt=job&ppk=0";
    $newMenuElement['icon'] = "";
    $newMenuElement['target'] = "";
    $newMenuElement['uid'] = "";
    $newMenuElement['type'] = "";
    $newMenuElement['action'] = "";
    $newMenuElement['pk'] = 0;
    $newMenuElement['right'] = array("*");

    $add[] = $newMenuElement;

    if(isset($asSetting['menu'][$sLanguage]))
      $add2 = $asSetting['menu'][$sLanguage];
    else
      $add2 = $asSetting['menu'][CONST_DEFAULT_LANGUAGE];

    foreach ($add2 as $key => $value)
    {
      $add[] = $value;
    }

    $asMenuArray = $add;

    $nLoginPk = $oLogin->getUserPk();

    global $gasMainMenu;
    //$gasMainMenu = CONST_SHOW_MENU_BAR; //Just to enter into the loop for now, modify later

    $useragent=$_SERVER['HTTP_USER_AGENT'];
    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
    {
      $newClass = " hidden"; // mobile version icin ekledik
      //header('Location: http://detectmobilebrowser.com/mobile');
    }
    else
    {
      $newClass = " ";
    }

    //if(!empty($gasMainMenu))
    {
      $sHTML.= $this->getBlocStart('', array('class' => 'firstMenuContainer'.$newClass,'style'=>'width=1000px;'));
      $sHTML.= '<ul style="padding-left:-200px;" class="menuNavList1">';

      //var_dump($asMenuArray);

      if(!empty($asMenuArray))
      {
        foreach($asMenuArray as $asMenuItems)
        {
          if($this->_canAccessMenu($asMenuItems))
          {
            $sHTML.= '<li>';
            $sHTML.= $this->getBlocStart();

            if(!empty($asMenuItems['link']))
            {
              if(isset($asMenuItems['embedLink']))
                $sLink = $oPage->getUrlEmbed($asMenuItems['link']);
              else
                $sLink = $asMenuItems['link'];
            }
            else
            {
              if(!empty($asMenuItems['uid']))
              {
                $nPnPk = (int)$asMenuItems['pk'];
                $sLink = $oPage->getUrl(''.$asMenuItems['uid'].'',''.$asMenuItems['action'].'',''.$asMenuItems['type'].'',$nPnPk);
                }
                else
                {
                  $sLink = 'javascript:;';
                }
            }

            $useragent=$_SERVER['HTTP_USER_AGENT'];
            if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
            {
              $newClass = " hidden"; // mobile version icin ekledik
              //header('Location: http://detectmobilebrowser.com/mobile');
            }
            else
            {
              $newClass = " ";
            }

            if(isset($asMenuItems['ajaxpopup']) && !empty($asMenuItems['ajaxpopup']))
            {
              $sURL = $oPage->getAjaxUrl($asMenuItems['uid'], $asMenuItems['action'], $asMenuItems['type'], $nLoginPk);
              $sAjax = $this->getAjaxPopupJS($sURL, 'body','','600','800',1);
              $sHTML.= $this->getLink($this->getPicture($this->getResourcePath().$asMenuItems['icon'],  $asMenuItems['name'], '', array('class' => 'mainMenuPic'.$newClass)), 'javascript:;', array('onclick'=>$sAjax));
            }
            else
            {
              if(!empty($asMenuItems['icon']))
                $sHTML.= $this->getPicture($this->getResourcePath().$asMenuItems['icon'], $asMenuItems['name'], $sLink, array('class' => 'mainMenuPic'.$newClass));
              else
              {
                if($asMenuItems['name'] == "ジョブズ")
                {
                  $sLink = "https://jobs.slate.co.jp/";
                }
                $sHTML.= $this->getLink($asMenuItems['name'], $sLink, array('class' => 'mainMenuPic test '.$newClass));
              }

              if(isset($asMenuItems['legend']) && !empty($asMenuItems['legend']))
              {
                $sHTML.= $this->getCarriageReturn();
                $sHTML.= $this->getSpanStart('', array('class' => 'menuNavLegend'));
                $sHTML.= $this->getLink($asMenuItems['legend'], $sLink, array('class' => 'mainMenuPic'.$newClass));
                $sHTML.= $this->getSpanEnd();
              }
            }

            $sCurrentCp = $oPage->getRequestedUid();
            if($sCurrentCp == $asMenuItems['uid'])
              $sExtraClass = ' display ';
            else
              $sExtraClass = ' hidden ';

            //Display submenu if the child is set
            if(!empty($asMenuItems['child']))
            {
              $asChildMenu = $asMenuItems['child'];
              $sHTML.= '<ul class="subMenu" id="addressbookSubMenu">';

              foreach($asChildMenu as $asChildren)
              {
                if($this->_canAccessMenu($asChildren))
                {
                  if(!isset($asChildren['onclick']))
                      $asChildren['onclick'] = '';

                  if(!isset($asChildren['target']))
                      $asChildren['target'] = '';

                  if(!isset($asChildren['icon']) || empty($asChildren['icon']))
                      $asChildren['icon'] = '';
                  else
                  {
                    if(substr($asChildren['icon'], 0, 1) == '/' || substr($asChildren['icon'], 0, 4) == 'http')
                      $asChildren['icon'] = $this->getPicture($asChildren['icon']).' ';
                    else
                      $asChildren['icon'] = $this->getPicture($this->getResourcePath().$asChildren['icon']).' ';
                  }

                  if(!empty($asChildren['link']))
                  {
                    if(isset($asChildren['embedLink']))
                      $sURL = $oPage->getUrlEmbed($asChildren['link']);
                    else
                      $sURL = $asChildren['link'];
                  }
                  else
                  {
                    if(!empty($asChildren['uid']))
                    {
                      $nPnPk = (int)$asChildren['pk'];

                      if(isset($asChildren['loginpk']) && !empty($asChildren['loginpk']))
                        $sURL = $oPage->getUrl(''.$asChildren['uid'].'',''.$asChildren['action'].'',''.$asChildren['type'].'',$nPnPk,array('loginpk'=>(int)$nLoginPk));
                      else
                        $sURL = $oPage->getUrl(''.$asChildren['uid'].'',''.$asChildren['action'].'',''.$asChildren['type'].'',$nPnPk);
                      }
                      else
                      {
                        $sURL = 'javascript:;';
                      }
                   }

                    if(isset($asChildren['ajaxpopup']) && !empty($asChildren['ajaxpopup']))
                    {
                      $sURL = $oPage->getAjaxUrl($asChildren['uid'], $asChildren['action'], $asChildren['type'], $nLoginPk);
                      $sAjax = $this->getAjaxPopupJS($sURL, 'body','','600','800',1);
                      $sHTML.= '<li>'.$this->getLink($asChildren['icon'].$asChildren['name'], 'javascript:;', array('onclick'=>$sAjax.' '.$asChildren['onclick'], 'target'=>$asChildren['target'])).'</li>';
                    }
                    else
                      $sHTML.= '<li>'.$this->getLink($asChildren['icon'].$asChildren['name'], $sURL, array('onclick' => '/*setCoverScreen(true, true);*/ '.$asChildren['onclick'], 'target'=>$asChildren['target'])).'</li>';
                  }
                }
                $sHTML.= '</ul>';
              }


            $sHTML.= $this->getBlocEnd();
            $sHTML.= '</li>';
          }
        }
      }
      $sHTML.= '</ul>';

      $sHTML.= $this->getFloatHack();

      $sHTML.= $this->getBlocStart('', array('class' => 'topMenuLayer'));
      $sHTML.= $this->getSpace();
      $sHTML.= $this->getBlocEnd();

      //Display the user datas / logout link...
      $sHTML.= $this->_getUserMenuBloc($pbIsLogged);
      $sHTML.= $this->getBlocEnd();

      //=================================================
      //SUBMENU event
      $sHTML.= $this->getBlocStart();
      $sHTML.= '<ul class="subMenu hidden" id="eventSubMenu">';
      $sHTML.= '<li>Last events</li>';
      $sHTML.= '<li>Reminders</li>';
      $sHTML.= '</ul>';
      $sHTML.= $this->getBlocEnd();

      $sEmbedUrl = $oPage->getEmbedUrl();
      if(!empty($sEmbedUrl))
      {
        $sHTML.= $this->getBlocStart('', array('class' => 'embedMenuLink'));
        //$sHTML.= 'Currently on <input type="text" id="urlFieldId" value="'.$sEmbedUrl.'" style="width:450px; color:orange; font-size:11px; height: 15px; border: 0;" /> ';
        //$sHTML.= '<a href="javascript:;" onClick="var sUrl = $(\'#urlFieldId\').val(); $(\'#embedFrameId\').attr(\'src\', sUrl);">>></a>  ' ;
        $sHTML.= $this->getLink('+ Open in new tab', $sEmbedUrl, array('target' => '_blank'));
        $sHTML.= $this->getBlocEnd();
      }

      $sHTML.= $this->getBlocEnd();
      $sHTML.= $this->getBlocEnd();
    }

    //-----------------------------------------------------------------
    //vertical action left menu

      $sHTML.= $this->_getSecondLevelMenu($psComponentUid);

      $sHTML.= '</ul>';
      $sHTML.= $this->getBlocEnd();
      $sHTML.= $this->getBlocEnd();

      return $sHTML;
  }


  private function _getSecondLevelMenu($psComponentUid)
  {
    $oPage = CDependency::getComponentByName('page');
    $asComponent = CDependency::getComponentIdByInterface('menuGlobalAction');
    $sSearchComponentId = CDependency::getComponentUidByName('search');

    $sHTML = $this->getBlocStart('menuActionContainerId');

    //TODO : Need to make this condition generic later

    if(CONST_WEBSITE == 'talentAtlas')
    {
      $sLanguage = $oPage->getLanguage();

      $sJavascript = ' var value = $(this).val();
       if(value == \'en\')
       {
          url = \''.CONST_CRM_DOMAIN.'?setLang=en\'
        }
       else
       {
         url = \''.CONST_CRM_DOMAIN.'?setLang=jp\'
       }
       window.location.href = url;';

      $sHTML.= $this->getBlocStart('',array('class'=>'languageSelect'));
       $sHTML.= '<select name="language_change" onchange="'.$sJavascript.'" class="selectLang">';
        $sHTML.= '<option value= "en" '.(($sLanguage == 'en')? 'selected="selected"':'').'> English </option>';
        $sHTML.= '<option value= "jp" '.(($sLanguage == 'jp')? 'selected="selected"':'').'> Japanese </option>';
       $sHTML.= '</select>';
      $sHTML.= $this->getBlocEnd();
    }

    $sHTML.= '<ul>';

    if(!empty($asComponent))
    {
      foreach($asComponent as $sComponentId)
      {
        $oComponent = CDependency::getComponentByUid($sComponentId);
        $asComponentAction = $oComponent->getPageActions($oPage->getAction(), $oPage->getType(), $oPage->getPk());

        foreach ($asComponentAction as $aasAction)
        {
          $nAction= count($aasAction);
          $nCount = 0;
          foreach($aasAction as $sType => $asAction)
          {
            if($nCount == 1)
            {
              //More than 1 action: close the Menu container + add arrow + open submenu container
              $sHTML.= '</div>';
              $sHTML.= '<div class="menuActionMenuExtend"><div><a href="javascript:;"><img src="'.CONST_PICTURE_MENU_MULTIPLE.'" /></a></div></div>';
              $sHTML.= '<div class="menuActionSubMenuContainer"><ul class="menuActionSubMenu">';
            }

            if(!isset($asAction['picture']) || empty($asAction['picture']))
              $asAction['picture'] = $this->getResourcePath().'pictures/menu/unknown.png';

            if(!isset($asAction['title']))
              $asAction['title'] = '';

            if(!isset($asAction['option']))
              $asAction['option'] = array();

            if(!empty($sSearchComponentId))
            {
              if($sComponentId != $sSearchComponentId)
              {
                $sHTML.= '<li><div class="menuActionMenuContainer">';
               // $sHTML.= $this->getPicture($asAction['picture'], $asAction['title'], $asAction['url'], $asAction['option']);
              }
            else
            {
              if(CONST_DISPLAY_SEARCH_MENU)
              {
                $sHTML.= '<li class="menuSearchForm" ><div class="menuActionMenuContainer">';
                if(isset($asAction['option']['onclick']))
                  $asAction['option']['onclick'].= " $('li.menuSearchForm div div').fadeToggle();";
                else
                {
                  $sHTML.= '<li class="menuSearchForm" ><div class="menuActionMenuContainer">';
                  if(isset($asAction['option']['onclick']))
                    $asAction['option']['onclick'].= " $('li.menuSearchForm div div').fadeToggle();";
                  else
                    $asAction['option']['onclick'] = " $('li.menuSearchForm div div').fadeToggle();";

                 // $sHTML.= $this->getPicture($asAction['picture'], $asAction['title'], $asAction['url'], $asAction['option']);

                  //TODO: fix this crap and call the form in ajax if needed
                  $sURL = $oPage->getAjaxUrl('search', CONST_ACTION_SEARCH);
                  $sJs = $this->getAjaxJs($sURL, '#body', 'searchFormId', 'componentContainerId');

                  $sHTML.= $this->getBlocStart('searchBlocId',array('class' => 'searchBloc'));
                  $sHTML.= '<script language="javascript">$("#searchFormId").submit(function(event){ event.preventDefault(); '.$sJs.' });</script>';

                  $sHTML.= '<form name="searchForm" action="#" id="searchFormId" onsubmit="return false;">';
                  $sHTML.= 'Search: <input type="text" name="search" style="width:150px;" /><br />';
                  $sHTML.= '<br /><input type="button" value="search" onClick="'.$sJs.'" />';
                  $sHTML.= '&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onClick=" alert(\'get in ajax the search form\');">Advanced search</a>';
                  $sHTML.= '</form>';
                  $sHTML.= $this->getBlocEnd();
                }
              }
            }
            if($nAction <= 1)
              $sHTML.= '</div><div class="floatHack"></div></li>';

            $nCount++;
          }

          if($nAction > 1)
            $sHTML.= ' </ul></div><div class="floatHack"></div></li>';

          }
        }
      }
    }

    //Display components custom actions
    if($psComponentUid)
    {
      $oComponent = CDependency::getComponentByUid($psComponentUid, 'menuAction');
      if(!empty($oComponent))
      {
        $asComponentAction = $oComponent->getPageActions($oPage->getAction(), $oPage->getType(), $oPage->getPk());
        //dump($asComponentAction);

        foreach ($asComponentAction as $aasAction)
        {
          $nAction= count($aasAction);
          $nCount = 0;
          foreach($aasAction as $sType => $asAction)
          {
            if($nCount == 1)
            {
              //More than 1 action: close the Menu container + add arrow + open submenu container
              $sHTML.= '</div>';
              $sHTML.= '<div class="menuActionMenuExtend"><div><a href="javascript:;"><img src="'.CONST_PICTURE_MENU_MULTIPLE.'" /></a></div></div>';
              $sHTML.= '<div class="menuActionSubMenuContainer"><ul class="menuActionSubMenu">';
            }

            if(!isset($asAction['title']))
              $asAction['title'] = '';

            if(!isset($asAction['option']))
              $asAction['option'] = array();

            $sHTML.= '<li><div class="menuActionMenuContainer">';

            if(isset($asAction['picture']) && !empty($asAction['picture']))
              $sHTML.= $this->getPicture($asAction['picture'], $asAction['title'], $asAction['url'], $asAction['option']);
            else
              $sHTML.= $this->getLink($asAction['title'], $asAction['url'], $asAction['option']);

            if($nAction <= 1)
              $sHTML.= '</div><div class="floatHack"></div></li>';

            $nCount++;
          }

          if($nAction > 1)
          {
            $sHTML.= ' </ul></div><div class="floatHack"></div></li>';
          }
        }
      }
    }

    $sHTML.= '</ul>';
    $sHTML.= $this->getBlocEnd();
    return $sHTML;
  }

  private function _getLogo()
  {
    $useragent=$_SERVER['HTTP_USER_AGENT'];
    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
    {
      return '';
    }
    else
    {
      $oPage = CDependency::getComponentByName('page');

      $sPicture = $this->getPicture(CONST_HEADER_LOGO, $oPage->getPageTitle());
      $sHTML = $this->getBlocStart('', array('class'=>'userBloc'));
      $sHTML.= $this->getLink($sPicture, CONST_WEBSITE_LOGO_URL);

      if(CONST_DISPLAY_VERSION)
      {
        $sHTML.= $this->getBlocStart('', array('class'=>'versionBloc'));
        $sHTML.= $this->getText('v. '.CONST_VERSION);
        $sHTML.= $this->getBlocEnd();
      }

      $sHTML.= $this->getBlocEnd();
      return $sHTML;
    }
  }


  private function _getUserMenuBloc($pbIsLogged)
  {
    if(!$pbIsLogged)
       return '';

    /* @var $oLogin CLoginEx */
    /* @var $oPage CPageEx */
    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');

    $sURL = $oPage->getAjaxUrl('login',CONST_ACTION_LOGOUT,'',0,array('logout'=>1));
    $sAjax = $this->getAjaxJs($sURL);
    $sPicture = $this->getPicture('/media/picture/logout.png', 'Logout from CRM');

    $sHTML = $this->getBlocStart('', array('class'=>'userBlock'));

    $sHTML.= $this->getBlocStart('', array('style' => 'color:#fff;'));
    $sHTML.= $this->getText('Welcome '.$oLogin->getUserName());
    $sHTML.= $this->getBlocEnd();

    $sHTML.= $this->getBlocStart('',array('style'=>'width:200px;'));
    $sHTML.= $this->getLink($sPicture.' Logout', 'javascript:;', array('onclick'=>$sAjax));
    $sHTML.= ' - ';

    $pnLoginPk = $oLogin->getUserPk();

    $sURL = $oPage->getUrl('login', CONST_ACTION_EDIT, CONST_LOGIN_TYPE_USER,$pnLoginPk);
    $sHTML.= $this->getLink('My account', $sURL, array('target'=>'_parent'));
    $sHTML.= $this->getSpace(2);

    //Display only to the administrator
    $sSettingURL = $oPage->getUrl('settings', CONST_ACTION_ADD, CONST_TYPE_SETTINGS);
    if($oLogin->isAdmin())
      $sHTML.= $this->getLink('Settings ',$sSettingURL,array('style' => 'color:#fff;'));

    $sHTML.= $this->getBlocEnd();

    $sHTML.= $this->getBlocEnd();

    return $sHTML;
  }

  public function getComponentStart($pbIsLogged, $pasParam = array())
  {
    if($pbIsLogged)
    {
      $sHTML = $this->getBlocStart('componentContainerId', $pasParam);
    }
    else
    {
      if(isset($pasParam['class']))
        $pasParam['class'].= ' containerUnlogged';
      else
        $pasParam['class'] = 'containerUnlogged';

      $sHTML = $this->getBlocStart('componentContainerId', $pasParam);
    }

    $sHTML.= $this->getBlocStart('', array('class' => 'componentMainContainer'));
    return $sHTML;
  }

  public function getComponentEnd()
  {
    $sHTML = $this->getBlocEnd();
    $sHTML.= $this->getBlocEnd();
    return $sHTML;
  }

  public function getFooter()
  {
    $oPage = CDependency::getComponentByName('page');
    $oSettings = CDependency::getComponentByName('settings');

    $asFooter = $oSettings->getSettings('footer');

    $sHTML = $this->getFloatHack();

    //closing blocs opened in header
    $sHTML.= $this->getBlocEnd();
    $sHTML.= $this->getBlocEnd();

    //start displaying the footer
    $sHTML.= getCustomWebsiteFooter($asFooter);

    $sHTML.= $this->getBlocStart('ajaxErrorContainerId', array('class' => 'ajaxErrorBlock'));
      $sHTML.= $this->getBlocStart('ajaxErrorInnerId', array('class' => 'notice2'));
      $sHTML.= $this->getBlocStart();

      $sHTML.= $this->getBlocStart('', array('style' => 'float:right; '));
      $sHTML.= $this->getLink('Close', 'javascript:;', array('onclick' => "$('#ajaxErrorContainerId').hide();"));
      $sHTML.= $this->getBlocEnd();

      $sHTML.= $this->getTitle('Oops, an error occured', 'h2', true);
      $sHTML.= $this->getCarriageReturn();
      $sHTML.= $this->getText("An unknown error occured while executing your last action.");
      $sHTML.= $this->getCarriageReturn();
      $sHTML.= $this->getText("If you're seeing this message for the first time, please try to reload the page or close your web browser before starting again.");
      $sHTML.= $this->getCarriageReturn();
      $sHTML.= "In the other case, please contact the administrator or report the problem using <a href='javascript:;' onclick=' $(\"#dumpFormId\").submit();'>this form</a>.";
      $sHTML.= "<form name='dumpForm' id='dumpFormId' method='post' action='/error_report.php5' ><input type='hidden' name='dump' id='dumpId' /> </form>";
      $sHTML.= $this->getBlocEnd();
      $sHTML.= $this->getBlocEnd();
      $sHTML.= $this->getBlocEnd();

      $sHTML.= $this->getBlocStart('embedPopupId');
      $sHTML.= $this->getBlocEnd();

      $sHTML.= $this->getBlocStart('popupBlockId', array('style' => 'display:none; position:absolute;'));
      $sHTML.= $this->getBlocEnd();


      $sHTML.= '
        <div id="loadingScreenAnimation">
          <img src="'.CONST_WEBSITE_LOADING_PICTURE.'"/>
        </div>
      </div>

      <div id="loadingScreen">

        <div id="loadingScreenAnimation">
          <img src="'.CONST_WEBSITE_LOADING_PICTURE.'"/>
        </div>
      </div>

      <div id="popupContainer">
      <div id="popupInner">

        <div id="popupClose">
          <a href="javascript:;" onClick="setLoadingScreen(\'#slystemContainer\', false); removePopup();"><img src="/media/picture/close_24.png"/></a>
        </div>

        <div id="popupInnerContainer">
        </div>

      <div id="popupCloseBottom">
        <input type="button" value="Close" onClick="setLoadingScreen(\'#slystemContainer\', false); removePopup()">
      </div>

      <img id="loaderGif" src="'.CONST_WEBSITE_LOADING_PICTURE.'" style="display: none;" border=0 />

      <div class="floatHack"></div>

    </div>
    </div>';
    return $sHTML;
  }

  public function getNoContentMessage()
  {
    //closing blocs opened in header
    $sHTML = $this->getBlocMessage("Sorry, the page you're requesting doesn't exist.");

    return $sHTML;
  }

  public function getEmbedPage($psUrl)
  {
    $sHTML = '<iframe src="'.$psUrl.'" id="embedFrameId" class="embedFrame" scrolling="auto" frameborder="0" width="800" height="500" ';

    $sHTML.= 'onload="
    var nwidth = $(this).parent().width() - 30;
    var nHeight = $(document).height() -125;
    $(this).animate({\'height\':nHeight, \'width\':nwidth}, 1, function(){ $(this).fadeIn(); });" ';
    $sHTML.= ' id="embedIframeId" class="embedIframe"></iframe>';

    return $sHTML;
  }

  public function getBlocMessage($psMessage, $pbIsHtml = false)
  {
    $sHTML = $this->getBlocStart('', array('class'=>'blocMessage'));
    if($pbIsHtml)
      $sHTML.= $psMessage;
    else
      $sHTML.= $this->getText($psMessage);
    $sHTML.= $this->getBlocEnd();

    return $sHTML;
  }


  public function getErrorMessage($psMessage)
  {
    $sHTML = $this->getBlocStart('', array('class'=>'notice2'));
    $sHTML.= $this->getText($psMessage);
    $sHTML.= $this->getBlocEnd();

    return $sHTML;
  }

  public function getMessage($psMessage, $psURL = '', $psType = '')
  {
    switch($psType)
    {
      case '':
      case 'info':
        $sClass = 'notice';
      break;

      case 'error':
        $sClass = 'notice2';
      break;
    }

    $sHTML = $this->getBlocStart('', array('class' => $sClass));
    $sHTML.= $this->getText($psMessage);
    $sHTML.= $this->getBlocEnd();

    $sHTML.= '<script>$(document).ready(function(){ setPopup("'.$psMessage.'", "'.$psURL.'", "'.$sClass.'"); });</script>';

    return $sHTML;
  }

  public function getExtract($psString, $nCount)
  {
    if(!assert('!empty($psString)') )
      return '';

    if(!assert('is_integer($nCount)')&& !assert('!empty($nCount)') )
      return '';

    if(strlen($psString) <= $nCount)
      return $psString;

    $sString = substr($psString, 0, $nCount);
    return $sString.'...';
  }

  public function getNiceTime($psDate = '', $pnTime = 0, $pbAdvDisplay = false, $pbAdvDisplayPic = false, $pnConvertUntil = 999)
  {
    if((empty($psDate) || $psDate == '0000-00-00' || $psDate == '0000-00-00 00:00:00') && empty($pnTime))
    {
      return ' - ';
    }

    if(!empty($psDate))
      $nTime = (int)strtotime($psDate);
    else
      $nTime = (int)$pnTime;

    $nNow = (int)time();
    $nTimeDif = ($nNow - $nTime);

    $sDateNow = date('Y-m-d', $nNow);
    $sDate = date('Y-m-d', $nTime);

    if($sDate > $sDateNow)
    {
      $sPrefix = 'in ';
      $sSuffix = '';
    }
    else
    {
      $sPrefix = '';
      $sSuffix = ' ago';
    }

    if($pbAdvDisplay)
    {
      $sHtmlStart = '<a href="javascript:;" class="niceTimeLink ';
      if($pbAdvDisplayPic)
      {
        $sHtmlStart.= ' niceTimePic ';
      }
      $sHtmlStart.= '">';

      $sTimeDisplay = date('H:i:s', $nTime);
      if($sTimeDisplay == '00:00:00')
        $sHtmlEnd = '<div class="niceTimeDetail">'.date('Y-m-d', $nTime).'</div></a>';
      else
        $sHtmlEnd = '<div class="niceTimeDetail">'.date('Y-m-d H:i:s', $nTime).'</div></a>';
    }
    else
    {
      $sHtmlStart = '';
      $sHtmlEnd = '';
    }

    //text for todays dates
    if($sDateNow == $sDate)
    {
       if($nTimeDif <= 60)
         return $sHtmlStart.$sPrefix.'a few sec.'.$sSuffix.$sHtmlEnd;

       if($nTimeDif > 60 && $nTimeDif <= 3600)
         return $sHtmlStart.$sPrefix.floor($nTimeDif/60).' minutes'.$sSuffix.$sHtmlEnd;

       if($nTimeDif > 3600 && $nTimeDif <= 86400)
         return $sHtmlStart.$sPrefix.floor($nTimeDif/3600).' hours'.$sSuffix.$sHtmlEnd;
    }

    $oDatetNow = new DateTime();
    $oDate = new DateTime($sDate);
    $oDateDiff = $oDatetNow->diff($oDate);

    $nDiffDays = (int)$oDateDiff->format('%a');
    if($pnConvertUntil >= 15 && $nDiffDays <= 15)
      return $sHtmlStart.$sPrefix.$nDiffDays.' days'.$sSuffix.$sHtmlEnd;

    if($pnConvertUntil >= 100 && $nDiffDays <= 100)
      return $sHtmlStart.$sPrefix.floor($nDiffDays/7).' weeks'.$sSuffix.$sHtmlEnd;

    if($pnConvertUntil >= 365 && $nDiffDays <= 365)
       return $sHtmlStart.$sPrefix.$oDateDiff->format('%m').' months'.$sSuffix.$sHtmlEnd;

    if($pnConvertUntil >= 999)
      return $sHtmlStart.$sPrefix.$oDateDiff->format('%y').' years'.$sSuffix.$sHtmlEnd;

    return $sDate;
  }

  //****************************************************************************
  //****************************************************************************
  //Form managment
  //****************************************************************************
  //****************************************************************************

  /**
   *
   * Enter description here ...
   * @param string $psFormName
   * @return CForm
   */
  public function initForm($psFormName = '')
  {
    require_once('component/form/form.class.ex.php5');
    $oForm = new CFormEx($psFormName);

    return $oForm;
  }

  private function _canAccessMenu($pasMenuItem)
  {
    if(!assert('is_array($pasMenuItem) && !empty($pasMenuItem)'))
      return false;

    if(!isset($pasMenuItem['right']) || !is_array($pasMenuItem['right']))
    {
      assert('false; // no right data for the menu item ');
      return false;
    }

    if(in_array('*', $pasMenuItem['right']))
      return true;

    $oLogin = CDependency::getComponentByName('login');

    if(in_array('logged', $pasMenuItem['right']) && $oLogin->isLogged())
      return true;

    $oRight = CDependency::getComponentByName('right');

    foreach($pasMenuItem['right'] as $asRight)
    {
      if(count($asRight) >= 3)
      {
        if($oRight->canAccess(@$asRight['uid'], @$asRight['action'], @$asRight['type'], @$asRight['pk']))
          return true;
      }
    }

    return false;
  }
}
