<div class="industries form">
<?php echo $this->Form->create('Industry'); ?>
    <fieldset>
        <legend><?php echo __('Add Industry'); ?></legend>
        <?php
        echo $this->Form->input('name');
        ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>