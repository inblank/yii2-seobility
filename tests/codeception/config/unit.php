<?php
return [
    'id' => 'unitTest',
    'basePath' => __DIR__ . '/../app',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=testdb',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
        ],
    ],
];
