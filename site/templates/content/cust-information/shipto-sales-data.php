<?php 
	$topshiptos = get_topxsellingshiptos(session_id(), $customer->custid, 25);
	$data = array();
	foreach ($topshiptos as $shipto) {
		$data[] = array(
			'label' => get_shiptoname($customer->custid, $shipto['shiptoid']),
			'value' => $shipto['amountsold'],
			'shiptoid' => $shipto['shiptoid']
		); 
	}
?>
<div class="panel panel-primary not-round" id="shipto-sales-panel">
	<div class="panel-heading not-round">
		<a href="#shipto-sales-div" class="panel-link" data-parent="#contacts-panel" data-toggle="collapse" ><i class="fa fa-pie-chart" aria-hidden="true"></i> &nbsp; Shipto Sales Data <span class="caret"></span></a>
	</div>
	<div id="shipto-sales-div">
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-5">
					<div id="shipto-sales-graph"></div>
				</div>
				<div class="col-sm-7">
					<table class="table table-striped table-bordered table-condensed table-excel" id="legend-table">
						<thead>
							<tr>
								<th>Color</th> <th>Shiptoid</th> <th>Name</th> <th>Times Sold</th> <th>Amount</th> <th>Last Sale Date</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($topshiptos as $shipto) : ?>
								<?php $location = Customer::load($customer->custid, $shipto['shiptoid']); ?>
								<?php $data[] = $location->generate_piesalesdata($shipto['amountsold']); ?>
								<tr>
									<td id="<?= $shipto['shiptoid'].'-shipto'; ?>"></td>
									<td><?= $shipto['shiptoid']; ?></td>
									<td><?= $location->get_name(); ?></td>
									<td class="text-right"><?= $shipto['timesold']; ?></td>
									<td class="text-right">$ <?= $page->stringerbell->format_money($shipto['amountsold']); ?></td>
									<td class="text-right"><?= $shipto['lastsaledate'] == 0 ? '' : DplusDateTime::formatdate($shipto['lastsaledate']); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(function() {
		$('#legend-table').DataTable();
		var pie = Morris.Donut({
			element: 'shipto-sales-graph',
			data: <?= json_encode($data); ?>,
		});
		
		pie.options.data.forEach(function(label, i) {
			var index = i;
			if (index >= 10) {
				var multiply = parseInt(i / 10);
				var subtract = 10 * multiply;
				index = i - subtract;
			}
			$('#legend-table').find('#'+label['shiptoid']+'-shipto').css('backgroundColor', pie.options.colors[index]);
		});
	});
</script>
