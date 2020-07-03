<?php

use Services\Translation;

if(!isset($data)) $data = [];

    if(!key_exists('success_info', $data)) $data['success_info'] = Translation::translate('success');
    if(!key_exists('success_code', $data)) $data['success_code'] = "200";
    if(!key_exists('success_description', $data)) $data['success_description'] = Translation::translate('operation_done');


    echo "
    <div class='ui icon message success large'>
        <i onclick='window.history.back()' class='close icon'></i>
        <i class='thumbs up outline icon'></i>
        <div class='content'>
            <div class='header'>
            ". $data['success_code'] ." - ". $data['success_info'] ."
            </div>
            <div class='divider'></div>
            <p>". $data['success_description'] ."</p>
        </div>
    </div>";
    exit();
?>