<?php

use Services\Translation;

if(!isset($data)) $data = [];

    if(!key_exists('error_info', $data)) $data['error_info'] = Translation::translate('permission_error') ;
    if(!key_exists('error_code', $data)) $data['error_code'] = "401";
    if(!key_exists('error_description', $data)) $data['error_description'] = Translation::translate('denied');

    echo "
    <div class='ui icon message yellow large' style='word-break: break-all'>
        <i onclick='window.history.back()' class='close icon'></i>
        <i class='exclamation triangle icon'></i>
        <div class='content'>
            <div class='header'>
            ". Translation::translate('error') ."  ". $data['error_code'] ." - ". $data['error_info'] ."
            </div>
            <div class='divider'></div>
            <div>". $data['error_description'] ."</div>
        </div>
    </div>";
    exit();
?>
