<?php
App::uses('AppController', 'Controller');

class AccountController extends AppController {
	public function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->allow('');
	}
	
	public function myAccount(){
		$this->set('title', 'My Account Details');
	}
	
	public function myNotifications(){
	}
	
	public function myNotificationTriggers(){
	}
	
	public function addNotificationTrigger(){
	}
	
	public function editNotificationTrigger($id = null){
	}
	
	public function deleteNotificationTrigger($id = null){
	}
}