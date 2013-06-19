<?php 
global $weightlabels;
foreach($field->field_values as $pid){
	foreach($pid as $f){
		$weightlabels[] = $f['value'];
	}
}
?>