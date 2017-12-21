<?php
	class CI_SalesHistoryFormatter extends TableScreenFormatter {
        protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ci-sales-history'; // ii-sales-history
		protected $title = 'Customer Sales History';
		protected $datafilename = 'cisaleshist'; // iisaleshist.json
		protected $testprefix = 'cish'; // iish
		protected $formatterfieldsfile = 'cishfmattbl'; // iishfmtbl.json
		protected $datasections = array(
            "header" => "Header",
			"detail" => "Detail",
			"lotserial" => "Lot / Serial",
			"total" => "Total",
			"shipments" => "Shipments"
		);
        
        public function generate_screen() {
            $url = new \Purl\Url(wire('config')->pages->ajaxload."ci/ci-documents/order/");
            $bootstrap = new Contento();
            $content = '';
			$this->generate_tableblueprint();
			
            foreach ($this->json['data'] as $whse) {
                $content .= $bootstrap->h3('', $whse['Whse Name']);
                $tb = new Table('class=table table-striped table-bordered table-condensed table-excel|id='.key($this->json['data']));
            	$tb->tablesection('tbody');
            		foreach($whse['orders'] as $invoice) {
                         for ($x = 1; $x < $this->tableblueprint['header']['maxrows'] + 1; $x++) {
         					$tb->tr();
         					for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
         						if (isset($this->tableblueprint['header']['rows'][$x]['columns'][$i])) {
         							$column = $this->tableblueprint['header']['rows'][$x]['columns'][$i];
         							$class = wire('config')->textjustify[$this->fields['data']['header'][$column['id']]['datajustify']];
         							$colspan = $column['col-length'];
                                    $celldata = $bootstrap->b('', $column['label'].": ");
         							$celldata .= Table::generatejsoncelldata($this->fields['data']['header'][$column['id']]['type'], $invoice, $column);
                                    
                                     if ($column['id'] == 'Invoice Number') {
                                        $ordn = $invoice['Ordn'];
                                        $custID = $this->json['custid'];
 										$url->query->setData(array('custID' => $custID, 'ordn' => $ordn, 'returnpage' => urlencode(wire('page')->fullURL->getUrl())));
 										$href = $url->getUrl();
 										$celldata .= "&nbsp; " . $bootstrap->openandclose('a', "href=$href|class=load-order-documents|title=Load Order Documents|aria-label=Load Order Documents|data-ordn=$ordn|data-custid=$custID|data-type=$this->type", $bootstrap->createicon('fa fa-file-text'));
									}
         							$tb->td("colspan=$colspan|class=$class", $celldata);
         							$i = ($colspan > 1) ? $i + ($colspan - 1) : $i;
         						} else {
         							$tb->td();
         						}
         					}
         				}
                        
                        for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
                			$tb->tr();
                			for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
                				if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
                					$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
                					$class = wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['headingjustify']];
                					$colspan = $column['col-length'];
                					$tb->td("colspan=$colspan|class=$class", $bootstrap->b('', $column['label']));
                					$i = ($colspan > 1) ? $i + ($colspan - 1) : $i;
                				} else {
                					$tb->td();
                				}
                			}
                		}
                        foreach ($invoice['details'] as $detail) {
                            for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
            					$tb->tr();
            					for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
            						if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
            							$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
            							$class = wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
            							$colspan = $column['col-length'];
            							$celldata = Table::generatejsoncelldata($this->fields['data']['detail'][$column['id']]['type'], $detail, $column);
            							$tb->td("colspan=$colspan|class=$class", $celldata);
            							$i = ($colspan > 1) ? $i + ($colspan - 1) : $i;
            						} else {
            							$tb->td();
            						}
            					}
            				}
                            
                            if (sizeof($detail['lotserial']) > 0) {
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
            					
            					foreach ($detail['lotserial'] as $lot) {
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
                                
                                foreach ($detail['detailnotes'] as $ordernote) {
            						$tb->tr('class=show-notes');
            						for ($i = 1; $i < $tableblueprint['maxcolumns'] + 1; $i++) {
            							if ($i == 2) {
            								$tb->td('colspan=2', $ordernote['Detail Notes']);
            								$i++;
            							} else {
            								$tb->td();
            							}
            						}
            					}
            				} // END IF (sizeof($invoice['lots']) > 0)
                        }
            		}
            	$tb->closetablesection('tbody');
            	$content = $tb->close();
			}
			return $content;
        }
    }
