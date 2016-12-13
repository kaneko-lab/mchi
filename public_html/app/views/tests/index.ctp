<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>twitter API サンプル</title>
</head>
<body>

<?php
$count=130;
$ch = curl_init(); // 1. 初期化	

while($count<200){

$url = "http://www.chicagotribune.com/sports/breaking/";
        curl_setopt( $ch, CURLOPT_URL, $url ); // 2. オプションを設定
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result =mb_convert_encoding( curl_exec( $ch ),'UTF-8', 'auto'); // 3. 実行してデータを得る
   
//echo $result;


$pattern = "/(\bhref\s*=\s*[\"']?)(\/sports\/[^\s\"'>]*.story)/i";

//$pattern = "/(\bhref\s*=\s*[\"']?)(http:\/\/[^\s\"'>]*)/i";
 preg_match_all($pattern,$result,$link);

//$html_title[0][0]=str_replace( "href='", "", $link[0][0] );
 // echo $html_title[0][0]."<br />\n";

for ($j = 0 ; $j <count($link[1])-6; $j++) {

$link[0][$j]=str_replace( "href=\"", "", $link[0][$j] );
  echo $link[0][$j]."<br />\n";
 //echo $link[$j][0]."<br />\n";
  

}
$count=$count+10;
}
curl_close($ch); // 4. 終了

	/*
$ch = curl_init(); // 1. 初期化	
        curl_setopt( $ch, CURLOPT_URL, $link[0][31] ); // 2. オプションを設定
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result =mb_convert_encoding( curl_exec( $ch ),'UTF-8', 'auto'); // 3. 実行してデータを得る
  

$pattern = "/<h2 class=\"orange\">(.*?)<\/h2>/mis";
 preg_match($pattern,$result,$html_title);
echo $html_title[0];
$pattern = "/<div class=\"NewsTextFull\">(.*?)<\/div>/mis";
//$pattern = "/(?<=<title>).+?(?=<\/title>)/mis";
 preg_match($pattern,$result,$html_title);
 echo $html_title[0];
 curl_close($ch); // 4. 終了
*/

//////////////////////////////// 
/*
$html=mb_convert_encoding(file_get_contents('http://sankei.jp.msn.com/region/news/111231/nar11123102040000-n1.htm'), 'UTF-8', 'auto');;
 
$pattern = "/<h2 class=\"purple\">(.*?)<\/h2>/mis";

preg_match($pattern,$html,$html_title);
$html_title[0]= strip_tags(strtolower($html_title[0]));
echo $html_title[0];
$link = "/<a href=../../../$html_title[0]/(.*?)>htm/mis";

echo $link;
*/
/////////////////////////////////
?>



</body>
</html>