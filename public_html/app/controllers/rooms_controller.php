<?php
	class RoomsController extends AppController{
        var $name ='Rooms';
        var $uses = array('Message');
		var $components = array('Auth');
		var $helpers=array('javascript');
		function index(){
		    header( "Access-Control-Allow-Origin:*");	
			header( 'Access-Control-Allow-Origin:http://mchi.kaneko-lab.net' );
			$this->set('userLang',$this->Session->read('userLang'));
			$this->set('currentLatestMsgId',$this->Session->read('firstMsgId'));
			$this->set('currentUserId',$this->Auth->user('id'));
			$this->set('nickName',($this->Auth->user('nickname')));
			$this->set('lastLogin',($this->Auth->user('last_login')));
		}
		

	}

