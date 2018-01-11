<?php

namespace Sevenpointsix\Zfw;

use Illuminate\Support\Facades\Facade;

class ZfwFacade extends Facade {

    protected static function getFacadeAccessor() {
        return Zfw::class;
    }

}