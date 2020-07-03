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
        
        $req_path = strtolower($_SERVER['PATH_INFO']);
        
        if(preg_match($pattern, $req_path, $matches)) {
            
            
            $data = [];
            $i = 1;
            $params = '';
            foreach ($with as $key) {
                if(isset($matches[$i])) {
                    $data[$key] = $matches[$i];
                    $params = $matches[$i].', ';
                }
                $i++;
            }
            
            if (class_exists($class, false)) {
                $class = new $class();
                if (method_exists($class, $method)) {
                    $class->$method($params);
                } else {
                    Presenter::present("generics.top_error", [
                        "error_info" => "Failed",
                        "error_code" => 87,
                        "error_description"=>"Class '". get_class($class) ."' must implement '".$method."' method"
                    ]);
                }
            } else {
                Presenter::present("generics.top_error", [
                    "error_info" => "Failed",
                    "error_code" => 90,
                    "error_description"=>"No resource class found for '".$class."'"
                ]);
            };

            exit();
        }
    }
}

?>