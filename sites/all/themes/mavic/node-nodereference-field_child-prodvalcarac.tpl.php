<li class="li_child_techno">
	<img src="<?php echo $technologies_path.'/children/'.$field_code[0]['value'].'.jpg'; ?>" />
	<ul>
		<h5><?php echo t('Associated products:'); ?></h5>
		<?php
			$macro_nid_tab = db_query('SELECT n.nid, n.title FROM content_field_technologienode o INNER JOIN node_revisions n USING (vid), menu_links m WHERE o.field_technologienode_nid='.$nid.' and m.link_path=concat("node/", o.nid) order by m.weight');
			$i = 0;
			while(($prod = db_fetch_array($macro_nid_tab)) && $i < 5) {
				$i++;
		?>
			<li><a href="<?php echo url('node/'.$prod['nid']);?>"><?php echo $prod['title']?></a></li>
		<?php
			}
		?>
	</ul>
	<div class="child_techno_content">
		<h4 class="helvetica"><?php echo $title; ?></h4>
		<?php echo $node->content['body']['#value']; ?>
	</div>
	<div class="clear">&nbsp;</div>
</li>