<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {
	public $components = array(
		'Auth' => array('authorize' => 'Controller'),
		);
	public function isAuthorized($user = null) {
		// Any registered user can access public functions
		//if (empty($this->request->params['admin'])) {
		//	return true;
		//}

		// Only admins can access admin functions
		//if (isset($this->request->params['admin'])) {
		//	
		//}
		
		return (bool)($user['role'] === 'admin');
	}

    public function beforeFilter() {
        parent::beforeFilter();
		//$this->Auth->allow('');
	}

    public function index() {
		$this->set('title', 'System Users');
        $this->User->recursive = 0;
		$this->paginate = array('order' => array('lastname' => 'asc', 'firstname' => 'asc'));
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
		$this->set('title', 'User Details');
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('User', $this->User->findById($id));
    }

    public function add() {
		$this->set('title', 'Add New User');
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Flash->success(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Flash->error(
                __('The user could not be saved. Please, try again.')
            );
        }
    }

    public function edit($id = null) {
		$this->set('title', 'Edit User Details');

        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
			
			if(empty($this->request->data['User']['password'])){
				unset($this->request->data['User']['password']);
			}
			
            if ($this->User->save($this->request->data)) {
                $this->Flash->success(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Flash->error(
                __('The user could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $this->User->findById($id);
            unset($this->request->data['User']['password']);
        }
    }

	public function delete($id = null) {
		$this->request->allowMethod('post');

		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Flash->success(__('User deleted'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->Flash->error(__('User was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}
	
	public function myAccount(){
		$this->set('title', 'My Account Details');
	}
	
	public function notifications($id = null){
	}

}