<?php defined('_PORTAL') or die(); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title><?php echo $title;?></title>
		<meta name="description" content="Web portal">
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<link href="<?php echo $theme_path;?>css/bootstrap.min.css" rel="stylesheet">
		<link href="<?php echo $theme_path;?>css/font-awesome.min.css" rel="stylesheet">
		<link href="<?php echo $theme_path;?>css/bootstrap3-wysihtml5.min.css" rel="stylesheet">
		<link href="<?php echo $theme_path;?>css/style.css" rel="stylesheet">

		<link rel="icon" type="image/ico" href="<?php echo $theme_path;?>img/favicon.ico"/>
        <!--[if lt IE 9]>
          <script src="<?php echo $theme_path;?>/js/html5shiv.js"></script>
          <script src="<?php echo $theme_path;?>/js/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo $theme_path;?>js/jquery.min.js" type="text/javascript"></script>
		<script src="<?php echo $theme_path;?>js/bootstrap.min.js" type="text/javascript"></script>
		<script src="<?php echo $theme_path;?>js/app.js" type="text/javascript"></script>
		<script src="<?php echo $theme_path;?>js/validator.js" type="text/javascript"></script>
		<script src="<?php echo $theme_path;?>js/custom.js" type="text/javascript"></script>
	</head>

	<body class="skin-black loader">
		<header class="header">
			<a href="<?php echo _REWRITE_BASE; ?>" class="logo"><img src="<?php echo $theme_path;?>/img/logo.png" alt="logo" /></a>
			<nav class="navbar navbar-static-top" role="navigation">
				<a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<div id="header" class="navbar-right">
					<?php require_once ($realm_path && file_exists($realm_path.'header.php') ? $realm_path.'header.php' : $view_path.'html/header.php'); ?>
				</div>
			</nav>
		</header>
		<div class="wrapper row-offcanvas row-offcanvas-left">
			<aside class="left-side sidebar-offcanvas">
				<section id="sidebar" class="sidebar">
				<?php require_once ($realm_path && file_exists($realm_path.'sidebar.php') ? $realm_path.'sidebar.php' : $view_path.'html/sidebar.php'); ?>
				</section>
			</aside>
			<aside class="right-side">
				<section id="breadcrumps" class="content-header">
					<?php require_once ($realm_path && file_exists($realm_path.'breadcrumps.php') ? $realm_path.'breadcrumps.php' : $view_path.'html/breadcrumps.php'); ?>
				</section>
				<section class="content">
					<div id="notifications" class="notifications-container"><?php require_once ($realm_path && file_exists($realm_path.'notifications.php') ? $realm_path.'notifications.php' :  $view_path.'html/notifications.php'); ?></div>
					<div id="content"><?php require_once $tmeplate_file; ?></div>
				</section>
			</aside>
		</div>
      	<script>
			var token = '<?php echo $token; ?>',
				JS_THIS = '<?php echo empty($locale['JS_THIS']) ? 'This' : $locale['JS_THIS']; ?>',
				JS_MAND_CHECKBOX = '<?php echo empty($locale['JS_MAND_CHECKBOX']) ? 'This checkbox is mandatory' : $locale['JS_MAND_CHECKBOX']; ?>',
				JS_ENTER_VALID_EMAIL = '<?php echo empty($locale['JS_ENTER_VALID_EMAIL']) ? 'Please enter valid email address' : $locale['JS_ENTER_VALID_EMAIL']; ?>',
				JS_SELECT_ITEM = '<?php echo empty($locale['JS_SELECT_ITEM']) ? 'Please select an item' : $locale['JS_SELECT_ITEM'] ?>',
				JS_INVALID_CHARACTER = '<?php echo empty($locale['JS_INVALID_CHARACTER']) ? 'This character is forbidden' : $locale['JS_INVALID_CHARACTER']; ?>',
				JS_MAND_FIELD = '<?php echo empty($locale['JS_MAND_FIELD']) ? 'This field is mandatory' : $locale['JS_MAND_FIELD']; ?>',
				url = '<?php echo _REWRITE_BASE; ?>',
				date_format = '<?php echo !empty($js_date_format) ? $js_date_format : ''; ?>',
				time_format = '<?php echo !empty($js_time_format) ? $js_time_format : ''; ?>',
				theme_path = '<?php echo $theme_path; ?>';
		</script>
	</body>
</html>