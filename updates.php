<?php

	require_once('component/jobboard/jobboard.class.php5');
	require_once('component/taaggregator/resources/lib/encoding_converter.class.php5');

	echo "Updates<br><br>";
$array=array('	1	 '=> '	東京	',
'	2	 '=> '	エドモントン	',
'	3	 '=> '	バンクーバー	',
'	4	 '=> '	マニラ	',
'	5	 '=> '	ロサンゼルス	',
'	6	 '=> '	サンフランシスコ	',
'	7	 '=> '	シアトル	',
'	8	 '=> '	ロンドン	',
'	9	 '=> '	香港	',
'	10	 '=> '	シンガポール	',
'	11	 '=> '	台湾	',
'	12	 '=> '	上海	',
'	13	 '=> '	北京	',
'	14	 '=> '	マカオ	',
'	15	 '=> '	トロント	',
'	16	 '=> '	カルガリー	',
'	17	 '=> '	ニューヨーク	',
'	18	 '=> '	シカゴ	',
'	19	 '=> '	ボストン	',
'	20	 '=> '	大阪	',
'	21	 '=> '	名古屋	',
'	22	 '=> '	モスクワ	',
'	23	 '=> '	ドバイ	',
'	24	 '=> '	ブカレスト	',
'	25	 '=> '	アムステルダム	',
'	26	 '=> '	シドニー	',
'	27	 '=> '	ラスベガス	',
'	28	 '=> '	デンバー	',
'	29	 '=> '	ダラス	',
'	30	 '=> '	ヒューストン	',
'	31	 '=> '	デトロイト	',
'	32	 '=> '	サンディエゴ	',
'	33	 '=> '	アトランタ	',
'	34	 '=> '	フェニックス	',
'	35	 '=> '	ミネアポリス	',
'	36	 '=> '	ワシントンDC	',
'	37	 '=> '	クリーブランド	',
'	38	 '=> '	タンパ	',
'	39	 '=> '	マイアミ	',
'	40	 '=> '	ニューアーク	',
'	41	 '=> '	シンシナティ	',
'	42	 '=> '	ホーチミン市	',
'	43	 '=> '	クアラルンプール	',
'	44	 '=> '	ムンバイ	',
'	45	 '=> '	ソウル	',
'	46	 '=> '	マーシャル諸島	',
'	48	 '=> '	広島	',
'	50	 '=> '	アッパーメインランド	',
'	56	 '=> '	横浜	',
'	52	 '=> '	レジャイナ	',
'	53	 '=> '	サスカトゥーン	',
'	54	 '=> '	ウィニペグ	',
'	55	 '=> '	ブランドン	',
'	57	 '=> '	アルバータ州	',
'	58	 '=> '	マニトバ州	',
'	59	 '=> '	サスカチュワン州	',
'	60	 '=> '	アリックス	',
'	61	 '=> '	キッチナー	',
'	62	 '=> '	アルトナ	',
'	63	 '=> '	レスブリッジ	',
'	64	 '=> '	カムローズ	',
'	65	 '=> '	ハイリバー	',
'	66	 '=> '	パリ	',
'	67	 '=> '	フランス	');


	define('DB_NAME_SLISTEM','slistem');
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

	}

