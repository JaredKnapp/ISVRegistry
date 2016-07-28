<div class="platforms form">
	<?php echo $this->Form->create('Platform'); ?>
		<fieldset>
			<legend><?php echo __('Edit Platform'); ?></legend>
			<?php
			echo $this->Form->input('name');
			echo $this->Form->input('sortorder');
            echo $this->Form->input('organizations_id', array('label'=>'Organization'));
			?>
		</fieldset>
	<?php echo $this->Form->end(__('Submit')); ?>
	<h1>Available Versions</h1>
	<?php echo $this->Html->link('Add New Version',array('action' => 'platformVersionAdd', $this->request->data['Platform']['id'])); ?>  
	<table>
		<thead>
			<tr>
				<th>Version</th>
				<th>Show?</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->request->data['PlatformVersions'] as $platformVersion): ?>
	<tr>
	<td>
					<?php echo h($platformVersion['version']); ?>
</td>
				<td> <?php echo h($platformVersion['visibledefault']=='1'?'Yes':'No'); ?> </td>
				<td class='actions'>
					<?php
					echo $this->Html->link( 'Edit', array('action' => 'platformVersionEdit', $platformVersion['id']) );
					echo $this->Form->postLink( 'Delete', array('action' => 'platformVersionDelete', $platformVersion['id']), array('confirm'=>'Are you sure you want to delete version \''.$platformVersion['version'].'\'?') );
					?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
