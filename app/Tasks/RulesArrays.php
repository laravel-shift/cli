<?php

namespace App\Tasks;

use App\Contracts\Task;
use App\Models\File;
use App\Traits\FindsFiles;

class RulesArrays implements Task
{
    use FindsFiles;

    private static $indent = 16;

    public function perform(): int
    {
        foreach ($this->findFiles() as $path) {
            $contents = file_get_contents($path);

            if (! preg_match('/\s+extends\s+FormRequest\s/', $contents)) {
                continue;
            }

            if (! preg_match('/\s+public\s+function\s+rules\(\)/', $contents)) {
                continue;
            }

            $class = $this->parseClass($contents);

            if (empty($class['methods']['rules'])) {
                continue;
            }

            $rules_method = $class['methods']['rules'];

            $returns = array_filter($this->parseRules($contents), function ($return) use ($rules_method) {
                return $return['line']['start'] >= $rules_method['line']['start'] && $return['line']['end'] <= $rules_method['line']['end'];
            });

            if (empty($returns)) {
                continue;
            }

            $file = File::fromPath($path);

            foreach ($returns as $return) {
                $body = $file->segment($return['line']['start'], $return['line']['end']);
                $new_body = $body;

                foreach ($return['values'] as $rules) {
                    $block = $file->segment($rules['line']['start'], $rules['line']['end']);

                    if (is_array($rules['value'])) {
                        $skipped = false;
                        $new_block = $block;

                        foreach ($rules['value'] as $rule) {
                            if (! str_contains($rule, '|')) {
                                $skipped = true;

                                continue;
                            }

                            $output = '';
                            $parts = explode('|', $rule);
                            if ($skipped) {
                                $part = array_shift($parts);
                                if ($part) {
                                    $rule = str_replace($part . '|', '', $rule);
                                    $new_block = str_replace("'$part|", "'$part','", $new_block);
                                }
                            }

                            $parts = array_filter($parts);
                            foreach ($parts as $part) {
                                $output .= PHP_EOL;
                                $output .= str_repeat(' ', self::$indent);
                                // TODO: what about special characters...
                                $output .= sprintf("'%s',", $part);
                            }

                            $skipped = false;
                            $new_block = str_replace("'" . $rule . "'", $output, $new_block);
                        }

                        $new_block .= str_repeat(' ', self::$indent - 4);
                        $new_block .= '],';
                        $new_block .= PHP_EOL;
                        $new_block = preg_replace('/=>\s*/m', '=> [' . PHP_EOL . str_repeat(' ', self::$indent), $new_block, 1);
                        $new_block = preg_replace('/\s*,(\s*\.\s*)/', '$1', $new_block);
                        $new_block = preg_replace('/\s*\.\s*$/m', ',', $new_block);
                        $new_block = preg_replace('/\s*,,$/m', ',', $new_block);

                        $new_body = str_replace($block, $new_block, $new_body);

                        continue;
                    }

                    $parts = explode('|', $rules['value']);

                    $output = '=> [';
                    foreach ($parts as $part) {
                        $output .= PHP_EOL;
                        $output .= str_repeat(' ', self::$indent);
                        // TODO: what about special characters...
                        $output .= sprintf("'%s',", $part);
                    }
                    $output .= PHP_EOL;
                    $output .= str_repeat(' ', self::$indent - 4);
                    $output .= '],';

                    $new_block = preg_replace('/=>\s*(\'|")' . preg_quote($rules['value'], '/') . '\1,?/', $output, $block);
                    $new_body = str_replace($block, $new_block, $new_body);
                }

                $contents = str_replace($body, $new_body, $contents);
            }

            file_put_contents($path, $contents);
        }

        return 0;
    }

    private function parseRules(string $contents): array
    {
        static $finder;

        $finder ??= new \App\Parsers\NikicParser(new \App\Parsers\Finders\FormRequestStringRulesFinder());

        return $finder->parse($contents);
    }

    private function parseClass(string $contents): array
    {
        static $finder;

        $finder ??= new \App\Parsers\NikicParser(new \App\Parsers\Finders\ClassDefinition());

        return $finder->parse($contents);
    }
}
