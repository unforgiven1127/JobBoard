
function searchTitle(psMessage, pbToggle, pbForceHide)
{
  if(psMessage)
    $('.searchTitle').html(psMessage);

  if(pbToggle)
  {
    $('.searchTitle').parent().toggleClass('searchFolded');
    $('.queryBuilderContainer').fadeToggle();
    return true;
  }

  if(pbForceHide != null || pbForceHide != undefined)
  {
    if(pbForceHide)
    {
      $('.searchTitle').parent().addClass('searchFolded');
      $('.queryBuilderContainer').fadeOut();
      return true;
    }

    $('.searchTitle').parent().removeClass('searchFolded');
    $('.queryBuilderContainer').fadeIn();

    return true;
  }
}


function removeFormField(poElement, psSelector)
{
  var oElement = null;
  if(poElement)
    oElement = jQuery(poElement);
  else
    oElement = jQuery(psSelector);

  if(!oElement.hasClass('formFieldContainer'))
    oElement = jQuery(oElement).closest('.formFieldContainer:visible');

  //refresh sidebar
  jQuery('.searchFormFieldSelector li[fieldname='+oElement.attr('fieldname')+']').removeClass('fieldUsed');

  jQuery(oElement).fadeOut(250);
  jQuery(oElement).find('input,select,textarea').attr('disabled', 'disabled');

}

function displayFormField(poElement, psSelector)
{
  var oContainer = null;

  if(poElement)
  {
    if(jQuery(poElement).hasClass('formFieldContainer'))
      oContainer = poElement;
    else
      oContainer = jQuery(poElement).closest('.formFieldContainer');
  }
  else
  {
    if(jQuery(psSelector).hasClass('formFieldContainer'))
      oContainer = jQuery(psSelector);
    else
      oContainer = jQuery(psSelector).closest('.formFieldContainer');

    jQuery(psSelector).removeAttr('disabled');
  }

  if(jQuery(oContainer).find('.hideFieldLink').length == 0)
  {
    jQuery('.formField', oContainer).after('<div class="hideFieldLink" fieldname="'+jQuery(oContainer).attr('fieldname')+'" onclick="removeFormField(this);" title="Remove this field">&nbsp;</div>');
  }
 
    jQuery(oContainer).show(350);
    jQuery(oContainer).find('input,select,textarea').removeAttr('disabled');

}


function refreshFormField()
{
  var oForm = jQuery('.queryBuilderContainer').find('form');

  //alert('refresh left // '+ jQuery('.formFieldContainer', oForm).length);
  jQuery(oForm).find('.formFieldContainer:not(.formFieldHidden)').each(function()
  {
    //alert( jQuery(this).hasClass('hidden')+' || '+jQuery(this).css('display'));

    if(jQuery(this).hasClass('hidden') || jQuery(this).css('display') == 'none')
    {
      //alert('['+jQuery(this).is(':visible')+'] hide '+jQuery(this).attr('class'));
      // disable the field
      removeFormField(this);
    }
    else
    {
      // add X on the field, and select the li in the sidebar
      //alert('['+jQuery(this).is(':visible')+'] show (x/side) '+jQuery(this).attr('class'));

      var sFieldClass = jQuery(this).attr('class');
      var asFieldClass = sFieldClass.split(' ');
      jQuery(asFieldClass).each(function(nIndex, sValue)
      {
        if(sValue != 'formFieldContainer' && sValue.indexOf('search_') != -1)
        {
          jQuery('.searchFormFieldSelector li[fieldname~='+sValue+']').addClass('fieldUsed');
        }
      });

      if(jQuery(this).find('.hideFieldLink').length == 0)
      {
        jQuery('.formField', this).after('<div class="hideFieldLink" fieldname="'+jQuery(this).attr('fieldname')+'" onclick="removeFormField(this);" title="Remove this field">&nbsp;</div>');
      }
    }
  });
}