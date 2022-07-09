<?php

declare(strict_types=1);

return [
    'user' => [
        'id' => '1',
        'role' => 'user',
        'inheritsFrom' => 'guest',
        'label' => 'User',
    ],
    'staff' => [
        'id' => '2',
        'role' => 'staff',
        'inheritsFrom' => 'user',
        'label' => 'Staff',
    ],
    'admin' => [
        'id' => '3',
        'role' => 'admin',
        'inheritsFrom' => 'staff',
        'label' => 'Admin',
    ],
];