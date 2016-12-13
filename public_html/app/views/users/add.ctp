<div data-role="page" data-theme="e">
<div data-role="header">
<h2>Registration</h2>
</div>
<div data-role="content">
<div class='error-message'><h3><?php echo $this->Session->flash('inputError'); ?></h2></div>
<?php echo $this->Form->create('User');?>
    <fieldset>
        <legend><?php __('Registration'); ?></legend>
    <?php
        //echo $this->Form->input('name');
        echo $this->Form->input('username',
                array('class'=>'email hankaku required',
	                  'div'=>false,
		              'label'=>'Login ID ',
                ));
        echo $this->Form->input('nickname',
                array('div'=>false,
                      'label'=>'Nick name'
				));
  
        echo $this->Form->input('password',
                            array('class'=>'',
                                  'div'=>false,
								  'value'=>null,
                                  'label'=>'Password'));
    ?>  
    </fieldset>
<?php echo $this->Form->end(__('Regist', true));?>
</div>
