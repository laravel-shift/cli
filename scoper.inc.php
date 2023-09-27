<?php

declare(strict_types=1);

return [
    'expose-global-functions' => false,
    //    'exclude-functions' => ['app'],
    'patchers' => [
        function (string $filePath, string $prefix, string $content): string {
            if (\str_contains($content, $prefix . '\\app')) {
                $content = \preg_replace(
                    '/' . $prefix . '[\\\\]+app' . '/',
                    'app',
                    $content
                );
            }

            if (\str_starts_with($filePath, 'src/Tasks/')) {
                $content = \str_replace(
                    $prefix . '\\\\Illuminate\\\\',
                    'Illuminate\\\\',
                    $content
                );
                $content = \str_replace(
                    $prefix . '\\\\App\\\\',
                    'App\\\\',
                    $content
                );
                $content = \str_replace(
                    $prefix . '\\\\PHPUnit\\\\Framework\\\\',
                    'PHPUnit\\\\Framework\\\\',
                    $content
                );
            }

            return $content;
        },
    ],
    'exclude-files' => [
        'vendor/symfony/polyfill-php80/bootstrap.php',
        'vendor/symfony/polyfill-ctype/bootstrap80.php',
        'vendor/symfony/polyfill-ctype/bootstrap.php',
        'vendor/symfony/polyfill-intl-normalizer/bootstrap80.php',
        'vendor/symfony/polyfill-intl-normalizer/bootstrap.php',
        'vendor/symfony/polyfill-mbstring/bootstrap80.php',
        'vendor/symfony/polyfill-mbstring/bootstrap.php',
        'vendor/symfony/polyfill-intl-grapheme/bootstrap80.php',
        'vendor/symfony/polyfill-intl-grapheme/bootstrap.php',
    ],
    'exclude-namespaces' => [
        'Symfony\Polyfill\*',
    ],
];
