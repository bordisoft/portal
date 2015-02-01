<?php
defined('_PORTAL') or die();
$form_fields = $this->_registry->get('form_fields');
?>
<link href="<?php echo $theme_path;?>css/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $theme_path;?>css/datepicker.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $theme_path;?>js/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
<div class="row">
	<div class="col-md-4">
		<div class="box box-solid box-primary">
			<div class="box-header"><h3 class="box-title"><?php echo $locale["voucher_topup"]; ?></h3></div>
			<form role="form" class="validate" method="POST" action="" autocomplete="off">
				<div class="box-body">
					<div class="form-group">
						<label for="voucher_amount"><?php echo $locale["voucher"].", ".$iso_4217; ?></label>
						<input id="voucher_amount" type="text" value="" name="args[voucher]" class="form-control check digits-letters" />
					</div>
				</div>
				<input type="hidden" value="payment" name="route" />
				<input type="hidden" value="voucher_topup" name="task" />
				<input type="hidden" value="set" name="action" />
				<div class="box-footer">
					<button type="submit"<?php echo "N" == $payment_info["voucher_payment"]->value ? ' disabled="disabled"' : ''; ?> class="btn btn-primary"><?php echo $locale['pay_now']; ?></button>
				</div>
			</form><?php echo ("N" == $payment_info["voucher_payment"]->value ? '<span class="overlay"></span>' : ''); ?>
		</div>
	</div>
	<div class="col-md-4">
		<div class="box box-solid box-danger">
			<div class="box-header"><h3 class="box-title"><?php echo $locale["make_payment"]; ?></h3></div>
			<form role="form" class="validate" method="POST" action="" autocomplete="off">
				<div class="box-body">
					<div class="form-group">
						<label for="pay_to_payment"><?php echo $payment_info["pay_to"]->title; ?></label>
						<input disabled="disabled" type="text" id="pay_to_payment" value="<?php echo $payment_info["pay_to"]->value; ?>" class="form-control" />
					</div>
					<?php if(!empty($payment_info["use_cc"])): ?>
					<div class="form-group">
						<label for="voucher_amount"><?php echo $payment_info["use_cc"]->title; ?></label>
						<input disabled="disabled" type="text" value="<?php echo empty($payment_info["use_cc"]->value) ? "" : $payment_info["use_cc"]->value; ?>" class="form-control" />
					</div>
					<?php endif; ?>
					<div class="form-group">
						<label for="payment_amount"><?php echo $locale["amount"].", ".$iso_4217; ?></label>
						<input id="payment_amount" type="text" value="" name="args[amount]" class="form-control check digits" />
					</div>
				</div>
				<input type="hidden" value="payment" name="route" />
				<input type="hidden" value="credit_card_payment" name="task" />
				<input type="hidden" value="set" name="action" />
				<div class="box-footer">
					<button type="submit"<?php echo "N" == $payment_info["cc_payment"]->value ? ' disabled="disabled"' : ''; ?> class="btn btn-primary"><?php echo $locale['pay_now']; ?></button>
				</div>
			</form>
			<?php echo ("N" == $payment_info["cc_payment"]->value ? '<span class="overlay"></span>' : ''); ?>
		</div>
	</div>
	<div class="col-md-4">
		<div class="box box-solid box-info">
			<div class="box-header"><h3 class="box-title">PayPal</h3></div>
			<form role="form" class="validate" method="POST" action="" autocomplete="off">
				<div class="box-body">
					<div class="form-group">
						<label for="pay_to_payment"><?php echo $payment_info["pay_to"]->title; ?></label>
						<input disabled="disabled" type="text" id="pay_to_payment" value="<?php echo $payment_info["pay_to"]->value; ?>" class="form-control" />
					</div>
					<div class="form-group">
						<label for="paypal_amount"><?php echo $locale["amount"].", ".$iso_4217; ?></label>
						<input id="paypal_amount" type="text" value="" name="args[amount]" class="form-control check digits" />
					</div>
				</div>
				<input type="hidden" value="payment" name="route" />
				<input type="hidden" value="paypal_payment" name="task" />
				<input type="hidden" value="set" name="action" />
				<div class="box-footer">
					<button type="submit"<?php echo "N" == $payment_info["paypal_payment"]->value ? ' disabled="disabled"' : ''; ?> class="btn btn-primary"><?php echo $locale['pay_now']; ?></button>
				</div>
			</form><?php echo ("N" == $payment_info["paypal_payment"]->value ? '<span class="overlay"></span>' : ''); ?>
		</div>
	</div>
