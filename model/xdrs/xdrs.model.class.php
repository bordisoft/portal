<?php defined('_PORTAL') or die();

Class XdrsModel extends SelfcareBaseModel
{
	protected function getLocaleConstants()
	{
		$constants = array_merge(parent::getLocaleConstants(),array("voice_calls","subscriptions","payments","credits","amount","pager_of",
				"all","fee_name","MOBILE_INTERNET","from","to","description","date_time","charged_time","quantity","country","connect_time",
				"date","duration","comment","fee_type","fee_name","from_date","to_date","services","search","filter","ok","cancel"));
		return $constants;
	}

	protected function getXdrs($requests=array())
	{/*Cache::getInstance()->clearCache('getXdrs');*/
		$output = array();
		$input = $this->registry->get("args");
		$services = $this->registry->get('services') ? $this->registry->get('services') : array(
			"voice_calls" => array("account_id","cli","cld","country","description","connect_date","connect_time","duration","amount"),
			"subscriptions" => array("account_id","fee_type","fee_name","from_date","to_date","amount"),
			"payments" => array("account_id","description","comment","date_time","amount"),
			"credits" => array("account_id","description","comment","date_time","amount"),
			"data_service" => array("account_id","description","date_time","country","quantity","amount"),
			"quantity_based" => array("account_id","description","date_time","country","quantity","amount"),
		);
		$this->registry->set('services',$services);

		if(!($output = Cache::getInstance()->getCache("getXdrs",$input)))
		{
			$dates = empty($input["dates"]) ? array() : explode(" - ",$input["dates"]);
			$from_date = date("Y-m-d H:i:s",strtotime((empty($dates[0]) ? "-3 month" : $dates[0])));
			$to_date = date("Y-m-d H:i:s",(empty($dates[1]) ? time() : strtotime($dates[1])));
			$args = array("get_cdrs" => array());
			foreach($services as $service => $options)
			{
				if(empty($input["service"]) || $service == $input["service"])
				{
					$args["get_cdrs"][$service] = array(
						"from_date" => $from_date,
						"to_date" => $to_date,
						"limit" => 	empty($input[$service]["limit"]) ? 10 : intval($input[$service]["limit"]),
						"from" => empty($input[$service]["from"]) ? 0 : intval($input[$service]["from"]),
					);
					if(!empty($input["service"])) break;
				}
			}
			$response = $this->getData(array_merge($requests,$args));
			$output = array_merge((array)$response["get_cdrs_response"], array("input" => $input));
			Cache::getInstance()->setCache("getXdrs",$output,$input);
		}
		$cache_timestamp = date("Y/m/d H:i:s", Cache::getInstance()->getCacheTimestamp("getXdrs",$input));

		return array_merge($output, array("cache_timestamp" => $cache_timestamp));
	}
}

Class TelisimXdrsModel extends XdrsModel
{
	protected function getXdrs($requests=array())
	{
		$services = array(
			"voice_calls" => array("cli","cld","country","description","connect_date","connect_time","duration","amount"),
			"subscriptions" => array("fee_type","fee_name","from_date","to_date","amount"),
			"payments" => array("description","comment","date_time","amount"),
			"credits" => array("description","comment","date_time","amount"),
			"data_service" => array("description","date_time","country","quantity","amount"),
			"quantity_based" => array("description","date_time","country","quantity","amount"),
		);
		$this->registry->set('services',$services);

		return parent::getXdrs($requests);
	}
}

?>