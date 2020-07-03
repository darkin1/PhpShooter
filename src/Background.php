<?php

namespace Phpshooter;

use Serafim\SDL\Image\Image;
use Serafim\SDL\SurfacePtr;
use Serafim\SDL\Rect;

final class Background extends Box
{
    protected int $height = WINDOW_HEIGHT;
    protected int $width = WINDOW_WIDTH;
}
