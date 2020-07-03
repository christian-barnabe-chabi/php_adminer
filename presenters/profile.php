
    <?php
        use Services\Auth;
use Services\DateFormater;
use Services\Presenter;
use Services\Resource;
use Services\Translation;

?>

            <div class="ui stackable grid one column centered">
                <div class="row">
                    <img src="<?= Auth::user()->avatar ?>" alt="" srcset="" class="ui image circular avatar small" style="height: 128px; width: 128px">
                </div>
        
                <h3 class="uk-margin-remove"><?= ucwords(Auth::user()->name) ?></h3>
                
                <div class="row">
                    <h3><?= Auth::user()->username ?></h3>
                </div>

                <div class="row">
                    <a href="/user/edit/<?= Auth::user()->id ?>" class="uk-link-reset">
                        <i class="ui edit icon"></i>
                        <?= Translation::translate('edit') ?>
                    </a>
                </div>
        
                <div class="row ui divider"></div>
        
                <div class="row">
                    <div class="column">
                        
                        <div class="ui two column stackable grid centered">
                            <div class="column four wide desktop-only">
                                <div>
                                    <h3>Actions</h3>
                                    <ul class="uk-list uk-list-large">
                                        <li>
                                            <a class=" uk-link-reset" href=" <?= Resource::link('notification') ?> ">
                                                <i class="ui inbox icon"></i>
                                                <?= Translation::translate('notification') ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a class=" uk-link-reset" href="/user/edit/<?= Auth::user()->id ?>">
                                                <i class="ui edit icon"></i>
                                                <?= Translation::translate('edit') ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a class=" uk-link-reset" href="/logout">
                                                <i class="ui logout icon"></i>
                                                <?= Translation::translate('logout') ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
        
                            <div class="column twelve wide">
                                <h3 class=""><?= Translation::translate('about') ?></h3>
                                <div class="ui two column stackable container grid">
                                    <div class="column">
                                        <table class="uk-table uk-table-divider no-sort">
                                            <tr>
                                                <th class=" uk-text-left"><?= Translation::translate('firstname') ?></th>
                                                <td><?= Auth::user()->firstname ?></td>
                                            </tr>
                                            <tr>
                                                <th class=" uk-text-left"><?= Translation::translate('lastname') ?></th>
                                                <td><?= Auth::user()->lastname ?></td>
                                            </tr>
                                            <tr>
                                                <th class=" uk-text-left"><?= Translation::translate('username') ?></th>
                                                <td><?php echo Auth::user()->username?></td>
                                            </tr>
                                            <tr>
                                                <th class=" uk-text-left"><?= Translation::translate('email') ?></th>
                                                <td><?php echo Auth::user()->email ?></td>
                                            </tr>
                                        </table>
                                    </div>
                
                                    <div class="column">
                                        <table class="uk-table uk-table-divider no-sort">
                                            <tr>
                                                <th class=" uk-text-left"><?= Translation::translate('phone') ?></th>
                                                <td><?= Auth::user()->phone ?></td>
                                            </tr>
                                            <tr>
                                                <th class=" uk-text-left"><?= Translation::translate('phone_alt') ?></th>
                                                <td><?= Auth::user()->phone_alt ?></td>
                                            </tr>
                                            <tr>
                                                <th class=" uk-text-left"><?= Translation::translate('role') ?></th>
                                                <td>
                                                    <span class="ui label small <?= app('primary_color') ?>"><?php echo Auth::user()->role->type?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class=" uk-text-left"><?= Translation::translate('created_at') ?></th>
                                                <td><?= DateFormater::format(app('date_format'), Auth::user()->created_at) ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                    </div>
                </div>
            </div>
