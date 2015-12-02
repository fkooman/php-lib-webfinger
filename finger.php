<?php

require_once 'vendor/autoload.php';

use fkooman\WebFinger\WebFinger;
use fkooman\WebFinger\Exception\WebFingerException;

if ($argc < 2) {
    printf('Syntax: %s user@domain.tld'.PHP_EOL, $argv[0]);
    exit(1);
}

try {
    $w = new WebFinger();
    //$w->setOption('verify', false);
    //$w->setOption('ignore_media_type', true);
    //$w->setOption('ignore_property_value_type', true);
    echo $w->finger($argv[1]);
} catch (WebFingerException $e) {
    printf('WebFinger Exception: %s'.PHP_EOL, $e->getMessage());
    exit(1);
} catch (Exception $e) {
    printf('Other Exception: %s'.PHP_EOL, $e->getMessage());
    exit(1);
}
