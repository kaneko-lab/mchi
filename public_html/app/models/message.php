<?php
class Message extends AppModel {
    var $name = 'Message';
	var $useTable='messages';
	var $belongsTo=array(
		'User'=>array(
			'className'=>'User',
			'foreignKey'=>'user_id',
			'fields'=>array('nickname','last_login')
		),
		'Category1'=>array(
			'className'=>'Category',
			'foreignKey'=>'category_id1'
			),
		'Category2'=>array(
			'className'=>'Category',
			'foreignKey'=>'category_id2'
			),
		'Category3'=>array(
			'className'=>'Category',
			'foreignKey'=>'category_id3'
			),
		);
	var $hasMany = array(
		'TranslatedMessage'=>array(
			'className'=>'TranslatedMessage'
		),
		'TranslateHelper'=>array(
			'className'=>'TranslateHelper'
		)
	);
	const NOUN = "NOUN"; //名詞
	const PRONOUN = "PRONOUN"; //代名詞
	const ADJECTIVE = "ADJECTIVE"; // 形容詞
	const VERB = "VERB";//動詞
	const ADVERB = "ADVERB";//副詞
	const PREPOSITION = "PREPOSITION"; //前置詞
	const CONJUNCTION = "CONJUNCTION"; //接続詞
	const INTERJECTION = "INTERJECTION"; //感動詞
	const ETC = "ETC"; //その他
	const MAX_KEYWORD_NUM = 2;

	private  $_previousCURLError = null;

	/**
	 * @param $msgId
	 * @param $targetLang
	 * @return array
	 */
	public function getMessageWithTransAndKeyword($msgId,$targetLang){
		$returnData = array('data'=>array('translated_message'=>null,'help_data'=>null));
		$retry = 0;
		//Set Message Lock
		while($this->isTranslatedMessageLock($msgId)){
			usleep(500000);//wait 0.5;
			$retry++;
			if($retry == 20){
				$returnData['data']['translated_message']="Failed to get lock for message id ".$msgId;
				return $returnData;

			}
		}


		//Get Translated Data From database first.

		$this->doLockForTranslatedMessage($msgId);

		$this->unbindModel(array('belongsTo'=>array('Category1','Category2','Category3')));
		$this->id = $msgId;
		$message = $this->read();
		$translatedMessage = null;


		if($targetLang == $message['Message']['lang']){
			$translatedMessage = $message['Message']['contents'];
		}

		//check target lang is exist. if not get translate from google and save it.
		else if (!empty($message['TranslatedMessage'])){
			foreach ($message['TranslatedMessage'] as $tm){
				if($tm['lang'] == $targetLang){
					$translatedMessage = $tm;
					break;
				}
			}
		}

		//Get translate from google.
		if($translatedMessage == null){
			$requestUrl =
				"https://www.googleapis.com/language/translate/v2?".
				"key=".Configure::read('GOOGLE_API_KEY')."&q=".urlencode($message['Message']['content']).
				"&source=".$message['Message']['lang'].
				"&target=".$targetLang;

			$resultString = $this->curl($requestUrl);
			if($resultString == false){
				$translatedMessage = "Failed to get data from CURL with curl error " . $this->_previousCURLError. " in message.php on line ".__LINE__;
			}else {
				$result = json_decode($resultString,true);
				//Failed to get trans with reason.
				if (isset($result['error'])) {
					$translatedMessage = "Failed to get trans from google with error message : " . $result['error']['message'];
				} else {
					$translatedMessage = $result['data']['translations'][0]['translatedText'];
				}
			}

			$this->TranslatedMessage->create();
			$this->TranslatedMessage->save(array('message_id'=>$msgId,'lang'=>$targetLang,'translated_message'=>$translatedMessage));
		}

		$returnData['data']['translated_message'] = $translatedMessage;

		if(!empty($message['TranslateHelper'])){
			$jsonData = json_decode($message['TranslateHelper'][0]['img_json'],true);
			$returnData['data']['help_data'] =isset($jsonData['items'])?$jsonData['items']:array();
		}else{
			$returnData['data']['help_data'] = array();
		}

		$this->unLockForTranslatedMessage($msgId);
		return $returnData;
	}

