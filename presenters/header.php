<?php 
    $primary_color = app('primary_color');
    $inverted = app('colorful') ? 'inverted' : '';
?>

<div class="ui <?= $inverted ?> <?= $primary_color ?> menu fixed top grid desktop-only" id="topNavbar" style="z-index: 100; border-radius: 0px !important;">
    <nav class="ui left menu secondary uk-padding-small" uk-grid>
        <!-- brand -->
        <div class="item" style="margin: 0px; padding: 0px; margin-right: 10px;">
            <a href="/<?= app('entrypoint') ?>" class="link uk-link-reset">
                <?php if(app('icon')): ?>
                    <img class="ui middle aligned mini image squared" src="<?= app('icon') ?>">
                <?php endif; ?>

                <?php if(app('app_name')): ?>
                    <h3 class="ui aligned middle uk-margin-remove" style="display: inline-block !important; color: <?= app('colorful') ? 'white' : 'grey' ?>;">
                    <?= app('app_name') ?>
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

<?php

use Services\API;
use Services\Auth;
use Services\Resource;
use Services\Translation;

if(!empty(Auth::user())):
            $avatar = "/assets/img/profile_user.jpg";
            if(isset(Auth::user()->avatar)) {
                $avatar = Auth::user()->avatar;
            }
            
        ?>

            <!-- notification -->
            <?php
                // get notfication count to $notifications_count
                $notifications_count = 4;
            ?>
            <a href="<?= Resource::link('notification') ?>" class="item ui floating tiny button icon circular" style="margin: 0px;">
                <i class="ui bell outline large icon"></i>
                <span class="ui floating circular basic mini label <?= $primary_color ?>"><?= $notifications_count ?></span>
            </a>


            <!-- user avatar & options -->

            <div class="item" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px;">
                <div class="ui pointing dropdown top right" style="margin: 0px; padding: 0px;">
                    <div class="ui">
                        <img class="ui avatar image" src="<?= $avatar ?>" style="width: 45px; height: 45px;"> <?= ucwords(Auth::user()->username) ?>
                        <i class="dropdown icon"></i>
                    </div>
                    <div class="ui secondary vertical menu">
                        <div class="item" style="background: transparent !important;">
                            <div class="ui one column grid">
                                <div class="column uk-text-center" style="padding: 2px;">
                                    <img src="<?= Auth::user()->avatar ?>" alt="" srcset="" class='ui tiny avatar image' style="width: 80px; height: 80px;">
                                </div>
                                <div class='column uk-text-center'>
                                    <span><?= ucwords(Auth::user()->username) ?></span>
                                    <br>
                                    <small class='ui subtitle'><?= ucwords(Auth::user()->email) ?></small>
                                    <br>
                                    <small class='ui label tiny subtitle'><?= ucwords(Auth::user()->role->type) ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="divider"></div>
                        <!-- profile -->
                        <a href='/user/show/<?= Auth::user()->id ?>' class="item">
                            <i class="ui address book outline icon"></i> Mon compte
                        </a>
                        <a href='<?= Resource::link('profile') ?>' class="item">
                            <i class="ui user outline icon"></i> Profile
                        </a>
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
                if(!app('must_auth')):
        ?>

            <!-- if not loged in -->
            <div class="item">
                <span class="">
                    <i class="ui user icon"></i> Guest
                </span>
            </div>
        <?php 
                endif; 
            endif;
        ?>
    </nav>
</div>

<div id="underHeader"></div>

<?php
    if(app('must_auth'))
    {
        if(Auth::user()) {
            new MenuScaffold();
        } 
    } else {
        new MenuScaffold();        
    }
?>