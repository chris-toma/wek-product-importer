<?php
require_once 'config/bootstrap.php';


use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

$productCount = $_ENV['DEFAULT_PRODUCT_COUNT'];
$phpBinaryFinder = new PhpExecutableFinder();
$phpBinaryPath = $phpBinaryFinder->find();

$myPid = getmypid();
$myPpid = posix_getppid();

shell_exec("echo {$myPpid} {$myPid} >> log.log");
pcntl_fork();
passthru("kill {$myPid}");
if(!isset($argv[1]))
    exit(1);
$process = new Process(["{$phpBinaryPath}", 'import_products.php', $argv[1], $productCount]);
$process->run();

if ($process->isSuccessful()) {
    // go to next page and dance again
    shell_exec("php run_import.php " . ($argv[1] + 1));
} else {
    if ($process->getExitCode() != Task::IMPORT_STATUS_NO_MORE_PRODUCTS) {
        // restarting from page 1
        shell_exec("php run_import.php " . 1);
    }
    shell_exec("echo no more products {$myPpid} {$myPid} >> log.log");
}