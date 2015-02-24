/* basic functions needed all the time in the page */


function in_array(pasArray, p_val)
{
	for(var i = 0, l = pasArray.length; i < l; i++)
  {
		if(pasArray[i] == p_val)
			return true;
	}
	return false;
}

function parse_url(psUrl)
{

  var parse_url = /^(?:([A-Za-z]+):)?(\/{0,3})([0-9.\-A-Za-z]+)(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/;
  var url = 'http://www.ora.com:80/goodparts?q#fragment';

  var result = parse_url.exec(psUrl);
  var names = ['url', 'scheme', 'slash', 'host', 'port', 'path', 'query', 'hash'];
  var asUrl = new Array();
  for (var i = 0; i < names.length; i += 1)
  {
    asUrl[names[i]] = result[i];
  }

  return asUrl;
}


/* Ajax engine  */
var asIncludedFile = {};

function AjaxRequest(psUrl, psLoadingScreen, psFormToSerialize, psZoneToRefresh, pbReloadPage,  pbSynch, psCallback, pbWithAnimation)
{
  if(!psZoneToRefresh)
    psZoneToRefresh = '';

  if(!psLoadingScreen)
    psLoadingScreen = false;

  if(!pbReloadPage)
    pbReloadPage = false;

  if(!psZoneToRefresh)
    psZoneToRefresh = '';

  if(!psFormToSerialize)
    psFormToSerialize = '';

  if(!pbSynch)
    pbSynch = 'false';

  if(!pbWithAnimation)
    pbWithAnimation = 'false';

  //disable all functionnality while the request is processing
  if(psLoadingScreen)
  {
    $(document).ajaxSend(function(){setLoadingScreen(psLoadingScreen, true, pbWithAnimation);});
    $(document).ajaxError(function(){setLoadingScreen(psLoadingScreen, false, pbWithAnimation);alert('Oops, an error occured while processing your last action. Please reload the page and contact the adinistrator if the problem persists.');});
  }

  sExtraParams = '';
  if(psFormToSerialize != '')
  {
    sExtraParams = $('#'+psFormToSerialize).serialize();
    //alert('serialize form: '+sExtraParams);
  }


  if(psZoneToRefresh == '')
  {
    if(pbReloadPage)
    {
      //No refresh + reload after execution
      $.ajax({
        type: 'POST',
        data: sExtraParams,
        url: psUrl,
        scriptCharset: "utf-8" ,
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        context: document.body,
        success: function(sURL)
        {
          $(document).load(sURL);

          if(oJsonData.error)
            setPopup(oJsonData.error, '', '', 150, 400);
        },
        async: pbSynch,
        dataType: "JSON"
      });
    }
    else
    {
      //No refresh + callback or action from json
      $.ajax({
        type: 'POST',
        data: sExtraParams,
        url: psUrl,
        scriptCharset: "utf-8" ,
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        context: document.body,
        success: function(oJsonData)
        {
          if(!oJsonData)
            oJsonData = {error:"An unknown error occured"};

          if(oJsonData.error)
          {
            setPopup(oJsonData.error,'', '', 150, 400);
            $(document).ajaxSuccess().unbind();

            //requested by server
            if(oJsonData.action)
              eval(oJsonData.action);

            //requested by browser
            if(psCallback)
              eval(psCallback);
          }
          else
          {
            //0- include extra css or js files
            if(oJsonData.cssfile)
            {
              asFile = checkCssToInclude(oJsonData.cssfile);
              for(var sKey in asFile)
                $('head').append('<link rel="stylesheet" href="'+asFile[sKey]+'" type="text/css" />');
            }

            if(oJsonData.js)
              $('head').append('<script type="text/javascript">'+oJsonData.js+'</script>');

            if(oJsonData.jsfile)
            {
              asFile = checkJsToInclude(oJsonData.jsfile);
              console.log('have to load: ');
              console.log(asFile);

              yepnope({load: asFile,
              complete: function ()
              {
                console.log('all files loaded');
                jsonEngine_noRefresh(oJsonData, psUrl, psLoadingScreen, psFormToSerialize, psZoneToRefresh, pbReloadPage,  pbSynch, psCallback, pbWithAnimation);
              }});
            }
            else
              jsonEngine_noRefresh(oJsonData, psUrl, psLoadingScreen, psFormToSerialize, psZoneToRefresh, pbReloadPage,  pbSynch, psCallback, pbWithAnimation);
          }

          //6- Remove the loading screen
          setLoadingScreen(psLoadingScreen, false);
        },
        async: pbSynch,
        dataType: "JSON"
      });
    }
  }
  else
  {

   //Refresh a part of the page + callback or action from json
    $.ajax({
      type: 'POST',
      data: sExtraParams,
      url: psUrl,
      scriptCharset: "utf-8" ,
      contentType: "application/x-www-form-urlencoded; charset=UTF-8",
      context: document.body,
      success: function(oJsonData)
      {
        // pager Issue here
        if(!oJsonData)
          oJsonData = {notice:"An  unknown error occured"};

        if(oJsonData.error)
        {
          $('#'+psZoneToRefresh).html(oJsonData.error);
          $('#'+psZoneToRefresh).addClass('notice3');
          //6- Remove the loading screen
          setLoadingScreen(psLoadingScreen, false);
        }
        else
        {
          //0- include extra css or js files
          if(oJsonData.cssfile)
          {
            asFile = checkCssToInclude(oJsonData.cssfile);
            for(var sKey in asFile)
              $('head').append('<link rel="stylesheet" href="'+asFile[sKey]+'" type="text/css" />');
          }

          if(oJsonData.js)
            $('head').append('<script type="text/javascript">'+oJsonData.js+'</script>');

          if(oJsonData.jsfile)
          {
            asFile = checkJsToInclude(oJsonData.jsfile);
            console.log('have to load: ');
            console.log(asFile);

            yepnope({load: asFile,
            complete: function ()
            {
              console.log('all files loaded');
              jsonEngine_withRefresh(oJsonData, psUrl, psLoadingScreen, psFormToSerialize, psZoneToRefresh, pbReloadPage,  pbSynch, psCallback, pbWithAnimation);
            }});
          }
          else
            jsonEngine_withRefresh(oJsonData, psUrl, psLoadingScreen, psFormToSerialize, psZoneToRefresh, pbReloadPage,  pbSynch, psCallback, pbWithAnimation);
        }
      },
      error: function(oJsonData)
      {
        //send the result to the error report form, and display the error message
        $('#dumpId').val('['+ sExtraParams +'] ['+psUrl +'] ['+pbSynch +']');
        $('#ajaxErrorContainerId').show();
      },
      async: pbSynch,
      dataType: "JSON"
    });
  }

  $(document).unbind('ajaxError');
  $(document).unbind('ajaxSend');
}

//
function jsonEngine_noRefresh(oJsonData, psUrl, psLoadingScreen, psFormToSerialize, psZoneToRefresh, pbReloadPage,  pbSynch, psCallback, pbWithAnimation)
{
  //2- Alert the message
  if(oJsonData.alert)
    alert(oJsonData.alert);

  //--------------------------------------------------
  //Display

  //Message: display a popup in the middle of the screen
  if(oJsonData.message)
  {
    if(oJsonData.timedUrl)
    {
      setPopup(oJsonData.message, oJsonData.timedUrl);
      oJsonData.timedUrl = null;
    }
    else
      setPopup(oJsonData.message);
  }

  //Notice: display a message at the bottom left of the screen : redirect or hiding automatic
  if(oJsonData.notice)
    setNotice(oJsonData);
  else
  {
    //--------------------------------------------------
    //Redirections

    if(oJsonData.url)
      document.location.href = oJsonData.url;

    if(oJsonData.reload)
      window.location.reload();

    if(oJsonData.timedUrl)
      setTimeout("document.location.href = '"+oJsonData.timedUrl+"'; ", 3000);

    if(oJsonData.timedReload)
      setTimeout("window.location.reload();", 3000);
  }

  //--------------------------------------------------
  //Action to be done once the ajax request has been done

  //requested by server
  if(oJsonData.action)
    eval(oJsonData.action);

  //requested by browser
  if(psCallback)
     eval(psCallback);
}

function jsonEngine_withRefresh(oJsonData, psUrl, psLoadingScreen, psFormToSerialize, psZoneToRefresh, pbReloadPage,  pbSynch, psCallback, pbWithAnimation)
{
  //2- Alert the message
  if(oJsonData.alert)
    alert(oJsonData.alert);


  //3- Display message from server (in the refresh zone)
  if(oJsonData.message)
  {
    $('#'+psZoneToRefresh).html(oJsonData.message);
    $('#'+psZoneToRefresh).attr('class', 'notice');
  }

  //4- Reload the page
  if(oJsonData.reload)
  {
    window.location.reload();
  }


  if(oJsonData.data)
    $('#'+psZoneToRefresh).html(oJsonData.data);

  //Notice: display a message at the bottom left of the screen : redirect or hiding automatic
  if(oJsonData.notice)
    setNotice(oJsonData);
  else
  {
    //--------------------------------------------------
    //Redirections

    if(oJsonData.url)
      document.location.href = oJsonData.url;

    if(oJsonData.reload)
      window.location.reload();

    if(oJsonData.timedUrl)
      setTimeout("document.location.href = '"+oJsonData.timedUrl+"'; ", 3000);

    if(oJsonData.timedReload)
      setTimeout("window.location.reload();", 3000);
  }

  //- Execute action ask from server
  if(oJsonData.action)
    eval(oJsonData.action);


  //5- Execute callback requested after the ajax query
  if(psCallback)
     eval(psCallback);

   setLoadingScreen(psLoadingScreen, false);
}


function AjaxPopup(psUrl, psLoadingScreen, pbSynch, psHeight, psWidth, pbNoFooter)
{
  if(!psLoadingScreen)
    psLoadingScreen = false;

  if(!pbSynch)
    pbSynch = 'false';

  //disable all functionnality by displaying a grey layer while the request is processing
  if(psLoadingScreen)
  {
    $(document).ajaxSend(function(){setLoadingScreen(psLoadingScreen, true);});
    $(document).ajaxError(function()
    {
      setPopup("Oops, the page couldn't get loaded. Please try again.");
      setLoadingScreen(psLoadingScreen, false);
    });
  }

  $.ajax({
    type: 'POST',
    url: psUrl,
    context: document.body,
    success: function(oJsonData)
    {
      //0- include extra css or js files
      if(oJsonData.cssfile)
      {
        asFile = checkCssToInclude(oJsonData.cssfile);
        for(var sKey in asFile)
          $('head').append('<link rel="stylesheet" href="'+asFile[sKey]+'" type="text/css" />');
      }

      if(oJsonData.jsfile)
      {
        asFile = checkJsToInclude(oJsonData.jsfile);
        console.log('have to load: ');
        console.log(asFile);
      }

      if(asFile.length)
      {
        yepnope({load: asFile,
        complete: function ()
        {
          console.log('all files loaded');
          jsonEngine_Popup(oJsonData, psUrl, psLoadingScreen, pbSynch, psHeight, psWidth, pbNoFooter);

        }});
      }
      else
        jsonEngine_Popup(oJsonData, psUrl, psLoadingScreen, pbSynch, psHeight, psWidth, pbNoFooter);

    },
    async: pbSynch,
    dataType: "JSON"
  });

  $(document).unbind('ajaxError');
  $(document).unbind('ajaxSend');

}

/**
 * check the list of js files requested to be included in the page,
 * Return an array with the ones that are not already included
 * Log the files in the global array
 */
function checkJsToInclude(pasFiles)
{
  var asFile = new Array();
  for(var sKey in pasFiles)
  {
    asUrl = parse_url(pasFiles[sKey]);
    //console.log(asUrl['path']);
    if(!in_array(gasJsFile, asUrl['path']))
    {
      asFile.push(pasFiles[sKey]);
      gasJsFile.push(asUrl['path']);
    }
  }
  return asFile;
}

function checkCssToInclude(pasFiles)
{
  var asFile = new Array();
  for(var sKey in pasFiles)
  {
    asUrl = parse_url(pasFiles[sKey]);
    if(!in_array(gasCssFile, asUrl['path']))
    {
      asFile.push(pasFiles[sKey]);
      gasCssFile.push(asUrl['path']);
    }
  }

  return asFile;
}


function insertJs(psContent)
{
  //http://www.sencha.com/forum/showthread.php?100865-Execute-an-include-js-file-from-ajax

  var head = document.getElementsByTagName("head")[0];
  var script = document.createElement("script");
  script.type = 'text/javascript';
  script.text = psContent;
  head.appendChild(script);
}

function jsonEngine_Popup(oJsonData, psUrl, psLoadingScreen, pbSynch, psHeight, psWidth, pbNoFooter)
{
  console.log('do popup stuff');
  if(oJsonData.data)
  {
    setPopup(oJsonData.data, '', '', psHeight, psWidth, pbNoFooter);
    console.log('content added');

    // once the content and the file have been included, i fire the js
    if(oJsonData.js)
      $('head').append('<script type="text/javascript">'+oJsonData.js+'</script>');
  }
  else
  {
    setPopup("Oops, the page couldn't get loaded. Please try again.");
  }
}

function setPopup(psMessage, psUrl, psClass, psHeight, psWidth, pbNoFooter)
{
  //add lock so the loadingscreen doesnt disappear if any other function play with it
  nLockScreenOn = true;

  if(psUrl)
    setTimeout('document.location.href = "'+psUrl+'"; ', 2250);

  setLoadingScreen('body', true, false);

  jQuery('#popupInnerContainer').html(psMessage);

  if(psMessage)
  {
    if(!psClass)
      psClass = 'standardPopup';

    sStyle = '';

    if(psHeight)
      sStyle = sStyle+' min-height:'+psHeight+'px;';
    else
      sStyle = sStyle+' min-height:50px;';

    if(psWidth)
      sStyle = sStyle+' min-width:'+psWidth+'px;';
    else
      sStyle = sStyle+' width:100%;';
      //sStyle = sStyle+' width:'+ ($('#popupInnerContainer').width()+100)+'px;';

    $('#popupInner').attr('style', sStyle);
    $('#popupInner').addClass(psClass);

    if(pbNoFooter)
    {
      $('#popupCloseBottom').hide();
    }

    $('#popupContainer').show();
  }
}

function removePopup()
{
  //hide the popup, remove all class , release the loadingScreen lock, and hide the loadingscreen if it was set
  $('#popupContainer').fadeOut(function(){$('#popupContainer').attr('class', '');nLockScreenOn = false;setLoadingScreen('', false);$('#popupCloseBottom').show();});
}


/**
 *Display a notice at the bottom right of the page. Use setPopup adding extra class
 **/
function setNotice(asData)
{
  $('#popupContainer').addClass('notifierPopup');

  if(asData.url)
    return setPopup(asData.notice, asData.url, 'noticeBloc');

  if(asData.timedUrl)
    return setPopup(asData.notice, asData.timedUrl, 'noticeBloc');

  if(asData.reload || asData.timedReload)
    return setPopup(asData.notice, document.URL, 'noticeBloc')

  setPopup(asData.notice,'','noticeBloc');
  setTimeout('removePopup(); ', 2250);

  return true;
}


nLockScreenOn = false;
nLockScreenOff = false;
function setLoadingScreen(psSelector, pbSetup, pbWithAnimation)
{
  oHtmlElement = $(psSelector);
  oHiddenDiv = $('#loadingScreen');

  if(pbSetup && !nLockScreenOff)
  {
    nHeight = oHtmlElement.height();
    nWidth = oHtmlElement.width();
    oPosition = oHtmlElement.offset();
    oHiddenDiv.attr('style', 'width:'+nWidth+'; height:'+nHeight+'px; top:'+oPosition.top+'px; left:'+oPosition.left);

    if(pbWithAnimation == false)
       $('#loadingScreenAnimation').hide();
    else
      $('#loadingScreenAnimation').attr('style', 'margin:150px auto 0;');

    oHiddenDiv.show();
    return true;
  }

  if(!nLockScreenOn)
  {
    oHiddenDiv.hide();
  }
}


/*
 * ============================================================================
 * ============================================================================
 * ============================================================================
 * ============================================================================
 */

var oEmbedTimer;

function getEmbedOption(psFieldId)
{
  oEmbedTimer = setTimeout('displayEmbedLink("'+psFieldId+'");', '1250');
}
function clearEmbedOption(psFieldId)
{
  clearTimeout(oEmbedTimer);
  displayEmbedLink(psFieldId, true)
}

function displayEmbedLink(psFieldId, pbRemove)
{
  $('#embedPopupId').clearQueue();

  if(pbRemove)
  {
    $('#embedPopupId').fadeOut().html('');
  }
  else
  {
    oLinkPosition = $('#'+psFieldId).position();
    $('#embedPopupId').attr('style', 'top:'+(oLinkPosition.top+20)+'; left:'+(oLinkPosition.left + 15)+';');
    $('#embedPopupId').html('2 links').fadeIn();
  }
}

/**
 * Hide all elements having the parameter class, and display the element with the parameter id
 *
 **/

function toggleBlocks(psClassToHide, psIdToDisplay)
{
  $('.' + psClassToHide).slideUp('fast', function()
  {
      $('#' + psIdToDisplay).slideDown();
  });
}



function SwitchFullSearch()
{

	   $JQ('#topPageId > *').toggle('fast', function(){
		     $JQ('#personnel-FRM').animate({height:'1000px'}, 1200);
	   });
}

function toggleDisplay(psElementId, pvSpeed)
{
  if(!pvSpeed)
    pvSpeed = 'slow';

  /*$JQ('#'+psElementId).toggle(pvSpeed);*/
  $JQ('#'+psElementId).fadeToggle(pvSpeed);
  /*$JQ('#'+psElementId).slideToggle(pvSpeed);*/
}


function toggleImage(psImageSelector, psMode)
{
  oImage = $(psImageSelector);

  if(!oImage || oImage == undefined)
    return false;

  if(!psMode)
  {
		//autodetect which one to display
		sNewSrc = $(oImage).attr('imgDisplay');
		sCurrentSrc = $(oImage).attr('src');

		if(sCurrentSrc == sNewSrc)
		  sNewSrc = $(oImage).attr('imgHidden');
	}
	else
	{
		if(psMode == 'view')
		  sNewSrc = $(oImage).attr('imgDisplay');
		else
		  sNewSrc = $(oImage).attr('imgHidden');
	}


  $(oImage).fadeOut(function()
  {
    $(oImage).attr('src', sNewSrc).delay(10).fadeIn();
  });

}

/*
function customRotate(sSelector, nAngle, sRotateArg)
{

  nStep = (parseInt(nAngle) / 10);
  for(nCount = 1; nCount < 10; nCount++)
  {
    nNewAngle = (nCount*nStep)
    //alert(nNewAngle);
    $(sSelector).delay(250).rotate(nNewAngle);
  }

  //$(sSelector).rotate(nAngle);
}*/


function zoomPicture(psPicSelector)
{
  oPic = $(psPicSelector);
  sPicPath = oPic.attr('src');


  setLoadingScreen('#body', true, false);

  setPopup('<img src="'+sPicPath+'" style="max-width:1024; max-height:900;" />');

  /*  $('#loadingScreenContainer').html('');
  $('#loadingScreenContainer').attr('style', 'height:'+ $('#loadingScreenContainer').height()+'px; width:'+$('#loadingScreenContainer').width()+'px;');
  $('#loadingScreenContainer').show();*/

}

function pagerGetPage(oCurrentElement,psUrl, pnIsAjax, psRefreshZone)
{
  var sElementValue = $(oCurrentElement).attr('pagervalue');

  if(sElementValue)
    var nPageOffset = parseInt(sElementValue);
  else
    var nPageOffset = parseInt($(oCurrentElement).html());

  psUrl = psUrl+'&pageoffset='+nPageOffset;

  if(pnIsAjax)
  {
    if(!psRefreshZone)
      return alert('Ajax pager, but no bloc to refresh ');

   //   alert(psRefreshZone);

    AjaxRequest(psUrl, '', '', psRefreshZone);
  }
  else
  {
    document.location.href = psUrl;
  }
}

function pagerSetPageNbResult(psUrl, pnIsAjax, pnNbResult, psRefreshZone)
{
  psUrl = psUrl+'&nbresult='+pnNbResult;

  if(pnIsAjax)
  {
    if(!psRefreshZone)
      return alert('Ajax pager (set results), but no bloc to refresh ');

    AjaxRequest(psUrl, '', '', psRefreshZone);
  }
  else
  {
    document.location.href = psUrl;
  }
}


var oPagerTimer;
var bPagerStop;
function slidePager(psCurrentPager, pbNext, pnTime, pbStop, pbRecursiveCall)
{
  oPager = jQuery(psCurrentPager);

  if(pbRecursiveCall && bPagerStop === true)
    return true;

  if(pbStop)
  {
    bPagerStop = true;
    clearTimeout(oPagerTimer);
    return true;
  }

  var nNbElement = 9;
  var nPages = parseInt(jQuery('.pagerNavigationNumbers', oPager).attr('nbpages'));
  var nCurrent = parseInt(jQuery('.pagerNavigationNumbers', oPager).attr('currentpage'));
  var nMaxDisplayed = parseInt(jQuery('.pagerNavigationNumbers', oPager).attr('maxdisplayed'));
  var nMinDisplayed = parseInt(jQuery('.pagerNavigationNumbers', oPager).attr('mindisplayed'));

  if(parseInt(nPages) == 1)
    return true;

  //going forward increasing page number
  if(pbNext && nPages && nCurrent)
  {
    nMin = (nMaxDisplayed+1);
    if(nMin >= nPages)
      return true;

    nMax = (nMaxDisplayed+1+nNbElement);
    nPagedUp = false;

    jQuery('.pagerNavigationbefore .pager_pageLinkPic', oPager).fadeIn();
    for(nCount = nMin; nCount <= nMax; nCount++)
    {
      if(nCount <= nPages)
      {
        nPagedUp = true;
        jQuery('.pagerNavigationNumbers div:first', oPager).remove();
        var oElem = jQuery('.pager_toClone', oPager).clone();
        jQuery(oElem).removeClass('pager_toClone');

        if(nCount == nCurrent)
          jQuery(oElem).addClass('pager_CurrentPage');

        if(nCount > 9999)
          jQuery(oElem).addClass('pagerSmaller');

        jQuery('a', oElem).html(nCount);
        oElem.appendTo(jQuery('.pagerNavigationNumbers', oPager));
      }
    }

    if(nPagedUp)
    {
      jQuery('.pagerNavigationNumbers', oPager).attr('maxdisplayed', nMax);
      jQuery('.pagerNavigationNumbers', oPager).attr('mindisplayed', nMin);
      //get to the last page
      if(nCount >= nPages)
        jQuery('.pagerNavigationAfter .pager_pageLinkPic', oPager).fadeOut();
    }
  }

  //Going backward, decreasing the page number
  if(!pbNext && nPages && nCurrent)
  {
    nPagedDown = false;
    nMax = (nMinDisplayed-1);
    if(nMax < 1)
      return true;

    nMin = (nMinDisplayed-1-nNbElement);
    //console.log('from '+nMax+' to '+nMin+' <br />');

    jQuery('.pagerNavigationAfter .pager_pageLinkPic', oPager).fadeIn();
    for(nCount = nMax; nCount >= nMin; nCount--)
    {
      if(nCount > 0)
      {
        nPagedDown = true;
        jQuery('.pagerNavigationNumbers div:last', oPager).remove();
        var oElem = jQuery('.pager_toClone', oPager).clone();

        jQuery(oElem).removeClass('pager_toClone');
        if(nCount == nCurrent)
          jQuery(oElem).addClass('pager_CurrentPage');

        if(nCount > 9999)
          jQuery(oElem).addClass('pagerSmaller');

        jQuery('a', oElem).html(nCount);
        oElem.prependTo(jQuery('.pagerNavigationNumbers', oPager));
      }
    }

    if(nPagedDown)
    {
      jQuery('.pagerNavigationNumbers', oPager).attr('maxdisplayed', nMax);
      jQuery('.pagerNavigationNumbers', oPager).attr('mindisplayed', nMin);

      //get to the first page
      if(nCount <= 1)
        jQuery('.pagerNavigationBefore .pager_pageLinkPic', oPager).fadeOut();
    }
  }

  if(!pbStop)
  {
    if(!pnTime || pnTime == 0)
      pnTime = 500;

    if(parseInt(pnTime) >= 100)
      pnTime = parseInt(pnTime, 10) - 50;

    bPagerStop = false;
    setTimeout("slidePager('"+psCurrentPager+"', "+pbNext+", "+pnTime+", false, true); ", pnTime);
  }

  return true;
}
var oPagerTimer;
var bPagerStop;
function slidePager(psCurrentPager, pbNext, pnTime, pbStop, pbRecursiveCall)
{
  oPager = jQuery(psCurrentPager);

  if(pbRecursiveCall && bPagerStop === true)
    return true;

  if(pbStop)
  {
    bPagerStop = true;
    clearTimeout(oPagerTimer);
    return true;
  }

  var nNbElement = 9;
  var nPages = parseInt(jQuery('.pagerNavigationNumbers', oPager).attr('nbpages'));
  var nCurrent = parseInt(jQuery('.pagerNavigationNumbers', oPager).attr('currentpage'));
  var nMaxDisplayed = parseInt(jQuery('.pagerNavigationNumbers', oPager).attr('maxdisplayed'));
  var nMinDisplayed = parseInt(jQuery('.pagerNavigationNumbers', oPager).attr('mindisplayed'));

  if(parseInt(nPages) == 1)
    return true;

  //going forward increasing page number
  if(pbNext && nPages && nCurrent)
  {
    nMin = (nMaxDisplayed+1);
    if(nMin >= nPages)
      return true;

    nMax = (nMaxDisplayed+1+nNbElement);
    nPagedUp = false;

    jQuery('.pagerNavigationbefore .pager_pageLinkPic', oPager).fadeIn();
    for(nCount = nMin; nCount <= nMax; nCount++)
    {
      if(nCount <= nPages)
      {
        nPagedUp = true;
        jQuery('.pagerNavigationNumbers div:first', oPager).remove();
        var oElem = jQuery('.pager_toClone', oPager).clone();
        jQuery(oElem).removeClass('pager_toClone');

        if(nCount == nCurrent)
          jQuery(oElem).addClass('pager_CurrentPage');

        if(nCount > 9999)
          jQuery(oElem).addClass('pagerSmaller');

        jQuery('a', oElem).html(nCount);
        oElem.appendTo(jQuery('.pagerNavigationNumbers', oPager));
      }
    }

    if(nPagedUp)
    {
      jQuery('.pagerNavigationNumbers', oPager).attr('maxdisplayed', nMax);
      jQuery('.pagerNavigationNumbers', oPager).attr('mindisplayed', nMin);
      //get to the last page
      if(nCount >= nPages)
        jQuery('.pagerNavigationAfter .pager_pageLinkPic', oPager).fadeOut();
    }
  }

  //Going backward, decreasing the page number
  if(!pbNext && nPages && nCurrent)
  {
    nPagedDown = false;
    nMax = (nMinDisplayed-1);
    if(nMax < 1)
      return true;

    nMin = (nMinDisplayed-1-nNbElement);
    //console.log('from '+nMax+' to '+nMin+' <br />');

    jQuery('.pagerNavigationAfter .pager_pageLinkPic', oPager).fadeIn();
    for(nCount = nMax; nCount >= nMin; nCount--)
    {
      if(nCount > 0)
      {
        nPagedDown = true;
        jQuery('.pagerNavigationNumbers div:last', oPager).remove();
        var oElem = jQuery('.pager_toClone', oPager).clone();

        jQuery(oElem).removeClass('pager_toClone');
        if(nCount == nCurrent)
          jQuery(oElem).addClass('pager_CurrentPage');

        if(nCount > 9999)
          jQuery(oElem).addClass('pagerSmaller');

        jQuery('a', oElem).html(nCount);
        oElem.prependTo(jQuery('.pagerNavigationNumbers', oPager));
      }
    }

    if(nPagedDown)
    {
      jQuery('.pagerNavigationNumbers', oPager).attr('maxdisplayed', nMax);
      jQuery('.pagerNavigationNumbers', oPager).attr('mindisplayed', nMin);

      //get to the first page
      if(nCount <= 1)
        jQuery('.pagerNavigationBefore .pager_pageLinkPic', oPager).fadeOut();
    }
  }

  if(!pbStop)
  {
    if(!pnTime || pnTime == 0)
      pnTime = 500;

    if(parseInt(pnTime) >= 100)
      pnTime = parseInt(pnTime, 10) - 50;

    bPagerStop = false;
    setTimeout("slidePager('"+psCurrentPager+"', "+pbNext+", "+pnTime+", false, true); ", pnTime);
  }

  return true;
}

function showEmailPopup(oLinkElement)
{
  var oPosition = $(oLinkElement).offset();
  if(!oPosition)
    return null;

  $('.emailpopup').stop();
  $('.emailpopup').clearQueue();

  var oLink = $(oLinkElement).clone();
  var sEmail = $(oLink).html();
  oLink.html('BCM mail');


  $('#body').append('<div class="emailpopup" onmouseout="hideEmailPopup();"><div style="width:200px;">Sent your email using:</div> <div><a href="mailto:'+sEmail+'">Local client</a></div><div class="webMailLinkDiv"></div></div>');
  $('.webMailLinkDiv').append(oLink);

  $('.emailpopup').attr('style', 'position:absolute; top:'+oPosition.top+'px; left:'+oPosition.left+'px;');
  $('.emailpopup').fadeIn();
}


function hideEmailPopup()
{
  $('.emailpopup').delay(1500).fadeOut(1000, function(){$('.emailpopup').html('');$('.emailpopup').remove();});
}

function showActivityPopup(oElement)
{
  var oPosition = $(oElement).offset();
  if(!oPosition)
    return null;

   $('#body').append('<div class="activityPopup" "></div>');
   $('.activityPopup').attr('style', 'position:absolute; top:'+oPosition.top+'px; left:'+(parseInt(oPosition.left) -350)+'px;');
   $('.activityPopup').html($(oElement).parent().attr('title'));
   $('.activityPopup').css('display:block');

}

function hideActivityPopup()
{
    $('.activityPopup').delay(1000).fadeOut(1000, function(){$('.activityPopup').html('');$('.activityPopup').remove();});

}

function reloadPage(url)
{
    window.location=url;
}

function setCoverScreen(pbSetup, pbWithAnimation)
{
  if(pbSetup)
  {
    nWidth = jQuery(document).width();
    nHeight = jQuery(document).height();
    if(pbWithAnimation)
    {
      jQuery('<div id="coverScreen" style=" width:'+nWidth+'px;height:'+nHeight+'px;"></div>').appendTo('body');
      jQuery('<div id="coverScreenPic" style=" width:'+nWidth+'px;"><div><img src="'+$('#loadingScreenAnimation img').attr('src')+'" border=0 /></div></div>').appendTo('body');
    }
    else
      jQuery('<div id="coverScreen" style=" width:'+nWidth+'px;height:'+nHeight+'px; "></div>').appendTo('body');

    jQuery('#coverScreen').fadeIn(150, function(){jQuery('#coverScreenPic ').fadeIn(250);});
  }
  else
  {
    jQuery('#coverScreen, #coverScreenPic').fadeOut(150, function(){jQuery('#coverScreen').remove();});
  }
}

function getLoadingLink(psUrl)
{
  setCoverScreen(true, true);
  setTimeout('document.location.href = "'+psUrl+'"; ', 700);
}


function resetContactSearch()
{
  $("#queryFormId").find(':input').each(function() {

        switch(this.type) {
	            case 'text':
	            case 'textarea':
               	case 'select-one':
                case 'select-multiple':

                  $(this).val('');
	                break;
	         }
      });

    $("#queryFormId .autocompleteField").tokenInput("clear").blur();
    $('.bsmSelect option:disabled').removeClass('bsmOptionDisabled');
    $('.bsmSelect option:disabled').removeAttr('disabled');
    $('#contact_industryId option:selected').removeAttr('selected');
    $('.bsmListItem').remove();

 }

function resetCompanySearch()
{
  $("#queryFormId").find(':input').each(function() {
        switch(this.type) {
	            case 'text':
	            case 'textarea':
	                $(this).val('');
	                break;
	         }
      });
  $("#queryFormId .autocompleteField").tokenInput("clear").blur();

 }

function showHide(displaytext,hidetext,display,hide)
{
  $('#'+displaytext).show();
  $('#'+hidetext).hide();

  $('#'+display).show();
  $('#'+hide).hide();
}


function resetJobSearch()
{
  $("#advSearchFormId").find(':input').each(function()
  {
    switch(this.type)
    {
        case 'text':
        case 'textarea':
        case 'select-multiple':
        case 'select-one':
        case 'select':

        $(this).val('');
          break;
      }

      $("#industry_treeId").val('');

     mRange = $("#salary_monthId").attr("default");
     yRange = $("#salary_yearId").attr("default");
     hRange = $("#salary_hourId").attr("default");

     $("#salary_monthId").val(mRange);
     $("#salary_yearId").val(yRange);
     $("#salary_hourId").val(hRange);

    });

   $("form[name=advSearchForm] input[type=submit]").click();
   searchFormToggle(true);
}

/**
 * Comment
 */
function searchFormToggle(pbForceDisplay)
{
  //if the form is not anchored at the top, ignore the dummy form
  var bIgnoreDummy = jQuery('.jobLeftSectionInner').hasClass('menuFloating');

  if(bIgnoreDummy)
    jQuery('.jobDummySearchForm:visible').fadeOut(50);

  if(pbForceDisplay === false)
  {
    //form visible => we hide it
    if(bIgnoreDummy)
      jQuery('.jobSearchContainer:visible').fadeOut();
    else
      jQuery('.jobSearchContainer:visible, .jobDummySearchForm:visible').fadeOut();
    return true;
  }

  if(pbForceDisplay === false)
  {
    //form visible => we hide it
    if(bIgnoreDummy)
      jQuery('.jobSearchContainer:not(:visible)').fadeIn();
    else
      jQuery('.jobSearchContainer:not(:visible), .jobDummySearchForm:not(:visible)').fadeIn();
    return true;
  }

  if(bIgnoreDummy)
  {
    jQuery('.jobSearchContainer').fadeToggle();
  }
  else
  {
    jQuery('.jobSearchContainer, .jobDummySearchForm').fadeToggle();
  }
  return true;
}

function showHideSalary(value)
{
  if(value==1)
  {
    $("#salary").css('display','block');
    $("#salary_hourId").closest('div .formFieldContainer').hide();
    $("#salary_monthId").closest('div .formFieldContainer').show();

  }
  else
  {
    $("#salary").css('display','none');
    $("#salary_hourId").closest('div .formFieldContainer').show();
    $("#salary_monthId").closest('div .formFieldContainer').hide();
    $("#salary_yearId").closest('div .formFieldContainer').hide();
   }
}

function displaySalary(value)
{
  if(value==0)
   {
    $("#salary_monthId").closest('div .formFieldContainer').show();
    $("#salary_yearId").closest('div .formFieldContainer').hide();
   }
  else
   {
     $("#salary_yearId").closest('div .formFieldContainer').show();
     $("#salary_monthId").closest('div .formFieldContainer').hide();
    }
 }

 function submitForm(oElement)
 {
    var industrypk = $(oElement).attr("industrypk");
    var industryname = $(oElement).attr("industryname");
    var companypk = $(oElement).attr("companypk");
    var companyname = $(oElement).attr("companyname");

    $('input[name=industrypk]').val(industrypk);
    $('input[name=industryname]').val(industryname);

    $('input[name=companypk]').val(companypk);
    $('input[name=companyname]').val(companyname);

   $("form[name=hiddenForm] input[type=submit]").click();

 }

 var oFilterTime;

 function clearFilter()
 {
    clearTimeout(oFilterTime);

    jQuery('.filterRemovalLoader:not(:visible)').fadeIn();
    oFilterTime =  setTimeout(function(){$("form[name=advSearchForm]").submit();jQuery('.filterRemovalLoader:visible').fadeOut();},1500);
 }


  function addParameter(oElement)
  {
    var _href = $(oElement).attr("href");
    $(oElement).attr("href", _href + '&settime='+$.now());
  }

