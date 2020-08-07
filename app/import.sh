#!/bin/bash
# set $? to 1
false
while [ $? -ne 0 ]; do
    php /var/www/html/app/import_products.php
done