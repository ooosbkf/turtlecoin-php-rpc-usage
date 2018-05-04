<?php
#Start session
session_start();

if (isset($_GET["logout"])) {
  session_destroy();
  header('Location: index.php');
}
#Load libs
require '../vendor/autoload.php';

#Check Session
if (!isset($_SESSION["passconf"]) || !isset($_SESSION["ipconf"]) || !isset($_SESSION["portconf"])) {
  header('Location: login.php');
}
/*
if (!isset($_SESSION["thistory"])) {
  $_SESSION["thistory"] = array(0 => "null");
}
*/

#Config
use TurtleCoin\Walletd;

$config = [
    'rpcHost'     => $_SESSION["ipconf"],
    'rpcPort'     => intval($_SESSION["portconf"]),
    'rpcPassword' => $_SESSION["passconf"],
];
$walletd = new Walletd\Client($config);

#JSON responses
$status = $walletd->getStatus()->getBody()->getContents();
$bal = $walletd->getBalance()->getBody()->getContents();

#Decode
$decstats = json_decode($status, true);
$decbal = json_decode($bal, true);
$decstats = json_decode($status, true);

#Balances
$balance = intval($decbal["result"]["availableBalance"]) / 100;
$lbalance = intval($decbal["result"]["lockedAmount"]) / 100;

#Stats
$sblocks = $decstats["result"]["blockCount"];
$bcount = $decstats["result"]["knownBlockCount"];

#Market value
$usd = 'https://tradesatoshi.com/api/public/getmarkethistory?market=TRTL_USDT&count=1';
$response = json_decode(file_get_contents($usd, false), true);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Home</title>
    <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
  </head>
  <body>
    <!-- Output stats and balance -->
    <div id="stats">
    Your available balance is: <?php echo $balance . " TRTL" . " (" . $balance * $response["result"][0]["price"] . " $)"; ?> <br>
    Your locked balance is: <?php echo $lbalance . " TRTL" . " (" . $lbalance * $response["result"][0]["price"] . " $)"; ?><br>
    Daemon status: <?php echo $sblocks . " of " . $bcount . " blocks synced"; ?></p>
    </div>
    <!-- Links to the sites -->
    <div id="main-container">
    <span id="fimgs"><a href="transact.php"><img src="img/transaction.png" alt="Make a transaction"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="address.php"><img src="img/address.png" alt="Show addresses"></a></span><br>
    &nbsp;&nbsp;&nbsp;<caption>Make a transaction</caption>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<caption>Show addresses</caption></p>
    <span id="fimgs"><a href="maintain.php"><img src="img/maintain.png" alt="Manage addresses"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="history.php"><img src="img/history.png" alt="Session transaction history"></a></span></p>
    &nbsp;&nbsp;&nbsp;<caption>Manage addresses</caption>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<caption>Transaction history</caption></p>
    <br>
  </div>
    <a href="index.php?logout=true"><img height="3%" width="3%" src="img/logout.png" alt="Logout"></a><br>
    <div><caption>Logout</caption></div>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
  if (!Notification) {
    alert('Desktop notifications not available in your browser.');
    return;
  }
  if (Notification.permission !== "granted")
    Notification.requestPermission();
});
    </script>
  </body>
</html>
