<?php
	class MessagesController extends AppController{
        var $name ='Messagess';
        var $uses = array('Message');
		var $helpers=array('javascript');
		var $layout = null;
		var $autoRender =false;
		var $nouns=array();		
		
		function init(){
			if(isset($this->params['url']['time'])){
				$time=$this->params['url']['time'];
				$conditions=array('Message.created >'=>$time);
			}else{
				$conditions=array('Message.created >'=>date('Y-m-d H:i:s'));
			}
			$messages=$this->Message->find('all',array('conditions'=>$conditions,'order'=>'Message.created asc'));
			echo json_encode($messages);
			return;
		}
		
	
		function get($msgId=null){
			if($msgId==null){
				echo json_encode(array());
				return;
			}
			$conditions=array('Message.id >'=>$msgId);
			$messages=$this->Message->find('all',array('conditions'=>$conditions,'order'=>'Message.created asc'));
			echo json_encode($messages);
		}

		/**
		 * Save user message.
		 * Will create translate helper
		 */
		function add(){
			if(!isset($this->params['url']['msg'])||!isset($this->params['url']['user_id'])||!isset($this->params['url']['lang'])){
				echo json_encode(array("reulst"=>"failed"));
				return;
			}

			$message = $this->params['url']['msg'];
			$userId = $this->params['url']['user_id'];
			$lang	= $this->params['url']['lang'];
			$result = $this->Message->saveMessage($message,$userId,$lang);

			echo json_encode(array("result"=>($result)?"success":"failed"));
		}


		function getTranslatedMessageAndHelpers($msgId,$tgtLang){
			$result = $this->Message->getMessageWithTransAndKeyword($msgId,$tgtLang);
			echo json_encode(array("result"=>$result));
			return;
		}

		function getTranslated(){
			$messageId = $this->params['url']['msg_id'];
			$srcLang = $this->params['url']['src_lang'];
			$targetLang = $this->params['url']['tgt_lang'];

			if($this->params['url']['src_lang']=='ch'){
				$this->params['url']['src_lang']="zh-CN";
			}
			if($this->params['url']['tgt_lang']=='ch'){
				$this->params['url']['tgt_lang']="zh-CN";
			}


//			//Get Message.
//			$result = $this->Message->getMessageWithTransAndKeyword($messageId);
//
//			pr($result);
//			return;



			$translateUrl="https://www.googleapis.com/language/translate/v2?".
					 "key=AIzaSyARvc-ax5gVkGFlesv7xjC4cm7ldXZJBqY&q=".urlencode($this->params['url']['msg']).
					 "&source=".$this->params['url']['src_lang'].
					 "&target=".$this->params['url']['tgt_lang'];
			$ch = curl_init();
			// Now set some options (most are optional)
			// Set URL to download
			curl_setopt($ch, CURLOPT_URL, $translateUrl);

			//curl_setopt($ch, CURLOPT_REFERER, "http://mchi.kaneko-lab.net");
			// User agent
			curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
			// Include header in result? (0 = yes, 1 = no)
			curl_setopt($ch, CURLOPT_HEADER, 0);
			// Should cURL return or print out the data? (true = return, false = print)
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// Timeout in seconds
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			// Download the given URL, and return output
			$output = curl_exec($ch);
			// Close the cURL resource, and free system resources
			curl_close($ch);
			echo $output;

		return;
		}

		function getParsed($lang='ja'){
			if($lang=='ja'){
			$msg=$this->params['url']['msg'];
			//解析用のファイルを作成
			$tmpFolder="/home/mchi/tmp/mecab/ja/";
			$fname=uniqid().'.wd';
			$fp=fopen($tmpFolder.$fname,"w");
			fwrite($fp,$msg);
			fclose($fp);
			//めきぶに解析をさせる
			
			exec('/usr/local/bin/mecab -Osimple '.$tmpFolder.$fname,$result);
			$nouns=array();
			$nouns=$result;
			unlink($tmpFolder.$fname);
			echo json_encode($nouns);
			//結果出力
			}else if($lang=='en'){
				$tmpFolder="/home/mchi/tmp/sp/en/";
				$fname=uniqid().'.wd';
			    $input=$tmpFolder.$fname;		
				$fp=fopen($input,"w");
				fwrite($fp,$this->params['url']['msg']);
				fclose($fp);	
				$output="text.out";
				$data=exec(" cd /home/mchi/sp-full/ &&  ./tagging.sh ${input} ${output} ");
				unlink($input);
				$data=explode(' ',$data);
				echo json_encode($data);
				return ;
			}
		}

		function obj2hash($object){
			$privious="";
			if(is_object($object)){
				$list=get_object_vars($object);
				while(list($k,$v)=each($list)){	
					if($k=='tok'){
						print_r($v."tok<br><br>");
						echo count($list);
					}
					$res[$k]=$this->obj2hash($v);
				}
			}else if(is_array($object)){
				while(list($k,$v)=each($object)){
					if($k=='tok'){
						print_r($v."tok<br><br>");
						echo count($object);
					}$res[$k]=$this->obj2hash($v);
				}
			}else{
				return $object;
			}
			return $res;
		}


}
?>
