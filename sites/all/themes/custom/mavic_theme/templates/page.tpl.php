<?php 
/**
 * @file
 * Mavic theme's theme implementation to display a single Drupal page.
 */
?>
<div<?php print $attributes; ?>>
  <?php if (isset($page['header'])) : ?>
    <?php print render($page['header']); ?>
  <?php endif; ?>
  
  <?php if (isset($page['content'])) : ?>
  <?php if(isset($is_mobile) ==  FALSE) : ?> 
  <div id="btn_up">
     <a href="#" title="<?php echo t('Top', array(), array('context' => 'return top page', 'lancode' => 'en')); ?>"><?php echo t('Top', array(), array('context' => 'return top page', 'lancode' => 'en')); ?></a>
  </div>
  <?php endif; ?>
    <?php print render($page['content']); ?>
  <?php endif; ?>  
  
  <?php if (isset($page['footer'])) : ?>
    <?php print render($page['footer']); ?>
  <?php endif; ?>
</div>