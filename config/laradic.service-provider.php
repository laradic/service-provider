<?php
/**
 * Part of the CLI PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */


return [
    'first'  => 1,
    'second' => 'something else',

    'plugins' => [
        'priorities' => [
            'defaults' => [
                'bindings::register'   => 10,
                'commands::register'   => 10,
                'config::register'     => 10,
                'events::register'     => 10,
                'middleware::register' => 10,
                'paths::register'      => 10,
                'providers::register'  => 10,
                'resources::register'  => 10,


                'config::boot' => 10,
            ],
        ],
    ],
];