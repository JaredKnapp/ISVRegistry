<div class="users form">
<?php echo $this->Flash->render('auth'); ?>
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend>
			<?php echo __('Please enter your email address and password'); ?>
		</legend>
		<?php
		echo $this->Form->input('email', array('label' => 'Email Address'));
		echo $this->Form->input('password', array('label' => 'Password', 'type' => 'password', 'div'=>array('class'=>'required')));
		?>
    </fieldset>
<?php echo $this->Form->end(__('Login')); ?>
</div>