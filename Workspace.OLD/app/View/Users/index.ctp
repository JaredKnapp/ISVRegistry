<?php echo $this->Html->link('Add New User',array('controller' => 'Users', 'action' => 'add')); ?>  
<table>
    <tr>
        <th><?php echo $this->Paginator->sort('firstname', 'First Name'); ?></th>
        <th><?php echo $this->Paginator->sort('lastname', 'Last Name'); ?></th>
		<th><?php echo $this->Paginator->sort('is_sa', 'Solution Architect'); ?></th>
		<th><?php echo $this->Paginator->sort('is_ba', 'Business Development'); ?></th>
        <th><?php echo $this->Paginator->sort('email', 'Email'); ?></th>
		<th>Actions</th>
    </tr>
       <?php foreach ($users as $user): ?>
	<tr>
        <td><?php echo h($user['User']['firstname']); ?> </td>
        <td><?php echo h($user['User']['lastname']); ?> </td>
		<td><?php echo h($user['User']['is_sa']==1?'Yes':''); ?></td>
		<td><?php echo h($user['User']['is_ba']==1?'Yes':''); ?></td>
        <td><?php echo h($user['User']['email']); ?> </td>
<td class='actions'>
		<?php 
		echo $this->Html->link( 'Edit', array('action' => 'edit', $user['User']['id']) );
		echo $this->Form->postLink( 'Delete', array('action' => 'delete', $user['User']['id']), array('confirm'=>'Are you sure you want to delete \''.$user['User']['email'].'\'?') );
		?>
</td>
</tr>
    <?php endforeach; ?>
</table>


<?php 
echo $this->Paginator->counter('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}');
?>
