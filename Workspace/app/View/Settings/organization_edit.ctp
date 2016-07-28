<div class="organizations form">
    <?php echo $this->Form->create('Organization'); ?>
    <fieldset>
        <legend>
            <?php echo __('Edit Organization'); ?>
        </legend>
        <?php
        echo $this->Form->input('name');
        echo $this->Form->input('url');
        echo $this->Form->input('css', array('label'=>'CSS Text'));
        echo $this->Form->input('is_default', array('label'=>'Default Org?'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
</div>
