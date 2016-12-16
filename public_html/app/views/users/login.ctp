<div data-role="page">
	<div data-role="header">
	<h2>MCHI - 多言語チャットシステム：ログイン</h2>
	</div>

	<div data-role="content" style="margin:10px;">
	<div style="max-width:500px;margin:auto">
	<?php
	if ($session->check('Message.auth'))echo $session->flash('auth');
	echo $form->create('User', array('action' => 'login'));
	echo $form->input('username',array('class'=>'','label'=>'Login id','div'=>false));
	echo $form->input('password',array('label'=>'Password'));
	echo $form->input('lang',array('type'=>'select',
									'label'=>'Choose your language',
									'options'=>array(
										'en'=>'English',
										'ja'=>'Japanese',
										'ko'=>'Korean',
										'th'=>'Thai',
										'vi'=>'Vietnamese')));
echo $form->end('Log on');
	?>
	<a href="/Users/add/" data-transition="pop" data-role="button">Regist</a>
	</div>
	</div>
</div>
