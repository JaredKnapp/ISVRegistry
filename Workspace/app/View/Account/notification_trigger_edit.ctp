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
<div class="triggerEdit form">

    <?php echo $this->Form->create('NotificationTrigger'); ?>

    <fieldset>

        <legend>
            <?php echo __('Edit Notification Trigger'); ?>
        </legend>

        <?php echo $this->Form->hidden('owner_id'); ?>
        <?php echo $this->Form->input('description'); ?>
        <?php echo $this->Form->input('partners_id'); ?>
        <?php echo $this->Form->input('platforms_id'); ?>
        <?php echo $this->Form->input('industries_id'); ?>
        <?php echo $this->Form->input('protocol'); ?>
        <?php echo $this->Form->input('is_certified', array('label'=>'Certified Validations Only', 'type'=>'checkbox', 'options'=>array('1'))); ?>
        <?php echo $this->Form->input('do_send_email', array('label'=>'Send an Email Notification?', 'type'=>'checkbox', 'options'=>array('1'))); ?>
        <?php echo $this->Form->input('alternate_email', array('label'=>'Alternate Notification Email Address', 'placeholder' => AuthComponent::user('email'), 'div'=>array('id'=>'AlternateEmailDiv'))); ?>

        <table>
            <tr>
                <td>
                    <?php
                    $levels= Configure::read('Validation.levels');
                    $options = array();
                    foreach($levels as $key=>$value){
                        $options[$key] = $value['label'];
                    }
                    echo $this->Form->input('level_choices', array('type'=>'select', 'escape'=>false, 'label'=>array('text'=>'<strong>Validation Levels</strong>', 'escape'=>false), 'div'=>false, 'multiple'=>'checkbox', 'options'=>$options));
                    ?>
                </td>
                <td>
                    <?php
                    $statuses= Configure::read('Validation.statusOptions');
                    $options = array();
                    foreach($statuses as $key=>$value){
                        $options[$key] = '<div class="statusovalsmall" style="background-color:'.$statuses[$key]['color']['back'].'; color:'.$statuses[$key]['color']['fore'].'; border-color:'.$statuses[$key]['color']['border'].';">&nbsp;</div>&nbsp'.$value['label'];
                    }
                    echo $this->Form->input('status_choices', array('type'=>'select', 'escape'=>false, 'label'=>array('text'=>'<strong>Available Status Options</strong>', 'escape'=>false, 'div'=>false), 'div'=>false, 'multiple'=>'checkbox', 'options'=>$options));
                    ?>
                </td>
            </tr>
        </table>

    </fieldset>

    <?php echo $this->Form->end(__('Submit')); ?>

</div>

<script type="text/javascript">
    $(document).ready(function () {

        $('#NotificationTriggerDoSendEmail').change(function () {
            if (this.checked) {
                $("#AlternateEmailDiv").show(100);
            } else {
                $("#AlternateEmailDiv").hide(100);
            }
        });

        if( !$('#NotificationTriggerDoSendEmail').is(':checked') ){
            $("#AlternateEmailDiv").hide(0);
        }

    });
</script>
