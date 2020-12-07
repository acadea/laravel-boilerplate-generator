<?php
// dummy schema for testing

return [

    'post' => [
        'title' => [
            'type' => 'string',
            'attributes' => ['nullable'],
        ],
        'body' => [
            'type' => 'mediumText',
            'attributes' => ['nullable'],
        ],
        'author_id' => [
            'type' => 'foreignId',
            'foreign' => [
                'references' => 'id',
                'on' => 'authors',
            ],
        ],

    ],

];
