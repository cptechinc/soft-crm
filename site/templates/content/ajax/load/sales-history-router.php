<?php
	$filteron = $input->urlSegment(2);
	$ordn = ($input->get->ordn) ? $input->get->text('ordn') : NULL;
	
	switch ($filteron) {
		case 'cust':
			// TODO
			// $custID = $sanitizer->text($input->urlSegment(3));
			// $shipID = '';
			// if ($input->urlSegment(4)) {
			//     if (strpos($input->urlSegment(4), 'shipto') !== false) {
			//         $shipID = str_replace('shipto-', '', $input->urlSegment(4));
			//     }
			// }
			// $page->body = $config->paths->content.'customer/cust-page/orders/orders-panel.php';
			break;
		default:
			$page->body = $config->paths->content.'dashboard/sales-history/sales-history-panel.php';
			break;
	}

	if ($config->ajax) {
		if ($config->modal) {
			include $config->paths->content.'common/modals/include-ajax-modal.php';
		} else {
			include($page->body);
		}
	} else {
		include $config->paths->content."common/include-blank-page.php";
	}