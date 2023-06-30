<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

return [
    'environment' => App::environment(),

    'path' => Storage::path('app'),

    'qualified' => Event::class,
];
