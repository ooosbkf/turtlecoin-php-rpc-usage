<?php
require '../vendor/autoload.php';
session_start();
if (!isset($_SESSION["passconf"]) || !isset($_SESSION["ipconf"]) || !isset($_SESSION["portconf"])) {
  header('Location: login.php');
}
use TurtleCoin\Walletd;

$config = [
    'rpcHost'     => $_SESSION["ipconf"],
    'rpcPort'     => intval($_SESSION["portconf"]),
    'rpcPassword' => $_SESSION["passconf"],
];
$walletd = new Walletd\Client($config);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <a href="index.php">Back</a>
    <form id="form" action="transact.php" method="post">
      <input type="text" name="rec" placeholder="Receiver" required><br>
      <input type="text" name="amount" placeholder="Amount" required><br>
      <input type="text" name="fee" placeholder="Fee (min 1)" required><br>
      <input type="number" min="0" name="anon" placeholder="Anonymity (1 to ∞)" required><br>
      <input type="text" name="extra" placeholder="Extra (optional)"><br>
      <input type="text" name="pid" placeholder="Payment ID (optional)"><br>
      <?php
      $addrs = $walletd->getAddresses()->getBody()->getContents();
      $decaddrs = json_decode($addrs, true);
      $fcount = count($decaddrs["result"]["addresses"]);
      if ($fcount > 1) {
        echo '<input type="text" name="caddr" size="55%" placeholder="Address to send from (because you have two addresses saved)" required>';
      }
      else {
        echo '<input type="hidden" name="caddr">';
      }
       ?>
      <input type="submit" value="Pay">
    </form>
    <?php
    if (isset($_GET["send"])) {
      $strans = $walletd->sendDelayedTransaction($_GET["send"])->getBody()->getContents();
      $decstrans = json_decode($strans, true);
      echo "Transaction sent to blockchain: <a target='_blank' href='https://turtle-coin.com/?hash=" . $_GET["send"] . "#blockchain_transaction'>Watch status</a>";
    }
    elseif (isset($_GET["cancel"])) {
      $strans = $walletd->deleteDelayedTransaction($_GET["cancel"])->getBody()->getContents();
      $decstrans = json_decode($strans, true);
      if (!isset($decstrans["error"])) {
        echo "Transaction cancelled";
      }
    }
    else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $anonymity = intval($_POST["anon"]);
      $fee = intval($_POST["fee"]) * 100;
      $rec = $_POST["rec"];
      $amount = intval($_POST["amount"]) * 100;
      $transfers = [
         [
            "address" => $rec,
            "amount"  => $amount
         ]
      ];
      if (strlen($_POST["caddr"]) != 0) {
        $changeAddress = array($_POST["caddr"]);
      }
      else {
        $changeAddress = array();
      }
      $extra = $_POST["extra"];
      $pid = $_POST["pid"];
      if (strlen($extra) != 0 && strlen($pid) != 0) {
        if (strlen($changeAddress) != 0) {
          # code...
        }
      }
      elseif (strlen($extra) != 0 && strlen($pid) == 0) {
        if (strlen($changeAddress) != 0) {
          # code...
        }
      }
      elseif (strlen($extra) == 0 && strlen($pid) != 0) {
        if (strlen($changeAddress) != 0) {
          # code...
        }
      }
      else {
        if (count($changeAddress) != 0) {
          $trans = $walletd->createDelayedTransaction($anonymity, $transfers, $fee, $changeAddress)->getBody()->getContents();
        }
        else {
          $trans = $walletd->createDelayedTransaction($anonymity, $transfers, $fee)->getBody()->getContents();
        }
        $dectrans = json_decode($trans, true);
        if (isset($dectrans["error"])) {
          if ($dectrans["error"]["message"] == "Wrong amount") {
            die("<script>alert('Insufficient funds');</script>");
          }
          elseif ($dectrans["error"]["message"] == "Bad address"){
            die("<script>alert('The sender/receiver address is invalid');</script>");
          }
          else {
            die("<script>alert('" . $dectrans["error"]["message"] . "');</script>");
          }
        }
        $yeslink = 'javascript:window.location = "transact.php?send=' . $dectrans["result"]["transactionHash"] . '"';
        $nolink = 'javascript:window.location = "transact.php?cancel=' . $dectrans["result"]["transactionHash"] . '"';
        echo "Are you sure you want to send " . $amount / 100 . "trtl to " . $rec . " with a fee of " . $fee / 100 . "trtl and an anonymity level of " . $anonymity . "<br><button onclick='" . $yeslink . "'>Yes</button><button onclick='" . $nolink . "'>No</button>";
      }
    }
  }
     ?>
  </body>
</html>
