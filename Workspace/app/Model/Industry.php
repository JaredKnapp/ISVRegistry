<?php
App::uses('AppModel', 'Model');

class Industry extends AppModel {
	
	public $useTable = 'industries';
	
	public $order = array("Industry.name" => "asc");

	public $hasMany = array(
		'Workloads'=>array(
			'className'=>'Workload',
			'foreignKey'=>'industries_id',
			'order'=>'name'
			)
		);
	
	public function beforeDelete($cascade = true) {
		$count = $this->Workloads->find("count", array("conditions" => array("industries_id" => $this->id)));
		if ($count == 0) {
			return true;
		}
		return false;
	}

}