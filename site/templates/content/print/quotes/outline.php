<div class="row">
	<div class="col-xs-6">
		<img src="<?= $appconfig->companylogo->url; ?>" alt="<?= $appconfig->companydisplayname.' logo'; ?>">
	</div>
	<div class="col-xs-6 text-right">
		<h1>Quote # <?= $quote->quotnbr; ?></h1>
		</br>
	</div>
</div>
<div class="row">
	<div class="col-xs-6">
		<?php if (!$input->get->print) : ?>
			<a href="<?= $emailurl->getUrl(); ?>" class="btn btn-primary load-into-modal hidden-print" data-modal="#ajax-modal"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i> Send as Email</a>
		<?php endif; ?>
	</div>

	<div class="col-xs-6">
		<div class="row">
			<table class="pull-right">
				<tr> <td class="col-xs-6"><label>Quote Date:</label></td> <td class="col-xs-6 text-right"><?= $quote->quotdate; ?></td> </tr>
				<tr> <td class="col-xs-6"><label>Expire Date:</label></td> <td class="col-xs-6 text-right"><?= $quote->expdate; ?></td> </tr>
				<tr> <td class="col-xs-6"><label>Customer ID:</label></td> <td class="col-xs-6 text-right"><?= $quote->custid; ?></td></tr>
				<tr> <td class="col-xs-6"><label>Customer PO:</label></td> <td class="col-xs-6 text-right"><?= $quote->custpo; ?></td> </tr>
				<tr> <td class="col-xs-6"><label>Shipping Method:</label></td> <td class="col-xs-6 text-right"><?= $quote->shipviadesc; ?></td></tr>
				<tr> <td class="col-xs-6"><label>Payment Terms:</label></td> <td class="col-xs-6 text-right"><?= $quote->termcodedesc; ?></td></tr>
				<tr> <td class="col-xs-6"><label>Salesperson:</label></td> <td class="col-xs-6 text-right"><?= $quote->sp1name; ?></td></tr>
			</table>
		</div>
	</div>
</div>
</br>

<div class="row">
	<div class="col-xs-4">
		<div class="address-header"><h4>Ship To </h4></div>
		<address>
			<?php if (strlen($quote->shipname) > 0) : ?>
				<?= $quote->shipname; ?><br>
			<?php endif; ?>
			<?= $quote->shipaddress; ?><br>
			<?php if (strlen($quote->shipaddress2) > 0) : ?>
				<?= $quote->shipaddress2; ?><br>
			<?php endif; ?>
			<?= $quote->shipcity.", ".$quote->shipstate." ".$quote->shipzip; ?>
		</address>
	</div>
	<div class="col-xs-4">
		<div class="address-header"><h4>Bill To </h4></div>
		<address>
			<?= $quote->billname; ?><br>
			<?= $quote->billaddress; ?><br>
			<?php if (strlen($quote->billaddress2) > 0) : ?>
				<?= $quote->billaddress2; ?><br>
			<?php endif; ?>
			<?= $quote->billcity.", ".$quote->billstate." ".$quote->billzip; ?>
		</address>
	</div>
</div>
</br>
</br>

<table class="table table-bordered table-condensed">
	 <tr class="detail item-header active">
		<th class="text-right">Qty</th>
		<th class="text-center">Item</th>
		<th>Description</th>
		<th>U/M</th>
		<th class="text-right" width="100">Price</th>
		<th class="text-right">Line Total</th>
	</tr>
	<?php $details = $quotedisplay->get_quotedetails($quote); ?>
	<?php foreach ($details as $detail) : ?>
		<tr class="detail table-bordered">
			<td class="text-right"> <?= intval($detail->quotqty); ?> </td>
			<td class="text-center"><?= $detail->itemid; ?></td>
			<td><?= $detail->desc1; ?></td>
			<td><?=$detail->uom; ?></td>
			<td class="text-right">$ <?= formatmoney($detail->quotprice); ?></td>
			<td class="text-right">$ <?= formatmoney($detail->quotprice * intval($detail->quotqty)) ?> </td>
		</tr>
	<?php endforeach; ?>
</table>

<div class="row">
	<div class="col-xs-9"></div>
	<div class="col-xs-3">
		<table class="table table-condensed pull-right">
			<tr><td class="col-xs-6"><label>Subtotal</label></td> <td class="text-right col-xs-6">$ <?= formatmoney($quote->subtotal); ?></td></tr>
			<tr><td class="col-xs-6"><label>Tax</label></td> <td class="text-right col-xs-6">$ <?= formatmoney($quote->salestax); ?></td></tr>
			<tr><td class="col-xs-6"><label>Freight</label></td> <td class="text-right col-xs-6">$ <?= formatmoney($quote->freight); ?></td></tr>
			<tr><td class="col-xs-6"><label>Misc.</label></td> <td class="text-right col-xs-6">$ <?= formatmoney($quote->misccost); ?></td></tr>
			<tr class="active"><td class="col-xs-6"><label>Total</label></td> <td class="text-right col-xs-6">$ <?= formatmoney($quote->ordertotal); ?></td></tr>
		</table>
	</div>
</div>
	
