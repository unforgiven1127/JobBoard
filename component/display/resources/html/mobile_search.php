<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Slate Consulting</title>

    <script type="text/javascript">

    var windowWidth = window.innerWidth;
    //alert(windowWidth);
    $('#keyword').width(windowWidth);

    </script>

  </head>

  <body>

    <form action='https://jobs.slate.co.jp/index.php5?uid=153-160&ppa=ppal&ppt=job&ppk=0&pg=ajx' method='post'>
       <table style='margin-top:-50px;'>
        <tr style='width:100%;'>
          <td style='width:100%;'>
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