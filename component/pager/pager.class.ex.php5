<?php

require_once('component/pager/pager.class.php5');

class CPagerEx extends CPager
{
  private $csSearchForm = '';
  private $csPagerId = '';
  private $cbInitialized = false;
  private $cnPagerOffset = 1;
  private $cnPagerLimit = CONST_PAGER_NUM;
  private $cnMinResultNb = 10;

  function __contruct()
  {
    //TODO: Display and edit js to function based on this uniqId
    $this->csPagerId = uniqid('pager_');
  }


  public function setLanguage($psLanguage)
  {
    require_once('language/language.inc.php5');
    if(isset($gasLang[$psLanguage]))
      $this->casText = $gasLang[$psLanguage];
    else
      $this->casText = $gasLang[CONST_DEFAULT_LANGUAGE];
  }

  function initPager($pnMinResultNb = -1)
  {
    //==============================================
    //Pager initialization
    $nPagerOffset = (int)getValue('pageoffset', 0);
    $nPagerLimit = (int)getValue('nbresult',CONST_PAGER_NUM);

    if(empty($nPagerOffset) || $nPagerOffset < 1)
      $nPagerOffset = 0;

    if($pnMinResultNb >= 0)
     $this->cnMinResultNb = $pnMinResultNb;

    //include not specified and errors
    if($nPagerLimit < $this->cnMinResultNb)
    {
      //use session var
      if(isset($_SESSION['userPreference']['list']))
        $nPagerLimit = (int)$_SESSION['userPreference']['list'];
      elseif(isset($_SESSION['pager']))
        $nPagerLimit = $_SESSION['pager'];
      else
          $nPagerLimit = 25;
    }
$nPagerLimit = 8;
    //if the user change the Limit, we calculate the new page number
    /*if(isset($_SESSION['pager']) && $nPagerLimit != $_SESSION['pager'])
    {
      $nPreviousNbRows = ($_SESSION['pager'] * $nPagerOffset);
      $nPagerOffset = floor($nPreviousNbRows / $nPagerLimit);
    }*/

    $_SESSION['pager'] = $nPagerLimit;
    //Pager initialization End
    //==============================================

    $this->cnPagerLimit = $nPagerLimit;
    $this->cnPagerOffset = $nPagerOffset;
    $this->cbInitialized = true;
    return true;
  }

  public function setLimit($pnLimit)
  {
    $this->cnPagerLimit = $pnLimit;
  }

  public function setOffset($pnOffset)
  {
    $this->cnPagerOffset = $pnOffset;
  }

  public function getLimit()
  {
    if(!$this->cnPagerLimit)
       return CONST_PAGER_NUM;

    return $this->cnPagerLimit;
  }

  public function getOffset()
  {
    //cnPagerOffset is the actual requested page, meaning it's all the results from cnPagerOffset-1 + current limit
    if(($this->cnPagerOffset-1) < 0)
       return 0;

    return ($this->cnPagerOffset-1);
  }

  public function getSqlOffset()
  {
    $nQueryOffset = ($this->getOffset() * $this->cnPagerLimit);
    if($nQueryOffset <= 0)
      return 0;

    return $nQueryOffset;
  }

  /*
   * $pnResult : nb of results
   * $psUrl : called url when paging up down
   * $pasUrlOption : link options (to be able to manage ajax and callback function
   */
  public function getDisplay($pnResult, $psUrl, $pasUrlOption = array())
  {
    if(!assert('is_integer($pnResult) && is_array($pasUrlOption)') || empty($pnResult))
      return '';

    //===========================================================================================
    //===========================================================================================
    //Init
    //===========================================================================================

    if(!$this->cbInitialized)
      $this->initPager();

    $nPagerOffset = $this->cnPagerOffset;
    $nPagerLimit = $this->cnPagerLimit;

    if($nPagerOffset < 1)
      $nPagerOffset = 1;

    $sPager = '';

    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'/css/pager.css');

    $oHTML = CDependency::getComponentByName('display');

    $sPagerId = uniqid('pagerId_');
    $bAjaxPager = $oPage->isAjaxUrl($psUrl);

    if(!isset($pasUrlOption['ajaxTarget']))
      $pasUrlOption['ajaxTarget'] = '';

    if(!isset($pasUrlOption['ajaxCallback']))
      $pasUrlOption['ajaxCallback'] = '';

    if($bAjaxPager && empty($pasUrlOption['ajaxTarget']))
    {
      assert('false; //page in ajax mode, but no zone to refresh.');
      return '';
    }


    //Get url and js ready for pager elements
    $asOption['nbresult'] = $this->cnPagerLimit;
    $sUrl = $oPage->addUrlParams($psUrl, $asOption);
    $sJs = ' pagerGetPage(this, \''.$sUrl.'\', '.(int)$bAjaxPager.', \''.$pasUrlOption['ajaxTarget'].'\'); ';

