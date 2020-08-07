#!/bin/bash
# Note that false sets $? to 1
false
while [ $? -ne 0 ]; do
    php /var/www/html/app/products_update.php
#    php /var/www/html/app/test.php >> log.log
done