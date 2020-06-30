<?php

namespace Phpschooter;

use FFI;
use Serafim\SDL\Image\Image;
use Serafim\SDL\SurfacePtr;
use Serafim\SDL\Rect;

final class Enemy extends Box
{
    const LIVE_TIME = 2; // sec.

    private $currentTime;
    private $lastTime = 0;
    private $hittedTick = null;

    public $remainingLiveTime = self::LIVE_TIME;

    public function reborn()
    {
        $this->rect->x = rand(0, WINDOW_WIDTH - $this->rect->w);
        $this->rect->y = rand(30, 300);
        $this->remainingLiveTime = self::LIVE_TIME;
    }

    public function shrink()
    {
        $this->hittedTick = $this->sdl->SDL_GetTicks();
        $this->rect->h -= 24;
        $this->rect->w -= 24;
        $this->rect->x += 12;
        $this->rect->y += 12;
    }
    public function restoreAfterShrinkage()
    {
        if(!is_null($this->hittedTick) && $this->hittedTick + 300 <= $this->sdl->SDL_GetTicks()) {
            $this->rect->h += 24;
            $this->rect->w += 24;
            $this->rect->x -= 12;
            $this->rect->y -= 12;
            $this->hittedTick = null;
        }
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
