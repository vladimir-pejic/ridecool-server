<?php

require 'vendor/autoload.php';

use Elasticsearch\ClientBuilder;


$dotnev = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$elasticsearch_host = getenv('ELASTICSEARCH_HOST');

$hosts = [
  [
    'host' => $elasticsearch_host
  ]
];

$client = ClientBuilder::create()->setHosts($hosts)->build();
