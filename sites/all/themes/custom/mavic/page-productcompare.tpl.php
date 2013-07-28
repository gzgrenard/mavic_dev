<?php 
global $breadcrumb;
// Get first product of this list to get all other product information from DB
$firstProduct = current($breadcrumb[3]['below']);
$select = 'SELECT dst FROM url_alias WHERE src = "' . $firstProduct['link']['href'] .'" LIMIT 1';
$result = db_fetch_object(db_query($select));
$dst = split('/', $result->dst);
$dst = $dst[0].'/'.$dst[1].'/'.$dst[2].'/'.$dst[3].'/';
$lang=$language->language;

// Get all range with product nid/title and first model nid (for display picture)
$select = "SELECT ctm.field_usp_value AS description, ar.title AS image_name, SUBSTRING(ua.src, 6) AS node_id, n.title AS model_name
                    FROM url_alias ua INNER JOIN node n ON n.nid = SUBSTRING(ua.src, 6)
                    INNER JOIN content_field_otherarticle cfo USING(nid,vid)
                    INNER JOIN content_type_macromodel ctm USING(nid,vid)
                    INNER JOIN node ar on ar.nid= field_otherarticle_nid
                    WHERE ua.dst LIKE \"$dst%\"
                    AND ar.language = \"$lang\"
                    AND ar.type = \"article\"
                    AND n.type = \"macromodel\"
                    AND ar.language = n.language 
                    AND cfo.delta=0
                    ORDER BY model_name";
$result = db_query($select);
$productList = array();
while ($product = db_fetch_array($result)) {
    $productList[$product['node_id']] = $product;
}

$imagePath = '/sites/default/files/products/range/';
$pc_select = '<select onchange="updateProductCompare(this,this.options[this.selectedIndex].value);" class="pc_select_page">';
$pc_select .= '<option data-description="" data-image="" value="0"> --- '.t('Choose product to compare').' --- </option>';
foreach($breadcrumb[3]['below'] as $product) {
    $selected = ($product['link']['in_active_trail'] == true) ?  'selected' : '';
    $cnid = explode('/',$product['link']['href']);
    if (isset($productList[$cnid[1]])) {
        $dataImage = $imagePath.$productList[$cnid[1]]['image_name'].'.jpg';
        $dataDescription = $productList[$cnid[1]]['description'];
        $dataTitle = $productList[$cnid[1]]['model_name'];
        $dataNodeId = $cnid[1];
        $pc_select.= "<option value=\"$dataNodeId\" data-image=\"$dataImage\" data-description=\"$dataDescription\" $selected >$dataTitle</option>\n";
    } else {
        $dataNodeId = $cnid[1];
        $dataTitle = $product['link']['title'];
        $pc_select.= "<option data-description=\"\" data-image=\"\" value=\"$dataNodeId\" $selected >$dataTitle</option>\n";
    }
}
$pc_select .= '</select>';
print str_replace('<!--select-->',$pc_select,$content); 
?>
	
<?php 
/*
 * View code
 * */
