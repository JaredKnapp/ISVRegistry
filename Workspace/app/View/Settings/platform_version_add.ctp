<div class="platforms form">
<?php echo $this->Form->create('PlatformVersion'); ?>
    <fieldset>
        <legend><?php echo __('Add ' . $platform['Platform']['name'] . ' Version'); ?></legend>
        <?php
        echo $this->Form->input('version');
        echo $this->Form->input('visibledefault', array('label'=>'Show by default in reports?', 'type'=>'checkbox'));
        ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>