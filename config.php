<?php
/**
 * Local config — not committed to git.
 * Edit this file with your real DB credentials and base URL.
 */

return [
    'db' => [
        'host'    => 'localhost',
        'name'    => 'bdms',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name'      => 'Blood Donor Management System',
        'base_url'  => '', // '' when serving from project root via `php -S`
        'debug'     => true,
        'timezone'  => 'Asia/Kolkata',
    ],
    'mail' => [
        'from_email' => 'no-reply@bdms.local',
        'from_name'  => 'BDMS',
    ],
    'otp' => [
        'length_minutes' => 10,
    ],
];
