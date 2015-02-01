<?php defined('_PORTAL') or die(); ?>
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header"><h3 class="box-title"><?php echo $locale["select_destination"]; ?></h3></div>
			<div class="box-body table-responsive">
				<form action="" method="POST" class="validate" role="form" autocomplete="off" name="rateCalculator">
					<div class="col-xs-3">
						<div class="form-group">
							<label><?php echo $locale["CALL_FROM"];?></label>
							<select name="args[iso_3166_1_a2_from]" class="form-control mand">
								<?php foreach($countries as $iso_3166_1_a2 => $country): ?>
								<option<?php echo empty($input["iso_3166_1_a2_from"]) ? "" : ($input["iso_3166_1_a2_from"] == $iso_3166_1_a2 ? " selected=\"selected\"" : "");
									?> value="<?php echo $iso_3166_1_a2; ?>"><?php echo $country; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-xs-3">
						<div class="form-group">
							<label><?php echo $locale["CALL_TO"];?></label>
							<select class="form-control mand" name="args[iso_3166_1_a2_to]" >
								<?php foreach($countries as $iso_3166_1_a2 => $country): ?>
								<option<?php echo empty($input["iso_3166_1_a2_to"]) ? "" : ($input["iso_3166_1_a2_to"] == $iso_3166_1_a2 ? " selected=\"selected\"" : "");
									?> value="<?php echo $iso_3166_1_a2; ?>"><?php echo $country; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-xs-3">
						<button class="btn btn-app" type="submit"><i class="fa fa-search"></i><?php echo $locale["search"]; ?></button>
					</div>
					<input type="hidden" name="route" value="rates" />
					<input type="hidden" name="task" value="calculator" />
					<input type="hidden" name="action" value="get" />
				</form>
				<table id="rate-calculator" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th rowspan="1" colspan="2" scope='colgroup'><?php echo $locale["voice_calls"]; ?></th>
							<th rowspan="2" scope='col'><?php echo "SMS"; ?></th>
							<th rowspan="2" scope='col'><?php echo $locale["MOBILE_INTERNET"].", 1 MB"; ?></th>
						</tr>
						<tr>
							<th scope='col'><?php echo $locale["LANDLINE"]; ?></th>
							<th scope='col'><?php echo $locale["mobile"]; ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo empty($vc_landline) ? "" : $vc_landline; ?></td>
							<td><?php echo empty($vc_mobile) ? "" : $vc_mobile; ?></td>
							<td><?php echo empty($sms) ? "" : $sms; ?></td>
							<td><?php echo empty($mobile_internet) ? "" : $mobile_internet; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>