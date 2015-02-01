<?php defined('_PORTAL') or die();

Abstract Class AccountBaseModel
{
	protected function getAllowed()
	{
		return array(
			'dashboard' => 'read',
			'info' => array('attr' => 'Portal-Info', 'obj' => 'WebForms'),
			'payment' => array('attr' => 'Portal-Payments', 'obj' => 'WebForms'),
			'rates' => array('attr' => 'Portal-RateCalculator', 'obj' => 'WebForms'),
			'xdrs' => array('attr' => 'Portal-Xdrs', 'obj' => 'WebForms'),
			'password_field' => array('attr' => "password", 'obj' => ("Telisim" == _REALM ? "Account" : _REALM)."s"),
			'ch_pass_form' => array('attr' => "Change Password", 'obj' => "WebForms")
		);
	}

	protected function getUserInfoRequests()
	{
		return array(
			'get_status' => array(),
			'get_password_expired' => array(),
			'get_attributes' => array('balance','in_date_format','in_time_format','credit_limit','tz','id','iso_4217','login',"i_lang","bm_name"),
			'get_subscriber' => array('firstname','lastname')
		);
	}

	protected function getFormFields($task=NULL)
	{
		$fields = array(

		);
		return $task ? (empty($fields[$task]) ? array() : $fields[$task]) : $fields;
	}

	protected function processDefaultRequests($response)
	{
		if(isset($response['get_subscriber_response']))
		{
			$user_info_requests = $this->getUserInfoRequests();
			$cache = Cache::getInstance()->getCache('userInfo');
			foreach($user_info_requests['get_subscriber'] as $attribute)
			{
				if(isset($response['get_subscriber_response']->$attribute))
				{
					$cache[$attribute] = $response['get_subscriber_response']->$attribute->value;
				}
			}
			Cache::getInstance()->setCache('userInfo',$cache);
		}

		return $response;
	}
}

?>