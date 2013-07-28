<?php
	global $list_all_news; // to store node data for view-unformatted
	$trad = array();
	$trad['01'] = t('January');
	$trad['02'] = t('February');
	$trad['03'] = t('March');
	$trad['04'] = t('April');
	$trad['05'] = t('May');
	$trad['06'] = t('June');
	$trad['07'] = t('July');
	$trad['08'] = t('August');
	$trad['09'] = t('September');
	$trad['10'] = t('October');
	$trad['11'] = t('November');
	$trad['12'] = t('December');
	
	$fields['path'] = url('node/'.$row->nid);
	$fields['nid'] = $row->nid;
	$fields['tnid'] = $fields['tnid']->raw;
	$day = (int)substr($fields['field_news_date_value']->raw,8,2);
	$month = $trad[substr($fields['field_news_date_value']->raw,5,2)];
	$year = substr($fields['field_news_date_value']->raw,0,4);
	$fields['date'] = $day.' '.$month.' '.$year;
	$list_all_news[] = $fields;