<?php

/**
 * Override or insert PHPTemplate variables into the templates.
 */
require_once(base_path(). "sites/default/modules/mavic_mobilesp_lib/mobileesp.php");

function phptemplate_preprocess(&$vars, $hook) {
	//echo'<pre>'; print_r($vars); echo'</pre>';
	static $menu_array, $active_menu_name, $description, $keywords, $head_title, $og_title, $og_description, $og_img, $facebook_thumbnail;
	global $breadcrumb;
	global $language;
	global $base_root;
	//
	// user agent
	//
	
	if (preg_match('/(?i)msie [1-8]/', $_SERVER['HTTP_USER_AGENT'])) { // if IE<=8 
		$vars['userAgent'] = 'msie7andminus';
	}
	else { // if other
		$vars['userAgent'] = 'other';
	}
	$vars['mobile'] = 'desktop';
	$uagent_obj = new uagent_info();
	//can't set detection on server side as page get cached : need a diff url...
	/*$vars['mobile'] = FALSE;
	 if ($uagent_obj->isTierTablet) {
	  $vars['mobile'] = 'tablet';
	  } else if ($uagent_obj->isTierIphone){
	  $vars['mobile'] = 'smartphone';
	  }
*/
	//
	// sub-domain
	//
	switch ($_SERVER['SERVER_NAME']) {
		case 'mavic.local':
			$vars['supdomain'] = 'mavic.local';
			break;
		case 'localhost' :
		case 'localmavic.com':
		case 'mtb.localmavic.com':
		case 'triathlon.localmavic.com':
		case 'roadcycling.localmavic.com':
			$vars['supdomain'] = 'localmavic.com';
			break;
		case 'mavic.bi-dev.com' :
		case 'mtb.mavic.bi-dev.com' :
		case 'triathlon.mavic.bi-dev.com' :
		case 'roadcycling.mavic.bi-dev.com' :
			$vars['supdomain'] = 'mavic.bi-dev.com';
			break;
		default :
			$vars['supdomain'] = 'mavic.com';
	}

	$vars['discipline'] = '';
	switch ($base_root) {
		case "http://roadcycling." . $vars['supdomain']:
			$vars['discipline'] = 'road';
			break;
		case "http://mtb." . $vars['supdomain']:
			$vars['discipline'] = 'mtb';
			break;
		case "http://triathlon." . $vars['supdomain']:
			$vars['discipline'] = 'triathlon';
			break;
		default:
			$vars['discipline'] = '';
	}
	//
	// path for image, css, and js
	//
	
	if ($vars['template_files'][0] == 'page-productcompare') {
		//echo print_r($vars['template_files'][1]);
		$nid = explode('-', $vars['template_files'][1]);
		menu_set_active_item('node/' . $nid[2]);
	}

	$vars['theme_path'] = base_path() . path_to_theme();
	$vars['theme_images'] = $vars['theme_path'] . '/images';
	$vars['product_path'] = base_path() . file_directory_path() . '/products';
	$vars['product_path_sys'] = file_directory_path() . '/products';
	$vars['features_path'] = base_path() . file_directory_path() . '/features';
	$vars['features_path_sys'] = file_directory_path() . '/features';
	$vars['technologies_path'] = base_path() . file_directory_path() . '/technologies';
	$vars['photos_path'] = base_path() . file_directory_path() . '/photos';
	$vars['technologies_path_sys'] = file_directory_path() . '/technologies';
	$vars['home_img_path'] = base_path() . file_directory_path() . '/homepage';
	


	// savoir si on est sur une page apparel
	$vars['isApparel'] = false;
	//
	// lang
	//
	if (!empty($vars['language']->language))
		$vars['lang'] = $vars['language']->language;
	elseif (!empty($vars['language']))
		$vars['lang'] = $vars['language'];
	else
		$vars['lang'] = $language->language;
	
	


	if (!$vars['is_admin'] &&
			($hook == "node" && ($vars['page'] || $vars['type'] == 'encart') || $hook == "page" || $hook == "views_view__news__page_1")) {

		//
		// menu
		//
		if (empty($menu_array)) {
			if (!menu_get_item()) // on initialise le menu
				menu_set_item(NULL, menu_get_item('node'));
			$menu_array = array('primary_links' => array('name' => 'menu-primary-links-' . $vars['lang']),
				'menu_video' => array('name' => 'menu-videos'),
				'menu_photo' => array('name' => 'menu_photo'),
				'menu_download' => array('name' => 'menu-download'),
				'menu_technologies' => array('name' => 'menu-menu-technologies-' . $vars['lang']),
				'menu_history' => array('name' => 'menu-history'),
				'menu_news' => array('name' => 'menu-news'),
				'menu_athlete' => array('name' => 'menu_athletes'),
				'menu_bottom' => array('name' => 'menu-bottom'),
				'menu_assistance' => array('name' => 'menu-assistance-' . $vars['lang']),
				'menu_careers' => array('name' => 'menu-careers')
			);
			foreach ($menu_array as $key => $value) {
				$menu_array[$key]['menu'] = menu_tree_page_data($value['name']);
				if ($breadcrumb_tmp = clean_menu($menu_array[$key]['menu'])) {
					$breadcrumb = $breadcrumb_tmp;
					if (($key == 'primary_links') && (isset($breadcrumb[3]))) {
						$getLine = node_load(str_replace('node/', '', $breadcrumb[1]['link']['link_path']));
						$vars['isApparel'] = ($getLine && ($getLine->body == 'apparel'));
						unset($getLine);
						if ((($vars['discipline'] == 'mtb') || ($vars['discipline'] == 'road')) && ($vars['isApparel'])) {
							// si on est sur le site mtb, il faut classer les produits apparels autrement
							// mtb en 1er
							// si on est sur site road on supprime les mtb
							$i = 0;
							foreach ($breadcrumb[3]['below'] as $key => $value) {
								if (db_result(db_query('SELECT nid FROM {content_field_featurenode} where field_featurenode_nid in (SELECT nid FROM {node} where title = \'practice: MTB\') and nid = ' . str_replace('node/', '', $value['link']['link_path'])))) {
									//s'il y a un resultat � la requete : bouge la place dans le menu
									unset($breadcrumb[3]['below'][$key]);
									if ($vars['discipline'] == 'mtb')
										array_splice($breadcrumb[3]['below'], $i, 0, array($key => $value));
									$i++;
								}
							}
						}
					}
					$active_menu_name = $value['name'];
				}
			}

			if (empty($breadcrumb)) {
				$breadcrumb[] = array('link' => array('title' => t('home page'), 'href' => '<front>', 'localized_options' => array(), 'type' => 0));
			}
		}
		foreach ($menu_array as $key => $value)
			$vars[$key] = $value['menu'];

		$vars['breadcrumb'] = $breadcrumb;
		$vars['active_menu_name'] = $active_menu_name;

		if ($hook == "page") {
			
			$vars['head_title'] = $head_title; //'Mavic | ' . t('Mavic is a french manufacturer of high-end bike systems and rider\'s equipment.');
			$vars['description'] = $description; //t('Mavic is a french manufacturer of high-end bike systems and rider\'s equipment.');
			$vars['keywords'] = $keywords;
			$vars['og_title'] = $og_title;
			$vars['og_description'] = $og_description;
			$vars['og_img'] = $og_img;
			
			if (!$vars['is_front'] || $uagent_obj->isTierIphone || $uagent_obj->isTierTablet) {

				//
				// landscape
				//
				$tempLandscape = 'all';

				if (!empty($vars['discipline']))
					$tempLandscape = $vars['discipline'];
				if (empty($vars['node']->field_landscape)) {
					require 'sites/default/modules/mavic_import/parameters.php';
					$land_tab = $parameters['landscape'][$tempLandscape];
				} else {
					$land_tab = $vars['node']->field_landscape;
				}
				$nbLand = count($land_tab);
				if ($vars['node']->type == 'cxr_landing_page') {
					$vars['landscape'] = $vars['theme_images'] . '/landingpage/cxr/background.jpg';
				}
				elseif ($vars['node']->type == 'ss2012_range_landing_page') {
					$vars['landscape'] = $vars['theme_images'] . '/landingpage/ss2012/landscape.jpg';
				}
				elseif ($vars['node']->type == 'cc40_landing_page') {
					$vars['landscape'] = $vars['theme_images'] . '/landscapes/road_mountain.jpg';//$vars['theme_images'] . '/landingpage/cc40c/cc40_bg.jpg';
				}
				elseif ($vars['node']->type == 'ss2013_landing_page') {
					if ($vars['discipline'] == 'mtb' || $_GET['disc'] == 'mtb') {
						$vars['landscape'] = $vars['theme_images'] . '/landscapes/MTB_cross-country.jpg';
					}
					else {
						$vars['landscape'] = $vars['theme_images'] . '/landscapes/road_mountain.jpg';
					}
					$tempLandscape = 'road';
				}
				else {
					$vars['landscape'] = $vars['theme_images'] . '/landscapes/' . $land_tab[rand(0, $nbLand - 1)]['value'] . '.jpg?v=2';
				}
			}
			else {
								$defNodeNid = mavicmeta_variable_get('defNid');
				$defNode = node_load($defNodeNid[$vars['lang']]);
				
				$vars['head_title'] = $defNode->field_page_title[0]['value'];
				$vars['description'] = $defNode->field_page_description[0]['value']; //t('Mavic is a french manufacturer of high-end bike systems and rider\'s equipment.');
				$vars['keywords'] = $defNode->field_page_keyword[0]['value'];
				$vars['og_title'] = $defNode->field_page_metashare_title[0]['value'];
				$vars['og_description'] = $defNode->field_page_metashare_description[0]['value'];
				$vars['og_img'] = $base_root . '/' . $defNode->field_page_metashare_image[0]['filepath'];

			}
		}
		elseif ($hook == "node" && $vars['page']) { // node de page
			//
			// get article, associated article and parent macromodel
			//


			switch ($vars['type']) {
				case 'macromodel' :
					$vars['list_color'] = array();
					foreach ($vars['field_otherarticle'] as $i => $article) {
						$tmp = node_load($article['nid']);
						$tmp->node_associated = array();
						foreach ($tmp->field_associated as $color) {
							if (!empty($color['nid'])) {
								$assoc_node = node_load($color['nid']);
								$macro_nid_tab = db_fetch_object(db_query('SELECT o.nid, m.field_usp_value, n.title FROM {content_field_otherarticle} o INNER JOIN {content_type_macromodel} m using (nid) INNER JOIN {node} n using (nid) WHERE n.status=1 and o.field_otherarticle_nid=' . $color['nid']));
								if (!empty($macro_nid_tab->nid)) {
									$assoc_node->macro_path = url('node/' . $macro_nid_tab->nid);
									$assoc_node->macro_usp = $macro_nid_tab->field_usp_value;
									$assoc_node->macro_title = $macro_nid_tab->title;
									$tmp->node_associated[] = $assoc_node;
								}
							}
						}
						$vars['list_color'][$tmp->title] = $tmp;
						$vars['list_color'][$tmp->title]->url_default = $tmp->title . '.jpg';

						if (file_exists($vars['product_path_sys'] . '/normal/' . $tmp->title . '_wheelsFront.jpg')) {
							$vars['altview_label'] = array(t('front wheel'), t('rear wheel'));
							$vars['altview_image_suffix'] = '_wheelsFront.jpg';
							$vars['list_color'][$tmp->title]->url_altview = $tmp->title . '_wheelsFront.jpg';
						}
						if (file_exists($vars['product_path_sys'] . '/normal/' . $tmp->title . '_glovesBottom.jpg')) {
							$vars['altview_label'] = array(t('palm view'), t('top view'));
							$vars['altview_image_suffix'] = '_glovesBottom.jpg';
							$vars['list_color'][$tmp->title]->url_altview = $tmp->title . '_glovesBottom.jpg';
						}
						if (file_exists($vars['product_path_sys'] . '/normal/' . $tmp->title . '_pedalsSide.jpg')) {
							$vars['altview_label'] = array(t('lateral view'), t('top view'));
							$vars['altview_image_suffix'] = '_pedalsSide.jpg';
							$vars['list_color'][$tmp->title]->url_altview = $tmp->title . '_pedalsSide.jpg';
						}
						if (file_exists($vars['product_path_sys'] . '/normal/' . $tmp->title . '_footwearBottom.jpg')) {
							$vars['altview_label'] = array(t('outsole view'), t('top view'));
							$vars['altview_image_suffix'] = '_footwearBottom.jpg';
							$vars['list_color'][$tmp->title]->url_altview = $tmp->title . '_footwearBottom.jpg';
						}
						if (file_exists($vars['product_path_sys'] . '/normal/' . $tmp->title . '_bootiesBottom.jpg')) {
							$vars['altview_label'] = array(t('outsole view'), t('top view'));
							$vars['altview_image_suffix'] = '_bootiesBottom.jpg';
							$vars['list_color'][$tmp->title]->url_altview = $tmp->title . '_bootiesBottom.jpg';
						}
						// pour les helmets
						if (file_exists($vars['product_path_sys'] . '/normal/' . $tmp->title . '_b.jpg')) {
							$vars['altview_label'] = array(t('interior view'), t('lateral view'));
							$vars['altview_image_suffix'] = '_b.jpg';
							$vars['list_color'][$tmp->title]->url_altview = $tmp->title . '_b.jpg';
							if (file_exists($vars['product_path_sys'] . '/normal/' . $tmp->title . '_c.jpg')) {
								array_push($vars['altview_label'], t('visor'));
								$vars['altview_image_suffix2'] = '_c.jpg';
								$vars['list_color'][$tmp->title]->url_altview2 = $tmp->title . '_c.jpg';
							}
						}
						// fin pour les helmets
					}
					if (!empty($vars['list_color'][$_POST['default_article']])) {
						$vars['default_color'] = $_POST['default_article'];
					}
					else {
						$vars['default_color'] = key($vars['list_color']);
					}

					if (mb_strtolower($breadcrumb[1]['link']['title']) == mb_strtolower($breadcrumb[2]['link']['title']))
						$head_title = $breadcrumb[4]['link']['title'] . ' - ' . $breadcrumb[1]['link']['title'] . ' - Mavic';
					else
						$head_title = $breadcrumb[4]['link']['title'] . ' - ' . $breadcrumb[1]['link']['title'] . ' - ' . $breadcrumb[2]['link']['title'] . ' - Mavic';

					break;
				case 'family' :
					// Get all other color for each product
					$products_list = $breadcrumb[3]['below'];
					$isFirst = true;
					foreach ($breadcrumb[3]['below'] as $key => $product) {
						$item = menu_get_item($product['link']['href']);
						$itemMap = $item['map'][1];
						if ($isFirst) {
							$isFirst = false;
							$vars['first_nid'] = $itemMap->nid;
						}
						foreach ($itemMap->field_otherarticle as $altArticle) {
							$model = node_load($altArticle['nid']);
							$products_list[$key]['assoc_color'][$altArticle['nid']] = $model->title;
						}
					}
					$vars['products_list'] = $products_list;
					// End get all other color 
					if (mb_strtolower($breadcrumb[1]['link']['title']) == mb_strtolower($breadcrumb[2]['link']['title']))
						$head_title = $breadcrumb[1]['link']['title'] . ' - Mavic';
					else
						$head_title = $breadcrumb[1]['link']['title'] . ' - ' . $breadcrumb[2]['link']['title'] . ' - Mavic';
					
					break;
				case 'technoline' :
				case 'prodvalcarac' :
					$head_title = $vars['node']->title . ' - ' . $breadcrumb[1]['link']['title'] . ' - ' . t('technology') . ' - Mavic';
					break;
				case 'history' :
					$head_title = $vars['node']->title . ' - ' . $breadcrumb[1]['link']['title'] . ' - ' . t('history') . ' - Mavic';
					break;
				case 'news' :
					$head_title = $vars['node']->title . ' - ' . $breadcrumb[1]['link']['title'] . ' - ' . t('news') . ' - Mavic';
					break;
				case 'download_category' :
					$head_title = $vars['node']->title . ' - ' . $breadcrumb[1]['link']['title'] . ' - ' . t('download') . ' - Mavic';
					break;
				case 'cxr_landing_page' :
				case 'page' :
				case 'discipline_home_page' :
				case 'tyre_landing_page' :
				case 'crossmax_landing_page' :
				case 'ss2012_range_landing_page' :
				case 'ss2013_landing_page' :
				case 'range_landing_page' :
				case 'contest':
					$head_title = $vars['node']->title . ' - Mavic';
					if (isset($vars['node']->field_facebook_img[0]['filepath']) && !empty($vars['node']->field_facebook_img[0]['filepath'])) {
						$facebook_thumbnail = base_path() . $vars['node']->field_facebook_img[0]['filepath'];
					}
					else {
						$facebook_thumbnail = base_path() . path_to_theme() . '/images/logo.png';
					}
					break;
				default :
					$head_title = 'Mavic';
					$facebook_thumbnail = base_path() . path_to_theme() . '/images/logo.png';
			}
				$defNodeNid = mavicmeta_variable_get('defNid');
				$defNode = node_load($defNodeNid[$vars['lang']]);
				
				$head_title = (!empty($head_title)) ? $head_title : $defNode->field_page_title[0]['value'];
				$head_title = (!empty($vars['node']->field_page_title[0]['value'])) ? $vars['node']->field_page_title[0]['value'] :  $head_title;//'Mavic | ' . t('Mavic is a french manufacturer of high-end bike systems and rider\'s equipment.');
				$description = (!empty($vars['node']->field_page_description[0]['value'])) ? $vars['node']->field_page_description[0]['value'] :  $defNode->field_page_description[0]['value']; //t('Mavic is a french manufacturer of high-end bike systems and rider\'s equipment.');
				$keywords = (!empty($vars['node']->field_page_keyword[0]['value'])) ? $vars['node']->field_page_keyword[0]['value'] : $defNode->field_page_keyword[0]['value'];
				$og_title = (isset($vars['node']->field_page_metashare_title[0]['value']) && !empty($vars['node']->field_page_metashare_title[0]['value'])) ? $vars['node']->field_page_metashare_title[0]['value'] : $defNode->field_page_metashare_title[0]['value'];
				$og_description = (!empty($vars['node']->field_page_metashare_description[0]['value'])) ? $vars['node']->field_page_metashare_description[0]['value'] : $defNode->field_page_metashare_description[0]['value'];
				//some early node hade a sharing img field named differently, take it as default before the main default one
				$tempOgImg = (!empty($vars['node']->field_page_metashare_image[0]['filepath'])) ? $vars['node']->field_page_metashare_image[0]['filepath'] : $vars['node']->field_facebook_img[0]['filepath'];
				$og_img = (!empty($tempOgImg)) ? $base_root . '/' . $tempOgImg : $base_root . '/' . $defNode->field_page_metashare_image[0]['filepath'];
				
		}
		else { // view all news
			
				$defNodeNid = mavicmeta_variable_get('defNid');
				$defNode = node_load($defNodeNid[$vars['lang']]);
				
				$head_title = $defNode->field_page_title[0]['value'];
				$description = $defNode->field_page_description[0]['value']; //t('Mavic is a french manufacturer of high-end bike systems and rider\'s equipment.');
				$keywords = $defNode->field_page_keyword[0]['value'];
				$og_title = $defNode->field_page_metashare_title[0]['value'];
				$og_description = $defNode->field_page_metashare_description[0]['value'];
				$og_img = $base_root . '/' . $defNode->field_page_metashare_image[0]['filepath'];
				
				if (isset($vars['view']->result[0]->node_title))
					$head_title = $vars['view']->result[0]->node_title. ' - Mavic';
		}
		
	}
}

