<?php
require '../vendor/autoload.php';

session_start();

use chillerlan\QRCode\QRCode;

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
    <title>Maintain</title>
  </head>
  <body>
    <a href="index.php">Back</a><br>
    Create address
    <form action="maintain.php" method="post">
      <input type="hidden" name="method" value="gen">
      <input type="submit" value="Generate">
    </form>
    Delete address !WARNING!: You can't recover an address (until now)<!-- TODO: Recover Address functiom -->
    <form action="maintain.php" method="post">
      <input type="hidden" name="method" value="del">
      <input type="text" name="addr" placeholder="Address to delete">
      <input type="submit" value="Delete">
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($_POST["method"] == "gen") {
        $gen = $walletd->createAddress()->getBody()->getContents();
        $decgen = json_decode($gen, true);
        $naddr = $decgen["result"]["address"];
        echo $naddr;
        echo '<br><img src="'.(new QRCode)->render($naddr).'" />';
      }
      elseif ($_POST["method"] == "del") {
        $resp = $walletd->deleteAddress($_POST["addr"])->getBody()->getContents();
        $decresp = json_decode($resp, true);
        if (isset($decresp["error"])) {
          echo "<script>alert('The address is invalid, or doesn\'t exists!')</script>";
        }
        else {
          echo "<script>alert('Address deleted!')</script>";
        }
      }
    }
     ?>
  </body>
</html>
