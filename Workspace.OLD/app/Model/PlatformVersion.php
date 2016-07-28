<?php
App::uses('AppModel', 'Model');
class PlatformVersion extends AppModel {
	
	public $useTable = 'platformversions';
	
	public $order = array("PlatformVersion.platforms_id" => "asc", "PlatformVersion.version" => "asc");
	
	public $belongsTo = array(
		'Platform'=>array(
			'className'=>'Platform',
			'foreignKey'=>'platforms_id'
		)
	);

	public $hasMany = array(
		'Validations'=>array(
			'className'=>'Validation',
			'foreignKey'=>'platformversions_id',
			'order'=>'version DESC'
			)
		);

	public function beforeDelete($cascade = true) {
		$count = $this->Validations->find("count", array("conditions" => array("platformversions_id" => $this->id)));
		if ($count == 0) {
			return true;
		}
		return false;
	}
	
	
	function find($type='first', $query = array()) {
		switch ($type) {
			case 'superlist':
				if(!isset($query['fields']) || count($query['fields']) < 3) {
					return parent::find('list', $query);
				}

				if(!isset($query['separator'])) {
					$query['separator'] = ' ';
				}

				$query['recursive'] = -1;              
				$list = parent::find('all', $query);

				for($i = 1; $i <= 2; $i++) {
					$field[$i] = $query['fields'][$i];
				}

				return Set::combine($list, '{n}.'.$this->alias.'.'.$this->primaryKey,
					array('%s'.$query['separator'].'%s',
						'{n}.'.$field[1],
						'{n}.'.$field[2]));
				break;                      

			default:
				return parent::find($type, $query);
				break;
		}
	}
}