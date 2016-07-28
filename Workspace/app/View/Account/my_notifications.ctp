<div id="exportlinks">
    <span>
        <!--Export to:-->
    </span>

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

<?php $statuses= Configure::read('Validation.statusOptions'); ?>

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
    <?php echo $this->Html->link('My Notifications', array('controller'=>'Account', 'action'=>'myNotifications'), array('class'=>'subnavlinkactive')); ?> &nbsp;
    <?php echo $this->Html->link('Edit Notification Settings', array('controller'=>'Account', 'action'=>'myNotificationTriggers'), array('class'=>'subnavlink')); ?>
    <?php echo $this->Form->create('Notification'); ?>
    <fieldset>
        <table>
            <tr>
                <th valign="bottom" align="center"><input name="selectall" id="selectall" type="checkbox" /></th>
                <th>
                    <?php echo $this->Paginator->sort('created', 'Date'); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('partner', 'Partner'); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('product', 'Product'); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('version', 'Version'); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('version', 'Protocol'); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('platform', 'Platform'); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('industry', 'Industry'); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('level', 'Level'); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('status', 'Status'); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('date_sent', 'Date Sent'); ?>
                </th>
                <th>Actions</th>
            </tr>
            <?php foreach ($notifications as $notification): ?>
            <tr>
                <td style="width: 20px;">
                    <?php echo $this->Form->input('notification_'.$notification['Notification']['id'], array('label'=>false, 'div'=>array('class'=>'notifications'), 'hiddenField'=>false, 'type'=>'checkbox', 'value'=>$notification['Notification']['id'])); ?>
                </td>
                <td>
                    <?php echo h($this->Time->format('M d, Y', $notification['Notification']['created'])); ?>
                </td>
                <td>
                    <?php echo h($notification['Validation']['Product']['Partner']['name']);?>
                </td>
                <td>
                    <?php echo h($notification['Validation']['Product']['name']); ?>
                </td>
                <td>
                    <?php echo h($notification['Validation']['version']);?>
                </td>
                <td>
                    <?php echo h($notification['Validation']['protocol']);?>
                </td>
                <td>
                    <?php echo h($notification['Validation']['PlatformVersion']['Platform']['name'] . ' ' . $notification['Validation']['PlatformVersion']['version']);?>
                </td>
                <td>
                    <?php echo h($notification['Validation']['Product']['Workload']['Industry']['name']);?>
                </td>
                <td>
                    <?php echo h($notification['Validation']['level']);?>
                </td>
                <td>
                    <?php echo h($statuses[$notification['Validation']['status']]['label']); ?>
                </td>
                <td>
                    <?php echo h($this->Time->format('M d, Y h:i A', $notification['Notification']['date_sent'])); ?>
                </td>
                <td class='actions'>
                    <?php
                      //echo $this->Form->postLink( 'Acknowledge', array('action' => 'acknowledgeNotification', $notification['Notification']['id']), array('confirm'=>'Are you sure you want to acknowledge this Notification?') );
                      echo $this->Html->link( 'View Rule', array('action' => 'notificationTriggerEdit', $notification['Notification']['notification_triggers_id']) );
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </fieldset>
    <?php echo $this->Form->end(__('Acknowledge Selected')); ?>
</div>
<script>
    $('#selectall').click(function () {
        var checked = $(this).is(':checked');
        $('.notifications input[type=checkbox]').each(function () {
            $(this).prop('checked', checked);
        });
    });
</script>