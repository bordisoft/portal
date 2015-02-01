<?php defined('_PORTAL') or die();

Class BaseController
{
	protected $registry;

	public function __construct(&$registry)
	{
		$this->registry = $registry;
	}

	public function get()
	{
		$this->_display((array)$this->callModelMethod('get'));
	}

	public function set()
	{
		$requests = $this->callModelMethod('set');
		$data = (array)$this->callModelMethod('get',$requests);
		$this->_display($data);
	}

	protected function callModelMethod($task_prefix='',$requests=array())
	{
		$model_class = (defined('_REALM') ? _REALM.ucfirst($this->registry->get('route')) : ucfirst($this->registry->get('route'))).'Model';
		$_task = ($this->registry->get('task') == 'index') ? $this->registry->get('route') : $this->registry->get('task');
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
		$possible_tasks = array($task_prefix.ucfirst($task),$task);
		$data = NULL;
		foreach($possible_tasks as $task)
		{
			if(method_exists($model,$task) || method_exists($model,"_".$task))
			{
				$data = $model->call($task,$requests);
				break;
			}
		}

		if(is_null($data))
		{
			throw new Exception('Not Found',404);
		}

		return $data;
	}

	private function _display($data)
	{
		$data['token'] = Helper::getToken(TRUE);
		$template = new Template($this->registry);
		$template->show($data);
	}
}

Class DashboardController extends BaseController
{
	public function authorization()
	{
		$this->callModelMethod();
	}
}
?>