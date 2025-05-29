<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests;

use Flat3\RevPi\Events\PollingEvent;
use Flat3\RevPi\ServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Revolt\EventLoop;

class TestCase extends BaseTestCase
{
    /**
     * @var Application
     */
    protected $app;

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function loop(int $times, int $wait = 10): void
    {
        EventLoop::repeat(0, function () use ($wait, $times) {
            static $iterations = 0;

            if (++$iterations === $times) {
                EventLoop::getDriver()->stop();
            }

            usleep($wait * 1000);

            PollingEvent::dispatch();
        });

        EventLoop::run();
    }
}
