<div class="workloads form">
    <?php echo $this->Form->create('Workload'); ?>
    <fieldset>
        <legend>
            <?php echo __('Add ' . $industry['Industry']['name'] . ' workload'); ?>
        </legend>
        <?php
        echo $this->Form->input('name');
        echo $this->Form->input('url');
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
</div>
