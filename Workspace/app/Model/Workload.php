<?php
App::uses('AppModel', 'Model');

class Workload extends AppModel {
	
	public $useTable = 'workloads';
	
	public $order = array("Workload.industries_id" => "asc", "Workload.name" => "asc");

	public $hasMany = array(
		'Products'=>array(
			'className'=>'Product',
			'foreignKey'=>'workloads_id'
			)
		);
	
	public $belongsTo = array(
		'Industry'=>array(
			'className'=>'Industry',
			'foreignKey'=>'industries_id'
			)
		);
	
	
	public function beforeDelete($cascade = true) {
		$count = $this->Products->find("count", array("conditions" => array("workloads_id" => $this->id)));
		if ($count == 0) {
			return true;
		}
		return false;
	}
	
}

