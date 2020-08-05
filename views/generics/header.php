<?php 

use Services\API;
use Services\Auth;
use Services\Resource;
use Services\Translation;

    $primary_color = app('primaryColor');
    $inverted = app('colorful') ? 'inverted' : '';
    $bg_white = empty($inverted)  ? 'background: white' : '';
?>

<div class="ui <?= $inverted ?> <?= $primary_color ?> menu fixed top grid desktop-only" id="topNavbar" style="z-index: 100; border-radius: 0px !important; <?= $bg_white ?>">
    <nav class="ui left menu secondary uk-padding-small" uk-grid>
        <!-- brand -->
        <div class="item" style="margin: 0px; padding: 0px; margin-right: 10px;">
            <a href="/<?= app('entrypoint') ?>" class="link uk-link-reset">
                <?php if(app('icon')): ?>
                    <img class="ui middle aligned mini image squared" src="<?= app('icon') ?>">
                <?php endif; ?>

                <?php if(app('appName')): ?>
                    <h3 class="ui aligned middle uk-margin-remove" style="display: inline-block !important; color: <?= app('colorful') ? 'white' : 'grey' ?>;">
                    <?= app('appName') ?>
                    </h3>
                <?php else: ?>
                    <h3 class="ui aligned middle uk-margin-remove" style="display: inline-block !important; color: <?= app('colorful') ? 'white' : 'grey' ?>;">
                        PHP API ADMIN +
                    </h3>
                <?php endif; ?>
            </a>
        </div>
    </nav>


    <nav class="ui right menu secondary uk-padding-small">

        <div class="item uk-margin-remove uk-padding-remove">
            <label><?= Translation::translate('night_mode') ?></label>
            <div class="ui toggle fitted tiny checkbox uk-margin-left" id="selected-theme">
                <input type="checkbox" name="public">
            </div>
        </div>

<?php

if(!empty(Auth::user())):
            $avatar = "/assets/img/profile_user.jpg";
            if(isset(Auth::user()->avatar)) {
                $avatar = Auth::user()->avatar;
            }
            
        ?>
            <a href="<?= Resource::link('notification') ?>" class="item ui floating tiny button icon circular" style="margin: 0px;">
                <i class="ui bell outline large icon"></i>
                <span class="ui floating circular basic mini label <?= $primary_color ?>">0</span>
            </a>


            <!-- user avatar & options -->

            <div class="item" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px;">
                <div class="ui pointing dropdown top right" style="margin: 0px; padding: 0px;">
                    <div class="ui">
                        <img class="ui avatar image" src="<?= $avatar ?>" style="width: 45px; height: 45px;"> <?= ucwords(Auth::user()->name ?? "User") ?>
                        <i class="dropdown icon"></i>
                    </div>
                    <div class="ui secondary vertical menu">
                        <div class="item" style="background: transparent !important;">
                            <div class="ui one column grid">
                                <div class="column uk-text-center" style="padding: 2px;">
                                    <img src="<?= $avatar ?>" alt="" srcset="" class='ui tiny avatar image' style="width: 80px; height: 80px;">
                                </div>
                                <div class='column uk-text-center'>
                                    <span><?= ucwords(Auth::user()->name ?? 'User') ?></span>
                                    <br>
                                    <small class='ui subtitle'><?= Auth::user()->email ?? 'user@example.com' ?></small>
                                    <br>
                                    <small class='ui label tiny subtitle'><?= Auth::user()->role_ ?? 'admin account' ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="divider"></div>
                        
                        <!-- logout -->
                        <a class="ui uk-link-reset uk-link-muted item uk-margin-remove uk-padding-remove" href='/logout'>
                            <i class="ui logout icon"></i> <?= Translation::translate('logout') ?>
                        </a>
                    </div>
                </div>
            </div>

        <?php 
            else:
                if(!app('mustAuth')):
        ?>

            <!-- if not loged in -->
            <div class="ui">
                <img class="ui avatar image" src="/assets/img/profile_user.jpg" style="width: 45px; height: 45px;"> <?= Translation::translate('guest') ?>
            </div>
        <?php 
                endif; 
            endif;
        ?>
    </nav>
</div>

<div id="underHeader"></div>

<?php
    if(app('mustAuth'))
    {
        if(Auth::user()) {
            new MenuScaffold();
        } 
    } else {
        new MenuScaffold();        
    }
?>
