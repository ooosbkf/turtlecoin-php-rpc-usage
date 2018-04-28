<?php
require '../vendor/autoload.php';

use TurtleCoin\Walletd;

$config = [
    'rpcHost'     => 'http://127.0.0.1',
    'rpcPort'     => 8070,
    'rpcPassword' => 'test',
];

$walletAddress = "TRTLv1q297ZUArny7sRygKisFervypBQgfNsRYBjP8bKFxRFNwBReJUfXbtKGmX1P5X4ZLoHWUSRfdUM2aBuQjAYeSG7qgtt54A";

$walletd = new Walletd\Client($config);

$status = $walletd->getStatus()->getBody()->getContents();
$bal = $walletd->getBalance()->getBody()->getContents();
$decstats = json_decode($status, true);
$decbal = json_decode($bal, true);

echo "</p>" . $decstats["result"]["blockCount"] . "/" . $decstats["result"]["knownBlockCount"];
echo "</p>Available balance " . $decbal["result"]["availableBalance"] / 100 . "<br>Locked balance " . $decbal["result"]["lockedAmount"] / 100;
if (isset($_GET["create"])) {
#  $testt = $walletd->createDelayedTransaction($anonymity, $transfers, $fee)->getBody()->getContents();;
#  echo $testt;
 $senddel = $walletd->sendDelayedTransaction("7e32d920e3584395e616182945e4b225e9ec51d9b9f4226dd827b2df1d6e7cd2")->getBody()->getContents();
 echo $senddel;
}
$outt = $walletd->getDelayedTransactionHashes()->getBody()->getContents();
$decout = json_decode($outt, true);

for ($i=0; $i < count($decout["result"]["transactionHashes"]); $i++) {
  echo "<br>" . $decout["result"]["transactionHashes"][$i] . "</p>";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $anonymity = 1;
  $fee = 10;
  $transfers = [
     [
        "address" => $_POST["address"],
        "amount"  => intval($_POST["amount"]) * 100
     ]
  ];
  $testt = $walletd->createDelayedTransaction($anonymity, $transfers, $fee)->getBody()->getContents();
  echo $testt;
}
 ?>
 <form action="pay.php" method="post">
   <input type="text" name="address" placeholder="TRTL...">
   <input type="text" name="amount" placeholder="Amount">
   <input type="submit">
 </form>
<!-- <button onclick="javascript:window.location = 'pay.php?create=true'">Test transact</button> -->
