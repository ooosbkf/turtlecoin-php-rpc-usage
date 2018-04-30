<?php
#Load libs
require '../vendor/autoload.php';

#Start session
session_start();

#Config
use TurtleCoin\Walletd;

$config = [
    'rpcHost'     => $_SESSION["ipconf"],
    'rpcPort'     => intval($_SESSION["portconf"]),
    'rpcPassword' => $_SESSION["passconf"],
];

$walletd = new Walletd\Client($config);

$status = $walletd->getStatus()->getBody()->getContents();
$addrs = $walletd->getAddresses()->getBody()->getContents();

$decstats = json_decode($status, true);
$decaddrs = json_decode($addrs, true);

$addresses = $decaddrs["result"]["addresses"];
$fcount = count($decaddrs["result"]["addresses"]);
$baddrs = array();
$bcount = intval($decstats["result"]["knownBlockCount"]);
$fbi = 1;
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
    <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet">
    <link rel="stylesheet" href="css/address.css">
  </head>
  <body>
    <?php
    for ($i=0; $i < $fcount; $i++) {
      array_push($baddrs, $addresses[$i]);
    }
    $ltrans = $walletd->getTransactions($bcount, $fbi, null, $baddrs)->getBody()->getContents();
    $decltrans = json_decode($ltrans, true);
    $pcount = count($decltrans["result"]["items"]);
    for ($i=0; $i < $pcount; $i++) {
      echo "<a target='_blank' href='https://turtle-coin.com/?hash=" . $decltrans["result"]["items"][$i]["transactions"][0]["transactionHash"] . "#blockchain_transaction'>" . $decltrans["result"]["items"][$i]["transactions"][0]["transactionHash"] . "</a><br>";
    }
     ?>
  </body>
</html>
