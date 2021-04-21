<?php

return [
    "namespaces" => [
        'model' => 'App\\Models',
        'repository' => 'App\\Repositories\\V1',
        'event' => 'App\\Event\\Models',

    ],
    'paths' => [
        'api-routes' => '/routes/api/v1',
        'schema-structure' => '/database/structure/schema.php'
    ]
];
