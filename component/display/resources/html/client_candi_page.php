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

.innerTable{
  border:1px solid #DADADA;
}

.innerTD
{
  padding-bottom: 5px;
  padding-top: 5px;
}

</style>

  </head>


  <body>

  <table >
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
            <th style='padding-left: 5px; font-size: 15px;'>Last state</th>
          </tr>

          <?php for($i=0;$i<6;$i++){ ?>

          <tr>
            <td class='innerTD' style=' font-size: 13px; width:40px !important;'>
              <input style='width:40px !important;' type="checkbox" name="vehicle" value="Bike">
            </td>
            <td class='innerTD' style='padding-left: 5px; font-size: 13px;'>123456</td>
            <td class='innerTD' style='padding-left: 5px; font-size: 13px;'>
              <img src="/common/pictures/star.png" />
              <img src="/common/pictures/star.png" />
              <img src="/common/pictures/star.png" />
              <img src="/common/pictures/star.png" />
              <img src="/common/pictures/star_grey.png" />
            </td>
            <td class='innerTD' style='padding-left: 5px; font-size: 13px;'>Anameric</td>
            <td class='innerTD' style='padding-left: 5px; font-size: 13px;'>Munir</td>
            <td class='innerTD' style='padding-left: 5px; font-size: 13px;'>Software Engineer</td>
            <td class='innerTD' style='padding-left: 5px; font-size: 13px;'>XYZ</td>
          </tr>

          <?php } ?>

        </table>
      </td>
    </tr>
    <tr>
      <td>
        
      </td>
    </tr>
  </table>


  </body>

</html>