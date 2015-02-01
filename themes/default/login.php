<?php defined('_PORTAL') or die();?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Log in</title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<link href="<?php echo $theme_path;?>css/bootstrap.min.css" rel="stylesheet">
		<link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $theme_path;?>css/style.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="<?php echo $theme_path;?>/js/html5shiv.js"></script>
			<script src="<?php echo $theme_path;?>/js/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div style="margin-top:20px;" id="notifications" class="notifications-container"><?php require_once $view_path.'html/notifications.php'; ?></div>
		<div class="form-box box" id="login-box">
			<div class="header">Sign In</div>
			<form action="" class="validate" autocomplete="off" method="POST">
				<div class="body bg-gray">
					<div class="form-group">
						<input type="text" name="args[pb_auth_user]" class="form-control mand" placeholder="Login"/>
					</div>
					<div class="form-group">
						<input type="password" name="args[pb_auth_password]" class="form-control mand" placeholder="Password"/>
					</div>
					<div class="form-group">
						<input type="checkbox" name="args[remember_me]"/> Remember me
					</div>
				</div>
				<div class="footer">
					<button type="submit" class="btn bg-olive btn-block">Sign me in</button>
					<p><a href="#" id="pasword-restore">I forgot my password</a></p>
				</div>
				<input type="hidden" name="route" value="dashboard" />
				<input type="hidden" name="task" value="login" />
				<input type="hidden" name="action" value="authorization" />
			</form>
		</div>
		<script src="<?php echo $theme_path;?>js/jquery.min.js" type="text/javascript"></script>
		<script src="<?php echo $theme_path;?>js/bootstrap.min.js" type="text/javascript"></script>
		<script src="<?php echo $theme_path;?>js/validator.js" type="text/javascript"></script>
		<script src="<?php echo $theme_path;?>js/custom.js" type="text/javascript"></script>
		<script>
			var token = '<?php echo $token; ?>',
				url = '<?php echo _REWRITE_BASE;?>',
				JS_THIS = '<?php echo empty($locale['JS_THIS']) ? 'This' : $locale['JS_THIS']; ?>',
				JS_MAND_CHECKBOX = '<?php echo empty($locale['JS_MAND_CHECKBOX']) ? 'This checkbox is mandatory' : $locale['JS_MAND_CHECKBOX']; ?>',
				JS_ENTER_VALID_EMAIL = '<?php echo empty($locale['JS_ENTER_VALID_EMAIL']) ? 'Please enter valid email address' : $locale['JS_ENTER_VALID_EMAIL']; ?>',
				JS_SELECT_ITEM = '<?php echo empty($locale['JS_SELECT_ITEM']) ? 'Please select an item' : $locale['JS_SELECT_ITEM'] ?>',
				JS_INVALID_CHARACTER = '<?php echo empty($locale['JS_INVALID_CHARACTER']) ? 'This character is forbidden' : $locale['JS_INVALID_CHARACTER']; ?>',
				JS_MAND_FIELD = '<?php echo empty($locale['JS_MAND_FIELD']) ? 'field is mandatory' : $locale['JS_MAND_FIELD']; ?>';
			$(document).ready(function(){
				$("#pasword-restore").click(function(){
					var form = $("<form method=\"POST\" action=\""+url+"\"></form>");
					$("<input type=\"hidden\" name=\"route\" value=\"dashboard\" />").appendTo(form);
					$("<input type=\"hidden\" name=\"task\" value=\"restore\" />").appendTo(form);
					form.appendTo($("body"));
					form.submit();
					loader($("body"));
				});
			});
		</script>
	</body>
</html>