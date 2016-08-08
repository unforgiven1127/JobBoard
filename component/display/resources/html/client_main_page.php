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
          <tr>
            <td>
              <table style="width:105%; margin-left:-5px;">
                <tbody><tr class="closeTR">
                  <th>
                      <div class="label  hidden"><b>Candidate</b></div>
                  </th>
                  <td>
                      <div class="field">
                        <input type="text" name="candidate" placeholder="ID  or  lastname, firstname" value="" onblur="if($(this).val().trim().length == 0)
                          { $(this).val($(this).attr('data-default'));}
                          else
                          {
                            asValue = $(this).val().trim().split(',');
                            if(asValue.length > 2)
                              return alert('There should be only 1 comma to separate the lastname and the firstname.');

                            if(asValue.length == 2)
                            return true;

                          }">
                      </div>
                    </td>
                  </tr>
                  <tr class="closeTR">
                      <th>
                        <div class="label  hidden"><b>Contacts</b></div>
                      </th>
                      <td>
                        <div class="field">
                          <input type="text" name="contact" class="defaultText" data-default="Contact" value="Contact" onfocus="if($(this).val() == $(this).attr('data-default')){ $(this).val(''); $(this).removeClass('defaultText'); }" onblur="if($(this).val().trim().length == 0){ $(this).val($(this).attr('data-default')); $(this).addClass('defaultText');}">
                        </div>
                      </td>
                  </tr>
                  <tr class="closeTR">
                    <th>
                      <div class="label  hidden"><b>Company</b></div>
                    </th>
                    <td>
                      <div class="field">
                        <input type="text" name="company" class="defaultText" data-default="Company" value="Company" onfocus="if($(this).val() == $(this).attr('data-default')){ $(this).val(''); $(this).removeClass('defaultText'); }" onblur="if($(this).val().trim().length == 0){ $(this).val($(this).attr('data-default')); $(this).addClass('defaultText');}">
                      </div>
                    </td>
                  </tr>
                  <tr class="closeTR">
                    <th>
                      <div class="label  hidden"><b>Department</b></div>
                    </th>
                    <td>
                      <div class="field">
                        <input type="text" name="department" class="defaultText" data-default="Department" value="Department" onfocus="if($(this).val() == $(this).attr('data-default')){ $(this).val(''); $(this).removeClass('defaultText'); }" onblur="if($(this).val().trim().length == 0){ $(this).val($(this).attr('data-default')); $(this).addClass('defaultText');}">
                      </div>
                    </td>
                  </tr>
                  <tr class="closeTR">
                    <th>
                      <div class="label  hidden"><b>Position</b></div>
                    </th>
                    <td>
                      <div class="field">
                        <input type="text" name="position" class="defaultText" data-default="Position ID or title" value="Position ID or title" onfocus="if($(this).val() == $(this).attr('data-default')){ $(this).val(''); $(this).removeClass('defaultText'); }" onblur="if($(this).val().trim().length == 0){ $(this).val($(this).attr('data-default')); $(this).addClass('defaultText');}" onchange="$('#qs_pos_status').val(''); ">
                        <input type="hidden" id="qs_pos_status" name="position_status" value="">
                      </div>
                    </td>
                  </tr>
                  <tr class="closeTR">
                    <th>
                      <div class="label  hidden"><b>Keyword</b></div>
                    </th>
                    <td>
                      <div class="field">
                        <input type="text" name="keyword" class="defaultText" data-default="Keyword" value="Keyword" onfocus="if($(this).val() == $(this).attr('data-default')){ $(this).val(''); $(this).removeClass('defaultText'); }" onblur="if($(this).val().trim().length == 0){ $(this).val($(this).attr('data-default')); $(this).addClass('defaultText');}">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                <div class="hidden option">Search options</div>

      <div class="hidden option"><div class="label  hidden">wide search</div><div class="field">
      <input type="checkbox" name="qs_wide" checked="checked"> (contains the string)</div></div>

      <div class="hidden option"><div class="label  hidden">Get lucky</div><div class="field">
      <input type="checkbox" name="qs_super_wide"> (lastname OR firstname)</div></div>

      <div class="hidden option"><div class="label  hidden">name format</div><div class="field">
      <select name="qs_name_format">
        <option value="lastname">Lastname, Firstname</option>
        <option value="firstname">Firstname, Lastname</option>
        <option value="none" selected="selected">Indifferent (slower/wider search)</option>
      </select>
      </div></div>

      <div class="hidden option" style="margin-top: 15px;"><a class="floatLeft" href="javascript:;" onclick="$(this).closest('td').find('> div:not(.option_link)').toggle(0); $('.closeTR').toggle(0);">&nbsp;apply&nbsp;</a></div>


    <div class="qs_action_row">
    <a class="floatLeft" href="javascript:;" onclick="$(this).closest('td').find('> div:not(.option_link)').toggle(0); $('.closeTR').toggle(0);">&nbsp;<img src="/component/sl_menu/resources//pictures/qs_option.png">&nbsp;</a>
    <a class="floatLeft" href="javascript:;" onclick="$(this).closest('form').find('input:visible').val('').blur();">&nbsp;<img src="/component/form/resources/pictures/tree_clear.png" title="Clear quick search form" onclick="tp(this);">&nbsp;</a><a id="alt_submit" href="javascript:;" class="floatRight" onclick="
          var asContainer = goTabs.create('candi', '', '', 'Candidate QS');
          AjaxRequest('https://beta1.slate.co.jp/index.php5?uid=555-001&amp;ppa=ppasea&amp;ppt=candi&amp;ppk=0&amp;pg=ajx', 'body', 'quickSearchForm',  asContainer['id'], '', '', 'initHeaderManager(); ');
          goTabs.select(asContainer['number']);">&nbsp;<img src="/component/search/resources/pictures/search_24.png"></a>
          <input type="submit" style="opacity:0; width: 0px; height: 0px;">
    </div><p class="floatHack"></p></td></tr></tbody></table>
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