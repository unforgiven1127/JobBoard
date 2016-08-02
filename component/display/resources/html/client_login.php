<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Sl[i]stem by Slate</title>

  <link rel="stylesheet" href="/common/style/login_style.css">
  <!--<link rel="stylesheet" type="text/css" href="common/lib/verticalSlider/css/style.css">-->
  <link href='https://fonts.googleapis.com/css?family=Merriweather' rel='stylesheet' type='text/css'>


<script type="text/javascript">


//alert($('#bottomCandidateSection').height());

var loginHeight = $('#bottomCandidateSection').height();
var windowHeight = window.innerHeight;

//alert(windowHeight);

var oran = 100*loginHeight/windowHeight;
//alert(oran);
var newOran = 95*windowHeight/100;
newOran = newOran+'px';

var url = document.URL;
var search = "/?";
var search2 = ",pg=norma";

if(url.indexOf(search)>-1)
{
  url = url.substring(0, url.length - 1);
  var res = url.split("/?");
  //alert(res[0]);
  window.location.href = res;
  //alert();
}

if(oran < 85)
{
  $('#bottomCandidateSection').height(newOran);
}

$(window).resize(function() {
  var loginHeightR = $('#bottomCandidateSection').height();
  var windowHeightR = window.innerHeight;
  var oranR = 100*loginHeightR/windowHeightR;
  //alert(oranR);
  if(oranR < 90)
  {
    var newOranR = 95*windowHeightR/100;
    newOranR = newOranR+'px';
    $('#bottomCandidateSection').height(newOranR);
  }
});
//location.reload(); // sayfayi tekrar yukler

//document.getElementsByClassName("userBloc").style.visibility='hidden';

function closeExtra()
{
  var divsToHide = document.getElementsByClassName("closeAll");

  for(var i = 0; i < divsToHide.length; i++)
    {
    divsToHide[i].style.display="none";
    }
}

function openExtra(open)
{

    var divsToHide = document.getElementsByClassName("closeAll");

    var td_ = open+"_";

    for(var i = 0; i < divsToHide.length; i++)
    {
    divsToHide[i].style.display="none";
    }
    document.getElementById(open).style.display = "table-row";
}

