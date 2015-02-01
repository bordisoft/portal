<?php defined('_PORTAL') or die(); ?>
<?php if(!empty($notifications)): ?>
<?php foreach($notifications as $notification): ?>
<div class="alert alert-<?php echo $notification['class']; ?> alert-dismissable">
	<i class="fa <?php echo $notification['icon']; ?>"></i>
	<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	<b><?php echo (empty($locale[$notification['type']])
			? ucfirst($notification['type']).(isset($notification['code']) ? " ".$notification['code'] : "")
			: (isset($notification['code']) ? str_replace("#code#",$notification['code'],$locale[$notification['type']]) : "")); ?></b>
	<?php echo $notification['content'] ?>
</div>
<?php endforeach; ?>
<?php endif; ?>