<table class="table table-bordered table-condensed table-striped">
	<thead> 
		<tr> <th>Date</th> <th>Amount</th> </tr> 
	</thead>
	<tbody>
		<?php foreach ($bookings as $booking) : ?>
			<tr>
				<td>
					<?= DplusDateTime::format_date($booking['bookdate'], 'F Y'); ?>
				</td>
				<td class="text-right">$ <?= $page->stringerbell->format_money($booking['amount']); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
