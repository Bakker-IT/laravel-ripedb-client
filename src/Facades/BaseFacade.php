<?php

namespace Bakkerit\LaravelRipedbClient\Facades;

use Illuminate\Support\Facades\Facade;

class BaseFacade extends Facade
{

    public static function getFacadeAccessor(){
        return 'ripedb.' . lcfirst(class_basename(static::class));
    }

}
