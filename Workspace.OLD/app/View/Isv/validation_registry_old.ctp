<?php
$isCollapsed = true;
if(isset($this->request['named']) && array_key_exists('collapsed', $this->request['named'])){
	$isCollapsed = ( $this->request['named']['collapsed'] == 'true' );
}
?>

<script>
$(function() {
	$( "#accordion" ).accordion({
		active: <?php echo $isCollapsed ? 'false' : 0 ?>,
		activate: function(event, ui) { 
				$('#FilterCollapsed').val(ui.newHeader.text() ? false : true);
			},
		heightStyle: "content",
		collapsible: true,
		create: function(event, ui) { $("#accordion").show(); }
	});
});
</script>

<div id="exportlinks">
<span><!--Export to:--></span>

<?php 
$linkParams = array(
	'controller'=>$this->request->params['controller'],
	'action'=>$this->request->params['action'],
	'ext'=>'csv',
	'page'=>1
	);

$linkParams = array_merge($linkParams, $this->request->params['named']);

echo $this->Html->image('CSV.png',array(
	'height'=>35,
	'alt'=>'Export to CSV', 
	'url'=>$linkParams
	));
?>
</div>

<?php
echo $this->Form->create("Filter");
echo $this->Form->hidden('collapsed');

$certifiedIcon = $this->Html->image('CertifiedCheckmark.png', array('height'=>'20px', 'width'=>'20px', 'class'=>'certifiedicon', 'title'=>'ISV Certified Validation'));
?>

<div id="filters">
	<div id="accordion">
	  <h3>View Filters</h3>
		  <div>
			<table width="100%">
				<tr>
					<td width="40%">
						<table>
							<thead>
								<th>Validation Filters</th>
							</thead>
							<tbody>
								<tr><td><?php echo $this->Form->text("registry-partner", array('placeholder' => "Partner names...", 'default' => '')); ?></td></tr>
								<tr><td><?php echo $this->Form->text("registry-product", array('placeholder' => "Product names...", 'default' => '')); ?></td></tr>
								<tr><td><?php echo $this->Form->text("registry-industry", array('placeholder' => "Industries...", 'default' => '')); ?></td></tr>
								<tr><td><?php echo $this->Form->text("registry-workload", array('placeholder' => "Workloads...", 'default' => '')); ?></td></tr>
								<tr>
									<td>
	<?php 
	$statuses= Configure::read('Validation.statusOptions');
	$options = array();
	foreach($statuses as $key=>$value){
		$options[$key] = '<div class="statusovalsmall" style="background-color:'.$statuses[$key]['color']['back'].'; color:'.$statuses[$key]['color']['fore'].'; border-color:'.$statuses[$key]['color']['border'].';">&nbsp;</div>&nbsp'.$value['label'];
	}
	echo $this->Form->input('versions-status', array('type'=>'select', 'escape'=>false, 'label'=>false, 'div'=>false, 'multiple'=>'checkbox', 'options'=>$options)); 
	?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					<td width="60%">
						<table>
							<thead>
								<tr>
									<th>Platform</th>
									<th>Validation</th>
									<th>
										Show?
									</th>
								</tr>
							</thead>
							<tbody>
						
	<?php
	$levels= Configure::read('Validation.levels');
	$options = array();
	foreach($levels as $key=>$value){
		$options[$key] = $value['label'];
	}

	foreach($platforms as $index=>$platform){
		echo '<tr><td>';
		echo $this->Form->label('T'.$platform['platformversions']['id'].'-level', $platform['platforms']['name']. ' ' . $platform['platformversions']['version']);
		echo '</td><td>';
		echo $this->Form->input('T'.$platform['platformversions']['id'].'-level', array('label' => false, 'div'=>false, 'options'=>$options, 'empty'=>'(all levels)'));
		echo '</td><td>';
		$checkvalue = isset($this->data['Show']) ?  $this->data['Show']['T'.$platform['platformversions']['id']] : $platform['platformversions']['visible'];
		echo $this->Form->checkbox('Show.T'.$platform['platformversions']['id'], array('label'=>false, 'checked'=>$checkvalue));
		echo '</td></tr>';
	}
	?>
							</tbody>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<table>
  <thead>
    <tr>
      <th><?php echo $this->Paginator->sort('partner', 'Partners'); ?></th>
      <th><?php echo $this->Paginator->sort('product', 'Products'); ?></th>
      <th><?php echo $this->Paginator->sort('version', 'Version'); ?></th>
      <th><?php echo $this->Paginator->sort('industry', 'Industry'); ?></th>
      <th><?php echo $this->Paginator->sort('workload', 'Workload'); ?></th>
