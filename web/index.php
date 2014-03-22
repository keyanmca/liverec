<?php

// Index file for development - use with PHP 5.4 Web Server

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

require __DIR__.'/../src/liverec.php';

?>