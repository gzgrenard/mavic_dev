<?php 
global $weightvalues;
foreach($field->field_values as $pid){
	foreach($pid as $f){
		$weightvalues[] = $f['value'];
	}
}
?>