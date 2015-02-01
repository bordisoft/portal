<?php defined('_PORTAL') or die();

Class InfoModel extends SelfcareBaseModel
{
	protected function setInfo()
	{
		$requests = $this->processArgs($this->getFormFields("info"));
		Cache::getInstance()->clearCache('getInfo');
		$this->registry->set("task","index");

		return $requests;
	}

	protected function getLocaleConstants()
	{
		$constants = array_merge(parent::getLocaleConstants(),array('subscriber'));
		return $constants;
	}

	protected function getInfo($requests=array())
	{
		$form_fields = $this->registry->get('form_fields');
		foreach($form_fields as $field => $options)
		{
			if(isset($options['param_key']))
			{
				$requests[str_replace('set_', 'get_', $options['method'])][$options['param_key']][] = $field;
			}
			else
			{
				$requests[str_replace('set_', 'get_', $options['method'])][] = $field;
			}
		}

		return $this->getData($requests);
	}
}
?>