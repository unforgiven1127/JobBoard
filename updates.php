<?php

	require_once('component/jobboard/jobboard.class.php5');
	require_once('component/taaggregator/resources/lib/encoding_converter.class.php5');

	//echo "Updates<br><br>";
$arrayMulti = array();



$array1=array( '	03-07-2012 17:39	','	354	','	279387	','	354	','	1	','	03-07-2012 17:39	','	Assessed in person	','	1	','	03-07-2012 17:39	');
$array2=array( '	01-02-2010 19:46	','	354	','	272772	','	354	','	1	','	01-02-2010 19:46	','	Assessed in person	','	1	','	01-02-2010 19:46	');
$array3=array( '	26-09-2008 13:03	','	354	','	262416	','	354	','	1	','	26-09-2008 13:03	','	Assessed in person	','	1	','	26-09-2008 13:03	');
$array4=array( '	16-09-2008 20:26	','	354	','	259143	','	354	','	1	','	16-09-2008 20:26	','	Assessed in person	','	1	','	16-09-2008 20:26	');
$array5=array( '	11-09-2008 12:22	','	363	','	257856	','	363	','	1	','	11-09-2008 12:22	','	Assessed in person	','	1	','	11-09-2008 12:22	');
$array6=array( '	18-09-2008 17:23	','	333	','	257422	','	333	','	1	','	18-09-2008 17:23	','	Assessed in person	','	1	','	18-09-2008 17:23	');
$array7=array( '	24-09-2008 12:25	','	270	','	254697	','	270	','	1	','	24-09-2008 12:25	','	Assessed in person	','	1	','	24-09-2008 12:25	');
$array8=array( '	28-02-2013 17:27	','	155	','	252792	','	360	','	1	','	28-02-2013 17:27	','	Assessed in person	','	1	','	28-02-2013 17:27	');

array_push($arrayMulti,$array1);
array_push($arrayMulti,$array2);
array_push($arrayMulti,$array3);
array_push($arrayMulti,$array4);
array_push($arrayMulti,$array5);
array_push($arrayMulti,$array6);
array_push($arrayMulti,$array7);
array_push($arrayMulti,$array8);



foreach ($arrayMulti as $key => $array)
{
	foreach ($array as $key => $value)
	{
		$array[$key] = TRIM($value);
		echo $array[$key]." - ";
	}
	echo "<br><br>";
}

	/*JOBBOARD CONNECTION INFO
	define('DB_NAME', 'jobboard');
    define('DB_SERVER', '127.0.0.1');
    define('DB_USER', 'jobboard');
    define('DB_PASSWORD', 'KCd7C56XJ8Nud7uF');
    /*JOBBOARD CONNECTION INFO

    /*SLISTEM CONNECTION INFO
	define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');
    SLISTEM CONNECTION INFO*/

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

	/*define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');

	mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
    mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());

	foreach ($array as $key => $value)
	{
		$id = TRIM($key);
		$jpTitle = TRIM($value);

		echo $id.$jpTitle."<br><br>";

	    $slistemQuery = " UPDATE sl_location SET location_jp = '".$jpTitle."' WHERE sl_locationpk ='".$id."'";

    	$slistemQuery = mysql_query($slistemQuery);

	}*/

