<?php
namespace Phpschooter;

use Serafim\SDL\SDL;
use Serafim\SDL\Event;
use Serafim\SDL\Kernel\Event\Type;
use Serafim\SDL\TTF\TTF;

define("WINDOW_WIDTH", 640);
define("WINDOW_HEIGHT", 480);

final class Game 
{
    private $window;
    private $renderer;
    private $sdl;
    private $ttl;

    /**
     * SdlRenderer constructor.
     *
     * @param Game   $game
     * @param string $title
     * @param int    $width  in pixels
     * @param int    $height in pixels
     */
    public function __construct(string $title = 'PHP Schooter')
    {
        /** @var Serafim\SDL\TTF\SDLTTFNativeApiAutocomplete $ttf */
        $this->ttf = new TTF();
        $this->sdl = new SDL();
        $this->sdl->SDL_Init(SDL::SDL_INIT_VIDEO | SDL::SDL_INIT_TIMER);

        $this->window = $this->sdl->SDL_CreateWindow(
            $title,
            SDL::SDL_WINDOWPOS_CENTERED, SDL::SDL_WINDOWPOS_CENTERED,
            WINDOW_WIDTH, WINDOW_HEIGHT,
            SDL::SDL_WINDOW_SHOWN
        );
        if ($this->window === null) {
            throw new \Exception(sprintf('Could not create window: %s', $this->sdl->SDL_GetError()));
        }

        $this->renderer = $this->sdl->SDL_CreateRenderer($this->window, -1, SDL::SDL_RENDERER_ACCELERATED);
        if($this->renderer === null) {
            $this->sdl->SDL_DestroyWindow($this->window);
            $this->sdl->SDL_Quit();
            throw new \Exception(sprintf('Could not render window: %s', $this->sdl->SDL_GetError()));
        }
    }

    public function __destruct()
    {
        // $this->sdl->SDL_DestroyTexture($texture);
        $this->sdl->SDL_DestroyRenderer($this->renderer);
        $this->sdl->SDL_DestroyWindow($this->window);
        $this->sdl->SDL_Quit();
    }

    public function centerPlayer($player)
    {
        $player->rect->x = (WINDOW_WIDTH - $player->getWidth()) / 2; 
        $player->rect->y = (WINDOW_HEIGHT - $player->getHeight()) - 2;
        // $player->x_vel = 0;
        // $player->y_vel = 0;
    }

    public function gameFrameCollisionDetection($player)
    {
        if ($player->rect->x <= 0) $player->rect->x = 0; 
        if ($player->rect->y <= 0) $player->rect->y = 0;
        if ($player->rect->x >= WINDOW_WIDTH - $player->rect->w) $player->rect->x = WINDOW_WIDTH - $player->rect->w;
        if ($player->rect->y >= WINDOW_HEIGHT - $player->rect->h) $player->rect->y = WINDOW_HEIGHT - $player->rect->h;    
    }

    public function hitted($bullet, $enemy): bool
    {
        return (bool) $this->sdl->SDL_HasIntersection(SDL::addr($bullet->rect()), SDL::addr($enemy->rect()));
    }

    public function run()
    {
        $background = new Background($this->sdl, $this->renderer, 'background.jpg');
        $player = new Player($this->sdl, $this->renderer, 'elephant.png');
        $bullet = $player->bullet($this->sdl, $this->renderer);// TODO: move to constructor?
        $enemy = new Enemy($this->sdl, $this->renderer, 'enemy.png', WINDOW_WIDTH);
        $board = new ScoreBoard($this->sdl, $this->renderer, $this->ttf);

        $this->centerPlayer($player);
        $enemy->reborn();//TODO: move to constructor?


        $up = $right = $down = $left = $fire = false;
        $event = $this->sdl->new(Event::class);
        $quit = false;
        while (!$quit) {
            $this->sdl->SDL_PollEvent(SDL::addr($event));
            if ($event->type === Type::SDL_QUIT) {
                $quit = true;
            }
            if($event->type === Type::SDL_KEYDOWN) {
                switch($event->key->keysym->scancode) {
                    case SDL::SDL_SCANCODE_W:
                    case SDL::SDL_SCANCODE_UP:
                        $up = true;
                    break;

                    case SDL::SDL_SCANCODE_A:
                    case SDL::SDL_SCANCODE_LEFT:
                        $left = true;
                    break;

                    case SDL::SDL_SCANCODE_S:
                    case SDL::SDL_SCANCODE_DOWN:
                        $down = true;
                    break;

                    case SDL::SDL_SCANCODE_D:
                    case SDL::SDL_SCANCODE_RIGHT:
                        // $player->right();
                        $right = true;
                    break;

                    case SDL::SDL_SCANCODE_SPACE:
                        $fire = true;
                    break;
                }
            }
            if($event->type === Type::SDL_KEYUP) {
                switch($event->key->keysym->scancode) {
                    case SDL::SDL_SCANCODE_W:
                    case SDL::SDL_SCANCODE_UP:
                        $up = false;
                    break;

                    case SDL::SDL_SCANCODE_A:
                    case SDL::SDL_SCANCODE_LEFT:
                        $left = false;
                    break;

                    case SDL::SDL_SCANCODE_S:
                    case SDL::SDL_SCANCODE_DOWN:
                        $down = false;
                    break;

                    case SDL::SDL_SCANCODE_D:
                    case SDL::SDL_SCANCODE_RIGHT:
                        $right = false;
                    break;

                    case SDL::SDL_SCANCODE_SPACE:
                        $fire = false;
                    break;
                }
            }

            $player->initializeMovement($up, $right, $down, $left);
            $player->initializeFire($fire, $bullet);
            // if($fire) 
            //     $bullet->shoot();
            // if(!$fire) 
            //     $bullet->setPosition($player->rect->x, $player->rect->y, $player->rect->w, $player->rect->h);
            
            $this->gameFrameCollisionDetection($player);

            if($this->hitted($bullet, $enemy)) {
                $fire = false;
                $enemy->reborn();
                $board->updateScore(10);
            }

            // render
            $this->sdl->SDL_RenderClear($this->renderer);

            $this->sdl->SDL_RenderCopy($this->renderer, $background->texture(), null, null);
            $this->sdl->SDL_RenderCopy($this->renderer, $board->texture(), null, SDL::addr($board->rect()));
            $this->sdl->SDL_RenderCopy($this->renderer, $player->texture(), null, SDL::addr($player->rect()));
            $this->sdl->SDL_RenderCopy($this->renderer, $enemy->texture(), null, SDL::addr($enemy->rect()));
            if($fire)
                $this->sdl->SDL_RenderCopy($this->renderer, $bullet->texture(), null, SDL::addr($bullet->rect()));
            


            $this->sdl->SDL_SetRenderDrawColor($this->renderer, 0, 0, 0, 255); //background color
            $this->sdl->SDL_RenderPresent($this->renderer);

            $this->sdl->SDL_Delay(1000/60);
        }
        
       //TODO: skasować 
        $this->sdl->SDL_DestroyRenderer($this->renderer);
        $this->sdl->SDL_DestroyWindow($this->window);
        $this->sdl->SDL_Quit();
    }
}


// TODO: 
// [-] limit enemy to not go in score board
// [x] rebortn enemy after X sec.
// add time for game
// change background
// add game to repositoiry https://github.com/gabrielrcouto/awesome-php-ffi