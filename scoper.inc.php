<?php

declare(strict_types=1);

return [
    'patchers' => [
        function (string $filePath, string $prefix, string $content): string {
            if (str_starts_with($filePath, 'src/Tasks/')) {
                $content = str_replace(
                    $prefix . '\\\\Illuminate\\\\',
                    'Illuminate\\\\',
                    $content
                );

                $content = str_replace(
                    $prefix . '\\\\App\\\\',
                    'App\\\\',
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
