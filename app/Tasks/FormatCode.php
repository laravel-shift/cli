<?php

namespace App\Tasks;

class FormatCode
{
    public function perform()
    {
        exec('vendor/bin/pint', result_code: $exit_code);

        exit($exit_code);
    }
}
