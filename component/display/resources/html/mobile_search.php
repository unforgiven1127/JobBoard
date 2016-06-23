<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Slate Consulting</title>
    <link rel="stylesheet" href="common/lib/bootstrap/css/bootstrap.min.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>



    <script type="text/javascript">

    var windowWidth = window.innerWidth;
    windowWidth = "'"+windowWidth+"px'";
    //alert($('#keyword').width());
    //document.getElementById("keyword").style.width = windowWidth;

    </script>

    <style>
      .log-btn
      {
        background: #892828;
        dispaly: inline-block;
        width: 50%;
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
       <table bgcolor="white" style='width:100%; background: url("http://jobs.slate.co.jp/component/display/resources/jobboard/pictures/bg_s.png") no-repeat center center fixed;'>
        <tr>
          <td >
            <a href="http://www.slate.co.jp">
              <img style='margin-left:50px; width: 400px;' src="http://jobs.slate.co.jp/component/display/resources/jobboard/pictures/top_slate_logo.png" title="Slate Consulting job board">
            </a>
            <a href="" >
              <img align="right" style='width: 100px; margin-right:10px;' src="http://jobs.slate.co.jp/component/display/resources/jobboard/pictures/eng.png" title="English">
            </a>
            <a href="" >
              <img align="right" style='margin-left:10px; margin-right:50px; width: 100px;' src="http://jobs.slate.co.jp/component/display/resources/jobboard/pictures/japan.png" title="日本語">
            </a>
          </td>
        </tr>
        <tr>
          <td style='padding-top:15px;' class='width'>
            <center>
              <input style="width:90%; height: 100px; font-size: 40pt; font-weight: bold; background-color:rgba(0,0,0,0) !important;" placeholder='Keywords' type='text' class='form-control' name='keyword' id='keyword'>
            </center>
          </td>
        </tr>
        <tr>
          <td style='padding-top:15px;'>
            <center>
              <input style="width:90%; height: 100px; font-size: 40pt; font-weight: bold; background-color:rgba(0,0,0,0) !important;" placeholder='Occupation' type='text' class='form-control' name='occupation' id='occupation'>
            </center>
          </td>
        </tr>
        <tr>
          <td style='padding-top:15px;'>
            <center>
              <select style="width:90%; height: 100px; font-size: 40pt; font-weight: bold; background-color:rgba(0,0,0,0) !important;" class="form-control" name="industry" id="industry">
                <option value='' >Select Industry</option>
                <?php foreach($industries as $key => $value){
                  echo "<option value='$key' >$value</option>";
                } ?>
              </select>
            </center>
          </td>
        </tr>
        <tr>
          <td style='padding-top:15px;'>
            <center><button style="font-size: 40pt; height: 100px;" type="submit" class="log-btn" >Find Job</button></center>
          </td>
        </tr>
       </table>
     </form>

  </body>

</html>