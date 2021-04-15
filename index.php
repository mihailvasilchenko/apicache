<?php

use Apicache\Apicache;
use Apicache\Exceptions\ApicacheException;

require 'vendor/autoload.php';

// Setup the service.
$key = '';
$baseUri = 'https://jsonplaceholder.typicode.com';
$timeout = 3;
$service = new Apicache($key, $baseUri, $timeout);

// Try to get some todos from the API or catch an error.
try {
    $todos = $service->getTodos('GET', '/todos');

    var_export($todos);
} catch (ApicacheException $e) {
    echo $e->getMessage();
}
