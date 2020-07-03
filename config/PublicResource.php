<?php

namespace Config;

class PublicResource {

    private static $routes = [
        '/password_reset', '/logout', '/untracked_ticket/create', '/untracked_ticket/save'
    ];

    public static function routes() {
        return self::$routes;
    }

}

?>