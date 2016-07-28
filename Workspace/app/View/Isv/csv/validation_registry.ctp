<?php
function C($input){
	return '"' . str_replace('"', '""', $input) . '"';
}

$statuses= Configure::read('Validation.statusOptions');
$platformIds = array_values($this->data['Filter']['show-platforms']);

echo '"Partners","Products","Version","Industry","Workload"';

if(isset($this->data['Filter']['show-platforms'])){
	foreach($platforms as $index=>$platform){
		$platformindex = $platform['platformversions']['id'];
		if(in_array($platformindex, $platformIds)){
			echo ', "' . $platform['platforms']['name'] . ' ' . $platform['platformversions']['version'] . '"';
		}
	}
}

echo "\r\n";

foreach ($validations as $validation){
	
	if(empty($validation['registry']['partner_url'])){
		echo C($validation['registry']['partner']); 
	} else {
		echo C($this->Html->link($validation['registry']['partner'], $validation['registry']['partner_url'], array('target' => '_blank'))); 
	}
	
	echo ',';
	
	if(empty($validation['registry']['product_url'])){
		echo C($validation['registry']['product']);
	} else {
		echo C($this->Html->link($validation['registry']['product'], $validation['registry']['product_url'], array('target' => '_blank'))); 
	}
	
	echo ',';
	
	echo C($validation['registry']['version']);
	
	echo ',';
	
	echo C($validation['registry']['industry']);

	echo ',';
	
	echo C($validation['registry']['workload']);
	
	foreach($platforms as $index=>$platform){
		$platformindex = $platform['platformversions']['id'];
		if(in_array($platformindex, $platformIds)){
			$status = $validation['T'.$platformindex]['T'.$platformindex.'_status'];
			echo ',';
			if(is_null($status)){
				echo ' ';
			} else {
				$level = $validation['T'.$platformindex]['T'.$platformindex.'_level'];
				$isCertified = $validation['T'.$platformindex]['T'.$platformindex.'_iscertified'];
				$url = $validation['T'.$platformindex]['T'.$platformindex.'_url'];
				
				echo C($level . ': ' . $statuses[$status]['label'] . ':' . ($isCertified=='1'?'Certified':' '));
			}
		}
	}
	
	echo "\r";
	
}

?>