//
// set breadcrumb
//
function clean_menu($menus) {
	$breadcrumb = '';
	foreach ($menus as $key => $value) {
		if ($value['link']['in_active_trail']) {
			$breadcrumb = array();
			$breadcrumb[] = array('link' => array('title' => t('home page'), 'href' => '<front>', 'localized_options' => array(), 'type' => 0));
			set_mavic_breadcrumb($breadcrumb, $value, $key);
		}
	}
	return $breadcrumb;
}

//
// create breadcrumb (recursion)
//
function set_mavic_breadcrumb(&$breadcrumb, $value, $key) {
	$value['key_breadcrumb'] = $key;
	$breadcrumb[] = $value;
	if ($value['link']['has_children']) {
		foreach ($value['below'] as $key2 => $child) {
			if ($child['link']['in_active_trail']) {
				set_mavic_breadcrumb($breadcrumb, $child, $key2);
				break;
			}
		}
	}
}

/*
  function mavic_select($element)
  {

  $select = '';
  $size = $element['#size'] ? ' size="'. $element['#size'] .'"' : '';
  _form_set_class($element, array('form-select'));
  $multiple = $element['#multiple'];//to be implemented when needed
  $html = '';
  $element['#attributes']['class'].=' customselect';

  $html .= '<div id="'.$element['#id'].'-display"  '.drupal_attributes($element['#attributes']).' onclick="show_hide_select(\'#'.$element['#id'].'\')">';
  if($element['#value'])
  {
  $html .= $element['#options'][$element['#value']];
  }
  else
  {
  $html .= $element['#options'][''];
  }
  $html .= '</div>';
  $html .= '<div style="position:absolute;">	<ul id="'.$element['#id'].'" class="customselect-list" style="display:none">';
  foreach($element['#options'] as $key=>$value)
  {
  $html .= '		<li><a onclick="$(\'#'.$element['#id'].'-input\')[0].value=\''.addslashes($key).'\'; $(\'#'.$element['#id'].'-display\')[0].innerHTML=\''.addslashes(t($value)).'\'; ">'.t($value).'</a></li>';
  }

  $html .= '	</ul></div>';
  $html.='
  <script type="text/javascript">
  $("#body").click( function(){ $(\'#'.$element['#id'].'\').css("display","none"); });
  </script>
  ';
  $html .= '<input type="hidden" name="'.$element['#name'].'" id="'.$element['#id'].'-input" value="'.t($element['#value']).'" />';


  // '<select name="'. $element['#name'] .''. ($multiple ? '[]' : '') .'"'. ($multiple ? ' multiple="multiple" ' : '') . drupal_attributes($element['#attributes']) .' id="'. $element['#id'] .'" '. $size .'>'. form_select_options($element) .'</select>'
  return theme('form_element', $element, $html );

  }
 */

