<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort;

enum ControlFlag: int
{
    /**
     * Enable receiver.
     * If unset, data received on this port is ignored.
     */
    case EnableReceiver = 0000200; // CREAD

    /**
     * Ignore modem control lines; treat as a "local" connection.
     * Ignores carrier detect and other modem state.
     */
    case IgnoreModemControlLines = 0004000; // CLOCAL

    /**
     * Enable hardware RTS/CTS flow control.
     * Linux and BSD extension; not in portable POSIX.
     */
    case EnableHardwareFlowControl = 0x80000000; // CRTSCTS (Linux/BSD)

    /**
     * Hang up on last close of the tty.
     */
    case HangUpOnClose = 0x00000400; // HUPCL
}
