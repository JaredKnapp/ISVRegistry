<?php
if (!empty($workloads)) {
	echo '<option value="">' . Configure::read('Select.emptyOptionText') . '</option>';
	foreach ($workloads as $k => $v) {
		echo '<option value="' . $k . '">' . h($v) . '</option>';
	}
} else {
	echo '<option value="">' . Configure::read('Select.emptyOptionText') . '</option>';
}
