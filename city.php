<?php

//require("c:\\program files\\access\\cookiecheck.php");
function encode($str, $convertTags = 0, $encoding = "") {
 if (is_array($arrOutput = $str)) {
   foreach (array_keys($arrOutput) as $key)
     $arrOutput[$key] = makeSafeEntities($arrOutput[$key],$encoding);
   return $arrOutput;
   }
 else if (!empty($str)) {
     $str .= " ";
   $str = makeUTF8($str,$encoding);
   $str = mb_convert_encoding($str,"HTML-ENTITIES","UTF-8");
   $str = makeAmpersandEntities($str);
   if ($convertTags)
     $str = makeTagEntities($str);
   $str = correctIllegalEntities($str);
   return substr($str, 0, strlen($str)-1);
   }
 }
// Convert str to UTF-8 (if not already), then convert to HTML numbered decimal entities.
// If selected, it first converts any illegal chars to safe named (and numbered) entities
// as in makeSafeEntities(). Unlike mb_convert_encoding(), mb_encode_numericentity() will
// NOT skip any already existing entities in the string, so use a regex to skip them.
function makeAllEntities($str, $useNamedEntities = 0, $encoding = "") {
  if (is_array($str)) {
    foreach ($str as $s)
      $arrOutput[] = makeAllEntities($s,$encoding);
    return $arrOutput;
    }
  else if ($str !== "") {
    $str = makeUTF8($str,$encoding);
    if ($useNamedEntities)
      $str = mb_convert_encoding($str,"HTML-ENTITIES","UTF-8");
    $str = makeTagEntities($str,$useNamedEntities);
    // Fix backslashes so they don't screw up following mb_ereg_replace
    // Single quotes are fixed by makeTagEntities() above
    $str = mb_ereg_replace('\\\\',"&#92;", $str);
    mb_regex_encoding("UTF-8");
    $str = mb_ereg_replace("(?>(&(?:[a-z]{0,4}\w{2,3};|#\d{2,5};)))|(\S+?)",
                          "'\\1'.mb_encode_numericentity('\\2',array(0x0,0x2FFFF,0,0xFFFF),'UTF-8')", $str, "ime");
    $str = correctIllegalEntities($str);
    return $str;
    }
  }

// Convert common characters to named or numbered entities
function makeTagEntities($str, $useNamedEntities = 1) {
  // Note that we should use &apos; for the single quote, but IE doesn't like it
  $arrReplace = $useNamedEntities ? array('&#39;','&quot;','&lt;','&gt;') : array('&#39;','&#34;','&#60;','&#62;');
  return str_replace(array("'",'"','<','>'), $arrReplace, $str);
  }

// Convert ampersands to named or numbered entities.
// Use regex to skip any that might be part of existing entities.
function makeAmpersandEntities($str, $useNamedEntities = 1) {
  return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,5};)/m", $useNamedEntities ? "&amp;" : "&#38;", $str);
  }

// Convert illegal HTML numbered entities in the range 128 - 159 to legal couterparts
function correctIllegalEntities($str) {
  $chars = array(
    128 => '&#8364;',
    130 => '&#8218;',
    131 => '&#402;',
    132 => '&#8222;',
    133 => '&#8230;',
    134 => '&#8224;',
    135 => '&#8225;',
    136 => '&#710;',
    137 => '&#8240;',
    138 => '&#352;',
    139 => '&#8249;',
    140 => '&#338;',
    142 => '&#381;',
    145 => '&#8216;',
    146 => '&#8217;',
    147 => '&#8220;',
    148 => '&#8221;',
    149 => '&#8226;',
    150 => '&#8211;',
    151 => '&#8212;',
    152 => '&#732;',
    153 => '&#8482;',
    154 => '&#353;',
    155 => '&#8250;',
    156 => '&#339;',
    158 => '&#382;',
    159 => '&#376;');
  foreach (array_keys($chars) as $num)
    $str = str_replace("&#".$num.";", $chars[$num], $str);
  return $str;
  }

// Compare to native utf8_encode function, which will re-encode text that is already UTF-8
function makeUTF8($str,$encoding = "") {
  if ($str !== "") {
    if (empty($encoding) && isUTF8($str))
      $encoding = "UTF-8";
    if (empty($encoding))
      $encoding = mb_detect_encoding($str,'UTF-8, ISO-8859-1');
    if (empty($encoding))
      $encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
    return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str,"UTF-8",$encoding);
    }
  }

