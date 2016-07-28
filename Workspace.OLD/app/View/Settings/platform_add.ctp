<div class="platforms form">
<?php echo $this->Form->create('Platform'); ?>
    <fieldset>
        <legend><?php echo __('Add Platform'); ?></legend>
        <?php
        echo $this->Form->input('name');
        echo $this->Form->input('sortorder');
        ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>