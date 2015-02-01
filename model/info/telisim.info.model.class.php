<?php defined('_PORTAL') or die();

Class TelisimInfoModel extends AccountInfoModel
{
	protected function getInfo($requests = array())
	{/*Cache::getInstance()->clearCache('getInfo');*/
		$output = array();
		$this->registry->set('form_fields',$this->getFormFields('info'));

		if(!($output = Cache::getInstance()->getCache('getInfo')))
		{
			$output = array(
				'subscriber_info' => array(),
				'sim_info' => array()
			);

			$requests['get_attributes'] = array('expiration_date','issue_date','first_usage','last_usage','email','last_recharge');
			$requests['get_aliases'] = array();
			$response = parent::getInfo($requests);

			$subscriber_info = array('firstname','lastname','baddr1','state','zip','country','cont1');
			foreach($subscriber_info as $attribute)
			{
				if(isset($response['get_subscriber_response']->$attribute))
				{
					$output['subscriber_info'][$attribute] = $response['get_subscriber_response']->$attribute;
				}
			}
			if(isset($response['get_attributes_response']->email))
			{
				$output["subscriber_info"]["email"] = $response['get_attributes_response']->email;
			}

			if(!empty($response['get_custom_fields_response']))
			{
				$sim_info = array('SIM_PIN1' => 'PIN', 'SIM_PUK1' => 'PUK');
				foreach($sim_info as $field => $title)
				{
					foreach((array)$response['get_custom_fields_response'] as $_field => $value)
					{
						if($field == $_field)
						{
							$output["sim_info"][$field] = $value;
							$output["sim_info"][$field]->title = $title;
						}
					}
				}
			}

			if(!empty($response['get_attributes_response']))
			{
				if(isset($response['get_attributes_response']->centrex) && "update" == $response['get_attributes_response']->centrex->access)
				{
					$i = 0;
					$centrex_value = $response['get_attributes_response']->centrex->value;
					foreach($response['get_aliases_response']->list as $alias_info)
					{
						if("Y" == $alias_info->blocked && FALSE === strpos($alias_info->id,"*44*4*"))
						{
							if(!isset($output['sim_info']['centrex']))
							{
								$output['sim_info']['centrex'] = $response['get_attributes_response']->centrex;
								$output['sim_info']['centrex']->value = array();
							}
							$value = "|".$alias_info->id;;
							$output['sim_info']['centrex']->value[$i] = new stdClass();
							$output['sim_info']['centrex']->value[$i]->value = $value;
							$output['sim_info']['centrex']->value[$i]->name = $alias_info->id;
							$output['sim_info']['centrex']->value[$i]->sel = $value == $centrex_value ? 1 : 0;
							$i++;
						}
					}
				}
				elseif(isset($response['get_attributes_response']->centrex))
				{
					$output["sim_info"]["centrex"] = $response['get_attributes_response']->centrex;
					$output["sim_info"]["centrex"]->value = str_replace("|", "", $output["sim_info"]["centrex"]->value);
				}

				$sim_info = array('expiration_date','issue_date','first_usage','last_usage','last_recharge');
				foreach($sim_info as $attribute)
				{
					if(isset($response['get_attributes_response']->$attribute))
					{
						$output['sim_info'][$attribute] = $response['get_attributes_response']->$attribute;
					}
				}
			}
			Cache::getInstance()->setCache('getInfo',$output);
		}

		return $output;
	}

	protected function setSim()
	{
		$form_fields = array();
		$info_fields = $this->getFormFields("info");
		foreach($info_fields as $field => $options)
		{
			if("set_attributes" == $options["method"] && "centrex" == $field || "set_custom_fields" == $options["method"])
			{
				$form_fields[$field] = $options;
			}
		}
		$requests = $this->processArgs($form_fields);
		if(FALSE === strpos($requests["set_attributes"]["centrex"], "|"))
		{
			throw new Exception('Bad Request',400);
		}
		$requests["set_attributes"]["cli"] = "Y";
		$requests["set_attributes"]["display_number_check"] = "D";
		Cache::getInstance()->clearCache('getInfo');
		$this->registry->set("task","index");

		return $requests;
	}
}
?>
