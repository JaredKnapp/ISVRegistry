<?php
class ImportController extends AppController {
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('registry');
	}
	
	public function links(){
		$this->set('title', 'Import Registry Web Page');
		$this->loadModel("Partner");
		$this->loadModel("Product");

		echo '<table width="100%">';

		$csvFile = file('links.csv');
		foreach ($csvFile as $line) {
			$row = str_getcsv($line);
			
			echo "<tr>";

			$partner = mysql_escape_string(trim($row[0]));
			
			$partnerArray = split('=', $partner);
			$partnerName = trim($partnerArray[0]);
			$partnerURL = trim($partnerArray[1]);
			
			$product = mysql_escape_string(trim($row[1]));
			$productArray = split('=', $product);
			$fullProductName = split('~', $productArray[0]);
			$productName = trim($fullProductName[1]);
			$productURL = trim($productArray[1]);
			
			if(!empty($partnerURL) || !empty($productURL)){
				$partnerRecord = $this->Partner->findByName($partnerName);
				if($partnerRecord){
					if(!empty($partnerURL) && empty($partnerRecord['Partner']['url'])){
						echo '<td>'.$partnerRecord['Partner']['id'].'='.substr($partnerURL, 0, 15).'</td>';
						$partnerRecord['Partner']['url'] = $partnerURL;
						$this->Partner->save($partnerRecord);
					} else {
						if(empty($productURL) && !empty($partnerURL) && $partnerURL != $partnerRecord['Partner']['url']){
							$productURL = $partnerURL;
						}
						echo '<td></td>';
					}
					
					if(!empty($productURL)){
						$productRecord = $this->Product->find('first', array('conditions'=>array('partners_id'=>$partnerRecord['Partner']['id'], 'Product.name'=>$productName)));
						if($productRecord){
							if(empty($productRecord['Product']['url'])){
								echo '<td>'.$productRecord['Product']['id'].'='.substr($productURL, 0, 15).'</td>';
								$productRecord['Product']['url'] = $productURL;
								$this->Product->save($productRecord);
							} else {
								echo '<td></td>';
							}
						} else {
							echo '<td>!!!!!!!</td>';
						}
					} else {
						echo '<td></td>';
					}
				} else {
					echo "<td>?????</td><td></td>";
				}
			} else {
				echo "<td></td><td></td>";
			}
			echo "</tr>";
		}		
		
		echo '</table>';
		
	}
	public function registry(){
		$this->set('title', 'Import Registry Web Page');
		$this->loadModel("Partner");
		$this->loadModel("Product");
		$this->loadModel("Validation");
		$this->loadModel("Industry");
		$this->loadModel("Workload");
		
		$platforms=array(1, 2, 3, 4, 9, 6, 5, 8, 7);
		
		$this->Validation->query('TRUNCATE TABLE validations;');
		$this->Product->query('TRUNCATE TABLE products;');
		$this->Partner->query('TRUNCATE TABLE partners;');
		$this->Workload->query('TRUNCATE TABLE workloads;');
		$this->Industry->query('TRUNCATE TABLE industries;');

		$csvFile = file('registry.csv');
		foreach ($csvFile as $line) {
			$row = str_getcsv($line);
			
			$partnerName = mysql_escape_string(trim($row[0]));
			$productName = mysql_escape_string(trim($row[1]));
			$version = mysql_escape_string(trim($row[2]));
			$industryName = mysql_escape_string(trim($row[3]));
			$workloadName = mysql_escape_string(trim($row[4]));
			
			if($industryName=='Life Science') $industryName = 'Life Sciences';
			
			if(!empty($partnerName) && !empty($productName)){
				$partner = $this->Partner->find('first', array('conditions'=>array('name'=>$partnerName), 'recursive'=>-1));
				if(empty($partner)){
					$this->Partner->create();
					$partner = $this->Partner->save(array('Partner'=>array('name'=>$partnerName)));
				}
				
				$industry = $this->Industry->find('first', array('conditions'=>array('name'=>$industryName), 'recursive'=>-1));
				if(empty($industry)){
					$this->Industry->create();
					$industry = $this->Industry->save(array('Industry'=>array('name'=>$industryName)));
				}
				
				$workload = $this->Workload->find('first', array('conditions'=>array('industries_id'=>$industry['Industry']['id'], 'name'=>$workloadName), 'recursive'=>-1));
				if(empty($workload)){
					$this->Workload->create();
					$workload = $this->Workload->save(array('Workload'=>array('name'=>$workloadName, 'industries_id'=>$industry['Industry']['id'])));
				}
				
				$product = $this->Product->find('first', array('conditions'=>array('partners_id'=>$partner['Partner']['id'], 'workloads_id'=>$workload['Workload']['id'], 'name'=>$productName), 'recursive'=>-1));
				if(empty($product)){
					$this->Product->create();
					$product = $this->Product->save(array('Product'=>array('name'=>$productName, 'partners_id'=>$partner['Partner']['id'], 'workloads_id'=>$workload['Workload']['id'])));
				}
				
				//				echo var_dump($row);
				
				for($index = 5; $index <= 13; $index++){
					$level = trim(substr($row[$index].'  ', 0, 2));
					if(!empty($level) && $level != '-' && $level != '--'){
						$this->Validation->create();
						$this->Validation->save(array('Validation'=>array('level'=>$level, 'version'=>$version, 'status'=>'complete', 'products_id'=>$product['Product']['id'], 'platformversions_id'=>$platforms[$index - 5])));
					}
				}
			}
		}
	}

	public function schedule(){
		$this->set('title', 'Import Schedule Web Page');
		$this->loadModel("Partner");
		$this->loadModel("Product");
		$this->loadModel("Validation");
		$this->loadModel("Industry");
		$this->loadModel("Workload");
		
		$platforms=array('ECS'=>8,'Isilon'=>9,'ScaleIO'=>7);
		
		$this->Validation->query('TRUNCATE TABLE validations;');
		$this->Product->query('TRUNCATE TABLE products;');
		$this->Partner->query('TRUNCATE TABLE partners;');
		$this->Workload->query('TRUNCATE TABLE workloads;');
		$this->Industry->query('TRUNCATE TABLE industries;');

		$csvFile = file('registry.csv');
		foreach ($csvFile as $line) {
			$row = str_getcsv($line);
			
			$saname = mysql_escape_string(trim($row[0]));
			$partnerName = mysql_escape_string(trim($row[1]));
			$productName = mysql_escape_string(trim($row[2]));
			$industryName = mysql_escape_string(trim($row[3]));
			$workloadName = mysql_escape_string(trim($row[4]));
		}
	}
}
