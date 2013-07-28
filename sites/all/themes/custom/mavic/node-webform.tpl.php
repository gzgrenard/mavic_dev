<script type="text/javascript" >
	$(document).ready(function() {	
		$("#body-background").ezBgResize();	
		checkSize();
		/* move * marks */
		$('.webform-component').find('label').each(
			function(index) {
				if($(this).find('input').length==0)
				{
					this.innerHTML = this.innerHTML.replace(':','');
				}
			}
		);
		
		/*handle error messages*/
		//var webFormComponents = $('.webform-component:has(.error)');
		var inputMessages = $('#error-messages li');
		if(inputMessages.length == 0 ){inputMessages=$('#error-messages .error')}
		var treated = new Array();
		$('.webform-component:has(.error)').each(
			function(index)
			{				
				$(this).find('.form-item:first').append('<div class="errorMessage" >'+inputMessages[index].innerHTML+'</div>');	
			}
		)
		$('#edit-actions').prepend($('#mandatory'));
		
	});
	
	//skinSelect('webform-component-country');
</script>
<div id="mandatory">
<span class="form-required" >*</span><?php echo t('Mandatory fields');?>
</div>
<div class="webform">
	<h1 class="helvetica"><?php print $title; ?></h1>
	<?php print $content; ?>
</div>
