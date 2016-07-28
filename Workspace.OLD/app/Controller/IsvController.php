<?php
class IsvController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index','validationRegistry','validationSchedule', 'validationRegistryOld');
	}

	public function index(){
		
		return $this->redirect(array('action' => 'validationRegistry'));
		
		$this->set('title', 'OLD!! ISV Partner Validation Registry');
		$this->loadModel('Platform');
		$this->loadModel('ValidationSummary');
		
		//Load filter settings from request
		$conditions = $this->Components->load('Filter')->filter($this);
		
		//If no status is set, default to 'complete' status
		if(!isset($conditions['versions.status'])){
			$conditions['versions.status'] = array('complete');
			if(!isset($this->request->data['Filter'])){
				$this->request->data['Filter'] = array();
			}
			$this->request->data['Filter']['versions-status'] = array('complete');
		}
		
		//Generate Report
		$this->set('platforms', $this->Platform->view());
		if(array_key_exists('ext', $this->request->params)){
			$this->response->download("isv_registry.csv");
			$this->layout = 'ajax';
			$this->set('validations', $this->ValidationSummary->find('filtered', array('conditions'=>$conditions)));
			$this->render('csv/index');
		} else {
			//No extension - use standard view
			$this->set('validations', $this->paginate('ValidationSummary', $conditions));
		}
		
	}
	
	public function validationRegistry(){
		$this->set('title', 'ISV Partner Validation Registry');
		$this->loadModel('Platform');
		$this->loadModel('PlatformVersion');
		$this->loadModel('ValidationRegistry');
		
		//Load filter settings from request
		$conditions = $this->Components->load('Filter')->filter($this);
		
		//If none of the Validation LEVELs are selected, default to ALL.
		if(!isset($conditions['level'])){
			$levels= Configure::read('Validation.levels');
			$conditions['level'] = array_keys($levels);
			array_push($conditions['level'], 'empty');
			$this->request->data['Filter']['level'] = $conditions['level'];
		}
		
		//If none of the status options are selected, default to 'complete' status.
		if(!isset($conditions['versions.status'])){
			$conditions['versions.status'] = array('complete');
			$this->request->data['Filter']['versions-status'] = $conditions['versions.status'];
		}
		
		//If none of the platforms options are selected, default to 'visible' platforms.
		if(!isset($conditions['show.platforms'])){
			$platformDefault = $this->PlatformVersion->find('list', array('fields'=>array('PlatformVersion.id'), 'conditions'=>array('PlatformVersion.visibledefault'=>'1'), 'recursive'=>0));
			$conditions['show.platforms'] = array_keys($platformDefault);
			$this->request->data['Filter']['show-platforms'] = $conditions['show.platforms'];
		}

		$this->set('platforms', $this->Platform->view());
		
		//var_dump($conditions);
		
		//Generate Report
		if(array_key_exists('ext', $this->request->params)){
			$this->response->download("isv_registry.csv");
			$this->layout = 'ajax';
			$this->set('validations', $this->ValidationRegistry->find('filtered', array('conditions'=>$conditions)));
			$this->render('csv/validation_registry');
		} else {
			//No extension - use standard view
			$this->set('validations', $this->paginate('ValidationRegistry', $conditions));
		}
	}
	
	public function validationRegistryOld(){
		$this->set('title', 'ISV Partner Validation Registry');
		$this->loadModel('Platform');
		$this->loadModel('ValidationRegistryOld');
		
		//Load filter settings from request
		$conditions = $this->Components->load('Filter')->filter($this);
		
		//If no status is set, default to 'complete' status
		if(!isset($conditions['versions.status'])){
			$conditions['versions.status'] = array('complete');
			if(!isset($this->request->data['Filter'])){
				$this->request->data['Filter'] = array();
			}
			$this->request->data['Filter']['versions-status'] = array('complete');
		}
		
		//Generate Report
		$this->set('platforms', $this->Platform->view());
		if(array_key_exists('ext', $this->request->params)){
			$this->response->download("isv_registry.csv");
			$this->layout = 'ajax';
			$this->set('validations', $this->ValidationRegistry->find('filtered', array('conditions'=>$conditions)));
			$this->render('csv/validation-registry');
		} else {
			//No extension - use standard view
			$this->set('validations', $this->paginate('ValidationRegistryOld', $conditions));
		}
	}
	
	public function validationSchedule(){
		$this->set('title', 'ISV Validation Schedule');
		$this->loadModel('PlatformVersion');
		$this->loadModel("ValidationSchedule");
		$this->loadModel("User");
		
		//Load filter settings from request
		$conditions = $this->Components->load('Filter')->filter($this);
		
		//If none of the Validation LEVELs are selected, default to ALL.
		if(!isset($conditions['validations.level'])){
			$levels= Configure::read('Validation.levels');
			$conditions['validations.level'] = array_keys($levels);
			$this->request->data['Filter']['validations-level'] = $conditions['validations.level'];
		}
		
		//If none of the status options are selected, default to 'complete' status.
		if(!isset($conditions['validations.status'])){
			$conditions['validations.status'] = array('propose','schedule','active','delay','alert');
			$this->request->data['Filter']['validations-status'] = $conditions['validations.status'];
		}

		$this->set('sausers', $this->User->find(
			'superlist', array(
						'conditions'=>array('is_sa'=>1), 
						'fields'=>array('id', 'firstname', 'lastname'), 
						'separator'=>' ',
						'recursive'=>0
						)
					));

		$this->set('platformOptions', $this->PlatformVersion->find(
			'superlist', array(
						'fields'=>array('PlatformVersion.id', 'Platform.name', 'PlatformVersion.version'), 
						'separator'=>' ', 
						'order'=>array('Platform.sortorder ASC', 'PlatformVersion.version ASC'),
						'recursive'=>0,
						'contain'=>array('Platform')
						)
					));
		if(array_key_exists('searchtext LIKE ', $conditions)){
			$conditions["CONCAT(partners.name,'~',products.name,'~',if(isnull(validations.validator), '', validations.validator),'~',if(isnull(platforms.name), '', platforms.name)) LIKE "] = $conditions['searchtext LIKE '];
			unset($conditions['searchtext LIKE ']);
		}

		$this->set('validations', $this->paginate('ValidationSchedule', $conditions));

	}	
	
	public function validationScheduleOld(){
		$this->set('title', 'ISV Validation Schedule');
		$this->loadModel("Platform");
		$this->loadModel("User");
		$this->loadModel("ValidationSchedule");
		
		$conditions = $this->Components->load('Filter')->filter($this);
		
		//If no status is set, default to open statuses
		$statusesDefault = array('propose','schedule','active','delay','alert');
		if(!isset($conditions['validations.status'])){
			$conditions['validations.status'] = $statusesDefault;
			if(!isset($this->request->data['Filter'])){
				$this->request->data['Filter'] = array();
			}
			$this->request->data['Filter']['validations-status'] = $statusesDefault;
		}
		
		$this->set('platforms', $this->Platform->find('list'));
		$this->set('sausers', $this->User->find('superlist', array('conditions'=>array('is_sa'=>1), 'fields'=>array('id', 'firstname', 'lastname'), 'separator'=>' ')));
		
		if(array_key_exists('ext', $this->request->params)){
			$this->response->download("schedule.csv");
			$this->layout = 'ajax';
			$this->set('validations', $this->ValidationSchedule->find('schedule', array('conditions'=>$conditions)));
			$this->render('csv/index');
		} else {
			//No extension - use standard view
			$this->set('validations', $this->paginate('ValidationSchedule', $conditions));
		}

	}
}
