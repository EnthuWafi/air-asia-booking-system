<?php

/**
 * Used to store website configuration information.
 *
 * @var string or null
 */
function config($key = ''): ?string
{
    $config = [
        'name' => 'airasia',
        'version' => 'v1.0',
    ];

    return $config[$key] ?? null;
}
