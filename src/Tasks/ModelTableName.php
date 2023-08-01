<?php

namespace Shift\Cli\Tasks;

use Illuminate\Support\Str;
use ReflectionClass;
use Shift\Cli\Contracts\Task;
use Shift\Cli\Facades\Reflector;
use Shift\Cli\Models\File;
use Shift\Cli\Traits\FindsFiles;

class ModelTableName implements Task
{
    use FindsFiles;

    public function perform(): int
    {
        foreach ($this->findFilesContaining('/^class\s+/m') as $path) {
            $model = $this->modelClass($path);
            if (is_null($model)) {
                continue;
            }

            var_dump('here');

            $conventional_name = $this->tableNameFromClassName($model->getShortName(), $this->isPivotModel($model));

            if ($model->getProperty('table')->getDefaultValue() !== $conventional_name) {
                continue;
            }

            $file = File::fromPath($path);
            $class = $this->parseClass($file->contents());

            $start = $class['properties']['table']['comment'] ? $class['properties']['table']['comment']['line']['start'] : $class['properties']['table']['line']['start'];
            $file->removeBlankLinesAfter($class['properties']['table']['line']['end']);
            $file->removeSegment($start, $class['properties']['table']['line']['end']);

            file_put_contents($path, $file->contents());
        }

        return 0;
    }

    private function isPivotModel($model): bool
    {
        return $model->isSubclassOf('Illuminate\\Database\\Eloquent\\Relations\\Pivot');
    }

    private function tableNameFromClassName($class, $pivot)
    {
        if ($pivot) {
            return str_replace('\\', '', Str::snake(Str::singular($class)));
        }

        return str_replace('\\', '', Str::snake(Str::plural($class)));
    }

    private function modelClass($file): ?ReflectionClass
    {
        $class = Reflector::classFromPath($file);
        if (is_null($class)) {
            return null;
        }

        if (! $class->isSubclassOf('Illuminate\\Database\\Eloquent\\Model')) {
            return null;
        }

        $properties = $class->getDefaultProperties();
        if (! isset($properties['table'])) {
            return null;
        }

        return $class;
    }

    private function parseClass(string $contents)
    {
        static $finder;

        $finder ??= new \Shift\Cli\Parsers\NikicParser(new \Shift\Cli\Parsers\Finders\ClassDefinition());

        return $finder->parse($contents);
    }
}
