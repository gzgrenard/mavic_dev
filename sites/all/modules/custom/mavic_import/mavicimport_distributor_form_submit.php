<?php

//
// get data
//
$tab = build_tab($form, $form_state, $parameters['nb_xls_cols_distributor'],array(0), array(0,1,2,3,8));
if(!is_array($tab) && $tab == 'error') return;

//
// drop data
//
if($form_state['values']['drop'] && !$form_state['values']['simulation']) 
{
	$res = db_query('select n.nid from {node} n where n.type="distributor"');
	while($node = db_fetch_array($res)) node_delete($node['nid']);
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
$lang = '';
foreach($tab as $row_nb => $row) {
	$row_nb++; // on commence a 1
	if ($form_state['values']['title_bar']) $row_nb++;
	
	//
	// shop
	//
	$newNode = build_mavic_node($row[8], 'distributor', '', $lang, $lang, 0,
							'INNER JOIN {node_revisions} r using (vid) where n.type="distributor" and r.`title`="'.$row[8].'"');
	
	$listCountryDistrib = array_map('trim',explode(';',$row[3]));
	$newNode->field_country_distrib = array();
	foreach($listCountryDistrib as $country) {
		if(!isset($country_code[strtolower($country)])) {
			drupal_set_message('country '.$country.' not exist or not suported by location module row '.$row_nb,'error');
			continue 2;
		}
		$newNode->field_country_distrib[] = array('value'=>$country);
	}
	
	$newNode->field_email = array(array('email'=>$row[5]));
	$newNode->field_website = array(array('value'=>$row[10]));
	if($row[15] == -1)
		$newNode->field_premium = array(array('value'=>'mavic_yellow'));
	else
		$newNode->field_premium = array(array('value'=>'mavic_white'));
	$newNode->field_mp3 = array(array('value'=>-1*$row[12]));
	$newNode->field_wheels = array(array('value'=>-1*$row[16]));
	$newNode->field_rims = array(array('value'=>-1*$row[17]));
	$newNode->field_tyres = array(array('value'=>-1*$row[18]));
	$newNode->field_computers = array(array('value'=>-1*$row[19]));
	$newNode->field_pedals = array(array('value'=>-1*$row[20]));
	$newNode->field_footwear = array(array('value'=>-1*$row[22]));
	$newNode->field_apparel = array(array('value'=>-1*$row[23]));
	$newNode->field_accessories = array(array('value'=>-1*$row[24]));
	$newNode->field_mavic_lab = array(array('value'=>-1*$row[14]));
	$newNode->field_tech_dealer = array(array('value'=>-1*$row[13]));
	$newNode->field_street = array(array('value'=>$row[4]));
	$newNode->field_city = array(array('value'=>$row[1]));
	$newNode->field_postal_code = array(array('value'=>$row[11]));
	$newNode->field_country_name = array(array('value'=>$row[2]));
	$newNode->field_fax = array(array('value'=>$row[6]));
	$newNode->field_phone = array(array('value'=>$row[7]));
	$newNode->field_province = array(array('value'=>$row[9]));
	$newNode->field_filtre_un = array(array('value'=>-1*$row[25]));
	$newNode->field_filtre_deux = array(array('value'=>-1*$row[21]));//helmets
	$newNode->field_filtre_trois = array(array('value'=>-1*$row[26]));
	$newNode->field_filtre_quatre = array(array('value'=>-1*$row[27]));
	save_mavic_node($newNode);
} // for each row

//$_SESSION['debug_mavic'] = "done :<br><pre>".print_r($done['fr'],true)."</pre>";
//$_SESSION['debug_mavic'] = "filter_value :<br><pre>".print_r($filter_value,true)."</pre>";
//$_SESSION['debug_mavic'] = "filter_type :<br><pre>".print_r($filter_type,true)."</pre>";

