<?php

/**
 * @file
 * This file is empty by default because the base theme chain (Alpha & Omega) provides
 * all the basic functionality. However, in case you wish to customize the output that Drupal
 * generates through Alpha & Omega this file is a good place to do so.
 * 
 * Alpha comes with a neat solution for keeping this file as clean as possible while the code
 * for your subtheme grows. Please read the README.txt in the /preprocess and /process subfolders
 * for more information on this topic.
 */

/**
 * Implements hook_form_FORM_ID_alter
 */ 
function mavic_theme_form_google_cse_results_searchbox_form_alter(&$form, &$form_state, $form_id) {
  //unseting Title  
  unset($form['query']['#title']);
  //unseting google widget
  unset($form['sa']['#suffix']);
  //modifying query size
  $form['query']['#size'] = 20;
  //modifying the input submit button
  //$files_dir = file_stream_wrapper_get_instance_by_uri('public://')->getDirectoryPath();
  //$form['sa'] = array('#type' => 'image_button', '#src' => $files_dir . '/images/search-button.png');
  $form['query']['#attributes'] = array('placeholder' => 'Rechercher...');
}

/**
 * Implements hook_views_pre_render
 */
function mavic_theme_views_pre_render(&$view) {
  if($view->name === 'upcoming_events' && $view->vid === '25') {
    $results = $view->result;
    foreach ($results as $key => $result) {
        $field_date_event = $result->_field_data['nid']['entity']->field_date;
        $date_event = _mavic_theme_get_date_next_event($field_date_event['und'][0]['value']);
        $results[$key]->field_field_date[0]['rendered']['#markup'] = $date_event;
    }
  }
}

/**
 * formatting date event
 * @param string $date_event
 * @return string $output
 */
function _mavic_theme_get_date_next_event($date_event) {
  $parts_date = explode(" ", $date_event);
  $part_date = str_replace('-', '', $parts_date[0]);
  $format_date = strtotime($part_date);
  $day = date('d', $format_date);
  $month = date('M', $format_date);
  $year = date('Y', $format_date);
  $output = '<span class="date-display-single">';
  $output .= '<span class="day_event">' . t('@day', array('@day' => $day)) . '</span> ';
  $output .= '<span class="m-y_event">' . t('@month @year', array('@month' => $month, '@year' => $year)) . '</span>';
  $output .= '</span>';
  
  return $output;
}
/**
 * Suppression du feed Promote en page d'accueil
 */
function mavic_theme_preprocess_page(&$variables) {
  module_load_include('php', 'mavic_mobilesp_lib', 'mobileesp');
  if (drupal_is_front_page()) {
    // unsetting the usual content that don't have to appear on the homepage
    unset($variables['page']['content']['content']);
  }
  $page_home = $variables['page']['content']['home']['home'];
  $Uagent = new uagent_info;
  $user_agent = $Uagent->Get_Uagent();
  $smartphone = $Uagent->DetectSmartphone();
  $tablet = $Uagent->DetectTierTablet();
  if($smartphone == 1) {
    $variables['is_mobile'] = TRUE; 
    // adding mobile class  
    array_push($variables['attributes_array']['class'], 'mobile');
    unset($variables['page']['content']['home']['home']['block_9']); // Block Mavic Live
    unset($variables['page']['content']['home']['home']['block_11']); // Block filter
    unset($variables['page']['content']['home']['home']['block_12']); // Block title Latest products
    unset($variables['page']['content']['home']['home']['views_home_latest_product-block']); // Block latest product1
    unset($variables['page']['content']['home']['home']['views_home_latest_product-block_1']); // Block latest product2
    unset($variables['page']['content']['home']['home']['block_13']); // Block title Upcoming events 
    unset($variables['page']['content']['home']['home']['views_home_next_event-block']); // Block Next events
    unset($variables['page']['content']['home']['home']['views_upcoming_events-block']); // Block Upcoming events
    unset($variables['page']['content']['home']['home']['views_2920c08c7bb93685ab9eb80c38d9923d']); // Block news    
    unset($variables['page']['footer']['footer']['footer_first']['menu_menu-footer-products']); // Block menu block products
    unset($variables['page']['footer']['footer']['footer_first']['menu_block_4']); // Block menu block downloads
    unset($variables['page']['header']['header']['header_first']['block_8']); // Block jeu concours
    unset($variables['page']['header']['header']['header_second']['block_10']); // Block latest videos
    unset($variables['page']['footer']['branding']['footer_second']['block_7']); // Block language switcher
    unset($variables['page']['footer']['branding']['footer_second']['block_6']); // Block text bottom
    
    $variables['page']['content']['home']['home']['#sorted'] = FALSE;
    if($page_home['views_home_video-block']) {
      $variables['page']['content']['home']['home']['views_engineers_talk-block']['#weight'] = 3;
    }
    if($page_home['views_home_video-block']) {
      $variables['page']['content']['home']['home']['views_home_video-block']['#weight'] = 2;
    }    
    if($page_home['block_15']) {
      $variables['page']['content']['home']['home']['block_15']['#weight'] = 1;
    } 
    if($page_home['views_745f8269a3dfd45303719437b3d02521']) {
      $variables['page']['content']['home']['home']['views_745f8269a3dfd45303719437b3d02521']['#weight'] = 4;
    }           
    /*variable_del('context_status');
    $context = context_load('home-page');
    ctools_export_set_object_status($context);
    drupal_flush_all_caches();*/
  }
  elseif($tablet == 1) {
    $variables['is_tablet'] = TRUE;
    // adding tablet class  
    array_push($variables['attributes_array']['class'], 'tablet');
    unset($variables['page']['content']['home']['home']['views_745f8269a3dfd45303719437b3d02521']); // Block news mobile
    unset($variables['page']['content']['home']['home']['block_15']); // Block jeu concours mobile
   /* variable_del('context_status');
    $context = context_load('home-page-mobile');
    ctools_export_set_object_status($context);
    drupal_flush_all_caches();*/
  }
  else {
    $variables['is_mobile'] = FALSE;
    $variables['is_tablet'] = FALSE;
    unset($variables['page']['content']['home']['home']['views_745f8269a3dfd45303719437b3d02521']); // Block news mobile
    unset($variables['page']['content']['home']['home']['block_15']); // Block jeu concours mobile
    /*variable_del('context_status');  
    $context = context_load('home-page-mobile');
    ctools_export_set_object_status($context);
    drupal_flush_all_caches();*/
  }
}

