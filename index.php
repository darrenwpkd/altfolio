<?php
$json = file_get_contents(dirname(__FILE__) . '/coins.json');
$coins = json_decode($json,true);

date_default_timezone_set('Europe/London');
$new = array();

if (!empty($_POST)) {

	if (isset($_POST['exchange'])) {
	
		if ($_POST['exchange']==='bittrex' || $_POST['exchange']==='cryptsy') {
			$new = array(
				"exchange" => $_POST['exchange']
			);
			
			$new['coin'] = htmlspecialchars($_POST['coin'],ENT_QUOTES,'utf-8');
			$new['amount'] = ($_POST['amount']);
			$new['rate'] = ($_POST['rate']);
		}
	}
}
/* $new = array(
	"exchange" => "bittrex",
	"coin" => "OK",
	"amount" => "5000",
	"rate" => "0.00000168"
); */

if (!empty($new)) {
	$hasBeen = false;
	foreach($coins as $found => $coin) {
		if ($coin['exchange']==$new['exchange']) {
			if ($coin['coin']==$new['coin']) {
				if ($coins[$found]["amount"]<$new["amount"]) {
					$lowPerc = $coins[$found]["amount"] / $new["amount"];
					$highPerc = 1 - $lowPerc;
					$rate1 = $coins[$found]["rate"] * $lowPerc;
					$rate2 = $new["rate"] * $highPerc;
				} else {
					$lowPerc = $new["amount"] / $coins[$found]["amount"];
					$highPerc = 1 - $lowPerc;
					$rate1 = $new["rate"] * $lowPerc;
					$rate2 = $coins[$found]["rate"] * $highPerc;				
				}
				$hasBeen = true;	
				$coins[$found]["rate"] = round($rate1 + $rate2,8);
				$coins[$found]["amount"] += $new["amount"]; 			
			
				break;
			}
		}
	}
	
	if ($hasBeen===false) {
		$new['date'] = time();
		$coins[] = $new;
	}
	
	// make backup
	$safe = file_get_contents(dirname(__FILE__) . "/coins.json");
	
	file_put_contents(dirname(__FILE__) . "/coins.json",json_encode($coins));
	
	file_put_contents(dirname(__FILE__) . "/coins-" . time() . ".backup",$safe);
}


?>
<!DOCTYPE html>
<html>
	<head>
		<title>Trader</title>
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/bootstrap-theme.min.css" />
		<script type="text/javascript" src="js/jquery-2.1.4.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<style>
			#deleterow {
				background-color: #222;
				border-radius: 12px;
				padding: 4px 6px;
				padding-top: 3px;
				padding-bottom: 4px;
				line-height: 1em;
				color: #fff;
				text-align: center;
				font-weight: bold;
				float: right;
				cursor: pointer;
			}
			table tr td {
				-webkit-transition: 0.5s all;
				background-color: white;
			}			
		
			table tr[data-type=positive] td {
				background-color: #AAFF66;
				color: black;
			}
			table tr[data-type=negative] td {
				background-color: #DD4444;
				color: white;
			}

			table tr[data-type=loading] td {
				color: #8f8f8f;
			}
			
		</style>
	</head>
	<body>
		<div class="container">
			<h1>Altfolio v0.1 - by Coinfinder</h1>
			<table class="table table-condensed">
				<thead>
					<tr>
						<th>Exchange</th>
						<th>Date</th>
						<th>Amount</th>
						<th>Coin</th>
						<th>Rate</th>
						<th>Spent</th>
						<th>Latest Rate</th>
						<th>Current Balance</th>
						<th>Profit BTC</th>
						<th>Profit %</th>
					</tr>
				</thead>
				<tbody>
				<?php if (!empty($coins)) : ?>
					<?php foreach($coins as $coin) : ?>
						<tr data-exchange="<?php echo $coin['exchange']; ?>" 
							data-coin="<?php echo $coin['coin']; ?>" 
							data-amount="<?php echo $coin['amount']; ?>"
							data-rate="<?php echo $coin['rate']; ?>">
							<td><?php echo ucwords($coin['exchange']); ?> <span id='deleterow'>x</span></td>
							<td><?php echo date("Y-m-d",$coin['date']); ?></td>
							<td><?php echo number_format($coin['amount'],8,'.',''); ?></td>
							<td><?php echo $coin['coin']; ?></td>
							<td><?php echo number_format($coin['rate'],8,'.',''); ?></td>
							<td><?php echo number_format($coin['amount'] * $coin['rate'],8,'.',''); ?></td>
							<td data-latest></td>
							<td data-balance></td>
							<td data-profit-btc></td>
							<td data-profit-perc></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="10">No Coins Yet!</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
			<hr />
			<div class="panel panel-info">
				<div class="panel-heading"><h2 class="panel-title">Add Trade</h2></div>
				<div class="panel-body">
				<form action="#" method="post">
					<div class="col-md-3">
						<div class="input-group">
							<label>Exchange</label>
							<select class="form-control" name="exchange">
								<option value="cryptsy">Cryptsy</option>
								<option value="bittrex">Bittrex</option>
							</select>
						</div>			
					</div>

					<div class="col-md-3">
						<div class="input-group">
							<label>Coin</label>
							<input type="text" maxlength="5" size="5" name="coin" class="form-control" />
						</div>			
					</div>

					<div class="col-md-3">						
						<div class="input-group">
							<label>Amount</label>
							<input type="text" class="form-control" name="amount" />
						</div>			
					</div>

					<div class="col-md-3">
						<div class="input-group">
							<label>Rate</label>
							<input type="text" class="form-control" name="rate" />
						</div>			
					</div>
					
					<div class="pull-right" style="padding-top: 10px;">
						<button type="submit" class="btn btn-primary">Add Trade</button>
					</div>
				</form>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				$("[data-exchange]").each(function() {
					update($(this),$(this).data());
				});
				
				$(document).on("click","#deleterow",function() {
					var row = $(this).closest("tr");
					if (!window.confirm("Are you sure you wish to delete this row?")) {
						return false;
					}
					
					$.post("delete.php",row.data(),function(data) {
						row.slideUp(function() {
							row.remove();
						});
					});
				});
			});
			
			function update(row,sdata) {
				$(row).attr("data-type","loading");
				$.post("get.php",sdata,function(data) {
					$("[data-latest]",row).text(data.latest);
					$("[data-balance]",row).text(data.balance);
					$("[data-profit-btc]",row).text(data.profitbtc);
					$("[data-profit-perc]",row).text(data.profitperc + "%");
					$(row).attr("data-type",data.profittype).attr("data-perc",data.profitperc);
		
					$("[data-exchange]").each(function() {
						var thisrow = this;
						var done = false;
						var before = null;
						
						$("[data-exchange]").each(function() {
							if (done!=false) { return; }
							if (this==thisrow) {
								return;
							}
							if (parseFloat($(this).data("perc"))>parseFloat($(thisrow).data("perc"))) {
								done = true;
								return;
							}
							before = this;
						});
					
						if (before!==null) {
							$(thisrow).insertAfter(before);
						}
					});
					
					setTimeout(function() {
						update(row,sdata)
					},30000);
				},"json");			
			}
		</script>
	</body>
</html>
