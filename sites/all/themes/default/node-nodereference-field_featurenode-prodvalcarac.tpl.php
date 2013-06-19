<?php if($field_label[0]['value'] == 0) { 
	global $typeTechno;
	$typeTechno[] = $field_type[0]['value'];
?>
	title : <?php print check_plain($title); ?><br>
	description : <?php print check_plain($node->content['body']['#value']); ?><br>
	img : <?php echo $technologies_path.'/'.$field_code[0]['value'].'.jpg'; ?><br>
<?php } ?>