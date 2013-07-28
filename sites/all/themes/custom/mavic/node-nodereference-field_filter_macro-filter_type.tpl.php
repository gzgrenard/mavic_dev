<?php 
if ($field_multi[0]['value']) $fieldtype= 'checkbox';
else $fieldtype='radiobox' ;
?>
<div onclick="show_hide_filters('#<?php echo $nid ?>');">
	<div class="filters_up_down"></div>
	<h1>
	<?php echo $title ?>
	</h1>
</div>
<img id="reset_<?php echo $nid ?>" alt="<?php echo t('reset this filter') ?>" src="/sites/default/themes/mavic/images/filters_reset.gif" onclick="resetfilters(<?php echo $nid ?>);"/>
<script>
filters = new Array();
pfilters = new Array();
</script>
<div id="<?php echo $nid ?>" class="allfilters">
<?php
 foreach($field_filter_value_list as $value):	?>
	<?php echo $value['view']; ?>
<?php endforeach; ?>
</div>
<script>
	filters_list[<?php echo $nid ?>]=[filters,'<?php echo $fieldtype; ?>'];
	if(pfilters.length>0) prefilter[<?php echo $nid ?>] = pfilters;
</script>
	
