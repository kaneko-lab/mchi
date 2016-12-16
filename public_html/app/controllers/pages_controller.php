<?php
	class PagesController extends AppController{
        var $name ='Pages';
        var $uses = array('Message');
		var $components = array( 'Auth');
		var $helpers=array('javascript');
		function display(){
			$this->redirect(array("controller" => "Users", "action" => "logout"));
		}
		

	}

?>
