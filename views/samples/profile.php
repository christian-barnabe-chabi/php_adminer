
    <?php
        use Services\Auth;
        use Services\Presenter;
    ?>

            <div class="ui stackable grid one column centered">
                <div class="row">
                    <img src="./assets/img/profile_user.jpg" alt="" srcset="" class="ui image circular avatar small" style="height: 128px; width: 128px">
                </div>
        
                <h3 class="uk-margin-remove"><?= ucwords(Auth::user()->name) ?></h3>
                
                <div class="row">
                    <a href="" class="uk-link-reset">
                        <i class="ui edit icon"></i>
                        Modify
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
                                            <a class=" uk-link-reset" href="#">
                                                <i class="ui icon lock"></i>
                                                Lock account
                                            </a>
                                        </li>
                                        <li>
                                            <a class=" uk-link-reset" href="#">
                                                <i class="ui icon trash"></i>
                                                Delete Account
                                            </a>
                                        </li>
                                        <li>
                                            <a class=" uk-link-reset" href="#">
                                                <i class="ui icon building"></i>
                                                Change role
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
        
                            <div class="column twelve wide">
                                <h3 class="">About</h3>
                                <div class="ui two column stackable container grid">
                                    <div class="column">
                                        <table class="uk-table uk-table-divider uk-table-hover">
                                            <tr>
                                                <th class=" uk-text-left">Firstname</th>
                                                <td>Jenny</td>
                                            </tr>
                                            <tr>
                                                <th class=" uk-text-left">Lastname</th>
                                                <td>Hess</td>
                                            </tr>
                                            <tr>
                                                <th class=" uk-text-left">Username</th>
                                                <td><?php echo Auth::user()->name?></td>
                                            </tr>
                                            <tr>
                                                <th class=" uk-text-left">Email</th>
                                                <td><?php echo Auth::user()->email ?></td>
                                            </tr>
                                        </table>
                                    </div>
                
                                    <div class="column">
                                        <table class="uk-table uk-table-divider uk-table-hover">
                                            <tr>
                                                <th class=" uk-text-left">Phone</th>
                                                <td>78 453 64 23</td>
                                            </tr>
                                            <tr>
                                                <th class=" uk-text-left">Phone (alt)</th>
                                                <td>78 456 43 23</td>
                                            </tr>
                                            <tr>
                                                <th class=" uk-text-left">Role</th>
                                                <td>
                                                    <span class="ui label small yellow"><?php echo Auth::user()->name?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class=" uk-text-left">Last sign in</th>
                                                <td>2020/04/12 - 21:18</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                    </div>
                </div>
            </div>
