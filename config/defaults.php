<?php

return [
    'tasks' => [
        \Shift\Cli\Tasks\AnonymousMigrations::class,
        \Shift\Cli\Tasks\CheckLint::class,
        \Shift\Cli\Tasks\ClassStrings::class,
        \Shift\Cli\Tasks\DebugCalls::class,
        \Shift\Cli\Tasks\DeclareStrictTypes::class,
        \Shift\Cli\Tasks\DownMigration::class,
        \Shift\Cli\Tasks\ExplicitOrderBy::class,
        \Shift\Cli\Tasks\FacadeAliases::class,
        \Shift\Cli\Tasks\FakerMethods::class,
        \Shift\Cli\Tasks\LaravelCarbon::class,
        \Shift\Cli\Tasks\LatestOldest::class,
        \Shift\Cli\Tasks\ModelTableName::class,
        \Shift\Cli\Tasks\OrderModel::class,
        \Shift\Cli\Tasks\RemoveDocBlocks::class,
        \Shift\Cli\Tasks\RulesArrays::class,
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
