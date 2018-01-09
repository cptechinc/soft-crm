<?php
    function renderNavTree($items, $maxDepth = 3) {
    	// if we've been given just one item, convert it to an array of items
    	if($items instanceof Page) $items = array($items);
    	// if there aren't any items to output, exit now
    	if(!count($items)) return;
    	// $out is where we store the markup we are creating in this function
    	// start our <ul> markup
    	echo "<div class='list-group'>";
    	// cycle through all the items
    	foreach($items as $item) {
    		// markup for the list item...
    		// if current item is the same as the page being viewed, add a "current" class to it
    		// markup for the link
    		if($item->id == Processwire\wire('page')->id) {
    			echo "<a href='$item->url' class='list-group-item bg-primary'>$item->title</a>";
    		} else {
    			echo "<a href='$item->url' class='list-group-item'>$item->title</a>";
    		}
    		// if the item has children and we're allowed to output tree navigation (maxDepth)
    		// then call this same function again for the item's children
    		if($item->hasChildren() && $maxDepth) {
    			renderNavTree($item->children, $maxDepth-1);
    		}
    		// close the list item
    		//echo "</li>";
    	}
    	// end our <ul> markup
    	echo "</div>";
    }
/* =============================================================
   STRING FUNCTIONS
 ============================================================ */
     function latin_to_utf($string) {
    	$encode = array("â€¢" => '&bull;', "â„¢" => '&trade;', "â€" => '&prime;');
    	foreach ($encode as $key => $value) {
    		if (strpos($string, $key) !== false) {
    			$string = str_replace($key, $value, $string);
    		}
    	}
    	return $string;
     }
 
     function ordinal($number) {
		$ends = array('th','st','nd','rd','th','th','th','th','th','th');
		if ((($number % 100) >= 11) && (($number%100) <= 13))
			return $number. 'th';
		else
			return $number. $ends[$number % 10];
	}

    function ordinalword($number) {
        switch ($number) {
            case '1':
                return 'first';
                break;
            case '2':
                return 'second';
                break;
            case '3':
                return 'third';
                break;
            case '4':
                return 'fourth';
                break;
        }
    }
	
	function strToHex($string){
		$hex = '';
		for ($i=0; $i<strlen($string); $i++){
			$ord = ord($string[$i]);
			$hexCode = dechex($ord);
			$hex .= substr('0'.$hexCode, -2);
		}
		return strToUpper($hex);
	}

	function hexToStr($hex){
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
	
	function formatmoney($amt) {
		return number_format($amt, 2, '.', ',');
	}

	function formatnumber($number, $beforedecimal, $afterdecimal) {
		$array = explode('.', $number);
		return str_pad($array[0], $beforedecimal, '0', STR_PAD_LEFT) . '.' . str_pad($array[1], $afterdecimal, '0', STR_PAD_RIGHT);
	}

	function formatphone($number) {
		return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '$1-$2-$3', $number);
	}
	
	function cleanforjs($str) {
		return urlencode(str_replace(' ', '-', str_replace('#', '', $str)));
	}
	
/* =============================================================
   URL FUNCTIONS
 ============================================================ */
	function paginate($url, $page, $insertafter, $hash) {
		if (strpos($url, 'page') !== false) {
			$regex = "((page)\d{1,3})";
			if ($page > 1) { $replace = "page".$page; } else {$replace = ""; }
			$newurl = preg_replace($regex, $replace, $url);
		} else {
			$insertafter = str_replace('/', '', $insertafter)."/";
			$regex = "(($insertafter))";
			if ($page > 1) { $replace = $insertafter."page".$page."/";} else {$replace = $insertafter; }
			$newurl = preg_replace($regex, $replace, $url);
		}
		return $newurl . $hash;
	 }