</script>
<style>

    .jobs{
        overflow-y: auto;    /* Trigger vertical scroll    */
        overflow-x: hidden;  /* Hide the horizontal scroll */
    }

      .test {
        font-family: 'Merriweather', serif;
        margin-left: 5px;

      }
      .borderlist {
        list-style-position:inside;
        border: 1px solid black;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        border-radius: 4px;
        margin-bottom: 10px;
      }

      .jobs {
          border-collapse: collapse;
          width: 80%;
          margin-top: 20px;
      }

      td {
          padding: 8px;
          font-size: 12pt;
          border-bottom: solid 1px #892828;
      }

      tr:nth-child(even){background-color: #f2f2f2}

      /*th {
          background-color: #4CAF50;
          color: white;
      }*/

</style>

  </head>


  <body>

  <table style="width: 100%; height:80%; margin-left: -10px;">
    <tr>
      <td valign="middle" align="middle" class="half" style=" width: 50%;">
        <div class="login-form">
        <form name="loginFormData" enctype="multipart/form-data" submitajax="1" action="https://jobs.slate.co.jp/index.php5?uid=153-160&ppa=cla&ppt=job&ppk=0" method="POST" id="loginFormDataId" onbeforesubmit onsubmit>
          <div style="width: 300px;"><center><img style="text-align: center; width: 300px; margin-bottom: 20px;" src="/common/pictures/slistem_large.gif" /></center></div>
         <div style="width: 300px;" class="form-group ">
           <input style='width: 300px;' type="text" name="login" class="form-control" placeholder="Username " id="UserName">
         </div>
         <div style="width: 300px;" class="form-group log-status">
           <input style='width: 300px;' type="password" name="password" class="form-control" placeholder="Password" id="Passwod">
         </div>
         <div style="width: 300px;">
            <?php if(isset($msg)){ ?>
              <div style=" font-size:15px; width: 300px; color:#585858; font-weight: bold; " class="alert alert-danger" role="alert">
                Incorrect username or password
              </div>
            <?php } ?>
            <!--<a class="link" href=<?php echo "'".$lost."'"; ?> >Lost your password?</a>-->
            <button type="submit" class="log-btn" >Log in</button>
         </div>
        </form>

       </div>
      </td>

      <!--<td  style="width: 50%;">
        <div class="login-form2">
          <center><p class="test" style="color: #892828; font-size: 48px;">LATEST JOBS</p></center>

          <table class="jobs" align="center">
          <?php for($i=0 ; $i<5 ; $i++) { ?>
            <tr>
              <td>
                <p style="font-size: 12pt;" class="test"><?php echo $firstFive[$i]['name']; ?></p>
                <p style="font-size: 12pt;" class="test"><?php echo $firstFive[$i]['title']; ?> (#<?php echo $firstFive[$i]['sl_positionpk']; ?>)</p>
                <p style="font-size: 12pt;" class="test">&yen;<?php echo $firstFive[$i]['salary_from']; ?> - &yen;<?php echo $firstFive[$i]['salary_to']; ?></p>
                <p style="font-size: 12pt;" class="test"><?php echo $firstFive[$i]['firstname']; ?> <?php echo $firstFive[$i]['lastname']; ?></p>
                <div style="margin-top: 5px;">
                  <p style="font-size: 11pt; " class="test"><b>More: </b><img style="cursor:pointer; width: 20px; vertical-align: text-bottom;" src="common/pictures/plus.png" onclick="openExtra(<?php echo "'firstFive_".$i."'" ?>)"> <img style="cursor:pointer; width: 20px; vertical-align: text-bottom;" src="common/pictures/minus.png" onclick="closeExtra()"></p>
                </div>
              </td>
              <td>
                <p style="font-size: 12pt;" class="test"><?php echo $lastFive[$i]['name']; ?></p>
                <p style="font-size: 12pt;" class="test"><?php echo $lastFive[$i]['title']; ?> (#<?php echo $lastFive[$i]['sl_positionpk']; ?>)</p>
                <p style="font-size: 12pt;" class="test">&yen;<?php echo $lastFive[$i]['salary_from']; ?> - &yen;<?php echo $lastFive[$i]['salary_to']; ?></p>
                <p style="font-size: 12pt;" class="test"><?php echo $lastFive[$i]['firstname']; ?> <?php echo $lastFive[$i]['lastname']; ?></p>
                <div style="margin-top: 5px;">
                  <p style="font-size: 11pt; " class="test"><b>More: </b><img style="cursor:pointer; width: 20px; vertical-align: text-bottom;" src="common/pictures/plus.png" onclick="openExtra(<?php echo "'lastFive_".$i."'" ?>)"> <img style="cursor:pointer; width: 20px; vertical-align: text-bottom;" src="common/pictures/minus.png" onclick="closeExtra()"></p>
                </div>
              </td>
            </tr>
            <tr class="closeAll" style="display: none;"  id=<?php echo "'firstFive_".$i."'" ?> >
              <th class="extra " id=<?php echo "'firstFive_".$i."_'" ?>  style=" background-color: rgba(138, 40, 40, 0.08);" colspan="2">
                <p style="font-size: 12pt;" class="test"><b>Company: </b><i style="color: #424242; font-size:11pt;"><?php echo $firstFive[$i]['name']; ?></i></p><br>
                <p style="font-size: 12pt;" class="test"><b>Title: </b><i style="color: #424242; font-size:11pt;"><?php echo $firstFive[$i]['long_title']; ?> (#<?php echo $firstFive[$i]['sl_positionpk']; ?>)</i></p><br>
                <p style="font-size: 12pt;" class="test"><b>Salary range: </b><i style="color: #424242; font-size:11pt;">&yen;<?php echo $firstFive[$i]['salary_from']; ?> - &yen;<?php echo $firstFive[$i]['salary_to']; ?></i></p><br>
                <p style="font-size: 12pt;" class="test"><b>Consultant: </b><i style="color: #424242; font-size:11pt;"><?php echo $firstFive[$i]['firstname']; ?> <?php echo $firstFive[$i]['lastname']; ?></i></p><br>
                <p style="font-size: 12pt;" class="test"><b>Description: </b><i style="color: #424242; font-size:11pt;"><?php echo $firstFive[$i]['description']; ?></i></p><br>
                <p style="font-size: 12pt;" class="test"><b>Requirements: </b><i style="color: #424242; font-size:11pt;"><?php echo $firstFive[$i]['requirements']; ?></i></p><br>
                <div style="margin-top: 5px;">
                  <p style="font-size: 11pt; " class="test"><b>Less: </b><img style="cursor:pointer; width: 20px; vertical-align: text-bottom;" src="common/pictures/minus.png" onclick="closeExtra()"></p>
                </div>
              </th>
            </tr>
            <tr class="closeAll" style="display: none;" id=<?php echo "'lastFive_".$i."'" ?> >
              <th class="extra " id=<?php echo "'lastFive_".$i."_'" ?>  style=" background-color: rgba(138, 40, 40, 0.08);" colspan="2">
                <p style="font-size: 12pt;" class="test"><b>Company: </b><i style="color: #424242; font-size:11pt;"><?php echo $lastFive[$i]['name']; ?></i></p><br>
                <p style="font-size: 12pt;" class="test"><b>Title: </b><i style="color: #424242; font-size:11pt;"><?php echo $lastFive[$i]['long_title']; ?> (#<?php echo $lastFive[$i]['sl_positionpk']; ?>)</i></p><br>
                <p style="font-size: 12pt;" class="test"><b>Salary range: </b><i style="color: #424242; font-size:11pt;">&yen;<?php echo $lastFive[$i]['salary_from']; ?> - &yen;<?php echo $lastFive[$i]['salary_to']; ?></i></p><br>
                <p style="font-size: 12pt;" class="test"><b>Consultant: </b><i style="color: #424242; font-size:11pt;"><?php echo $lastFive[$i]['firstname']; ?> <?php echo $lastFive[$i]['lastname']; ?></i></p><br>
                <p style="font-size: 12pt;" class="test"><b>Description: </b><i style="color: #424242; font-size:11pt;"><?php echo $lastFive[$i]['description']; ?></i></p><br>
                <p style="font-size: 12pt;" class="test"><b>Requirements: </b><i style="color: #424242; font-size:11pt;"><?php echo $lastFive[$i]['requirements']; ?></i></p><br>
                <div style="margin-top: 5px;">
                  <p style="font-size: 11pt; " class="test"><b>Less: </b><img style="cursor:pointer; width: 20px; vertical-align: text-bottom;" src="common/pictures/minus.png" onclick="closeExtra()"></p>
                </div>
              </th>
            </tr>

            <?php } ?>
          </table>

        </div>
      </td>-->
    </tr>
  </table>


  </body>

</html>