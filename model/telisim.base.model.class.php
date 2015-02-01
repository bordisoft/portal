<?php defined('_PORTAL') or die();

Abstract Class TelisimBaseModel extends AccountBaseModel
{
	protected function getAllowed()
	{
		return array_merge(parent::getAllowed(), array('rates' => array('attr' => 'Portal-RateCalculator', 'obj' => 'WebForms')));
	}

	protected function getFormFields($task=NULL)
	{
		$fields = array(
			'info' => array(
				'firstname' => array('mandatory' => 0,'type' => 'string','method' => 'set_subscriber'),
				'lastname' => array('mandatory' => 0,'type' => 'string','method' => 'set_subscriber'),
				'baddr1' => array('mandatory' => 0,'type' => 'string','method' => 'set_subscriber','html' => 'textarea'),
				'state' => array('mandatory' => 0,'type' => 'string','method' => 'set_subscriber'),
				'zip' => array('mandatory' => 0,'type' => 'string','method' => 'set_subscriber'),
				'city' => array('mandatory' => 0,'type' => 'string','method' => 'set_subscriber'),
				'country' => array('mandatory' => 0,'type' => 'string','method' => 'set_subscriber'),
				'email' => array('mandatory' => 0,'type' => 'string','method' => 'set_attributes'),
				'phone1' => array('mandatory' => 0,'type' => 'string','method' => 'set_subscriber'),
				'centrex' => array('mandatory' => 1,'type' => 'string','method' => 'set_attributes','html' => 'select'),
				'SIM_PIN1' => array('mandatory' => 0,'type' => 'string','method' => 'set_custom_fields','param_key' => 'fields'),
				'SIM_PUK1' => array('mandatory' => 0,'type' => 'string','method' => 'set_custom_fields','param_key' => 'fields'),
			)
		);
		return $task ? (empty($fields[$task]) ? array() : $fields[$task]) : $fields;
	}

	protected function getLocaleConstants()
	{
		return array('TITLE_DASHBOARD','TITLE_INFO','TITLE_PAYMENT','TITLE_XDRS','TITLE_CALCULATOR');
	}

	protected function getDefaultRequests()
	{
		return array("get_sim_location" => array());
	}

	protected function processDefaultRequests($response)
	{
		$response = is_callable(array('parent','processDefaultRequests')) ? parent::processDefaultRequests($response) : $response;
		if(isset($response['get_sim_location_response']))
		{
			$cache = Cache::getInstance()->getCache('userInfo');
			$cache["sim_location"] = $response['get_sim_location_response'];
			unset($response['get_sim_location_response']);
			Cache::getInstance()->setCache('userInfo',$cache);
		}

		return $response;
	}
}

?>