<?php
App::uses('AppController', 'Controller');

class IsvController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index','validationRegistry','validationSchedule', 'validationRegistryOld');
	}

    public function index(){
        return $this->redirect(
                    array('action' => 'validationRegistry')
                );
    }

	public function validationRegistry(){
		$this->set('title', 'ISV Partner Validation Registry');
		$this->loadModel('Platform');
		$this->loadModel('PlatformVersion');
		$this->loadModel('ValidationRegistry');
        $this->loadModel('Organization');

		//Load filter settings from request
		$conditions = $this->Components->load('Filter')->filter($this);

		//If none of the Validation LEVELs are selected, default to ALL.
		if(!isset($conditions['level'])){
			$levels = Configure::read('Validation.levels');
			$conditions['level'] = array_keys($levels);
			$this->request->data['Filter']['level'] = $conditions['level'];

            if(!isset($conditions['viewoptions'])){
                //Select the default view options (if levels have not been set)
                $conditions['viewoptions'] = array('empty');    //Default to NOT show empty validations
                $this->request->data['Filter']['viewoptions'] = $conditions['viewoptions'];
            }
        }

		//If none of the status options are selected, default to 'complete' status.
		if(!isset($conditions['versions.status'])){
			$conditions['versions.status'] = array('schedule','active','complete');
			$this->request->data['Filter']['versions-status'] = $conditions['versions.status'];
		}

		//If none of the platforms options are selected, default to 'visible' platforms.
		if(!isset($conditions['show.platforms'])){
			$platformDefault = $this->PlatformVersion->find('list', array('fields'=>array('PlatformVersion.id'), 'conditions'=>array('PlatformVersion.visibledefault'=>'1'), 'recursive'=>0));
			$conditions['show.platforms'] = array_keys($platformDefault);
			$this->request->data['Filter']['show-platforms'] = $conditions['show.platforms'];
		}

		$this->set('platforms', $this->Platform->view($this->Session->read('org.id')));
        $this->set('organizations', $this->Organization->find('list'));
        $this->set('organizations', array());

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

	public function validationSchedule(){
		$this->set('title', 'ISV Validation Schedule');
		$this->loadModel('PlatformVersion');
		$this->loadModel("ValidationSchedule");
		$this->loadModel("User");

        $conditions = array();

        if(array_key_exists('named', $this->request->params) && array_key_exists('id', $this->request->params['named'])){
            $conditions['validations.id'] = $this->request->params['named']['id'];
        } else {

            //Load filter settings from request
            $conditions = $this->Components->load('Filter')->filter($this);

            //Set the current / default organization
            if($this->Session->read('org.id')>'0'){
                $conditions['platforms.organizations_id ='] = $this->Session->read('org.id');
            }

            //Replace the 'SearchText' with the actual field names we want to search for
            if(array_key_exists('searchtext', $conditions)){
                $conditions["CONCAT(partners.name,'~',products.name,'~',saowners.firstname,'~',saowners.lastname,'~',if(isnull(validations.validator), '', validations.validator),'~',if(isnull(platforms.name), '', platforms.name))"] = $conditions['searchtext'];
                unset($conditions['searchtext']);
            }

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
                        'contain'=>array('Platform'),
                        'conditions'=>(($this->Session->read('org.id')>'0')?array('Platform.organizations_id'=>$this->Session->read('org.id')):null)
                        )
                    ));

		$this->set('validations', $this->paginate('ValidationSchedule', $conditions));

	}

}
