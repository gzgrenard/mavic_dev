<?php

/**
 * @file
 * This template is used to print a single field in a view.
 *
 * It is not actually used in default Views, as this is registered as a theme
 * function which has better performance. For single overrides, the template is
 * perfectly okay.
 *
 * Variables available:
 * - $view: The view object
 * - $field: The field handler object that can process the input
 * - $row: The raw SQL result that can be used
 * - $output: The processed output that will normally be used.
 *
 * When fetching output from the $row, this construct should be used:
 * $data = $row->{$field->field_alias}
 *
 * The above will guarantee that you'll always get the correct data,
 * regardless of any changes in the aliasing that might happen if
 * the view is modified.
 */
 //dsm($row);
 $pictosCouleur = '';
 if (count($row->field_field_d_clinaisons) > 1) {
    foreach ($row->field_field_d_clinaisons as $value) {
       $img = theme('image_style', array( 'path' => $value['rendered']['field_photo_principale']['#items'][0]['uri'], 'style_name' => '14x12_picto_couleur'));
       $pictosCouleur .= l( $img, '', array('attributes' => array('class' => 'changeColor ' . $value['raw']['nid']), 'html' => true) );
    }
}
?>
<?php print $output; ?>
<div class='pictosCouleur'>
  <?php print $pictosCouleur; ?>
</div>