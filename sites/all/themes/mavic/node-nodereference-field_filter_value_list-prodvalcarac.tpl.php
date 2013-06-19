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
<div class="filterfield" id="filterField<?php echo $nid; ?>" onclick="checkUncheckButtons('<?php echo $nid; ?>', '<?php echo $referencing_node->nid; ?>'); highlightMacroModels();omniture_click_filter(this, '<?php echo mb_ereg_replace("'",'',mb_ereg_replace('"','',$english_referencing_node->title.':'.$english_node->field_filter_title[0]['value'])); ?>')"
	<?php 
		$desc = $node->content['body']['#value'];//$field_description[0]['value'];
		if($desc != 'no' && $desc != 'iddesc' && $desc != ''){
	?> 
		onmouseover="overImage('filter_<?php echo $nid?>')" onmouseout="outImage('filter_<?php echo $nid?>')"
	<?php }?>
>
	<div class="bullet <?php echo $fieldtype?>" id="imgFilter<?php echo $nid; ?>">&nbsp;</div>
	<div class="label" >
		<?php echo $field_filter_title[0]['value'] ?> 
		<script>
			filters['<?php echo $nid; ?>'] = false;<?php
				if(strpos(','.$_GET['pf'].',',','.$nid.',') !== FALSE) echo "\npfilters.push(".$nid.");";
			?>
		</script>
<?php 
	//check discipline filter if on discipline sub-domain
	$jsfunc = '';
	if((strtolower($english_node->field_filter_title[0]['value']) == 'mtb') && ($discipline == 'road') ) {
		$jsfunc = '			var cfEl = '.$nid.';
							var mtbcfNid = "'.$nid.'";
							var cfMtb = true;
							discFilterOff = true;';
	} else if ((strtolower($english_node->field_filter_title[0]['value']) == 'mtb') && ($discipline != 'road') ){
		$jsfunc = '			var cfEl = '.$nid.';
							var cfMtb = false;
							discFilterOff = true;';
	} else if ((strtolower($english_node->field_filter_title[0]['value']) == 'road') && ($discipline == 'road') ){
		$jsfunc = '			var roadcfNid = "'.$nid.'";
							var roadcfRNid = "'.$referencing_node->nid.'";';
	}
	
?>
	<script>
	<?php print $jsfunc;?>
	</script>
	</div>
</div>
<div class="altpop" id="altpopfilter_<?php echo $nid?>" >
	<?php echo $desc;	?>
</div>