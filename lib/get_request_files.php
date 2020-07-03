<?php

namespace Lib;

function get_request_files() {

    $req_files = $_FILES;
    $files = [];

    if(function_exists('curl_file_create')) {

        foreach ($req_files as $varname => $value) {
            if(empty($value['tmp_name'])) {
                $files[$varname] = "";
                continue;
            }
            $path = $value['tmp_name'];
            $filename = $value['name'];
            $type = $value['type'];
            $files[$varname] = curl_file_create($path, $type);
        }

    } else {

        foreach ($req_files as $varname => $value) {
            $path = $value['tmp_name'];
            $filename = $value['name'];
            $type = $value['type'];
            $files[$varname] = "@$path;filename=$filename;type=$type";
        }
    }



    return $files;

}

?>