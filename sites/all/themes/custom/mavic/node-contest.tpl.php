<?php 
		$idLangueDolist = array();
		$idLangueDolist['en'] = 15;
		$idLangueDolist['fr'] = 14;
		$idLangueDolist['de'] = 16;
		$idLangueDolist['it'] = 17;
		$idLangueDolist['es'] = 18;
		$idLangueDolist['ja'] = 19;
?>
<div class="webform node-contest-webform">
	<div style="float:right;" id="share-contest">
	<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox">
					<div class="custom_images">
					<a class="addthis_button_google_plusone" g:plusone:annotation="none">
						</a>
						<a class="addthis_button_twitter">
							<img src="<?php echo base_path().path_to_theme();?>/images/share/tweet.gif" height="14" border="0" alt="<?php print t('Share to Twitter'); ?>" />
						</a>
							<a class="addthis_button_facebook">
							<img src="<?php echo base_path().path_to_theme();?>/images/share/share_<?php print $lang; ?>.gif" height="14" border="0" alt="<?php print t('Share to Facebook'); ?>" />
						</a>
					</div>
				</div>
	            <script>
                var addthis_config = {
                    ui_language: '<?php echo $lang; ?>',
                    ui_click: true,
                    ui_use_css: true,
					data_track_clickback: false,
                    data_track_addressbar: false
                };
                var addthis_share = {
                    url_transforms : { clean: true, remove: ['intcmp'] }, 
                    templates: { twitter: '<?php print $title; ?> {{url}} @mavic' }
                };
								
            </script>

			<!-- AddThis Button END -->
			</div>
			
			<?php if (isset($_GET['contest'])) : ?>
				<script type="text/javascript">
					var _gaq = _gaq || [];
					_gaq.push(['_trackEvent', 'contest_<?php print $field_landing_omiture_global[0]['safe']; ?>', 'Register', '<?php print $lang; ?>']);
					
				</script>
				<span id="confirmation-message-contest">
					<?php print $webform['confirmation']; ?>
				</span>
				<?php if(isset($_GET['newsletterok'])) : ?>
					<script type='text/javascript'>
					_gaq.push(['_trackEvent', 'newslettersub_contest_<?php print $field_landing_omiture_global[0]['safe']; ?>', 'Register', '<?php print $lang; ?>']);
					</script>
				<?php endif; ?>
			<?php endif;?>
	<h1 class="helvetica"><?php print $title; ?></h1>
	
	<?php print $content; ?>
	<div id="mandatory">
	<span class="form-required" >*</span><?php echo t('mandatory fields');?>
	</div>
		<div style="float:right;" id="share-contest">
	<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox">
					<div class="custom_images">
					<a class="addthis_button_google_plusone" g:plusone:annotation="none">
						</a>
						<a class="addthis_button_twitter">
							<img src="<?php echo base_path().path_to_theme();?>/images/share/tweet.gif" height="14" border="0" alt="<?php print t('Share to Twitter'); ?>" />
						</a>
							<a class="addthis_button_facebook">
							<img src="<?php echo base_path().path_to_theme();?>/images/share/share_<?php print $lang; ?>.gif" height="14" border="0" alt="<?php print t('Share to Facebook'); ?>" />
						</a>
					</div>
				</div>
			<!-- AddThis Button END -->
			</div>

</div>
<script type="text/javascript" >
var formPrincipal;
var oDoList;
var bDoSubmit = false;
	$(document).ready(function() {	
		formPrincipal = $('.node-contest-webform > form.webform-client-form');
	
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
		
		
		<?php if(isset($_GET['contest'])): ?>
		$('#edit-actions').append($('#confirmation-message-contest'));
		<?php endif; ?>
		var inputMessages = $('#error-messages li');
		if(inputMessages.length == 0 ){inputMessages=$('#error-messages .error')}
		var treated = new Array();
		$('.webform-component:has(.error)').each(
			function(index)
			{				
				$(this).find('.form-item:first').append('<div class="errorMessage" >'+inputMessages[index].innerHTML+'</div>');	
			}
		)
	});
	
	skinSelect('edit-submitted-titre');
	skinSelect('edit-submitted-date-de-naissance-year');
	skinSelect('edit-submitted-date-de-naissance-month');
	skinSelect('edit-submitted-date-de-naissance-day');
	skinSelect('edit-submitted-pays');
	$("#edit-submit").val("<?php echo t('participate'); ?>");
	$('#webform-component-rules-accept').find(' > .form-item').find(' > label').html($('#mandatory')).css({'font-weight':'normal'});
	$('#webform-component-receive-newsletters').find(' > .form-item').find(' > label').html('');
	
