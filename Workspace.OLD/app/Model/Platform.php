<?php
App::uses('AppModel', 'Model');
class Platform extends AppModel {

	public $useTable = 'platforms';
	
	public $order = array("Platform.sortorder" => "asc");
	
	public $hasMany = array(
		'PlatformVersions'=>array(
			'className'=>'PlatformVersion',
			'foreignKey'=>'platforms_id'
			)
	);

	public function beforeDelete($cascade = true) {
		$count = $this->PlatformVersions->find("count", array("conditions" => array("platforms_id" => $this->id)));
		if ($count == 0) {
			return true;
		}
		return false;
	}
		
	public function view(){
		$sql = "SELECT
			platforms.name as name, 
			platformversions.version as version, 
			platformversions.visibledefault as visible, 
			platformversions.id as id
		FROM platforms, platformversions 
		WHERE platforms.id=platformversions.platforms_id 
		ORDER BY platforms.sortorder, platformversions.version ";

		return $this->query($sql);
	}

}