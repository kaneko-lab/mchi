<?php
class TranslatedMessage extends AppModel {
    var $name = 'TranslatedMessage';
	var $useTable='translated_messages';
	var $belongsTo=array(
		'Message'=>array(
			'className'=>'Message',
			'foreignKey'=>'message_id'
		)
	);
   }
?>
