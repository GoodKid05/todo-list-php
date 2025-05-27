<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'local' => [
            'adapter' => 'pgsql',
            'host' => 'db',
            'name' => 'todo_db',
            'user' => 'postgres',
            'pass' => '939615',
            'port' => '5432',
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'pgsql',
            'host' => 'db',
            'name' => 'todo_db_test',
            'user' => 'postgres',
            'pass' => '939615',
            'port' => '5432',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