</div>
<?php if(!empty($credit_card)): ?>
<div class="row">
	<div class="col-md-6">
		<div class="box box-danger">
			<div class="box-header"><h3 class="box-title"><?php echo $locale['payment_info']; ?></h3></div>
				<form role="form" class="validate credit_card" method="POST" action="" autocomplete="off">
				<?php
				$updatable = FALSE;
				$fields = Helper::ProcessFieldsArray($credit_card,$form_fields);
				$field_names = array('cc_i_payment_method','cc_number','cc_exp_month','cc_exp_year','cc_cvv','cc_name','cc_address',
						'cc_city','cc_iso_3166_1_a2','cc_i_country_subdivision','cc_zip',"ecommerce_enabled");
				foreach($field_names as $attribute):
				if(isset($fields[$attribute])):
				if("alternate" == $attribute) continue;
				$field = $fields[$attribute];
				$updatable = $updatable ? $updatable : ('update' == $credit_card[$attribute]->access ? TRUE : FALSE);
				?>
					<?php if(in_array($attribute,array("cc_exp_year","cc_exp_month"))): continue;?>
					<?php elseif("cc_number" == $attribute):?>
					<div class="box-body">
						<div class="form-group">
							<label for="<?php echo $attribute; ?>"><?php echo $field['title']; ?></label><br/>
							<?php echo str_replace("name=\"args[cc_number]\"","name=\"args[cc_number]\" id=\"cc_number\" style=\"width:77%;display:inline-block;\"",$field['html']); ?>
							<?php if(isset($fields["cc_exp_month"])): ?>
							<?php echo str_replace("name=\"args[cc_exp_month]\"","name=\"args[cc_exp_month]\" style=\"width:10%;display:inline-block;margin-left:1%;\"",$fields["cc_exp_month"]['html']); ?>
							<?php endif; ?>
							<?php if(isset($fields["cc_exp_year"])): ?>
							<?php echo str_replace("name=\"args[cc_exp_year]\"","name=\"args[cc_exp_year]\" style=\"width:10%;display:inline-block;margin-left:1%;\"",$fields["cc_exp_year"]['html']); ?>
							<?php endif; ?>
						</div>
					</div>
					<?php elseif("ecommerce_enabled" == $attribute):?>
					<div class="box-body">
						<div class="form-group">
							<div class="checkbox">
								<label><?php echo $field['html'].$field['title']; ?></label>
							</div>
						</div>
					</div>
					<?php else: ?>
					<div class="box-body">
						<div class="form-group">
							<label for="<?php echo $attribute; ?>"><?php echo $field['title']; ?></label>
							<?php echo ("cc_cvv" == $attribute) ? str_replace("type=\"text\"","type=\"password\"",$field['html']) : $field['html']; ?>
						</div>
					</div>
					<?php endif; ?>
				<?php
				endif;
				endforeach;
				?>
					<input type="hidden" name="route" value="payment" />
					<input type="hidden" name="task" value="credit_card" />
					<input type="hidden" name="action" value="set" />
					<div class="box-footer">
						<button type="submit"<?php echo !$updatable ? ' disabled="disabled"' : ''; ?> class="btn btn-primary"><?php echo $locale['UPDATE']; ?></button>
					</div>
			</form>
		</div>
	</div>
	<div class="col-md-6">
		<div class="box box-info">
			<div class="box-header"><h3 class="box-title"><?php echo $locale['another_cc_payment']; ?></h3></div>
			<form role="form" class="validate credit_card" method="POST" action="" autocomplete="off">
				<div class="box-body">
					<div class="form-group">
						<label for="pay_to_payment"><?php echo $payment_info["pay_to"]->title; ?></label>
						<input disabled="disabled" type="text" id="pay_to_payment" value="<?php echo $payment_info["pay_to"]->value; ?>" class="form-control" />
					</div>
				</div>
				<div class="box-body">
					<div class="form-group">
						<label for="another_cc_amount"><?php echo $locale["amount"].", ".$iso_4217; ?></label>
						<input id="another_cc_amount" type="text" value="" name="args[amount]" class="form-control check digits" />
					</div>
				</div>
			<?php
			$updatable = FALSE;
			$fields = Helper::ProcessFieldsArray($another_credit_card,$form_fields);
			foreach($field_names as $attribute):
			if("ecommerce_enabled" == $attribute) continue;
			if(isset($fields[$attribute])):
			$field = $fields[$attribute];
			$updatable = $updatable ? $updatable : ('update' == $another_credit_card[$attribute]->access ? TRUE : FALSE);
			?>
				<?php if(in_array($attribute,array("cc_exp_year","cc_exp_month"))): continue;?>
				<?php elseif("cc_number" == $attribute):?>
				<div class="box-body">
					<div class="form-group">
						<label for="<?php echo "another_".$attribute; ?>"><?php echo $field['title']; ?></label><br/>
						<?php echo str_replace("name=\"args[cc_number]\"","name=\"args[another_cc_number]\" id=\"another_cc_number\" style=\"width:77%;display:inline-block;\"",$field['html']); ?>
						<?php if(isset($fields["cc_exp_month"])): ?>
						<?php echo str_replace("name=\"args[cc_exp_month]\"","name=\"args[another_cc_exp_month]\" id=\"another-cc-exp-month\" style=\"width:10%;display:inline-block;margin-left:1%;\"",$fields["cc_exp_month"]['html']); ?>
						<?php endif; ?>
						<?php if(isset($fields["cc_exp_year"])): ?>
						<?php echo str_replace("name=\"args[cc_exp_year]\"","name=\"args[another_cc_exp_year]\" id=\"another-cc-exp-year\" style=\"width:10%;display:inline-block;margin-left:1%;\"",$fields["cc_exp_year"]['html']); ?>
						<?php endif; ?>
					</div>
				</div>
				<?php else: ?>
				<div class="box-body">
					<div class="form-group">
						<label for="<?php echo "another_".$attribute; ?>"><?php echo $field['title']; ?></label>
						<?php echo ("cc_cvv" == $attribute) ? str_replace("type=\"text\"","type=\"password\"",preg_replace("/cc_/m","another_cc_",$field['html'])) : preg_replace("/cc_/m","another_cc_",$field['html']); ?>
					</div>
				</div>
				<?php endif; ?>
			<?php
			endif;
			endforeach;
			?>
				<input type="hidden" name="args[alternate]" value="1" />
				<input type="hidden" name="route" value="payment" />
				<input type="hidden" name="task" value="credit_card_payment" />
				<input type="hidden" name="action" value="set" />
				<div class="box-footer">
					<button type="submit"<?php echo !$updatable ? ' disabled="disabled"' : ''; ?> class="btn btn-primary"><?php echo $locale['pay_now']; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if("Y" == $payment_info["ppayments"]->value): ?>