// Much simpler UTF-8-ness checker using a regular expression created by the W3C:
// Returns true if $string is valid UTF-8 and false otherwise.
// From http://w3.org/International/questions/qa-forms-utf-8.html
function isUTF8($str) {
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           // ASCII
       | [\xC2-\xDF][\x80-\xBF]            // non-overlong 2-byte
       | \xE0[\xA0-\xBF][\x80-\xBF]        // excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} // straight 3-byte
       | \xED[\x80-\x9F][\x80-\xBF]        // excluding surrogates
       | \xF0[\x90-\xBF][\x80-\xBF]{2}     // planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         // planes 4-15
       | \xF4[\x80-\x8F][\x80-\xBF]{2}     // plane 16
   )*$%xs', $str);
  }

	//{return htmlentities($rawinput,ENT_QUOTES,"UTF-8");}
function decode($encoded)
	{return html_entity_decode($encoded,ENT_QUOTES);}
$funkynum=array(encode("O")=>"0",encode("P")=>"1",encode("Q")=>"2", encode("R")=> "3" , encode("S")=> "4" , encode("T")=>"5",encode("U")=>"6",encode("V")=>"7",encode("W")=>"8",encode("X")=>"9",encode("@")=>" ",encode("[")=>"-");
function numchk($number)
	{
		global $funkynum;
		//echo $number;
		$result=strtr($number,$funkynum);
		//echo $result;
		return ($result);
	}

  function hyphenator($string,$vowels)
	{
		$off2=0;
		for ($a=0;$a<=5;$a++)
			{
				$off1[$a]=rstrpos($string,$vowels[$a],2);
				if ($off1[$a]>$off2)
					{
						$off2=$off1[$a];
					}
			}
		$string=substr($string,0,$off2) . "-" . substr($string,$off2);
		return $string;
	}
//Function to check the right hand position of a string
function rstrpos ($haystack, $needle, $offset)
	{
		$size = strlen ($haystack);
		$pos = strpos (strrev($haystack), $needle, $offset);
    	if ($pos === false) return false;
        return $size - $pos;
	}


