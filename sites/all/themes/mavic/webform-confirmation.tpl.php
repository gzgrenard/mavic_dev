<script type="text/javascript" >
	$(document).ready(function() {	
		$("#body-background").ezBgResize();	
		checkSize();
	});
	
	<?php 
	if($node->type == 'contest') : ?>
		var _gaq = _gaq || [];
		_gaq.push(['_trackEvent', 'contest_<?php print $field_landing_omiture_global[0]['safe']; ?>', 'Register', '<?php print $lang; ?>']);
	<?php endif; ?>
</script>
<div class="webform-confirmation">
  <?php if ($confirmation_message): ?>
    <?php print $confirmation_message ?>
  <?php else: ?>
    <p><?php print t('Thank you, your submission has been received.'); ?></p>
  <?php endif; ?>
</div>
<div class="links">
  <a href="<?php print url('node/'. $node->nid) ?>"><?php print t('Go back to the form') ?></a>
</div>
