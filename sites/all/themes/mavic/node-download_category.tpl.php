<script type="text/javascript" >
	$(document).ready(function() {
		$("#body-background").ezBgResize();
		checkSize();
	});
</script>
<div id="tabs">
<?php
function replace_special_char($nom){
	return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $nom); 
}
function array_sort($array, $on) 
{ 
  $new_array = array(); 
  $sortable_array = array(); 

  if (count($array) > 0) { 
	  foreach ($array as $k => $v) { 
		  if (is_array($v)) { 
			  foreach ($v as $k2 => $v2) { 
				  if ($k2 == $on) { 
					  $sortable_array[$k] = replace_special_char($v2); 
				  } 
			  } 
		  } else { 
			  $sortable_array[$k] = replace_special_char($v); 
		  }
	  } 

      asort($sortable_array);

	  foreach($sortable_array as $k => $v) { 
		  $new_array[] = $array[$k];
	  }
  } 
  return $new_array; 
} 

?>
<?php
global $language;
$idLangueDolist = array();
$idLangueDolist['en'] = 15;
$idLangueDolist['fr'] = 14;
$idLangueDolist['de'] = 16;
$idLangueDolist['it'] = 17;
$idLangueDolist['es'] = 18;
$idLangueDolist['ja'] = 19;
?>
<?php 
$visitoremail = isset($_POST['adress'])?htmlspecialchars($_POST['adress']):'';
?>

<?php
	require_once('node-download_category-tabs.tpl.php');
