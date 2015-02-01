<?php
defined('_PORTAL') or die();
$services = $this->_registry->get('services');
$titles = array(
	"quantity_based" => "SMS",
	"data_service" => $locale["MOBILE_INTERNET"],
	"cli" => $locale["from"],
	"cld" => $locale["to"],
	"connect_date" => $locale["date"],
);
?>
<link href="<?php echo $theme_path;?>css/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $theme_path;?>css/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $theme_path;?>js/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
<div class="loader">
	<form action="" method="POST" class="validate" role="form" autocomplete="off" name="xdrFilter">
		<div class="row">
			<div class="col-md-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><?php echo $locale["filter"]; ?></h3>
					</div>
					<div class="box-body">
						<div class="row xdr-filter">
								<div class="col-xs-7">
									<label><?php echo $locale["from_date"]." - ".$locale["to_date"]; ?></label>
									<input type="text" class="form-control" readonly id="filter-date" name="args[dates]" value="<?php echo empty($input["dates"]) ? "" : $input["dates"]; ?>" />
								</div>
								<div class="col-xs-3">
									<label><?php echo $locale["services"];?></label>
									<select class="form-control" name="args[service]">
										<option value="0"></option>
										<?php foreach(array_keys($services) as $service): ?>
										<option<?php echo empty($input["service"]) ? "" : ($input["service"] == $service ? " selected=\"selected\"" : ""); ?> value="<?php echo $service; ?>"><?php echo isset($titles[$service]) ? $titles[$service] : $locale[$service]; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-xs-2">
									<button class="btn btn-app" type="submit"><i class="fa fa-search"></i><?php echo $locale["search"]; ?></button>
								</div>
								<input type="hidden" name="route" value="xdrs" />
								<input type="hidden" name="action" value="get" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php foreach($services as $service => $fields): ?>
		<?php $data = isset(${$service}) ? (array)${$service} : array(); ?>
		<?php if(!empty($data["total_count"])): ?>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><?php echo isset($titles[$service]) ? $titles[$service] : $locale[$service]; ?></h3>
			</div>
			<div class="box-body table-responsive">
				<div role="grid" class="dataTables_wrapper form-inline data-table" id="<?php echo $service; ?>_wrapper">
					<div class="row">
						<?php if($data["total_count"] > 10):?>
						<div class="col-xs-6">
							<div id="<?php echo $service; ?>_length" class="dataTables_length">
								<label>
									<select<?php echo ($data["limit"] < $data["total_count"] - $data["from"] || $data["limit"] >= $data["total_count"]) ? "" : " disabled=\"disabled\""; ?> class="on-page form-control" id="<?php echo $service."-limit"; ?>" name="args[<?php echo $service; ?>][limit]" size="1" aria-controls="<?php echo $service; ?>">
										<option value="10">10</option>
										<?php if($data["total_count"] - $data["from"] > 10):?>
										<option<?php echo empty($input[$service]) ? "" : ($input[$service]["limit"] == 20 ? " selected=\"selected\"" : ""); ?> value="20">
											20
										</option>
										<?php endif; ?>
										<?php if($data["total_count"] - $data["from"] >= 20 && !($data["total_count"] - $data["from"] < 30)):?>
										<option<?php echo empty($input[$service]) ? "" : ($input[$service]["limit"] == 30 ? " selected=\"selected\"" : ""); ?> value="30">
											30
										</option>
										<?php endif; ?>
									</select>
									<?php echo $data["from"]." - ".($data["subtotal_count"]+$data["from"])." ".$locale["pager_of"]." ".$data["total_count"]; ?>
									<?php if($data["limit"] >= $data["total_count"] - $data["from"] && $data["limit"] < $data["total_count"]): echo "<input type=\"hidden\" name=\"args[{$service}][limit]\" value=\"{$data["limit"]}\" />"; endif;?>
								</label>
							</div>
						</div>
						<?php endif; ?>
						<?php if(0): ?>
						<div class="col-xs-6">
							<div class="dataTables_filter" id="example1_filter">
								<label>Search: <input type="text" aria-controls="example1"></label>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
							<?php foreach($fields as $field): ?>
								<th><?php echo isset($titles[$field]) ? $titles[$field] : $locale[$field]; ?></th>
							<?php endforeach; ?>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th rowspan="1" colspan="<?php echo count($fields)-1;?>"></th>
								<th rowspan="1" colspan="1"><?php echo number_format($data["subtotal_amount"],2,".","")." ".$iso_4217; ?></th>
							</tr>
						</tfoot>
						<tbody>
							<?php foreach($data["list"] as $value): ?>
							<tr>
								<?php foreach($fields as $field): ?>
								<td><?php
								if("amount" == $field):
									echo number_format($value->$field,2,".","")." ".$iso_4217;
								elseif("quantity" == $field && "data_service" == $service):
									echo number_format($value->$field,2,".","")." MB";
								else:
									echo $value->$field;
								endif;?></td>
								<?php endforeach; ?>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class="row">
						<?php if($data["limit"] < $data["total_count"]): ?>
						<div class="col-xs-12">
							<div class="dataTables_paginate paging_bootstrap">
								<ul class="pagination">
									<?php
									$max_pages = 10;
									$steps = ceil($data["total_count"]/$data["limit"]);
									$i = floor($data["from"]/$data["limit"]) >= $max_pages ? floor($data["from"]/$data["limit"]/$max_pages)*$max_pages : 0;
									$j = $i;
									if($data["from"] >= $data["limit"]*$max_pages):
									?><li>
										<a data-name="args[<?php echo $service; ?>][from]" class="page" data-value="0" href="#"><<</a>
									</li>
									<li>
										<a data-name="args[<?php echo $service; ?>][from]" class="page" data-value="<?php echo intval(intval(($j-1)*$data["limit"])); ?>" href="#"><</a>
									</li>
									<?php
									endif;
									while($i < $steps && $i < $max_pages + $j):
									$step = intval($i*$data["limit"]);
									?><li<?php if($step == $data["from"]): echo " class=\"active\""; endif; ?>>
										<a data-name="args[<?php echo $service; ?>][from]" data-value="<?php echo $step; ?>" class="page" href="#"><?php echo $i+1; ?></a>
									</li><?php
									$i++;
									endwhile;
									if($data["limit"]*$max_pages <= $data["total_count"] - $j*$data["limit"]):
									?><li>
										<a data-name="args[<?php echo $service; ?>][from]" class="page" data-value="<?php echo intval($data["limit"]*$max_pages); ?>" href="#">></a>
									</li>
									<li>
										<a data-name="args[<?php echo $service; ?>][from]" class="page" data-value="<?php echo (ceil($data["total_count"]/$data["limit"])-1)*$data["limit"]; ?>" href="#">>></a>
									</li>
									<?php endif; ?>
								</ul>
							</div>
						</div>
						<?php endif; ?>
						<input type="hidden" id="<?php echo $service."-from"; ?>" name="args[<?php echo $service; ?>][from]" value="<?php echo $data["from"]; ?>" />
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<?php endforeach; ?>
	</form>
</div>
<script type="text/javascript">
	setTimeout(function(){
		$('#filter-date').daterangepicker({
			timePicker: true,
			timePickerIncrement: 30,
			format: 'MM/DD/YYYY hh:mm A',
			locale: {
					applyLabel: "<?php echo $locale["ok"];?>",
					cancelLabel: "<?php echo $locale["cancel"];?>",
					fromLabel: "<?php echo $locale["from_date"];?>",
					toLabel: "<?php echo $locale["to_date"];?>",
					weekLabel: 'W',
					customRangeLabel: 'Custom Range',
					daysOfWeek: moment()._lang._weekdaysMin.slice(),
					monthNames: moment()._lang._monthsShort.slice(),
					firstDay: 0
					}
		});
	},1000);
</script>