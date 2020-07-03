<?php

namespace Config;

class PublicResource {

    private static $routes = [
        '/password_reset', '/logout',
    ];

    public static function routes() {
        return self::$routes;
    }

}

?>