<!DOCTYPE html>
<html >
  <head>
    <meta name="HandheldFriendly" content="true" />
    <meta name="MobileOptimized" content="320" />
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, width=device-width, user-scalable=no" />
    <title>Slate Consulting</title>
    <link rel="stylesheet" href="common/lib/bootstrap/css/bootstrap.min.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>



    <script type="text/javascript">

    $(document).ready(function(){
        $(".more").click(function(){
            var id = this.id;
            //alert(id);
            var hidden = id+"_hidden";
            document.getElementById(hidden).style.display = 'block';

        });

        $(".less").click(function(){
            var id = this.id;
            //alert(id);
            var hidden = id;
            document.getElementById(hidden).style.display = 'none';

        });
    });

    </script>

    <style>

      tr.border_bottom td
      {
        border-bottom:1pt solid black;
      }

      .log-btn
      {
        background: #892828;
        dispaly: inline-block;
        width: 100%;
        font-size: 16px;
        height: 50px;
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

    <form action='https://jobs.slate.co.jp/index.php5?uid=153-160&ppa=ppal&ppt=job&ppk=0&pg=ajx' method='post'>
       <center><table bgcolor="white" style='width:90%; '>
        <tr>
          <td >
            <a href="http://www.slate.co.jp">
              <img style=' width: 150px;' src="http://jobs.slate.co.jp/component/display/resources/jobboard/pictures/top_slate_logo.png" title="Slate Consulting job board">
            </a>
            <a href="" >
              <img align="right" style='width: 40px; ' src="http://jobs.slate.co.jp/component/display/resources/jobboard/pictures/japan.png" title="日本語">
            </a>
            <a href="" >
              <img align="right" style='margin-left:10px; margin-right:10px; width: 40px;' src="http://jobs.slate.co.jp/component/display/resources/jobboard/pictures/eng.png" title="English">
            </a>
          </td>
        </tr>
        <tr>
          <td style='padding-top:5px;' class='width'>
            <center>
              <input style="width:100%; height: 25px; font-size: 12pt; font-weight: bold; background-color:rgba(0,0,0,0) !important;" placeholder='Keywords' type='text' class='form-control' name='keyword' id='keyword'>
            </center>
          </td>
        </tr>
        <tr>
          <td style='padding-top:5px;'>
            <center>
              <input style="width:100%; height: 25px; font-size: 12pt; font-weight: bold; background-color:rgba(0,0,0,0) !important;" placeholder='Occupation' type='text' class='form-control' name='occupation' id='occupation'>
            </center>
          </td>
        </tr>
        <tr>
          <td style='padding-top:5px;'>
            <center>
              <select style="border: 1px solid grey; width:100%; height: 30px; font-size: 10pt; font-weight: bold; background-color:rgba(0,0,0,0) !important;" class="form-control" name="industry" id="industry">
                <option value='' >Select Industry</option>
                <?php foreach($industries as $key => $value){
                  echo "<option value='$key' >$value</option>";
                } ?>
              </select>
            </center>
          </td>
        </tr>
        <tr>
          <td style='padding-top:5px; padding-bottom:10px;'>
            <center><button style="font-size: 15pt; height: 40px;" type="submit" class="log-btn" >Find Job</button></center>
          </td>
        </tr>
        <?php
        $i = 2;
        foreach($positions as $key => $value)
        {
            if($i % 2 == 0)
            {
              $color = "background-color:rgba(138, 40, 40, 0.05);";
            }
            else
            {
              $color = "background-color:rgba(212, 212, 212, 0.4);";
            }
            echo "<tr class='border_bottom' style='width:90% !important; ".$color." '>
                    <td id='".$i."' class='more' style='padding-left:5%; padding-right:5%; width:90% !important;'>
                      <b><p style='font-size: 10pt;'>".$value['position_title']."</p></b>
                      <b><p style='font-size: 10pt;'>".$value['location']."</p></b>
                      <b><p style='font-size: 10pt;'>".$value['name']."</p></b>
                      <b><p style='font-size: 10pt;'>+ Click for more</p></b>
                    </td>
                  </tr>";
            echo "<tr class='less' id='".$i."_hidden' class='border_bottom' style='display:none; width:90% !important; ".$color." '>
              <td style='padding-left:5%; padding-right:5%; width:90% !important;'>
                <i><p style='font-size: 8pt;'><b>Description: <br></b>".($value['position_desc'])."</p></i>
                <i><p style='font-size: 8pt;'><b>Requirements: <br></b>".($value['requirements'])."</p></i>
                <i><p style='font-size: 8pt;'>- Click for less</p></i>
              </td>
            </tr>";
            $i++;
        } ?>
       </table></center>
     </form>

  </body>

</html>