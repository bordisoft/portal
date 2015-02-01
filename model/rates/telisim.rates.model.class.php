<?php defined('_PORTAL') or die();

Class TelisimRatesModel extends RatesModel
{
	protected function getFormFields($task=NULL)
	{
		$fields = array_merge(parent::getFormFields(),array(
			'calculator' => array(
				'iso_3166_1_a2_from' => array('mandatory' => 1,'type' => 'string','method' => 'calculate_rates'),
				'iso_3166_1_a2_to' => array('mandatory' => 1,'type' => 'string','method' => 'calculate_rates')
			)
		));
		return $task ? (empty($fields[$task]) ? array() : $fields[$task]) : $fields;
	}

	protected function getLocaleConstants()
	{
		$constants = array_merge(parent::getLocaleConstants(),array("CALL_FROM","CALL_TO"));
		return $constants;
	}

	protected function getCalculator($requests = array())
	{
		$output = array();
		$input = $this->registry->get("args");

		if(!($output = Cache::getInstance()->getCache('getCalculator',$input)))
		{
			$request = $input ? $this->processArgs($this->getFormFields("calculator")) : array();
			$response = $this->getData(array_merge($requests,$request));
			if(isset($response["calculate_rates_response"]))
			{
				$result = (array)$response["calculate_rates_response"];
				foreach(array("vc_landline","vc_mobile","sms","mobile_internet") as $name)
				{
					$output[$name] = ($result[$name]->price_max == $result[$name]->price_min)
						? $result[$name]->price_max
						: $result[$name]->price_min."...".$result[$name]->price_max;
				}
			}
			$output = array_merge($output, array("countries" => Helper::GetLocations("countries")));
			Cache::getInstance()->setCache('getCalculator',$output,$input);
		}
		if(!empty($input)) { $output["input"] = $input; }

		return $output;
	}
}
?>