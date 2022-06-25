<?php

// $Id: phpMyEdit.class.php,v 1.12 2002/09/29 17:11:30 nepto Exp $

/*	phpMyEdit intro {{{ */
/*
	This is a generic table editing program. The table and fields to be
	edited are defined in the calling program.

	This program works in three passes. Pass 1 (the last part of
	the program) displays the selected MySQL table in a scrolling table
	on the screen. Radio buttons are used to select a record for editing
	or deletion. If the user chooses Add, Change, or Delete buttons,
	Pass 2 starts, displaying the selected record. If the user chooses
	the Save button from this screen, Pass 3 processes the update and
	the display returns to the original table view (Pass 1).

	version 3.5 - 06-May-01
	
	important variables passed between calls to this program
	
	$fm     first record to display
	$inc    no of records to display (SELECT ... LIMIT $fm,$inc)
	$fl     is the filter row displayed (boolean)
	$rec    unique id of record selected for editing
	$qf0,.. value of filter for column 0
	$qfn    value of all filters used during the last pass
	$sfn    sort field number (- = descending sort order)
	$operation	operation to do: Add, Change, Delete
	$message	informational message to print
	$filter filter query
	$sw     filter display/hide button

	$prev, $next  navigation buttons
	$labels narrative for buttons, etc

	Conversion to PHP Classes by Pau Aliagas (pau@newtral.com)

	ToDo:
	'Copy' button 

	Aggregates:
	nonworking code commented out in list_table()
	doesn't work yet

	Query Building:

	Multi-Part Date Handling:
	Finish converting date handling to internal date handling functions
	Abstract date field gathering to get rid of _many_ redundant lines of code
	There was some kludged fix for dateformat'ting where '%'s are removed
	Better support for more date format macros
	Better documentation for valid date format macros

	Multi-Language support:
	Finish implementing language labels
	Use browser-supplied language if available
	Allow programmer override in setup.php generated .inc file
	Add 'Search' and 'Go!' to labels array

	Data Validation:
	Expand JS field validation to match JS regexes
	Create PHP field validation to match PHP regexes

	Change Tracking/Notification:
	Add change notification (via mail()) support
		Don't die if mail() not available

	CSS:
	Document & solicit feedback to standardize class names

	Even/Odd Coloring:
	Move to CSS
	Put values in setup.php generated file

	Timer Class:
	Solicit user input whether to put timer class into this lib
*/
/* }}} */

if (@require_once dirname(__FILE__).'/timer.class') {
	$timer = new timerClass();
}

function debug_var($name,$val)
{
	if (is_array($val)) {
		echo "<pre>$name\n";
		print_r($val);
		echo "</pre>\n";
	} else {
		echo "$name::$val::<br>\n";
	}
}

if (! function_exists('array_search')) { /* {{{ */
	function array_search($needle, $haystack)
	{
		foreach ($haystack as $key => $value) {
			if ($needle == $value)
				return $key;
		}
		return false;
	}
} /* }}} */

class phpMyEdit
{
	var $hn;        // hostname
	var $un;        // user name
	var $pw;        // password
	var $db;        // database
	var $tb;        // table

	var $key;       // Name of field which is the unique key
	var $key_type;  // Type of key field (int/real/string/date etc)
	var $key_delim;

	var $inc;       // no of records to display (SELECT ... LIMIT $fm, $inc)
	var $fm;        // first record to display
	var $fl;        // is the filter row displayed (boolean)

	var $options;   // Options for users: A(dd) C(hange) D(elete) F(ilter) V(iew) co(P)y U(nsorted)
	var $fdd;       // field definitions
	var $qfn;       // value of all filters used during the last pass
	var $sfn;       // sort field number (- = descending sort order)

	var $rec;       // no. of record selected for editing
	var $prev;      // navigation buttons
	var $next;
	var $sw;        // filter display/hide button
	var $labels;    // labels for buttons, etc (multilingual)
	var $operation; // operation to do: Add, Change, Delete
	var $message;   // informational message to print

	var $saveadd;
	var $moreeadd;
	var $savechange;
	var $savedelete;

	var $fds;       // sql field names
	var $num_fds;   // number of fields

	var $logtable;   // name of optional logtable
	var $navigation; // navigation style

	function myquery($qry, $line = 0) /* {{{ */
	{
		global $debug_query;
		if ($debug_query) {
			$line = intval($line);
			echo '<h4>qry at '.$line.': '.htmlspecialchars($qry).'</h4><hr>'."\n";
		}
		$this->elog("qry: $qry",$line);
		$ret = @mysql_db_query($this->db,$qry);
		if (! $ret) {
			$this->elog(mysql_errno().": ".mysql_error().' in '.$qry,__LINE__);
		}
		return $ret;
	} /* }}} */

	function htmlDisplay($field,$str,$usemask=true,$usecodec=true) /* {{{ */
	{
		// undo the add slashes
		$str = stripslashes($str);

		// if there's a field mask, use it as first arg to sprintf
		if (isset($field['mask']) && $usemask)
			$str = sprintf($field['mask'],$str);

		if ($usecodec) {
			// if db codec is in effect, use it
			if (isset($field['dbdecode'])) {
				$str = htmlspecialchars(eval('return '.$field['dbdecode'].'(\''.$str.'\');'));
			} else {
				$str = htmlspecialchars($str);
			}
		}

		return $str;
	} /* }}} */

	function encode($field,$str) /* {{{ */
	{
		if (isset($field['dbencode'])) {
			return eval(
					'return '
					.$field['dbencode']
					.'(\''.$str.'\');');
		} else {
			return $str;
		}
	} /* }}} */

	function elog($str,$line) /* {{{ */
	{
		error_log(__FILE__.":$line::\n$str",0);
		return true;
	} /* }}} */

	function make_language_labels($language) /* {{{ */
	{
		// just try the first language and variant
		// this isn't content-negotiation rfc compliant
		$language = strtoupper(substr($language,0,5));

		// try the full language w/ variant
		$ret = @include($this->dir['lang'].'PME.lang.'.$language.'.inc');
		if (! $ret) {
			// try the language w/o variant
			$ret = @include($this->dir['lang'].'PME.lang.'.substr($language,0,2).'.inc');
		}
		if (! $ret) {
			// default to English-U.S.
			$ret = @include($this->dir['lang'].'PME.lang.EN-US.inc');
		}
		if (!isset($ret['Search'])) $ret['Search'] = 'v';
		if (!isset($ret['Hide']))   $ret['Hide']   = '^';
		if (!isset($ret['Go']))     $ret['Go']     = htmlspecialchars('>');
		if (!isset($ret['of']))     $ret['of']     = '/';
		return $ret;
	} /* }}} */

	function set_values_from_table($field_num, $prepend = '') /* {{{ */
	{
		//echo $field_num;
		//echo '<pre>';
		//var_dump($this->fdd);
		//echo '</pre>';
		if($this->fdd[$field_num]['values']['db']) {
			$db = $this->fdd[$field_num]['values']['db'];
		} else {
			$db = $this->db;
		}
		$table = $this->fdd[$field_num]['values']['table'];
		$key   = $this->fdd[$field_num]['values']['column'];
		$desc  = $this->fdd[$field_num]['values']['description'];
		$qparts['type']   = 'select';
		$qparts['select'] = 'DISTINCT '.$key;
		if ($desc) {
			//- $qparts['select'] .= ','.$desc;
			//- $qparts['orderby'] = $desc;
			//	Changes 08/08/02 Shaun Johnston
			if (is_array($desc)) {
				$qparts['select'] .= ',CONCAT('; // )
				$num_cols = sizeof($desc['columns']);
				for ($i = 0; $i <= $num_cols; $i++) {
					$qparts['select'] .= $desc['columns'][$i];
					if ($desc['divs'][$i]) {
						$qparts['select'] .= ',"'.$desc['divs'][$i].'"';
					}
					if ($i < ($num_cols - 1)) {
						$qparts['select'] .= ',';
					}
				}
				$qparts['select'] .= ') AS select_alias_'.$field_num;
				$qparts['orderby'] = $desc['orderby'];
			} else {
				$qparts['select'] .= ','.$desc;
				$qparts['orderby'] = $desc;
			}
		} else {
			$qparts['orderby'] = $key;
		}
		//$qparts['from'] = "$db.$table.$sel;
		$qparts['from'] = "$db.$table";
		$qparts['where'] = $this->fdd[$field_num]['values']['filters'];
		if ($this->fdd[$field_num]['values']['orderby']) {
			$qparts['orderby'] = $this->fdd[$field_num]['values']['orderby'];
		}
		$res = $this->myquery($this->query_make($qparts),__LINE__);
		$values = array();
		if ($prepend != '') {
			$values[$prepend[0]] = $prepend[1];
		}
		while ($row = mysql_fetch_row($res)) {
			if ($desc) {
				$values[$row[0]] = $row[1];
			} else {
				$values[$row[0]] = $row[0];
			}
		}
		return $values;
	} /* }}} */

	/*
	 * get the table/field name
	 */
	function fqn($field, $use_qfx=false) /* {{{ */
	{
		if (is_string($field)) {
			$field = array_search($field,$this->fds);
		}

		// on copy/change always use simple key retrieving
		if ($this->add_operation()
				|| $this->copy_operation()
				|| $this->change_operation()) {
				$ret = 'Table0.'.$this->fds[$field];
		} else {
			if (isset($this->fdd[$field]['expression'])) {
				$ret = $this->fdd[$field]['expression'];
			} elseif ($this->fdd[$this->fds[$field]]['values']['description']) {
				//	Changed 06/08/02 Shaun Johnston
				$desc = $this->fdd[$this->fds[$field]]['values']['description'];
				if (is_array($desc)) {
					$ret = 'CONCAT('; // )
					$num_cols = sizeof($desc['columns']);
					for ($i = 0; $i < $num_cols; $i++) {
						$ret .= 'JoinTable'.$field.'.'.$desc['columns'][$i];
						if ($desc['divs'][$i]) {
							$ret .= ',"'.$desc['divs'][$i].'"';
						}
						if ($i < ($num_cols - 1)) {
							$ret .= ',';
						}
					}
					$ret .= ')';
				} else {
					$ret = 'JoinTable'.$field.'.'.$this->fdd[$this->fds[$field]]['values']['description'];
				}
			} elseif ($this->fdd[$this->fds[$field]]['values']['column']) {
				$ret = 'JoinTable'.$field.'.'.$this->fdd[$this->fds[$field]]['values']['column'];
			} else {
				$ret = 'Table0.'.$this->fds[$field];
			}
		}

		// what to do with $format XXX
		if ($use_qfx)
			$ret = 'qf'.$field;
		// return the value
		return $ret;
	} /* }}} */

	function create_column_list() /* {{{ */
	{
		$fields = array();
		for ($k = 0; $k < $this->num_fds; $k++) {
			if ($this->col_is_date($k)) {
				//$fields[] = 'DATE_FORMAT('.$this->fqn($k).',"%Y%m%d%H%i%s") AS qf'.$k;
				$fields[] = $this->fqn($k).' AS qf'.$k;
			} else {
				$fields[] = $this->fqn($k).' AS qf'.$k;
				//echo '[['.$this->fqn($k).' AS qf'.$k.']]<br>';
			}
		}
		return join(',',$fields);
	} /* }}} */

	function query_make($parts) /* {{{ */
	{
		foreach ($parts as $k => $v) {
			$parts[$k] = trim($parts[$k]);
		}
		
		switch ($parts['type']) {
			case 'select':
				$ret  = 'SELECT ';
				if ($parts['DISTINCT'])
					$ret .= 'DISTINCT ';
				$ret .= $parts['select'];
				$ret .= ' FROM '.$parts['from'];
				if ($parts['where'] != '')
					$ret .= ' WHERE '.$parts['where'];
				if ($parts['groupby'] != '')
					$ret .= ' GROUP BY '.$parts['groupby'];
				if ($parts['having'] != '')
					$ret .= ' HAVING '.$parts['having'];
				if ($parts['orderby'] != '')
					$ret .= ' ORDER BY '.$parts['orderby'];
				if ($parts['limit'] != '')
					$ret .= ' LIMIT '.$parts['limit'];
				if ($parts['procedure'] != '')
					$ret .= ' PROCEDURE '.$parts['procedure'];
				break;
			case 'update':
				$ret  = 'UPDATE '.$parts['table'];
				$ret .= ' SET '.$parts['fields'];
				if ($parts['where'] != '')
					$ret .= ' WHERE '.$parts['where'];
				break;
			case 'insert':
				$ret  = 'INSERT INTO '.$parts['table'];
				$ret .= ' VALUES '.$parts['values'];
				break;
			case 'delete':
				$ret  = 'DELETE FROM '.$parts['table'];
				if ($parts['where'] != '')
					$ret .= ' WHERE '.$parts['where'];
				break;
			default:
				die('unknown query type');
				break;
		}
		return $ret;
	} /* }}} */

