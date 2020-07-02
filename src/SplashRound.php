<?php

namespace Phpschooter;

use Serafim\SDL\Image\Image;
use Serafim\SDL\SurfacePtr;
use Serafim\SDL\Rect;
use Serafim\SDL\Color;

final class SplashRound extends Box
{
    private $ttl;
    private $font;
    private $color;
    private $currentTime;
    private $lastTime = 0;

    protected int $height = 28;
    protected int $width = 84;

    public function __construct($sdl, $renderer, $ttf)
    {
        $this->sdl = $sdl;
        $this->renderer = $renderer;
        $this->ttf = $ttf;

        $this->includeFont();
        $this->generateTexture();
        $this->generateRect();
    }

    protected function includeFont()
    {
        $this->font = $this->ttf->TTF_OpenFont(__DIR__ . '/../assets/ARCADECLASSIC.TTF', 18);
        $this->color = $this->ttf->new(Color::class);
        $this->color->r = 79;
        $this->color->g = 91;
        $this->color->b = 147;
    }

    protected function generateTexture($unknown = null)
    {
        $surface = $this->ttf->TTF_RenderText_Solid($this->font, 'NEW ROUND', $this->color);
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
        $this->rect->x = (WINDOW_WIDTH / 2) - ($this->width / 2);
        $this->rect->y = 10;
    }

    public function show()
    {
        $this->currentTime = $this->sdl->SDL_GetTicks();

        if ($this->lastTime == 0) {
            $this->lastTime = $this->currentTime;
        }

        if ($this->currentTime > $this->lastTime + 2000) {
            $this->lastTime = 0;

            return false;
        }

        return true;
    }
}
