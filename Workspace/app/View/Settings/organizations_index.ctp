<?php echo $this->Html->link('Add New Organization',array('controller' => 'Settings', 'action' => 'organizationAdd')); ?>
<table>
    <thead>
        <tr>
            <th>
                <?php echo $this->Paginator->sort('name', 'Name'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('url', 'URL'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('is_default', 'Default'); ?>
            </th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($organizations as $organization): ?>
        <tr>
            <td>
                <?php echo $this->Html->link(h($organization['Organization']['name']), array('action' => 'Organizationview', $organization['Organization']['id']) ); ?>
            </td>
            <td>
                <?php echo h($organization['Organization']['url']); ?>
            </td>
            <td>
                <?php echo h($organization['Organization']['is_default'] ? "Yes" : "No"); ?>
            </td>
            <td class='actions'>
                <?php
                  echo $this->Html->link( 'Edit', array('action' => 'organizationEdit', $organization['Organization']['id']) );
                  echo $this->Form->postLink( 'Delete', array('action' => 'organizationDelete', $organization['Organization']['id']), array('confirm'=>'Are you sure you want to delete \''.$organization['Organization']['name'].'\'?') );
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
