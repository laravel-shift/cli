<?php

namespace App\Support;

class ExampleClass
{
    public function simple()
    {
        User::query()->orderBy('email', 'asc')->get();
        User::query()->orderBy('email', 'desc')->get();
        User::orderBy('email', 'asc')->get();
        User::orderBy('email', 'desc')->get();
    }

    public function variable()
    {
        User::query()
            ->orderBy($column, 'ASC')
            ->get();
        User::query()
            ->orderBy($column, "DESC")
            ->get();

        User::orderBy($column, "ASC")->get();
        User::orderBy($column, 'desc')->get();
    }
}
