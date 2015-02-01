<?php defined('_PORTAL') or die(); ?>
<div class="row">
	<div class="col-md-6">
		<div class="box box-success">
			<div class="box-header"><h3 class="box-title"><?php echo $locale['CHANGE_PASS']; ?></h3></div>
			<form role="form" class="validate" method="POST" action="" autocomplete="off">
				<div class="box-body">
					<div class="form-group">
						<label for="old_password"><?php echo $locale['old_password']; ?></label>
						<input autocomplete="off" type="password" value="" name="args[old_password]" class="form-control mand">
					</div>
				</div>
				<div class="box-body">
					<div class="form-group">
						<label for="new_password"><?php echo $locale['new_password']; ?></label>
						<input autocomplete="off" type="password" value="" name="args[new_password]" class="form-control mand">
					</div>
				</div>
				<div class="box-body">
					<div class="form-group">
						<label for="retype_password"><?php echo $locale['retype_password']; ?></label>
						<input autocomplete="off" type="password" value="" name="args[retype_password]" class="form-control mand">
					</div>
				</div>
				<input type="hidden" name="route" value="dashboard" />
				<input type="hidden" name="task" value="pass" />
				<input type="hidden" name="action" value="set" />
				<div class="box-footer">
					<button type="submit" class="btn btn-primary"><?php echo $locale['UPDATE']; ?></button>
					<button type="button" data-location="dashboard" class="btn btn-primary request"><?php echo $locale['cancel']; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>