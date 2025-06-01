<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort;

enum RS485Flag: int
{
    /**
     * RS-485 mode enabled.
     * Activates RS-485 features on the serial port.
     */
    case Enabled = 0x01; // SER_RS485_ENABLED

    /**
     * Set RTS (Request to Send) high while sending data.
     * Indicates to the driver that RTS should be asserted when sending.
     */
    case RtsOnSend = 0x02; // SER_RS485_RTS_ON_SEND

    /**
     * Set RTS high after sending data.
     * Keeps RTS asserted after transmit has finished.
     */
    case RtsAfterSend = 0x04; // SER_RS485_RTS_AFTER_SEND

    /**
     * Enable receiving data during transmission (TX).
     * Allows the receiver to stay enabled during transmission (useful for loopback/testing).
     */
    case RxDuringTx = 0x10; // SER_RS485_RX_DURING_TX

    /**
     * Enable bus termination resistor.
     * Connects the bus terminator (if available in hardware).
     */
    case TerminateBus = 0x20; // SER_RS485_TERMINATE_BUS
}