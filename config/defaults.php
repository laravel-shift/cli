<?php

return [
    'tasks' => [
        'anonymous-migrations' => \Shift\Cli\Tasks\AnonymousMigrations::class,
        'check-lint' => \Shift\Cli\Tasks\CheckLint::class,
        'class-strings' => \Shift\Cli\Tasks\ClassStrings::class,
        'debug-calls' => \Shift\Cli\Tasks\DebugCalls::class,
        'declare-strict' => \Shift\Cli\Tasks\DeclareStrictTypes::class,
        'down-migration' => \Shift\Cli\Tasks\DownMigration::class,
        'explicit-orderby' => \Shift\Cli\Tasks\ExplicitOrderBy::class,
        'facade-aliases' => \Shift\Cli\Tasks\FacadeAliases::class,
        'faker-methods' => \Shift\Cli\Tasks\FakerMethods::class,
        'laravel-carbon' => \Shift\Cli\Tasks\LaravelCarbon::class,
        'latest-oldest' => \Shift\Cli\Tasks\LatestOldest::class,
        'model-table' => \Shift\Cli\Tasks\ModelTableName::class,
        'order-model' => \Shift\Cli\Tasks\OrderModel::class,
        'remove-docblocks' => \Shift\Cli\Tasks\RemoveDocBlocks::class,
        'rules-arrays' => \Shift\Cli\Tasks\RulesArrays::class,
    ],

    'run' => [
        'anonymous-migrations',
        'class-strings',
        'explicit-orderby',
        'facade-aliases',
        'faker-methods',
        'model-table',
        'rules-arrays',
    ],

];
