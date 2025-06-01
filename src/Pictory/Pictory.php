<?php

declare(strict_types=1);

namespace Flat3\RevPi\Pictory;

use Carbon\CarbonImmutable;
use Flat3\RevPi\Attributes\Input;
use Flat3\RevPi\Attributes\Memory;
use Flat3\RevPi\Attributes\Output;
use Flat3\RevPi\IO\InputIO;
use Flat3\RevPi\IO\MemoryIO;
use Flat3\RevPi\IO\OutputIO;
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

        foreach ($this->configuration->Devices as $device) {
            foreach ($device->inp as $tag) {
                $io = IO::fromPictory($tag);

                if ($io->unit === null) {
                    continue;
                }

                $class->addComment(sprintf('@method InputIO %s()', $io->name));
                $class->addAttribute(Input::class, $io->attributeArgs());
            }

            foreach ($device->out as $tag) {
                $io = IO::fromPictory($tag);

                if ($io->unit === null) {
                    continue;
                }

                $class->addComment(sprintf('@method OutputIO %s()', $io->name));
                $class->addAttribute(Output::class, $io->attributeArgs());
            }

            foreach ($device->mem as $tag) {
                $io = IO::fromPictory($tag);

                if ($io->unit === null) {
                    continue;
                }

                $class->addComment(sprintf('@method MemoryIO %s()', $io->name));
                $class->addAttribute(Memory::class, $io->attributeArgs());
            }
        }

        foreach ($this->configuration->Devices as $device) {
            foreach ($device->inp as $tag) {
                $io = IO::fromPictory($tag);

                if ($io->unit === null) {
                    continue;
                }

                $class->addComment(sprintf('@property-read %s %s %s', $io->unit, $io->name, $io->comment));
            }

            foreach ($device->out as $tag) {
                $io = IO::fromPictory($tag);

                if ($io->unit === null) {
                    continue;
                }

                $class->addComment(sprintf('@property %s %s %s', $io->unit, $io->name, $io->comment));
            }

            foreach ($device->mem as $tag) {
                $io = IO::fromPictory($tag);

                if ($io->unit === null) {
                    continue;
                }

                $class->addComment(sprintf('@property %s %s %s', $io->unit, $io->name, $io->comment));
            }
        }

        return (string) $file;
    }
}
