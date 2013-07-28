<article<?php print $attributes; ?>>
  <?php print $user_picture; ?>
  <?php print render($title_prefix); ?>
  <?php if (!$page && $title): ?>
  <header>
    <h2<?php print $title_attributes; ?>><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h2>
  </header>
  <?php endif; ?>
  <?php print render($title_suffix); ?>
  <?php if ($display_submitted): ?>
  <footer class="submitted"><?php print $date; ?> -- <?php print $name; ?></footer>
  <?php endif; ?>  
  <?php if(isset($gamme_nid)) : ?>
  <?php print t('<a href="@gamme_url" title="Return to gamme @gamme_title" rel="prev" class="return_gamme">Return to Gamme @gamme_title</a>', array('@gamme_url' => $gamme_url, '@gamme_title' => $gamme_title), array('langcode' => 'en')); ?>      
  <?php endif; ?>  
  <div<?php print $content_attributes; ?>>
    <?php
      // We hide the comments and links now so that we can render them later.
		hide($content['comments']);
		hide($content['links']);
		print views_embed_view('fiche_produit_galerie_couleur','block', $node->nid);
		print render($content);
		?>
		<div class="product-links">
			<a href="#" class="product-compare"><?php echo t('Comparer'); ?></a>
			<a href="#" class="product-print"><?php echo t('Imprimer'); ?></a>
			<a href="#" class="product-share"><?php echo t('Partager'); ?></a>
		</div>
		<a href="#" class="product-finshop"><?php echo t('Trouver un revendeur'); ?></a>
		<?php
		print views_embed_view('fiche_d_tail_choix_d_clinaison','block', $node->nid);
		print views_embed_view('block_campagne','block', $node->nid);
		?>
		<h2><?php echo t('Produits associés'); ?></h2>
		<?php
		print views_embed_view('fiche_d_tail_choix_d_clinaison','block_1', $node->nid);
		print views_embed_view('fiche_d_tail_choix_d_clinaison','block_2', $node->nid);
		?>
		<h2><?php echo t('Principales caractéristiques'); ?></h2>
		<div id="block_carac_principales">
		<?php
		$tabCarac = array();
		$iCarac = 0;
		foreach($node->field_caracteristique_label_1["und"] as $field){
			$tabCarac[$iCarac]['label']=$field["safe_value"];
			$iCarac++;
		}
		$iCarac = 0;
		foreach($node->field_caracteristique_texte_1["und"] as $field){
			$tabCarac[$iCarac]['txt']=$field["safe_value"];
			$iCarac++;
		}
		foreach($tabCarac as $carac){
		?>
			<div class="carac_block"><h4 class="carac_label" ><?php echo $carac['label']; ?></h4><p  class="carac_txt" ><?php echo $carac['txt']; ?></p></div>
		<?php
		}
		?>
		</div>
		<h2><?php echo t('Galerie photos'); ?></h2>
		<?php
		print views_embed_view('fiche_produit_galerie_photos','block', $node->nid);
		?>
		<h2><?php echo t('Technologies clés'); ?></h2>
		<?php
		print views_embed_view('technologies_associ_es_a_un_produit','block', $node->nid);
		?>
		<h2><?php echo t('Caractéristiques détaillées'); ?></h2>
		<?php
		print views_embed_view('caract_ristiques_d_taill_es_taxo','block', $node->nid);
		?>
		<h2><?php echo t('La presse en parle'); ?></h2>
		<?php
		print views_embed_view('la_presse_en_parle','block', $node->nid);
		?>
		<h2><?php echo t('Documentation'); ?></h2>
		<?php
		print views_embed_view('fiche_produit_documentations','block', $node->nid);
		
    ?>
  </div>
  
  <div class="clearfix">
    <?php if (!empty($content['links'])): ?>
      <nav class="links node-links clearfix"><?php print render($content['links']); ?></nav>
    <?php endif; ?>

    <?php print render($content['comments']); ?>
  </div>
</article>