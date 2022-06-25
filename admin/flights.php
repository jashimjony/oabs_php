<div class="main">
<?php
// Flights Version 2
include ("../Connections/Database.php");
$opts["key"] = "number";
$opts["key_type"] = "int";
$opts["sort_field"] = "number";
$opts['inc'] = 20;
$opts['options'] = 'AD';
$opts['multiple'] = '4';
$opts['navigation'] = 'UDG';
$opts['display'] = array(
	'query' => false,
	'sort'  => false,
	'time'  => false
	);
$fdd["number"] = array(
	'name'=>'Number',
	'select'=>'T',
	'type'=>'int',
	'maxlen'=>4,
	'nowrap'=>false,
	'required'=>false,
	'sort'=>true
);

$fdd["Dest"] = array(
	'name'=>'Dest',
	'select'=>'T',
	'type'=>'blob',
	'maxlen'=>500,
	'nowrap'=>false,
	'required'=>true,
	'textarea'=>array(
		'rows'=>3,
		'cols'=>50,
		'wrap'=>'virtual'
	),
	'sort'=>true
);

$fdd["datetime"] = array(
	'name'=>'Datetime',
	'select'=>'T',
	'type'=>'timestamp',
	'maxlen'=>14,
	'nowrap'=>false,
	'required'=>true,
	'sort'=>true
);

$fdd["passb"] = array(
	'name'=>'Passb',
	'select'=>'T',
	'type'=>'int',
	'maxlen'=>4,
	'nowrap'=>false,
	'required'=>true,
	'default'=>'0',
	'sort'=>true
);

$fdd["passf"] = array(
	'name'=>'Passf',
	'select'=>'T',
	'type'=>'int',
	'maxlen'=>4,
	'nowrap'=>false,
	'required'=>true,
	'default'=>'0',
	'sort'=>true
);

$fdd["passe"] = array(
	'name'=>'Passe',
	'select'=>'T',
	'type'=>'int',
	'maxlen'=>4,
	'nowrap'=>false,
	'required'=>true,
	'default'=>'0',
	'sort'=>true
);

$fdd["cost"] = array(
	'name'=>'Cost',
	'select'=>'T',
	'type'=>'int',
	'maxlen'=>4,
	'nowrap'=>false,
	'required'=>true,
	'default'=>'0',
	'sort'=>true
);

$fdd["priceb"] = array(
	'name'=>'Priceb',
	'select'=>'T',
	'type'=>'int',
	'maxlen'=>4,
	'nowrap'=>false,
	'required'=>true,
	'default'=>'0',
	'sort'=>true
);

$fdd["pricef"] = array(
	'name'=>'Pricef',
	'select'=>'T',
	'type'=>'int',
	'maxlen'=>4,
	'nowrap'=>false,
	'required'=>true,
	'default'=>'0',
	'sort'=>true
);

$fdd["pricee"] = array(
	'name'=>'Pricee',
	'select'=>'T',
	'type'=>'int',
	'maxlen'=>4,
	'nowrap'=>false,
	'required'=>true,
	'default'=>'0',
	'sort'=>true
);

$fdd["senior"] = array(
	'name'=>'Senior',
	'select'=>'T',
	'type'=>'int',
	'maxlen'=>4,
	'nowrap'=>false,
	'required'=>true,
	'default'=>'0',
	'sort'=>true
);

$fdd["child"] = array(
	'name'=>'Child',
	'select'=>'T',
	'type'=>'int',
	'maxlen'=>4,
	'nowrap'=>false,
	'required'=>true,
	'default'=>'0',
	'sort'=>true
);


$opts['fdd'] = $fdd;


/*
$opts['triggers']['insert']['before']='flights.TIB.inc';
$opts['triggers']['insert']['after'] ='flights.TIA.inc';
$opts['triggers']['update']['before']='flights.TUB.inc';
$opts['triggers']['update']['after'] ='flights.TUA.inc';
$opts['triggers']['delete']['before']='flights.TDB.inc';
$opts['triggers']['delete']['after'] ='flights.TDA.inc';
*/

$opts['language']= $HTTP_SERVER_VARS['HTTP_ACCEPT_LANGUAGE'];
require_once 'viewer.class.php';
$MyForm = new phpMyEdit($opts);
?>
  </div>
