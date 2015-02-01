<?php defined('_PORTAL') or die();

Class RatesModel extends SelfcareBaseModel
{
	protected function getLocaleConstants()
	{
		$constants = array_merge(parent::getLocaleConstants(),array("rates","LANDLINE","mobile","voice_calls",'MOBILE_INTERNET',"select_destination","search"));
		return $constants;
	}
}
?>