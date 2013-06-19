<?php 
	$trad = array();
	$trad['01'] = t('January');
	$trad['02'] = t('February');
	$trad['03'] = t('March');
	$trad['04'] = t('April');
	$trad['05'] = t('May');
	$trad['06'] = t('June');
	$trad['07'] = t('July');
	$trad['08'] = t('August');
	$trad['09'] = t('September');
	$trad['10'] = t('October');
	$trad['11'] = t('November');
	$trad['12'] = t('December');
	$day = (int)substr($fields['field_news_date_value']->raw,8,2);
	$month = $trad[substr($fields['field_news_date_value']->raw,5,2)];
	$year = substr($fields['field_news_date_value']->raw,0,4);
?>
<div id="blocknews" class="element">
<table cellpadding="0" border="0" cellspacing="0" width="236" class="big" >
	<tbody>
		<tr>
			<td align="center" valign="top" >
				<img alt="<?php print $fields['title']->content ?>" src="<?php echo str_replace('.jpg','_m.jpg',$fields['field_news_picture_flickr_value']->content) ?>">
			</td>
		</tr>
	</tbody>
</table>
	<a href="<?php echo $fields['path']->content;?>" class="link" style="display:none"><?php echo $fields['path']->content;?></a>
</div>
<div class="clear mavic_menu_spacer"></div>
<div class="complement">
	<p class="title"><a href="<?php echo $fields['path']->content;?>"><?php print $fields['title']->content ?></a></p>
	<p class="description"><a href="<?php echo $fields['path']->content;?>"><?php print $fields['field_news_intro_value']->content ?></a></p>
	<span class="news_date_menu">(<?php echo "$day $month $year" ?>)</span>
</div>
