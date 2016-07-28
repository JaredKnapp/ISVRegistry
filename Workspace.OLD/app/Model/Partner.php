<?php
App::uses('AppModel', 'Model');
class Partner extends AppModel {
	
	public $useTable = 'partners';

	public $displayField = 'name';
	public $recursive = 2;

	public $validateXX = array(
		'url'=>'url'
		);
	
	public $belongsTo = array(
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

	public $hasMany = array(
		'Products'=>array(
			'className'=>'Product',
			'foreignKey'=>'partners_id',
			'order'=>'name'
			)
		);
	
	public function beforeDelete($cascade = true) {
		$count = $this->Products->find("count", array("conditions" => array("partners_id" => $this->id)));
		if ($count == 0) {
			return true;
		}
		return false;
	}

}