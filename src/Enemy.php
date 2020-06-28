<?php

namespace Phpschooter;

use FFI;
use Serafim\SDL\Image\Image;
use Serafim\SDL\SurfacePtr;
use Serafim\SDL\Rect;

final class Enemy extends Box
{
    const LIVE_TIME = 3; // sec.

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
        $buffer = \FFI::new('char[256]');
        $this->sdl->SDL_AddTimer(
            500, 
            function ($delay, $params) {

                if($this->remainingLiveTime <= 0) {
                    $this->reborn();
                }

                $this->decreaseLifeTime();
            
                return $delay;
            }, 
            \Serafim\SDL\SDL::addr($buffer)
        );
    }

}
