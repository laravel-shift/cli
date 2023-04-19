<?php

namespace App\Support;

class ConfigurationRepository
{
    private string $path = 'shift-cli.json';

    private ?array $data = null;

    public function get($key, $default = null)
    {
       return data_get($this->data(), $key, $default);
    }

    private function data(): array
    {
        $this->data ??= $this->load();

        return $this->data;
    }

    private function load(): array
    {
        if (! file_exists($this->path)) {
            return [];
        }

        $configuration = json_decode(file_get_contents($this->path), true);
        if (! is_array($configuration)) {
            abort(1, sprintf('The configuration file [%s] is not valid JSON.', $this->path));
        }

        return $configuration;
    }
}
