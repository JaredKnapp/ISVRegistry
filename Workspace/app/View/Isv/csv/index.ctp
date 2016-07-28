<?php 

function C($input){
	return '"' . str_replace('"', '""', $input) . '"';
}


$statuses= Configure::read('Validation.statusOptions');

echo '"Partners","Products","Version","Industry","Workload"';

foreach($platforms as $index=>$platform){
	$isVisible = true;
	$platformindex = 'T'.$platform['platformversions']['id'];
	if(isset($this->data['Show'])){
		$isVisible = $this->data['Show'][$platformindex]=='0' ? false : true;
	} else {
		$isVisible = $platform['platformversions']['visible']=='1'? true : false;
	}
	if($isVisible){
		echo ',' . C($platform['platforms']['name'] . ' ' . $platform['platformversions']['version']);
	}
}
echo "\r";

foreach ($validations as $validation){
	if(empty($validation['partners']['partner_url'])){
		echo C($validation['partners']['partner']); 
	} else {
		echo C($this->Html->link($validation['partners']['partner'], $validation['partners']['partner_url'], array('target' => '_blank'))); 
	}
	
	echo ',';
	
	if(empty($validation['products']['product_url'])){
		echo C($validation['products']['product']);
	} else {
		echo C($this->Html->link($validation['products']['product'], $validation['products']['product_url'], array('target' => '_blank'))); 
	}

	echo ',';
	
	echo C($validation['versions']['version']);
	
	echo ',';
	
	echo C($validation['industries']['industry']);

	echo ',';
	
	echo C($validation['workloads']['workload']);
	
	foreach($platforms as $index=>$platform){
		$isVisible = true;
		$platformindex = 'T'.$platform['platformversions']['id'];
		if(isset($this->data['Show'])){
			$isVisible = $this->data['Show'][$platformindex]=='0' ? false : true;
		} else {
			$isVisible = $platform['platformversions']['visible']=='1'?true:false;
		}
		if($isVisible){
			$status = $validation[$platformindex][$platformindex.'_status'];
			
			if(is_null($status)){
				echo ',';
			} else {
				$level = $validation[$platformindex][$platformindex];
				echo ',' . C($level . ': ' . $statuses[$status]['label']);
			}
		}
	}
	
	echo "\r";
}
