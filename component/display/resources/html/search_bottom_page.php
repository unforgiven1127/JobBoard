<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Sl[i]stem by Slate</title>

  <link rel="stylesheet" href="/common/style/login_style.css">
  <!--<link rel="stylesheet" type="text/css" href="common/lib/verticalSlider/css/style.css">-->
  <link href='https://fonts.googleapis.com/css?family=Merriweather' rel='stylesheet' type='text/css'>
  </head>

  <style>
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
  </style>

<body>
  <table>
    <tr>
      <td colspan="2">
          <table class='outherTableSearch' >
            <tr>
              <td valign="top">
              <?php if(isset($selectedCandi)){echo $selectedCandi;} ?>
              <td>
            <tr>
            <tr>
              <td valign="top" style='width: 980px;' >
                <table border="1" class='innerTableSearch' style='width: 980px;' >
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
                    <td class='innerTDSearch' style=' font-size: 13px; width:40px !important;'>
                      <input style='width:40px !important;' type="checkbox" name="vehicle" value="Bike">
                    </td>
                    <td class='innerTDSearch' style='padding-left: 5px; font-size: 13px;'><?php echo $value['sl_candidatepk']; ?></td>
                    <td class='innerTDSearch' style='padding-left: 5px; font-size: 13px;'>
                      <img src='<?php echo $value['grade']; ?>' />
                    </td>
                    <!--<td class='innerTD' onclick="pop_up('<?php echo $value['candiPopup']; ?>');" style='padding-left: 5px; font-size: 13px;'><?php echo $value['firstname']; ?></td>-->
                    <td class='innerTDSearch' onclick="openHref('<?php echo $value['candiPopup']; ?>');" style='padding-left: 5px; font-size: 13px;'><?php echo $value['firstname']; ?></td>
                    <td class='innerTDSearch' style='padding-left: 5px; font-size: 13px;'><?php echo $value['lastname']; ?></td>
                    <td class='innerTDSearch' style='padding-left: 5px; font-size: 13px;'><?php echo $value['title']; ?></td>
                    <td class='innerTDSearch' style='padding-left: 5px; font-size: 13px;'><?php echo $value['flag']; ?></td>
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
      </td>
    </tr>
  </table>

</body>

</html>