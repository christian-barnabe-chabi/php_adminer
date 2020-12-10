<?php

namespace Services\Scaffolders;

use Abstracts\BaseBlueprint;
use Services\Scaffolders\Embeded\CreateGuesser;
use Services\Scaffolders\Embeded\EditGuesser;

class CustomBlueprint extends BaseBlueprint {
    protected $embeded = true;

    public function embeded_create(string $parent)
    {
        return CreateGuesser::render($this, $parent);
    }

    public function embeded_edit($data,string $parent)
    {
        return EditGuesser::render($this, (Object) $data, $parent);
    }
}

?>