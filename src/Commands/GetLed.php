<?php

declare(strict_types=1);

namespace Flat3\RevPi\Commands;

use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Led\LedPosition;
use Illuminate\Console\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class GetLed extends Command
{
    protected $description = 'Get the colour of the specified LED';

    protected $signature = 'revpi:led:get {position}';

    public function handle(): void
    {
        $this->info(
            app(Module::class)
                ->led(match ($this->argument('position')) {
                    'a1' => LedPosition::A1,
                    'a2' => LedPosition::A2,
                    'a3' => LedPosition::A3,
                    'a4' => LedPosition::A4,
                    'a5' => LedPosition::A5,
                    default => throw new InvalidArgumentException('Invalid LED position'),
                })
                ->get()
                ->name
        );
    }
}
