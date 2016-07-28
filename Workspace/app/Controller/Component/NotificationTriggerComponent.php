<?php

class NotificationTriggerComponent extends Component {
    public function buildNotifications($validation, $doSave = true){
        $triggerModel = ClassRegistry::init('NotificationTrigger');
        $notificationModel = ClassRegistry::init('Notification');

        $conditions = array();
        $conditions[] = array('OR'=>array('partners_id'=>null, 'partners_id ='=>$validation['Product']['partners_id']));
        $conditions[] = array('OR'=>array('industries_id'=>null, 'industries_id ='=>$validation['Product']['Workload']['industries_id']));
        $conditions[] = array('OR'=>array('platforms_id'=>null, 'platforms_id ='=>$validation['PlatformVersion']['platforms_id']));
        $conditions[] = array('OR'=>array('is_certified'=>false, 'is_certified ='=>$validation['Validation']['iscertified']));
        $conditions[] = array('OR'=>array('protocol'=>null, 'protocol'=>'', 'protocol LIKE'=>'%'. $validation['Validation']['protocol'] .'%'));
        $conditions[] = array('level LIKE'=>'%!'. $validation['Validation']['level'] .'!%');
        $conditions[] = array('status LIKE'=>'%!'. $validation['Validation']['status'] .'!%');

        $triggers = $triggerModel->find('all', array('conditions'=>array('AND'=>$conditions), 'contain'=>array()));
        $notificationCount = count($triggers);
        if($notificationCount>0){

            $newNotifications = array();
            $notifiedOwners = array();

            foreach($triggers as $trigger){

                //Make sure there is not a notification waiting to be sent. If not, then create one.
                $notification = $notificationModel->find('first', array('conditions'=>array('owner_id'=>$trigger['NotificationTrigger']['owner_id'], 'validations_id'=>$validation['Validation']['id'], 'date_sent'=>null)));
                if(!$notification){
                    if( !in_array($trigger['NotificationTrigger']['owner_id'], $notifiedOwners) ){
                        $notification = array();
                        $notification['Notification']['notification_triggers_id'] = $trigger['NotificationTrigger']['id'];
                        $notification['Notification']['owner_id'] = $trigger['NotificationTrigger']['owner_id'];
                        $notification['Notification']['description'] = $trigger['NotificationTrigger']['description'];
                        $notification['Notification']['partners_id'] = $trigger['NotificationTrigger']['partners_id'];
                        $notification['Notification']['platforms_id'] = $trigger['NotificationTrigger']['platforms_id'];
                        $notification['Notification']['industries_id'] = $trigger['NotificationTrigger']['industries_id'];
                        $notification['Notification']['protocol'] = $trigger['NotificationTrigger']['protocol'];
                        $notification['Notification']['is_certified'] = $trigger['NotificationTrigger']['is_certified'];
                        $notification['Notification']['do_send_email'] = $trigger['NotificationTrigger']['do_send_email'];
                        $notification['Notification']['alternate_email'] = $trigger['NotificationTrigger']['alternate_email'];
                        $notification['Notification']['level'] = $trigger['NotificationTrigger']['level'];
                        $notification['Notification']['status'] = $trigger['NotificationTrigger']['status'];
                        $notification['Notification']['validations_id'] = $validation['Validation']['id'];

                        $newNotifications[] = $notification;

                        $notifiedOwners[] = $trigger['NotificationTrigger']['owner_id'];
                    }
                }
            }
            if(count($newNotifications)>0){
                $notificationModel->saveAll($newNotifications);
            }
        }
        return $notificationCount;
    }
}