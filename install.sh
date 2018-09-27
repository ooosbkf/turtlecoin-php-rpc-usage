sudo apt install composer apache2 php7.2 php7.2-mbstring php7.2-gd
sudo cp -r . /var/www/html
cd /var/www/html
composer require chillerlan/php-qrcode turtlecoin/turtlecoin-walletd-rpc-php
sudo rm install.sh
