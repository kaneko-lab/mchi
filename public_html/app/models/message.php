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
   }
?>
