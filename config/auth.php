<?php

use Sis_medico\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => (int) env('AUTH_PASSWORD_RESET_EXPIRE_MINUTES', 30),
            'throttle' => (int) env('AUTH_PASSWORD_RESET_THROTTLE_SECONDS', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the number of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

    'login_security' => [
        'pair_max_attempts' => (int) env('AUTH_LOGIN_PAIR_MAX_ATTEMPTS', 5),
        'pair_decay_seconds' => (int) env('AUTH_LOGIN_PAIR_DECAY_SECONDS', 300),
        'account_max_attempts' => (int) env('AUTH_LOGIN_ACCOUNT_MAX_ATTEMPTS', 10),
        'account_decay_seconds' => (int) env('AUTH_LOGIN_ACCOUNT_DECAY_SECONDS', 900),
        'ip_max_attempts' => (int) env('AUTH_LOGIN_IP_MAX_ATTEMPTS', 50),
        'ip_decay_seconds' => (int) env('AUTH_LOGIN_IP_DECAY_SECONDS', 300),
        'remember_duration_minutes' => (int) env('AUTH_REMEMBER_DURATION_MINUTES', 43200),
    ],

    'password_reset_security' => [
        'pair_max_attempts' => (int) env('AUTH_PASSWORD_RESET_PAIR_MAX_ATTEMPTS', 3),
        'pair_decay_minutes' => (int) env('AUTH_PASSWORD_RESET_PAIR_DECAY_MINUTES', 5),
        'ip_max_attempts' => (int) env('AUTH_PASSWORD_RESET_IP_MAX_ATTEMPTS', 20),
        'ip_decay_minutes' => (int) env('AUTH_PASSWORD_RESET_IP_DECAY_MINUTES', 5),
    ],

];
