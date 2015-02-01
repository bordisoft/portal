<?php
require_once 'includes/init.php';
$registry = new Registry;
$core = new Core($registry);
try
{
	initializeRouter:
	$core->router = new Router($core->registry);
	$core->router->route();
}
catch (Exception $e)
{
	$errors = $core->registry->get('errors') ? $core->registry->get('errors') : array();
	$error_code = NULL;
	if($errors)
	{
		foreach($errors as $error)
		{
			if($error && $error['code'] == $e->getCode())
			{
				$error_code = 503;
				break;
			}
		}
	}
	$error_code = $error_code ? $error_code : $e->getCode();
	$core->registry->set('action','get');
	switch($error_code)
	{
		case 401:
		case 503:
			session_unset();
			session_destroy();
			session_name("SelfcareSession");
			session_start();
			if("ajax" == $core->registry->get("request_via"))
			{
				echo json_encode(array('content' => "<script>window.location.href=url;</script>"), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
				exit;
			}
			else
			{
				$core->registry->set('route','dashboard');
				$core->registry->set('task', "login");
			}
			break;
		case 500:
			$core->registry->set('route','dashboard');
			$core->registry->set('task','index');
			break;
		case 402:
			$core->registry->set('route','dashboard');
			$core->registry->set('task','change_pass');
			break;
		case 400:
		case 403:
		case 404:
		case 403:
			$core->registry->set('route','error');
		default:
			$core->registry->set('errors',array(array('content' => $e->getMessage(),'code' => $error_code)));
			$template = new Template($core->registry);
			$template->show(array('token' => Helper::getToken(TRUE)));
			exit;
	}
	$errors[] = array('content' => $e->getMessage(),'code' => $error_code);
	$core->registry->set('errors', $errors);
	goto initializeRouter;
}
?>