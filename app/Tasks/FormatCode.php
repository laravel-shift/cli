<?php

namespace App\Tasks;

class FormatCode
{
    public function perform(): int
    {
        exec('vendor/bin/pint', result_code: $exit_code);

        return $exit_code;
    }
}
