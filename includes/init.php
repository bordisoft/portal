<?php
$errors = array();
$extensions = @get_loaded_extensions();

if (!in_array("json", $extensions))
{
	array_push($errors, "PHP does not have the Json extension enabled. Please contact your web hosting provider.");
}
if (!in_array("session", $extensions))
{
	array_push($errors, "Session support is disabled in PHP. Please contact your web hosting provider.");
}
if (!in_array("openssl", $extensions))
{
	array_push($errors, "PHP does not have the OpenSSL extension enabled. Please contact your web hosting provider.");
}
if (ini_get("safe_mode"))
{
	array_push($errors, "The PHP safe mode feature must be disabled. Please contact your web hosting provider.");
}
if(!function_exists('curl_version') && !function_exists('fsockopen'))
{
	array_push($errors, "PHP does not have the Curl and Socket enabled. Please contact your web hosting provider and ask to enable at least one of them.");
}
if(!empty($errors))
{
	foreach($errors as $error)
	{
		echo $error."\r\n";
	}
	exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
date_default_timezone_set("UTC");
session_name("SelfcareSession");
while(!isset($_SESSION)) { session_start(); }
define('_PORTAL',TRUE);
define ('_SITE_PATH', str_replace('includes/','',realpath(dirname(__FILE__)).'/'));
define('_REWRITE_BASE', str_replace('index.php','',$_SERVER['PHP_SELF']));
spl_autoload_register('AutoLoad');
$userInfo = Cache::getInstance()->getCache('userInfo');
if($userInfo)
{
	define('_REALM',$userInfo['realm']);
	if(isset($userInfo["tz"])) { date_default_timezone_set($userInfo["tz"]); }
}

function AutoLoad($class_name)
{
	$file = NULL;
	if(strpos($class_name, 'Model') !== FALSE)
	{
		$split = preg_split("/(?<=[a-z])(?![a-z])/", $class_name, -1, PREG_SPLIT_NO_EMPTY);
		$path = _SITE_PATH.'model/'.strtolower($split[count($split)-2]).'/';
		$filename = '';
		$i = 0;
		$limit = count($split)-1;
		while($i < $limit)
		{
			$filename .= ('' == $filename ? '' : '.').strtolower($split[$i]);
			$i++;
		}
		$filename .= '.model.class.php';
		if(file_exists($path.$filename))
		{
			$file = $path.$filename;
		}
		elseif(file_exists($path.strtolower($split[count($split)-2]).'.model.class.php'))
		{
			$file = $path.strtolower($split[count($split)-2]).'.model.class.php';
		}
		elseif(file_exists(_SITE_PATH.'model/'.$filename))
		{
			$file = _SITE_PATH.'model/'.$filename;
		}
	}
	else
	{
		$filename = strtolower($class_name) . '.class.php';
		$require_pathes = array(
			_SITE_PATH . 'application/',
			_SITE_PATH . 'includes/'
		);
		foreach($require_pathes as $path)
		{
			if (file_exists($path.$filename))
			{
				$file = $path.$filename;
				break;
			}
		}
	}

	if(!$file)
	{
		return FALSE;
	}

	require_once $file;
}
?>