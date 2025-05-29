<?php

declare(strict_types=1);

namespace Flat3\RevPi\Commands;

use Flat3\RevPi\Contracts\ProcessImage;
use Illuminate\Console\Command;

class Dump extends Command
{
    protected $description = 'Dump the process image to a file';

    protected $signature = 'revpi:dump {file}';

    public function handle(): void
    {
        /** @var string $filename */
        $filename = $this->argument('file');

        file_put_contents(
            filename: $filename,
            data: app(ProcessImage::class)->dumpImage()
        );
    }
}
