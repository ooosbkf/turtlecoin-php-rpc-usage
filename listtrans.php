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
//    print_r($decltrans["result"]["items"]);
    $pcount = count($decltrans["result"]["items"]);
    $cadd = count($baddrs);
    for ($i=0; $i < $pcount; $i++) {
      $tcount = count($decltrans["result"]["items"][$i]["transactions"][0]["transfers"]);
      for ($j=0; $j < $tcount; $j++) {
        for ($k=0; $k < $cadd; $k++) {
          if ($baddrs[$k] == $decltrans["result"]["items"][$i]["transactions"][0]["transfers"][$j]["address"]) {
            if ($decltrans["result"]["items"][$i]["transactions"][0]["transfers"][$j]["amount"] < 0) {
              echo "Outgoing: " . "<a target='_blank' href='https://turtle-coin.com/?hash=" . $decltrans["result"]["items"][$i]["transactions"][0]["transactionHash"] . "#blockchain_transaction'>" . $decltrans["result"]["items"][$i]["transactions"][0]["transactionHash"] . "</a><br>";
            }
            else {
              echo "Incoming: " . "<a target='_blank' href='https://turtle-coin.com/?hash=" . $decltrans["result"]["items"][$i]["transactions"][0]["transactionHash"] . "#blockchain_transaction'>" . $decltrans["result"]["items"][$i]["transactions"][0]["transactionHash"] . "</a><br>";
            }
          }
        }
      }
      //echo "<a target='_blank' href='https://turtle-coin.com/?hash=" . $decltrans["result"]["items"][$i]["transactions"][0]["transfers"] . "#blockchain_transaction'>" . $decltrans["result"]["items"][$i]["transactions"][0]["transactionHash"] . "</a><br>";
    }
    if ($pcount == 0) {
      echo "No transactions found";
    }
     ?>
  </body>
</html>
