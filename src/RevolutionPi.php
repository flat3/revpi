<?php

declare(strict_types=1);

namespace Flat3\RevPi;

use Amp\Websocket\Client\WebsocketHandshake;
use Flat3\RevPi\Attributes\Tag;
use Flat3\RevPi\Exceptions\AttributeNotFoundException;
use Flat3\RevPi\Interfaces\Led;
use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Interfaces\Modules\Remote;
use Flat3\RevPi\IO\IO;
use Flat3\RevPi\Led\LedPosition;
use Illuminate\Support\Facades\App;
use Psr\Http\Message\UriInterface as PsrUri;
use ReflectionAttribute;
use ReflectionClass;
use Revolt\EventLoop;

trait RevolutionPi
{
    protected WebsocketHandshake|PsrUri|string|null $address;

    public function module(): Module
    {
        if ($this->address) {
            $module = app(Remote::class);
            $module->connect($this->address);

            return $module;
        }

        return app(Module::class);
    }

    public function address(WebsocketHandshake|PsrUri|string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function led(LedPosition $position): Led
    {
        return $this->module()->getLed($position);
    }

    public function repeat(float $interval, callable $callback): void
    {
        EventLoop::repeat($interval, function () use ($callback) {
            App::call($callback, ['pi' => $this]);
        });
    }

    public function __get(string $name): int|bool
    {
        return $this->{$name}()->get(); // @phpstan-ignore method.dynamicName
    }

    public function __set(string $name, int|bool $value): void
    {
        $this->{$name}()->set($value); // @phpstan-ignore method.dynamicName
    }

    /**
     * @param  array<string,string>  $arguments
     */
    public function __call(string $name, array $arguments): IO
    {
        $reflection = new ReflectionClass($this);

        $attribute = collect($reflection->getAttributes(Tag::class, ReflectionAttribute::IS_INSTANCEOF))
            ->firstWhere(fn (ReflectionAttribute $attribute) => $attribute->getArguments()['name'] === $name);

        if (! $attribute instanceof ReflectionAttribute) {
            throw new AttributeNotFoundException($name);
        }

        return $attribute->newInstance()->tag($this->module());
    }
}
