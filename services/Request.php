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

        $request['uid'] = $_REQUEST['php_admin_uid'] ?? null;
        if( isset($_REQUEST['php_admin_action']) ) {
            switch($_REQUEST['php_admin_action']) {
    
                case 'show':
                    $request['php_admin_show'] = '';
                break;
    
                case 'delete':
                    $request['php_admin_delete'] = '';
                break;
    
                case 'save':
                    $request['php_admin_save'] = '';
                break;
    
                case 'update':
                    $request['php_admin_update'] = '';
                break;
    
                case 'export':
                    $request['php_admin_export'] = '';
                break;
            }
        }
        
        else if(preg_match("/^\/\w+\/show\/\w/", $req_path)) {
            $request['php_admin_show'] = '';
            if(isset($path[2])) {
                $request['uid'] = $path[2];
            }
        }
        
        // core
        $req = array_merge($_REQUEST, $request);
        $req = array_merge($req, get_request_files());
        self::$request = (Object)$req;

        // var_dump($req);

        // exit();

        foreach (self::$request as $key => $value) {
            $this->$key = $value;
        }
    }

}

function request(String $key) {
    return Request::$request->$key ?? null;
}

?>