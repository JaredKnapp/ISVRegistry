<div class="myaccount form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo 'Logged in as: ' . AuthComponent::user('email'); ?></legend>
        <?php
        echo $this->Form->input('firstname');
        echo $this->Form->input('lastname');
        echo $this->Form->input('email');
        echo $this->Form->input('password', array('type'=>'password'));
        ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link('Account Info', array('controller' => 'Account', 'action'=>'myAccount')); ?></li>
		<li><?php echo $this->Html->link('Notifications', array('controller' => 'Account', 'action'=>'myNotifications')); ?></li>
		<li><?php echo $this->Html->link('Log Out',array('controller' => 'Auth', 'action' => 'logout')); ?></li>
	</ul>
</div>