<?php defined('_PORTAL') or die();

Class Template
{
	private $_registry;

	public function __construct(&$registry)
	{
		$this->_registry = $registry;
		$this->_registry->set('view_path', _SITE_PATH.'themes/'.$this->_registry->get('theme').'/');
		$this->_registry->set('theme_path', _REWRITE_BASE.'themes/'.$this->_registry->get('theme').'/');
	}

	public function show($data)
	{
		$route = $this->_registry->get('route');
		$task = $this->_registry->get('task');
		$user_info = Cache::getInstance()->getCache('userInfo');
		if($user_info)
		{
			extract($user_info);
			if(isset($password_expired) && "Y" == $password_expired->value && ("dashboard" != $route || "change_pass" != $task))
			{
				throw new Exception('Password has expired',402);
			}
			$js_time_format = empty($in_time_format) ? '' : Helper::DateTimeFormatCovertToJS($in_time_format);
			$js_date_format = empty($in_date_format) ? '' : Helper::DateTimeFormatCovertToJS($in_date_format);
			$time_format = empty($in_time_format) ? '' : Helper::DateTimeFormatCovertToPHP($in_time_format);
			$date_format = empty($in_date_format) ? '' : Helper::DateTimeFormatCovertToPHP($in_date_format);
		}
		$view_path = $this->_registry->get('view_path');
		$theme_path = $this->_registry->get('theme_path');
		$realm_path = defined('_REALM') ? $view_path.'html/'.(strtolower(_REALM)).'/' : NULL;
		$notifications = $this->_getNotifications();
		$locale = Cache::getInstance()->getCache('locale');
		$tmeplate_file = $this->_getTemplateFile();
		$title = isset($locale['TITLE_'.strtoupper($task)]) ? $locale['TITLE_'.strtoupper($task)] : (isset($locale['TITLE_'.strtoupper($route)]) ? $locale['TITLE_'.strtoupper($route)] : 'Loading...');
		extract($data);

		if('index' != $task && file_exists($view_path.$task.'.php'))
		{
			require_once $view_path.$task.'.php';
			exit;
		}
		elseif('error' == $this->_registry->get('route') || !$tmeplate_file)
		{
			$error = 'error' == $this->_registry->get('route') ? $this->_registry->get('errors') : array(array('content' => 'Not Found', 'code' => 404));
			$code = $error[0]['code'];
			$error_status = $error[0]['content'];
			$title = $code." ".$error_status;
			$notifications = array();
			$tmeplate_file = $view_path.'html/error.php';
		}

		if('browser' == $this->_registry->get('request_via'))
		{
			require_once $view_path.'index.php';
		}
		else
		{
			ob_start();
			require_once ($realm_path && file_exists($realm_path.'header.php') ? $realm_path.'header.php' :  $view_path.'html/header.php');
			$header = ob_get_contents();
			ob_end_clean();

			ob_start();
			require_once ($realm_path && file_exists($realm_path.'sidebar.php') ? $realm_path.'sidebar.php' :  $view_path.'html/sidebar.php');
			$sidebar = ob_get_contents();
			ob_end_clean();

			ob_start();
			require_once ($realm_path && file_exists($realm_path.'breadcrumps.php') ? $realm_path.'breadcrumps.php' :  $view_path.'html/breadcrumps.php');
			$breadcrumps = ob_get_contents();
			ob_end_clean();

			ob_start();
			require_once ($realm_path && file_exists($realm_path.'notifications.php') ? $realm_path.'notifications.php' :  $view_path.'html/notifications.php');
			$notifications = ob_get_contents();
			ob_end_clean();

			ob_start();
			require_once $tmeplate_file;
			$content = ob_get_contents();
			ob_end_clean();

			$output = array(
				'header' => $header,
				'sidebar' => $sidebar,
				'breadcrumps' => $breadcrumps,
				'notifications' => $notifications,
				'content' => $content,
				'route' => $route,
				'task' => $task,
				'title' => $title,
				'token' => $token
			);

			echo json_encode($output, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
		}
	}

	private function _getTemplateFile()
	{
		$temeplate_file = NULL;
		$route = $this->_registry->get('route');
		$task = $this->_registry->get('task');
		$view_path = $this->_registry->get('view_path');
		$possible_pathes = array();
		if(defined('_REALM') && 'ajax' == $this->_registry->get('request_via'))
		{
			$possible_pathes[] = $view_path.'html/'.strtolower(_REALM).'/'.$task.'.php';
		}
		$possible_pathes[] = $view_path.'html/'.$task.'.php';
		if(defined('_REALM') && 'ajax' == $this->_registry->get('request_via'))
		{
			$possible_pathes[] = $view_path.'html/'.strtolower(_REALM).'/'.$route.'.php';
		}
		$possible_pathes[] = $view_path.'html/'.$route.'.php';
		$possible_pathes[] = $view_path.$route.'.php';

		foreach($possible_pathes as $path)
		{
			if(file_exists($path))
			{
				$temeplate_file = $path;
				break;
			}
		}

		return $temeplate_file;
	}

	private function _getNotifications()
	{
		$notifications = array();
		$notification_attributes = array(
			'error' => array("class" => 'danger', "icon" => "fa-ban"),
			'notice' => array("class" => 'info', "icon" => "fa-info"),
			'warning' => array("class" => 'warning', "icon" => "fa-warning"),
			'success' => array("class" => 'success', "icon" => "fa-check"),
		);
		foreach(array('error','notice','warning','success') as $type)
		{
			$_notifications = NULL;
			if($_notifications = $this->_registry->get($type.'s'))
			{
				foreach($_notifications as $notification)
				{
					$notifications[] = array_merge(array(
							'class' => $notification_attributes[$type]["class"],
							"icon" => $notification_attributes[$type]["icon"],
							'type' => strtoupper($type)
						),$notification
					);
				}
			}
		}

		return $notifications;
	}
}

?>