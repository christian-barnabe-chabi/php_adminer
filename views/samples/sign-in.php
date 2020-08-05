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
                        <img src="https://societegenerale.sn/fileadmin/user_upload/logos/senegal.svg" alt="" srcset="" class='ui image medium'>
                    </div>
                </div>
            </div>

            <div class="column ui five wide">
                <form class="ui segment <?= app('primaryColor') ?>" action="" method="post">
                    <small class=" uk-text-center uk-text-danger"><?= isset($data['error']) ? $data['error'] : '' ?></small>
                    <div class="ui form">
                        <div class="field">
                            <label for="email"><?= Translation::translate('email') ?></label>
                            <input id="email" type="text" placeholder="Email" name="email" value="<?= isset($data['email']) ? $data['email'] : '' ?>"  >
                        </div>
                        <div class="field">
                            <label for="password"><?= Translation::translate('password') ?></label>
                            <input id="password" type="password" placeholder="Password" name="password" value="">
                        </div>
                        <div class="field">
                            <button class="ui button <?= app('primaryColor') ?> fluid"><?= Translation::translate('login') ?></button>
                        </div>
                        <div class="field uk-text-right">
                            <!-- <a href="<?= Resource::link('password_reset') ?>" class='uk-link-muted uk-link-reset ui label orange'> <?= Translation::translate('password_forgot') ?> </a> -->
                            <a href="http://reptxstudios.com:8000/password/reset" class='uk-link-muted uk-link-reset ui label grey'> <?= Translation::translate('password_forgot') ?> </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="ui vertical divider"><?= Translation::translate('welcome') ?></div>
        </div>
    </div>
</div>