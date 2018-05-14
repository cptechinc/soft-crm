<?php
	class ActionsPanel extends UserActionDisplay {
		use Filterable;
		use AttributeParser;

		/**
		* Session Identifier
		* @var string
		*/
		protected $sessionID;

		/**
		* Modal to load some Ajax content into
		* @var string
		*/
		protected $modal = '#ajax-modal';

		/**
		* What Action Type to filter
		* @var string
		*/
		protected $actiontype = '';

		/**
		 * Segment of URL path to put pagination after
		 * @var string
		 */
		protected $paginateafter = 'user-actions';

		/**
		* ID of element to focus on
		* @var string
		*/
		protected $focus = '';

		/**
		* ID of element to load ajax content into
		* @var string
		*/
		protected $loadinto = '';

		/**
		* Page URL
		* @var Purl\Url
		*/
		protected $pageurl = false;

		/**
		* String of data attributes
		* @var string e.g. data-loadinto='$this->loadinto' data-focus='$this->focus'
		*/
		protected $ajaxdata;

		/**
		* Whether or not the panel div shows opened or collapse
		* @var string
		*/
		protected $collapse = 'collapse';
		// TODO $tablesorter

		/**
		* Page Number
		* @var int
		*/
		protected $pagenbr = 0;

		/**
		* Number of Actions that match
		* @var int
		*/
		protected $count = 0;

		/**
		* Array of filters that will apply to the orders
		* @var array
		*/
		protected $filters = false;

		/**
		 * Array of filterable columns and their attributes
		 * @var array
		 */
		protected $filterable = array(
			'actiontype' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Action Type'
			),
			'completed' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Completed'
			),
			'assignedto' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Assigned To'
			),
			'datecreated' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Date Created',
				'date-format' => "m/d/Y H:i:s"
			),
			'datecompleted' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Date Completed',
				'date-format' => "m/d/Y H:i:s"
			),
			'dateupdated' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Date Updated',
				'date-format' => "m/d/Y H:i:s"
			),
			'duedate' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Due Date',
				'date-format' => "m/d/Y H:i:s"
			)
		);

		/**
		 * Task Status and the possible values they could have
		 * @var [type]
		 */
		protected $taskstatuses = array(
			'completed' => array(
				'value' => 'Y',
				'label' => 'Completed'
			),
			'incomplete' => array (
				'value' => '',
				'label' => 'Incomplete'
			),
			'rescheduled' => array(
				'value' => 'R',
				'label' => 'Rescheduled'
			)
		);

		protected $panelID = 'actions-panel';
		protected $panelbody = 'actions';
		protected $paneltype = 'user';
		protected $view = 'day';

		/* =============================================================
			CONSTRUCTOR FUNCTIONS
		============================================================ */
		/**
		 * Constructor
		 * @param string                $sessionID   Session Identifier
		 * @param Purl\Url              $pageurl     Object that contains URL to Page
		 * @param ProcessWire\WireInput $input       Input such as the $_GET array to run generate_filter
		 * @param bool                  $throughajax If panel was loaded through ajax
		 * @param string                $panelID     Panel element ID
		 */
		public function __construct($sessionID, \Purl\Url $pageurl, ProcessWire\WireInput $input, $throughajax = false, $panelID = '') {
			$this->sessionID = $sessionID;
			$this->pageurl = new \Purl\Url($pageurl->getUrl());
			$this->pagenbr = Paginator::generate_pagenbr($pageurl);
			$this->panelID = !empty($panelID) ? $panelID : $this->panelID;
			$this->loadinto = $this->focus = "#$this->panelID";
			$this->ajaxdata = "data-loadinto='$this->loadinto' data-focus='$this->focus'";
			$this->collapse = $throughajax ? '' : 'collapse';
			$this->generate_filter($input);
			$this->setup_pageurl();
			$this->count_actions();
		}

		/* =============================================================
			SETTER FUNCTIONS
		============================================================ */
		/**
		 * Manipulates $this->pageurl path and query data as needed
		 * then sets $this->paginateafter value
		 * @return void
		 */
		public function setup_pageurl() {
			$this->paginateafter = $this->paginateafter;
		}

		/* GENERATE URLS - URLS ARE THE HREF VALUE */
		/**
		 * Returns URL of the panel's state
		 * @return string URL
		 */
		public function generate_refreshurl() {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->useractions;
			return $url->getUrl();
		}
		
		
		public function generate_loadurl() { 
			$url = new \Purl\Url($this->pageurl);
			$url->query->remove('filter');
			foreach (array_keys($this->filterable) as $filtercolumns) {
				$url->query->remove($filtercolumns);
			}
			return $url->getUrl();
		}
		
		public function generate_clearfilterlink() {
			$bootstrap = new Contento();
			$href = $this->generate_loadurl();
			$icon = $bootstrap->createicon('fa fa-times');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->openandclose('a', "href=$href|class=load-link btn btn-sm btn-warning btn-block|$ajaxdata", "Clear Filter $icon");
		}
		
		

		/**
		 * Returns URL to load add new action[type=$this->actiontype] form
		 * @return string                 URL to load add new action[type=$this->actiontype] form
		 */
		public function generate_addactionurl() {
			if (DplusWire::wire('config')->cptechcustomer == 'stempf') {
				$actiontype = ($this->actiontype == 'all') ? 'task' : $this->actiontype;
			} else {
				$actiontype = '';
			}
			$url = new \Purl\Url($this->generate_refreshurl());
			$url->path = DplusWire::wire('config')->pages->useractions."add/";
			$url->query->set('type', $actiontype);
			return $url->getUrl();
		}

		/**
		 * Returns URL to go to a month by adding to $date's month
		 * @param  string  $date String formatted date
		 * @param  int     $add  Number of months to add, can be negative
		 * @return string        URL to view calendar at month that is $add months from $date
		 */
		public function generate_addmonthurl($date, $add = 1) {
			$date = $date ? new DateTime($date) : new DateTime();
			$modify = $add > 0 ? "+$add" : "$add";
			$date->modify("$modify month");
			$url = new Purl\Url($this->pageurl->getUrl());
			$url->query->set('month', $date->format('M-Y'));
			return $url->getUrl();
		}
		
		public function set_view($view) {
			$this->view = !empty($view) ? $view : $this->view;
		}

		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		/**
		 * Returns the URL to view the actions in a calendar view
		 * @return string URL to calendar view
		 */
		public function generate_calendarviewurl() {
			$url = new Purl\Url($this->generate_refreshurl());
			$url->query->set('view', 'calendar');
			if ($url->query->get('day')) {
				$monthyear = date('M-Y', strtotime($url->query->get('day')));
				$url->query->set('month', $monthyear);
				$url->query->remove('day');
			}
			return $url->getUrl();
		}

		/**
		 * Returns the URL to view the actions in a day view
		 * @param  string $date Datetime string usually in (m/d/Y) format
		 * @return string       URL to day view
		 */
		public function generate_dayviewurl($date) {
			$date = $date ? date('m/d/Y', strtotime($date)) : date('m/d/Y');
			$url = new Purl\Url($this->generate_refreshurl());
			$url->query->set('view', 'day');
			$url->query->set('day', $date);
			return $url->getUrl();
		}

		public function generate_dayviewscheduledtasks($date) {
			$date = $date ? date('m/d/Y', strtotime($date)) : date('m/d/Y');
			$url = new Purl\Url($this->generate_dayviewurl($date));
			$url->query->set('actiontype', array('tasks'));
			$url->query->set('duedate', array($date));
			return $url->getUrl();
		}

		public function generate_daynotescreated($date) {
			$date = $date ? date('m/d/Y', strtotime($date)) : date('m/d/Y');
			$url = new Purl\Url($this->generate_dayviewurl($date));
			$url->query->set('actiontype', array('notes'));
			$url->query->set('datecreated', array($date));
			return $url->getUrl();
		}

		/**
		 * Returns the URL to view the panel in List View
		 * @return string URL to load List View
		 */
		public function generate_listviewurl() {
			$url = new Purl\Url($this->generate_refreshurl());
			$url->query->remove('day');
			$url->query->remove('month');
			$url->query->set('view', 'list');
			return $url->getUrl();
		}

		/* = GENERATE LINKS - LINKS ARE THE HTML MARKUP FOR LINKS */
		/**
		 * Returns HTML Link to refresh the panel
		 * @return string  HTML Link
		 */
		public function generate_refreshlink() {
			$bootstrap = new Contento();
			$href = $this->generate_refreshurl();
			$icon = $bootstrap->createicon('material-icons md-18', '&#xE86A;');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->openandclose('a', "href=$href|class=btn btn-info btn-xs load-link actions-refresh pull-right hidden-print|title=button|title=Refresh Actions|aria-label=Refresh Actions|$ajaxdata", $icon);
		}

		/**
		 * Returns a HTML Link to view the Printable version of this Page
		 * @return string HTML Link to Print Page
		 */
		public function generate_printlink() {
			$bootstrap = new Contento();
			$href = $this->generate_refreshurl();
			$icon = $bootstrap->createicon('glyphicon glyphicon-print');
			return $bootstrap->openandclose('a', "href=$href|class=h3|target=_blank", $icon." View Printable");
		}


		public function generate_addlink() {
			if (get_class($this) == 'UserActionsPanel') return '';
			$bootstrap = new Contento();
			$href = $this->generate_addactionurl();
			$icon = $bootstrap->createicon('material-icons md-18', '&#xE146;');
			if (DplusWire::wire('config')->cptechcustomer == 'stempf') {
				return $bootstrap->openandclose('a', "href=$href|class=btn btn-info btn-xs load-into-modal pull-right hidden-print|data-modal=$this->modal|role=button|title=Add Action", $icon);
			}
			return $bootstrap->openandclose('a', "href=$href|class=btn btn-info btn-xs add-action pull-right hidden-print|data-modal=$this->modal|role=button|title=Add Action", $icon);
		}

		public function generate_removeassigneduserIDlink() {
			$bootstrap = new Contento();
			$href = $this->generate_removeassigneduserIDurl();
			$icon = $bootstrap->createicon('fa fa-user-times');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->openandclose('a', "href=$href|class=btn btn-warning btn-xs load-link pull-right hidden-print|title=button|title=Return to Your Actions|aria-label=Return to Your Actions|$ajaxdata", $icon.' Remove User lookup');
		}

		/* CONTENT FUNCTIONS  */
		public function generate_rowclass($action) {
			if ($action->actiontype == 'tasks') {
				if ($action->is_rescheduled()) {
					return 'bg-info';
				}
				if ($action->is_overdue()) {
					return 'bg-warning';
				}
				if ($action->is_completed()) {
					return 'bg-success';
				}
			}
			return '';
		}

		public function generate_legend() {
			$bootstrap = new Contento();
			$tb = new Table('class=table table-bordered table-condensed table-striped');
			$tb->tr('class=bg-warning')->td('', 'Task Overdue');
			$tb->tr('class=bg-info')->td('', 'Task Rescheduled');
			$tb->tr('class=bg-success')->td('', 'Task Completed');
			$content = str_replace('"', "'", $tb->close());
			$attr = "tabindex=0|role=button|class=btn btn-sm btn-info|data-toggle=popover|data-placement=bottom|data-trigger=focus";
			$attr .= "|data-html=true|title=Icons Definition|data-content=$content";
			return $bootstrap->openandclose('a', $attr, 'Icon Definitions');
		}

		public function generate_completetasklink(UserAction $task) {
			$bootstrap = new Contento();
			$href = $this->generate_viewactionjsonurl($task);
			$icon = $bootstrap->createicon('fa fa-check-circle');
			$icon .= ' <span class="sr-only">Mark as Complete</span>';
			return $bootstrap->openandclose('a', "href=$href|role=button|class=btn btn-xs btn-primary complete-action|title=Mark Task as Complete", $icon);
		}

		/**
		* Generates title for Panel
		* Will be overwritten by children
		* @return string
		*/
		public function generate_title() {
			return 'Your Actions';
		}

		/**
		* Returns if the panel should have the add link
		* Will be overwritten by children
		* @return bool
		*/
		public function should_haveaddlink() {
			return false;
		}

		/* =============================================================
			CLASS DATABASE FUNCTIONS
		============================================================ */
		/**
		 * Returns the number of UserActions that meet the $this->filter
		 * @param  bool   $debug Wether to return number or SQL Query
		 * @return int        number of UserActions | SQL Query
		 */
		public function count_actions($debug = false) {
			return $debug ? count_actions($this->filters, $this->filterable, $debug) : $this->count = count_actions($this->filters, $this->filterable, $debug);
		}

		public function get_actions($debug = false) {
			return get_actions($this->filters, $this->filterable, DplusWire::wire('session')->display, $this->pagenbr, $debug);
		}

		public function count_dayallactions($day, $debug = false) {
			return count_dayallactions($day, $this->filters, $this->filterable, $debug);
		}

		public function get_dayallactions($day, $debug = false) {
			return get_dayallactions($day, $this->filters, $this->filterable, $debug);
		}

		public function count_daytasks($day, $debug = false) {
			$filters = $this->filters;
			$filters['actiontype'] = array('tasks');
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns UserActions [type=tasks] that meet the $this->filter criteria
		 * @param  string $day   Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return array of UserActions or SQL Query
		 * @return array         array of UserActions | SQL Query
		 */
		public function get_daytasks($day, $debug = false) {
			$filters = $this->filters;
			$filters['actiontype'] = array('tasks');
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns of the number of UserActions[type=tasks] scheduled for that day
		 * @param  string  $day  Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return Number or SQL Query
		 * @return int           Number of Tasks scheduled for that day | SQL Query
		 */
		public function count_dayscheduledtasks($day, $debug = false) {
			$filters = $this->filters;
			unset($filters['completed']);
			$filters['duedate'] = array($day);
			$filters['actiontype'] = array('tasks');
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns of the number of UserActions[type=tasks] rescheduled that day
		 * @param  string  $day  Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return Number or SQL Query
		 * @return int           Number of Tasks rescheduled that day | SQL Query
		 */
		public function count_dayrescheduledtasks($day, $debug = false) {
			$filters = $this->filters;
			$filters['actiontype'] = array('tasks');
			$filters['completed'] = array('R');
			$filters['dateupdated'] = array($day);
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns of the number of UserActions[type=tasks] completed that day
		 * @param  string  $day  Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return Number or SQL Query
		 * @return int           Number of Tasks Completed that day | SQL Query
		 */
		public function count_daycompletedtasks($day, $debug = false) {
			$filters = $this->filters;
			$filters['actiontype'] = array('tasks');
			$filters['completed'] = array('Y');
			$filters['datecompleted'] = array($day);
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns the number of UserActions[type=notes] made today
		 * @param  string  $day   Date time string usually formatted (m/d/Y)
		 * @param  bool    $debug Whether to return Number or SQL Query
		 * @return int            Number of Notes made that day | SQL Query
		 */
		public function count_daynotes($day, $debug = false) {
			$filters = $this->filters;
			unset($filters['completed']);
			$filters['actiontype'] = array('notes');
			$filters['datecreated'] = array($day);
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns UserActions[type=notes] made that day
		 * @param  string $day   Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return array of Notes or SQL Query
		 * @return array         array of Notes | SQL Query
		 */
		public function get_daynotes($day, $debug = false) {
			$filters = $this->filters;
			unset($filters['completed']);
			$filters['actiontype'] = array('notes');
			$filters['datecreated'] = array($day);
			return get_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns Page Number the List is on along with the preceding string Page
		 * @return string Page $this->pagenbr | (blank)
		 */
		public function generate_pagenumberdescription() {
			return ($this->pagenbr > 1) ? "Page $this->pagenbr" : '';
		}

		public function generate_filter(ProcessWire\WireInput $input) {
			$this->generate_defaultfilter($input);

			if (!isset($this->filters['completed'])) {
				$this->filters['completed'] = array('');
			}

			if (!isset($this->filters['assignedto'])) {
				$this->filters['assignedto'] = array(DplusWire::wire('user')->loginid);
			}

			if (isset($this->filters['datecreated'])) {
				if (empty($this->filters['datecreated'][1])) {
					$this->filters['datecreated'][1] = $this->filters['datecreated'][0];
				}

				if (empty($this->filters['datecreated'][0])) {
					unset($this->filters['datecreated']);
				}
			}

			if (isset($this->filters['datecompleted'])) {
				$this->filters['completed'] = array('Y');
				if (empty($this->filters['datecompleted'][1])) {
					$this->filters['datecompleted'][1] = $this->filters['datecreated'][0];
				}
			}

			if (isset($this->filters['actiontype'])) {
				if (sizeof($this->filters['actiontype']) > 1) {
					$this->actiontype = 'all';
				} else {
					$this->actiontype = $this->filters['actiontype'][0];
				}
			} else {
				$this->actiontype = 'all';
			}
		}

		public function generate_calendar($month, $year) {
			$bootstrap = new Contento();

			$dateComponents = getdate();
			// Create array containing abbreviations of days of week.
			$weekdays = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

			// What is the first day of the month in question?
			$firstday = mktime(0, 0, 0,$month, 1, $year);

			// How many days does this month contain?
			$monthdaycount = date('t',$firstday);

			// Retrieve some information about the first day of the
			// month in question.
			$dateComponents = getdate($firstday);

			// What is the index value (0-6) of the first day of the
			// month in question.
			$weekdayindex = $dateComponents['wday'];

			// Create the table tag opener and day headers
			$tb = new Table('class=calendar table table-condensed table-bordered');

			// Create the calendar headers
			$tb->tablesection('thead');
			$tb->tr();
			foreach ($weekdays as $day) {
				$tb->th('class=header text-center|width=14.3%', $day);
			}
			$tb->closetablesection('thead');

			$tb->tablesection('tbody');
			// Create the rest of the calendar
			// Initiate the day counter, starting with the 1st.
			$currentday = 1;

			// The variable $weekdayindex is used to
			// ensure that the calendar
			// display consists of exactly 7 columns.

			if ($weekdayindex > 0) {
				$tb->td("colspan=$weekdayindex", '&nbsp;');
			}

			$month = str_pad($month, 2, "0", STR_PAD_LEFT);

			while ($currentday <= $monthdaycount) {
				// Seventh column (Saturday) reached. Start a new row.
				if ($weekdayindex == 7) {
					$weekdayindex = 0;
					$tb->tr();
				}

				$currentdaypadded = str_pad($currentday, 2, "0", STR_PAD_LEFT);

				$date = "$month/$currentdaypadded/$year";

				$content = $bootstrap->div('class=day-number text-right', $currentday);
				$class = 'day';
				$listitems = '';

				if ($date == date('m/d/Y')) {
					$class = 'day active';
				}

				if ($this->count_daynotes($date)) {
					$listitems .= $bootstrap->li('role=presentation', 'Notes '.$bootstrap->span('class=badge pull-right', $this->count_daynotes($date)).'<br>');
				}

				if ($this->count_dayscheduledtasks($date)) {
					$listitems .= $bootstrap->li('role=presentation', 'Tasks '.$bootstrap->span('class=badge pull-right', $this->count_dayscheduledtasks($date)).'<br>');
				}

				if ($this->count_dayrescheduledtasks($date)) {
					$listitems .= $bootstrap->li('role=presentation', 'Tasks Rescheduled'.$bootstrap->span('class=badge bg-info pull-right', $this->count_dayrescheduledtasks($date)).'<br>');
				}

				$list = $bootstrap->ul('class=list-unstyled', $listitems);

				$content .= $bootstrap->div('class=day-list', $list);

				if ($this->count_dayallactions($date) || $this->count_dayrescheduledtasks($date) || $this->count_dayscheduledtasks($date)) {
					$href = $this->generate_dayviewurl($date);
					$ajaxdata = $this->generate_ajaxdataforcontento();
					$content .= $bootstrap->div('class=action-bar', $bootstrap->a("href=$href|class=btn btn-xs btn-block btn-primary load-link|$ajaxdata", 'View Actions'));
				}

				$tb->td("class=$class", $content);
				// Increment counters
				$currentday++;
				$weekdayindex++;
			}

			// Complete the row of the last week in month, if necessary
			if ($weekdayindex != 7) {
				$daysremaining = 7 - $weekdayindex;
				$tb->td("colspan=$daysremaining", '&nbsp;');
			}
			$tb->closetablesection('tbody');
			return $bootstrap->div('class=table-responsive', $tb->close());
		}
	}
