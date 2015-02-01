<?php defined('_PORTAL') or die();

Class TelisimDashboardModel extends AccountDashboardModel
{
	protected function getDashboard($requests=array())
	{/*Cache::getInstance()->clearCache('getDashboard');*/
		$output = array();
		if(!($output = Cache::getInstance()->getCache('getDashboard')))
		{
			$output = array(
				'calls' => array(),
				'countries'	=> array(),
				'sms' => array(),
				'mobile_data' => array(),
				'percentages' => array(
					'calls_incoming' => '0%',
					'calls_outgoing' => '0%',
					'sms' => '0%',
					'mobile_data' => '0%'
				),
				'bars' => array(),
				'totals' => array(
					'calls_incoming' => '0',
					'calls_outgoing' => '0',
					'sms' => '0',
					'mobile_data' => '0'
				)
			);
			$cdrs = $args = array();
			$from_date = date('Y-m-d H:i:s', strtotime('-1 month'));
			$to_date = date('Y-m-d H:i:s', strtotime('+1 day'));
			foreach(array('voice_calls','data_service','quantity_based') as $service)
			{
				$args[$service] = array(
					'from_date' => $from_date,
					'to_date' => $to_date
				);
			}
			$data = $this->getData(array_merge($requests,array('get_cdrs' => $args)));
			$cdrs = (array)$data['get_cdrs_response'];

			if(!empty($cdrs['voice_calls']->total_count) || !empty($cdrs['data_service']->total_count) || !empty($cdrs['quantity_based']->total_count))
			{
				$countries = $calls = $sms = $mobile_data = $percentages = $bars = $totals = array();
				$voice_calls = empty($cdrs['voice_calls']) ? array() : (array)$cdrs['voice_calls'];
				if(!empty($voice_calls['list']))
				{
					foreach($voice_calls['list'] as $call)
					{
						$country = $this->_getCountryCode($call->country);
						$date = date('Y-m-d',$call->unix_connect_time);
						if(!isset($countries[$country]))
						{
							$countries[$country] = array(
								'calls' => array(
									'incoming' => array('duration' => 0, 'number' => 0, 'price' => 0),
									'outgoing' => array('duration' => 0, 'number' => 0, 'price' => 0),
									'other' => array('duration' => 0, 'number' => 0, 'price' => 0)
								),
							);
						}
						$direction = "LEGA+LEGB" == $call->accessibility ? "outgoing" : ("CALLBACK_FAIL" == $call->accessibility ? "other" : "incoming");
						++$countries[$country]['calls'][$direction]['number'];
						$countries[$country]['calls'][$direction]['price'] += $call->amount;
						$tmp = explode(':',$call->duration);
						$duration = $tmp[0]*60 + $tmp[1];
						$countries[$country]['calls'][$direction]['duration'] += $duration;

						if(!isset($calls[$date]))
						{
							$calls[$date] = array(
								'outgoing' => array('duration' => 0, 'number' => 0, 'price' => 0),
								'incoming' => array('duration' => 0, 'number' => 0, 'price' => 0),
								'other' => array('duration' => 0, 'number' => 0, 'price' => 0),
							);
						}
						++$calls[$date][$direction]['number'];
						$calls[$date][$direction]['price'] += $call->amount;
						$calls[$date][$direction]['duration'] += $duration;

						if(!isset($percentages['calls_'.$direction]))
						{
							$percentages['calls_'.$direction] = 0;
						}
						$percentages['calls_'.$direction] += $call->amount;
						if(!isset($totals['calls_'.$direction]))
						{
							$totals['calls_'.$direction] = 0;
						}
						++$totals['calls_'.$direction];
					}
					$output['calls'] = $calls;
				}

				$_sms = empty($cdrs['quantity_based']) ? array() : (array)$cdrs['quantity_based'];
				if(!empty($_sms['list']))
				{
					foreach($_sms['list'] as $__sms)
					{
						$country = $this->_getCountryCode($__sms->country);
						$date = date('Y-m-d',$__sms->unix_time);
						if(!isset($countries[$country]))
						{
							$countries[$country] = array(
								'sms' => array(
									'number' => 0,
									'price' => 0
								),
							);
						}
						elseif(isset($countries[$country]) && !isset($countries[$country]['sms']))
						{
							$countries[$country]['sms'] = array(
								'number' => 0,
								'price' => 0
							);
						}
						if(!isset($sms[$date]))
						{
							$sms[$date] = array(
								'number' => 0,
								'price' => 0
							);
						}
						$sms[$date]['number'] += $__sms->quantity;
						$sms[$date]['price'] += $__sms->amount;
						$countries[$country]['sms']['number'] += $__sms->quantity;
						$countries[$country]['sms']['price'] += $__sms->amount;

						if(!isset($percentages['sms']))
						{
							$percentages['sms'] = 0;
						}
						$percentages['sms'] += $__sms->amount;
						if(!isset($totals['sms']))
						{
							$totals['sms'] = 0;
						}
						$totals['sms'] += $__sms->quantity;
					}
					$output['sms'] = $sms;
				}

				$_mobile_data = empty($cdrs['data_service']) ? array() : (array)$cdrs['data_service'];
				if(!empty($_mobile_data['list']))
				{
					foreach($_mobile_data['list'] as $__mobile_data)
					{
						$country = $this->_getCountryCode($__mobile_data->country);
						$date = date('Y-m-d',$__mobile_data->unix_time);
						if(!isset($countries[$country]))
						{
							$countries[$country] = array(
								'mobile_data' => array(
									'number' => 0,
									'price' => 0
								),
							);
						}
						elseif(isset($countries[$country]) && !isset($countries[$country]['mobile_data']))
						{
							$countries[$country]['mobile_data'] = array(
								'number' => 0,
								'price' => 0
							);
						}
						if(!isset($mobile_data[$date]))
						{
							$mobile_data[$date] = array(
								'number' => 0,
								'price' => 0
							);
						}
						$mobile_data[$date]['number'] += $__mobile_data->quantity;
						$mobile_data[$date]['price'] += $__mobile_data->amount;
						$countries[$country]['mobile_data']['number'] += $__mobile_data->quantity;
						$countries[$country]['mobile_data']['price'] += $__mobile_data->amount;

						if(!isset($percentages['mobile_data']))
						{
							$percentages['mobile_data'] = 0;
						}
						$percentages['mobile_data'] += $__mobile_data->amount;
						if(!isset($totals['mobile_data']))
						{
							$totals['mobile_data'] = 0;
						}
						$totals['mobile_data'] += $__mobile_data->quantity;
					}
					$output['mobile_data'] = $mobile_data;
				}

				$output['countries'] = $countries;

				if(!empty($calls) || !empty($sms) || !empty($mobile_data))
				{
					if(!empty($calls))
					{
						foreach($calls as $date => $call)
						{
							$bars[$date] = $call;
						}
					}

					if(!empty($sms))
					{
						foreach($sms as $date => $_sms)
						{
							$bars[$date]['sms'] = $_sms;
						}
					}

					if(!empty($mobile_data))
					{
						foreach($mobile_data as $date => $_mobile_data)
						{
							$bars[$date]['mobile_data'] = $_mobile_data;
						}
					}

					$output['bars'] = $bars;
				}

				if(!empty($percentages))
				{
					$total = 0;
					foreach($percentages as $value)
					{
						$total += $value;
					}
					$output['percentages'] = array(
						'calls_incoming' => empty($percentages['calls_incoming']) ? '0%' :  round($percentages['calls_incoming']/$total*100).'%',
						'calls_outgoing' => empty($percentages['calls_outgoing']) ? '0%' :  round($percentages['calls_outgoing']/$total*100).'%',
						'sms' => empty($percentages['sms']) ? '0%' :  round($percentages['sms']/$total*100).'%',
						'mobile_data' => empty($percentages['mobile_data']) ? '0%' :  round($percentages['mobile_data']/$total*100).'%',
					);
				}

				if(!empty($totals))
				{
					$output['totals'] = array(
						'calls_incoming' => empty($totals['calls_incoming']) ? '0' :  $totals['calls_incoming'],
						'calls_outgoing' => empty($totals['calls_outgoing']) ? '0' :  $totals['calls_outgoing'],
						'sms' => empty($totals['sms']) ? '0' :  $totals['sms'],
						'mobile_data' => empty($totals['mobile_data']) ? '0' :  $totals['mobile_data'],
					);
				}

			}
			$userInfo = Cache::getInstance()->getCache("userInfo");
			date_default_timezone_set($userInfo["tz"]);
			Cache::getInstance()->setCache("getDashboard",$output);
		}
		$cache_timestamp = date("Y/m/d H:i:s", Cache::getInstance()->getCacheTimestamp("getDashboard"));

		return array_merge($output, array("cache_timestamp" => $cache_timestamp));
	}

	private function _getCountryCode($country)
	{
		static $country_codes;

		$country_codes = is_null($country_codes) ? array() : $country_codes;
		$country = $country ? $country : 'UNDEFINED';
		if('UNDEFINED' != $country && !isset($country_codes[$country]))
		{
			$locations = Helper::GetLocations();
			foreach($locations as $country_code => $info)
			{
				if($info['country'] == $country)
				{
					$country_codes[$country] = $country_code;
					break;
				}
			}

		}

		return isset($country_codes[$country]) ? $country_codes[$country] : $country;
	}
}
?>