<?php

namespace App\Resources;

use Abstracts\Resource as AbstractsResource;
use Services\API;
use Services\Auth;
use Services\Presenter;
use Services\Translation;

class Notification extends AbstractsResource {

    public function handle(array $data = [])
    {

        $api = new API();
        $api->header("Authorization", app('authType').' '.Auth::token());

        if(isset($_POST['mark_all_as_read'])) {
            $url = "notifications/mark_all_as_read/";
            $api->post(app('baseUrl').$url);
        }

        if(isset($_POST['mark_as_read'])) {
            $url = "notifications/mark_as_read/".$_POST['mark_as_read'];
            $api->post(app('baseUrl').$url);
        }

        config('date_format', 'd/m/Y');

        echo "<h3 class='uk-heading-divider'>{$this->go_back()} <i class='ui inbox icon'></i>".
         Translation::translate('notification') ." 
            <form class='uk-display-inline' method='post'>
                <button class='ui mini button' type='submit' name='mark_all_as_read'> 
                    <i class='ui open envelope icon'></i>" .Translation::translate('mark all as read').
                "</button>
            </form></h3>";

        $url = 'notifications';
        $api->get(app('baseUrl').$url);

        Presenter::present('notification', ['notifications' => $api->response()]);
    }
}

?>