    if($pnResult > $nPagerLimit)
    {
       $nNbPages = floor(($pnResult / $nPagerLimit));
      if(($nNbPages * $nPagerLimit) < $pnResult)
        $nNbPages++;

      //----------------------------------------
      //calculating the page range for the pager

      $nMax = $nMin = $nPagerOffset;
      $nCount = 0;
      $nPageSize = 0;
      $nCountPlus = 1;
      $nCountMinus = 1;
      While($nPageSize < 10 && $nCount < 20)
      {
        if(($nCount%2) == 0)
        {
          $nPage = $nPagerOffset+$nCountPlus;
          if($nPage <= $nNbPages &&  $nPage > $nMax)
          {
            $nMax = $nPage;
            $nCountPlus++;
          }
        }
        else
        {
          $nPage = $nPagerOffset-$nCountMinus;
          if($nPage > 1 &&  $nPage < $nMin)
          {
            $nMin = $nPage;
            $nCountMinus++;
          }
        }

        $nCount++;
        $nPageSize = $nCountPlus + $nCountMinus;
      }
    }
    $nDisplayed = 0;

    //===========================================================================================
    //===========================================================================================
    //Displaying pager bloc
    //===========================================================================================
    $sPager = '<div id="'.$sPagerId.'" class="pagerContainer">';


    //Displaying pager navigation (if multiple pages)
    //===========================================================================================


    if($pnResult > $nPagerLimit)
    {
      $sPager.= '<div class="pagerInfo pagerDisplay">';
      $sPager.= '<strong>'.$pnResult.'</strong> '.$this->casText['PAGER_RESULTS'].'.<br /><strong>'.$nNbPages.'</strong> '.$this->casText['PAGER_PAGES'];
      $sPager.= '</div>';
    }
    else
    {
      $sPager.= '<div class="pagerInfo pagerDisplay">';
      $sPager.= '<strong>'.$pnResult.'</strong> '.$this->casText['PAGER_RESULTS'].'.<br />1 '.$this->casText['PAGER_PAGE'];
      $sPager.= '</div>';
    }

    if($pnResult > $nPagerLimit)
    {

      $sPager.= '<div class="pagerNavigation">';
      $sPager.= '<div class="pagerNavigationInner">';

      //----------------------------------------
      //hide first and previous if on first page
      if(($nPagerOffset) <= 1)
        $sClass = ' hidden ';
      else
        $sClass = '';

      //----------------------
      $sPager.= '<div class="pagerNavigationBefore">&nbsp;';

      $sPager.= '<div class="pager_pageLink pager_pageLinkPic '.$sClass.'">';

      $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/pager_first.png');
      if(CONST_WEBSITE !='bcm')
         $sPager.= ' <div class="pager_pageLink">'.$oHTML->getLink($sPic, 'javascript:;', array('onclick' => $sJs, 'pagervalue' => 1)).'</div>';
       else
         $sPager.= ' <div class="pager_pageLink">'.$oHTML->getLink($sPic, 'javascript:;', array('onclick' => $sJs."jQuery(body).animate({scrollTop: '0px'}, 600, 'linear');", 'pagervalue' => 1)).'</div>';

      $sPager.= '</div>';
      //----------------------

      $sPager.= '<div class="pager_pageLink pager_pageLinkPic'.$sClass.'">';
      if($nNbPages > 10)
     {
        $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/pager_previous.png');
        $sPager.= ' <div class="pager_pageLink">'.$oHTML->getLink($sPic, 'javascript:;', array('pagervalue' => ($nPagerOffset-1), 'onmousedown' => 'slidePager(\'#'.$sPagerId.'\', false); ', 'onmouseup' => ' slidePager(\'#'.$sPagerId.'\', false, 0, true); ')).'</div>';
     }
      $sPager.= '</div>  ';
      //----------------------

      //$sPager.= '<div class="floatHack"></div>';
      $sPager.= '</div>  ';

      //-------------------------------------------------------------------------
      //-------------------------------------------------------------------------
      //----------------------
      //Page number container (ajax ready)
      $sPager.= '<div class="pagerNavigationNumbers" id="pagerNavigationNumbers" nbpages="'.$nNbPages.'" currentpage="'.$nPagerOffset.'" maxdisplayed="'.$nMax.'" mindisplayed="'.$nMin.'">';

      for($nCount = $nMin; $nCount <= $nMax; $nCount++)
      {
        if($nCount > 0)
        {
          if($nCount > 9999)
            $sClass = 'pagerSmaller';
          else
            $sClass = '';

          if($nCount == $nPagerOffset)
            $sPager.= ' <div class="pager_pageLink pager_CurrentPage '.$sClass.'">'.$nCount.'</div> ';
          else
          {
            if(CONST_WEBSITE !='bcm')
            $sPager.= ' <div class="pager_pageLink '.$sClass.'">'.$oHTML->getLink($nCount, 'javascript:;', array('onclick' => $sJs."jQuery(body).animate({scrollTop: '0px'}, 600, 'linear');", 'pagervalue' => $nCount)).'</div>';
            else
            $sPager.= ' <div class="pager_pageLink '.$sClass.'">'.$oHTML->getLink($nCount, 'javascript:;', array('onclick' => $sJs, 'pagervalue' => $nCount)).'</div>';

          }

          $nDisplayed++;
          if($nDisplayed >= 10)
            $nCount = $nMax+10;
        }
      }
      //$sPager.= '<div class="floatHack"></div>';
      $sPager.= '</div>';

      //Empty pager line (copy from above) to give a template to the js function
      $sPager.= ' <div id="pager_template" class="pager_toClone pager_pageLink">'.$oHTML->getLink('', 'javascript:;', array('onclick' => $sJs)).'</div>';

      //----------------------------------------
      //Displaying pager pages links

      if($nPagerOffset < $nNbPages)
        $sClass = '';
      else
        $sClass = ' hidden ';

      $sPager.= '<div class="pagerNavigationAfter">&nbsp;';

      $sPager.= '<div class="pager_pageLink pager_pageLinkPic'.$sClass.'">';

      if($nNbPages > 10)
      {
        $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/pager_next.png');
        $sPager.= ' <div class="pager_pageLink">'.$oHTML->getLink($sPic, 'javascript:;', array('pagervalue' => ($nPagerOffset+1), 'onmousedown' => 'slidePager(\'#'.$sPagerId.'\', true); ', 'onmouseup' => ' slidePager(\'#'.$sPagerId.'\', true, 0, true); ')).'</div>';
      }
      $sPager.= '</div>';

      $sPager.= '<div class="pager_pageLink pager_pageLinkPic'.$sClass.'">';

      $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/pager_last.png');
      $sPager.= ' <div class="pager_pageLink">'.$oHTML->getLink($sPic, 'javascript:;', array('onclick' => $sJs, 'pagervalue' => $nNbPages)).'</div>';

      $sPager.= '</div>';

      $sPager.= '<div class="floatHack"></div>';
      $sPager.= '</div>';

      $sPager.= '<div class="floatHack"></div>';
      $sPager.= '</div>';
      $sPager.= '</div>';

    }

