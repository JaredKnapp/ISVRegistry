<?php 
$this->Html->addCrumb('Partner List', array('controller' => 'partners', 'action' => 'index')); 
$this->Html->addCrumb($this->request->data['Partner']['name'], array('controller'=>'partners', 'action' => 'edit', $this->request->data['Partner']['id'])); 
$this->Html->addCrumb($this->request->data['Product']['name']);
?>
<div class="products form">
<?php echo $this->Form->create('Product'); ?>
    <fieldset>
        <legend><?php echo __('Edit Product'); ?></legend>
        <?php
        echo $this->Form->input('name');
        echo $this->Form->input('url');
        
        $url = $this->Html->url(array('controller' => 'Settings', 'action' => 'getWorkloadsByIndustry'));
        echo $this->Form->input('industries_id', array('label'=>'Industry [{add new} link]', 'id' => 'industries', 'empty' => '(choose one...)', 'rel'=>$url));
        echo $this->Form->input('workloads_id', array('label'=>'Workload [{add new} link]', 'id' => 'workloads', 'empty' => '(choose one...)'));

        echo $this->Form->input('sa_owner_id', array('label'=>'Solution Architect', 'type' => 'select', 'options' => $sausers, 'empty' => '(choose one...)'));
        echo $this->Form->input('ba_owner_id', array('label'=>'Business Development', 'type' => 'select', 'options' => $bausers, 'empty' => '(choose one...)'));
        ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>

<?php $statuses = Configure::read('Validation.statusOptions'); ?>


<h1>All Validations</h1>
		<?php echo $this->Html->link('Add New Validation',array('action' => 'validationAdd', $this->request->data['Product']['id'])); ?>  
		<table>
			<thead>
				<tr>
					<th>Version</th>
					<th>Platform</th>
					<th>Solution Architect</th>
					<th>Validator</th>
					<th>Level</th>
					<th>Status</th>
					<th>ISV Certified?</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->request->data['Validations'] as $validation): ?>
	<tr>
	<td>
						<?php echo h($validation['version']); //echo var_dump($validation); ?>
</td>
<td>
						<?php echo h($validation['PlatformVersion']['Platform']['name'].' '.$validation['PlatformVersion']['version']); ?>
</td>
<td>
						<?php 
						echo h(isset($validation['SaOwner']['id']) ? ($validation['SaOwner']['firstname']. ' ' . $validation['SaOwner']['lastname']) : 'none'); 
						?>
</td>
<td>
						<?php echo h($validation['validator']); ?>
</td>
<td>
						<?php echo h($validation['level']); ?>
</td>
<td style="text-align:center;">
						<?php echo h(array_key_exists($validation['status'], $statuses) ? $statuses[$validation['status']]['label'] : $validation['status']); ?>
</td>
<td>
	<?php echo h($validation['iscertified']=='1'?'Yes':''); ?>
</td>
<td class='actions'>
						<?php
						echo $this->Html->link( 'Edit', array('action' => 'validationEdit', $validation['id']) );
						echo $this->Form->postLink( 'Delete', array('action' => 'validationDelete', $validation['id']), array('confirm'=>'Are you sure you want to delete this validation?') );
						?>
</td>
</tr>
				<?php endforeach; ?>
			</tbody>
		</table>



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
				var save = $('#workloads').val()
				$('#workloads').html(r);
				$('#workloads').val(save);
			}
		},
		error: function(e) {
			alert("An error occurred: " + e.responseText.message);
			console.log(e);
		}
	});
}

$(function() {
	$('#industries').change(function() { updateChoices($(this)); });
	$( document ).ready(function() { updateChoices($('#industries')); });
});
</script>