<?php

require_once 'config/bootstrap.php';

$page = $argv[1];
$productCount = $argv[2];

$task = new Task($entityManager);
$task->executeImport((int)$page, (int)$productCount);

