<?php

use Services\Resource;
use Services\Translation;

?>
        
<div class="ui containe uk-inline" id="main-container"">
    <div class="ui container">
        
        <div class="ui two column stackable grid centered" id="login_form_container" style="top: 8%">
            <div class="ui column aligned left">
                <div class="uk-position-center-left">
                    <div class=''>
                        <h1><?= app('app_name') ?></h1>
                        <div>
                            <div class="ui divider"></div>
                            <span class="ui mini button black" href="#" >Action 1</span>
                            <span class="ui mini button red" href="#">Action 2</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="column ui five wide">
                <form class="ui segment <?= app('primary_color') ?>" action="" method="post">
                    <small class=" uk-text-center uk-text-danger"><?= isset($data['error']) ? $data['error'] : '' ?></small>
                    <div class="ui form">
                        <div class="field">
                            <label for="email"><?= Translation::translate('email') ?></label>
                            <input required id="email" type="email" placeholder="<?= Translation::translate('email') ?>" name="email" value="<?= $data['email'] ?? '' ?>"  >
                        </div>
                        <div class="field">
                            <label for="password"><?= Translation::translate('password') ?></label>
                            <input required id="password" type="password" placeholder="<?= Translation::translate('password') ?>" name="password" value="">
                        </div>
                        <div class="field">
                            <button class="ui button <?= app('primary_color') ?> fluid"><?= Translation::translate('login') ?></button>
                        </div>
                        <div class="field uk-text-right">
                            <!-- <a href="<?= Resource::link('password_reset') ?>" class='uk-link-muted uk-link-reset ui label orange'> <?= Translation::translate('password_forgot') ?> </a> -->
                            <a target="_blank" href="http://45.79.221.17:8000/password/reset" class='uk-link-muted uk-link-reset ui label grey'> <?= Translation::translate('password_forgot') ?> </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="ui vertical divider"></div>
        </div>
    </div>
</div>