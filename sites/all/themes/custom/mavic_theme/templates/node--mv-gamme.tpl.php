<?php

/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */
 //dsm($content['field_discipline']);  
 
//tableau de correspondance entre les gammes et la taxo parente des filtres à afficher
// nid => tid
$aFiltresTid = array(
    '349577' => array('tid'=>'25','display'=>'block'),
    '349578' => array('tid'=>'48','display'=>'block'),  
    '349580' => array('tid'=>'87','display'=>'block_footwear_road'),  
);

$discipline = taxonomy_term_load($node->field_discipline[LANGUAGE_NONE][0]['tid']);
?>
<article<?php print $attributes; ?>>
  <div<?php print $content_attributes; ?>>
    <?php
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
      print render($content);
    ?>
    <?php if ($page) { ?>
      <div class="toolbar-links">
      	<a href="#" class="product-compare"><?php echo t('Comparer'); ?></a>
      </div>
      <div class="block-listing-produits">
          <?php 
           //print views_embed_view('listing_de_produit','block', $node->nid);
           print views_embed_view('gamme',$aFiltresTid[ $node->nid ]['display'], $aFiltresTid[ $node->nid ]['tid']);
          ?>    
      </div>
      <div class="block-autres-gammes">
          <h2><?php print t('Découvrir la gamme @term', array('@term' => $discipline->name)); ?></h2>
          <?php 
           print views_embed_view('autres_gammes','block', $node->field_discipline[LANGUAGE_NONE][0]['tid'], $node->nid);
          ?>    
      </div>
      <div class="block-campagnes">
          <h2><?php print t('Latest products'); ?></h2>
          <?php print views_embed_view('block_campagne','block_1', $node->nid); ?>
      </div>
      <div class="block-mavic-live">
          <h2><?php print t('Mavic Live'); ?></h2>
          <?php /*print views_embed_view('home_block_mavic_live_news','block_2', $node->field_discipline[LANGUAGE_NONE][0]['tid']);*/ ?>
          <?php /*print views_embed_view('home_video','block_1', $node->field_discipline[LANGUAGE_NONE][0]['tid']);*/ ?>
          <?php print views_embed_view('home_block_mavic_live_news','block_2'); ?>
          <?php print views_embed_view('home_video','block_1'); ?>
      </div>
    <?php } ?>     
  </div>
  
  <div class="clearfix">
    <?php if (!empty($content['links'])): ?>
      <nav class="links node-links clearfix"><?php print render($content['links']); ?></nav>
    <?php endif; ?>

    <?php print render($content['comments']); ?>
  </div>
</article>