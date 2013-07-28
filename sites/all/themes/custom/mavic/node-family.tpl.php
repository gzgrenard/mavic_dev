<?php
    $firstProduct = current($products_list);
    $item = menu_get_item($firstProduct['link']['href']);
    $itemMap = $item['map'][1];
    $nid = $itemMap->nid;
?>
<script type="text/javascript" >

    $(document).ready(function() {	
        $("#body-background").ezBgResize();	
		
        getSessionProductCompare('<?php echo $nid; ?>');
        checkSize();
    });
    var macromodels=new Array();
    var prefilter=new Array();
    var showCompare = false;
    var activeFamily = '<?php echo str_replace('node/', '', $breadcrumb[3]['link']['href']) ?>';
    var basePath ='<?php echo base_path() . $language; ?>'; 
    var itemtimer=new Array();
</script>
<div id="tabs" >
    <?php
    if (mb_strtolower($breadcrumb[1]['link']['title']) != mb_strtolower($breadcrumb[2]['link']['title']) && empty($discipline) || $isApparel) {
        require_once('node-family-tabs.tpl.php');
    }
    ?>
    <?php require_once('node-family-filters.tpl.php'); ?>

    <div id="content_gamme_items" >		
        <?php
        $i = 1;
        $col = 4;
        //Adds same item for debug purpose
        //$breadcrumb[3]['below'][11] = $breadcrumb[3]['below'][10] = $breadcrumb[3]['below'][5] = $breadcrumb[3]['below'][4] = $breadcrumb[3]['below']['50000 jantes aliage 453'];
        $ssc = false;
        $altium = false;
        foreach ($products_list as $product) :
            $item = menu_get_item($product['link']['href']);
            $itemMap = $item['map'][1];

            if (!empty($itemMap->field_sscnode[0]['nid']))
                $ssc = $itemMap->field_sscnode[0]['nid'];
            if (!empty($itemMap->field_altiumnode[0]['nid']))
                $altium = $itemMap->field_altiumnode[0]['nid'];
            $code = $itemMap->field_modelco;

            $model = node_load($itemMap->field_otherarticle[0]['nid']);
            $nid = $itemMap->nid;
            $displayAltColor = (count($product['assoc_color']) > 1) ? true : false;
            ?>
    <?php $class = 'product_item';
    if ($i % $col == 0) $class.=" last_row_item" ?>
            <div id="divProduct<?php echo $nid ?>" class="<?php echo $class ?>" onmouseover="overItem('<?php echo $nid ?>');" onmouseout="itemtimer[<?php echo $nid ?>]=setTimeout('outItem(\'<?php echo $nid ?>\')',0);" >	
                <div alt="<?php echo url($product['link']['href']); ?>" id="productImg<?php echo $nid ?>" class="product_image" onmouseover="overImage('<?php echo $nid ?>');" onmouseout="itemtimer[<?php echo $nid ?>]=setTimeout('outImage(\'<?php echo $nid ?>\')',0);" onclick="document.location.href='<?php echo url($product['link']['href']); ?>#<?php echo $model->title; ?>'">
                    <?php if (file_exists($product_path_sys . '/range/' . $model->title . '.jpg') && !$displayAltColor) : ?>
                        <img id="range<?php echo $model->title; ?>" alt="<?php print $itemMap->title ?>" name="<?php echo $model->title; ?>" src="<?php echo $product_path . '/range/' . $model->title . '.jpg'; ?>" />
                    <?php elseif (!$displayAltColor) : ?>
                        <img id="range<?php print $itemMap->title; ?>" alt="<?php print $itemMap->title ?>" name="<?php echo $model->title; ?>" src="<?php echo $product_path . '/range/default.jpg'; ?>" />
                    <?php endif; ?>
                    <?php if ($displayAltColor): ?>
                        <?php 
						$i=0;
						foreach ($product['assoc_color'] as $altColor): ?>
                            <?php $i++;
							if (file_exists($product_path_sys . '/colors/' . $altColor . '.jpg')) : ?>
								<?php $displayIt = ($i > 1) ? ' style="display: none;"' : ''; ?>
                                <img id="range<?php echo $altColor; ?>"<?php echo $displayIt; ?>  alt="<?php echo $altColor ?>" name="<?php echo $altColor ?>" src="<?php echo $product_path . '/range/' . $altColor . '.jpg'; ?>" />
                            <?php endif; ?>
        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="alter-range-choice">
                    <?php if ($displayAltColor): ?>
                        <?php $isFirst = true; ?>
                        <?php foreach ($product['assoc_color'] as $altColor): ?>
                            <?php if (file_exists($product_path_sys . '/colors/' . $altColor . '.jpg')) : ?>
                                <img class="alter-range-img<?php if ($isFirst) echo ' selected'; ?>" id="alter-range-<?php echo $altColor; ?>" onclick="rangeOtherColor('<?php echo $nid; ?>', '<?php echo $altColor; ?>')" src="<?php echo $product_path . '/thumbscolor/' . $altColor . '.jpg'; ?>" />
                            <?php endif; ?>
                            <?php $isFirst = false; ?>
        <?php endforeach; ?>
    <?php endif; ?>
                </div>
                <div id="item<?php echo $nid ?>" name="item<?php echo $nid ?>" class="name ">
                    <a class="helvetica" href="<?php echo url($product['link']['href']); ?>">
                        <?php
                        if (!empty($itemMap->field_sscnode[0]['nid']))
                            $img = '<img src="' . $theme_path . '/images/logos/ssc.gif" alt="' . t('Special Service Course. The Mavic SSC label products are developed in close collaboration with our top athletes. These products meet the strict performance demands of pro riders, and are made for pro cycling.') . '" />';
                        else
                            $img = '';

                        //notice : concatenate img tag and text for display issue with cufon
                        print $img . $itemMap->title;
                        ?>
                    </a>
                </div>
                <a id="hidder<?php echo $nid ?>" href="<?php echo url($product['link']['href']); ?>" class="hidder" ></a>

                    <?php if (!$itemMap->field_usp[0]['value'] == ''): ?>
                    <div id="altpop<?php echo $nid ?>" name="altpop<?php echo $nid ?>" class="altpop">
                        <?php echo ($itemMap->field_usp[0]['value']); ?>
                            <?php if ($itemMap->field_new_product[0]['value'] == 1) { ?>
                            - <span id="new_product"><?php echo t('new') ?></span>
                            <?php } ?>
                        <div class="sscaltiumAltpop" >
                            <?php
                            $imgs = array();
                            if (!empty($itemMap->field_sscnode[0]['nid']))
                                $imgs[] = '<img src="' . $theme_path . '/images/logos/ssc.gif" alt="' . t('Special Service Course. The Mavic SSC label products are developed in close collaboration with our top athletes. These products meet the strict performance demands of pro riders, and are made for pro cycling.') . '" />';
                            if (!empty($itemMap->field_altiumnode[0]['nid']))
                                $imgs[] = '<img src="' . $theme_path . '/images/logos/altium_gamme.gif" alt="' . t('The altium label identifies Mavic’s most progressive, innovative and technical softgoods.') . '" />';
                            echo implode('&nbsp;', $imgs);
                            ?>		
                        </div>
                    </div>
    <?php endif; ?>


                <script>
    									
                    //filter values
                    macromodels[<?php echo $nid ?>] = new Array();
    <?php
    //$tags = '0';
    /* ["field_filter_value"]=> array(2) { [0]=> array(1) { ["nid"]=> string(6) "275217" } [1]=> array(1) { ["nid"]=> string(6) "275245" } }

      foreach($itemMap->field_featurenode as $tag)
      if(isset($tag) && ($tag['nid'] != ''))$tags .= ','.$tag['nid'];
      foreach($itemMap->field_technologienode as $tag)
      if(isset($tag) && ($tag['nid'] != '')) $tags .= ','.$tag['nid'];
     */
    foreach ($itemMap->field_filter_value as $tag)
        if (isset($tag) && ($tag['nid'] != '')) {//$tags .= ','.$tag['nid'];
            //$filters = db_query('select distinct c.field_filter_value_list_nid c.nid from {content_field_filter_value_list} c WHERE c.field_filter_value_list_nid in ('.$tags.')');
            //while($tagx = db_fetch_array($filters)) {
            ?>
                                                                                    
            macromodels[<?php echo $nid ?>][<?php echo $tag['nid'] ?>] =  true;
        <?php } ?>
            //itemtimer for blinking ie
            itemtimer[<?php echo $nid ?>]='';
                </script>
            </div>
            <?php
            $i++;
        endforeach;
        ?>


    </div>
    <div class="clear"></div>
    <div id="sscaltiumBlock">
        <?php
        if ($ssc) {
            //$sscBody = db_result(db_query('select r.body from {node_revisions} r INNER JOIN {node} n using (vid) where n.type="prodvalcarac" and n.`language`="'.$language.'" and n.nid = '.$ssc));
            ?>
            <div class="ssc">
                <div class="sscaltiumImage" style="" ><img  src="<?php echo $theme_path ?>/images/logos/ssc.gif" alt="<?php echo t('Special Service Course. The Mavic SSC label products are developed in close collaboration with our top athletes. These products meet the strict performance demands of pro riders, and are made for pro cycling.'); ?>" /></div>
                <div class="sscaltiumContent" style="" ><?php echo t('Special Service Course. The Mavic SSC label products are developed in close collaboration with our top athletes. These products meet the strict performance demands of pro riders, and are made for pro cycling.'); ?></div>
            </div>
            <?php
        }

        if ($altium) {
            //$altiumBody = db_result(db_query('select r.body from {node_revisions} r INNER JOIN {node} n using (vid) where n.type="prodvalcarac" and n.`language`="'.$language.'" and n.nid = '.$altium));
            ?>
            <div class="altium" >
                <div class="sscaltiumImage" style="" ><img  src="<?php echo $theme_path ?>/images/logos/altium_gamme.gif" alt="<?php echo t('The altium label identifies Mavic’s most progressive, innovative and technical softgoods.'); ?>" /></div>
                <div class="sscaltiumContent" style="" ><?php echo t('The altium label identifies Mavic’s most progressive, innovative and technical softgoods.'); ?></div>
            </div>

            <?php
        }
        ?>
    </div>


</div>

<div id="altpopdisplayer" style="position:absolute;z-index: 3;" ></div>