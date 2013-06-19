<?php
// $Id: views-view.tpl.php,v 1.13.2.2 2010/03/25 20:25:28 merlinofchaos Exp $
/**
 * @file views-view.tpl.php
 * Main view template
 * Variables available:
 * - $classes_array: An array of classes determined in
 *   template_preprocess_views_view(). Default classes are:
 *     .view
 *     .view-[css_name]
 *     .view-id-[view_name]
 *     .view-display-id-[display_name]
 *     .view-dom-id-[dom_id]
 * - $classes: A string version of $classes_array for use in the class attribute
 * - $css_name: A css-safe version of the view name.
 * - $css_class: The user-specified classes names, if any
 * - $header: The view header
 * - $footer: The view footer
 * - $rows: The results of the view query, if any
 * - $empty: The empty text to display if the view is empty
 * - $pager: The pager next/prev links to display, if any
 * - $exposed: Exposed widget form/info to display
 * - $feed_icon: Feed icon to display, if any
 * - $more: A link to view more, if any
 * - $admin_links: A rendered list of administrative links
 * - $admin_links_raw: A list of administrative links suitable for theme('links')
 *
 * @ingroup views_templates
 */
?>
  <?php if ($rows): ?>
	<div class="buttons">
	<div>
		<div class="right_buttons">			  
			<a class="prev browse left disabled"><img src="<?php echo base_path().path_to_theme();?>/images/carousel_prev.gif" alt="" /></a>
			<a class="next browse right"><img src="<?php echo base_path().path_to_theme();?>/images/carousel_next.gif" alt="" /></a>				
		</div>
		<p class="helvetica title"><?php echo t('news') ?></p></div>
		<div class="left_buttons">
			<img src="<?php echo base_path().path_to_theme();?>/images/more_info.gif" alt=""><a href="<?php echo url('news/all-news') ?>" ><?php echo t('All news');?></a>
		</div>
		<div class="clear"></div>
	</div>
  <!-- root element for scrollable -->
	<div class="scrollable view-content">
		<!-- root element for the items -->
		<div class="items">
			<?php print $rows; ?>
		</div>
	</div>
<?php endif; ?>
