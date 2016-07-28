<div class="platforms form">
<?php echo $this->Form->create('Platform'); ?>
    <fieldset>
        <legend><?php echo __('Add Platform'); ?></legend>
        <?php
        echo $this->Form->input('name');
        echo $this->Form->input('sortorder');
        echo $this->Form->input('organizations_id', array('label'=>'Organization'));
        ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>