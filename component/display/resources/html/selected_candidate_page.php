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

function openHref(url)
{
  alert(url);
  window.location.href = url;
}

function pop_up_(url){
  //alert(url);
  window.open(url,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1076,height=768,directories=no,location=no');
}

function pop_up(url, title, w, h) {
    // Fixes dual-screen position                         Most browsers      Firefox
    w = '1076';
    h = '768';

    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left,'toolbar=0,location=0, directories=0, status=0, menubar=0');
    // Puts focus on the newWindow
    if (window.focus)
    {
        newWindow.focus();
    }
}

</script>

<style>

.innerTable{
  border-top:1px solid #DADADA;
  border-left:1px solid #DADADA;
  border-right:1px solid #DADADA;
  border-bottom:1px solid #DADADA;

}

.innerTableTopless
{
  border-left:1px solid #DADADA;
  border-right:1px solid #DADADA;
  border-bottom:1px solid #DADADA;
}

.outherTable
{
  height: 458px;
  /*overflow-y: scroll;*/
  display: block;
}

.innerTD
{
  padding-bottom: 5px;
  padding-top: 5px;
  cursor:pointer;
}

.titles
{
  text-align: right;
  margin-right: 5px;
}

</style>

  </head>


  <body>
  <table class='outherTable' >
    <tr>
      <td colspan="2" valign="top" style='width: 770px;' >
        <table class='innerTable' style='width: 770px;' >
          <tr style='background-color: #EEEEEE;'>
            <td><p style="font-size: 11pt; color: black; font-weight: bold; margin-top: 10px; margin-left: 10px;">
              #154310 Test User
            </p></td>
            <!--<td><img src='<?php echo $grade; ?>' /></td>-->
            <td style='width: 40px;' ><img style="cursor:pointer;height:18px;margin-right: 10px;" src="/common/pictures/edit_48.png" title="Clear"/></td>
            <td style='width: 40px;' ><img style="cursor:pointer;height:18px;margin-right: 10px;" src="/common/pictures/trash.png" title="Clear"/></td>
          </tr>

        </table>
      </td>
    </tr>
    <tr>
      <td valign="top" style='width: 385px;'>
        <table class='innerTableTopless' style='width: 385px; height: 420px;' >
          <tr style='height: 20px !important;'>
            <td valign="top" style='padding-top: 10px; width:80px; height: 20px !important; '><p class='titles'>Position :</p></td>
            <td valign="top" style='padding-top: 10px; height: 20px !important;'><p>#8853 - test new position</p></td>
          </tr>
          <tr>
            <td style='height: 20px;'><p class='titles'>Status</p></td>
            <td style='height: 20px;'><p>Meeting 1</p></td>
          </tr>
          <tr>
            <td style='height: 20px;'><p class='titles'>Title</p></td>
            <td style='height: 20px;'><p>Security Consultant</p></td>
          </tr>
          <tr>
            <td style='height: 20px;'><p class='titles'>Occupation</p></td>
            <td style='height: 20px;'><p>Consultant</p></td>
          </tr>
          <tr>
            <td style='height: 20px;'><p class='titles'>Industry</p></td>
            <td style='height: 20px;'><p>IT-Other</p></td>
          </tr>
          <tr>
            <td style='height: 20px;'><p class='titles'>Last meeting</p></td>
            <td style='height: 20px;'><p>2015-11-30</p></td>
          </tr>
        </table>
      </td>
      <td valign="top" style='width: 385px;'>
        <table class='innerTableTopless' style='width: 385px; height: 420px;' >
          <tr>
            <td valign="top">
              <table>
                <tr>
                  <td style='padding-top: 10px;' >ASDF</td>
                </tr>
                <tr>
                  <td >ASDF</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
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