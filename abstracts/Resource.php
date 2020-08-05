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
        $referer = htmlspecialchars($_SERVER['HTTP_REFERER']);
        return "<a href='$referer' class='ui uk-button'><i class='ui left arrow icon small'></i>". Translation::translate("back") ."</a>";
    }
}

?>