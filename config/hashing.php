<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    | This option controls the default hash driver used to hash passwords for
    | your application. By default, the bcrypt algorithm is used; however, you
    | remain free to modify this option if you wish.
    |
    | Supported: "bcrypt", "argon", "argon2id"
    */
    'driver' => env('HASH_DRIVER', 'argon2id'),

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    */
    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => env('HASH_VERIFY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options
    |--------------------------------------------------------------------------
    */
    'argon' => [
        'memory'  => 65536,
        'threads' => 1,
        'time'    => 4,
        'verify'  => env('HASH_VERIFY', true),
    ],
];
