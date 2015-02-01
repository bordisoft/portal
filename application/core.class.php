<?php defined('_PORTAL') or die();

Class Core
{
	public $registry;

	public function __construct(&$registry)
	{
		$route = $task = $action = NULL;
		$this->registry = $registry;
		$this->registry->set('request_via',((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? 'ajax' : 'browser'));
		getUserInfo:
		$userInfo = Cache::getInstance()->getCache('userInfo');
		$request_options = array(
			'server_url' => $this->registry->get('server_url'),
			'api_path' => $this->registry->get('api_path'),
			'xid' => isset($userInfo['xid']) ? $userInfo['xid'] : NULL,
			'realm' => isset($userInfo['realm']) ? $userInfo['realm'] : NULL,
		);
		$this->registry->set('request_options',$request_options);

		if($userInfo)
		{
			if (time() - $userInfo['created'] < $this->registry->get('session_lifetime') || isset($userInfo['remember']))
			{
				$userInfo['created'] = time();
				Cache::getInstance()->setCache('userInfo',$userInfo);

				if(!($this->registry->get('route')) || 'browser' == $this->registry->get('request_via'))
				{
					$route = 'dashboard';
					$task = 'blank';
					$action = 'get';
				}
				else
				{
					$route = $this->registry->get('route');
					$action = $this->registry->get('action');
					$task = $this->registry->get('task') ? $this->registry->get('task') : "index";
					if($this->registry->get('token') != Helper::getToken() || !$action || !$task)
					{
						$errors = array();
						if($this->registry->get('token') != Helper::getToken())
						{
							$errors[] = array("content" => "Forbidden", "code" => 403);
						}
						else
						{
							$errors[] = array("content" => "Not Found", "code" => 404);
						}
						$this->registry->set("errors",$errors);
						$this->registry->set("route","error");
						$template = new Template($this->registry);
						$template->show(array("token" => Helper::getToken(TRUE)));
						exit;
					}
				}
			}
			else
			{
				$errors = $this->registry->get('errors');
				$errors[] = array('content' => 'Session has expired','code' => 440);
				$this->registry->set('errors',$errors);
				JsonApi::getInstance($this->registry->get('request_options'))->Call(array('logout' => ''));
				session_unset();
				session_destroy();
				session_name("SelfcareSession");
				session_start();
				goto getUserInfo;
			}
		}
		elseif('browser' == $this->registry->get('request_via'))
		{
			$route = 'dashboard';
			$task = "restore" == $this->registry->get("task") ? "restore" : "login";
			$action = 'authorization' == $this->registry->get('action') ? 'authorization' : 'get';
		}
		else
		{
			echo json_encode(array('content' => "<script>window.location.href=url;</script>"), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
			exit;
		}

		$this->registry->set('route',$route);
		$this->registry->set('task',$task);
		$this->registry->set('action',$action);
	}
}

?>