<?php defined('_PORTAL') or die();

Class AccountInfoModel extends InfoModel
{
	protected function setSubscriber()
	{
		$form_fields = array();
		$info_fields = $this->getFormFields("info");
		foreach($info_fields as $field => $options)
		{
			if("set_subscriber" == $options["method"] || "email" == $field)
			{
				$form_fields[$field] = $options;
			}
		}
		$requests = $this->processArgs($form_fields);
		Cache::getInstance()->clearCache('getInfo');
		$this->registry->set("task","index");

		return $requests;
	}
}
?>