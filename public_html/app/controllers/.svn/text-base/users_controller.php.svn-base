<?php 
class UsersController extends AppController {    
	var $components = array( 'Auth');
	var $uses = array('User','Message');
	function beforeFilter() { 
		$this->Auth->loginError="ログイン情報が正しくありません。";        
		$this->Auth->authError = "ログインしてください。";        
		$this->Auth->allow('add','login');        
		$this->Auth->autoRedirect=false;
//		$this->Auth->loginRedirect="/Users/home";
	}
	function login() {
		if($this->Auth->user()){
			$this->User->id=$this->Auth->user('id');
			$this->User->saveField('last_login',date('Y-m-d H:i:s'));
			$id=$this->Message->find('first',array(
													'order'=>'Message.created desc',
													'fields'=>array('id')));
			$this->Session->write('firstMsgId',$id['Message']['id']);
			$this->Session->write('userLang',$this->data['User']['lang']);
			$this->redirect("/");
		}
		$this->layout='logout';
	
	}   
	function logout(){
		$this->redirect($this->Auth->logout());
		
	}   

	function add() {
		if (!empty($this->data)) {
			//ユニークユーザ確認
			$this->User->set($this->data);
				$this->User->create();
				if ($this->User->save($this->data)) {
					$this->Session->setFlash(__('The user has been saved', true));
				} else {
					$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
				}
					$this->redirect('login/');
		}else{
			$this->layout='logout';
		}
	}
} 
?>
