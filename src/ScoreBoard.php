<?php

namespace Phpschooter;

use Serafim\SDL\Image\Image;
use Serafim\SDL\SurfacePtr;
use Serafim\SDL\Rect;
use Serafim\SDL\Color;

final class ScoreBoard extends Box
{
    private $ttl;
    private $font;
    private $color;

    protected int $height = 28;
    protected int $width = 84;

    public float $x_vel = 0;
    public float $y_vel = 0;

    public $scored = 0;

    public function __construct($sdl, $renderer, $ttf)
    {
        $this->sdl = $sdl;
        $this->renderer = $renderer;
        $this->ttf = $ttf;
        $this->font;
        $this->color;

        $this->includeFont();
        $this->generateTexture();
        $this->generateRect();
    }

    protected function includeFont()
    {
        $this->font = $this->ttf->TTF_OpenFont(__DIR__ . '/../assets/ARCADECLASSIC.TTF', 18);
        $this->color = $this->ttf->new(Color::class);
        $this->color->r = 255; $this->color->g = 255; $this->color->b = 255;
    }

    protected function generateTexture($unknown = null)
    {
        $surface = $this->ttf->TTF_RenderText_Solid($this->font, 'Scored    ' . $this->scored, $this->color);
        if($surface === null) {
            throw new \Exception(sprintf('Could not render surface: %s', $this->sdl->SDL_GetError()));
        }

        $this->texture = $this->sdl->SDL_CreateTextureFromSurface(
            $this->renderer, 
            $sdlSurface = $this->sdl->cast(SurfacePtr::class, $surface)
        );
        if($this->texture == null) {
            throw new \Exception(sprintf('Could not render texture: %s', $this->sdl->SDL_GetError()));
        }

        $this->sdl->SDL_FreeSurface($sdlSurface);
    }

    protected function generateRect()
    {
        $this->rect = $this->sdl->new(Rect::class);

        $this->rect->h = $this->height;
        $this->rect->w = $this->width;
        $this->rect->x = WINDOW_WIDTH - $this->rect->w - 50;
        $this->rect->y = 10;
    }

    public function updateScore($scored)
    {        
        $this->scored += $scored;

        $this->generateTexture();
    }
}
