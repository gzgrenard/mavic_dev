<div style="float:left">
	<b><?php echo l($breadcrumb[0]['link']['title'],$breadcrumb[0]['link']['href']); ?></b>
	>&nbsp;&nbsp;
</div>

<div id="select_page">
	<?php
		if(mb_strtolower($breadcrumb[1]['link']['title']) == mb_strtolower($breadcrumb[2]['link']['title']))
			$title_bread = $breadcrumb[1]['link']['title'];
		else
			$title_bread = $breadcrumb[1]['link']['title'].'&nbsp;-&nbsp;'.$breadcrumb[2]['link']['title'];
	?>
	<div id="selected_product" onclick="show_hide_select('#list_select')"><?php echo $title_bread; ?></div>
	<ul id="list_select">
		<?php 
			foreach($primary_links as $line) {
				foreach ($line['below'] as $category) {
					$family = @reset($category['below']);
					if(mb_strtolower($line['link']['title']) == mb_strtolower($category['link']['title']))
						$title_bread = $line['link']['title'];
					else
						$title_bread = $line['link']['title'].'&nbsp;-&nbsp;'.$category['link']['title'];
					if(!$category['link']['in_active_trail'])
						echo '<li><a href="'.url($family['link']['href']).'">'.$title_bread."</a></li>";
					else
						echo '<li><a class="active" href="'.url($family['link']['href']).'">'.$title_bread."</a></li>";
				}
			}
		?>
	</ul>
	<script type="text/javascript">
		//$('#body').click( function(){ $('#list_select').css('display','none'); });
	</script>
</div>
