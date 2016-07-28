<?php 
$this->Html->addCrumb('Partner List', array('controller' => 'partners', 'action' => 'index')); 
$this->Html->addCrumb('Add Partner'); 
?>

<div class="partners form">
<?php echo $this->Form->create('Partner'); ?>
    <fieldset>
        <legend><?php echo __('Add Partner'); ?></legend>
        <?php
        echo $this->Form->input('name');
        echo $this->Form->input('url');
        echo $this->Form->input('sa_owner_id', array('label'=>'Solution Architect', 'type' => 'select', 'options' => $sausers, 'empty' => '(choose one...)'));
        echo $this->Form->input('ba_owner_id', array('label'=>'Business Development', 'type' => 'select', 'options' => $bausers, 'empty' => '(choose one...)'));
        ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>