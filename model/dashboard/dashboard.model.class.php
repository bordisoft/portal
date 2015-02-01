<?php defined('_PORTAL') or die();

Class DashboardModel extends SelfcareBaseModel
{
	protected function expose($task,$args)
	{
		$task = '_'.$task;
		if(is_callable(array($this, $task)))
		{
			return $this->$task($args);
		}
		return FALSE;
	}

	protected function getLocaleConstants()
	{
		$constants = parent::getLocaleConstants();
		return array_merge($constants,array('recent_calls','subscriber',"total_charged",'services','status','balance','COUNTRIES','incoming_calls','voice_calls',
				'outgoing_calls','MOBILE_INTERNET','more',"cancel"));
	}

	protected function getChangePass()
	{
		$allowed = Cache::getInstance()->getCache('allowed');
		if(empty($allowed["password_field"]) || "read" == $allowed["password_field"] || empty($allowed["ch_pass_form"]))
		{
			throw new Exception('Forbidden',403);
		}

		return array();
	}

	protected function setPass()
	{
		$this->registry->set("task","change_pass");
		$this->registry->set("action","get");
		$allowed = Cache::getInstance()->getCache('allowed');
		if(empty($allowed["password_field"]) || "read" == $allowed["password_field"] || empty($allowed["ch_pass_form"]))
		{
			throw new Exception('Forbidden',403);
		}
		$user_info = Cache::getInstance()->getCache('userInfo');
		$locale = Cache::getInstance()->getCache('locale');
		$args = $this->processArgs($this->getFormFields("ch_pass"));
		if($args["set_pass"]['new_password'] != $args["set_pass"]['retype_password'])
		{
			throw new Exception($locale["ERROR_409"],409);
		}
		elseif(strlen($args["set_pass"]['new_password']) < 6)
		{
			throw new Exception($locale["ERROR_421"],421);
		}
		elseif($user_info["password"] != $args["set_pass"]['old_password'])
		{
			throw new Exception($locale["ERROR_420"],420);
		}
		elseif($args["set_pass"]['new_password'] == $args["set_pass"]['old_password'])
		{
			throw new Exception($locale["ERROR_410"],410);
		}
		unset($args["set_pass"]['retype_password']);

		$this->getData($args);

		if(!$this->registry->get("errors"))
		{
			$user_info["password"] = $args["set_pass"]['new_password'];
			Cache::getInstance()->setCache("userInfo",$user_info);
		}
		$this->registry->set("task","index");

		return array();
	}

	private function _login()
	{
		$args = $this->processArgs($this->getFormFields("login"));
		$login = $args['login']['pb_auth_user'];
		$password = $args['login']['pb_auth_password'];
		$response = $this->apiResponse($args);

		if(!empty($response->xid) && !empty($response->realm))
		{
			if("Telisim" != $response->realm)
			{
				JsonApi::getInstance($this->registry->get('request_options'))->Call(array('logout' => ''));
				session_unset();
				session_destroy();
				session_name("SelfcareSession");
				session_start();
				throw new Exception("Unauthorized",401);
			}
			$userInfo = array(
				'xid' => $response->xid,
				'realm' => $response->realm,
				'login' => $login,
				'password' => $password,
				'created' => time()
			);

			if(isset($_REQUEST['remember_me']))
			{
				$userInfo['remember'] = 1;
			}
			Cache::getInstance()->setCache('userInfo',$userInfo);
			header('Location: '._REWRITE_BASE);
		}
		else
		{
			throw new Exception('Service unavailable',503);
		}
	}

	private function _restore()
	{
		$args = $this->processArgs($this->getFormFields("restore"));
		$response = $this->apiResponse($args);

		if(!empty($response->success))
		{
			$this->registry->set('successs',array('content' => $value->response->success));
			$this->registry->set('route','dashboard');
			$this->registry->set('task', "login");
			$template = new Template($this->registry);
			$template->show(array('token' => Helper::getToken(TRUE)));
		}
		else
		{
			throw new Exception('Service unavailable',503);
		}
	}

	private function _getLogin() { return array(); }

	private function _getRestore() { return array(); }
}
?>