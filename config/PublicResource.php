<?php

namespace Config;

class PublicResource {

    private static $routes = [
        '/password_reset', '/logout','/login', '/untracked_ticket', '/sme'
    ];

    public static function routes() {
        return self::$routes;
    }

    public static function isPublic(string $route) {

        // return in_array($route, self::routes());
        
        // return preg_match("#$route.*#", $route);

        return [];
    }

}

?>