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
    text-decoration: none;
    font-size: 15px;
    font-family: 'Merriweather', serif;
}

.leftSide {
    width:200px;
}

.leftSide li a.active {
    background-color: #892828;
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
    margin-top:50px;
}


/*body{
    background-color:#eee;
}*/

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
    width:150px !important;
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
  margin-top: 10px;
  font-size: 35px;
  text-align: center;
}

</style>

  </head>


  <body>

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
                <p class="topMenu" >Placed</p>
              </div>
            </td>
          </tr>
          <tr>
            <td valign="top">
              <?php echo $innerPage; ?>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  </body>

</html>