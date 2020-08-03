<?php

namespace Config;

class PublicResource {

    private static $routes = [
        '/password_reset', '/logout','/login','/untracked_ticket/create', '/untracked_ticket/save', '/sme/create', '/sme/save',
        '/mode?theme=night', '/mode?theme=light'
    ];

    public static function routes() {
        return self::$routes;
    }

    public static function isPublic(string $route) {

        // return in_array($route, self::routes());
        
        return preg_match("#$route.*#", $route);
    }

}

?>