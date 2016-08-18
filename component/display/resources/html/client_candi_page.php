<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Sl[i]stem by Slate</title>

  <link rel="stylesheet" href="/common/style/login_style.css">
  <!--<link rel="stylesheet" type="text/css" href="common/lib/verticalSlider/css/style.css">-->
  <link href='https://fonts.googleapis.com/css?family=Merriweather' rel='stylesheet' type='text/css'>


<script type="text/javascript">


</script>

<script>
function pop_up(url){
  //alert(url);
  window.open(url,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=no,width=1076,height=768,directories=no,location=no');
}

$('.bootpopup').click(function(){
    var frametarget = $(this).attr('href');
  var targetmodal = $(this).attr('target');
  if (targetmodal == undefined) {
    targetmodal = '#popupModal';
  } else { 
    targetmodal = '#'+targetmodal;
  }
  if ($(this).attr('title') != undefined) {
    $(targetmodal+ ' .modal-header h3').html($(this).attr('title'));
    $(targetmodal+' .modal-header').show();
  } else {
     $(targetmodal+' .modal-header h3').html('');
    $(targetmodal+' .modal-header').hide();
  }  
    $(targetmodal).on('show', function () {
        $('iframe').attr("src", frametarget );   
  });
    $(targetmodal).modal({show:true});
  return false;
    
});

</script>

<style>

.innerTable{
  border:1px solid #DADADA;

}

.outherTable
{
  height: 458px;
  overflow-y: scroll;
  display: block;
}

.innerTD
{
  padding-bottom: 5px;
  padding-top: 5px;
  cursor:pointer;
}

</style>

  </head>


  <body>
  <a href="http://twitter.github.io/bootstrap/" class="btn bootpopup" title="This is title" target="popupModal2">Open modal</a>

<div id="popupModal2" class="modal hide fade" tabindex="-1" role="dialog">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
      <h3>Title</h3>
  </div>
  <div class="modal-body">
      <iframe src="" style="zoom:0.60" frameborder="0" height="250" width="99.6%"></iframe>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal">OK</button>
  </div>
</div>
  <table class='outherTable' >
    <tr>
      <td valign="top" style='width: 750px;' >
        <table border="1" class='innerTable' style='width: 750px;' >
          <tr style='background-color: #EEEEEE;'>
            <th style='width:40px !important;'>
              <center><img src="/common/pictures/list_action.png" /></center>
            </th>
            <th style='padding-left: 5px; font-size: 15px;'>ID</th>
            <th style='padding-left: 5px; font-size: 15px;'>Grade</th>
            <th style='padding-left: 5px; font-size: 15px;'>Firsname</th>
            <th style='padding-left: 5px; font-size: 15px;'>Lastname</th>
            <th style='padding-left: 5px; font-size: 15px;'>Position</th>
            <th style='padding-left: 5px; font-size: 15px;'>Current status</th>
          </tr>

          <?php foreach ($suggestedCandidates as $key => $value)
          { ?>
          <tr>
            <td class='innerTD' style=' font-size: 13px; width:40px !important;'>
              <input style='width:40px !important;' type="checkbox" name="vehicle" value="Bike">
            </td>
            <td class='innerTD' style='padding-left: 5px; font-size: 13px;'><?php echo $value['sl_candidatepk']; ?></td>
            <td class='innerTD' style='padding-left: 5px; font-size: 13px;'>
              <img src='<?php echo $value['grade']; ?>' />
            </td>
            <td class='innerTD' onclick="pop_up('<?php echo $value['candiPopup']; ?>');" style='padding-left: 5px; font-size: 13px;'><?php echo $value['firstname']; ?></td>
            <td class='innerTD' style='padding-left: 5px; font-size: 13px;'><?php echo $value['lastname']; ?></td>
            <td class='innerTD' style='padding-left: 5px; font-size: 13px;'><?php echo $value['title']; ?></td>
            <td class='innerTD' style='padding-left: 5px; font-size: 13px;'><?php echo $value['flag']; ?></td>
          </tr>

          <?php } ?>

        </table>
      </td>
    </tr>
    <tr>
      <td>
      <!-- kayan tablonun altina ekler -->
      </td>
    </tr>
  </table>
  <table>
    <tr>
      <td>
        <!-- kayan tablo sonrasi ekler -->
      </td>
    </tr>
  </table>


  </body>

</html>