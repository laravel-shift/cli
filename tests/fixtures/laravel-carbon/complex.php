<?php

namespace App\Support;

use Carbon\Carbon;
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
        $now = \Carbon\Carbon::now();
        $period = \Carbon\CarbonPeriod::create();
    }
}
