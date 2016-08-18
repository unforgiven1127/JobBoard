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
function pop_up_(url){
  //alert(url);
  window.open(url,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1076,height=768,directories=no,location=no');
}

function pop_up(url, title, w, h) {
    // Fixes dual-screen position                         Most browsers      Firefox
    w = '1076';
    h = '768';
    title = 'Munir';

    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left,'toolbar=0,location=0, directories=0, status=0, menubar=0');
    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
}

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