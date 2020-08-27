<?php

namespace Lib;

function str_cut($string, int $lenght) {

    if(is_string($string)) {
        if(strlen($string) >= $lenght) {
            return substr($string, 0, $lenght).' ...';
        }
    }

    return $string;
}

?>