	function create_join_clause() /* {{{ */
	{
		$tbs[] = $this->tb;
		$join = $this->tb.' AS Table0';
		for ($k = 0,$numfds = sizeof($this->fds); $k<$numfds; $k++) {
			$field = $this->fds[$k];
			if($this->fdd[$field]['values']['db']) {
				$db = $this->fdd[$field]['values']['db'];
			} else {
				$db = $this->db;
			}
			$table = $this->fdd[$field]['values']['table'];
			$id    = $this->fdd[$field]['values']['column'];
			$desc  = $this->fdd[$field]['values']['description'];

			if ($desc != '' || $id != '') {
				$alias = 'JoinTable'.$k;
				if (!in_array($alias,$tbs)) {
					$join .= 
						" LEFT OUTER JOIN $db.".
						$table.
						' AS '.$alias.
						' ON '.$alias.
						'.'.$id.
						'='.'Table0.'.$field;
					$tbs[]=$alias;
				}
			}
		}
		return $join;
	} /* }}} */

	function make_where_from_query_opts($qp='') /* {{{ */
	{
		if ($qp == '')
			$qp = $this->query_opts;
		$where = array();
		foreach ($qp as $field => $ov) {
			$where[] = sprintf('%s %s %s',$field,$ov['oper'],$ov['value']);
		}

		// Add any coder specified filters
		if ($this->filters)
			$where[] = '('.$this->filters.')';
		if (count($where) > 0)
			return join(' AND ',$where);

		return false;
	} /* }}} */

	function make_text_where_from_query_opts($qp='') /* {{{ */
	{
		if ($qp == '')
			$qp = $this->query_opts;
		$where = array();
		foreach ($qp as $field => $ov) {
			$where[] = sprintf('%s %s %s',$field,$ov['oper'],$ov['value']);
		}

		if (count($where) > 0)
			return str_replace('%','*',join(' AND ',$where));

		return false;
	} /* }}} */

	/*
	 * get_cgi_var()
	 */
	function get_cgi_var($name, $default_value = null)
	{
		global $HTTP_GET_VARS;
		$var = $HTTP_GET_VARS[$name];

		if (! isset($var)) {
			global $HTTP_POST_VARS;
			$var = $HTTP_POST_VARS[$name];
		}

		if (isset($var)) {
			$var = stripslashes($var);
		} else {
			$var = $default_value;
		}

		return $var;
	}

	/*
	 * functions for get/post/query args
	 */

	function gather_post_vars() /* {{{ */
	{
		global $HTTP_POST_VARS;
		foreach ($HTTP_POST_VARS as $key => $val) {
			if ($val != '' && $val != '*') {
				$pv[$key] = $val;
			}
		}
		$this->pv = $pv;
	} /* }}} */

	function gather_query_opts() /* {{{ */
	{
		// gathers query options into an array, $this->query_opts

		$query_opts = array();
		$qo = array();

		for ($k = 0; $k < $this->num_fds; $k++) {
			$l    = 'qf'.$k;
			$lc   = 'qf'.$k.'_comp';
			$$l   = $this->get_cgi_var($l);
			$$lc  = $this->get_cgi_var($lc);
			$m    = $this->web2plain($$l);  // get the field name and value
			$mc   = $this->web2plain($$lc); // get the comparison operator for numeric/date types
			$type = $this->fdd[$k]['type'];

			if ($m != '') {
				if (is_array($m)) { // multiple selection has been used
					if (!in_array('*',$m))	{ // one '*' in a multiple selection is all you need
						for ($n=0; $n<count($m); $n++) {
							if ($n==0) {
								$qf_op = 'IN';
								$qf_val = "'".addslashes($m[$n])."'";
						 		$afilter=" IN ('".addslashes($m[$n])."'";
							} else {
								$afilter=$afilter.",'".addslashes($m[$n])."'";
								$qf_val .= ",'".addslashes($m[$n])."'";
							}
						}
						$afilter = $afilter.')';
						$qo[$this->fqn($k)] =
							array( 'oper'  => $qf_op, 'value' => '('.$qf_val.')');
					}
				} else {
					$afilter = $m;
					if ($afilter != '*') {
						if ($this->fdd[$k]['values']['description']) {
							$qo[$this->fqn($k)] =
								array( 'oper'  => '=', 'value' => "'".$afilter."'");
						} elseif ($this->fdd[$k]['values']['column']) {
							$qo[$this->fqn($k)] =
								array( 'oper'  => '=', 'value' => "'".$afilter."'");
						} elseif ($this->col_is_string($k)) {
							// massage the filter for a string comparison
							if (($afilter != '') AND ($afilter != '*')) {
								$afilter = addslashes(addslashes('%'
											.str_replace ('*', '%', $afilter).'%'));
								$qo[$this->fqn($k)] =
									array('oper'  => 'like', 'value' => "'".$afilter."'");
							}
						} elseif ($this->col_is_number($k) && ($$lc != '')) {
							if ($$lc != '') {
								$qo[$this->fqn($k)] =
									array('oper'  => $mc, 'value' => $afilter);
							}
						} elseif ($this->col_is_date($k)) {
							#if ($$lc != '') {
							#	$val = $this->gather_date_fields_into_type($$l,$type);
							#	$val = $this->mdate_set(date($this->mdate_masks[$type],$this->mdate_getFromPost($k)),$type); 
							#	$val = $this->mdate_getFromPost($k); 
							#	if ($val != '') {
							#		$qo[$this->fqn($k)] =
							#			array( 'oper'  => $mc, 'value' => '"'.$val.'"');
							#	}
							#}
							# massage the filter for a string comparison
							if (($afilter != '') AND ($afilter != '*')) {
								$afilter = addslashes(addslashes('%'
										.str_replace ('*', '%', $afilter).'%'));
								$qo[$this->fqn($k)] =
									array('oper'  => 'like', 'value' => "'".$afilter."'");
							}
						} elseif($this->fdd[$k]['values']) {
//debug_var('col_is_string',$this->fdd[$k]['name'].'::'.$this->fdd[$k]['type']);
							$qo[$this->fqn($k)] =
								array( 'oper'  => '=', 'value' => "'".$afilter."'");
						} else {
							// unknown (to mysql/php interface) field type massage the filter for a string comparison
							$afilter = addslashes(addslashes('%'.str_replace ('*', '%', $afilter).'%'));
							$qo[$this->fqn($k)] =
								array('oper'  => 'like', 'value' => "'".$afilter."'");
						}
					}
				}
			} // if
		} // for

		$this->query_opts = $qo;
	} // gather_query_opts  /* }}} */

	function gather_get_vars() /* {{{ */
	{
		global $HTTP_SERVER_VARS;
		$vals = array();
		$parts = split('&',$HTTP_SERVER_VARS['QUERY_STRING']);
		if (count($parts) > 0) {
			foreach ($parts as $part) {
				list($key,$val) = split('=',$part,2);
				$vals[$key] = $val;
			}
		}
		$this->get_opts = $vals;
	} /* }}} */

	function unify_opts() /* {{{ */
	{
		$all_opts = array();
		if (count($this->qo) > 0) {
			foreach ($this->qo as $key=>$val)
				$all_opts[$key] = $val;
		}
		if (count($this->pv) > 0) {
			foreach ($this->pv as $key=>$val)
				$all_opts[$key] = $val;
		}
		if (count($this->get_opts) > 0) {
			foreach ($this->get_opts as $key=>$val)
				$all_opts[$key] = $val;
		}
		$this->all_opts = $all_opts;
	} /* }}} */

	/*
	 * type functions
	 */

	function col_is_date($k)   { return in_array($this->fdd[$k]['type'], $this->dateTypes  ); }
	function col_is_number($k) { return in_array($this->fdd[$k]['type'], $this->numberTypes); }
	function col_is_string($k) { return in_array($this->fdd[$k]['type'], $this->stringTypes); }
	function col_is_set($k)    { return $this->fdd[$k]['type'] == 'set'; }

	/*
	 * functions for indicating whether navigation style is enabled
     */

	function nav_buttons()       { return stristr($this->navigation, 'B'); }
	function nav_text_links()    { return stristr($this->navigation, 'T'); }
	function nav_graphic_links() { return stristr($this->navigation, 'G'); }
	function nav_up()            { return stristr($this->navigation, 'U'); }
	function nav_down()          { return stristr($this->navigation, 'D'); }

	/*
	 * functions for indicating whether operations are enabled
	 */

	function initial_sort_suppressed() { return (stristr ($this->options, 'I')); }
	function add_enabled()    { return stristr($this->options, 'A'); }
	function change_enabled() { return stristr($this->options, 'C'); }
	function delete_enabled() { return stristr($this->options, 'D'); }
	function filter_enabled() { return stristr($this->options, 'F'); }
	function view_enabled()   { return stristr($this->options, 'V'); }
	function copy_enabled()   { return stristr($this->options, 'P') && $this->add_enabled(); }
	function hidden($k)       { return stristr($this->fdd[$k]['options'],'H'); }
	function password($k)     { return stristr($this->fdd[$k]['options'],'P'); }
	function readonly($k)     { return stristr($this->fdd[$k]['options'],'R')
		|| $this->fdd[$k]['expression']; }

	function add_operation() {
		return ( $this->operation == $this->labels['Add']
				/* or $this->saveadd == $this->labels['Save'] */)
			and $this->add_enabled();
	}

	function more_operation() {
		return (0/* $this->moreadd == $this->labels['More'] */)
			and $this->add_enabled();
	}

#	function display_operation() {
#		return ($this->operation  == $this->labels['Delete']
#				/* or $this->savedelete == $this->labels['Save'] */)
#			and $this->delete_enabled();
#	}

	function change_operation() {
		return ($this->operation  == $this->labels['Change']
				/* or $this->savechange == $this->labels['Save'] */)
			and $this->change_enabled();
	}

	function copy_operation() {
		return ($this->operation  == $this->labels['Copy']
				/* or $this->savechange == $this->labels['Save'] */)
			and $this->add_enabled();
	}

	function delete_operation() {
		return ($this->operation  == $this->labels['Delete']
				/* or $this->savedelete == $this->labels['Save'] */)
			and $this->delete_enabled();
	}

	function view_operation() {
		return $this->operation  == $this->labels['View']
			and $this->view_enabled();
	}

	function filter_operation() {
		return isset($this->filter) and $this->filter_enabled();
	}

	function displayed($k) /* {{{ */
	{
		if (is_numeric($k)) {
			$k = $this->fds[$k];
		}
		//echo $k.': '.$this->fdd[$k]['options'].'<hr>'; 

		return empty($this->fdd[$k]['options']) ||
			( ! $this->hidden($k) && (
			( $this->add_operation()     and stristr($this->fdd[$k]['options'],'A')) ||
			( $this->more_operation()    and stristr($this->fdd[$k]['options'],'A')) ||
			( $this->view_operation()    and stristr($this->fdd[$k]['options'],'V')) ||
			( $this->change_operation()  and stristr($this->fdd[$k]['options'],'C')) ||
			( $this->delete_operation()  and stristr($this->fdd[$k]['options'],'D')) ||
			( $this->filter_operation()  and stristr($this->fdd[$k]['options'],'F')) ||
			( stristr($this->fdd[$k]['options'],'L') and
			 ! $this->add_operation() &&
			 ! $this->more_operation() &&  
			 ! $this->view_operation() && 
			 ! $this->change_operation() &&
			 ! $this->delete_operation() &&
			 ! $this->filter_operation())
			)
		);
	} /* }}} */

	/*
	 * Create JavaScripts
	 */

	function create_javascripts() /* {{{ */
	{
		/*
			Need a lot of work in here
			using something like:
			$fdd['fieldname']['validate']['js_regex']='/something/';
			$fdd['fieldname']['validate']['php_regex']='something';
		*/

		if ($this->add_operation() or $this->change_operation() or $this->more_operation()) {
			echo '<script type="text/javascript">'."\n";
			echo '// <!--
function form_control(theForm)
{
	'; // echo
	for ($k = 0; $k < $this->num_fds; $k++) {
		if ($this->displayed($k) and $this->fdd[$k]['required']) {
			if (! isset ($this->fdd[$k]['values'])
					and isset ($this->fdd[$k]['regex']['js'])) {
						// use a javascript regex to validate it
						//
						// XXX not done yet
						//
						echo "
	if ( theForm.".$this->fds[$k].".value.length == 0 ) {
		alert( '".$this->labels['Please enter']." ".$this->fdd[$k]['name']." .' );
		theForm.".$this->fds[$k].".focus();
		return false;
	}
				"; // echo
					} elseif (!isset ($this->fdd[$k]['values'])) {
						// confirm that it's not empty
						echo "
	if ( theForm.".$this->fds[$k].".value.length == 0 ) {
		alert( '".$this->labels['Please enter']." ".$this->fdd[$k]['name']." .' );
		theForm.".$this->fds[$k].".focus();
		return false;
	}
				"; // echo
					}
				}
			}

			echo '
theForm.submit();
return true;
}
//-->
</script>' . "\n"; // echo

			echo '<form action="'.$this->page_name.'" method="POST" onSubmit="return form_control(this);">'."\n";
		} else {
			echo '<form action="'.$this->page_name.'" method="POST">'."\n";
		}
	} /* }}} */

