<?php
// $Id: views-view-fields.tpl.php,v 1.6 2008/09/24 22:48:21 merlinofchaos Exp $
/**
 * @file views-view-fields.tpl.php
 * Default simple view template to all the fields as a row.
 *
 * - $view: The view in use.
 * - $fields: an array of $field objects. Each one contains:
 *   - $field->content: The output of the field.
 *   - $field->raw: The raw data for the field, if it exists. This is NOT output safe.
 *   - $field->class: The safe class id to use.
 *   - $field->handler: The Views field handler object controlling this field. Do not use
 *     var_export to dump this object, as it can't handle the recursion.
 *   - $field->inline: Whether or not the field should be inline.
 *   - $field->inline_html: either div or span based on the above flag.
 *   - $field->separator: an optional separator that may appear before a field.
 * - $row: The raw result object from the query, with all data it fetched.
 *
 * @ingroup views_templates
 */
?>
<div class="pcblockimage" data-title="<?php echo $fields['nid']->content; ?>">
    <img 
        class="img"
        id="img_<?php echo $fields['nid']->content; ?>"
        src="<?php echo '/sites/default/files/products/normal/' . $fields['field_otherarticle_nid']->content . '.jpg'; ?>"
        style=""
        />
    <!--select-->
    <div class="linkcontainer">
        <?php
        echo $fields['view_node']->content; //->content;
        ?>
    </div>
</div>
<?php
global $weightlabels;
global $weightvalues;
if (!empty($weightlabels[0])):
    ?>
    <div class="pcblockweight" data-title="<?php echo $fields['nid']->content; ?>">
        <div class="infoblock">
            <h2><?php echo t('Weight') ?></h2>
            <?php foreach ($weightlabels as $i => $label): ?>
                <?php echo t($label . ':') ?> <?php echo $weightvalues[$i] ?><br/>
            <?php endforeach; ?>
        </div>
    </div>			
<?php endif; ?>
<?php /* * **
 * FEATURES
 * 
 * * */ ?>
<?php
$filtersName = array("'Filters'", "'Filtres'", "'Filtri'", "'フィルターor分類'", "'Filtros'");
foreach ($fields['field_featurenode_nid']->handler->field_values as $_nid) {

    // Get all NID for feature list
    $tabNid = array();
    foreach ($_nid as $f) {
        $tabNid[] = $f['nid'];
    }
}

// Get Category / Title / Weight for each feature
$query = db_query('SELECT ctp.field_type_value, n.title, ctp.field_poids_value
                        FROM {node} n INNER JOIN {content_type_prodvalcarac} ctp USING (nid)
                        WHERE n.nid IN (' . implode(',', $tabNid) . ') AND ctp.field_type_value NOT IN (' . implode(',', $filtersName) . ')');

// Get data on a structured array
$tabFeature = array();
while ($row = db_fetch_array($query)) {
    if (isset($tabFeature[$row['field_type_value']])) {
        $tabFeature[$row['field_type_value']]['list'][] = $row['title'];
    } else {
        $tabFeature[$row['field_type_value']] = array();
        $tabFeature[$row['field_type_value']]['weight'] = floor($row['field_poids_value'] / 100);
        $tabFeature[$row['field_type_value']]['list'][] = $row['title'];
    }
}

foreach ($tabFeature as $category => $feature):
    ?>
    <div data-title="<?php echo md5($category) ?>|<?php echo $fields['nid']->content; ?>|<?php echo $feature['weight'] ?>" id="feature<?php echo $category ?><?php echo $fields['nid']->content; ?>" class="pcblockfeatures" >
        <div class="infoblock">
            <h2><?php echo $category ?></h2>
            <ul>
                <?php foreach ($feature['list'] as $featureItem): ?>
                    <li><?php echo $featureItem ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php
    $i++;
endforeach;
?>

<?php /* * **
 * TECHNOLOGIES
 * 
 * * */ ?>
<div class="pcblocktechnos" data-title="<?php echo $fields['nid']->content; ?>">
    <div class="infoblock">
        <h2><?php echo t('TECHNOLOGIES') ?></h2>

        <ul class="technologies">
            <?php
            foreach ($fields['field_technologienode_nid']->handler->field_values as $_nid)
                foreach ($_nid as $f) { {
                        $technologies[] = node_load($f['nid']);
                    }
                }
            foreach ($technologies as $i => $techno):
                ?>
                <li>

                    <?php if (file_exists($technologies_path_sys . '/logos/' . $techno->field_feature_codes[0]['value'] . '.jpg')): ?>
                        <img src="<?php echo $technologies_path . '/logos/' . $techno->field_feature_codes[0]['value'] . '.jpg'; ?>" width="30px" class="picto"  alt="" />	 	
                    <?php else: ?>
                        <div class="techno_nopicto"></div>
                    <?php endif; ?>	
                    <?php echo $techno->title ?>

                </li>
                <?php
            endforeach;
            ?>
        </ul>
    </div>
</div>
