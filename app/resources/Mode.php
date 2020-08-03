<?php

namespace App\Resources;

use Abstracts\Resource;
use Services\Router;

use function Services\request;

class Mode extends Resource {

    public function __construct()
    {
        if(in_array(strtolower(request('theme')), ['night', 'light'])) {
            config('theme', strtolower(request('theme')));
        }
        Router::back();
    }

    public function handle(array $data = [])
    {
        
    }
}

?>