<?php

namespace Acewings\EasyApi\Models;

use Acewings\EasyApi\Traits\BaseApi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use ReflectionClass;

abstract class BaseApiModel extends Model
{
    use BaseApi;
    public static function BootRoutes()
    {
        $Reflector= new ReflectionClass(static::class);
            Route::prefix($Reflector->getShortName())->group(function () {
                Route::get('paginateAll',[static::class,'paginateAll']);
                Route::get('getOneById/{id}',[static::class,'getOneById']);
                Route::delete('deleteOneById/{id}',[static::class,'deleteOneById']);
                Route::put('updateResource/{id}',[static::class,'updateResource']);
                Route::patch('patchResource/{id}',[static::class,'patchResource']);
            })
        ;
    }
}
