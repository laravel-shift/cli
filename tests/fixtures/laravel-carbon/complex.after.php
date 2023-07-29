<?php

namespace Shift\Cli\Support;

use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;

class TimeService
{
    public function convert()
    {
        $now = Carbon::now();
        $period = CarbonPeriod::create();
    }

    private function fqcn()
    {
        $now = \Illuminate\Support\Carbon::now();
        $period = \Carbon\CarbonPeriod::create();
    }
}