/**
 * surcharge de Better Exposed Filter => rendu "nested links"
 * utilis� pour le bloc des filtres des gammes
 * on renvoie une liste de liens avec hi�rarchie (pas de lien sur le 1er niveau)  
 */
function mavic_theme_select_as_links($vars) {
  $element = $vars['element'];

  $output = '<ul class="bef-tree">';
  $curr_depth = 0;
  $name = $element['#name'];

  // Collect selected values so we can properly style the links later.
  $selected_options = array();
  $parents = array();
  if (empty($element['#value'])) {
    if (!empty($element['#default_values'])) {
      $selected_options[] = $element['#default_values'];
    }
  }
  else {
    $selected_options[] = $element['#value'];
    $parents[] = array_keys(taxonomy_get_parents($element['#value']));
  }

  // Add to the selected options specified by Views whatever options are in the
  // URL query string, but only for this filter.
  $urllist = parse_url(request_uri());
  if (isset($urllist['query'])) {
    $query = array();
    parse_str(urldecode($urllist['query']), $query);
    foreach ($query as $key => $value) {
      if ($key != $name) {
        continue;
      }
      if (is_array($value)) {
        // This filter allows multiple selections, so put each one on the
        // selected_options array.
        foreach ($value as $option) {
          $selected_options[] = $option;
        }
      }
      else {
        $selected_options[] = $value;
      }
    }
  }

  // Clean incoming values to prevent XSS attacks.
  if (is_array($element['#value'])) {
    foreach ($element['#value'] as $index => $item) {
      unset($element['#value'][$index]);
      $element['#value'][filter_xss($index)] = filter_xss($item);
    }
  }
  elseif (is_string($element['#value'])) {
    $element['#value'] = filter_xss($element['#value']);
  }

  // Go through each filter option and build the appropriate link or plain text.
  foreach ($element['#options'] as $option => $elem) {
    // Check for Taxonomy-based filters.
    if (is_object($elem)) {
      $slice = array_slice($elem->option, 0, 1, TRUE);
      list($option, $elem) = each($slice);
    }

    // Check for optgroups.  Put subelements in the $element_set array and add
    // a group heading. Otherwise, just add the element to the set.
    $element_set = array();
    if (is_array($elem)) {
      $element_set = $elem;
    }
    else {
      $element_set[$option] = $elem;
    }

    $links = array();
    $html = '';
    $multiple = !empty($element['#multiple']);

    // If we're in an exposed block, we'll get passed a path to use for the
    // Views results page.
    $path = '';
    if (!empty($element['#bef_path'])) {
      $path = $element['#bef_path'];
    }

    foreach ($element_set as $key => $value) {
      if (t('- Any -') == $value) {
        $depth = 0;
      }
      else {
        preg_match('/^(-*).*$/', $value, $matches);
        $depth = strlen($matches[1]);
        $value = ltrim($value, '-');
      }
      // Custom ID for each link based on the <select>'s original ID.
      $id = drupal_html_id($element['#id'] . '-' . $key);
      $elem = array(
        '#id' => $id,
        '#markup' => '',
        '#type' => 'bef-link',
        '#name' => $id,
      );

      $active = NULL;
      if (array_search($key, $selected_options) === FALSE) {
        if ($depth == 1) $elem['#children'] = $value; 
        else $elem['#children'] = l($value, bef_replace_query_string_arg($name, $key, $multiple, FALSE, $path));
        $html .= theme('form_element', array('element' => $elem));
        $selected = '';
        if (in_array($key, $parents[0]) == TRUE) {
          $active = ' active';
        }
      }
      else {
        if ($depth == 1) $elem['#children'] = $value; 
        else $elem['#children'] = l($value, bef_replace_query_string_arg($name, $key, $multiple, TRUE, $path));
        _form_set_class($elem, array('bef-select-as-links-selected'));
        $html .= str_replace('form-item', 'form-item selected', theme('form_element', array('element' => $elem)));
        $selected = ' selected';
        $active = NULL;
      }
    }

    if ($depth > $curr_depth) {
      // We've moved down a level: create a new nested <ul>.
      // TODO: Is there is a way to jump more than one level deeper at a time?
      // I don't think so...
      $output .= "<ul class='bef-tree-child bef-tree-depth-$depth". $selected ."'><li class='depth-". $depth ." item-". $key ."'>$html";
      $curr_depth = $depth;
    }
    elseif ($depth < $curr_depth) {
      // We've moved up a level: finish previous <ul> and <li> tags, once for
      // each level, since we can jump multiple levels up at a time.
      while ($depth < $curr_depth) {
        $output .= '</li></ul>';
        $curr_depth--;
      }
      $output .= "</li><li class='depth-". $depth ." item-". $key . $active . $selected ."'>$html";
    }
    else {
      // Remain at same level as previous entry. No </li> needed if we're at
      // the top level.
      if (0 == $curr_depth) {
        $output .= "<li class='depth-". $depth ." item-". $key . $active . $selected ."'>$html";
      }
      else {
        $output .= "</li><li class='depth-". $depth ." item-". $key ."'>$html";
      }
    }
  }

  if (!$curr_depth) {
    // Close last <li> tag.
    $output .= '</li>';
  }
  else {
    // Finish closing <ul> and <li> tags.
    while ($curr_depth) {
      $curr_depth--;
      $output .= '</li></ul></li>';
    }
  }

  // Close the opening <ul class="bef-tree"> tag.
  $output .= '</ul>';


  /*$properties = array(
    '#description' => isset($element['#bef_description']) ? $element['#bef_description'] : '',
    '#children' => $output,
  );*/

  $output .= '<div class="bef-select-as-links">';
  //$output .= theme('form_element', array('element' => $properties));
  if (!empty($element['#value'])) {
    if (is_array($element['#value'])) {
      foreach ($element['#value'] as $value) {
        $output .= '<input type="hidden" name="' . $name . '[]" value="' . $value . '" />';
      }
    }
    else {
      $output .= '<input type="hidden" name="' . $name . '" value="' . $element['#value'] . '" />';
    }
  }
  $output .= '</div>';

  return $output;
}

/**
 * Implements hook_preprocess_node()
 * Create preprocess function for each node content type
 */
function mavic_theme_preprocess_node(&$variables, $hook) {
  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  } 
}

/**
 * function mavic_theme_preprocess_node_mv_produit()
 * @param array $variables
 */
function mavic_theme_preprocess_node_mv_produit(&$variables) {
 // dpm($variables, 'variables pp node prd');
  if(isset($variables['nid'])) {
    $gamme_nid = $variables['field_gamme']['und'][0]['nid'];
    $variables['gamme_nid'] = $gamme_nid;
    $gamme = node_load($gamme_nid);
    $variables['gamme_title'] = $gamme->title;
    $variables['gamme_url'] = '/node/' . $gamme_nid;
  }
}

function mavic_theme_breadcrumb($variables) {
  $sep = '<span class="sep_breadcrumb">&nbsp;</span>';
  if (count($variables['breadcrumb']) > 0) {
    return implode($sep, $variables['breadcrumb']) . $sep;
  }
  else {
    return t("Home");
  }
}
