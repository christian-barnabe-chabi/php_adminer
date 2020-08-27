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

if(!isset($data)) $data = [];

    if(!key_exists('error_info', $data)) $data['error_info'] = "Failure" ;
    if(!key_exists('error_code', $data)) $data['error_code'] = "100";
    if(!key_exists('error_description', $data)) $data['error_description'] = "";

    try {
        $error_description = json_decode($data['error_description']);
        $error_message = $error_description->message ?? $data['error_description'];
    } catch (\Throwable $th) {
        $error_message = "Failure";
    }
?>

<div class="ui modal compact mini error">
    <div class="header">
         <?= 'Error '. $data['error_code'] .' - '. $data['error_info'] ?>
    </div>
    <div class="content">
        <?= $error_message ?>
    </div>
    <div class="actions">
        <button onclick="$('.ui.error.modal').modal('hide');" class="ui mini yellow button"> Close </button>
        <!-- <button onclick="$('.ui.error.modal').modal('hide')" class="ui mini yellow button"> Cancel </button> -->
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
