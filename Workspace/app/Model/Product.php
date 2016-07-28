<?php
App::uses('AppModel', 'Model');
class Product extends AppModel {

	public $useTable = 'products';
	
	public $order = array("Product.partners_id" => "asc", "Product.name" => "asc");

	public $belongsTo = array(
		'Partner'=>array(
			'className'=>'Partner',
			'foreignKey'=>'partners_id'
			),
		'Workload'=>array(
			'className'=>'Workload',
			'foreignKey'=>'workloads_id'
			),
		'SaOwner' => array(
			'className' => 'User',
			'foreignKey' => 'sa_owner_id'
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
		'Validations'=>array(
			'className'=>'Validation',
			'foreignKey'=>'products_id',
			'order'=>'created DESC'
			)
		);
	
	public function beforeDelete($cascade = true) {
		$count = $this->Validations->find("count", array("conditions" => array("products_id" => $this->id)));
		if ($count == 0) {
			return true;
		}
		return false;
	}
	
	
}