<?php
App::uses('AppModel', 'Model');

class ValidationRegistry extends AppModel {

	public $useTable = false;

	public function find($type = 'first', $queryData = array()) {
		App::import('Model','PlatformVersion');
		App::import('Model','Organization');

		switch($type) {
			case "filtered":

				$conditions = (array_key_exists('conditions', $queryData)) ? $queryData['conditions'] : array();
				$fields = (array_key_exists('fields', $queryData)) ? $queryData['fields'] : null;
				$order = (array_key_exists('order', $queryData)) ? $queryData['order'] : null;
				$limit = (array_key_exists('limit', $queryData)) ? $queryData['limit'] : null;
				$page = (array_key_exists('page', $queryData)) ? $queryData['page'] : 1;
				$recursive = (array_key_exists('recursive', $queryData)) ? $queryData['recursive'] : null;
				$extra = (array_key_exists('extra', $queryData)) ? $queryData['extra'] : array();

                $orgTable = new Organization();
                $defaultorg = $orgTable->find('first', array('conditions'=>array('is_default'=>true)));
                $organization = (array_key_exists('org', $conditions)) ? $conditions['org'] : $defaultorg['Organization']['id'];

				$platformVersionTable = new PlatformVersion();
				$platforms =  $platformVersionTable->find('list', array('fields'=>array('PlatformVersion.id'),
                    'conditions'=> ($organization > '0' ? array('Platform.organizations_id' => $organization) : null),
					'recursive'=>0,
					'contain'=>('Platform'),
					'order'=>array('Platform.sortorder ASC', 'PlatformVersion.version ASC')
					));

                $organizationTable = new Organization();
                $organizations = $organizationTable->find('list');
                $organizations = array();

				$showPlatforms = array();
				if (isset($conditions) && array_key_exists('show.platforms', $conditions)) {
					$showPlatforms = array_values($conditions['show.platforms']);
				}

				$sql="SELECT
    registry.partner_id,
    registry.partner,
    registry.partner_url,
    registry.product_id,
    registry.product,
    registry.product_url,
    registry.industry_id,
    registry.industry,
    registry.industry_url,
    registry.workload_id,
    registry.workload,
    registry.workload_url,
    registry.version,
    registry.protocol";

				foreach($platforms as $index=>$platform){
					if(in_array($index, $showPlatforms)){
						$sql = $sql . ",\r\n    T" . $index . ".level AS T" . $index . "_level";
						$sql = $sql . ",\r\n    T" . $index . ".status AS T" . $index . "_status";
						$sql = $sql . ",\r\n    T" . $index . ".iscertified AS T" . $index . "_iscertified";
						$sql = $sql . ",\r\n    T" . $index . ".url AS T" . $index . "_url";
					}
				}

                foreach($organizations as $index=>$organization){
					$sql = $sql . ",\r\n    O" . $index . ".count AS O" . $index . "_count";
				}

				$sql = $sql . "\r\nFROM vregistry as `registry`\r\n";
				$platformWheres = array();

				foreach($platforms as $index=>$platform){
					if(in_array($index, $showPlatforms)){
						$table = 'T' . $index;

						$sql = $sql . "			LEFT JOIN validations as $table ON $table.products_id = registry.product_id ";
						$sql = $sql . " AND $table.version = registry.version ";
                        $sql = $sql . " AND IFNULL($table.protocol, '') = IFNULL(registry.protocol, '') ";
						$sql = $sql . " AND $table.platformversions_id = $index ";

						$levelClause = "";
						if(isset($conditions) && array_key_exists('level', $conditions)){
							$value = $conditions['level'];
							$levelClause = $table . '.level ';
							if(is_array($value)){
								$levelClause = $levelClause . " IN ('" . implode("','", $value) . "') ";
							} else {
								$levelClause = $levelClause . "='" . $value . "' ";
							}
							$sql = $sql . ' AND ' . $levelClause;
							$platformWheres[] = $levelClause;
						}

						if(isset($conditions) && array_key_exists('versions.status', $conditions)){
							$value = $conditions['versions.status'];
							$sql = $sql . " AND $table.status ";
							if(is_array($value)){
								$sql = $sql . " IN ('" . implode("','", $value) . "') ";
							} else {
								$sql = $sql . "='" . $value . "' ";
							}
						}

                        if(isset($conditions) && array_key_exists('versions.protocol' , $conditions)){
                            $sql = $sql . " AND $table.protocol LIKE '%". $conditions['versions.protocol']."%'";
                        }


                        if(isset($conditions) && array_key_exists('viewoptions' , $conditions) && in_array('certified', $conditions['viewoptions'])){
                            $sql = $sql . " AND $table.iscertified=1 ";
                        }

						$sql = $sql . "\r\n";

					}
				}
                foreach($organizations as $index=>$organization){
					$table = 'O' . $index;

                    $sql = $sql . "			LEFT JOIN
    (SELECT
        COUNT(validations.id) as count,
            validations.products_id,
            ifnull(validations.version, '') as `version`,
            ifnull(validations.protocol, '') as `protocol`,
            platforms.organizations_id
    FROM
        validations
    LEFT JOIN platformversions ON (validations.platformversions_id = platformversions.id)
    LEFT JOIN platforms ON (platformversions.platforms_id = platforms.id)
    WHERE
        1=1
";

                    if(isset($conditions) && array_key_exists('level', $conditions)){
                        $value = $conditions['level'];
                        $sql = $sql . " AND validations.level ";
                        if(is_array($value)){
                            $sql = $sql . " IN ('" . implode("','", $value) . "') ";
                        } else {
                            $sql = $sql . "='" . $value . "' ";
                        }
                    }

                    if(isset($conditions) && array_key_exists('versions.status', $conditions)){
                        $value = $conditions['versions.status'];
                        $sql = $sql . " AND validations.status ";
                        if(is_array($value)){
                            $sql = $sql . " IN ('" . implode("','", $value) . "') ";
                        } else {
                            $sql = $sql . "='" . $value . "' ";
                        }
                    }

                    $sql = $sql . "
    GROUP BY validations.products_id , ifnull(validations.version, '') , ifnull(validations.protocol, '') , platforms.organizations_id) AS $table ON $table.products_id = registry.product_id";
                    $sql = $sql . " AND $table.version = registry.version ";
                    $sql = $sql . " AND IFNULL($table.protocol, '') = IFNULL(registry.protocol, '') ";
                    $sql = $sql . " AND $table.organizations_id = $index ";

                    if(isset($conditions) && array_key_exists('versions.protocol' , $conditions)){
                        $sql = $sql . " AND $table.protocol LIKE '%". $conditions['versions.protocol']."%'";
                    }


                    if(isset($conditions) && array_key_exists('viewoptions' , $conditions) && in_array('certified', $conditions['viewoptions'])){
                        $sql = $sql . " AND $table.iscertified=1 ";
                    }

                    $sql = $sql . "\r\n";

				}

				$sql = $sql . "WHERE 1=1\r\n";

				//FILTER Commands
				if(isset($conditions) && array_key_exists('searchtext' , $conditions)){
					$sql = $sql . " AND concat(partner,'~',product,'~', industry , '~', workload) LIKE '" . '%'.$conditions['searchtext'].'%' . "'\r\n";
				}

				if(isset($platformWheres) && count($platformWheres) > 0 && isset($conditions) && array_key_exists('viewoptions', $conditions) && in_array('empty', $conditions['viewoptions'])){
					$join = '';
					$sql = $sql . ' AND (';
					foreach($platformWheres as $where){
						$sql = $sql . $join . $where;
						$join = ' OR ';
					}
					$sql = $sql . ")\r\n";
				}

				//ORDER Commands
				$sql = $sql . "ORDER BY ";
				if(array_key_exists('sort', $extra)){
					$sql = $sql . $extra['sort'] . ' ' . $extra['direction'] . ',';
				}
				$sql = $sql . " partner ASC, product ASC, version ASC, industry ASC, workload ASC\r\n";

				//PAGINATION Commands
				if(!empty($limit)){
					$sql = $sql . "LIMIT " . (($page - 1) * $limit) . ', ' . $limit;
				}

				//echo '<br>==============<br>'.$sql.'<br>==============<br>';
				return $this->query($sql);
				break;
			default:
				return parent::find($type, $queryData);
		}
	}

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
		$queryArray = array('conditions'=>$conditions, 'fields'=>$fields, 'order'=>$order, 'limit'=>$limit, 'page'=>$page, 'recursive'=>$recursive, 'extra'=>$extra);
		return $this->find('filtered', $queryArray);
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		return count($this->paginate($conditions, null, null, null, null, $recursive, $extra));
	}

}