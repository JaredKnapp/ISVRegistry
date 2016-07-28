<?php
App::uses('AppModel', 'Model');

class ValidationRegistryOld extends AppModel {
	
	public $useTable = false;
	
	public function find($type = 'first', $queryData = array()) {
		App::import('Model','Platform');

		switch($type) {
			case "filtered":

				$conditions = (array_key_exists('conditions', $queryData)) ? $queryData['conditions'] : array();
				$fields = (array_key_exists('fields', $queryData)) ? $queryData['fields'] : null;
				$order = (array_key_exists('order', $queryData)) ? $queryData['order'] : null;
				$limit = (array_key_exists('limit', $queryData)) ? $queryData['limit'] : null;
				$page = (array_key_exists('page', $queryData)) ? $queryData['page'] : 1;
				$recursive = (array_key_exists('recursive', $queryData)) ? $queryData['recursive'] : null;
				$extra = (array_key_exists('extra', $queryData)) ? $queryData['extra'] : array();
				
				$platformModel = new Platform();
				$platforms =  $platformModel->view();
				
				$show = array();
				if (isset($conditions) && array_key_exists('show', $conditions)) {
					$show = $conditions['show'];
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
    registry.workload_id,
    registry.workload,
    registry.version";

				$versionIds = array();
				foreach($platforms as $index=>$platform){
					$key = 'show_T'.$platform['platformversions']['id'];
					if(!array_key_exists($key, $show) || $show[$key]=='1'){
						$versionIds[] = $platform['platformversions']['id'];
						$sql = $sql . ",\r\n    T" . $platform['platformversions']['id'] . ".level AS T" . $platform['platformversions']['id'] . "";
						$sql = $sql . ",\r\n    T" . $platform['platformversions']['id'] . ".status AS T" . $platform['platformversions']['id'] . "_status";
						$sql = $sql . ",\r\n    T" . $platform['platformversions']['id'] . ".iscertified AS T" . $platform['platformversions']['id'] . "_iscertified";
					}
				}

				$sql = $sql . "\r\nFROM 
    vregistry as `registry`\r\n";

				foreach($platforms as $index=>$platform){
					$key = 'show_T'.$platform['platformversions']['id'];
					$table = 'T' . $platform['platformversions']['id'];
					if(!array_key_exists($key, $show) || $show[$key]=='1'){
						
						$statusClause = '';
						if(isset($conditions) && array_key_exists('versions.status', $conditions)){
							$value = $conditions['versions.status'];
							$statusClause = ' AND ' . $table . '.status ';
							if(is_array($value)){
								$statusClause = $statusClause . " IN ('" . implode("','", $value) . "') ";
							} else {
								$statusClause = $statusClause . "='" . $value . "' ";
							}
						}
						$sql = $sql . '			LEFT JOIN validations as ' . $table . ' ON ' . $table . '.products_id = registry.product_id AND '. $table .'.version = registry.version AND ' . $table . '.platformversions_id = ' . $platform['platformversions']['id'] . $statusClause . "\r\n";
					}
				}
				
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

				$sql = $sql . "GROUP BY product_id, version\r\n";

				//ORDER Commands
				$sql = $sql . "ORDER BY ";
				if(array_key_exists('sort', $extra)){
					$sql = $sql . $extra['sort'] . ' ' . $extra['direction'] . ',';
				}
				$sql = $sql . " partner ASC, product ASC, industry ASC, workload ASC, version ASC\r\n";
				
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