<?php

namespace Phpschooter;

use Serafim\SDL\Image\Image;
use Serafim\SDL\SurfacePtr;
use Serafim\SDL\Rect;

abstract class Box
{
    const SPEED = 300;

    protected $sdl;
    protected $renderer;

    protected $texture;
    public $rect;

    protected int $height = 48;//TODO: change to const
    protected int $width = 48;

    public float $x_vel = self::SPEED;
    public float $y_vel = self::SPEED;


    public function __construct($sdl, $renderer, $textureImg)
    {
        /** @var \Serafim\SDL\SDLNativeApiAutocomplete $sdl */
        $this->sdl = $sdl;
        $this->renderer = $renderer;

        $this->generateTexture($textureImg);
        $this->generateRect();
    }

    protected function generateTexture($textureImg)
    {
        /** @var \Serafim\SDL\Image\ImageNativeApiAutocomplete $image */
        $image = new Image();
        $surface = $image->IMG_Load(__DIR__ . '/../assets/'.$textureImg);
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
        $this->rect->x = 0;
        $this->rect->y = 0;
    }

    public function texture()
    {
        return $this->texture;
    }

    public function rect()
    {
        return $this->rect;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }
}
