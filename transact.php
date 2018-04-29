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
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Transaction</title>
  </head>
  <body>
    <a href="index.php">Back</a>
    <form id="form" action="transact.php" method="post">
      <!-- Payment options -->
      <input type="text" name="rec" placeholder="Receiver" required><br>
      <input type="text" name="amount" placeholder="Amount" required><br>
      <input type="text" name="fee" placeholder="Fee (min 1)" required><br>
      <input type="number" min="0" name="anon" placeholder="Anonymity (1 to ∞)" required><br>
      <input type="text" name="extra" placeholder="Extra (optional)"><br>
      <input type="text" name="pid" placeholder="Payment ID (optional)"><br>
      <?php
      #Check if sender address needs to be set
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
      #Transact confirm response
      $strans = $walletd->sendDelayedTransaction($_GET["send"])->getBody()->getContents();
      $decstrans = json_decode($strans, true);
      echo "Transaction sent to blockchain: <a target='_blank' href='https://turtle-coin.com/?hash=" . $_GET["send"] . "#blockchain_transaction'>Watch status</a>";
    }
    elseif (isset($_GET["cancel"])) {
      #Transact cancelled response
      $strans = $walletd->deleteDelayedTransaction($_GET["cancel"])->getBody()->getContents();
      $decstrans = json_decode($strans, true);
      if (!isset($decstrans["error"])) {
        echo "Transaction cancelled";
      }
    }
    else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      #Get variables and set them
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
      #Check if address settings is required
      if (strlen($_POST["caddr"]) != 0) {
        $changeAddress = array($_POST["caddr"]);
      }
      else {
        $changeAddress = array();
      }
      $extra = $_POST["extra"];
      $pid = $_POST["pid"];
      #Check if extra, paymentid or both are set
      if (strlen($extra) != 0 && strlen($pid) != 0) {
        #Check for address to pay from
        if (count($changeAddress) != 0) {
          $trans = $walletd->createDelayedTransaction($anonymity, $transfers, $fee, $changeAddress, 0, $extra, $pid)->getBody()->getContents();
        }
        else {
          $trans = $walletd->createDelayedTransaction($anonymity, $transfers, $fee, null, 0, $extra, $pid)->getBody()->getContents();
        }
        #Decode
        $dectrans = json_decode($trans, true);
        #Check for errors
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
        #Confirm transaction
        $yeslink = 'javascript:window.location = "transact.php?send=' . $dectrans["result"]["transactionHash"] . '"';
        $nolink = 'javascript:window.location = "transact.php?cancel=' . $dectrans["result"]["transactionHash"] . '"';
        echo "Are you sure you want to send " . $amount / 100 . "trtl to " . $rec . " with a fee of " . $fee / 100 . "trtl and an anonymity level of " . $anonymity . "<br><button onclick='" . $yeslink . "'>Yes</button><button onclick='" . $nolink . "'>No</button>";
        }
        #Do the same with paymentid and no extra
      elseif (strlen($extra) != 0 && strlen($pid) == 0) {
        if (count($changeAddress) != 0) {
          $trans = $walletd->createDelayedTransaction($anonymity, $transfers, $fee, $changeAddress, 0, $extra, null)->getBody()->getContents();
        }
        else {
          $trans = $walletd->createDelayedTransaction($anonymity, $transfers, $fee, null, 0, $extra, null)->getBody()->getContents();
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
      #Do the same with extra and no paymentid
      elseif (strlen($extra) == 0 && strlen($pid) != 0) {
        if (count($changeAddress) != 0) {
          $trans = $walletd->createDelayedTransaction($anonymity, $transfers, $fee, $changeAddress, 0, null, $pid)->getBody()->getContents();
        }
        else {
          $trans = $walletd->createDelayedTransaction($anonymity, $transfers, $fee, null, 0, null, $pid)->getBody()->getContents();
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
      #Do the same without paymentid and extra
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
