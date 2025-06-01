<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort;

enum OutputFlag: int
{
    /**
     * Enable implementation-defined output processing.
     * When clear, all special output processing of characters is disabled.
     */
    case EnableOutputProcessing = 0000001; // OPOST

    /**
     * Map NL (newline, '\n') to CR-NL (carriage return & newline) on output.
     * Only valid if OPOST is set.
     */
    case MapNewlineToCarriageReturnNewline = 0000004; // ONLCR

    /**
     * Map CR (carriage return, '\r') to NL (newline, '\n') on output.
     * Only valid if OPOST is set.
     */
    case MapCarriageReturnToNewline = 0000010; // OCRNL

    /**
     * Do not output CR (carriage return, '\r') at column 0.
     * Only valid if OPOST is set.
     */
    case NoCarriageReturnAtColumnZero = 0000020; // ONOCR

    /**
     * Do not output NL (newline, '\n') at column 0.
     * Only valid if OPOST is set.
     */
    case NoNewlineAtColumnZero = 0000040; // ONLRET

    /**
     * Use fill characters (NUL bytes) for delays instead of timing.
     */
    case UseFillCharactersForDelay = 0000100; // OFILL

    /**
     * Use DEL as fill character for delays if OFILL is set.
     * Otherwise, use NUL as fill character.
     */
    case FillCharacterIsDel = 0000200; // OFDEL

    /**
     * Expands horizontal tabs to spaces on output.
     */
    case ExpandTabsToSpaces = 0014000; // TAB3
}
