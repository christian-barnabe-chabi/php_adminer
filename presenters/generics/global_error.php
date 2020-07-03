<?php

use Services\Presenter;
use Services\Translation;
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Translation::translate('error').' | '. app('app_name') ?></title>

    <script src="/assets/js/jquery.js"></script>

    <link rel="stylesheet" href="/assets/SemanticUI/semantic.min.css">
    <script src="/assets/SemanticUI/semantic.min.js"></script>
    <script src="/assets/SemanticUI/tablesort.js"></script>

    
    <link rel="stylesheet" href="/assets/uikit/css/uikit.min.css">
    <link rel="stylesheet" href="/assets/uikit/css/uikit-rtl.min.css">
    <script defer src="/assets/uikit/js/uikit-icons.min.js"></script>
    <script defer src="/assets/uikit/js/uikit.min.js"></script>
    
    <link rel="stylesheet" href="/assets/css/main.css">
    <script src="/assets/js/main.js"></script>
</head>

<?php


Presenter::present('header');


    echo "<div class='ui container fluid'> <div class='uk-position-relative uk-padding-small' id='main-container'>";

    if(!isset($data)) $data = [];


    if(!key_exists('error_info', $data)) $data['error_info'] = Translation::translate('failure') ;
    if(!key_exists('error_code', $data)) $data['error_code'] = "100";
    if(!key_exists('error_description', $data)) $data['error_description'] = "";

    echo "
    <div class='ui icon message orange large'>
        <i onclick='window.history.back()' class='close icon'></i>
        <i class='thumbs down outline icon'></i>
        <div class='content'>
            <div class='header'>
            ". Translation::translate('error') ."  ". $data['error_code'] ." - ". $data['error_info'] ."
            </div>
            <div class='divider'></div>
            <p>". $data['error_description'] ."</p>
        </div>
    </div>";

    echo "
    </div>
    ";

    exit();

?>