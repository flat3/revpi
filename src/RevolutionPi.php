<?php

declare(strict_types=1);

namespace Flat3\RevPi;

use Amp\Websocket\Client\WebsocketHandshake;
use Flat3\RevPi\Attributes\Tag;
use Flat3\RevPi\Exceptions\AttributeNotFoundException;
use Flat3\RevPi\Interfaces\Led;
use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Interfaces\Modules\Remote;
use Flat3\RevPi\Interfaces\ProcessImage;
use Flat3\RevPi\Interfaces\SerialPort;
use Flat3\RevPi\IO\IO;
use Flat3\RevPi\Led\LedPosition;
use Illuminate\Support\Facades\App;
use Psr\Http\Message\UriInterface as PsrUri;
use ReflectionAttribute;
use ReflectionClass;
use Revolt\EventLoop;

trait RevolutionPi
{
    protected ?WebsocketHandshake $handshake = null;

    protected ?Module $module = null;

    /**
     * Get the appropriate module instance (local or remote, depending on handshake).
     */
    public function module(): Module
    {
        if (! $this->module instanceof Module) {
            if ($this->handshake !== null) {
                $this->module = app(Remote::class);
                $this->module->handshake($this->handshake);
            } else {
                $this->module = app(Module::class);
            }
        }

        return $this->module;
    }

    /**
     * Set the handshake for remote module communication.
     */
    public function remote(WebsocketHandshake|PsrUri|string $handshake): static
    {
        if (! $handshake instanceof WebsocketHandshake) {
            $handshake = new WebsocketHandshake($handshake);
        }

        $this->handshake = $handshake;

        return $this;
    }

    /**
     * Get the LED object for a given LED position.
     */
    public function led(LedPosition $position): Led
    {
        return $this->module()->getLed($position);
    }

    /**
     * Get the SerialPort object for communication via a specific device path.
     */
    public function serialPort(string $devicePath = '/dev/ttyRS485-0'): SerialPort
    {
        return $this->module()->getSerialPort($devicePath);
    }

    /**
     * Get the process image interface for the module.
     */
    public function processImage(): ProcessImage
    {
        return $this->module()->getProcessImage();
    }

    /**
     * Repeatedly call a callback at a fixed interval.
     *
     * @param  float  $interval  Interval in seconds.
     * @param  callable  $callback  Callback to execute.
     */
    public function repeat(float $interval, callable $callback): void
    {
        EventLoop::repeat($interval, function () use ($callback) {
            App::call($callback, ['pi' => $this]);
        });
    }

    /**
     * Magic getter to access IO attribute by name.
     *
     * @param  string  $name  Attribute name.
     */
    public function __get(string $name): int|bool
    {
        return $this->{$name}()->get(); // @phpstan-ignore method.dynamicName
    }

    /**
     * Magic setter to set IO attribute by name.
     *
     * @param  string  $name  Attribute name.
     * @param  int|bool  $value  Value to set.
     */
    public function __set(string $name, int|bool $value): void
    {
        $this->{$name}()->set($value); // @phpstan-ignore method.dynamicName
    }

    /**
     * Magic method to call IO attributes dynamically by name.
     *
     * @param  string  $name  Attribute name.
     * @param  array<string, string>  $arguments  Method arguments.
     *
     * @throws AttributeNotFoundException If the attribute is not found.
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
