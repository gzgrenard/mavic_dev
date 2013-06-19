<?php 
	if(!empty($breadcrumb[0]['link']['title'])) {
		switch($node->type) {
			case 'macromodel' :
				require('breadcrumb-macromodel.tpl.php');
			break;
			default :
			break;
		} 
	
	} 
?>
