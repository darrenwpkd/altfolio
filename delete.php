<?php
if (!isset($_POST['coin'])) {
	exit();
}

if (!isset($_POST['exchange'])) {
	exit();
}

$json = file_get_contents(dirname(__FILE__) . '/coins.json');
$coins = json_decode($json,true);

$newcoins = array();

foreach($coins as $id => $coin) {
	if ($coin['exchange']===$_POST['exchange'] && $coin['coin']===$_POST['coin']) {
		continue;
	}
	$newcoins[$id] = $coin;
}

// make backup
$safe = file_get_contents(dirname(__FILE__) . "/coins.json");
	
file_put_contents(dirname(__FILE__) . "/coins.json",json_encode($newcoins));
	
file_put_contents(dirname(__FILE__) . "/coins-" . time() . ".backup",$safe);

?>

