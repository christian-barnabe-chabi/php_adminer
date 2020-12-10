<?php

namespace Services;

final class Session {
    private function __construct() {}

    public static function get($key) {
        return $_SESSION[$key] ?? null;
    }

    public static function set(String $key, $value) {
        $_SESSION[$key] = $value;
        return Session::get($key);
    }
}

?>