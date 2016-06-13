<?php

require_once('component/database/database.class.php5');
require_once ('component/database/dbresult.class.php5');

class CDatabaseEx extends CDatabase
{
  private $coConnection;

  public function __construct()
  {

  }

  public function __destruct()
  {
    return $this->dbClose();
  }

  function dbConnnectSlistem()
  {
    try
    {
      $this->coConnection = @mysql_connect(DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM);
      if(!$this->coConnection)
        exit('error '.__LINE__.': No database connection available.');

      $slistemConnected = @mysql_select_db(DB_NAME_SLISTEM);

      if(!$slistemConnected)
        exit('error '.__LINE__.': Can\'t connect to the database.');
    }
    catch (Exception $e)
    {
      exit('DB connection failure.');
    }

    return true;
  }

  function slistemGetAllData($query)
  {
    mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
    mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());

    $slistemQuery = mysql_query($query);
    //$result = mysql_fetch_assoc($slistemQuery);

    $result = array();
    $return = array();
    //$i = 0;

    while($row = mysql_fetch_assoc($slistemQuery))
    {
      //mysql_fetch_assoc($slistemQuery);

      //ChromePhp::log($add);
      array_push($result,$row);
      //$result[] = $row;
      //$i = $row['count'];
    }
//ChromePhp::log($result);
    //$return['count'] = $i;
    $return = $result;
    return $return;

  }

  function dbConnect()
  {
    try
    {
      $this->coConnection = @mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
      if(!$this->coConnection)
        exit('error '.__LINE__.': No database connection available.');

      $bConnected = @mysql_select_db(DB_NAME);

      if(!$bConnected)
        exit('error '.__LINE__.': Can\'t connect to the database.');
    }
    catch (Exception $e)
    {
      exit('DB connection failure.');
    }

    return true;
  }

  function dbClose()
  {
    if(!empty($this->coConnection))
    {
      mysql_close($this->coConnection);
      unset($this->coConnection);
    }
    return true;
  }


  function ExecuteQuery($psQuery)
  {
    //the function should always return an dbResult object
    $oDbResult = new CDbResult();


    if(!$this->dbConnect())
      exit('can\'t connect db in ExecuteQuery line '.__LINE__);

    //doesn't accept UNION query for now
    $sQueryType = substr(trim(strtolower($psQuery)),0, 3);
    if($sQueryType == 'sel')
    {
      try
      {
        $fTimeStart = microtime(true);
        $oSQLResult = mysql_query($psQuery, $this->coConnection);
        $oDbResult->loadDbResult($oSQLResult);

        if(!$oDbResult->isLoaded())
          throw new Exception();

        if (isset($_SESSION['debug']) && $_SESSION['debug'] == 'sql')
          echo round((microtime(true) -$fTimeStart)*1000, 2).' ms--> '.$psQuery.'<br />';

      }
      catch (Exception $e)
      {
        if(isDevelopment())
        {
          echo "Sorry, there seems to have been a problem with your query. An administrator has been notified.";
          echo' Connection: ';dump($this->coConnection);echo '<br />';
          echo' Query: ';dump($psQuery);echo '<br />';
          echo' oResult: ';dump($oSQLResult);echo'<br />';
          echo' oDbResult: ';dump($oDbResult);echo'<br /><br />';
          echo mysql_errno($this->coConnection).' : '.mysql_error($this->coConnection);
        }
      }
    }
    else
    {
      //update, insert, delete
      try
      {
        $fTimeStart = microtime(true);
        $oSQLResult = mysql_query($psQuery, $this->coConnection);

        if($oSQLResult === false)
          throw new Exception();

        if (isset($_SESSION['debug']) && $_SESSION['debug'] == 'sql')
          echo round((microtime(true) -$fTimeStart)*1000, 2).' ms--> '.$psQuery.'<br />';

        if($sQueryType == 'ins' && mysql_insert_id())
        {
          $oDbResult->setFieldValue('pk', (int)mysql_insert_id());
          return $oDbResult;

          //return (int)mysql_insert_id();
        }

        return true;
      }
      catch (Exception $e)
      {
        if(isDevelopment())
        {
          echo "Sorry, there seems to have been a problem with your query. An administrator has been notified.";
          echo' Connection: ';dump($this->coConnection);echo '<br />';
          echo' Query: ';dump($psQuery);echo '<br />';
          echo' oResult: ';dump($oSQLResult);echo'<br />';
          echo' oDbResult: ';dump($oDbResult);echo'<br /><br />';
          echo mysql_errno($this->coConnection).' : '.mysql_error($this->coConnection);
        }
      }
    }

    return $oDbResult;
  }


  public function dbEscapeString($pvValue, $pvDefault = '', $pbNoQuotes = false)
  {
    if(!$this->coConnection)
      $this->dbConnect();

    $vCleanValue =  mysql_real_escape_string($pvValue, $this->coConnection);

    if(empty($vCleanValue) && !empty($pvDefault))
      $vCleanValue =mysql_real_escape_string($pvDefault);

    if(strtolower($vCleanValue) == 'null')
      return 'NULL';

    $sEncoding = mb_detect_encoding( $vCleanValue, "auto" );
    $sString= mb_convert_encoding( $vCleanValue, "UTF-8", $sEncoding);


    if($pbNoQuotes)
      return $vCleanValue;
    else
      return '"'.$vCleanValue.'"';

  }
}