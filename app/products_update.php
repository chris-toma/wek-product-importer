<?php

require_once 'config/bootstrap.php';

$task = new Task($entityManager);

$page = 1;
$productCount = $_ENV['DEFAULT_PRODUCT_COUNT'];
$retryCount = 0;

do {
    if ($retryCount > $_ENV['RETRY_MAX_LIMIT']) {
        print "end by max retry \n";
        exit(1);
        break;
    }

    $ret = $task->getProducts(
        $_ENV['API_ENDPOINT_URL'],
        $page,
        $productCount
    );

    if ($ret['code'] == Task::STATUS_NOT_OK) {
        $retryCount++;
        // decreasing the product per page value in case this is the problem
        $productCount = $productCount > $_ENV['MIN_PRODUCT_COUNT']
            ? round($productCount / 2)
            : $_ENV['MIN_PRODUCT_COUNT'];
        // resetting to page 1 because we do not know if products has the same order.
        $page = 1;
        print "retry count {$retryCount} \n";
        sleep(
            (
            $retryCount < $_ENV['MAX_DELAY_MULTIPLIER']
                ? $retryCount
                : $_ENV['MAX_DELAY_MULTIPLIER']
            ) * $_ENV['RETRY_DELAY_SECONDS']
        );
        // log data
        continue;
    } else {
        $page++;
    }
    if (count($ret['data']['products']) > 0) {
        print "page {$ret['data']['current_page']} from {$ret['data']['total_pages']} has products \n";
        try {
            $task->saveProducts($ret['data']['products']);
            //log success
        } catch (Exception $e) {
            //log errors
        }
    } else {
        print "no more products \n";
        break;
    }

} while ($retryCount > 0 || $ret['data']['current_page'] <= $ret['data']['total_pages']);


