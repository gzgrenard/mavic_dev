<?php
  // This is also used in the installer, pre-database setup.
  $t = get_t();

  $output = '<tr class="form-item"';
  if (!empty($element['#id'])) {
    $output .= ' id="' . $element['#id'] . '-wrapper"';
  }
  $output .= ">\n";
  $required = !empty($element['#required']) ? '<span class="form-required" title="' . $t('This field is required.') . '">*</span>' : '';

  if (!empty($element['#title'])) {
    $title = $element['#title'];
    if (!empty($element['#id'])) {
      $output .= ' <td><label for="' . $element['#id'] . '">' . $t('!title: !required', array('!title' => filter_xss_admin($title), '!required' => $required)) . "</label></td>\n";
    }
    else {
      $output .= ' <td><label>' . $t('!title: !required', array('!title' => filter_xss_admin($title), '!required' => $required)) . "</label></td>\n";
    }
  }

  $output .= "<td>". $value ."</td>\n";

  if (!empty($element['#description'])) {
    $output .= ' <td class="description">' . $element['#description'] . "</td>\n";
  }

  $output .= "</tr>\n";

  print $output;
