<div id="tabs_buttons_gamme">
<?php
$i = 1;
$menu_photo_length = count($menu_photo);
foreach($menu_photo as $data) :
	$classes = 'tab';
	if ($data['link']['in_active_trail']) $classes.= " active";
	if( $i == $menu_photo_length ) $classes.= " last";
?>

	<div id="<?php print $data['link']['title']?>"
	onmouseout="outTab(this)"
	onmouseover="overTab(this)"
	class="<?php echo $classes;?>">
	<?php
		if($data['link']['expanded']) {
			$first = reset($data['below']);
			$link = $first['link']['href'];
		} else {
			$link = $data['link']['href'];
		}
	?>
	<a class="helvetica"  href="<?php echo url($link);?>" ><?php print(mb_strtoupper($data['link']['title'])); //$data['href']?></a>

	</div>

<?php
	$i++;
endforeach;
?>
</div>
<div class="clear"></div>
