<?php
// $Id: page.tpl.php,v 1.18.2.1 2009/04/30 00:13:31 goba Exp $
//
// GARLAND THEME USER
//
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language ?>" lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">
  <head>
    <?php print $head ?>
    <title><?php print $head_title ?></title>
    <?php print $styles ?>
    <?php print $scripts ?>
    <!--[if lt IE 7]>
      <?php print '<link type="text/css" rel="stylesheet" media="all" href="'. base_path() . path_to_theme() .'/fix-ie.css" />'; ?>
    <![endif]-->
  </head>
  <body class="sidebars">
<!-- Layout -->


    <div id="wrapper">
    <div id="container" class="clear-block">

      <div id="header">

    

      </div> <!-- /header -->

      <?php if ($left): ?>
        <div id="sidebar-left" class="sidebar">

        </div>
      <?php endif; ?>

      <div id="center"><div id="squeeze"><div class="right-corner"><div class="left-corner">
          <?php if ($show_messages && $messages): print $messages; endif; ?>
          <?php print $help; ?>
          <div class="clear-block">
            <?php print $content ?>
          </div>
      </div></div></div></div> <!-- /.left-corner, /.right-corner, /#squeeze, /#center -->


        <div id="sidebar-right" class="sidebar">
        </div>

    </div> <!-- /container -->
  </div>
<!-- /layout -->

  <?php print $closure ?>
  </body>
</html>
