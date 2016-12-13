<?php
	class PagesController extends AppController{
        var $name ='Pages';
        var $uses = array('Message');
		var $components = array( 'Auth');
		var $helpers=array('javascript');
		function display(){
//			$sOrigin = ( Empty($_SERVER['HTTP_ORIGIN']) ) ? '*' :　$_SERVER['HTTP_ORIGIN'];
		    header( "Access-Control-Allow-Origin:*");	
			header( 'Access-Control-Allow-Origin:http://mchi.kaneko-lab.net' );

//			print_r($this->Session->read('userLang'));
			$this->set('userLang',$this->Session->read('userLang'));
			$this->set('currentLatestMsgId',$this->Session->read('firstMsgId'));
			$this->set('currentUserId',$this->Auth->user('id'));
			$this->set('nickName',($this->Auth->user('nickname')));
			$this->set('lastLogin',($this->Auth->user('last_login')));
		}
		

	}

?>
