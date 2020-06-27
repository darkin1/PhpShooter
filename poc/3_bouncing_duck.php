<?php

require __DIR__.'/vendor/autoload.php';

const WINDOW_HEIGHT = 480;
const WINDOW_WIDTH = 640;
const SCROLL_SPEED = 300;

use Serafim\SDL\SDL;
use Serafim\SDL\Event;
use Serafim\SDL\Kernel\Event\Type;

$sdl = new SDL();

$sdl->SDL_Init(SDL::SDL_INIT_VIDEO | SDL::SDL_INIT_TIMER);
// $window = $sdl->SDL_CreateWindowAndRenderer(800, 600, SDL::SDL_WINDOW_SHOWN);
// $window = $sdl->SDL_CreateWindow( 
//     'An SDL2 window',
//     SDL::SDL_WINDOWPOS_UNDEFINED,
//     SDL::SDL_WINDOWPOS_UNDEFINED, 
//     640,
//     480,
//     SDL::SDL_WINDOW_OPENGL
// );

$window = $sdl->SDL_CreateWindow(
    'Game',
    SDL::SDL_WINDOWPOS_CENTERED, SDL::SDL_WINDOWPOS_CENTERED,
    WINDOW_WIDTH, WINDOW_HEIGHT,
    SDL::SDL_WINDOW_SHOWN
);
if ($window === null) {
    throw new \Exception(sprintf('Could not create window: %s', $sdl->SDL_GetError()));
}

$flag = SDL::SDL_RENDERER_ACCELERATED;
$renderer = $sdl->SDL_CreateRenderer($window, -1, $flag);

if($renderer === null) {
    var_dump(SDL::SDL_GetError());
    $sdl->SDL_DestroyWindow($window);
    $sdl->SDL_Quit();
}

/** @var \Serafim\SDL\Image\ImageNativeApiAutocomplete $image */
$image = new \Serafim\SDL\Image\Image();
$surface = $image->IMG_Load(__DIR__ . '/duck.png');

if($surface === null) {
    var_dump(SDL::SDL_GetError());
    $sdl->SDL_DestroyRenderer($renderer);
    $sdl->SDL_DestroyWindow($window);
    $sdl->SDL_Quit();
}

$sdlSurface = $sdl->cast(\Serafim\SDL\SurfacePtr::class, $surface); // <<<<< HERE
$texture = $sdl->SDL_CreateTextureFromSurface($renderer, $sdlSurface);
$sdl->SDL_FreeSurface($sdlSurface);
if($texture == null) {
    $sdl->SDL_DestroyRenderer($renderer);
    $sdl->SDL_DestroyWindow($window);
    $sdl->SDL_Quit();
}

// clear the window
$sdl->SDL_RenderClear($renderer);

// draw the imageto the window
$sdl->SDL_RenderCopy($renderer, $texture, null, null);
$sdl->SDL_RenderPresent($renderer);

$dest = $sdl->new(SDL_Rect::class);
$dest->h = 32;
$dest->w = 32;
// $w = FFI::new("int"); 
// $h = FFI::new("int"); 

//$sdlRect = $sdl->cast(\Serafim\SDL\RectPtr::class, $rect); // <<<<< HERE
// $sdl->SDL_QueryTexture($texture, null, null,  SDL::addr($h), SDL::addr($h) );

// start sprite in the center of the screen
(float) $x_pos = (WINDOW_WIDTH - $dest->w) / 2; 
(float) $y_pos = (WINDOW_WIDTH - $dest->h) / 2;
(float) $x_vel = 0;
(float) $y_vel = 0;


//give sprite initial velocity
(float) $x_vel = SCROLL_SPEED;
(float) $y_vel = SCROLL_SPEED;

$event = $sdl->new(Event::class);
$running = true;

while ($running) {
    $sdl->SDL_PollEvent(SDL::addr($event));
    if ($event->type === Type::SDL_QUIT) {
        $running = false;
    }

    //collision detection with bounds
    if ($x_pos <= 0) {
        $x_pos = 0;
        $x_vel = -$x_vel;
    }
    if($y_pos <= 0) {
        $y_pos = 0;
        $y_vel = -$y_vel;
    }
    if($x_pos >= WINDOW_WIDTH - $dest->w) {
        $x_pos = WINDOW_WIDTH - $dest->w;
        $x_vel = -$x_vel;
    }
    if($y_pos >= WINDOW_HEIGHT - $dest->h) {
        $y_pos = WINDOW_HEIGHT - $dest->h;
        $y_vel = -$y_vel;
    }

    //update position
    $x_pos += $x_vel/60;
    $y_pos += $y_vel/60;

    //set position in the struct
    $dest->y = (int) $y_pos;
    $dest->x = (int) $x_pos;


    $sdl->SDL_RenderClear($renderer);
    $sdl->SDL_RenderCopy($renderer, $texture, null, SDL::addr($dest));
    $sdl->SDL_RenderPresent($renderer);

    $sdl->SDL_Delay(1000/60);
}



$sdl->SDL_DestroyTexture($texture);
$sdl->SDL_DestroyRenderer($renderer);
$sdl->SDL_DestroyWindow($window);
$sdl->SDL_Quit();
