<?php

namespace Abstracts;

use Services\Request;
use Services\Translation;

abstract class Resource {
    /**
     * handle incoming request
     */
    public abstract function handle(array $data = []);

    /**
     * history back
     */
    protected function go_back() {
        $primary_color = app('primary_color');
        return "<a onclick='window.history.back();' class='ui butto mini $primary_color uk-button'><i class='ui left arrow icon small'></i>". Translation::translate("back") ."</a>";
    }
}

?>