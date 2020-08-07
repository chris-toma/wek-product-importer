<?php

require_once 'config/bootstrap.php';

$task = new Task($entityManager);
$task->execute();