$view = new view;
$view->name = 'productcompare';
$view->description = 'productcompare';
$view->tag = 'productcompare';
$view->view_php = '';
$view->base_table = 'node';
$view->is_cacheable = FALSE;
$view->api_version = 2;
$view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */
$handler = $view->new_display('default', 'Defaults', 'default');
$handler->override_option('fields', array(
  'nid' => array(
    'id' => 'nid',
    'table' => 'node',
    'field' => 'nid',
  ),
  'title' => array(
    'id' => 'title',
    'table' => 'node',
    'field' => 'title',
  ),
  'field_otherarticle_nid' => array(
    'label' => 'Articles',
    'alter' => array(
      'alter_text' => 0,
      'text' => '',
      'make_link' => 0,
      'path' => '',
      'link_class' => '',
      'alt' => '',
      'prefix' => '',
      'suffix' => '',
      'target' => '',
      'help' => '',
      'trim' => 0,
      'max_length' => '',
      'word_boundary' => 1,
      'ellipsis' => 1,
      'html' => 0,
      'strip_tags' => 0,
    ),
    'empty' => '',
    'hide_empty' => 0,
    'empty_zero' => 0,
    'link_to_node' => 0,
    'label_type' => 'widget',
    'format' => 'plain',
    'multiple' => array(
      'group' => 1,
      'multiple_number' => '1',
      'multiple_from' => '0',
      'multiple_reversed' => 0,
    ),
    'exclude' => 0,
    'id' => 'field_otherarticle_nid',
    'table' => 'node_data_field_otherarticle',
    'field' => 'field_otherarticle_nid',
    'relationship' => 'none',
  ),
  'view_node' => array(
    'label' => 'Link',
    'alter' => array(
      'alter_text' => 0,
      'text' => '',
      'make_link' => 0,
      'path' => '',
      'link_class' => '',
      'alt' => '',
      'prefix' => '',
      'suffix' => '',
      'target' => '',
      'help' => '',
      'trim' => 0,
      'max_length' => '',
      'word_boundary' => 1,
      'ellipsis' => 1,
      'html' => 0,
      'strip_tags' => 0,
    ),
    'empty' => '',
    'hide_empty' => 0,
    'empty_zero' => 0,
    'text' => 'View product',
    'exclude' => 0,
    'id' => 'view_node',
    'table' => 'node',
    'field' => 'view_node',
    'relationship' => 'none',
  ),
  'field_featurenode_nid' => array(
    'label' => '',
    'alter' => array(
      'alter_text' => 0,
      'text' => '',
      'make_link' => 0,
      'path' => '',
      'link_class' => '',
      'alt' => '',
      'prefix' => '',
      'suffix' => '',
      'target' => '',
      'help' => '',
      'trim' => 0,
      'max_length' => '',
      'word_boundary' => 1,
      'ellipsis' => 1,
      'html' => 0,
      'strip_tags' => 0,
    ),
    'empty' => '',
    'hide_empty' => 0,
    'empty_zero' => 0,
    'link_to_node' => 0,
    'label_type' => 'none',
    'format' => 'plain',
    'multiple' => array(
      'group' => 1,
      'multiple_number' => '',
      'multiple_from' => '',
      'multiple_reversed' => 0,
    ),
    'exclude' => 0,
    'id' => 'field_featurenode_nid',
    'table' => 'node_data_field_featurenode',
    'field' => 'field_featurenode_nid',
    'relationship' => 'none',
  ),
  'field_weight_value' => array(
    'label' => '',
    'alter' => array(
      'alter_text' => 0,
      'text' => '',
      'make_link' => 0,
      'path' => '',
      'link_class' => '',
      'alt' => '',
      'prefix' => '',
      'suffix' => '',
      'target' => '',
      'help' => '',
      'trim' => 0,
      'max_length' => '',
      'word_boundary' => 1,
      'ellipsis' => 1,
      'html' => 0,
      'strip_tags' => 0,
    ),
    'empty' => '',
    'hide_empty' => 0,
    'empty_zero' => 0,
    'link_to_node' => 0,
    'label_type' => 'none',
    'format' => 'default',
    'multiple' => array(
      'group' => 1,
      'multiple_number' => '',
      'multiple_from' => '',
      'multiple_reversed' => 0,
    ),
    'exclude' => 0,
    'id' => 'field_weight_value',
    'table' => 'node_data_field_weight',
    'field' => 'field_weight_value',
    'relationship' => 'none',
  ),
  'field_weight_label_value' => array(
    'label' => 'Weight label',
    'alter' => array(
      'alter_text' => 0,
      'text' => '[field_weight_label_value] ||',
      'make_link' => 0,
      'path' => '',
      'link_class' => '',
      'alt' => '',
      'prefix' => '',
      'suffix' => '',
      'target' => '',
      'help' => '',
      'trim' => 0,
      'max_length' => '',
      'word_boundary' => 1,
      'ellipsis' => 1,
      'html' => 0,
      'strip_tags' => 0,
    ),
    'empty' => '',
    'hide_empty' => 0,
    'empty_zero' => 0,
    'link_to_node' => 0,
    'label_type' => 'widget',
    'format' => 'default',
    'multiple' => array(
      'group' => 1,
      'multiple_number' => '',
      'multiple_from' => '',
      'multiple_reversed' => 0,
    ),
    'exclude' => 0,
    'id' => 'field_weight_label_value',
    'table' => 'node_data_field_weight_label',
    'field' => 'field_weight_label_value',
    'relationship' => 'none',
    'override' => array(
      'button' => 'Override',
    ),
  ),
  'field_technologienode_nid' => array(
    'label' => '',
    'alter' => array(
      'alter_text' => 0,
      'text' => '',
      'make_link' => 0,
      'path' => '',
      'link_class' => '',
      'alt' => '',
      'prefix' => '',
      'suffix' => '',
      'target' => '',
      'help' => '',
      'trim' => 0,
      'max_length' => '',
      'word_boundary' => 1,
      'ellipsis' => 1,
      'html' => 0,
      'strip_tags' => 0,
    ),
    'empty' => '',
    'hide_empty' => 0,
    'empty_zero' => 0,
    'link_to_node' => 0,
    'label_type' => 'none',
    'format' => 'plain',
    'multiple' => array(
      'group' => 1,
      'multiple_number' => '',
      'multiple_from' => '',
      'multiple_reversed' => 0,
    ),
    'exclude' => 0,
    'id' => 'field_technologienode_nid',
    'table' => 'node_data_field_technologienode',
    'field' => 'field_technologienode_nid',
    'relationship' => 'none',
  ),
));
$handler->override_option('arguments', array(
  'nid' => array(
    'default_action' => 'empty',
    'style_plugin' => 'default_summary',
    'style_options' => array(),
    'wildcard' => 'all',
    'wildcard_substitution' => 'All',
    'title' => '',
    'breadcrumb' => 'node/%1',
    'default_argument_type' => 'fixed',
    'default_argument' => '',
    'validate_type' => 'none',
    'validate_fail' => 'not found',
    'break_phrase' => 0,
    'not' => 0,
    'id' => 'nid',
    'table' => 'node',
    'field' => 'nid',
    'validate_user_argument_type' => 'uid',
    'validate_user_roles' => array(
      '2' => 0,
      '4' => 0,
      '3' => 0,
      '5' => 0,
    ),
    'relationship' => 'none',
    'default_options_div_prefix' => '',
    'default_argument_user' => 0,
    'default_argument_fixed' => '',
    'default_argument_php' => '',
    'validate_argument_node_type' => array(
      'article' => 0,
      'category' => 0,
      'family' => 0,
      'filter' => 0,
      'filter_type' => 0,
      'front' => 0,
      'line' => 0,
      'macromodel' => 0,
      'news' => 0,
      'news_category' => 0,
      'page' => 0,
      'prodvalcarac' => 0,
      'technocat' => 0,
      'technoline' => 0,
    ),
    'validate_argument_node_access' => 0,
    'validate_argument_nid_type' => 'nid',
    'validate_user_restrict_roles' => 0,
    'validate_argument_php' => '',
    'validate_argument_vocabulary' => array(),
    'validate_argument_type' => 'tid',
    'validate_argument_transform' => 0,
  ),
));
$handler->override_option('filters', array(
  'type' => array(
    'operator' => 'in',
    'value' => array(
      'macromodel' => 'macromodel',
    ),
    'group' => '0',
    'exposed' => FALSE,
    'expose' => array(
      'operator' => FALSE,
      'label' => '',
    ),
    'id' => 'type',
    'table' => 'node',
    'field' => 'type',
    'relationship' => 'none',
  ),
));
$handler->override_option('access', array(
  'type' => 'none',
));
$handler->override_option('cache', array(
  'type' => 'none',
));
$handler->override_option('items_per_page', 1);
$handler = $view->new_display('page', 'Page', 'page_1');
$handler->override_option('path', 'productcompare');
$handler->override_option('menu', array(
  'type' => 'none',
  'title' => '',
  'description' => '',
  'weight' => 0,
  'name' => 'navigation',
));
$handler->override_option('tab_options', array(
  'type' => 'none',
  'title' => '',
  'description' => '',
  'weight' => 0,
  'name' => 'navigation',
));


/******************************************************************/

?>
