<?php defined('_PORTAL') or die();

Class PaymentModel extends SelfcareBaseModel
{
	protected function getFormFields($task=NULL)
	{
		$fields = array(
			"credit_card" => array(
				'cc_i_payment_method' => array('mandatory' => 0,'type' => 'integer','html' => 'select','method' => 'set_credit_card'),
				'cc_number' => array('mandatory' => 2,'type' => 'string','method' => 'set_credit_card',"dependence" => "cc_i_payment_method","dependence_clause" => '%s'),
				'cc_exp_month' => array('mandatory' => 0,'type' => 'integer','html' => 'select','method' => 'set_credit_card'),
				'cc_exp_year' => array('mandatory' => 0,'type' => 'integer','html' => 'select','method' => 'set_credit_card'),
				'cc_cvv' => array('mandatory' => 2,'type' => 'string','method' => 'set_credit_card',"dependence" => "cc_i_payment_method","dependence_clause" => '%s'),
				'cc_name' => array('mandatory' => 2,'type' => 'string','method' => 'set_credit_card',"dependence" => "cc_i_payment_method","dependence_clause" => '%s'),
				'cc_address' => array('mandatory' => 2,'type' => 'string', "html" => "textarea",'method' => 'set_credit_card',"dependence" => "cc_i_payment_method","dependence_clause" => '%s'),
				'cc_city' => array('mandatory' => 2,'type' => 'string','method' => 'set_credit_card',"dependence" => "cc_i_payment_method","dependence_clause" => '%s'),
				'cc_iso_3166_1_a2' => array('mandatory' => 2,'type' => 'string','html' => 'select','child'=>'cc_i_country_subdivision','method' => 'set_credit_card',"dependence" => "cc_i_payment_method","dependence_clause" => '%s'),
				'cc_i_country_subdivision' => array('mandatory' => 2,'type' => 'integer','html' => 'select','parent'=>'cc_iso_3166_1_a2','method' => 'set_credit_card',"dependence" => "cc_i_payment_method","dependence_clause" => '%s'),
				'cc_zip' => array('mandatory' => 2,'type' => 'string','method' => 'set_credit_card',"dependence" => "cc_i_payment_method","dependence_clause" => '%s'),
				'alternate' => array('mandatory' => 0,'type' => 'integer','html' => 'hidden','method' => 'set_credit_card'),
				"ecommerce_enabled" => array("mandatory" => 0,"type" => "string","html" => "checkbox","method" => "set_attributes")
			),
			"ppayment" => array(
				"i_ppayment" =>	array('mandatory' => 0,'type' => 'integer', 'method' => 'set_ppayment'),
				"amount" =>	array('mandatory' => 1,'type' => 'float', 'method' => 'set_ppayment'),
				"i_periodical_payment_period" => array('mandatory' => 1,'type' => 'integer', 'method' => 'set_ppayment'),
				"balance_threshold"=> array('mandatory' => 2,'type' => 'float','method' => 'set_ppayment',"dependence" => "i_periodical_payment_period","dependence_clause" => '%s > 1'),
				"from_date" => array('mandatory' => 1,'type' => 'string', 'method' => 'set_ppayment'),
				"to_date" => array('mandatory' => 1,'type' => 'string', 'method' => 'set_ppayment'),
				"frozen" => array('mandatory' => 0,'type' => 'string', 'method' => 'set_ppayment', 'html' => "checkbox"),
				"discontinued" => array('mandatory' => 0,'type' => 'string', 'method' => 'set_ppayment', 'html' => "checkbox"),
			),
		);
		if(is_callable(array('parent','getFormFields')))
		{
			$fields = array_merge($fields,parent::getFormFields());
		}
		return $task ? (empty($fields[$task]) ? array() : $fields[$task]) : $fields;
	}

	protected function getPayment($requests=array())
	{/*Cache::getInstance()->clearCache('getPayment');*/
		$output = array();
		$this->registry->set('form_fields',$this->getFormFields("credit_card"));
		$input = $this->registry->get("args");
		if(!($output = Cache::getInstance()->getCache("getPayment",$input)))
		{
			$requests = array_merge($requests,array(
				"get_payment_info" => array(),
				"get_credit_card" => array(),
				"get_ppayments" => array(
					"filter" => (empty($input["filter"]) ? "Now" : $input["filter"]),
					"limit" => (empty($input["limit"]) || intval($input["limit"]) > 30 ? 10 : $input["limit"]),
					"from" => (empty($input["from"]) ? 0 : $input["from"])
				),
				"get_attributes" => (in_array(_REALM,array("Telisim","Account")) ? array("ecommerce_enabled") : array("ppm_enabled")),
			));
			$data = $this->getData($requests);
			if(!empty($data["get_credit_card_response"]->cc_i_payment_method) && is_array($data["get_credit_card_response"]->cc_i_payment_method->value))
			{
				foreach($data["get_credit_card_response"]->cc_i_payment_method->value as $key => $option)
				{
					if(in_array($option->value,array(8,10,11)))
					{
						unset($data["get_credit_card_response"]->cc_i_payment_method->value[$key]);
					}
				}
			}
			if(!empty($data["get_credit_card_response"]->cc_exp_date))
			{
				$exp_date = empty($data["get_credit_card_response"]->cc_exp_date) ? date("Y-m-d") : $data["get_credit_card_response"]->cc_exp_date;
				$exp_year = intval(preg_replace('/(\d{4})\-\d{2}\-\d{2}/',"$1",$exp_date->value));
				$exp_month = intval(preg_replace('/\d{4}\-(\d{2})\-\d{2}/',"$1",$exp_date->value));
				$access = $exp_date->access;

				unset($data["get_credit_card_response"]->cc_exp_date);

				$data["get_credit_card_response"]->cc_exp_year = new stdClass();
				$data["get_credit_card_response"]->cc_exp_year->value = array();
				$i = 0;
				$year = intval(date('Y'));
				while ($i <= 10)
				{
					$data["get_credit_card_response"]->cc_exp_year->value[$i] = new stdClass();
					$data["get_credit_card_response"]->cc_exp_year->value[$i]->value = $year;
					$data["get_credit_card_response"]->cc_exp_year->value[$i]->name = $year;
					$data["get_credit_card_response"]->cc_exp_year->value[$i]->sel = ($exp_year == $year ? 1 : 0);
					$i++;
					$year++;
				}
				$data["get_credit_card_response"]->cc_exp_year->access = $access;
				$data["get_credit_card_response"]->cc_exp_month = new stdClass();
				$data["get_credit_card_response"]->cc_exp_month->value = array();
				$i = 0;
				while ($i < 12)
				{
					$data["get_credit_card_response"]->cc_exp_month->value[$i] = new stdClass();
					$data["get_credit_card_response"]->cc_exp_month->value[$i]->value = $i+1;
					$data["get_credit_card_response"]->cc_exp_month->value[$i]->name = ($i+1 < 10 ? '0'.(string)($i+1) : (string)($i+1));
					$data["get_credit_card_response"]->cc_exp_month->value[$i]->sel = ($exp_month == $i+1 ? 1 : 0);
					$i++;
				}
				$data["get_credit_card_response"]->cc_exp_month->access = $access;
			}
			if(!empty($data["get_credit_card_response"]))
			{
				if(!empty($data["get_attributes_response"]))
				{
					$options = reset($data["get_attributes_response"]);
					$data["get_credit_card_response"]->ecommerce_enabled = $options;
				}
				$output["credit_card"] = (array)$data["get_credit_card_response"];
				$output["another_credit_card"] = array();
				foreach($output["credit_card"] as $attribute => $value)
				{
					$output["another_credit_card"][$attribute] = new stdClass();
					$output["another_credit_card"][$attribute]->value = $output["credit_card"][$attribute]->value;
					$output["another_credit_card"][$attribute]->access = $output["credit_card"][$attribute]->access;
					if(!empty($output["credit_card"][$attribute]->title))
					{
						$output["another_credit_card"][$attribute]->title = $output["credit_card"][$attribute]->title;
					}
					if("cc_i_country_subdivision" != $attribute)
					{
						if(!is_array($output["another_credit_card"][$attribute]->value))
						{
							$output["another_credit_card"][$attribute]->value = "";
						}
					}
				}
			}
			$output["payment_info"] = (array)$data["get_payment_info_response"];
			$output["ppayments"] = empty($data["get_ppayments_response"]) ? NULL : (array)$data["get_ppayments_response"];
			$output = array_merge(array("input" => $input),$output);

			Cache::getInstance()->setCache('getPayment',$output,$input);
		}

		return $output;
	}

	protected function getLocaleConstants()
	{
		$constants = array_merge(parent::getLocaleConstants(),array('voucher','use','amount','make_payment','another_cc_payment',"ok","cancel",
			'payment_info','another_cc_payment','ppayments','voucher_topup','pay_now','another_cc_payment',"ppayments",	"Balance Driven",
			"accepted","discontinued" ,"number_payments","i_periodical_payment_period","balance_threshold","from_date","to_date","frozen",
			"Balance Driven","weekly","monthly","pager_of","all","now"));
		return $constants;
	}

	protected function setCreditCardPayment()
	{
		$args = array();
		$input = $this->registry->get('args');
		$alternate = isset($input["alternate"]) ? 1 : 0;
		if($alternate)
		{
			foreach($input as $name => $value)
			{
				if(in_array($name,array("amount","alternate"))) {
					continue;
				}
				$key = str_replace("another_","",$name);
				$input[$key] = $value;
				unset($input[$name]);
			}
			$this->registry->set('args',$input);
			$args = $this->setCreditCard();
			$args["set_payment"] = $args["set_credit_card"];
			unset($args["set_credit_card"]);
		}
		$args = $args ? $args : array("set_payment" => array());
		$_args = $this->processArgs(array("amount" => array('mandatory' => 1,'type' => 'float','method' => 'set_payment')));
		$args["set_payment"]["amount"] = $_args["set_payment"]["amount"];
		if($args["set_payment"]["amount"] < 1)
		{
			throw new Exception('Amount is not big enough',604);
		}

		Cache::getInstance()->clearCache('getPayment');
		$this->registry->set('task','index');

		return $args;
	}

	protected function setPaypalPayment()
	{
		$args = $this->processArgs(array("amount" => array('mandatory' => 1,'type' => 'float','method' => 'set_paypal_payment')));
		$args["set_paypal_payment"]["return_page"] = ((@$_SERVER["HTTPS"] == "on") ? "https://" : "http://").
			$_SERVER["SERVER_NAME"].(($_SERVER["SERVER_PORT"] != "80") ? ":".$_SERVER["SERVER_PORT"] : '')._REWRITE_BASE;
		$args["set_paypal_payment"]["cancel_page"] = $args["set_paypal_payment"]["return_page"];
		$data = $this->getData($args);
		if(empty($data["set_paypal_payment_response"]))
		{
			$this->registry->set('task','index');
			return array();
		}
		Cache::getInstance()->clearCache('getPayment');
		echo json_encode(array('content' => "<div style=\"display:none;\" id=\"paypal-form\">".$data["set_paypal_payment_response"]
				."</div><script>$(\"#paypal-form form\").submit();loader($('body'));</script>")
				, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
		exit;
	}

	protected function setPpayment()
	{
		$args = $this->processArgs($this->getFormFields("ppayment"));
		$args["set_ppayment"]["action"] = empty($args["set_ppayment"]["i_ppayment"]) ? "add" : "update";
		Cache::getInstance()->clearCache('getPayment');
		$this->registry->set('task','index');

		return $args;
	}

	protected function setCreditCard()
	{
		$args = $this->processArgs($this->getFormFields("credit_card"));
		if(isset($args["set_credit_card"]['cc_exp_month']) && isset($args["set_credit_card"]['cc_exp_year']))
		{
			$args["set_credit_card"]['cc_exp_date'] = (string)$args["set_credit_card"]['cc_exp_year'].
				'-'.(intval($args["set_credit_card"]['cc_exp_month']) < 10 ? '0'.(string)$args["set_credit_card"]['cc_exp_month'] : (string)$args["set_credit_card"]['cc_exp_month']).
				'-01';
			unset($args["set_credit_card"]['cc_exp_month']);
			unset($args["set_credit_card"]['cc_exp_year']);
		}
		if(empty($args["set_credit_card"]['cc_number']) || strpos($args["set_credit_card"]['cc_number'], 'xxx') !== FALSE)
		{
			unset($args["set_credit_card"]['cc_number']);
		}
		if(empty($args["set_credit_card"]['cc_i_payment_method']))
		{
			$args["set_credit_card"]['cc_i_payment_method'] =  "";
			foreach($args["set_credit_card"] as $key => $value)
			{
				if("cc_i_payment_method" != $key)
				{
					unset($args["set_credit_card"][$key]);
				}
			}
		}
		if(isset($args["set_attributes"]["ecommerce_enabled"]) && !in_array(_REALM,array("Telisim","Account")))
		{
			$args["set_attributes"]["ppm_enabled"] = $args["set_attributes"]["ecommerce_enabled"];
			unset($args["set_attributes"]["ecommerce_enabled"]);
		}

		Cache::getInstance()->clearCache('getPayment');
		$this->registry->set('task','index');

		return $args;
	}
}

Class AccountPaymentModel extends PaymentModel
{
	protected function setVoucherTopup()
	{
		$args = $this->processArgs(array("voucher" => array('mandatory' => 1,'type' => 'string','method' => 'set_voucher_topup')));
		Cache::getInstance()->clearCache('getPayment');
		$this->registry->set('task','index');

		return $args;
	}
}

Class CustomerPaymentModel extends PaymentModel {}

Class TelisimPaymentModel extends AccountPaymentModel {}

?>