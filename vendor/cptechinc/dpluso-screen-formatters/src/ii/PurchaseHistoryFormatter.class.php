<?php
	class II_PurchaseHistoryFormatter extends TableScreenFormatter {
        protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-purchase-history'; // ii-sales-history
		protected $title = 'Item Purchase History';
		protected $datafilename = 'iipurchhist'; // iisaleshist.json
		protected $testprefix = 'iiph'; // iish
		protected $formatterfieldsfile = 'iiphfmattbl'; // iishfmtbl.json
		protected $datasections = array(
			"detail" => "Detail",
            "lotserial" => "Lot / Serial"
		);
		
        public function generate_screen() {
			$url = new \Purl\Url(wire('config')->pages->ajaxload."ii/ii-documents/order/");
			$itemID = $this->json['itemid'];
            $bootstrap = new Contento();
            $content = '';
			$this->generate_tableblueprint();
		    
			foreach ($this->json['data'] as $whse) {
				$content .= $bootstrap->h3('', $whse['Whse Name']);
				$tb = new Table('class=table table-striped table-bordered table-condensed table-excel|id='.key($this->json['data']));
					$tb->tablesection('thead');
						for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
							$tb->tr();
							for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
								if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
									$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
									$class = wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['headingjustify']];
									$colspan = $column['col-length'];
									$tb->th("colspan=$colspan|class=$class", $column['label']);
									$i = ($colspan > 1) ? $i + ($colspan - 1) : $i;
								} else {
									$tb->th();
								}
							}
						}
					$tb->closetablesection('thead');
					$tb->tablesection('tbody');
						foreach ($whse['orders'] as $invoice) {
							$ponbr = $invoice['Purchase Order Number'];
							if ($invoice != $whse['orders']['TOTAL']) {
								for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
									$tb->tr();
									for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
										if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
											$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
											$class = wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
											$colspan = $column['col-length'];
											$celldata = Table::generatejsoncelldata($this->fields['data']['detail'][$column['id']]['type'], $invoice, $column);
											
											if ($column['id'] == 'Purchase Order Number') {
												$url->query->setData(array('itemID' => $this->json['itemid'], 'ordn' => $ponbr, 'returnpage' => urlencode(wire('page')->fullURL->getUrl())));
												$href = $url->getUrl();
												$celldata .= "&nbsp; " . $bootstrap->openandclose('a', "href=$href|class=load-order-documents|title=Load Order Documents|aria-label=Load Order Documents|data-ordn=$ponbr|data-itemid=$itemID|data-type=$this->type", $bootstrap->createicon('fa fa-file-text'));
											}
											$tb->td("colspan=$colspan|class=$class", $celldata);
											$i = ($colspan > 1) ? $i + ($colspan - 1) : $i;
										} else {
											$tb->td();
										}
									}
								}
								if (sizeof($invoice['lots']) > 0) {
									for ($x = 1; $x < $this->tableblueprint['lotserial']['maxrows'] + 1; $x++) {
										$tb->tr();
										for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
											if (isset($this->tableblueprint['lotserial']['rows'][$x]['columns'][$i])) {
												$column = $this->tableblueprint['lotserial']['rows'][$x]['columns'][$i];
												$class = wire('config')->textjustify[$this->fields['data']['lotserial'][$column['id']]['headingjustify']];
												$colspan = $column['col-length'];
												$tb->th("colspan=$colspan|class=$class", $column['label']);
												$i = ($colspan > 1) ? $i + ($colspan - 1) : $i;
											} else {
												$tb->th();
											}
										}
									}
									foreach ($invoice['lots'] as $lot) {
										for ($x = 1; $x < $this->tableblueprint['lotserial']['maxrows'] + 1; $x++) {
											$tb->tr();
											for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
												if (isset($this->tableblueprint['lotserial']['rows'][$x]['columns'][$i])) {
													$column = $this->tableblueprint['lotserial']['rows'][$x]['columns'][$i];
													$class = wire('config')->textjustify[$this->fields['data']['lotserial'][$column['id']]['datajustify']];
													$colspan = $column['col-length'];
													$celldata = Table::generatejsoncelldata($this->fields['data']['lotserial'][$column['id']]['type'], $lot, $column);
													$tb->td("colspan=$colspan|class=$class", $celldata);
													$i = ($colspan > 1) ? $i + ($colspan - 1) : $i;
												} else {
													$tb->td();
												}
											}
										}
									}
								}
							} // END IF($invoice != $whse['orders']['TOTAL']) 
						} // END FOREACH ($whse['orders'] as $invoice) {
					$tb->closetablesection('tbody');
					$tb->tablesection('tfoot');
						$invoice = $whse['orders']['TOTAL'];
						$x = 1;
						$tb->tr('class=totals');
						for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
							if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
								$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
								$class = wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
								$celldata = Table::generatejsoncelldata($this->fields['data']['detail'][$column['id']]['type'], $invoice, $column);
								$tb->td('colspan=|class='.$class, $celldata);
							} else {
								$tb->td();
							}
						}
					$tb->closetablesection('tfoot');
					$content .= $tb->close();
			}
            return $content;
        }
		
		public function generate_javascript() {
			$bootstrap = new Contento();
			$content = $bootstrap->open('script', '');
				$content .= "\n";
				$content .= $bootstrap->indent().'$(function() {';
					if ($this->tableblueprint['detail']['rows'] < 2) {
						foreach ($this->json['data'] as $whse) {
							$name = key($this->json['data']);
							$content .= $bootstrap->indent()."$('#$name').DataTable();";
						}
					}
				$content .= $bootstrap->indent().'});';
				$content .= "\n";
			$content .= $bootstrap->close('script');
			return $content;
		}
    }
