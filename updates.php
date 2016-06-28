<?php

	require_once('component/jobboard/jobboard.class.php5');
	require_once('component/taaggregator/resources/lib/encoding_converter.class.php5');

	//echo "Updates<br><br>";
$array=array(	'	8893	',
	'	8892	',
	'	8891	',
	'	8890	',
	'	8889	',
	'	8863	',
	'	8857	',
	'	8839	',
	'	8838	',
	'	8837	',
	'	8789	',
	'	8778	',
	'	8777	',
	'	8776	',
	'	8775	',
	'	8774	',
	'	8762	',
	'	8760	',
	'	8759	',
	'	8758	',
	'	8757	',
	'	8755	',
	'	8754	',
	'	8753	',
	'	8751	',
	'	8742	',
	'	8741	',
	'	8740	',
	'	8739	',
	'	8738	',
	'	8737	',
	'	8736	',
	'	8709	',
	'	8708	',
	'	8706	',
	'	8705	',
	'	8702	',
	'	8701	',
	'	8700	',
	'	8699	',
	'	8698	',
	'	8697	',
	'	8695	',
	'	8694	',
	'	8693	',
	'	8691	',
	'	8690	',
	'	8689	',
	'	8688	',
	'	8687	',
	'	8680	',
	'	8679	',
	'	8677	',
	'	8676	',
	'	8675	',
	'	8674	',
	'	8673	',
	'	8669	',
	'	8668	',
	'	8667	',
	'	8665	',
	'	8664	',
	'	8663	',
	'	8662	',
	'	8661	',
	'	8660	',
	'	8659	',
	'	8658	',
	'	8657	',
	'	8656	',
	'	8655	',
	'	8654	',
	'	8653	',
	'	8652	',
	'	8651	',
	'	8649	',
	'	8648	',
	'	8647	',
	'	8646	',
	'	8643	',
	'	8641	',
	'	8640	',
	'	8639	',
	'	8638	',
	'	8637	',
	'	8636	',
	'	8633	',
	'	8632	',
	'	8631	',
	'	8630	',
	'	8629	',
	'	8628	',
	'	8627	',
	'	8625	',
	'	8624	',
	'	8621	',
	'	8620	',
	'	8619	',
	'	8614	',
	'	8613	',
	'	8609	',
	'	8607	',
	'	8606	',
	'	8605	',
	'	8604	',
	'	8603	',
	'	8602	',
	'	8595	',
	'	8591	',
	'	8590	',
	'	8589	',
	'	8588	',
	'	8587	',
	'	8586	',
	'	8585	',
	'	8584	',
	'	8583	',
	'	8581	',
	'	8580	',
	'	8570	',
	'	8569	',
	'	8568	',
	'	8566	',
	'	8565	',
	'	8564	',
	'	8563	',
	'	8562	',
	'	8561	',
	'	8560	',
	'	8556	',
	'	8555	',
	'	8554	',
	'	8553	',
	'	8552	',
	'	8551	',
	'	8550	',
	'	8549	',
	'	8548	',
	'	8547	',
	'	8546	',
	'	8545	',
	'	8544	',
	'	8542	',
	'	8541	',
	'	8537	',
	'	8536	',
	'	8528	',
	'	8527	',
	'	8526	',
	'	8525	',
	'	8524	',
	'	8523	',
	'	8522	',
	'	8521	',
	'	8520	',
	'	8519	',
	'	8518	',
	'	8517	',
	'	8516	',
	'	8515	',
	'	8514	',
	'	8513	',
	'	8512	',
	'	8509	',
	'	8507	',
	'	8506	',
	'	8505	',
	'	8504	',
	'	8503	',
	'	8502	',
	'	8501	',
	'	8497	',
	'	8496	',
	'	8495	',
	'	8488	',
	'	8487	',
	'	8486	',
	'	8485	',
	'	8484	',
	'	8483	',
	'	8481	',
	'	8478	',
	'	8477	',
	'	8476	',
	'	8475	',
	'	8473	',
	'	8470	',
	'	8469	',
	'	8467	',
	'	8466	',
	'	8465	',
	'	8464	',
	'	8463	',
	'	8462	',
	'	8457	',
	'	8456	',
	'	8455	',
	'	8454	',
	'	8448	',
	'	8447	',
	'	8446	',
	'	8443	',
	'	8440	',
	'	8439	',
	'	8438	',
	'	8437	',
	'	8436	',
	'	8435	',
	'	8432	',
	'	8431	',
	'	8429	',
	'	8428	',
	'	8427	',
	'	8425	',
	'	8424	',
	'	8423	',
	'	8412	',
	'	8410	',
	'	8409	',
	'	8408	',
	'	8407	',
	'	8406	',
	'	8404	',
	'	8403	',
	'	8402	',
	'	8401	',
	'	8400	',
	'	8399	',
	'	8398	',
	'	8394	',
	'	8393	',
	'	8391	',
	'	8390	',
	'	8388	',
	'	8387	',
	'	8386	',
	'	8384	',
	'	8383	',
	'	8381	',
	'	8380	',
	'	8379	',
	'	8378	',
	'	8377	',
	'	8376	',
	'	8371	',
	'	8342	',
	'	8341	',
	'	8339	',
	'	8335	',
	'	7779	',
	'	7778	',
	'	7689	');

foreach ($array as $key => $value)
{
	$array[$key] = TRIM($value);
	//echo $array[$key]."<br>";
}

	/*JOBBOARD CONNECTION INFO*/
	define('DB_NAME', 'jobboard');
    define('DB_SERVER', '127.0.0.1');
    define('DB_USER', 'jobboard');
    define('DB_PASSWORD', 'KCd7C56XJ8Nud7uF');
    /*JOBBOARD CONNECTION INFO*/

    /*SLISTEM CONNECTION INFO*/
	define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');
    /*SLISTEM CONNECTION INFO*/

    /*JOBBOARD ISLEMLERI ICIN*/
    mysql_connect( DB_SERVER, DB_USER, DB_PASSWORD) or die(mysql_error());
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
    	$slistemQuery = "UPDATE sl_position_detail SET description = '".$value['position_desc']."' WHERE positionfk = '".$value['external_key']."'";

    	$slistemQuery = mysql_query($slistemQuery);
    	$positionData = mysql_fetch_assoc($slistemQuery);
    }

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

