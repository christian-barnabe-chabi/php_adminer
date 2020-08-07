<?php

namespace Lib;

use Services\Presenter;
use Services\Request;
use Services\Translation;

spl_autoload_register(function($class_name) {
    $class_name = str_replace("\\", DIRECTORY_SEPARATOR, $class_name);
    $class_name = explode(DIRECTORY_SEPARATOR, $class_name);

    for($i=0; $i<count($class_name)-1; $i++) {
        $class_name[$i] = strtolower($class_name[$i]);
    }

    $class_name = implode(DIRECTORY_SEPARATOR, $class_name);

    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$class_name.'.php')) {
        require_once( $_SERVER['DOCUMENT_ROOT'].'/'.$class_name.'.php' );
    } else {
        Presenter::present("generics.error", [
            "error_info" => Translation::translate('class_not_found'),
            "error_code" => 1736,
            "error_description"=> Translation::translate('the_class')." '".$class_name."' ".Translation::translate('is_not_found')
        ]);
        exit('Exit');
    }

});

include_files_in("lib");
include_files_in("config");

// check php version
$version = str_split(phpversion());
$version =  $version[0];
if($version < 6) {
    Presenter::present('generics.version_error');
}

function include_files_in(string $dir) {
    $base_root = $_SERVER['DOCUMENT_ROOT'].'/'.$dir.'/';
    $scan = scandir($base_root);

    foreach ($scan as $file) {

        if(file_exists($base_root.$file) && is_file($base_root.$file)) {
            try {
                require_once($base_root.$file);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }
}

new Request();


// delete temporary files
deldir('temp');

function deldir(string $dir) {
    if(\is_dir($dir)) {
        $files = scandir($dir);
        $files = array_slice($files, 2); // remove '.' and '..'
        foreach ($files as $file) {
            if(\is_dir($dir.'/'.$file)) {
                deldir($dir.'/'.$file);
            }
            unlink($dir.'/'.$file);
        }
        rmdir($dir);
    }
}


?>