<?php
function c($input){
	return '"' . str_replace('"', '""', $input) . '"';
}

echo '"Date","Partner","Product","Platform","Industry","Level","Status"';
echo "\r\n";

foreach ($notifications as $notification){
    echo c($this->Time->format('M d, Y', $notification['Notification']['created']));
    echo ',';
    echo c($notification['Validation']['Product']['Partner']['name']);
    echo ',';
    echo c($notification['Validation']['Product']['name']);
    echo ',';
    echo c($notification['Validation']['PlatformVersion']['Platform']['name'] . ' ' . $notification['Validation']['PlatformVersion']['version']);
    echo ',';
    echo c($notification['Validation']['Product']['Workload']['Industry']['name']);
    echo ',';
    echo c($notification['Validation']['level']);
    echo ',';
    echo c($notification['Validation']['status']);

    echo "\r\n";
}
?>