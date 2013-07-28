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
?>
<article <?php print $attributes; ?>>
<h2><?php echo $node->field_usp['und'][0]['value']; ?></h2>
<div <?php print $content_attributes; ?>>
<div class="block-images-profil">
	<?php
	print render($content['field_image_profil_1']);
	print render($content['field_image_profil_2']);
	?>
	<a href="#" class="product-profil1"><?php echo t('Vue côté'); ?></a>
	<a href="#" class="product-profil2"><?php echo t('vue intérieur'); ?></a>
	<a href="#" class="product-zoom"><?php echo t('Zoom'); ?></a>
</div>
<div class="field-body field-body-<?php echo $node->type; ?>"><?php echo $node->body[$node->language][0]['value']; ?></div>
<div class="product-links">
	<a href="#" class="product-compare"><?php echo t('Comparer'); ?></a>
	<a href="#" class="product-print"><?php echo t('Imprimer'); ?></a>
	<a href="#" class="product-share"><?php echo t('Partager'); ?></a>
</div>
<a href="#" class="product-finshop"><?php echo t('Trouver un revendeur'); ?></a>

<h3><?php echo t('Principal caracteristique'); ?></h3>
<?php
$caracs = array();
$i = 0;
foreach($node->field_killerpointmacrolb['und'] as $f){
	$caracs[$i]['title'] = $f["value"];
	$i++;
}

$i = 0;
foreach($node->field_kcbarglb['und'] as $f){
	$caracs[$i]['desc'] = $f["value"];
	$i++;
}
?>
<div class="main-features-block">
<?php
foreach($caracs as $carac){
?>
<div>
	<h4><?php echo $carac['title']; ?></h4>
	<p><?php echo $carac['desc']; ?></p>
</div>
<?php
}
?>
</div>
<h3><?php echo t('Galerie photo'); ?></h3>
<?php
	print render($content['field_gallerie1']);
	print render($content['field_gallerie2']);
?>
<h3><?php echo t('Technologies clés'); ?></h3>
<?php
	print render($content['field_technologienode']);
	
	
	// var_dump($content['field_featurenode']);
	//hide($content["field_featurenode"]);

$allFeatures = array();
foreach($node->field_featurenode['und'] as $f){
	$nodeFeature = node_load($f['nid']);
	$allFeatures[$nodeFeature->field_type["und"][0]["value"]][]= $nodeFeature->title;
}
?>
<h3><?php echo t('caractéristiques détaillées'); ?></h3>
<?php
foreach($allFeatures as $label=>$groupeFeature){
	?>
	<div class="features-block">
		<p class="features-block-title"><?php echo $label; ?></p>
		<ul>
		<?php
			foreach($groupeFeature as $feature){
			?>
			<li><?php echo $feature; ?></li>
			<?php
			}
		?>
		</ul>
	</div>
	<?php
}

?>
<?php


$lapresse = array();
$i = 0;
foreach($node->field_la_presse_en_parle_texte_['und'] as $f){
	$lapresse[$i]['texte'] = $f["value"];
	$i++;
}

$i = 0;
foreach($node->field_la_presse_en_parle_auteur_['und'] as $f){
	$lapresse[$i]['auteur'] = $f["value"];
	$i++;
}

$i = 0;
foreach($node->field_la_presse_en_parle_date_['und'] as $f){
	$lapresse[$i]['date'] = $f["value"];
	$i++;
}
?>
<div class="main-features-block">
<?php
foreach($lapresse as $presse){
?>
<div>
	<h4>"<?php echo $presse['texte']; ?>"</h4>
	<p><?php echo $presse['auteur']; ?></p>
	<p><?php echo $presse['date']; ?></p>
</div>
<?php
}
?>
</div>
</article>