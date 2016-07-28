<?php
class SettingsController extends AppController {

	public $components = array('Auth' => array('authorize' => 'Controller'));
    public $publicFunctions = array('getWorkloadsByIndustry', 'getVersionsByPlatform', 'roadmap');


	public function isAuthorized($user = null) {
        if(in_array($this->request->params['action'], $this->publicFunctions)){
            return true;
        }
		return (bool)($user['role'] === 'admin');
	}

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('about','roadmap');
	}

	public function about(){
		$this->set('title', 'PHP Info');
	}

	public function roadmap(){
		$this->set('title', 'ISV Registry Development Roadmap');
	}

	public function platformsIndex(){
		$this->loadModel('Platform');

		$this->paginate = array('order' => array('Platform.name' => 'asc'));

		$this->set('platforms', $this->paginate('Platform'));
		$this->set('title', 'Supported EMC Platforms');
	}

	public function platformAdd(){
		$this->set('title', 'Add New Platform');
		$this->loadModel('Platform');

		if ($this->request->is('post')) {
			$this->Platform->create();
			if ($this->Platform->save($this->request->data)) {
				$this->Flash->success(__('The platform has been saved'));
				return $this->redirect(array('action' => 'platformsIndex'));
			}
			$this->Flash->error(__('The platform could not be saved. Please, try again.'));
		} else {
            $this->set('organizations', $this->Platform->Organization->find('list'));
        }
	}

	public function platformEdit($id = null){
		$this->set('title', 'Edit Platform');
		$this->loadModel('Platform');

        $this->Platform->id = $id;
		if (!$this->Platform->exists()) {
			throw new NotFoundException(__('Invalid platform'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Platform->save($this->request->data)) {
				$this->Flash->success(__('The platform has been saved'));
				return $this->redirect(array('action' => 'platformsIndex'));
            }
            $this->Flash->error(
				__('The platform could not be saved. Please, try again.')
            );
        } else {
            $this->set('organizations', $this->Platform->Organization->find('list'));
			$this->request->data = $this->Platform->findById($id);
        }
	}

	public function platformDelete($id=null){
		$this->loadModel('Platform');

		$this->request->allowMethod('post');

		$this->Platform->id = $id;
		if (!$this->Platform->exists()) {
			throw new NotFoundException(__('Invalid platform'));
		}
		if ($this->Platform->delete()) {
			$this->Flash->success(__('Platform deleted'));
		} else {
			$this->Flash->error(__('Platform was not deleted'));
		}
		return $this->redirect(array('action' => 'platformsIndex'));
	}

	public function platformVersionAdd($parent_id=null){
		$this->set('title', 'Add Platform Version');
		$this->loadModel('Platform');
		$this->loadModel('PlatformVersion');

		$this->Platform->id = $parent_id;
		if (!$this->Platform->exists()) {
			throw new NotFoundException(__('Invalid platform'));
		}

		if ($this->request->is('post')) {
			$this->request->data['PlatformVersion']['platforms_id'] = $parent_id;

			if ($this->PlatformVersion->save($this->request->data)) {
				$this->Flash->success(__('The version has been saved'));
			} else {
				$this->Flash->error(__('The version could not be saved. Please, try again.'));
			}
			$this->redirect(array('action' => 'platformEdit', $parent_id));
		}

		$platform = $this->Platform->read();
		$this->set('platform', $platform);
	}

	public function platformVersionDelete($id=null){
		$this->loadModel('PlatformVersion');

		$this->request->allowMethod('post');

		$this->PlatformVersion->id = $id;
		if (!$this->PlatformVersion->exists()) {
			throw new NotFoundException(__('Invalid platform'));
		}

		$platformVersion = $this->PlatformVersion->read();
		$parent_id = $platformVersion['PlatformVersion']['platforms_id'];

		if ($this->PlatformVersion->delete()) {
			$this->Flash->success(__('Platform Version deleted<br>'));
		} else {
			$this->Flash->error(__('Platform Version was not deleted'));
		}
		return $this->redirect(array('action' => 'platformEdit', $parent_id));
	}

	public function platformVersionEdit($id=null){
		$this->set('title', 'Edit Platform Version');
		$this->loadModel('Platform');
		$this->loadModel('PlatformVersion');

		$this->PlatformVersion->id = $id;
		if (!$this->PlatformVersion->exists()) {
			throw new NotFoundException(__('Invalid Platform Version'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->PlatformVersion->save($this->request->data)) {
				$this->Flash->success(__('The Platform Version has been saved'));

				$version = $this->PlatformVersion->read();
				//$this->Platform->id = $version['Platform']['platforms_id'];
				//$this->set('platform', $this->Platform->read());
				//var_dump($version);
				return $this->redirect(array('action' => 'platformEdit', $version['PlatformVersion']['platforms_id']));// $this->Platform->id));
			}
			$this->Flash->error(__('The Platform Version could not be saved. Please, try again.'));
		} else {
			$version = $this->PlatformVersion->findById($id);
			$this->request->data = $version;
		}
	}

	public function industriesIndex(){
		$this->loadModel('Industry');

		$this->paginate = array('order' => array('Industry.name' => 'asc', 'recursive' => -1));

		$this->set('industries', $this->paginate('Industry'));
		$this->set('title', 'Industries');
	}

	public function industryAdd(){
		$this->set('title', 'Add New Industry');
		$this->loadModel('Industry');

		if ($this->request->is('post')) {
			$this->Industry->create();
			if ($this->Industry->save($this->request->data)) {
				$this->Flash->success(__('The industry has been saved'));
				return $this->redirect(array('action' => 'industriesIndex'));
			}
			$this->Flash->error(__('The industry could not be saved. Please, try again.'));
		}
	}

	public function industryEdit($id = null){
		$this->set('title', 'Edit Industry');
		$this->loadModel('Industry');

		$this->Industry->id = $id;
		if (!$this->Industry->exists()) {
			throw new NotFoundException(__('Invalid Industry'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Industry->save($this->request->data)) {
				$this->Flash->success(__('The industry has been saved'));
				return $this->redirect(array('action' => 'industriesIndex'));
			}
			$this->Flash->error(
				__('The industry could not be saved. Please, try again.')
				);
		} else {
			$this->request->data = $this->Industry->findById($id);
		}
	}

	public function industryDelete($id=null){
		$this->loadModel('Industry');

		$this->request->allowMethod('post');

		$this->Industry->id = $id;
		if (!$this->Industry->exists()) {
			throw new NotFoundException(__('Invalid Industry'));
		}
		if ($this->Industry->delete()) {
			$this->Flash->success(__('Industry deleted'));
		} else {
			$this->Flash->error(__('Industry was not deleted'));
		}
		return $this->redirect(array('action' => 'industriesIndex'));
	}

    public function organizationsIndex(){
        $this->loadModel('Organization');

		$this->paginate = array('order' => array('Organization.name' => 'asc'));

		$this->set('organizations', $this->paginate('Organization'));
		$this->set('title', 'Organizations');
    }

    public function organizationAdd(){
		$this->set('title', 'Add New Organization');
		$this->loadModel('Organization');

		if ($this->request->is('post')) {
			$this->Organization->create();
			if ($this->Organization->save($this->request->data)) {
				$this->Flash->success(__('The organization has been saved'));
				return $this->redirect(array('action' => 'organizationsIndex'));
			}
			$this->Flash->error(__('The organization could not be saved. Please, try again.'));
		}
	}

	public function organizationEdit($id = null){
		$this->set('title', 'Edit Organization');
		$this->loadModel('Organization');

		$this->Organization->id = $id;
		if (!$this->Organization->exists()) {
			throw new NotFoundException(__('Invalid Organization'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Organization->save($this->request->data)) {
				$this->Flash->success(__('The organization has been saved'));
				return $this->redirect(array('action' => 'organizationsIndex'));
			}
			$this->Flash->error(
				__('The organization could not be saved. Please, try again.')
				);
		} else {
			$this->request->data = $this->Organization->findById($id);
		}
	}

    public function organizationDelete($id=null){
		$this->loadModel('Organization');

		$this->request->allowMethod('post');

		$this->Organization->id = $id;
		if (!$this->Organization->exists()) {
			throw new NotFoundException(__('Invalid Organization'));
		}
		if ($this->Organization->delete()) {
			$this->Flash->success(__('Organization deleted'));
		} else {
			$this->Flash->error(__('Organization was not deleted'));
		}
		return $this->redirect(array('action' => 'organizationsIndex'));
	}

	public function workloadAdd($parent_id=null){
		$this->set('title', 'Add Workload');
		$this->loadModel('Industry');
		$this->loadModel('Workload');

		$this->Industry->id = $parent_id;
		if (!$this->Industry->exists()) {
			throw new NotFoundException(__('Invalid Industry'));
		}

		if ($this->request->is('post')) {
			$this->request->data['Workload']['industries_id'] = $parent_id;

			if ($this->Workload->save($this->request->data)) {
				$this->Flash->success(__('The Workload has been saved'));
			} else {
				$this->Flash->error(__('The Workload could not be saved. Please, try again.'));
			}
			$this->redirect(array('action' => 'industryEdit', $parent_id));
		}

		$industry = $this->Industry->read();
		$this->set('industry', $industry);
	}

	public function workloadDelete($id=null){
		$this->loadModel('Workload');

		$this->request->allowMethod('post');

		$this->Workload->id = $id;
		if (!$this->Workload->exists()) {
			throw new NotFoundException(__('Invalid Workload'));
		}

		$workload = $this->Workload->read();
		$parent_id = $workload['Workload']['industries_id'];

		if ($this->Workload->delete()) {
			$this->Flash->success(__('Workload deleted<br>'));
		} else {
			$this->Flash->error(__('Workload was not deleted'));
		}
		return $this->redirect(array('action' => 'industryEdit', $parent_id));
	}

	public function workloadEdit($id=null){
		$this->set('title', 'Edit Workload');
		$this->loadModel('Industry');
		$this->loadModel('Workload');

		$this->Workload->id = $id;
		if (!$this->Workload->exists()) {
			throw new NotFoundException(__('Invalid Workload'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Workload->save($this->request->data)) {
				$this->Flash->success(__('The workload has been saved'));

				$workload = $this->Workload->read();
				$this->Industry->id = $workload['Workload']['industries_id'];
				$this->set('industry', $this->Industry->read());

				return $this->redirect(array('action' => 'industryEdit', $this->Industry->id));
			}
			$this->Flash->error(
				__('The Workload could not be saved. Please, try again.')
				);
		} else {
			$workload = $this->Workload->findById($id);
			$this->request->data = $workload;
		}
	}

	public function getWorkloadsByIndustry($id = null){
		$this->loadModel('Industry');
		$this->loadModel('Workload');

		$workloads = $this->Workload->find('list', array('conditions'=>array('Workload.industries_id'=>$id), 'fields'=>'name'));
		$this->set(compact('workloads'));

		$this->layout = 'ajax';
	}

	public function getVersionsByPlatform($id = null){
		$this->loadModel('Platform');
		$this->loadModel('PlatformVersion');

		$platformVersions = $this->PlatformVersion->find('list', array('conditions'=>array('PlatformVersion.platforms_id'=>$id), 'fields'=>'version'));
		$this->set(compact('platformVersions'));

		$this->layout = 'ajax';
	}

}