<?php

return [
    'Hosts' => [
        env('ELASTIC_HOST', 'http://host.docker.internal:9200'),
    ],
    'BasicAuthentication' => [
        env('ELASTICSEARCH_USERNAME', 'elastic'),
        env('ELASTICSEARCH_PASSWORD', 'abcxyz'),
    ],
    'sslVerification' => env('ELASTIC_VERIFY_SSL', false),
];

