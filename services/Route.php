<?php

namespace Services;

class Route {
    public static function intercept(string $pattern, string $resource, array $with = []) {
        $resource = explode("@", $resource);

        if(isset($resource[0])) {
            $class = $resource[0];
        }
        if(isset($resource[1])) {
            $method = $resource[1];
        }
        
        $req_path = strtolower($_SERVER['REQUEST_URI'] ?? '');
        
        if(preg_match($pattern, $req_path, $matches)) {
            
            
            $data = [];
            $i = 1;
            foreach ($with as $key) {
                if(isset($matches[$i])) {
                    $data[$key] = $matches[$i];
                    $params = $matches[$i].', ';
                }
                $i++;
            }
            
            Resource::load($class, (Array)Request::$request, $method);
            exit();
        }
    }
}

?>