<?php
class PartnersController extends AppController {
	
	public $helpers = array('Js');
	
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
		
		return (bool)($user['role'] === 'editor' || $user['role'] === 'admin');
	}

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function index() {
		$this->set('title', 'All Partners');
		$conditions = $this->Components->load('Filter')->filter($this);
		$this->paginate = array('order' => array('name' => 'asc'));
		$this->set('partners', $this->paginate($conditions));
	}

	public function add(){
		$this->set('title', 'Add New Partner');
		$this->loadModel('User');
		
		if ($this->request->is('post')) {
			$this->Partner->create();
			if ($this->Partner->save($this->request->data)) {
				$this->Flash->success(__('The partner has been saved'));
				return $this->redirect(array('action' => 'edit', $this->Partner->id));
			}
			$this->Flash->error(__('The partner could not be saved. Please, try again.'));
		}
		
		$sausers = $this->User->find('superlist', array('conditions'=>array('is_sa'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ', 'order'=>array('firstname ASC', 'lastname ASC')));
		$this->set('sausers', $sausers);

		$bausers = $this->User->find('superlist', array('conditions'=>array('is_ba'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ', 'order'=>array('firstname ASC', 'lastname ASC')));
		$this->set('bausers', $bausers);
	}
	
	public function edit($id = null) {
		$this->set('title', 'Edit Partner');
		$this->loadModel('Product');
		$this->loadModel('User');
		
		$this->Partner->id = $id;
		if (!$this->Partner->exists()) {
			throw new NotFoundException(__('Invalid partner'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Partner->save($this->request->data)) {
				$this->Flash->success(__('The Partner has been saved'));
				return $this->redirect(array('action' => 'index'));
			}
			$this->Flash->error(
				__('The partner could not be saved. Please, try again.')
				);
		} else {
			$sausers = $this->User->find('superlist', array('conditions'=>array('is_sa'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ', 'order'=>array('firstname ASC', 'lastname ASC')));
			$this->set('sausers', $sausers);

			$bausers = $this->User->find('superlist', array('conditions'=>array('is_ba'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ', 'order'=>array('firstname ASC', 'lastname ASC')));
			$this->set('bausers', $bausers);
			
			$result = $this->Partner->find('first', array(
				'conditions' => array('Partner.id'=>$id),
				'contain' => array('Products'=>array('Workload'=>array('Industry'=>array())))
				));
			
			$this->request->data = $result;
		}
	}
	
	public function delete($id = null) {
		$this->request->allowMethod('post');

		$this->Partner->id = $id;
		if (!$this->Partner->exists()) {
			throw new NotFoundException(__('Invalid partner'));
		}
		if ($this->Partner->delete()) {
			$this->Flash->success(__('Partner deleted'));
		} else {
			$this->Flash->error(__('Partner was not deleted'));
		}
		return $this->redirect($this->referer());
	}
	
	public function productAdd($parent_id = null){
		$this->set('title', 'Add Product');
		$this->loadModel('Partner');
		$this->loadModel('Product');
		$this->loadModel('User');
		$this->loadModel('Industry');

		$this->Partner->id = $parent_id;
		if (!$this->Partner->exists()) {
			throw new NotFoundException(__('Invalid Partner'));
		}

		if ($this->request->is('post')) {
			$this->request->data['Product']['partners_id'] = $parent_id;

			if ($this->Product->save($this->request->data)) {
				$this->Flash->success(__('The Product has been saved'));
			} else {
				$this->Flash->error(__('The Product could not be saved. Please, try again.'));
			}
			$this->redirect(array('action' => 'productEdit', $this->Product->id));
		}

		$industries = $this->Industry->find('list', array('fields' => array('id', 'name')));
		$this->set('industries', $industries);
		$this->set('workloads', array());

		$sausers = $this->User->find('superlist', array('conditions'=>array('is_sa'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ', 'order'=>array('firstname ASC', 'lastname ASC')));
		$this->set('sausers', $sausers);

		$bausers = $this->User->find('superlist', array('conditions'=>array('is_ba'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ', 'order'=>array('firstname ASC', 'lastname ASC')));
		$this->set('bausers', $bausers);
		
		$partner = $this->Partner->read();
		$this->set('partner', $partner);
	}
	
	public function productEdit($id=null){
		$this->set('title', 'Edit Product');
		$this->loadModel('Partner');
		$this->loadModel('Product');
		$this->loadModel('User');
		$this->loadModel('Industry');

		$this->Product->id = $id;
		if (!$this->Product->exists()) {
			throw new NotFoundException(__('Invalid Product'));
		}

		$contain = array(
			'Partner',
			'Workload'=>array('Industry'),
			'Validations'=>array(
					'PlatformVersion'=>array('Platform'),
					'SaOwner'
					)
				);
		
		$product = $this->Product->find('first', array('conditions'=>array('Product.id'=>$id), 'contain'=>$contain));
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Product->save($this->request->data)) {
				$this->Flash->success(__('The Product has been saved'));
				return $this->redirect(array('action' => 'edit', $product['Product']['partners_id']));
			}
			$this->Flash->error(__('The Platform Version could not be saved. Please, try again.'));
		} else {
			$this->request->data = $product;
			$this->request->data['Product']['industries_id'] = $product['Workload']['Industry']['id'];
		}
		
		$industries = $this->Industry->find('list', array('fields' => array('id', 'name')));
		$this->set('industries', $industries);
		$this->set('workloads', array($product['Workload']['id']=>$product['Workload']['name']));
		
		$sausers = $this->User->find('superlist', array('conditions'=>array('is_sa'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ', 'order'=>array('firstname ASC', 'lastname ASC')));
		$this->set('sausers', $sausers);

		$bausers = $this->User->find('superlist', array('conditions'=>array('is_ba'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ', 'order'=>array('firstname ASC', 'lastname ASC')));
		$this->set('bausers', $bausers);
	}
	
	public function productDelete($id = null){
		$this->loadModel('Product');
		
		$this->request->allowMethod('post');

		$this->Product->id = $id;
		if (!$this->Product->exists()) {
			throw new NotFoundException(__('Invalid Product'));
		}
		if ($this->Product->delete()) {
			$this->Flash->success(__('Product deleted'));
		} else {
			$this->Flash->error(__('Product was not deleted'));
		}
		return $this->redirect($this->referer());
	}
	
	public function validationAdd($parent_id = null){
		$this->set('title', 'Add Validation');
		$this->loadModel('Product');
		$this->loadModel('Validation');
		$this->loadModel('User');
		$this->loadModel('Platform');
		
		$this->Product->id = $parent_id;
		if (!$this->Product->exists()) {
			throw new NotFoundException(__('Invalid Product'));
		}

		if ($this->request->is('post')) {
			$this->request->data['Validation']['products_id'] = $parent_id;

			if ($this->Validation->save($this->request->data)) {
				$this->Flash->success(__('The Validation has been saved'));
			} else {
				$this->Flash->error(__('The Validation could not be saved. Please, try again.'));
			}
			$this->redirect(array('action' => 'productEdit', $parent_id));
		}
		
		$platforms = $this->Platform->find('list', array('fields'=>array('id', 'name')));
		$this->set('platforms', $platforms);
		$this->set('platformversions', array());

		$sausers = $this->User->find('superlist', array('conditions'=>array('is_sa'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ', 'order'=>array('firstname ASC', 'lastname ASC')));
		$this->set('sausers', $sausers);

		$bausers = $this->User->find('superlist', array('conditions'=>array('is_ba'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ', 'order'=>array('firstname ASC', 'lastname ASC')));
		$this->set('bausers', $bausers);
		
		$product = $this->Product->find('first', array('conditions'=>array('Product.id'=>$parent_id), 'contain'=>array('Partner')));
		$this->set('product', $product);
	}
	
	public function validationEdit($id = null){
		$this->set('title', 'Edit Validation');
		$this->loadModel('Partner');
		$this->loadModel('Product');
		$this->loadModel('Validation');
		$this->loadModel('User');
		$this->loadModel('Platform');

		$this->Validation->id = $id;
		if (!$this->Validation->exists()) {
			throw new NotFoundException(__('Invalid Validation'));
		}

		$validation = $this->Validation->findById($id);
		if ($this->request->is('post') || $this->request->is('put')) {
			
			$referer = $this->request->data['Validation']['referer'];
			unset($this->request->data['Validation']['referer']);
			
			if ($this->Validation->save($this->request->data)) {
				$this->Flash->success(__('The Validation has been saved'));
				//return $this->redirect(array('action' => 'productEdit', $validation['Validation']['products_id']));
				return $this->redirect($referer);
			}
			$this->Flash->error(__('The Validation could not be saved. Please, try again.'));
		} else {
			$this->request->data = $validation;
			$this->request->data['Validation']['platforms_id'] = $validation['PlatformVersion']['platforms_id'];
			$this->request->data['Validation']['referer'] = $this->referer();
		}
		
		$platforms = $this->Platform->find('list', array('fields'=>array('id', 'name')));
		$this->set('platforms', $platforms);
		$this->set('platformversions', array($validation['PlatformVersion']['id']=>$validation['PlatformVersion']['version']));
		
		$sausers = $this->User->find('superlist', array('conditions'=>array('is_sa'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ', 'order'=>array('firstname ASC', 'lastname ASC')));
		$this->set('sausers', $sausers);

		$bausers = $this->User->find('superlist', array('conditions'=>array('is_ba'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ', 'order'=>array('firstname ASC', 'lastname ASC')));
		$this->set('bausers', $bausers);
	}
	
	public function validationDelete($id = null){
		$this->loadModel('Validation');
		
		$this->request->allowMethod('post');

		$this->Validation->id = $id;
		if (!$this->Validation->exists()) {
			throw new NotFoundException(__('Invalid Validation'));
		}
		if ($this->Validation->delete()) {
			$this->Flash->success(__('Validation deleted'));
		} else {
			$this->Flash->error(__('Validation was not deleted'));
		}
		return $this->redirect($this->referer());
	}

}
