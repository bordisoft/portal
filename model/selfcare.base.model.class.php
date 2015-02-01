<?php defined('_PORTAL') or die();

if(!defined('_REALM'))
{
	Abstract Class DynamicParent {}
}
elseif('Telisim' == _REALM)
{
	Abstract Class DynamicParent extends TelisimBaseModel {}
}
elseif('Account' == _REALM)
{
	Abstract Class DynamicParent extends AccountBaseModel {}
}
elseif('Customer' == _REALM)
{
	Abstract Class DynamicParent extends CustomerBaseModel {}
}
else
{
	throw new Exception('Internal Server Error', 500);
}

Abstract Class SelfcareBaseModel extends DynamicParent
{
	protected $registry;

	public function __construct(&$registry)
	{
		$this->registry = $registry;
		$route = $this->registry->get('route');
		$allowed = Cache::getInstance()->getCache('allowed');
		if($allowed)
		{
			if(empty($allowed[$route]))
			{
				throw new Exception('Not Found',404);
			}
		}
	}

	public function call($task,$args)
	{
		if(is_callable(array($this, $task)))
		{
			return $this->$task($args);
		}
		elseif(is_callable(array($this,'expose')))
		{
			$method = 'expose';
			return $this->$method($task,$args);
		}
		return FALSE;
	}

	protected function getData($requests=array())
	{
		$request = array();
		$default_requests = $this->getDefaultRequests();
		if(isset($default_requests['get_attributes']) && isset($requests['get_attributes']))
		{
			$request['get_attributes'] = array_merge($default_requests['get_attributes'],$requests['get_attributes']);
			unset($default_requests['get_attributes']);
			unset($requests['get_attributes']);
		}
		if(isset($default_requests['get_subscriber']) && isset($requests['get_subscriber']))
		{
			$request['get_subscriber'] = array_merge($default_requests['get_subscriber'],$requests['get_subscriber']);
			unset($default_requests['get_subscriber']);
			unset($requests['get_subscriber']);
		}
		$request = array_merge(array_merge($request,array_merge($requests,$default_requests)),$request);
		$response = empty($request) ? array() : (array)$this->apiResponse($request);

		if($response)
		{
			$response_types = array('error','notice','warning','success');
			foreach($response_types as $response_type)
			{
				${$response_type.'s'} = $this->registry->get($response_type.'s') ? $this->registry->get($response_type.'s') : array();
			}
			foreach($response as $method => $value)
			{
				foreach(array('notice','warning','success') as $response_type)
				{
					if(!empty($value->response->$response_type))
					{
						$exists = 0;
						foreach(${$response_type.'s'} as $_mes)
						{
							if($value->response->$response_type == $_mes["content"])
							{
								$exists = 1;
								break;
							}
						}
						if(!$exists) {
							${$response_type.'s'}[] = array('content' => $value->response->$response_type);
						}
						unset($response[$method]);
					}
				}
				if(!empty($value->error))
				{
					unset($response[$method]);
					if(FALSE !== strpos($method,"get_")) continue;
					$exists = 0;
					foreach($errors as $_mes)
					{
						if($value->code == $_mes["code"])
						{
							$exists = 1;
							break;
						}
					}
					if(!$exists) {
						$errors[] = array('content' => $value->response, 'code' => $value->code);
					}
				}
				if(isset($response[$method]))
				{
					$response[$method] = $value->response;
				}
			}
			foreach($response_types as $response_type)
			{
				if(!empty(${$response_type.'s'}))
				{
					$this->registry->set($response_type.'s',${$response_type.'s'});
				}
			}
			$response = $this->processDefaultRequests($response);
		}

		return $response;
	}

	protected function getLocaleConstants()
	{
		$constants =  array('SUCCESS','NOTICE','WARNING','ERROR','JS_THIS','JS_MAND_CHECKBOX','JS_SELECT_ITEM','HELLO','JS_INVALID_CHARACTER','statistics',
			'JS_MAND_FIELD','JS_ENTER_VALID_EMAIL','UPDATE','CHANGE_PASS','LOGOUT','edit','add','delete','user','old_password','new_password','retype_password',
			'ERROR_409','ERROR_410','ERROR_420','ERROR_421');
		if(is_callable(array('parent','getLocaleConstants')))
		{
			$constants = array_merge(parent::getLocaleConstants(),$constants);
		}
		return $constants;
	}

	protected function getFormFields($task=NULL)
	{
		$fields = array(
			'login' => array(
				'pb_auth_user' => array('mandatory' => 1,'type' => 'string','method' => 'login'),
				'pb_auth_password' => array('mandatory' => 1,'type' => 'string','method' => 'login'),
				'remember_me' => array('mandatory' => 0,'type' => 'integer','method' => 'login')
			),
			'restore' => array(
				'pb_auth_user' => array('mandatory' => 1,'type' => 'string','method' => 'restore'),
				'email' => array('mandatory' => 1,'type' => 'string','method' => 'restore'),
			),
			'ch_pass' => array(
				'old_password' => array('mandatory' => 1,'type' => 'string','method' => 'set_pass'),
				'new_password' => array('mandatory' => 1,'type' => 'string','method' => 'set_pass'),
				'retype_password' => array('mandatory' => 1,'type' => 'string','method' => 'set_pass')
			)
		);
		if(is_callable(array('parent','getFormFields')))
		{
			$fields = array_merge($fields,parent::getFormFields());
		}
		return $task ? (empty($fields[$task]) ? array() : $fields[$task]) : $fields;
	}

	protected function apiResponse($requests)
	{
		$result = JsonApi::getInstance($this->registry->get('request_options'))->Call($requests);

		if(empty($result))
		{
			throw new Exception('Service unavailable',503);
		}
		elseif($result->error)
		{
			throw new Exception($result->response,$result->code);
		}

		return $result->response;
	}

	protected function processArgs($form_fields)
	{
		$args = $mandatory_fields = array();
		$input = $this->registry->get('args');
		foreach($form_fields as $var => $options)
		{
			if(isset($input[$var]))
			{
				$value = $input[$var];
				if('integer' == $options['type'])
				{
					$value = intval($value);
				}
				elseif('float' == $options['type'])
				{
					$value = floatval($value);
				}
				elseif('string' == $options['type'])
				{
					$value = Helper::PrepareString($value);
				}
				if(!$value && $options['mandatory'] === 1)
				{
					$mandatory_fields[] = $var;
				}
				else if(!$value && $options['mandatory'] === 2)
				{
					$dependence = empty($input[$options['dependence']]) ? "0" : $input[$options['dependence']];
					$clause = str_replace("%s",$dependence,$options["dependence_clause"]);
					eval('$clause = !('.$clause.');');
					if(!$clause)
					{
						$mandatory_fields[] = $var;
					}
				}
				$args[$options['method']][$var] = $value;
				unset($input[$var]);
			}
			elseif (!empty($options['html']) && 'checkbox' == $options['html'])
			{
				$args[$options['method']][$var] = 'N';
			}
			elseif ($options['mandatory'] === 1)
			{
				$mandatory_fields[] = $var;
			}
		}
		if($mandatory_fields)
		{
			throw new Exception('Bad Request',400);
		}
		$this->registry->set('args',$input);

		return $args;
	}

	protected function getDefaultRequests()
	{
		$default_requets = is_callable(array('parent','getDefaultRequests')) ? parent::getDefaultRequests() : array();
		$args = array();
		$locale_constants = $this->getLocaleConstants();
		$cache = Cache::getInstance()->getCache('locale');
		foreach($locale_constants as $constant)
		{
			if(!$cache || !isset($cache[$constant])) $args[] = $constant;
		}
		if($args) $default_requets['get_locale'] = $args;
		$cache = Cache::getInstance()->getCache('allowed');
		if(!$cache) $default_requets['check_access'] = $this->getAllowed();
		$default_requets = array_merge($default_requets,$this->getUserInfoRequests());

		return $default_requets;
	}

	protected function processDefaultRequests($response)
	{
		$response = is_callable(array('parent','processDefaultRequests')) ? parent::processDefaultRequests($response) : $response;
		if(isset($response['get_locale_response']))
		{
			$cache = Cache::getInstance()->getCache('locale');
			$cache = $cache ? $cache : array();
			foreach((array)$response['get_locale_response'] as $constant => $translation)
			{
				$cache[$constant] = $translation;
			}
			Cache::getInstance()->setCache('locale',$cache);
			unset($response['get_locale_response']);
		}
		if(isset($response['check_access_response']))
		{
			$cache = Cache::getInstance()->getCache('allowed');
			foreach((array)$response['check_access_response'] as $attribute => $value)
			{
				$cache[$attribute] = $value;
			}
			unset($response['check_access_response']);
			Cache::getInstance()->setCache('allowed',$cache);
		}
		if(isset($response['get_password_expired_response']))
		{
			$cache = Cache::getInstance()->getCache('userInfo');
			$cache['password_expired'] = $response['get_password_expired_response'];
			unset($response['get_password_expired_response']);
			Cache::getInstance()->setCache('userInfo',$cache);
		}
		if(isset($response['get_status_response']))
		{
			$cache = Cache::getInstance()->getCache('userInfo');
			$cache['status'] = $response['get_status_response'];
			unset($response['get_status_response']);
			Cache::getInstance()->setCache('userInfo',$cache);
		}
		if(isset($response['get_attributes_response']))
		{
			$user_info_requests = $this->getUserInfoRequests();
			$cache = Cache::getInstance()->getCache('userInfo');
			foreach($user_info_requests['get_attributes'] as $attribute)
			{
				if(isset($response['get_attributes_response']->$attribute))
				{
					if("i_lang" == $attribute)
					{
						$cache[$attribute] = $response['get_attributes_response']->$attribute;
					}
					elseif(is_array($response['get_attributes_response']->$attribute->value))
					{
						foreach($response['get_attributes_response']->$attribute->value as $val)
						{
							if(!empty($val->sel))
							{
								$cache[$attribute] = $val->value;
								break;
							}
						}
					}
					else
					{
						$cache[$attribute] = isset($response['get_attributes_response']->$attribute->selection)
							? $response['get_attributes_response']->$attribute->selection
							: $response['get_attributes_response']->$attribute->value;
					}
				}
				else
				{
					throw new Exception('Service Unavailable',503);
				}
			}
			$balance = floatval(str_replace(array("(",")"),array("",""),$cache['balance']));
			$credit_limit = empty($cache['credit_limit']) ? 0 : floatval(str_replace(array("(",")"),array("",""),$cache['credit_limit']));
			$cur_balance = isset($cache["bm_name"]) && "Debit" == $cache["bm_name"] ? $balance : abs($credit_limit - $balance);
			$cache['balance'] = $cur_balance." ".$cache['iso_4217'];
			Cache::getInstance()->setCache('userInfo',$cache);
		}

		return $response;
	}

	protected function setLocale()
	{
		$requests = $this->processArgs(array('i_lang' => array('mandatory' => 1,'type' => 'string','method' => 'set_attributes')));
		$userInfo = Cache::getInstance()->getCache('userInfo');
		Cache::getInstance()->clearCache();
		Cache::getInstance()->setCache('userInfo',$userInfo);
		$input = $this->registry->get('args');
		$this->registry->set("task",$input["task"]);
		$this->registry->set("route",$input["route"]);
		unset($input["task"]);
		unset($input["route"]);
		$this->registry->set('args',$input);

		return $requests;
	}

	protected function setRefresh()
	{
		$args = $this->processArgs(array(
			'route' => array('mandatory' => 1,'type' => 'string','method' => 'input'),
			'task' => array('mandatory' => 1,'type' => 'string','method' => 'input'),
		));
		$model_class = _REALM.ucfirst($args["input"]["route"])."Model";
		$_task = ($args["input"]["task"] == 'index') ? $args["input"]["route"] : $args["input"]["task"];
		$task = '';
		$i = 0;
		foreach(explode('_',$_task) as $str)
		{
			$task .= $i++ ? ucfirst($str) : $str;
		}

		if(!class_exists($model_class))
		{
			throw new Exception('Not Found',404);
		}
		$model = new $model_class($this->registry);
		$possible_tasks = array("get".ucfirst($task),$task);
		$cache_name = NULL;
		foreach($possible_tasks as $task)
		{
			$__task = method_exists($model,$task) ? $task : (method_exists($model,"_".$task) ? "_".$task : NULL);
			if($__task)
			{
				$cache_name = $__task;
				break;
			}
		}
		unset($model);
		Cache::getInstance()->clearCache($cache_name);
		$this->registry->set("task",$args["input"]["task"]);
		$this->registry->set("route",$args["input"]["route"]);

		return array();
	}

	protected function getBlank() { return array(); }

	protected function logout()
	{
		JsonApi::getInstance($this->registry->get('request_options'))->Call(array('logout' => ''));

		session_unset();
		session_destroy();
		session_name("SelfcareSession");
		session_start();

		echo json_encode(array('content' => "<script>window.location.href=url;</script>"), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
		exit;
	}
}

?>