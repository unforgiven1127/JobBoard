
bOrderChanged = false;
bObjectDropped = false;


function initWallDragAndDrop()
{

  $(document).ready(function()
  {
     //Set list divs draggable
    //       helper: 'clone',
    $('.taskWallTask').each(function(n, oElement)
    {
      sVal = $('#initOrder').val() + ';'+$(oElement).attr('pk');
      $('#initOrder').val(sVal);
      $('#currentOrder').val(sVal);
    });

  /*
  cursor: 'crosshair',
  grid: [50, 50],
  helper: 'clone',

  connectToSortable: '#taskWallDropZone',
  */

  /*$('*', this).fadeOut(1);
  $(this).animate({height:'35px', width:'35px'}, 10);*/
  /*$('*', this).fadeIn(1);
  $(this).animate({height:'200px', width:'190px'}, 10);*/

    $(window).unload(function()
    {
      if(bOrderChanged && window.confirm('Save order ?'))
        saveTaskOrder();
    });

    //webki bug :(  no onbefore unload in safari and chrome (blocked)
    //window.onbeforeunload = function(){ alert("bye"); }

    $('.taskWallTask, #taskWallDropZone').disableSelection();

    $('.taskWallTask').draggable(
    {
      stack: '.taskWallContainer',
      revert: 'invalid',
      distance: 10,
      connectToSortable: '#taskWallDropZone',
      start: function(event, ui){},
      stop: function(event, ui)
      {
      }
    });

    $("#taskWallDropZone" ).sortable(
    {
      change: function(event, ui)
      {
        bOrderChanged = true;
      },
      stop: function(event, ui)
      {
        saveTaskOrder(false, true);
      }
    });

  });
}

function saveTaskOrder(pbManual, pbSilentMode)
{
   $('#currentOrder').val('');
   $('.taskWallTask').each(function(n, oElement)
   {
     sVal = $('#currentOrder').val() + ';'+$(oElement).attr('pk');
     $('#currentOrder').val(sVal);
   });

   //alert(  $('#initOrder').val()+' // '+  $('#currentOrder').val());

   if($('#initOrder').val() != $('#currentOrder').val())
   {
     if(pbManual || pbSilentMode || window.confirm('Wanna save new order?') )
     {
       $.ajax({
          type: 'POST',
          data: $('#taskOrderFormId').serialize(),
          url: '/index.php5?uid=456-789&ppa=ppava&ppt=prj&pg=ajx',
          context: document.body,
          error: function(oJsonData)
          {
            alert('error, couldn\'t save task order');
          },
          success: function(sJsonData)
          {
            if(sJsonData.message)
            {
              if(pbManual)
                alert('Task order saved.');

              $('#inittOrder').val( $('#currentOrder').val() );
            }
            else
            {
              if(pbManual)
                alert('Couldn\'t save the task order.');
            }

          },
          async: false,
          dataType: "JSON"
        });
     }
   }
   else
   {
     if(pbManual)
       alert('Task order saved.');
   }
}

function popo()
{
  console.log('popopopppooppopo');
  return true;
}


function showHideUserList(val)
{
  if(val==2)
    $('.userList').show(); 
  else
   $('.userList').hide(); 
}