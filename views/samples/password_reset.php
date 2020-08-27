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
                <h4><?= Translation::translate('reset_password_full_text') ?></h4>
                
                <?php if(!isset($data['reset_email_sent'])): ?>

                    
                <form class="ui segment <?= app('primaryColor') ?>" action="" method="post">
                    <small><?= Translation::translate('reset_password_indication') ?></small>
                    <div class="ui divider"></div>
                    <div class="ui form">
                        <div class="field">
                            <label for="email"><?= Translation::translate('email') ?></label>
                            <input id="email" required type="text" placeholder="Email" name="reset_email" value=""  >
                        </div>
                        <div class="field">
                            <button class="ui button <?= app('primaryColor') ?> fluid"><?= Translation::translate('reset_password') ?></button>
                        </div>
                        <div class="field uk-text-right">
                            <a href="<?= Resource::link('login') ?>" class='uk-link-muted uk-link-reset ui label blue'> <?= Translation::translate('login') ?> </a>
                        </div>
                    </div>
                </form>

                <?php else :?>

                <div class="ui segment <?= app('primaryColor') ?>">
                    <small><?= Translation::translate('reset_password_check_email_indication') ?></small>
                    <div class="ui divider"></div>
                    <a href="<?= Resource::link('login') ?>" class='uk-link-muted uk-link-reset ui label blue'> <?= Translation::translate('login') ?> </a>
                </div>

                <?php endif;?>
            </div>

            <div class="ui vertical divider"><?= Translation::translate('welcome') ?></div>
        </div>
    </div>
</div>