/* =============================================================
   ORDERBY / SORT FUNCTIONS
 ============================================================ */
	function get_symbols($orderby, $match, $page_orderby) { // DEPRECATED 10/6/2017 REPLACED BY TABLEPAGESORTCLASS
		$symbol = "";
		if ($orderby == $match) {
			if ($page_orderby == "ASC") {
				$symbol = "&#x25B2;";
				$symbol = "<span class='glyphicon glyphicon-arrow-up'></span>";
			} else {
				$symbol = "&#x25BC;";
				$symbol = "<span class='glyphicon glyphicon-arrow-down'></span>";
			}
		}
		return $symbol;
	}

	function get_sorting_rule($orderingby, $sort, $orderby) { // DEPRECATED 10/6/2017 REPLACED BY TABLEPAGESORTCLASS
		if ($orderingby != $orderby || $sort == false) {
			$sortrule = "ASC";
		} else {
			switch ($sort) {
				case 'ASC':
					$sortrule = 'DESC';
					break;
				case 'DESC':
					$sortrule = 'ASC';
					break;
			}
		}
		return $sortrule;
	}

/* =============================================================
   ORDERS FUNCTIONS
 ============================================================ */
	function returntracklink($carrier, $tracknbr, $on) {
		$link = '';
		if (strpos(strtolower($carrier), 'fed') !== false) {
			$link = "https://www.fedex.com/fedextrack/WTRK/index.html?action=track&trackingnumber=".$tracknbr."&cntry_code=us&fdx=1490";
		} elseif (strpos(strtolower($carrier), 'ups') !== false) {
			$link = "http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=".$tracknbr."&loc=en_us";
		} elseif (strpos(strtolower($carrier), 'gro') !== false) {
			$link = "http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=".$tracknbr."&loc=en_us";
		} elseif ((strpos(strtolower($carrier), 'will') !== false)) {
			$link = "#$on";
		} else {
			$link = "#$on";
		}
		return $link;
	}

