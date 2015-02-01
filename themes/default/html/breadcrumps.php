<?php defined('_PORTAL') or die();
$breadcrumps = array($title => $route);
if('index' != $task && 'error' != $route && !empty($locale[strtoupper($task)]))
{
	$breadcrumps[$locale[strtoupper($task)]] = $route.'.'.$task;
}
$icon = Helper::GetIcon($task) ? Helper::GetIcon($task) : Helper::GetIcon($route);
$i = 0;
$last = count($breadcrumps) - 1;
?>
<h1>
	<?php echo $title ?>
	<small><?php if(!empty($cache_timestamp)): echo "<button class=\"refresh\" data-task=\"{$task}\" data-route=\"{$route}\"><i class=\"fa fa-refresh\"></i> {$cache_timestamp}</button>"; endif; ?></small>
</h1>
<?php if(count($breadcrumps) > 1): ?>
<ol class="breadcrumb">
<?php foreach($breadcrumps as $name => $link):?>
	<li<?php if($i == $last):?> class="active"<?php endif; ?>>
		<a<?php if($i): ?> class="request" data-location="<?php echo $link; ?>"<?php endif; ?> href="#"><?php echo ($i ? '' : $icon.' ').$name; ?></a>
	</li>
<?php $i++; endforeach;?>
</ol>
<?php endif; ?>