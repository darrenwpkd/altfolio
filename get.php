<?php

$rate = 0;
switch($_POST['exchange']) {
	case "bittrex": {
		$url = 'https://bittrex.com/api/v1.1/public/getticker?market=BTC-' . strtoupper($_POST['coin']);
		$json = file_get_contents($url);
		$output = json_decode($json,true);
		$rate = $output["result"]["Bid"];
		break;
	}
	case "cryptsy": {
		$url = 'https://www.cryptsy.com/api/v2/markets/' . strtoupper($_POST['coin']) . '_BTC/ticker';
		$json = file_get_contents($url);
		$output = json_decode($json,true);
		$rate = $output["data"]["bid"];
		break;
	}
}

$total = $rate * $_POST['amount'];
$oldtotal = $_POST['rate'] * $_POST['amount'];

$diffbtc = $total - $oldtotal;

$diffperc = round(($diffbtc / $oldtotal) * 100,2);

if ($diffperc>0) {
	$type = "positive";
} 

if ($diffperc==0) {
	$type = "neutral";
} 

if ($diffperc<0) {
	$type = "negative";
}

$output = array(
	"latest" => number_format($rate,8,'.',''),
	"balance" => number_format($total,8,'.',''),
	"profitbtc" => number_format($diffbtc,8,'.',''),
	"profitperc" => $diffperc,
	"profittype" => $type
);

echo json_encode($output);

