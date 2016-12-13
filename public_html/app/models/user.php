<?php
class User extends AppModel {
    var $name = 'User';
    var $validate = 
        array('username' => array( 
                'isUnique' => array(    
                    'rule' => 'isUnique',
                    'message'=>'すでに登録されているIDです。'  
                    ),   
                'notEmpty' => array(  
                    'rule' => 'notEmpty',    
                    'required' => true, 
                    'message'=>'ログインIDを入力してください。',
                'email'=>array(
                    'rule'=>'email',
                    'message'=>'メールアドレスを入力してください。'
                )   
                    )   
                ),  
            'nickname' => array( 
                'isUnique' => array(    
                    'rule' => 'isUnique',
                    'message'=>'Input diffrent nick name'  
                    ),   
                'notEmpty' => array(  
                    'rule' => 'notEmpty',    
                    'required' => true, 
                    'message'=>'Input nick name'  
                    )   
                ),  
            'password' => array( 
                'notEmpty' => array(  
                    'rule' => 'notEmpty',    
                    'required' => true, 
                    'message'=>'パスワードを入力してください。'  
                    ),
				 'minLength' => array(
				    'rule' => array('minLength', '4'),
	                'message' => 'Mimimum 4 characters long'

					), 
                ),  
			  );  
}
?>
