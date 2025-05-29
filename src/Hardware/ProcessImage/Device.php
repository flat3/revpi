<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\ProcessImage;

use Flat3\RevPi\Hardware\ProcessImage\Message\SDeviceInfoMessage;

final class Device
{
    public int $address;

    public int $serialNumber;

    public ModuleType $moduleType;

    public int $hwRevision;

    public int $swMajor;

    public int $swMinor;

    public int $svnRevision;

    public int $inputLength;

    public int $outputLength;

    public int $configLength;

    public int $baseOffset;

    public int $inputOffset;

    public int $outputOffset;

    public int $configOffset;

    public int $firstEntry;

    public int $entries;

    public ModuleState $moduleState;

    public bool $active;

    public static function fromMessage(SDeviceInfoMessage $message): self
    {
        $info = new self;

        $info->address = $message->address;
        $info->serialNumber = $message->serialNumber;
        $info->moduleType = ModuleType::tryFrom($message->moduleType) ?? ModuleType::UNKNOWN;
        $info->hwRevision = $message->hwRevision;
        $info->swMajor = $message->swMajor;
        $info->swMinor = $message->swMinor;
        $info->svnRevision = $message->svnRevision;
        $info->inputLength = $message->inputLength;
        $info->outputLength = $message->outputLength;
        $info->configLength = $message->configLength;
        $info->baseOffset = $message->baseOffset;
        $info->inputOffset = $message->inputOffset;
        $info->outputOffset = $message->outputOffset;
        $info->configOffset = $message->configOffset;
        $info->firstEntry = $message->firstEntry;
        $info->entries = $message->entries;
        $info->moduleState = ModuleState::from($message->moduleState);
        $info->active = $message->active === 1;

        return $info;
    }

    public function version(): string
    {
        return "{$this->swMajor}.{$this->swMinor}";
    }
}
