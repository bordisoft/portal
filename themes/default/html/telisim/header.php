<?php
defined('_PORTAL') or die();
$allowed = Cache::getInstance()->getCache('allowed');
?>
<ul class="nav navbar-nav">
	<?php if(!empty($sim_location) && "NOT APPLICABLE" != $sim_location): ?>
	<li class="dropdown messages-menu" id="location">
		<a href="#" class="dropdown-toggle">
			<i class="fa fa-map-marker"></i>
			<span><?php echo $sim_location; ?></span>
		</a>
	</li>
	<?php endif; ?>
	<?php if(!empty($tz)): ?>
	<li class="dropdown messages-menu" id="timezone">
		<a href="#" class="dropdown-toggle">
			<i class="fa fa-clock-o"></i>
			<span><?php echo $tz; ?></span>
		</a>
	</li>
	<?php endif; ?>
	<?php if(!empty($status) && 'ok' != $status->value): ?>
	<li class="dropdown messages-menu">
		<a href="#" class="dropdown-toggle">
			<span id="status"><?php echo Helper::GetStatusHtml($status); ?></span>
		</a>
	</li>
	<?php endif;?>
	<?php if(!empty($balance)): ?>
	<li class="dropdown messages-menu">
		<a href="#" class="dropdown-toggle request" data-location="payment">
			<i class="fa fa-money"></i>
			<span id="balance"><?php echo $balance; ?></span>
		</a>
	</li>
	<?php if(!empty($i_lang) && "update" == $i_lang->access): ?>
	<li class="dropdown tasks-menu locales">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<?php foreach((array)$i_lang->value as $option): ?>
			<?php if(!empty($option->sel)):?>
			<span>
				<img alt="<?php echo $option->name; ?>" src="<?php echo in_array($option->value, array("en","de","es","fr","it","ru")) ? $theme_path."img/flags/".$option->value.".png" : ""; ?>" />
				<?php echo preg_replace("/^\w{2}\s-\s(.+)$/","$1",$option->name); ?>
				<i class="caret"></i>
			</span>
			<?php break; endif;?>
			<?php endforeach; ?>
		</a>
		<ul class="dropdown-menu">
			<li class="locale-itmes">
				<ul class="menu">
					<?php foreach((array)$i_lang->value as $option): ?>
					<?php if(in_array($option->value, array("en","de","es","fr","it","ru")) && empty($option->sel)):?>
					<li>
						<a href="#" class="locale" data-value="<?php echo $option->value; ?>" data-route="<?php echo $route; ?>" data-task="<?php echo $task; ?>">
							<img alt="<?php echo $option->value; ?>" src="<?php echo $theme_path;?>img/flags/<?php echo $option->value.".png"; ?>" />
							<?php echo preg_replace("/^\w{2}\s-\s(.+)$/","$1",$option->name);?>
						</a>
					</li>
					<?php endif;?>
					<?php endforeach;?>
				</ul>
			</li>
		</ul>
	</li>
	<?php endif; ?>
	<li class="dropdown user tasks-menu user-actions">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<i class="glyphicon glyphicon-user"></i>
			<span>
				<span id="user-name"><?php
				echo empty($firstname) ? '' : $firstname.' ';
				echo empty($lastname) ? '' : $lastname;
				echo empty($lastname) && empty($lastname) ? (empty($login) ? '' : $login) : '';
			?></span> <i class="caret"></i></span>
		</a>
		<ul class="dropdown-menu">
			<li>
				<ul class="menu">
					<?php if(!empty($allowed["password_field"]) && !empty($allowed["ch_pass_form"])): ?>
					<li><a class="request" data-location="dashboard.change_pass" href="#"><i class="fa fa-exchange"></i> <span><?php echo $locale['CHANGE_PASS'];?></span></a></li>
					<?php endif; ?>
					<li><a class="request" data-location="<?php echo $route; ?>.logout" href="#"><i class="fa fa-sign-out"></i> <span><?php echo $locale['LOGOUT'];?></span></a></li>
				</ul>
			</li>
		</ul>
	</li>
	<?php endif; ?>
</ul>