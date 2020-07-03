<?php

namespace Phpschooter;

use Serafim\SDL\Image\Image;
use Serafim\SDL\SurfacePtr;
use Serafim\SDL\Rect;

final class Player extends Box
{
    public function resetMove()
    {
        $this->x_vel = 0;
        $this->y_vel = 0;
    }

    public function up()
    {
        $this->y_vel = -self::SPEED;
        $this->rect->y += $this->y_vel / 60;
    }

    public function down()
    {
        $this->y_vel = self::SPEED;
        $this->rect->y += $this->y_vel / 60;
    }

    public function left()
    {
        $this->x_vel = -self::SPEED;
        $this->rect->x += $this->x_vel / 40;
    }

    public function right()
    {
        $this->x_vel = self::SPEED;
        $this->rect->x += $this->x_vel / 40;
    }

    public function initializeMovement(?bool $up = false, ?bool $right = false, ?bool $down = false, ?bool $left = false)
    {
        if($up) $this->up();
        if($down) $this->down();
        if($left) $this->left();
        if($right) $this->right();
    }

    public function bullet($sdl, $renderer)
    {
        return new Bullet($sdl, $renderer, 'bullet.png');
    }
    public function initializeFire($fire, $bullet)
    {
        if($fire) 
            $bullet->shoot();
        if(!$fire) 
            $bullet->setPosition($this->rect->x, $this->rect->y, $this->rect->w, $this->rect->h);
    }

}
