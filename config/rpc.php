<?php

return array(

    'client' => [
        /**
         * Server URL
         */
        'url' => env('RPC_URL', 'http://market.cc/rpc'),

        /**
         * HTTP client timeout
         */
        'timeout'    => env('RPC_TIMEOUT', 15),

        /**
         * Custom HTTP headers
         */
        'headers'    => array(),

        /**
         * Username for authentication
         */
        'username' => false,
        'password' => null,

        /**
         * Enable debug output to the php error log
         */
        'debug' => env('RPC_DEBUG', false),

        /**
         * SSL certificates verification
         */
        'ssl_verify_peer' => env('RPC_SSL', true),

        /**
         * Methods to Cache
         * '*' to allow all, and 'method_name' to single method
         */
        'cache' => env('RPC_CACHE', null),

        'cache_duration' => env('RPC_CACHE_TIME', 15),
    ],

    'server' => [
    ],

);