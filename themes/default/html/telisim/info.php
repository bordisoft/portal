<?php
defined('_PORTAL') or die();
$form_fields = $this->_registry->get('form_fields');
?>
<div class="row">
	<div class="col-md-6">
		<div class="box box-primary">
			<div class="box-header"><h3 class="box-title"><?php echo $locale['subscriber']; ?></h3></div>
				<form role="form" class="validate" method="POST" action="" autocomplete="off">
				<?php
				$updatable = FALSE;
				$fields = Helper::ProcessFieldsArray($subscriber_info,$form_fields);
				foreach($subscriber_info as $attribute => $value):
				if(isset($fields[$attribute])):
				$field = $fields[$attribute];
				$updatable = $updatable ? $updatable : ('update' == $subscriber_info[$attribute]->access ? TRUE : FALSE);
				?>
					<div class="box-body">
						<div class="form-group">
							<label for="<?php echo $attribute; ?>"><?php echo $field['title']; ?></label>
							<?php echo $field['html']; ?>
						</div>
					</div>
				<?php
				endif;
				endforeach;
				?>
					<input type="hidden" name="route" value="info" />
					<input type="hidden" name="task" value="subscriber" />
					<input type="hidden" name="action" value="set" />
					<div class="box-footer">
						<button type="submit"<?php echo !$updatable ? ' disabled="disabled"' : ''; ?> class="btn btn-primary"><?php echo $locale['UPDATE']; ?></button>
					</div>
			</form>
		</div>
	</div>
	<div class="col-md-6">
		<div class="box box-warning">
			<div class="box-header"><h3 class="box-title">SIM</h3></div>
			<div class="box-body">
				<form role="form" class="validate" method="POST" action="" autocomplete="off">
				<?php
				$updatable = FALSE;
				$fields = Helper::ProcessFieldsArray($sim_info,$form_fields);
				foreach($sim_info as $attribute => $value):
				if(isset($fields[$attribute])):
				$field = $fields[$attribute];
				$updatable = $updatable ? $updatable : ('update' == $sim_info[$attribute]->access ? TRUE : FALSE);
				?>
					<div class="box-body">
						<div class="form-group">
							<label for="<?php echo $attribute; ?>"><?php echo $field['title']; ?></label>
							<?php echo $field['html']; ?>
						</div>
					</div>
				<?php
				endif;
				endforeach;
				?>
					<input type="hidden" name="route" value="info" />
					<input type="hidden" name="task" value="sim" />
					<input type="hidden" name="action" value="set" />
					<div class="box-footer">
						<button type="submit"<?php echo !$updatable ? ' disabled="disabled"' : ''; ?> class="btn btn-primary"><?php echo $locale['UPDATE']; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>