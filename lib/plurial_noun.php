<?php

namespace Lib;

function plurial_noun(string $noun) {
    $noun = trim($noun);

    if(preg_match("/(a)$/i", $noun)) {
        return $noun;
    }

    if(preg_match("/(s)$/i", $noun)) {
        return $noun;
    }

    if(preg_match("/(ies)$/i", $noun)) {
        return $noun;
    }

    if(preg_match('/(y)$/i', $noun)) {
        return preg_replace("/y$/i", "ies", $noun);
    }

    return $noun."s";
}

?>
