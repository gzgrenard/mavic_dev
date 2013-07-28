<div id="tabs_buttons_gamme">
	<div id="shopfinder" 
		onmouseout="outTab(this)" 
		onmouseover="overTab(this)" 
		class="tab <?php echo $classe_shop;?>">
		<a class="helvetica"  href="<?php echo url('shopfinder');?>" ><?php print(t('stores')); ?></a>
	</div>
	<div id="distributor" 
		onmouseout="outTab(this)" 
		onmouseover="overTab(this)" 
		class="tab <?php echo $classe_distrib;?>">
		<a class="helvetica"  href="<?php echo url('distributor');?>" ><?php print(t('distributors')); ?></a>
	</div>
	<div id="shop_in_shop" 
		onmouseout="outTab(this)" 
		onmouseover="overTab(this)" 
		class="tab <?php echo $classe_sis;?>">
		<a class="helvetica"  href="<?php echo url('shopinshop');?>" ><?php print(t('shop in shop')); ?></a>
	</div>
	<?php if($language == 'ja'):?>
		<div id="japanese" 
			onmouseout="outTab(this)" 
			onmouseover="overTab(this)" 
			class="tab">
			<a class="helvetica"  href="http://www.mavic.jp/shop.html" target="_blank" ><?php print(t('Local Shopfinder')); ?></a>
		</div>
	<?php endif;?>
</div>
<div class="clear"></div>