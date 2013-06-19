<?php if($field_label[0]['value'] == 0) { ?>
	title : <?php print $title; ?><br>
	description : <?php print $node->content['body']['#value']; ?><br>
	img : <?php echo $technologies_path.'/'.$field_code[0]['value'].'.jpg'; ?><br>
<?php } ?>