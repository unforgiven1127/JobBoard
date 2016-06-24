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

    //var yPosition = 0;
    //var xPosition = 0;

    $(document).ready(function(){
        $(".more").click(function(e){
            var id = this.id;
            //yPosition = e.clientY;
            //xPosition = e.clientX;
            //alert(yPosition);
            var hidden = id+"_hidden_div";
            document.getElementById(hidden).style.display = 'block';

        });

        $(".less").click(function(){
            var id = this.id;
            //alert(id);
            var hidden = id;
            var hidden = hidden+"_div";
            document.getElementById(hidden).style.display = 'none';
            //alert(yPosition);
            //window.scrollTo(xPosition, yPosition);

        });
    });

    </script>

    <style>

      .border_bottom
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

      ::-webkit-input-placeholder
      {
         padding-left:3px;
         font-size: 11pt;
        }
        :-moz-placeholder { /* older Firefox*/
         padding-left:3px;
         font-size: 11pt;
        }
        ::-moz-placeholder { /* Firefox 19+ */
         padding-left:3px;
         font-size: 11pt;
        }
        :-ms-input-placeholder {
         padding-left:3px;
         font-size: 11pt;
        }
        li{
          list-style-type: disc;
          list-style-position: inside;
          padding-left: 10px;
          text-indent: -11px;
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
            <a href="https://jobs.slate.co.jp/?setLang=jp" >
              <img align="right" style='width: 40px; ' src="http://jobs.slate.co.jp/component/display/resources/jobboard/pictures/japan.png" title="日本語">
            </a>
            <a href="https://jobs.slate.co.jp/?setLang=en" >
              <img align="right" style='margin-left:10px; margin-right:10px; width: 40px;' src="http://jobs.slate.co.jp/component/display/resources/jobboard/pictures/eng.png" title="English">
            </a>
          </td>
        </tr>
        <tr>
          <td style='padding-top:5px;' class='width'>
            <center>
              <input style="width:100%; height: 25px; font-size: 12pt; font-weight: bold; background-color:rgba(0,0,0,0) !important;" placeholder='<?php echo $keywords; ?>' type='text' class='form-control' name='keyword' id='keyword'>
            </center>
          </td>
        </tr>
        <tr>
          <td style='padding-top:5px;'>
            <center>
              <input style="width:100%; height: 25px; font-size: 12pt; font-weight: bold; background-color:rgba(0,0,0,0) !important;" placeholder='<?php echo $occupation; ?>' type='text' class='form-control' name='occupation' id='occupation'>
            </center>
          </td>
        </tr>
        <tr>
          <td style='padding-top:5px;'>
            <center>
              <select style="border: 1px solid grey; width:100%; height: 30px; font-size: 10pt; font-weight: bold; background-color:rgba(0,0,0,0) !important;" class="form-control" name="industry" id="industry">
                <option value='' ><?php echo $selectIndustry; ?></option>
                <?php foreach($industries as $key => $value){
                  echo "<option value='$key' >$value</option>";
                } ?>
              </select>
            </center>
          </td>
        </tr>
        <tr>
          <td style='padding-top:5px; padding-bottom:10px;'>
            <center><button style="font-size: 15pt; height: 40px;" type="submit" class="log-btn" ><?php echo $findJob; ?></button></center>
          </td>
        </tr>
        <?php
        $i = 2;
        foreach($positions as $key => $value)
        {
            $value['position_desc'] = str_replace('<strong class="seo_keyword">','',$value['position_desc']);

            if($i % 2 == 0)
            {
              $color = "background-color:rgba(138, 40, 40, 0.05);";
            }
            else
            {
              $color = "background-color:rgba(212, 212, 212, 0.4);";
            }
            echo "<tr id='".$i."_move' class='border_bottom' style='width:90% !important; ".$color." '>
                    <td style='padding-left:5%; padding-right:5%; width:90% !important;'>
                      <b><li style='font-size: 10pt; margin-top: 1em; margin-bottom: 0em;'>".$value['position_title']."</li></b>
                      <b><li style='font-size: 10pt; margin-top: 3px; margin-bottom: 0em;'>".$value['location']."</li></b>
                      <b><li style='font-size: 10pt; margin-top: 3px; margin-bottom: 0em;'>".$value['name']."</li></b>
                      <b><p id='".$i."' class='more' style='font-size: 10pt; margin-top: 1em;'>
                        <a style='font-size: 10pt; font-style: oblique;'>+ <?php echo $more; ?></a>
                      </p></b>
                    </td>
                  </tr>";
            echo "<tr id='".$i."_hidden_div' class='border_bottom' style='display:none; width:100% !important; ".$color." '>
                    <td style='padding-left:5%; padding-right:5%; width:90% !important;'>
                      <i><p style='font-size: 8pt; margin-top: 0em; margin-bottom: 0em;'><b style='font-size: 8pt;'>Description: <br></b>".nl2br($value['position_desc'])."</p></i>
                      <i><p style='font-size: 8pt; margin-top: 0em; margin-bottom: 0em;'><b style='font-size: 8pt;'><br>Requirements: <br></b>".nl2br($value['requirements'])."</p></i>
                      <table style='width:100%;'>
                        <tr>
                          <td style='width:50%;'>
                            <i><p class='less' id='".$i."_hidden' style='font-size: 8pt; margin-top: 1em; margin-bottom: 1em;'>
                              <a href='#".$i."_move' style='font-size: 10pt; font-style: oblique;'>- Click for less</a>
                            </p></i>
                          <td>

                          <td align='right' style='width:50%;'>
                            <i>
                              <p class='less' align='right' id='".$i."_hidden' style='font-size: 8pt; margin-top: 1em; margin-bottom: 1em;'>
                                <a href='#".$i."_move' style='font-size: 10pt; font-style: oblique;'># Apply</a>
                              </p>
                            </i>
                          <td>
                        </tr>
                      </table>
                    </td>
                  </tr>";
            $i++;
        } ?>
       </table></center>
     </form>

  </body>

</html>