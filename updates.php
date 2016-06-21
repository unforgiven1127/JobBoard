<?php

	$array=array('	1	' => '	アカデミー	',
'	2	' => '	広告	',
'	3	' => '	航空宇宙	',
'	4	' => '	航空会社	',
'	5	' => '	建築	',
'	6	' => '	FIN-監査法人	',
'	8	' => '	FIN-バンキングサービス	',
'	9	' => '	化学品	',
'	10	' => '	CNS-アパレル	',
'	12	' => '	CNS-フード/ドリンク	',
'	13	' => '	CNS-全般	',
'	14	' => '	CNS-ラグジュアリー	',
'	15	' => '	CNS-メディア	',
'	16	' => '	CNS-MKTの研究	',
'	18	' => '	CNS-パーソナルケア	',
'	19	' => '	CNS小売り	',
'	20	' => '	CNS-タバコ	',
'	21	' => '	IT-ハードウェア	',
'	22	' => '	建設	',
'	23	' => '	ITコンサルティング	',
'	25	' => '	FIN-クレジットカード	',
'	26	' => '	顧客管理	',
'	27	' => '	設計	',
'	28	' => '	教育	',
'	29	' => '	エレクトロニクス	',
'	30	' => '	エネルギー電気	',
'	32	' => '	エネルギーガス/オイル	',
'	33	' => '	エンジニアリング	',
'	34	' => '	イベント管理	',
'	35	' => '	FIN-IT	',
'	36	' => '	FIN-サービス	',
'	39	' => '	アグリ/林業	',
'	40	' => '	政府	',
'	41	' => '	健康サービス	',
'	42	' => '	ホスピタリティー	',
'	43	' => '	人事	',
'	44	' => '	インダス。エンジニアリング	',
'	45	' => '	FIN-保険	',
'	47	' => '	IT-インターネット関連サービス	',
'	49	' => '	IT-その他	',
'	50	' => '	FIN-リース	',
'	51	' => '	法律サービス	',
'	52	' => '	ライセンシング	',
'	53	' => '	物流	',
'	54	' => '	機械	',
'	55	' => '	製造業	',
'	56	' => '	メディア	',
'	57	' => '	生命科学	',
'	58	' => '	医療機器	',
'	59	' => '	医療OTC一般用医薬品	',
'	62	' => '	ネットワークビジネス	',
'	63	' => '	音楽	',
'	66	' => '	他の	',
'	68	' => '	薬剤,医薬品	',
'	69	' => '	広報	',
'	70	' => '	出版	',
'	71	' => '	FIN-不動産	',
'	72	' => '	募集/求人	',
'	73	' => '	再配置	',
'	75	' => '	レストラン	',
'	76	' => '	FIN-証券	',
'	77	' => '	IT-セミコン	',
'	78	' => '	サービス	',
'	80	' => '	IT - ソフトウェア	',
'	82	' => '	鋼	',
'	83	' => '	IT-シーア/サービス	',
'	84	' => '	IT-テレコム有線	',
'	85	' => '	IT-テレコムハードウェア	',
'	86	' => '	IT-テレコムその他	',
'	87	' => '	テキスタイル	',
'	90	' => '	商社	',
'	91	' => '	翻訳	',
'	92	' => '	交通,運送、輸送	',
'	93	' => '	FINベンチャー投資	',
'	94	' => '	IT-テレコムワイヤレス	',
'	97	' => '	自動車・部品	',
'	98	' => '	自動車、受託製造	',
'	99	' => '	FIN-資産管理	',
'	100	' => '	FIN-銀行 - プライベート	',
'	101	' => '	FIN-未公開株式	',
'	103	' => '	FIN-アドバイザリー	',
'	104	' => '	FIN-銀行 - 投資	',
'	105	' => '	FIN-銀行 -小売	',
'	106	' => '	FIN-銀行 - 株式会社	',
'	107	' => '	FIN-銀行 - 信託/証券保管	',
'	108	' => '	FIN-仲介	',
'	109	' => '	FIN-保険 - 生命	',
'	110	' => '	FIN-保険 - 損害	',
'	112	' => '	IT-ゲーム	',
'	114	' => '	IT-資産運用	',
'	115	' => '	IT-フィン保険	',
'	116	' => '	IT-フィンバンキング	',
'	117	' => '	IT-フィンアウトソーシング	',
'	118	' => '	IT-フィンフィンその他	',
'	119	' => '	IT-フィン証券	',
'	120	' => '	経営コンサルティング	',
'	122	' => '	FIN-ヘッジファンド	',
'	123	' => '	バイオテクノロジー/診断	',
'	124	' => '	CNS-その他	',
'	125	' => '	自動車-アフターサービス	',
'	126	' => '	機密	',
'	500	' => '	CNS	',
'	501	' => '	エネルギー	',
'	502	' => '	金融	',
'	503	' => '	情報技術	',
'	504	' => '	医療	',
'	505	' => '	AUTOMOTIVE	',
'	506	' => '	その他の	',
'	127	' => '	エネルギー再生可能	',
'	128	' => '	ファースト・ネーション	',
'	507	' => '	IT-ファーマ	',
'	129	' => '	市場調査	',
'	130	' => '	ITクラウド	',
'	131	' => '	CNS-電子工学	');

	foreach ($array as $key => $value)
	{
		$id = TRIM($key);
		$jpTitle = TRIM($value);

		$slistemDB = CDependency::getComponentByName('database');
	    $slistemQuery = " UPDATE sl_industry SET label_jp = '".$jpTitle."' WHERE sl_industrypk ='".$id."'";

	    $positionData = $slistemDB->slistemGetAllData($slistemQuery);
	}

