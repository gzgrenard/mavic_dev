<?php
	global $language;
	preg_match_all('`<a.*/a>`', $block->content, $matches);
	$links = $matches[0];
	$newsl = array_shift($links);
	/*
	$dom = new DOMDocument;
	$dom->loadHTML($newsl);
	$nodes = $dom->getElementsByTagName('a');
	$newshref  = $nodes->item(0)->getAttribute('href');	 */
	$newshref = "/".$language->language."/newsletter/";
	if($newshref != "") {
		$newsletter = '<form id="nlsubmit" action="'.$newshref.'" method="post">'.
		str_replace('title=','target=', $newsl).		
		'<div class="whitebg"><input class="enternl" id="newsl_input" type="text" name="adress" value="'.t("Enter your email").'" maxlength="100" autocomplete="off" />'.
		'<input class="submitnl" type="submit" name="submitnl" value="'.t("OK").'" /></div>'.
		'</form>';
	}
	
?>

<script type="text/javascript" >
	var nlvalue,firstFocus=true;
	$(document).ready(function() {
	var enternl = $('#newsl_input');
	enternl.focus( function () {
		if(firstFocus){
			firstFocus=false;
			nlvalue = enternl.val();
			enternl.val('');
		}
	}).blur( function () {
		(enternl.val().replace(/\s*/,"") == "")?enternl.val(nlvalue):"";
	});
	popupnewslettersubscript();
	});
</script>

	<div class="left">
	<?php print $newsletter ?>
<?php foreach($links as $link) : ?>
		<?php echo str_replace('title=','target=', $link) ?>
<?php endforeach; ?>

		<span><?php echo t("Find Mavic on:"); ?></span>
		<a target="_blank" href="http://www.youtube.com/user/adminMavic" onclick="omniture_click(this, 'youtube')"; class="findMavicOnLinks youtube"><img src="/sites/default/themes/mavic/images/picto_youtube.gif" alt="" />Youtube</a>
		<a target="_blank" href="http://www.facebook.com/mavic" onclick="omniture_click(this, 'facebook')"; class="findMavicOnLinks facebook"><img src="/sites/default/themes/mavic/images/picto_facebook.gif" alt="" />Facebook</a>
		<a target="_blank" href="http://www.flickr.com/photos/mavicssc" onclick="omniture_click(this, 'flickr')"; class="findMavicOnLinks flickr"><img src="/sites/default/themes/mavic/images/picto_flickr.gif" alt="" />Flickr</a>
	</div>