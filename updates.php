<?php

	require_once('component/jobboard/jobboard.class.php5');
	require_once('component/taaggregator/resources/lib/encoding_converter.class.php5');
	require_once('common/lib/phpExcel/Classes/PHPExcel.php');

$fileName = "oldKeys.xlsx";
$excelReader = PHPExcel_IOFactory::createReaderForFile($fileName);

$excelReader->setReadDataOnly();

//load only certain sheets from the file
//$loadSheets = array('keywords');
//$excelReader->setLoadSheetsOnly($loadSheets);

//the default behavior is to load all sheets
//$excelReader->setLoadAllSheets();

/*$excelObj = $excelReader->load($fileName);

$excelObj->getActiveSheet()->toArray(null, true,true,true);

$worksheetNames = $excelObj->getSheetNames($fileName);
$return = array();
foreach($worksheetNames as $key => $sheetName)
{
	//set the current active worksheet by name
	$excelObj->setActiveSheetIndexByName($sheetName);
	//create an assoc array with the sheet name as key and the sheet contents array as value
	$return[$sheetName] = $excelObj->getActiveSheet()->toArray(null, true,true,true);

}*/
//show the final array


	//echo "Updates<br><br>";
/*$arrayMulti = array();



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

/*foreach($multiArray as $key => $value)
{
	var_dump($value);
	echo "<br><br>";
}*/

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

    $array = array('	2016-01-01	','	Friday	','	New Years day	','	National holiday	',
'	2016-01-02	','	Saturday	','	January 2 Bank Holiday	','	Bank holiday	',
'	2016-01-03	','	Sunday	','	January 3 Bank Holiday	','	Bank holiday	',
'	2016-01-11	','	Monday	','	Coming of Age Day	','	National holiday	',
'	2016-02-11	','	Thursday	','	National Foundation Day	','	National holiday	',
'	2016-03-03	','	Thursday	','	Dolls Festival/Girls Festival	','	Observance	',
'	2016-03-20	','	Sunday	','	March equinox	','	Season	',
'	2016-03-20	','	Sunday	','	Spring Equinox	','	National holiday	',
'	2016-03-21	','	Monday	','	Spring Equinox observed	','	National holiday	',
'	2016-04-29	','	Friday	','	Showa Day	','	National holiday	',
'	2016-05-03	','	Tuesday	','	Constitution Memorial Day	','	National holiday	',
'	2016-05-04	','	Wednesday	','	Greenery Day	','	National holiday	',
'	2016-05-05	','	Thursday	','	Childrens Day	','	National holiday	',
'	2016-06-20	','	Monday	','	June Solstice	','	Season	',
'	2016-07-07	','	Thursday	','	Star Festival	','	Observance	',
'	2016-07-18	','	Monday	','	Sea Day	','	National holiday	',
'	2016-08-11	','	Thursday	','	Mountain Day	','	National holiday	',
'	2016-09-19	','	Monday	','	Respect for the Aged Day	','	National holiday	',
'	2016-09-22	','	Thursday	','	September equinox	','	Season	',
'	2016-09-22	','	Thursday	','	Autumn Equinox	','	National holiday	',
'	2016-10-10	','	Monday	','	Sports Day	','	National holiday	',
'	2016-11-03	','	Thursday	','	Culture Day	','	National holiday	',
'	2016-11-15	','	Tuesday	','	7-5-3 Day	','	Observance	',
'	2016-11-23	','	Wednesday	','	Labor Thanksgiving Day	','	National holiday	',
'	2016-12-21	','	Wednesday	','	December Solstice	','	Season	',
'	2016-12-23	','	Friday	','	Emperors Birthday	','	National holiday	',
'	2016-12-25	','	Sunday	','	Christmas	','	Observance	',
'	2016-12-31	','	Saturday	','	December 31 Bank Holiday	','	Bank holiday	');

$addArray = array();
$multiArray = array();

$i = 0;
foreach($array as $key => $value)
{

	$addArray[$i] = TRIM($value);
	$i++;
	if($i == 4)
	{
		array_push($multiArray,$addArray);
		$i = 0;
	}
}

foreach($multiArray as $key => $value)
{
	$holiday_date = $value[0];
	$holiday_day = $value[1];
	$holiday_name = $value[2];
	$holiday_type = $value[3];

	$slistemQuery = " INSERT INTO sl_meeting (holiday_date,holiday_day,holiday_name,holiday_type)
					  VALUES('".$holiday_date."','".$holiday_day."','".$holiday_name."','".$holiday_type."')";

	echo $slistemQuery."<br><br>";

	$slistemQuery = mysql_query($slistemQuery);

}
    /*$slistemQuery = " SELECT * FROM login l WHERE l.status = '1'";
    $slistemQuery = mysql_query($slistemQuery);

	while($userData = mysql_fetch_assoc($slistemQuery))
	{
		$pass = $userData['password'];
		$user_id = $userData['loginpk'];
		$pass_encrypted = sha1($pass);

		$slistemQueryUpdate = "UPDATE login SET password_crypted = '".$pass_encrypted."' WHERE loginpk = '".$user_id."'";
		$slistemQueryUpdate = mysql_query($slistemQueryUpdate);

		echo $user_id." - ".$pass_encrypted;
		echo "<br><br>";
	}*/

    //$i = 0;
    /*foreach ($return as $key => $array)
	{
		foreach ($array as $key => $value)
		{
			$candidate_id = $value['A'];
			$keyword = $value['B'];

			$slistemQuery = "SELECT * FROM sl_candidate_profile WHERE candidatefk = '".$candidate_id."'";
			$slistemQuery = mysql_query($slistemQuery);
			$candidateData = mysql_fetch_assoc($slistemQuery);

			$keyword = $candidateData['keyword'];
			$exploded = explode(',',$keyword);


			$newKeywordArray = array();
			foreach ($exploded as $key => $value)
			{
				if(!in_array(TRIM($value),$newKeywordArray) && !empty(TRIM($value)))
				{
					array_push($newKeywordArray,TRIM($value));
				}
			}

			$newKeyword = implode(',',$newKeywordArray);

			//$newKeyword = $candidateData['keyword']." , ".$keyword;

			$slistemQuery = "UPDATE sl_candidate_profile SET keyword ='".$newKeyword."' WHERE candidatefk = '".$candidate_id."'";
			//$slistemQuery = mysql_query($slistemQuery);
			//var_dump($newKeyword);
			//echo "<br><br>";
			$i++;
			//echo $candidateData['keyword']." ------ ".$value['A']." - ".$value['B']." - ".$newKeyword;
			//echo "<br><br>";
		}
	}
	echo $i." data updated";*/
	/*foreach ($multiArray as $key => $array)
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

    	//$slistemQuery = mysql_query($slistemQuery);
	}*/

	/*foreach ($array as $key => $value)
	{
		$id = TRIM($key);
		$jpTitle = TRIM($value);

		echo $id.$jpTitle."<br><br>";

	    $slistemQuery = " UPDATE sl_location SET location_jp = '".$jpTitle."' WHERE sl_locationpk ='".$id."'";

    	$slistemQuery = mysql_query($slistemQuery);

	}*/

