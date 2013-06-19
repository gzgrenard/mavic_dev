<br>views-view-field--frontpage.tpl.php<br>
title : <?php echo $fields['title']->raw ?><br>
img : <?php echo $home_img_path.'/'.$fields['field_image_value']->raw.'.jpg' ?><br>
descriptif : <?php echo $fields['body']->raw ?><br>
<?php if($fields['field_ssc_value']->raw) {?>
	ssc IMG : <?php echo $theme_images;?>/logos/ssc_home.gif<br>
<?php }?>
url : <?php echo base_path().$fields['field_url_value']->raw ?><br>
<br>fin : views-view-field--frontpage.tpl.php<br>