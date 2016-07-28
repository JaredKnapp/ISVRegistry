<?php
App::uses('AppModel', 'Model');

class ValidationSummary extends AppModel {
	
	public $useTable = false;
	
	public function view($platforms){
		$sql="SELECT
			partners.id AS partner_id,
			partners.name AS partner,
			partners.url AS partner_url,
			products.id AS product_id,
			products.name AS product,
			products.url as product_url,
			industries.id AS industry_id,
			industries.name AS industry,
			workloads.id as workload_id,
			workloads.name AS workload, 
			versions.version AS version";

		foreach($platforms as $index=>$platform){
			$sql = $sql . ",\r\n			T" . $platform['platformversions']['id'] . ".level AS T" . $platform['platformversions']['id'] . "";
		}

		$sql = $sql . "\r\nFROM 
			partners
			LEFT JOIN products ON products.partners_id = partners.id 
			LEFT JOIN workloads ON products.workloads_id = workloads.id 
			LEFT JOIN industries ON workloads.industries_id = industries.id 
			LEFT JOIN validations as versions ON versions.products_id = products.id\r\n";

		foreach($platforms as $index=>$platform){
			$sql = $sql . '			LEFT JOIN validations as T' . $platform['platformversions']['id'] . ' ON T' . $platform['platformversions']['id'] . '.products_id = products.id AND T' . $platform['platformversions']['id'] . '.platformversions_id = ' . $platform['platformversions']['id'] . "\r\n";
		}
		
		//FILTER Commands
		//$sql = $sql . "WHERE partners.name LIKE 'b%'\r\n";

		$sql = $sql . "GROUP BY partner, product, industry, workload, version\r\n";

		//ORDER Commands
		$sql = $sql . "ORDER BY ";
		//$sql = $sql . "T1 DESC,";
		$sql = $sql . " Partner ASC, Product ASC, Industry ASC, Workload ASC, Version ASC\r\n";
		
		//PAGINATION Commands
		//$sql = $sql . "LIMIT 10, 100\r\n";
		
		return $this->query($sql);
	}

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
			partners.id AS partner_id,
			partners.name AS partner,
			partners.url as partner_url,
			products.id AS product_id,
			products.name AS product,
			products.url as product_url,
			industries.id AS industry_id,
			industries.name AS industry,
			workloads.id as workload_id,
			workloads.name AS workload, 
			versions.version AS version";

				$versionIds = array();
				foreach($platforms as $index=>$platform){
					$key = 'show_T'.$platform['platformversions']['id'];
					if(!array_key_exists($key, $show) || $show[$key]=='1'){
						$versionIds[] = $platform['platformversions']['id'];
						$sql = $sql . ",\r\n			T" . $platform['platformversions']['id'] . ".level AS T" . $platform['platformversions']['id'] . "";
						$sql = $sql . ",\r\n			T" . $platform['platformversions']['id'] . ".status AS T" . $platform['platformversions']['id'] . "_status";
					}
				}

				$sql = $sql . "\r\nFROM 
			partners
			LEFT JOIN products ON products.partners_id = partners.id 
			LEFT JOIN workloads ON products.workloads_id = workloads.id 
			LEFT JOIN industries ON workloads.industries_id = industries.id 
			LEFT JOIN validations as versions ON versions.products_id = products.id AND versions.platformversions_id in (".implode(', ', $versionIds).")\r\n";

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
						$sql = $sql . '			LEFT JOIN validations as ' . $table . ' ON ' . $table . '.products_id = products.id AND ' . $table . '.platformversions_id = ' . $platform['platformversions']['id'] . $statusClause . "\r\n";
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

				$sql = $sql . "GROUP BY partner, product, industry, workload, version\r\n";

				//ORDER Commands
				$sql = $sql . "ORDER BY ";
				if(array_key_exists('sort', $extra)){
					$sql = $sql . $extra['sort'] . ' ' . $extra['direction'] . ',';
				}
				$sql = $sql . " Partner ASC, Product ASC, Industry ASC, Workload ASC, Version ASC\r\n";
				
				//PAGINATION Commands
				if(!empty($limit)){
					$sql = $sql . "LIMIT " . (($page - 1) * $limit) . ', ' . $limit;
				}
				
				//echo $sql.'<br><br>';
				
				return $this->query($sql);				break;
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