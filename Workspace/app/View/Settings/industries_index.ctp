<?php echo $this->Html->link('Add New Industry',array('controller' => 'Settings', 'action' => 'industryAdd')); ?>
<table>
    <thead>
        <tr>
            <th>
                <?php echo $this->Paginator->sort('name', 'Name'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('url', 'Url'); ?>
            </th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($industries as $industry): ?>
        <tr>
            <td>
                <?php echo h($industry['Industry']['name']); ?>
            </td>
            <td>
                <?php echo h($industry['Industry']['url']); ?>
            </td>
            <td class='actions'>
                <?php
                  echo $this->Html->link( 'Edit', array('action' => 'industryEdit', $industry['Industry']['id']) );
                  echo $this->Form->postLink( 'Delete', array('action' => 'industryDelete', $industry['Industry']['id']), array('confirm'=>'Are you sure you want to delete \''.$industry['Industry']['name'].'\'?') );
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
