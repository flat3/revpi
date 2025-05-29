<?php

declare(strict_types=1);

namespace Flat3\RevPi\Commands;

use Flat3\RevPi\Pictory\Pictory;
use Illuminate\Console\Command;
use stdClass;

class Generate extends Command
{
    protected $signature = 'revpi:generate {pictory} {class?}';

    protected $description = 'Generate a PHP class from a Pictory configuration';

    public function handle(): void
    {
        /** @var string $input */
        $input = $this->argument('pictory');

        /** @var string $className */
        $className = $this->argument('class') ?: 'Pi';

        $configuration = json_decode((string) file_get_contents($input));
        assert($configuration instanceof stdClass);
        $class = app(Pictory::class)->import($configuration)->export($className);
        file_put_contents(app_path($className).'.php', $class);
    }
}
