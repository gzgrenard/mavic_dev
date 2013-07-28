<?php 
	if(strpos($_REQUEST['q'],'distributor') !== false) {
		$distrib = 'distributor';
		$classe_distrib = 'active';
		$classe_sis = '';
		$classe_shop = '';
		$script = '';
	} else if (strpos($_REQUEST['q'],'shopinshop') !== false) {
		$distrib = 'shopinshop';
		$classe_distrib = '';
		$classe_sis = 'active';
		$classe_shop = '';
		$script = 'sis_initialize();';
	} else {
		$distrib = 'shopfinder';
		$classe_distrib = '';
		$classe_shop = 'active';
		$classe_sis ='';
		$script = 'sf_initialize();';
	}


?>
<script type="text/javascript" >
	$(document).ready(function() {	
		$("#body-background").ezBgResize();	
		checkSize();
		<?php echo $script; ?>
	});
	var shopfinderpage = true;
</script>

<div id="tabs">
	<?php
		include('node-shopfinder_cat-tabs.tpl.php');
		include($distrib.'.php');
		
	?>
</div>

