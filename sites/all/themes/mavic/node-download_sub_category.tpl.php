
	<div class="sub_category_download_content">
		<h2 class="helvetica"><?php echo $title; ?></h2>
		<?php foreach($field_download as $download) { ?>
			<?php echo $download['view']; ?>
		<?php } ?>
                <?php if(!empty($field_download_archive[0]['view'])) { ?>
					<div class="archive_button">
                        <a class="button_view" href="javascript: void(0);" title="<?php echo t('archive'); ?> <?php echo $title; ?>">
							<?php echo t('archive @title',array('@title'=>$title)); ?>
						</a>
					</div>
					<div class="archive_files">
                        <?php foreach($field_download_archive as $download) { ?>
                                <?php echo $download['view']; ?>
                        <?php } ?>
					</div>
                <?php } ?>
	<div class="clear"></div>
	</div>


