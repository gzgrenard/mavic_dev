<?php
global $base_url;
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?php print $head_title ?></title>

		<meta name="keywords" content="<?php print $keywords ?>" />
		<meta http-equiv="content-language" content="<?php echo $lang; ?>">
		<meta name="description" content="<?php print $description ?>" />
		<?php
		$mPath = drupal_get_path_alias($_GET['q']);
		$discPath = $lang . '/' . $mPath;
		if (!empty($discipline) && $mPath != 'mtb-home' && $mPath != 'road-home') {
			print '<link rel="canonical" href="http://www.mavic.com/' . $discPath . '"/>';
		}
		?>
		<link href="http://www.mavic.com/en" hreflang="en" rel="alternate">
		<link href="http://www.mavic.com/fr" hreflang="fr" rel="alternate">
		<link href="http://www.mavic.com/de" hreflang="de" rel="alternate">
		<link href="http://www.mavic.com/it" hreflang="it" rel="alternate">
		<link href="http://www.mavic.com/es" hreflang="es" rel="alternate">
		<link href="http://www.mavic.com/ja" hreflang="ja" rel="alternate">
		<link rel="stylesheet" type="text/css" href="<?php echo base_path() . path_to_theme(); ?>/style-mavic.css?version=30">

		<!--[if IE]>
			<meta http-equiv="imagetoolbar" content="no" />
			<link rel="stylesheet" type="text/css" href="<?php echo base_path() . path_to_theme(); ?>/style-ie.css?version=3" />
		<![endif]-->

		<!--[if lte IE 7]>
			<link rel="stylesheet" type="text/css" href="<?php echo base_path() . path_to_theme(); ?>/style-ie67.css?version=2" />
		<![endif]-->

		<!--[if IE 6]>
			<script src="<?php echo base_path() . path_to_theme(); ?>/js/jquery.bgiframe.min.js" type="text/javascript" charset="utf-8"></script>
			<link rel="stylesheet" type="text/css" href="<?php echo base_path() . path_to_theme(); ?>/style-ie6.css?version=3" />
		<![endif]-->

		<link href="<?php echo base_path() . path_to_theme(); ?>/images/favicon.ico" rel="shortcut icon">
		<?php if ($lang == 'ja') { ?>
			<link rel="stylesheet" type="text/css" href="<?php echo base_path() . path_to_theme(); ?>/style-mavic.ja.css?version=3">
			<!--[if lte IE 7]>
				<link rel="stylesheet" type="text/css" href="<?php echo base_path() . path_to_theme(); ?>/style-ie67.ja.css?version=1" />
			<![endif]-->
			<!--[if IE 6]>
				<link rel="stylesheet" type="text/css" href="<?php echo base_path() . path_to_theme(); ?>/style-ie6.ja.css?version=2" />
			<![endif]-->
		<?php
		};
		
		switch ($mobile) {
			case 'tablet':
		?>
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<link rel="stylesheet" type="text/css" href="<?php echo base_path() . path_to_theme(); ?>/style-tablet.css?version=1">
				<?php
				break;
			case 'smartphone':
				?>
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<link rel="stylesheet" type="text/css" href="<?php echo base_path() . path_to_theme(); ?>/style-smartphone.css?version=1">			
				<?php
				break;
			default:
				?>
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<?php
				break;
		}
		?>

		<link rel="stylesheet" media="print" type="text/css" href="<?php echo base_path() . path_to_theme(); ?>/style-mavic-print.css?version=4">

		<script type="text/javascript" >
			var language ='<?php echo $lang; ?>';
			var mobileUA = '<?php echo $mobile; ?>';
		</script>
		<script type="text/javascript" src="<?php echo base_path() . path_to_theme(); ?>/js/script.combined.js?version=27"></script>
		<link rel="image_src" href="<?php echo $og_img; ?>" />
		<meta property="og:image" content="<?php echo $og_img; ?>" />
		<meta property="og:title" content="<?php echo $og_title; ?>" />
		<meta property="og:description" content="<?php echo $og_description ?>" />
	</head>
	<body id="body" class="<?php echo $lang; ?> <?php echo $node->type; ?>">