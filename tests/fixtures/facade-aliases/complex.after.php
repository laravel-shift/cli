<?php

namespace Shift\Cli\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Arr;

class ComplexClass
{
    public function imported()
    {
        App::make('app');
        Arr::wrap('arr');
    }

    public function global()
    {
        DB::query('SELECT * FROM users');
        Str::of('something');
    }

    public function noop()
    {
        SomeApp::make('app');
        Another\Arr::wrap('arr');
    }
}