function addjapeng($kanjicity, $kanalocal, $kanacity, $kanastreet,/* $kanahouse,*/ $hyphens)
	{
		//Check for "-gun" and adjust accordingly
		if (strstr(substr($kanjicity,8),encode("郡"))!==false) $kanacity=substr($kanacity,0,8) . str_replace(encode("ｸﾞﾝ"),"gun ",substr($kanacity,8));
		$string= trim($kanalocal." ". $kanacity . " " . $kanastreet/* . " " . $kanahouse*/);


		//Array to prevent "-shi,-ku" errors
		$cities=array(encode("ｻｲﾀﾏｼ") =>"Saitamashi ",encode("ｷｮｳﾄｼ") =>"Kyotoshi ",encode("ｾﾝﾀﾞｲｼ") =>"Sendaishi ",encode("ｷﾀｷｭｳｼｭｳｼ") =>"Kita Kyushushi ",encode("ﾁﾊﾞｼ") =>"Chibashi ",encode("ﾅｺﾞﾔｼ") =>"Nagoyashi ",encode("ｻｶｲｼ") =>"Sakaishi ",encode("ｵｵｻｶｼ") =>"Osakashi ",encode("ｶﾜｻｷｼ") =>"Kawasakishi ",encode("ﾋﾛｼﾏｼ") =>"Hiroshimashi ",encode("ﾆｲｶﾞﾀｼ") =>"Nigatashi ",encode("ｻｯﾎﾟﾛｼ") =>"Sapporoshi ",encode("ﾖｺﾊﾏｼ") =>"Yokohamashi ",encode("ﾊﾏﾏﾂｼ") =>"Hamamatsushi ",encode("ｺｳﾍﾞｼ") =>"Kobeshi ",encode("ﾌｸｵｶｼ") =>"Fukuokashi ",encode("ｼｽﾞｵｶｼ") =>"Shizuokashi ");
		//Irregular combinations
		$irregular=array(encode("ｩｳ") => encode("ｩ") ,encode("ｫｵ") => encode("ｫ") ,encode("ｮｳ") => encode("ｮ") ,encode("ｭｳ") => encode("ｭ")/*, encode(" ﾐﾅﾐ") =>" Minami ", encode(" ﾋｶﾞｼ") =>" higashi "/*, encode(" ｷﾀ") =>" kita ", encode(" ﾆｼ") =>" nishi" */);
		//Basic transition
		$trans=array(encode("ｼﾓｳ") => "shimou" ,encode("ﾓﾄｳ") => "motou" ,encode("ﾓﾄｵ") => "motoo" ,encode("ﾝ") => "n" ,encode("ｦ") => "wo" ,encode("ﾜ") => "wa" ,encode("ﾛｳ") => "ro" ,/*encode("ﾛｵ") => "ro" ,*/encode("ﾛ") => "ro" ,encode("ﾚ") => "re" ,encode("ﾙ") => "ru" ,encode("ﾘｲ") => "ri" ,encode("ﾘｮ") => "ryo" ,encode("ﾘｭ") => "ryu" ,encode("ﾘｬ") => "rya" ,encode("ﾘ") => "ri" ,encode("ﾗ") => "ra" ,encode("ﾖｳ") => "yo" ,encode("ﾕｳ") => "yu" ,encode("ﾖ") => "yo" ,encode("ﾕ") => "yu" ,encode("ﾔ") => "ya" ,encode("ﾓｵ") => "mo" ,encode("ﾓｳ") => "mo" ,encode("ﾓ") => "mo" ,encode("ﾒ") => "me" ,encode("ﾑ") => "mu" ,encode("ﾐｮ") => "myo" ,encode("ﾐｭ") => "myu" ,encode("ﾐｬ") => "mya" ,encode("ﾐ") => "mi" ,encode("ﾏ") => "ma" ,encode("ﾎﾞｳ") => "bo" ,encode("ﾎﾞ") => "bo" ,encode("ﾍﾞ") => "be" ,encode("ﾌﾞｳ") => "bu" ,encode("ﾌﾞ") => "bu" ,encode("ﾋﾞｮ") => "byo" ,encode("ﾋﾞｭ") => "byu" ,encode("ﾋﾞｬ") => "bya" ,encode("ﾋﾞ") => "bi" ,encode("ﾊﾞ") => "ba" ,encode("ﾎﾟｳ") => "po" ,encode("ﾎﾟ") => "po" ,encode("ﾍﾟ") => "pe" ,encode("ﾌﾟｳ") => "pu" ,encode("ﾌﾟ") => "pu" ,encode("ﾋﾟｮ") => "pyo" ,encode("ﾋﾟｭ") => "pyu" ,encode("ﾋﾟｬ") => "pya" ,encode("ﾊﾟ") => "pa" ,encode("ﾉｳ") => "no" ,encode("ﾎｳ") => "ho" ,encode("ﾎ") => "ho" ,encode("ﾍ") => "he" ,encode("ﾌｫ") => "fo" ,encode("ﾌｪ") => "fe" ,encode("ﾌｩ") => "fu" ,encode("ﾌｨ") => "fi" ,encode("ﾌｧ") => "fa" ,encode("ﾌｳ") => "fu" ,encode("ﾌ") => "fu" ,encode("ﾋｮ") => "hyo" ,encode("ﾋｭ") => "hyu" ,encode("ﾋｬ") => "hya" ,encode("ﾋ") => "hi" ,encode("ﾊ") => "ha" ,encode("ﾉ") => "no" ,encode("ﾈ") => "ne" ,encode("ﾇｳ") => "nu" ,encode("ﾇ") => "ne" ,encode("ﾆｮ") => "nyo" ,encode("ﾆｭ") => "nyu" ,encode("ﾆｬ") => "nya" ,encode("ﾆ") => "ni" ,encode("ﾅ") => "na" ,encode("ﾄﾞｵ") => "do" ,encode("ﾄﾞｳ") => "do" ,encode("ﾄﾞ") => "do" ,encode("ﾃﾞｨ") => "di" ,encode("ﾃﾞ") => "de" ,encode("ﾂﾞｳ") => "ju" ,encode("ﾂﾞ") => "ju" ,encode("ﾁﾞｮ") => "jyo" ,encode("ﾁﾞｭ") => "jyu" ,encode("ﾁﾞｬ") => "jya" ,encode("ﾁﾞｪ") => "jye" ,encode("ﾁﾞｨ") => "ji" ,encode("ﾁﾞ") => "ji" ,encode("ﾀﾞ") => "da" ,encode("ﾄｵ") => "to" ,encode("ﾄｳ") => "to" ,encode("ﾄ") => "to" ,encode("ﾃｨ") => "ti" ,encode("ﾃ") => "te" ,encode("ﾂｳ") => "tsu" ,encode("ﾂ") => "tsu" ,encode("ﾁｮ") => "cho" ,encode("ﾁｭ") => "chu" ,encode("ﾁｬ") => "cha" ,encode("ﾁｪ") => "che" ,encode("ﾁｨ") => "chi" ,encode("ﾁ") => "chi" ,encode("ﾀ") => "ta" ,encode("ｿﾞｳ") => "zo" ,encode("ｿﾞｵ") => "zo" ,encode("ｿﾞ") => "zo" ,encode("ｾﾞ") => "ze" ,encode("ｽﾞ") => "zu" ,encode("ｼﾞｮ") => "jo" ,encode("ｼﾞｪ") => "je" ,encode("ｼﾞｭ") => "ju" ,encode("ｼﾞｬ") => "ja" ,encode("ｼﾞ") => "ji" ,encode("ｻﾞ") => "za" ,encode("ｿｳ") => "so" ,encode("ｿｵ") => "so" ,encode("ｿ") => "so" ,encode("ｾ") => "se" ,encode("ｽ") => "su" ,encode("ｼｮ") => "sho" ,encode("ｼｪ") => "she" ,encode("ｼｭ") => "shu" ,encode("ｼｬ") => "sha" ,encode("ｼ") => "shi" ,encode("ｻ") => "sa" ,encode("ｺﾞｳ") => "go" ,encode("ｺﾞｵ") => "go" ,encode("ｺﾞ") => "go" ,encode("ｹﾞ") => "ge" ,encode("ｸﾞ") => "gu" ,encode("ｷﾞｮ") => "gyo" ,encode("ｷﾞｪ") => "gye" ,encode("ｷﾞｭ") => "gyu" ,encode("ｷﾞｨ") => "gyi" ,encode("ｷﾞｬ") => "gya" ,encode("ｷﾞ") => "gi" ,encode("ｶﾞ") => "ga" ,encode("ｺｳ") => "ko" ,encode("ｺｵ") => "ko" ,encode("ｺ") => "ko" ,encode("ｹ") => "ke" ,encode("ｸ") => "ku" ,encode("ｷｮ") => "kyo" ,encode("ｷｪ") => "kye" ,encode("ｷｭ") => "kyu" ,encode("ｷｨ") => "kyi" ,encode("ｷｬ") => "kya" ,encode("ｷ") => "ki" ,encode("ｶ") => "ka" ,encode("ｵｵ") => "o" ,encode("ｵｳ") => "o" ,encode("ｳﾞｫ") => "vo" ,encode("ｳﾞｪ") => "ve" ,encode("ｳﾞｨ") => "vi" ,encode("ｳﾞｧ") => "va" ,encode("ｳﾞ") => "vu" ,encode("ｵ") => "o" ,encode("ｴ") => "e" ,encode("ｳ") => "u" ,encode("ｲ") => "i" ,encode("ｱ") => "a",encode("ｯ") => "l");
		//Perform Conversion
		$string=strtr($string,$cities);
		$string=strtr($string,$irregular);
		$string=strtr($string,$trans);
		$string=strtoupper(substr($string,0,1)) . substr($string,1);
		$length=strlen($string);
		//echo "<br />basic switch $string";
		//Remove tsu or "l" as it is now written
		for ($pos=0;$pos<=$length;$pos++)
			{
				$part=substr($string,$pos,1);
				$tsu=strstr($part,"l");
				if ($tsu!==false)
					{
						$string=substr($string,0,$pos) . substr($string,($pos+1),1) . substr($string,($pos+1));
					}
			}
		//echo "<br />remove tsu $string";
		//Add Capital Letters to the start of each place
		$off=1+strpos($string," ");
		while ($off!=1)
			{
				$string=substr($string,0,$off) . strtoupper(substr($string,$off,1)) . substr($string,($off+1));
				$off=1+strpos($string," ",$off);
			}
		//echo "</br> Capital Letters $string";
		//Function to add Hyphens to the end of each word
		$vowels=array("a","e","i","o","u","n");
		$stringer=explode(" ",$string);
		$count=(count($stringer)-(1+$hyphens));
		for ($i=0;$i<=$count;$i++)
			{
				$stringer[$i]=hyphenator($stringer[$i],$vowels);
			}
		$string=implode(" ",$stringer);
		//echo "<br />Hyphens $string";
		//Strip off any bracketed areas
		$string=str_replace("ma-chi","machi",$string);
		if (strstr($string,encode("("))!==false) $string=substr($string,0,strpos($string,encode("("))) . substr($string,(8+strpos($string,encode(")"))));
		//echo "<br />Final $string";
		return $string;
	}


