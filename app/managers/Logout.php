<?php

namespace App\Managers;

use Abstracts\Resource as AbstractsResource;
use Services\Router;

class Logout extends AbstractsResource {

    public function __construct()
    {
        unset($_SESSION['oauth']);
        Router::redirect("login");
    }

    public function handle(array $data = [])
    {
        
    }
}

?>