	/**
	 * Save user message.
	 * Do POS tagging and create translate helper.
	 * @param $message
	 * @param $userId
	 * @param $lang
	 * @return bool
	 */
	public function saveMessage($message,$userId,$lang){
		$this->create();
		$this->save(array('content'=>$message,'user_id'=>$userId,'lang'=>$lang));
		$msgId = $this->getLastInsertID();
		$this->doLockForTranslatedMessage($msgId);

		//1.Get parsed data.
		$auth = "Xidkexo121xlaAadkxidg";
		$posTaggerRequestURL = "http://morph.kaneko-lab.net/api/getParsedMessage.json";
		$postData = array('auth'=>$auth,'lang'=>$lang,'message'=>$message);
		$posTaggerResult = $this->curl($posTaggerRequestURL,true,$postData);

		//Error on curl
		if($posTaggerResult === false){
			//Logging and do not save helpers.
			$this->log('Failed to get data from curl on message.php line :  '.__LINE__." with reason ".$this->_previousCURLError);
			$this->unLockForTranslatedMessage($msgId);
			return false;
		}else{
			//Get keywords for the message.
			$this->log($posTaggerResult);
			$posTaggerArray = json_decode($posTaggerResult,true);

			if($posTaggerArray['RESULT']['CODE']!= 1000){
				$this->log('Failed to get POS from morph.kaneko-lab.net with code ' .$posTaggerArray['RESULT']['CODE']. ' and DESC : '.$posTaggerArray['RESULT']['DESC'] );
				$this->unLockForTranslatedMessage($msgId);
				return false;
			}

			$nouns = array();
			$verbs = array();
			foreach($posTaggerArray['RESULT']['DATA']['ANALYSIS_RESULT'] as $ar){
				if($ar['SIMPLE'] == Message::NOUN){
					$nouns[] = $ar['WORD'];
				}
				if($ar['SIMPLE'] == Message::VERB){
					$verbs[] = $ar['WORD'];
				}
			}

			$maxKeyword = Message::MAX_KEYWORD_NUM;
			$keyCount = 0 ;

			//Save Images with noun
			foreach($nouns as $noun){
				if($keyCount >= $maxKeyword )
					break;
				$this->createTranslateHelper($msgId,Message::NOUN,$noun);
				$keyCount ++;
			}

			//Save images with whole sentence
			if($keyCount < $maxKeyword)
				$this->createTranslateHelper($msgId,Message::ETC,$message);
		}
		$this->unLockForTranslatedMessage($msgId);
		return true;
	}


	/**
	 *
	 * Create translate helper.
	 * @param $msgId
	 * @param $tag
	 * @param $keyword
	 * @return bool
	 */
	private function createTranslateHelper($msgId,$tag,$keyword){
		$imagesRequestURL = "https://www.googleapis.com/customsearch/v1?q=". urlencode($keyword).
							"&cx=".Configure::read('GOOGLE_SEARCH_SITE_ID')."&safe=high&searchType=image".
							"&key=".Configure::read('GOOGLE_API_KEY')."&start=1&num=10";

		$resultString = $this->curl($imagesRequestURL);
		if($resultString == false){
			$this->log("Failed to get CURL result with CURL error " . $this->_previousCURLError .'in message.php on line '.__LINE__);
			return false;
		}

		$jsonResult = json_decode($resultString,true);
		//Failed to get trans with reason.
		if(isset($jsonResult['error'])){
			$this->log("Failed to get trans from google with error message : " .  $jsonResult['error']['message'].' in message.php on line '.__LINE__);
			return false;
		}
		$this->TranslateHelper->create();
		$this->TranslateHelper->save(array('message_id'=>$msgId,'tag'=>$tag,'keyword'=>$keyword,'img_json'=>$resultString));
	}

	/**
	 * @param $url
	 * @param bool $isPost
	 * @param array $postData
	 * @return mixed
	 */
	private function curl($url,$isPost = false, $postData = array()){
		$ch = curl_init();
		// Now set some options (most are optional)
		// Set URL to download
		curl_setopt($ch, CURLOPT_URL, $url);

		if($isPost){
			curl_setopt($ch,CURLOPT_POST,true);
			curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($postData));
		}
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);  // オレオレ証明書対策
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);  //

		// Include header in result? (0 = yes, 1 = no)
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// Should cURL return or print out the data? (true = return, false = print)
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Timeout in seconds
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		// Download the given URL, and return output
		$output = curl_exec($ch);
		// Close the cURL resource, and free system resources

		if($output == false){
			$this->_previousCURLError = curl_errno($ch);
		}

		curl_close($ch);
		return $output;
	}

	private function doLockForTranslatedMessage($msgId){
		$fp = fopen($this->getLockFile($msgId), "w");
		fclose($fp);
	}
	private function unLockForTranslatedMessage($msgId){
		unlink($this->getLockFile($msgId));
	}
	private function isTranslatedMessageLock($msgId){
		$lockFile = $this->getLockFile($msgId);
		return file_exists($lockFile);
	}

	private function getLockFile($msgId){
		return TMP.'MSG_'.$msgId;
	}


   }


?>
