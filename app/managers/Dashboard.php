<?php

namespace App\Managers;

use Abstracts\Resource;
use Services\API;
use Services\Auth;
use Services\Presenter;
use Services\Request;

class Dashboard extends Resource {

    private $data;

    public function __construct() {    
        $api = new API();
        $api->header("Authorization", app('authType').' '.Auth::token());
        $api->get(app('baseUrl').'partners_enterprises');

        $this->data = [
            'partners' => null,
            'smes' => null,
        ];
        
        $this->data['partners'] = $api->response();

        $api->get(app('baseUrl').'smes_enterprises');
        $this->data['smes'] = $api->response();
    }

    public function handle(array $data = [])
    {


        if(Auth::user()->from != 'house') {
            Presenter::present('generics.unauthorised');
        }
        
        // else if(Auth::user()->from == 'partner') {
        //     Presenter::present('dashboard_partner');
        // }

        // else if(Auth::user()->from == 'sme') {
        //     Presenter::present('dashboard_sme');
        // }

        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/views/dashboard.php')) {
            Presenter::present('dashboard', $this->data);
        } else {
            Presenter::present('generics.dashboard', (Array)$data);
        }
    }
}

?>
