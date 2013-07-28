<?php
$empty = true;
$listDeMesFiltreHTML = array();
//$i = 0;
$lastone = ' firstone';
$nextblock = '<div class="clear"></div><div class="blockfilter secondline">';
$blockend = '</div>';
foreach($field_filter_macro as $filter) 
{
	if( !empty($filter['nid']) )
	{
		$empty= false;
		$listDeMesFiltreHTML[] = (($i == 4)?$nextblock:"").'<div class="filter '.(($i == 0 || $i == 4)?$lastone:'').'">'.$filter['view'].'</div>'.(($i == 3)?$blockend:'');
        $i++;
    }
}
$max = count($listDeMesFiltreHTML) - 1;
if ($max!=3) {
	$listDeMesFiltreHTML[$max] = mb_ereg_replace("</div></div>","</div></div></div>",$listDeMesFiltreHTML[$max]);
}
if(!$empty):
    ?>
    <div class="clear"></div>
    <div style="position:relative;">
    </div>
    <div id="filters_content" >
        <div class="blockfilter">
            <script type="text/javascript">
                var filters_list = new Array(),filters,pfilters;
                var discFilterOff = false;
    <?php
    print (empty($discipline)) ? 'var filtPHide = false;' : 'var filtPHide = true;';
    ?>
            </script>
    <?php
			echo implode("\n",$listDeMesFiltreHTML);
    ?>
            <div class="clear"></div>

        </div>


<?php endif;?>
    <div class="clear"></div>
    <script type="text/javascript">
var t_compare = "<?php echo t('COMPARE')?>";
    </script>
    <div id="product_compare_placeholder"  >
        <div id="product_compare_block" >
		<a href="javascript: void(0)" id="product_compare_title" onclick="openProductCompare()" >
			 <?php echo t('COMPARE')?>
            </a>
        </div>
    </div>
</div>
    <div class="clear"></div>
<script type="text/javascript" src="/<?php echo drupal_get_path('module', 'productcompare'); ?>/js/jquery.dd.js"></script>
<link rel="stylesheet" type="text/css" href="/<?php echo drupal_get_path('module', 'productcompare'); ?>/js/dd.css" />