function mavic_checkbox($element) {
	_form_set_class($element, array('form-checkbox'));
	$checkbox = '<input ';
	$checkbox .= 'type="checkbox" ';
	$checkbox .= 'name="' . $element['#name'] . '" ';
	$checkbox .= 'id="' . $element['#id'] . '" ';
	$checkbox .= 'value="' . $element['#return_value'] . '" ';
	$checkbox .= $element['#value'] ? ' checked="checked" ' : ' ';
	$checkbox .= drupal_attributes($element['#attributes']) . ' />';

	if (!is_null($element['#title'])) {
		$checkbox = '<label class="option" for="' . $element['#id'] . '">' . $checkbox . ' ' . t($element['#title']) . '</label>';
	}

	unset($element['#title']);
	return theme('form_element', $element, $checkbox);
}

function mavic_radio($element) {
	_form_set_class($element, array('form-radio'));
	$output = '<input type="radio" ';
	$output .= 'id="' . $element['#id'] . '" ';
	$output .= 'name="' . $element['#name'] . '" ';
	$output .= 'value="' . $element['#return_value'] . '" ';
	$output .= (check_plain($element['#value']) == $element['#return_value']) ? ' checked="checked" ' : ' ';
	$output .= drupal_attributes($element['#attributes']) . ' />';
	if (!is_null($element['#title'])) {
		$output = '<label class="option" for="' . $element['#id'] . '">' . $output . ' ' . t($element['#title']) . '</label>';
	}

	unset($element['#title']);
	return theme('form_element', $element, $output);
}

