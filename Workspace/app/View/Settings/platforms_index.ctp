<?php echo $this->Html->link('Add New Platform',array('controller' => 'Settings', 'action' => 'platformAdd')); ?>  
<table> 
	<thead>
		<tr>
            <th>
                <?php echo $this->Paginator->sort('name', 'Name'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('organization', 'Organization'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('sortorder', 'Sort Order'); ?>
			</th>
	        <th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($platforms as $platform): ?>
		<tr>
            <td>
                <?php echo h($platform['Platform']['name']); ?>
            </td>
            <td>
                <?php echo h($platform['Organization']['name']); ?>
            </td>
            <td>
                <?php echo h($platform['Platform']['sortorder']); ?>
			</td>
			<td class='actions'>
				<?php
					echo $this->Html->link( 'Edit', array('action' => 'platformEdit', $platform['Platform']['id']) );
					echo $this->Form->postLink( 'Delete', array('action' => 'platformDelete', $platform['Platform']['id']), array('confirm'=>'Are you sure you want to delete \''.$platform['Platform']['name'].'\'?') );
				?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>