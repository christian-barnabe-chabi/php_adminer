<?php
use Services\Auth;
use Services\DateFormater;

?>

<?php
    if(isset($data['notifications']) AND !empty($data['notifications'])) {
        foreach ($data['notifications'] as $notification) {
?>


<div class="ui icon message ?>">
    <i class="ui envelope outline icon"></i>
    <i class="ui close icon"></i>
    <div class="content">
        <div class="header">
            <?= $notification->title ?>
        </div>
        <p><?= $notification->content ?></p>
        <p><a class='ui button' href='<?= $notification->action ?>'>Action</a></p>
        <div class="footer">
            <form action="" method="post">
                <input type="hidden" name="mark_as_read" value="<?= $notification->id ?>">
                <input type="submit" value="Mark as read" class='uk-button-link uk-button'>
            </form>
            <!-- <a href="#" onclick="" class='uk-link-reset uk-text-blue'><i class="ui open envelope outline icon"></i> Marquer comme lue</a> -->
        </div>

        <div class="uk-text-right">
            <small class=""> <?= DateFormater::format(app('date_format'), $notification->publish) ?> </small>
        </div>
    </div>
</div>

<?php
        }
    } else {
?>
    <h3 class='uk-text-center'>Vous n'avez aucune notification</h3>
<?php
    }
?>