<?php

namespace laravelstudio\laravelgooglesheetintegration\facades;

use Illuminate\Support\Facades\Facade;
use App;

/**
 * @method static \Intervention\Image\Image make(mixed $data)
 * @method static self configure(array $config)
 * @method static \Intervention\Image\Image canvas(int $width, int $height, mixed $background = null)
 * @method static \Intervention\Image\Image cache(\Closure $callback, int $lifetime = null, boolean $returnObj = false)
 */
class googlesheet extends Facade
{
    protected static function getFacadeAccessor()
    {        
        return 'googleSheet';
    }
}
