<?php
defined('_PORTAL') or die();
$allowed_models = Cache::getInstance()->getCache('allowed');
?>
<div class="user-panel">
	<div class="pull-left image">
		<img src="<?php echo $theme_path; ?>img/user.png" class="img-circle" alt="User Image" />
	</div>
	<?php if(!empty($locale)): ?>
	<div class="pull-left info">
		<p><?php echo $locale['HELLO'].(!empty($firstname) ? ", ".$firstname : (empty($login) ? '' : ", ".$login)); ?></p>
		<?php if(isset($status) && 'ok' != $status->value): ?><a href="#"><?php echo Helper::GetStatusHtml($status); ?></a><?php endif; ?>
	</div>
	<?php endif; ?>
</div>
<?php if(!empty($allowed_models)): ?>
<ul class="sidebar-menu">
	<li>
		<a href="#" class="request" data-location="dashboard" >
			<?php echo Helper::GetIcon('dashboard'); ?> <span><?php echo $locale['TITLE_DASHBOARD']; ?></span>
		</a>
	</li>
	<?php if($allowed_models['info']): ?>
	<li>
		<a href="#" class="request" data-location="info" >
			<?php echo Helper::GetIcon('info'); ?> <span><?php echo $locale['TITLE_INFO']; ?></span>
		</a>
	</li>
	<?php endif; ?>
	<?php if($allowed_models['payment']): ?>
	<li>
		<a href="#" class="request" data-location="payment" >
			<?php echo Helper::GetIcon('payment'); ?> <span><?php echo $locale['TITLE_PAYMENT']; ?></span>
		</a>
	</li>
	<?php endif; ?>
	<?php if($allowed_models['rates']): ?>
	<li>
		<a href="#" class="request" data-location="rates.calculator" >
			<?php echo Helper::GetIcon('rates'); ?> <span><?php echo $locale['TITLE_CALCULATOR']; ?></span>
		</a>
	</li>
	<?php endif; ?>
	<?php if($allowed_models['xdrs']): ?>
	<li>
		<a href="#" class="request" data-location="xdrs" >
			<?php echo Helper::GetIcon('xdrs'); ?> <span><?php echo $locale['TITLE_XDRS']; ?></span>
		</a>
	</li>
	<?php endif; ?>
</ul>
<?php endif; ?>