/* =============================================================
   HTML CONTENT FUNCTIONS
 ============================================================ */
	function highlight($haystack, $needle, $element) { //\b(\w*".$needle."\w*)\b //DEPRECATED now use vend/StringerBell
		$regex = "/(".$needle.")/i";
		$contains = preg_match($regex, $haystack, $matches);
		if ($contains) {
			$replace =  str_replace('{ele}', $matches[0], $element);
			return preg_replace($regex, $replace, $haystack);
		} else {
			return $haystack;
		}
	}
	
	function createshopasform($custID, $shipID) {
		$form = '<form action="'.Processwire\wire(config)->pages->customer.'redir/" method="post">';
		$form .= '<input type="hidden" name="action" value="shop-as-customer">';
		$form .= '<input type="hidden" name="page" value="'.Processwire\wire(config)->filename.'">';
		$form .= '<input type="hidden" name="custID" value="'.$custID.'">';
		if ($shipID) {
			$form .= '<input type="hidden" name="shipID" value="'.$shipID.'">';
			$form .= '<button type="submit" class="btn btn-sm btn-primary">Shop as '.get_customername($custID).' - '. $shipID.'</button>';
		} else {
			$form .= '<button type="submit" class="btn btn-sm btn-primary">Shop as '.get_customername($custID).'</button>';
		}
		$form .= '</form>';
		return $form;
	}
	
	function createalert($alerttype, $msg) { // DEPRECATED 10/2/2017
		return '<div class="alert alert-'.$alerttype.'" role="alert">' . $msg . '</div>';
	}

	function makeprintlink($link, $msg) { // DEPRECATED 10/2/2017
		return '<a href="'.$link.'" class="h4" target="_blank"><i class="glyphicon glyphicon-print" aria-hidden="true"></i> '.$msg.'.</a>';
	}

 /* =============================================================
   DB FUNCTIONS
 ============================================================ */
	 function returnsqlquery($sql, $oldtonew, $havequotes) {
		$i = 0;
		foreach ($oldtonew as $old => $new) {
			if ($havequotes[$i]) {
				$sql = str_replace($old, "'".$new."'", $sql);
			} else {
				$sql = str_replace($old, $new, $sql);
			}
			$i++;
		}
		return $sql;
	}
	
	function returnlimitstatement($limit, $page) {
		if ($limit) {
			if ($page > 1 ) {$start_point = ($page * $limit) - $limit; } else { $start_point = 0; }
			return "LIMIT ".$start_point.",".$limit;
		} else {
			return "";
		}
	}
	/**
	 * [returnpreppedquery description]
	 * @param  [array] $originalarray [Key-Valued array with original record column values]
	 * @param  [type] $changedarray  [Key-Valued array with changed record column values]
	 * @return [array]                [Array that has the Set statement with prepped values, columns changed, how many need quotes, AND
	 * 								   The count of how many values changed between the original and changed.]
	 */
	function returnpreppedquery($originalarray, $changedarray) {
		$withquotes = $switching = array();
		$setstmt = '';
		$columns = array_keys($originalarray);
		foreach ($columns as $column) {
			if (strlen($changedarray[$column])) {
				if ($originalarray[$column] != $changedarray[$column]) {
					$prepped = ':'.$column;
					$setstmt .= $column." = ".$prepped.", ";
					$switching[$prepped] = $changedarray[$column];
					$withquotes[] = true;
				}
			}
		}
		$setstmt = rtrim($setstmt, ', ');
		return array(
			'switching' => $switching,
			'withquotes' => $withquotes,
			'setstatement' => $setstmt,
			'changecount' => sizeof($switching)
		);
	}

	function returnupdatequery($newlinks, $oldlinks, $wherelinks) {
		$wherestmt = '';
		$query = returnpreppedquery($oldlinks, $newlinks);
		foreach ($wherelinks as $column => $val) {
			$prepped = ':x'.$column;
			$wherestmt .= $column." = ".$prepped." AND ";
			$query['switching'][$prepped] = $val;
			$query['withquotes'][] = true;
		}
		$wherestmt = rtrim($wherestmt, ' AND ');
		$query['wherestatement'] = $wherestmt;
		return $query;
	}

	function returnwherelinks($linkarray) {
		$withquotes = $switching = array();
		$wherestmt = '';
		$columns = array_keys($linkarray);
		foreach ($linkarray as $key => $val) {
			if (strlen($val)) {
				$prepped = ':'.$key;
				$wherestmt .= $key." = ".$prepped." AND ";
				$switching[$prepped] = $val;
				$withquotes[] = true;
			}
		}
		$wherestmt = rtrim($wherestmt, ' AND ');
		return array(
			'switching' => $switching,
			'withquotes' => $withquotes,
			'wherestatement' => $wherestmt,
			'changecount' => sizeof($switching)
		);
	}

	function returninsertlinks($linkarray) {
		$withquotes = $switching = array();
		$columnlist = $valueslist = '';
		$columns = array_keys($linkarray);
		foreach ($linkarray as $key => $val) {
			if (strlen($val)) {
				$prepped = ':'.$key;
				$columnlist .= $key.", ";
				$valueslist .= $prepped.", ";
				$switching[$prepped] = $val;
				$withquotes[] = true;
			}
		}
		$columnlist = rtrim($columnlist, ', ');
		$valueslist = rtrim($valueslist, ', ');

		return array(
			'switching' => $switching,
			'withquotes' => $withquotes,
			'valuelist' => $valueslist,
			'columnlist' => $columnlist,
			'changecount' => sizeof($switching)
		);
	}

 /* =============================================================
   DATE FUNCTIONS
 ============================================================ */
	function get_time($timeString) {
		$partofDay = ""; $colon = ":";
		$timeAsString = substr($timeString, 0, 2) . $colon . substr($timeString, 2, 2);
		$time = explode($colon, $timeAsString, 2);
		$hour = $time[0];

		$hr = (int)$hour;

		if ($hr == 00) {
			$hr = 12;
			$partofDay = "AM";
		} else if ($hr > 12) {
			$hr = $hr - 12;
			$partofDay = "PM";
		} else {
			$partofDay = "AM";
		}

		$time = strval($hr) . $colon . $time[1].' '.$partofDay;
		return $time;
	}

	function dplusdate($date) { // DEPRECATED 8/23/2017 DELETE IN A MONTH
		if (date('m/d/Y', strtotime($date)) == "12/31/1969") {
			return '';
		} else {
			return date('m/d/Y', strtotime($date));
		}
	}
	