?>

	<div id="category_download_content">
		<?php  if($node->field_download_cat_newsletter[0]['value'] == 1) :?>
		<h2 class="helvetica"><?php print t('download the catalog'); ?></h2>
		<?php endif ?>
		<?php
			foreach($field_download as $download) {
		?>
				<?php echo $download['view']; ?>
		<?php
			}
		?>
		<div class="clear"></div>
		<?php if(!empty($field_download_archive[0]['view'])) { ?>
			<div class="archive_button">
				<a class="button_view" href="javascript: void(0);" title="<?php echo t('archive'); ?> <?php echo $title; ?>">
					<?php echo t('archive'); ?>
				</a>
			</div>
			<div class="archive_files">
				<?php foreach($field_download_archive as $download) { ?>
						<?php echo $download['view']; ?>
				<?php } ?>
			</div>
		<?php } 
		if($node->field_download_cat_newsletter[0]['value'] == 1){
		?>

<script type="text/javascript" >
function VerifForm (){
var error = false,fields = {email:'email',titre:'select',firstname:'text',lastname:'text',zipcode:'text',city:'text',country:'select',interest:'checkbox',confirmation:'checkbox'},item;
for(item in fields){
error |= !ControlChamp(item,fields[item]);
}
if (error) {
alert ("<?php print t('Please, check the fields in red') ?>");
} else {
	//GA tracking
	_gaq.push(['_trackEvent', 'Newsletter', 'Register', '<?php print $lang; ?>']);
}
return !error;
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
</script>
<div>
<div class="webform newsletter_download">
	<h2 class="helvetica"><?php print t('Would you like to receive updates about products ?') ?><br /><?php print t('Sign up for mavic updates') ?></h2>
	<span class="download_subsc_subti"><?php print t('(once per month maximum)') ?></span>
	<form NAME='AbonnementDOLIST' METHOD='post' action='http://form.dolist.net/sw/default.aspx' accept-charset='UTF-8' id="webform-client" class="webform-client-form">
<input type="hidden" name='do_ListId' value='D8E'>
<input type="hidden" name="do_IdSubscribe" value="4">
<input type="hidden" id="customstr3" Name="do_field_37_13" value="website">
<input type="hidden" id="customstr4" Name="do_field_39_16" value="mavic.com_download">
<input type="hidden" name='do_redirect' value="http://www.mavic.com/<?php print $language->language ?>?newsletterok=1">
<div class="webform-component-fieldset">
<div class="form-item">
 <label for="email"><?php print t('e-mail')?> <span class="form-required" title="<?php print t('This field is required.') ?>.">*</span></label>
 <input type="Text" id="email" Name="do_field_24_7" size="60" class="form-text required" value="<?php print $visitoremail ?>">
</div>
<div class="form-item">
<label for="titre"><?php print t('title') ?> <span class="form-required" title="<?php print t('This field is required.') ?>.">*</span></label>
<select id="titre" name='do_field_25_31' class="form-select required customselect">
<option value="">- <?php print t('choose') ?> -</option>
<option value="1"><?php print t('Mrs.') ?></option>
<option value="2"><?php print t('Miss') ?></option>
<option value="3"><?php print t('Mr.') ?></option>
</select>
</div>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
	skinSelect("titre");
//--><!]]>
</script>
<div class="form-item">
 <label for="firstname"><?php print t('first name')?> <span class="form-required" title="<?php print t('This field is required.') ?>.">*</span></label>
 <input type="Text" id="firstname" Name="do_field_26_1" size="60" maxlength="64" class="form-text required">
</div>
<div class="form-item">
 <label for="lastname"><?php print t('last name')?> <span class="form-required" title="<?php print t('This field is required.') ?>.">*</span></label>
 <input type="Text" id="lastname" Name="do_field_27_2" size="60" maxlength="64" class="form-text required">
</div>
<div class="form-item">
 <label for="address1"><?php print t('postal address')?></label>
 <input type="Text" id="address1" Name="do_field_32_4" size="60" maxlength="128" class="form-text">
</div>
<div class="form-item">
 <label for="zipcode"><?php print t('zip code')?> <span class="form-required" title="<?php print t('This field is required.') ?>.">*</span></label>
 <input type="Text" id="zipcode" Name="do_field_28_6" size="20" maxlength="16" class="form-text required">
</div>
<div class="form-item">
 <label for="city"><?php print t('city')?> <span class="form-required" title="<?php print t('This field is required.') ?>.">*</span></label>
 <input type="Text" id="city" Name="do_field_29_5" size="60" maxlength="64" class="form-text required">
</div>
<div class="form-item" id="edit-submitted-pays-wrapper">
 <label for="country"><?php print t('country') ?> <span class="form-required" title="<?php print t('This field is required.') ?>.">*</span></label>
<select id="country" name='do_field_31_36' class="form-select required customselect"><option value="">- <?php print t('choose') ?> -</option><?php
$countries = array(array("id" => 660, "name" =>  t ('Afghanistan')),
array("id" => 70, "name" =>  t ('Albania')),
array("id" => 208, "name" =>  t ('Algeria')),
array("id" => 43, "name" =>  t ('Andorra')),
array("id" => 330, "name" =>  t ('Angola')),
array("id" => 446, "name" =>  t ('Anguilla')),
array("id" => 459, "name" =>  t ('Antigua And Barbuda')),
array("id" => 528, "name" =>  t ('Argentina')),
array("id" => 77, "name" =>  t ('Armenia')),
array("id" => 474, "name" =>  t ('Aruba')),
array("id" => 800, "name" =>  t ('Australia')),
array("id" => 38, "name" =>  t ('Austria')),
array("id" => 78, "name" =>  t ('Azerbaijan')),
array("id" => 453, "name" =>  t ('Bahamas')),
array("id" => 640, "name" =>  t ('Bahrain')),
array("id" => 666, "name" =>  t ('Bangladesh')),
array("id" => 469, "name" =>  t ('Barbados')),
array("id" => 73, "name" =>  t ('Belarus')),
array("id" => 2, "name" =>  t ('Belgium')),
array("id" => 421, "name" =>  t ('Belize')),
array("id" => 284, "name" =>  t ('Benin')),
array("id" => 413, "name" =>  t ('Bermuda')),
array("id" => 675, "name" =>  t ('Bhutan')),
array("id" => 516, "name" =>  t ('Bolivia, Plurinational State Of')),
array("id" => 93, "name" =>  t ('Bosnia And Herzegovina')),
array("id" => 391, "name" =>  t ('Botswana')),
array("id" => 508, "name" =>  t ('Brazil')),
array("id" => 703, "name" =>  t ('Brunei Darussalam')),
array("id" => 68, "name" =>  t ('Bulgaria')),
array("id" => 236, "name" =>  t ('Burkina Faso')),
array("id" => 328, "name" =>  t ('Burundi')),
array("id" => 302, "name" =>  t ('Cameroon')),
array("id" => 404, "name" =>  t ('Canada')),
array("id" => 247, "name" =>  t ('Cape Verde')),
array("id" => 463, "name" =>  t ('Cayman Islands')),
array("id" => 306, "name" =>  t ('Central African Republic')),
array("id" => 244, "name" =>  t ('Chad')),
array("id" => 512, "name" =>  t ('Chile')),
array("id" => 720, "name" =>  t ('China')),
array("id" => 480, "name" =>  t ('Colombia')),
array("id" => 375, "name" =>  t ('Comoros')),
array("id" => 318, "name" =>  t ('Congo')),
array("id" => 322, "name" =>  t ('Congo, The Democratic Republic Of The')),
array("id" => 436, "name" =>  t ('Costa Rica')),
array("id" => 272, "name" =>  t ('Côte D\'Ivoire')),
array("id" => 92, "name" =>  t ('Croatia')),
array("id" => 448, "name" =>  t ('Cuba')),
array("id" => 600, "name" =>  t ('Cyprus')),
array("id" => 61, "name" =>  t ('Czech Republic')),
array("id" => 8, "name" =>  t ('Denmark')),
array("id" => 338, "name" =>  t ('Djibouti')),
array("id" => 460, "name" =>  t ('Dominica')),
array("id" => 456, "name" =>  t ('Dominican Republic')),
array("id" => 220, "name" =>  t ('Egypt')),
array("id" => 428, "name" =>  t ('El Salvador')),
array("id" => 310, "name" =>  t ('Equatorial Guinea')),
array("id" => 53, "name" =>  t ('Estonia')),
array("id" => 334, "name" =>  t ('Ethiopia')),
array("id" => 529, "name" =>  t ('Falkland Islands (Malvinas)')),
array("id" => 25, "name" =>  t ('Faroe Islands')),
array("id" => 815, "name" =>  t ('Fiji')),
array("id" => 32, "name" =>  t ('Finland')),
array("id" => 1, "name" =>  t ('France')),
array("id" => 496, "name" =>  t ('French Guiana')),
array("id" => 822, "name" =>  t ('French Polynesia')),
array("id" => 314, "name" =>  t ('Gabon')),
array("id" => 252, "name" =>  t ('Gambia')),
array("id" => 76, "name" =>  t ('Georgia')),
array("id" => 4, "name" =>  t ('Germany')),
array("id" => 276, "name" =>  t ('Ghana')),
array("id" => 44, "name" =>  t ('Gibraltar')),
array("id" => 9, "name" =>  t ('Greece')),
array("id" => 406, "name" =>  t ('Greenland')),
array("id" => 473, "name" =>  t ('Grenada')),
array("id" => 458, "name" =>  t ('Guadeloupe')),
array("id" => 416, "name" =>  t ('Guatemala')),
array("id" => 260, "name" =>  t ('Guinea')),
array("id" => 257, "name" =>  t ('Guinea-Bissau')),
array("id" => 488, "name" =>  t ('Guyana')),
array("id" => 452, "name" =>  t ('Haiti')),
array("id" => 45, "name" =>  t ('Holy See (Vatican City State)')),
array("id" => 424, "name" =>  t ('Honduras')),
array("id" => 740, "name" =>  t ('Hong Kong')),
array("id" => 64, "name" =>  t ('Hungary')),
array("id" => 24, "name" =>  t ('Iceland')),
array("id" => 664, "name" =>  t ('India')),
array("id" => 700, "name" =>  t ('Indonesia')),
array("id" => 616, "name" =>  t ('Iran')),
array("id" => 612, "name" =>  t ('Iraq')),
array("id" => 7, "name" =>  t ('Ireland')),
array("id" => 624, "name" =>  t ('Israel')),
array("id" => 5, "name" =>  t ('Italy')),
array("id" => 464, "name" =>  t ('Jamaica')),
array("id" => 732, "name" =>  t ('Japan')),
array("id" => 628, "name" =>  t ('Jordan')),
array("id" => 79, "name" =>  t ('Kazakhstan')),
array("id" => 346, "name" =>  t ('Kenya')),
array("id" => 812, "name" =>  t ('Kiribati')),
array("id" => 724, "name" =>  t ('Korea, Democratic People\'S Republic Of')),
array("id" => 728, "name" =>  t ('Korea, Republic Of')),
array("id" => 636, "name" =>  t ('Kuwait')),
array("id" => 83, "name" =>  t ('Kyrgyzstan')),
array("id" => 684, "name" =>  t ('Lao People\'S Democratic Republic')),
array("id" => 54, "name" =>  t ('Latvia')),
array("id" => 604, "name" =>  t ('Lebanon')),
array("id" => 395, "name" =>  t ('Lesotho')),
array("id" => 268, "name" =>  t ('Liberia')),
array("id" => 216, "name" =>  t ('Libya')),
array("id" => 37, "name" =>  t ('Liechtenstein')),
array("id" => 55, "name" =>  t ('Lithuania')),
array("id" => 23, "name" =>  t ('Luxembourg')),
array("id" => 743, "name" =>  t ('Macao')),
array("id" => 96, "name" =>  t ('Macedonia')),
array("id" => 370, "name" =>  t ('Madagascar')),
array("id" => 386, "name" =>  t ('Malawi')),
array("id" => 701, "name" =>  t ('Malaysia')),
array("id" => 667, "name" =>  t ('Maldives')),
array("id" => 232, "name" =>  t ('Mali')),
array("id" => 46, "name" =>  t ('Malta')),
array("id" => 824, "name" =>  t ('Marshall Islands')),
array("id" => 462, "name" =>  t ('Martinique')),
array("id" => 228, "name" =>  t ('Mauritania')),
array("id" => 373, "name" =>  t ('Mauritius')),
array("id" => 377, "name" =>  t ('Mayotte')),
array("id" => 412, "name" =>  t ('Mexico')),
array("id" => 823, "name" =>  t ('Micronesia, Federated States Of')),
array("id" => 74, "name" =>  t ('Moldova, Republic Of')),
array("id" => 825, "name" =>  t ('Monaco')),
array("id" => 716, "name" =>  t ('Mongolia')),
array("id" => 204, "name" =>  t ('Morocco')),
array("id" => 366, "name" =>  t ('Mozambique')),
array("id" => 676, "name" =>  t ('Myanmar')),
array("id" => 389, "name" =>  t ('Namibia')),
array("id" => 803, "name" =>  t ('Nauru')),
array("id" => 672, "name" =>  t ('Nepal')),
array("id" => 3, "name" =>  t ('Netherlands')),
array("id" => 809, "name" =>  t ('New Caledonia')),
array("id" => 804, "name" =>  t ('New Zealand')),
array("id" => 432, "name" =>  t ('Nicaragua')),
array("id" => 240, "name" =>  t ('Niger')),
array("id" => 288, "name" =>  t ('Nigeria')),
array("id" => 28, "name" =>  t ('Norway')),
array("id" => 649, "name" =>  t ('Oman')),
array("id" => 662, "name" =>  t ('Pakistan')),
array("id" => 442, "name" =>  t ('Panama')),
array("id" => 801, "name" =>  t ('Papua New Guinea')),
array("id" => 520, "name" =>  t ('Paraguay')),
array("id" => 504, "name" =>  t ('Peru')),
array("id" => 708, "name" =>  t ('Philippines')),
array("id" => 813, "name" =>  t ('Pitcairn')),
array("id" => 60, "name" =>  t ('Poland')),
array("id" => 10, "name" =>  t ('Portugal')),
array("id" => 455, "name" =>  t ('Puerto Rico')),
array("id" => 644, "name" =>  t ('Qatar')),
array("id" => 372, "name" =>  t ('Réunion')),
array("id" => 66, "name" =>  t ('Romania')),
array("id" => 75, "name" =>  t ('Russian Federation')),
array("id" => 324, "name" =>  t ('Rwanda')),
array("id" => 329, "name" =>  t ('Saint Helena, Ascension And Tristan Da Cunha')),
array("id" => 449, "name" =>  t ('Saint Kitts And Nevis')),
array("id" => 465, "name" =>  t ('Saint Lucia')),
array("id" => 408, "name" =>  t ('Saint Pierre And Miquelon')),
array("id" => 467, "name" =>  t ('Saint Vincent And The Grenadines')),
array("id" => 819, "name" =>  t ('Samoa')),
array("id" => 311, "name" =>  t ('Sao Tome And Principe')),
array("id" => 632, "name" =>  t ('Saudi Arabia')),
array("id" => 248, "name" =>  t ('Senegal')),
array("id" => 94, "name" =>  t ('Serbia')),
array("id" => 355, "name" =>  t ('Seychelles')),
array("id" => 264, "name" =>  t ('Sierra Leone')),
array("id" => 706, "name" =>  t ('Singapore')),
array("id" => 478, "name" =>  t ('Sint Maarten (Dutch Part)')),
array("id" => 63, "name" =>  t ('Slovakia')),
array("id" => 91, "name" =>  t ('Slovenia')),
array("id" => 806, "name" =>  t ('Solomon Islands')),
array("id" => 342, "name" =>  t ('Somalia')),
array("id" => 388, "name" =>  t ('South Africa')),
array("id" => 11, "name" =>  t ('Spain')),
array("id" => 669, "name" =>  t ('Sri Lanka')),
array("id" => 224, "name" =>  t ('Sudan')),
array("id" => 492, "name" =>  t ('Suriname')),
array("id" => 393, "name" =>  t ('Swaziland')),
array("id" => 30, "name" =>  t ('Sweden')),
array("id" => 36, "name" =>  t ('Switzerland')),
array("id" => 608, "name" =>  t ('Syrian Arab Republic')),
array("id" => 736, "name" =>  t ('Taiwan, Province Of China')),
array("id" => 82, "name" =>  t ('Tajikistan')),
array("id" => 352, "name" =>  t ('Tanzania, United Republic Of')),
array("id" => 680, "name" =>  t ('Thailand')),
array("id" => 280, "name" =>  t ('Togo')),
array("id" => 817, "name" =>  t ('Tonga')),
array("id" => 472, "name" =>  t ('Trinidad And Tobago')),
array("id" => 212, "name" =>  t ('Tunisia')),
array("id" => 52, "name" =>  t ('Turkey')),
array("id" => 80, "name" =>  t ('Turkmenistan')),
array("id" => 454, "name" =>  t ('Turks And Caicos Islands')),
array("id" => 807, "name" =>  t ('Tuvalu')),
array("id" => 350, "name" =>  t ('Uganda')),
array("id" => 72, "name" =>  t ('Ukraine')),
array("id" => 647, "name" =>  t ('United Arab Emirates')),
array("id" => 6, "name" =>  t ('United Kingdom')),
array("id" => 400, "name" =>  t ('United States')),
array("id" => 524, "name" =>  t ('Uruguay')),
array("id" => 81, "name" =>  t ('Uzbekistan')),
array("id" => 816, "name" =>  t ('Vanuatu')),
array("id" => 484, "name" =>  t ('Venezuela')),
array("id" => 690, "name" =>  t ('Viet Nam')),
array("id" => 461, "name" =>  t ('Virgin Islands, British')),
array("id" => 811, "name" =>  t ('Wallis And Futuna')),
array("id" => 653, "name" =>  t ('Yemen')),
array("id" => 378, "name" =>  t ('Zambia')),
array("id" => 382, "name" =>  t ('Zimbabwe')));
$countries = array_sort($countries,"name");
foreach($countries as $country){
	print '<option value="'.$country["id"].'">'.$country["name"].'</option>\n';
}
?>
<option value="999"><?php echo t ('other country') ?></option>
</select>
</div>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
	skinSelect("country");
//--><!]]>
</script>
</div>
<div class="form-item">
 <label for="phone"><?php print t('phone')?></label>
 <input type="Text" id="phone" Name="do_field_30_8" size="60" maxlength="32" class="form-text">
</div>
<div class="webform-component-fieldset">
<div class="form-item">
 <label><?php print t('practice(s)') ?> <span class="form-required" title="<?php print t('This field is required.') ?>.">*</span></label>
 <div class="form-checkboxes" id="interest">
 <div class="form-item">
 <label class="option" for="interest-1"><input type="checkbox" name="do_interest_3" id="interest-1" value="1"   class="form-checkbox" /><?php echo t ('road') ?></label>
</div>
<div class="form-item">
 <label class="option" for="interest-3"><input type="checkbox" name="do_interest_3" id="interest-3" value="7"   class="form-checkbox" /><?php echo t ('cross-country') ?></label>
</div>
<div class="form-item">
 <label class="option" for="interest-7"><input type="checkbox" name="do_interest_3" id="interest-7" value="11"   class="form-checkbox" /><?php echo t ('triathlon') ?></label>
</div>
<div class="form-item">
 <label class="option" for="interest-5"><input type="checkbox" name="do_interest_3" id="interest-5" value="9"   class="form-checkbox" /><?php echo t ('enduro') ?></label>
</div>
<div class="form-item">
 <label class="option" for="interest-8"><input type="checkbox" name="do_interest_3" id="interest-8" value="12"   class="form-checkbox" /><?php echo t ('track') ?></label>
</div>
<div class="form-item">
 <label class="option" for="interest-6"><input type="checkbox" name="do_interest_3" id="interest-6" value="10"   class="form-checkbox" /><?php echo t ('extreme MTB') ?></label>
</div>
</div>
</div>
<div class="clear-both"><!-- --></div>
</div>
<div id="mandatory" class="form-item">
 <label class="mandatory"><span class="form-required">*</span> <?php echo t ('mandatory fields')?></label>
 <div class="form-checkboxes" id="confirmation">
  <div class="form-item">
	<label class="option" for="confirm"><input type="checkbox" name="confirmation_box" id="confirm" value="1" class="form-checkbox" /><?php echo t ('I accept the opportunity to receive information from Mavic') ?></label>
  </div>
 </div>
</div>
<input type="hidden" id="customstr2" Name="do_field_34_12" value="consumer" />
<input type="hidden" name="do_interest_4" value="<?php print $idLangueDolist[$language->language] ?>" />
<div id="edit-actions" class="form-actions form-wrapper"><input name="op" id="edit-submit" type='submit' value='<?php echo t ('submit') ?>' class="form-submit" />
</div>
</form>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
	$("#webform-client").submit(function(){
		return VerifForm();
	});
//--><!]]>
</script>
</div>
</div>
<?php } ?>

	</div>


</div>
