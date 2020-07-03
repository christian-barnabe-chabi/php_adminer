<?php

namespace Services;

use Services\Request;

Class Authenticateable {
    private static function must_be_authentified() {
        // TODO cache the incomming request

        if(!Auth::user()) {
            $_SESSION['request'] = Request::$request;
            Resource::load('login', (Array)Request::$request);
        }
    }

    public static function auth() {
        self::must_be_authentified();
    }
}

?>
