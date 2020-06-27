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
$dest->h = 64;
$dest->w = 64;
// $w = FFI::new("int"); 
// $h = FFI::new("int"); 

//$sdlRect = $sdl->cast(\Serafim\SDL\RectPtr::class, $rect); // <<<<< HERE
// $sdl->SDL_QueryTexture($texture, null, null,  SDL::addr($h), SDL::addr($h) );


$dest->x = (WINDOW_WIDTH - $dest->y) / 2; 

$y_pos = WINDOW_HEIGHT;

while ($dest->y >= -$dest->h) {

    $sdl->SDL_RenderClear($renderer);

    $dest->y = (int) $y_pos;

    // $sdl->SDL_RenderFillRect($renderer, FFI::addr($dest));
    // $sdl->SDL_RenderDrawRect($renderer, FFI::addr($dest));
    // $sdl->SDL_SetRenderDrawColor($renderer, 255, 255, 255, 255);

    $sdl->SDL_RenderCopy($renderer, $texture, null, SDL::addr($dest));
    $sdl->SDL_RenderPresent($renderer);
    $y_pos -= (float) SCROLL_SPEED / 60;

    $sdl->SDL_Delay(1000/60);
}

// $sdl->SDL_Delay(1000);

// $event = $sdl->new(Event::class);
// $running = true;

// // $sdl->SDL_Delay(5000);
// while ($running) {
//     $sdl->SDL_PollEvent(SDL::addr($event));
//     if ($event->type === Type::SDL_QUIT) {
//         $running = false;
//     }

//     $sdl->SDL_SetRenderDrawColor($renderer, 0, 0, 0, 0);
//     $sdl->SDL_RenderClear($renderer);

//     // Game background color
//     $sdl->SDL_SetRenderDrawColor($renderer, 95, 150, 249, 255);
// }

$sdl->SDL_DestroyTexture($texture);
$sdl->SDL_DestroyRenderer($renderer);
$sdl->SDL_DestroyWindow($window);
$sdl->SDL_Quit();
