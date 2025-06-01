<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort;

enum LocalFlag: int
{
    /**
     * Enable canonical mode (line buffering, input editing with erase/kill/special chars).
     * Unset this for "raw" byte-by-byte serial communication.
     */
    case CanonicalInput = 0000002; // ICANON

    /**
     * Echo input characters back to the output.
     */
    case EchoInput = 0000010; // ECHO

    /**
     * Echo the erase character as backspace-space-backspace.
     */
    case EchoErase = 0000020; // ECHOE

    /**
     * Echo the kill character (line kill) by erasing the line.
     */
    case EchoKill = 0000040; // ECHOK

    /**
     * Echo newline character even if echo is off.
     */
    case EchoNewline = 0000100; // ECHONL

    /**
     * Enable interpretation of INTR, QUIT, SUSP, and DSUSP characters to generate signals.
     */
    case EnableSignals = 0000001; // ISIG

    /**
     * Enable implementation-defined input processing.
     * For example, LF -> NL, input extensions, etc.
     */
    case ExtendedInput = 0100000; // IEXTEN

    /**
     * Do not flush input and output queues when generating signals for special chars.
     */
    case NoFlushOnSignal = 0000200; // NOFLSH

    /**
     * Send SIGTTOU signal to background processes that attempt output to this terminal.
     */
    case BackgroundWriteSignal = 0000400; // TOSTOP

    /**
     * Output is being flushed (internal use; rarely set directly).
     */
    case OutputBeingFlushed = 0010000; // FLUSHO

    /**
     * Retype pending input at next read.
     */
    case RetypePendingInput = 0040000; // PENDIN
}