/* =============================================================
  FILE FUNCTIONS
============================================================ */
	function writedplusfile($data, $filename) {
		$file = '';
		foreach ($data as $key => $value) {
			if (is_string($key)) {
				if (is_string($value)) {
					$file .= $key . "=" . $value . "\n";
				} else {
					$file .= $key . "\n";
				}
			} else {
				$file .= $value . "\n";
			}

		}
		$vard = "/usr/capsys/ecomm/" . $filename;
		$handle = fopen($vard, "w") or die("cant open file");
		fwrite($handle, $file);
		fclose($handle);
	}
	
	function writedataformultitems($data, $items, $qtys) {
		for ($i = 0; $i < sizeof($items); $i++) {
			$itemID = str_pad(Processwire\wire('sanitizer')->text($items[$i]), 30, ' ');
			$qty = Processwire\wire('sanitizer')->text($qtys[$i]);
			if (empty($qty)) {$qty = "1"; }
			$data[] = "ITEMID=".$itemID."QTY=".$qty;
		}
		return $data;
	}
	
	/**
	 * [convertfiletojson description]
	 * @param  [string] $file [String that contains file location]
	 * @return [string]       [Returns json-encode string]
	 */
	function convertfiletojson($file) {
		$json = file_get_contents($file);
		$json = preg_replace('~[\r\n]+~', '', $json);
		$json = utf8_clean($json);
		return $json;
	}
	
	function hashtemplatefile($filename) {
		$hash = hash_file(Processwire\wire('config')->userAuthHashType, Processwire\wire('config')->paths->templates.$filename);
		return Processwire\wire('config')->urls->templates.$filename.'?v='.$hash;
	}
	
	function curl_redir($url) {
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_FOLLOWLOCATION => true
		));
		sleep(2);
		return curl_exec($curl);
	}

 /* =============================================================
   PROCESSWIRE USER FUNCTIONS
 ============================================================ */
	function setupuser($sessionID) {
		$loginrecord = get_loginrecord($sessionID);
		Processwire\wire('user')->fullname = $loginrecord['loginname'];
		Processwire\wire('user')->loginid = $loginrecord['loginid'];
		Processwire\wire('user')->hascontactrestrictions = $loginrecord['restrictcustomer'];
		Processwire\wire('user')->hasrestrictions = $loginrecord['restrictuseraccess'];
		Processwire\wire('user')->hasorderlocked = hasanorderlocked(session_id());
		if (Processwire\wire('user')->hasorderlocked) {
			Processwire\wire('user')->lockedordn = getlockedordn(session_id());
		}
		Processwire\wire('user')->hasquotelocked = hasaquotelocked(session_id());
		if (Processwire\wire('user')->hasquotelocked) {
			$user->lockedqnbr = getlockedquotenbr(session_id());
		}
	}
    
    /**
    	 * Trigger a PHP error, warning, or notice. Automatically prepends 'CP-DPLUSO' for easier management. Note
    	 * that fatal errors (E_USER_ERROR) will prevent further processing.
    	 * 
    	 * @param    string    $error          Error message (max 1024 characters)
    	 * @param    integer   $level          PHP error level, from PHP's E_USER constants
    	 * 
    	 * @return   null
    	 */
    	function error($error, $level = E_USER_ERROR) {
    		$error = (strpos($error, 'CP-DPLUSO: ') !== 0 ? 'CP-DPLUSO: ' . $error : $error);
    		trigger_error($error, $level);
    		return;
    	}