<?php $data = $ppayments; ?>
<?php $fields = array("accepted","amount","i_periodical_payment_period","balance_threshold","from_date","to_date","number_payments","frozen","discontinued"); ?>
<div class="row">
	<div class="col-md-12">
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><?php echo $locale["ppayments"]; ?></h3>
			</div>
			<form method="POST" action="" role="form" class="validate" autocomplete="off">
				<div class="box-body table-responsive">
					<div role="grid" class="dataTables_wrapper form-inline data-table" id="ppayments_wrapper">
						<div class="row">
							<?php if($data["total_count"] > 10):?>
							<div class="col-xs-10">
								<div id="ppayments_length" class="dataTables_length">
									<label>
										<select<?php echo ($data["limit"] < $data["total_count"] - $data["from"] || $data["limit"] >= $data["total_count"]) ? "" : " disabled=\"disabled\""; ?> class="on-page form-control" id="ppayments-limit" name="args[limit]" size="1" aria-controls="ppayments">
											<option value="10">10</option>
											<?php if($data["total_count"] - $data["from"] > 10):?>
											<option<?php echo empty($data["limit"]) ? "" : ($data["limit"] == 20 ? " selected=\"selected\"" : ""); ?> value="20">
												20
											</option>
											<?php endif; ?>
											<?php if($data["total_count"] - $data["from"] >= 20 && !($data["total_count"] - $data["from"] < 30)):?>
											<option<?php echo empty($data["limit"]) ? "" : ($data["limit"] == 30 ? " selected=\"selected\"" : ""); ?> value="30">
												30
											</option>
											<?php endif; ?>
										</select>
										<?php echo $data["from"]." - ".($data["subtotal_count"]+$data["from"])." ".$locale["pager_of"]." ".$data["total_count"]; ?>
										<?php if($data["limit"] >= $data["total_count"] - $data["from"] && $data["limit"] < $data["total_count"]): echo "<input type=\"hidden\" name=\"args[limit]\" value=\"{$data["limit"]}\" />"; endif;?>
									</label>
								</div>
							</div>
							<?php endif; ?>
							<div class="col-xs-<?php echo ($data["total_count"] > 10) ? "2" : "12"; ?>">
								<div class="dataTables_filter" id="filter">
									<label>Effective:
										<select name="args[filter]" class="filter form-control" id="ppayments-filter">
											<option<?php echo empty($data["filter"]) || "All" != $data["filter"] ? "" : " selected=\"selected\""; ?> value="All"><?php echo $locale["all"]; ?></option>
											<option<?php echo empty($data["filter"]) || "beforeNow" != $data["filter"] ? "" : " selected=\"selected\""; ?> value="beforeNow">-&gt;<?php echo $locale["now"]; ?></option>
											<option<?php echo !empty($data["filter"]) && "Now" != $data["filter"] ? "" : " selected=\"selected\""; ?> value="Now"><?php echo $locale["now"]; ?></option>
											<option<?php echo empty($data["filter"]) || "afterNow" != $data["filter"] ? "" : " selected=\"selected\""; ?> value="afterNow"><?php echo $locale["now"]; ?>-&gt;</option>
										</select>
									</label>
								</div>
							</div>
						</div>
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th><?php echo $locale['edit'];?></th>
									<?php foreach($fields as $field): ?>
									<th><?php echo $locale[$field].(in_array($field,array("amount","balance_threshold")) ? ", ".$iso_4217 : ""); ?></th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<tr data-i_ppayment="" data-i_value="i_ppayment" data-route="payment" data-task="ppayment" data-action="set" class="template"<?php if(count($data["list"]) > 0): echo " style=\"display:none;\""; endif;?>>
									<td>
										<button type="button" style="display:none;" class="btn-sm btn cancel-button"><i class="fa fa-times"></i></button>
										<button type="button" style="display:none;" class="btn-sm btn save-button"><i class="fa fa-save"></i></button>
									</td>
									<?php foreach($fields as $field): ?>
										<td><?php if("i_periodical_payment_period" == $field): ?>
											 <select disabled="disabled" name="args[i_periodical_payment_period]" class="form-control readonly">
												<option value="1"><?php echo $locale["Balance Driven"]; ?></option>
												<option value="3"><?php echo $locale["weekly"]; ?></option>
												<option value="5"><?php echo $locale["monthly"]; ?></option>
											</select>
										<?php elseif(in_array($field,array("frozen","discontinued"))): ?>
											<input disabled="disabled" type="checkbox" name="args[<?php echo $field; ?>]" value="Y" />
										<?php elseif(!in_array($field,array("accepted","number_payments"))): ?>
											<input disabled="disabled" type="text"<?php echo in_array($field,array("from_date","to_date")) ? " readonly class=\"datepickers mand readonly\"" : " class=\"mand check digits readonly\""; ?> name="args[<?php echo $field; ?>]" value="" />
										<?php endif; ?></td>
									<?php endforeach; ?>
								</tr>
								<?php if(count($data["list"]) > 0): ?>
								<?php foreach($data["list"] as $value): ?>
								<tr data-i_ppayment="<?php echo $value->i_ppayment; ?>" data-route="payment" data-task="ppayment" data-action="set" data-i_value="i_ppayment" class="editable">
									<td>
										<?php if($value->editable): ?>
										<button type="button" class="btn-sm btn edit-button"><i class="fa fa-edit"></i></button>
										<button type="button" style="display:none;" class="btn-sm btn cancel-button"><i class="fa fa-times"></i></button>
										<button type="button" style="display:none;" class="btn-sm btn save-button"><i class="fa fa-save"></i></button>
										<?php endif; ?>
									</td>
									<?php foreach($fields as $field): ?>
									<td><?php
									if(is_array($value->$field)):
										echo "<select disabled=\"disabled\" name=\"args[{$field}]\" class=\"form-control readonly\">";
										foreach($value->$field as $v):
											echo "<option value=\"{$v->value}\"".(empty($v->sel) ? "" : " selected=\"selected\"").">{$v->name}</option>";
										endforeach;
										echo "</select>";
									elseif(in_array($field,array("accepted","number_payments"))):
										echo $value->$field;
									elseif(in_array($field,array("frozen","discontinued"))):
										echo "<input type=\"checkbox\" disabled=\"disabled\" name=\"args[{$field}]\" value=\"Y\" ".("Y" == $value->$field ? " checked=\"checked\"" : "")."/>";
									else:
										echo "<input type=\"text\"".(in_array($field,array("from_date","to_date")) ? " readonly class=\"datepickers mand readonly\"" : " class=\"mand check digits readonly\"")." disabled=\"disabled\" name=\"args[{$field}]\" value=\"{$value->$field}\" />";
									endif;
									?></td>
									<?php endforeach; ?>
								</tr>
								<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
						<div class="row">
							<div class="col-xs-4">
								<span class="btn-sm btn add-button"><i class="fa fa-plus-square"></i> <?php echo $locale['add']; ?></span>
							</div>
							<?php if($data["limit"] < $data["total_count"]): ?>
							<div class="col-xs-8">
								<div class="dataTables_paginate paging_bootstrap">
									<ul class="pagination">
										<?php
										$max_pages = 10;
										$steps = ceil($data["total_count"]/$data["limit"]);
										$i = floor($data["from"]/$data["limit"]) >= $max_pages ? floor($data["from"]/$data["limit"]/$max_pages)*$max_pages : 0;
										$j = $i;
										if($data["from"] >= $data["limit"]*$max_pages):
										?><li>
											<a data-name="args[from]" class="page" data-value="0" href="#"><<</a>
										</li>
										<li>
											<a data-name="args[from]" class="page" data-value="<?php echo intval(intval(($j-1)*$data["limit"])); ?>" href="#"><</a>
										</li>
										<?php
										endif;
										while($i < $steps && $i < $max_pages + $j):
										$step = intval($i*$data["limit"]);
										?><li<?php if($step == $data["from"]): echo " class=\"active\""; endif; ?>>
											<a data-name="args[from]" data-value="<?php echo $step; ?>" class="page" href="#"><?php echo $i+1; ?></a>
										</li><?php
										$i++;
										endwhile;
										if($data["limit"]*$max_pages <= $data["total_count"] - $j*$data["limit"]):
										?><li>
											<a data-name="args[from]" class="page" data-value="<?php echo intval($data["limit"]*$max_pages); ?>" href="#">></a>
										</li>
										<li>
											<a data-name="args[from]" class="page" data-value="<?php echo (ceil($data["total_count"]/$data["limit"])-1)*$data["limit"]; ?>" href="#">>></a>
										</li>
										<?php endif; ?>
									</ul>
								</div>
							</div>
							<?php endif; ?>
							<input type="hidden" id="ppayments-from" name="args[from]" value="<?php echo $data["from"]; ?>" />
							<input type="hidden" name="route" value="payment" />
							<input type="hidden" name="action" value="get" />
							<input type="hidden" name="task" value="index" />
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php endif; ?>
<script>
	$(".credit_card").each(function(){ ccFields($(this)); });
	$('.datepickers').datepicker({format:date_format});
	$("#ppayments_wrapper").find("select[name=args\\[i_periodical_payment_period\\]]").change(function(){
		var balance_threshold = $(this).closest("tr").find("input[name=args\\[balance_threshold\\]]"),
			disabled = $(this).val() == 1 ? "" : "disabled";
		balance_threshold.prop("disabled",disabled);
	});
	$(".dataTables_wrapper input[type=text]").each(function(){
		var id = "tmp-span-"+Math.floor((Math.random() * 10000) + 1).toString();
			span = $("<span id=\""+id+"\"></span>");
		span.css("font-family",$(this).css("font-family"))
			.css("font-size",$(this).css("font-size"))
			.css("font-weight",$(this).css("font-weight"))
			.css("visibility","hidden")
			.html($(this).val());
		$("body").append(span);
		$(this).width($("#"+id).width());
		$("#"+id).remove();
	});
</script>