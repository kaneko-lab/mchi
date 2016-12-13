<?php
class TestsController extends AppController { 
//var $uses=array('Message','Japanese','English');
  var $uses=array('Message','English','Japanese','Url_ja','Url_en');  

//error_reporting(1);




function getcate($html,$lang){

if($lang=='ja'){
     
//$pattern = "/<div class=\"News(.*?)\">(.*?)<\/div>/mis";//sankei 

//	$pattern = "/<h2 class=(.*?)<\/h2>/mis";
 //preg_match($pattern,$html,$html_title);
//echo $html_title[0];
//$html_title[0]=mb_convert_encoding(strip_tags($html_title[0]),'UTF-8', 'auto');
//echo $html_title[0];

$pattern = "/<div class=\"NewsTextFull\">(.*?)<\/div>/mis";
//$pattern = "/(?<=<title>).+?(?=<\/title>)/mis";
preg_match($pattern,$html,$html_text);
  $html_text[0]=mb_convert_encoding(strip_tags($html_text[0]),'UTF-8', 'auto');


}

if($lang=='en'){
 //$pattern =  "/<div id=\"mainbody\">(.*?)<!-- google_ad_section_end -->/mis";
$pattern = "/<div id=\"story-body-text\">(.*?)<script type=\"text\/javascript\">/mis";
//$pattern = "/<div id=\"story-body-text\">(.*?)<div id=\"subFooter\" class=\"clearfix\">/mis";
 preg_match($pattern,$html,$html_text);
 //$pattern = "/<div class=\"articlerail\">(.*?)<ul>/mis";
  //$html_text[0]=str_replace($pattern,"",$html_text[0]);
 
 
  $html_text[0]=mb_convert_encoding(strip_tags($html_text[0]),'UTF-8', 'auto');
 //echo $html_text[0];

$html_text[0]=str_replace("function showExtras(*.?)return new Effect.Opacity(element,options);
		};","",$html_text[0]);

}

return $html_text;

}




function get($msg,$lang){
	//$this->layout="debug";

	if($lang=='ja'){
		//$msg="";

		$msg=mb_convert_encoding( $msg ,'UTF-8', 'auto');
		//echo $msg;
		//$msg=array_chunk ( $msg , 400 );
		//$ms=var_dump( str_split($msg,400));
		//print_r($ms);
		$msg=urlencode($msg);
		$url = "http://mchi.kaneko-lab.net/messages/getParsed/{$lang}?msg={$msg}";
		$ch = curl_init(); // 1. 初期化	
		curl_setopt( $ch, CURLOPT_URL, $url ); // 2. オプションを設定
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result =mb_convert_encoding( curl_exec( $ch ),'UTF-8', 'auto'); // 3. 実行してデータを得る
		$result=json_decode($result,true);
		$k=0;
		$t=0;
		//print_r($result);

		foreach($result as $r){

			$data[$k]=explode('	',$r);
			//print_r ($data);
			$pattern=mb_convert_encoding('名詞','UTF-8', 'auto');

			if(preg_match("/$pattern/",$data[$k][1])){
				$noun[$t]= $data[$k][0];
				$t++;
			}

			$k++;
		}



		return $noun;

	}else if($lang=='en'){

		//$msg="i+like+it.               he+sees+America";


		//$msg=urlencode($msg);
		//$msg=str_replace("\n","",$msg);
		//$msg=str_replace(" ","+",$msg);
		$msg=trim($msg);
		$msg=trim($msg);

		//print_r($msg);
		$url = "http://mchi.kaneko-lab.net/messages/getParsed/{$lang}?msg={$msg}";
		$ch = curl_init(); // 1. 初期化	
		curl_setopt( $ch, CURLOPT_URL, $url ); // 2. オプションを設定
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result =mb_convert_encoding( curl_exec( $ch ),'UTF-8', 'auto'); // 3. 実行してデータを得る
		$result=json_decode($result,true);

		$k=0;
		$t=0;
		// print_r( $result);
		foreach($result as $r){

			$data[$k]=explode('_',$r);

			if($data[$k][1]=='PRP' ||$data[$k][1]=='NN'||$data[$k][1]=='JJ'||$data[$k][1]=='NNP'||$data[$k][1]=='NNS'){
				$noun[$t]= $data[$k][0];
				$t++;
			}

			$k++;
		}



		return $noun;

	}
}




function dt($name,$category,$point,$lang){

	if($lang=='ja'){
		switch($category){

			case "world":$category="nation";break;
			case "entertainments":$category="entertainment";break;
			case "スポーツ":$category="sports";break;
			case "芸術・アート":$category="arts";break;/////
			case "life":$category="arts";break;///////
			case "学校":$category="schools";break;////////
			case "it":$category="technology";break;///////
			case "政治":$category="politics";break;

		}


		//$name=$this->params['url']['name'];
		//$point=$this->params['url']['point'];
		//$category=$this->params['url']['category'];

		$result=$this->Japanese->find('first',
				array('conditions'=>
					array('name'=>$name)
					)
				);

		if($result===false){

			$this->Japanese->create();

			$data=array('name'=>$name,$category=>$point);	

			$this->Japanese->save($data);

		}else{
			$id=$result['Japanese']['id'];
			$point+=$result['Japanese'][$category];
			$this->Japanese->id=$id;
			$this->Japanese->saveField($category,$point);
		}								

	}else if($lang=='en'){

		//$name=$this->params['url']['name'];
		//$point=$this->params['url']['point'];
		//$category=$this->params['url']['category'];

		//$this->layout="debug";

		$result=$this->English->find('first',
				array('conditions'=>
					array('name'=>$name)
					)
				);

		if($result===false){
			$this->English->create();
			$data=array('name'=>$name,$category=>$point);	
			$this->English->save($data);


		}else{
			$id=$result['English']['id'];
			$point+=$result['English'][$category];
			$this->English->id=$id;
			$this->English->saveField($category,$point);
		}								

	}


}



function cron(){
	$this->layout="debug";	
	$this->craw();	
	$time=date('Y-m-d H:i:s');
	$this->craw();
	if(date('h')==12){
		$message = "Chat System Cron executed at ".$time;
		$message = wordwrap($message, 70);
		//mail('50009268027@st.tuat.ac.jp', 'Making Sytem 2011', $message);
		mail('bhag__s@hotmail.com', 'Making Sytem 2011', $message);
	}
}


function craw(){
	set_time_limit(0);
	$this->layout="debug";	
	$ch = curl_init(); // 1. 初期化	
	
	$count=0;
	$k=0;
	
	while($k<16){
	   switch($k)
	        {
	    case 0:$url="http://sankei.jp.msn.com/politics/newslist/politics-n1.htm";
               $cate='politics';
               $lang='ja';
               break;
        case 1:$url="http://sankei.jp.msn.com/economy/newslist/it-its-n1.htm";
               $cate='technology';
               $lang='ja';
               break;
        case 2:$url="http://sankei.jp.msn.com/world/newslist/world-n1.htm";
               $cate='nation';
               $lang='ja';
               break;
        case 3:$url="http://sankei.jp.msn.com/sports/newslist/sports-n1.htm";
               $cate='sports';
               $lang='ja';
               break;
        case 4:$url="http://sankei.jp.msn.com/entertainments/newslist/entertainments-n1.htm";
               $cate='entertainment';
               $lang='ja';
               break; 
        case 5:$url="http://sankei.jp.msn.com/life/newslist/body-bdy-n1.htm";
               $cate='health';
               $lang='ja';
               break;
        case 6:$url="http://sankei.jp.msn.com/life/newslist/education-edc-n1.htm";
               $cate='schools';
               $lang='ja';
               break;
        case 7:$url="http://sankei.jp.msn.com/life/newslist/arts-art-n1.htm";
               $cate='arts';
               $lang='ja';
               break;
        case 8:$url="http://www.chicagotribune.com/sports/breaking/";
               $cate='sports';
               $lang='en';
               break;       
        case 9:$url="http://www.chicagotribune.com/news/nationworld/#&lid=Nation & World&lpos=Sub";
               $cate='nation';
               $lang='en';
               break;    
        case 10:$url="http://www.chicagotribune.com/news/politicsnow/#&lid=Politics&lpos=Sub";
               $cate='politics';
               $lang='en';
               break;  
        case 11:$url="http://www.chicagotribune.com/business/technology/#&lid=Technology&lpos=Sub";
               $cate='technology';
               $lang='en';
               break;  
        
        case 12:$url="http://www.chicagotribune.com/entertainment/breaking/";
               $cate='entertainment';
               $lang='en';
               break;
        
        case 13:$url="http://www.chicagotribune.com/health/#&lid=Health&lpos=Main";
               $cate='health';
               $lang='en';
               break;
        case 14:$url="http://www.chicagotribune.com/entertainment/art/#&lid=Arts&lpos=Sub";
               $cate='arts';
               $lang='en';
               break;
        case 15:$url="http://www.chicagotribune.com/news/education/#&lid=Schools&lpos=Sub";
               $cate='schools';
               $lang='en';
               break;
               
               
        }
	
	echo $cate;
	if($lang=='en'){

        
        //$url = "http://www.japantimes.co.jp/sports/world_cup.html";

		curl_setopt( $ch, CURLOPT_URL, $url ); // 2. オプションを設定
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result =mb_convert_encoding( curl_exec( $ch ),'UTF-8', 'auto'); // 3. 実行してデータを得る


		//$pattern = "/(\bhref\s*=\s*[\"']?)(http:\/\/www.japantimes.co.jp\/text\/sw[^\s\"'>]*.html)/i";
		//$pattern = "/(\bhref\s*=\s*[\"']?)(\/news\/education\/[^\s\"'>]*.story)/i";
        
        
        if     ($cate=='sports')           {$pattern = "/(\bhref\s*=\s*[\"']?)(\/sports\/[^\s\"'>]*.story)/i";                  $union="http://www.chicagotribune.com";}
		else if($cate=='nation')       {$pattern = "/(\bhref\s*=\s*[\"']?)(\/news\/[^\s\"'>]*.story)/i";                    $union="http://www.chicagotribune.com";}
		else if($cate=='politics')     {$pattern = "/(\bhref\s*=\s*[\"']?)(\/news\/[^\s\"'>]*.story)/i";                   $union="http://www.chicagotribune.com";}
		else if($cate=='technology')       {$pattern = "/(\bhref\s*=\s*[\"']?)(\/business\/[^\s\"'>]*.story)/i";                     $union="http://www.chicagotribune.com";}
		else if($cate=='entertainment'){$pattern = "/(\bhref\s*=\s*[\"']?)(\/entertainment\/[^\s\"'>]*.story)/i";            $union="http://www.chicagotribune.com";}
		else if($cate=='health')       {$pattern = "/(\bhref\s*=\s*[\"']?)(\/health\/[^\s\"'>]*.story)/i";           $union="http://www.chicagotribune.com";}
		else if($cate=='arts')      {$pattern = "/(\bhref\s*=\s*[\"']?)(\/entertainment\/[^\s\"'>]*.story)/i";           $union="http://www.chicagotribune.com";}
		else if($cate=='schools')         {$pattern = "/(\bhref\s*=\s*[\"']?)(\/news\/[^\s\"'>]*.story)/i";           $union="http://www.chicagotribune.com";}
		
        
        
		preg_match_all($pattern,$result,$link);
        
		for ($j = 0 ; $j <count($link[1]); $j++) {
            
           
			
			 $link[0][$j]=str_replace( "href=\"", "", $link[0][$j]);//chucago tribune
			 $link[0][$j]=$union.$link[0][$j];
			
			
			 $result=$this->Url_en->find('first',
				array('conditions'=>
					array('url'=>$link[0][$j])
					)
				);
			
			if(!($result===false)){ 
				//echo "exist";
				continue;
			}
			
			
			
			
			$this->Url_en->create();
			$data=array('url'=>$link[0][$j]);	
			$this->Url_en->save($data);
			
			
			//echo $link[0][$j];
			curl_setopt( $ch, CURLOPT_URL, $link[0][$j] ); // 2. オプションを設定
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$html =mb_convert_encoding( curl_exec( $ch ),'UTF-8', 'auto'); // 3. 実行してデータを得る

			$html_text=$this->getcate($html,$lang);
			//本文
			//echo $html_text[0];
			
			$html_text[0]=str_replace("(","",$html_text[0]);
			$html_text[0]=str_replace(")","",$html_text[0]);
			$html_text[0]=str_replace("\n+","",$html_text[0]);
			$html_text[0]=str_replace("[:space:]","",$html_text[0]);
			$html_text[0]=str_replace("\n\n","",$html_text[0]);
			$html_text[0]=str_replace("&#(.*?);","",$html_text[0]);
			
			$html_text[0]=str_replace(" ","+",$html_text[0]);
			
			
			$html_text[0]=str_replace(" ","",$html_text[0]);
			
			$html_text[0]=str_replace("++"," ",$html_text[0]);
			$html_text[0]=str_replace(" ","",$html_text[0]);
			$html_text[0]=str_replace("[:space:]","",$html_text[0]);
			
			//$html_text=array_merge($html_text);
			//echo  $html_text[0];//カテゴリ
            //echo $html_text[0];

			$noun=$this->get($html_text[0],$lang);
			//for ($i = 0 ; $i <count($link[1]); $i++){echo $noun[$i][1];}
			//print_r ($noun);
			if($noun!=null)

				foreach($noun as $r){
					//echo $r;
					$this->dt($r,$cate,1,$lang);

				}


		}



	}

	if($lang=='ja'){

		

		curl_setopt( $ch, CURLOPT_URL, $url ); // 2. オプションを設定
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result =mb_convert_encoding( curl_exec( $ch ),'UTF-8', 'auto'); // 3. 実行してデータを得る

		if     ($cate=='technology')           {$pattern = "/<a href=\"..\/..\/economy\/news\/(.*?)\">/i";                    $union="http://sankei.jp.msn.com/economy/news/";}
		else if($cate=='nation')       {$pattern = "/<a href=\"..\/..\/world\/news\/(.*?)\">/i";                      $union="http://sankei.jp.msn.com/world/news/";}
		else if($cate=='politics')     {$pattern = "/<a href=\"..\/..\/politics\/news\/(.*?)\">/i";                   $union="http://sankei.jp.msn.com/politics/news/";}
		else if($cate=='sports')       {$pattern = "/<a href=\"..\/..\/sports\/news\/(.*?)\">/i";                     $union="http://sankei.jp.msn.com/sports/news/";}
		else if($cate=='entertainment'){$pattern = "/<a href=\"..\/..\/entertainments\/news\/(.*?)\">/i";             $union="http://sankei.jp.msn.com/entertainments/news/";}
		else if($cate=='health')       {$pattern = "/<a href=\"..\/..\/life\/news\/(.*?)\">/i";           $union="http://sankei.jp.msn.com/life/news/";}
		else if($cate=='schools')      {$pattern = "/<a href=\"..\/..\/life\/news\/(.*?)\">/i";           $union="http://sankei.jp.msn.com/life/news/";}
		else if($cate=='arts')         {$pattern = "/<a href=\"..\/..\/life\/news\/(.*?)\">/i";           $union="http://sankei.jp.msn.com/life/news/";}
		
		//$pattern = "(\bhref\s*=\s*[\"']?)(\/life\/news\/[^\s\"'>]*.htm)/i";
	     	
		preg_match_all($pattern,$result,$link);
       
		for ($j = 0 ; $j <count($link[1]); $j++) {

          
			$link[1][$j]=str_replace( "href=", "", $link[1][$j]);//産経用
			 $link[1][$j]=$union.$link[1][$j];
			
			
			$result=$this->Url_ja->find('first',
				array('conditions'=>
					array('url'=>$link[1][$j])
					)
				);
		   
			
			if(!($result===false)){ 
			//	echo "exist";
			continue;
			}
	
			
			
			$this->Url_ja->create();
			$data=array('url'=>$link[1][$j]);	
			$this->Url_ja->save($data);
			
			
			//echo $link[1][$j];
			
			curl_setopt( $ch, CURLOPT_URL, $link[1][$j] ); // 2. オプションを設定
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$html =mb_convert_encoding( curl_exec( $ch ),'UTF-8', 'auto'); // 3. 実行してデータを得る


			$html_text=$this->getcate($html,$lang);
			//echo $html_text[0];//本文



			$noun=$this->get($html_text[0],$lang);
			//for ($i = 0 ; $i <count($link[1]); $i++){echo $noun[$i][1];}
			//print_r ($noun);
			if($noun!=null)

				foreach($noun as $r)
					//echo $r;
					$this->dt($r,$cate,1,$lang);

		}

	}
	
	echo "finish\r\n";

	
    $k=$k+1;
    
}
curl_close($ch); // 4. 終了
}




}
?>
