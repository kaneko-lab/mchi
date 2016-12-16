<?php
class CategorizationsController extends AppController { 
//var $uses=array('Message','Japanese');
  var $uses=array('Message','English','Japanese','Category');  
  var $NO_CATEGORY=9;



function analyzeMsgCategory($messageId=null,$mode=null){

	if($messageId==null)return false;

	//*メッセージ情報をIDより取得*//
	
	//現在のメッセージ情報をＤＢより取得
	$conditions1=array('id'=>$messageId);
	$currentMsg=$this->Message->find('first',array('conditions'=>$conditions1,'recursive'=>-1));
	//取得完了


	//$lang設定
	$lang=$currentMsg['Message']['lang'];		

	//現在のメッセージより過去のメッセージの情報をＤＢより取得
	$msgUserId=$currentMsg['Message']['user_id'];
	
	//取得したい過去のメッセージ件数
	$maxPrevMsgNum=2;

	//メッセージ時刻
    $msgTimeStamp=$currentMsg['Message']['created'];

	//過去のメッセージ検索条件
	$conditions2=array('user_id'=>$msgUserId,//同じユーザの中で検索
					   'created <'=>$msgTimeStamp);//過去のメッセージだけを検索

	$prevMsg=$this->Message->find('all',array('conditions'=>$conditions2,
											  'limit'=>$maxPrevMsgNum,//最大検索件数
											  'order'=>'created desc',
											  'recursive'=>-1));//冗長な検索を防ぐーcakephpの特性上の問題
	$text=$currentMsg['Message']['content'];
	
	//取得した過去のメッセージと今のメッセージを統合
	foreach($prevMsg as $msg){
		$text.=" ".$msg['Message']['content'];
	}
    //過去のメッセージを取得完了

	$this->layout="debug";

	//$textには過去のメッセージがあれば３個分のメッセージが入っている.
	//区切りは半角スペースである。
	if($mode=='debug'){
		echo "現在のメッセージID : ".$messageId;
	
		echo "<br />";
		echo "取得した過去のメッセージ";
		foreach($prevMsg as $msg){
		echo "<hr>";
		echo " User ID : ".$msg['Message']['user_id']
			." Message ID : ".$msg['Message']['id']
			." Message : ".$msg['Message']['content']
			." Time Stamp : ".$msg['Message']['created'];
		}	

		echo "<hr>";
		echo "<br />結合された文字列の詳細:<br />";
		var_dump($text);
	
		echo "<hr>";
		echo "<br />実際に解析される文字列:<br /><br />";
		echo "<font style='color:red;font-size:1.5em'>".$text."</font>";
		$this->render(false);
	
	}
	
	
   if($lang=='en')$text=str_replace(" ","+",$text);
	
 	
 	$noun=$this->noun_get($text,$lang);
	 
 	
 	$result=$this->get($noun,$lang);
	$data=array();
	arsort($result);
	$sum=array_sum($result);
	$i=0;
	foreach ($result as $key => $val) {
		if($sum==0){
			$data[]=array('key'=>$this->NO_CATEGORY,'rate'=>0);
		}else{
		$per=round($val/$sum,3);
		$data[]=array('key'=>$key,'rate'=>$per);
		}
		$i++;
		if($i>3)break;
 	}
	
	//SAVE CATEGORIES;
	$categoryList=$this->Category->find('list',array('fields'=>array('key','id')));
	$this->Message->id=$messageId;
	
	$saveData=array('category_id1'=>$categoryList[$data[0]['key']],
					'category_id2'=>$categoryList[$data[1]['key']],
					'category_id3'=>$categoryList[$data[2]['key']],
					'rate1'=>$data[0]['rate'],
					'rate2'=>$data[1]['rate'],
					'rate3'=>$data[2]['rate'],
					);
	
	$this->Message->save($saveData);
}	

function jsonGetCategory($messageId,$currentUserLang='en',$wait=0){

	//Todo figure out who use this method.
	return;
		//This condition will be occur when chat user get other users messages;
	sleep($wait);
		
	$this->layout="debug";
	$categoryList=$this->Category->find('list',array('fields'=>array('id',$currentUserLang)));
	$fields=array('category_id1','category_id2','category_id3','rate1','rate2','rate3');
	$found=$this->Message->find('first',array('conditions'=>array('id'=>$messageId),'recursive'=>-1,'fields'=>$fields));
	if(!($found===false)){
		if($found['Message']['category_id1']==0){
			$this->analyzeMsgCategory($messageId);
			$found=$this->Message->find('first',array('conditions'=>array('id'=>$messageId),'recursive'=>-1,'fields'=>$fields));
		}
		$data=array();
		$data['category1']=$categoryList[$found['Message']['category_id1']].':'.$found['Message']['rate1'];
		$data['category2']=$categoryList[$found['Message']['category_id2']].':'.$found['Message']['rate2'];
		$data['category3']=$categoryList[$found['Message']['category_id3']].':'.$found['Message']['rate3'];
		echo json_encode($data);
	}else{
		echo json_encode(array());
	}
}


function get($name,$lang){
	if($lang=='ja')$lang_model='Japanese';
	  else if($lang=='en')$lang_model='English';
		$category["nation"]=0;
		$category["sports"]=0;
		$category["entertainment"]=0;
		$category["arts"]=0;
		$category["health"]=0;
		$category["schools"]=0;
		$category["technology"]=0;
		$category["politics"]=0;
	
	foreach($name as $r){
	
	   $result=$this->$lang_model->find('first',
									array('conditions'=>
											array('name'=>$r)
										 )
								);
	
	
	
								
	if($result===false){
		
		
	}else{
		$id=$result[$lang_model]['id'];
		$category["nation"]+=$result[$lang_model]["nation"];
		$category["sports"]+=$result[$lang_model]["sports"];
		$category["entertainment"]+=$result[$lang_model]["entertainment"];
		$category["arts"]+=$result[$lang_model]["arts"];
		$category["health"]+=$result[$lang_model]["health"];
		$category["schools"]+=$result[$lang_model]["schools"];
		$category["technology"]+=$result[$lang_model]["technology"];
		$category["politics"]+=$result[$lang_model]["politics"];
	}

  }
	return $category;			
	
}	



function noun_get($msg,$lang){
	$noun=array();

	if($lang=='ja'){
	
		$msg=urlencode($msg);
		$url = "http://mchi.kaneko-lab.net/messages/getParsed/{$lang}?msg={$msg}";
		$ch = curl_init(); // 1. 初期化	
	    curl_setopt( $ch, CURLOPT_URL, $url ); // 2. オプションを設定
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result =mb_convert_encoding( curl_exec( $ch ),'UTF-8', 'auto'); // 3. 実行してデータを得る
	    $result=json_decode($result,true);
		$k=0;
 		$t=0;
  
        foreach($result as $r){
			if($r=="EOS")break;
			$data[$k]=explode('	',$r);
			
			$pattern=mb_convert_encoding('名詞','UTF-8', 'auto');
			
			if(preg_match("/$pattern/",$data[$k][1])){
				$noun[$t]= $data[$k][0];
		     	$t++;
			}
			
			$k++;
		}
	    
	    
	    
		return $noun;
		
	}else if($lang=='en'){

		//$msg="I+love+tennis+time";
		//$msg=urlencode($msg);
		$url = "http://mchi.kaneko-lab.net/messages/getParsed/{$lang}?msg={$msg}";
		$ch = curl_init(); // 1. 初期化	
	    curl_setopt( $ch, CURLOPT_URL, $url ); // 2. オプションを設定
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result =mb_convert_encoding( curl_exec( $ch ),'UTF-8', 'auto'); // 3. 実行してデータを得る
	    $result=json_decode($result,true);
		$k=0;
        $t=0;
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


}
?>
