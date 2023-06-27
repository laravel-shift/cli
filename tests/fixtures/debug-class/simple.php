<?php

$foo = 'bar';
$baz = [];

var_dump($foo);
var_dump($foo, $baz);

print_r($baz);
$output = print_r($baz, true);

var_export($baz);
$output = var_export($baz, true);

dump($foo);
dd($foo);

die('no-op');
