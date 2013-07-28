<script type="text/javascript" >
	$(document).ready(function() {	
		$("#body-background").ezBgResize();	
		showMoreNews(6);
		checkSize();
		$('#main_content').css('height','auto');
	});
	
</script>

<div id="tabs" class="news-page">
	<?php require_once('node-news-tabs.tpl.php'); ?>
	<?php print $rows; ?>
</div>


