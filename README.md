# Info
This repo shows you most of the ways to use this <a href="https://github.com/turtlecoin/turtlecoin-walletd-rpc-php/">library</a>
But it can also be used to send and receive transactions and the other stuff (design will follow in future commits).
# Requirements
Requirements: composer, webserver with php 7.2 and the <a href="https://github.com/turtlecoin/turtlecoin/releases">turtlecoin wallet</a>.
# Installation
clone this repository into the /var/www/html(Linux) directory or in the htdocs directory(Windows, Mac);
The the easiest way to install all librarys for php is running this command: <code>composer require chillerlan/php-qrcode turtlecoin/turtlecoin-walletd-rpc-php</code>
# Using
You are now ready to visit your webserver and do your stuff, but before that we have to start the wallet daemon.
You don't have a wallet? run on terminal/cmd<code>./walletd -g -w walletname</code> on Linux/Mac and <code>walletd.exe -g -w walletname</code> on Windows.
Have a wallet already(or generated one yet)? you'r on the target line, just run <code>./walletd -w walletname --rpc-password thestrongestpasswordeversonoonecancrackit --daemon-address public.turtlenode.io</code> and <code>walletd.exe -w walletname --rpc-password thestrongestpasswordeversonoonecancrackit --daemon-address public.turtlenode.io</code> on Windows.
# Be Happy
Now you are finished, you can visit you webserver under localhost and have fun with turtlecoin!
If you have too much money: TRTLuxns7wcNqnoBMjYrMEhRTQdq8AKcwi1G58uqfgdiMqhDZS1fyaAenTwKiPgryn5TQNukGkQScdVqExcLj9XE5EZWvw8Y9R5
