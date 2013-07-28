<article<?php print $attributes; ?>>
  <?php print $user_picture; ?>
  <?php print render($title_prefix); ?>
  <?php if (!$page && $title): ?>
  <header>
    <h2<?php print $title_attributes; ?>><?php print $title ?></h2>
  </header>
  <?php endif; ?>
  <?php print render($title_suffix); ?>
  <?php if ($display_submitted): ?>
  <footer class="submitted"><?php print $date; ?> -- <?php print $name; ?></footer>
  <?php endif; ?>  
  
  <div<?php print $content_attributes; ?>>
    <?php
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
     // print render($content);
	  $item = field_get_items('node', $node, 'field_fichier');
	  $output2 = field_view_value('node', $node, 'field_fichier', $item[0]);
		foreach ($output2 as $key) {
		  if(is_object($key)) {
			$item2 = field_get_items('node', $node, 'field_type_de_fichier');
			$output22 = field_view_value('node', $node, 'field_type_de_fichier', $item2[0]);
			?>
			<a href="<?php echo file_create_url($key->uri);?>" class="fichier-type-<?php echo $output22["#title"]; ?>"><?php echo t('Download file'); ?></a>
			<?php
		  }
		}
    ?>
  </div>
  
  <div class="clearfix">
    <?php if (!empty($content['links'])): ?>
      <nav class="links node-links clearfix"><?php print render($content['links']); ?></nav>
    <?php endif; ?>

    <?php print render($content['comments']); ?>
  </div>
</article>