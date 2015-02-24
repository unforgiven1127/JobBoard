/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function checkForm(psFormName)
{
  var oForm = $('form[name='+psFormName+']');
  var bError = false;

  //clear previous error messages
  clearErrorMessage();

  //checking form fields
  $(oForm[0].elements).each(function()
  {
     //no js control on hiddem fields and ones with no control defined

     if($(this).attr('type') != 'hidden' && $(this).attr('jsControl') !== undefined)
     {
        var asControls = $(this).attr('jsControl').split('|');

        if($(this).is('select') && $(this).attr('multiple'))
        {
          asFieldValue = $(this).val() || [];
          var sFieldValue = asFieldValue.join(',');
        }
        else
          var sFieldValue = $(this).val();

        for( nCount=0; nCount < asControls.length; nCount++)
        {
          var asControlDetail = asControls[nCount].split('@');

          switch(asControlDetail[0])
          {
            case 'jsFieldNotEmpty':
              if(sFieldValue.split(' ').join('').length == 0)
              {
                bError = true;
                bindErrorMessage($(this), 'Can\'t be empty');
              }
            break;

            case 'jsFieldMinSize':
              if(sFieldValue)
              {
                nLength = sFieldValue.split(' ').join('').length;
                if(nLength < parseInt(asControlDetail[1]))
                {
                  bError = true;
                  bindErrorMessage($(this), asControlDetail[1]+' characters min (currently '+nLength+')');
                }
              }
            break;

            case 'jsFieldMaxSize':
              if(sFieldValue)
              {
                nLength = sFieldValue.split(' ').join('').length;
                if(nLength > parseInt(asControlDetail[1]))
                {
                  bError = true;
                  bindErrorMessage($(this), asControlDetail[1]+' characters max (currently '+nLength+')');
                }
              }
            break;

            case 'jsFieldMinValue':
              if(sFieldValue)
              {
                if(parseInt(sFieldValue) < parseInt(asControlDetail[1]))
                {
                  bError = true;
                  bindErrorMessage($(this), 'Value must not be under '+asControlDetail[1]);
                }
              }
            break;

            case 'jsFieldMaxValue':
              if(sFieldValue)
              {
                if(parseInt(sFieldValue) > parseInt(asControlDetail[1]))
                {
                  bError = true;
                  bindErrorMessage($(this), 'Value must not exeed '+asControlDetail[1]);
                }
              }
            break;

            case 'jsFieldTypeInteger':
              if(sFieldValue)
              {
                if(sFieldValue.length == 0 || parseInt(sFieldValue) == Number.NaN)
                {
                  bError = true;
                  bindErrorMessage($(this), 'Should be a integer number');
                }
              }
            break;

            case 'jsFieldTypeIntegerNegative':
              if(sFieldValue)
              {
                if(sFieldValue.length == 0 || parseInt(sFieldValue) == Number.NaN || parseInt(sFieldValue) > 0)
                {
                  bError = true;
                  bindErrorMessage($(this), 'Should be a negative integer number');
                }
              }
            break;

            case 'jsFieldTypeIntegerPositive':
              if(sFieldValue)
              {
                if(sFieldValue.length == 0 || parseInt(sFieldValue) == Number.NaN || parseInt(sFieldValue) < 0)
                {
                  bError = true;
                  bindErrorMessage($(this), 'Should be a positive integer number');
                }
              }
            break;

            case 'jsFieldTypeFloat':
              if(sFieldValue)
              {
                if(sFieldValue.length == 0 || parseFloat(sFieldValue) == Number.NaN)
                {
                  bError = true;
                  bindErrorMessage($(this), 'Should be a decimal number');
                }
              }
            break;

            case 'jsFieldTypeCurrency':
              /*bError = true;
              alert('is currency');
              bindErrorMessage($(this), 'Currency not valid');*/
            break;

            case 'jsFieldGreaterThan':
              if(sFieldValue)
              {
                var sRef = $(asControlDetail[1]).val();
                if(sRef && sFieldValue < sRef)
                {
                  bError = true;
                  bindErrorMessage($(this), 'Should be greater than '+sRef);
                }
              }
            break;

            case 'jsFieldSmallerThan':
              if(sFieldValue)
              {
                var sRef = $(asControlDetail[1]).val();
                if(sRef && sFieldValue > sRef)
                {
                  bError = true;
                  bindErrorMessage($(this), 'Should be greater than '+sRef);
                }
              }
            break;

            case 'jsFieldTypeEmail':
              if(sFieldValue)
              {
                 sFieldValue =$.trim(sFieldValue);
               //   var regExp = new RegExp("^[0-9a-zA-Z]+@[0-9a-zA-Z]+[\.]{1}[0-9a-zA-Z]+[\.]?[0-9a-zA-Z]+$","");
               var regExp = new RegExp("[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])","");

                if(!regExp.test(sFieldValue))
                {
                  bError = true;
                  bindErrorMessage($(this), 'Mail format not valid');
                }
              }
            break;

            case 'jsFieldTypeUrl':
              if(sFieldValue)
              {
                var regExp = new RegExp(/[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi);
                if(!regExp.test(sFieldValue))
                {
                  bError = true;
                  bindErrorMessage($(this), 'Url format not valid');
                }
              }
            break;
          }
        }
     }
  });

  if(!bError)
   return true;

  return false;
}

function bindErrorMessage(poField, psMsg)
{
  var oPosition = $(poField).offset();
  var fieldName = $(poField).attr('name');
  fieldName = fieldName.split('[').join('').split(']').join('');

  if(!oPosition || !oPosition.top)
  {
    var oParentDiv = $(poField).closest('div.formField');
    oPosition = oParentDiv.offset();
    var nFieldWidth = parseInt(oParentDiv.css('width'));
  }
  else
    var nFieldWidth = parseInt($(poField).css('width'));

  if($("#formError_"+fieldName).is('div'))
    $("#formError_"+fieldName).append(", "+psMsg);
  else
    $('#body').append("<div id='formError_"+fieldName+"' class='formErrorMsg' style='top:"+oPosition.top+"px; left:"+(parseInt(oPosition.left)+nFieldWidth +20)+"px;'><div class='formErrorArrow'></div>"+psMsg+"</div>");
}

function clearErrorMessage(poField, psMsg)
{
  $('.formErrorMsg').remove();
}

