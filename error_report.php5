<?php
session_start();


//receive the ajax dump data
if(!empty($_POST['mail']))
{
  /* send an email with everything we've got:
   * http referer, session (user), dump data received
   *
   */

  $sMessage = 'Informations gathered concerning the crash:'."\n";

  $sMessage.="Session: \n\n";
  $sMessage.= var_export($_SESSION, true);
  $sMessage.= "\n\n";

  $sMessage.="Post: \n\n";
  $sMessage.= var_export($_POST, true);
  $sMessage.= "\n\n";

  /*mail('sboudoux@bulbouscell.com', 'Error on BC-CRM', $sMessage);
  sleep(4);*/
  header('location: /index.php5');

}


?>

<html>

<head>
<title>BC Media - CRM</title>

<link media="screen" type="text/css" href="/common/style/style.css?n=1317368869" rel="stylesheet">
<script src="/common/js/jquery.js" type="text/javascript"></script>
</head>

<body style="font-size:12px;">

Error reporting form<br/>
Post the form fields + http referer to go back to the previous page afetr sending the email to us
<br />
<br />
<br />
<br />


<fieldset>
<legend>Error report</legend>
<br />
Thank you for reporting the errors you're experiencing, we'll do our best to treat it as sonn as possible.<br />
Do not hesitate to call us if it's impossible for you to work because of this error.<br />
<br />


<form name='dumpForm' method='post' action='/error_report.php5' >

<input type='hidden' name='mail' value="1" />
<?php

//receive the ajax dump data
if(!empty($_POST['dump']))
{
  /* send an email with everything we've got:
   * http referer, session (user)
   */

  echo "<input type='hidden' name='dump' id='dumpId' value=\"".$_POST['dump']."\" />";
}
?>
<br />
User name : <input type='text' name='user' value="" /> <br /><br />
Error description : <textarea name='description' cols="120" rows="5" ></textarea><br /><br />
When did it occured : <textarea name='actions' cols="120" rows="5" ></textarea><br /><br />

<br />
<input type='submit' value="Send the error report" />
<br /><br />


</form>


</body>
</html>