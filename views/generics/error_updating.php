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

use App\Resources\User;
use Services\Request;
use Services\Resource;
use Services\Router;
use Services\Translation;

    echo "<div class='ui container fluid'>

    
    <div class='uk-position-relative uk-padding-small' id='main-container'>";
    
    
    if(!isset($data)) $data = [];
    
    if(!key_exists('error_info', $data)) $data['error_info'] = Translation::translate('failure');
    if(!key_exists('error_code', $data)) $data['error_code'] = 100;
    if(!key_exists('error_description', $data)) $data['error_description'] = Translation::translate('error');

    try {
        $error_description = json_decode($data['error_description']);
        $error_message = $error_description->message ?? $data['error_description'];
    } catch (\Throwable $th) {
        $error_message = Translation::translate('failure');
    }
?>

<div class="ui modal compact mini error">
    <div class="header">
        <?php
            if(app('debug'))
                echo Translation::translate('error') .' '. $data['error_code'] .' - ';
        ?>
         <?= $data['error_info'] ?>
    </div>
    <div class="content">
        <?= app('debug') && isset($data['url']) ? '<code>'.$data['url'].'</code><br>' : '' ?>
        <?= $error_message ?>
    </div>
    <div class="actions">
        <button onclick="$('.ui.error.modal').modal('hide'); window.location.href ='<?= Router::backLink() ?>'" class="ui mini red button"> <?= Translation::translate('back') ?> </button>
        <button onclick="$('#create_form').modal({closable:false}).modal('show')" class="ui mini blue button"> <?= Translation::translate('correct_data') ?> </button>
    </div>
</div>

<div class='ui large modal' id='create_form'>
    <i class="ui close icon" onclick="window.location.href ='<?= Router::backLink() ?>'"></i>
    <div class='header'>
        <i class='ui folder open outline icon'></i> | <?= $data['php_admin_resource_class_name_singular'] ?> | <?= Translation::translate('edit') ?> 
    </div>

    <div class='content scrolling'>
        <?php //Request::$request->uid = Request::$request->uid;  ?>
        <?= Resource::call($data['php_admin_resource_class'], $data['data_edited'], 'edit') ?>
    </div>
</div>

<script>
    $('.ui.error.modal').modal({
        detachable: true,
        closable: false,
        transition: 'horizontal flip',
    }).modal('show');
</script>



<?php  exit(); ?>