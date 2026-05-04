<?php
/**
 * Copy this file to config.php and adjust values for your environment.
 * config.php is gitignored so each install can have its own settings.
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
        'name'      => 'BDMS',
        'base_url'  => '', // '' when serving from project root via `php -S`
        'debug'     => true, // shows OTP on screen for local dev
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
