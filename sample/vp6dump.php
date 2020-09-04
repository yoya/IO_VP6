<?php

if (is_readable('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    require_once 'IO/VP6.php';
}

$options = getopt("f:");

if ((isset($options['f']) === false) || (($options['f'] !== "-") && is_readable($options['f']) === false)) {
    fprintf(STDERR, "Usage: php vp6dump.php -f <vp6_file>\n");
    fprintf(STDERR, "ex) php vp6dump.php -f test.vp6 -t \n");
    exit(1);
}

$filename = $options['f'];
if ($filename === "-") {
    $filename = "php://stdin";
}
$vp6data = file_get_contents($filename);

$opts = [];

$vp6 = new IO_VP6();
try {
    $vp6->parse($vp6data, $opts);
} catch (Exception $e) {
    echo "ERROR: vp6dump: $filename:".PHP_EOL;
    echo $e->getMessage()." file:".$e->getFile()." line:".$e->getLine().PHP_EOL;
    echo $e->getTraceAsString().PHP_EOL;
    exit (1);
}

$vp6->dump($opts);
