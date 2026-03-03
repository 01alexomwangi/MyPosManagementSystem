<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

  'little' => [
    'auth_url'   => env('LITTLE_AUTH_URL'),
    'basic_auth' => env('LITTLE_BASIC_AUTH'),
    'estimate_url' => env('LITTLE_ESTIMATE_URL'),
    'ride_url'       => env('LITTLE_RIDE_URL'),
    'driver'         => env('LITTLE_DRIVER'),
    'corporate_id'   => env('LITTLE_CORPORATE_ID'),
    'rider_mobile'   => env('LITTLE_RIDER_MOBILE'),
    'rider_name'     => env('LITTLE_RIDER_NAME'),
    'rider_email'    => env('LITTLE_RIDER_EMAIL'),
    'rider_picture'  => env('LITTLE_RIDER_PICTURE'),
    
],

];
