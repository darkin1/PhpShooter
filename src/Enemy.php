<?php

namespace Phpschooter;

use Serafim\SDL\Image\Image;
use Serafim\SDL\SurfacePtr;
use Serafim\SDL\Rect;

final class Enemy extends Box
{

    public function __construct($sdl, $renderer, $textureImg)
    {
        parent::__construct($sdl, $renderer, $textureImg);
        
    }

    public function reborn($windowW)
    {
        $this->rect->x = rand(0, $windowW - $this->rect->w);
        $this->rect->y = rand(30, 300);
    }

}
