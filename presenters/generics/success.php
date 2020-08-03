<?php

use Services\Translation;

if(!isset($data)) $data = [];

    if(!key_exists('success_info', $data)) $data['success_info'] = Translation::translate('success');
    if(!key_exists('success_code', $data)) $data['success_code'] = "200";
    if(!key_exists('success_description', $data)) $data['success_description'] = Translation::translate('operation_done');


    try {
        $success_description = json_decode($data['success_description']);
        $success_message = $success_description->message ?? $data['success_description'];
    } catch (\Throwable $th) {
        $success_message = Translation::translate('success');
    }
?>

<div class="ui modal compact mini error">
    <div class="header">
         <?= $data['success_info'] ?>
    </div>
    <div class="content">
        <?= $success_message ?>
    </div>
    <div class="actions">
        <button onclick="$('.ui.error.modal').modal('hide'); history.back()" class="ui mini green button"> <?= Translation::translate('continue') ?> </button>
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