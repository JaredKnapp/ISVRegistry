<?php
App::uses('AppModel', 'Model');

class Validation extends AppModel {
	
	public $useTable = 'validations';

	public $belongsTo = array(
		'Product'=>array(
			'className'=>'Product',
			'foreignKey'=>'products_id'
			),
		'PlatformVersion'=>array(
			'className'=>'PlatformVersion',
			'foreignKey'=>'platformversions_id'
			),
		'SaOwner' => array(
			'className' => 'User',
			'foreignKey' => 'sa_owner_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
			),
		'BaOwner' => array(
			'className' => 'User',
			'foreignKey' => 'ba_owner_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
			)
		);
	
	/**
	 * Event Callbacks
	 **/
	public function afterFind($results, $primary = false) {
		parent::afterFind($results, $primary);
		foreach ($results as $key => $val) {
			if (isset($val['Validation']['estimatedcompletiondate'])) {
				$results[$key]['Validation']['estimatedcompletiondate'] = $this->dateFormatAfterFind($val['Validation']['estimatedcompletiondate']);
			}
			if (isset($val['Validation']['completiondate'])) {
				$results[$key]['Validation']['completiondate'] = $this->dateFormatAfterFind($val['Validation']['completiondate']);
			}
		}
		return $results;
	}
	
	/**
	* Internal helper methods
	**/
	public function dateFormatAfterFind($dateString) {
		return date('Y-m-d', strtotime($dateString));
	}
}