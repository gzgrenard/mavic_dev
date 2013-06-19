<?php
//
// get data
//
$tab = build_tab($form, $form_state, $parameters['nb_xls_cols_news'],array(0,4,6,7,8,9,10,11),array(0,4,6,7,1,2,3));
if(!is_array($tab) && $tab == 'error') return;

//
// drop features
//
if($form_state['values']['drop'] && !$form_state['values']['simulation']) 
{
	$res = db_query('select n.nid from {node} n where n.type="news"');
	while($node = db_fetch_array($res)) node_delete($node['nid']);
}

if(!is_array($tab)) return; // only drop

//
// recuperation des categories
//
$cat = array();
$done = array(); // stock de l anglais pour traduction
foreach($parameters['langs'] as $lang) {
	$cat[$lang] = array();
}
$res = db_query('select n.nid, n.language, r.body from {node_revisions} r INNER JOIN {node} n using (vid) where n.type="news_category"');
while($node = db_fetch_array($res)) {
	$cat[$node['language']][$node['body']] = db_result(db_query('select m.mlid from {menu_links} m where m.link_path="node/'.$node['nid'].'"'));
}

//
// insert into DB
// for each row
//
foreach($tab as $row_nb => $row) {
	$row_nb++; // on commence a 1
	if ($form_state['values']['title_bar']) $row_nb++;
	
	//
	// tests suplementaires
	//
	if(empty($parameters['landscape'][$row[6]])) {
		drupal_set_message("Unknown landscape ".$row[6]." on row $row_nb",'error');
		continue;
	}
	$lang = $parameters['filiale'][$row[8]];
	if(empty($lang)) {
		drupal_set_message("Unknown language id ".$row[8]." on row $row_nb",'error');
		continue;
	}
	$cat_mlid = $cat[$lang][$row[4]];
	if(empty($cat_mlid)) {
		drupal_set_message("Unknown category ".$row[4]." on row $row_nb",'error');
		continue;
	}
	
	//
	// news
	//
	if(!empty($row[9])) $id = $row[9];
	else $id = $row[0];
	$day = substr($row[7],0,2);
	$month = substr($row[7],2,2);
	$year = substr($row[7],4,4);
	$shortDate = (date('yz',mktime(0,0,0,$month,$day,$year)))*-1;
	$newNode = build_mavic_node($row[1], 'news', $row[3], $lang, $done[$id], 1,
							false,
							false,false, array('weight'=>$shortDate,'link_title'=>$row[1], 'menu_name'=>'menu-news', 'plid'=>$cat_mlid));
	if($newNode == 'error') {
		drupal_set_message("English version not found for news id $id ($lang) on row $row_nb",'error');
		continue;
	}
	$newNode->field_landscape = $parameters['landscape'][$row[6]];
	$newNode->field_news_intro = array(array('value'=>$row[2]));
	$newNode->field_page_title = array(array('value'=>$newNode->title));
	$newNode->field_page_description = array(array('value'=>$row[2]));
	$newNode->field_page_keyword = array(array('value'=>''));
	$newNode->field_news_picture_flickr = array(array('value'=>$row[5]));
	$newNode->field_news_date = array(array('value'=>$year.'-'.$month.'-'.$day.'T00:00:00'));
	
	//
	// link to range (category)
	//
	if(!empty($row[11]) && $row[11] != '0') {
		$values = explode(';',$row[11]);
		foreach($values as $value) {
			if(!empty($value)) {
				if(!$range_nid = db_result(db_query('select n.nid from {node_revisions} r INNER JOIN {node} n USING (vid) where n.type="category" and n.language="'.$lang.'" and r.body="'.$value.'"'))) {
					drupal_set_message("range $value not exist (language:$lang), row $row_nb.",'error');
					continue;
				} else {
					$newNode->field_news_family[] = array('nid'=>$range_nid);
				}
			}
		}
	}
	
	//
	// link to product
	//
	if(!empty($row[10]) && $row[10] != '0') {
		$values = explode(';',$row[10]);
		foreach($values as $value) {
			if(!empty($value)) {
				if(!$prod_nid = db_result(db_query('select n.nid from {node} n INNER JOIN {content_type_macromodel} c using (vid) where n.type="macromodel" and c.`field_modelco_value`="'.$value.'" and n.`language`="'.$lang.'"'))) {
					drupal_set_message("product ($value) not exist (language:$lang), row $row_nb.",'error');
					continue;
				} else {
					$newNode->field_news_product[] = array('nid'=>$prod_nid);
				}
			}
		}
	}
	
	
	save_mavic_node($newNode);
	if($lang == 'en')$done[$id] = $newNode->nid;
	
} // for each row

