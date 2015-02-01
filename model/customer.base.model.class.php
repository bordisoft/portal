<?php defined('_PORTAL') or die();

Abstract Class CustomerBaseModel extends SelfcareBaseModel
{
	protected function getAllowed()
	{

	}

	protected function getUserInfoRequests()
	{
		return array(
			'get_status' => array(),
			'get_password_expired' => array(),
			'get_attributes' => array('balance','in_date_format','in_time_format','credit_limit','tz','name','firstname','lastname','iso_4217','login',"i_lang")
		);
	}

	protected function getFormFields($task=NULL)
	{
		$fields = array_merge(array(

		),parent::getFormFields());
		return $task ? (empty($fields[$task]) ? array() : $fields[$task]) : $fields;
	}
}

?>