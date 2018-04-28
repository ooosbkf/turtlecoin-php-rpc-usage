<?php
require '../vendor/autoload.php';
session_start();
use TurtleCoin\Walletd;

$config = [
    'rpcHost'     => $_SESSION["ipconf"],
    'rpcPort'     => intval($_SESSION["portconf"]),
    'rpcPassword' => $_SESSION["passconf"],
];
$walletd = new Walletd\Client($config);

echo $walletd->deleteDelayedTransaction($_GET["tx"])->getBody()->getContents();
