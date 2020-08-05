<?php

namespace Services;

use Services\Request;

Class Authenticateable {
    private static function must_be_authentified() {
        // TODO cache the incomming request

        if(!Auth::user()) {
            Resource::load('App\Providers\Login');
            exit();
        }
    }

    public static function auth() {
        self::must_be_authentified();
    }
}

?>
