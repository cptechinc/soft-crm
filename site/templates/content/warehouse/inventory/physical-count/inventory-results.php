<div class="form-group">
	<a href="<?= $config->pages->inventory_physicalcount; ?>" class="btn btn-primary not-round">
		<i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Search
	</a>
</div>
<div class="list-group">
	<?php if ($resultscount) : ?>
		<?php foreach ($items as $item) : ?>
			<a href="<?= $physicalcounter->get_choose_itemURL($item->get_itemtypeproperty(), $item->get_itemidentifier()); ?>" class="list-group-item">
				<div class="row">
					<div class="col-xs-12">
						<h4 class="list-group-item-heading"><?= strtoupper($item->get_itemtypepropertydesc()) . " " . $item->get_itemidentifier(); ?></h4>
						<p class="list-group-item-text">ItemID: <?= $item->itemid; ?></p>
						<p class="list-group-item-text"><?= $item->desc1; ?></p>
						<p class="list-group-item-text"><?= "Origin: " . strtoupper($item->xorigin); ?></p>
						<p class="list-group-item-text"><?= "X-ref Item ID: " . $item->xitemid; ?></p>
					</div>
				</div>
			</a>
		<?php endforeach; ?>
	<?php else : ?>
		<a href="#" class="list-group-item">
			<div class="row">
				<div class="col-xs-12">
					<h3 class="list-group-item-heading">No items found for "<?= $input->get->text('scan'); ?>"</h3>
					<p class="list-group-item-text"></p>
				</div>
			</div>
		</a>
	<?php endif; ?>
</div>