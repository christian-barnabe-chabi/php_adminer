<?php

namespace Lib;

function deep_walk( $array, string $search) {
    
    $array = json_encode($array);
    $array = json_decode($array, true);

    if(array_key_exists($search, $array)) {
        return $array[$search];
    }

    foreach ($array as $key => $value) {
        if(is_array($array[$key])) {
            return deep_walk($array[$key], $search);
        }
    }

    return null;
}

?>