/*$out=array(","," ",".","\\","-");
$outbil=array("ko-po", "Ko-po", "Bi-ru", "bi-ru");
$outalpha=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"," ","--");
$Postcode=decode(str_replace($out,"",$Postcode));
$address=decode(str_replace($outalpha,"",$strtwn));*/



mysql_connect('127.0.0.1', 'root', '');
mysql_select_db('distribution');

//$sQuery = "SELECT * from postoffice  limit 5000 ";
$sQuery = "SELECT * from postoffice ORDER BY cityfk ";
$oRes = mysql_query($sQuery);


//var_dump($oRes);
echo mysql_num_rows($oRes);

$asCreatedCity = array();
$nCount = 0;
while($asRes = mysql_fetch_assoc($oRes) )
{
  //var_dump($asRes);
  /*if(!in_array($asRes["FullCode"], $asCreatedCity))
  {
    $asCreatedCity[$asRes["FullCode"]] = $asRes["FullCode"];*/



      $addresska = $asRes["KanjiStreet"];
      //echo $addressk . " hm ";
      if (strstr($addresska,"&#65289")!==false)
      {
        $addresska=substr($addresska,0,(strpos($addresska,"&#65288"))) .
        substr($addresska,(8+strpos($addresska,"&#65289")));  //echo "hello";
      }
      //echo $addressk;
      $jlocal=$asRes["KanjiLocal"];
      $jcity=$asRes["KanjiCity"];
      $addressk=$jlocal . $jcity . $addresska;
      $jkaddress1=$asRes["KanaStreet"];
      if (strstr($jkaddress1,encode("("))!==false) $jkaddress1=substr($jkaddress1,0,strpos($jkaddress1,encode("("))) . substr($jkaddress1,(8+strpos($jkaddress1,encode(")"))));
      $jklocal=$asRes["KanaLocal"];
      $jkcity=$asRes["KanaCity"];
      $tidy=array("-to"=>"","-do"=>"do");

      $enaddbase=strtr(addjapeng($jcity,$jklocal,$jkcity,$jkaddress1,1) . " "/* . $address*/,$tidy);
      //$enaddbase=strtr(addjapeng($jcity, $jklocal,$jkcity,'',1) . " "/* . $address*/,$tidy);

      $asCity = explode(' ', $enaddbase);
      //var_dump($asCity); echo '<br />';



  /*if(!in_array($enaddbase, $asCreatedCity))
  {
    $asCreatedCity[$enaddbase] = $enaddbase;*/

    $sQuery = 'UPDATE postoffice SET EngLocal = "'.utf8_encode($asCity[0]).'", EngCity = "'.utf8_encode($asCity[1]).'", EngStreet = "';

    unset($asCity[0]);
    unset($asCity[1]);
    $sQuery.= utf8_encode(implode(' ',$asCity)).'" WHERE cityfk = '.(int)$asRes['cityfk'];
    //echo $sQuery;

    $oInsertRes = mysql_query($sQuery);

    if(!$oInsertRes)
    {
      echo 'bad query: '.$sQuery.' ['.  mysql_error().']<br />';
    }
    usleep(100);
    flush(); ob_flush();
  //}

    $nCount++;

    if(($nCount % 100) == 0)
      echo $nCount.'<br />';

}

echo "done importing <br />";






