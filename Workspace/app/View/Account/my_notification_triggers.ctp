<div class="actions">
    <ul>
        <li>
            <?php echo $this->Html->link('Account Info', array('controller' => 'Account', 'action'=>'myAccount')); ?>
        </li>
        <li>
            <?php echo $this->Html->link('Notifications', array('controller' => 'Account', 'action'=>'myNotifications')); ?>
        </li>
        <li>
            <?php echo $this->Html->link('Log Out',array('controller' => 'Auth', 'action' => 'logout')); ?>
        </li>
    </ul>
</div>


<div class="myaccount form">

    <table class="navLinks" width="200px">
        <tr>
            <td>
                <?php echo $this->Html->link('My Notifications', array('controller'=>'Account', 'action'=>'myNotifications')); ?>
            </td>
            <td>
                <?php echo $this->Html->link('Edit Notification Settings', array('controller'=>'Account', 'action'=>'myNotificationTriggers')); ?>
            </td>
        </tr>
    </table>

    <?php echo $this->Html->link('Add Notification', array('controller'=>'Account', 'action'=>'notificationTriggerAdd')); ?>
    <br />
    <br />
    <table>
        <tr>
            <th>
                <?php echo $this->Paginator->sort('partner', 'Partner'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('platform', 'Platform'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('industry', 'Industry'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('is_certified', 'Certified Only?'); ?>
            </th>
            <th>Actions</th>
        </tr>
        <?php foreach ($triggers as $trigger): ?>
        <tr>
            <td>
                <?php echo h($trigger['NotificationTrigger']['partners_id']); ?>
            </td>
            <td>
                <?php echo h($trigger['NotificationTrigger']['platforms_id']); ?>
            </td>
            <td>
                <?php echo h($trigger['NotificationTrigger']['industries_id']); ?>
            </td>
            <td>
                <?php echo h($trigger['NotificationTrigger']['is_certified']); ?>
            </td>
            <td class='actions'>
                <?php
		echo $this->Html->link( 'Edit', array('action' => 'notificationTriggerEdit', $trigger['NotificationTrigger']['id']) );
//		echo $this->Form->postLink( 'Delete', array('action' => 'delete', $user['User']['id']), array('confirm'=>'Are you sure you want to delete \''.$user['User']['email'].'\'?') );
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php echo var_dump($triggers); ?>
</div>

