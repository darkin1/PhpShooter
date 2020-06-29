<?php

namespace Phpschooter;

use FFI;
use Serafim\SDL\Image\Image;
use Serafim\SDL\SurfacePtr;
use Serafim\SDL\Rect;

final class Enemy extends Box
{
    const LIVE_TIME = 3; // sec.

    private $currentTime;
    private $lastTime = 0;

    public $remainingLiveTime = self::LIVE_TIME;

    public function __construct($sdl, $renderer, $textureImg)
    {
        parent::__construct($sdl, $renderer, $textureImg);
        
        $this->rebornAfterTime();
    }

    public function reborn()
    {
        $this->rect->x = rand(0, WINDOW_WIDTH - $this->rect->w);
        $this->rect->y = rand(30, 300);
        $this->remainingLiveTime = self::LIVE_TIME;
    }

    public function decreaseLifeTime()
    {
        $this->remainingLiveTime--;
    }

    public function rebornAfterTime() 
    {
        $this->currentTime = $this->sdl->SDL_GetTicks();

        if ($this->remainingLiveTime <= 0) {
            $this->reborn();
        }

        if ($this->currentTime > $this->lastTime + 1000) {
            $this->decreaseLifeTime();
            $this->lastTime = $this->currentTime;
        }
    }

}
