<?php

namespace App\Tasks;

use App\Traits\FindsFiles;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;

class ModelTableName
{
    use FindsFiles;

    public function perform(): void
    {
        foreach ($this->files as $file) {
            $contents = file_get_contents($file);

            if (!str_contains($contents, 'class ')) {
                continue;
            }

            $model = $this->modelClass($file);
            if (is_null($model)) {
                continue;
            }

            $conventional_name = $this->tableNameFromClassName($model->getName(), $this->isPivotModel($model));

            if ($model->getProperty('table')->getDefaultValue() !== $conventional_name) {
                continue;
            }

            // TODO: use ClassFinder to remove property
            $contents = substr_replace(
                $contents,
                '',
                $instance['offset']['start'],
                $instance['offset']['end'] - $instance['offset']['start'] + 1
            );

            file_put_contents($file, $contents);
        }
    }

    private function isPivotModel($model): bool
    {
        return $model->isSubclassOf('Illuminate\\Database\\Eloquent\\Relations\\Pivot'),
    }

    private function tableNameFromClassName($class, $pivot)
    {
        if ($pivot) {
            return str_replace('\\', '', Str::snake(Str::singular($class)));
        }

        return str_replace('\\', '', Str::snake(Str::plural($class)));
    }

    private function modelClass($file): ?\ReflectionClass
    {
        try {
            $class = new ReflectionClass(class_from_path($file));
        } catch (ReflectionException) {
            return null;
        }

        if (!$class->isSubclassOf('Illuminate\\Database\\Eloquent\\Model')) {
            return null;
        }

        $properties = $class->getDefaultProperties();
        if (!isset($properties['table'])) {
            return null;
        }

        return $class;
    }

    private function class_from_path($path): string
    {
        return ucfirst(substr(str_replace('/', '\\', $path), 0, -4));
    }
}