function VerifForm (){
var error = false,item;

for(item in fields){
error |= !ControlChamp(item,fields[item]);
}
if (error) {
alert ("<?php print t('Please, check the fields in red') ?>");
} else {
	
	if($('#edit-submitted-receive-newsletters-1').is(':checked')) {
	//doList processing
	
	//get the practices list
	var aPractices = new Array();
	 if($('#edit-submitted-pratiques-1').is(':checked'))
		aPractices.push($('#edit-submitted-pratiques-1').val());
		
	if($('#edit-submitted-pratiques-2').is(':checked'))
		aPractices.push($('#edit-submitted-pratiques-2').val());
	
	if($('#edit-submitted-pratiques-3').is(':checked'))
		aPractices.push($('#edit-submitted-pratiques-3').val());
	
	if($('#edit-submitted-pratiques-4').is(':checked'))
		aPractices.push($('#edit-submitted-pratiques-4').val());
	
	if($('#edit-submitted-pratiques-5').is(':checked'))
		aPractices.push($('#edit-submitted-pratiques-5').val());
	
	if($('#edit-submitted-pratiques-6').is(':checked'))
		aPractices.push($('#edit-submitted-pratiques-6').val());
		
		
	var oMatch = {
						'do_field_24_7':$('#edit-submitted-e-mail').val(),
						'do_field_25_31':$('#edit-submitted-titre').val(),
						'do_field_27_2':$('#edit-submitted-nom').val(),
						'do_field_26_1':$('#edit-submitted-prenom').val(),
						'do_interest_4':<?php print $idLangueDolist[$lang]; ?>,
						'do_field_32_4':$('#edit-submitted-adresse-postale').val(),
						'do_field_28_6':$('#edit-submitted-code-postal').val(),
						'do_field_29_5':$('#edit-submitted-ville').val(),
						'do_field_31_36':$('#edit-submitted-pays').val(),
						'do_field_30_8':$('#edit-submitted-telephone').val(),
						'do_field_34_12':'consumer',
						'do_interest_3':aPractices,
						'do_ListId':'D8E',
						'do_IdSubscribe':'4',
						'do_field_37_13':'contest',
						'do_field_39_16':'<?php print $field_landing_omiture_global[0]['safe']; ?>',
						'do_redirect':'http://<?php echo $_SERVER['SERVER_NAME'];?><?php echo $_SERVER['REQUEST_URI']; ?>?newsletterok=1&contest=<?php print $field_landing_omiture_global[0]['safe']; ?>'
	};
	oDoList = oMatch;
	return true;
	} else {
		return !error;
	}
}
return !error;
}


function crossDomainPost(oElements) {
  // Add the iframe with a unique name
  var iframe = document.createElement("iframe");
  var uniqueString = "contestForm";
  document.body.appendChild(iframe);
  iframe.style.display = "none";
  iframe.contentWindow.name = uniqueString;
  iframe.id = uniqueString;
  // construct a form with hidden inputs, targeting the iframe
  var form = document.createElement("form");
  form.target = uniqueString;
  form.action = "http://form.dolist.net/sw/default.aspx";
  form.method = "POST";

  // repeat for each parameter
  
  for(var elt in oElements) {
	var input = document.createElement("input");
	input.type = "hidden";
	input.name = elt;
	input.value = oElements[elt];	
	form.appendChild(input);
  }
	
	var objDoc = getFrameDocument(uniqueString);

	if(objDoc.body) {
		objDoc.body.appendChild(form);
	} else {
		objDoc.appendChild(form);
	}
	form.submit();
	return true;
}

function getFrameDocument(idFrame)
{
  var oIframe = document.getElementById(idFrame);
  var oDoc = (oIframe.contentWindow || oIframe.contentDocument);
  if (oDoc.document) oDoc = oDoc.document;
  return oDoc;
}

function ControlChamp(name,typ){
var obj=$('#'+name),showerr=obj,regex,result = (obj.val() != '');

switch (typ){
case 'email':
	
regex = new RegExp('^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$');
result = regex.test(obj.val());
break;
case 'select':
showerr = obj.parent();
break;
case 'checkbox':
result = (obj.find("input:checked").length>0);
break;
}
if(result) showerr.removeClass("error");
else{
	showerr.addClass("error");
	var callControlChamp = function(){ControlChamp(name,typ);};
	obj.unbind('change', callControlChamp).change(callControlChamp);
}
return result;
}

function timeOutSubmit() {
	$('#edit-submit').css({"disabled":"disabled"});
	setTimeout("forceSubmit()",4000);
}
function forceSubmit() {
	bDoSubmit = true;
	formPrincipal.submit();
}

$(".webform-client-form").submit(function(){
		if(bDoSubmit == false) {
			if(VerifForm() == true) {
				if($('#edit-submitted-receive-newsletters-1').is(':checked')) {
					if(crossDomainPost(oDoList) == true) {
						bDoSubmit = true;
						timeOutSubmit();
						return false;
					} else {
						return false;
					}
				} else {
					return true;
				}
			} else {
				return false;
			}
		} else {
			return true;
		}
	});

</script>