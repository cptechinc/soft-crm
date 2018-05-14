<?php
	if ($input->get->month) {
		$date = date('m/d/Y', strtotime($input->get->text('month')));
		$month = date('m', strtotime($input->get->text('month')));
		$year = date('Y', strtotime($input->get->text('month')));
	} else {
		$date = date('m/d/Y');
		$month = date('m');
		$year = date('Y');
	}
?>
<div class="panel-body">
	<div class="row">
		<div class="col-sm-4 form-group">
			<a href="<?= $actionpanel->generate_addmonthurl($date, -1); ?>" class="btn btn-success load-link" title="Prevous Month" data-loadinto="<?= $actionpanel->loadinto; ?>" data-focus="<?= $actionpanel->focus; ?>">
				<i class="fa fa-2x fa-arrow-circle-left" aria-hidden="true"></i> <span class="sr-only">Previous Month</span>
			</a>
		</div>
		<div class="col-sm-4 form-group">
			<h2 class="text-center"><strong><?= date('F', strtotime($date)); ?> <?= $year; ?></strong></h2>
			<?php if (date('M-Y', strtotime($date)) != date('M-Y')) : ?>
				<div class="text-center">
					<a href="<?= $actionpanel->generate_addmonthurl(date('m/d/Y'), 0); ?>" class="load-link" data-loadinto="<?= $actionpanel->loadinto; ?>" data-focus="<?= $actionpanel->focus; ?>">Go to Current Month</a> <i class="fa fa-calendar-o" aria-hidden="true"></i>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-sm-4 form-group">
			<a href="<?= $actionpanel->generate_addmonthurl($date, 1); ?>" title="Next Month" class="btn btn-success pull-right load-link" data-loadinto="<?= $actionpanel->loadinto; ?>" data-focus="<?= $actionpanel->focus; ?>">
				<i class="fa fa-2x fa-arrow-circle-right" aria-hidden="true"></i> <span class="sr-only">Next Month</span>
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<?php if (date('m/d/Y', strtotime($date)) != date('m/d/Y')) : ?>
				<a href="<?= $actionpanel->generate_dayviewurl(date('m/d/Y')); ?>" class="load-link" data-loadinto="#actions-panel" data-focus="#actions-panel"><b>Go to Today</b> <i class="glyphicon glyphicon-calendar"></i></a>
			<?php endif; ?>
		</div>
		<div class="col-sm-6">
			<button type="button" class="btn btn-sm btn-primary pull-right" data-toggle="collapse" data-target="#<?= $actionpanel->panelID.'-filter'; ?>" aria-expanded="false" aria-controls="<?= $actionpanel->panelID.'-filter'; ?>">
				Toggle Filter
			</button>
		</div>
	</div>
	</br>
	<div class="<?= $input->get->filter ? '' : 'collapse'; ?> form-group" id="<?= $actionpanel->panelID.'-filter'; ?>">
		<form action="<?= $actionpanel->generate_refreshurl(); ?>" class="form-ajax form-inline display-inline-block form-group" data-loadinto="<?= $actionpanel->loadinto; ?>" data-focus="<?= $actionpanel->focus; ?>">
			<input type="hidden" name="filter" value="filter">
			<input type="hidden" name="view" value="day">
			<label class="control-label">Go to Date</label> &nbsp; &nbsp;
			<div class="input-group date" style="width: 180px;">
				<?php $name = 'day'; $value = ''; ?>
				<?php include $config->paths->content."common/date-picker.php"; ?>
			</div>
			<button type="submit" class="btn btn-sm btn-success">Go</button>&nbsp; &nbsp;
		</form>
		<?php include $config->paths->content."user-actions/views/$actionpanel->paneltype/calendar/search-form.php"; ?>
	</div>
	<?= $actionpanel->generate_calendar($month, $year);?>
</div>
