<?php
$host = "localhost";
$port = "8000";
$docRoot = __DIR__;

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $phpPath = 'C:\xampp\php\php.exe';
    $command = "$phpPath -S $host:$port -t \"$docRoot\" router.php";
    $process = popen("start /B " . $command, "r");
} else {
    $command = "php -S $host:$port -t \"$docRoot\" router.php";
    $process = popen($command, 'r');
}

if ($process) {
    echo "PHP Server started at http://$host:$port\n";
    while (!feof($process)) {
        echo fgets($process);
    }
    pclose($process);
} else {
    echo "Failed to start PHP server.\n";
}
