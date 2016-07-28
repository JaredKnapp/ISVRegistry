<!-- app/View/Users/add.ctp -->
<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Add User'); ?></legend>
        <?php
        echo $this->Form->input('firstname');
        echo $this->Form->input('lastname');
        echo $this->Form->input('email');
        echo $this->Form->input('password', array('type'=>'password'));
        echo $this->Form->input('is_sa', array('label'=>'Is Solution Architect?'));
        echo $this->Form->input('is_ba', array('label'=>'Is Business Dev Manager?'));
        echo $this->Form->input('role', array('options' => array('admin' => 'Admin', 'editor' => 'Editor', 'user' => 'User')));
        ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>