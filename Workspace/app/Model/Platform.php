<?php
App::uses('AppModel', 'Model');
class Platform extends AppModel {

	public $useTable = 'platforms';

	public $order = array("Platform.sortorder" => "asc");

    public $belongsTo = array(
        'Organization' => array(
            'className' => 'Organization',
            'foreignKey' => 'organizations_id',
            'fields'=>array('id', 'name')
        )
    );

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

    public function getVersion($id){
		$sql = "SELECT
            platforms.id as id,
			platforms.name as name,
            platforms.organizations_id as organizations_id,
			platformversions.id as id,
			platformversions.version as version,
			platformversions.visibledefault as visible
		FROM platforms, platformversions
		WHERE platforms.id=platformversions.platforms_id
              AND platformversions.id = $id";

		return $this->query($sql);
	}

	public function view($org = null){
		$sql = "SELECT
            platforms.id as id,
			platforms.name as name,
            platforms.organizations_id as organizations_id,
			platformversions.id as id,
			platformversions.version as version,
			platformversions.visibledefault as visible
		FROM platforms, platformversions
		WHERE platforms.id=platformversions.platforms_id";

        if( !is_null($org) && $org > '0' ){
            $sql = $sql . "
            AND platforms.organizations_id = ". $org;
        }

        $sql = $sql . "
        ORDER BY platforms.sortorder, platformversions.version ";

		return $this->query($sql);
	}

}