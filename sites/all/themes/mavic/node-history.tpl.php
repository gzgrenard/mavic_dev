<script type="text/javascript" >
	$(document).ready(function() {
		$("#body-background").ezBgResize();
		checkSize();
	});
</script>
<div id="tabs">
<?php
	require_once('node-history-tabs.tpl.php');
	$depth = count($breadcrumb);
?>
	<div id="history_content">
		<h1 class="helvetica"><?php echo $node->field_history_year[0]['value']; ?> : <?php echo $node->title;?> </h1>

		<?php echo $node->content['body']['#value']; ?>

	</div>

<?php

	$parent = $depth - 2;
	$elem = $depth - 1;
	$current = $breadcrumb[$elem]['key_breadcrumb'];
	if(count($breadcrumb[$parent]['below']) > 1) {
		reset($breadcrumb[$parent]['below']);
		$previous = '';
		while(key($breadcrumb[$parent]['below']) != $current) {
			$previous = current($breadcrumb[$parent]['below']);
			next($breadcrumb[$parent]['below']);
		}
		$next = next($breadcrumb[$parent]['below']);
?>
		<br class='clear' />
		<div id="history_prev_next">
			<?php
				if($previous != '') {
					echo '<a class="button_view button-view-previous" href="'.url($previous['link']['href']).'">'.t('previous date').'</a>';
				}
				if($next) {
					echo ' <a class="button_view button-view-next" href="'.url($next['link']['href']).'">'.t('next date').'</a>';
				}
			?>
		</div>

<?php
	}
	?>

	<div id="history_preview">
		<?php
		$i = 0;
		foreach($breadcrumb[$parent]['below'] as $product) {
			$i++;
			$item = menu_get_item($product['link']['href']);
			$itemMap = $item['map'][1];
			$link = url($product['link']['href']);
			if($product['link']['in_active_trail'])
			{
				$link = '#';
				$classe = '_active';
			}
			else $classe = '';
		?>
			<a href="<?php echo $link?>" class="history_item<?php echo $classe ?> <?php echo($i % 3 == 0)?'last':''?>">
				<span class="year helvetica"><?php echo $itemMap->field_history_year[0]['value'] ?></span>
				<span class="title">
					<?php echo $itemMap->title ?>
				</span>
				<?php echo $itemMap->field_history_introduction[0]['value'] ?>
				<span class="readmore">
					<img src="/sites/default/themes/mavic/images/more_info.gif" alt="" />
					<?php echo t('Read more');?>
				</span>
			</a>
			<?php if ($i % 3 == 0):?>
				<div style="clear: both"></div>
			<?php endif;?>
		<?php
		}
		?>
		<div style="clear: both">&nbsp;</div>
	</div>
</div>