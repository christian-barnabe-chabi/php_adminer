<?php

namespace App\Resources;

use Abstracts\Resource as AbstractsResource;
use Services\Auth;
use Services\Presenter;
use Services\Router;
use Services\Translation;

class Login extends AbstractsResource {

    public function handle(array $data = [])
    {
        if(!app('must_auth') || Auth::user()) {
            Router::redirect(app('entrypoint'));
        }

        $data = (Object) $data;
    
        if (isset($data->email) and isset($data->password)) {
            new Auth($data->email, $data->password);
    
            $data->error = Translation::translate('login_error');
            
        }
        if( ! Auth::user() ) {
            Presenter::present('sign-in', (Array)$data);
            exit();
        }
    }
}

?>