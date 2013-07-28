<?php 
/**
 * @file
 * Mavic theme's theme implementation to display a single Drupal page front.
 */
?>

<div<?php print $attributes; ?>>
  <?php if (isset($page['header'])) : ?>
    <?php print render($page['header']); ?>
  <?php endif; ?>
  <?php if ($messages): ?>
    <div id="messages" class="grid-12"><?php print $messages; ?></div>
  <?php endif; ?>
  <?php if (isset($page['content'])) : ?>
  <div id="btn_up">
     <a href="#" title="<?php echo t('Top', array(), array('context' => 'return top page', 'lancode' => 'en')); ?>"><?php echo t('Top', array(), array('context' => 'return top page', 'lancode' => 'en')); ?></a>
  </div>    
    <?php print render($page['content']); ?>
  <?php endif; ?>  
  
  <?php if (isset($page['footer'])) : ?>
    <?php print render($page['footer']); ?>
  <?php endif; ?>
</div>
