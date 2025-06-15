<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort;

/**
 * BaudRate represents standard baud rates for serial communication.
 * These constants correspond to termios(3) speed_t codes.
 */
enum BaudRate: int
{
    /**
     * Hang up (0 baud, drops DTR).
     */
    case B0 = 0000000;

    /**
     * 50 baud.
     */
    case B50 = 0000001;

    /**
     * 75 baud.
     */
    case B75 = 0000002;

    /**
     * 110 baud.
     */
    case B110 = 0000003;

    /**
     * 134.5 baud.
     */
    case B134 = 0000004;

    /**
     * 150 baud.
     */
    case B150 = 0000005;

    /**
     * 200 baud.
     */
    case B200 = 0000006;

    /**
     * 300 baud.
     */
    case B300 = 0000007;

    /**
     * 600 baud.
     */
    case B600 = 0000010;

    /**
     * 1200 baud.
     */
    case B1200 = 0000011;

    /**
     * 1800 baud.
     */
    case B1800 = 0000012;

    /**
     * 2400 baud.
     */
    case B2400 = 0000013;

    /**
     * 4800 baud.
     */
    case B4800 = 0000014;

    /**
     * 9600 baud.
     */
    case B9600 = 0000015;

    /**
     * 19200 baud.
     */
    case B19200 = 0000016;

    /**
     * 38400 baud.
     */
    case B38400 = 0000017;

    /**
     * 57600 baud (non-POSIX, extended).
     */
    case B57600 = 0010001;

    /**
     * 115200 baud (non-POSIX, extended).
     */
    case B115200 = 0010002;

    /**
     * 230400 baud (non-POSIX, extended).
     */
    case B230400 = 0010003;

    /**
     * 460800 baud (non-POSIX, extended).
     */
    case B460800 = 0010004;

    /**
     * 500000 baud (non-POSIX, extended).
     */
    case B500000 = 0010005;

    /**
     * 576000 baud (non-POSIX, extended).
     */
    case B576000 = 0010006;

    /**
     * 921600 baud (non-POSIX, extended).
     */
    case B921600 = 0010007;

    /**
     * 1,000,000 baud (non-POSIX, extended).
     */
    case B1000000 = 0010010;

    /**
     * 1,152,000 baud (non-POSIX, extended).
     */
    case B1152000 = 0010011;

    /**
     * 1,500,000 baud (non-POSIX, extended).
     */
    case B1500000 = 0010012;

    /**
     * 2,000,000 baud (non-POSIX, extended).
     */
    case B2000000 = 0010013;

    /**
     * 2,500,000 baud (non-POSIX, extended).
     */
    case B2500000 = 0010014;

    /**
     * 3,000,000 baud (non-POSIX, extended).
     */
    case B3000000 = 0010015;

    /**
     * 3,500,000 baud (non-POSIX, extended).
     */
    case B3500000 = 0010016;

    /**
     * 4,000,000 baud (non-POSIX, extended).
     */
    case B4000000 = 0010017;
}
