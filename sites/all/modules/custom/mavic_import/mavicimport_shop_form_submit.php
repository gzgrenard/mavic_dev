<?php

/*Script for loading all shops unlocated, see Site configuration, location :
* Enable JIT geocoding (needs to set location.source on 4 for all unlocated shop before running it)
* *$tempres = db_query('SELECT DISTINCT (node.nid) AS nid,
   node.vid AS node_vid,
   location.country AS location_country,
   location.latitude AS location_latitude,
   location.province AS location_province,
   location.lid lid
 FROM node node 
 LEFT JOIN location_instance location_instance ON node.vid = location_instance.vid
 LEFT JOIN location location ON location_instance.lid = location.lid
 WHERE (node.type in ("shop")) AND (location.latitude = 0)');
$listUnlocatedShop = array();
$i=0;
while ($row = db_fetch_array($tempres)) {
	$i++;
	node_load($row['nid']);
	echo '<span id="debug_shop_'.$i.'" class="debug_shop" style="display: none;">'. $row['nid'].' done </span>';
	sleep(1);
}
*/


//
// get data
//
$tab = build_tab($form, $form_state, $parameters['nb_xls_cols_shop'],array(0), array(0,1,2,7));
if(!is_array($tab) && $tab == 'error') return;

//
// drop data
//
if(!$form_state['values']['simulation']) 
{
	$drop_arr = array();
	$list_country = $form_state['values']['field_set'];
	
	if(!empty($list_country)) {
		foreach($list_country as $key => $value) {
			if(substr($key,0,5) == 'drop_' && $value) {
				$code = substr($key,5,2);
				$name = location_country_name($code);
				if (user_access('Edit Shop '.$name.' ('.$code.')') || user_access('Edit All Shops'))
					$drop_arr[] = 'l.country="'.$code.'"';
			}
		}
	}
	if(!empty($drop_arr)) {
		$query = implode(' or ', $drop_arr);
		
		$req = 'select n.nid from {node} n INNER JOIN {location_instance} USING (nid) INNER JOIN {location} l USING (lid) where n.type="shop" and ('.$query.')';
		$res = db_query($req);
	} else if ($form_state['values']['drop'] && user_access('administer menu'))
		$res = db_query('select n.nid from {node} n where n.type="shop"');
	global $user;//give temporary admin right to delete nodes
	$original_user = $user;
	$user = user_load(1);
	while($node = db_fetch_array($res)) {
		node_delete($node['nid']);
	}
	$user = $original_user;	//reset user to its original value
}

if(!is_array($tab)) return; // only drop


//
// insert into DB
// for each row
//
cache_clear_all('location:supported-countries', 'cache_location');//clear the cache as countries are stored in the language used when during the last caching
$country = _location_supported_countries();
$country = array_map('strtolower',$country);
$country_code = array_flip($country);

foreach($tab as $row_nb => $row) {
	$row_nb++; // on commence a 1
	if ($form_state['values']['title_bar']) $row_nb++;
	
	//
	// shop
	//
	$country_name_code = $country_code[strtolower($row[2])];
	//var_dump('Edit Shop '.str_replace(',','',strtolower($row[2])).' ('.$country_name_code.')');die;
	if(!user_access('Edit Shop '.str_replace(',','',$row[2]).' ('.$country_name_code.')') && !user_access('Edit All Shops')) {
		drupal_set_message("You don't have right to insert shop row ".$row[0],'error');
		continue;
	}
	$lang = '';
	$title = strtoupper($country_name_code).'-'.$row[10].' : '.$row[7];
	$newNode = build_mavic_node($title, 'shop', $row[7], $lang, $lang, 0);
	if($newNode == 'error') {
		drupal_set_message("English version not found for shop ($lang) : ".$row[0],'error');
		continue;
	}
	if(!isset($country_name_code)) {
		drupal_set_message('country '.$row[2].' not exist or not suported by location module row '.$row_nb,'error');
		continue;
	}
	$newNode->field_email = array(array('email'=>$row[4]));
	$newNode->field_website = array(array('value'=>$row[9]));
	$newNode->field_storefinder = array(array('value'=>1));//not available anymore in the xls file : checked by default
	$newNode->field_shopinshop = array(array('value'=>-1*$row[13]));//old Mavic lab option
	if($row[14] == -1)
		$newNode->field_premium = array(array('value'=>'mavic_yellow'));
	else
		$newNode->field_premium = array(array('value'=>'mavic_white'));
	$newNode->field_mp3 = array(array('value'=>-1*$row[11]));
	$newNode->field_wheels = array(array('value'=>-1*$row[15]));
	$newNode->field_rims = array(array('value'=>-1*$row[16]));
	$newNode->field_tyres = array(array('value'=>-1*$row[17]));
	$newNode->field_computers = array(array('value'=>-1*$row[18]));
	$newNode->field_pedals = array(array('value'=>-1*$row[19]));
	$newNode->field_helmets = array(array('value'=>-1*$row[20]));
	$newNode->field_footwear = array(array('value'=>-1*$row[21]));
	$newNode->field_apparel = array(array('value'=>-1*$row[22]));
	$newNode->field_accessories = array(array('value'=>-1*$row[23]));
	$newNode->field_mavic_lab = array(array('value'=>0));//not available anymore in the xls file : unchecked by default
	$newNode->field_tech_dealer = array(array('value'=>-1*$row[12]));
	$newNode->field_filtre_un = array(array('value'=>-1*$row[24]));
	$newNode->field_filtre_deux = array(array('value'=>-1*$row[25]));
	$newNode->field_filtre_trois = array(array('value'=>-1*$row[26]));
	$newNode->field_filtre_quatre = array(array('value'=>-1*$row[27]));
	$newNode->locations = array(array()); // pas d init pour pas ecraser si ca existe
	$newNode->locations[0]['street'] = $row[3];
	$newNode->locations[0]['city'] = $row[1];
	$newNode->locations[0]['postal_code'] = $row[10];
	$newNode->locations[0]['country_name'] = $row[2];
	$newNode->locations[0]['country'] = $country_name_code;
	$newNode->locations[0]['fax'] = $row[5];
	$newNode->locations[0]['phone'] = $row[6];
	$newNode->locations[0]['province'] = $row[8];
	save_mavic_node($newNode);
	usleep($form_state['values']['sleep_time']*100000); // stop the script to let google geocode correctly
} // for each row

//$_SESSION['debug_mavic'] = "done :<br><pre>".print_r($done['fr'],true)."</pre>";
//$_SESSION['debug_mavic'] = "filter_value :<br><pre>".print_r($filter_value,true)."</pre>";
//$_SESSION['debug_mavic'] = "filter_type :<br><pre>".print_r($filter_type,true)."</pre>";