	/*
	 * Display functions
	 */

	function display_add_record() /* {{{ */
	{
		echo '  <tr>'."\n";
		echo '    <th>Field</th>'."\n";
		echo '    <th>Value</th>'."\n";
		if ($this->guidance)
			echo '    <th>Guidance</th>'."\n";
		echo '  </tr>'."\n";
		for ($k = 0; $k < $this->num_fds; $k++) {
			echo '  <tr>'."\n";
			if ( $this->displayed($k) ) {
				echo '    <td>'.$this->fdd[$k]['name'].'</td>'."\n";

				if ($this->fdd[$k]['select'] == 'M')
					{ $a = ' multiple size="'.$this->multiple.'"'; } else { $a=''; }
				if (isset ($this->fdd[$k]['values'])) {
					echo '    <td>' ."\n";
					if (isset($this->fdd[$k]['values']['table'])) {
						$vals = /* array(''=>'') + */ $this->set_values_from_table($k);
					} else {
						$vals = /* array(''=>'') + */ $this->fdd[$k]['values'];
					}
					echo $this->htmlSelect($this->fds[$k], $vals, '', $this->col_is_set($k));
					echo '    </td>'."\n";
				} elseif (isset ($this->fdd[$k]['textarea'])) {
					echo '    <td><textarea ';
					if (isset ($this->fdd[$k]['textarea']['rows'])) {
						echo 'rows="'.$this->fdd[$k]['textarea']['rows'].'" ';
					}
					if (isset ($this->fdd[$k]['textarea']['cols'])) {
						echo 'cols="'.$this->fdd[$k]['textarea']['cols'].'" ';
					}
					echo 'name="'.$this->fds[$k].'" wrap="virtual">';
					echo $this->htmlDisplay(
						$this->fdd[$k],
						$this->fdd[$k]['default'],
						false, false);
					echo '</textarea></td>'."\n";
				} else {
					// Simple edit box required
					$type = $this->fdd[$k]['type'];
					echo '    ';
					echo '<td>';
					if ($this->readonly($k)) {
						echo $this->htmlDisplay($this->fdd[$k],'',false,false)
							.'<input type="hidden" name="'
							.$this->fds[$k]
							.'" value="'
							.$this->htmlDisplay($this->fdd[$k],$this->fdd[$k]['default'],false,false)
							.'" />';
					} else {
						if ($this->col_is_string($k) || $this->col_is_number($k)) {
							// string type
							$maxwidth = intval($this->fdd[$k]['maxlen']);
							$size = min(60,$maxwidth);
							echo '<input type="text" name="'.$this->fds[$k].'" size="'.$size.'" maxwidth="'.$maxwidth.'" value="'
							.$this->htmlDisplay($this->fdd[$k],$this->fdd[$k]['default'],false,false)
							.'" />';
						} elseif ($this->col_is_date($k)) {
							// date type, get date components
							//if ($this->fdd[$k]['default'])
							//	$value = $this->mdate_set($this->fdd[$k]['default'],$this->fdd[$k]['type']);
							//$value = time();
							//echo $this->mdate_disperse($k,$value,true);
							// string type
							$maxwidth = intval($this->fdd[$k]['maxlen']);
							$size = min(60,$maxwidth);
							echo '<input type="text" name="'.$this->fds[$k].'" size="'.$size.'" maxwidth="'.$maxwidth.'" value="'
								.$this->htmlDisplay($this->fdd[$k],$this->fdd[$k]['default'],false,false)
								.'" />';
						} else {
							// unknown type
							echo '<input type="text" name="'.$this->fds[$k].'" value="'
							.$this->htmlDisplay($this->fdd[$k],$this->fdd[$k]['default'],false,false)
							.'" />';
						}
					}
					echo '</td>';
				} // if elseif else
				if ($this->guidance)
					if ($this->fdd[$k]['help'])
						echo '    <td>'.$this->fdd[$k]['help'].'</td>'."\n";
				else
					echo "<td>&nbsp;</td>\n";
				echo '  </tr>'."\n";
			}
		} // for k < this->num_fds
	} // display_add_record  /* }}} */

	function display_copy_change_delete_record() /* {{{ */
	{
		/*
		 * for delete or change: SQL SELECT to retrieve the selected record
		 */

		$qparts['type']   = 'select';
		$qparts['select'] = $this->create_column_list();
		$qparts['from']   = $this->create_join_clause();
		$qparts['where']  = '('.$this->fqn($this->key).'='
			.$this->key_delim.$this->rec.$this->key_delim.')';

		$res = $this->myquery($this->query_make($qparts),__LINE__);
		if ($row = mysql_fetch_array($res)) {
			for ($k = 0; $k < $this->num_fds; $k++) {
				if ($this->copy_operation()) {
					if ($this->displayed($k)) {
						echo '  <tr>';
						echo '     <td>'.$this->fdd[$k]['name'].'</td>'."\n";
						if ($this->readonly($k)) {
							echo $this->display_delete_field($row, $k);
						} elseif ($this->password($k)) {
							echo '     <td><input type="password" name="'.$this->fds[$k]
								.'" value="'.$this->htmlDisplay($this->fdd[$k],$row[$k],false)
								.'" /></td>';
						} else {
							echo $this->display_change_field($row, $k);
						}
						if ($this->guidance) {
							if ($this->fdd[$k]['help'])
								echo '     <td>'.$this->fdd[$k]['help'].'</td>'."\n";
							else
								echo '     <td>&nbsp;</td>'."\n";
						}
						echo '   </tr>'."\n";
					} // if field displayed
					elseif ($this->hidden($k)) {
						if ($k != $this->key_num) {
							echo '<input type="hidden" name="'.$this->fds[$k]
								.'" value="'.$this->htmlDisplay($this->fdd[$k],$row[$k],false)
								.'" />'."\n";
						}
					}
				} elseif ($this->change_operation()) {
					if ( $this->hidden($k) ) {
						echo '<input type="hidden" name="'.$this->fds[$k]
							.'" value="'.$this->htmlDisplay($this->fdd[$k],$row[$k],false)
							.'" />'."\n";
					} elseif ( $this->displayed($k)) {
						echo '  <tr>'."\n";
						echo '    <td>'.$this->fdd[$k]['name'].'</td>'."\n";
						$this->display_change_field($row, $k);
						if ($this->guidance) {
							if ($this->fdd[$k]['help'])
								echo '     <td>'.$this->fdd[$k]['help'].'</td>'."\n";
							else
								echo '     <td>&nbsp;</td>'."\n";
						}
						echo '  </tr>'."\n";
					}
				} elseif ($this->delete_operation() || $this->view_operation()) {
					if ($this->displayed($k))  {
						echo '  <tr>'."\n";
						echo '    <td>'.$this->fdd[$k]['name'].'</td>'."\n";
						$this->display_delete_field($row, $k);
						if ($this->guidance)
							if ($this->fdd[$k]['help'])
								echo '     <td>'.$this->fdd[$k]['help'].'</td>'."\n";
							else
								echo '     <td>&nbsp;</td>'."\n";
						echo '  </tr>'."\n";
					}
				}
			} // for
		} // if row
	} // display_copy_change_delete_record  /* }}} */

	function display_change_field($row, $k) /* {{{ */
	{
		echo '<td>'."\n";

		if (isset($this->fdd[$k]['values'])) {
			if (isset($this->fdd[$k]['values']['table'])) {
				$vals = $this->set_values_from_table($k);
			} else {
				$vals = $this->fdd[$k]['values'];
			}
			echo $this->htmlSelect($this->fds[$k], $vals, $row[$k], $this->col_is_set($k));
		} elseif (isset($this->fdd[$k]['textarea'])) {
			echo '<textarea name="'.$this->fds[$k].'"';
			// rows attr
			if (isset($this->fdd[$k]['textarea']['rows'])) {
				echo ' rows="'.$this->fdd[$k]['textarea']['rows'].'"';
			}
			// cols attr
			if (isset($this->fdd[$k]['textarea']['cols'])) {
				echo ' cols="'.$this->fdd[$k]['textarea']['cols'].'"';
			}
			// wrap attr
			if (isset($this->fdd[$k]['textarea']['wrap'])) {
				echo ' wrap="'.$this->fdd[$k]['textarea']['wrap'].'"';
			} else {
				echo ' wrap="virtual"';
			}
			echo '>';
			echo $this->htmlDisplay($this->fdd[$k], $row[$k], false);
			echo $row[$this->fds[$k]];
			echo '</textarea>'."\n";
		} else {
			if ($this->col_is_string($k) || $this->col_is_number($k)) {
				// string type
				$displaylen = 50;
				if (isset($this->fdd[$k]['maxlen'])) {
					$displaylen = min($displaylen,$this->fdd[$k]['maxlen']);
				}
				echo '<input type="text" '.($this->readonly($k)?'disabled ':'')
					.'name="'.$this->fds[$k].'" value="'
					.$this->htmlDisplay($this->fdd[$k],$row[$k],false)
					.'" size="'.$displaylen.'"/>';
			} elseif ($this->col_is_date($k)) {
				# date type, get date components
				#$value = $this->mdate_from_mysql($row[$k]);
				#if ($this->readonly($k)) {
				#	$mask = $this->fdd[$k]['datemask'];
				#	if (! $mask)
				#		$mask = $this->mdate_masks[$this->fdd[$k]['type']];
				#	echo $this->mdate_format($value,$mask);
				#} else {
				#	echo $this->mdate_disperse($k,$value,true);
				#}
				// string type
				$displaylen = 50;
				if (isset($this->fdd[$k]['maxlen'])) {
					$displaylen = min($displaylen,$this->fdd[$k]['maxlen']);
				}
				echo '<input type="text" '.($this->readonly($k)?'disabled ':'')
					.'name="'.$this->fds[$k].'" value="'
					.$this->htmlDisplay($this->fdd[$k],$row[$k],false)
					.'" size="'.$displaylen.'"/>';
			} else {
				// unknown type
				echo '<input type="text" '.($this->readonly($k)?'disabled ':'')
					.'name="'.$this->fds[$k].'" value="'
					.$this->htmlDisplay($this->fdd[$k],$row[$k],false).'" />';
			}
			echo "\n";
		} // if elseif else
		echo '</td>'."\n";
	} // display_change_field($row, $k)  /* }}} */

	function htmlHidden($name,$value) /* {{{ */
	{
		return '<input type=hidden name="'.htmlspecialchars($name)
			.'" value="'.htmlspecialchars($value).'">'."\n";
	} /* }}} */

	function htmlSelect($var, $kv_array, $selected, $multiple = false, $nat_sort = false) /* {{{ */
	{
		$ret  = '<select name="'.htmlspecialchars($var);
		if ($multiple) {
			$ret  .= '[]" multiple size="'.$this->multiple;
			$selected = explode(',', $selected);
		}
		$ret .= '">'."\n";

		if ($nat_sort) {
			uasort($kv_array,'strnatcasecmp');
		}
		if (! is_array($selected)) {
			$selected = array($selected);
		}

		//$keys = array_keys($kv_array);
		//debug_var('selected',$selected);

		$found = false;
		foreach ($kv_array as $key => $value) {
			$ret .= '<option value="'.htmlspecialchars($key).'"';
			if ((! $found || $multiple) && is_numeric(array_search($key, $selected))) {
				$ret  .= ' selected';
				$found = true;
			}
			$ret .= '>'.htmlspecialchars(urldecode($value)).'</option>'."\n";
			//debug_var("array search $key",is_numeric(array_search($key,$selected)));
		}
		$ret .= '</select>';
		return $ret;
	} /* }}} */

	function display_delete_field($row, $k) /* {{{ */
	{
		if ($row[$k] == '') {
			echo '    <td>&nbsp;</td>'."\n";
		} else {
			echo '    <td>'.nl2br($this->htmlDisplay($this->fdd[$k],$row[$k])).'</td>'."\n";
		}
	} /* }}} */

	function web2plain($x) /* {{{ */
	{
		if (isset($x)) {
			if (is_array($x)) {
				for ($n=0; $n<count($x); $n++) {
					$x[$n] = $this->web2plain($x[$n]);
				}
			} else {
				$x = rawurldecode($x);
			}
		}
		return $x;
	} /* }}} */
	
	function plain2web($x) /* {{{ */
	{
		if (isset($x)) {
			if (is_array($x)) {
				for ($n=0; $n<count($x); $n++) {
					$x[$n] = $this->plain2web($x[$n]);
				}
			} else {
				$x = rawurlencode($x);
			}
		}
		return $x;
	} /* }}} */
	
