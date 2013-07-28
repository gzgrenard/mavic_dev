<?php
//
// MAVIC THEME MAINTENANCE
//
if (isset($language->language)){
	switch ($language->language) {
		case 'fr':
			$text = "Mavic is currently under maintenance.<br /> We should be back shortly.<br /> Thank you for your patience.";
		break;
		case 'de':
			$text = 'Mavic is currently under maintenance.<br /> We should be back shortly.<br /> Thank you for your patience.';
		break;
		case 'it':
			$text = 'Mavic is currently under maintenance.<br /> We should be back shortly.<br /> Thank you for your patience.';
		break;
		case 'es':
			$text = 'Mavic is currently under maintenance.<br /> We should be back shortly.<br /> Thank you for your patience.';
		break;
		case 'ja':
			$text = 'Mavic is currently under maintenance.<br /> We should be back shortly.<br /> Thank you for your patience.';
		break;
		default:
			$text = 'Mavic is currently under maintenance.<br /> We should be back shortly.<br /> Thank you for your patience.';
		break;
	} 
	
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language ?>" lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">
  <head>
    <title><?php print $head_title ?></title>
    <link href="http://www.mavic.com/sites/default/themes/mavic/images/favicon.ico" rel="shortcut icon"></link>
    <link rel="stylesheet" type="text/css" href="/sites/default/themes/mavic/style-mavic.css?version=15"></link>
    <script type="text/javascript" src="/sites/default/themes/mavic/js/script.combined.js?version=12"></script>
  </head>
  <body class="sidebars">
  <div style="margin-right: auto; margin-left: auto; margin-top: 20%; width: 700px; height: 300px; padding: 0;">
     <p class="helvetica" style="margin: 0; padding: 0; font-size: 32px; font-family: Tahoma, helvetica, arial; line-height: 34px; font-weight:bold;"><?php print $text ?></p>
     <img src="/sites/default/themes/mavic/images/logo.png" alt="mavic" style="position: fixed; bottom: 30px; right: 0px;" />      
  </div>
  </body>
</html>
