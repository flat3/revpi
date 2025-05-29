<?php

declare(strict_types=1);

namespace Flat3\RevPi\Pictory;

use Carbon\CarbonImmutable;
use Flat3\RevPi\Attributes\Input;
use Flat3\RevPi\Attributes\Memory;
use Flat3\RevPi\Attributes\Output;
use Flat3\RevPi\Hardware\IO\InputIO;
use Flat3\RevPi\Hardware\IO\MemoryIO;
use Flat3\RevPi\Hardware\IO\OutputIO;
use Flat3\RevPi\RevolutionPi;
use Illuminate\Support\Str;
use Nette\PhpGenerator\PhpFile;
use stdClass;

class Pictory
{
    protected stdClass $configuration;

    public function import(stdClass $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function export(string $className): string
    {
        $file = new PhpFile;

        /** @var string $timestamp */
        $timestamp = $this->configuration->App->saveTS;

        $timestamp = CarbonImmutable::createFromFormat('YmdHis', $timestamp);

        assert($timestamp instanceof CarbonImmutable);

        $file->addComment(sprintf(
            'Created from a Pictory %s configuration exported %s',
            $this->configuration->App->version,
            $timestamp->toDateTimeString()
        ));

        $file->setStrictTypes();

        $namespace = $file->addNamespace(Str::beforeLast(app()->getNamespace(), '\\'));
        $namespace->addUse(RevolutionPi::class);
        $namespace->addUse(Output::class);
        $namespace->addUse(Input::class);
        $namespace->addUse(Memory::class);
        $namespace->addUse(OutputIO::class);
        $namespace->addUse(InputIO::class);
        $namespace->addUse(MemoryIO::class);

        $class = $namespace->addClass($className);
        $class->addTrait(RevolutionPi::class);

        $split = fn ($tag) => [
            'name' => $tag[0],
            'default' => match ($tag[2]) {
                'null' => null,
                '1' => (bool) $tag[1],
                default => (int) $tag[1],
            },
            'unit' => match ($tag[2]) {
                'null' => null,
                '1' => 'bool',
                default => 'int'
            },
            'offset' => $tag[3],
            'exported' => $tag[4],
            'displayorder' => $tag[5],
            'comment' => $tag[6],
        ];

        foreach ($this->configuration->Devices as $device) {
            foreach ($device->inp as $tag) {
                $tag = $split($tag);
                if (! $tag['unit']) {
                    continue;
                }
                $class->addComment(sprintf('@method InputIO %s()', $tag['name']));
                $class->addAttribute(
                    Input::class,
                    array_filter(['name' => $tag['name'], 'default' => $tag['default']])
                );
            }

            foreach ($device->out as $tag) {
                $tag = $split($tag);
                if (! $tag['unit']) {
                    continue;
                }
                $class->addComment(sprintf('@method OutputIO %s()', $tag['name']));
                $class->addAttribute(
                    Output::class,
                    array_filter(['name' => $tag['name'], 'default' => $tag['default']])
                );
            }

            foreach ($device->mem as $tag) {
                $tag = $split($tag);
                if (! $tag['unit']) {
                    continue;
                }
                $class->addComment(sprintf('@method MemoryIO %s()', $tag['name']));
                $class->addAttribute(
                    Memory::class,
                    array_filter(['name' => $tag['name'], 'default' => $tag['default']])
                );
            }
        }

        foreach ($this->configuration->Devices as $device) {
            foreach ($device->inp as $tag) {
                $tag = $split($tag);
                if (! $tag['unit']) {
                    continue;
                }
                $class->addComment(sprintf('@property-read %s %s %s', $tag['unit'], $tag['name'], $tag['comment']));
            }

            foreach ($device->out as $tag) {
                $tag = $split($tag);
                if (! $tag['unit']) {
                    continue;
                }
                $class->addComment(sprintf('@property %s %s %s', $tag['unit'], $tag['name'], $tag['comment']));
            }

            foreach ($device->mem as $tag) {
                $tag = $split($tag);
                if (! $tag['unit']) {
                    continue;
                }
                $class->addComment(sprintf('@property %s %s %s', $tag['unit'], $tag['name'], $tag['comment']));
            }
        }

        return (string) $file;
    }
}
