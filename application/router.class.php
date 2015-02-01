<?php defined('_PORTAL') or die();

class Router
{
	private $_registry;

	function __construct(&$registry)
	{
		$this->_registry = $registry;
	}

	public function route()
 	{
		$route = $this->_registry->get('route');
		$action = $this->_registry->get('action');

		if (class_exists('BaseController') && class_exists(ucfirst($route).'Controller'))
		{
			$class = ucfirst($route).'Controller';
			$controller = new $class($this->_registry);
		}
		else
		{
			$controller = new BaseController($this->_registry);
			if (!is_callable(array($controller, $action)))
			{
				throw new Exception('Not Found',404);
			}
		}
		$controller->$action();
	}
}

?>