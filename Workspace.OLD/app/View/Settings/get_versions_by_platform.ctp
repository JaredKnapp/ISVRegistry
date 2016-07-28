<?php
if (!empty($platformVersions)) {
	foreach ($platformVersions as $k => $v) {
		echo '<option value="' . $k . '">' . h($v) . '</option>';
	}
} else {
	echo '<option value="">' . Configure::read('Select.emptyOptionText') . '</option>';
}
