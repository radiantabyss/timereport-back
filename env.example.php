<?php
return [
    'APP_NAME' => 'Timereport',
    'APP_ENV' => 'local',
    'APP_KEY' => '',
    'APP_DEBUG' => true,
    'APP_DEFAULT_LANG' => 'en',

    'LOG_CHANNEL' => 'stack',

    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'timereport',
    'DB_USERNAME' => 'root',
    'DB_PASSWORD' => '',

    'CACHE_DRIVER' => 'file',
    'QUEUE_CONNECTION' => 'sync',

    'JWT_INPUT' => 'jwt_token',
    'JWT_SECRET' => '',

    'FRONT_URL' => 'http://tr-front',
    'BACK_URL' => 'http://tr-back',
    'UPLOADS_URL' => 'http://tr-back/uploads',
    'UPLOADS_PATH' => '/public/uploads',

    'MAIL_DRIVER' => 'log',
    'MAIL_FROM_ADDRESS' => 'robert@radiantabyss.com',
    'MAIL_FROM_NAME' => 'Robert',

    'MAILGUN_DOMAIN' => '',
    'MAILGUN_SECRET' => '',
    'MAILGUN_ENDPOINT' => '',

    'MONITOR_LOGS' => false,
    'MONITOR_LOGS_SLACK_CHANNEL' => '',
];
