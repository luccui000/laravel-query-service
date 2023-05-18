<?php

return [
    'excludes' => [
        'controller' => [
            'middleware',
            'getMiddleware',
            'callAction',
            'authorize',
            'authorizeForUser',
            'authorizeResource',
            'validateWith',
            'validate',
            'validateWithBag',
        ]
    ]
];
