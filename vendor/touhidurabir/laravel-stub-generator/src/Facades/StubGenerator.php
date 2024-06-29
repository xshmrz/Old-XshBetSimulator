<?php

namespace Touhidurabir\StubGenerator\Facades;

use Illuminate\Support\Facades\Facade;

class StubGenerator extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {

        return 'stub-generator';
    }
}