<?php

declare(strict_types=1);

namespace Flat3\RevPi;

use Flat3\RevPi\Attributes\Tag;
use Flat3\RevPi\Contracts\BaseModule;
use Flat3\RevPi\Contracts\Led;
use Flat3\RevPi\Exceptions\AttributeNotFoundException;
use Flat3\RevPi\Hardware\IO\IO;
use Flat3\RevPi\Hardware\Led\LedPosition;
use Illuminate\Support\Facades\App;
use ReflectionAttribute;
use ReflectionClass;
use Revolt\EventLoop;

trait RevolutionPi
{
    protected ?string $address = null;

    public function module(): BaseModule
    {
        return app(BaseModule::class);
    }

    public function address(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function led(LedPosition $position): Led
    {
        return $this->module()->led($position);
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
