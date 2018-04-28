<?php
session_start();
if (isset($_SESSION["passconf"]) && isset($_SESSION["ipconf"]) && isset($_SESSION["portconf"])) {
  header('Location: index.php');
}
elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
  $_SESSION["passconf"] = $_POST["password"];
  $_SESSION["ipconf"] = $_POST["ip"];
  $_SESSION["portconf"] = $_POST["port"];
  $_SESSION["addrconf"] = $_POST["address"];
  header('Location: index.php');
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Login</title>
  </head>
  <body>
    <form action="login.php" method="post">
      <input type="url" name="ip" value="http://127.0.0.1">
      <input type="number" name="port" value="8070">
      <input type="password" name="password" placeholder="Password">
      <input type="submit" value="Login">
      <input type="text" name="address" placeholder="Address (optional)">
    </form>
  </body>
</html>