	function get_http_get_var_by_name($name) /* {{{ */
	{
		global $HTTP_GET_VARS;

		if (is_array($HTTP_GET_VARS))
			$v = $HTTP_GET_VARS[$name];
		// $v could be an array if we allowed bidimensional form fields
		return $v;
	} /* }}} */

	function get_http_post_var_by_name ($name) /* {{{ */
	{
		global $HTTP_POST_VARS;

		if (is_array($HTTP_POST_VARS))
			$v = $HTTP_POST_VARS[$name];
		// $v could be an array if we allowed bidimensional form fields
		return $v;
	} /* }}} */

	/*
	 * Debug functions
	 */

	function print_get_vars ($miss = 'No GET variables found') // debug only /* {{{ */
	{
		global $HTTP_GET_VARS;

		// we parse form GET variables
		if (is_array($HTTP_GET_VARS)) {
			echo "<p> Variables per GET ";
			foreach ($HTTP_GET_VARS as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $akey => $aval) {
						// $HTTP_GET_VARS[$k][$akey] = strip_tags($aval);
						// $$k[$akey] = strip_tags($aval);
						echo "$k\[$akey\]=$aval   ";
					}
				} else {
					// $HTTP_GET_VARS[$k] = strip_tags($val);
					// $$k = strip_tags($val);
					echo "$k=$v   ";
				}
			}
			echo '</p>';
		} else {
			echo '<p>';
			echo $miss;
			echo '</p>';
		}
	} /* }}} */

	function print_post_vars($miss = 'No POST variables found')  // debug only /* {{{ */
	{
		global $HTTP_POST_VARS;
		// we parse form POST variables
		if (is_array($HTTP_POST_VARS)) {
			echo "<p>Variables per POST ";
			foreach ($HTTP_POST_VARS as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $akey => $aval) {
						// $HTTP_POST_VARS[$k][$akey] = strip_tags($aval);
						// $$k[$akey] = strip_tags($aval);
						echo "$k\[$akey\]=$aval   ";
					}
				} else {
					// $HTTP_POST_VARS[$k] = strip_tags($val);
					// $$k = strip_tags($val);
					echo "$k=$v   ";
				}
			}
			echo '</p>';
		} else {
			echo '<p>';
			echo $miss;
			echo '</p>';
		}
	} /* }}} */

	function print_vars ($miss = 'Current instance variables')  // debug only /* {{{ */
	{
		echo "$miss   ";
		echo 'page_name='.$this->page_name.'   ';
		echo 'hn='.$this->hn.'   ';
		echo 'un='.$this->un.'   ';
		echo 'pw='.$this->pw.'   ';
		echo 'db='.$this->db.'   ';
		echo 'tb='.$this->tb.'   ';
		echo 'key='.$this->key.'   ';
		echo 'key_type='.$this->key_type.'   ';
		echo 'inc='.$this->inc.'   ';
		echo 'options='.$this->options.'   ';
		echo 'fdd='.$this->fdd.'   ';
		echo 'fl='.$this->fl.'   ';
		echo 'fm='.$this->fm.'   ';
		echo 'sfn='.$this->sfn.'   ';
		echo 'qfn='.$this->qfn.'   ';
		echo 'sw='.$this->sw.'   ';
		echo 'rec='.$this->rec.'   ';
		echo 'prev='.$this->prev.'   ';
		echo 'next='.$this->next.'   ';
		echo 'saveadd='.$this->saveadd.'   ';
		echo 'moreadd='.$this->moreadd.'   ';
		echo 'savechange='.$this->savechange.'   ';
		echo 'savedelete='.$this->savedelete.'   ';
		echo 'operation='.$this->operation.'   ';
		echo "\n";
	} /* }}} */

	/*
	 * Display buttons at top and bottom of page - sparky
	 */
	function display_list_table_buttons($total_recs) /* {{{ */
	{
		// note that <input disabled isn\'t valid HTML but most browsers support it
		// TODO: classify this table and cells
		echo '<table border=0 cellpadding=0 cellspacing=0 width="100%" style="border:0;padding:0;">';
		echo '<tr><td align=left style="text-align:left;border:0;padding:0;" nowrap>' . "\n";
		if ($this->fm > 0) {
			echo '<input type="submit" name="prev" value="'.$this->labels['Prev'].'">&nbsp;';
		} else {
			echo '<input disabled type="submit" name="dprev" value="'.$this->labels['Prev'].'">&nbsp;';
		}
		if ($this->add_enabled ()) {
			echo '<input type="submit" name="operation" value="'.$this->labels['Add'].'">&nbsp;';
		}

		if ($this->nav_buttons()) {
			if ($this->view_enabled()) {
				echo '<input';
				if (! $total_recs) { echo ' disabled'; }
				echo ' type="submit" name="operation" value="'.$this->labels['View'].'">&nbsp;';
			}
			if ($this->change_enabled()) {
				echo '<input';
				if (! $total_recs) { echo ' disabled'; }
				echo ' type="submit" name="operation" value="'.$this->labels['Change'].'">&nbsp;';
			}
			if ($this->copy_enabled()) {
				echo '<input';
				if (! $total_recs) { echo ' disabled'; }
				echo ' type="submit" name="operation" value="'.$this->labels['Copy'].'">&nbsp;';
			}
			if ($this->delete_enabled()) {
				echo '<input';
				if (! $total_recs) { echo ' disabled'; }
				echo ' type="submit" name="operation" value="'.$this->labels['Delete'].'">&nbsp;';
			} // if else
		}


		if (($this->fm+$this->inc) < $total_recs) {
			echo '<input type="submit" name="next" value="'.$this->labels['Next'].'">';
		} else {
			echo '<input disabled type="submit" name="dnext" value="'.$this->labels['Next'].'">';
		}

		// Message is now written here
		echo '</td><td align=center style="text-align:center;border:0;padding:0;" nowrap><b>'.$this->message.'</b></td>';

		// display page and records statistics
		echo '<td align=right style="text-align:right;border:0;padding:0;" nowrap>' . "\n";
		if ($listall) {
			echo $this->labels['Page'].': 1 of 1';
		} else {
			echo $this->labels['Page'].': ';
			echo (($this->fm/$this->inc)+1).' '.$this->labels['of'];
			echo ' '.max(1,ceil($total_recs/abs($this->inc)));
		}
		echo '&nbsp;&nbsp;'.$this->labels['Records'].': '.$total_recs;
		echo '</td></tr></table>'."\n";
	} /* }}} */

	/*
	 * Display buttons at top and bottom of page - sparky
	 */
	function display_record_buttons() /* {{{ */
	{
		// TODO: classify this table and cells
		echo '<table border=0 cellpadding=0 cellspacing=0 width="100%" style="border:0;padding:0;">';
		echo '<tr><td align=left style="text-align:left;border:0;padding:0;" nowrap>' . "\n";
		if ($this->change_operation()) {
			echo '<input type="submit" name="savechange" value="'.$this->labels['Save'].'" />'."\n";
			echo '<input type="button" name="cancel" value="'.$this->labels['Cancel'].'" onClick="form.submit();" />'."\n";
		} elseif ($this->add_operation() or $this->more_operation()) {
			echo '<input type="submit" name="saveadd" value="'.$this->labels['Save'].'" />'."\n";
			echo '<input type="submit" name="moreadd" value="'.$this->labels['More'].'" />'."\n";
			echo '<input type="button" name="cancel" value="'.$this->labels['Cancel'].'" onClick="form.submit();" />'."\n";
		} elseif ($this->copy_operation()) {
			echo '<input type="submit" name="saveadd" value="'.$this->labels['Save'].'" />'."\n";
			echo '<input type="button" name="cancel" value="'.$this->labels['Cancel'].'" onClick="form.submit();" />'."\n";
		} elseif ($this->delete_operation()) {
			echo '<input type="submit" name="savedelete" value="'.$this->labels['Delete'].'" />'."\n";
			echo '<input type="submit" name="cancel" value="'.$this->labels['Cancel'].'" />'."\n";
		} elseif ($this->view_operation()) {
			if ($this->change_enabled()) {
				echo '<input type="submit" name="operation" value="'.$this->labels['Change'].'" />'."\n";
			}
			echo '<input type="submit" name="cancel" value="'.$this->labels['Cancel'].'" />'."\n";
		}
		echo '</td></tr></table>'."\n";
	} /* }}} */


	/*
	 * Table Page Listing
	 */
	function list_table() /* {{{ */
	{
		global $HTTP_SERVER_VARS;
		$PHP_SELF = $HTTP_SERVER_VARS['PHP_SELF'];
		// Process any navigation buttons

		//if (!isset ($this->fm))
		if ($this->fm == '') {
			$this->fm = 0;
		}
		if ($this->prev == $this->labels['Prev']) {             // Prev
			$this->fm = $this->fm - $this->inc;
			if ($this->fm < 0) {
				$this->fm = 0;
			}
		}
		if ($this->next == $this->labels['Next']) {             // Next
			$this->fm += $this->inc;
		}

		// Process filters

		if (!isset ($this->fl)) {
			$this->fl = 0;
		}  // by default, no filters

		// filter switch has been pressed
		if (isset ($this->sw)) {
			if ($this->sw == $this->labels['Search']) {
				$this->fl = 1;
			}
			if ($this->sw == $this->labels['Hide']) {
				$this->fl = 0;
			}
		}

		/*
		 * If user is allowed to Change/Delete records, we need an extra column
		 * to allow users to select a record
		 */

		$select_recs = (($this->change_enabled()
			or $this->delete_enabled()
			or $this->view_enabled())
			and ($this->key != ''));

		//echo "sfn::".$this->sfn."::<br>\n";

		// Default is to sort on first displayed field
		if (
			! is_numeric($this->sfn) &&
			! $this->initial_sort_suppressed() &&
			! isset($this->default_sort_columns)
		) {
			$k = 0;
			while (! $this->displayed($k)) {
				$k++;
			}
			$this->sfn = $k;
		}

		// Are we doing a listall?
		$listall = false;
		if ($this->inc == -1)
			$listall = true;

		/*
		 * Display the MySQL table in an HTML table
		 */

		$comp_ops = array(
				''=>'','%3C'=>'%3C','%3C%3D'=>'%3C%3D',
				'%3D'=>'%3D','%3E%3D'=>'%3E%3D','%3E'=>'%3E');
		echo '<form action="'.$this->page_name.'" method="POST">'."\n";
		echo '  <input type="hidden" name="sfn" value="';
		echo ($this->sort_asc?'':'-').$this->sfn.'" />'."\n";
		echo '  <input type="hidden" name="fl" value="'.$this->fl.'" />'."\n";

		//display buttons at top of page - sparky
		// setup query to get num_rows
		$total_recs = 0;
		$count_parts = array(
				'type'   => 'select',
				'select' => 'count(*) as num_rows',
				'from'   => $this->create_join_clause(),
				'where'  => $this->make_where_from_query_opts()
				);
		$res = $this->myquery($this->query_make($count_parts),__LINE__);
		$row = mysql_fetch_row($res);
		$total_recs = $row[0];

		if ($this->nav_up()) {
			$this->display_list_table_buttons($total_recs);
			echo '<hr>'."\n";
		}

		// if the filter input boxes are not displayed, we need to preserve the filter
		if (!$this->fl) {
			for ($k = 0; $k < $this->num_fds; $k++) {
				$l   = 'qf'.$k;
				$lc  = 'qf'.$k.'_comp';
				$$l  = $this->get_cgi_var($l);
				$$lc = $this->get_cgi_var($lc);
				$m   = $this->web2plain($$l);  // get the field name and value
				$mc  = $this->web2plain($$lc); // get the comparison operator for numeric/date types

				if (isset ($m)) {
					if (is_array($m)) { // multiple selection has been used
						if (!in_array('*',$m)) {// one '*' in a multiple selection is all you need
							for ($n=0; $n<count($m); $n++) {
								if ($this->plain2web($m[$n]) != '') {
									echo '  <input type="hidden" name="qf'.$k.'['.$n
										.']" value="'.$this->plain2web($m[$n]).'">'."\n";
								}
							}
						}
					} else {
						// query field comparison operator (if any)
						if ($this->plain2web($mc) != '') {
							$this->qfn = $this->qfn.'&qf'.$k.'_comp='.$this->plain2web($mc);
							echo '  <input type="hidden" name="'.$lc.'" value="'.$mc.'">'."\n";
						}
						// preserve query field & value
						if ($this->plain2web($m) != '') {
							$this->qfn = $this->qfn.'&qf'.$k.'='.$this->plain2web($m);
							echo '  <input type="hidden" name="'.$l.'" value="'.$m.'">'."\n";
						}
					}
				}
			}
		}

		/*
		 * Set up the URLs which allow sorting by clicking on column headings
		 */
		$prev_qfn  = $this->qfn;
		$this->qfn = '';
		for ($k = 0; $k < $this->num_fds; $k++) {
			$l   = 'qf'.$k;
			$lc  = 'qf'.$k.'_comp';
			$$l  = $this->get_cgi_var($l);
			$$lc = $this->get_cgi_var($lc);
			$m   = $this->web2plain($$l);  // get the field name and value
			$mc  = $this->web2plain($$lc); // get the comparison operator for numeric/date types

			if (isset ($m)) {
				if (is_array($m)) { // multiple selection has been used
					if (!in_array('*',$m)) { // one '*' in a multiple selection is all you need
						for ($n=0; $n<count($m); $n++) {
							if ($this->plain2web($m[$n]) != '') {
								$this->qfn = $this->qfn.'&qf'.$k.'['.$n.']='
									.$this->plain2web($m[$n]); }
						}
					}
				} else {
					if ($this->plain2web($m)!='') {
						if ($$lc) {
							$this->qfn .= '&'.$lc.'='.$$lc;
						}
						$this->qfn = $this->qfn.'&qf'.$k.'='.$this->plain2web($m);

						/*
						// check for multipart date/time/datetime/timestamp/years
						$qfyear = "qf".$k."_ye";
						$qfhour = "qf".$k."_ho";
						global $$qfyear,$$qfhour;
						if ($$qfyear || $$qfhour) {
							// we have a multi part date/time thingy
							$qfmont = "qf".$k."_mo";
							$qfday  = "qf".$k."_da";
							$qfminu = "qf".$k."_mi";
							$qfsec  = "qf".$k."_se";
							global $$qfmont,$$qfday,$$qfminu,$$qfsec;
							foreach (
								array(
									$qfyear=>$$qfyear,$qfmonth=>$$qfmonth,
									$qfday=>$$qfday,$qfhour=>$$qfhour,
									$qfminute=>$$qfminute,$qfsecond=>$$qfsecond
								) as $qfk => $qfv
							)
								if ($qfv)
									$this->qfn .= "&$qfk=".$this->plain2web($qfv);
						}
						*/
					}
				}
			}
		}
		echo '  <input type="hidden" name="qfn" value="'.htmlspecialchars($this->qfn).'" />'."\n";

		// if sort sequence has changed, restart listing
		if ($this->qfn != $prev_qfn) {
			$this->fm = 0;
			//var_dump($this->qfn);
			//var_dump($prev_qfn);
		}

		echo '  <input type="hidden" name="fm" value="'.$this->fm.'" />'."\n";

		//$this->print_nav_buttons();

		echo '  <table width="100%" border="1" cellpadding="1" cellspacing="0"';
		echo ' summary="'.$this->tb.'">'."\n";
		echo '    <tr>'."\n";

		/*
		 * System (navigation, selection) columns counting
		 */
		$sys_cols = 0;
		$sys_cols += intval($this->filter_enabled() || $select_recs);
		if ($sys_cols > 0) {
			$sys_cols += intval($this->nav_buttons()
					&& ($this->nav_text_links() || $this->nav_graphic_links()));
		}
		
		/*
		 * We need an initial column(s) (sys columns)
		 * if we have filters, Changes or Deletes enabled
		 */
		if ($sys_cols) {
			echo '<th colspan='.$sys_cols.' align="center">';
			if ($this->filter_enabled ()) {
				echo '<input type="submit" name="sw" value="'
					.($this->fl ? $this->labels['Hide'] : $this->labels['Search']).'">';
			} else {
				echo '&nbsp;';
			}
			echo '</th>'."\n";
		}

		for ($k = 0; $k < $this->num_fds; $k++) {
			$fd = $this->fds[$k];

			/*
			if (
				(
					stristr($this->fdd[$fd]['options'],'L') ||
					! isset ($this->fdd[$fd]['options'])
				) &&
				! $this->hidden($k)
			)
			*/
			if ($this->displayed($k)) {
				$fdn = $this->fdd[$fd]['name'];
				if (isset ($this->fdd[$fd]['width'])) {
					$w = ' width="'.$this->fdd[$fd]['width'].'"';
				} else {
					$w = '';
				}
				if ($this->fdd[$fd]['sort']) {
					// clicking on the current sort field reverses the sort order
					echo '<th'.$w.'><a href="'.$this->page_name.'?fm=0&fl='.$this->fl.'&sfn=';
					if ($this->sfn == $k) {
						// reverse the sort order sign
						echo ($this->sort_asc?'-':'');
					}
					echo $k.$this->qfn.'">'.$fdn.'</a></th>'."\n";
				} else {
					echo '<th'.$w.'>'.$fdn.'</th>'."\n";
				}
			} // if

			// if we have any aggregates going on, then we have to list all results
			$var_to_total  = 'qf'.$k.'_aggr';
			$$var_to_total = $this->get_cgi_var($var_to_total);
			if ($$var_to_total != '') {
				$listall = true;
			}
		} // for

		echo '</tr>'."\n";


		/*
		 * Prepare the SQL Query from the data definition file
		 */
		$qparts['type'] = 'select';
		$qparts['select'] = $this->create_column_list();
		// Even if the key field isn't displayed, we still need its value
		if ($select_recs) {
			if (!in_array ($this->key, $this->fds)) {
				$qparts['select'] .= ','.$this->fqn($this->key);
			}
		}
		$qparts['from'] = $this->create_join_clause();
		$qparts['where'] = $this->make_where_from_query_opts();
		// build up the ORDER BY clause
		if (
			is_numeric($this->sfn) || 
			isset($this->default_sort_columns)
		) {
			$raw_sort_fields = array();
			$sort_fields     = array();
			$sort_fields_w   = array();
			//if ($this->sfn != '')
			if (is_numeric($this->sfn)) {
				if (isset($this->fdd[$this->sfn]['expression'])) {
					$raw_sort_field = 'qf'.$this->sfn;
					$sort_field     = 'qf'.$this->sfn;
					$sort_field_w   = $this->sfn.'(expression)';
				} else {
					$raw_sort_field = $this->fqn($this->sfn);
					$sort_field     = $this->fqn($this->sfn);
					$sort_field_w   = $this->fdd[$this->sfn]['name'];
				}
				if ( ! $this->sort_asc) {
					$sort_field .= ' DESC';
					$sort_field_w .= ' descending';
				}
				$raw_sort_fields[] = $raw_sort_field;
				$sort_fields[] = $sort_field;
				$sort_fields_w[] = $sort_field_w;
			}
			if (isset($this->default_sort_columns)) {
				foreach ($this->default_sort_columns as $dsc) {
					if (substr($dsc,0,1)=='-') {
						$field = substr($dsc,1);
						$desc = true;
					} else {
						$field = $dsc;
						$desc = false;
					}
					$raw_candidate = $this->fqn($field);
					$candidate = $this->fqn($field,true);
					$sort_field_w = $this->fdd[$field]['name'];
					if ($desc) {
						$candidate .= ' DESC';
						$sort_field_w .= ' descending';
					}
					if (! in_array($raw_candidate,$raw_sort_fields)) {
						$sort_fields[] = $candidate;
						$sort_fields_w[] = $sort_field_w;
					}
				}
			}
			if (count($sort_fields) > 0) {
				$qparts['orderby'] = join(',',$sort_fields);
			}
		}
		$to = $this->fm + $this->inc;
		if ($listall) {
			$qparts['limit'] = $this->fm.',-1';
		} else {
			$qparts['limit'] = $this->fm.','.$this->inc;
		}

		if ($qparts['orderby'] && $this->display['sort']) {
			// XXX this doesn't preserve filters
			echo '<tr>';
			if ($this->sfn != 0) {
				echo '<td align="center">'.
					'<a class="pme_a_t" href="'.$PHP_SELF.'">Clear</a>'.
					'</td>';
				echo '<td colspan="'.($this->num_fields_displayed + $sys_cols - 1).'">Sorted By: ';
			} else {
				echo '<td colspan="'.($this->num_fields_displayed + $sys_cols).'">Default Sort Order: ';
			}
			echo join(', ',$sort_fields_w);
			echo '</td></tr>'."\n";
		}

		/*
		 * FILTER
		 *
		 * Draw the filter and fill it with any data typed in last pass and stored
		 * in the array parameter keyword 'filter'. Prepare the SQL WHERE clause.
		 */

		if ($this->fl) {
			echo '<tr><td colspan='.$sys_cols.' align="center"><input type="submit" name="filter" value="'
				.$this->labels['Go'].'" /></td>'."\n";
			for ($k = 0; $k < $this->num_fds; $k++) {
				$this->field_name = $this->fds[$k];
				$fd               = $this->field_name;
				$this->field      = $this->fdd[$fd];
				$l   = 'qf'.$k;
				$lc  = 'qf'.$k.'_comp';
				$$l  = $this->get_cgi_var($l);
				$$lc = $this->get_cgi_var($lc);
				$m   = $this->web2plain($$l);  // get the field name and value
				$mc  = $this->web2plain($$lc); // get the comparison operator for numeric/date types

				$widthStyle = '';
				if (isset($this->fdd[$fd]['width']));
					$widthStyle = ' STYLE=\'width: "'.(6*$this->fdd[$fd]['width']).'px"\'';

				$opened = false;
				if ( $this->displayed($k) ) {
					echo '<td '.$widthStyle.'>';
					$opened = true;
				}


				$type = $this->fdd[$fd]['type'];
				if (isset ($this->fdd[$k]['values'])) {
					$type='string';
				}

				/*
				if (
					stristr($this->fdd[$fd]['options'],'L') or
					!isset ($this->fdd[$fd]['options'])
				)
				*/
				if (
					$this->displayed($k)
				) {
					if ($this->fdd[$fd]['select'] == 'D' or $this->fdd[$fd]['select'] == 'M') {
						/*       
						 * Multiple fields processing - default size is 2 and array required for values
						 */
						$selected = '';
						if ($m != '') {
							$selected = $m;
						}
						if ($this->fdd[$k]['values']['table']) {
							$x = $this->set_values_from_table($k, array('*' => '*'));
						} elseif ($this->fdd[$k]['values']) {
							$x = array('*' => '*') + $this->fdd[$k]['values'];
						}
						echo $this->htmlSelect($l,$x,$selected,$multiple='');
					} elseif ($this->fdd[$fd]['select'] == 'T') {
						// this is where we put the comparison selects
						if (
							! $this->password($k) &&
							! $this->hidden($k)
						) {
							if ($this->col_is_string($k)) {
								// it's treated as a string
								echo '<input type="text" name="qf'.$k.'"';
								echo ' value="'.stripslashes($m).'"';
								if ($type != 'blob') {
									echo ' size="'.min($this->fdd[$k]['maxlen'],20).'"';
									echo ' maxlength="'.$this->fdd[$k]['maxlen'].'"';
								}
								echo '/>';
							} elseif ($this->col_is_date($k)) {
								// it's a date
								//echo $this->htmlSelect($l.'_comp',$comp_ops,$$lc);
								// first get any date elements that were passed in
								//$filter_val = $this->gather_search_date_fields_into_mysql_timestamp('qf'.$k);
								// display the search formlet
								//if ($mc) {
								//	//echo $this->display_search_field_date($type,'qf'.$k,$filter_val,$this->fdd[$k]['datemask']);
								//	//echo $this->mdate_displayForm($filter_val,$type,'qf'.$k,$this->fdd[$k]['datemask'],true);
								//	echo $this->mdate_disperse($k,true,$filter_val);
								//}
								//else {
								//	//echo $this->display_search_field_date( $type,'qf'.$k,'',$this->fdd[$k]['datemask']);
								//	echo $this->mdate_displayForm('',$type,'qf'.$k,$this->fdd[$k]['datemask'],true);
								//}
								// it's treated as a string
								echo '<input type="text" name="qf'.$k.'"';
								echo ' value="'.stripslashes($m).'"';
								if ($type != 'blob') {
									echo ' size="'.min($this->fdd[$k]['maxlen'],20).'"';
									echo ' maxlength="'.$this->fdd[$k]['maxlen'].'"';
								}
								echo '/>';
							} elseif ($this->col_is_number($k)) {
								// it's a number
								echo $this->htmlSelect($l.'_comp',$comp_ops,$$lc);
								// it's treated as a string
								echo '<input type="text" name="qf'.$k.'"'.
									' value="'.$m.'"'.
									' size="'.min($this->fdd[$k]['maxlen'],20).'"'.
									' maxlength="'.$this->fdd[$k]['maxlen'].'"'.
									'/>';
							} else {
								// type is 'unknown' or not set, it's treated as a string
								echo '<input type="text" name="qf'.$k.'"';
								echo ' value="'.stripslashes($m).'"';
								if ($type != 'blob') {
									echo ' size="'.min($this->fdd[$k]['maxlen'],20).'"';
									echo ' maxlength="'.$this->fdd[$k]['maxlen'].'"';
								}
								echo '/>';
							}
						} else {
							echo "&nbsp;";
						}

						// if it's int or real and if not password or hidden, display aggr options
						/* XXX Disabled until we have time to work on this
						if (
							(
								! $this->password($k) &&
								! $this->hidden($k)
							) && (
								(
									$this->col_is_number($k)
								) && (
									! isset($this->fdd[$k]['values'])
								)
							)
						) {
							$var_to_total = 'qf'.$k.'_aggr';
							global $$var_to_total;
							$aggr_function = $$var_to_total;
							if (isset($$var_to_total)) {
								$vars_to_total[] = $this->fqn($k);
								$aggr_from_clause .=
									' '.$aggr_function.'('.
									$this->fqn($k).
									') as '.$var_to_total;
							}
							echo '<br>Aggr: ';
							echo $this->htmlSelect($var_to_total,$this->sql_aggrs,$$var_to_total);
							if ($$var_to_total != '') {
								$listall = true;
							}
						} else {
							echo '&nbsp;';
						}
						*/
						echo '</td>'."\n";
					} else {
						echo '<td>&nbsp;</td>'."\n";
					} // if elseif else

				} // end if bro1
			} // for
			echo '</tr>'."\n";
		} // if first and fl

		/*
		 * Display the current query
		 */
		$text_query = $this->make_text_where_from_query_opts();
		if ($text_query != '' && $this->display['query']) {
			echo '<tr><td colspan='.$sys_cols.' align="center">'.
				'<a class="pme_a_t" href="'.$PHP_SELF;
			if ($this->sfn) {
				echo "?sfn=".($this->sort_asc?'':'-').$this->sfn."&fl=".$this->fl."&fm=".$this->fm;
			}
			echo '">Clear</a>'.
				'</td>';
			echo '<td colspan="'.$this->num_fields_displayed.'">Current Query: '.
				htmlspecialchars(stripslashes(stripslashes(stripslashes($text_query)))).
				"</td></tr>\n";
		}

		/*
		 * Each row of the HTML table is one record from the SQL Query
		 */
		//echo "<h4>".$this->query_make($qparts)."</h4>\n";
		$res      = $this->myquery($this->query_make($qparts),__LINE__);
		$first    = true;
		$rowCount = 0;

		if ($this->nav_text_links() || $this->nav_graphic_links()) {
			// gather query & GET options to preserve for Update/Delete links
			$qstrparts = array();
			if (count($this->qo) > 0) {
				foreach ($this->qo as $key=>$val) {
					if ($key != '' && $key != 'operation' && ! is_array($val) )
						$qstrparts[] = "$key=$val";
				}
			}
			if (count($this->get_opts) > 0) {
				foreach ($this->get_opts as $key=>$val) {
					if ($key != '' && $key != 'operation' && ! is_array($val) )
						$qstrparts[] = "$key=$val";
				}
			}

			// preserve sort field number, filter row, and first record to display
			if (isset($this->sfn))
				$qstrparts[] = 'sfn='.($this->sort_asc?'':'-').$this->sfn;
			if (isset($this->fl))
				$qstrparts[] = 'fl='.$this->fl;
			if (isset($this->fm))
				$qstrparts[] = 'fm='.$this->fm;

			// do we need to preserve filter (filter query) and sw (filter display/hide button)?

			$qpview      = $qstrparts;
			$qpview[]    = 'operation='.$this->labels['View'];
			$qpviewStr   = '?'.join('&',$qpview);

			$qpcopy      = $qstrparts;
			$qpcopy[]    = 'operation='.$this->labels['Copy'];
			$qpcopyStr   = '?'.join('&',$qpcopy);

			$qpchange    = $qstrparts;
			$qpchange[]  = 'operation='.$this->labels['Change'];
			$qpchangeStr = '?'.join('&',$qpchange);

			$qpdelete    = $qstrparts;
			$qpdelete[]  = 'operation='.$this->labels['Delete'];
			$qpdeleteStr = '?'.join('&',$qpdelete);
		}

		while ($row = mysql_fetch_array ($res)) {
			echo '<tr class="'.(($rowCount++%2)?'pme_tr_o':'pme_tr_e')."\">\n";
			if ($sys_cols) {
				$key_rec    = $row[$this->key_num];
				$qviewStr   = $qpviewStr  .'&rec='.$key_rec;
				$qcopyStr   = $qpcopyStr  .'&rec='.$key_rec;
				$qchangeStr = $qpchangeStr.'&rec='.$key_rec;
				$qdeleteStr = $qpdeleteStr.'&rec='.$key_rec;
				if ($select_recs) {
					if (! $this->nav_buttons() || $sys_cols > 1) {
						echo '<td NOWRAP align=center>';
					}
					if ($this->nav_graphic_links()) {
						if ($this->view_enabled()) {
							echo '<a class="pme_a_t" href="';
							echo htmlspecialchars($this->page_name.$qviewStr);
							echo '"><img src="'.$this->url['images'].'pme-view.png"';
							echo ' height=15 width=16 border=none alt="'.htmlspecialchars($this->labels['View']).'"></a> ';
						}
						if ($this->change_enabled()) {
							echo '<a class="pme_a_t" href="';
							echo htmlspecialchars($this->page_name.$qchangeStr);
							echo '"><img src="'.$this->url['images'].'pme-change.png"';
							echo ' height=15 width=16 border=none alt="'.htmlspecialchars($this->labels['Change']).'"></a> ';
						}
						if ($this->copy_enabled()) {
							echo '<a class="pme_a_t" href="';
							echo htmlspecialchars($this->page_name.$qcopyStr);
							echo '"><img src="'.$this->url['images'].'pme-copy.png"';
							echo ' height=15 width=16 border=none alt="'.htmlspecialchars($this->labels['Copy']).'"></a> ';
						}
						if ($this->delete_enabled()) {
							echo '<a class="pme_a_t" href="';
							echo htmlspecialchars($this->page_name.$qdeleteStr);
							echo '"><img src="'.$this->url['images'].'pme-delete.png"';
							echo ' height=15 width=16 border=none alt="'.htmlspecialchars($this->labels['Delete']).'"></a> ';
						}
					}
					if ($this->nav_text_links()) {
						if ($this->nav_graphic_links()) {
							echo '<br>';
						}
						if ($this->view_enabled())
							echo '<a class="pme_a_t" href="'.htmlspecialchars($this->page_name.$qviewStr).'">V</a> ';
						if ($this->change_enabled())
							echo '<a class="pme_a_t" href="'.htmlspecialchars($this->page_name.$qchangeStr).'">C</a> ';
						if ($this->copy_enabled())
							echo '<a class="pme_a_t" href="'.htmlspecialchars($this->page_name.$qcopyStr).'">P</a> ';
						if ($this->delete_enabled())
							echo '<a class="pme_a_t" href="'.htmlspecialchars($this->page_name.$qdeleteStr).'">D</a>';
					}
					if (! $this->nav_buttons() || $sys_cols > 1) {
						echo '</td>'."\n";
					}
					if ($this->nav_buttons()) {
						echo '<td NOWRAP align=center>';
						echo '<input type="radio" name="rec" value="'.$row[$this->key_num].'"';
						if ($first) {
							echo ' checked';
							$first = false;
						}
						echo ' />';
						echo '</td>'."\n";
					}
				} elseif ($this->filter_enabled()) {
					echo '<td colspan='.$sys_cols.'>&nbsp;</td>'."\n";
				}
			}

			// calculate the url query string for optional URL support
			$urlqueryproto = 'fm='.$this->fm.'&sfn='.($this->sort_asc?'':'-')
				.$this->sfn.'&'.'fl='.$this->fl.'&qfn='.$this->qfn;
			for ($k = 0; $k < $this->num_fds; $k++) {
				$fd = $this->fds[$k];
				if ($this->hidden($k) || $this->password($k)) {
					// XXX do nothing KLUDGE KLUDGE
				/*
				} elseif (
					stristr($this->fdd[$fd]['options'],'L') ||
					! isset($this->fdd[$fd]['options'])
				) {
				*/
				} elseif ($this->displayed($k)) {
					// XXX: echo 'displayed: '.$k.'-'.$fd;
					if ((trim ($row[$k]) == '') or ($row[$k] == 'NULL')) {
						echo '      <td>&nbsp;</td>'."\n";
					} else {
						// display the contents
						$colattrs = $this->fdd[$fd]['colattrs'];
						if ($colattrs != '')
							$colattrs = ' '.$colattrs;
						if ($this->fdd[$fd]['nowrap'])
							$colattrs .= ' NOWRAP';
						if (isset($this->fdd[$fd]['width'])) {
							$colattrs .= ' width="'.$this->fdd[$fd]['width'].'"';
						}
						echo '      <td'.$colattrs.'>';
						if (! $this->hidden($k) && ! $this->password($k)) {
							// displayable
							if (isset($this->fdd[$k]['URL'])) {
								// it's an URL

								// put some conveniences in the namespace for the user
								// to be able to use in the URL string
								$key      = $row[$this->key_num];
								$name     = $this->fds[$k];
								$value    = $row[$k];
								$page     = $this->page_name;
								$urlquery = $urlqueryproto."&rec=$key";
								// remember that $row is a mysql_fetch_array, so it contains all fields
								//debug_var('URL',$this->fdd[$k]);
								//debug_var('urlquery',$urlquery);
								// it's built, now eval it
								$urlstr = eval('return "'.$urlquery.'";');
								//debug_var('urlstr',$urlstr);
								$urllink = eval('return "'.$this->fdd[$k]['URL'].'";');
								// TODO: document this, is is undocumented
								$urldisp = isset($this->fdd[$k]['URLdisp'])
									? eval('return "'.$this->fdd[$k]['URLdisp'].'";')
									: $value;
								$target = isset($this->fdd[$k]['URLtarget'])
									? 'target="'.htmlspecialchars($this->fdd[$k]['URLtarget']).'" '
									: '';
								$urllink = htmlspecialchars($urllink);
								$urldisp = htmlspecialchars($urldisp);
								echo '<a '.$target.'class="pme_a_u" href="'.$urllink.'">'.$urldisp.'</a>';
							} elseif (isset($this->fdd[$k]['datemask'])) {
								// display date according to a mask if any
								//echo $this->mdate_set($row[$k],$this->fdd[$k]['type'],$this->fdd[$k]['datemask']);
								//echo 
								//	$this->mdate_displayPlain(
								//		$this->mdate_from_mysql(
								//			$row[$k]),
								//			(
								//				$this->fdd[$k]['datemask']?
								//					$this->fdd[$k]['datemask']
								//				:
								//					$this->mdate_masks[$this->fdd[$k]['type']]
								//			)
								//		);
								//echo $row[$k];
								// it's a normal field
								if (isset($this->fdd[$k]['trimlen'])) {
									if (strlen($row[$k]) > $this->fdd[$k]['trimlen']) {
										$shortdisp = ereg_replace("[\r\n\t ]+",' ',$row[$k]);
										$shortdisp = substr($shortdisp,0,$this->fdd[$k]['trimlen']-3).'...';
									} else {
										$shortdisp = $row[$k];
									}
									echo nl2br(
										$this->htmlDisplay (
											$this->fdd[$k],
											$shortdisp
										)
									);
								} else {
									echo nl2br($this->htmlDisplay($this->fdd[$k],$row[$k]));
								}
							} else {
								// it's a normal field
								if (isset($this->fdd[$k]['trimlen'])) {
									if (strlen($row[$k]) > $this->fdd[$k]['trimlen']) {
										$shortdisp = ereg_replace("[\r\n\t ]+",' ',$row[$k]);
										$shortdisp = substr($shortdisp,0,$this->fdd[$k]['trimlen']-3).'...';
									} else {
										$shortdisp = $row[$k];
									}
									echo nl2br(
										$this->htmlDisplay (
											$this->fdd[$k],
											$shortdisp
										)
									);
								} else {
									echo nl2br($this->htmlDisplay($this->fdd[$k],$row[$k]));
								}
							}
						} else {
							// it's hidden or a password
							echo '<i>hidden</i>';
						}
						echo '</td>'."\n";
					} // if else
				} // if
			} // for

			echo '    </tr>'."\n";
		} // while


		/*
		 * Display and accumulate column aggregation info, do totalling query
		 * XXX this feature does not work yet!!!
		 */
		// aggregates listing (if any)
		if ($$var_to_total) {
			// do the aggregate query if necessary
			//if ($vars_to_total) {
				$qp = array();
				$qp['type'] = 'select';
				$qp['select'] = $aggr_from_clause;
				$qp['from'] = $this->create_join_clause ();
				$qp['where'] = $this->make_where_from_query_opts();
				$tot_query = $this->query_make($qp);
				//$this->elog('TOT_QRY: '.$tot_query,__LINE__);
				$totals_result = $this->myquery($tot_query,__LINE__);
				$tot_row=mysql_fetch_array($totals_result);
			//}
			$qp_aggr = $qp;
			echo "\n".'<tr>'."\n".'<td>&nbsp;</td>'."\n";
			/*
			echo '<td>';
			echo printarray($qp_aggr);
			echo printarray($vars_to_total);
			echo '</td>';
			echo '<td colspan="'.($this->num_fds-1).'">'.$var_to_total.' '.$$var_to_total.'</td>';
			*/
			// display the results
			for ($k=0;$k<$this->num_fds;$k++) {
				$fd = $this->fds[$k];
				if (stristr($this->fdd[$fd]['options'],'L') or !isset($this->fdd[$fd]['options'])) {
					echo '<td>';
					$aggr_var  = 'qf'.$k.'_aggr';
					$$aggr_var = $this->get_cgi_var($aggr_var);
					if ($$aggr_var) {
						echo $this->sql_aggrs[$$aggr_var].': '.$tot_row[$aggr_var];
					} else {
						echo '&nbsp;';
					}
					echo '</td>'."\n";
				}
			}
			echo '</tr>'."\n";
		}


		echo '  </table>'."\n"; // end of table rows listing


		if ($this->nav_down()) {
			//display buttons at bottom of page - sparky
			echo '<hr>'."\n";
			$this->display_list_table_buttons($total_recs);
		}

		echo '</form>'."\n";
		//phpinfo();
		/*
		foreach (
			array(
			//	'1999-12-31'=>'%Y-%m-%d',
			//	'99-Mar-31'=>'%y-%M-%d',
			//	'99-1-31'=>'%y-%n-%d'
			//	'March 8, 1999'=>'%F %j, %Y'
			//	'March 8, 1999 09:17:32'=>'%F %j, %Y %H:%i:%s'
				'March 8, 1999 9:17:32'=>'%F %j, %Y %G:%i:%s'
			) as $val=>$mask
		) {
			echo "<br>\n";
			debug_var('val,mask',"$val::$mask");
			debug_var('mdate_parse',date('Y m d H:i:s',$this->mdate_parse($val,$mask)));
		}
		*/
	} /* }}} */

	function display_record() /* {{{ */
	{
		$this->create_javascripts();

		if ($this->nav_up()) {
			//display buttons at top of page - sparky
			$this->display_record_buttons ();
			echo "  <hr />\n";
		}

		echo '<table width="100%" border="1" cellpadding="1" cellspacing="0" summary="'.$this->tb.'">'."\n";
		echo '  <input type="hidden" name="rec" value="'.($this->copy_operation()?'':$this->rec).'" />'."\n";
		echo '  <input type="hidden" name="fm" value="'.$this->fm.'" />'."\n";
		echo '  <input type="hidden" name="sfn" value="'.$this->sfn.'" />'."\n";
		echo '  <input type="hidden" name="fl" value="'.$this->fl.'" />'."\n";

		/*
		 * preserve the values of any filter fields qf0..qfn for Pass 3
		 */
		for ($k = 0; $k < $this->num_fds; $k++) {
			$l    = 'qf'.$k;
			$lc   = 'qf'.$k.'_comp';
			$$l   = $this->get_cgi_var($l);
			$$lc  = $this->get_cgi_var($lc);
			$m    = $this->web2plain($$l);  // get the field name and value
			$mc   = $this->web2plain($$lc); // get the comparison operator for numeric/date types
			if (isset ($m)) {
				if (is_array($m)) { // multiple selection has been used
					if (!in_array('*',$m)) {	// one '*' in a multiple selection is all you need
						for ($n=0; $n<count($m); $n++) {
							if ($this->plain2web($m[$n]) != '') {
								echo '  <input type="hidden" name="qf'.$k.'['.$n.']" value="'
									.$this->plain2web($m[$n]).'">'."\n";
							}
						}
					}
				} else {
					if ($this->plain2web($m) != '') {
						$this->qfn = $this->qfn.'&qf'.$k.'='.$m;
						echo '  <input type="hidden" name="qf'.$k.'" value="'.$this->plain2web($m).'">'."\n";
					}
				}
			}
		}
		echo '  <input type="hidden" name="qfn" value="'.$this->qfn.'" />'."\n";

		if ($this->add_operation() or $this->more_operation() ) {
			$this->display_add_record ();
		} else {
			$this->display_copy_change_delete_record ();
		}

		echo '</table>'."\n";

		if ($this->nav_down()) {
			//display buttons at bottom of page - sparky
			echo "  <hr />\n";
			$this->display_record_buttons ();
		}

		echo '</form>'."\n";

	} /* }}} */

	/*
	 * Action functions
	 */
	function do_add_record() /* {{{ */
	{
		global $HTTP_SERVER_VARS;
		$REMOTE_USER = $HTTP_SERVER_VARS['REMOTE_USER'];
		$REMOTE_ADDR = $HTTP_SERVER_VARS['REMOTE_ADDR'];
		$tib         = true;
		// check for a before-add trigger
		if (isset($this->triggers['insert']['before'])) {
			$tib = include($this->triggers['insert']['before']);
		}
		if ($tib) {
			// before trigger returned good status let's do the main operation
			$key_col_val = '';
			$qry = '';
			for ($k = 0; $k < $this->num_fds; $k++) {
				if ($this->displayed($k)) {
					$fd = $this->fds[$k];
					if ($fd == $this->key) {
						$key_col_val = addslashes($this->encode($this->fdd[$k],$fn));
					}
					if ($qry == '') {
						$qry = 'INSERT INTO '.$this->tb.' (`'.$fd.'`';
					} else {
						$qry = $qry.',`'.$fd.'`';
					}
				}
			}
			$tim = false;
			// do the main operation
			$val = ') VALUES (';
			$vals = array();
			for ($k = 0; $k < $this->num_fds; $k++) {
				$type = $this->fdd[$k]['type'];
				if ( $this->displayed($k) ) {
					$fd = $this->fds[$k];
					$fn = $this->get_http_post_var_by_name($fd);
					/*
					if ($this->col_is_date($k))
					{
						//$vals[$k] = '"'.$this->mdate_set($this->mdate_getFromPost($k),$type,$this->fds[$k]['type']).'"'; 
						if ($type == 'time')
							$vals[$k] = 'date_format(from_unixtime('.$this->mdate_getFromPost($k).'),"%H%i%s")'; 
						elseif ($type == 'year')
							$vals[$k] = 'date_format(from_unixtime('.$this->mdate_getFromPost($k).'),"%Y")'; 
						else
							$vals[$k] = 'from_unixtime('.$this->mdate_getFromPost($k).')'; 
					} else // continued on next line
					*/
					/* Old Jim code: $this->col_is_set($k) && $fn != ''*/
					if (is_array($fn)) {
						$vals[$k] = "'".addslashes($this->encode($this->fdd[$k],join(',',$fn)))."'";
					} else {
						$vals[$k] = "'".addslashes($this->encode($this->fdd[$k],$fn))."'";
					}
				}
			}
			$qry = $qry.$val.join(',',$vals).')';
			$res = $this->myquery($qry,__LINE__);
			if ($res) {
				$tim = true;
			}
			$this->message = mysql_affected_rows().' '.$this->labels['record added'];
		}
		if (
			$tib &&
			isset($this->triggers['insert']['after']) &&
			$tim
		) {
			// before executed ok
			// main op executed ok
			// let's do the after trigger
			$tia = include($this->triggers['insert']['after']);
		}
		// notify list
		$kv = array();
		if (($this->notify['insert'])) {
			$user = $REMOTE_USER;
			if (! $user)
				$user = $REMOTE_ADDR;
			$body = 'A new item was added to '.$this->page_name." by ".$user." with the following fields:\n";
			for ($k=0;$k<$this->num_fds;$k++) {
				if ( $this->displayed($k) ) {
					$body .= $this->fdd[$k]['name'].': '.$vals[$k]."\n";
					$kv[$this->fds[$k]] = $vals[$k];
				}
			}
			// mail it
			mail($this->notify['insert'],'Record Added to '.$this->tb,$body);
		}
		// note change in log table
		if ($this->logtable) {
			$this->myquery(
				"insert into ".$this->logtable." values (".
				"now(),".
				"'".$REMOTE_USER."',".
				"'".$REMOTE_ADDR."',".
				"'insert','".
				$this->tb."',".
				"'".$key_col_val."','','','".
				addslashes(serialize($kv))."')"
			,__LINE__);
		}
	} /* }}} */

	function do_change_record() /* {{{ */
	{
		global $HTTP_SERVER_VARS;
		$REMOTE_USER = $HTTP_SERVER_VARS['REMOTE_USER'];
		$REMOTE_ADDR = $HTTP_SERVER_VARS['REMOTE_ADDR'];
		$tub         = true;
		// check for a before-add trigger
		if (isset($this->triggers['update']['before'])) {
			$tub = include($this->triggers['update']['before']);
		}
		$tum = false;
		if ($tub) {
			// before trigger returned good status
			// let's do the main operation
			$qry = '';
			$qry_old_rec = '';
			for ($k = 0; $k < $this->num_fds; $k++) {
				$type = $this->fdd[$k]['type'];
				if ($this->displayed($k) && ! $this->readonly($k)) {
					$fd = $this->fds[$k];
					if ($fd == $this->key) {
						$key_col_val = addslashes($this->get_http_post_var_by_name($fd));
					}
					$fn = $this->get_http_post_var_by_name($fd);
					/*
					if ($this->col_is_date($k))
					{
						$fn = date(str_replace('%','',$this->mdate_masks[$type]),$this->mdate_getFromPost($k));
					}
					*/
					/* Old Jim code: $this->col_is_set($k) && $fn != ''*/
					if (is_array($fn)) {
						$newValue = addslashes($this->encode($this->fdd[$k],join(',',$fn)));
					} else {
						$newValue = addslashes($this->encode($this->fdd[$k],$fn));
					}
					if ($qry == '') {
						$qry = 'UPDATE '.$this->tb.' SET `'.$fd.'`=\''.$newValue.'\'';
						$qry_old_rec = 'SELECT `'.$fd.'`';
					} else {
						$qry = $qry.',`'.$fd.'`=\''.$newValue.'\'';
						$qry_old_rec .= ',`'.$fd.'`';
					}
					$newvalues[$this->fds[$k]] = addslashes($fn);
				} elseif ($this->hidden($k)) {
					// XXX do something
				}
			}
			$qry = $qry.' WHERE ('.$this->key.' = '.$this->key_delim.$this->rec.$this->key_delim.')';
			$qry_old_rec .= ' FROM '.$this->tb.' WHERE ('.$this->key.' = '.$this->key_delim.$this->rec.$this->key_delim.')';
		    // get the old data
		    $res_old = $this->myquery($qry_old_rec,__LINE__);
		    $oldvalues = mysql_fetch_array($res_old);
		    // update the data
			//echo "\n<h4>$qry</h4>\n";
			$res = $this->myquery($qry,__LINE__);
		    // find and accumulate the changes
		    $changes=array();
		    for ($k = 0; $k < $this->num_fds; $k++) {
		      if ($this->displayed($k)) {
			  	if ($oldvalues[$this->fds[$k]] != stripslashes($newvalues[$this->fds[$k]])) {
			  		$changes[$this->fds[$k]] = array();
			  		$changes[$this->fds[$k]]['was'] = $oldvalues[$this->fds[$k]];
			  		$changes[$this->fds[$k]]['is' ] = $newvalues[$this->fds[$k]];
			  	}
			  }
			}
			if ($res) {
				$tum = true;
			}

/*
echo '<h3>Was:</h3>'."\n";
echo '<pre>';
print_r($oldvalues);
echo '</pre>'."\n";

echo '<h3>Is:</h3>'."\n";
echo '<pre>';
print_r($newvalues);
echo '</pre>'."\n";

echo '<h3>Changes to be sent in e-mail:</h3>'."\n";
echo '<pre>';
print_r($changes);
echo '</pre>'."\n";
echo '<h5>'.mysql_affected_rows ().' '.$this->labels['Change'].'</h5>'."\n";
*/

			$this->message = mysql_affected_rows().' '.$this->labels['record changed'];
		}
		if (
			$tub &&
			isset($this->triggers['update']['after']) &&
			$tum
		) {
			// before executed ok
			// main op executed ok
			// let's do the after trigger
			$tua = include($this->triggers['update']['after']);
		}
		// notify list
		if (($this->notify['update'])) {
			if (count($changes) > 0) {
				$user = $REMOTE_USER;
				if (! $user)
					$user = $REMOTE_ADDR;
				$body = 'An item with '
					.$this->fdd[$this->key]['name']
					.'='
					.$this->key_delim.$this->rec.$this->key_delim
					.' was updated by '.$user.' in '.$this->page_name." with the following fields:\n";
				foreach ($changes as $key=>$vals) {
					if ( $this->displayed($k) ) {
						$fieldName = $this->fdd[$key]['name'];
						$body .=
							$fieldName.":\n".
							"was:\t\"".$vals['was']."\"\n".
							"is:\t\"".$vals['is']."\"\n";
					}
				}
				// mail it
				mail($this->notify['update'],'Record Updated in '.$this->tb,$body);
			}
		}

		// note change in log table
		if ($this->logtable) {
			foreach ($changes as $key=>$vals) {
				$qry = "insert into ".$this->logtable." values (".
					"now(),'".$REMOTE_USER."','".$REMOTE_ADDR."','update','".
					$this->tb."','".$key_col_val."','".$key."','".
					addslashes($vals['was'])."','".
					addslashes($vals['is'])."')";
				$this->myquery($qry,__LINE__);
			}
		}
	} /* }}} */

	function do_delete_record() /* {{{ */
	{
		global $HTTP_SERVER_VARS;
		$REMOTE_USER = $HTTP_SERVER_VARS['REMOTE_USER'];
		$REMOTE_ADDR = $HTTP_SERVER_VARS['REMOTE_ADDR'];
		$tdb         = true;
		// check for a before-add trigger
		if (isset($this->triggers['delete']['before'])) {
			$tdb = include($this->triggers['delete']['before']);
		}
		$tdm = false;
		// before trigger returned good status
		// let's do the main operation
		if ($tdb) {
			// before trigger returned good status
			// let's do the main operation
			for ($k = 0; $k < $this->num_fds; $k++) {
				if ( $this->displayed($k) ) {
					$fd = $this->fds[$k];
					if ($fd == $this->key) {
						$key_col_val = addslashes($this->encode($this->fdd[$k],$fn));
					}
				}
			}

			if ($this->logtable) {
				$res = $this->myquery(
					'select * from '.$this->tb.' where (`'.$this->key.'` = '.$this->key_delim.$this->rec.$this->key_delim.')'
				,__LINE__);
				$oldrow = mysql_fetch_array($res);
			}
			$qry = 'DELETE FROM '.$this->tb.' WHERE (`'.$this->key.'` = '.$this->key_delim.$this->rec.$this->key_delim.')';
			$res = $this->myquery($qry,__LINE__);
			if ($res) {
				$tdm = true;
			}
			$this->message = mysql_affected_rows().' '.$this->labels['record deleted'];
		}
		if (
			$tdb &&
			isset($this->triggers['delete']['after']) &&
			$tdm
		) {
			// before executed ok
			// main op executed ok
			// let's do the after trigger
			$tda = include($this->triggers['delete']['after']);
		}

		// notify list
		if (($this->notify['delete'])) {
			$user = $REMOTE_USER;
			if (! $user)
				$user = $REMOTE_ADDR;
			$body = 'An item was deleted by '.$user.' from '.$this->page_name."\n";
			foreach ($oldrow as $key=>$val) {
				if (is_string($key)) {
					$body .= $this->fdd[$key]['name'].":\t".$val."\n";
				}
			}
			// mail it
			mail($this->notify['delete'],'Record Deleted in '.$this->tb,$body);
		}
		// note change in log table
		if ($this->logtable) {
			$this->myquery(
				"INSERT INTO ".$this->logtable." VALUES (".
				"SYSDATE(),".
				"'".$REMOTE_USER."',".
				"'".$REMOTE_ADDR."',".
				"'delete','".
				$this->tb."',".
				"'".$key_col_val."',".
				"'".$key."','".
				addslashes(serialize($oldrow))."','')"
			,__LINE__);
		}
	} /* }}} */

	/*
	 * The workhorse
	 */
	function execute() /* {{{ */
	{
		set_magic_quotes_runtime(0); // let's do explicit quoting ... it's safer

		// XXX fix this to use col_is_[type]
		if (in_array($this->key_type,array('string','blob','date','time','datetime','timestamp','year')))
		{
			$this->key_delim = '"';
		} else {
			$this->key_delim = '';
			$this->rec = intval($this->rec); // Fixed #523390 [7/8/2002] [1/2]
		}

		$this->gather_query_opts();
		$this->gather_get_vars();
		$this->gather_post_vars();
		$this->unify_opts();

//  debug code - uncomment to enable

//  phpinfo();
//  $this->print_get_vars();
//  $this->print_post_vars();
//  $this->print_vars();
//  echo "<pre>query opts:\n";
//  echo print_r($this->query_opts);
//  echo "</pre>\n";
//  echo "<pre>get vars:\n";
//  echo print_r($this->get_opts);
//  echo "</pre>\n";

		if (!isset ($this->db)) {
			die("<h1>phpMyEdit: no database defined</h1>\n</body>\n</html>\n");
		}
		if (!isset ($this->tb)) {
			die ("<h1>phpMyEdit: no table defined</h1>\n</body>\n</html>\n");
		}

		$dbl = @mysql_pconnect($this->hn, $this->un, $this->pw)
			or die("<h1>phpMyEdit: could not connect to MySQL</h1>\n</body>\n</html>\n");

		/*
		 * ======================================================================
		 * Pass 3: process any updates generated if the user has selected
		 * a save button during Pass 2
		 * ======================================================================
		 */
		if ($this->saveadd == $this->labels['Save']) {
			$this->do_add_record();
		}
		if ($this->moreadd == $this->labels['More']) {
			$this->do_add_record();
			$this->operation = $this->labels['Add']; // to force add operation
		}
		if ($this->savechange == $this->labels['Save']) {
			$this->do_change_record();
		}
		if ($this->savedelete == $this->labels['Delete']) {
			$this->do_delete_record();
		}

		/*
		 * ======================================================================
		 * Pass 2: display an input/edit/confirmation screen if the user has
		 * selected an editing button on Pass 1 through this page
		 * ======================================================================
		 */
		if ($this->add_operation()           || $this->more_operation()
				|| $this->change_operation() || $this->delete_operation()
				|| $this->view_operation()   || $this->copy_operation()) {
			$this->display_record();
		}

		/*
		 * ======================================================================
		 * Pass 1 and Pass 3: display the MySQL table in a scrolling window on
		 * the screen (skip this step in 'Add More' mode)
		 * ======================================================================
		 */
		else {
			$this->list_table();
		}

		//phpinfo();
		global $timer;
		if ($this->display['time'] && $timer) {
			echo $timer->end();
		}
	} /* }}} */

	/*
	 * Class constructor
	 */
	function phpMyEdit($opts) /* {{{ */
	{
		global $HTTP_SERVER_VARS;

		/*
		 * Creating directory variables
		 */
		$this->dir['root'] = dirname(__FILE__)
			. (strlen(dirname(__FILE__)) > 0 ? '/' : '');
		$this->dir['lang'] = $this->dir['root'].'lang/';

		/*
		 * Creting URL variables
		 */
		$this->url['images'] = 'images/';

		/*
		 * Instance class variables
		 */
		$this->hn       = $opts['hn'];
		$this->hn       = $opts['hn'];
		$this->un       = $opts['un'];
		$this->pw       = $opts['pw'];
		$this->db       = $opts['db'];
		$this->tb       = $opts['tb'];
		$this->key      = $opts['key'];
		$this->key_type = $opts['key_type'];
		$this->inc      = $opts['inc'];
		$this->options  = $opts['options'];
		$this->fdd      = $opts['fdd'];
		$this->multiple = intval($opts['multiple']);
		if ($this->multiple <= 0) {
			$this->multiple = 2;
		}
		$this->display = $opts['display'];
		if ($opts['language']) {
			$this->labels = $this->make_language_labels($opts['language']);
		} else {
			$this->labels = $this->make_language_labels($HTTP_SERVER_VARS['HTTP_ACCEPT_LANGUAGE']);
		}
		$this->filters = $opts['filters'];
		$this->default_sort_columns = $opts['default_sort_columns'];
		$this->triggers = $opts['triggers'];
		$this->logtable = $opts['logtable'];


		$this->page_name = $this->tb;
		if ($opts['page_name'])
			$this->page_name = $opts['page_name'];

		// alternate row background colors
		if (isset($opts['bgcolorOdd'])) {
			$this->bgcolorOdd = 'White';
		} else {
			$this->bgcolorOdd = $opts['bgcolorOdd'];
		}
		if (isset($opts['bgColorEven'])) {
			$this->bgcolorEven = 'Silver';
		} else {
			$this->bgcolorEven = $opts['bgcolorEven'];
		}

		// e-mail notification
		if (isset($opts['notify'])) {
			$this->notify = $opts['notify'];
		}

		// navigation
		$this->navigation = $opts['navigation'];
		if (! $this->nav_buttons() && ! $this->nav_text_links() && ! $this->nav_graphic_links()) {
			$this->navigation .= 'B'; // buttons are default
		}
		if (! $this->nav_up() && ! $this->nav_down()) {
			$this->navigation .= 'D'; // down position is default
		}

		/*
		 *		Find the URL to post forms
		 */

		$this->page_name = basename($HTTP_SERVER_VARS['PHP_SELF']);

		/*
		 *		form variables all around
		 */
		//global $operation, $apply, $fl, $fm, $sfn, $qfn, $sw, $rec, $prev, $next;
		//global $saveadd, $moreadd, $savechange, $savedelete;

		$this->operation  = $this->get_cgi_var('operation');
		$this->apply      = $this->get_cgi_var('apply');
		$this->fl         = $this->get_cgi_var('fl');
		$this->fm         = intval($this->get_cgi_var('fm'));

		$this->sfn        = $this->get_cgi_var('sfn');
		$this->sort_asc   = $this->sfn[0] != '-';
		$this->sfn        = abs(intval($this->sfn));

		//$this->qfn        = intval($this->get_cgi_var('qfn'));
		$this->qfn        = '';
		$this->sw         = $this->get_cgi_var('sw');
		$this->rec        = $this->get_cgi_var('rec', ''); // Fixed #523390 [7/8/2002] [2/2]
		$this->prev       = $this->get_cgi_var('prev');
		$this->next       = $this->get_cgi_var('next');
		$this->saveadd    = $this->get_cgi_var('saveadd');
		$this->moreadd    = $this->get_cgi_var('moreadd');
		$this->savechange = $this->get_cgi_var('savechange');
		$this->savedelete = $this->get_cgi_var('savedelete');

		/*
		 *		Extract SQL Field Names and number of fields
		 */
		$this->guidance = false;
		$field_num = 0;
		$num_fields_displayed = 0;
		foreach ($this->fdd as $akey => $aval) {
			$this->fds[] = $akey;
			if ($sfn == '' && $akey == $sort_field) {
				$this->sfn = $field_num;
			}
			if ($this->displayed($field_num))
				$num_fields_displayed++;
			if (is_array($aval['values']) && (! $aval['values']['table'])) {
				$values = array();
				foreach ($aval['values'] as $val) {
					$values[$val]=$val;
				}
				$aval['values'] = $values;
			}
			$this->fdd[$field_num] = $aval;
			/*
// prep for full text search
if ($aval['type'] == 'string' || $aval['type'] == 'blob') {
$this->string_fields[] = $akey;
}
			 */
			if ($aval['help'])
			$this->guidance = true;
			$field_num++;
			}
$this->num_fds = $field_num;
$this->num_fields_displayed = $num_fields_displayed;

$this->key_num = array_search($this->key,$this->fds);

/*
 *		Constants
 */

// code to use this is commented out
$this->sql_aggrs = array(''=>'','sum'=>'Total','avg'=>'Average','min'=>'Minimum','max'=>'Maximum','count'=>'Count');

// to support quick type checking
$this->stringTypes = array('string','blob','set','enum');
$this->numberTypes = array('int','real');
$this->dateTypes   = array('date','datetime','timestamp','time','year');

// mdate constants
$this->mdate_masks = array(
		'date'=>'%Y-%m-%d',
		'datetime'=>'%Y-%m-%d %H:%i:%s',
		'timestamp'=>'%Y%m%d%H%i%s',
		'time'=>'%H:%i:%s',
		'year'=>'%Y');

$this->mdate_daterange = range(date('Y')-10,date('Y')+10);

$this->months_short = array(
		'~~PME~~'=>0,
		'Jan'=>1, 'Feb'=>2, 'Mar'=>3, 'Apr'=>4,
		'May'=>5, 'Jun'=>6, 'Jul'=>7, 'Aug'=>8,
		'Sep'=>9, 'Oct'=>10, 'Nov'=>11, 'Dec'=>12);
$this->months_long = array(
		'~~PME~~'=>0,
		'January'=>1,'February'=>2,'March'=>3,
		'April'=>4,'May'=>5,'June'=>6,
		'July'=>7,'August'=>8,'September'=>9,
		'October'=>10,'November'=>11,'December'=>12);
$this->months_long_keys = array_keys($this->months_long);

/* If you are phpMyEdit developer, set this to 1.
   You can also hide some new unfinished and/or untested features under
   if ($this->development) { new_feature(); } statement.

   Also note, that this is currently unused. */
$this->development = 0;

// Call to Action
// Moved this from the setup.php generated file to here
$this->execute();
} /* }}} */

} // end of phpMyEdit class

/* Modeline for ViM {{{
 * vim:set ts=4:
 * vim600:fdm=marker fdl=0 fdc=0:
 * }}} */

?>
