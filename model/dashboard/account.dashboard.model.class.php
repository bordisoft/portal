<?php defined('_PORTAL') or die();

Class AccountDashboardModel extends DashboardModel
{
	protected function getDashboard($requests=array())
	{
		$data = $this->getData(array_merge($requests,array('get_cdrs' => array(
			'args' => array(
				'limit' => 1000,
				'from_date' => date('Y-m-d H:i:s', strtotime('-1 month')),
				'to_date' => date('Y-m-d H:i:s')
			)
		))));
	}
}
?>