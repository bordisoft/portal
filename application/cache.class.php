<?php defined('_PORTAL') or die();

Class Cache
{
	private static $instance;

	public static function getInstance()
	{
		if ( is_null( self::$instance ) )
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function clearCache($cache_name=NULL,$args=array())
	{
		if(!empty($args) && !empty($cache_name))
		{
			unset($_SESSION['cache'][$cache_name][md5(serialize($args))]);
		}
		elseif(!empty($cache_name))
		{
			unset($_SESSION['cache'][$cache_name]);
		}
		else
		{
			unset($_SESSION['cache']);
		}
	}

	public function getCache($cache_name,$args=array())
	{
		return $this->_getCache("data", $cache_name, $args);
	}

	public function getCacheTimestamp($cache_name,$args=array())
	{
		return $this->_getCache("created", $cache_name, $args);
	}

	public function setCache($cache_name,$data,$args=array(),$lifetime=NULL)
	{
		$cache = array(
			'data' => $data,
			'created' => time(),
			'lifetime' => $lifetime
		);
		if(!empty($args))
		{
			$key = md5(serialize($args));
			$_SESSION['cache'][$cache_name][$key] = $cache;
		}
		else
		{
			$_SESSION['cache'][$cache_name] = $cache;
		}
	}

	private function __construct()
	{
		if(!isset($_SESSION))
		{
			die('Caching requires sessions to be enabled');
		}
	}

	private function _getCache($key,$cache_name,$args)
	{
		$data = "data" == $key ? array() : NULL;
		if(isset($_SESSION['cache'][$cache_name]))
		{
			$cache_key = empty($args) ? NULL : md5(serialize($args));
			$cache = !$cache_key ? (empty($_SESSION['cache'][$cache_name]) ? array() : $_SESSION['cache'][$cache_name])
			: (empty($_SESSION['cache'][$cache_name][$cache_key]) ? array() : $_SESSION['cache'][$cache_name][$cache_key]);
			if(!empty($cache[$key]) && (!$cache['lifetime'] || time() - $cache['created'] < $cache['lifetime']))
			{
				$data = $cache[$key];
			}
			elseif(!empty($cache[$key]) && $cache['lifetime'] && time() - $cache['created'] > $cache['lifetime'])
			{
				unset($_SESSION['cache'][$cache_name]);
			}
		}

		return $data;
	}
}
?>