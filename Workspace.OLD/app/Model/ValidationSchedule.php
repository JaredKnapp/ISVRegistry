<?php
App::uses('AppModel', 'Model');

class ValidationSchedule extends AppModel {
	
	public $useTable = false;
	
	public function find($type = 'first', $queryData = array()) {

		switch($type) {
			case "filtered":

				$conditions = (array_key_exists('conditions', $queryData)) ? $queryData['conditions'] : array();
				$fields = (array_key_exists('fields', $queryData)) ? $queryData['fields'] : null;
				$order = (array_key_exists('order', $queryData)) ? $queryData['order'] : null;
				$limit = (array_key_exists('limit', $queryData)) ? $queryData['limit'] : null;
				$page = (array_key_exists('page', $queryData)) ? $queryData['page'] : 1;
				$recursive = (array_key_exists('recursive', $queryData)) ? $queryData['recursive'] : null;
				$extra = (array_key_exists('extra', $queryData)) ? $queryData['extra'] : array();
				
				$sql="SELECT 
    validations.id,
    validations.level,
    validations.status,
    validations.estimatedcompletiondate,
    validations.completiondate,
    validations.created,
    validations.modified,
    validations.sa_owner_id,
	validations.notes,
    CONCAT_WS(' ',
            saowners.firstname,
            saowners.lastname) AS `sa_owner`,
    validations.ba_owner_id,
    CONCAT_WS(' ',
            baowners.firstname,
            baowners.lastname) AS `ba_owner`,
	validations.version,
	validations.validator, 
    products.id,
    products.name,
    partners.id,
    partners.name,
    platforms.name,
    platformversions.version,
    CONCAT(platforms.name,
            ' ',
            platformversions.version) AS 'platform'\r\n";

				$sql = $sql . "FROM 
	validations
        LEFT OUTER JOIN
    users AS `saowners` ON (validations.sa_owner_id = saowners.id)
        LEFT OUTER JOIN
    users AS `baowners` ON (validations.ba_owner_id = baowners.id)
        LEFT JOIN
    platformversions ON (validations.platformversions_id = platformversions.id)
        LEFT JOIN
    platforms ON (platformversions.platforms_id = platforms.id)
        LEFT JOIN
    products ON (validations.products_id = products.id)
        LEFT JOIN
    partners ON (products.partners_id = partners.id)\r\n";
				
				//FILTER Commands
				if (isset($conditions)) {
					$concat = 'WHERE ';
					foreach($conditions as $paramname=>$value){
						if(!in_array($paramname, array('show', 'versions.status'))){
							if(is_array($value)){
								$sql = $sql . "$concat $paramname IN ('".implode("','", $value)."')";
							} else {
								$sql = $sql . "$concat $paramname '$value'";
							}
							
							$concat = ' AND ';
						}
					}
					$sql = $sql . "\r\n";
				}

				//ORDER Commands
				$sql = $sql . "ORDER BY ";
				if(array_key_exists('sort', $extra)){
					$sql = $sql . $extra['sort'] . ' ' . $extra['direction'] . ',';
				}
				//Constant ORDER Commands
				$sql = $sql . " modified DESC\r\n";
				
				//PAGINATION Commands
				if(!empty($limit)){
					$sql = $sql . "LIMIT " . (($page - 1) * $limit) . ', ' . $limit;
				}
				
				//echo $sql.'<br><br>';
				
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