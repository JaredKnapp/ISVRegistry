<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class NotifyShell extends AppShell {
    public function main() {
        $this->loadModel('Notification');

        $appName = Configure::read('App.Name');
        $appMailFrom = Configure::read('App.MailFrom');
        $appUrl = Configure::read('App.Url');


        $contain = array(
            'Owner',
            'Validation'=>array(
                'Product'=>array(
                    'Partner',
                    'Workload'=>array('Industry')
                ),
                'PlatformVersion'=>array('Platform')
            )
        );

        $notifications = $this->Notification->find('all', array('conditions'=>array('Notification.do_send_email'=>1, 'Notification.date_sent'=>null), 'contain'=>$contain));
        //$this->out('found ' . count($notifications) . ' notifications that have not been sent.');
        foreach($notifications as $notification){


            $to = $notification['Owner']['email'];
            if( !empty($notification['Notification']['alternate_email']) ){
                $to = $notification['Notification']['alternate_email'];
            }

            $message = array();
            $message[] = '<span style="font-family: Titillium, Arial, sans-serif;font-size: 14px;">The following validation matches your notification search criteria "' . $notification['Notification']['description'] . '":';
            $message[] = '';
            $message[] = '&nbsp;&nbsp;EMC Platform: ' . $notification['Validation']['PlatformVersion']['Platform']['name'] . ' ' . $notification['Validation']['PlatformVersion']['version'];
            $message[] = '&nbsp;&nbsp;Industry: ' . $notification['Validation']['Product']['Workload']['Industry']['name'] . '/' . $notification['Validation']['Product']['Workload']['name'];
            $message[] = '&nbsp;&nbsp;Partner: ' . $notification['Validation']['Product']['Partner']['name'];
            $message[] = '&nbsp;&nbsp;Product: ' . $notification['Validation']['Product']['name'];
            $message[] = '&nbsp;&nbsp;Version: ' . $notification['Validation']['version'];
            $message[] = '&nbsp;&nbsp;Protocol: ' . $notification['Validation']['protocol'];
            $message[] = '&nbsp;&nbsp;Level: ' . $notification['Validation']['level'];
            $message[] = '&nbsp;&nbsp;Status: ' . $notification['Validation']['status'];
            $message[] = '';
            $message[] = '<a href="' . $appUrl.'/id:' . $notification['Validation']['id'].'">Validation Link</a>';
            $message[] = '';
            $message[] = '</span>';

            $email = new CakeEmail();
            $email->emailFormat('html');
            $email->sender($appMailFrom, $appName);
            $email->from(array($appMailFrom=>$appName));
            $email->to($to);
            $email->subject($appName . ': Validation Notification');
            $email->send(implode("<br />", $message));

            $this->Notification->id = $notification['Notification']['id'];
            $this->Notification->save(array('date_sent'=>date('Y-m-d H:i:s')));
        }
    }
}

?>