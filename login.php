<?php
#Start session
session_start();
#Check session
if (isset($_SESSION["passconf"]) && isset($_SESSION["ipconf"]) && isset($_SESSION["portconf"])) {
  header('Location: index.php');
}
#Check for request
elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
  $_SESSION["passconf"] = $_POST["password"];
  $_SESSION["ipconf"] = $_POST["ip"];
  $_SESSION["portconf"] = $_POST["port"];
  header('Location: index.php');
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Login</title>
    <style media="screen">
      body {
        background-color: #5dcc63;
      }
      #loginform {
        text-align: center;
      }
    </style>
  </head>
  <body>
    <form id="loginform" action="login.php" method="post">
      <input type="url" name="ip" value="http://127.0.0.1"><!-- Standart values -->
      <input type="number" name="port" value="8070"><!-- Standart values -->
      <input type="password" name="password" placeholder="Password"></p>
      <input type="submit" value="Login">
    </form>
  </body>
</html>