    //Displaying the row nb combo
    //===========================================================================================

    $sJs = ' pagerSetPageNbResult(\''.$psUrl.'\', '.(int)$bAjaxPager.', $(\'option:selected\',this).val(), \''.$pasUrlOption['ajaxTarget'].'\'); ';

    $sPager.= '<div class="pagerRowSelector pagerDisplay">';
    $sPager.= '<select onchange="'.$sJs.'" ></a>';
    if($this->cnMinResultNb < 10)
      $sPager.= '<option value=\''.$this->cnMinResultNb.'\' '.(($nPagerLimit==$this->cnMinResultNb)?'selected="selected"':'').'>'.$this->cnMinResultNb.' rows</option>';

    $sPager.= '<option value=\'10\' '.(($nPagerLimit==10)?'selected="selected"':'').'>10 '.$this->casText['PAGER_ROWS'].'</option>';
    $sPager.= '<option value=\'25\' '.(($nPagerLimit==25)?'selected="selected"':'').'>25 '.$this->casText['PAGER_ROWS'].'</option>';
    $sPager.= '<option value=\'50\' '.(($nPagerLimit==50)?'selected="selected"':'').'>50 '.$this->casText['PAGER_ROWS'].'</option>';
    $sPager.= '<option value=\'100\' '.(($nPagerLimit==100)?'selected="selected"':'').'>100 '.$this->casText['PAGER_ROWS'].'</option>';
    $sPager.= '<option value=\'200\' '.(($nPagerLimit==200)?'selected="selected"':'').'>200 '.$this->casText['PAGER_ROWS'].'</option>';
    $sPager.= '</select>';
    $sPager.= '</div>';

    $sPager.= '<div class="floatHack"></div>';
    $sPager.= '</div>';

