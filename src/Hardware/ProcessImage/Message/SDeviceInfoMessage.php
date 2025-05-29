<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\ProcessImage\Message;

class SDeviceInfoMessage extends Message
{
    public int $address = 0;

    public int $serialNumber = 0;

    public int $moduleType = 0;

    public int $hwRevision = 0;

    public int $swMajor = 0;

    public int $swMinor = 0;

    public int $svnRevision = 0;

    public int $inputLength = 0;

    public int $outputLength = 0;

    public int $configLength = 0;

    public int $baseOffset = 0;

    public int $inputOffset = 0;

    public int $outputOffset = 0;

    public int $configOffset = 0;

    public int $firstEntry = 0;

    public int $entries = 0;

    public int $moduleState = 0;

    public int $active = 0;

    public function definition(): array
    {
        return [
            'address' => 'C',
            'x3',
            'serialNumber' => 'V',
            'moduleType' => 'v',
            'hwRevision' => 'v',
            'swMajor' => 'v',
            'swMinor' => 'v',
            'svnRevision' => 'V',
            'inputLength' => 'v',
            'outputLength' => 'v',
            'configLength' => 'v',
            'baseOffset' => 'v',
            'inputOffset' => 'v',
            'outputOffset' => 'v',
            'configOffset' => 'v',
            'firstEntry' => 'v',
            'entries' => 'v',
            'moduleState' => 'C',
            'active' => 'C',
            'x30',
            'x2',
        ];
    }

    public function isActive(): bool
    {
        return $this->active === 1;
    }
}
