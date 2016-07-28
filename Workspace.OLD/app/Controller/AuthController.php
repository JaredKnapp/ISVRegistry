<?php
App::uses('AppController', 'Controller');

class AuthController extends AppController {
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login', 'logout');
    }

	public function login() {
		$this->set('title', 'Log In');
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				return $this->redirect($this->Auth->redirectUrl());
			}
			$this->Flash->error(__('Invalid username or password, try again'));
		}
	}

	public function logout() {
		$this->set('title', 'Log Out');
		return $this->redirect($this->Auth->logout());
	}

}