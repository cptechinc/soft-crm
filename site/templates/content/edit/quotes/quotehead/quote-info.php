<legend>Contact</legend>
<table class="table table-striped table-bordered table-condensed">
	<tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['contact']['label']; ?><?= $formconfig->generate_asterisk('contact'); ?></td>
        <td> <input type="text" name="contact" class="form-control input-sm <?php echo $formconfig->generate_showrequiredclass('contact'); ?>" id="shiptocontact" value="<?= $quote->contact; ?>"> </td>
    </tr>
    <?php if ($config->phoneintl) : ?>
		<tr>
			<td class="control-label">International ?</td>
			<td>
				<select class="form-control input-sm" name="intl" onChange="showphone(this.value)">
					<?php foreach ($config->yesnoarray as $key => $value) : ?>
						<?php $selected = ($quote->phintl == $value) ? 'selected' : ''; ?>
						<option value="<?= $value; ?>" <?= $selected; ?>><?= $key; ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
    	<?php include $config->paths->content.'edit/quotes/quotehead/phone-domestic.php'; ?>
    <?php else : ?>
    	<?php include $config->paths->content.'edit/quotes/quotehead/phone-domestic.php'; ?>
    <?php endif; ?>
    <tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['emailadr']['label']; ?><?= $formconfig->generate_asterisk('emailadr'); ?></td>
        <td> <input type="text" name="contact-email" class="form-control input-sm <?php echo $formconfig->generate_showrequiredclass('emailadr'); ?> email" value="<?= $quote->emailadr; ?>"> </td>
    </tr>
</table>

<legend>Quote</legend>
<table class="table table-striped table-bordered table-condensed">
	<tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['sp1']['label']; ?><?= $formconfig->generate_asterisk('sp1'); ?></td> <td> <p class="form-control-static "><?= $quote->sp1; ?> - <?= $quote->sp1name; ?></p> </td>
    </tr>
	<tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['custpo']['label']; ?><?= $formconfig->generate_asterisk('custpo'); ?></td> <td> <input type="text" name="custpo" class="form-control input-sm <?php echo $formconfig->generate_showrequiredclass('custpo'); ?>" value="<?= $quote->custpo; ?>"> </td>
    </tr>
    <tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['custref']['label']; ?><?= $formconfig->generate_asterisk('custref'); ?></td> <td> <input type="text" name="reference" class="form-control input-sm <?php echo $formconfig->generate_showrequiredclass('custref'); ?>" value="<?= $quote->custref; ?>"> </td>
    </tr>

	<tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['termcode']['label']; ?><?= $formconfig->generate_asterisk('termcode'); ?></td> <td class="value"><?= $quote->termcode; ?> - <?= $quote->termcodedesc; ?></td>
    </tr>
    <tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['taxcode']['label']; ?><?= $formconfig->generate_asterisk('taxcode'); ?></td> <td class="value"><?= $quote->taxcode; ?> - <?= $quote->taxcodedesc; ?></td>
    </tr>
    <tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['quotdate']['label']; ?><?= $formconfig->generate_asterisk('quotdate'); ?></td> <td class="value text-right"><?= $quote->quotdate; ?></td>
    </tr>
    <tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['revdate']['label']; ?><?= $formconfig->generate_asterisk('revdate'); ?></td>
        <td>
			<div class="input-group date">
            	<?php $name = 'reviewdate'; $value = $quote->revdate;  ?>
				<?php include $config->paths->content."common/date-picker.php"; ?>
            </div>
        </td>
    </tr>
    <tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['expdate']['label']; ?><?= $formconfig->generate_asterisk('expdate'); ?></td>
        <td>
			<div class="input-group date">
               	<?php $name = 'expiredate'; $value = $quote->expdate;  ?>
				<?php include $config->paths->content."common/date-picker.php"; ?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="control-label"><?= $formconfig->fields['fields']['sviacode']['label']; ?><?= $formconfig->generate_asterisk('sviacode'); ?></td>
        <td>
            <select name="shipvia" class="form-control input-sm">
				<option value="N/A">Choose Ship Method</option>
                <?php $shipvias = getshipvias(session_id()); ?>
                <?php foreach($shipvias as $shipvia) : ?>
					<?php $selected = ($quote->sviacode == $shipvia['code']) ? 'selected' : ''; ?>
                    <option value="<?= $shipvia['code']; ?>" <?= $selected; ?>><?= $shipvia['via']; ?> </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['fob']['label']; ?><?= $formconfig->generate_asterisk('fob'); ?></td> <td class="value text-right"><?= $quote->fob; ?></td>
    </tr>
    <tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['deliverydesc']['label']; ?><?= $formconfig->generate_asterisk('deliverydesc'); ?></td> <td> <input type="text" name="delivery" class="form-control input-sm <?php echo $formconfig->generate_showrequiredclass('deliverydesc'); ?>" value="<?= $quote->deliverydesc; ?>"> </td>
    </tr>
    <tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['whse']['label']; ?><?= $formconfig->generate_asterisk('whse'); ?></td> <td class="value text-right"><?= $quote->whse; ?></td> <?php //TODO ?>
    </tr>
    <tr>
    	<td class="control-label"><?= $formconfig->fields['fields']['careof']['label']; ?><?= $formconfig->generate_asterisk('careof'); ?></td> <td> <input type="text" name="careof" class="form-control input-sm <?php echo $formconfig->generate_showrequiredclass('careof'); ?>" value="<?= $quote->careof; ?>"> </td>
    </tr>
</table>
