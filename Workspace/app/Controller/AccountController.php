<?php
App::uses('AppController', 'Controller');

class AccountController extends AppController {
	public function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->allow('');
	}

	public function myAccount(){
		$this->set('title', 'My Account Details');

        $this->loadModel('User');

        $this->User->id = AuthComponent::user('id');
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {

			if(empty($this->request->data['User']['password'])){
				unset($this->request->data['User']['password']);
			}

            if ($this->User->save($this->request->data)) {
                $this->Flash->success(__('The user has been saved'));
                return $this->redirect($this->referer());
            }
            $this->Flash->error(
                __('The user could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $this->User->findById(AuthComponent::user('id'));
            unset($this->request->data['User']['password']);
        }
	}

	public function myNotifications(){
        $this->set('title', 'My Watched Validations');
        $this->loadModel('Notification');

        if ($this->request->is('post')) {
            if(array_key_exists('Notification', $this->request->data)){
                foreach($this->request->data['Notification'] as $field=>$id){
                    $this->setAcknowledgeFlag($id);
                }
            }
        }

        $contain = array(
            'Validation'=>array(
                'Product'=>array(
                    'Partner',
                    'Workload'=>array('Industry')
                ),
                'PlatformVersion'=>array('Platform')
            )
        );

        $notifications = $this->Notification->find('all', array('conditions'=>array('owner_id'=>AuthComponent::user('id'), 'date_acknowledged'=>null), 'contain'=>$contain));

        if(array_key_exists('ext', $this->request->params)){
			$this->response->download("notifications.csv");
			$this->layout = 'ajax';
            $this->set(compact('notifications'));
			$this->render('csv/my_notifications');
		} else {
            $this->set(compact('notifications'));
        }
	}

    public function acknowledgeNotification($id = null){
        $this->request->allowMethod('post');

        $this->setAcknowledgeFlag($id);

        return $this->redirect($this->referer());
    }

    private function setAcknowledgeFlag($id = null){
        $this->loadModel('Notification');
        $this->Notification->id = $id;
        $this->Notification->save(array('date_acknowledged'=>date('Y-m-d H:i:s')));
    }

	public function myNotificationTriggers(){
        $this->set('title', 'My Validation Notification Triggers');
        $this->loadModel('NotificationTrigger');

        $triggers = $this->NotificationTrigger->find('all', array('conditions'=>array('owner_id'=>AuthComponent::user('id')), 'contain'=>array()));
        $this->set(compact('triggers'));
	}

	public function notificationTriggerAdd(){
        $this->set('title', 'Add new Notification Trigger');

        $this->loadModel('NotificationTrigger');
        $this->loadModel('Partner');
        $this->loadModel('Platform');
        $this->loadModel('Industry');

        if ($this->request->is('post')) {
            $this->NotificationTrigger->create();

            $this->request->data['NotificationTrigger']['level'] = '!'.implode('!', empty($this->request->data['NotificationTrigger']['level_choices'])?array():$this->request->data['NotificationTrigger']['level_choices']).'!';
            $this->request->data['NotificationTrigger']['status'] = '!'.implode('!', empty($this->request->data['NotificationTrigger']['status_choices'])?array():$this->request->data['NotificationTrigger']['status_choices']).'!';

            if ($this->NotificationTrigger->save($this->request->data)) {
                $this->Flash->success(__('The Notification Trigger has been saved'));
                return $this->redirect(array('action' => 'myNotificationTriggers'));
            }
            $this->Flash->error(
                __('The Notification Trigger could not be saved. Please, try again.')
            );
        }

        $partners = $this->Partner->find('list', array('order'=>'name', 'contain'=>array()));
        $partners = array(''=>'--Any Partner--') + $partners;

        $platforms = $this->Platform->find('list', array('order'=>'name', 'contain'=>array()));
        $platforms = array(''=>'--Any Platform--') + $platforms;

        $industries = $this->Industry->find('list', array('order'=>'name', 'contain'=>array()));
        $industries = array(''=>'--Any Industry--') + $industries;

        $this->set(compact('partners', 'platforms', 'industries'));

        $id = AuthComponent::user('id');
        $this->request->data['NotificationTrigger']['owner_id'] = AuthComponent::user('id');
        $this->request->data['NotificationTrigger']['level_choices'] = array_keys(Configure::read('Validation.levels'));
        $this->request->data['NotificationTrigger']['status_choices'] = array('complete');

	}

	public function notificationTriggerEdit($id = null){
        $this->set('title', 'Edit Notification Trigger');

        $this->loadModel('NotificationTrigger');
        $this->loadModel('Partner');
        $this->loadModel('Platform');
        $this->loadModel('Industry');

        $this->NotificationTrigger->id = $id;
        if (!$this->NotificationTrigger->exists()) {
            throw new NotFoundException(__('Invalid Notification Trigger'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {

            $this->request->data['NotificationTrigger']['level'] = '!'.implode('!', empty($this->request->data['NotificationTrigger']['level_choices'])?array():$this->request->data['NotificationTrigger']['level_choices']).'!';
            $this->request->data['NotificationTrigger']['status'] = '!'.implode('!', empty($this->request->data['NotificationTrigger']['status_choices'])?array():$this->request->data['NotificationTrigger']['status_choices']).'!';

            if ($this->NotificationTrigger->save($this->request->data)) {
                $this->Flash->success(__('The Notification Trigger has been saved'));
                return $this->redirect(array('action' => 'myNotificationTriggers'));
            }

            $this->Flash->error(__('The Notification Trigger could not be saved. Please, try again.'));

        }



        $partners = $this->Partner->find('list', array('order'=>'name', 'contain'=>array()));
        $partners = array(''=>'--Any Partner--') + $partners;

        $platforms = $this->Platform->find('list', array('order'=>'name', 'contain'=>array()));
        $platforms = array(''=>'--Any Platform--') + $platforms;

        $industries = $this->Industry->find('list', array('order'=>'name', 'contain'=>array()));
        $industries = array(''=>'--Any Industry--') + $industries;

        $this->set(compact('partners', 'platforms', 'industries'));

        $this->request->data = $this->NotificationTrigger->findById($id);
        $this->request->data['NotificationTrigger']['level_choices'] = explode('!', $this->request->data['NotificationTrigger']['level']);
        $this->request->data['NotificationTrigger']['status_choices'] = explode('!', $this->request->data['NotificationTrigger']['status']);
	}

	public function deleteNotificationTrigger($id = null){
        $this->loadModel('NotificationTrigger');

        $this->request->allowMethod('post');

		$this->NotificationTrigger->id = $id;
		if (!$this->NotificationTrigger->exists()) {
			throw new NotFoundException(__('Invalid Notification Trigger'));
		}
		if ($this->NotificationTrigger->delete()) {
			$this->Flash->success(__('Notification Trigger deleted'));
		} else {
			$this->Flash->error(__('Notification Trigger was not deleted'));
		}
		return $this->redirect($this->referer());
	}
}