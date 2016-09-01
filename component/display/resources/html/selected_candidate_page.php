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


.candidateInfoTitle
{
  height: 15px;
  width: 85px;
  border-bottom: 1px solid #DADADA;
  vertical-align: bottom;
  text-align: right;
  padding-right: 3px;
  font-weight: bold;
  padding-top: 10px;
  background-color: #EEEEEE
}

.candidateInfo
{
  height: 15px;
  vertical-align: bottom;
  padding-top: 10px;
  padding-left: 7px;
  border-bottom: 1px solid #DADADA;
}

.wsTableCD{
  width:100%;
  border-left:0px;
  border-right:0px;
  border-top:0px;
}

.stayFixedCD
{
  display: block;
}

.workSpaceCD {
  display: block;
  height: 350px;
  border-left:0px;
  border-right:0px;
  border-top:0px;
  overflow-y: scroll;
  width:100%;
  border:1px solid #DADADA;
}

.noteTD
{
  width:100%;
  border-bottom:1px solid #DADADA;
  padding-bottom: 3px;
  padding-top: 3px;
  padding-left: 5px;
  padding-right: 5px;
  font-size: 11px;
}

.innerTableSearch{
  border:1px solid #DADADA;

}

.outherTableSearch
{
  height: 200px;
  overflow-y: scroll;
  display: block;
}

.innerTDSearch
{
  padding-bottom: 5px;
  padding-top: 5px;
  cursor:pointer;
}

.log-btn_ {
  background: #892828;
  display: block;
  margin: auto;
  width: 150px;
  font-size: 13px;
  height: 20px;
  color: #fff;
  text-decoration: none;
  border: none;
  -moz-border-radius: 4px;
  -webkit-border-radius: 4px;
  border-radius: 4px;
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
          <tr>
            <td valign="top">
              <table>
                <tr>
                  <td class='candidateInfoTitle'>Position : </td>
                  <td class='candidateInfo'>#8853 - test new position</td>
                </tr>
                <tr>
                  <td class='candidateInfoTitle'>Status : </td>
                  <td class='candidateInfo'>Meeting 1</td>
                </tr>
                <tr>
                  <td class='candidateInfoTitle'>Title : </td>
                  <td class='candidateInfo'>Security Consultant</td>
                </tr>
                <tr>
                  <td class='candidateInfoTitle'>Occupation : </td>
                  <td class='candidateInfo'>Consultant</td>
                </tr>
                <tr>
                  <td class='candidateInfoTitle'>Industry : </td>
                  <td class='candidateInfo'>IT-Other</td>
                </tr>
                <tr>
                  <td class='candidateInfoTitle'>Last meeting : </td>
                  <td class='candidateInfo'>2015-11-30</td>
                </tr>
                <tr>
                  <td class='candidateInfoTitle'>Sent by : </td>
                  <td class='candidateInfo'>Munir Anameric | 2015-11-25</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
      <td valign="top" style='width: 385px;'>
        <table class='innerTableTopless' style='width: 385px; height: 420px; border-left: 0px !important;' >
          <tr style='width:100% !important;'>
            <td style='width:100% !important;' valign="top">
              <table style='width:100% !important;'>
                <tr style='width:100% !important;'>
                  <td class='candidateInfoTitle' style='width:100% !important; padding-top: 10px;text-align:center !important;' >NOTES</td>
                </tr>
                <tr>
                  <td >
                    <table class='wsTableCD'>
                      <!--<thead class='stayFixed'>
                        <tr style='width:100%;'>
                          <th style='width:100%; font-size: 16px;'>My Workspace</th>
                        </tr>
                      </thead>-->
                      <tbody class='stayFixedCD workSpaceCD'>
                        <?php for($i=0;$i<22;$i++){ ?>
                          <tr style='width:100%;'>
                            <td class="noteTD">
                              <p style='border-bottom:1px #DADADA dashed; color:#4f96e7;' >Munir Anameric | 2015-11-25 10:30</p>
                              PERSONALITY: asd asd adsasd dasa sdasa ads asdasdasdada sdasdasdasd asd asd asd
                              CAREER: asdasd asd adsasd dasa sdasa ads asdasdasdada sdasdasdasd asd asd asd
                              EDUCATION: asd asd adsasd dasa sdasa ads asdasdasdada sdasdasdasd asd asd asd
                              MOVE: asd asd adsasd dasa sdasa ads asdasdasdada sdasdasdasd asd asd asd
                              COMPENSATIONS: asd asd adsasd dasa sdasa ads asdasdasdada sdasdasdasd asd asd asd
                            </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <button type="button" class="log-btn_" >Add new note</button>
            </td>
          </tr>
        </table>
      </td>
    </tr>


  </table>



  </body>

</html>