<?php

/**
 * @file mavicmeta/includes/mavicmeta.admin.inc
 * Administrative functions for mavicmeta.
 */

/**
 * The administrative settings form for mavicmeta.
 */
function mavicmeta_settings() {

	return drupal_get_form('mavicmeta_admin_form');
}

function mavicmeta_admin_form() {
	$form = array();

	$form['#cache'] = TRUE;

	$form['#prefix'] = '<div id="mavicmeta_admin_wrapper">';
	$form['#suffix'] = '</div>';

	$form['list'] = array();

	$form['#theme'] = 'mavicmeta_admin_render';

	return $form;
}

function theme_mavicmeta_admin_render($form = array()) {
	global $language;
	//get current language
	$lang = $language->language;
	$defParams = mavicmeta_variable_get('langs');
	/*$form['debug'] = array(
		'#type' => 'textarea',
		'#value' => print_r($defParams, true),
	);*/
	$params = $def_params[$lang];
	//foreach ($defParams as $lang => $params) {

		$headToken = array(
			array('data' => 'Level'),
			array('data' => 'Tokens list'),
		);

		$headType = array(
			array('data' => 'Delete'),
			array('data' => 'System name'),
			array('data' => 'Language mode'),
			array('data' => 'tokens'),
		);
		$storedStuctures = $typeContent['structure'];
		$rows = array();
		$listB = array_keys($form[$lang]['list']);
		foreach ($listB as $type) {
			
			if (isset($form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_structure']['list'])) {
				$listA = array_keys($form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_structure']['list']);
				//$counter = 1;
				$rowsB = array();

				foreach ($listA as $level) {
					if (isset($form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_structure']['list'][$level . '_*_list'])) {
						//$counter++;
						$fieldsA = array();
						$fieldsA[] = drupal_render($form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_structure']['list'][$level][$lang . '_*_' . $type . '_*_structure_*_' . $level]);
						$fieldsA[] = drupal_render($form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_structure']['list'][$level . '_*_list']);
						$rowsB[] = $fieldsA;
					}
				}
				//output a table if more than on level
				if (count($rowsB) > 1) {
					
					$form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_structure']['#value'] = theme('table', $headToken, $rowsB);
				} else {
					$form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_structure']['#value'] = implode(' ', $rowsB[0]);
				}
				
			}
			$fields = array();
			if (isset($form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_systemName'])) {
				$fields[] = drupal_render($form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_delete']);
				$fields[] = drupal_render($form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_systemName']) . drupal_render($form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_name']) . drupal_render($form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_type']);
				$fields[] = drupal_render($form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_lang']);
				$fields[] = drupal_render($form[$lang]['list'][$type][$lang . '_*_' . $type . '_*_structure']);
				$rows[] = $fields;
			}
		}

		$form[$lang]['#value'] = theme('table', $headType, $rows); // Theme as table output
		
	//}
	$output = drupal_render($form); // Render rest of form components.
	return $output;
}

function mavicmeta_admin_form_submit($form, &$form_state) {
	
	$defParams = mavicmeta_variable_get('langs');
	$newOnes = array();
	$storeIt = true;
	$typeList = array();
	$toDelete = array();

	foreach ($form_state['clicked_button']['#post'] as $key => $value) {
		$parts = explode('_*_', $key);
		
		//store and skip the one to delete
		if (isset($form_state['clicked_button']['#post'][$parts[0] . '_*_' . $parts[1] . '_*_delete'])) {
			
			if (!isset($toDelete[$parts[0]]))
				$toDelete[$parts[0]] = array();
			if (!in_array($parts[1], $toDelete[$parts[0]]))
				$toDelete[$parts[0]][] = $parts[1];
			continue;
		}

		if (isset($defParams[$parts[0]])) {
			if (!isset($newOnes[$parts[0]])) {

				$typeN = explode('**', $form_state['clicked_button']['#post'][$parts[0] . '_*_addOne_*_systemName']);
				//special case for nodes : the value (nid) is given by the textfield
				if ($typeN[0] == 'node') {
					$nid = (int) $form_state['clicked_button']['#post'][$parts[0] . '_*_addOne_*_name'];
					$path = 'node/' . $nid;
					if (drupal_get_path_alias($path, $parts[0]) != $path) {
						$newOnes[$parts[0]] = array(
							0 => 'node',
							1 => $nid,
						);
					} else {
						drupal_set_message(t('The system did not find any path alias associated to the node <em>' . $nid . '</em>, please check it.'));
						$storeIt = false;
						break;
					}
				} else {
					$newOnes[$parts[0]] = $typeN;
				}
			}


			switch ($parts[1]) {

				case 'HP' : //the only things which can be changed are the name and tokens
					if ($parts[2] == 'name') {
						$defParams[$parts[0]]['HP']['name'] = $value;
					} elseif ($parts[2] == 'structure' && !empty($value) && $value != 1) {
						$defParams[$parts[0]]['HP']['structure'][1]['[' . $parts[4] . ']'] = $value;
					}
					break;

				case 'addOne' :
					if ($newOnes[$parts[0]][0] != '0') {
						if (!isset($defParams[$parts[0]][$newOnes[$parts[0]][1]]))
							$defParams[$parts[0]][$newOnes[$parts[0]][1]] = array();
						switch ($parts[2]) {
							case 'type' :
								$defParams[$parts[0]][$newOnes[$parts[0]][1]]['type'] = $newOnes[$parts[0]][0];
								break;
							case 'systemName' :
							case 'structure' :
								break;
							default :
								$defParams[$parts[0]][$newOnes[$parts[0]][1]][$parts[2]] = $value;
						}
					}
					break;

				default:
					if ($parts[2] === 'structure') {
						if (!in_array($parts[1] . $parts[3], $typeList)) {
							$typeList[] = $parts[1] . $parts[3];
							$reset = true;
						}
						if (!isset($defParams[$parts[0]][$parts[1]]['structure']))
							$defParams[$parts[0]][$parts[1]]['structure'] = array();
						//if this level is not selected, drop all tokens
						if (!isset($form_state['clicked_button']['#post'][$parts[0] . '_*_' . $parts[1] . '_*_structure_*_' . $parts[3]])) {
							$defParams[$parts[0]][$parts[1]]['structure'][$parts[3]] = 0;
						} else {
							//drop all tokens first
							if ($reset) {
								$defParams[$parts[0]][$parts[1]]['structure'][$parts[3]] = array();
								$reset = false;
							}
							if (isset($parts[4]) && !empty($value)) {
								//$tokenName = implode('_*_', array_slice($parts, 4));
								$defParams[$parts[0]][$parts[1]]['structure'][$parts[3]]['[' . $parts[4] . ']'] = $value;
							}
						}
					} elseif ($parts[2] == 'lang' || $parts[2] == 'name') {
						$defParams[$parts[0]][$parts[1]][$parts[2]] = $value;
					}
			}
		}
	}
	//delete
	if (!empty($toDelete)) {
		foreach ($toDelete as $lang => $typeA) {
			foreach ($typeA as $type) {
				unset($defParams[$lang][$type]);
			}
		}
	}
	//save
	if ($storeIt) {
		mavicmeta_variable_set('langs', $defParams);

		drupal_set_message('The settings have been successfully saved.');
	}
}