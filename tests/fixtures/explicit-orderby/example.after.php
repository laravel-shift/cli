<?php

namespace Shift\Cli\Support;

class ExampleClass
{
    public function simple()
    {
        User::query()->orderBy('email')->get();
        User::query()->orderByDesc('email')->get();
        User::orderBy('email')->get();
        User::orderByDesc('email')->get();
    }

    public function variable()
    {
        User::query()
            ->orderBy($column)
            ->get();
        User::query()
            ->orderByDesc($column)
            ->get();

        User::orderBy($column)->get();
        User::orderByDesc($column)->get();
    }
}
