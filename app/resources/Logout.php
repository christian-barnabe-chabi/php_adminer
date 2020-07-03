<?php

namespace App\Resources;

use Abstracts\Resource as AbstractsResource;
use Services\Router;

class Logout extends AbstractsResource {

    public function handle(array $data = [])
    {
        unset($_SESSION['oauth']);
        unset($_SESSION['ENV']);
        Router::redirect("login");
    }
}

?>