function notify(message, title) {
  if (Notification.permission !== "granted")
    Notification.requestPermission();
  else {
    var notification = new Notification(title, {
      icon: 'img/transaction.png',
      body: message,
    });

    notification.onclick = function () {
      nh();
    };
    setTimeout(function () {
      nh()
    }, 5000);
  }
}
function cnotify() {
  if (Notification.permission !== "granted")
    Notification.requestPermission();
  else {
    var notification = new Notification("Transaction cancelled", {
      icon: 'img/transaction.png',
      body: "You cancelled your transaction",
    });
    notification.onclick = function () {
      ni();
    };
    setTimeout(function () {
      ni()
    }, 5000);
  }
}
function ni() {
  window.open("index.php");
  window.close();
}
function nh() {
  window.open("history.php");
  window.close();
}
