<?php

declare(strict_types=1);

namespace Flat3\RevPi\Commands;

use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Led\LedColour;
use Flat3\RevPi\Led\LedPosition;
use Illuminate\Console\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class SetLed extends Command
{
    protected $description = 'Set the specified LED to the specified colour';

    protected $signature = 'revpi:led:set {position} {colour}';

    public function handle(): void
    {
        app(Module::class)
            ->led(match ($this->argument('position')) {
                'a1' => LedPosition::A1,
                'a2' => LedPosition::A2,
                'a3' => LedPosition::A3,
                'a4' => LedPosition::A4,
                'a5' => LedPosition::A5,
                default => throw new InvalidArgumentException('Invalid LED position'),
            })
            ->set(match ($this->argument('colour')) {
                'off' => LedColour::Off,
                'red' => LedColour::Red,
                'green' => LedColour::Green,
                'orange' => LedColour::Orange,
                'blue' => LedColour::Blue,
                'magenta' => LedColour::Magenta,
                'cyan' => LedColour::Cyan,
                'white' => LedColour::White,
                default => throw new InvalidArgumentException('Invalid LED colour'),
            });
    }
}
