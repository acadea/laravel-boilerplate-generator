<?php
// dummy schema for testing

return [
    'book_author' => [
        'name' => [
            'type' => 'string',
        ],
    ],

    'post'           => [
        'title'          => [
            'type' => 'string',
        ],
        'body'           => [
            'type'       => 'mediumText',
            'attributes' => [
                'nullable',

            ],
        ],
        'options'        => [
            'type'       => 'json',
            'attributes' => [
                'nullable'
            ],
        ],
        'book_author_id' => [
            'type'    => 'foreignId',
            'foreign' => [
                'references' => 'id',
                'on'         => 'book_authors',
            ],
        ],
        'price'          => [
            'type' => 'decimal'
        ],
        'published'      => [
            'type'       => 'boolean',
            'attributes' => [
                'default' => [true],
            ]
        ],
        'tags'           => [
            'type'  => 'pivot',
            'pivot' => [
                'table'       => 'post_tag',
                'related_key' => 'tag_id',
                'foreign_key' => 'post_id',
            ]
        ]
    ],
    'pivot:post_tag' => [
        'post_id' => [
            'primary'    => true,
            'type'       => 'foreignId',
            'attributes' => [
                'index'
            ],
            'foreign'    => [
                'references' => 'id',
                'on'         => 'posts',
            ],
        ],
        'tag_id'  => [
            'primary'    => true,
            'type'       => 'foreignId',
            'attributes' => [
                'index'
            ],
            'foreign'    => [
                'references' => 'id',
                'on'         => 'tags',
            ],
        ],
    ],

    'comment' => [
        'title'   => [
            'type'       => 'string',
            'attributes' => ['nullable'],
        ],
        'post_id' => [
            'type'    => 'foreignId',
            'foreign' => [
                'references' => 'id',
                'on'         => 'posts',
            ],
        ],
    ],

    'tag' => [
        'title'   => [
            'type'       => 'string',
            'attributes' => ['nullable'],
        ],
    ],


];

