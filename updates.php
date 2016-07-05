<?php

	require_once('component/jobboard/jobboard.class.php5');
	require_once('component/taaggregator/resources/lib/encoding_converter.class.php5');

	//echo "Updates<br><br>";
$arrayMulti = array();



$array=array( '	2012-07-03 17:39	','	354	','	279387	','	354	','	1	','	2012-07-03 17:39	','	Assessed in person	','	1	','	2012-07-03 17:39	',
'	2010-02-01 19:46	','	354	','	272772	','	354	','	1	','	2010-02-01 19:46	','	Assessed in person	','	1	','	2010-02-01 19:46	',
'	2008-09-26 13:03	','	354	','	262416	','	354	','	1	','	2008-09-26 13:03	','	Assessed in person	','	1	','	2008-09-26 13:03	',
'	2008-09-16 20:26	','	354	','	259143	','	354	','	1	','	2008-09-16 20:26	','	Assessed in person	','	1	','	2008-09-16 20:26	',
'	2008-09-11 12:22	','	363	','	257856	','	363	','	1	','	2008-09-11 12:22	','	Assessed in person	','	1	','	2008-09-11 12:22	',
'	2008-09-18 17:23	','	333	','	257422	','	333	','	1	','	2008-09-18 17:23	','	Assessed in person	','	1	','	2008-09-18 17:23	',
'	2008-09-24 12:25	','	270	','	254697	','	270	','	1	','	2008-09-24 12:25	','	Assessed in person	','	1	','	2008-09-24 12:25	',
'	2013-02-28 17:27	','	155	','	252792	','	360	','	1	','	2013-02-28 17:27	','	Assessed in person	','	1	','	2013-02-28 17:27	');

$addArray = array();
$multiArray = array();

$i = 0;
foreach($array as $key => $value)
{

	$addArray[$i] = TRIM($value);
	$i++;
	if($i == 9)
	{
		array_push($multiArray,$addArray);
		$i = 0;
	}
}

foreach($multiArray as $key => $value)
{
	var_dump($value);
	echo "<br><br>";
}

/*foreach ($arrayMulti as $key => $array)
{
	foreach ($array as $key => $value)
	{
		$array[$key] = TRIM($value);
		echo $array[$key]." - ";
	}
	echo "<br><br>";
}*/

	/*JOBBOARD CONNECTION INFO
	define('DB_NAME', 'jobboard');
    define('DB_SERVER', '127.0.0.1');
    define('DB_USER', 'jobboard');
    define('DB_PASSWORD', 'KCd7C56XJ8Nud7uF');
    JOBBOARD CONNECTION INFO*/

    //SLISTEM CONNECTION INFO
	define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');
    //SLISTEM CONNECTION INFO



    /*JOBBOARD ISLEMLERI ICIN*/
    /*mysql_connect( DB_SERVER, DB_USER, DB_PASSWORD) or die(mysql_error());
    mysql_select_db(DB_NAME) or die(mysql_error());

    $positionArrayJB = array();

    foreach ($array as $key => $value)
    {
    	$slistemQuery = "SELECT * FROM position p WHERE p.external_key = '".$value."'";

    	$slistemQuery = mysql_query($slistemQuery);
    	$positionData = mysql_fetch_assoc($slistemQuery);
//var_dump($positionData);
//echo'<br><br>';
    	array_push($positionArrayJB,$positionData);
    }
//echo "<br><br><br><br>";
//var_dump($positionArrayJB);
//

	define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');
    foreach ($positionArrayJB as $key => $value)
   	{
    	$slistemQuery = "UPDATE sl_position_detail SET requirements = '".$value['requirements']."' WHERE positionfk = '".$value['external_key']."'";

    	$slistemQuery = mysql_query($slistemQuery);
    	$positionData = mysql_fetch_assoc($slistemQuery);
    }*/

	define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');

	mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
    mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());


	/*foreach ($arrayMulti as $key => $array)
	{
		$date_created = TRIM($array[0]);
		$created_by = TRIM($array[1]);
		$candidatefk = TRIM($array[2]);
		$attendeefk = TRIM($array[3]);
		$type = TRIM($array[4]);
		$date_meeting = TRIM($array[5]);
		$description = TRIM($array[6]);
		$meeting_done = TRIM($array[7]);
		$date_met = TRIM($array[8]);

		$slistemQuery = " INSERT INTO sl_meeting (date_created,created_by,candidatefk,attendeefk,type,date_meeting,description,meeting_done,date_met) VALUES('".$date_created."','".$created_by."','".$candidatefk."','".$attendeefk."','".$type."','".$date_meeting."','".$description."','".$meeting_done."','".$date_met."')";

		echo $slistemQuery."<br><br>";

    	$slistemQuery = mysql_query($slistemQuery);
	}*/

	/*foreach ($array as $key => $value)
	{
		$id = TRIM($key);
		$jpTitle = TRIM($value);

		echo $id.$jpTitle."<br><br>";

	    $slistemQuery = " UPDATE sl_location SET location_jp = '".$jpTitle."' WHERE sl_locationpk ='".$id."'";

    	$slistemQuery = mysql_query($slistemQuery);

	}*/

