<?php

// TinoDidriksen: w is writable, w+ is read-write.


$modes = array(
    'r' => false,
    'r+' => true,
    'w' => true,
    'w+' => true,
    'a' => true,
    'a+' => true,
    'x' => true,
    'x+' => true,
    'c' => true,
    'c+' => true
);

foreach ($modes as $m => $writing) {

    $mem = fopen('php://memory', $m);
    $meta = stream_get_meta_data($mem);
    echo $m."\t".$writing."\t".$meta['mode']."\n";
}

