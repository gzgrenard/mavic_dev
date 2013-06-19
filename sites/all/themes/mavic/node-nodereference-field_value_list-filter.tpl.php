<?php 
	if ($referencing_node->field_multi[0]['value']) $fieldtype= 'checkbox';
	else $fieldtype='radiobox' ;
	
	//
	// to have english version for omniture
	//
	if($tnid != $nid) {
		$english_node = node_load($tnid);
		$english_referencing_node = node_load($referencing_node->tnid);
	} else {
		$english_node = $node;
		$english_referencing_node = $referencing_node;
	}
?>

<div class="filterfield" id="filterField<?php echo $nid; ?>" onclick="checkUncheckButtons('<?php echo $nid; ?>', '<?php echo $referencing_node->nid; ?>'); highlightMacroModels();omniture_click_filter(this, '<?php echo $english_referencing_node->title.':'.$english_node->title; ?>')"
	<?php 
		$desc = $field_description[0]['value'];
		if($desc != 'no' && $desc != 'iddesc' && $desc != ''){
	?> 
		onmouseover="overImage('filter_<?php echo $nid?>')" onmouseout="outImage('filter_<?php echo $nid?>')"
	<?php }?>
>
	<div class="bullet <?php echo $fieldtype?>" id="imgFilter<?php echo $nid; ?>">&nbsp;</div>
	<div class="label" >
		<?php echo $title; ?> 
		<script>
			filters['<?php echo $nid; ?>'] = false; 
		</script>
	</div>
</div>
		
<div class="altpop" id="altpopfilter_<?php echo $nid?>" >
	<?php echo $desc;	?>
</div>