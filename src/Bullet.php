<?php

namespace Phpshooter;

use Serafim\SDL\Image\Image;
use Serafim\SDL\SurfacePtr;
use Serafim\SDL\Rect;

final class Bullet extends Box
{
    protected int $width = 16;

    public function setPosition($playerX, $playerY, $playerW, $playerH)
    {
        $this->rect->y = $playerY - $playerH + 15;
        $this->rect->x = $playerX + (($playerW/2) - ($this->rect->w/2));
    }

    public function shoot()
    {
        $this->y_vel = -self::SPEED;
        $this->rect->y += $this->y_vel / 20;
    }
}
