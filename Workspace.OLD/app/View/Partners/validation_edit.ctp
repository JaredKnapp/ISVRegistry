<?php 
$this->Html->addCrumb('Partner List', array('controller' => 'partners', 'action' => 'index')); 
$this->Html->addCrumb($this->request->data['Product']['Partner']['name'], array('controller'=>'partners', 'action' => 'edit', $this->request->data['Product']['Partner']['id'])); 
$this->Html->addCrumb($this->request->data['Product']['name'], array('controller'=>'partners', 'action' => 'productEdit', $this->request->data['Product']['id']));
$this->Html->addCrumb($title);
?>
<div class="validations form">
<?php 
echo $this->Form->create('Validation'); 
echo $this->Form->input('referer', array('type'=>'hidden'));
?>
    <fieldset>
        <legend><?php echo __('Edit Validation');?></legend>
        <?php
        echo $this->Form->input('version', array('label'=> $this->request->data['Product']['name'] . ' Version'));
        
        $url = $this->Html->url(array('controller' => 'Settings', 'action' => 'getVersionsByPlatform'));
        echo $this->Form->input('platforms_id', array('label'=>'EMC Platform', 'id'=>'platforms', 'options'=>$platforms, 'rel'=>$url));
        echo $this->Form->input('platformversions_id', array('label'=>'Platform Version', 'id'=>'platformversions', 'options'=>$platformversions));

        $levels = Configure::read('Validation.levels');
        $options = array();
        foreach($levels as $key=>$value){
        	$options[$key] = $value['label'];
        }
        echo $this->Form->input('level', array('label'=>'Validation Level', 'options'=>$options));
        echo $this->Form->input('validator', array('label'=>'External Validator'));

        $statuses= Configure::read('Validation.statusOptions');
        $options = array();
        foreach($statuses as $key=>$value){
        	$options[$key] = $value['label'];
        }
        echo $this->Form->input('status', array('label'=>'Status', 'options'=>$options));
        echo $this->Form->input('estimatedcompletiondate', array('type' => 'text','label' => 'Estimated Completion Date (yyyy-mm-dd)','class' => ' j-date','div' => true));
        echo $this->Form->input('completiondate', array('type' => 'text','label' => 'Actual Completion Date (yyyy-mm-dd)','class' => ' j-date','div' => true));
        echo $this->Form->input('iscertified', array('label'=>'ISV Certified Validation'));
        echo $this->Form->input('sa_owner_id', array('label'=>'Solution Architect', 'type' => 'select', 'options' => $sausers, 'empty' => '(choose one...)'));
        echo $this->Form->input('ba_owner_id', array('label'=>'Business Development', 'type' => 'select', 'options' => $bausers, 'empty' => '(choose one...)'));
        echo $this->Form->input('url', array('label'=>'Url'));
        echo $this->Form->input('notes', array('label'=>'Notes', 'type'=>'textarea', 'maxlength'=>'10000'));
        ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>


<script>
function updateChoices(s){
	var selectedValue = s.val();
	var targeturl = s.attr('rel') + '/' + selectedValue;
	$.ajax({
		type: 'get',
		url: targeturl,
		beforeSend: function(r) {
			r.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		},
		success: function(r) {
			if (r) {
				var save = $('#platformversions').val()
				$('#platformversions').html(r);
				$('#platformversions').val(save);
			}
		},
		error: function(e) {
			alert("An error occurred: " + e.responseText.message);
			console.log(e);
		}
	});
}

$(function() {
	$('#platforms').change(function() { updateChoices($(this)); });
	$( document ).ready(function() { 
		updateChoices($('#platforms')); 
	    $('.j-date').each(function() { $(this).datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true }); });
	});
});
</script>