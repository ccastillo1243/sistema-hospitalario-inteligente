<?php

return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'sistema_hospitalario',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'session_name' => 'hospital_sid',
        'max_login_attempts' => 5,
        'lockout_minutes' => 15,
    ],
];
