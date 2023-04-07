<?php

return [
    'tables' => [
        'posts'     => App\Models\Post::class,
        'tags'      => App\Models\Tag::class,
        'comments'  => App\Models\Comment::class,
        'users'     => App\Models\User::class,
    ],
    'orders' => [
        'posts' => [
            'id',
            'title',
            'content',
            'auth.name',
            'comments.updated_at'
        ]
    ],
    'searchable' => [
        'posts' => [
            'title',
            'content',
            'auth.name',
            'comments.content'
        ]
    ]
];
