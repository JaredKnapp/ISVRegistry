<?php 
$this->Html->addCrumb('Partner List', array('controller' => 'partners', 'action' => 'index')); 
$this->Html->addCrumb($this->request->data['Partner']['name']); 
?>
<div class="partners form">
	<?php echo $this->Form->create('Partner'); ?>
		<fieldset>
			<legend><?php echo __('Edit Partner'); ?></legend>
			<?php
			echo $this->Form->input('name');
			echo $this->Form->input('url');
			echo $this->Form->input('sa_owner_id', array('label'=>'Solution Architect', 'type' => 'select', 'options' => $sausers, 'empty' => '(choose one...)'));
			echo $this->Form->input('ba_owner_id', array('label'=>'Business Analyst', 'type' => 'select', 'options' => $bausers, 'empty' => '(choose one...)'));
			?>
		</fieldset>
	<?php echo $this->Form->end(__('Submit')); ?>
		<h1>Available Products</h1>
		<?php echo $this->Html->link('Add New Product',array('action' => 'productAdd', $this->request->data['Partner']['id'])); ?>  
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Url</th>
					<th>Industry</th>
					<th>Workload</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->request->data['Products'] as $product): ?>
	<tr>
	<td>
						<?php echo h($product['name']); ?>
</td>
<td>
						<?php echo h($product['url']); ?>
</td>
<td>
						<?php echo h($product['Workload']['Industry']['name']); ?>
</td>
<td>
						<?php echo h($product['Workload']['name']);
						?>
</td>
<td class='actions'>
						<?php
						echo $this->Html->link( 'Edit', array('action' => 'productEdit', $product['id']) );
						echo $this->Form->postLink( 'Delete', array('action' => 'productDelete', $product['id']), array('confirm'=>'Are you sure you want to delete product \''.$product['name'].'\'?') );
						?>
</td>
</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
</div>