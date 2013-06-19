<!-- lightbox -->
<div id="lightbox-overlay">
	<div id="lightbox-box">
			<div id="lightbox-info">
				<div id="lightbox-title"></div>
				<div id="lightbox-counter"><?php echo t('Image XXX of YYY');?></div>
			</div>
			<div id="lightbox-image">
				<img id="lightbox-img" />
			</div>
			<div id="lightbox-prevholder" onmouseover="lightbox.overPrev()" onmouseout="lightbox.outPrev()" onclick="lightbox.showPrev()">
				<div id="lightbox-prev">&nbsp;</div>
			</div>
			<div id="lightbox-nextholder" onmouseover="lightbox.overNext()" onmouseout="lightbox.outNext()" onclick="lightbox.showNext()">
				<div id="lightbox-next">&nbsp;</div>
			</div>
	</div>
	<div id="lightbox-loader"><img id="loader" src="<?php echo base_path().path_to_theme();?>/images/mavic-loader.gif"></div>
	<a href="" id="lightbox-close" class="button_view" onclick="lightbox.hide();return false;"><?php echo strtoupper(t('close'));?></a>
</div>