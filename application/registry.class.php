<?php defined('_PORTAL') or die();

require_once _SITE_PATH.'/config.php';

Class Registry
{
	private $vars = array();

	function __construct()
	{
		foreach(array_merge($_POST,get_class_vars('Config')) as $name => $value)
		{
			$this->set($name, $value);
		}
		$_POST = $_GET = $_REQUEST = array();
	}

	public function set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	public function get($name)
	{
		return (!empty($this->vars[$name]) ? $this->vars[$name] : NULL);
	}
}

?>