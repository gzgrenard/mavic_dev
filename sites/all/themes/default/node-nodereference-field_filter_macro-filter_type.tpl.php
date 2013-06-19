<?php 
echo $title." : ";
if ($field_multi[0]['value']) echo "(multi) ";
echo check_plain($field_description[0]['value']);
?>
<br>
<?php
foreach($field_value_list as $value) {
	echo check_plain($value['safe']['title']).'(id:'.$value['safe']['nid'].')<br>';
}