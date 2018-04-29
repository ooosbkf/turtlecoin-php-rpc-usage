<?php
#Load libs
require '../vendor/autoload.php';

#Start session
session_start();

#Check session
if (!isset($_SESSION["passconf"]) || !isset($_SESSION["ipconf"]) || !isset($_SESSION["portconf"])) {
  header('Location: login.php');
}
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
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Home</title>
  </head>
  <body>
    <!-- Output stats and balance -->
    Daemon status: <?php echo $sblocks . " of " . $bcount . " blocks synced"; ?><br>
    Your available balance is: <?php echo $balance; ?> <br>
    Your locked balance is: <?php echo $lbalance; ?></p>
    <!-- Links to the sites -->
    <a href="transact.php"><img src="none" alt="Make a transaction"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="address.php"><img src="none" alt="Show addresses"></a><br>
    <a href="maintain.php"><img src="none" alt="Manage addresses"></a>
  </body>
</html>
