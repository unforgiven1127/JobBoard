<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Sl[i]stem by Slate</title>

  <link rel="stylesheet" href="/common/style/login_style.css">
  <!--<link rel="stylesheet" type="text/css" href="common/lib/verticalSlider/css/style.css">-->
  <link href='https://fonts.googleapis.com/css?family=Merriweather' rel='stylesheet' type='text/css'>

  <script src="/common/lib/selectImage/js/msdropdown/jquery-1.3.2.min.js" type="text/javascript"></script>
  <script src="/common/lib/selectImage/js/msdropdown/jquery.dd.min.js" type="text/javascript"></script>
  <link rel="stylesheet" type="text/css" href="/common/lib/selectImage/css/msdropdown/dd.css" />


<style>
.leftSide ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    width: 200px;
    background-color: #f1f1f1;
}

.leftSide li a {
    display: block;
    color: #000;
    padding: 8px 16px;
    /*text-decoration: none;*/
    font-size: 15px;
    font-weight: bold;
    /*font-family: 'Merriweather', serif;*/
}

.leftSide {
    width:200px;
}

.leftSide li a.active {
    background-color: #98321F;
    color: white;
}

.leftSide li a:hover:not(.active) {
    background-color: #555;
    color: white;
}

.leftSideTable{
    border-collapse: collapse;
    border: 1px solid #DADADA;
}

html, body {
    width: 100%;
}
.box {
    margin: 0 auto;
    margin-top:15px;
}


/*body{
    background-color:#eee;
}*/

#footerDiv{
  position: absolute;
}

.hero:hover
{
  background-color: #EEEEEE !important;
}
.hero-last:hover
{
  background-color: #EEEEEE !important;
}

.hero {
    position:relative;
    /*background-color:white;*/
    height:113px !important;
    width:154px !important;
    border-top:1px solid #DADADA;
    border-bottom:1px solid #DADADA;
    border-right:1px solid #DADADA;
    border-collapse: collapse;
    cursor:pointer;
}
.hero-last {
    position:relative;
    /*background-color:white;*/
    height:113px !important;
    width:150px !important;
    border-top:1px solid #DADADA;
    border-bottom:1px solid #DADADA;
    border-right:1px solid #DADADA;
    border-collapse: collapse;
    cursor:pointer;
}
.hero:after {
    z-index: 1;
    position: absolute;
    /*border:1px solid black;*/
    top: 35px;
    left: 100%;
    /*margin-left: -10px;*/
    content:'';
    width: 0;
    height: 0;
    /*border-top: solid 10px #659EC7;
    border-left: solid 10px transparent;
    border-right: solid 10px transparent;*/

    border-left: solid 20px #DADADA;
    border-top: solid 20px transparent;
    border-bottom: solid 20px transparent;
}

.topMenu
{
  margin-top: -5px;
  font-size: 15px;
  text-align: center;
}
.topMenuBottom
{
  margin-top: 14px;
  font-size: 35px;
  text-align: center;
}

.searchInput{
  margin-left: 5px !important;
  margin-right: 5px !important;
  height: 20px !important;
  width:190px !important;
  margin-top: 10px !important;
  margin-bottom: 10px !important;
}

::-webkit-input-placeholder
{
 padding-left:3px;
 font-size: 10pt;
}
:-moz-placeholder { /* older Firefox*/
 padding-left:3px;
 font-size: 10pt;
}
::-moz-placeholder { /* Firefox 19+ */
 padding-left:3px;
 font-size: 10pt;
}
:-ms-input-placeholder {
 padding-left:3px;
 font-size: 10pt;
}
li{
  list-style-type: disc;
  list-style-position: inside;
  padding-left: 10px;
  text-indent: -11px;
}

.workSpace {
  display: block;
  height: 180px;
  overflow-y: scroll;
  width:100%;
  border:1px solid #DADADA;
}

.wsTable{
  width:100%;
}

.stayFixed
{
  display: block;
}

</style>

<script type="text/javascript">


