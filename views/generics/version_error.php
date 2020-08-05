<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>

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

use Services\Presenter;
use Services\Translation;

echo "<div class='ui container fluid'>
    <div class='uk-text-center'>
        <span uk-spinner='ratio: 4.5' id='spinner'></span>
    </div>

    <div class='uk-position-relative uk-padding-small' id='main-container'>";

    if(!isset($data)) $data = [];

    if(!key_exists('error_info', $data)) 
        $data['error_info'] = Translation::translate('php_version_error');//"PHP Version error";
    if(!key_exists('error_code', $data)) 
        $data['error_code'] = "";
    if(!key_exists('error_description', $data)) 
        $data['error_description'] = Translation::translate('php_version_error_desc')." ".phpversion();

    echo "
    <div class='ui icon message orange large'>
        <i class='exclamation icon'></i>
        <div class='content'>
            <div class='header'>
            ". Translation::translate('error') ."  ". $data['error_code'] ." - ". $data['error_info'] ."
            </div>
            <div class='divider'></div>
            <p>". $data['error_description'] ."</p>
        </div>
    </div>";

    // echo " <h2 class=' uk-text-center uk-heading-large'>". $data['error_info'] ."</h2>";

    // echo "<h1 class=' uk-text-center uk-text-danger uk-heading-2xlarge'>". $data['error_code'] ."</h1>";

    // echo "<p class=' uk-text-center uk-text-meta'>". $data['error_description'] ."</p>";
    
    echo "
    </div>
    ";
    exit();

?>