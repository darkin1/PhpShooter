<?php

namespace Phpschooter;

use Serafim\SDL\Image\Image;
use Serafim\SDL\SurfacePtr;
use Serafim\SDL\Rect;
use Serafim\SDL\Color;

final class RoundTime extends Box
{
    const ROUND_TIME = 20;

    private $ttl;
    private $font;
    private $color;
    private $currentTime;
    private $lastTime = 0;

    protected int $height = 28;
    protected int $width = 84;

    public float $x_vel = 0;
    public float $y_vel = 0;

    public $remainingTime = self::ROUND_TIME; // sec.

    protected $board;

    public function __construct($sdl, $renderer, $ttf, $board)
    {
        $this->sdl = $sdl;
        $this->renderer = $renderer;
        $this->ttf = $ttf;
        $this->board = $board;
        $this->font;
        $this->color;
        $this->currentTime;
        $this->lastTime;

        $this->includeFont();
        $this->generateTexture();
        $this->generateRect();
    }

    protected function includeFont()
    {
        $this->font = $this->ttf->TTF_OpenFont(__DIR__ . '/../assets/ARCADECLASSIC.TTF', 18);
        $this->color = $this->ttf->new(Color::class);
        $this->color->r = 255;
        $this->color->g = 255;
        $this->color->b = 255;
    }

    protected function generateTexture($unknown = null)
    {
        $surface = $this->ttf->TTF_RenderText_Solid($this->font, 'Time left    ' . $this->remainingTime, $this->color);
        if ($surface === null) {
            throw new \Exception(sprintf('Could not render surface: %s', $this->sdl->SDL_GetError()));
        }

        $this->texture = $this->sdl->SDL_CreateTextureFromSurface(
            $this->renderer,
            $sdlSurface = $this->sdl->cast(SurfacePtr::class, $surface)
        );
        if ($this->texture == null) {
            throw new \Exception(sprintf('Could not render texture: %s', $this->sdl->SDL_GetError()));
        }

        $this->sdl->SDL_FreeSurface($sdlSurface);
    }

    protected function generateRect()
    {
        $this->rect = $this->sdl->new(Rect::class);

        $this->rect->h = $this->height;
        $this->rect->w = $this->width;
        $this->rect->x = 30;
        $this->rect->y = 10;
    }

    public function updateRemainingTime()
    {
        $this->remainingTime--;

        $this->generateTexture();
    }

    public function newRound()
    {
        $this->remainingTime = self::ROUND_TIME + 1;
        $this->board->scored = 0;
        $this->board->generateTexture();
    }

    public function endRoundAfterTime()
    {
        $this->currentTime = $this->sdl->SDL_GetTicks();

        if ($this->remainingTime <= 0) {
            $this->newRound();
        }

        if ($this->currentTime > $this->lastTime + 1000) {
            $this->updateRemainingTime();
            $this->lastTime = $this->currentTime;
        }
    }
}
