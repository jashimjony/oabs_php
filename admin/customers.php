<div class="main">
<h2>Customer Maintainance</h2>
<?php
// Customer Maintainance Version 2
include ("../Connections/Database.php");
$opts["tb"] = $customertable;
$opts["key"] = "customer";
$opts["key_type"] = "int";
$opts["sort_field"] = "customer";
$opts['inc'] = 15;
$opts['options'] = 'D';
$opts['multiple'] = '4';
$opts['navigation'] = 'UDG';
$opts['display'] = array(
	'query' => false,
	'sort'  => false,
	'time'  => false
	);

$fdd["name"] = array(
	'name'=>'Name',
	'select'=>'T',
	'type'=>'blob',
	'maxlen'=>65535,
	'nowrap'=>false,
	'required'=>true,
	'textarea'=>array(
		'rows'=>5,
		'cols'=>50,
		'wrap'=>'virtual'
	),
	'sort'=>true
);

$fdd["address1"] = array(
	'name'=>'Address1',
	'select'=>'T',
	'type'=>'blob',
	'maxlen'=>65535,
	'nowrap'=>false,
	'required'=>true,
	'textarea'=>array(
		'rows'=>5,
		'cols'=>50,
		'wrap'=>'virtual'
	),
	'sort'=>true
);

$fdd["address2"] = array(
	'name'=>'Address2',
	'select'=>'T',
	'type'=>'blob',
	'maxlen'=>65535,
	'nowrap'=>false,
	'required'=>true,
	'textarea'=>array(
		'rows'=>5,
		'cols'=>50,
		'wrap'=>'virtual'
	),
	'sort'=>true
);

$fdd["county"] = array(
	'name'=>'County',
	'select'=>'T',
	'type'=>'blob',
	'maxlen'=>65535,
	'nowrap'=>false,
	'required'=>true,
	'textarea'=>array(
		'rows'=>5,
		'cols'=>50,
		'wrap'=>'virtual'
	),
	'sort'=>true
);

$fdd["postcode"] = array(
	'name'=>'Postcode',
	'select'=>'T',
	'type'=>'blob',
	'maxlen'=>65535,
	'nowrap'=>false,
	'required'=>true,
	'textarea'=>array(
		'rows'=>5,
		'cols'=>50,
		'wrap'=>'virtual'
	),
	'sort'=>true
);

$fdd["customer"] = array(
	'name'=>'Customer',
	'select'=>'T',
	'type'=>'int',
	'maxlen'=>4,
	'nowrap'=>false,
	'required'=>true,
	'sort'=>true
);

$fdd["seats"] = array(
	'name'=>'Seats',
	'select'=>'T',
	'type'=>'blob',
	'maxlen'=>65535,
	'nowrap'=>false,
	'required'=>true,
	'textarea'=>array(
		'rows'=>5,
		'cols'=>50,
		'wrap'=>'virtual'
	),
	'sort'=>true
);

$fdd["number"] = array(
	'name'=>'Number',
	'select'=>'T',
	'type'=>'int',
	'maxlen'=>4,
	'nowrap'=>false,
	'required'=>true,
	'default'=>'0',
	'sort'=>true
);

$fdd["class"] = array(
	'name'=>'Class',
	'select'=>'T',
	'type'=>'blob',
	'maxlen'=>65535,
	'nowrap'=>false,
	'required'=>true,
	'textarea'=>array(
		'rows'=>5,
		'cols'=>50,
		'wrap'=>'virtual'
	),
	'sort'=>true
);


$opts['fdd'] = $fdd;

$opts['language']= $HTTP_SERVER_VARS['HTTP_ACCEPT_LANGUAGE'];
require_once 'viewer.class.php';
$MyForm = new phpMyEdit($opts);
?>
<font size="1"><a href="admin.php">Back to Admin</A></font>
  </div>
