<?php defined('_PORTAL') or die(); ?>
<link href="//code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $theme_path;?>css/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $theme_path;?>css/morris.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $theme_path;?>js/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
<script src="<?php echo $theme_path;?>js/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
<script src="<?php echo $theme_path;?>js/plugins/flot/jquery.flot.pie.min.js" type="text/javascript"></script>
<script src="<?php echo $theme_path;?>js/plugins/flot/jquery.flot.time.min.js" type="text/javascript"></script>
<script src="<?php echo $theme_path;?>js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
<script src="<?php echo $theme_path;?>js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
<script src="<?php echo $theme_path;?>js/plugins/jqueryKnob/jquery.knob.js" type="text/javascript"></script>
<div class="row">
	<div class="col-lg-3 col-xs-6">
		<div class="small-box bg-aqua">
			<div class="inner">
				<h3><?php echo $totals['calls_outgoing']; ?></h3>
				<p><?php echo $locale['outgoing_calls']; ?></p>
			</div>
			<div class="icon">
				<i class="ion ion-android-call"></i><i class="ion ion-arrow-right-c"></i>
			</div>
			<a href="#" class="small-box-footer request" data-location="xdrs">
				<?php echo $locale['more']; ?> <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	<div class="col-lg-3 col-xs-6">
		<div class="small-box bg-green">
			<div class="inner">
				<h3><?php echo $totals['calls_incoming']; ?></h3>
				<p><?php echo $locale['incoming_calls']; ?></p>
			</div>
			<div class="icon">
				<i class="ion ion-android-call"></i><i class="ion ion-arrow-left-c"></i>
			</div>
			<a href="#" class="small-box-footer request" data-location="xdrs">
				<?php echo $locale['more']; ?> <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	<div class="col-lg-3 col-xs-6">
		<div class="small-box bg-yellow">
			<div class="inner">
				<h3><?php echo $totals['sms']; ?></h3>
				<p><?php echo 'SMS'; ?></p>
			</div>
			<div class="icon">
				<i class="ion ion-android-mail"></i>
			</div>
			<a href="#" class="small-box-footer request" data-location="xdrs">
				<?php echo $locale['more']; ?> <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	<div class="col-lg-3 col-xs-6">
		<div class="small-box bg-red">
			<div class="inner">
				<h3><?php echo $totals['mobile_data'].' MB'; ?></h3>
				<p><?php echo $locale['MOBILE_INTERNET']; ?></p>
			</div>
			<div class="icon">
				<i class="ion ion-android-download"></i>
			</div>
			<a href="#" class="small-box-footer request" data-location="xdrs">
				<?php echo $locale['more']; ?> <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12" id="total-charged">
		<div class="box box-solid">
			<div class="box-header">
				<i class="fa fa-pie-chart"></i>
				<h3 class="box-title"><?php echo $locale['total_charged']; ?></h3>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-3 col-sm-6 col-xs-6 text-center">
						<input type="text" class="knob" value="<?php echo floatval($percentages['calls_outgoing']); ?>" data-width="120" data-height="120" data-fgColor="#00c0ef"/>
						<div class="knob-label"><?php echo $locale['outgoing_calls']; ?></div>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-6 text-center">
						<input type="text" class="knob" value="<?php echo floatval($percentages['calls_incoming']); ?>" data-width="120" data-height="120" data-fgColor="#00a65a"/>
						<div class="knob-label"><?php echo $locale['incoming_calls']; ?></div>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-6 text-center">
						<input type="text" class="knob" value="<?php echo floatval($percentages['sms']); ?>" data-width="120" data-height="120" data-fgColor="#f39c12"/>
						<div class="knob-label">SMS</div>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-6 text-center">
						<input type="text" class="knob" value="<?php echo floatval($percentages['mobile_data']); ?>" data-width="120" data-height="120" data-fgColor="#f56954"/>
						<div class="knob-label"><?php echo $locale['MOBILE_INTERNET']; ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="box">
			<div class="box-header">
				<i class="fa fa-phone"></i>
				<h3 class="box-title"><?php echo $locale['voice_calls']; ?></h3>
			</div>
			<div class="box-body">
				<div id="calls-chart" style="height: 300px;"></div>
			</div>
		</div>
		<div class="box box-warning">
			<div class="box-header">
				<i class="fa fa-envelope"></i>
				<h3 class="box-title">SMS</h3>
			</div>
			<div class="box-body">
				<div id="sms-chart" style="height: 300px;"></div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="box box-solid bg-light-blue-gradient">
			<div class="box-header">
				<i class="fa fa-map-marker"></i>
				<h3 class="box-title"><?php echo $locale['COUNTRIES']; ?></h3>
			</div>
			<div class="box-body">
				<div id="world-map" style="height: 300px;"></div>
			</div>
		</div>
		<div class="box box-danger">
			<div class="box-header">
				<i class="fa fa-cloud-download"></i>
				<h3 class="box-title"><?php echo $locale['MOBILE_INTERNET']; ?></h3>
			</div>
			<div class="box-body">
				<div id="mobile-data" style="height: 300px;"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		/* Calls chart */
		var incoming = [], outgoing = [];
		<?php
			$dates = array_keys($calls);
			if(!empty($dates)):
				foreach($dates as $date):
					echo "incoming.push([new Date(\"{$date}\"), parseInt({$calls[$date]['incoming']['number']})]);";
					echo "outgoing.push([new Date(\"{$date}\"), parseInt({$calls[$date]['outgoing']['number']})]);";
				endforeach;
			else:
				$i = 0;
				while($i < 31):
					$date = date("Y-m-d",strtotime(date("Y-m-d",time()) . "-".$i++." days"));
					echo "incoming.push([new Date(\"{$date}\"), 0]);";
					echo "outgoing.push([new Date(\"{$date}\"), 0]);";
				endwhile;
			endif;
		?>
		var line_data1 = {
			data: incoming,
			color: "#00a65a",
			name: "incoming"
		};
		var line_data2 = {
			data: outgoing,
			color: "#00c0ef",
			name: "outgoing"
		};
		$.plot("#calls-chart", [line_data1, line_data2], {
			grid: {
				hoverable: true,
				borderColor: "#f3f3f3",
				borderWidth: 1,
				tickColor: "#f3f3f3"
			},
			series: {
				shadowSize: 0,
				lines: {show: true},
				points: {show: true}
			},
			lines: {
				fill: false,
				color: ["#3c8dbc", "#f56954"]
			},
			yaxis: {show: true},
			xaxis: {
				show: true,
				mode: "time",
			    timeformat: "%m/%d"
			}
		});
		$("<div class='tooltip-inner' id='calls-chart-tooltip'></div>").css({
			position: "absolute",
			display: "none",
			opacity: 0.8
		}).appendTo("body");
		$("#calls-chart").bind("plothover", function(event, pos, item) {
			if (item)
			{
				var title = "outgoing" == item.series.name ? "<?php echo $locale['outgoing_calls']; ?>" : "<?php echo $locale['incoming_calls']; ?>",
					date = item.series.data[item.dataIndex][0],
					x = date.getFullYear() + '/' + (parseInt(date.getMonth())+1) + '/' + date.getDate(),
					y = item.datapoint[1].toFixed(2);
				$("#calls-chart-tooltip").html(title + ": " + parseInt(y) + " (" + x + ")")
					.css({top: item.pageY + 5, left: item.pageX + 5})
					.fadeIn(200);
			}
			else { $("#calls-chart-tooltip").hide(); }

		});

		/* World map */
		var countries = {},
			data = {};
		<?php
			if(!empty($countries)):
				foreach($countries as $country_code => $info):
					if('UNDEFINED' != $country_code):
					$_incoming_calls = empty($info['calls']['incoming']['number']) ? 0 : $info['calls']['incoming']['number'];
					$_outgoing_calls = empty($info['calls']['outgoing']['number']) ? 0 : $info['calls']['outgoing']['number'];
					$_totals = $_incoming_calls + $_outgoing_calls;
					$_sms = empty($info['sms']['number']) ? 0 : $info['sms']['number'];
					$_mobile_data = empty($info['mobile_data']['number']) ? 0 : $info['mobile_data']['number'];
					echo "countries['{$country_code}'] = {incoming:{$_incoming_calls},outgoing:{$_outgoing_calls},sms:{$_sms},mobile_data:{$_mobile_data}};";
					echo "data['{$country_code}'] = {$_totals};";
					endif;
				endforeach;
			endif;
		?>
	    $('#world-map').vectorMap({
	        map: 'world_mill_en',
	        backgroundColor: "transparent",
	        regionStyle: {
	            initial: {
	                fill: '#e0e0e0',
	                "fill-opacity": 1,
	                stroke: 'none',
	                "stroke-width": 0,
	                "stroke-opacity": 1
	            }
	        },
	        series: {
	            regions: [{
	                    values: data,
	                    scale: ["#acb8a4", "#b4b48d", "#c5aead"],
	                    normalizeFunction: 'polynomial'
	                }]
	        },
	        onRegionLabelShow: function(e, el, code) {
				if (typeof(countries[code]) != "undefined")
				{
	                el.html(el.html() + '<br/><?php echo $locale['outgoing_calls']; ?>:  ' + countries[code]['outgoing'] + '<br/>'
	                		+ '<?php echo $locale['incoming_calls']; ?>:  ' + countries[code]['incoming'] + '<br/>'
	                		+ 'SMS:  ' + countries[code]['sms'] + '<br/>'
	                		+ '<?php echo $locale['MOBILE_INTERNET']; ?>:  ' + countries[code]['mobile_data'] + ' MB<br/>'
	                );
				}
				else
				{
	                el.html(el.html() + '<br/><?php echo $locale['outgoing_calls']; ?>:  0<br/>'
	                		+ '<?php echo $locale['incoming_calls']; ?>:  0<br/>'
	                		+ 'SMS:  0<br/>'
	                		+ '<?php echo $locale['MOBILE_INTERNET']; ?>:  0 MB<br/>'
	                );
				}
	        }
	    });

	    /* SMS chart */
	    var data = [];
		<?php
			if(!empty($sms)):
				foreach($sms as $date => $info):
					echo "data.push([new Date(\"{$date}\"),parseFloat({$info['number']})]);";
				endforeach;
			else:
				$i = 0;
				while($i < 31):
					$date = date("Y-m-d",strtotime(date("Y-m-d",time()) . "-".$i++." days"));
					echo "data.push([new Date(\"{$date}\"),0]);";
				endwhile;
			endif;
		?>
        var line = $.plot("#sms-chart", [data], {
            grid: {
            	hoverable: true,
                borderColor: "#f3f3f3",
                borderWidth: 1,
                tickColor: "#f3f3f3"
            },
			series: {
				shadowSize: 0,
				lines: {show: true},
				points: {show: true}
			},
            lines: {
                fill: false,
                color: "#3c8dbc"
            },
            yaxis: {show: true},
            xaxis: {
				show: true,
				mode: "time",
				timeformat: "%m/%d"
            }
		});
		$("<div class='tooltip-inner' id='sms-chart-tooltip'></div>").css({
			position: "absolute",
			display: "none",
			opacity: 0.8
		}).appendTo("body");
		$("#sms-chart").bind("plothover", function(event, pos, item) {
			if (item)
			{
				var title = "SMS",
					date = item.series.data[item.dataIndex][0],
					x = date.getFullYear() + '/' + (parseInt(date.getMonth())+1) + '/' + date.getDate(),
					y = item.datapoint[1].toFixed(2);
				$("#sms-chart-tooltip").html(title + ": " + parseInt(y) + " (" + x + ")")
					.css({top: item.pageY + 5, left: item.pageX + 5})
					.fadeIn(200);
			}
			else { $("#sms-chart-tooltip").hide(); }

		});

        /* Mobile data chart */
        var data = [];
		<?php
			if(!empty($mobile_data)):
				foreach($mobile_data as $date => $info):
					echo "data.push([new Date(\"{$date}\"), parseFloat({$info['number']})]);";
				endforeach;
			else:
				$i = 0;
				while($i < 31):
					$date = date("Y-m-d",strtotime(date("Y-m-d",time()) . "-".$i++." days"));
					echo "data.push([new Date(\"{$date}\"), 0]);";
				endwhile;
			endif;
		?>
        var interactive_plot = $.plot("#mobile-data", [data], {
            grid: {
                borderColor: "#f3f3f3",
                borderWidth: 1,
                tickColor: "#f3f3f3"
            },
            series: {
                shadowSize: 0,
                color: "#3c8dbc"
            },
            lines: {
                fill: true,
                color: "#3c8dbc"
            },
            yaxis: {show: true},
            xaxis: {
				show: true,
				mode: "time",
				timeformat: "%m/%d"
            }
		});

		/* Services chart */
		$(".knob").knob({
			readOnly: true,
			draw: function() { $(this.i).val(this.cv + '%'); }
		});
	});
    function labelFormatter(label, series) {
        return "<div style='font-size:16px; text-align:center; padding:2px; color: #fff; font-weight: 600;'>"+ Math.round(series.percent) + "%</div>";
    }
</script>