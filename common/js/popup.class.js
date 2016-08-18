$.ui.dialog.prototype._focusTabbable = function(){};

var CPopup = function()
{
  //contain the list of all the popups:  popupId => poPopupConf
  this.caoPopup = {};


  this.getConfig = function(psTagId, psType)
  {
    if(!psTagId)
      psTagId = Math.random().toString(36).substr(4);

    //most common case that require specific settup
    if(!psType)
      psType = 'layer';

    var oPopup =
    {
      //custom BCM
      type: psType,
      tag: psTagId,

      contentTag: null,
      persistent: false,
      forceRefresh: false,

      //jquery-ui settings
      title: null,
      buttons: {},

      width: 380,
      height: 190,
      maxHeight:780,
      maxWidth:1380,
      minHeight:40,
      minWidth:70,
      position: {},

      autoOpen: true,
      modal: true,
      closeOnEscape: true,
      closeText: '',
      dialogClass: '',
      draggable: true,
      resizable: false,

      hide: {effect: 'fade', delay: 0, duration: 100},
      show:{effect: 'fade', delay: 0, duration: 250},
      //events:
      close: function() { goPopup.remove(this); }
    };

    return oPopup;
  };

  this.remove = function(pvPopup, pbForce)
  {
    //console.log('try to remove ' + pvPopup);

    //if string ==> popupID, else the dialog object
    if(typeof pvPopup === "string")
      pvPopup = '#'+pvPopup;

    if(!$(pvPopup).hasClass('ui-dialog-content'))
    {
      //console.log('Dialog ['+$(pvPopup).attr('id')+'] doesn\'t have html tag ready. In the array listing popups (next row)?');
      //console.log(this.caoPopup);
      return false;
    }

    var sTag = $(pvPopup).attr('id');
    $(pvPopup).dialog('close');
    $(pvPopup).closest('.ui-dialog').hide();
    //console.log('close popup: [#'+sTag+' is a '+pvPopup+'] ');


    if(pbForce === true || !$(pvPopup).dialog('option', 'persistent'))
    {
      $(pvPopup).dialog('destroy');
      delete this.caoPopup[sTag];

      //remove the html tags created for the popup
      $(pvPopup).remove();

      console.log('removed/destroyed '+sTag);
      //console.log(this.caoPopup);

      return true;
    }
    /*else
    {
      console.log('layer not deleted!! persistent layer ? '+$(pvPopup).dialog('option', 'persistent')+'  // forcing ? '+pbForce);
    }*/


    //save the popup position to re-open it at the same position
    if($(pvPopup).dialog('option', 'draggable'))
    {
      //console.log('Dialog ['+sTag+'] is persistent, kept in array.');
      //console.log(this.caoPopup);

      //save throw new Error("position of throw new Error("dialog when closed
      //console.log('persistent closed at '+ $(pvPopup).dialog('option', 'position'));
      //console.log( $(pvPopup).dialog('option', 'position'));
      this.caoPopup[sTag].position = $(pvPopup).dialog('option', 'position');

      //console.log(this.caoPopup);
    }

    return true;
  };


  this.removeAll = function(pbForce)
  {
    //$(this.caoPopup).each(function(nIndex, oPopup)
    $.map(this.caoPopup, function(oPopup, sTagId)
    {
      goPopup.remove(oPopup.tag, pbForce);
    });

    this.caoPopup = {};
  };


  this.removeByType = function(psType, pbForce)
  {
    if(!psType)
      return false;

    //$(this.caoPopup).each(function(nIndex, oPopup)
    $.map(this.caoPopup, function(oPopup, sTagId)
    {
      console.log('remove by type:  popup - ['+sTagId+'] '+oPopup.type+' ? param -'+psType);
      if(oPopup.type == psType)
      {
        goPopup.remove(oPopup.tag, pbForce);
      }
    });

    return true;
  };

  this.removeLastByType = function(psType, pbForce)
  {
    if(!psType)
      return false;

    var sTagToRemove = '';

    //can't reverse the array, so we make a full loop and keep last T_T
    $.map(this.caoPopup, function(oPopup, sTagId)
    {
      if(oPopup.type == psType)
      {
        //save tags
        sTagToRemove = oPopup.tag;
        return true;
      }
    });

    console.log('removeLastByType --> tag to remove: '+sTagToRemove);
    this.remove(sTagToRemove, pbForce);
    return true;
  };


  this.create = function(poConf, psContent, pbTagExist)
  {
    if(!poConf)
      return '';

    if(!psContent === undefined || psContent === null)
      psContent = ' &nbsp; ';

    //console.log('creating new dialog : '+poConf.tag);
    //console.log(poConf);

    this.caoPopup[poConf.tag] = poConf;
    //console.log(this.caoPopup);

    if((poConf.title === undefined || poConf.title === null || poConf.title === false))
      poConf.title = '';




    if(!pbTagExist)
      $('<div class="popupMessage hidden" id="'+poConf.tag+'"></div>').html(psContent).appendTo('body');

    $('#'+poConf.tag).dialog(poConf);

    return poConf.tag;
  };




  //last active dialog is always moved at the bottom of the body (z-index related probably)
  this.getActive = function(psType)
  {
    var oDialog = null;
    if(psType)
    {
      switch(psType)
      {
        case 'layer': oDialog = $('.ui-dialog.popup_layer:visible:last'); break;
        case 'notice': oDialog = $('.ui-dialog.popup_layer:visible:last'); break;
        case 'message': oDialog = $('.ui-dialog.popup_message:visible:last'); break;

        default:
          oDialog = $('.ui-dialog:visible:last'); break;
      }
    }


    if(!oDialog)
      return '';

    var sId = $('div.ui-dialog-content', oDialog).attr('id');
    if(!sId)
    {
      //console.log('getActive: no id --> no active dialog ?_?');
      return '';
    }

    var sOpen = $('#'+sId).dialog("isOpen");
    //console.log('get active -> '+sOpen);
    if(!sOpen || sOpen == 'undefined')
      return '';

    return sId;
  };



  //Remove the active popup
  this.removeActive = function(psType)
  {
    var sId = this.getActive(psType);

    if(!sId)
    {
      //can't find active popup, removing the last
      console.log('removeActive --> no active layer --> removeLastByType('+psType+')');
      console.log(this.caoPopup);
      return this.removeLastByType(psType);
    }

    //alert('remove active -> '+sId);
    console.log('removeActive -->'+sId);
    return this.remove(sId);
  };



  //helper to create buttons
  this.addButton = function(psLabel, psAction, pbKeepPopup)
  {
    if(pbKeepPopup)
    {
      var oButton =
      {
        text: psLabel,
        click: function() { eval(psAction); }
      }
    }
    else
    {
      var oButton =
      {
        text: psLabel,
        id: 'close_button',
        click: function()
        {
          eval(psAction);
          $(this).dialog('close');
        }
      }
    }

    return oButton;
  };




  //Change properties of a popup
  this.changeConfig = function(psPopupId, psProperty, psValue)
  {
    if(!this.caoPopup[psPopupId])
      return false;

    //console.log('change config of popup'+ psPopupId);
    this.caoPopup[psPopupId][psProperty] = psValue;
    $('#'+psPopupId).dialog('option', psProperty, psValue);

    //console.log(this.caoPopup[psPopupId]);
    return true;
  };




  /******************************************/
  /******************************************/
  //High level popup functions

  /**
  * Simple message, no html content or buttons
  */
  this.setPopupMessage = function(psMessage, pbModal, psTitle, pnWidth, pnHeight, psClass)
  {
    var oPopup = this.getConfig('', 'msg');
    var prevent_multi_error = false;

    if(pbModal)
      oPopup.modal = true;

    if(psClass)
      oPopup.dialogClass = psClass;

    if (psClass === 'ui-state-error')
    {
      oPopup.open = function() { $("#close_button").focus(); };

      if ($('.ui-state-error').dialog("isOpen") === true)
        prevent_multi_error =  true;
    }

    if(psTitle === undefined || psTitle === null || psTitle === false)
    {
      oPopup.dialogClass+= ' noTitle';
      oPopup.buttons = [this.addButton('close')];
    }
    else
      oPopup.title = psTitle;

    if(pnWidth)
      oPopup.width = pnWidth;

    if(pnHeight)
      oPopup.height = pnHeight;

    oPopup.dialogClass+= ' popup_message';
    if (prevent_multi_error)
      return false;
    else
      return this.create(oPopup, psMessage);
  };

  this.setErrorMessage = function(psMessage, pbModal, psTitle, pnWidth, pnHeight)
  {
    psMessage = psMessage.split("\n").join('<br />');
    return this.setPopupMessage(psMessage, pbModal, psTitle, pnWidth, pnHeight, 'ui-state-error');
  };





  this.setPopupConfirm = function(psMessage, psConfirmedAction, psRefusedAction, psConfirmedBtn, psRefusedBtn, psTitle, pnWidth, pnHeight)
  {
    var oPopup = this.getConfig('', 'confirm');
    oPopup.modal = true;

    if(pnWidth)
      oPopup.width = pnWidth;

    if(pnHeight)
      oPopup.height = pnHeight;

    if(psTitle)
      oPopup.title = psTitle;
    else
      oPopup.dialogClass = 'noTitle';

    if(!psConfirmedBtn)
      psConfirmedBtn = 'Ok';

    if(!psRefusedBtn)
      psRefusedBtn = 'Cancel';

    oPopup.dialogClass += ' popup_confirm';

    oPopup.buttons =
    [
      {
        text: psConfirmedBtn,
        click: function()
        {
          eval(psConfirmedAction);
          goPopup.remove(this);
        }
      },
      {
        text: psRefusedBtn,
        click: function()
        {
          eval(psRefusedAction);
          goPopup.remove(this);
        }
      }
    ];

    oPopup.dialogClass+= ' popup_message';

    return this.create(oPopup, psMessage);
  };



  /**
  * notifier (bottom right), with basic reload/redirect features
  */
  this.setNotice = function(psContent, paAction, pbAnimation, pbModal, pnWidth, pnHeight, psClass)
  {
    var oPopup = this.getConfig('', 'notice');
    oPopup.position = {at: "right bottom"};
    oPopup.closeOnEscape = true;
    oPopup.dialogClass = 'notifierPopup popup_notice';
    oPopup.draggable = false;
    oPopup.resizable = false;

    if(pbModal === true)
      oPopup.modal = true;
    else
      oPopup.modal = false;

    if(pbAnimation !== false)
      oPopup.dialogClass += ' notifierPopupAnimation';

    if(pnWidth)
      oPopup.width = pnWidth;
    else
      oPopup.width = 350;

    if(pnHeight)
      oPopup.height = pnHeight;
    else
      oPopup.height = 60;

    if(psClass)
      oPopup.dialogClass += ' '+psClass;

    var sId = this.create(oPopup, psContent);

    if(!paAction)
      paAction = {};

    //reduce a bit the delay include some time for the page to load
    //500ms added on the timeout below
    if(!paAction.delay)
      paAction.delay = 1000;
    else
      paAction.delay = parseInt(paAction.delay) -500;

    //retro-compatibility
    if(paAction.timedUrl)
      paAction.url = paAction.timedUrl;

    if(paAction.callback)
      eval(paAction.callback);
    else
    {
      if(paAction.url)
      {
        /*/setTimeout("goPopup.remove('"+sId+"'); goPopup.removeActive();", (paAction.delay+500));
        var sPage = window.location.protocol+'//'+window.location.host + window.location.pathname + +window.location.search;

        //if url is the currently displayed page (with or without anchor) => force reload
        if(paAction.url == window.location.href || paAction.url == sPage)
          setTimeout("window.location.reload();", paAction.delay);
        else*/
          setTimeout("window.location.href = '"+paAction.url+"'; ", paAction.delay);
          setTimeout("window.location.reload(); ", (paAction.delay+2500));
      }
      else
        setTimeout("goPopup.remove('"+sId+"'); ", (paAction.delay+500));
    }

    return sId;
  };




  /**
  * a real "popup" in which we can display content and forms
  * need to be persistent if needed
  */
  this.setLayer = function(psLayerId, psContent, psTitle, pbModal, pbPersistant, pnWidth, pnHeight)
  {
    var oPopup = this.getConfig(psLayerId, 'layer');

    if(pbPersistant)
      oPopup.persistent = pbPersistant;

    if(psTitle && psTitle != 'undefined')
      oPopup.title = psTitle;

    if(pbModal !== false)
      oPopup.modal = true;

    if(pnWidth)
      oPopup.width = pnWidth;
    else
      oPopup.width = 800;

    if(pnHeight)
      oPopup.height = pnHeight;
    else
      oPopup.height = 600;

    oPopup.dialogClass+= ' popup_layer';

    return this.setLayerByConfig(psLayerId, oPopup, psContent);
  };


  /**
  * a real "popup" in which we can display content and forms
  * need to be persistent if needed
  */
  this.setLayerByConfig = function(psLayerId, poConfig, psContent)
  {
    if(!psLayerId)
      psLayerId = poConfig.tag;

    //console.log('open a layer '+ psLayerId);
    //console.log(this.caoPopup);

    if(this.restorePersistentLayer(poConfig))
      return true;

    poConfig.type = 'layer';

    if((poConfig.title === undefined || poConfig.title === null || poConfig.title === false))
      poConfig.title = null;

    //no buttons AND no title ? how to close the popup ?
    if(!poConfig.buttons && poConfig.title === null)
    {
      poConfig.title = '';
    }

    poConfig.position = {my: "center", at: "center", of: window};

    return this.create(poConfig, psContent);
  };


  /**
  * a real "popup" in which we can display content and forms
  * need to be persistent if needed
  */
  this.setLayerFromTag = function(pvTagId)
  {
    var oTag;

    if(typeof(pvTagId) == 'object')
      oTag = $(pvTagId);
    else
      oTag = $('#'+pvTagId);

    var asDialogConfig = $(oTag).data();
    if(asDialogConfig.length <= 0)
      return false;

    var oPopup = this.getConfig($(oTag).attr('id'), 'layer');

    $.map(asDialogConfig, function(vValue, sKey)
    {
      if(sKey == 'class')
        sKey = 'dialogClass';

      oPopup[sKey] = vValue;
    });

    oPopup.dialogClass+= ' popup_layer';

    oPopup.position = {my: 'left top', at: 'left top+60', of: window};

    console.log(oPopup);

    if(this.restorePersistentLayer(oPopup))
      return true;

    return this.create(oPopup, $(oTag).html() , true);
  };


  /**
  * a real "popup" in which we can display content and forms fetched in ajax
  */
  this.setLayerFromAjax = function(poConfig, psUrl, psLoadingScreen, psFormToSerialize, pbReloadPage, pbSynch, psCallback, pbWithAnimation)
  {
    var oConf = null;

    if(!poConfig)
    {
      oConf = this.getConfig('', 'layer');
      oConf.width = 800;
      oConf.height = 600;
      oConf.modal = true;
      oConf.autoOpen = false;
    }
    else
    {
      oConf = poConfig;
      oConf.type = 'layer';
    }

    if(this.restorePersistentLayer(poConfig))
      return true;

    oConf.dialogClass+= ' popup_layer';

    //create an empty dialog not opened
    this.setLayerByConfig(oConf.tag, oConf, '<span class="popupLoading"><span class="popupLoadingImage" >&nbsp;</span><span class="popupLoadingText" > Loading ... </span></span>');

    if(!psCallback)
      psCallback = '';

    psCallback += ' goPopup.setLayer("'+oConf.tag+'");'
    AjaxRequest(psUrl, psLoadingScreen, psFormToSerialize, oConf.tag, pbReloadPage,  pbSynch, psCallback, pbWithAnimation);

    return oConf.tag;
  };

  this.restorePersistentLayer = function(poConfig)
  {
    //==================================================================
    //check if the layer is already existing, and if it does: re-open it
    if(poConfig)
      var oPrevConfig = this.caoPopup[poConfig.tag];
    else
      var oPrevConfig = false;

    if(oPrevConfig)
    {
      //We can force a persistent popup to be refreshed... keeping the persistent status
      // But there is an exception: if multiple contents are displayed in the same popup, we need to check
      // if the requested contentTag is the same as currrently displayed one
      if(poConfig.forceRefresh && (!poConfig.contentTag || poConfig.contentTag != oPrevConfig.contentTag))
      {
        //console.log("force refresh on persistent [current:" + poConfig.contentTag + "// old: "+ oPrevConfig.contentTag +"]" );
        return '';
      }

      //console.log('restore persistent ['+poConfig.tag+']');
      //console.log(this.caoPopup[poConfig.tag]);

      $('#'+poConfig.tag).dialog(this.caoPopup[poConfig.tag]).dialog('open');
      return poConfig.tag;
    }

    return '';
  };
};