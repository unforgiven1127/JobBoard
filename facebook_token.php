<?php


$oFs = fopen('./facebook.log', 'a+');
if($oFs)
{
  fputs($oFs, date('Y-m-d H:i:s'));
  fputs($oFs, var_export($_GET, true).' || '.var_export($_POST, true));
  fclose($oFs);
}

?>