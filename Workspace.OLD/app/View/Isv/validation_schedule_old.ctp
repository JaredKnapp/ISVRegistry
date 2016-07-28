<?php
$isCollapsed = true;
if(isset($this->request['named']) && array_key_exists('collapsed', $this->request['named'])){
	$isCollapsed = ( $this->request['named']['collapsed'] == 'true' );
}

$statuses= Configure::read('Validation.statusOptions');
?>

<script>
$(function() {
	$( "#accordion" ).accordion({
		active: <?php echo $isCollapsed ? 'false' : 0 ?>,
		activate: function(event, ui) { 
				$('#FilterCollapsed').val(ui.newHeader.text() ? false : true);
				//$('#FilterIndexForm').submit();
			},
		heightStyle: "content",
		collapsible: true,
		create: function(event, ui) { $("#accordion").show(); }
	});
});
</script>

<?php
echo $this->Form->create("Filter");
echo $this->Form->hidden('collapsed');
?>
<div id="filters">
	<div id="accordion">
	  <h3>View Filters</h3>
		<div>
			<table>
				<tbody>
					<tr>
						<td>
							<?php echo $this->Form->text("partners-name", array('placeholder' => "Partner Name...", 'default' => '')); ?>
						</td>
						<td>
							<?php echo $this->Form->text("validator", array('placeholder' => "Validator...", 'default' => '')); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo $this->Form->text("products-name", array('placeholder' => "Product Name...", 'default' => '')); ?>
						</td>
						<td>
							<?php echo $this->Form->input('platforms-id', array('type'=>'select', 'label'=>false, 'div'=>false, 'options'=>$platforms, 'empty'=>'(Platform...)')) ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php
							$options = array();
							foreach($statuses as $key=>$value){
								$options[$key] = '<div class="statusovalsmall" style="background-color:'.$statuses[$key]['color']['back'].'; color:'.$statuses[$key]['color']['fore'].'; border-color:'.$statuses[$key]['color']['border'].';">&nbsp;</div>&nbsp'.$value['label'];
							}
							echo $this->Form->input('validations-status', array('type'=>'select', 'escape'=>false, 'label'=>false, 'div'=>false, 'multiple'=>'checkbox', 'options'=>$options)); 
							?>
						</td>
						<td>
							<?php echo $this->Form->input('validations-sa_owner_id', array('type'=>'select', 'label'=>false, 'div'=>false, 'options'=>$sausers, 'empty'=>'(Solution Architect...)')) ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<table>
	<thead>
		<tr>
			<th>
				<?php echo $this->Paginator->sort('sa_owner', 'Solutions Architect'); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('partners.name', 'ISV Partner'); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('products.name', 'ISV Product'); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('validator', 'Validator'); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('platform', 'Targeted EMC Technology'); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('level', 'Validation Level'); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('status', 'Effort Phase'); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('estimatedcompletiondate', 'Target Completion Date'); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('notes', 'Notes'); ?>
			</th>
			<?php
			if (AuthComponent::user('id') && (AuthComponent::user('role')==='editor' || AuthComponent::user('role')==='admin')) {
				echo '<th>Actions</th>';
			}
			?>
		</tr>
	</thead>
	<tbody>
	
<?php foreach ($validations as $validation): ?>
	<tr>
		<?php $status = $validation['validations']['status']; ?>
		<td width="200"><?php echo h(empty($validation[0]['sa_owner']) ? 'unknown' : $validation[0]['sa_owner']); ?></td>
		<td width="400"><?php echo h($validation['partners']['name']); ?></td>
		<td width="400"><?php echo h($validation['products']['name']); ?></td>
		<td width="200"><?php echo h($validation['validations']['validator']); ?></td>
		<td width="200"><?php echo h($validation[0]['platform']); ?></td>
		<td width="150"><?php echo $validation['validations']['level']; ?></td>
		<td width="125"><div class="statusovallarge" style="background-color:<?php echo $statuses[$status]['color']['back'] ?>; color:<?php echo $statuses[$status]['color']['fore'] ?>; border-color:<?php echo $statuses[$status]['color']['border'] ?>;"><?php echo h($statuses[$status]['label']); ?></div></td>
		<td width="125"><?php echo h($this->Time->format('m/Y', $validation['validations']['estimatedcompletiondate'])); ?></td>
<td width="500">
		<?php 
		echo h($validation['validations']['notes']);
		?>
</td>

<?php
if (AuthComponent::user('id') && (AuthComponent::user('role')==='editor' || AuthComponent::user('role')==='admin')) {
	echo '<td  class="actions">';
	echo $this->Html->link( 'Edit', array('controller' => 'Partners', 'action' => 'validationEdit', $validation['validations']['id']) );
	echo '</td>';
}
?>

</tr>
<?php endforeach; ?>
	</tbody>
</table>

<ul class="paging">
<?php 
echo $this->Paginator->prev('&laquo;PREV', array( 'tag' => 'li', 'escape' => false), null, array('class' => 'paging prev disabled' ,'tag' => 'li', 'escape' => false));
echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li' ,'currentClass' => 'current', 'currentTag' => 'a' , 'escape' => false));
echo $this->Paginator->next('NEXT&raquo;', array( 'tag' => 'li', 'escape' => false), null, array('class' => 'paging next disabled' ,'tag' => 'li', 'escape' => false));
?>
</ul>
<br>
<?php
echo $this->Paginator->counter('Page {:page} of {:pages}');
echo ', showing &nbsp;'.$this->Form->input("Filter.limit", array('label'=>false, 'div'=>false, 'options'=>array('10'=>'10', '20'=>'20', '50'=>'50', '100'=>'100'), 'default'=>'20', 'onChange'=>'this.form.submit()'));
echo '&nbsp;'.$this->Paginator->counter('records out of {:count} total, starting on record {:start}, ending on {:end}');
echo $this->Form->end();
?> 

<script>
var timerid;
var delay = 500;
$('#FilterValidationScheduleForm :input').keyup(function() {
  var form = this;
  clearTimeout(timerid);
  timerid = setTimeout(function() { $('#FilterValidationScheduleForm').submit(); }, delay);
});

$('#FilterValidationScheduleForm :checkbox').click(function() {
  var form = this;
  clearTimeout(timerid);
  timerid = setTimeout(function() { $('#FilterValidationScheduleForm').submit(); }, delay);
});

$('select').change(function() {
  var form = this;
  clearTimeout(timerid);
  timerid = setTimeout(function() { $('#FilterValidationScheduleForm').submit(); }, delay);
});
</script>