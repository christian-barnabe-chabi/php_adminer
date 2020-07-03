<?php

use Services\Translation;

if(!isset($data)) $data = [];

    if(!key_exists('error_info', $data)) $data['error_info'] = Translation::translate('failure') ;
    if(!key_exists('error_code', $data)) $data['error_code'] = "100";
    if(!key_exists('error_description', $data)) $data['error_description'] = "";

    echo "
    <div class='ui icon message orange large' style='word-break: break-all'>
        <i onclick='window.history.back()' class='close icon'></i>
        <i class='thumbs down outline icon'></i>
        <div class='content'>
            <div class='header'>
            ". Translation::translate('error') ."  ". $data['error_code'] ." - ". $data['error_info'] ."
            </div>
            <div class='divider'></div>
            <div>". $data['error_description'] ."</div>
        </div>
    </div>";

    // echo " <h2 class=' uk-text-center uk-heading-large'>". $data['error_info'] ."</h2>";

    // echo "<h1 class=' uk-text-center uk-text-danger uk-heading-2xlarge'>". $data['error_code'] ."</h1>";

    // echo "<p class=' uk-text-center uk-text-meta'>". $data['error_description'] ."</p>";
    exit();
?>