    return $sPager;

  }


  /*
   * $pnResult : nb of results
   * $psUrl : called url when paging up down
   * $pasUrlOption : link options (to be able to manage ajax and callback function
  */
  public function getCompactDisplay($pnResult, $psUrl, $pasUrlOption = array())
  {
    if(!assert('is_integer($pnResult) && !empty($pnResult) && is_array($pasUrlOption)'))
      return '';

    //===========================================================================================
    //===========================================================================================
    //Init
    //===========================================================================================

    if(!$this->cbInitialized)
      $this->initPager();

    //less results than the smallest limit option: no pager displayed
    if($pnResult < $this->cnMinResultNb)
      return '';

    $nPagerOffset = $this->cnPagerOffset;
    $nPagerLimit = $this->cnPagerLimit;

    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'/css/pager.css');

    $oHTML = CDependency::getComponentByName('display');

    $sPagerId = uniqid('pagerId_');
    $bAjaxPager = $oPage->isAjaxUrl($psUrl);

    if(!isset($pasUrlOption['ajaxTarget']))
      $pasUrlOption['ajaxTarget'] = '';

    if(!isset($pasUrlOption['ajaxCallback']))
      $pasUrlOption['ajaxCallback'] = '';

    if($bAjaxPager && empty($pasUrlOption['ajaxTarget']))
    {
      assert('false; //page in ajax mode, but no zone to refresh.');
      return '';
    }


    //Get url and js ready for pager elements
    $asOption['nbresult'] = $this->cnPagerLimit;
    $sUrl = $oPage->addUrlParams($psUrl, $asOption);
    $sPager = '';

    if($pnResult > $nPagerLimit)
    {
      $nNbPages = floor(($pnResult / $nPagerLimit));
      if(($nNbPages * $nPagerLimit) < $pnResult)
        $nNbPages++;

      //----------------------------------------
      //calculating the page range for the pager

      //1. Calculate max value
      $nMax = $nPagerOffset+5;
      if($nMax > $nNbPages)
        $nMax = $nNbPages;

      //2. calculate min from previous max: current -5 -(a few pages if we didn't use some at the end)
      $nMin = ($nPagerOffset -10 - ($nPagerOffset - $nMax));
      if($nMin < 1)
        $nMin = 1;

      //3. Correcting the max
      if(($nPagerOffset - $nMin) < 5)
        $nMax+= (5 - ($nPagerOffset - $nMin));

      if($nMax > $nNbPages)
        $nMax = $nNbPages;


      $nDisplayed = 0;
      if($nPagerOffset < 1)
        $nPagerOffset = 1;
      //----------------------------------------

      $sPagerId = uniqid('pagerId_');

      $sPager = '<div id="'.$sPagerId.'" class="pagerCompact">';

        //----------------------------------------
        //hide first and previous if on first page
        if(($nPagerOffset) <= 1)
          $sClass = ' hidden ';
        else
          $sClass = '';

        $sPager.= '<div class="pagerCompactNavigation">';

        $sPager.= '<div class="pager_pageLink pager_pageLinkPic'.$sClass.'">';
        $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/compact_first.png');
        $sPager.= $oHTML->getLink($sPic, $sUrl.'&pageoffset=1', $pasUrlOption);
        $sPager.= '</div>';

        if(($nPagerOffset-1) > 1)
        {
          $sPager.= '<div class="pager_pageLink pager_pageLinkPic'.$sClass.'">';
          $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/compact_previous.png');
          $sPager.= $oHTML->getLink($sPic, $sUrl.'&pageoffset='.($nPagerOffset-1), $pasUrlOption);
          $sPager.= '</div>';
        }


        $sPager.= '<div class="pagerCompactInfo">';
        $sPager.= '<strong>'.$nPagerOffset.'/'.$nNbPages.'</strong> '.$this->casText['PAGER_PAGES'];
        $sPager.= '</div>';

        //----------------------------------------
        //Displaying pager pages links

        if($nPagerOffset < $nNbPages)
          $sClass = '';
        else
          $sClass = ' hidden ';


        if(($nPagerOffset+1) < $nMax)
        {
          //$sPager.= '<div class="pager_pageLink pager_pageLinkPic'.$sClass.'"><a href="javascript:;" onclick="parent.pagerAction(\''.$this->csSearchForm.'\', '.($nPagerOffset+1).', '.$nPagerLimit.', '.$this->cnSearchtabNb.', \''.$this->csQueryUrl.'\');">';
          $sPager.= '<div class="pager_pageLink pager_pageLinkPic'.$sClass.'">';
          $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/compact_next.png');


          $sPager.= $oHTML->getLink($sPic, $sUrl.'&pageoffset='.($nPagerOffset+1), $pasUrlOption);
          $sPager.= '</div>';
        }

        $sPager.= '<div class="pager_pageLink pager_pageLinkPic'.$sClass.'">';
        $sPic = $oHTML->getPicture($this->getResourcePath().'/pictures/compact_last.png');
        $sPager.= $oHTML->getLink($sPic, $sUrl.'&pageoffset='.$nNbPages, $pasUrlOption);
        $sPager.= '</div>';


        $sPager.= '<div class="floatHack"></div>';
        $sPager.= '</div>';

        $sPager.= '<div class="floatHack"></div>';
        $sPager.= '</div>';
    }

    return $sPager;
  }


}

?>
