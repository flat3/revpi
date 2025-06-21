<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Remote;

use Flat3\RevPi\Interfaces\Hardware\PiControl;

class RemotePiControlDevice extends RemoteBlockDevice implements PiControl
{
    public function ioctl(int $request, ?string &$argp = null): int
    {
        $response = $this->client->request('ioctl', ['request' => $request, 'argp' => $argp])->await();
        $argp = $response['argp'];

        return $response['return'];
    }
}
