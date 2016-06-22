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
    alert($('#keyword').width());
    windowWidth = windowWidth+'px';
    $('#keyword').width(windowWidth);
    $("#keyword").val("Dolly Duck");

    </script>

  </head>

  <body>

    <form action='https://jobs.slate.co.jp/index.php5?uid=153-160&ppa=ppal&ppt=job&ppk=0&pg=ajx' method='post'>
       <table style='margin-top:-50px;'>
        <tr >
          <td class='width'>
            <input placeholder='Keywords' type='text' class='form-control' name='keyword' id='keyword'>
          </td>
        </tr>
        <tr>
          <td style='width:100%; padding-top:15px;'>
            <input placeholder='Occupation' type='text' class='form-control' name='occupation' id='occupation'>
          </td>
        </tr>
       </table>
     </form>

  </body>

</html>