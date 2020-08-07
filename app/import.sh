#!/bin/bash
# set $? to 1
false
while [ $? -ne 0 ]; do
    php /var/www/html/app/products_update.php
done