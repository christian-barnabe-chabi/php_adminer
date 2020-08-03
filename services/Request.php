<?php

namespace Services;

use function Lib\get_request_files;

class Request {
    public static $request;
    public function __construct()
    {
        $req_path = strtolower($_SERVER['REQUEST_URI']);

        $path = explode('/', $_SERVER['REQUEST_URI']);
        $path = array_slice($path, 1); // remove the first invalid and empty string on index 0
        $request = [
            'php_admin_resource'=>null,
            'uid'=>null,
        ];
        
        if(preg_match("/^\/\w+/", $req_path)) {
            $request['php_admin_resource'] = explode('?', $path[0])[0];
        }

        if(preg_match("/^\/\w+\/create/", $req_path)) {
            $request['php_admin_create'] = '';
        }
        
        else if(preg_match("/^\/\w+\/edit\/\w/", $req_path)) {
            $request['php_admin_edit'] = '';
            if(isset($path[2])) {
                $request['uid'] = $path[2];
            }
        }
        
        else if(preg_match("/^\/\w+\/show\/\w/", $req_path)) {
            $request['php_admin_show'] = '';
            if(isset($path[2])) {
                $request['uid'] = $path[2];
            }
        }
        
        else if(preg_match("/^\/\w+\/delete\/\w/", $req_path)) {
            $request['php_admin_delete'] = '';
            if(isset($path[2])) {
                $request['uid'] = $path[2];
            }
        }
        
        else if(preg_match("/^\/\w+\/save/", $req_path)) {
            $request['php_admin_save'] = '';
        }
        
        else if(preg_match("/^\/\w+\/update/", $req_path)) {
            $request['php_admin_update'] = '';
            if(isset($path[2])) {
                $request['uid'] = $path[2];
            }
        }

        else if(preg_match("/^\/\w+\/export/", $req_path)) {
            $request['php_admin_export'] = '';
        }

        // core
        $req = array_merge($_REQUEST, $request);
        $req = array_merge($req, get_request_files());
        self::$request = (Object)$req;

        foreach (self::$request as $key => $value) {
            $this->$key = $value;
        }
    }

}

function request(String $key) {
    return Request::$request->$key ?? null;
}

?>