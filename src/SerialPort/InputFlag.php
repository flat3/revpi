<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort;

enum InputFlag: int
{
    /**
     * Ignore break condition on input.
     * No signal is sent and the break character is ignored.
     */
    case IgnoreBreak = 0000001; // IGNBRK

    /**
     * Signal interrupt on break condition.
     * If set, a SIGINT is sent to the process on break input.
     */
    case InterruptOnBreak = 0000002; // BRKINT

    /**
     * Ignore framing and parity errors.
     * Characters with parity errors are not reported.
     */
    case IgnoreParityErrors = 0000004; // IGNPAR

    /**
     * Mark parity errors.
     * Characters with parity errors are replaced by the NUL character (0).
     */
    case MarkParityErrors = 0000010; // PARMRK

    /**
     * Enable input parity checking.
     * Parity errors are checked and flagged.
     */
    case EnableInputParityCheck = 0000020; // INPCK

    /**
     * Strip the 8th (high) bit from each input byte.
     */
    case StripHighBit = 0000040; // ISTRIP

    /**
     * Map NL (newline, '\n') to CR (carriage return, '\r') on input.
     */
    case MapNewlineToCarriageReturn = 0000100; // INLCR

    /**
     * Ignore CR (carriage return, '\r') characters on input.
     */
    case IgnoreCarriageReturn = 0000200; // IGNCR

    /**
     * Map CR (carriage return, '\r') to NL (newline, '\n') on input.
     */
    case MapCarriageReturnToNewline = 0000400; // ICRNL

    /**
     * Enable XON/XOFF flow control on output.
     * Output is stopped when the system receives the STOP character and started when the START character is received.
     */
    case EnableStartStopOutputFlowControl = 0002000; // IXON

    /**
     * Enable XON/XOFF flow control on input.
     * Input is stopped when the system sends the STOP character and started when the START character is received.
     */
    case EnableStartStopInputFlowControl = 0010000; // IXOFF

    /**
     * Any character will restart output after stopping (if IXON is set).
     */
    case AnyCharWillRestartOutput = 0004000; // IXANY

    /**
     * Ring bell (beep) when input queue is full.
     */
    case RingBellWhenInputQueueFull = 0020000; // IMAXBEL

    /**
     * Input is UTF-8 encoded.
     * Assists with correct erase and kill processing.
     */
    case InputIsUtf8 = 0040000; // IUTF8
}