</script>

  </head>


  <body>

   <script language="javascript">
    $("#pageContainerId").remove();
    $(document).ready(function(e)
    {
      try
      {
        $("#webmenu").msDropDown();
      } catch(e)
      {
        alert(e.message);
      }
    });
  </script>

  <?php echo $header; ?>
  <table class="box" align="center">
    <tr>
      <td valign="top">
        <table class='leftSideTable'>
          <tr>
            <td valign="top" class="leftSide">
              <ul>
                <li><a class="active" href="#home">My Candidates</a></li>
                <li><a href="#news">My Positions</a></li>
                <li><a href="#contact">My Meetings</a></li>
              </ul>
            </td>
          </tr>
        </table>

        <table><tr><td style='padding-top: 10px;'></td></tr></table>

        <table class='leftSideTable'>
          <tr>
            <td>
              <input style='margin-top: 20px !important;'placeholder="ID"class='searchInput' id="inputsm" type="text">
            </td>
          </tr>
          <tr>
            <td style=''>
              <input placeholder="Lastname, firstname" class='searchInput' id="inputsm" type="text">
            </td>
          </tr>
          <tr>
            <td style=''>
              <input placeholder="Position" class='searchInput' id="inputsm" type="text">
            </td>
          </tr>
          <tr>
            <td style=''>
              <input placeholder="Keyword" class='searchInput' id="inputsm" type="text">
            </td>
          </tr>
          <tr>
            <td>
              <select style='height:20px !important; margin-left: 5px !important; margin-bottom: 5px !important; margin-top: 10px !important; margin-bottom: 10px !important; width: 190px !important; border:1px solid #999999;' name="webmenu" id="webmenu">
                <option value="0" selected="selected" data-image="/common/pictures/0star.png"></option>
                <option value="1" data-image="/common/pictures/1star.png"></option>
                <option value="2" data-image="/common/pictures/2star.png"></option>
                <option value="3" data-image="/common/pictures/3star.png"></option>
                <option value="4" data-image="/common/pictures/4star.png"></option>
                <option value="5" data-image="/common/pictures/5star.png"></option>
              </select>
            </td>
          </tr>
          <tr>
            <td style='text-align: right; padding-top: 8px !important;'>
              <img style="cursor:pointer;height:20px;margin-bottom:5px;margin-right: 10px;" src="/common/pictures/eraser.png" title="Clear"/>
              <img style="cursor:pointer;height:20px;margin-bottom:5px;margin-right: 5px;" src="/common/pictures/search.png" title="Search"/>
            </td>
          </tr>
        </table>

        <table><tr><td style='padding-top: 10px;'></td></tr></table>

        <table class='wsTable'>
        <thead class='stayFixed'>
          <tr style='width:100%;'>
            <th style='width:100%; font-size: 16px;'>My Workspace</th>
          </tr>
        </thead>
        <tbody class='stayFixed workSpace'>
          <?php for($i=0;$i<22;$i++){ ?>
            <tr style='width:100%;'>
              <td style='width:180px; border-bottom:1px solid #DADADA; padding-bottom: 3px;padding-top: 3px;padding-left: 3px;'>
                <a style=' font-size: 12px;' href="#" >My test folder (8)</a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
        </table>

      </td>
      <td valign="top" style='padding-left: 20px;'>
        <table>
          <tr>
            <td valign="top">
              <div style='border-left:1px solid #DADADA;' class="hero">
                <p class="topMenuBottom">21</p>
                <p class="topMenu" >Candidate Send</p>
              </div>
            </td>
            <td valign="top">
              <div class="hero">
                <p class="topMenuBottom">15</p>
                <p class="topMenu" >Unseen</p>
              </div>
            </td>
            <td valign="top">
              <div class="hero">
                <p class="topMenuBottom">6</p>
                <p class="topMenu" >Meeting scheduled</p>
              </div>
            </td>
            <td valign="top">
              <div class="hero">
                <p class="topMenuBottom">3</p>
                <p class="topMenu" >Offer</p>
              </div>
            </td>
            <td valign="top">
              <div class="hero-last">
                <p class="topMenuBottom">1</p>
                <p class="topMenu" >Hired</p>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="5" style='padding-top: 10px;' valign="top">
              <?php echo $innerPage; ?>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <table>
    <tr>
      <td>
        <?php if(isset($searchBottom)) echo $searchBottom; ?>
      </td>
    </tr>
  </table>

  </body>

</html>