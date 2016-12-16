<?php
class TranslateHelper extends AppModel {
    var $name = 'TranslateHelper';
	var $useTable='translate_helpers';
	var $belongsTo=array(
		'Message'=>array(
			'className'=>'Message',
			'foreignKey'=>'message_id'
		)
	);
}
?>
