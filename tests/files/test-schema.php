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
        'user_id' => [
            'type' => 'foreignId',
            'foreign' => [
                'references' => 'id',
                'on' => 'users',
            ],
        ],
        'book_author_id' => [
            'type' => 'foreignId',
            'foreign' => [
                'references' => 'id',
                'on' => 'book_authors',
            ],
        ],
        'tags' => [
            'type' => 'pivot',
            'pivot' => [
                'table' => 'post_tag',
                'related_key' => 'post_id',
                'foreign_key' => 'tag_id',
            ]
        ]

    ],

    'pivot:post_tag' => [
        'post_id' => [
            'primary' => true,
            'type' => 'foreignId',
            'attributes' => [
                'index'
            ],
            'foreign' => [
                'references' => 'id',
                'on' => 'posts',
            ],
        ],
        'tag_id' => [
            'primary' => true,
            'type' => 'foreignId',
            'attributes' => [
                'index'
            ],
            'foreign' => [
                'references' => 'id',
                'on' => 'tags',
            ],
        ],

    ],

];