<?php
foreach($platforms as $index=>$platform){
	$isVisible = true;
	$platformindex = 'T'.$platform['platformversions']['id'];
	if(isset($this->data['Show'])){
		$isVisible = $this->data['Show'][$platformindex]=='0' ? false : true;
	} else {
		$isVisible = $platform['platformversions']['visible']=='1'? true : false;
	}
	if($isVisible){
		echo '      <th style="text-align:center;">' . $this->Paginator->sort($platformindex, $platform['platforms']['name'] . ' ' . $platform['platformversions']['version']) . '</th>'."\r\n";
	}
}
if (AuthComponent::user('id') && (AuthComponent::user('role')==='editor' || AuthComponent::user('role')==='admin')) {
	echo '<th>Actions</th>';
}
?>
    </tr>
  </thead>
  <tbody>
  
<?php foreach ($validations as $validation): ?>
	<tr>
	<td><?php 
	if(empty($validation['registry']['partner_url'])){
		echo h($validation['registry']['partner']); 
	} else {
		echo $this->Html->link(h($validation['registry']['partner']), $validation['registry']['partner_url'], array('target' => '_blank')); 
	}
	?></td>
	<td><?php 
	if(empty($validation['registry']['product_url'])){
		echo h($validation['registry']['product']);
	} else {
		echo $this->Html->link(h($validation['registry']['product']), $validation['registry']['product_url'], array('target' => '_blank')); 
	}
	?></td>
	<td><?php echo h($validation['registry']['version']); ?></td>
	<td><?php echo h($validation['registry']['industry']); ?></td>
	<td><?php echo h($validation['registry']['workload']); ?></td>
	<?php
	foreach($platforms as $index=>$platform){
		//var_dump($validation[$platformindex]);
		$isVisible = true;
		$platformindex = 'T'.$platform['platformversions']['id'];
		if(isset($this->data['Show'])){
			$isVisible = $this->data['Show'][$platformindex]=='0' ? false : true;
		} else {
			$isVisible = $platform['platformversions']['visible']=='1'?true:false;
		}
		if($isVisible){
			$status = $validation[$platformindex][$platformindex.'_status'];
			echo '<td style="text-align: center;">';
			if(is_null($status)){
				echo '<div class="statusblank">&nbsp;</div>';
			} else {
				$level = $validation[$platformindex][$platformindex];
				$ovalStyle = 'background-color:'.$statuses[$status]['color']['back'].'; color:'.$statuses[$status]['color']['fore'].'; border-color:'.$statuses[$status]['color']['border'].';';
				echo '<div class="statusoval hasicon" title="'.$statuses[$status]['label'].'" style="'. $ovalStyle . '">';
				echo $level;
				if($validation[$platformindex][$platformindex.'_iscertified']=='1'){
					echo $certifiedIcon;
				}
				echo '</div>';
			}
			echo '</td>';
		}
	}
	
	if (AuthComponent::user('id') && (AuthComponent::user('role')==='editor' || AuthComponent::user('role')==='admin')) {
		echo '<td  class="actions">';
		echo $this->Html->link( 'Edit', array('controller' => 'Partners', 'action' => 'productEdit', $validation['registry']['product_id']) );
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

//echo var_dump($this->request->params);

?> 

<script>
var timerid;
var delay = 500;
$('#FilterValidationRegistryForm :input').keyup(function() {
  var form = this;
  clearTimeout(timerid);
  timerid = setTimeout(function() { $('#FilterValidationRegistryForm').submit(); }, delay);
});

$('#FilterValidationRegistryForm :checkbox').click(function() {
  var form = this;
  clearTimeout(timerid);
  timerid = setTimeout(function() { $('#FilterValidationRegistryForm').submit(); }, delay);
});

$('select').change(function() {
  var form = this;
  clearTimeout(timerid);
  timerid = setTimeout(function() { $('#FilterValidationRegistryForm').submit(); }, delay);
});
</script>

