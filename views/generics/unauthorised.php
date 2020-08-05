<?php

use Services\Router;
use Services\Translation;

if(!isset($data)) $data = [];

    if(!key_exists('error_info', $data)) $data['error_info'] = Translation::translate('denied') ;
    if(!key_exists('error_code', $data)) $data['error_code'] = "401";
    if(!key_exists('error_description', $data)) $data['error_description'] = Translation::translate('permission_error');

    try {
        $error_description = json_decode($data['error_description']);
        $error_message = $error_description->message ?? $data['error_description'];
    } catch (\Throwable $th) {
        $error_message = Translation::translate('failure');
    }
?>

<div class="ui modal compact mini error">
    <div class="header">
         <?= Translation::translate('error') .' '. $data['error_code'] .' - '. $data['error_info'] ?>
    </div>
    <div class="content">
        <?= $error_message ?>
    </div>
    <div class="actions">
        <button onclick="$('.ui.error.modal').modal('hide'); window.location.href ='<?= Router::backLink() ?>'" class="ui mini orange button"> <?= Translation::translate('back') ?> </button>
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
