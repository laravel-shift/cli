<?php

declare(strict_types=1);

return [
    'patchers' => [
        function (string $filePath, string $prefix, string $content): string {
            if ($filePath === 'src/Tasks/ModelTableName.php') {
                $content = str_replace(
                    $prefix . '\\\\Illuminate\\\\Database\\\\',
                    'Illuminate\\\\Database\\\\',
                    $content
                );

                $content = str_replace(
                    $prefix . '\\\\App\\\\Models\\\\',
                    'App\\\\Models\\\\',
                    $content
                );
            }

            return $content;
        },
    ],
    'exclude-files' => [
        'vendor/symfony/polyfill-php80/bootstrap.php',
    ],
    'exclude-namespaces' => [
        'Symfony\Polyfill\*',
    ],
];
