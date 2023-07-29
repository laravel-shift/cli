<?php

namespace Shift\Cli\Http\Controllers;

use Illuminate\Support\Carbon;

class SimpleController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        return view('welcome', compact('now'));
    }
}
